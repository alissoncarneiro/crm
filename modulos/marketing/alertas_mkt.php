<?

//================================================================================================================================//
// BLOCO DE ALERTA POR E-MAIL DA ATIVIDADE
//================================================================================================================================//
require_once("../../funcoes.php");
require_once("../../conecta.php");
require_once("../../gera_segmentacao.php");
require_once("../../smtp.class.php");

echo '<font name="Arial" Size=2>';
$qry_is_workflow_alerta_mkt = farray(query("select * from is_workflow_alerta_mkt where numreg = " . $_GET["pnumreg"]));
$qry_segmentacao = farray(query("select * from is_segmentacao where numreg = '" . $qry_is_workflow_alerta_mkt["id_segmentacao"] . "'"));


$res = array();
$remetente = 'areademembros@sbcoaching.com.br';
$params['host'] = 'smtp.sbcoaching.com.br';    // The smtp server host/ip
$params['port'] = 25;      // The smtp server port
$params['helo'] = 'SBCoaching';   // What to use when sending the helo command. Typically, your domain/hostname
$params['auth'] = TRUE;      // Whether to use basic authentication or not
$params['user'] = $remetente;    // Username for authentication
$params['pass'] = 'als0215';    // Password for authentication


if (($qry_is_workflow_alerta_mkt["sn_umavez"] == "1") or ($qry_is_workflow_alerta_mkt["sn_ativo"] == "0")) {
    if ($qry_is_workflow_alerta_mkt["dt_ult_execucao"] == date("Y-m-d")) {
        exit;
    }
}


// PEÇA =================================================================================
$qry_campanha_peca = farray(query("select * from is_campanha_peca where numreg = '" . $qry_is_workflow_alerta_mkt["id_peca"] . "'"));
//abrimos o arquivo em modo de leitura e lemos o conteudo
$nome_peca = substr($qry_campanha_peca["arquivo"], 0, strlen($qry_campanha_peca["arquivo"]) - 4);
$arquivo = $caminho_arquivos . $nome_peca . ".htm";
$fp = fopen($arquivo, 'r');
$texto = fread($fp, filesize($arquivo));
$texto_ori = $texto;
$assunto_ori = $qry_campanha_peca["assunto"];
fclose($fp);
//=======================================================================================

$filtro_segmentacao = trata_tags_sql($qry_segmentacao["sql_filtro_conta"]);
$filtro_segmentacao_contato = trata_tags_sql($qry_segmentacao["sql_filtro_contato"]);

// Prepara SQLs
if ($filtro_segmentacao) {
    $sql_segmenta_pessoa = "select * from is_pessoa where " . $filtro_segmentacao;
} else {
    $sql_segmenta_pessoa = "select * from is_pessoa";
}
if ($filtro_segmentacao_contato) {
    $sql_segmenta_contato = "select * from is_contato where " . $filtro_segmentacao_contato . " and id_empresa = ";
} else {
    $sql_segmenta_contato = "select * from is_contato where id_empresa = ";
}


// Ou inclui na Lista o resultado da segmentação

$sql_atualiza = query($sql_segmenta_pessoa);
while ($qry_atualiza = farray($sql_atualiza)) {
    $ntot_conta++;
	$nome_segmentacao= $qry_segmentacao['nome_segmentacao'];

	// Se deve incluir contatos - é necessário aplicar o filtro de contatos para adicionar cada contato de cada conta na lista
    if ($qry_segmentacao["sn_incluir_contato_lista"] == '1') {

        $sql_atualiza_contato = query($sql_segmenta_contato . ' ' . $qry_atualiza["numreg"]);
        $sn_possui_contato = 0;

		while ($qry_atualiza_contato = farray($sql_atualiza_contato)) {
            $sn_possui_contato = 1;
            alertas_mkt_executar($nome_segmentacao);
            $ntot_contato++;
        }
        // Se não achou contato deve incluir sem os dados do contato
        if ($sn_possui_contato == 0) {
			
            alertas_mkt_executar($nome_segmentacao);
        }
    } else {
        alertas_mkt_executar($nome_segmentacao);
    }
}


query("update is_workflow_alerta_mkt set dt_ult_execucao = '" . date("Y-m-d") . "', hr_ult_execucao = '" . date("H:i") . "' where numreg = " . $_GET["pnumreg"]);


