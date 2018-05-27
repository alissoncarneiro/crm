<?php

require_once("../../../conecta.php");
require_once("../../../funcoes.php");

//Recebe os Campos
$n_lote_inicial = $_POST['lote_inicio'];
$n_lote_final = $_POST['lote_final'];

$Data_Atual = date("d/m/Y");
$Dia_Atual = substr($Data_Atual,0,2);
$Mes_Atual = substr($Data_Atual,3,2);
$Ano_Atual = substr($Data_Atual,6,4);
$Data_Atribuicao_Tecnico = $Ano_Atual."-".$Mes_Atual."-".$Dia_Atual;

//Verifica se o Inicio não é Maior que o Fim
if($n_lote_inicial > $n_lote_final){
    echo '<script type="text/javascript"> alert("O Número de Lacre de Início não pode ser maior que o número de Lacre final"); window.history.go(-1); </script>';
    exit;
}

//Verifica se o Lacre Inicial é igual ao Lacre Final
if($n_lote_inicial == $n_lote_final){
    echo '<script type="text/javascript"> alert("O Número de Lacre Final deve ser maior que o Número de Lacre de Início"); window.history.go(-1); </script>';
    exit;
}

$update = mysql_query("UPDATE is_lacres SET data_atrib_tecnico = '".$Data_Atribuicao_Tecnico."', responsavel_lacre = '".$_POST['tecnicos']."' WHERE n_lacre between '".$n_lote_inicial."' AND '".$n_lote_final."' ");

if($update){
    $msg = "Os Lacres foram atualizados com Sucesso";
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
