<?
require_once("../conecta.php");
require_once("../functions.php");
set_time_limit(0);
//mysql_query("TRUNCATE TABLE is_atividades");
//mysql_query("TRUNCATE TABLE is_titulos_telecobranca");
$qtde_dias_gerar_telecobranca = GetParam('QDAVGTC');
$qtde_ativ_dia_telecobranca = GetParam('QAPDGT');
$dt_hoje_mais_x_dias = date("Y-m-d",strtotime(" + ".$qtde_dias_gerar_telecobranca." days"));
$qry_titulos_first = query("SELECT * FROM is_titulos 
WHERE vl_saldo > 0 
AND DATE(DATE_ADD(dt_vencimento, INTERVAL $qtde_dias_gerar_telecobranca DAY)) <= '".date("Y-m-d")."' 
AND id_pedido NOT IN(SELECT id_pedido FROM is_atividades WHERE id_tp_atividade = 'SAC' AND (id_caso_motivo = 'EM' OR id_caso_motivo = 'DT' OR id_caso_motivo = 'DP' OR id_caso_motivo = 'MA')) 
AND id_cliente NOT IN(
						SELECT id_cliente FROM is_atividades 
						WHERE (id_tp_atividade = 'CONFEN' AND id_situacao = 'P')
						
					  )
ORDER BY dt_vencimento DESC, vl_titulo DESC");
$qry_usuarios = query("SELECT id_usuario FROM is_usuarios WHERE id_perfil = '13' ORDER BY RAND()");
$array_usuarios = array();
while($ar_usuarios = farray($qry_usuarios)){
	$array_usuarios[] = $ar_usuarios['id_usuario'];
}
$qtde_usuarios = count($array_usuarios)-1;
$qtde_ativ_dia_telecobranca = $qtde_ativ_dia_telecobranca * ($qtde_usuarios + 1);
$count = 0;
$count_row = 0;
$dt_inicio = date("Y-m-d");
$count_ativ_add = 0;
while($ar_titulos_first = farray($qry_titulos_first)){
	$qry_titulos = query("SELECT * FROM is_titulos 
	WHERE vl_saldo > 0 
	AND DATE(DATE_ADD(dt_vencimento, INTERVAL $qtde_dias_gerar_telecobranca DAY)) <= '".date("Y-m-d")."' 
	AND id_pedido NOT IN(SELECT id_pedido FROM is_atividades WHERE id_tp_atividade = 'SAC' AND (id_caso_motivo = 'EM' OR id_caso_motivo = 'DT' OR id_caso_motivo = 'DP' OR id_caso_motivo = 'MA')) 
	AND id_cliente NOT IN(
							SELECT id_cliente FROM is_atividades 
							WHERE (id_tp_atividade = 'CONFEN' AND id_situacao = 'P')
							
						  )
	AND id_cliente = 
	ORDER BY dt_vencimento DESC, vl_titulo DESC");
	while($ar_titulos = farray($qry_titulos)){
		$qry_ativ = query("SELECT * FROM is_atividades WHERE id_cliente = '".$ar_titulos['id_cliente']."' AND (id_situacao = 'P' OR id_situacao = 'AG') AND id_tp_atividade = 'COBR'");
		$numrows_ativ = numrows($qry_ativ);
		#Se já houver atividade de cobrança para o cliente
		if($numrows_ativ > 0){
			$ar_ativ = farray($qry_ativ);
			$qry_ativ_tit = query("SELECT * FROM is_titulos_telecobranca WHERE id_atividade = '".$ar_ativ['id_atividade']."' AND id_titulo = '".$ar_titulos['id_titulo']."' AND n_parcela = '".$ar_titulos['n_parcela']."'");
			$numrows_ativ_tit = numrows($qry_ativ_tit);
			#Se este título já estiver na atividade
			if($numrows_ativ_tit > 0){
				query("UPDATE is_titulos_telecobranca SET dt_alteracao = '".date("Y-m-d")."',hr_alteracao='".date("H:i")."',id_usuario_alt='".'WORKFLOW-TELECOBRANÇA'."',dt_vencimento='".$ar_titulos['dt_vencimento']."',vl_titulo='".$ar_titulos['vl_titulo']."',vl_saldo='".$ar_titulos['vl_saldo']."' WHERE id_atividade = '".$ar_ativ['id_atividade']."' AND id_titulo = '".$ar_titulos['id_titulo']."' AND n_parcela = '".$ar_titulos['n_parcela']."'",2);
			}
			#Se não insere um novo título para a atividade
			else{
				#=========
				$sql_qry_insert_tit = "INSERT INTO is_titulos_telecobranca (dt_cadastro,hr_cadastro,id_usuario_cad,id_atividade,id_titulo,n_parcela,id_pedido,id_cliente,dt_emissao,dt_vencimento,dt_vencimento_original,";
				if($ar_titulos['dt_pagamento'] != ''){
					$sql_qry_insert_tit .= "dt_pagamento,";
				}
				$sql_qry_insert_tit .= "vl_titulo,vl_saldo,id_situacao,historico)
						VALUES('".date("Y-m-d")."','".date("H:i")."','".'WORKFLOW-TELECOBRANÇA'."','".$ar_ativ['id_atividade']."','".$ar_titulos['id_titulo']."','".$ar_titulos['n_parcela']."','".$ar_titulos['id_pedido']."','".$ar_titulos['id_cliente']."','".$ar_titulos['dt_emissao']."','".$ar_titulos['dt_vencimento']."',";
						if($ar_titulos['dt_pagamento'] != ''){
							$sql_qry_insert_tit .= "'".$ar_titulos['dt_pagamento']."',";
						}
				$sql_qry_insert_tit .= "'".$ar_titulos['dt_vencimento']."','".$ar_titulos['vl_titulo']."','".$ar_titulos['vl_saldo']."','".$ar_titulos['id_situacao']."','".$ar_titulos['historico']."')";
				#=========
				query($sql_qry_insert_tit);
			}
		}
		#Se não for econtrado atividade cria uma e insere o título
		else{
			if($count_ativ_add >= $qtde_ativ_dia_telecobranca){exit;}
			if($count >= $qtde_usuarios){
				$count = 0;
			}
			else{
				$count += 1;
			}
			$count_ativ_add +=1;
			$id_atividade_new = max_id('is_atividades','id_atividade',6,'0');
			$id_tp_atividade = 'COBR';
			$ar_pessoa = farray(query("SELECT * FROM is_pessoas WHERE id_pessoa = '".$ar_titulos['id_cliente']."'"));
			$id_usuario_resp = $array_usuarios[$count];
			$id_cliente = $ar_titulos['id_cliente'];
			$telefones = $ar_pessoa['tel1'];
			$telefones .= ($ar_pessoa['tel2'] != '')?'/'.$ar_pessoa['tel2']:'';
			$assunto = "Cobrança de título(s): ";
			$n_contatos = 0;
			if(($count_row == $qtde_ativ_dia_telecobranca) && $count_row > 0){
				$count_row = 0;
				$dt_inicio = dt_fds(date("Y-m-d",strtotime($dt_inicio." + 1 day")));
			}
			$count_row += 1;	
			$id_situacao = 'P';
			$sql = "INSERT INTO is_atividades(id_usuario_cad,dt_cadastro,hr_cadastro,id_atividade,id_tp_atividade,assunto,id_usuario_resp,id_cliente,telefones,n_contatos,dt_inicio,id_situacao) VALUES('WORKFLOW','".date("Y-m-d")."','".date("H:i")."','$id_atividade_new','$id_tp_atividade','$assunto','$id_usuario_resp','$id_cliente','$telefones','$n_contatos','$dt_inicio','$id_situacao')";
			query($sql,2);
			#=========
			$sql_qry_insert_tit = "INSERT INTO is_titulos_telecobranca (dt_cadastro,hr_cadastro,id_usuario_cad,id_atividade,id_titulo,n_parcela,id_pedido,id_cliente,dt_emissao,dt_vencimento,dt_vencimento_original,";
			if($ar_titulos['dt_pagamento'] != ''){
				$sql_qry_insert_tit .= "dt_pagamento,";
			}
			$sql_qry_insert_tit .= "vl_titulo,vl_saldo,id_situacao,historico)
					VALUES('".date("Y-m-d")."','".date("H:i")."','".'WORKFLOW-TELECOBRANÇA'."','".$id_atividade_new."','".$ar_titulos['id_titulo']."','".$ar_titulos['n_parcela']."','".$ar_titulos['id_pedido']."','".$ar_titulos['id_cliente']."','".$ar_titulos['dt_emissao']."','".$ar_titulos['dt_vencimento']."',";
					if($ar_titulos['dt_pagamento'] != ''){
						$sql_qry_insert_tit .= "'".$ar_titulos['dt_pagamento']."',";
					}
			$sql_qry_insert_tit .= "'".$ar_titulos['dt_vencimento']."','".$ar_titulos['vl_titulo']."','".$ar_titulos['vl_saldo']."','".$ar_titulos['id_situacao']."','".$ar_titulos['historico']."')";
			#=========
			query($sql_qry_insert_tit,2);
		}
	#	$sql_n_titulos .= " (t2.id_titulo = '".$ar_titulo['id_titulo']."' AND t2.n_parcela = '".$ar_titulo['n_parcela']."') OR ";
	#	$sql = "INSERT INTO is_atividades(id_usuario_cad,dt_cadastro,hr_cadastro,id_atividade,id_tp_atividade,assunto,id_usuario_resp,id_cliente,telefones,n_contatos,vl_total_titulos,dt_inicio,id_situacao) VALUES('WORKFLOW','".date("Y-m-d")."','".date("H:i")."','$id_atividade','$id_tp_atividade','$assunto','$id_usuario_resp','$id_cliente','$telefones','$n_contatos','$valor_total_titulos','$dt_inicio','$id_situacao')";
	}
}
?>