<?
function dten2brsb($dt){
	return substr($dt,6,2).substr($dt,6,2).substr($dt,0,2);
}
function dtbr2enimp($dt){
	return substr($dt,4,4).'-'.substr($dt,2,2).'-'.substr($dt,0,2);
}
$z = 1;
set_time_limit(0);
include"../conecta.php";
require("../functions.php");

$ar_diretorio = farray(mysql_query("SELECT * FROM is_parametros_sistema WHERE id_parametro = 'IMP_PED_DIR'"));
$param_dir = $ar_diretorio['parametro'];
$ar_diretorio_move = farray(mysql_query("SELECT * FROM is_parametros_sistema WHERE id_parametro = 'PED_IMP_MOVE_DIR'"));
$param_dir_move = $ar_diretorio_move['parametro'];
$diretorio = opendir($param_dir);
$total_insert = 0;
$total_update = 0;
$total_n_imp = 0;
$reg_n_imp = '';
$qtde_arquivos = 0;
$id_relatorio = -1;
while ($arquivo = readdir($diretorio)) {
    if(!is_dir($arquivo)){
		$qtde_arquivos = $qtde_arquivos + 1;
		$id_relatorio = $id_relatorio + 1;
		if(file_exists($param_dir.$arquivo)) {
			$linha = file($param_dir.$arquivo);
			$ql = count($linha);
			//Verfica se há texto no arquivo
			if($ql > 0){
				for($i=0;$i<$ql;$i++){
					$tipo_linha = trim(substr($linha[$i],0,2));
					if($tipo_linha == 1){
						$id_tab_preco = trim(substr($linha[$i],2-$z,8));
						$nome_tab_preco = trim(substr($linha[$i],10-$z,40));
						$qry_tab_preco = query("SELECT * FROM is_tab_prec WHERE id_tab_preco = '".$id_tab_preco."'");
						if(numrows($qry_tab_preco) == 0){
							$sql = "INSERT INTO is_tab_preco (dt_cadastro,hr_cadastro,id_usuario_cad,id_tab_preco,nome_tab_preco) VALUES('".date("Y-m-d")."','".date("H:i")."','IMPORT','".$id_tab_preco."','".$nome_tab_preco."')";
						}
						else{
							$sql = "UPDATE is_tab_preco SET 
							dt_alteracao = '".date("Y-m-d")."',
							hr_alteracao = '".date("H:i")."',
							id_usuario_alt = 'IMPORT',
							nome_tab_preco = '".$nome_tab_preco."' WHERE id_tab_preco = '".$id_tab_preco."')";
						}
					}
					elseif($tipo_linha == 3){
						$id_produto = trim(substr($linha[$i],2-$z,16));
						$id_tab_preco = trim(substr($linha[$i],18-$z,8));
					}
				}
			}
			if($exec != 'erro'){
				if(!file_exists($param_dir_move.$arquivo)){
					rename($param_dir.$arquivo,$param_dir_move.$arquivo);
				}
				else{
					rename($param_dir.$arquivo,$param_dir_move.date("YmdHis").$arquivo);
				}
			}
			$exec = '';
		}
	}
}
if(!empty($relatorio)){
	$ar_diretorio_relat = farray(query("SELECT * FROM is_parametros_sistema WHERE id_parametro = 'PED_IMP_RELAT_DIR'"));
	$param_dir_relat = $ar_diretorio_relat['parametro'];
	$fp = fopen($param_dir_relat."Erro_Importacao_Pedido_".date("Ymd_His").".txt","w+");
	fwrite($fp,$relatorio);
	fclose($fp);
}

?>