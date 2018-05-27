<?php
/*
 * interface_pedido_exp_download.php
 * Autor: Alex
 * 24/11/2010 09:08
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

include('../../conecta.php');
include('../../functions.php');
include('../../classes/class.Usuario.php');

$NumregPedidoTXT = $_GET['pnumreg'];

if($NumregPedidoTXT == ''){
    echo "Registro Não Informado.";
    exit;
}

$SqlPedidoTXT = "SELECT * FROM is_pedido_txt_datasul WHERE numreg  = '".$NumregPedidoTXT."'";
$QryPedidoTXT = query($SqlPedidoTXT);

$NumrowsPedidoTXT = numrows($QryPedidoTXT);

if($NumrowsPedidoTXT == 0){
    echo "Registro Não Encontrado.";
    exit;
}

$ArPedidoTXT = farray($QryPedidoTXT);

if($ArPedidoTXT['texto'] == ''){
    echo "Arquivo Vazio.";
    exit;
}

$NomeArquivo = 'pedido_conteudo_'.$ArPedidoTXT['numreg'].'.lst';

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header( "Content-Disposition: attachment; filename=".$NomeArquivo);
header( "Content-Description: File Transfer");
echo $ArPedidoTXT['texto'];
?>