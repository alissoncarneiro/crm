<?php
/*
 * replicar_atividade.php
 * Autor: Alex
 * 08/09/2011 15:48:28
 */
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('../../conecta.php');
require('../../functions.php');
require('../../classes/class.Atividade.php');
require('../../classes/class.CalculoAlocacao.php');
require('../../classes/class.CalendarioData.php');

$IdAtividade = $_POST['id_atividade'];
$QtdeAtividades = $_POST['qtde_atividades'];
$Periodicidade = $_POST['periodicidade'];

if($IdAtividade == '' || $QtdeAtividades <= 0 || $Periodicidade <= 0){
    echo 'Parâmetros informados incorretos!';
    exit;
}

$Atividade = new Atividade($IdAtividade);

if(!$Atividade->isReplicavel()){
    echo 'Este tipo de atividade não é replicável!';
    exit;
}

$ArAtividade = $Atividade->getDadosAtividade();

$ArSqlInsertAtividade = array();
foreach($ArAtividade as $Coluna => $Valor){
    if(!is_int($Coluna) && trim($Valor) != ''){
        $ArSqlInsertAtividade[$Coluna] = $Valor;
    }
}
unset($ArSqlInsertAtividade['numreg'],$ArSqlInsertAtividade['dt_real_fim'],$ArSqlInsertAtividade['hr_real_fim']);
$ArSqlInsertAtividade['id_atividade_pai'] = $IdAtividade;
$ArSqlInsertAtividade['id_situacao'] = 1;

$CalculoAlocacao = new CalculoAlocacao();
$CalculoAlocacao->setIdUsuario($ArAtividade);

$Data = substr(trim($ArAtividade['dt_inicio']),0,10);
$Data = ($Data < date("Y-m-d"))?date("Y-m-d"):$Data;

$ArrayDatasGeradas = array();

for($i=1;$i<=$QtdeAtividades;$i++){
    $Data = date("Y-m-d",strtotime($Data." + ".$Periodicidade." days"));
    $Data = $CalculoAlocacao->getProximoDiaLivre($Data);
    $ArSqlInsertAtividade['dt_inicio'] = $Data;
    $ArSqlInsertAtividade['dt_prev_fim'] = $Data;
    $SqlInsertAtividade = AutoExecuteSql(TipoBancoDados, 'is_atividade', $ArSqlInsertAtividade, 'INSERT');
    $QryInsert = query($SqlInsertAtividade);
    if(!$QryInsert){
        echo 'Erro ao gerar atividade para o dia '.dten2br($Data)."\r\n";
    }
    else{
        $ArrayDatasGeradas[] = dten2br($Data);
    }
}
$Mensagem = 'Foram geradas atividades para as datas '.implode(', ', $ArrayDatasGeradas);
echo $Mensagem;
GravaLogEvento('422', true, 'Replicação de atividades.','Atividade Nº '.$IdAtividade.'. '.$Mensagem);
?>