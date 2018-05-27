<?php

/*
 * class.PoliticaComercialBloqueioFinalizacao
 * Autor: Alex
 * 05/08/2011 12:00:06
 */
class PoliticaComercialBloqueioFinalizacao{

    private $ArDadosVenda;
    private $ArDadosPessoa;
    private $ArDadosItem;
    private $ArDadosProduto;
    private $Status = false;
    private $StringStatus;
    private $NumregRegraEncontrada;
    
    public function setArDadosVenda($ArDadosVenda){
        $this->ArDadosVenda = $ArDadosVenda;
    }

    public function setArDadosPessoa($ArDadosPessoa){
        $this->ArDadosPessoa = $ArDadosPessoa;
    }

    public function setArDadosItem($ArDadosItem){
        $this->ArDadosItem = $ArDadosItem;
    }

    public function setArDadosProduto($ArDadosProduto){
        $this->ArDadosProduto = $ArDadosProduto;
    }

    public function setStatus($Status){
        $this->Status = $Status;
    }

    public function setArDadoItem($IdCampo,$Valor){
        $this->ArDadosItem[$IdCampo] = $Valor;
    }

    public function setArDadoProduto($IdCampo,$Valor){
        $this->ArDadosProduto[$IdCampo] = $Valor;
    }

    public function setArDadoVenda($IdCampo,$Valor){
        $this->ArDadosVenda[$IdCampo] = $Valor;
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

    public function getNumregRegraEncontrada(){
        return $this->NumregRegraEncontrada;
    }

    public function ValidaBloqueioFinalizacao(){
        $ArrayCamposValores = array(
            'tipo_venda'                    => $this->ArDadosVenda['tipo_venda'],
            'id_pessoa'                     => $this->ArDadosVenda['id_pessoa'],
            'id_pessoa_regiao'              => $this->ArDadosPessoa['id_regiao'],
            'pessoa_cidade'                 => $this->ArDadosPessoa['cidade'],
            'pessoa_uf'                     => $this->ArDadosPessoa['uf'],
            'id_pessoa_canal_venda'         => $this->ArDadosPessoa['id_canal_venda'],
            'id_pessoa_grupo_cliente'       => $this->ArDadosPessoa['id_grupo_cliente'],
            'id_tp_pessoa'                  => $this->ArDadosPessoa['id_tp_pessoa'],
            'pessoa_sn_contribuinte_icms'   => $this->ArDadosPessoa['sn_contribuinte_icms'],
            'id_venda_estabelecimento'      => $this->ArDadosVenda['id_estabelecimento'],
            'id_venda_repres_pri'           => $this->ArDadosVenda['id_repres_pri'],
            'id_venda_tab_preco'            => $this->ArDadosVenda['id_tab_preco'],
            'id_venda_tp_venda'             => $this->ArDadosVenda['id_tp_venda'],
            'id_venda_dest_merc'            => $this->ArDadosVenda['id_dest_merc'],
            'id_venda_moeda'                => $this->ArDadosVenda['id_moeda'],
            'id_venda_grupo_tab_preco'      => $this->ArDadosVenda['id_grupo_tab_preco'],
            'id_venda_cond_pagto'           => $this->ArDadosVenda['id_cond_pagto'],
            'id_venda_tp_frete'             => $this->ArDadosVenda['id_tp_frete'],
            'id_venda_transportadora'       => $this->ArDadosVenda['id_transportadora'],
            'id_venda_cond_pagto'           => $this->ArDadosVenda['id_cond_pagto'],
            'venda_sn_faturamento_parcial'  => $this->ArDadosVenda['sn_faturamento_parcial'],
            'venda_sn_aprovacao_parcial'    => $this->ArDadosVenda['sn_aprovacao_parcial'],
            'venda_sn_antecipa_entrega'     => $this->ArDadosVenda['sn_antecipa_entrega'],
            'venda_vl_tot'                  => $this->ArDadosVenda['venda_vl_tot'],
            'venda_med_dias'                => $this->ArDadosVenda['med_dias'],
            'id_produto'                    => $this->ArDadosProduto['id_produto'],
            'id_produto_linha'              => $this->ArDadosProduto['id_linha'],
            'id_produto_familia_comercial'  => $this->ArDadosProduto['id_familia_comercial'],
            'id_produto_familia'            => $this->ArDadosProduto['id_familia'],
            'id_produto_unid_medida'        => $this->ArDadosProduto['id_unid_medida'],
            'id_produto_grupo_estoque'      => $this->ArDadosProduto['id_grupo_estoque'],
            'item_qtde'                     => $this->ArDadosItem['qtde'],
            'item_vl_tot'                   => $this->ArDadosItem['item_vl_tot'],
            'item_unid_medida'              => $this->ArDadosItem['id_unid_medida'],
            'item_pct_media_desc'           => $this->ArDadosItem['pct_media_desc']
        );
        include('BloqueioFinalizacaoCustom.php');
        $SqlBloqueio = "SELECT ".((TipoBancoDados == 'mssql')?'TOP(1)':'')." numreg,descricao_regra FROM is_param_politica_comercial_bloq_venda WHERE sn_ativo = 1 AND ('".date("Y-m-d")."' BETWEEN dthr_validade_ini AND dthr_validade_fim) ";
        foreach($ArrayCamposValores as $Campo => $Valor){
            $Valor = trim($Valor);
            if($Campo == 'venda_vl_tot'){
                if($Valor != ''){
                    $SqlBloqueio .= " AND ((".uB::GeraStringSqlBetweenCampo('venda_vl_tot_ini','venda_vl_tot_fim',$Valor).") OR (venda_vl_tot_ini IS NULL AND venda_vl_tot_fim IS NULL))";
                }
                else{
                    $SqlBloqueio .= " AND (venda_vl_tot_ini IS NULL AND venda_vl_tot_fim IS NULL)";
                }
            }
            elseif($Campo == 'venda_med_dias'){
                if($Valor != ''){
                    $SqlBloqueio .= " AND ((".uB::GeraStringSqlBetweenCampo('venda_med_dias_ini','venda_med_dias_fim',$Valor).") OR (venda_med_dias_ini IS NULL AND venda_med_dias_fim IS NULL))";
                }
                else{
                    $SqlBloqueio .= " AND (venda_med_dias_ini IS NULL AND venda_med_dias_fim IS NULL)";
                }
            }
            elseif($Campo == 'item_qtde'){
                if($Valor != ''){
                    $SqlBloqueio .= " AND ((".uB::GeraStringSqlBetweenCampo('item_qtde_ini','item_qtde_fim',$Valor).") OR (item_qtde_ini IS NULL AND item_qtde_fim IS NULL))";
                }
                else{
                    $SqlBloqueio .= " AND (qtde_ini IS NULL AND qtde_fim IS NULL)";
                }
            }
            elseif($Campo == 'item_vl_tot'){
                if($Valor != ''){
                    $SqlBloqueio .= " AND ((".uB::GeraStringSqlBetweenCampo('item_vl_tot_ini','item_vl_tot_fim',$Valor).") OR (item_vl_tot_ini IS NULL AND item_vl_tot_fim IS NULL))";
                }
                else{
                    $SqlBloqueio .= " AND (item_vl_tot_ini IS NULL AND item_vl_tot_fim IS NULL)";
                }
            }
            elseif($Campo == 'item_pct_media_desc'){
                if($Valor != ''){
                    $SqlBloqueio .= " AND ((".uB::GeraStringSqlBetweenCampo('item_pct_media_desc_ini','item_pct_media_desc_fim',$Valor).") OR (item_pct_media_desc_ini IS NULL AND item_pct_media_desc_fim IS NULL))";
                }
                else{
                    $SqlBloqueio .= " AND (item_pct_media_desc_ini IS NULL AND item_pct_media_desc_fim IS NULL)";
                }
            }
            else{
                if($Valor != ''){
                    $SqlBloqueio .= " AND (".$Campo." = '".TrataApostrofoBD($Valor)."' OR ".$Campo." IS NULL)";
                }
                else{
                    $SqlBloqueio .= " AND ".$Campo." IS NULL";
                }
            }
        }
        $SqlBloqueio .= " ".((TipoBancoDados == 'mysql')?' LIMIT 1':'');
        $QryBloqueio = query($SqlBloqueio);
        $NumRowsBloqueio = numrows($QryBloqueio);
        if($NumRowsBloqueio > 0){
            $ArBloqueio = farray($QryBloqueio);
            $this->NumregRegraEncontrada    = $ArBloqueio['numreg'];
            
            $this->Status = true;
            $this->setStringStatus('Bloqueado pela regra comercial NК '.$this->NumregRegraEncontrada.'('.$ArBloqueio['descricao_regra'].'), nуo permite finalizaчуo.');

            return true;
        }
        else{
            $this->Status = false;
            $this->setStringStatus('Nenhuma regra de bloqueio econtrada');
            return false;
            
        }
    }
}
?>