<?php
header('Content-Type: text/html; charset=iso-8859-1');

echo "*============================================================*<br>";
echo "Carga de Vendedores/Representantes Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

$odbc_c = true;

require("../../conecta.php");
include "../../funcoes.php";

// Carregando Parâmetros

$tabela_erp = 'pub."repres"';
$campo_chave_erp = 'cod-rep';
$campo_descr_erp = 'nome';

$tabela_crm = 'is_usuario';
$campo_chave_crm = 'id_representante';

//query("delete from ".$tabela_crm);


$id_usuario = 'IMPORT';
//Conecta com os bancos ODBC
/*===========================================================================================================*/
// Importa Clientes
/*===========================================================================================================*/
echo 'Importando Registros<br>';

$ar_depara = array(
								'id_usuario' 		=> 'cod-rep',
								'id_representante' 	=> 'cod-rep',
								'nome_usuario' 		=> 'nome',
								'email' 		=> 'e-mail',
								'nome_abreviado'        => 'nome-abrev'
								);

$ar_fixos = array(
								'hr_cadastro' 			=> "'".date("H:i:s")."'",
								'id_usuario_cad' 		=> "'IMPORT'",
								'dt_alteracao' 			=> "'".date("Y-m-d")."'",
								'hr_alteracao' 			=> "'".date("H:i:s")."'",
								'id_usuario_alt' 		=> "'IMPORT'",
								'id_perfil' 			=> "'5'",
								'idioma' 			=> "'PT'",
								'senha' 			=> "'oasis'"
								);

		$campos = '';
		foreach($ar_depara as $k => $v){
			$campos .=  $k.', ';
		}
		foreach($ar_fixos as $k => $v){
			$campos .=  $k.', ';
		}
        $campos = substr($campos, 0, strlen($campos)-2);

  $sql = 'select * from '.$tabela_erp.' order by "'.$campo_chave_erp.'"';

  echo "Buscando Registros ".date("H:i:s").'<br>';
  $q_erp = odbc_exec($cnx1,$sql);

  $u = 0; $i= 0;

  while ($a_erp = odbc_fetch_array($q_erp) ) {

		$q_existe = farray(query("select numreg from ".$tabela_crm." where ".$campo_chave_crm." = '".$a_erp[$campo_chave_erp]."'"));
		$pnumreg = $q_existe["numreg"];
		echo $a_erp[$campo_chave_erp]." ".$a_erp[$campo_descr_erp]." - ".$q_existe["numreg"].'<br>';
		$q_max = farray(query("select (max(numreg)+1) as ultimo from ".$tabela_crm));

		// UPDATE
		if ($pnumreg) {
			$conteudos = '';
			foreach($ar_depara as $k => $v){
				$conteudos .=  $k." = '".str_replace(';'," ",str_replace('"'," ",str_replace("'"," ",$a_erp[$v])))."', ";
			}
            $conteudos = substr($conteudos, 0, strlen($conteudos)-2);
			$sql = 'UPDATE '.$tabela_crm.' SET '.$conteudos." where numreg = '".$pnumreg."'";
			$u = $u + 1;
		} else {
		// INSERT
			$conteudos = '';
			foreach($ar_depara as $k => $v){
				$conteudos .=  "'".str_replace(';'," ",str_replace('"'," ",str_replace("'"," ",$a_erp[$v])))."', ";
			}
			foreach($ar_fixos as $k => $v){
				$conteudos .=  $v.', ';
			}
            $conteudos = substr($conteudos, 0, strlen($conteudos)-2);
			$sql = 'INSERT INTO '.$tabela_crm.' ( '.$campos.' ) VALUES ('.$conteudos.' )';
			$i = $i + 1;
		}


	  $rq = query($sql); 

      if ($rq != "1") {
		  //echo $sql;
	  }


  }



/*===========================================================================================================*/
// Fecha Conexões 
/*===========================================================================================================*/
  
  odbc_close( $cnx1 );

  echo 'Fim do Processamento : Total'.($u+$i).' Inclusões : '.$i.' Atualizações : '.$u.' '.date("H:i:s");


?>