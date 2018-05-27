<?
@session_start();

	header("Content-type: application/x-msdownload");
	header("Content-type: application/ms-excel");
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=forma_preco_".date("Ymdhis").".xls");
	header("Pragma: no-cache");
	header("Expires: 0");


$simulado = $_POST["edtsimulado"];
$campo_custo = $_POST["edtcusto"];
$id_formula = $_POST["edtformula"];
$id_tabela = $_POST["edttab"];

include "../../conecta.php";
include "../../funcoes.php";
include "../../functions.php";

$a_formula = farray(query("select * from  is_preco_formula where id_formula = '".$id_formula."'"));
$txtFormula =  str_replace('<br />',chr(13),$a_formula["formula"]);

$id_usuario = $_SESSION["id_usuario"];
$data = date("Y-m-d");
$hora = date("H:s:i");

$q_prod = query("select * from is_produtos");

echo "<table border=1>";
echo "<tr><td>Tabela : ".$id_tabela."</td><td>Fórmula : ".$a_formula["nome_formula"]."</td></tr>";

echo "<tr><td>Cod.Prod.</td><td>Descr.Prod.</td><td>Custo Usado</td><td>Preço de Venda</td></tr>";

while ($a_prod = farray($q_prod)) {
  $custo = $a_prod[$campo_custo];

  eval($txtFormula);

  $resultado_bd = number_format($resultado,2,".","");
  $resultado_tela = number_format($resultado,2,",",".");

  $a_tab = farray(query("select * from is_tab_preco_valor where id_lista_preco = '".$id_tabela."' and id_produto = '".$a_prod["id_produto"]."'"));

  if($a_tab["numreg"]) {
	  $sql = "UPDATE is_tab_preco_set valor = ".$resultado_bd.",valor_unitario = ".$resultado_bd." where id_lista_preco = '".$id_tabela."' and id_produto = '".$a_prod["id_produto"]."'";
  } else {
	  $sql = "INSERT into is_tab_preco_valor(id_lista_preco,id_produto,qtde_por_uni_med,valor,valor_unitario) values (";
	  $sql .= "'".$id_tabela."','".$a_prod["id_produto"]."',1,".$resultado_bd.",".$resultado_bd.")";
  }
  echo "<tr><td>".$a_prod["id_produto"]."</td><td>".$a_prod["nome_produto"]."</td><td align=right>". number_format($a_prod[$campo_custo],2,",",".")."</td><td align=right>".$resultado_tela."</td></td>";

  if ($simulado=="N") {
	  query($sql);
  }

}

echo "</table>";




?>