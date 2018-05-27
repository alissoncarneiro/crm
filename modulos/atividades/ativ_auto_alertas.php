<?
  require_once("../../funcoes.php");
  require_once("../../conecta.php");
  require_once('../../smtp.class.php');

  $data_atual = date("Y-m-d");
  $hora_atual = date("H:i:s");
  $operacao = "P";

  $sql  = "select * from is_atividades where ( not id_ativ_auto_acao is null)  and  (not id_situacao in ('R','C')) ";

  $sql_atividades = query(RemoveAcentos($sql));

  while ($qry_atividades = farray($sql_atividades)) {
     $pnumreg = $qry_atividades["numreg"];
     $ativ_numreg = $qry_atividades["numreg"];
     $id_ativ_auto_acao = $qry_atividades["id_ativ_auto_acao"];
     
     $id_usuario = "admin";
     $edtid_tp_atividade = $qry_atividades["id_tp_atividade"];
     $qry_tpa = farray(query("select nome_tp_atividade from is_tp_atividades where id_tp_atividade = '".$edtid_tp_atividade."'")); $nome_atividade = $qry_tpa["nome_tp_atividade"];
     $edtid_empresa_contato = $qry_atividades["id_empresa_contato"];
     $qry_emp = farray(query("select razao_social from is_empresas where id_empresa = '".$edtid_empresa_contato."'")); $edtdescrid_empresa_contato = $qry_emp["razao_social"];
     $edtid_pessoa_contato = $qry_atividades["id_pessoa_contato"];
     $qry_pes = farray(query("select nome from is_pessoas where id_pessoa = '".$edtid_pessoa_contato."'")); $edtdescrid_pessoa_contato = $qry_pes["nome"];
     $edtassunto = $qry_atividades["assunto"];
     $edtdt_prev_fim = $qry_atividades["dt_prev_fim"];
     $edtdt_prev_fim = substr($edtdt_prev_fim,8,2).'/'.substr($edtdt_prev_fim,5,2).'/'.substr($edtdt_prev_fim,0,4);
     $edthr_prev_fim = $qry_atividades["hr_prev_fim"];
     $edtid_usuario_resp = $qry_atividades["id_usuario_resp"];
     $qry_usu = farray(query("select nome_usuario from is_usuarios where id_usuario = '".$edtid_usuario_resp."'")); $nome_usuario = $qry_usu["nome_usuario"];
     $edtobs = $qry_atividades["obs"];
     
     $qry_ativ_auto_acao = farray(query("select * from is_ativ_auto_acao where id_ativ_auto_acao = '".$id_ativ_auto_acao."'"));
     
     $emails_alerta1 = $qry_ativ_auto_acao["emails_alerta1"];
     $emails_alerta2 = $qry_ativ_auto_acao["emails_alerta2"];
     $emails_alerta3 = $qry_ativ_auto_acao["emails_alerta3"];
     $emails_alerta4 = $qry_ativ_auto_acao["emails_alerta4"];
     $emails_alerta5 = $qry_ativ_auto_acao["emails_alerta5"];
     
     $dt_tolerancia1 = substr($qry_atividades["dt_tolerancia1"],0,10);
     $dt_tolerancia2 = substr($qry_atividades["dt_tolerancia2"],0,10);
     $dt_tolerancia3 = substr($qry_atividades["dt_tolerancia3"],0,10);
     $dt_tolerancia4 = substr($qry_atividades["dt_tolerancia4"],0,10);
     $dt_tolerancia5 = substr($qry_atividades["dt_tolerancia5"],0,10);
     
     $hr_tolerancia1 = $qry_atividades["hr_tolerancia1"];
     $hr_tolerancia2 = $qry_atividades["hr_tolerancia2"];
     $hr_tolerancia3 = $qry_atividades["hr_tolerancia3"];
     $hr_tolerancia4 = $qry_atividades["hr_tolerancia4"];
     $hr_tolerancia5 = $qry_atividades["hr_tolerancia5"];

     $qt_avisos1 = $qry_atividades["qt_avisos1"];
     $qt_avisos2 = $qry_atividades["qt_avisos2"];
     $qt_avisos3 = $qry_atividades["qt_avisos3"];
     $qt_avisos4 = $qry_atividades["qt_avisos4"];
     $qt_avisos5 = $qry_atividades["qt_avisos5"];

     
     $emails_alerta = "";
     if (($dt_tolerancia1.' '.$hr_tolerancia1 <= $data_atual.' '.$hora_atual ) && (empty($qt_avisos1)) ) {
        $emails_alerta = $emails_alerta1;
        query("update is_atividades set qt_avisos1 = 1 where numreg = '$ativ_numreg'");
     }
     if (($dt_tolerancia2.' '.$hr_tolerancia2 <= $data_atual.' '.$hora_atual ) && (empty($qt_avisos2)) ) {
        $emails_alerta = $emails_alerta.';'.$emails_alerta2;
        query("update is_atividades set qt_avisos2 = 1 where numreg = '$ativ_numreg'");
     }
     if (($dt_tolerancia3.' '.$hr_tolerancia3 <= $data_atual.' '.$hora_atual ) && (empty($qt_avisos3)) ) {
        $emails_alerta = $emails_alerta.';'.$emails_alerta3;
        query("update is_atividades set qt_avisos3 = 1 where numreg = '$ativ_numreg'");
     }
     if (($dt_tolerancia4.' '.$hr_tolerancia4 <= $data_atual.' '.$hora_atual ) && (empty($qt_avisos4)) ) {
        $emails_alerta = $emails_alerta.';'.$emails_alerta4;
        query("update is_atividades set qt_avisos4 = 1 where numreg = '$ativ_numreg'");
     }
     if (($dt_tolerancia5.' '.$hr_tolerancia5 <= $data_atual.' '.$hora_atual ) && (empty($qt_avisos5)) ) {
        $emails_alerta = $emails_alerta.';'.$emails_alerta5;
        query("update is_atividades set qt_avisos5 = 1 where numreg = '$ativ_numreg'");
     }
     if ($emails_alerta) {
        include("ativ_auto_email_blat.php");
        
        echo "Alerta(s) gerado(s) para a atividade pendente ".$pnumreg."-".$edtassunto." para ".$emails_alerta."<br>";
     }
  }
  
?>
