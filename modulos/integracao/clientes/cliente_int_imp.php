<?
session_start();
header("Content-Type: text/html; charset=ISO-8859-1");
include('../../../conecta.php');
include('../../../classes/class.debug.php');
include('../../../classes/class.impcli.php');
include('../../../functions.php');
#Iniciando classe cliente
$cliente = new Cliente();
if($cliente->getNumCliImp() > 0){
	#Pegando numero para usar no log de importaчуo
	$nr_log = mysql_fetch_array(mysql_query("SELECT max FROM is_max_ids WHERE id_cadastro = 'log_int_cliente'"));
	$nr_log = ($nr_log['max'])*1;
	mysql_query("UPDATE is_max_ids SET max = '".($nr_log+1)."' WHERE id_cadastro = 'log_int_cliente'");
	#Iniciando Log da Importaчуo
	$Log = new PHPDebug();
	$Log->createFile(getParam('dir_log_imp_cliente').$nr_log.'_log_importacao_'.date('Ymd_His').'.txt');
	$Log->sep(100);
	$Log->w('Iniciando importaчуo de clientes OASIS.');
	$Log->w('Versуo 1.0');
	$Log->w(date("d/m/Y H:i:s"));
	$cliente->setLog($Log);
	$cliente->impTodosCliente();
	$cliente->finalizaIntegracao();
}
?>