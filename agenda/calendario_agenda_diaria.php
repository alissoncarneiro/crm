<?php
require_once("../conecta.php");
require_once("../functions.php");
@session_start();

echo '<br><br>';
$semana_name = array("Sun" => "Domingo", "Mon" => "Segunda-Feira", "Tue" => "Ter&ccedil;a-Feira","Wed" => "Quarta-Feira", "Thu" => "Quinta-Feira", "Fri" => "Sexta-Feira", "Sat" => "S&aacute;bado");
$semana_n = array("Sun" => "1", "Mon" => "2", "Tue" => "3","Wed" => "4", "Thu" => "5", "Fri" => "6", "Sat" => "7");
$mes_name = array("", "Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");


$get_dt_inicio = $_GET['dt_inicio'];
$dt_inicio = (!empty($get_dt_inicio))?dtbr2en($get_dt_inicio):date("Y-m-d");

$base_height = 130;
$base_left = 140;
$base_height_atual = $base_height + 100;

//Definindo valores padrao para horarios do gr?fico
$ar_hr_padrao = farray(query("SELECT * FROM is_hr_grafico WHERE id_horario = 'GRAFICOAGENDA'"));
$hr_inicio = $ar_hr_padrao['hr_inicio'];
$hr_fim = $ar_hr_padrao['hr_fim'];

$hr_inicio_n = explode(':',$hr_inicio);
$hr_inicio_n = round($hr_inicio_n[0],0);

$hr_fim_n = explode(':',$hr_fim);
$hr_fim_n = round($hr_fim_n[0],0);

$dt_inicio_loop = $dt_inicio;
$dias = $qty_dias_semana;


//Definido se houve sele??o de usu?rio, se n?o usa-se o usuario logado
$id_usuario_logado = $_SESSION['id_usuario'];
$ar_user = farray(query("SELECT * FROM is_usuario WHERE id_usuario = '".$id_usuario_logado."'"));
$ar_perfil = farray(query("SELECT * FROM is_perfil WHERE id_perfil = '".$ar_user['id_perfil']."'"));
if($ar_perfil['sn_bloquear_leitura'] == '1'){
	if(!empty($_GET['usuario'])){
		$id_usuario_logado = $_GET['usuario'];
	}
	else{
		$id_usuario_logado = $_SESSION['id_usuario'];
	}
}

$left = $base_left + 55;
$height = $base_height_atual;
//Definindo tamanho das colunas 
$size_width = 800;
//Criando array guardar quantas atividades ser?o exibidas
$ar_ativ_back_exibidas = array();
//Simulando a cria??o das atividades que tem inicio e fim em dias diferentes

	//Verfificando quantas atividades ser?o exibidas abaixo do nome dos dias
	$qry_ativ_back = query("SELECT t1.* FROM is_atividade t1 LEFT JOIN is_atividade_participante_int t2 ON t1.numreg = t2.id_atividade WHERE (t2.id_usuario = '".$id_usuario_logado."' OR t1.id_usuario_resp ='".$id_usuario_logado."') AND ('".$dt_inicio_loop."' BETWEEN t1.dt_inicio AND t1.dt_prev_fim) AND dt_inicio <> dt_prev_fim ORDER BY dt_inicio,hr_inicio ASC");

	$cor_ct = "#000000";
	$cor_t_bg = "#BFD2EA";
	$style_montar = 'z-index:500;background-color:#3868A9;border:1px solid #000000;opacity: 0.3;filter: progid:DXImageTransform.Microsoft.Alpha(opacity=30);';
	while($ar_ativ_back = farray($qry_ativ_back)){
		if(numrows($qry_ativ_back) > 0 && $ar_ativ_back['dt_inicio'] != $dt_inicio_loop && $ar_ativ_back['dt_prev_fim'] != $dt_inicio_loop){
			if(array_search($ar_ativ_back['numreg'],$ar_ativ_back_exibidas) < -1){$ar_ativ_back_exibidas[] = $ar_ativ_back['numreg'];}
		}
		if(numrows($qry_ativ_back) > 0 && $ar_ativ_back['dt_inicio'] == $dt_inicio_loop){
			if(array_search($ar_ativ_back['numreg'],$ar_ativ_back_exibidas) < -1){$ar_ativ_back_exibidas[] = $ar_ativ_back['numreg'];}
		}
		if(numrows($qry_ativ_back) > 0 && $ar_ativ_back['dt_prev_fim'] == $dt_inicio_loop){
			if(array_search($ar_ativ_back['numreg'],$ar_ativ_back_exibidas) < -1){
				$ar_ativ_back_exibidas[] = $ar_ativ_back['numreg'];
			}
		}
	}
