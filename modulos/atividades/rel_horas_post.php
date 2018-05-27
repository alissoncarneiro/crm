<?
@session_start();

$id_usuario = $_POST["edtid_usuario"];
$data_ini = $_POST["edtdtini"];
$data_fim = $_POST["edtdtfim"];

$detalhado = $_POST["edtdetalhado"];

header("Content-type: application/x-msdownload");
header("Content-type: application/ms-excel");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=horas_".$id_usuario."_".date("Ymdhis").".xls");
header("Pragma: no-cache");
header("Expires: 0");

require_once("../../conecta.php");
require_once("../../functions.php");

$dias = array("Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado");

$dt_ini = substr($data_ini,6,4).'-'.substr($data_ini,3,2).'-'.substr($data_ini,0,2);
$dt_fim = substr($data_fim,6,4).'-'.substr($data_fim,3,2).'-'.substr($data_fim,0,2);

$a_usuario = farray(query("select nome_usuario from is_usuarios where id_usuario = '".$id_usuario."'"));

echo '<table border=1>';
echo '<tr><td bgcolor="#dae8f4" colspan=9>Relatório de Horas - '.$data_ini.' a '.$data_fim.'</td></tr>';
echo '<tr><td bgcolor="#dae8f4" colspan=9>Nome Completo : '.$a_usuario["nome_usuario"].'</td></tr>';
echo '<tr>';
echo '<td bgcolor="#dae8f4">Dia da Semana</td>';
echo '<td bgcolor="#dae8f4">Data</td>';
echo '<td bgcolor="#dae8f4">Hr.Início</td>';
echo '<td bgcolor="#dae8f4">Hr.Fim</td>';
echo '<td bgcolor="#dae8f4">Intervalo</td>';
echo '<td bgcolor="#dae8f4">Qtde de Horas</td>';
echo '<td bgcolor="#dae8f4">Local</td>';
echo '<td bgcolor="#dae8f4">Atividade</td>';
echo '<td bgcolor="#dae8f4">Assunto</td>';
echo '</tr>';


$q_ativ = query("select t1.*, t2.nome_tp_atividade from is_atividades t1, is_tp_atividades t2 where t1.id_formulario_workflow is null and t1.id_tp_atividade = t2.id_tp_atividade and t2.sn_relat_horas = 'S' and t1.id_usuario_resp = '$id_usuario' and t1.dt_prev_fim between '$dt_ini' and '$dt_fim' order by t1.dt_prev_fim, t1.hr_inicio");

$total_hora = 0;
if($detalhado == "S") { $color = 'bgcolor="#D0D0D0"'; } else { $color = ""; }

while ($a_ativ = farray($q_ativ)) {
    $a_empr = farray(query("select * from is_pessoas where id_pessoa = '".$a_ativ["id_pessoa"]."'"));
    $local = $a_empr["fantasia_apelido"];
    $d_semana = $dias[date("w",strtotime($a_ativ["dt_prev_fim"]))];
    $data = substr($a_ativ["dt_prev_fim"],8,2).'/'.substr($a_ativ["dt_prev_fim"],5,2).'/'.substr($a_ativ["dt_prev_fim"],0,4);
    $qt_intervalo = $a_ativ["tempo_intervalo"]*1;
    $qt_horas = (diferenca_hr($a_ativ["hr_inicio"],$a_ativ["hr_prev_fim"],'S',1)*1) - $qt_intervalo;

    echo '<tr>';
    echo '<td '.$color.'>'.$d_semana .'</td>';
    echo '<td '.$color.'>'.$data.'</td>';
    echo '<td '.$color.'>'.$a_ativ["hr_inicio"].'</td>';
    echo '<td '.$color.'>'.$a_ativ["hr_prev_fim"].'</td>';
    echo '<td '.$color.'>'.str_replace(".",",",$qt_intervalo).'</td>';
    echo '<td '.$color.'>'.str_replace(".",",",$qt_horas).'</td>';
    echo '<td '.$color.'>'.$local.'</td>';
    echo '<td '.$color.'>'.$a_ativ["nome_tp_atividade"].'</td>';
    echo '<td '.$color.'>'.$a_ativ["assunto"].'</td>';
    echo '</tr>';
    if ($detalhado == 'S') {
       echo '<tr><td colspan=9>Descrição : '.$a_ativ["obs"].'</td></tr>';
    }
    $total_hora += $qt_horas;

}

$q_visitas = farray(query("select count(distinct dt_prev_fim) as total from is_atividades where (id_tp_atividade = 'VIS' or id_tp_atividade = 'VTEC') and id_usuario_resp = '$id_usuario' and dt_prev_fim between '$dt_ini' and '$dt_fim'"));
$tot_visitas = $q_visitas["total"]*2;

echo '<tr><td  bgcolor="#dae8f4" colspan=9>Total : '.str_replace(".",",",$total_hora).'h + '.$tot_visitas.'h (referente a '.($tot_visitas/2).' dia(s) de visita) = '.str_replace(".",",",($total_hora+$tot_visitas)).'  </td></tr>';
echo '</table>';



?>




