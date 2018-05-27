<?php
echo "*============================================================*<br>";
echo "Atualizar Score <br>";
echo "*============================================================*<br>";

require("../../conecta.php");
include "../../funcoes.php";

// Carregando Parâmetros
$ap = farray(query("select * from is_dm_param"));

$qtde_dias = $ap["qtde_dias_rfv"];

$data_base = soma_dias($qtde_dias*-1);

echo "Data Base = ".$data_base."<br>";
/*===========================================================================================================*/
// Importa Clientes
/*===========================================================================================================*/

  echo "Buscando Registros ".date("H:i:s").'<br>';
  query("UPDATE is_pessoas SET score = '0' where id_relac = '4'");

  $u = 0; $i= 0;

  $cli = "@";
  $q_rfv = query("select cd_emitente, it_codigo, sum(qt_faturada) as qtde from is_dm_notas where dt_emis_nota >= '".$data_base."' group by cd_emitente, it_codigo order by cd_emitente"); 

  while ($a_rfv = farray($q_rfv) ) {

	  if ( $cli == "@" ) { $cli = $a_rfv["cd_emitente"]; }

	  $a_fator = farray(query("select pontos_valor, pontos_qtde from is_produtos where id_produto = '".$a_rfv["it_codigo"]."'"));

	  $fator = $a_fator["pontos_valor"]*1; 

	  if($fator == 0) { $fator = 1; }

	  $qtde = number_format($a_rfv["qtde"]/$fator,0,".","")*1;

	  $score = $score + $qtde * $a_fator["pontos_qtde"];

	  if ( $cli <> $a_rfv["cd_emitente"]) {
		  echo $a_rfv["cd_emitente"].' '.$a_rfv["it_codigo"].'<br>';
		  $cli = $a_rfv["cd_emitente"];
		  $sql = "UPDATE is_pessoas SET score = '".$score."' where id_pessoa_erp = '".$a_rfv["cd_emitente"]."'";
		  $score = 0;
		  $i = $i + 1;
		  $rq = query(  TextoBD("mysql",$sql) ); 
		  if ($rq != "1") {
			  echo $sql;
		  }
	  }
  }



/*===========================================================================================================*/
// Fecha Conexões 
/*===========================================================================================================*/
  
  echo 'Fim do Processamento : Total'.($i).' '.date("H:i:s");




?>