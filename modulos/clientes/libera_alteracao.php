<?php
/*
 * libera_alteracao.php
 * Autor: Alex
 * 14/02/2012 09:57:27
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

session_start();
if($_SESSION['id_usuario'] == ''){
    include('nao_logado.php');
    exit;
}
require('../../conecta.php');
require('../../functions.php');
require('../../classes/class.Pessoa.php');
require('../../classes/class.Usuario.php');

$Usuario = new Usuario($_SESSION['id_usuario']);
if(!$Usuario->getPermissao('sn_trans_cliente_prospect')){
    echo 'Usuário sem permissão!';
    exit;
}

$IdPessoa = $_POST['pnumreg'];

$Pessoa = new Pessoa($IdPessoa);
$Resultado = $Pessoa->TransformaClienteEmProspect();
echo $Resultado[1];
if($Resultado[0] === true){
    echo 'Verifique o cadastro de prospects e transforme a conta em cliente novamente.';
}
?>