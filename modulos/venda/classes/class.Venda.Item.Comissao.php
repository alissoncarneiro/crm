<?php
/*
 * class.Venda.Item.Comissao.php
 * Autor: Alex
 * 19/05/2011 10:29:07
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class VendaItemComissao{
    private $NumregVendaItemComissao;

    private $VendaItem;

    private $ArDadosItemComissao;

    private $NomeTabela;
    private $CampoChave;

    public function  __construct($VendaItem,$IdRepresentante){
        if(!is_object($VendaItem) || !$VendaItem instanceof VendaItem){
            echo 'Objeto Item inválido';
            return false;
        }
        $this->VendaItem = $VendaItem;
        $this->NomeTabela = $this->VendaItem->getObjVenda()->getTabelaVendaItemRepresentanteComissao();
        $this->CampoChave = $this->VendaItem->getObjVenda()->getCampoChaveTabelaVendaItemRepresentanteComissao();
        $this->ArDadosItemComissao['id_representante'] = $IdRepresentante;

        if($this->CarregaDadosBD()){
            return true;
        }
        elseif($this->AdicionaRegistroBD($this->ArDadosItemComissao['id_representante'])){
            return true;
        }
        else{
            return false;
        }
    }

    public function AdicionaRegistroBD($IdRepresentante){
        $ArSqlInsert = array(
            $this->CampoChave       => $this->VendaItem->getNumregItem(),
            'id_representante'      => $IdRepresentante,
            'pct_comissao'          => 0,
            'vl_comissao'           => 0,
            'sn_alterado_manual'    => 0
        );
        $SqlInsert = AutoExecuteSql(TipoBancoDados, $this->NomeTabela, $ArSqlInsert, 'INSERT');
        $QryInsert = iquery($SqlInsert);
        if($QryInsert){
            $this->NumregVendaItemComissao = $QryInsert;
            $this->ArDadosItemComissao = $ArSqlInsert;
            $this->ArDadosItemComissao['numreg'] = $QryInsert;
            return true;
        }
        else{
            return false;
        }
    }

    public function CarregaDadosBD(){
        $SqlComissao = "SELECT * FROM ".$this->NomeTabela." WHERE ".$this->CampoChave." = ".$this->VendaItem->getNumregItem()." AND id_representante = ".$this->ArDadosItemComissao['id_representante'];
        $QryComissao = query($SqlComissao);
        $NumRowsComissao = numrows($QryComissao);
        if($NumRowsComissao > 1){
            $QtdeRegistros = $NumRowsComissao - 1;
            $SqlDelete = "DELETE ".((TipoBancoDados == 'mssql')?'TOP ('.$QtdeRegistros.')':'')." FROM ".$this->NomeTabela." WHERE id_representante = ".$this->ArDadosItemComissao['id_representante']." ".((TipoBancoDados == 'mysql')?'LIMIT '.$QtdeRegistros:'');
            query($SqlDelete);
        }
        elseif($NumRowsComissao == 0){
            return false;
        }
        $ArComissao = farray($QryComissao);
        foreach($ArComissao as $Coluna => $Valor){
            if(!is_int($Coluna)){
                $this->ArDadosItemComissao[$Coluna] = $Valor;
            }
        }
        return true;
    }

    public function AtualizaDadosBD(){
        $ArSqlUpdate = $this->getDadosItemComissao(NULL);
        $ArSqlUpdate['pct_comissao'] = ($ArSqlUpdate['pct_comissao'] == '')?0:$ArSqlUpdate['pct_comissao'];
        $SqlUpdate = AutoExecuteSql(TipoBancoDados, $this->NomeTabela, $ArSqlUpdate, 'UPDATE', array('numreg'));
        if(query($SqlUpdate)){
            return true;
        }
        return false;
    }

    public function getDadosItemComissao($IdCampo=NULL){
        if($IdCampo == NULL){
            return $this->ArDadosItemComissao;
        }
        return $this->ArDadosItemComissao[$IdCampo];
    }

    public function setDadosItemComissao($IdCampo,$Valor){
        if($IdCampo != 'numreg'){
            $this->ArDadosItemComissao[$IdCampo] = $Valor;
            return true;
        }
        return false;
    }

    public function getComissaoAlteradaManualmente(){
        if($this->getDadosItemComissao('sn_alterado_manual') == '1'){
            return true;
        }
        return false;
    }

    public function CalculaComissao(){
        if($this->getDadosItemComissao('sn_alterado_manual') == '1'){
            $PctComissao = $this->getDadosItemComissao('pct_comissao');
            $VlComissao = uM::uMath_pct_de_valor($PctComissao,$this->VendaItem->getDadosVendaItem('vl_total_liquido'));
            $this->setDadosItemComissao('vl_comissao', $VlComissao);
            $this->VendaItem->getObjVenda()->setMensagemDebug('Comissão alterada manualmente. Considerando '.$PctComissao.'%');
            return $VlComissao;
        }
        $CalculoComissao = new PoliticaComercialComis($this->VendaItem);
        /*
         * Dados do cabeçalho
         */
        $ArDadosVenda                   = $this->VendaItem->getObjVenda()->getDadosVenda();
        $ArDadosVenda['med_dias']       = $this->VendaItem->getObjVenda()->getMedDiasCondPagto();
        $ArDadosVenda['id_repres_pri']  = $this->VendaItem->getObjVenda()->getRepresentantePrincipal();
        $ArDadosVenda['venda_vl_tot']   = $this->VendaItem->getObjVenda()->getVlTotalVendaLiquido();
        
        $ArDadosVenda['id_venda_participante']   = $this->ArDadosItemComissao['id_representante'];
        
        /*
         * Dados do cliente
         */
        $ArDadosPessoa                  = $this->VendaItem->getObjVenda()->getPessoa()->getDadoPessoa();

        /*
         * Dados do produto
         */
        $ArDadosProduto                 = $this->VendaItem->getProduto()->getDadosProduto();
        
        if($ArDadosProduto['id_familia_comercial'] != ''){
            $QryFamiliaComercial = query("SELECT id_produto_linha FROM is_familia_comercial WHERE numreg = ".$ArDadosProduto['id_familia_comercial']);
            $ArFamiliaComercial = farray($QryFamiliaComercial);        
            $ArDadosProduto['id_linha']     = $ArFamiliaComercial['id_produto_linha'];
        }
        $ArDadosProduto['id_produto']   = $this->VendaItem->getProduto()->getNumregProduto();

        /*
         * Dados do item da venda
         */
        $ArDadosItem                    = $this->VendaItem->getDadosVendaItem();
        $ArDadosItem['item_vl_tot']     = $this->VendaItem->getDadosVendaItem('vl_total_liquido');
        $ArDadosItem['pct_media_desc']  = $this->VendaItem->getDadosVendaItem('pct_desconto_total');

        /*
         * Definindo o tipo de participação do representante
         */
        if(!empty($IndiceRepresentante)){
            $IdTpParticipacao = $this->VendaItem->getObjVenda()->getRepresentante($IndiceRepresentante)->getDadosVendaRepresentante('id_tp_participacao');
            $CalculoComissao->setIdTpParticipacao($IdTpParticipacao);
        }

        $CalculoComissao->setArDadosVenda($ArDadosVenda);
        $CalculoComissao->setArDadosPessoa($ArDadosPessoa);
        $CalculoComissao->setArDadosProduto($ArDadosProduto);
        $CalculoComissao->setArDadosItem($ArDadosItem);

        $CalculoComissao->CalculaComissao();

        $PctComissao = $CalculoComissao->getPctComissao();

        $VlComissao = uM::uMath_pct_de_valor($PctComissao,$this->VendaItem->getDadosVendaItem('vl_total_liquido'));

        $this->setDadosItemComissao('pct_comissao', $PctComissao);
        $this->setDadosItemComissao('vl_comissao', $VlComissao);

        return $VlComissao;
    }

}
?>