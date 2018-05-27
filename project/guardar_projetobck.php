<?
$id_projeto = $_GET['id_projeto'];
if(empty($_GET['id_projeto'])){
echo "Projeto não Informado !";
exit;
}
require_once("../conecta.php");
$id_backup = farray(query("SELECT MAX(id_backup * 1) as max_id FROM is_projetos_bck"));
$id_backup = ($id_backup['max_id'] * 1) + 1;

echo $id_backup;
//========================================================================================================================
//BACKUP DO PROJETO
//========================================================================================================================
$sql_projeto = query("SELECT * FROM is_projetos WHERE id_projeto='".$id_projeto."'");
while ($qry_projeto = farray($sql_projeto)) {
	$i = 0;
	$nome_campos = "";
	$conteudos = "";
	while ($i < mysql_num_fields($sql_projeto)) {
		$meta = mysql_fetch_field($sql_projeto,$i);
		$tipo = $meta->type;
		if ($meta->name != "numreg" && $meta->name != "id_backup" && $meta->name != "dt_backup") {
			if(!empty($qry_projeto[$meta->name])){
				$nome_campos .= $meta->name.',';
				$conteudos .= "'".$qry_projeto[$meta->name]."',";
			}
		}   
		$i = $i + 1;
	}
	$sql_insert = "INSERT INTO is_projetos_bck (".substr($nome_campos,0,strlen($nome_campos)-1).",id_backup,dt_backup) values (".substr($conteudos,0,strlen($conteudos)-1).",'".$id_backup."',NOW()); ";
	query($sql_insert);
}
//========================================================================================================================
//BACKUP DAS ATIVIDADES
//========================================================================================================================
$sql_ativ = query("SELECT * FROM is_atividades WHERE id_projeto='".$id_projeto."'");
while ($qry_ativ = farray($sql_ativ)) {
	$i = 0;
	$nome_campos = "";
	$conteudos = "";
	while ($i < mysql_num_fields($sql_ativ)) {
		$meta = mysql_fetch_field($sql_ativ,$i);
		$tipo = $meta->type;
		if ($meta->name != "numreg" && $meta->name != "id_backup" && $meta->name != "dt_backup") {
			if(!empty($qry_ativ[$meta->name])){
				$nome_campos .= $meta->name.',';
				$conteudos .= "'".$qry_ativ[$meta->name]."',";
			}
		}   
		$i = $i + 1;
	}
	$sql_insert = "INSERT INTO is_atividades_bck (".substr($nome_campos,0,strlen($nome_campos)-1).",id_backup,dt_backup) values (".substr($conteudos,0,strlen($conteudos)-1).",'".$id_backup."',NOW()); ";
	query($sql_insert);
}
//========================================================================================================================
//BACKUP DAS DESPESAS DA ATIVIDADE
//========================================================================================================================
$sql_ativ_despesa = query("SELECT t1.* FROM is_ativ_despesa t1 LEFT JOIN is_atividades t2 ON t1.id_atividade = t2.id_atividade WHERE t2.id_projeto='".$id_projeto."'");
while ($qry_ativ_despesa = farray($sql_ativ_despesa)) {
	$i = 0;
	$nome_campos = "";
	$conteudos = "";
	while ($i < mysql_num_fields($sql_ativ_despesa)) {
		$meta = mysql_fetch_field($sql_ativ_despesa,$i);
		$tipo = $meta->type;
		if ($meta->name != "numreg" && $meta->name != "id_backup" && $meta->name != "dt_backup") {
			if(!empty($qry_ativ_despesa[$meta->name])){
				$nome_campos .= $meta->name.',';
				$conteudos .= "'".$qry_ativ_despesa[$meta->name]."',";
			}
		}   
		$i = $i + 1;
	}
	$sql_insert = "INSERT INTO is_ativ_despesa_bck (".substr($nome_campos,0,strlen($nome_campos)-1).",id_backup,dt_backup) values (".substr($conteudos,0,strlen($conteudos)-1).",'".$id_backup."',NOW()); ";
	query($sql_insert);
}
//========================================================================================================================
//BACKUP DAS DEPENDENCIAS DA ATIVIDADE
//========================================================================================================================
$sql_ativ_despesa = query("SELECT t1.* FROM is_ativ_dependencia t1 LEFT JOIN is_atividades t2 ON t1.id_atividade_pai = t2.id_atividade WHERE t2.id_projeto='".$id_projeto."'");
while ($qry_ativ_despesa = farray($sql_ativ_despesa)) {
	$i = 0;
	$nome_campos = "";
	$conteudos = "";
	while ($i < mysql_num_fields($sql_ativ_despesa)) {
		$meta = mysql_fetch_field($sql_ativ_despesa,$i);
		$tipo = $meta->type;
		if ($meta->name != "numreg" && $meta->name != "id_backup" && $meta->name != "dt_backup") {
			if(!empty($qry_ativ_despesa[$meta->name])){
				$nome_campos .= $meta->name.',';
				$conteudos .= "'".$qry_ativ_despesa[$meta->name]."',";
			}
		}   
		$i = $i + 1;
	}
	$sql_insert = "INSERT INTO is_ativ_despesa_bck (".substr($nome_campos,0,strlen($nome_campos)-1).",id_backup,dt_backup) values (".substr($conteudos,0,strlen($conteudos)-1).",'".$id_backup."',NOW()); ";
	query($sql_insert);
}
?>