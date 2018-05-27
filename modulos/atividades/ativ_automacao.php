<?
header("Content-Type: text/html;  charset=ISO-8859-1");
require('../../conecta.php');
switch ($_POST['acao']){
	case 'set_cb_gc_prod':
		$ar = mysql_fetch_array(mysql_query("
		SELECT t2.id_usuario_resp 
		FROM is_produtos t1 INNER JOIN is_prod_grupos t2 ON t1.id_grupo = t2.id_grupo
		WHERE t1.id_produto = '".$_POST['valor']."'
		"));
		echo $ar['id_usuario_resp'];
	break;
	case 'set_cb_gc_atividade':
		$ar = mysql_fetch_array(mysql_query("
		SELECT id_usuario_resp 
		FROM is_tp_atividades 
		WHERE id_tp_atividade = '".$_POST['valor']."'
		"));
		echo $ar['id_usuario_resp'];
	break;
	case 'set_cb_gc_linha_fabr_prod':
		$ar = mysql_fetch_array(mysql_query("
		SELECT 
		t2.id_usuario_resp,
		t1.id_linha,
		t1.id_fabricante,
		t3.razao_social_nome
		FROM is_produtos t1 
		INNER JOIN is_prod_grupos t2 ON t1.id_grupo = t2.id_grupo
		LEFT JOIN is_pessoas t3 ON t1.id_fabricante = t3.id_pessoa
		WHERE t1.id_produto = '".$_POST['valor']."'
		"));
		echo $ar['id_usuario_resp'].';'.$ar['id_linha'].';'.$ar['id_fabricante'].';'.$ar['razao_social_nome'];
	break;
	case 'set_cb_prioridade':
		$ar = mysql_fetch_array(mysql_query("SELECT * FROM is_caso_motivo WHERE id_caso_motivo = '".$_POST['valor']."'"));
		echo $ar['id_prioridade'];
	break;
	case 'set_cb_resp_motivo':
		$ar = mysql_fetch_array(mysql_query("SELECT * FROM is_caso_motivo WHERE id_caso_motivo = '".$_POST['valor']."'"));
		echo $ar['id_usuario_resp'];
	break;
	case 'set_cb_gerou_op':
		$ar = mysql_fetch_array(mysql_query("SELECT * FROM is_caso_motivo WHERE id_caso_motivo = '".$_POST['valor']."'"));
		if ($ar['gerou_op']) { echo $ar['gerou_op']; } else { echo 'N'; }
	break;
}
?>
