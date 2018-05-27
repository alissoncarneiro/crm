<?php

/*************************************************************************************/
//Desevolvido: Alisson Carneiro
//Data: 30/07/2012 09:15
//Objetivo: Buscar todos os registro na tabela "is_email_pessoa" e enviar email, 
//apos este processo faz update no campo wcp_sn_envia =1.
//Regra: Sempre Verificar se existe um vendedor para conta e enviar com os dados dele.
/**************************************************************************************/

	
	require("functions.php");
	conectaOasisInternoTreinamento();
	
	$SqlEmailPendente = "
		SELECT emailPessoa. *, pessoa.id_vendedor_padrao, usuario.nome_usuario, usuario.email
		  FROM is_email_pessoa as emailPessoa
			INNER JOIN is_pessoa as pessoa
			ON pessoa.numreg = emailPessoa.id_pessoa
			INNER JOIN is_usuario as usuario
			ON usuario.numreg =  pessoa.id_vendedor_padrao
		WHERE  emailPessoa.wcp_sn_envia = 0 AND emailPessoa.dthr_email <= DATE_FORMAT(CURDATE(),'%Y-%m-%d %H:%i:%s') limit 5";
	
	$QrySqlEmailPendente = query($SqlEmailPendente);
	
	while($ArQrySqlEmailPendente = mysql_fetch_array($QrySqlEmailPendente)){
		$retNumreg[] = envia_email($ArQrySqlEmailPendente);
	}
 	$numregs = implode(',',$retNumreg);	
	$UpdateMensagemEnviada = "update emailPessoa set wcp_sn_envia = '1' where numreg  in($numregs)";
	query($UpdateMensagemEnviada);



	require("../phpmailer/class.phpmailer.php");
	
	function envia_email($ArQrySqlEmailPendente){

			if($ArQrySqlEmailPendente['id_vendedor_padrao'] <> 1 || $ArQrySqlEmailPendente['id_vendedor_padrao'] <> ''){
				$fromName = $ArQrySqlEmailPendente['nome_usuario'];
				$from = $ArQrySqlEmailPendente['email'];
			}else{
				$from = "atendimento@sbcoaching.com.br";
				$fromName = "SBCoaching";
			}
			$cont++;
			if($cont % 2){
				$host = "smtp.sbcoaching.com.br";
				$Username = "seosbc@sbcoaching.com.br";
				$Password = "sbcseo1988";
			}else{
				$host = "smtp.sbcoaching.com.br";
				$Username = "seosbc@sbcoaching.com.br";
				$Password = "sbcseo1988";
			}
			
			
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->Host = $host; 
			$mail->Username = $Username; 
			$mail->Password = $Password;
			$mail->SMTPAuth = true; 
			$mail->Port = 587;
			$mail->From = $from;
			$mail->FromName = $fromName; 
			$mail->IsHTML(true); 
			$mail->CharSet = 'iso-8859-1';
			$mail->Subject = $ArQrySqlEmailPendente['email_assunto'];
			$mail->ClearAllRecipients();
			$mail->AddAddress($email, $nome);
			$mail->Body = $ArQrySqlEmailPendente['email_corpo'];
			
			if($mail->Send()){
				return $ArQrySqlEmailPendente['numreg'];
			}			
	}
	
	
?>