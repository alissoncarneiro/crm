<?php
/*
 * class.PoliticaComercialVenda.php
 * Autor: Alex
 * 08/12/2010 14:18
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class PoliticaComercialVenda{
    
    private $ArDadosVenda;
    private $ArDadosPessoa;

    private $Status = false;
    private $StringStatus;

    public function setArDadosVenda($ArDadosVenda){
        $this->ArDadosVenda = $ArDadosVenda;
    }

    public function setArDadosPessoa($ArDadosPessoa){
        $this->ArDadosPessoa = $ArDadosPessoa;
    }

    public function setStatus($Status){
        $this->Status = $Status;
    }

    public function setStringStatus($String){
        $this->StringStatus = $String;
    }

    public function getStatus(){
        return $this->Status;
    }

    public function getStringStatus(){
        return $this->StringStatus;
    }

    public function ValidaPolitica($PctMedDesc){
        $PctMedDesc = ($PctMedDesc == '')?0:$PctMedDesc;
        $ArrayCamposValores = array(
            'id_pessoa'                     => $this->ArDadosPessoa['id_pessoa'],
            'id_pessoa_regiao'              => $this->ArDadosPessoa['id_regiao'],
            'pessoa_cidade'                 => $this->ArDadosPessoa['cidade'],
            'pessoa_uf'                     => $this->ArDadosPessoa['uf'],
            'id_pessoa_canal_venda'         => $this->ArDadosPessoa['id_canal_venda'],
            'id_pessoa_grupo_cliente'       => $this->ArDadosPessoa['id_grupo_cliente'],
            'id_tp_pessoa'                  => $this->ArDadosPessoa['id_tp_pessoa'],
            'sn_contribuinte_icms'          => $this->ArDadosPessoa['sn_contribuinte_icms'],

            'id_venda_estabelecimento'      => $this->ArDadosVenda['id_estabelecimento'],
            'id_venda_repres_pri'           => $this->ArDadosVenda['id_repres_pri'],
            'id_venda_vendedor'             => $this->ArDadosVenda['id_usuario_cad'],
            'id_venda_tab_preco'            => $this->ArDadosVenda['id_tab_preco'],
            'id_venda_tp_venda'             => $this->ArDadosVenda['id_tp_venda'],
            'id_venda_dest_merc'            => $this->ArDadosVenda['id_destino_mercadoria'],
            'id_venda_moeda'                => $this->ArDadosVenda['id_moeda'],
            'id_venda_grupo_tab_preco'      => $this->ArDadosVenda['id_grupo_tab_preco'],
            'med_dias'                      => $this->ArDadosVenda['med_dias_cond_pagto'],

            'qtde'                          => $this->ArDadosVenda['qtde'],
            'vl_tot_venda'                  => $this->ArDadosVenda['vl_total_liquido'],

        );
        $SqlPoliticaVenda = "SELECT ".((TipoBancoDados == 'mssql')?'TOP(1)':'')." pct_max_desc FROM is_param_politica_comercial_desc WHERE sn_ativo = 1 AND ('".date("Y-m-d")."' BETWEEN dthr_validade_ini AND dthr_validade_fim)";
        foreach($ArrayCamposValores as $Campo => $Valor){
            $Valor = trim($Valor);
            if($Campo == 'qtde'){
                if($Valor != ''){
                    $SqlPoliticaVenda .= " AND ((".uB::GeraStringSqlBetweenCampo('qtde_ini','qtde_fim',$Valor).") OR (qtde_ini IS NULL AND qtde_fim IS NULL))";
                }
                else{
                    $SqlPoliticaVenda .= " AND (qtde_ini IS NULL AND qtde_fim IS NULL)";
                }
            }
            elseif($Campo == 'vl_tot_venda'){
                if($Valor != ''){
                    $SqlPoliticaVenda .= " AND ((".uB::GeraStringSqlBetweenCampo('vl_tot_venda_ini','vl_tot_venda_fim',$Valor).") OR (vl_tot_venda_ini IS NULL AND vl_tot_venda_fim IS NULL))";
                }
                else{
                    $SqlPoliticaVenda .= " AND (vl_tot_venda_ini IS NULL AND vl_tot_venda_fim IS NULL)";
                }
            }
            elseif($Campo == 'med_dias'){
                if($Valor != ''){
                    $SqlPoliticaVenda .= " AND ((".uB::GeraStringSqlBetweenCampo('med_dias_ini','med_dias_fim',$Valor).") OR (med_dias_ini IS NULL AND med_dias_fim IS NULL))";
                }
                else{
                    $SqlPoliticaVenda .= " AND (med_dias_ini IS NULL AND med_dias_fim IS NULL)";
                }
            }
            else{
                if($Valor != ''){
                    $SqlPoliticaVenda .= " AND (".$Campo." = '".TrataApostrofoBD($Valor)."' OR ".$Campo." IS NULL)";
                }
                else{
                    $SqlPoliticaVenda .= " AND ".$Campo." IS NULL";
                }
            }
        }
        $SqlPoliticaVenda .= " ORDER BY nr_pontos DESC, pct_max_desc DESC ".((TipoBancoDados == 'mysql')?' LIMIT 1':'');
        $QryPoliticaVenda = query($SqlPoliticaVenda);
        $ArPoliticaVenda = farray($QryPoliticaVenda);
        if($ArPoliticaVenda['pct_max_desc'] < $PctMedDesc){
            $this->Status = false;
            $this->setStringStatus('Cabe&ccedil;alho fora da pol&iacute;tica comercial');
            return false;
        }
        else{
            $this->Status = true;
            $this->setStringStatus('Cabe&ccedil;alho OK');
            return true;
        }
    }
}
?>