#############################################################################################################################################################
	//Somente para definir o scroll
	//Exibindo as divs dos compromissos
	$qry_ativ = query("SELECT t1.* FROM is_atividade t1 LEFT JOIN is_atividade_participante_int t2 ON t1.numreg = t2.id_atividade WHERE (t2.id_usuario = '".$id_usuario_logado."' OR t1.id_usuario_resp ='".$id_usuario_logado."') AND t1.dt_inicio = '".$dt_inicio_loop."' AND dt_inicio = dt_prev_fim ORDER BY dt_inicio,hr_inicio ASC");
	$top_last_ativ = 0;
	while($ar = farray($qry_ativ)){
		
		$inicio = diferenca_hr($hr_inicio,$ar['hr_inicio'],'','S') + $base_height_atual;
		// Se a atividade come?ar no mesmo dia
		if($ar['dt_inicio'] == $ar['dt_prev_fim']){
			//Se esta atividade estiver em conflito com outras
			if(!empty($ar['ativ_conflitantes'])){
				$ar_conflitantes = explode(',',$ar['ativ_conflitantes']);
				for($w = 0; $w < count($ar_conflitantes) ; $w++){
					$numregs_conflitantes .= "'".$ar_conflitantes[$w]."',";
					$ar_nao_exibir[] = $ar_conflitantes[$w];
				}
				$result_search = array_search($ar['numreg'],$ar_nao_exibir);
				if($result_search < -1){
					$top_last_ativ = $inicio;

				}
			}
			//Se n?o estiver em conflito
			else{
				$top_last_ativ = $inicio;
			}
		}
	}
	//Somente para definir o scroll
#############################################################################################################################################################

	//Pegando as atividades que dever?o ser exibidas	
	$qry_ativ = query("SELECT t1.* FROM is_atividade t1 LEFT JOIN is_atividade_participante_int t2 ON t1.numreg = t2.id_atividade WHERE (t2.id_usuario = '".$id_usuario_logado."' OR t1.id_usuario_resp ='".$id_usuario_logado."') AND t1.dt_inicio = '".$dt_inicio_loop."' AND dt_inicio = dt_prev_fim ORDER BY dt_inicio,hr_inicio ASC");
	//Atividades azuis de cima do gr?fico, query usada s? para exibir quantidade de atividades no dia
	$qry_ativ_back = query("SELECT t1.* FROM is_atividade t1 LEFT JOIN is_atividade_participante_int t2 ON t1.numreg = t2.id_atividade WHERE (t2.id_usuario = '".$id_usuario_logado."' OR t1.id_usuario_resp ='".$id_usuario_logado."') AND ('".$dt_inicio_loop."' BETWEEN t1.dt_inicio AND t1.dt_prev_fim) AND dt_inicio <> dt_prev_fim ORDER BY dt_inicio,hr_inicio ASC");
	$ativ_no_dia = numrows($qry_ativ) + numrows($qry_ativ_back);
	//Definindo se h? atividades na coluna
	if($ativ_no_dia == 0){
		$echo_atividades = '';
	}
	elseif($ativ_no_dia == 1){
		$echo_atividades = '<br><a title="Clique para ver a &uacute;ltima" href="javascript:window.scroll(0,'.$top_last_ativ.');">'.$ativ_no_dia.' atividade</a>';
		echo '<div style="z-index:200;position:absolute;top:'.($height-20).'px;left:'.($left+$size_width-20).'px;"><a title="Clique para ver a &uacute;ltima" href="javascript:window.scroll(0,'.$top_last_ativ.');"><img src="agenda/images/btn_down.png" border="0"></a></div>';
	}
	else{
		$echo_atividades = '<br><a title="Clique para ver a &uacute;ltima" href="javascript:window.scroll(0,'.$top_last_ativ.');">'.$ativ_no_dia.' atividades</a>';
		echo '<div style="z-index:200;position:absolute;top:'.($height-20).'px;left:'.($left+$size_width-20).'px;"><a title="Clique para ver a &uacute;ltima" href="javascript:window.scroll(0,'.$top_last_ativ.');"><img src="agenda/images/btn_down.png" border="0"></a></div>';
	}
	//T?tulo da coluna
	echo '<div class="title_day" style="width:'.$size_width.'px;top:'.($height-26).'px;left:'.($left).'px;">'.substr($dt_inicio_loop,8,2).' '.$semana_name[date("D",strtotime($dt_inicio_loop))].$echo_atividades.'</div>';
	$top_last_ativ = $inicio;
	$left = $left + $size_width;
	$dt_inicio_loop = date("Y-m-d",strtotime($dt_inicio_loop." + 1 day"));

