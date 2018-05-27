<?php
/*
 * p2_produto_kit_post.php
 * Autor: Alex
 * 20/06/2011 16:27:38
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");
require('includes.php');

$_POST = uB::UrlDecodePost($_POST);

$XML = '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.'>
    <resposta>
        <status>{!STATUS!}</status>
            <acao>{!ACAO!}</acao>
            <url>{!URL!}</url>
            <mensagem>{!MENSAGEM!}</mensagem>
    </resposta>
';

$Erro = false;


$Usuario = new Usuario($_SESSION['id_usuario']);
if($_POST['ptp_venda'] == 1 || $_POST['ptp_venda'] == 2){
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
    }
    elseif($_POST['ptp_venda'] == 2){
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
    }
}
else{
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
$VendaParametro = new VendaParametro();

$IdKIT = $_POST['id_kit'];
$ArItensAdicionados = array();

$IdSequenciaKIT = $Venda->getProximaSequenciaKIT();

$QryProdutoKIT = query("SELECT * FROM is_kit_produto WHERE id_kit = '".$IdKIT."' ORDER BY ordem");
while($ArProdutoKIT = farray($QryProdutoKIT)){
    $IdProduto = $ArProdutoKIT['id_produto'];
    
    if($ArProdutoKIT['sn_obrigatorio'] != '1' && $_POST['Adicionar_chk_item_kit_'.$IdProduto] != '1'){
        continue;
    }
    
    $Produto = new Produto($IdProduto);
    
    if($Venda->isPrecoInformado()){ /* Se a venda é preco informado */
        $PrecoProduto = new PrecoProduto($IdProduto, $Venda->getGrupoTabPreco(), NULL, NULL, NULL);

        /* Tratamento para quando usa sugetsão de preço por NF */
        if($VendaParametro->getSnUsaSugestaoDePrecoDeNF()){
            $ArDadosPrecoNF                 = array();
            $ArDadosPrecoNF['uf']           = $Venda->getDadosEnderecoEntrega('uf');
            $ArDadosPrecoNF['id_produto']   = $IdProduto;
            $PrecoProduto->CalculaSugestaoDePrecoDeNF($ArDadosPrecoNF);
        }

        $ProdutoVlUnitario = $PrecoProduto->getPreco();
    }
    else{
        $PrecoProduto = new PrecoProduto($IdProduto,$Venda->getGrupoTabPreco(), $Venda->getIdTabPreco());
        $ProdutoVlUnitario = $PrecoProduto->getPreco();
    }
    
    $IdKIT = $_POST['id_kit'];
    $QtdeKIT = TrataFloatPost($_POST['qtde_kit']);    
    $QtdePorKIT = ($ArProdutoKIT['sn_permite_alterar_qtde'] == '1')?TrataFloatPost($_POST['Adicionar_qtde_'.$IdProduto]):$ArProdutoKIT['qtde'];
    $IdUnidMedida = $_POST['Adicionar_id_unid_medida_'.$IdProduto];
    $VlUnitario = ($Venda->isPrecoInformado())?$_POST['Adicionar_vl_unitario_'.$IdProduto]:str_replace('.',',',$ProdutoVlUnitario);
    $IdTabPreco = $_POST['Adicionar_id_tab_preco_'.$IdProduto];
    $IdProdutoEmbalagem = $_POST['Adicionar_id_produto_embalagem_'.$IdProduto];
    $IdItemKIT = $_POST['Adicionar_id_item_kit_'.$IdProduto];
    
    $ArDadosInsertItem = array(
        'qtde'                  => $QtdePorKIT * $QtdeKIT,
        'id_moeda'              => '',
        'id_unid_medida'        => $IdUnidMedida,
        'id_produto_embalagem'  => $IdProdutoEmbalagem,
        'id_referencia'         => '',
        'id_tab_preco'          => $IdTabPreco,
        'id_kit'                => $IdKIT,
        'qtde_kit'              => $QtdeKIT,
        'vl_unitario'           => $VlUnitario,
        'qtde_por_unid_medida'  => '',
        'id_sequencia_kit'      => $IdSequenciaKIT,
        'id_item_kit'           => $IdItemKIT,
        'qtde_por_kit'          => $QtdePorKIT
    );
    
    $StatusItem = $Venda->AdicionaItemBD($IdProduto,$ArDadosInsertItem);
    if($StatusItem === false){
        foreach($ArItensAdicionados as $NumregItem){
            $Venda->RemoveItem($NumregItem);
        }
        $Erro = true;
        $Mensagem = TextoParaXML('Erro ao adicionar KIT.');
        break;
    }
    else{
        $ArItensAdicionados[] = $StatusItem;
    }
}
if($Erro === false){
    $Mensagem = TextoParaXML('KIT adicionado com sucesso.');
    $Status = 1;
    $Acao = 1;
}
else{
    $Status = 2;
    $Acao = 1;
}
$XML = str_replace('{!STATUS!}',$Status,$XML);
$XML = str_replace('{!ACAO!}',$Acao,$XML);
$XML = str_replace('{!URL!}',$Url,$XML);
$Mensagem = ($Mensagem == '')?TextoParaXML($Venda->getMensagem()):$Mensagem;
$XML = str_replace('{!MENSAGEM!}',$Mensagem,$XML);
header("Content-Type: text/xml");
echo $XML;
?>