echo '</font>';

function alertas_mkt_executar($nome_segmentacao) {

   global $params, $tipoBanco, $qry_atualiza, $qry_atualiza_contato, $qry_is_workflow_alerta_mkt, $remetente, $send_params, $texto, $texto_ori, $assunto_ori;
    // Prepara Saudação
    $hora_atual = gmdate("H", time() + 3600 * -2);
    if (($hora_atual >= 0) && ($hora_atual <= 12)) {
        $saudacao = 'Bom dia';
    }
    if (($hora_atual >= 13) && ($hora_atual <= 18)) {
        $saudacao = 'Boa tarde';
    }
    if (($hora_atual >= 19) && ($hora_atual <= 24)) {
        $saudacao = 'Boa noite';
    }

    $nome_pessoa = $qry_atualiza["razao_social_nome"];
    $nome_contato = $qry_atualiza_contato["nome"];
    if ($qry_atualiza_contato["sexo"] == "M") {
        $VSoax = "o";
    } else {
        $VSoax = "a";
    }

	//Desenvolvido por Alisson Carneiro
	$SqlCoachingInscricaoCursoDetalhe =("
										SELECT *, min(dt_curso) as data1,  max(dt_curso) as data2 FROM c_coaching_inscricao_curso_detalhe 
											WHERE 
												id_agenda='".$nome_segmentacao."' 
													AND id_pessoa ='".$qry_atualiza["numreg"]."' 
										group by id_pessoa
										");
	$QrySqlCoachingInscricaoCursoDetalhe = query($SqlCoachingInscricaoCursoDetalhe);

	while($ArSqlCoachingInscricaoCursoDetalhe = farray($QrySqlCoachingInscricaoCursoDetalhe)){

		$data1 = explode('-',$ArSqlCoachingInscricaoCursoDetalhe['data1']);
		$data_convertida1 =explode(' ',$data1[2]);

		$data2 = explode('-',$ArSqlCoachingInscricaoCursoDetalhe['data2']);
		$data_convertida2 =explode(' ',$data2[2]);
		
		
		$mes= $data2[1];
			switch($mes){ 
			case "1": $mes = "de Janeiro";      break;
			case "2": $mes = "de Fevereiro";    break;
			case "3": $mes = "de Mar&ccedil;o"; break;
			case "4": $mes = "de Abril";        break;
			case "5": $mes = "de Maio";         break;
			case "6": $mes = "de Junho";        break;
			case "7": $mes = "de Julho";        break;
			case "8": $mes = "de Agosto";       break;
			case "9": $mes = "de Setembro";     break;
			case "10": $mes = "de Outubro";     break;
			case "11": $mes = "de Novembro";    break;
			case "12": $mes = "de Dezembro";    break;
			}

			$VS_DT_1 = $data_convertida1[0];
			$VS_DT_2 = $data_convertida2[0];
			
			$hotel=$ArSqlCoachingInscricaoCursoDetalhe['id_hotel'];
			
	}
	$SqlHotel = query("select * from c_coaching_hotel where numreg = '".$hotel."'");
	$ArSqlHotel= farray($SqlHotel);

	$VS_ENDERECO_CURSO = " ".$ArSqlHotel['nome_hotel']." | ".$ArSqlHotel['endereco'].", ".$ArSqlHotel['numero']." | ".$ArSqlHotel['bairro']." - ".$ArSqlHotel['cidade']." - ".$ArSqlHotel['uf']."";
	$VS_TEL_LOCAL_CURSO = $ArSqlHotel['tel_hotel'];

    $texto = str_replace($nome_peca . '_arquivos', $caminho_web . 'arquivos/', $texto_ori);
    $texto = str_replace('VSNOME', $nome_pessoa, $texto);
    $texto = str_replace('VSCONTATO', $nome_contato, $texto);
    $texto = str_replace('VSOAX', $VSoax, $texto);
    $texto = str_replace('VSSAUDACAO', $saudacao, $texto);

	$texto = str_replace('VS_DT_1', $VS_DT_1, $texto);
	$texto = str_replace('VS_DT_2', $VS_DT_2, $texto);
	$texto = str_replace('VS_MES_1', $mes, $texto);

	$texto = str_replace('VS_ENDERECO_CURSO', $VS_ENDERECO_CURSO, $texto);
	$texto = str_replace('VS_TEL_LOCAL_CURSO', $VS_TEL_LOCAL_CURSO, $texto);

    $assunto = str_replace('VSNOME', $nome_pessoa, $assunto_ori);
    $assunto = str_replace('VSCONTATO', $nome_contato, $assunto);
    $assunto = str_replace('VSOAX', $VSoax, $assunto);
    $assunto = str_replace('VSSAUDACAO', $saudacao, $assunto);
	

    // Se tem e-mail
    if ((($qry_atualiza["email"]) || ($qry_atualiza_contato["email_profissional"])) && ($qry_is_workflow_alerta_mkt["sn_apenas_atividade"] == "0")) {

print_r($qry_atualiza_contato["email_profissional"]."<br>"   );
        //Prepara Recipients
        if ($qry_is_workflow_alerta_mkt["sn_teste"] == "0") {
            
			$edtemails = $qry_atualiza["email"];
			
			if (empty($edtemails)) {
                $edtemails = $qry_atualiza_contato["email_profissional"];
           	}
        } else {
            $edtemails = $qry_is_workflow_alerta_mkt["email_teste"];
        }
	

        $recipients = array();
        $i = 0;
        while ($edtemails) {
            $pos = strpos($edtemails, ';');
            if ($pos === false) {
                $recipients[$i] = $edtemails;
                $edtemails = '';
            } else {
                $recipients[$i] = substr($edtemails, 0, $pos);
                $edtemails = str_replace($recipients[$i] . ';', '', $edtemails);
            }
            $i = $i + 1;
        }
		
	

        for ($j = 0; $j <= ($i - 1); $j++) {

            $send_params['recipients'] = array($recipients[$j]);
            $send_params['headers'] = array(
                'Content-Type: text/html; charset=iso-8859-1',
                'From: "' . "OASIS" . '" <' . $remetente . '>', // Headers
                'To: ' . $recipients[$j] . '',
                'Subject: ' . $assunto . '',
                'Return-Path: <' . $remetente . '>'
            );
            $send_params['from'] = $remetente; // This is used as in the MAIL FROM: cmd
            $send_params['body'] = $texto;
            // The body of the email
            if (is_object($smtp = smtp::connect($params)) AND $smtp->send($send_params)) {
                echo 'e-mail enviado com sucesso para ' . $recipients[$j] . " " . $nome_pessoa . ' !<br>';
                query("insert into is_atividade(id_tp_atividade,assunto,id_pessoa,id_pessoa_contato,id_usuario_resp,dt_inicio,hr_inicio,dt_prev_fim,hr_prev_fim,id_situacao,pecahtm) values (6,'Peça de comunicação : " . TextoBD($tipoBanco, $assunto) . "','" . $qry_atualiza["numreg"] . "','" . $qry_atualiza_contato["numreg"] . "','" . $_SESSION["id_usuario"] . "','" . date("Y-m-d") . "','" . date("H:i") . "','" . date("Y-m-d") . "','" . date("H:i") . "',4,'" . nl2br(str_replace("'", "\'", $texto)) . "')");
            } else {
                echo "Erro, não foi possível enviar este e-mail " . print_r($smtp->errors) . "<br>";
                // The reason for failure should be in the errors variable
            }
        }
    } else {
        // Gerar Atividade
        if ($qry_is_workflow_alerta_mkt["sn_gera_atividade"] == "1") {
            query("insert into is_atividade(id_tp_atividade,assunto,id_pessoa,id_pessoa_contato,id_usuario_resp,dt_inicio,hr_inicio,dt_prev_fim,hr_prev_fim,id_situacao,pecahtm) values (6,'Enviar peça de comunicação : " . TextoBD($tipoBanco, $assunto) . "','" . $qry_atualiza["numreg"] . "','" . $qry_atualiza_contato["numreg"] . "','" . $qry_is_workflow_alerta_mkt["id_usuario_resp"] . "','" . date("Y-m-d") . "','" . date("H:i") . "','" . soma_dias(1) . "','" . date("H:i") . "',1,'" . nl2br(str_replace("'", "\'", $texto)) . "')");
            echo 'Atividade de envio manual gerada com sucesso para ' . $nome_pessoa . ' !<br>';
        }
    }
}

?>