<?php
	require("../../../../conecta.php");
	//Ckeck//
	$ola_coach = $_POST['ola_coach'];
	$lembrete_vencimento= $_POST['lembrete_vencimento'];
	$mensagem_presidente= $_POST['mensagem_presidente'];
	$blog_facebook= $_POST['blog_facebook'];
	$diferenciais= $_POST['diferenciais'];
	//hidden//
	$ola_coach_hidden = $_POST['ola_coach_hidden'];
	$lembrete_vencimento_hidden= $_POST['lembrete_vencimento_hidden'];
	$mensagem_presidente_hidden= $_POST['mensagem_presidente_hidden'];
	$blog_facebook_hidden= $_POST['blog_facebook_hidden'];
	$diferenciais_hidden= $_POST['diferenciais_hidden'];
	//Data//
	$ola_coach_data= $_POST['ola_coach_data'];
	$lembrete_vencimento_data= $_POST['lembrete_vencimento_data'];
	$mensagem_presidente_data= $_POST['mensagem_presidente_data'];
	$blog_facebook_data= $_POST['blog_facebook_data'];
	$diferenciais_data= $_POST['diferenciais_data'];
	//-Pessoa-Inscricao-//
	$inscricao = $_POST['inscricao'];
	$id_pessoa = $_POST['id_pessoa'];

	if($ola_coach =='1' and $ola_coach_data != ''){
		$categoria = '538';
		$assunto = "Olá Coach";
		$where_pagamento = '';
		$retPecHtml = peca($categoria,$inscricao,$id_pessoa,$where_pagamento);
		$msg =CriaBlocoInsert($retPecHtml, $id_pessoa,$ola_coach_data,$assunto);
	}
	if($lembrete_vencimento == '1' and $lembrete_vencimento_data != ''){
		$categoria = '540';
		$assunto = "Parabéns pela decisão";
		$where_pagamento = " and pagto.id_tp_pagto = '1' ";
		$retPecHtml = peca($categoria,$inscricao,$id_pessoa,$where_pagamento);
		$msg =CriaBlocoInsert($retPecHtml, $id_pessoa,$lembrete_vencimento_data,$assunto);
	}
	if($mensagem_presidente =='1' and $mensagem_presidente_data != ''){
		$categoria = '539';
		$assunto = "Mensagem do Presidente";
		$where_pagamento = '';
		$retPecHtml = peca($categoria,$inscricao,$id_pessoa,$where_pagamento);
		$msg =CriaBlocoInsert($retPecHtml, $id_pessoa,$mensagem_presidente_data,$assunto);		
	}
	if($blog_facebook =='1' and $blog_facebook_data != ''){
		$categoria = '542';
		$where_pagamento = '';
		$assunto = "Redes Sociais";
		$retPecHtml = peca($categoria,$inscricao,$id_pessoa,$where_pagamento);
		$msg =CriaBlocoInsert($retPecHtml, $id_pessoa,$blog_facebook_data,$assunto);		
	}
	if($diferenciais =='1' and $diferenciais_data != ''){
		$categoria = '541';
		$where_pagamento = '';
		$assunto = "Diferenciais - SBCoaching";
		$retPecHtml = peca($categoria,$inscricao,$id_pessoa,$where_pagamento);
		$msg =CriaBlocoInsert($retPecHtml, $id_pessoa,$diferenciais_data,$assunto);		
	}
	if($msg==''){ $msg = "Preencha as Informações";}
echo "<script>alert('$msg');window.close();</script>";
	
	function peca($categoria,$inscricao,$id_pessoa,$where_pagamento){

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
				ON modeloPeca.wcp_id_curso= InsCursoDetalhe.id_curso
        		LEFT JOIN c_coaching_inscricao_pagto AS pagto
		        ON pagto.id_inscricao = InsCursoDetalhe.id_inscricao				
			  WHERE
			  	InsCursoDetalhe.id_inscricao = '".$inscricao."' and
				InsCursoDetalhe.id_pessoa = '".$id_pessoa."' AND 
				modeloPeca.wcp_categoria_peca = '".$categoria."' 
				$where_pagamento
		  GROUP BY id_agenda
		ORDER BY min(dt_curso) ASC
		";

		$QrySqlCursoEmailPessoa = mysql_query($SqlCursoEmailPessoa);						
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
			
			$array = array( 
			  "razao_social_nome" 	=> $ArQrySqlCursoEmailPessoa['razao_social_nome'],
			  "email" 				=> $ArQrySqlCursoEmailPessoa['email'],
			  "email_pessoal"		=> $ArQrySqlCursoEmailPessoa['email_pessoal'],
			  "modeloPeca" 			=> $html);
		}
		return($array);
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

	function CriaBlocoInsert($retPecHtml,$id_pessoa, $dthr_email,$assunto){		

		$email_assunto = $assunto;
		$arInsert = array(
						  'id_pessoa' 			=> $id_pessoa,
						  'nome_contato' 		=> $retPecHtml['razao_social_nome'],
						  'email_contato' 		=> $retPecHtml['email'],
						  'email_assunto' 		=> $email_assunto,
						  'email_corpo' 		=> $retPecHtml["modeloPeca"],
						  'dthr_email'			=> converte($dthr_email),
						  'wcp_sn_envia'		=> '0'
						  );
		foreach($arInsert as $vazio){
			if($vazio != ''){
				$n++;
			}
		}
		if($n == 7){
			$keys = array_keys($arInsert);
			$vals = array_values($arInsert);
			
			$insert = "INSERT INTO `is_email_pessoa` "
			 . "(" . implode(", ", $keys) . ") "
			 . "VALUES('" . implode("', '", $vals) . "')";	
		
			if(mysql_query($insert)){
				$msg = "Agendado com Sucesso";
			}else{
				$msg ="Erro ao gravar";
			}
		}else{
				$msg ="Erro ao gravar. Verifique se o e-mail ou o nome esta vazio";		
		}
		return $msg;
	}
?>