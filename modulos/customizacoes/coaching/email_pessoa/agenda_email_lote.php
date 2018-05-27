<?php

header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
require("../../../../conecta.php");

function CriaBlocoInsert($retPecHtml,$id_pessoa, $nome, $email, $dthr_email,$assunto){		

		$email_assunto = $assunto;
		$arInsert = array(
						  'id_pessoa' 			=> $id_pessoa,
						  'nome_contato' 		=> $nome,
						  'email_contato' 		=> $email,
						  'email_assunto' 		=> $email_assunto,
						  'email_corpo' 		=> $retPecHtml,
						  'dthr_email'			=> $dthr_email,
						  'wcp_sn_envia'		=> 0,
						  'email_remetente'		=> 'areademembros@sbcoaching.com.br',
						  'id_usuario_resp'		=> '99'
						  );
		
		$keys = array_keys($arInsert);
		$vals = array_values($arInsert);
		
		$insert = "INSERT INTO `is_email_pessoa` "
		 . "(" . implode(", ", $keys) . ") "
		 . "VALUES('" . implode("', '", $vals) . "')";	
	
		if(mysql_query($insert)){
			$msg = "Agendado com Sucesso";
		}else{
			$msg = "Erro ao Inserir";
		}
		return $msg;
}

	echo '<html><head><title>Envio de e-mail'.$_REQUEST['pnumreg'].'</title></head><body>';
	
	$assunto = $_REQUEST['assunto'];
	$numreg= $_REQUEST['pnumreg'];
	$htmlPadrao = $_REQUEST['text_area_corpo_email'];

	$sqlAgendaLote = "select inscricao.id_pessoa,pessoa.razao_social_nome , pessoa.email from c_coaching_inscricao_curso_detalhe as inscricao
inner join is_pessoa as pessoa on pessoa.numreg =inscricao.id_pessoa where inscricao.id_agenda = '".$numreg."' group by id_pessoa";
	$qryAgendaLote = mysql_query($sqlAgendaLote);
	
	while($arAgendaLote = mysql_fetch_array($qryAgendaLote)){
		$dthr_email	= date("Y-m-d h:i:s");			
		
		$html = str_replace('VS_NOME',$arAgendaLote['razao_social_nome'],$htmlPadrao);
		
		$ret = CriaBlocoInsert($html,$arAgendaLote['id_pessoa'],$arAgendaLote['razao_social_nome'],$arAgendaLote['email'], $dthr_email,$assunto);	
		$valErro = 0;
		if($ret == "Agendado com Sucesso"){
			$val ++;
		}else{
			$valErro ++;
		}
	}
$msg = "Agendados: ".$val." Erros:".$valErro  ;
echo '<script>alert(\''.$msg.'\');window.close();</script>';
echo '</body></html>';
?>