//Resetnado os valores que foram alterados pela simula??o
$dt_inicio_loop = $dt_inicio;
$base_height_atual = $base_height_atual + (20 * count($ar_ativ_back_exibidas));
$ar_ativ_back_exibidas = array();

//Montando a barra lateral de horas
$height = $base_height_atual;
$loop_hr = 0;
$hora = explode(":",$hr_inicio);
$hr_inicial = $hora[0];
for($i=$hr_inicio_n; $i <= $hr_fim_n; $i++){
	$link_link = 'gera_cad_detalhe.php?pfuncao=atividades_cad_lista&pnumreg=-1&dt_inicio='.dten2br($dt_inicio_loop).'&dt_prev_fim='.dten2br($dt_inicio_loop).'&hr_inicio='.$hr_inicial.':00&hr_prev_fim='.$hr_inicial.':00';
	$link_onclick = 'window.open(\''.$link_link.'\',\'inserir_novo_registro\',\'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=100,left=100\');';
	echo '<div onclick="'.$link_onclick.'" class="cal_time" style="cursor:pointer;z-index:1;top:'.$height.'px;left:'.$base_left.'px;">'.(($i<10)?'0':'').$i.'<span class="cal_time_font_small">00</span></div>';
	$hr_inicial = $hr_inicial + 1;
	$height = $height + 60;
	$loop_hr = $loop_hr + 1;
}
//Exibindo hora de agora
$hr_agora = diferenca_hr($hr_inicio,date("H:i"),'','S') + $base_height_atual;
echo '<div class="div_hr_agora" style="z-index:1000; top:'.$hr_agora.'px; left:'.$base_left.'px; width:52px;"></div>';

//Resetando os valores que foram altarados
$base_left = $base_left + 55;
$left = $base_left;
$dt_inicio_loop = $dt_inicio;
$dias = $qty_dias_semana;
//Criando e exibindo as atividades que s?o bg azul claro e criando o topo do
	//Criando a tabela temporaria
	$qry_ativ_back = query("SELECT t1.* FROM is_atividade t1 LEFT JOIN is_atividade_participante_int t2 ON t1.numreg = t2.id_atividade WHERE (t2.id_usuario = '".$id_usuario_logado."' OR t1.id_usuario_resp ='".$id_usuario_logado."') AND ('".$dt_inicio_loop."' BETWEEN t1.dt_inicio AND t1.dt_prev_fim) AND dt_inicio <> dt_prev_fim ORDER BY dt_inicio,hr_inicio ASC");
	//echo numrows($qry_ativ_back)."-";
	$show = true;
	while($ar_ativ_back = farray($qry_ativ_back)){
		//Setado para 1=1 para sempre exibir
		if(1==1){//$show == true){
			if(numrows($qry_ativ_back) > 0 && $ar_ativ_back['dt_inicio'] != $dt_inicio_loop && $ar_ativ_back['dt_prev_fim'] != $dt_inicio_loop){
				echo utf8_encode(monta_div_ativ_all('','','',$base_height_atual,(diferenca_hr($hr_inicio,$hr_fim,'','S')+59),$left,$size_width,'',$style_montar));
				if(array_search($ar_ativ_back['numreg'],$ar_ativ_back_exibidas) < -1){$ar_ativ_back_exibidas[] = $ar_ativ_back['numreg'];}
				$show = false;
			}
			if(numrows($qry_ativ_back) > 0 && $ar_ativ_back['dt_inicio'] == $dt_inicio_loop){
				echo utf8_encode(monta_div_ativ_all('','','',(diferenca_hr($hr_inicio,$ar_ativ_back['hr_inicio'],'','S') + $base_height_atual),(diferenca_hr($ar_ativ_back['hr_inicio'],$hr_fim,'','S')+59),$left,$size_width,'',$style_montar));
				if(array_search($ar_ativ_back['numreg'],$ar_ativ_back_exibidas) < -1){$ar_ativ_back_exibidas[] = $ar_ativ_back['numreg'];}
				$show = false;
			}
			if(numrows($qry_ativ_back) > 0 && $ar_ativ_back['dt_prev_fim'] == $dt_inicio_loop){
				echo utf8_encode(monta_div_ativ_all('','','',$base_height_atual,(diferenca_hr($hr_inicio,$ar_ativ_back['hr_prev_fim'],'','S')-1),$left,$size_width,'',$style_montar));
				if(array_search($ar_ativ_back['numreg'],$ar_ativ_back_exibidas) < -1){$ar_ativ_back_exibidas[] = $ar_ativ_back['numreg'];}
				$show = false;
			}
		}
	}
	$left = $left + $size_width;
	$dt_inicio_loop = date("Y-m-d",strtotime($dt_inicio_loop." + 1 day"));

