<?php
echo "*============================================================*<br>";
echo "Carga de Familias Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

require("../../conecta.php");
include "../../funcoes.php";

// Carregando Par�metros

$alias_odbc = "ems2cad_prod";
$tabela_erp = 'pub."familia"';
$campo_chave_erp = 'fm-codigo';
$campo_descr_erp = 'familia';

$tabela_crm = 'is_familia';
$campo_chave_crm = 'id_familia';

//query("delete from ".$tabela_crm);


$id_usuario = 'IMPORT';
//Conecta com os bancos ODBC
$cnx_erp = odbc_connect($alias_odbc,"sysprogress","sysprogress") or die("Erro na conex�o com o Database"); 
/*===========================================================================================================*/
// Importa Clientes
/*===========================================================================================================*/
echo 'Importando Registros<br>';

$ar_depara = array(
								'id_familia' 			=> 'fm-codigo',
								'nome_familia' 			=> 'descricao'
								);

$ar_fixos = array(
								'dt_cadastro' 			=> "'".date("Y-m-d")."'",
								'hr_cadastro' 			=> "'".date("H:i:s")."'",
								'id_usuario_cad' 		=> "'IMPORT'",
								'dt_alteracao' 			=> "'".date("Y-m-d")."'",
								'hr_alteracao' 			=> "'".date("H:i:s")."'",
								'id_usuario_alt' 		=> "'IMPORT'"

								);

		$campos = '';
		foreach($ar_depara as $k => $v){
			$campos .=  $k.', ';
		}
		foreach($ar_fixos as $k => $v){
			$campos .=  $k.', ';
		}
        $campos = substr($campos, 0, strlen($campos)-2);

  $sql = 'select * from '.$tabela_erp;

  echo "Buscando Registros ".date("H:i:s").$sql.'<br>';
  $q_erp = odbc_exec($cnx_erp,$sql); 

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
  
  odbc_close( $cnx_erp ); 

  echo 'Fim do Processamento : Total'.($u+$i).' Inclus�es : '.$i.' Atualiza��es : '.$u.' '.date("H:i:s");


?>