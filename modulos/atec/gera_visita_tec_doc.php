<?php

@session_start();

$vs_id_usuario = $_SESSION["id_usuario"];
$vs_id_perfil = $_SESSION["id_perfil"];
$sn_bloquear_leitura = $_SESSION["sn_bloquear_leitura"];
$pnumreg = $_GET["pnumreg"];

require_once("../../conecta.php");
require_once("../../funcoes.php");

$qry_atividade = farray(query("select * from is_atividade where numreg = '$pnumreg'"));
$qry_empresa = farray(query("select * from is_pessoa where numreg = '".$qry_atividade["id_pessoa"]."'"));
$qry_pessoa = farray(query("select * from is_contato where numreg = '".$qry_atividade["id_pessoa_contato"]."'"));
$qry_usuario = farray(query("select * from is_usuario where numreg = '".$qry_atividade["id_usuario_resp"]."'"));

$qry_modelo = farray(query("select id_arquivo from is_modelo_visita_tec where numreg = '".$qry_atividade["id_modelo_visita_tec"]."'"));

if(empty($qry_modelo["id_arquivo"])){
    echo "<script>alert('Por favor primeiro selecione um modelo de documento !'); window.close();</script>";
    exit;
}

$arquivo = $caminho_arquivos.trim($qry_modelo["id_arquivo"]);
$fp = fopen($arquivo,'r');
$texto = fread($fp,filesize($arquivo));
fclose($fp);

// Participantes Externos
$a_busca = farray(query("select * from is_contato where numreg = '".$qry_atividade["id_pessoa_contato"]."'"));
$txt_temp = $a_busca["nome"].' ';
$q_temp = (query("select * from is_atividade_participante_ext where id_atividade = '".$qry_atividade["numreg"]."'"));
while($a_temp = farray($q_temp)){
    $a_busca = farray(query("select * from is_contato where numreg = '".$a_temp["id_pessoa_contato"]."'"));
    $txt_temp .= $a_busca["nome"].' ';
}
$part_ext = $txt_temp;

// Participantes Internos
$a_busca = farray(query("select * from is_usuario where numreg = '".$qry_atividade["id_usuario_resp"]."'"));
$txt_temp = $a_busca["nome_usuario"].' ';
$q_temp = (query("select * from is_atividade_participante_int where id_atividade = '".$qry_atividade["numreg"]."'"));
while($a_temp = farray($q_temp)){
    $a_busca = farray(query("select * from is_usuario where numreg = '".$a_temp["id_usuario"]."'"));
    $txt_temp .= $a_busca["nome_usuario"].' ';
}
$part_int = $txt_temp;

// Material Utilizado
$txt_temp = '';
$q_temp = (query("select * from is_atividade_prod_util where id_atividade = '".$qry_atividade["numreg"]."'"));
while($a_temp = farray($q_temp)){
    $a_busca = farray(query("select * from is_produto where numreg = '".$a_temp["id_produto"]."'"));
    $txt_temp .= '('.$a_temp["qtde"].')'.$a_busca["nome_produto"].' ';
}
$material = $txt_temp;

// Acessorios
$txt_temp = '';
$q_temp = (query("select * from is_atividade_ac_util where id_atividade = '".$qry_atividade["numreg"]."'"));
while($a_temp = farray($q_temp)){
    $a_busca = farray(query("select * from is_produto where numreg = '".$a_temp["id_produto"]."'"));
    $txt_temp .= '('.$a_temp["qtde"].')'.$a_busca["nome_produto"].' ';
}
$acessorios = $txt_temp;

// Despesas
$txt_temp = '';
$q_temp = (query("select * from is_atividade_despesa where id_atividade = '".$qry_atividade["numreg"]."'"));
while($a_temp = farray($q_temp)){
    $txt_temp .= 'KM : R$'.number_format(($a_temp["vl_km"] * 1),2,',','.').' ';
    $txt_temp .= 'Estacionamento(s) : R$'.number_format(($a_temp["vl_estac"] * 1),2,',','.').' ';
    $txt_temp .= 'Pedágio(s) : R$'.number_format(($a_temp["vl_pedagio"] * 1),2,',','.').' ';
    $txt_temp .= 'Alimentação : R$'.number_format((($a_temp["vl_aliment"] + $a_temp["vl_aliment2"]) * 1),2,',','.').' ';
    $txt_temp .= 'Outros : R$'.number_format((($a_temp["vl_outros"]) * 1),2,',','.').' ';
    $txt_temp .= 'Total : R$'.number_format(($a_temp["vl_total"] * 1),2,',','.').' ';
}
$despesas = $txt_temp;

$texto = (str_replace("VS_CLIENTE",utf8_encode($qry_empresa["razao_social_nome"]),$texto));
$texto = (str_replace("VS_DATA",DataGetBD($qry_atividade["dt_prev_fim"]),$texto));
$texto = (str_replace("VS_HR_INICIO",($qry_atividade["hr_inicio"]),$texto));
$texto = (str_replace("VS_HR_FIM",($qry_atividade["hr_prev_fim"]),$texto));
$texto = (str_replace("VS_HR_INTERVALO",($qry_atividade["tempo_intervalo"]),$texto));
$texto = (str_replace("VS_HR_TOTAL",($qry_atividade["tempo_real"]),$texto));
$texto = (str_replace("VS_PARTICIPANTES_EXTERNOS",utf8_encode($part_ext),$texto));
$texto = (str_replace("VS_PARTICIPANTES_INTERNOS",utf8_encode($part_int),$texto));
$texto = (str_replace("VS_ASSUNTO",utf8_encode($qry_atividade["assunto"]),$texto));
$texto = (str_replace("VS_DESCRICAO",str_replace("<br />",". ",utf8_encode($qry_atividade["obs"])),$texto));
$texto = (str_replace("VS_MATERIAL",utf8_encode($material),$texto));
$texto = (str_replace("VS_ACESSORIOS",utf8_encode($acessorios),$texto));
$texto = (str_replace("VS_DESPESAS",utf8_encode($despesas),$texto));

$texto = (str_replace("VS_CONTATO",utf8_encode($qry_pessoa["nome"]),$texto));
$texto = (str_replace("VS_NUMERO_CONTRATO",utf8_encode($qry_atividade["nr_contrato"]),$texto));
$texto = (str_replace("VS_DATA_EMISSAO",utf8_encode(DataBD2DMMY(date("Y-m-d"))),$texto));
$texto = (str_replace("VS_LISTA_DETALHES_PRODUTOS",utf8_encode($lista_detalhes_produtos),$texto));
$texto = (str_replace("VS_LISTA_PRECOS_PRODUTOS",utf8_encode($lista_precos_produtos),$texto));
$texto = (str_replace("VS_CONTRATO_OBS",utf8_encode($qry_atividade["obs"]),$texto));

// se o arquivo tem conteudo
if($texto){
    header("Content-Type: application/vnd.ms-word");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("content-disposition: attachment;filename=visita_tecnica.doc");

    echo $texto;
}

function DataBD2DMY($data){
    return substr($data,8,2).'/'.substr($data,5,2).'/'.substr($data,0,4);
}

function DataBD2DMMY($data){
    $mes_name = array("","Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
    return substr($data,8,2).' de '.$mes_name[(substr($data,5,2) * 1)].' de '.substr($data,0,4);
}
?>