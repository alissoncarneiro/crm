<?php
header('Content-Type: text/html; charset=iso-8859-1');
$odbc_c = true;
echo "*============================================================*<br>";
echo "Carga de Tab. Preço Valores Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

require("../../conecta.php");
include "../../funcoes.php";

// Carregando Parâmetros

$tabela_erp = 'pub."preco-item"';
$campo_chave_erp = 'nr-tabpre';
$campo_descr_erp = 'preco-venda';

$tabela_crm = 'is_tab_preco_valor';
$campo_chave_crm = 'id_tab_preco';

query("delete from ".$tabela_crm);



/*===========================================================================================================*/
// Importa Clientes
/*===========================================================================================================*/
echo 'Importando Registros<br>';

$ar_depara = array(
                    'id_tab_preco' 			=> 'nr-tabpre',
                    'id_produto'			=> 'it-codigo',
                    'vl_unitario' 			=> 'preco-venda'
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

	$q_tab = query("select id_tab_preco_erp from is_tab_preco");
	$tabelas = "";
	while ($a_tab = farray($q_tab)) {
	   $tabelas .= "'".$a_tab["id_tab_preco_erp"]."',";
	}
	$tabelas = substr($tabelas,0,strlen($tabelas)-1);


  $sql = 'select * from '.$tabela_erp.' where "nr-tabpre" in ('.$tabelas.')';

  echo "Buscando Registros ".date("H:i:s").'<br>';
  $q_erp = odbc_exec($cnx1,$sql);

  $u = 0; $i= 0;

  while ($a_erp = odbc_fetch_array($q_erp) ) {
      $importa = true;

                $sql = 'SELECT numreg FROM is_tab_preco WHERE id_tab_preco_erp = \''.$a_erp[$campo_chave_erp].'\'';
                                //echo '<hr>',$sql,'<hr>';
                                $qry = query($sql);
                                $nrows = numrows($qry);
                                if($nrows > 0){
                                    $ar_sql = farray($qry);
                                    $valor =  addslashes($ar_sql['numreg']);
                                } else {
                                    $valor =  'NULL';
                                }
                $sql = 'SELECT numreg FROM is_produto WHERE id_produto_erp = \''.$a_erp["it-codigo"].'\'';
                                //echo '<hr>',$sql,'<hr>';
                                $qry = query($sql);
                                $nrows = numrows($qry);
                                if($nrows > 0){
                                    $ar_sql = farray($qry);
                                    $valor2 =  addslashes($ar_sql['numreg']);
                                } else {
                                    $importa =  'NULL';
                                }
                $sql_check = "select numreg from ".$tabela_crm." where ".$campo_chave_crm." = '".$valor."' and id_produto = '".$valor2."'";
		$q_existe = farray(query($sql_check));
		$pnumreg = $q_existe["numreg"];
		echo $valor." ".$valor2." ".$a_erp[$campo_descr_erp]." - ".$q_existe["numreg"].'<br>';

		// UPDATE
		if ($pnumreg) {
			$conteudos = '';
			foreach($ar_depara as $k => $v){
                            if($k == 'id_lista_preco'){
                                $sql = 'SELECT numreg FROM is_tab_preco WHERE id_tab_preco_erp = \''.$a_erp[$v].'\'';
                                //echo '<hr>',$sql,'<hr>';
                                $qry = query($sql);
                                $nrows = numrows($qry);
                                if($nrows > 0){
                                    $ar_sql = farray($qry);
                                    $conteudos .=  $k." = '".addslashes($ar_sql['numreg'])."', ";
                                } else {
                                    $conteudos .=  $k." = NULL, ";
                                }
                            } else if($k == 'id_produto'){
                                $sql = 'SELECT numreg FROM is_produto WHERE id_produto_erp = \''.$a_erp[$v].'\'';
                                //echo '<hr>',$sql,'<hr>';
                                $qry = query($sql);
                                $nrows = numrows($qry);
                                if($nrows > 0){
                                    $ar_sql = farray($qry);
                                    $conteudos .=  $k." = '".addslashes($ar_sql['numreg'])."', ";
                                } else {
                                    $importa = false;
                                }
                            } else {
                                $conteudos .=  $k." = '".str_replace('"'," ",str_replace("'"," ",$a_erp[$v]))."', ";
                            }
			}
            $conteudos = substr($conteudos, 0, strlen($conteudos)-2);
			$sql = 'UPDATE '.$tabela_crm.' SET '.$conteudos." where numreg = '".$pnumreg."'";
                        if($importa){
                            $u = $u + 1;
                        }
		} else {
		// INSERT
			$conteudos = '';
			foreach($ar_depara as $k => $v){
                            if($k == 'id_lista_preco'){
                                $sql = 'SELECT numreg FROM is_tab_preco WHERE id_tab_preco_erp = \''.$a_erp[$v].'\'';
                                //echo '<hr>',$sql,'<hr>';
                                $qry = query($sql);
                                $nrows = numrows($qry);
                                if($nrows > 0){
                                    $ar_sql = farray($qry);
                                    $conteudos .=  "'".addslashes($ar_sql['numreg'])."', ";
                                } else {
                                    $conteudos .=  " NULL, ";
                                }
                            } else if($k == 'id_produto'){
                                $sql = 'SELECT numreg FROM is_produto WHERE id_produto_erp = \''.$a_erp[$v].'\'';
                                //echo '<hr>',$sql,'<hr>';
                                $qry = query($sql);
                                $nrows = numrows($qry);
                                if($nrows > 0){
                                    $ar_sql = farray($qry);
                                    $conteudos .=  "'".addslashes($ar_sql['numreg'])."', ";
                                } else {
                                    $importa = false;
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
                        if($importa){
                            $i = $i + 1;
                        }
		}

      if($importa){
	  $rq = query($sql); 

          if ($rq != "1") {
                      //echo $sql;
          }
      }


  }



/*===========================================================================================================*/
// Fecha Conexões 
/*===========================================================================================================*/
  
  odbc_close( $cnx1 );

  echo 'Fim do Processamento : Total'.($u+$i).' Inclusões : '.$i.' Atualizações : '.$u.' '.date("H:i:s");
?>