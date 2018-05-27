<?php
/*
 * class.PoliticaComercialDescVendaCampoDescontoFixo.php
 * Autor: Alex
 * 11/05/2011 13:24:58
 * - Classe responsável por tratar os campos de desconto de Tab. Preço, Desc. Cliente e Desc. Informado do cabeçalho do pedido
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class PoliticaComercialDescVendaCampoDescontoFixo{

    private $ArDadosVenda;
    private $ArDadosPessoa;
    private $IdCampoDesconto;
    private $PctMaxCampoDesconto = 0;
    
    private $ArDadosCampoDesconto = array();
    
    private $Status = false;
    private $StringStatus;

    public function __construct($IdCampoDesconto){
        $this->IdCampoDesconto = $IdCampoDesconto;
        
        /* Carregando os dados do campo de desconto */
        $SqlCampoDesconto = "SELECT * FROM is_param_campo_desconto_venda_fixo WHERE numreg = '".$this->IdCampoDesconto."'";
        $QryCampoDesconto = query($SqlCampoDesconto);
        $ArCampoDesconto = farray($QryCampoDesconto);
        if(is_array($ArCampoDesconto)){
            foreach($ArCampoDesconto as $Coluna => $Valor){
                if(!is_int($Coluna)){
                    $this->ArDadosCampoDesconto[$Coluna] = $Valor;
                }
            }
        }        
    }

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

    public function getPctMaxCampoDesconto(){
        return $this->PctMaxCampoDesconto;
    }
    
    public function getDadosCampoDesconto($IdCampo=NULL){
        if($IdCampo == NULL){
            return $this->ArDadosCampoDesconto;
        }
        return $this->ArDadosCampoDesconto[$IdCampo];
    }

    public function getSnPermiteFinalizar(){
        if($this->getDadosCampoDesconto('sn_permite_finalizar') == '1'){
            return true;
        }
        return false;
    }

    public function ValidaPolitica($PctMedDesc){
        $PctMedDesc = ($PctMedDesc == '')?0:$PctMedDesc;
        $ArrayCamposValores = array(
            'id_campo_desconto'             => $this->IdCampoDesconto,
            'id_pessoa'                     => $this->ArDadosVenda['id_pessoa'],
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
        $SqlPoliticaVenda = "SELECT ".((TipoBancoDados == 'mssql')?'TOP(1)':'')." numreg,pct_max_desc FROM is_param_politica_comercial_desc_venda_campo_desconto_fixo WHERE sn_ativo = 1 AND ('".date("Y-m-d")."' BETWEEN dthr_validade_ini AND dthr_validade_fim)";
        foreach($ArrayCamposValores as $Campo => $Valor){
            $Valor = trim($Valor);
            if($Campo == 'qtde'){
                if($Valor != ''){
                    $SqlPoliticaVenda .= " AND ((".uB::GeraStringSqlBetweenCampo('qtde_ini','qtde_fim',$Valor).") OR (qtde_ini IS NULL AND qtde_fim IS NULL))\r\n";
                }
                else{
                    $SqlPoliticaVenda .= " AND (qtde_ini IS NULL AND qtde_fim IS NULL)\r\n";
                }
            }
            elseif($Campo == 'vl_tot_venda'){
                if($Valor != ''){
                    $SqlPoliticaVenda .= " AND ((".uB::GeraStringSqlBetweenCampo('vl_tot_venda_ini','vl_tot_venda_fim',$Valor).") OR (vl_tot_venda_ini IS NULL AND vl_tot_venda_fim IS NULL))\r\n";
                }
                else{
                    $SqlPoliticaVenda .= " AND (vl_tot_venda_ini IS NULL AND vl_tot_venda_fim IS NULL)\r\n";
                }
            }
            elseif($Campo == 'med_dias'){
                if($Valor != ''){
                    $SqlPoliticaVenda .= " AND ((".uB::GeraStringSqlBetweenCampo('med_dias_ini','med_dias_fim',$Valor).") OR (med_dias_ini IS NULL AND med_dias_fim IS NULL))\r\n";
                }
                else{
                    $SqlPoliticaVenda .= " AND (med_dias_ini IS NULL AND med_dias_fim IS NULL)\r\n";
                }
            }
            else{
                if($Valor != ''){
                    $SqlPoliticaVenda .= " AND (".$Campo." = '".TrataApostrofoBD($Valor)."' OR ".$Campo." IS NULL)\r\n";
                }
                else{
                    $SqlPoliticaVenda .= " AND ".$Campo." IS NULL\r\n";
                }
            }
        }
        $SqlPoliticaVenda .= " ORDER BY nr_pontos DESC, pct_max_desc DESC ".((TipoBancoDados == 'mysql')?' LIMIT 1':'');
        $QryPoliticaVenda = query($SqlPoliticaVenda);
        $ArPoliticaVenda = farray($QryPoliticaVenda);

        $this->PctMaxCampoDesconto = ($ArPoliticaVenda['pct_max_desc'] != '')?$ArPoliticaVenda['pct_max_desc']:0;

        if($ArPoliticaVenda['pct_max_desc'] < $PctMedDesc){
            $this->Status = false;
            $StringStatus = 'fora da pol&iacute;tica comercial.';
            if(!$this->getSnPermiteFinalizar()){
                $StringStatus .= ' <span style="color:#FF0000;">Este campo não permite finalização da venda nestas condições.</span>';
            }
            $this->setStringStatus($StringStatus);
            if($_SESSION['debug'] === true){
                if(!$ArPoliticaVenda){
                    echo 'Nenhuma Regra Encontrada. SQL:',pre($SqlPoliticaVenda),'<hr/>';
                }
                else{
                    echo 'Desconto máximo permitido de ',$ArPoliticaVenda['pct_max_desc'],'% - (Regra ',$ArPoliticaVenda['numreg'],')<hr/>';
                }
            }
        }
        else{
            $this->Status = true;
            $StringStatus = 'OK.';
            if($_SESSION['debug'] === true){
                $StringStatus = 'OK. Regra encontrada Nº'.$ArPoliticaVenda['numreg'];
            }
            $this->setStringStatus($StringStatus);
            return true;
        }
    }
}
?>