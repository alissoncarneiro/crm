<?php
header("Content-Type: text/html; charset=ISO-8859-1");
include('../../../../conecta.php');
$modelo = $_POST['acao'];
$id_pessoa = $_POST['id_pessoa'];
$numregModelo = $_POST['numregModelo'];
	
	$onModelo = "";
	if($modelo == '43'){
		$onModelo = "";
	}else{
		$onModelo = "ON modeloPeca.wcp_id_curso= InsCursoDetalhe.id_curso";
	
	}
	
	if($numregModelo){
			
	     $innerParte = "INNER JOIN c_coaching_parte AS parte ON parte.numreg = InsCursoDetalhe.id_parte";
		 $whereParte ="and parte.numreg= '".$numregModelo."'";
	}else{
		$innerParte  = "";
		$whereParte = "";
	}
	
	
	$SqlCursoEmailPessoa = "
		SELECT
			  InsCursoDetalhe.id_pessoa,
			  pessoa.razao_social_nome,
			  pessoa.email,
			  pessoa.email_pessoal,
			  InsCursoDetalhe.id_agenda,
			  DATE_FORMAT(MIN(dt_curso), '%d-%m-%Y') AS dataInicio,
			  DATE_FORMAT(MAX(dt_curso), '%d-%m-%Y') AS dataFim,
			  curso.nome_curso,
			  InsCursoDetalhe.id_curso,
			  InsCursoDetalhe.id_modulo,
			  InsCursoDetalhe.id_hotel,
			  hotel.nome_hotel,
			  hotel.endereco,
			  hotel.numero,
			  hotel.complemento,
			  hotel.bairro,
			  hotel.cidade,
			  hotel.uf,
			  hotel.pais,
			  hotel.tel_hotel,
			  modeloPeca.textohtm_corpo,
			  DATE_FORMAT(pagto.dt_primeiro_pagto, '%d/%m/%Y') as dt_primeiro_pagto			  
			  FROM c_coaching_inscricao_curso_detalhe AS InsCursoDetalhe
				INNER JOIN c_coaching_hotel AS hotel
				ON hotel.numreg = InsCursoDetalhe.id_hotel
				
				INNER JOIN is_pessoa as pessoa
				ON pessoa.numreg = InsCursoDetalhe.id_pessoa
				
				INNER JOIN c_coaching_curso AS curso
				ON curso.numreg = InsCursoDetalhe.id_curso
				
				INNER JOIN is_modelo_orcamento AS modeloPeca
				$onModelo
				
				INNER JOIN c_coaching_inscricao_pagto AS pagto
				ON pagto.id_inscricao = InsCursoDetalhe.id_inscricao	
				
				$innerParte
			  WHERE
  				(InsCursoDetalhe.id_pessoa = '".$id_pessoa."' AND	pagto.id_tp_pagto = '1' and  modeloPeca.numreg = '".$modelo."' $whereParte)
				or
  				(InsCursoDetalhe.id_pessoa = '".$id_pessoa."' AND  modeloPeca.numreg = '".$modelo."' $whereParte)
				
		  GROUP BY id_agenda
		ORDER BY min(dt_curso) ASC";
	
	
	$QrySqlCursoEmailPessoa = mysql_query($SqlCursoEmailPessoa);
	$QryCountSqlCursoEmailPessoa = mysql_num_rows($QrySqlCursoEmailPessoa);

	if($QryCountSqlCursoEmailPessoa > 0)
	{
		while($ArQrySqlCursoEmailPessoa = mysql_fetch_array($QrySqlCursoEmailPessoa)){
		 
			$DmA = explode('-',$ArQrySqlCursoEmailPessoa['dataInicio']);
			$DmAFim = explode('-',$ArQrySqlCursoEmailPessoa['dataFim']);
			$linha ++;
			
			
				
			if($linha == '1'){
				$html = str_replace('VSDATA1M1',$DmA[0],$ArQrySqlCursoEmailPessoa['textohtm_corpo']);
				$html = str_replace('VS_NOME',$ArQrySqlCursoEmailPessoa['razao_social_nome'],$html);
				$html = str_replace('VSCURSO',$ArQrySqlCursoEmailPessoa['nome_curso'],$html);
				
				$html = str_replace('VSDATA2M1',$DmAFim[0] ,$html);
				$html = str_replace('VSMESM1', getMes($DmAFim [1]),$html);
				$html = str_replace('VSANOM1',$DmAFim [2],$html);
				
				$html = str_replace('VSHOTELM1',$ArQrySqlCursoEmailPessoa['nome_hotel'],$html);
				$html = str_replace('VSENDERECOM1',
									$ArQrySqlCursoEmailPessoa['endereco']		.', '.
									$ArQrySqlCursoEmailPessoa['numero']  		.', '. 
									($ArQrySqlCursoEmailPessoa['complemento'] !='' ? $ArQrySqlCursoEmailPessoa['complemento'].', ' : ""). 	
									$ArQrySqlCursoEmailPessoa['bairro']  		.', '.
									$ArQrySqlCursoEmailPessoa['cidade']  		.', '. 
									$ArQrySqlCursoEmailPessoa['uf'],$html);
				$html = str_replace('VSTELM1',$ArQrySqlCursoEmailPessoa['tel_hotel'],$html);
				$html = str_replace('VSDATAVENCIMENTO',$ArQrySqlCursoEmailPessoa['dt_primeiro_pagto'],$html);
				
			}
			if($linha == '2'){
		
				$html = str_replace('VSDATA1M2',$DmA[0],$html);
				$html = str_replace('VS_NOME',$ArQrySqlCursoEmailPessoa['razao_social_nome'],$html);
				$html = str_replace('VSCURSO',$ArQrySqlCursoEmailPessoa['nome_curso'],$html);
				$html = str_replace('VSDATA2M2',$DmAFim[0] ,$html);
				$html = str_replace('VSMESM2', getMes($DmAFim [1]),$html);
				$html = str_replace('VSANOM2',$DmAFim [2],$html);
				$html = str_replace('VSENDERECOM2',
									$ArQrySqlCursoEmailPessoa['endereco']		.', '.
									$ArQrySqlCursoEmailPessoa['numero']  		.', '. 
									($ArQrySqlCursoEmailPessoa['complemento'] !='' ? $ArQrySqlCursoEmailPessoa['complemento'].', ' : ""). 								     
									$ArQrySqlCursoEmailPessoa['bairro']  		.', '.
									$ArQrySqlCursoEmailPessoa['cidade']  		.', '. 
									$ArQrySqlCursoEmailPessoa['uf'],$html);
				$html = str_replace('VSHOTELM2',$ArQrySqlCursoEmailPessoa['nome_hotel'],$html);
				$html = str_replace('VSTELM2',$ArQrySqlCursoEmailPessoa['tel_hotel'],$html);
				$html = str_replace('VSDATAVENCIMENTO',$ArQrySqlCursoEmailPessoa['dt_primeiro_pagto'],$html);
			}
		}
	}
	else{
		$sql_modelo_html = mysql_query("select textohtm_corpo from is_modelo_orcamento where numreg ='".$modelo."'");
		$Arsql_modelo_html = mysql_fetch_array($sql_modelo_html);
		$html = $Arsql_modelo_html['textohtm_corpo'];

	}
	function getMes($mes){
		switch($mes){ 
			case "01": $mes= "Janeiro";      break;
			case "02": $mes= "Fevereiro";    break;
			case "03": $mes= "Mar&ccedil;o"; break;
			case "04": $mes= "Abril";        break;
			case "05": $mes= "Maio";         break;
			case "06": $mes= "Junho";        break;
			case "07": $mes= "Julho";        break;
			case "08": $mes= "Agosto";       break;
			case "09": $mes= "Setembro";     break;
			case "10": $mes= "Outubro";      break;
			case "11": $mes= "Novembro";     break;
			case "12": $mes= "Dezembro";     break;
		}
		return $mes;
	}
	
	function converte($data) {
   		return (substr($data,6,4).'-'.substr($data,3,2).'-'.substr($data,0,2). ' 00:00:00');
	}
	
	echo  $html;
	



?>