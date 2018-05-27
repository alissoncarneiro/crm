<?php
/*
 * atualizacao_post.php
 * Autor: Alex
 * 03/01/2011 17:45:00
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
require('../../conecta.php');
require('../../functions.php');
require('../../classes/class.AtualizacaoOasis.php');
require('../../classes/class.pclzip.php');

$TipoArquivo = $_FILES['edtarquivo_atualizacao']['type'];
$CaminhoArquivo = $_FILES['edtarquivo_atualizacao']['tmp_name'];
if(!file_exists($CaminhoArquivo)){
    echo 'O arquivo não foi encontrado.';
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
    echo 'Arquivo ZIP inválido.<br />'.$TipoArquivo;
    exit;
}

$Atualizacao = new AtualizacaoOasis($CaminhoArquivo);
$Mensagens = $Atualizacao->getArrayMensagens();
foreach($Mensagens as $Indice => $Mensagem){
    echo '<img src="../../images/'.(($Mensagem['Tipo'] == 1)?'btn_vermelho.png':'btn_verde.png').'" />&nbsp;&nbsp;';
    echo $Mensagem['Mensagem'].'<hr>';
}
echo 'Log de alterações: '.htmlentities($Atualizacao->getLogAlteracoes()).'<hr>';
?>
<a href="#" onclick="javascript:window.close();"/> Fechar </a> | <a href="#" onclick="javascript:history.go(-1); return false;"/>Voltar</a>