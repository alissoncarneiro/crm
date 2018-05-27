<?php
@header("Content-Type:text/html; charset=iso-8859-1;");
@session_start();
$id_usuario = $_SESSION["id_usuario"];

include "../../conecta.php";
include "../../funcoes.php";
include "../../functions.php";
include('../../classes/class.send.php');

$MicroTimeInicio = microtime(true);
$QtdeErro = 0;
$qtde_processada = 0;
$NumregLog = CriaLog('p_alerta_atraso');

$email_teste = $_POST["edtemail_teste"];

$enviar_email = $_POST["edtenviar_email"];
if (empty($enviar_email)) {
    $enviar_email = $_GET["enviar_email"];
    if (empty($enviar_email)) {
        $enviar_email = 'N';
    }
}

//INSTANCIANDO A CLASSE E PREPARANDO A MESMA PARA EFETUAR O ENVIO
$Mail = new SendMail;
$Mail->IsSMTP(smtp_porta); //PORTA UTILIZADA PARA O SMTP
$Mail->Host = smtp_host; // SMTP servers
$Mail->SMTPAuth = smtp_exige_autenticacao; // Caso o servidor SMTP precise de autenticação TRUE/FALSE
$Mail->Username = smtp_email; // USER NAME QUE IRÁ ENVIAR O E-MAIL
$Mail->Password = smtp_email_senha; // SENHA DO E-MAIL
$Mail->From = smtp_email; // E-MAIL DE QUEM ESTÁ ENVIANDO
$Mail->FromName = smtp_cliente_nome; // NOME DE QUEM ENVIOU O E-MAIL
$Mail->SetLanguage('pt-br', 'classes/language/'); // TIPO DE LINGUAGEM DE ERRO QUE DEVE SER INFORMADO
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: Calcular atraso de atividades com opção de enviar e-mail de alerta</title>
        <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../../estilos_css/cadastro.css">
            <link rel="stylesheet" type="text/css" media="all" href="../../estilos_css/calendar-blue.css" title="win2k-cold-1" />
            <style type="text/css">
                <!--
                body {
                    margin-left: 0px;
                    margin-top: 0px;
                    margin-right: 0px;
                    margin-bottom: 0px;
                }
                -->
            </style>
            <body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
                <center>
                    <div id="principal_detalhes">
                        <div id="topo_detalhes">
                            <div id="logo_empresa"></div>
                            <!--logo -->
                        </div><!--topo -->

                        <?
                        if ($enviar_email == "N") {
                            echo '<hr><b>PROCESSAMENTO - ATRASOS</b><hr>';
                        } else {
                            echo '<hr><b>PROCESSAMENTO - ATRASOS COM ENVIO DE E-MAIL</b><hr>';
                        }
                        echo $email_teste . '<br>';

                        $sql = query("select distinct id_usuario_resp from is_atividade where id_situacao in (1,2,3) and dt_prev_fim <  '" . date("Y-m-d") . "' order by id_usuario_resp");
                        while ($qry = farray($sql)) {
                            $count = 0;
                            $qry_usu = farray(query("select * from is_usuario where numreg = '" . $qry["id_usuario_resp"] . "'"));
                            if ($qry_usu["nome_usuario"]) {
                                $html_conteudo_cab = '<table border="0">';
                                $html_conteudo_cab .= '<tr bgcolor="#dae8f4"><td colspan="6"><b>Responsável : ' . $qry_usu["nome_usuario"] . '</b></td></tr>';
                                $html_conteudo_cab .= '<tr bgcolor="#dae8f4"><td>Tipo</td><td>Dt.Prazo</td><td>Hr.Prazo</td><td>Conta</td><td>Assunto</td><td>Atraso (Dias corridos)</td></tr>';
                                $html_conteudo = "";
                                $html_conteudo_superior = "";
                                $sql_ativ = query("select * from is_atividade where id_situacao in (1,2,3) and dt_prev_fim <  '" . date("Y-m-d") . "' and id_usuario_resp = '" . $qry["id_usuario_resp"] . "' order by dt_prev_fim, hr_prev_fim");
                                while ($qry_ativ = farray($sql_ativ)) {
                                    $atraso = (DiferencaEntreDatas($qry_ativ["dt_prev_fim"] . ' ' . $qry_ativ["hr_prev_fim"] . ':00', date("Y-m-d H:i:s"), 86400) * 1) - 1;
                                    query("update is_atividade set atraso_dias = $atraso where numreg = '" . $qry_ativ["numreg"] . "'");
                                    $qry_cli = farray(query("select * from is_pessoa where numreg = '" . $qry_ativ["id_pessoa"] . "'"));
                                    $qry_tipo = farray(query("select * from is_tp_atividade where numreg = '" . $qry_ativ["id_tp_atividade"] . "'"));
                                    $html_conteudo .= '<tr><td>' . $qry_tipo["nome_tp_atividade"] . "</td><td>" . DataGetBD($qry_ativ["dt_prev_fim"]) . "</td><td>" . $qry_ativ["hr_prev_fim"] . "</td><td>" . $qry_cli["razao_social_nome"] . '</td><td>' . $qry_ativ["assunto"] . '</td><td>' . $atraso . '</td></tr>';
                                    // Checando se deve avisar o superior
                                    if (($qry_usu["atraso_dias_alerta"] * 1 > 0) && ($qry_usu["id_usuario_gestor"] * 1 > 0)) {
                                        if (($atraso) >= ($qry_usu["atraso_dias_alerta"] * 1)) {
                                            $html_conteudo_superior .= '<tr><td>' . $qry_tipo["nome_tp_atividade"] . "</td><td>" . DataGetBD($qry_ativ["dt_prev_fim"]) . "</td><td>" . $qry_ativ["hr_prev_fim"] . "</td><td>" . $qry_cli["razao_social_nome"] . '</td><td>' . $qry_ativ["assunto"] . '</td><td>' . $atraso . '</td></tr>';
                                        }
                                    }
                                    $count++;
                                }
                                $html_conteudo_rod = "</table><br>";
                                $html_conteudo_rod .= "Atividades Atrasadas : " . $count;
                                echo $html_conteudo_cab . $html_conteudo . $html_conteudo_rod . "<br><hr>";
                                if ($enviar_email == "S") {
                                    // Enviando e-mail para o responsável
                                    $Mail->Subject = 'OASIS : ' . $count . ' atraso(s) - ' . $qry_usu["nome_usuario"]; // TÍTULO DO ARQUIVO
                                    if ($email_teste) {
                                        $Mail->Emails[$email_teste] = $qry_usu["nome_usuario"];
                                    } else {
                                        $Mail->Emails[$qry_usu["email"]] = $qry_usu["nome_usuario"];
                                    }
                                    $Mail->RecebeEmails();
                                    $Mail->CriaBody($html_conteudo_cab . $html_conteudo . $html_conteudo_rod . "<br><hr>");
                                    $Mail->BodyNoHTML('Seu e-mail não suporta o formanto de envio do pedido.');
                                    if ($Mail->EnviarMail()) {
                                        echo 'E-Mail enviado com sucesso';
                                    } else {
                                        echo $Mail->getDescription();
                                    }
                                    unset($Mail->Emails);
                                    unset($Mail->to);
                                    // Enviando e-mail para o Superior Imediato
                                    if ($html_conteudo_superior) {
                                        $qry_sup = farray(query("select * from is_usuario where numreg = '" . $qry_usu["id_usuario_gestor"] . "'"));
                                        $Mail->Subject = 'OASIS : Aviso a(o) Gestor(a) ' . $count . ' atraso(s) - ' . $qry_usu["nome_usuario"]; // TÍTULO DO ARQUIVO
                                        if ($email_teste) {
                                            $Mail->Emails[$email_teste] = $qry_sup["nome_usuario"];
                                        } else {
                                            $Mail->Emails[$qry_sup["email"]] = $qry_sup["nome_usuario"];
                                        }
                                        $Mail->RecebeEmails();
                                        $Mail->CriaBody($html_conteudo_cab . $html_conteudo_superior . $html_conteudo_rod . "<hr><br>");
                                        $Mail->BodyNoHTML('Seu e-mail não suporta o formanto de envio do pedido.');
                                        if ($Mail->EnviarMail()) {
                                            echo '<br>E-Mail enviado com sucesso para o(a) gestor(a) ' . $qry_sup["nome_usuario"] . "<hr><br>";
                                        } else {
                                            $QtdeErro++;
                                            GravaLogDetalhe($NumregLog,print_r($Mail->Emails,true),'Erro ao enviar email: '.$Mail->getDescription(),'','Erro');
                                            echo $Mail->getDescription() . "<hr><br>";
                                        }
                                        $qtde_processada++;
                                        unset($Mail->Emails);
                                        unset($Mail->to);
                                    }
                                }
                            }
                        }
                        
                        FinalizaLog($NumregLog,$MicroTimeInicio,0,0,$QtdeErro,0,$qtde_processada);
                        
                        ?>
                        <hr>
                            <center>
                                <input type="button" value="Imprimir" name="B4" class="botao_form" onclick="javascript:window.print();">
                                <input type="button" value="Voltar" name="B4" class="botao_form" onclick="javascript:history.back(1);">
                                    <input type="button" value="Fechar" name="B4" class="botao_form" onclick="javascript:window.close();">
                                            </center>
                                            </div>
                                            </body>
                                            </html>