//Resetando os valores que foram modificados
$dt_inicio_loop = $dt_inicio;
$left = $base_left;
//Se vai ser exibida alguma atividade no topo
if(count($ar_ativ_back_exibidas) > 0){
	$style_div_back = 'background:#A5BFE1;position:absolute;top:'.($base_height_atual - (20 * count($ar_ativ_back_exibidas))).'px;left:'.$left.'px;width:'.($size_width).'px;height:'.(20 * count($ar_ativ_back_exibidas)-2).'px;border:1px solid #000000;';
	echo '<div style="'.$style_div_back.'"></div>';
	$initial_height = $base_height_atual - (20 * count($ar_ativ_back_exibidas));
	//Para cada atividade faz um loop
	for($w = 0; $w < count($ar_ativ_back_exibidas) ; $w++){
		$qry_atividade = farray(query("SELECT * FROM is_atividade where numreg='".$ar_ativ_back_exibidas[$w]."'"));
		$dt_ultimo_dia =  date("Y-m-d",strtotime($dt_inicio_loop." + ".($dias-1)." days"));
		//Se a atividade tem inicio no primeiro dia ou antes
		if($qry_atividade['dt_inicio'] <= $dt_inicio_loop){
			$left_div = $left;
			if($qry_atividade['dt_prev_fim'] >= $dt_ultimo_dia){
				$width_div = $size_width;
			}
			else{
				$width_div = $size_width;
			}
		}
		//Se n?o come?a antes da data in?cio do gr?fico
		elseif($qry_atividade['dt_inicio'] > $dt_inicio_loop){
			$left_div = $left + $size_width * diferenca_dt($dt_inicio_loop,$qry_atividade['dt_inicio']);
			if($qry_atividade['dt_prev_fim'] >= $dt_ultimo_dia){
				$width_div = $size_width;
			}
			else{
				$width_div = $size_width;
			}
		}
		//Definindo se haver? imagem de antes
		//if($qry_atividade['dt_inicio'] < $dt_inicio_loop){
			//$img_back = '<img src="agenda/images/btn_back.png" width="15" height="15" style="cursor:pointer;" />';
		//}
		//else{
			//$img_back = "";
		//}
		//Definindo se haver? imagem de depois
		//if($qry_atividade['dt_prev_fim'] > $dt_ultimo_dia){
			//$img_next = '<img src="agenda/images/btn_next.png" width="15" height="15" style="cursor:pointer;" />';
		//}
		//else{
			//$img_next = "";
		//}
		$link = ' onclick="window.open(\'gera_cad_detalhe.php?pfuncao=atividades_cad_lista&pnumreg='.$qry_atividade['numreg'].'\',\'inserir_novo_registro\',\'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=100,left=100\'); return false;"';

		$style_div_back_bg = 'cursor:pointer; line-height:18px; overflow:hidden; position:absolute; top:'.($initial_height).'px;left:'.$left_div.'px;width:'.($width_div).'px;height:18px;';
		echo utf8_encode('<div '.$link.monta_div_info($qry_atividade['numreg']).'style="'.$style_div_back_bg.'">'.table_cca($qry_atividade['assunto'],$width_div,'10','#000000','#E9E9E9').'</div>');
		$initial_height = $initial_height + 19;
	}
}

