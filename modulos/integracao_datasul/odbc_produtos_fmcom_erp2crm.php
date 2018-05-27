<?php
header('Content-Type: text/html; charset=iso-8859-1');
$odbc_c = true;
echo "*============================================================*<br>";
echo "Carga de Produtos Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

include("../../conecta.php");
include "../../funcoes.php";

// Carregando Parâmetros
$ap = farray(query("select * from is_dm_param"));

$tabela_erp = 'pub.item';
$campo_chave_erp = 'it-codigo';
$campo_descr_erp = 'desc-item';

$campo_familia = 'fm-cod-com';

$tabela_crm = 'is_produto';
$campo_chave_crm = 'id_produto_erp';

//query("delete from ".$tabela_crm);


/*===========================================================================================================*/
// Importa Clientes
/*===========================================================================================================*/
echo 'Importando Registros<br>';

$ar_depara = array(
								'id_produto_erp' 		=> 'it-codigo',
								'id_familia' 			=> 'fm-cod-com',
								'nome_produto' 			=> 'desc-item',
								//'id_uni_med' 			=> 'un',
                                                                'nome_produto_detalhado'        => 'narrativa',
								'custo_ult_ent ' 		=> 'preco-ul-ent',
								'custo_repos' 			=> 'preco-repos',
								'custo_base' 			=> 'preco-base',
								//'cod_ativo'			=> 'cod-obsoleto',
								'pct_aliq_ipi' 			=> 'aliquota-ipi'
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

  $sql = 'select distinct i."data-implant",i."it-codigo",i."'.$campo_familia.'",i."desc-item",i."un",i."preco-ul-ent",i."preco-repos",i."aliquota-ipi",i."preco-base", i."cod-obsoleto" from '.$tabela_erp.' i, pub."item-uni-estab"  f where i."it-codigo" = f."it-codigo" and f."ind-item-fat" = 1';

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
                                    if($k == 'id_familia'){
                                        $sql = 'SELECT numreg FROM is_familia_comercial WHERE id_familia_erp = \''.$a_erp[$v].'\'';
                                        //echo '<hr>',$sql,'<hr>';
                                        $qry = query($sql);
                                        $nrows = numrows($qry);
                                        if($nrows > 0){
                                            $ar_sql = farray($qry);
                                            $conteudos .=  $k." = '".addslashes($ar_sql['numreg'])."', ";
                                        } else {
                                            $conteudos .=  $k." = NULL, ";
                                        }
                                    } else {
					$conteudos .=  $k." = '".str_replace('"'," ",str_replace("'"," ",$a_erp[$v]))."', ";
                                    }
				}
				$conteudos = substr($conteudos, 0, strlen($conteudos)-2);
				$sql = 'UPDATE '.$tabela_crm.' SET '.$conteudos." where numreg = '".$pnumreg."'";
				$u = $u + 1;
			} else {
			// INSERT
				$conteudos = '';
				foreach($ar_depara as $k => $v){
                                    if($k == 'id_familia'){
                                        $sql = 'SELECT numreg FROM is_familia_comercial WHERE id_familia_erp = \''.$a_erp[$v].'\'';
                                        //echo '<hr>',$sql,'<hr>';
                                        $qry = query($sql);
                                        $nrows = numrows($qry);
                                        if($nrows > 0){
                                            $ar_sql = farray($qry);
                                            $conteudos .=  "'".addslashes($ar_sql['numreg'])."', ";
                                        } else {
                                            $conteudos .=  " NULL, ";
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
				$i = $i + 1;
			}


		  $rq = query($sql); 

		  if ($rq != "1") {
			  echo $sql;
		  }

  }



/*===========================================================================================================*/
// Fecha Conexões 
/*===========================================================================================================*/
  
  odbc_close( $cnx1 );

  echo 'Fim do Processamento : Total'.($u+$i).' Inclusões : '.$i.' Atualizações : '.$u.' '.date("H:i:s");


?>