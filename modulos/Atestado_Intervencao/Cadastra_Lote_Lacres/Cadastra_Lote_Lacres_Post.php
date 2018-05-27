<?php

session_start();

require_once("../../../conecta.php");
require_once("../../../funcoes.php");

//Recebe os Campos
$n_lote_inicial = $_POST['lote_inicio'];
$n_lote_final = $_POST['lote_final'];
$cnpj_lacres = $_POST['cnpj_lacres'];

//Verifica se um dos campos não está vazio ( ou ambos )
if($n_lote_inicial == '' || $n_lote_final == ''){
    echo '<script type="text/javascript"> alert("Você deve informar um Número de Lacre de Início e um número de Lacre final"); window.history.go(-1); </script>';
    exit;
}

//Verifica se o Inicio não é Maior que o Fim
if($n_lote_inicial > $n_lote_final){
    echo '<script type="text/javascript"> alert("O Número de Lacre de Início não pode ser maior que o número de Lacre final"); window.history.go(-1); </script>';
    exit;
}

$ar_existem = array();

// Faz o Loop para pegar todos os valores e insere-os no Banco de Dados
for($i = $n_lote_inicial; $i <= $n_lote_final; $i++){

    $verifica = mysql_query("SELECT count(*) as cnt FROM is_lacres WHERE n_lacre = '".$i."' ") or die(mysql_error());
    $retorno = mysql_fetch_array($verifica);

    if($retorno['cnt'] >= 1){

        $ar_existem[] = $i;
    }else{

        $id_novo = mysql_fetch_array(mysql_query("SHOW TABLE STATUS LIKE 'is_lacres'"));
        $qr_cad_lacre = mysql_query("INSERT INTO is_lacres (n_lacre, id_lacre, cnpj_lacre) VALUES('".$i."', '".$id_novo['Auto_increment']."', '".$cnpj_lacres."') ") or die(mysql_error());
        $msg = "Os Lacres foram cadastrados com Sucesso \\n\\n";
    }
}

if(count($ar_existem) >= 1){
    $msg .= "Os seguintes Lacres já existem: \\n".implode("-",$ar_existem);
}

//Verifica se existe a variavel $msg
if(@$msg != ""){
    echo '<script type="text/javascript">
                        alert("'.$msg.'");
                        window.opener.exibe_programa(\'gera_cad_lista.php?pfuncao=cad_lacres\');
                        window.close();
			  </script>';
}
?>