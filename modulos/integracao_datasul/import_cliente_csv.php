<?
require("../conecta.php");
$file = file('csv_clientes.csv');
$ql = count($file);
for($i=0;$i<$ql;$i++){
	$linha = explode(';',$file[$i]);
	$id_cliente = $linha[0];
	
	
	
	
	
	
}






?>