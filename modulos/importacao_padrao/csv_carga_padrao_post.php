
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: OASIS :: </title>
<link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../../estilos_css/cadastro.css">
<link rel="stylesheet" type="text/css" media="all" href="../../estilos_css/calendar-blue.css" title="win2k-cold-1" />
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<center>
<div id="principal_detalhes">
   <div id="topo_detalhes">
   <div id="logo_empresa"></div>
   <!--logo -->
   </div><!--topo -->
   <div id="conteudo_detalhes">
   <span class="tit_detalhes">Carga de Dados</span><br><br>
<?
  @session_start();
  @header("Content-Type: text/html;  charset=ISO-8859-1",true);
  @header ("Pragma: no-cache");
  @header("Cache-Control: no-store, no-cache, must-revalidate");
  @header("Cache-Control: post-check=0, pre-check=0", false);

include "../../conecta.php";
include "../../funcoes.php";

/* ----------------------------------------------------------------------------
RECEBE E COPIA O ARQUIVO
-----------------------------------------------------------------------------*/

$temp = $_FILES['edtarq']["tmp_name"];
$nome_arquivo = $_FILES['edtarq']["name"];
$size = $_FILES['edtarq']["size"];
$type = $_FILES['edtarq']["type"];

if ($nome_arquivo) {
    copy($temp,$caminho_arquivos."carga.csv");
}


$conteiner = file($caminho_arquivos.'carga.csv');
$file = count($conteiner);

$outros = trim($_POST["edtoutros"]);

if ($outros) {
	$nome_tabela = $outros;
} else {
	$nome_tabela = $_POST["edttabela"];
}

$id_usuario = $_SESSION["id_usuario"];

$registro = '';
$f = -1;
$contador = 0;
for($i=0;$i < $file; $i++ ){
	$a = explode(';',$conteiner[$i]);
	$b = count($a);

	if ($i==0) {
	    // se for a primeira linha deve pegar os nomes dos campos
		$campos = "";
		for($j=0;$j<$b; $j++){
			$campos .= $a[$j].',';
		}
		$campos = substr($campos,0,strlen($campos)-1);
	} else {
	    // senao deve montar os conteúdos
		$conteudos = "";
		for($j=0;$j<$b; $j++){
			if (empty($a[$j])) {
				$conteudos .= "null,";
			} else {
				$conteudos .= "'".RemoveAcentos(str_replace('"'," ",str_replace("'","´",$a[$j])))."',";
			}
		}
		$conteudos = substr($conteudos,0,strlen($conteudos)-1);
		$sql = "insert into ".$nome_tabela."(".$campos.") values (".$conteudos.")";

		$rq = query( TextoBD("mysql",$sql) );
	    if ($rq != 1) {
			echo " ERRO : ".$sql."<br>";

		} else {
		  $contador++;
		}
	}
}

/* ----------------------------------------------------------------------------
************************         FIM              *****************************
-----------------------------------------------------------------------------*/
echo "<br>Total de Registros Importados : ".$contador."<br>";
?>
<br>
<input type="button" value="Fechar"  class="botao_form"  onclick="javascript:window.close();">
</body>
</html>