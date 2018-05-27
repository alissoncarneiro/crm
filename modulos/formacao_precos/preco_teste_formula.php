<?
  @header("Content-Type: text/html;  charset=ISO-8859-1",true);
  require_once("../../conecta.php");
  require_once("../../funcoes.php");
  
  $numreg = $_GET["pnumreg"];
  
  $a_formula = farray(query("select * from  is_preco_formula where numreg = '".$numreg."'"));

  $custo =  $a_formula["custo_teste"]*1;
  $txtFormula =  str_replace('<br />',chr(13),$a_formula["formula"]);

  echo "Teste da Formula : ".$a_formula["nome_formula"]."<br>";
  echo "Usando Custo de Teste : ".$custo."<br>";

  eval($txtFormula);

  echo "Resultado : ".number_format($resultado,2,".",",")."<br>";



?>


