<?
$id_projeto = $_POST['id_projeto'];
if(empty($_POST['id_projeto'])){
echo "Não foi possível armazenar a versão do projeto";
exit;
}
require_once("../conecta.php");
$id_backup = mysql_fetch_array(mysql_query("SELECT MAX(id_versao * 1) as max_id FROM is_projetos_versoes"));
$id_backup = ($id_backup['max_id'] * 1) + 1;

function BACKUP($id_backup,$qry_sql,$tabela_orig,$tabela_dest){
	//mysql_query("CREATE TABLE IF NOT EXISTS ".$tabela_dest." (SELECT * FROM ".$tabela_orig.")");
	//mysql_query("TRUNCATE TABLE ".$tabela_dest.";");
	//mysql_query("ALTER TABLE ".$tabela_dest." ADD COLUMN id_backup VARCHAR(45), ADD COLUMN dt_backup DATETIME;");
	//mysql_query("ALTER TABLE ".$tabela_dest." MODIFY COLUMN numreg DOUBLE NOT NULL DEFAULT NULL AUTO_INCREMENT PRIMARY KEY;");

	$sql = mysql_query($qry_sql);
	while ($qry = mysql_fetch_array($sql)) {
		$i = 0;
		$nome_campos = "";
		$conteudos = "";
		while ($i < mysql_num_fields($sql)) {
			$meta = mysql_fetch_field($sql,$i);
			$tipo = $meta->type;
			if ($meta->name != "numreg" && $meta->name != "id_backup" && $meta->name != "dt_backup") {
				if(!empty($qry[$meta->name])){
					$nome_campos .= $meta->name.',';
					$conteudos .= "'".$qry[$meta->name]."',";
				}
			}   
			$i = $i + 1;
		}
		$sql_insert = "INSERT INTO ".$tabela_dest." (".substr($nome_campos,0,strlen($nome_campos)-1).",id_backup,dt_backup) values (".substr($conteudos,0,strlen($conteudos)-1).",'".$id_backup."',NOW()); ";
		mysql_query($sql_insert);
	}
}
//========================================================================================================================
//BACKUP DO PROJETO
//========================================================================================================================
BACKUP($id_backup, "SELECT * FROM is_projetos WHERE id_projeto='".$id_projeto."'", "is_projetos", "is_projetos_bck");
//========================================================================================================================
//BACKUP DAS ATIVIDADES
//========================================================================================================================
BACKUP($id_backup, "SELECT * FROM is_atividades WHERE id_projeto='".$id_projeto."'", "is_atividades", "is_atividades_bck");
//========================================================================================================================
//BACKUP DOS PARTICIPANTES DA ATIVIDADE
//========================================================================================================================
BACKUP($id_backup, "SELECT t1.* FROM is_atividade_participantes  t1 LEFT JOIN is_atividades t2 ON t1.id_atividade = t2.id_atividade WHERE t2.id_projeto='".$id_projeto."'", "is_atividade_participantes ", "is_atividade_participantes_bck");
//========================================================================================================================
//BACKUP DAS DESPESAS DA ATIVIDADE
//========================================================================================================================
BACKUP($id_backup, "SELECT t1.* FROM is_ativ_despesa t1 LEFT JOIN is_atividades t2 ON t1.id_atividade = t2.id_atividade WHERE t2.id_projeto='".$id_projeto."'", "is_ativ_despesa", "is_ativ_despesa_bck");
//========================================================================================================================
//BACKUP DAS DEPENDENCIAS DA ATIVIDADE
//========================================================================================================================
BACKUP($id_backup, "SELECT t1.* FROM is_ativ_dependencia t1 LEFT JOIN is_atividades t2 ON t1.id_atividade_pai = t2.id_atividade WHERE t2.id_projeto='".$id_projeto."'", "is_ativ_dependencia", "is_ativ_dependencia_bck");
//========================================================================================================================
//BACKUP DAS MACRO ATIVIDADES DO PROJETO
//========================================================================================================================
BACKUP($id_backup, "SELECT * FROM is_projeto_macro_atividade WHERE id_projeto='".$id_projeto."'", "is_projeto_macro_atividade", "is_projeto_macro_atividade_bck");
//========================================================================================================================
//BACKUP DAS ACOES
//========================================================================================================================
BACKUP($id_backup, "SELECT * FROM is_projeto_acoes WHERE id_projeto='".$id_projeto."'", "is_projeto_acoes", "is_projeto_acoes_bck");
//========================================================================================================================
//BACKUP SUB PROJETOS
//========================================================================================================================
BACKUP($id_backup, "SELECT * FROM is_projeto_sub WHERE id_projeto='".$id_projeto."'", "is_projeto_sub", "is_projeto_sub_bck");

//Campos do sistema - Lançamentos
session_start();
$dt_cadastro = date("Y-m-d");
$hr_cadastro = date("H:i");
$id_usuario_cad = $_SESSION['id_usuario'];
$dt_alteracao = date("Y-m-d");
$hr_alteracao = date("H:i");
$id_usuario_alt = $_SESSION['id_usuario'];

$max_id_versao = farray(query("SELECT MAX(id_versao * 1) as max_id FROM is_projetos_versoes"));
$max_id_versao = (($max_id_versao['max_id'] * 1) + 1);
mysql_query("INSERT INTO is_projetos_versoes(dt_cadastro, hr_cadastro, id_usuario_cad, dt_alteracao, hr_alteracao, id_usuario_alt,id_versao,id_projeto,versao,dt_versao,hr_versao) VALUES('".$dt_cadastro."','".$hr_cadastro."','".$id_usuario_cad."','".$dt_cadastro."','".$hr_cadastro."','".$id_usuario_cad."','".$max_id_versao."','".$id_projeto."','".$id_backup."',NOW(),'".date("H:i")."')");

echo utf8_encode("A versão do projeto foi armazenada com sucesso!");

?>