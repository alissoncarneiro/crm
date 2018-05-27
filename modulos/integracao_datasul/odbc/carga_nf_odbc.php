<?php
echo "*============================================================*<br>";
echo "Carga de NFs Datasul ERP via ODBC<br>";
echo "*============================================================*<br>";

require("../../conecta.php");
include "../../funcoes.php";

// Carregando Parâmetros
$ap = farray(query("select * from is_dm_param"));

$mov2dis = $ap["odbc_nf"];
$ems2adm = $ap["odbc_emit"];
$ems2dis = $ap["odbc_canal"];
$ems2ind = $ap["odbc_item"];
$ems2uni = $ap["odbc_moeda"];
$id_moeda = $ap["id_moeda"];

$nat_opers = "'".str_replace(",","','",str_replace(" ","",$ap["nat_opers"]))."'";
$dt_base = $ap["dt_base"];
$dt_base_fim = $ap["dt_base_fim"];

// Excluir qualquer NF fora nas Naturezas de Operacao parametrizadas ou com data de emissao superior a que sera feita a importacao
query("delete from is_dm_notas where not nat_operacao in (".$nat_opers.")");
query("delete from is_dm_notas where dt_emis_nota >= '".$dt_base."' and dt_emis_nota <= '".$dt_base_fim."'");

$id_usuario = 'IMPORT';
//Conecta com os bancos ODBC
$cnx_nf = odbc_connect($mov2dis,"sysprogress","sysprogress") or die("Erro na conexão com o Database"); 
$cnx_emit = odbc_connect($ems2adm,"sysprogress","sysprogress") or die("Erro na conexão com o Database"); 
$cnx_canal = odbc_connect($ems2dis,"sysprogress","sysprogress") or die("Erro na conexão com o Database"); 
$cnx_item = odbc_connect($ems2ind,"sysprogress","sysprogress") or die("Erro na conexão com o Database"); 
$cnx_moeda = odbc_connect($ems2uni,"sysprogress","sysprogress") or die("Erro na conexão com o Database"); 
/*===========================================================================================================*/
// Importa NFs
/*===========================================================================================================*/
echo 'Importando Itens de Nota Fiscal : <br>';

