<?

  require_once("conecta.php");

  $qry_atividades = farray(query("select pecahtm from is_atividade where numreg = '".$_GET["numreg"]."'"));

  echo str_replace('src=',' src=',str_replace('//arquivos','/arquivos',str_replace('<br />',' ',$qry_atividades["pecahtm"])));



?>