//Loop para montar coluna por coluna do grafica
	//Pegando as atividades que dever?o ser exibidas	
	$qry_ativ = query("SELECT t1.* FROM is_atividade t1 LEFT JOIN is_atividade_participante_int t2 ON t1.numreg = t2.id_atividade WHERE (t2.id_usuario = '".$id_usuario_logado."' OR t1.id_usuario_resp ='".$id_usuario_logado."') AND t1.dt_inicio = '".$dt_inicio_loop."' AND dt_inicio = dt_prev_fim ORDER BY dt_inicio,hr_inicio ASC");
	//Criando a tabela temporaria
        if ($tipoBanco=="mysql") {
            query("DROP TEMPORARY TABLE IF EXISTS tmp_ativ");
        
            mysql_query("CREATE TEMPORARY TABLE tmp_ativ (
				numreg VARCHAR(45),
				assunto varchar(500),
				id_pessoa varchar(100),
				id_situacao varchar(100),
				dt_inicio DATE,
				dt_prev_fim DATE,
				hr_inicio VARCHAR(45),
				hr_prev_fim VARCHAR(45),
				inicio VARCHAR(45),
				duracao VARCHAR(45),
				fim VARCHAR(45),
				ativ_conflitantes VARCHAR(500))
				ENGINE = MyISAM;") or die (mysql_error())."654";
            $prefixo_temp = "";
        }
        if ($tipoBanco=="mssql") {
            query("CREATE TABLE #tmp_ativ (
				numreg VARCHAR(45),
				assunto varchar(255),
				id_pessoa varchar(100),
				id_situacao varchar(100),
				dt_inicio DATETIME,
				dt_prev_fim DATETIME,
				hr_inicio VARCHAR(45),
				hr_prev_fim VARCHAR(45),
				inicio VARCHAR(45),
				duracao VARCHAR(45),
				fim VARCHAR(45),
				ativ_conflitantes VARCHAR(255) NULL )
				") or die (mssql_error())."654";
            $prefixo_temp = "#";

        }
	//query("DELETE FROM tmp_ativ") or die (mysql_error()."122");
	//Setando inicio duracao e fim de cada atividade
	while($ar = farray($qry_ativ)){
		//S? insere se ainda n?o existir
		$qry_existe = query("SELECT * FROM ".$prefixo_temp."tmp_ativ WHERE numreg = '".$ar['numreg']."'") or die (mysql_error()."415");
		if(numrows($qry_existe) == 0){
			$duracao_tmp = diferenca_hr($ar['hr_inicio'],$ar['hr_prev_fim'],'','S');
			$inicio_tmp = diferenca_hr($hr_inicio,$ar['hr_inicio'],'','S') + $base_height_atual;
			$fim_tmp = $inicio_tmp + $duracao_tmp;
			//echo "ativ=".$ar['numreg']."inicio = ".$inicio_tmp.",fim=".$fim_tmp."<hr>";
			$sql = "INSERT INTO ".$prefixo_temp."tmp_ativ (numreg,assunto,id_pessoa,id_situacao,dt_inicio,dt_prev_fim,hr_inicio,hr_prev_fim,inicio,duracao,fim) VALUES('".$ar['numreg']."','".$ar['assunto']."','".$ar['id_pessoa']."','".$ar['id_situacao']."','".$ar['dt_inicio']."','".$ar['dt_prev_fim']."','".$ar['hr_inicio']."','".$ar['hr_prev_fim']."','".$inicio_tmp."','".$duracao_tmp."','".$fim_tmp."')";
			query($sql) or die ("465");
		}
	}
	
	//Setando os conflitos de cada atividade
	$qry_tmp = query("SELECT * FROM ".$prefixo_temp."tmp_ativ WHERE dt_inicio = '".$dt_inicio_loop."' ORDER BY dt_inicio,hr_inicio ASC") or die (mysql_error()."415");
	while($ar_tmp = farray($qry_tmp)){
		$select_from = "SELECT * FROM ".$prefixo_temp."tmp_ativ WHERE ((inicio >= '".$ar_tmp['inicio']."' AND fim <= '".$ar_tmp['fim']."') OR (inicio <= '".$ar_tmp['inicio']."' AND fim >= '".$ar_tmp['inicio']."') OR (inicio <= '".$ar_tmp['fim']."' AND fim >= '".$ar_tmp['fim']."')) AND numreg <> '".$ar_tmp['numreg']."' AND dt_inicio='".$dt_inicio_loop."'";
		//echo $select_from."<br>";
		$qry_afetadas = query($select_from) or die ("ERRO");
		//echo numrows($qry_afetadas)."**";
		if(numrows($qry_afetadas) > 0){
			$afet = "";
			while($ar_afetadas = farray($qry_afetadas)){
				$afet .= $ar_afetadas['numreg'].",";
			}
			//echo $ar_tmp ['id_atividade'].'-'.$afet."***<br>";
			//echo $afet."<br>";
			query("UPDATE ".$prefixo_temp."tmp_ativ SET ativ_conflitantes = '".substr($afet,0,strlen($afet)-1)."' WHERE numreg='".$ar_tmp['numreg']."'") or die (mysql_error()."793");
		}
	}
	
	//Exibindo as divs dos compromissos
	$qry_ativ = query("SELECT * FROM ".$prefixo_temp."tmp_ativ WHERE dt_inicio='".$dt_inicio_loop."' ORDER BY dt_inicio,hr_inicio ASC");
	$inicio_sem_hora = $base_height_atual;
	$top_last_ativ = 0;
	$z_index = 1000;
	$ar_nao_exibir = "";
	$ar_nao_exibir = array();
	while($ar = farray($qry_ativ)){
		
		$inicio = diferenca_hr($hr_inicio,$ar['hr_inicio'],'','S') + $base_height_atual;
		$duracao = diferenca_hr($ar['hr_inicio'],$ar['hr_prev_fim'],'','S');
		//Verificando a hora esta em branco
		if(empty($ar['hr_inicio'])){
			$inicio = $inicio_sem_hora;
			$duracao = 60;
			$cor_ct = "#FF0000";
			$cor_t_bg = "#E8E8E8";
		}
		elseif($ar['id_situacao'] == 'R'){
			$cor_ct = "#000000";
			$cor_t_bg = "#CCFF99";
		}
		elseif($ar['id_situacao'] != 'R' && $ar['dt_prev_fim'] < date("Y-m-d")){
			$cor_ct = "#000000";
			$cor_t_bg = "#FFA264";
		}
		elseif($ar['id_situacao'] != 'R' && $ar['dt_prev_fim'] >= date("Y-m-d")){
			$cor_ct = "#000000";
			$cor_t_bg = "#98B7FE";
		}

		$link_det = "gera_cad_detalhe.php?pfuncao=atividades_cad_lista&pnumreg=".$ar['numreg'];
		// Se a atividade come?ar no mesmo dia
		if($ar['dt_inicio'] == $ar['dt_prev_fim']){
			//Se esta atividade estiver em conflito com outras
			if(!empty($ar['ativ_conflitantes'])){
				$style_link = ($ar['id_situacao'] == "R")?' style="background:#CCFF99;" ':'';
				$ar_conflitantes = explode(',',$ar['ativ_conflitantes']);
				$numregs_conflitantes = '';
				$assunto = "";
				$link = ' style="display:block;" href="gera_cad_detalhe.php?pfuncao=atividades_cad_lista&pnumreg='.$ar['numreg'].'" ';
				$link .= ' onclick="window.open(this.href,\'inserir_novo_registro\',\'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=100,left=100\'); return false;"';
				$div_info = monta_div_info($ar['numreg']);
				$assunto = '<a '.$style_link.$link.$div_info.' >';
				$assunto .= '<strong>'.$ar['hr_inicio'].'</strong>-'.'<strong>'.$ar['hr_prev_fim'].'</strong> '.$ar['assunto'];
				$assunto .= '</a>';
				
				for($w = 0; $w < count($ar_conflitantes) ; $w++){
					$numregs_conflitantes .= "'".$ar_conflitantes[$w]."',";
					$ar_nao_exibir[] = $ar_conflitantes[$w];
					$ar_ativ = farray(query("SELECT * FROM ".$prefixo_temp."tmp_ativ WHERE numreg='".$ar_conflitantes[$w]."'"));
					$style_link = ($ar_ativ['id_situacao'] == "R")?' style="background:#CCFF99;" ':'';
					$assunto .= '<hr size="1" noshade="noshade">';
					
					$link = ' style="display:block;" href="gera_cad_detalhe.php?pfuncao=atividades_cad_lista&pnumreg='.$ar_conflitantes[$w].'" ';
					$link .= ' onclick="window.open(this.href,\'inserir_novo_registro\',\'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=100,left=100\'); return false;"';
					$div_info = monta_div_info($ar_conflitantes[$w]);
					
					
					//$link = 'window.open(\'gera_cad_detalhe.php?pfuncao=atividades_cad_lista&pnumreg='.$ar_conflitantes[$w].'.\',\'\',\'width=750, height=550,scrollbars=yes,top=100,left=100\'); return false;';
					$assunto .= '<a '.$style_link.$link.$div_info.' >';
					$assunto .= '<strong>'.$ar_ativ['hr_inicio'].'</strong>-'.'<strong>'.$ar_ativ['hr_prev_fim'].'</strong> '.$ar_ativ['assunto'];
					$assunto .= '</a>';
				}
				$result_search = array_search($ar['numreg'],$ar_nao_exibir);
				//echo "-->".$ar['numreg'].'--'.$result_search."------ ";
				
				if($result_search < -1){
					//echo "<----------------------".strlen($numregs_conflitantes)."--------------------->";
					$numregs_conflitantes = substr($numregs_conflitantes,0,strlen($numregs_conflitantes)-1);
					//echo "<----------------------".$numregs_conflitantes."--------------------->";
					$fim_da_maior = farray(query("SELECT MAX(fim * 1) as fim, hr_prev_fim FROM ".$prefixo_temp."tmp_ativ WHERE numreg IN(".$numregs_conflitantes.") GROUP BY hr_prev_fim"));
					$style_montar = 'z-index:'.$z_index.';';
					if($fim_da_maior['fim'] > $ar['fim']){
						$duracao = diferenca_hr($ar['hr_inicio'],$fim_da_maior['hr_prev_fim'],'','S');
					}
					//echo "<><><><><><><><>><".$fim_da_maior['fim']."><><><><><><";
					$onclick = '';
					echo utf8_encode(monta_div_ativ($assunto,$cor_ct,$cor_t_bg,$inicio,($duracao-1),$left,$size_width,$onclick,$style_montar));
					//echo "***".$ar['hr_inicio']."**".$fim_da_maior['hr_prev_fim']."<br>";
					$top_last_ativ = $inicio;

				}
			}
			//Se n?o estiver em conflito
			else{
				$style_montar ='cursor: pointer;';
				$onclick = 'onclick="window.open(\''.$link_det.'\',\'\',\'width=750, height=550,scrollbars=yes,top=100,left=100\');" ';
				$assunto = '<strong>'.$ar['hr_inicio'].'</strong>-'.'<strong>'.$ar['hr_prev_fim'].'</strong> '.$ar['assunto'];
				$div_info = monta_div_info($ar['numreg']);
				echo utf8_encode(monta_div_ativ($assunto,$cor_ct,$cor_t_bg,$inicio,$duracao-1,$left,$size_width,$onclick,$style_montar,$div_info));
				$top_last_ativ = $inicio;
			}
		}
		$z_index = $z_index;
		$inicio_sem_hora = $inicio_sem_hora + 70;
	}

	$height = $base_height_atual;
	
	//Definindo bg das colunas de dias definais de semana
	if($dias == "7" && (date("D",strtotime($dt_inicio_loop)) == "Sat" || date("D",strtotime($dt_inicio_loop)) == "Sun")){
		$bg_cell = "background-color: #E6EDF7;";
	}
	else{
		$bg_cell = "";
	}
	//Montando o fundo do gr?fico
	for($j=0; $j < $loop_hr; $j++){
		echo '<div class="bg_cal_diary_top" style="'.$bg_cell.'z-index:0;width:'.$size_width.'px;height:30px;top:'.$height.'px;left:'.($left).'px;"></div>';
		echo '<div class="bg_cal_diary_bottom" style="'.$bg_cell.'z-index:0;width:'.$size_width.'px;height:30px;top:'.($height+30).'px;left:'.($left).'px;"></div>';
		$height = $height + 60;
	}
	$left = $left + $size_width;
	$dt_inicio_loop = date("Y-m-d",strtotime($dt_inicio_loop." + 1 day"));

