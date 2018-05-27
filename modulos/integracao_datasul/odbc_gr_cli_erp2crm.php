<?php
header('Content-Type: text/html; charset=iso-8859-1');
echo "*============================================================*<br>";
echo "Carga de Grupo de Clientes Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

$odbc_c = true;

require("../../conecta.php");
include "../../funcoes.php";

// Carregando Par�metros

$tabela_erp = 'pub."gr-cli"';
$campo_chave_erp = 'cod-gr-cli';
$campo_descr_erp = 'descricao';

$tabela_crm = 'is_grupo_cliente';
$campo_chave_crm = 'id_grupo_cliente_erp';

//query("delete from ".$tabela_crm);



/*===========================================================================================================*/
// Importa Clientes
/*===========================================================================================================*/
echo 'Importando Registros<br>';

$ar_depara = array(
								'id_grupo_cliente_erp' 			=> 'cod-gr-cli',
								'nome_grupo_cliente' 			=> 'descricao'
								);

$ar_fixos = array();

		$campos = '';
		foreach($ar_depara as $k => $v){
			$campos .=  $k.', ';
		}
		foreach($ar_fixos as $k => $v){
			$campos .=  $k.', ';
		}
        $campos = substr($campos, 0, strlen($campos)-2);

  $sql = 'select * from '.$tabela_erp;

  echo "Buscando Registros ".date("H:i:s").'<br>';
  $q_erp = odbc_exec($cnx1,$sql);

  $u = 0; $i= 0;

  while ($a_erp = odbc_fetch_array($q_erp) ) {

		$q_existe = farray(query("select numreg from ".$tabela_crm." where ".$campo_chave_crm." = '".$a_erp[$campo_chave_erp]."'"));
		$pnumreg = $q_existe["numreg"];
		echo $a_erp[$campo_chave_erp]." ".$a_erp[$campo_descr_erp]." - ".$q_existe["numreg"].'<br>';

		// UPDATE
		if ($pnumreg) {
			$conteudos = '';
			foreach($ar_depara as $k => $v){
				$conteudos .=  $k." = '".str_replace('"'," ",str_replace("'"," ",$a_erp[$v]))."', ";
			}
            $conteudos = substr($conteudos, 0, strlen($conteudos)-2);
			$sql = 'UPDATE '.$tabela_crm.' SET '.$conteudos." where numreg = '".$pnumreg."'";
			$u = $u + 1;
		} else {
		// INSERT
			$conteudos = '';
			foreach($ar_depara as $k => $v){
				$conteudos .=  "'".str_replace('"'," ",str_replace("'"," ",$a_erp[$v]))."', ";
			}
			foreach($ar_fixos as $k => $v){
				$conteudos .=  $v.', ';
			}
            $conteudos = substr($conteudos, 0, strlen($conteudos)-2);
			$sql = 'INSERT INTO '.$tabela_crm.' ( '.$campos.' ) VALUES ('.$conteudos.")";
			$i = $i + 1;
		}


	  $rq = query(  TextoBD("mysql",$sql) ); 

      if ($rq != "1") {
		  //echo $sql;
	  }


  }



/*===========================================================================================================*/
// Fecha Conex�es 
/*===========================================================================================================*/
  
  odbc_close( $cnx1 );

  echo 'Fim do Processamento : Total'.($u+$i).' Inclus�es : '.$i.' Atualiza��es : '.$u.' '.date("H:i:s");


?>