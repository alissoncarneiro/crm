<?
  require_once("funcoes.php");
  require_once("conecta.php");
  require_once('smtp.class.php');

  query("update is_atividades set id_atividade = numreg"); 


  // Se o evento tem e-mails de alerta para enviar
  $qry_usu = farray(query("select nome_usuario, email from is_usuarios where id_usuario = '".$edtid_usuario_resp."'")); $nome_usuario = $qry_usu["nome_usuario"];
  $qry_tpa = farray(query("select nome_tp_atividade from is_tp_atividades where id_tp_atividade = '".$edtid_tp_atividade."'")); $nome_atividade = $qry_tpa["nome_tp_atividade"];
  
  $emails_alerta = $qry_usu["email"];

  require_once("ativ_auto_email.php");

?>