$dt_inicio_loop = date("Y-m-d",strtotime($dt_inicio_loop." - 1 day"));

$data_exibida = substr($dt_inicio_loop,8,2).' de '.$mes_name[intval(substr($dt_inicio_loop,5,2))].' de '.substr($dt_inicio_loop,0,4);

?>
<link href="../estilos_css/estilo.css" rel="stylesheet" type="text/css" />

<div id="dica" class="toolTip"></div>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
    <td colspan="3" valign="center"><div align="left" valign="top"> &nbsp;&nbsp;&nbsp;<img src="images/seta.gif" width="4" height="7"/> <span class="tit_detalhes">Agenda Di&aacute;ria </span>&nbsp;
        	<input name="edtdt_inicio" type="text" id="edtdt_inicio"  value="<?php echo(!empty($_GET['dt_inicio']))?$_GET['dt_inicio']:date("d/m/Y");?>"/>
      <?php
	  if($ar_perfil['sn_bloquear_leitura'] == '1'){
	  ?>
		<select name="edtusuario" id="edtusuario">
		<?php
			$qry_users = query("SELECT * FROM is_usuario ORDER BY nome_usuario ASC");
			if(!empty($_GET['usuario'])) {
				$usu_sel = $_GET['usuario'];
			} else {
				$usu_sel = $_SESSION["id_usuario"];
			}
			while($ar_users = farray($qry_users)){
				if( $usu_sel == $ar_users['id_usuario']){
					$selected = 'selected="selected"';
				}
				else{
					$selected = '';
				}
				echo '<option value="'.utf8_encode($ar_users['id_usuario']).'" '.$selected.'>'.utf8_encode($ar_users['nome_usuario']).'</option>';
			}
		?>
		</select>
		<?php
		}
		if($ar_perfil['sn_bloquear_leitura'] == '1'){
			$comp_link = " + '&amp;usuario=' + document.getElementById('edtusuario').value";
		}
		else{
			$comp_link = "";
		}
		?>
		&nbsp;
        <input name="button" type="button" class="botao_form" onclick="javascript:exibe_programa('agenda/calendario_agenda_diaria.php?dt_inicio=' + document.getElementById('edtdt_inicio').value <?php echo$comp_link?>);" value="Exibir" />
      </div></td>
    <td colspan="1" align="right"></td>
  </tr>
  <tr>
    <td colspan="3" valign="center" style="font-size:18px;"><img src="agenda/images/btn_back.png" width="15" height="15" style="cursor:pointer;" onclick="javascript:exibe_programa('agenda/calendario_agenda_diaria.php?dt_inicio=<?php echodten2br(date("Y-m-d",strtotime($dt_inicio." - 1 days")));?>&amp;usuario=<?php echo$id_usuario_logado;?>');" /> <img src="agenda/images/btn_next.png" width="15" height="15" style="cursor:pointer;" onclick="javascript:exibe_programa('agenda/calendario_agenda_diaria.php?dt_inicio=<?php echodten2br(date("Y-m-d",strtotime($dt_inicio." + 1 days")));?>&amp;usuario=<?php echo$id_usuario_logado;?>');" />
      <?php echo$data_exibida;?></td>
    <td colspan="1" align="right"></td>
  </tr>
</table>
<?php
for($w = 0; $w < count($ar_ativ_back_exibidas) ; $w++){
	echo '<div style="height:20px;"></div>';
}
for($i=$hr_inicio_n; $i < $hr_fim_n+1; $i++){
	echo '<div style="height:65px;"></div>';
}

//Destruindo a tabela temporaria
query("DROP TEMPORARY TABLE tmp_ativ");
?>
