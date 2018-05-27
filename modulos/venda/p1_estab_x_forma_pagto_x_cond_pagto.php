<?php
/*
 * p1_estab_x_forma_pagto_X_cond_pagto.php
 * Autor: rodrigo
 * 11/07/2011 09:10:02
 */

require("../../conecta.php");
include "../../funcoes.php";
include "../../functions.php";

$XML = '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.'>'."\n";
$XML .= '<resposta>'."\n";
$XML .= "\t".'<mensagem></mensagem>'."\n";
$XML .= "\t".'<status>1</status>'."\n";
$XML .= "\t".'<options>'."\n";

$id_estabelecimento = $_POST['id_estabelecimento'];
$tipo_consulta = $_POST['tipo_consulta'];

if($tipo_consulta == 1 ){
    $XML .= "\t\t<option value=''></option>\n";
    $q_forma_pagto = query("SELECT DISTINCT id_forma_pagto from is_param_estabelecimento_x_forma_pagto WHERE id_estabelecimento = '".$id_estabelecimento."'");

    while($row_forma_pagto = farray($q_forma_pagto)){
        $row_desc_forma_pagto = farray(query("SELECT * FROM is_forma_pagto WHERE numreg = '".$row_forma_pagto['id_forma_pagto']."'"));
        $XML .= "\t\t<option value='".$row_forma_pagto['id_forma_pagto']."'>".$row_desc_forma_pagto['nome_forma_pagto']."</option>\n";
    }
}
else{    
    $id_forma_pagamento = $_POST['id_forma_pagamento'];
    $XML .= "\t\t<option value=''></option>\n";
    $q_cond_pagto = query("SELECT * FROM is_param_estabelecimento_x_forma_pagto t1
                           INNER JOIN is_cond_pagto t2
                           ON t1.id_cond_pagto = t2.numreg
                           WHERE t1.id_forma_pagto = '".$id_forma_pagamento."'
                           AND t1.id_estabelecimento = '".$id_estabelecimento."'
                           AND t2.sn_exibe_venda = 1");
    while($row_cond_pagto = farray($q_cond_pagto)){
        $XML .= "\t\t<option value='".$row_cond_pagto['id_cond_pagto']."'>".$row_cond_pagto['nome_cond_pagto']."</option>\n";
    }
}

$XML .= "\t".'</options>'."\n";
$XML .= '</resposta>'."\n";
header("Content-Type: text/xml");
echo $XML;
?>