<?php
/*
 * c_coaching_lista_presenca_post.php
 * Autor: Alex
 * 17/08/2011 16:30:00
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

session_start();
if($_SESSION['id_usuario'] == ''){
    echo 'Usuário não logado.';
    exit;
}

require('../../../../conecta.php');
require('../../../../functions.php');

if(!$_POST){
    BloqueiaAcessoDireto();
}

$IdAgenda = $_POST['id_agenda'];

if($IdAgenda == ''){
    echo alert('Agenda não informada!',true);
    echo windowclose(true);
    exit;
}

$QryAgenda = query("SELECT sn_lista_presenca_preenchida FROM c_coaching_agenda_curso WHERE numreg = '".$IdAgenda."'");
$ArAgenda = farray($QryAgenda);

$SqlAgendas = "SELECT numreg FROM c_coaching_inscricao_curso_detalhe WHERE id_agenda = '".$IdAgenda."'";
$QryAgendas = query($SqlAgendas);
while($ArAgendas = farray($QryAgendas)){
    $ArSqlUpdate = array();
    $ArSqlUpdate['numreg'] = $ArAgendas['numreg'];
    $ArSqlUpdate['sn_presente'] = ($_POST['chk_presente_'.$ArAgendas['numreg']] == '1')?1:0;
    $SqlUpdate = AutoExecuteSql(TipoBancoDados,'c_coaching_inscricao_curso_detalhe',$ArSqlUpdate,'UPDATE',array('numreg'));
    query($SqlUpdate);
}

$ArrayStaffs= array();
$SqlStaffs = "SELECT * FROM c_coaching_agenda_staff_lista_presenca WHERE id_agenda = '".$IdAgenda."'";
$QryStaffs = query($SqlStaffs);
while($ArStaffs = farray($QryStaffs)){
    $ArSqlUpdate = array();
    $ArSqlUpdate['numreg'] = $ArStaffs['numreg'];
    $ArSqlUpdate['sn_presente'] = ($_POST['chk_presente_staff_'.$ArStaffs['numreg']] == '1')?1:0;
    $SqlUpdate = AutoExecuteSql(TipoBancoDados,'c_coaching_agenda_staff_lista_presenca',$ArSqlUpdate,'UPDATE',array('numreg'));
    query($SqlUpdate);
}

$SqlUpdateAgenda = "UPDATE c_coaching_agenda_curso SET sn_lista_presenca_preenchida = 1, id_usuario_lista_presenca = '".$_SESSION['id_usuario']."',dt_lista_presenca = '".date("Y-m-d")."',hr_lista_presenca = '".date("H:i:s")."' WHERE numreg = '".$IdAgenda."'";
$QryUpdateAgenda = query($SqlUpdateAgenda);

echo alert('Lista de presença confirmada com sucesso!',true);
echo windowclose(true);
?>