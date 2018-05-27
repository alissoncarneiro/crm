<?php
/*
 * p4_venda_comissao_item_post.php
 * Autor: Alex
 * 19/05/2011 18:37:48
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("content-type: text/xml");
session_start();
require('includes.php');
$_POST = uB::UrlDecodePost($_POST);
/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */
if(empty($_POST['ptp_venda']) || empty($_POST['pnumreg'])){
    echo getError('0040030001',getParametrosGerais('RetornoErro'));
    exit;
}
if($_POST['ptp_venda'] == 1 || $_POST['ptp_venda'] == 2){
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
    }
    elseif($_POST['ptp_venda'] == 2){
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
    }
}
$IndiceRepresentante = $_POST['indice_representante'];

$StringLog = 'Alteração de Comissão dos itens.'."\r\n";
foreach($Venda->getItens() as $IndiceItem => $Item){
    $ItemComissao = $Item->getItemComissao($IndiceRepresentante);
    
    $PctComissao = TrataFloatPost($_POST['pct_comissao_'.$Item->getNumregItem()]);
    $PctComissaoAtual = $ItemComissao->getDadosItemComissao('pct_comissao');
    
    $StringLog .= 'Item '.$Item->getDadosVendaItem('id_sequencia').' - '.$Item->getProduto()->getDadosProduto('id_produto_erp').' de '.number_format_min($PctComissaoAtual,0,',','').'% para '.number_format_min($PctComissao,0,',','').'%.'."\r\n";
}

$Venda->ZeraComissaoRepresentante($IndiceRepresentante);
foreach($Venda->getItens() as $IndiceItem => $Item){
    $PctComissao = TrataFloatPost($_POST['pct_comissao_'.$Item->getNumregItem()]);
    $VlTotalLiquido = $Item->getDadosVendaItem('vl_total_liquido');
    $VlComissao = uM::uMath_pct_de_valor($PctComissao,$VlTotalLiquido);

    $ItemComissao = $Item->getItemComissao($IndiceRepresentante);
    $ItemComissao->setDadosItemComissao('pct_comissao',$PctComissao);
    $ItemComissao->setDadosItemComissao('vl_comissao', $VlComissao);
    $ItemComissao->setDadosItemComissao('sn_alterado_manual', 1);
    $ItemComissao->setDadosItemComissao('id_usuario_alteracao_manual', $_SESSION['id_usuario']);
    $ItemComissao->setDadosItemComissao('dt_alteracao_manual', date("Y-m-d"));
    $ItemComissao->setDadosItemComissao('hr_alteracao_manual', date("H:i:s"));
    $ItemComissao->AtualizaDadosBD();
}
$Venda->CalculaTotaisComissaoBD();
$Venda->GravaLogBD(19, 'Alteração de Comissão dos itens.'."\r\n".'Justificativa: '.$_POST['pjustificativaalteracacomissaoitem'], $StringLog);
$Mensagem = TextoParaXML('Comissões salvas com sucesso!');
header("Content-Type: text/xml");
echo '<?'.'xml version="1.0" encoding="ISO-8859-1"'.'?>'."\n";
echo '<root>'."\n";
echo "\t".'<status>'.$Status.'</status>'."\n";
echo "\t".'<mensagem>';
echo $Mensagem;
echo '</mensagem>'."\n";
echo '</root>';
?>