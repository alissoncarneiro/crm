<?
include('../../conecta.php');
$id_tab_preco = $_POST['id_tab_preco'];
$id_produto = $_POST['id_produto'];
$sql = "SELECT * FROM is_tab_preco_valor WHERE id_lista_preco = '".$id_tab_preco."' AND id_produto = '".$id_produto."'";
$qry = mysql_query($sql);
$ar = mysql_fetch_array($qry);
#echo $sql;exit;
echo number_format($ar['valor'],2,',','.');
?>