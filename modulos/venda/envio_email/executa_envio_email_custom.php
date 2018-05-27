<?php

header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
$PrefixoIncludes = '../';
require('../includes.php');

echo '<html><head><title>Envio de e-mail'.$_REQUEST['pnumreg'].'</title></head><body>';

$assunto = $_REQUEST['assunto'];
$numreg= $_REQUEST['pnumreg'];
$numreg_modelo = ($_REQUEST['id_modelo']!='')?$_REQUEST['id_modelo']:$_REQUEST['select_id_modelo'];
$cc_emails = ($_REQUEST['emails_copia'] != '')?$_REQUEST['emails_copia']:$_REQUEST['cc_emails'];

$cc_emails_Bd= $cc_emails;
$cc_emails = explode(';',$cc_emails);
$cco_emails = explode(';', $_REQUEST['cco_emails']);
$msg = '';

$sql_modelo = 'select * from is_modelo_orcamento where numreg='.$numreg_modelo;
$qry_modelo = query($sql_modelo);
$ar_modelo = farray($qry_modelo);

$nome_arquivo = $ar_modelo['caminho_modelo_orcamento'];
$extensao_arquivo = explode('.', $nome_arquivo);
$extensao_arquivo = end($extensao_arquivo);

if($ar_modelo['tp_arquivo'] != 'html'){
    $envio_em_anexo = 1;
} else {
    $envio_em_anexo = 0;
}

$Mail = new Email();
#$Mail->_setRementeComoUsuarioLogado(); /* Setando o remetente do e-mail como o usuário logado no sistema */
$Mail->_Assunto($assunto);




$SqlPessoa= "select razao_social_nome,email, email_pessoal from is_pessoa where numreg='".$numreg."'";
$QrySqlPessoa = mysql_query($SqlPessoa);

while($ArQrySqlPessoa = mysql_fetch_array($QrySqlPessoa)){
	$NomeContato = $ArQrySqlPessoa[razao_social_nome];
	$EmailContato = "".$ArQrySqlPessoa[email].";".$ArQrySqlPessoa[email_pessoal]."";
}


//Adiciona e-mails da Conta
$cc_emails_ficha = explode(';',$EmailContato);
foreach($cc_emails_ficha as $Email_ficha){
    if(!empty($Email_ficha)){
		$Mail->_AdicionaDestinatario($Email_ficha,$NomeContato);
    }
}

//Adiciona e-mails com Copia
foreach($cc_emails as $Email){
    if(!empty($Email)){
        $Mail->_AdicionaDestinatarioCC($Email);
    }
}

//Adiciona e-mails com Copia Oculta
foreach($cco_emails as $Email_ficha_cco){
    if(!empty($Email_ficha_cco)){
		$Mail->_AdicionaDestinatarioCCO($Email_ficha_cco);
    }
}

if($envio_em_anexo == 0) {

    //$Mail->AddEmbeddedImage('../../../images/logo_login.png', 'logoimg');
    $caminho_s_tratamento   = $_SERVER['PHP_SELF'];
    $caminho_explode        = explode('/',$caminho_s_tratamento);
    for($x=0;$x<2;$x++){
        unset($caminho_explode[count($caminho_explode)-1]);
    }
    $ip_servidor            = $_SERVER['SERVER_ADDR'];
    $porta_servidor         = $_SERVER['SERVER_PORT'];
    $caminho_tratado        = implode('/',$caminho_explode);

    $caminho_web_interno_servidor = GetParam('CAMINHO_WEB_INTERNO_SERVIDOR');

    $sufixo_url = $caminho_tratado.'/gera_modelo/'.$nome_arquivo.'&id_modelo='.$numreg_modelo.'&envia_email=1';


    if($caminho_web_interno_servidor != ''){
        $url_completa = $caminho_web_interno_servidor.$sufixo_url;

    }
    else{
        $url_completa = 'http://'.$ip_servidor.':'.$porta_servidor.$sufixo_url;
    }

    $autorizacao = stream_context_create(array(
        'http' => array(
            'header'  => "Authorization: Basic ".base64_encode($user_page.':'.$password_page)
        )
    ));

    $conteudo_para_envio =  urlencode (file_get_contents($url_completa,false,$autorizacao));

    
	
	if(!$conteudo_para_envio){
        echo '<script>alert(\'Erro ao enviar o e-mail, não foi possível carregar o conteúdo HTML para envio do e-mail.'.'\');window.opener.location.reload();window.close();</script>';
        exit;
    }
} 


