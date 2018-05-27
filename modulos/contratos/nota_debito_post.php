<?

require_once("../../conecta.php");

$dias = array("Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado");

$id_pessoa_mes_ano = $_POST["id_pessoa"];
$numero = $_POST["numero"];

$a_param = explode("-", $id_pessoa_mes_ano);
$id_pessoa = $a_param[0];
$a_mes_ano = explode("/", $a_param[1]);
$mes = $a_mes_ano[0];
$ano = $a_mes_ano[1];

$a_empr = farray(query("select * from is_pessoa where id_pessoa = '".$id_pessoa."'"));

?>

<html>
<head>
<style type="text/css">
* {font-family: Arial; font-size:12px;}
.titulo {font-family: Arial; font-size:20px; font-weight: bold;}
.sub_titulo {font-family: Arial; font-size:16px; font-weight: bold;}
table, th, td
{
border: 1px solid black;
border-collapse: collapse; border-spacing: 1px;
}

</style>
</head>

<center><img src="../../images/logo_login.jpg" border="0"></center>
<hr>
<center><span class=titulo>NOTA DE DÉBITO - Número <?=$numero?></span></center>
<br>
<br>
<br>
<span class=sub_titulo>DEVEDOR</span>
<br>
<table class=tabela width="100%">
<tr><td>Empresa:</td><td><?=$a_empr["razao_social_nome"];?></td></tr>
<tr><td>Endereço:</td><td><?=$a_empr["endereco"].' '.$a_empr["complemento"].' - '.$a_empr["bairro"].' - '.$a_empr["cidade"].' - '.$a_empr["uf"].' - '.$a_empr["cep"];?></td></tr>
<tr><td>C.N.P.J.:</td><td><?=$a_empr["cnpj_cpf"];?></td></tr>
<tr><td>I.E.:</td><td><?=$a_empr["ie_rg"];?></td></tr>
</table>
<br>
<br>
<span class=sub_titulo>DESCRIÇÃO DOS DÉBITOS</span>
<br>
<br>
Reembolso de despesas conforme contrato: os custos com translado, hospedagem e alimentação dos serviços correrão por conta do cliente.
<br>
<br>
<table class=tabela width="100%">
<tr><td><b>Data</b></td><td><b>Despesa</b></td><td><b>Recurso</b></td><td align=right><b>Valor</b></td></tr>
<?
$a_contrato = farray(query("select * from is_contrato where id_pessoa = '".$id_pessoa."'"));
$vl_km = $a_contrato["vl_km"]*1;
$vl_max_almoco = $a_contrato["vl_almoco"]*1;
$vl_max_jantar = $a_contrato["vl_jantar"]*1;
$vl_max_pedagio = $a_contrato["vl_pedagio"]*1;
$vl_hr_interest = $a_contrato["vl_hr_interest"]*1;

$tot_nota = 0;

