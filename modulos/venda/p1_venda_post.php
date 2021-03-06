<?php
/*
 * p1_pedido_post.php
 * Autor: Alex
 * 27/10/2010 11:19:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
if($_SESSION['id_usuario'] == ''){
    include('nao_logado.php');
    exit;
}
require('includes.php');

$Url = new Url();
$Url->setUrl($_POST['url_retorno']);
$Url->RemoveParam('ppostback');
$_POST['url_retorno'] = $Url->getUrl();
/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */
if($_POST['ptp_venda'] != 1 && $_POST['ptp_venda'] != 2){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
elseif(empty($_POST['pnumreg'])){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
else{
    $VisualizarRevisao = ($_POST['pvisualizar_revisao'] == '1')?true:false;
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg'],true,true,$VisualizarRevisao);
    }
    else{
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg'],true,true,$VisualizarRevisao);
    }
    /*
     * Tratando os campos
     */
    if(!$Venda->getDigitacaoCompleta() && !$Venda->VisualizarRevisao){
        $Venda->pfuncao = $_POST['pfuncao'];
        $Campos = new VendaCamposCustom($Venda->pfuncao,$Venda);
        $Campos->setIdPostBack($_POST['ppostback']);
        $Campos->setUrlRetorno($_POST['url_retorno']);
        $Campos->setPOST($_POST);
        $Campos->ValidaCamposObrigatorios();
        VendaCallBackCustom::ExecutaVenda($Venda, 'Passo1CamposObrigatorios', 'Depois', array('POST' => $_POST));
        $Campos->ValidaCamposDescontoFixo();
        $Campos->ValidaTrataPOST();
        $ArUpdate = $Campos->getArraySQL();
        $ArUpdate['numreg'] = $Venda->getNumregVenda();
        $ArUpdate['sn_passou_pelo_passo1'] = 1;
        $SqlUpdate = AutoExecuteSql(getParametrosGerais('TipoBancoDados'),$Venda->getTabelaVenda(),$ArUpdate,'UPDATE',array('numreg'));
        $Qry = query($SqlUpdate);
        if(!$Qry){
            echo alert(getError('0040010002',getParametrosGerais('RetornoErro')));
            $geraCadPost = new geraCadPost();
            $IdPostback = $geraCadPost->backupPost($_POST);
            $Url = new Url();
            $Url->setUrl($_POST['url_retorno']);
            $Url->AlteraParam('ppostback',$IdPostback);
            echo windowlocationhref($Url->getUrl());
            exit;
        }
        else{
            $VendaAntesUpdate = $Venda;
            unset($Venda);
            if($_POST['ptp_venda'] == 1){
                $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg'],true,true,$VisualizarRevisao);
            }
            else{
                $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg'],true,true,$VisualizarRevisao);
            }
            $Venda->CarregaDadosVendaBD();
            $Venda->RecalculaItensCFOPBD();
            $Venda->AtualizaDataEntregaBD();
            $Venda->AtualizaTaxaFinanceira();
            $Venda->GravaCfopVendaBD();
            $Venda->GravaRepresentantePessoaBD();
            $Venda->GravaRepresentantePrincipalCapa();
            $Venda->CalculaTotaisVendaItemBD();

            if($Venda->isOrcamento()){
                $Venda->AtualizaAtividadeEnvioOrcamento();                
            }
            VendaCallBackCustom::ExecutaVenda($Venda, 'Passo1Post', 'AposGravar',array('DadosAntesUpdate' => $VendaAntesUpdate->getDadosVenda()));
        }
    }
    $Url = new Url();
    $Url->setUrl($_POST['url_retorno']);
    $Url->AlteraParam('pnumreg',$Venda->getNumregVenda());
    $Url->AlteraParam('ppagina','p2');
    header("Location:./?".$Url->getStringQueryString());
    exit;
}
?>