else {
    include('../gera_modelo/'.$nome_arquivo);
    $Mail->_AdicionaAnexo('arquivos_gerados/'.$nome_arquivo_envio);

}
//Criando o corpo do documento.

 //recuperando email do contato
        $ArSqlUsuario = mysql_fetch_array(mysql_query("SELECT * FROM is_usuario WHERE numreg = ".$_SESSION['id_usuario']));
        $EnviadoPor = $ArSqlUsuario['nome_usuario'].'('.$ArSqlUsuario['email'].')';
        $dthr_envio = date('Y-m-d H:i:s');
		$assinatura = '';

		

$Mail->_Corpo($_POST['text_area_corpo_email']."<br>".$assinatura.$conteudo_para_envio);


/*
 * Tratando anexos
 */
$ArrayArquivos = array();
foreach($_FILES['arquivo_anexo']['name'] as $k => $v){
    $CaminhoArquivo = $_FILES['arquivo_anexo']['tmp_name'][$k];
    $NomeArquivo    = $_FILES['arquivo_anexo']['name'][$k];
    $ArrayArquivos[$CaminhoArquivo] = $NomeArquivo;
}
if(count($ArrayArquivos) > 0){
    /* Criando pasta temporária */
    $PastaTemporaria = 'arquivos_temporarios'.md5(base64_encode(rand(10,1000).rand(10,1000).rand(10,1000).rand(10,1000).rand(10,1000).rand(10,1000)));
    if(!is_dir($PastaTemporaria)){
        mkdir($PastaTemporaria,0777);
        chmodr($PastaTemporaria,0777);
    }
}
foreach ($ArrayArquivos as $k => $v){
    move_uploaded_file($k, $PastaTemporaria.'/'.$v);
    $Mail->_AdicionaAnexo($PastaTemporaria.'/'.$v, $k);
}

if(empty($msg)){
    if($Mail->_EnviaEmail()){
/*        $EmailEnviado = $Venda->getDadosVenda('sn_email_enviado');
        $Venda->AtualizaDadosVendaBD();*/

        if($envio_em_anexo == 1){
            unlink('arquivos_gerados/'.$nome_arquivo_envio);
        }
        
//gravando email enviado
$SqlInsert = "INSERT INTO `is_email_pessoa`(id_pessoa,dthr_email,nome_contato,email_contato,email_cc,email_assunto,email_corpo,email_anexo,email_remetente,id_usuario_resp)VALUES('".$numreg."','".$dthr_envio."','".$NomeContato."', '".$EmailContato."','".$cc_emails_Bd."','".$assunto."','".$_POST['text_area_corpo_email']."','".$anexo['name']."','".$EnviadoPor."','".$_SESSION['id_usuario']."')";
		
		if(!$QryInsert = mysql_query($SqlInsert)){
			$msg = trim($SqlInsert);
		}else{
			$msg = "Email Enviado com Sucesso";
		}
	} else {
        $msg = 'Erro ao enviar o e-mail. => '.$Mail->ErrorInfo . '. '.$Mail->Description;
    }
}
/* Apagando todos os arquivos que foram anexados */
rmdirr($PastaTemporaria);
echo '<script>alert(\''.$msg.'\');window.opener.location.reload();window.close();</script>';
echo '</body></html>';
?>