<?php
/*
 * class.PoliticaComercialDescVendaItemMedia.php
 * Autor: Alex
 * 11/05/2011 14:34:39
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class PoliticaComercialDescVendaItemMedia{
    private $ArDadosItem;
    private $ArDadosProduto;
    private $Status = false;
    private $StringStatus;


    public function setArDadosItem($ArDadosItem){
        $this->ArDadosItem = $ArDadosItem;
    }

    public function setArDadosProduto($ArDadosProduto){
        $this->ArDadosProduto = $ArDadosProduto;
    }

    public function setStatus($Status){
        $this->Status = $Status;
    }

    public function setArDadosVenda($ArDadosVenda){
        $this->ArDadosVenda = $ArDadosVenda;
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


    public function ValidaItem(){
        $ArrayCamposValores = array(
            'id_produto'            => $this->ArDadosProduto['id_produto'],
            'id_familia'            => $this->ArDadosProduto['id_familia'],
            'id_familia_comercial'  => $this->ArDadosProduto['id_familia_comercial'],
            'id_grupo_estoque'      => $this->ArDadosProduto['id_grupo_estoque'],
            'id_linha'              => $this->ArDadosProduto['id_linha'],
            'id_unid_medida'        => $this->ArDadosItem['id_unid_medida'],
            'qtde'                  => $this->ArDadosItem['qtde'],
            'vl_tot_item'           => $this->ArDadosItem['vl_total_bruto'],
            'med_dias'              => $this->ArDadosVenda['med_dias']
        );
        $SqlPoliticaItem = "SELECT ".((TipoBancoDados == 'mssql')?'TOP(1)':'')." pct_max_desc FROM is_param_politica_comercial_desc_venda_item_media WHERE sn_ativo = 1 AND ('".date("Y-m-d")."' BETWEEN dthr_validade_ini AND dthr_validade_fim)";
        foreach($ArrayCamposValores as $Campo => $Valor){
            $Valor = trim($Valor);
            if($Campo == 'qtde'){
                if($Valor != ''){
                    $SqlPoliticaItem .= " AND ((".uB::GeraStringSqlBetweenCampo('qtde_ini','qtde_fim',$Valor).") OR (qtde_ini IS NULL AND qtde_fim IS NULL))";
                }
                else{
                    $SqlPoliticaItem .= " AND (qtde_ini IS NULL AND qtde_fim IS NULL)";
                }
            }
            elseif($Campo == 'vl_tot_item'){
                if($Valor != ''){
                    $SqlPoliticaItem .= " AND ((".uB::GeraStringSqlBetweenCampo('vl_tot_item_ini','vl_tot_item_fim',$Valor).") OR (vl_tot_item_ini IS NULL AND vl_tot_item_fim IS NULL))";
                }
                else{
                    $SqlPoliticaItem .= " AND (vl_tot_item_ini IS NULL AND vl_tot_item_fim IS NULL)";
                }
            }
            elseif($Campo == 'med_dias'){
                if($Valor != ''){
                    $SqlPoliticaItem .= " AND ((".uB::GeraStringSqlBetweenCampo('med_dias_ini','med_dias_fim',$Valor).") OR (med_dias_ini IS NULL AND med_dias_fim IS NULL))";
                }
                else{
                    $SqlPoliticaItem .= " AND (med_dias_ini IS NULL AND med_dias_fim IS NULL)";
                }
            }
            else{
                if($Valor != ''){
                    $SqlPoliticaItem .= " AND (".$Campo." = '".TrataApostrofoBD($Valor)."' OR ".$Campo." IS NULL)";
                }
                else{
                    $SqlPoliticaItem .= " AND ".$Campo." IS NULL";
                }
            }
        }
        $SqlPoliticaItem .= " ORDER BY nr_pontos DESC, pct_max_desc DESC ".((TipoBancoDados == 'mysql')?' LIMIT 1':'');

        $QryPoliticaItem = query($SqlPoliticaItem);
        $ArPoliticaItem = farray($QryPoliticaItem);
        if($ArPoliticaItem['pct_max_desc'] < $this->ArDadosItem['pct_desconto_total']){
            $this->Status = false;
            $this->setStringStatus('Fora da politica');
            return false;
        }
        else{
            $this->Status = true;
            $this->setStringStatus('OK');
            return true;
        }
    }
}
?>