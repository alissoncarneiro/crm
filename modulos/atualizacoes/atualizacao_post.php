<?php
/*
 * atualizacao_post.php
 * Autor: Alex
 * 03/01/2011 17:45:00
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
require('../../conecta.php');
require('../../functions.php');
require('../../classes/class.AtualizacaoOasis.php');
require('../../classes/class.pclzip.php');

$TipoArquivo = $_FILES['edtarquivo_atualizacao']['type'];
$CaminhoArquivo = $_FILES['edtarquivo_atualizacao']['tmp_name'];
if(!file_exists($CaminhoArquivo)){
    echo 'O arquivo n�o foi encontrado.';
    exit;
}
$ArrayTiposArquivosPermitidos = array(
    'application/zip',
    'application/x-zip',
    'application/x-zip-compressed',
    'application/octetstream',
    'application/octet-stream',
);
if(!array_search($TipoArquivo, $ArrayTiposArquivosPermitidos)){
    echo 'Arquivo ZIP inv�lido.<br />'.$TipoArquivo;
    exit;
}

$Atualizacao = new AtualizacaoOasis($CaminhoArquivo);
$Mensagens = $Atualizacao->getArrayMensagens();
foreach($Mensagens as $Indice => $Mensagem){
    echo '<img src="../../images/'.(($Mensagem['Tipo'] == 1)?'btn_vermelho.png':'btn_verde.png').'" />&nbsp;&nbsp;';
    echo $Mensagem['Mensagem'].'<hr>';
}
echo 'Log de altera��es: '.htmlentities($Atualizacao->getLogAlteracoes()).'<hr>';
?>
<a href="#" onclick="javascript:window.close();"/> Fechar </a> | <a href="#" onclick="javascript:history.go(-1); return false;"/>Voltar</a>