$sql = 'select 
"cod-estabel", "serie", "nr-nota-fis", "nr-seq-fat","it-codigo","peso-bruto","qt-faturada", "un-fatur","vl-preuni","vl-tot-item", "vl-merc-sicm","nat-operacao", "dt-emis-nota", "nr-pedcli", "nr-pedido","cd-emitente","nome-ab-cli", "ind-sit-nota" from pub."it-nota-fisc" where "dt-emis-nota" >= '."'".$dt_base."' and ".'"dt-emis-nota"'." <= "."'".$dt_base_fim."' and ".'"nat-operacao" in ('.$nat_opers.') order by  "cod-estabel", "serie", "nr-nota-fis", "nr-seq-fat","it-codigo"';

  echo "Buscando NFs ".date("H:i:s").'<br>';
  $q_it_nf = odbc_exec($cnx_nf,$sql); 
  $nf_refer = "";

  while ($a_it_nf = odbc_fetch_array($q_it_nf) ) {

      // Se mudou a NF
	  if ($nf_refer != $a_it_nf["nr-nota-fis"]) {
	      echo $a_it_nf["nr-nota-fis"].' - '.$nome_emitente." ".$a_it_nf["dt-emis-nota"].' '.date("H:i:s").'<br>';

		  $nf_refer = $a_it_nf["nr-nota-fis"];

		  // Pesquisa o Cliente e outras tabelas relacionadas
		  $a_emit = odbc_fetch_array(odbc_exec($cnx_emit,'select "nome-emit", "natureza", "cgc", "atividade", "pais", "estado", "cidade", "nome-mic-reg", "cod-gr-cli", "cod-canal-venda","cod-rep" from pub."emitente" where "cod-emitente" = '."'".$a_it_nf["cd-emitente"]."'")); 
		  $nome_canal = "";
		  $nome_grupo = "";
		  $nome_repr = "";
		  $cod_repr = "";
		  $natureza = $a_emit["natureza"];
		  $nome_emitente = str_replace('"', " ",str_replace("'", " ",$a_emit["nome-emit"]));
		  $nome_abrev = str_replace('"', " ",str_replace("'", " ",$a_it_nf["nome-ab-cli"]));
		  $cnpj = $a_emit["cgc"];
		  $nome_ramo = str_replace('"', " ",str_replace("'", " ",$a_emit["atividade"]));
		  $nome_pais = str_replace('"', " ",str_replace("'", " ",$a_emit["pais"]));
		  $nome_estado = str_replace('"', " ",str_replace("'", " ",$a_emit["estado"]));
		  $nome_cidade = str_replace('"', " ",str_replace("'", " ",$a_emit["cidade"]));
		  $micro_regiao = str_replace('"', " ",str_replace("'", " ",$a_emit["nome-mic-reg"]));

		  if ($a_emit["cod-gr-cli"]) { 
			  $a_gr_cli = odbc_fetch_array(odbc_exec($cnx_emit,'select "descricao" from pub."gr-cli" where "cod-gr-cli" = '."'".$a_emit["cod-gr-cli"]."'")); 
			  $nome_grupo = str_replace('"', " ",str_replace("'", " ",$a_gr_cli["descricao"]));
		  }

		  $a_nf_cad = odbc_fetch_array(odbc_exec($cnx_nf,'select "cod-rep" from pub."nota-fiscal" where "cod-estabel" = '."'".$a_it_nf["cod-estabel"]."' and ".'"nr-nota-fis" = '."'".$a_it_nf["nr-nota-fis"]."'")); 

		  if ($a_nf_cad["cod-rep"]) { 
			  $a_repr = odbc_fetch_array(odbc_exec($cnx_emit,'select "nome" from pub."repres" where "cod-rep" = '."'".$a_nf_cad["cod-rep"]."'")); 
			  $nome_repr = str_replace('"', " ",str_replace("'", " ",$a_repr["nome"]));
			  $cod_repr = $a_nf_cad["cod-rep"];
		  }

		  if ($a_emit["cod-canal-venda"]) { 
			  $a_canal = odbc_fetch_array(odbc_exec($cnx_canal,'select "descricao" from pub."canal-venda" where "cod-canal-venda" = '."'".$a_emit["cod-canal-venda"]."'")); 
			  $nome_canal = str_replace('"', " ",str_replace("'", " ",$a_canal["descricao"]));
		  }

		  //moedas
		  $dt_refer = $a_it_nf["dt-emis-nota"];
		  $a_moeda = odbc_fetch_array(odbc_exec($cnx_moeda,"SELECT cotacao FROM pub.cotacao WHERE \"mo-codigo\"='".$id_moeda."' AND \"ano-periodo\" = '".substr($dt_refer,0,4).substr($dt_refer,5,2)."'"));
		  $str_cotacoes = $a_moeda['cotacao'];
		  $array_cotacoes 	= explode(";",$str_cotacoes);
		  $ind_array_cotacoes = (substr($dt_refer,8,2)*1)-1;

		  $cotacao_dia_anterior = $array_cotacoes[$ind_array_cotacoes]*1;
		  if ($cotacao_dia_anterior <= 0) { $cotacao_dia_anterior = 1; }

	  }

	  $nome_familia = "";
	  $nome_familia_com = "";
	  $it_nome = "";
	  $linha = $a_it_nf["qt-faturada"];
	  $aqtde = explode(';',$linha);
  	  $vl_tot_item_us = $a_it_nf["vl-tot-item"] / $cotacao_dia_anterior;
	  $vl_merc_sicm_us = $a_it_nf["vl-merc-sicm"] / $cotacao_dia_anterior;

	  if ($a_it_nf["it-codigo"]) { 
		  $a_item = odbc_fetch_array(odbc_exec($cnx_item,'select "desc-item", "fm-codigo", "fm-cod-com" from pub."item" where "it-codigo" = '."'".$a_it_nf["it-codigo"]."'")); 
		  $it_nome = str_replace('"', " ",str_replace("'", " ",$a_item["desc-item"]));
		  $nome_familia = str_replace('"', " ",str_replace("'", " ",$a_item["fm-codigo"]));

		  $a_fam = odbc_fetch_array(odbc_exec($cnx_item,'select "descricao" from pub."familia" where "fm-codigo" = '."'".$a_item["fm-codigo"]."'")); 
		  $nome_familia = str_replace('"', " ",str_replace("'", " ",$a_fam["descricao"]));

		  $a_fam_com = odbc_fetch_array(odbc_exec($cnx_canal,'select "descricao" from pub."fam-comerc" where "fm-cod-com" = '."'".$a_item["fm-cod-com"]."'")); 
		  $nome_familia_com = str_replace('"', " ",str_replace("'", " ",$a_fam_com["descricao"]));
	  }


	  $sql_insert = 'INSERT INTO is_dm_notas ( dt_cadastro , hr_cadastro , id_usuario_cad , dt_alteracao , hr_alteracao , id_usuario_alt , cod_estabel , serie , dt_emis_nota , nr_nota_fis , nr_seq_fat , nr_pedido , nr_pedcli , nome_familia , it_codigo , it_nome , cd_emitente , nome_emitente , nome_fantasia , nome_grupo , nome_canal , nome_ramo , peso_bruto , qt_faturada , vl_tot_item , vl_merc_sicm , nat_operacao , vl_tot_item_us , vl_merc_sicm_us , nome_familia_com , cnpj , natureza , nome_pais , nome_estado , nome_cidade, nome_regiao, cod_repr, nome_repr ) VALUES (';

	  $sql_insert .= "'".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','";
	  $sql_insert .= $a_it_nf["cod-estabel"]."','".$a_it_nf["serie"]."','".$a_it_nf["dt-emis-nota"]."','".$a_it_nf["nr-nota-fis"]."','".$a_it_nf["nr-seq-fat"]."','".$a_it_nf["nr-pedido"]."','".$a_it_nf["nr-pedcli"]."','".$nome_familia."','".$a_it_nf["it-codigo"]."','".$it_nome."','".$a_it_nf["cd-emitente"]."','".$nome_emitente."','".$nome_abrev."','".$nome_grupo."','".$nome_canal."','".$nome_ramo."','".$a_it_nf["peso-bruto"]."','".$aqtde[0]."','".$a_it_nf["vl-tot-item"]."','".$a_it_nf["vl-merc-sicm"]."','".$a_it_nf["nat-operacao"]."','".$vl_tot_item_us."','".$vl_merc_sicm_us."','".$nome_familia_com."','".$cnpj."','".$natureza."','".$nome_pais."','".$nome_estado."','".$nome_cidade."','".$nome_regiao."','".$cod_repr."','".$nome_repr."')";

	  $rq = query(  TextoBD("mysql",$sql_insert) ); 

      if ($rq != "1") {
		  echo $sql_insert;
	  }


  }

