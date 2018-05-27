<?
if($opc == 'alterar' || $opc == 'incluir') {
	if($id_funcao == 'atividades_cad_lista'){
		//Tratando a atividade postada e suas dependentes se for necessсrio
		trata_atividade($ar_ativ_before['id_atividade'], 'PAI', $ar_ativ_before['dt_inicio'], $ar_ativ_before['dt_prev_fim']);
		//Pegando as informaчѕes da atividade
		$ar_ativ = mysql_fetch_array(query("SELECT * FROM is_atividades WHERE id_atividade = '".$_POST['edtid_atividade']."'"));
		//Atualizando Sub-Projetos do Projeto
		$sql_sub_projeto = query("SELECT * FROM is_projeto_macro_atividade WHERE id_projeto = '".$ar_ativ['id_projeto']."'");
		while($ar_sub_projeto = mysql_fetch_array($sql_sub_projeto)){
			$menor_dt_sub_projeto = mysql_fetch_array(query("SELECT MIN(dt_inicio) AS menor_dt FROM is_atividades WHERE id_sub_projeto = '".$ar_sub_projeto['id_sub_projeto']."'"));
			$maior_dt_sub_projeto = mysql_fetch_array(query("SELECT MAX(dt_prev_fim) AS maior_dt FROM is_atividades WHERE id_sub_projeto = '".$ar_sub_projeto['id_sub_projeto']."'"));
			query("UPDATE is_projeto_sub SET dt_inicio = '".$menor_dt_sub_projeto['menor_dt']."', dt_prev_fim = '".$maior_dt_sub_projeto['maior_dt']."' WHERE id_sub_projeto = '".$ar_sub_projeto['id_sub_projeto']."'");
		}
		
		//Atualizando Macro-Atividade do Projeto
		$sql_m_a = query("SELECT * FROM is_projeto_macro_atividade WHERE id_projeto = '".$ar_ativ['id_projeto']."'");
		while($ar_m_a = mysql_fetch_array($sql_m_a)){
			$menor_dt_m_a = mysql_fetch_array(query("SELECT MIN(dt_inicio) AS menor_dt FROM is_atividades WHERE id_macro_atividade = '".$ar_m_a['id_macro_atividade']."'"));
			$maior_dt_m_a = mysql_fetch_array(query("SELECT MAX(dt_prev_fim) AS maior_dt FROM is_atividades WHERE id_macro_atividade = '".$ar_m_a['id_macro_atividade']."'"));
			query("UPDATE is_projeto_macro_atividade SET dt_prev_inicio = '".$menor_dt_m_a['menor_dt']."', dt_prev_fim = '".$maior_dt_m_a['maior_dt']."' WHERE id_macro_atividade = '".$ar_m_a['id_macro_atividade']."'");
		}
		//Atualizando Aчуo do Projeto
		$sql_acao = query("SELECT * FROM is_projeto_acoes WHERE id_projeto = '".$ar_ativ['id_projeto']."'");
		while($ar_acao = mysql_fetch_array($sql_acao)){
			$menor_dt_acao = mysql_fetch_array(query("SELECT MIN(dt_inicio) AS menor_dt FROM is_atividades WHERE id_acao = '".$ar_acao['id_acao']."'"));
			$maior_dt_acao = mysql_fetch_array(query("SELECT MAX(dt_prev_fim) AS maior_dt FROM is_atividades WHERE id_acao = '".$ar_acao['id_acao']."'"));
			query("UPDATE is_projeto_acoes SET dt_inicio = '".$menor_dt_acao['menor_dt']."', dt_prev_fim = '".$maior_dt_acao['maior_dt']."' WHERE id_acao = '".$ar_acao['id_acao']."'");
		}

		//Atualizando o Projeto
		$menor_dt = mysql_fetch_array(query("SELECT MIN(dt_inicio) AS menor_dt FROM is_atividades WHERE id_projeto = '".$_POST['edtid_projeto']."'"));
		$maior_dt = mysql_fetch_array(query("SELECT MAX(dt_prev_fim) AS maior_dt FROM is_atividades WHERE id_projeto = '".$_POST['edtid_projeto']."'"));
		
		//Atualizando quantidade de horas e custo do projeto
		$sql_ativ = query("SELECT * FROM is_atividades WHERE id_projeto = '".$_POST['edtid_projeto']."'");
		$tempo = 0;
		$custo = 0;
		while($ar_ativ = mysql_fetch_array($sql_ativ)){
			
			$ar_user = mysql_fetch_array(query("SELECT * FROM is_usuarios WHERE id_usuario = '".$ar_ativ['id_usuario_resp']."'"));
			//Verificando se a atividade termina hoje ou antes
			$tempo += $ar_ativ['tempo_real'];
			if(!empty($ar_ativ['tempo_real']) && $ar_ativ['tempo_real'] != 0){
				$custo += $ar_ativ['tempo_real'] * $ar_user['hr_custo'];
			}
		}
		
		query("UPDATE is_projetos SET dt_inicio = '".$menor_dt['menor_dt']."', dt_prev_fim = '".$maior_dt['maior_dt']."', tempo_real = '".$tempo."', custo_real = '".$custo."' WHERE id_projeto = '".$_POST['edtid_projeto']."'");
	}
}
?>