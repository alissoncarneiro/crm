<?php
/*
 * pesquisa_produto_autocomplete.php
 * Autor: Alex
 * 29/10/2010 16:32:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
$PrefixoIncludes = '../';
require('../includes.php');

/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */
if($_GET['ptp_venda'] == 1 || $_GET['ptp_venda'] == 2){
        if($_GET['ptp_venda'] == 1){
            $Venda = new Orcamento($_GET['ptp_venda'],$_GET['pnumreg']);
        }
        elseif($_GET['ptp_venda'] == 2){
            $Venda = new Pedido($_GET['ptp_venda'],$_GET['pnumreg']);
        }
} else{
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}

$VendaParametro = new VendaParametro();

$ArSuggestions = array();
$ArData = array();

$texto_filtro = trim(addslashes(stripslashes($_GET['query'])));

switch($_GET['campo']){
    case 'edttexto_filtro_1' :
        $Campo = 'id_produto_erp';
        $CampoBD = 't1.id_produto_erp';
        break;
    case 'edttexto_filtro_2' :
        $Campo = 'nome_produto';
        $CampoBD = 't1.nome_produto';
        break;
    case 'edttexto_filtro_3' :
        $Campo = 'id_produto_compl';
        $CampoBD = 't1.id_produto_compl';
        break;
    case 'edttexto_filtro_4' :
        $Campo = 'id_produto_pessoa';
        $CampoBD = 't2.id_produto_pessoa';
        break;
    default :
        exit;
        break;
}
$MaxResult = 15;
$SqlProduto = "SELECT DISTINCT ".((TipoBancoDados == 'mssql')?'TOP('.$MaxResult.')':'')." t1.numreg,".$CampoBD." FROM is_produto t1 ";
$SqlProduto .= " LEFT JOIN is_produto_pessoa t2 ON t1.numreg = t2.id_produto ";
$SqlProduto .= " LEFT JOIN is_familia_comercial t3 ON t1.id_familia_comercial = t3.numreg ";
$SqlProduto .= " WHERE t1.sn_ativo = 1";
$SqlProduto .= ($texto_filtro != '')?" AND ".$CampoBD." LIKE '".$texto_filtro."%'":'';
if($VendaParametro->getSnUsaBloqueioRepxFam()){
    $SqlProduto .= " AND t3.numreg IN(SELECT DISTINCT tsq1.id_familia_comercial FROM is_param_representantexfamilia_comercial tsq1 WHERE tsq1.id_representante = '".$_SESSION['id_usuario']."' AND tsq1.sn_ativo = 1 AND tsq1.dthr_validade_ini <= '".date("Y-m-d")."' AND tsq1.dthr_validade_fim >= '".date("Y-m-d")."')";
}

$BloqueioCustom = VendaCallBackCustom::ExecutaVenda($Venda,'SQLPesquisaProduto','');

$SqlProduto .= ($BloqueioCustom != '1')?$BloqueioCustom:'';

$SqlProduto .= (TipoBancoDados == 'mysql')?' LIMIT '.$MaxResult:'';

$QryProduto = query($SqlProduto);
while($ArProduto = farray($QryProduto)){
    $ArSuggestions[]    = $ArProduto[$Campo];
    $ArData[]           = $ArProduto['numreg'];
}
if(count($ArSuggestions) > 0){
    echo "{\n";
    echo " query:'".$texto_filtro."',\n";
    echo " suggestions:['".implode("','",$ArSuggestions)."'],\n";
    echo " data:['".implode("','",$ArData)."']\n";
    echo "}";
}
?>