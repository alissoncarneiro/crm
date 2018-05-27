<?php
header('Content-Type: text/html; charset=iso-8859-1');
$odbc_c = true;
echo "*============================================================*<br>";
echo "Carga de Endereços de Entrega Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

include('../../conecta.php');
include('../../funcoes.php');

// Carregando Parâmetros
//$ap = mysql_fetch_array(mysql_query("select * from is_dm_param"));

$tabela_erp = 'pub."loc-entr"';
$campo_chave_erp = 'nome-abrev';
$campo_descr_erp = 'endereco';

$tabela_crm = 'is_pessoa_endereco';
$campo_chave_crm = 'id_pessoa';

//mysql_query("delete from ".$tabela_crm);

/*===========================================================================================================*/
// Importa Clientes
/*===========================================================================================================*/
echo 'Importando Registros<br>';

$ar_depara = array(
								'cep' 			=> 'cep',
								'endereco' 		=> 'endereco',
								'bairro' 		=> 'bairro',
								'cidade' 		=> 'cidade',
								'uf' 			=> 'estado',
								'pais'                  => 'pais',
								'id_endereco_erp' 	=> 'cod-entrega',
								'id_pessoa'             => 'nome-abrev',
								);

$ar_fixos = array(
								'id_tp_endereco' 				=> "'1'",
								'id_logradouro' 				=> "'1'"
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

  echo "Buscando Registros ".date("H:i:s").'<br>';
  $q_erp = odbc_exec($cnx1,$sql);

  $u = 0; $i= 0;

  while ($a_erp = odbc_fetch_array($q_erp) ) {
      $import = true;

		$q_pes = farray(query("select numreg from is_pessoa where fantasia_apelido = '".str_replace(';'," ",str_replace('"'," ",str_replace("'"," ",$a_erp["nome-abrev"])))."'"));

                //echo "select numreg from is_pessoa where fantasia_apelido = '".str_replace(';'," ",str_replace('"'," ",str_replace("'"," ",$a_erp["nome-abrev"])))."'";exit;

		$id_pessoa = $q_pes["numreg"];

		$q_existe = farray(query("select numreg from ".$tabela_crm." where id_pessoa = '".$id_pessoa."' and id_endereco_erp = '".$a_erp["cod-entrega"]."'"));
		$pnumreg = $q_existe["numreg"];
		echo $a_erp[$campo_chave_erp]." ".$a_erp[$campo_descr_erp]." - ".$q_existe["numreg"].'<br>';

		// UPDATE
		if ($pnumreg) {
			$conteudos = '';
			foreach($ar_depara as $k => $v){
                            if($k == 'id_pessoa'){
                                    $sql = 'SELECT numreg FROM is_pessoa WHERE fantasia_apelido = \''.$a_erp[$v].'\'';
                                    //echo $sql,'<hr>';
                                    $qry = query($sql);
                                    $nrows = numrows($qry);
                                    if($nrows > 0){
                                        $ar_sql = farray($qry);
                                        $conteudos .=  $k." = '".addslashes($ar_sql['numreg'])."', ";
                                    } else {
                                        $import = false;
                                    }
                            } else {
				$conteudos .=  $k." = '".str_replace('"'," ",str_replace("'"," ",$a_erp[$v]))."', ";
                            }
			}
            $conteudos = substr($conteudos, 0, strlen($conteudos)-2);
			$sql = 'UPDATE '.$tabela_crm.' SET '.$conteudos." where numreg = '".$pnumreg."'";
                        if($import) {
                            $u = $u + 1;
                        }
		} else {
		// INSERT
			$conteudos = '';
			foreach($ar_depara as $k => $v){
                            if($k == 'id_pessoa'){
                                    $sql = 'SELECT numreg FROM is_pessoa WHERE fantasia_apelido = \''.$a_erp[$v].'\'';
                                    //echo $sql,'<hr>';
                                    $qry = query($sql);
                                    $nrows = numrows($qry);
                                    if($nrows > 0){
                                        $ar_sql = farray($qry);
                                        $conteudos .=  "'".addslashes($ar_sql['numreg'])."', ";
                                    } else {
                                        $import = false;
                                    }
                            } else {
				$conteudos .=  "'".str_replace('"'," ",str_replace("'"," ",$a_erp[$v]))."', ";
                            }
			}
			foreach($ar_fixos as $k => $v){
				$conteudos .=  $v.', ';
			}
            $conteudos = substr($conteudos, 0, strlen($conteudos)-2);
			$sql = 'INSERT INTO '.$tabela_crm.' ( '.$campos.' ) VALUES ('.$conteudos.")";
                        if($import) {
                            $i = $i + 1;
                        }
		}

      if($import) {
	  $rq = query($sql);
      

          if ($rq != "1") {
            echo $sql;
	  }
          
      }


  }



/*===========================================================================================================*/
// Fecha Conexões
/*===========================================================================================================*/

  odbc_close( $cnx1 );

  echo 'Fim do Processamento : Total'.($u+$i).' Inclusões : '.$i.' Atualizações : '.$u.' '.date("H:i:s");


?>