<?
@session_start();

$id_usuario = $_POST["edtid_usuario"];
$data_ini = $_POST["edtdtini"];
$data_fim = $_POST["edtdtfim"];

header("Content-type: application/x-msdownload");
header("Content-type: application/ms-excel");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=despesas_".$id_usuario."_".date("Ymdhis").".xls");
header("Pragma: no-cache");
header("Expires: 0");

require_once("../../conecta.php");
require_once("../../functions.php");

$dias = array("Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado");

$dt_ini = substr($data_ini,6,4).'-'.substr($data_ini,3,2).'-'.substr($data_ini,0,2);
$dt_fim = substr($data_fim,6,4).'-'.substr($data_fim,3,2).'-'.substr($data_fim,0,2);

$a_usuario = farray(query("select nome_usuario, dados_deposito from is_usuarios where id_usuario = '".$id_usuario."'"));

echo '<table border=1>';
echo '<tr><td bgcolor="#dae8f4" colspan=16>Relatório de Despesas - '.$data_ini.' a '.$data_fim.'</td></tr>';
echo '<tr><td bgcolor="#dae8f4" colspan=16>Nome Completo : '.$a_usuario["nome_usuario"].' - Dados Bancários : '.$a_usuario["dados_deposito"].'</td></tr>';
echo '<tr>';
echo '<td bgcolor="#dae8f4">Dia da Semana</td>';
echo '<td bgcolor="#dae8f4">Data</td>';
echo '<td bgcolor="#dae8f4">Hr.Início</td>';
echo '<td bgcolor="#dae8f4">Hr.Fim</td>';
echo '<td bgcolor="#dae8f4">Trajeto Ida</td>';
echo '<td bgcolor="#dae8f4">Trajeto Volta</td>';
echo '<td bgcolor="#dae8f4">KM</td>';
echo '<td bgcolor="#dae8f4">Vl.KM</td>';
echo '<td bgcolor="#dae8f4">Estacion.</td>';
echo '<td bgcolor="#dae8f4">Pedágio</td>';
echo '<td bgcolor="#dae8f4">Aliment.</td>';
echo '<td bgcolor="#dae8f4">Vl.Outros</td>';
echo '<td bgcolor="#dae8f4">Outros</td>';
echo '<td bgcolor="#dae8f4">Vl.Total</td>';
echo '<td bgcolor="#dae8f4">Empresa</td>';
echo '<td bgcolor="#dae8f4">Assunto</td>';
echo '</tr>';


$q_ativ = query("select * from is_atividades where id_tp_atividade in ('VTEC','INT') and id_usuario_resp = '$id_usuario' and dt_prev_fim between '$dt_ini' and '$dt_fim' order by dt_prev_fim, hr_inicio");

$total = 0;
$km_total = 0;
$vl_km_total = 0;

while ($a_ativ = farray($q_ativ)) {
    $a_desp = farray(query("select * from is_ativ_despesa where id_atividade = '".$a_ativ["id_atividade"]."'"));
    if($a_desp["id_atividade"]) {
      $a_ida = farray(query("select nome_trajeto from is_tabela_km where id_trajeto = '".$a_desp["id_trajeto_ida"]."'"));
      $a_volta = farray(query("select nome_trajeto from is_tabela_km where id_trajeto = '".$a_desp["id_trajeto_volta"]."'"));

      $a_empr = farray(query("select * from is_pessoas where id_pessoa = '".$a_ativ["id_pessoa"]."'"));
      $local = $a_empr["fantasia_apelido"];
      $d_semana = $dias[date("w",strtotime($a_ativ["dt_prev_fim"]))];
      $data = substr($a_ativ["dt_prev_fim"],8,2).'/'.substr($a_ativ["dt_prev_fim"],5,2).'/'.substr($a_ativ["dt_prev_fim"],0,4);

      echo '<tr>';
      echo '<td>'.$d_semana .'</td>';
      echo '<td>'.$data.'</td>';
      echo '<td>'.$a_ativ["hr_inicio"].'</td>';
      echo '<td>'.$a_ativ["hr_prev_fim"].'</td>';
      echo '<td>'.$a_ida["nome_trajeto"].'</td>';
      echo '<td>'.$a_volta["nome_trajeto"].'</td>';
      echo '<td>'.number_format($a_desp["qt_km"],1,',','.').'</td>';
      echo '<td>'.number_format($a_desp["vl_km"],2,',','.').'</td>';
      echo '<td>'.number_format(($a_desp["vl_estac"]*1),2,',','.').'</td>';
      echo '<td>'.number_format(($a_desp["vl_pedagio"]*1),2,',','.').'</td>';
      echo '<td>'.number_format(($a_desp["vl_aliment"]*1)+($a_desp["vl_aliment2"]*1),2,',','.').'</td>';
      echo '<td>'.number_format(($a_desp["vl_outros"]*1),2,',','.').'</td>';
      echo '<td>'.$a_desp["obs"].'</td>';
      echo '<td>'.number_format($a_desp["vl_total"],2,',','.').'</td>';
      echo '<td>'.$local.'</td>';
      echo '<td>'.$a_ativ["assunto"].'</td>';
      echo '</tr>';
      $total += ($a_desp["vl_total"]*1);
      $km_total += ($a_desp["qt_km"]*1);
      $vl_km_total += ($a_desp["vl_km"]*1);
    }
}

$q_visitas = farray(query("select count(distinct dt_prev_fim) as total from is_atividades where id_tp_atividade = 'VTEC' and id_usuario_resp = '$id_usuario' and dt_prev_fim between '$dt_ini' and '$dt_fim'"));
$tot_visitas = $q_visitas["total"]*2;

echo '<tr>';
echo '<td bgcolor="#dae8f4">Total</td>';
echo '<td bgcolor="#dae8f4"></td>';
echo '<td bgcolor="#dae8f4"></td>';
echo '<td bgcolor="#dae8f4"></td>';
echo '<td bgcolor="#dae8f4"></td>';
echo '<td bgcolor="#dae8f4"></td>';
echo '<td bgcolor="#dae8f4">'.number_format($km_total,1,',','.').'</td>';
echo '<td bgcolor="#dae8f4">'.number_format($vl_km_total,2,',','.').'</td>';
echo '<td bgcolor="#dae8f4"></td>';
echo '<td bgcolor="#dae8f4"></td>';
echo '<td bgcolor="#dae8f4"></td>';
echo '<td bgcolor="#dae8f4"></td>';
echo '<td bgcolor="#dae8f4"></td>';
echo '<td bgcolor="#FFFF00"><b>'.number_format($total,2,',','.').'</b></td>';
echo '<td bgcolor="#dae8f4"></td>';
echo '<td bgcolor="#dae8f4"></td>';
echo '</tr>';
echo '</table>';



?>




