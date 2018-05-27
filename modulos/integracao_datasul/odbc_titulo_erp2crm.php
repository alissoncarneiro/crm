<?php
header('Content-Type: text/html; charset=iso-8859-1');
$odbc_c = true;
echo "*============================================================*<br>";
echo "Carga de Titulos Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

include("../../conecta.php");
include "../../funcoes.php";

query("delete from is_titulo");


/*===========================================================================================================*/
// Importa NFs
/*===========================================================================================================*/
if (empty($sn_somente_baixa)) {
	echo 'Importando Titulos em Atraso :<br>';

	$sql = 'select 
	"cod-estabel", "cod-esp", "nr-docto", "parcela","cod-emitente","dt-emissao","dt-vencimen", "vl-original","vl-saldo","perc-juros", "sit-titulo","dt-vecto-orig", "dt-ult-pagto", "vl-pago", "nr-pedcli", "perc-multa","cod-emitente","cod-rep","nome-abrev" from pub."titulo"';

	  $q_titulo = odbc_exec($cnx2,$sql);

	  while ($a_titulo = odbc_fetch_array($q_titulo) ) {
              if($a_titulo['vl-saldo'] == '0') {
                  $situacao = '2';
              } else {
                  $situacao = '1';
              }

              if($a_titulo["cod-emitente"] != '') {
                  $sql = 'SELECT numreg FROM is_pessoa WHERE id_pessoa_erp = \''.$a_titulo["cod-emitente"].'\'';
                  $ar_sql = farray(query($sql));
                  $numreg_pessoa = $ar_sql['numreg'];
              }

			  $sql_insert = 'INSERT INTO is_titulo ( id_tp_situacao, id_pessoa, id_titulo_erp, n_parcela, id_pedido_erp, dt_emissao , dt_vencimento, dt_vencimento_original, dt_pagamento, vl_titulo, vl_saldo, pct_multa, pct_juros ) VALUES (';

			  $sql_insert .= "'".$situacao."','";
			  $sql_insert .= $numreg_pessoa."','".$a_titulo["nr-docto"]."','".$a_titulo["parcela"]."','".$a_titulo["nr-pedcli"]."','".$a_titulo["dt-emissao"]."','".$a_titulo["dt-vencimen"]."','".$a_titulo["dt-vecto-orig"]."','".$a_titulo["dt-ult-pagto"]."','".$a_titulo["vl-original"]."','".$a_titulo["vl-saldo"]."','".$a_titulo["perc-multa"]."','".$a_titulo["perc-juros"]."')";
			  echo $a_titulo["nr-docto"].' - '.$a_titulo["nome-abrev"].' importado.<br>';

			  query(str_replace("''","NULL",$sql_insert)); 


	  }

}


/*===========================================================================================================*/
// Atualiza Data Ultimo Titulo
/*===========================================================================================================*/
    echo 'Atualizando ultimo titulo do cliente :<br>';

	$sql = 'select "cod-emitente",max("dt-vencimen") as ULTIMO from pub."titulo" group by "cod-emitente"';

	$q_titulo = odbc_exec($cnx2,$sql);

	while ($a_titulo = odbc_fetch_array($q_titulo) ) {

		  echo $a_titulo["cod-emitente"].' - '.$a_titulo["ULTIMO"].' importado.<br>';
			  
		  $sql_update = "update is_pessoas set dts_titulo_vencimento = '".$a_titulo["ULTIMO"]."' where id_pessoa_erp = '".$a_titulo["cod-emitente"]."'";

		  query(str_replace("''","NULL",$sql_update));

	}
/*===========================================================================================================*/
// Baixar Titulos Pagos
/*===========================================================================================================*/
/*  echo 'Processando Atualizações no ERP - Baixas, Prorrogações, etc :<br>';

  $q_ativ = query("select id_titulo, n_parcela, numreg from is_titulos_telecobranca where id_situacao <> 'R' order by numreg desc");

  while ($a_ativ = farray($q_ativ) ) {
      $a_bx = odbc_fetch_array(odbc_exec($cnx_tit,'select "cod-estabel", "cod-esp", "nr-docto", "parcela","cod-emitente","dt-emissao","dt-vencimen", "vl-original","vl-saldo","perc-juros", "sit-titulo","dt-vecto-orig", "dt-ult-pagto", "vl-pago", "nr-pedcli", "perc-multa","cod-emitente","cod-rep","nome-abrev" from pub."titulo" where "nr-docto" = '."'".$a_ativ["id_titulo"]."' and ".'"parcela" = '."'".$a_ativ["n_parcela"]."'"));

      if (($a_bx["vl-saldo"]*1) == 0) { $sit = " id_situacao = 'R' ,"; } else { $sit = " id_situacao = 'P', "; }
	  $sql_update = "update is_titulos_telecobranca set ".$sit." dt_vencimento = '".$a_bx["dt-vencimen"]."', dt_pagamento = '".$a_bx["dt-ult-pagto"]."', vl_saldo = '".$a_bx["vl-saldo"]."' where numreg = '".$a_ativ["numreg"]."'";

	  query(  TextoBD("mysql",str_replace("''","NULL",$sql_update)) ); 
	  echo $a_bx["nr-docto"].' - '.$a_bx["nome-abrev"].' processado.<br>';

  }
*/

/*===========================================================================================================*/
// Fecha Conexões
/*===========================================================================================================*/
  
  odbc_close( $cnx2 );



?>
