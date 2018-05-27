<?php

/*
 * class.Venda.CondicaoPagamentoProgramado.php
 * Autor: Alex
 * 30/08/2011 16:11:30
 */

class VendaCondicaoPagamentoProgramado{
    private $ObjVenda;
    private $ArDatasProgramadas = array();
    private $SnCondPagtoProgramado = false;

    public function __construct(Venda $ObjVenda){
        $this->ObjVenda = $ObjVenda;
        $this->CarregaDatasProgramadas();
        if(count($this->ArDatasProgramadas) > 0){
            $this->SnCondPagtoProgramado = true;
        }
    }

    public function getSnCondPagtoProgramado(){
        return $this->SnCondPagtoProgramado;
    }

    public function CarregaDatasProgramadas(){
        $SqlParamCondPagto = "SELECT numreg FROM is_param_cond_pagto_programado WHERE sn_ativo = 1 AND dthr_validade_ini <= '".date("Y-m-d")."' AND dthr_validade_fim >= '".date("Y-m-d")."' AND id_cond_pagto = '".$this->ObjVenda->getDadosVenda('id_cond_pagto')."'";
        $QryParamCondPagto = query($SqlParamCondPagto);
        $ArParamCondPagto = farray($QryParamCondPagto);
        if($ArParamCondPagto){
            $this->ObjVenda->setMensagemDebug('Condição de Pagamento programado encontrada.');
            $SqlParamCondPagtoDetalhe = "SELECT dt_programada FROM is_param_cond_pagto_programado_detalhe WHERE id_param_cond_pagto_programado = '".$ArParamCondPagto['numreg']."' ORDER BY dt_programada";
            $QryParamCondPagtoDetalhe = query($SqlParamCondPagtoDetalhe);
            while($ArParamCondPagtoDetalhe = farray($QryParamCondPagtoDetalhe)){
                $this->ArDatasProgramadas[] = $ArParamCondPagtoDetalhe['dt_programada'];
                $this->ObjVenda->setMensagemDebug('Adicionada Data '.$ArParamCondPagtoDetalhe['dt_programada']);
            }
        }
        else{
            $this->ObjVenda->setMensagemDebug('Condição de pagamento programado não encontrada!');
        }
    }

    public function getDatasProgramadas(){
        return $this->ArDatasProgramadas;
    }

    public function GravaAtualizaDatasBD(){
        $SqlDelete = "DELETE FROM ".$this->ObjVenda->getTabelaVendaCondPagtoEspecial()." WHERE ".$this->ObjVenda->getCampoChaveTabelaVendaCondPagtoEspecial()." = ".$this->ObjVenda->getNumregVenda();
        query($SqlDelete);
        $QtdeParcelas = count($this->ArDatasProgramadas);
        if($QtdeParcelas > 0){
            $IdCampoChave       = $this->ObjVenda->getCampoChaveTabelaVendaCondPagtoEspecial();
            $DataBase           = $this->ObjVenda->getDadosVenda('dt_venda');
            $IdSequencia        = 10;
            $PctMediaParcela    = floor(100 / $QtdeParcelas);
            $UltimoRegistro     = 0;
            foreach($this->ArDatasProgramadas as $Data){
                $ArSqlInsert = array(
                    $IdCampoChave                       => $this->ObjVenda->getNumregVenda(),
                    'id_sequencia'                      => $IdSequencia,
                    'dt_pagto'                          => $Data,
                    'pct_parcela'                       => $PctMediaParcela,
                    'vl_parcela'                        => 0,
                    'qtde_dias_vencimento_parcela'      => DiferencaEntreDatas($DataBase, $Data)
                );
                $SqlInsert = AutoExecuteSql(TipoBancoDados, $this->ObjVenda->getTabelaVendaCondPagtoEspecial(), $ArSqlInsert, 'INSERT');
                $UltimoRegistro = iquery($SqlInsert);
                if(!$UltimoRegistro){
                    $this->ObjVenda->setMensagem('Erro ao inserir condição de pagamento programado.');
                    $this->ObjVenda->setMensagemDebug('Erro ao inserir condição de pagamento programado. SQL('.$SqlInsert.')');
                    return false;
                }
                $IdSequencia += 10;
            }

            $ArSqlUpdateDiferenca = array(
                'numreg'        => $UltimoRegistro,
                'pct_parcela'   => (100 - ($PctMediaParcela * ($QtdeParcelas - 1)))
            );
            $SqlUpdateDiferenca = AutoExecuteSql(TipoBancoDados, $this->ObjVenda->getTabelaVendaCondPagtoEspecial(), $ArSqlUpdateDiferenca, 'UPDATE', array('numreg'));
            $QryUpdateDiferenca = query($SqlUpdateDiferenca);
            if(!$QryUpdateDiferenca){
                $this->ObjVenda->setMensagem('Erro ao atualizar ultima parcela condição de pagamento programado.');
                $this->ObjVenda->setMensagemDebug('Erro ao atualizar ultima parcela condição de pagamento programado. SQL('.$SqlUpdateDiferenca.')');
                return false;
            }
            return true;
        }
    }
}
?>