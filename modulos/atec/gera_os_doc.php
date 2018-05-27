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
$qry_tp_atividade = farray(query('select * from is_tp_atividade where numreg = \''.$qry_atividade["id_tp_atividade"].'\''));

$qry_modelo = farray(query("select id_arquivo from is_modelo_os where numreg = '".$qry_atividade["id_modelo_os"]."'"));

if(empty($qry_modelo["id_arquivo"])){
    echo "<script>alert('Por favor primeiro selecione um modelo de documento !'); window.close();</script>";
    exit;
}

$arquivo = $caminho_arquivos.trim($qry_modelo["id_arquivo"]);
$fp = fopen($arquivo,'r');
$texto = fread($fp,filesize($arquivo));
fclose($fp);

// Produtos
$tot_prod = 0;
$lista_detalhes_produtos = '';
$lista_precos_produtos = '<w:tbl><w:tblPr><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:left w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:right w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:insideH w:val="single" w:sz="4" w:space="0" w:color="auto"/><w:insideV w:val="single" w:sz="4" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="04A0"/></w:tblPr><w:tblGrid><w:gridCol w:w="7338"/><w:gridCol w:w="2439"/></w:tblGrid><w:tr w:rsidR="003B656D" w:rsidTr="00A8698B"><w:trPr><w:trHeight w:val="309"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="7338" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="D9D9D9" w:themeFill="background1" w:themeFillShade="D9"/></w:tcPr><w:p w:rsidR="00DF084B" w:rsidRDefault="000D7946" w:rsidP="00A8698B"><w:pPr><w:spacing w:after="0" w:line="240" w:lineRule="auto"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>Item</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2439" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="D9D9D9" w:themeFill="background1" w:themeFillShade="D9"/></w:tcPr><w:p w:rsidR="003B656D" w:rsidRDefault="000D7946" w:rsidP="00A8698B"><w:pPr><w:spacing w:after="0" w:line="240" w:lineRule="auto"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>Valor</w:t></w:r></w:p></w:tc></w:tr>';


$sql_produtos = (query("select * from is_contrato_obj where id_contrato = '".$qry_atividade["id_contrato"]."'"));
while($qry_produtos = farray($sql_produtos)){
    $qp = farray(query("select * from is_produto where id_produto = '".$qry_produtos["id_produto"]."'"));
    $lista_detalhes_produtos .= "</w:t></w:r></w:p><w:p><w:r><w:t>".$qp["nome_produto"]." : ".$qp["descr_completa"]."";
    $lista_precos_produtos .= '<w:tr w:rsidR="003B656D" w:rsidTr="00A8698B"><w:tc><w:tcPr><w:tcW w:w="7338" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="FFFFFF" w:themeFill="background1"/></w:tcPr><w:p w:rsidR="003B656D" w:rsidRDefault="000D7946" w:rsidP="00A8698B"><w:pPr><w:spacing w:after="0" w:line="240" w:lineRule="auto"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>'.$qp["nome_produto"].'</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2439" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="FFFFFF" w:themeFill="background1"/></w:tcPr><w:p w:rsidR="003B656D" w:rsidRDefault="000D7946" w:rsidP="00A8698B"><w:pPr><w:spacing w:after="0" w:line="240" w:lineRule="auto"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>R$'.number_format($qry_produtos["valor"],2,',','.').'</w:t></w:r></w:p></w:tc></w:tr>';
    $tot_prod = $tot_prod + $qry_produtos["valor"];
}

$lista_detalhes_produtos = (str_replace("<br />"," ",$lista_detalhes_produtos));

if($lista_detalhes_produtos){
    $lista_detalhes_produtos = '<w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/>'.$lista_detalhes_produtos.'</w:t></w:r></w:p><w:p><w:r><w:t>';
    $lista_precos_produtos .= '<w:tr w:rsidR="003B656D" w:rsidTr="00A8698B"><w:trPr><w:trHeight w:val="173"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="7338" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="D9D9D9" w:themeFill="background1" w:themeFillShade="D9"/></w:tcPr><w:p w:rsidR="003B656D" w:rsidRDefault="000D7946" w:rsidP="00A8698B"><w:pPr><w:spacing w:after="0" w:line="240" w:lineRule="auto"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>Total</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="2439" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="D9D9D9" w:themeFill="background1" w:themeFillShade="D9"/></w:tcPr><w:p w:rsidR="003B656D" w:rsidRDefault="000D7946" w:rsidP="00A8698B"><w:pPr><w:spacing w:after="0" w:line="240" w:lineRule="auto"/><w:jc w:val="right"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>R$'.number_format($tot_prod,2,',','.').'</w:t></w:r></w:p></w:tc></w:tr></w:tbl>';
}else{
    $lista_precos_produtos = '';
}

$texto = (str_replace("VS_CLIENTE",utf8_encode($qry_empresa["razao_social_nome"]),$texto));
$texto = (str_replace("VS_CONTATO",utf8_encode($qry_pessoa["nome"]),$texto));
$texto = (str_replace("VS_NUMERO_CONTRATO",utf8_encode($qry_atividade["nr_contrato"]),$texto));
$texto = (str_replace("VS_DATA_EMISSAO",utf8_encode(DataBD2DMMY(date("Y-m-d"))),$texto));
$texto = (str_replace("VS_LISTA_DETALHES_PRODUTOS",utf8_encode($lista_detalhes_produtos),$texto));
$texto = (str_replace("VS_LISTA_PRECOS_PRODUTOS",utf8_encode($lista_precos_produtos),$texto));
$texto = (str_replace("VS_CONTRATO_OBS",utf8_encode($qry_atividade["obs"]),$texto));

$texto = (str_replace("VS_FANTASIA",utf8_encode($qry_empresa["razao_social_nome"]),$texto));
$texto = (str_replace("VS_CNPJ",utf8_encode($qry_empresa["cnpj_cpf"]),$texto));
$texto = (str_replace("VS_UF",utf8_encode($qry_empresa["uf"]),$texto));
$texto = (str_replace("VS_BAIRRO",utf8_encode($qry_empresa["bairro"]),$texto));
$texto = (str_replace("VS_CIDADE",utf8_encode($qry_empresa["cidade"]),$texto));
$texto = (str_replace("VS_ENDERECO",utf8_encode($qry_empresa["endereco"].', '.$qry_empresa["numero"].'-'.$qry_empresa["complemento"]),$texto));
$texto = (str_replace("VS_TELEFONE",utf8_encode($qry_empresa["tel1"]),$texto));

$texto = (str_replace("VS_NUM_OS",utf8_encode($qry_atividade["id_atividade"]),$texto));
$texto = (str_replace("VSDT_INICIO",utf8_encode($qry_atividade["dt_inicio"]),$texto));
$texto = (str_replace("VS_HR_INICIO",utf8_encode($qry_atividade["hr_inicio"]),$texto));
$texto = (str_replace("VSTIPO",utf8_encode($qry_tp_atividade['nome_tp_atividade']),$texto));

// se o arquivo tem conteudo
if($texto){
    header("Content-Type: application/vnd.ms-word");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("content-disposition: attachment;filename=os.doc");

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