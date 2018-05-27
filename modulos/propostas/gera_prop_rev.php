<?
  @header("Content-Type: text/html;  charset=ISO-8859-1",true);
  @session_start();

  require_once("../../conecta.php");
  require_once("../../funcoes.php");

  $pnumreg = $_GET["pnumreg"];
  $id_usuario = $_SESSION["id_usuario"];

  $qry_proposta = farray(query("select * from is_proposta where numreg = '".$pnumreg."'"));
  $qry_max_rev = farray(query("select max(revisao) as ultimo from is_proposta where id_proposta = '".$qry_proposta["id_proposta_orig"]."' or id_proposta_orig = '".$qry_proposta["id_proposta_orig"]."'"));
  $ultima_rev = ($qry_max_rev["ultimo"]*1)+1;
  $ultima_proposta = $qry_proposta["id_proposta_orig"].'/'.$ultima_rev;

  $sql = "INSERT INTO is_proposta (
  id_proposta ,
  id_proposta_orig ,
  revisao ,
  id_pessoa ,
  id_usuario_resp ,
  valor ,
  id_oportunidade,
  id_tab_preco,
  id_cond_pagto ,
  id_modelo_proposta ,
  id_pessoa_contato ,
  id_estabelecimento ,
  dt_entrega_desejada ,
  obs) values ( '"
  .$ultima_proposta."','"
  .$qry_proposta["id_proposta_orig"]."',"
  .$ultima_rev.",
  '".$qry_proposta["id_pessoa"]."',
  '".$qry_proposta["id_usuario_resp"]."',
  '".$qry_proposta["valor"]."',
  '".($qry_proposta["id_oportunidade"]*1)."',
  '".$qry_proposta["id_tab_preco"]."',
  '".$qry_proposta["id_cond_pagto"]."',
  '".$qry_proposta["id_modelo_proposta"]."',
  '".$qry_proposta["id_pessoa_contato"]."',
  '".$qry_proposta["id_estabelecimento"]."',
  '".$qry_proposta["dt_entrega_desejada"]."',
  '".$qry_proposta["obs"]."')";
  query( str_replace("''","NULL",$sql) );

  $qry_proposta_max = farray(query("select max(numreg) as ultima from is_proposta"));

  $sql_prop_iten = query("select * from is_proposta_prod where id_proposta = '".$qry_proposta["numreg"]."'");
  while ( $qry_prop_iten = farray($sql_prop_iten) ) {
		  $sql_item = "INSERT INTO is_proposta_prod  (
          id_proposta ,
          id_produto ,
          qtde ,
          pct_desc ,
          valor ,
          valor_total ) values (
          '".$qry_proposta_max["ultima"]."',
          '".$qry_prop_iten["id_produto"]."',
          '".$qry_prop_iten["qtde"]."',
          '".$qry_prop_iten["pct_desc"]."',
          '".$qry_prop_iten["valor"]."',
          '".$qry_prop_iten["valor_total"]."')";
		  query( str_replace("''","NULL",$sql_item) );
  }


  echo '<script language="Javascript"> ';
  echo "window.alert('Proposta/Revisão ".$ultima_proposta." gerada com sucesso !'); ";
  echo ' window.setTimeout( "'."window.close()".'", 100);';
  echo '</script>';

  
?>

