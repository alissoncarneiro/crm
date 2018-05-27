<?php
    require_once('../../../../functions.php');
    require_once('../../../../classes/class_pesquisa.php');
    require_once('../../../../conecta.php');
    require_once('../../../../classes/phpmailer/PHPMailerAutoload.php');

    $fk_coach = $_REQUEST['session'];
    $fk_coachee = $_REQUEST['id_pessoa'];
    $id_contato = $_REQUEST['id_contato'];
  
    if(is_numeric($id_contato)){
    	$fk_coachee = $id_contato;
		$sqlDinamico = "select nome as razao_social_nome, email_profissional as email from is_contato where numreg = $fk_coachee";    	
    	$nomeDinamico 		 = "razao_social_nome";
    	$emailDinamico 		 = "email";
    	$sn_contato = 1;
    }else{
    	$fk_coachee = $fk_coachee ;
		$sqlDinamico = "select email, razao_social_nome from is_pessoa where numreg = $fk_coachee";    	
    	$nomeDinamico 		 = "razao_social_nome";
    	$emailDinamico 		 = "email";
    	$sn_contato = 0;
    }
    
    $fk_tipo_assessments = $_REQUEST['valueAssessment'];
    
    $competencias_coach_coachee_programa_numreg_situacao = '1';
    if($fk_tipo_assessments ==  '42' || $fk_tipo_assessments ==  '43' || $fk_tipo_assessments == '44'){
        echo "Indisponivel no Momento";
        exit;
    }

    $sqlVerificaSeExiste = "select * from tb_competencias_coach_coachee_programa where fk_coachee = $fk_coachee  and fk_coach = $fk_coach  and fk_tipo_assessments = $fk_tipo_assessments";
    
   
    $qryVerificaSeExiste = mysql_query($sqlVerificaSeExiste);
    if($qryVerificaSeExiste){
        $count = mysql_num_rows($qryVerificaSeExiste) ;
        
        $dataAtual = date("Y-m-d");
        if($count < 1){
            $insertPrograma = "insert into tb_competencias_coach_coachee_programa ("
                    . "fk_coach,"
                    . "fk_coachee,"
                    . "fk_tipo_assessments,"
                    . "competencias_coach_coachee_programa_numreg_situacao,"
                    . "competencias_coach_coachee_programa_numreg_situacao_data, "
                    . "sn_contato "
                    		
                . ") values("
                    . "'$fk_coach',"
                    . "'$fk_coachee',"
                    . "'$fk_tipo_assessments',"
                    . "'1',"
                    . "'$dataAtual'," 
                    . "'$sn_contato')";
            if(mysql_query($insertPrograma)){
            	

                $sqlUsuario     = "select * from is_usuario where numreg = $fk_coach";
                $qryUsuario             = mysql_query($sqlUsuario);
                $arUsuario              = mysql_fetch_assoc($qryUsuario);
                $emailUsuario           = $arUsuario['email'];
                $nomeUsuario            = $arUsuario['nome_usuario'];
                $emailUsuario            = $arUsuario['email'];
                $telUsuario             = $arUsuario['tel1'];
                $smtpServidorSenha      = $arUsuario['smtp_senha'];
                $smtpServidor           = $arUsuario['smtp_servidor'];
                $smtpServidorUsuario    = $arUsuario['smtp_login'];
                $assinatura             = $nomeUsuario."</br></br>".$emailUsuario."</br></br>".$telUsuario;
                /*\ Envia Email \*/
                $_agendamento = new AgendamentoPesquisa();
                $senhaPessoa = $_agendamento->geraSenha($fk_coachee, $sn_contato);
                
                $sqlPessoa = "$sqlDinamico" ;
                $qryPessoa = mysql_query($sqlPessoa) or die(mysql_error());
                $arPessoaCoachee = mysql_fetch_assoc($qryPessoa );
                $nomePessoa     = $arPessoaCoachee[$nomeDinamico];
                $emailPessoa    = $arPessoaCoachee[$emailDinamico];
                $emailPessoal   = $arPessoaCoachee[$emailPessoaDinamico];
                $assunto        = 'Usuario e Senha para Acesso';
                $modelo_html =
                        "Caro(a)  {$arPessoaCoachee[$nomeDinamico]} .</br></br>
                        Você está recebendo o Assessment Competências, desenvolvido com exclusividade pela Sociedade Brasileira de Coaching, maior centro de referência e excelência no mercado de coaching no Brasil desde 1999 e Única organização no país capaz de maximizar o capital humano com foco em desenvolvimento de competências. </br></br>
                        Com este Assessment, você terá a sua disposição uma poderosa ferramenta que permitirá a sua empresa potencializar os resultados e as conquistas de metas estratégicas, bem como elevar e mapear os indicadores de desempenho dos líderes e equipes em consonância com um método único e customizado de desenvolvimento do capital humano para a sua organização. </br></br>
                        Descubra como nosso portfólio de soluções pode atender as suas necessidades de forma totalmente versátil e assertiva, a partir de um questionário elaborado de acordo com criteriosos padrões de qualidade que buscam oferecer estratégias e soluções para maximizar seus resultados em todos os níveis organizacionais.</br></br>
                        Para começar agora mesmo a responder este Assessment e obter imediatamente as melhores soluções direcionadas às suas necessidades empresariais, clique no endereço abaixo:</br></br>
                        http://competencias.sbcoaching.com.br</br></br>
                        Usuario: {$emailPessoa}</br></br>
                        Senha: {$senhaPessoa}</br></br>

                        <strong>Este é apenas o seu primeiro passo rumo à evolução dos níveis de performance e dos resultados de sua organização!</strong><br></br>
                        Conte conosco em caso de dúvidas e demais esclarecimentos.<br></br>
                        Atenciosamente, <br><br>
                        {$assinatura}";
                if(!$_agendamento->agendaEmail($fk_coachee, $dataAtual, $nomePessoa, $emailPessoa, $emailPessoal, $assunto, $modelo_html,$emailUsuario, $fk_coach)){
                    $msg = 'Erro ao Enviar Tente Novamente. '.mysql_error();
                }else{
                    $host = "smtp.sbcoaching.com.br";
                    $Username = "seosbc@sbcoaching.com.br";
                    $Password = "sbcseo1988";
                    
                    $Mail = new PHPMailer();
                    $Mail->IsSendmail(); 
                    $Mail->Port = 587;
                    $Mail->IsSMTP();
                    $Mail->IsHTML(true); 
                    $Mail->Host = $host; 
                    $Mail->SMTPAuth = true;  
					$Mail->CharSet = 'iso-8859-1';
        			$Mail->Username = $Username;
        			$Mail->Password = $Password;
                    
                    $Mail->From = $emailUsuario;
                    $Mail->FromName = $nomeUsuario; 
                    $Mail->AddAddress($emailPessoa, $nomePessoa);
                    $Mail->addBCC($smtpServidorSenha);
                    
                    $Mail->Subject = 'Acesso ao Assessment';
                    $Mail->Body    = $modelo_html;
                    $Mail->AltBody = 'Acesso ao Assessment';
                    
                    if($Mail->Send()){
                        $msg =  'Enviado Com Sucesso';
                    } else{
                        $msg = 'Erro ao enviar o e-mail:' . $Mail->ErrorInfo;
                    }			
                }   
            }else{
                $msg = mysql_error();
            }
        }else{


        	$msg =  "Ja existe um Assessment Cadastrado... REENVIANDO......";
        	 
        	$sqlUsuario     = "select * from is_usuario where numreg = $fk_coach";
        	$qryUsuario             = mysql_query($sqlUsuario);
        	$arUsuario              = mysql_fetch_assoc($qryUsuario);
        	$emailUsuario           = $arUsuario['email'];
        	$nomeUsuario            = $arUsuario['nome_usuario'];
        	$emailUsuario            = $arUsuario['email'];
        	$telUsuario             = $arUsuario['tel1'];
        	$smtpServidorSenha      = $arUsuario['smtp_senha'];
        	$smtpServidor           = $arUsuario['smtp_servidor'];
        	$smtpServidorUsuario    = $arUsuario['smtp_login'];
        	$assinatura             = $nomeUsuario."</br></br>".$emailUsuario."</br></br>".$telUsuario;
        	/*\ Envia Email \*/
        	$_agendamento = new AgendamentoPesquisa();
        	$senhaPessoa = $_agendamento->geraSenha($fk_coachee, $sn_contato);
        	
        	$sqlPessoa = "$sqlDinamico" ;
        	$qryPessoa = mysql_query($sqlPessoa) or die(mysql_error());
        	$arPessoaCoachee = mysql_fetch_assoc($qryPessoa );
        	$nomePessoa     = $arPessoaCoachee[$nomeDinamico];
        	$emailPessoa    = $arPessoaCoachee[$emailDinamico];
        	$emailPessoal   = $arPessoaCoachee[$emailPessoaDinamico];
        	$assunto        = 'Reenvio de Usuario e Senha para Acesso';

        	
        	$modelo_html =
        	"Caro(a)  {$arPessoaCoachee[$nomeDinamico]} .</br></br>
        	Você está recebendo o Assessment Competências, desenvolvido com exclusividade pela Sociedade Brasileira de Coaching, maior centro de referência e excelência no mercado de coaching no Brasil desde 1999 e Única organização no país capaz de maximizar o capital humano com foco em desenvolvimento de competências. </br></br>
        	Com este Assessment, você terá a sua disposição uma poderosa ferramenta que permitirá a sua empresa potencializar os resultados e as conquistas de metas estratégicas, bem como elevar e mapear os indicadores de desempenho dos líderes e equipes em consonância com um método único e customizado de desenvolvimento do capital humano para a sua organização. </br></br>
        	Descubra como nosso portfólio de soluções pode atender as suas necessidades de forma totalmente versátil e assertiva, a partir de um questionário elaborado de acordo com criteriosos padrões de qualidade que buscam oferecer estratégias e soluções para maximizar seus resultados em todos os níveis organizacionais.</br></br>
        	Para começar agora mesmo a responder este Assessment e obter imediatamente as melhores soluções direcionadas às suas necessidades empresariais, clique no endereço abaixo:</br></br>
        	http://competencias.sbcoaching.com.br</br></br>
        	Usuario: {$emailPessoa}</br></br>
        	Senha: {$senhaPessoa}</br></br>
        	
        	<strong>Este é apenas o seu primeiro passo rumo à evolução dos níveis de performance e dos resultados de sua organização!</strong><br></br>
        	Conte conosco em caso de dúvidas e demais esclarecimentos.<br></br>
        	Atenciosamente, <br><br>
        	{$assinatura}";
        	
        	$host = "smtp.sbcoaching.com.br";
        	$Username = "seosbc@sbcoaching.com.br";
        	$Password = "sbcseo1988";
        	
        	$Mail = new PHPMailer();
        	$Mail->IsSendmail();
        	$Mail->Port = 587;
        	$Mail->IsSMTP();
        	$Mail->IsHTML(true);
        	$Mail->Host = $host;
        	$Mail->SMTPAuth = true;
        	$Mail->CharSet = 'iso-8859-1';
        	$Mail->Username = $Username;
        	$Mail->Password = $Password;
        	
        	$Mail->From = $emailUsuario;
        	$Mail->FromName = $nomeUsuario;
        	$Mail->AddAddress($emailPessoa, $nomePessoa);
        	$Mail->addBCC($smtpServidorSenha);
        	
        	$Mail->Subject = 'Acesso ao Assessment';
        	$Mail->Body    = $modelo_html;
        	$Mail->AltBody = 'Acesso ao Assessment';
        	
        	if($Mail->Send()){
        			$msg =  'Enviado Com Sucesso';
        	} else{
        			$msg = 'Erro ao enviar o e-mail:' . $Mail->ErrorInfo;
        	}
        }
    }else{
        $msg =  "Erro ao Executar Consulta";
    }
        
    echo $msg ;

    
    



