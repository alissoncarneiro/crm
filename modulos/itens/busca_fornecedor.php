<?php
require("../../conecta.php");

if($_POST['tabela']=='f') {
    $ar = mysql_fetch_array(mysql_query("SELECT * FROM is_fornecedores WHERE codigo = '".$_POST['edtcodigo']."'"));
    echo $ar['nome'];
} else if($_POST['tabela']=='pro') {
    $ar = mysql_fetch_array(mysql_query("SELECT * FROM item WHERE `it_codigo` = '".$_POST['edtcodigo']."'"));
    if($ar['descricao']=="") {
        $_POST['id_produto'] = substr($_POST['edtcodigo'],2,4);
        $_POST['qtd'] = (substr($_POST['edtcodigo'],7,6)*1)/100;
        $ar = mysql_fetch_array(mysql_query("SELECT * FROM item WHERE `it_codigo` = '".$_POST['edtcodigo']."'"));
        echo "barras";
    } else {
        echo $ar['descricao'];
    }
}else if($_POST['tabela']=='cli') {
    $ar = mysql_fetch_array(mysql_query("SELECT * FROM emitente WHERE `cod_emitente` = '".$_POST['edtcodigo']."'"));
    if($ar['bloqueado']=="S") {
        echo "b";
    } else {
        echo $ar['nome_emit'];
    }
}else {
    $ar = mysql_fetch_array(mysql_query("SELECT * FROM is_matprima WHERE codigo = '".$_POST['edtcodigo']."'"));
    echo $ar['descricao'];
}

?>