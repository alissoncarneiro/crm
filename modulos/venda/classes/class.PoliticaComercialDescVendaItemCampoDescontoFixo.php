<?php
/*
 * class.PoliticaComercialDescVendaItemCampoDescontoFixo.php
 * Autor: Alex
 * 11/05/2011 12:00
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class PoliticaComercialDescVendaItemCampoDescontoFixo{

    private $VendaItem;
    
    private $IdCampoDesconto;
    private $PctCampoDescontoItem;
    private $PctMaxCampoDescontoItem;
    private $PctComissao;

    private $ArDadosVenda;
    private $ArDadosPessoa;
    private $ArDadosItem;
    private $ArDadosProduto;

    private $Status = false;
    private $StringStatus;

    public function  __construct(VendaItem $VendaItem,$IdCampoDesconto) {
        if(empty($IdCampoDesconto)){
            echo 'Campo de desconto não informado ('.__LINE__.')';
            exit;
        }
        $this->VendaItem = $VendaItem;
        $this->IdCampoDesconto = $IdCampoDesconto;
    }

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

    public function setStringStatus($String){
        $this->StringStatus = $String;
    }

    public function setPctCampoDescontoItem($PctCampoDescontoItem){
        $this->PctCampoDescontoItem = $PctCampoDescontoItem;
    }

    public function getStatus(){
        return $this->Status;
    }

    public function getStringStatus(){
        return $this->StringStatus;
    }

    public function getPctCampoDescontoItem(){
        return $this->PctCampoDescontoItem;
    }

    public function getPctComissao(){
        return $this->PctComissao;
    }

    public function getPctMaxCampoDescontoItem(){
        return $this->PctMaxCampoDescontoItem;
    }

    public function CalculaMaximoDescontoPermitido(){
        $ArrayCamposValores = array(
            'id_campo_desconto'             => $this->IdCampoDesconto,

            'id_pessoa'                     => $this->ArDadosPessoa['id_pessoa'],
            'id_pessoa_regiao'              => $this->ArDadosPessoa['id_regiao'],
            'pessoa_cidade'                 => $this->ArDadosPessoa['cidade'],
            'pessoa_uf'                     => $this->ArDadosPessoa['uf'],
            'id_pessoa_canal_venda'         => $this->ArDadosPessoa['id_canal_venda'],
            'id_pessoa_grupo_cliente'       => $this->ArDadosPessoa['id_grupo_cliente'],
            'id_tp_pessoa'                  => $this->ArDadosPessoa['id_tp_pessoa'],
            'sn_contribuinte_icms'          => $this->ArDadosPessoa['sn_contribuinte_icms'],

            'id_produto'                    => $this->ArDadosProduto['id_produto'],
            'id_produto_familia'            => $this->ArDadosProduto['id_familia'],
            'id_produto_familia_comercial'  => $this->ArDadosProduto['id_familia_comercial'],
            'id_produto_grupo_estoque'      => $this->ArDadosProduto['id_grupo_estoque'],
            'id_produto_linha'              => $this->ArDadosProduto['id_linha'],

            'id_venda_estabelecimento'      => $this->ArDadosVenda['id_estabelecimento'],
            'id_venda_repres_pri'           => $this->ArDadosVenda['id_representante_principal'],
            'id_venda_tab_preco'            => $this->ArDadosVenda['id_tab_preco'],
            'id_venda_tp_venda'             => $this->ArDadosVenda['id_tp_venda'],
            'id_venda_dest_merc'            => $this->ArDadosVenda['id_destino_mercadoria'],
            'id_venda_moeda'                => $this->ArDadosVenda['id_moeda'],
            'med_dias'                      => $this->ArDadosVenda['med_dias'],

            'id_unid_med'                   => $this->ArDadosVenda['id_unid_medida'],
            'qtde'                          => $this->ArDadosVenda['qtde'],
            'vl_tot_item'                   => $this->ArDadosItem['vl_total_liquido']
        );
        $SqlPoliticaCampoDescontoItem = "SELECT ".((TipoBancoDados == 'mssql')?'TOP(1)':'')." numreg,pct_max_desc FROM is_param_politica_comercial_desc_venda_item_campo_desconto_fixo WHERE sn_ativo = 1 AND ('".date("Y-m-d")."' BETWEEN dthr_validade_ini AND dthr_validade_fim)";
        foreach($ArrayCamposValores as $Campo => $Valor){
            $Valor = trim($Valor);
            if($Campo == 'qtde'){
                if($Valor != ''){
                    $SqlPoliticaCampoDescontoItem .= " AND ((".uB::GeraStringSqlBetweenCampo('qtde_ini','qtde_fim',$Valor).") OR (qtde_ini IS NULL AND qtde_fim IS NULL))";
                }
                else{
                    $SqlPoliticaCampoDescontoItem .= " AND (qtde_ini IS NULL AND qtde_fim IS NULL)";
                }
            }
            elseif($Campo == 'vl_tot_item'){
                if($Valor != ''){
                    $SqlPoliticaCampoDescontoItem .= " AND ((".uB::GeraStringSqlBetweenCampo('vl_tot_item_ini','vl_tot_item_fim',$Valor).") OR (vl_tot_item_ini IS NULL AND vl_tot_item_fim IS NULL))";
                }
                else{
                    $SqlPoliticaCampoDescontoItem .= " AND (vl_tot_item_ini IS NULL AND vl_tot_item_fim IS NULL)";
                }
            }
            elseif($Campo == 'med_dias'){
                if($Valor != ''){
                    $SqlPoliticaCampoDescontoItem .= " AND ((".uB::GeraStringSqlBetweenCampo('med_dias_ini','med_dias_fim',$Valor).") OR (med_dias_ini IS NULL AND med_dias_fim IS NULL))";
                }
                else{
                    $SqlPoliticaCampoDescontoItem .= " AND (med_dias_ini IS NULL AND med_dias_fim IS NULL)";
                }
            }
            else{
                if($Valor != ''){
                    $SqlPoliticaCampoDescontoItem .= " AND (".$Campo." = '".TrataApostrofoBD($Valor)."' OR ".$Campo." IS NULL)";
                }
                else{
                    $SqlPoliticaCampoDescontoItem .= " AND ".$Campo." IS NULL";
                }
            }
        }
        $SqlPoliticaCampoDescontoItem .= " ORDER BY nr_pontos DESC, pct_max_desc DESC ".((TipoBancoDados == 'mysql')?' LIMIT 1':'');

        $QryPoliticaCampoDescontoItem = query($SqlPoliticaCampoDescontoItem);
        $NumRowsPoliticaCampoDescontoItem = numrows($QryPoliticaCampoDescontoItem);
        if($NumRowsPoliticaCampoDescontoItem == 0){
            $this->PctMaxCampoDescontoItem = 0;
            $this->VendaItem->getObjVenda()->setMensagemDebug('Nenhuma regra de desconto econtrada. SQL:'.$SqlPoliticaCampoDescontoItem);
        }
        else{
            $ArPoliticaCampoDescontoItem = farray($QryPoliticaCampoDescontoItem);
            $this->PctMaxCampoDescontoItem = $ArPoliticaCampoDescontoItem['pct_max_desc'];
            $this->VendaItem->getObjVenda()->setMensagemDebug('Regra número '.$ArPoliticaCampoDescontoItem['numreg'].' encontrada ('.$this->PctMaxCampoDescontoItem.'%).');
        }
    }

    public function ValidaCampoDescontoItem(){
        if($this->getPctCampoDescontoItem() > $this->getPctMaxCampoDescontoItem()){
            $this->Status = false;
            $this->setStringStatus('Fora da politica - Desconto maior que o máximo permitido ('.number_format($this->getPctMaxCampoDescontoItem(), 2,',','.').'%)');
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