$q_visitas = query("select * from is_atividade where id_tp_atividade = '4' and id_pessoa = '".$id_pessoa."' and year(dt_inicio) = '$ano' and month(dt_inicio) = '$mes'");
while ($a_visitas = farray($q_visitas)) {
  $a_despesa = farray(query("select * from is_ativ_despesa where id_atividade = '".$a_visitas["numreg"]."'"));
  if ($a_despesa["id_atividade"]) {
    $data = substr($a_visitas["dt_prev_fim"],8,2).'/'.substr($a_visitas["dt_prev_fim"],5,2).'/'.substr($a_visitas["dt_prev_fim"],0,4);
    $a_km_1 = farray(query("select * from is_tabela_km where id_trajeto = '".$a_despesa["id_trajeto_ida"]."'"));
    $a_km_2 = farray(query("select * from is_tabela_km where id_trajeto = '".$a_despesa["id_trajeto_volta"]."'"));
    $a_km_orig = farray(query("select * from is_tabela_km where id_pessoa_origem = '17676' and id_pessoa_dest = '".$a_visitas["id_pessoa"]."' and unidade_cliente = '".$a_km_1["unidade_cliente"]."'"));
    $a_km_dest = farray(query("select * from is_tabela_km where id_pessoa_dest = '17676' and id_pessoa_origem = '".$a_visitas["id_pessoa"]."' and unidade_cliente = '".$a_km_2["unidade_cliente"]."'"));
    $a_usu = farray(query("select * from is_usuarios where id_usuario = '".$a_visitas["id_usuario_resp"]."'"));

    $vl_estac        = $a_despesa["vl_estac"]*1;
    $vl_pedagio      = $a_despesa["vl_pedagio"]*1;
    if ($vl_pedagio > $vl_max_pedagio) { $vl_pedagio = $vl_max_pedagio;}
    $vl_aliment      = $a_despesa["vl_aliment"]*1;
    if ($vl_aliment > $vl_max_almoco) { $vl_aliment = $vl_max_almoco;}
    $vl_aliment2     = $a_despesa["vl_aliment2"]*1;
    if ($vl_aliment2 > $vl_max_jantar) { $vl_aliment2 = $vl_max_jantar;}
    $vl_outros       =  $a_despesa["vl_outros"]*1;
    $obs             =  $a_despesa["obs"];
    $tot_hr_interest  =  ($a_despesa["qt_hr_interest"]*1)*$vl_hr_interest;
    
    echo "<tr><td>".$data."</td><td>".$a_km_orig["nome_trajeto"]." - ".$a_km_orig["km"]."km</td><td>".$a_usu["nome_usuario"]."</td><td align=right> R$ ".number_format(($a_km_orig["km"]*$vl_km),2,',','.')."</td></tr>";
    echo "<tr><td>".$data."</td><td>".$a_km_dest["nome_trajeto"]." - ".$a_km_dest["km"]."km</td><td>".$a_usu["nome_usuario"]."</td><td align=right> R$ ".number_format(($a_km_dest["km"]*$vl_km),2,',','.')."</td></tr>";
    $tot_nota += (($a_km_orig["km"]*$vl_km)+($a_km_dest["km"]*$vl_km));

    if ($vl_estac>0) {
       echo "<tr><td>".$data."</td><td>Estacionamento</td><td>".$a_usu["nome_usuario"]."</td><td align=right> R$ ".number_format(($vl_estac),2,',','.')."</td></tr>";
       $tot_nota += $vl_estac;
    }
    if ($vl_pedagio>0) {
       echo "<tr><td>".$data."</td><td>Pedágio(s)</td><td>".$a_usu["nome_usuario"]."</td><td align=right> R$ ".number_format(($vl_pedagio),2,',','.')."</td></tr>";
       $tot_nota += $vl_pedagio;
    }
    if ($vl_aliment>0) {
       echo "<tr><td>".$data."</td><td>Alimentação (Almoço)</td><td>".$a_usu["nome_usuario"]."</td><td align=right> R$ ".number_format(($vl_aliment),2,',','.')."</td></tr>";
       $tot_nota += $vl_aliment;
    }
    if ($vl_aliment2>0) {
       echo "<tr><td>".$data."</td><td>Alimentação (Jantar)</td><td>".$a_usu["nome_usuario"]."</td><td align=right> R$ ".number_format(($vl_aliment2),2,',','.')."</td></tr>";
       $tot_nota += $vl_aliment2;
    }
    if ($vl_outros>0) {
       echo "<tr><td>".$data."</td><td>".$obs."</td><td>".$a_usu["nome_usuario"]."</td><td align=right> R$ ".number_format(($vl_outros),2,',','.')."</td></tr>";
       $tot_nota += $vl_outros;
    }
    if ($tot_hr_interest>0) {
       echo "<tr><td>".$data."</td><td>Horas Interestaduais : ".$a_despesa["qt_hr_interest"]."h</td><td>".$a_usu["nome_usuario"]."</td><td align=right> R$ ".number_format(($tot_hr_interest),2,',','.')."</td></tr>";
       $tot_nota += $tot_hr_interest;
    }
  }
}
?>
</table>
<br>
Obs: As quilometragens foram consideradas de acordo com as distâncias informadas pelo Google Maps.
<br>
<br>
<span class=sub_titulo>Total: R$ <?=number_format($tot_nota,2,',','.');?></span>
<br>
<br>
<span class="sub_titulo">EMISSOR</span>
<br>
<table class=tabela width="100%" >
<tr><td>Empresa:</td><td>I-PARTNER DESENVOLVIMENTO DE SOFTWARE LTDA</td></tr>
<tr><td>Endereço:</td><td>AV. FRANCISCO PRESTES MAIA, 902 – CJ 12 - CENTRO – SÃO BERNARDO DO CAMPO – SP – 09770000</td></tr>
<tr><td>C.N.P.J.:</td><td>08.975.421/0001-40</td></tr>
<tr><td>I.E.:</td><td>ISENTO</td></tr>
</table>
<br>
São Bernardo do Campo, <?=date("d/m/Y");?>
<hr>
<center>i-Partner Consulting & Web Solutions</center>
<br>
<center>Av Francisco Prestes Maia, 902 – Cj 12 – Centro – São Bernardo – SP – CEP : 09770-000 – Fone: 11 2677-0655</center>
</font>
