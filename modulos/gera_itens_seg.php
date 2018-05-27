<?php

#-----Lucas Augusto Milanes  - 21/04/2010-----------------#

echo "*============================================================*<br/>";
echo "Carga CONSUMO ESTIMADO<br>";
echo "*============================================================*<br/>";

require_once("../conecta.php");

$prod_seg = mysql_query("SELECT * FROM is_prod_segmentos");
$insert = 0;
while($ps = mysql_fetch_array($prod_seg)) {
  $qry_clientes = mysql_query("SELECT id_pessoa, id_pessoa_erp, id_grupo_cliente FROM is_pessoas WHERE id_grupo_cliente = '".$ps['id_grupo_cliente']."'");
  while($clientes = mysql_fetch_array($qry_clientes)) {
    if(mysql_num_rows(mysql_query("SELECT * FROM is_cons_estimado WHERE id_pessoa = '".$clientes['id_pessoa']."' AND id_produto = '".$prod_seg['id_produto']."'"))==0){
      mysql_query("INSERT INTO is_cons_estimado (id_usuario_cad,dt_cadastro,hr_cadastro,id_usuario_alt,dt_alteracao,hr_alteracao,id_produto,id_pessoa) VALUES ('oasis','".date("Y-m-d")."','".date("H:i:s")."','oasis','".date("Y-m-d")."','".date("H:i:s")."','".$ps['id_produto']."','".$clientes['id_pessoa']."')");
      $insert++;
    }
  }
}

echo "Inseridos ".$insert." registros.";

?>