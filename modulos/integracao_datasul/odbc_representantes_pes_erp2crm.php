<?php
echo "*============================================================*<br>";
echo "Carga de Representantes Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

require("../../conecta.php");
include "../../funcoes.php";

// Carregando Parâmetros
$ap = farray(query("select * from is_dm_param"));

$alias_odbc = "ems2cad_prod";
$tabela_erp = 'pub.repres';
$campo_chave_erp = 'cod-rep';
$campo_descr_erp = 'nome';

$tabela_crm = 'is_pessoas';
$campo_chave_crm = 'id_pessoa_erp';

//query("delete from ".$tabela_crm." where sn_representante = 'S'");


$id_usuario = 'IMPORT';
//Conecta com os bancos ODBC
$cnx_erp = odbc_connect($alias_odbc,"sysprogress","sysprogress") or die("Erro na conexão com o Database"); 
/*===========================================================================================================*/
// Importa Clientes
/*===========================================================================================================*/
echo 'Importando Registros<br>';

$ar_depara = array(
								'id_pessoa_erp' 		=> 'cod-rep',
								'razao_social_nome' 	=> 'nome',
								'cnpj_cpf' 				=> 'cgc',
								'ie_rg' 				=> 'inscr-est',
								'email_prof' 			=> 'e-mail',
								'endereco' 				=> 'endereco',
								'bairro' 				=> 'bairro',
								'cidade' 				=> 'cidade',
								'uf' 					=> 'estado',
								'pais' 					=> 'pais',
								'cep' 					=> 'cep',
								'tipo_pessoa' 			=> 'natureza',
								'fantasia_apelido' 		=> 'nome-abrev',
								'id_representante' 		=> 'cod-rep',
								'site' 					=> 'home-page',
								'tel1' 					=> 'telefone',
								'fax' 					=> 'telefax',
								'nome_abreviado' 		=> 'nome-abrev',
								'id_usuario_gc' 		=> 'cod-rep',
								'id_vendedor' 			=> 'cod-rep'
								);

$ar_fixos = array(
								'dt_cadastro' 			=> "'".date("Y-m-d")."'",
								'hr_cadastro' 			=> "'".date("H:i:s")."'",
								'id_usuario_cad' 		=> "'IMPORT'",
								'dt_alteracao' 			=> "'".date("Y-m-d")."'",
								'hr_alteracao' 			=> "'".date("H:i:s")."'",
								'id_usuario_alt' 		=> "'IMPORT'",
								'id_relac' 				=> "'8'",
								'sn_representante' 		=> "'S'",
								'ativo' 				=> "'S'"
								);

		$campos = '';
		foreach($ar_depara as $k => $v){
			$campos .=  $k.', ';
		}
		foreach($ar_fixos as $k => $v){
			$campos .=  $k.', ';
		}
        $campos = substr($campos, 0, strlen($campos)-2);

  $sql = 'select * from '.$tabela_erp.' where "ind-situacao"=\'1\' order by "'.$campo_chave_erp.'"';

  echo "Buscando Registros ".date("H:i:s").'<br>';
  $q_erp = odbc_exec($cnx_erp,$sql); 

  $u = 0; $i= 0;

  while ($a_erp = odbc_fetch_array($q_erp) ) {

		$q_existe = farray(query("select numreg from ".$tabela_crm." where ".$campo_chave_crm." = '".$a_erp[$campo_chave_erp]."' and sn_representante = 'S'"));
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
			$sql = 'UPDATE '.$tabela_crm.' SET '.$conteudos.", ativo = 'S' where numreg = '".$pnumreg."' and sn_representante = 'S'";
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
			$sql = 'INSERT INTO '.$tabela_crm.' ( '.$campos.',id_pessoa'.' ) VALUES ('.$conteudos.','."'".$q_max["ultimo"]."')";
			$i = $i + 1;
		}


	  $rq = query(  TextoBD("mysql",$sql) ); 

      if ($rq != "1") {
		  //echo $sql;
	  }


  }



/*===========================================================================================================*/
// Fecha Conexões 
/*===========================================================================================================*/
  
  odbc_close( $cnx_erp ); 

  echo 'Fim do Processamento : Total'.($u+$i).' Inclusões : '.$i.' Atualizações : '.$u.' '.date("H:i:s");


?>