/*===========================================================================================================*/
// Excluir NFs Canceladas
/*===========================================================================================================*/
  echo 'Excluindo Notas Canceladas : '.date("H:i:s").'<br>';
  $sql_canc = 'select "cod-estabel", "nr-nota-fis", "nr-seq-fat", "dt-cancela", "it-codigo" from pub."it-nota-fisc" where "dt-cancela" >= '."'1980-01-01'";
  $q_canc = odbc_exec($cnx_nf,$sql_canc); 
  while ($a_canc = odbc_fetch_array($q_canc) ) {
	  query("delete from is_dm_notas where cod_estabel = '".$a_canc["cod-estabel"]."' and nr_nota_fis = '".$a_canc["nr-nota-fis"]."' and it_codigo = '".$a_canc["it-codigo"]."'");
	  echo $a_canc["nr-nota-fis"].' - '.$a_canc["it-codigo"].'<br>';
  }

/*===========================================================================================================*/
// Excluir Devoluções
/*===========================================================================================================*/
  echo 'Processando Devoluções : '.date("H:i:s").'<br>';

  $sql_dev = 'select "nr-nota-fis", "nr-sequencia", "qt-devolvida", "vl-devol", "dt-devol"  from pub."devol-cli"';
  $q_dev = odbc_exec($cnx_nf,$sql_dev); 
  while ($a_dev = odbc_fetch_array($q_dev) ) {
	  query("delete from is_dm_notas where nr_nota_fis = '".$a_dev["nr-nota-fis"]."' and nr_seq_fat = '".$a_dev["nr-sequencia"]."'");
	  echo $a_dev["nr-nota-fis"].'<br>';
  }


/*===========================================================================================================*/
// Fecha Conexões e atualiza data da importacao
/*===========================================================================================================*/
  
  odbc_close( $cnx_nf ); 
  odbc_close( $cnx_emit ); 
  odbc_close( $cnx_canal ); 
  odbc_close( $cnx_item ); 
  odbc_close( $cnx_moeda ); 

  query("update is_dm_param set dt_base = '".date("Y-m-d")."', dt_base_fim = '".date("Y-m-d")."'");

  echo 'Fim do Processamento : '.date("H:i:s");


?>