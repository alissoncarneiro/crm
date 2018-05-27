<?php
/*
 * p2_venda_tabela_faixa_preco_comissao.php
 * Autor: Alex
 * 18/05/2011 14:02:46
 */

header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
require('includes.php');

$VendaParametro = new VendaParametro();

if(!$VendaParametro->getSnExibeFaixaPrecoComissao()){
    exit;
}
$IdTabPreco = $_POST['id_tab_preco'];
$IdProduto = $_POST['id_produto'];

header("content-type: text/xml");
echo '<?'.'xml version="1.0" encoding="ISO-8859-1"'.'?>'."\n";
echo "<root>\n";
echo "\t<registros>\n";
if($IdTabPreco != '' && $IdProduto != ''){
    $QryTabelaFaixaPrecoComissao = query("SELECT * FROM is_meta_faixas_preco_comissao WHERE id_tab_preco = ".$IdTabPreco." AND id_produto = ".$IdProduto." ORDER BY vl_unit_inicial");
    while($ArTabelaFaixaPrecoComissao = farray($QryTabelaFaixaPrecoComissao)){
        echo "\t\t<registro>\n";
        echo "\t\t\t<vl_unit_inicial>".number_format_min($ArTabelaFaixaPrecoComissao['vl_unit_inicial'])."</vl_unit_inicial>\n";
        echo "\t\t\t<vl_unit_final>".number_format_min($ArTabelaFaixaPrecoComissao['vl_unit_final'])."</vl_unit_final>\n";
        echo "\t\t\t<pct_comissao>".number_format_min($ArTabelaFaixaPrecoComissao['pct_comissao'])."</pct_comissao>\n";
        echo "\t\t\t<sn_preco_default>".$ArTabelaFaixaPrecoComissao['sn_preco_default']."</sn_preco_default>\n";
        echo "\t\t</registro>\n";
    }
}
echo "\t</registros>\n";
echo '</root>';
?>