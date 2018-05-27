<?php
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
$PrefixoIncludes = '../';
require('../includes.php');
$numreg_venda = $_REQUEST['pnumreg'];
$numreg_modelo = ($_REQUEST['id_modelo']!='')?$_REQUEST['id_modelo']:$_REQUEST['select_id_modelo'];
$ptp_venda = $_REQUEST['ptp_venda'];
$cc_emails = ($_REQUEST['emails_copia'] != '')?$_REQUEST['emails_copia']:$_REQUEST['cc_emails'];
$cc_emails = explode(';',$cc_emails);
if(!is_numeric($numreg_venda) || !is_numeric($numreg_modelo) || !is_numeric($ptp_venda)){
    echo '<script>alert(\'Parâmetros infromados não estão corretos, favor tentar novamente.\');window.opener.location.reload();window.close();</script>';
    exit;
}
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
if($ptp_venda == 1){
    $NomeVenda = 'Orçamento Nº ';
    $NomeTabela = 'is_orcamento';
    $Venda = new Orcamento($ptp_venda,$numreg_venda);
} else {
    $NomeVenda = 'Pedido Nº ';
    $NomeTabela = 'is_pedido';
    $Venda = new Pedido($ptp_venda,$numreg_venda);
}
$Mail->_Assunto($NomeVenda.$numreg_venda);

/*
 * Adicionando o contato
 */
$Contato = $Venda->getContato();
if(is_object($Contato)){
    $NomeContato = trim($Contato->getNome());
    $EmailContato = trim($Contato->getEmail());
    if($EmailContato != ''){
        $Mail->_AdicionaDestinatarioCC($EmailContato,$NomeContato);
    }
}

foreach($cc_emails as $Email){
    if(!empty($Email)){
        $Mail->_AdicionaDestinatarioCC($Email);
    }
}

if($envio_em_anexo == 0) {

    $ImagemLogo = VendaCallBackCustom::ExecutaVenda($Venda, 'EnvioEmail', 'ImagemLogo', array('pid_usuario' => $_SESSION['id_usuario']));
    $ImagemLogo = ($ImagemLogo !== true)?$ImagemLogo:'../../../images/logo_login.png';
    $Mail->AddEmbeddedImage($ImagemLogo, 'logoimg', 'Logo');
    
    $caminho_s_tratamento   = $_SERVER['PHP_SELF'];
    $caminho_explode        = explode('/',$caminho_s_tratamento);
    for($x=0;$x<2;$x++){
        unset($caminho_explode[count($caminho_explode)-1]);
    }
    $ip_servidor            = $_SERVER['SERVER_ADDR'];
    $porta_servidor         = $_SERVER['SERVER_PORT'];
    $caminho_tratado        = implode('/',$caminho_explode);

    $caminho_web_interno_servidor = GetParam('CAMINHO_WEB_INTERNO_SERVIDOR');

    $sufixo_url = $caminho_tratado.'/gera_modelo/'.$nome_arquivo.'?pnumreg='.$numreg_venda.'&ptp_venda='.$ptp_venda.'&id_modelo='.$numreg_modelo.'&envia_email=1&pid_usuario='.$_SESSION['id_usuario'];

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

    $conteudo_para_envio = file_get_contents($url_completa,false,$autorizacao);

    if(!$conteudo_para_envio){
        echo '<script>alert(\'Erro ao enviar o e-mail, não foi possível carregar o conteúdo HTML para envio do e-mail.'.'\');window.opener.location.reload();window.close();</script>';
        exit;
    }
} else {
    include('../gera_modelo/'.$nome_arquivo);
    $Mail->_AdicionaAnexo('arquivos_gerados/'.$nome_arquivo_envio);
}
//Criando o corpo do documento.
$Mail->_Corpo($_POST['text_area_corpo_email'].'<hr/>'.$conteudo_para_envio);

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
    VendaCallBackCustom::ExecutaVenda($Venda, 'EnvioEmail', 'AntesEnviar', array($Mail,$_POST,$_FILES));
    if($Mail->_EnviaEmail()){
        $EmailEnviado = $Venda->getDadosVenda('sn_email_enviado');
        $Venda->setDadoVenda('sn_email_enviado',1);
        if($Venda->isOrcamento()){
            $Venda->setDadoVenda('id_situacao_venda',2);
            $Venda->GeraAtualizaOportunidadePaiEFilha();
        }
        $Venda->AtualizaDadosVendaBD();
        if($envio_em_anexo == 1){
            unlink('arquivos_gerados/'.$nome_arquivo_envio);
        }

        /* Se é um orçamento e controla por atividade e o e-mail ainda nao foi enviado */
        if($Venda->isOrcamento() && $EmailEnviado != 1){
            $Venda->FinalizaAtividadeEnvioOrcamento();
            $Venda->CriaAtividadeFollowupOrcamento();
        }

        $msg = 'E-Mail enviado com sucesso';
    } else {
        $msg = 'Erro ao enviar o e-mail. => '.$Mail->ErrorInfo . '. '.$Mail->Description;
    }
}
/* Apagando todos os arquivos que foram anexados */
rmdirr($PastaTemporaria);
echo '<script>alert(\''.$msg.'\');window.opener.location.reload();window.close();</script>';
echo '</body></html>';
?>