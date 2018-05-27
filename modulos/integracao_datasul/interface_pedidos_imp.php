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
				
				$pedido_vl_bruto = 0;
				$pedido_vl_desconto = 0;
				$pedido_vl_liquido = 0;
				$pedido_qtde_uidades =0;
				$ar_itens_do_pedido = array();
				for($i=0;$i<$ql;$i++){
					$tipo_linha = trim(substr($linha[$i],0,2));
					if($tipo_linha == 1){
						//Inicio do Tratamento se a linha for o cabecalho
						#################################################
						$id_pedido = trim(substr($linha[$i],15-$z,12));
						$id_pedido_repr = trim(substr($linha[$i],15-$z,12));
						$id_cliente = trim(substr($linha[$i],27-$z,9));
						$id_cond_pagto = trim(substr($linha[$i],93-$z,3));
						$dt_pedido = dtbr2enimp(trim(substr($linha[$i],67-$z,8)));
						$nome_abreviado = trim(substr($linha[$i],3-$z,12));//2
						$cgc_cliente = trim(substr($linha[$i],36-$z,19));//5
						$dt_pedido_emitido = dtbr2enimp(trim(substr($linha[$i],67-$z,8)));//7
						$cod_condicao_pagamento = trim(substr($linha[$i],93-$z,3));//11
						$id_estabelecimento = trim(substr($linha[$i],459-$z,3));//35
						$id_sit_ped = trim(substr($linha[$i],599-$z,3));//11
						$id_tab_preco = trim(substr($linha[$i],96-$z,3));//11
						$transporte = trim(substr($linha[$i],138-$z,12));
						#echo $transporte;
						//$tp_preco =trim(substr($linha[$i],108,2));//15
						//$cod_moeda =trim(substr($linha[$i],110,2));//16
						$nome_abrev_representante = trim(substr($linha[$i],150-$z,12));//23
						$natureza_operacao = trim(substr($linha[$i],381-$z,6));//29
						$natureza_operacao = strtoupper($natureza_operacao);
						
						#Desativado pois no momento não é usado na comparação
						//$cfop_nor1 = search_name('is_parametros_sistema','id_parametro','parametro','PED_EXP_REG01_NAT_OPER_SP_NOR');
						//$cfop_nor2 = search_name('is_parametros_sistema','id_parametro','parametro','PED_EXP_REG01_NAT_OPER_OUT_NOR');
						
						$cfop_comp1 = search_name('is_parametros_sistema','id_parametro','parametro','PED_EXP_REG01_NAT_OPER_SP_ELE');
						$cfop_comp2 = search_name('is_parametros_sistema','id_parametro','parametro','PED_EXP_REG01_NAT_OPER_OUT_ELE');
						
						$cfop_brin1 = search_name('is_parametros_sistema','id_parametro','parametro','PED_EXP_REG01_COD_NAT_SP_BRINDE');
						$cfop_brin2 = search_name('is_parametros_sistema','id_parametro','parametro','PED_EXP_REG01_COD_NAT_OUT_BRINDE');
						$cfop_brindts = search_name('is_parametros_sistema','id_parametro','parametro','PED_EXP_REG01_NAT_OPER_DTS_BRINDE');
						//$especie_pedido =trim(substr($linha[$i],469,2));//37
						if ($natureza_operacao == $cfop_brin1 || $natureza_operacao == $cfop_brin2 || $natureza_operacao == $cfop_brindts) { 
							$tipo_pedido = 'BRIN';
						}
						else {
							if ($natureza_operacao == $cfop_comp1 || $natureza_operacao == $cfop_comp2) {
								$tipo_pedido = 'COMP'; 
							}
							else {
								$tipo_pedido = 'NOR'; 
							}
						}
						
						$qry_pedido = mysql_query("SELECT * FROM is_pedidos WHERE id_pedido = '$id_pedido'");
						if(numrows($qry_pedido) == 0){
							$sql = ("INSERT INTO is_pedidos (dt_cadastro,hr_cadastro,id_usuario_cad,id_pedido,id_pedido_repr,id_empresa,id_tab_preco,id_cond_pagto,dt_pedido,id_pedido_cli,natureza_operacao,importado_erp,origem_pedido,tipo_pedido,id_sit_ped,id_estabelecimento,id_transporte) 
													VALUES ('".date("Y-m-d")."','".date("H:i")."','IMPORTDTS','$id_pedido','$id_pedido','$id_cliente','$id_tab_preco','$id_cond_pagto','$dt_pedido','$id_pedido','$natureza_operacao','S','IMPORTDTS','$tipo_pedido','$id_sit_ped','$id_estabelecimento','$transporte')");
							$qry = mysql_query($sql);
							if(!$qry){
								$relatorio .= "Pedido Não Inserido:-->".$id_pedido." SQL Executada: ".$sql."\n";
								$exec = 'erro';
								continue;
							}
							$total_insert = $total_insert * 1 + 1;
						}
						else{
							$sql = ("UPDATE is_pedidos SET 
							dt_alteracao = '".date("Y-m-d")."',
							hr_alteracao = '".date("H:i")."',
							id_usuario_alt = 'IMPORTDTS',
							id_empresa = '$id_cliente',
							id_cond_pagto = '$id_cond_pagto',
							dt_pedido = '$dt_pedido',
							natureza_operacao = '$natureza_operacao',
							importado_erp = 'S',
							origem_pedido = 'IMPORTDTS',
							tipo_pedido = '$tipo_pedido',
							id_sit_ped = '$id_sit_ped',
							id_tab_preco = '$id_tab_preco',
							id_transporte = '$transporte',
							id_estabelecimento = '$id_estabelecimento'
							WHERE id_pedido = '$id_pedido'");
							#echo $sql."<hr>";
							$qry = mysql_query($sql);
							if(!$qry){
								$relatorio .= "Pedido Não Atualizado:-->".$id_pedido." SQL Executada: ".$sql."\n\n";
								$exec = 'erro';
								continue;
							}
							$total_update = $total_update * 1 + 1;
						}
						
						//Fim do Tratamento se a linha for o cabecalho
						#################################################
					}
					elseif($tipo_linha == 7){
						//Inicio do Tratamento se a linha for o item
						#################################################
						$id_pedido = trim(substr($linha[$i],15-$z,12));
						$id_item = trim(substr($linha[$i],27-$z,5));
						$id_produto = trim(substr($linha[$i],32-$z,16));
						$ar_produto = farray(mysql_query("SELECT * FROM is_produtos WHERE id_produto = '$id_produto'"));
						$qtde_unidades = trim(substr($linha[$i],66-$z,11)) * 1;
						$qtde_unidades = $qtde_unidades / 10000;
						$vl_unitario = trim(substr($linha[$i],77-$z,14)) * 1;
						$vl_unitario = $vl_unitario / 100000;
						$pct_desconto = trim(substr($linha[$i],204-$z,50)) * 1;
						$vl_desconto = ($vl_unitario * $pct_desconto) / 100;
						$vl_total = ($vl_unitario) - ((($vl_unitario) * $pct_desconto) / 100);
						$vl_total_sem_desconto = ($vl_unitario * $qtde_unidades);
						//Definindo Linhas
						$linhas = trim(substr($linha[$i],419-$z,2000));
						$ar_linhas = explode("LINHA 2:",$linhas);
						$linha1 = str_replace("LINHA 1:",'',$ar_linhas[0]);
						$linha1 = addslashes(trim($linha1));
						$linha2 = str_replace("LINHA 2:",'',$ar_linhas[1]);
						$linha2 = addslashes(trim($linha2));
						
						$ar_itens_do_pedido[] = $id_item;
						
						if($ar_produto['qtde_por_caixa'] != 0){
							$qtde_cx = round(ceil(($qtde_unidades / $ar_produto['qtde_por_caixa'])),0);
						}
						if($qtde_cx == 0){
							$qtde_cx = 1;
						}
						//Setando os dados do cabecalho do pedido
						$pedido_vl_bruto += $vl_total_sem_desconto;
						$pedido_vl_desconto += $vl_desconto;
						$pedido_vl_liquido += ($vl_total * $qtde_unidades);
						$pedido_qtde_uidades += $qtde_unidades;
						$qry_item_pedido = mysql_query("SELECT numreg FROM is_pedidos_itens WHERE id_pedido = '$id_pedido' AND id_item = '$id_item'");
						if(numrows($qry_item_pedido) == 0){
							$sql = ("INSERT INTO is_pedidos_itens (dt_cadastro,hr_cadastro,id_usuario_cad,id_pedido,id_produto,qtde,vl_tabela,pct_desconto,vl_desconto,vl_total,total_unid,id_item)
							VALUES('".date("Y-m-d")."','".date("H:i")."','IMPORTDTS','$id_pedido','$id_produto','$qtde_cx','$vl_unitario','$pct_desconto','$vl_desconto','$vl_total','$qtde_unidades','$id_item')");
							$qry = mysql_query($sql);
							if(!$qry){
								$relatorio .= "Item Pedido Não Inserido:-->".$id_produto." Pedido-->".$id_pedido." SQL Executada: ".$sql."\n";
								$exec = 'erro';
							}
						}
						else{
							$sql = ("UPDATE is_pedidos_itens
							SET
							dt_alteracao = '".date("Y-m-d")."',
							hr_alteracao = '".date("H:i")."',
							id_usuario_alt = 'IMPORTDTS',
							id_produto = '$id_produto',
							qtde = '$qtde_cx',
							vl_tabela = '$vl_unitario',
							pct_desconto = '$pct_desconto',
							vl_desconto = '$vl_desconto',
							vl_total = '$vl_total',
							total_unid = '".($qtde_unidades*1)."'
							WHERE id_pedido = '$id_pedido' AND id_item = '$id_item'");
							$qry = mysql_query($sql);
							if(!$qry){
								$relatorio .= "Item Pedido Não Atualizado:-->".$id_produto." Pedido-->".$id_pedido." SQL Executada: ".$sql."\n";
								$exec = 'erro';
							}
						}
						//Fim do Tratamento se a linha for o item
						#################################################
					}
					//Se for a linha 9 pega o id do represnetante
					elseif($tipo_linha == 9){
						$id_representante = trim(substr($linha[$i],15-$z,5));
					}
					//Se for a linha 4 pega o obs do pedido
					elseif($tipo_linha == 4){
						$pedido_obs = trim(substr($linha[$i],3-$z,2000));
					}
					//Se for a linha 11 pega o status do item pedido
					elseif($tipo_linha == 11){
						$id_pedido = $id_pedido;
						$id_produto = trim(substr($linha[$i],3-$z,16));
						$id_situacao = trim(substr($linha[$i],67-$z,1));
						$qtde_faturada = trim(substr($linha[$i],70-$z,5));
						query("UPDATE is_pedidos_itens SET id_situacao = '$id_situacao', qtde_faturada = '$qtde_faturada' WHERE id_pedido = '$id_pedido' AND id_produto = '$id_produto'");
					}
					//Se é a ultima linha Fazer a atualizacao dos dados do cabecalho do pedido
					if($ql == ($i+1)){
						//Setando os dados do cabecalho do pedido
						$sql = "UPDATE is_pedidos SET ";
						
														if(!empty($pedido_vl_bruto)){$sql .= "vl_bruto = '".$pedido_vl_bruto."',";}
														if(!empty($pedido_vl_desconto)){$sql .= "vl_desconto = '$pedido_vl_desconto',";}
														if(!empty($pedido_vl_liquido)){$sql .= "vl_liquido = '$pedido_vl_liquido',";}
														if(!empty($pedido_qtde_unidades)){$sql .= "qtde_unid = '$pedido_qtde_unidades',";}
														if(!empty($id_representante)){$sql .= "id_representante = '$id_representante',";}
														if(!empty($pedido_obs)){$sql .= "obs = '$pedido_obs',";}
														if(!empty($linha1)){$sql .= "linha1 = '$linha1',";}
														if(!empty($linha2)){$sql .= "linha2 = '$linha2',";}
														$sql = substr($sql,0,strlen($sql)-1);
														$sql .= " WHERE id_pedido = '$id_pedido'";
						$qry = mysql_query($sql);
						if(!$qry){$exec = 'erro'; $relatorio .= "A Atualização do Pedido Falhou. Pedido-->".$id_pedido." SQL Executada: ".$sql."\n";}
						for($e=0;$e<count($ar_itens_do_pedido);$e++){
							$not_in .= "'".$ar_itens_do_pedido[$e]."',";
						}
						$not_in = substr($not_in,0,strlen($not_in)-1);
						mysql_query("DELETE FROM is_pedidos_itens WHERE id_item NOT IN ($not_in) AND id_pedido = '$id_pedido'");
						$not_in = '';
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
