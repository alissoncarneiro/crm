<?php
/*
 * post_before_c_coaching_gera_ocoach.php
 * Autor: Alisson
 * 03/04/2012 10:26:13
 */
session_start();
/*
$numreg=$_POST['numreg'];
$pnumreg=$_POST['pnumreg'];
$nome=$_POST['nome'];
$nome_site=$_POST['nome_site'];
$nome_site_update =$_POST['nome_site_update'];
$login=$_POST['email'];
$curso = $_POST['curso'];
*/

$numreg = getPost('numreg');
$pnumreg = getPost('pnumreg');
$nome = getPost('nome');
$nome_site = getPost('nome_site');
$nome_site_update = getPost('nome_site_update');
$login = getPost('email');
$curso = getPost('curso');

	if ($pnumreg == '-1'){

		if(($nome == "") || ($login == "") || ($nome_site == "")){
			echo "Preencha todos os campos!";
			exit;
		}
		//Conex�o Oasis
		require_once('../../../../functions.php');
		require_once('../../../../conecta.php');
		require_once('../../../../classes/class.phpmailer.php');
		require_once('../../../../classes/class.Email.php');
		
	
		$SqlUsuarioOcoach= "SELECT nome_site,email_pessoa_site FROM  is_gera_ocoach where nome_site= '".$nome_site."' or email_pessoa_site = '".$login."'  ";
		if(!$QrySqlUsuarioOcoach = mysql_query($SqlUsuarioOcoach)){
			$erro .= "\nERRO: ".mysql_error()."";
				echo $erro;
				exit;
		}
		$NumQryUsuarioOcoach = mysql_num_rows($QrySqlUsuarioOcoach);

		if($NumQryUsuarioOcoach == '0'){
			
			//Insert Oasis
			$SqlInsertOasis = "INSERT INTO is_gera_ocoach (id_pessoa,nome_site,email_pessoa_site)values('$numreg','$nome_site','$login')";
			if(!$QrySqlInsertOasis = mysql_query($SqlInsertOasis)){
				$erro .= "\nERRO: ".mysql_error()."";
				echo $erro;
				exit;
			}
			//Envia E-mail	
			$Mail = new Email();
			$assunto = 'Sua p�gina na SBCoaching foi criada';
			$Mail->_Assunto($assunto);
			$Mail->_AdicionaDestinatarioCC($email,$nome);
			$msg='';
			$mensagem .="Parab�ns, ".$nome."!<br><br>
			Agora voc� tem uma p�gina profissional hospedada dentro do site da <strong>Sociedade Brasileira de Coaching�</strong>. Com ela, voc� poder� elaborar o seu perfil, incluindo        informa��es personalizadas para divulgar o seu trabalho como coach.<br><br>
			Al�m de criar um perfil de acordo com os seus diferenciais, nesta p�gina, voc� tamb�m ter� acesso a um kit de marketing, contendo: cart�o de visita, folder, papel timbrado        (na vertical e na horizontal) e manual para a utiliza��o dos arquivos gr�ficos e de web.<br><br>
			
			<strong>Veja como � f�cil personalizar a sua p�gina:</strong><br><br>
			<blockquote>
			�	Acesse: http://www.sbcoaching.com.br/ocoach/".$nome_site."<br>
			�	Clique na op��o �Restrito� localizada no rodap� da p�gina;<br>
			�	Insira o email: ".$login." e a senha als0215;<br>
			�	Feito este processo, abrir� um p�gina com quatro op��es de menu: Alterar Cadastro, Kit de Marketing, Manual e Alterar Senha;<br>
			�	Clique em �Manual� para entender, passo a passo, como personalizar a sua p�gina, tendo a possibilidade de trocar fotos e inserir informa��es profissionais.<br>
			<blockquote>
			<br><br>
			� importante ressaltar que a sua p�gina est� hospedada dentro do dom�nio da <strong>Sociedade Brasileira de Coaching� (www.sbcoaching.com.br)</strong>, e que tanto o seu        perfil profissional quanto o kit de marketing ser�o de grande import�ncia para a divulga��o do seu trabalho como coach, fazendo com que voc� possa dar os primeiros passos        para lan�ar-se no mercado e chegar at� os seus clientes.
			<br><br>
			Agora, al�m de ser um membro da <strong>SBCoaching�</strong>, voc� tamb�m tem uma p�gina exclusiva para divulgar o seu trabalho e j� sair atuando na �rea!
			<br><br>
			
			Um forte abra�o!<br><br>
			<strong>Equipe Sociedade Brasileira de Coaching�</strong>.
			";
			$Mail->_Corpo($mensagem .'<br/>'.$assinatura);
			if(empty($msg)){
				if($Mail->_EnviaEmail()){
					$msg = "Gerado com Sucesso";
				} 
				else{
					$msg = 'Erro ao enviar o e-mail:' . $Mail->ErrorInfo;
				}
			}
		
			//Conex�o KingHost
			if(!$conexao = mysql_connect('mysql.sbcoaching.com.br', 'sbcoaching', 'ALS0215')){
				$erro .= "\nERRO: ".mysql_error()."";
				echo $erro;
				exit;
			}
			$db = mysql_select_db('sbcoaching') or die (mysql_error());
			//Insert Ocoachs
			$SqlInsert="insert into ocoachs(

				transforme_sua_vida,o_que_coaching, a_jornada, area_atuacao, direferenciais,depoimentos,saiu_na_midia, porque_coaching,         segredo_sucesso,beneficios_vantagem,mais_informacao, identifique_bons_coaching, como_comecar, sobre_mim,endereco, o_que_pc,home_texto,home_foto, nome_curso)
				SELECT transforme_sua_vida,o_que_coaching, a_jornada, area_atuacao, direferenciais,depoimentos,saiu_na_midia, porque_coaching, segredo_sucesso,        beneficios_vantagem,mais_informacao,identifique_bons_coaching, como_comecar, sobre_mim,endereco, o_que_pc,home_texto,home_foto, nome_curso 
				FROM ocoachs WHERE id=1";
			
			if(!$QrySqlInsert = mysql_query($SqlInsert,$conexao)){
				$erro .= "\nERRO: ".mysql_error()."";
				echo $erro."108";
				exit;
			}
			
			$idOcoachs = mysql_insert_id();
			
			$chave = gk(20);

			//Cria um Usuario com dados Cadastrais
$SqlInsertUsuariosOcoach = "Insert into usuarios_ocoach (`login`,`senha`,`ativo`,`nome_site`,`key`,`id_ocoach`,`nome`,`id_pessoa_oasis` ) values								        (\"$login\",\"d9f99aef1df7fca982100cd1fc52ed05\",\"S\",\"$nome_site\",\"$chave\",\"$idOcoachs\",\"$nome\",\"$numreg\");";			
			if(!$QrySqlInsertUsuariosOcoach = mysql_query($SqlInsertUsuariosOcoach,$conexao)){
				$erro .= "\nERRO: ".mysql_error()."";
					echo $erro;
					exit;
			}
			
			//Update Curso
			$sqlOcoachUpdate = "UPDATE ocoachs set nome_curso = '$curso' WHERE id= $idOcoachs";
			if(!$QryOcoachUpdate = mysql_query($sqlOcoachUpdate)){
				$erro .= "\nERRO: ".mysql_error()."";
					echo $erro."\n$sqlOcoachUpdate";
					exit;
			}
			mysql_close($conexao);
		}
		else{
			$msg = 'Ja existe este email ou nome site';
		}
	}
	else{
		
		if(!$conexao = mysql_connect('mysql.sbcoaching.com.br', 'sbcoaching', 'ALS0215')){
			$erro .= "\nERRO: ".mysql_error()."";
				echo $erro;
				exit;
		}
		$db = mysql_select_db('sbcoaching');
		
		$SqlUpdateUsuariosOcoach = "UPDATE usuarios_ocoach SET login=\"$login\", senha=\"d9f99aef1df7fca982100cd1fc52ed05\",nome_site=\"$nome_site\", nome=\"$nome\" WHERE id_pessoa_oasis = \"$numreg\"";
			if(!$QrySqlUpdateUsuariosOcoach = mysql_query($SqlUpdateUsuariosOcoach,$conexao)){
				$erro .= "\nERRO: ".mysql_error()."";
					echo $erro."149";
					exit;
			}
		
		if(!$SqlPesquisaId=mysql_query("SELECT * FROM usuarios_ocoach WHERE nome_site='".$nome_site_update."'")){
			$erro .= "\nERRO: ".mysql_error()."";
				echo $erro;
				exit;
		}
		$ArSqlPesquisaId = mysql_fetch_array($SqlPesquisaId);

		//Update Curso
		if(!$QryOcoachUpdate = mysql_query("UPDATE ocoachs set nome_curso = '".$curso."' WHERE id= '".$ArSqlPesquisaId['id_ocoach']."'")){
			$erro .= "\nERRO: ".mysql_error()."";
				echo $erro."163";
				exit;
		}
		
		

		require_once('../../../../functions.php');
		require_once('../../../../conecta.php');
		require_once('../../../../classes/class.phpmailer.php');
		require_once('../../../../classes/class.Email.php');
	
		$SqlUpdateOasis = "UPDATE is_gera_ocoach SET  id_pessoa='$numreg',nome_site='$nome_site', email_pessoa_site='$login' WHERE numreg='".$pnumreg."'";
		if(!$QrySqlInsertOasis = mysql_query($SqlUpdateOasis)){
			$erro .= "\nERRO: ".mysql_error()."";
				echo $erro;
				exit;
		}
		//Envia E-mail	
		$Mail = new Email();
		$assunto = 'Sua p�gina na SBCoaching foi criada';
		$Mail->_Assunto($assunto);
		$Mail->_AdicionaDestinatarioCC($email,$nome);
		$msg='';
		$mensagem .="Parab�ns, ".$nome."!<br><br>
		Agora voc� tem uma p�gina profissional hospedada dentro do site da <strong>Sociedade Brasileira de Coaching�</strong>. Com ela, voc� poder� elaborar o seu perfil, incluindo        informa��es personalizadas para divulgar o seu trabalho como coach.<br><br>
		Al�m de criar um perfil de acordo com os seus diferenciais, nesta p�gina, voc� tamb�m ter� acesso a um kit de marketing, contendo: cart�o de visita, folder, papel timbrado        (na vertical e na horizontal) e manual para a utiliza��o dos arquivos gr�ficos e de web.<br><br>
		
		<strong>Veja como � f�cil personalizar a sua p�gina:</strong><br><br>
		<blockquote>
		�	Acesse: http://www.sbcoaching.com.br/ocoach/".$nome_site."<br>
		�	Clique na op��o �Restrito� localizada no rodap� da p�gina;<br>
		�	Insira o email: ".$login." e a senha als0215;<br>
		�	Feito este processo, abrir� um p�gina com quatro op��es de menu: Alterar Cadastro, Kit de Marketing, Manual e Alterar Senha;<br>
		�	Clique em �Manual� para entender, passo a passo, como personalizar a sua p�gina, tendo a possibilidade de trocar fotos e inserir informa��es profissionais.<br>
		<blockquote>
		<br><br>
		� importante ressaltar que a sua p�gina est� hospedada dentro do dom�nio da <strong>Sociedade Brasileira de Coaching� (www.sbcoaching.com.br)</strong>, e que tanto o seu        perfil profissional quanto o kit de marketing ser�o de grande import�ncia para a divulga��o do seu trabalho como coach, fazendo com que voc� possa dar os primeiros passos        para lan�ar-se no mercado e chegar at� os seus clientes.
		<br><br>
		Agora, al�m de ser um membro da <strong>SBCoaching�</strong>, voc� tamb�m tem uma p�gina exclusiva para divulgar o seu trabalho e j� sair atuando na �rea!
		<br><br>
		
		Um forte abra�o!<br><br>
		<strong>Equipe Sociedade Brasileira de Coaching�</strong>.
		";
		$Mail->_Corpo($mensagem .'<br/>'.$assinatura);
		if(empty($msg)){
			if($Mail->_EnviaEmail()){
				 $msg = "Gerado com Sucesso";
			} 
			else {
				$msg = 'Erro ao enviar o e-mail:' . $Mail->ErrorInfo;
			}
		}


	}	


	echo $msg;
	
	
	
	function gk($length)
	{
		$caracs="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$key="";
		for($i=1; $i<=$length; $i++)
		{
			$key.=$caracs[mt_rand(1, strlen($caracs))-1];
		}
		return $key;
	}

	function getPost( $key ){
		return isset( $_POST[ $key ] ) ? filter( $_POST[ $key ] ) : null;
	}
	function filter( $var ){
		return $var;//fa�a o tratamento
	}

	
?>