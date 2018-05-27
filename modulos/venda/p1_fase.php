<?php

/*
 * p1_fase.php
 * Autor: Alex
 * 11/12/2012 09:42:31
 */
require("../../conecta.php");
include "../../functions.php";

$XML = '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.'>'."\n";
$XML .= '<resposta>'."\n";
$XML .= "\t".'<options>'."\n";

$IdCiclo = $_POST['id_ciclo'];

$XML .= "\t\t<option value=''></option>\n";
$QryCiclos = query("SELECT numreg,nome_opor_fase FROM is_opor_fase WHERE numreg IN(SELECT id_opor_fase FROM is_opor_ciclo_fase WHERE id_opor_ciclo = '".TrataApostrofoBD($IdCiclo)."') ORDER BY nome_opor_fase");
while($ArCiclos = farray($QryCiclos)){
    $XML .= "\t\t<option value='".$ArCiclos['numreg']."'>".$ArCiclos['nome_opor_fase']."</option>\n";
}
$XML .= "\t".'</options>'."\n";
$XML .= '</resposta>'."\n";
header("Content-Type: text/xml");
echo $XML;