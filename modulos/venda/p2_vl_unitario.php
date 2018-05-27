<?php
/*
 * p2_vl_unitario.php
 * Autor: Alex
 * 17/01/2011 16:47:00
 *
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
require('includes.php');

/*
 * Verifica se a v�ri�vel de tipo da venda foi preenchida.
 */
if($_POST['ptp_venda'] == 1 || $_POST['ptp_venda'] == 2){
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
    } elseif($_POST['ptp_venda'] == 2){
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
    }
}
else{
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
$IdProduto = $_POST['id_produto'];
$IdTabPreco = ($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem())?$_POST['id_tab_preco']:$Venda->getIdTabPreco();
$IdUnidMedida = $_POST['id_unid_medida'];
$IdProdutoEmbalagem = $_POST['id_produto_embalagem'];

if($Venda->isPrecoInformado()){
    $PrecoProduto = new PrecoProduto($IdProduto, $Venda->getGrupoTabPreco(),$IdTabPreco);
    $ArDadosPrecoNF                 = array();
    $ArDadosPrecoNF['uf']           = $Venda->getDadosEnderecoEntrega('uf');
    $ArDadosPrecoNF['id_produto']   = $_POST['id_produto'];
    $PrecoProduto->CalculaSugestaoDePrecoDeNF($ArDadosPrecoNF);
    $StringPreco = 'Informe o pre�o.'.$PrecoProduto->getStringCotacao();
}
else{
    $PrecoProduto = new PrecoProduto($IdProduto, $Venda->getGrupoTabPreco(), $IdTabPreco, $IdUnidMedida, $IdProdutoEmbalagem);
    $PrecoProduto->IdGrupoTabPreco = $Venda->getDadosVenda('id_grupo_tab_preco');
    $StringPreco = $PrecoProduto->getStringPreco(true);
}
header("content-type: text/xml");
echo '<?'.'xml version="1.0" encoding="ISO-8859-1"'.'?>'."\n";
echo '<root>'."\n";
echo "\t".'<preco>';
echo $StringPreco;
echo '</preco>'."\n";
echo '</root>';
?>