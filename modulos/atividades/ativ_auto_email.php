<?
    //================================================================================================================================//
    // BLOCO DE ALERTA POR E-MAIL DA ATIVIDADE
    //================================================================================================================================//

    $res = array();
    $remetente = 'oasiscrm@i-partner.com.br';

    $dthoje = gmdate("Y-m-d H:i:s", time() + 3600*-2);
    
    //Prepara Recipients
    $edtemails = $emails_alerta;
    $recipients = array(); $i = 0;
    while ($edtemails) {
         $pos = strpos($edtemails,';');
         if ($pos === false) {
            $recipients[$i] = $edtemails;
            $edtemails = '';
         } else {
            $recipients[$i] = substr($edtemails,0,$pos);
            $edtemails = str_replace($recipients[$i].';','',$edtemails);
         }
         $i = $i + 1;
    }
    
    // Prepara Saudação
    $hora_atual = gmdate("H", time() + 3600*-2);
    if (($hora_atual >=0) && ($hora_atual <=12)) { $saudacao = 'Bom dia'; }
    if (($hora_atual >=13) && ($hora_atual <=18)) { $saudacao = 'Boa tarde'; }
    if (($hora_atual >=19) && ($hora_atual <=24)) { $saudacao = 'Boa noite'; }

    // Prepara texto da operação
    if ($operacao=="I") { $txtoper = 'incluida'; }
    if ($operacao=="A") { $txtoper = 'alterada'; }
    if ($operacao=="E") { $txtoper = 'excluida'; }

	$params['host'] = 'smtp.i-partner.com.br';				// The smtp server host/ip
	$params['port'] = 25;						// The smtp server port
	$params['helo'] = exec('hostname');			// What to use when sending the helo command. Typically, your domain/hostname
	$params['auth'] = TRUE;						// Whether to use basic authentication or not
	$params['user'] = $remetente;				// Username for authentication
	$params['pass'] = 'oasis123';				// Password for authentication

    for ($j = 0; $j <= ($i-1); $j ++ ) {
      // Prepara URL de abertura
      switch ($edtid_tp_atividade) {
        case "SAC" : $func = 'sac_cad_lista'; break;
        case "OPOR" : $func = 'opo_cad_lista'; break;
        default: $func = 'atividades_cad_lista';
      }
      if ($edtsn_resp_caso =="S") { $func = 'resp_sac'; }
      if ($edtsn_acao_oport  =="S") { $func = 'acoes_oport'; }
      
      $url_det = "http://187.45.224.250/i-partner-crm/gera_cad_detalhe.php?pfuncao=".$func."&pnumreg=".$pnumreg."&psubdet=&pnpai=&pemail=".$recipients[$j];

      $send_params['recipients']	= array($recipients[$j]);							// The recipients (can be multiple)
      $send_params['headers']		= array(
                                        'Content-Type: text/html; charset=iso-8859-1',
                                        'From: "OASIS CRM" <'.$remetente.'>',	// Headers
										'To: '.$recipients[$j].'',
										'Subject: '.$edtdt_prev_fim.' - '.$edthr_inicio.' : '.$edtdescrid_pessoa.' - '.$nome_atividade.' - '.$edtassunto,
										'Return-Path: <'.$remetente.'>'
									   );
	  $send_params['from']		= $remetente;									// This is used as in the MAIL FROM: cmd
																						// It should end up as the Return-Path: header
	  $send_params['body']		= "<html><body>";
      $send_params['body']		= "$saudacao $nome_usuario,<br><br>";
      $send_params['body']	   .= "Você recebeu um novo alerta do sistema. A atividade abaixo foi $txtoper por $id_usuario.<br>";
      $send_params['body']	   .= "Tipo da Atividade : <b>".$nome_atividade."</b><br>";
      $send_params['body']	   .= "Empresa : <b>".$edtdescrid_pessoa."</b><br>";
      $send_params['body']	   .= "Contato : <b>".$edtdescrid_pessoa_contato."</b><br>";
      $send_params['body']	   .= "Assunto : ".$edtassunto."<br>";
      $send_params['body']	   .= "Prazo de Conclusão : ".$edtdt_prev_fim."  ".$edthr_inicio." - ".$edthr_prev_fim."<br>";
      $send_params['body']	   .= "Responsável : ".$nome_usuario."<br>";
      $send_params['body']	   .= "Descrição da Atividade : \n".$edtobs."<br><br>";

//      $send_params['body']	   .= '<a href="'.$url_det.'" target="_blank">';
//      $send_params['body']	   .= "Clique aqui para visualizar os detalhes desta atividade..."."</a><br><br>";
      $send_params['body']	   .= "* Esta mensagem é gerada automaticamente pelo sistema OASIS CRM.<br><br>";
      $send_params['body']	   .= "</body><html>";
   	
//echo  $send_params['body'];
				// The body of the email
	  if(is_object($smtp = smtp::connect($params)) AND $smtp->send($send_params)){
		//echo 'Sua mensagem foi enviada com sucesso '.$edtnome.' !';
		// Any recipients that failed (relaying denied for example) will be logged in the errors variable.
	  }else{
		echo "Desculpe, não foi possível enviar esta mensagem tente novamente mais tarde.";
		// The reason for failure should be in the errors variable
	  }
	}

?>
