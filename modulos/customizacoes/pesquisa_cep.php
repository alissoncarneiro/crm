<?php

include('../../conecta.php');
include('../../functions.php');

if(strlen($_POST['edtcep']) != 9){
    $cep = substr($_POST['edtcep'],0,5)."-".substr($_POST['edtcep'],5,3);
} else{
    $cep = $_POST['edtcep'];
}
echo $cep ;die;
$SqlParametroSistema = "SELECT parametro FROM is_parametros_sistema WHERE id_parametro = 'CLI_SN_BLOQUEIA_ENDERECO'";
$QryParametroSistema = query($SqlParametroSistema);
$ArParametroSistema = farray($QryParametroSistema);

$txt_out = '<'.'?'.'xml version="1.0" encoding="ISO-8859-1"'.'?'.'>'."\n";
$txt_out .= '<resposta>'."\n";
$conn_cep = conecta($cnx_servidor_cep,$cnx_usuario_cep,$cnx_senha_cep,$cnx_bd_cep) or die("Erro na conex�o com banco de dados de CEP");

$array_uf = array('SP','RJ','MG','DF','RS','PR','TO','SE','SC','RR','RO','RN','PI','PE','PB','PA','MS','MT','MA','GO','ES','CE','BA','AM','AP','AL','AC');

if(strlen($_POST['edtcep']) != 9){
    $cep = substr($_POST['edtcep'],0,5)."-".substr($_POST['edtcep'],5,3);
} else{
    $cep = $_POST['edtcep'];
}
$cep = str_replace('-','',$cep);
$resposta = '';
$qry_cep = query("SELECT * FROM ceps WHERE cep = '".$cep."'",$conn_cep);
if(numrows($qry_cep) > 0){
    
    $ar_cep = farray($qry_cep);
    $id_logradouro = DeparaCodigoDescricao($bd.'.is_logradouro', array('numreg'), array('id_logradouro_tab_cep' => $ar_cep['tipo_endereco']));
    
    $resposta  = "\t".'<id_cep>'.$ar_cep['numreg'].'</id_cep>'."\n";
    $resposta .= "\t".'<tipo_endereco>'.$ar_cep['tipo_endereco'].'</tipo_endereco>'."\n";
    $resposta .= "\t".'<id_logradouro>'.$id_logradouro.'</id_logradouro>'."\n";
    $resposta .= "\t".'<endereco>'.$ar_cep['endereco'].'</endereco>'."\n";
    $resposta .= "\t".'<bairro>'.$ar_cep['bairro'].'</bairro>'."\n";
    $resposta .= "\t".'<cidade>'.$ar_cep['cidade'].'</cidade>'."\n";
    $resposta .= "\t".'<uf>'.$ar_cep['estado'].'</uf>'."\n";
    $resposta .= "\t".'<cep>'.$ar_cep['cep'].'</cep>'."\n";
    $resposta .= "\t".'<pais>BRASIL</pais>'."\n";
}
if(!empty($resposta)){
    $txt_out .= $resposta."\t".'<status>true</status>'."\n";
}
else{
    $txt_out .= "\t".'<status>false</status>'."\n";
}


if($ArParametroSistema['parametro'] == '1'){
    $txt_out .= "\t".'<bloqueia>true</bloqueia>'."\n";
} else{
    $txt_out .= "\t".'<bloqueia>false</bloqueia>'."\n";
}

$txt_out .= '</resposta>';
header ("content-type: text/xml");
echo $txt_out;
?>