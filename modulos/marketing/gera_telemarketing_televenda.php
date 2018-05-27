<?php
require_once("../../conecta.php");
require_once("../../functions.php");
require_once("../../funcoes.php");
@header("Content-Type:text/html; charset=iso-8859-1;");
@session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: Programação da Agenda do TLMKT / Televendas</title>
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
                        $pnumreg = $_GET['pnumreg'];
                        $oficial = $_GET['oficial'];

// Recuperando informações do BD
                        $a_campanha_prog_ativ = farray(query("select * from is_campanha_prog_ativ where numreg = " . $pnumreg));
                        $a_campanha = farray(query("select * from is_campanha where numreg = " . $a_campanha_prog_ativ["id_campanha"]));
                        $q_lista_pessoa = query("select is_lista_pessoa.*, is_pessoa.valor from is_lista_pessoa, is_pessoa where is_lista_pessoa.id_pessoa = is_pessoa.numreg and is_lista_pessoa.id_lista = " . $a_campanha["id_lista"] . ' order by valor desc');
                        $a_tp_atividade = farray(query("select * from is_tp_atividade where numreg = " . $a_campanha_prog_ativ["id_tp_atividade"]));
// Setando parametros
                        $i_ini = $a_campanha_prog_ativ["filtro_lista_de"] * 1;
                        $i_fim = $a_campanha_prog_ativ["filtro_lista_ate"] * 1;
                        $retirar_inadimplente = $a_campanha_prog_ativ["sn_retirar_inadimplentes"];
                        $retirar_sac = $a_campanha_prog_ativ["sn_retirar_sac"];
                        $recencia_ini = $a_campanha_prog_ativ["filtro_recencia_de"] * 1;
                        $recencia_fim = $a_campanha_prog_ativ["filtro_recencia_ate"] * 1;

                        $id_tp_atividade = $a_campanha_prog_ativ["id_tp_atividade"];
                        $respeita_carteira = $a_campanha_prog_ativ["sn_respeita_carteira"];
// Montando Array com Operadores
                        $tot_distr = 0;
                        $q_campanha_prog_ativ_usu = query("select * from is_campanha_prog_ativ_usu where id_campanha_prog_ativ = " . $pnumreg);
                        while ($a_campanha_prog_ativ_usu = farray($q_campanha_prog_ativ_usu)) {
                            $tot_distr++;
                            $a_distr[$tot_distr]["data"] = substr($a_campanha_prog_ativ["dt_inicio"], 0, 10);
                            $a_distr[$tot_distr]["id_usuario"] = $a_campanha_prog_ativ_usu["id_usuario"];
                            $a_distr[$tot_distr]["qtde_gerada"] = 0;
                            $a_distr[$tot_distr]["qtde_gerada_tot"] = 0;
                            $a_distr[$tot_distr]["completo"] = 0;
                            $a_ind_distr[$a_campanha_prog_ativ_usu["id_usuario"]] = $tot_distr;
                        }


                        $i = 0;
                        $i_distr = 1;
                        $sn_completou_ditribuicao = 0;
                        echo '<table border="0">';
                        echo '<tr bgcolor="#dae8f4"><td>Conta</td><td>Contato</td><td>Operador(a) Padrão</td><td>Recência</td><td>Valor Acumulado</td><td>Atividade Gerada</td><td>Operador(a) Selecionado</td><td>Observações</td>';
                        while ($a_lista_pessoa = farray($q_lista_pessoa)) {
                            $i++;
                            // Abaixo para cada Conta deve-se checar se respeita os parametros da Programação
                            // Checar se esta na faixa
                            if (($i >= $i_ini) && ($i <= $i_fim)) {
                                $acao = "";
                                $a_pessoa = farray(query("select * from is_pessoa where numreg = " . $a_lista_pessoa["id_pessoa"]));
                                $a_operador_padrao = farray(query("select * from is_usuario where numreg = '" . $a_pessoa["id_operador_padrao"] . "'"));
                                // Checar se esta inadimplente
                                if ($retirar_inadimplente == '1') {
                                    if ($a_pessoa["sn_inadimplente"] == '1') {
                                        $acao .= 'Inadimplente, ';
                                    }
                                }
                                // Checar Atendimento-SAC em Aberto
                                if ($retirar_sac == '1') {
                                    $a_sac = farray(query("select id_situacao from is_atividade where id_tp_atividade = 1 and id_situacao = 1 and id_pessoa = " . $a_lista_pessoa["id_pessoa"]));
                                    if ($a_sac["id_situacao"] == '1') {
                                        $acao .= 'SAC em Aberto, ';
                                    }
                                }
                                // Checar Recencia
                                if (($recencia_ini >= 1) && ($recencia_fim >= 1)) {
                                    $recencia = $a_pessoa["recencia"] * 1;
                                    if ((($recencia < $recencia_ini) || ($recencia > $recencia_fim)) && ($recencia > 0)) {
                                        $acao .= 'Fora da Recência (' . $recencia . '), ';
                                    }
                                }
                                // Checar se Existe Televendas em Aberto
                                $a_existe = farray(query("select id_situacao from is_atividade where id_tp_atividade = " . $id_tp_atividade . " and id_situacao = 1 and id_pessoa = " . $a_lista_pessoa["id_pessoa"]));
                                if ($a_existe["id_situacao"] == '1') {
                                    $acao .= 'Já possui ' . $a_tp_atividade["nome_tp_atividade"] . ' em Aberto, ';
                                }
                                // Checar se Deve respeitar a carteira
                                if ($respeita_carteira == '1') {
                                    $a_existe_usu = farray(query("select * from is_campanha_prog_ativ_usu where id_campanha_prog_ativ = " . $pnumreg . " and id_usuario = '" . $a_pessoa["id_operador_padrao"] . "'"));
                                    if (($a_existe_usu["id_usuario"] * 1) == 0) {
                                        $acao .= 'Operador(a) não encontrado(a) na distribuição, ';
                                        $id_operador_escolhido = '-1';
                                    } else {
                                        $id_operador_escolhido = $a_pessoa["id_operador_padrao"];
                                        $i_distr = $a_ind_distr[$id_operador_escolhido];
                                    }
                                } else {
                                    // Deve distribuir a conta por operador que nao completou a lista ainda
                                    if ($sn_completou_ditribuicao == 0) {
                                        $sn_achou_oper = 0;
                                        for ($i_sel_oper = $i_distr; $i_sel_oper <= $tot_distr; $i_sel_oper++) {
                                            if (($a_distr[$i_sel_oper]["completo"] * 1) == 0) {
                                                $sn_achou_oper = 1;
                                                break;
                                            }
                                        }
                                        // Se não achou deve voltar a buscar no array do principio
                                        if ($sn_achou_oper == 0) {
                                            for ($i_sel_oper = 1; $i_sel_oper <= $i_distr; $i_sel_oper++) {
                                                if (($a_distr[$i_sel_oper]["completo"] * 1) == 0) {
                                                    break;
                                                }
                                            }
                                        }
                                        // Se todos já estão completos
                                        if ($sn_achou_oper == 0) {
                                            $sn_completou_ditribuicao = 1;
                                            $acao .= 'Todos(as) Operadores(as) já possuem qtde máx. de Atividades em Aberto, ';
                                            $i_distr = 0;
                                            $id_operador_escolhido = '';
                                        } else {
                                            $i_distr = $i_sel_oper;
                                            $id_operador_escolhido = $a_distr[$i_distr]["id_usuario"];
                                        }
                                    } else {
                                        $acao .= 'Todos(as) Operadores(as) já possuem qtde máx. de Atividades em Aberto, ';
                                    }
                                }

                                $a_operador_escolhido = farray(query("select is_campanha_prog_ativ_usu.*, is_usuario.nome_usuario from is_campanha_prog_ativ_usu, is_usuario where is_usuario.numreg = is_campanha_prog_ativ_usu.id_usuario and id_campanha_prog_ativ = " . $pnumreg . " and is_campanha_prog_ativ_usu.id_usuario = '" . $id_operador_escolhido . "'"));

                                if ($a_operador_escolhido["nome_usuario"]) {

                                    // Checar se não ultrapassou qtde maxima de atividades em aberto
                                    if ($a_operador_escolhido["qtde_max_ativ_aberto"] * 1 > 0) {
                                        if ($a_operador_escolhido["sn_considera_tudo"] == '1') {
                                            $id_tp_atividade_busca = '5,9';
                                        } else {
                                            $id_tp_atividade_busca = $id_tp_atividade;
                                        }
                                        $qtde_ativ_operador_total = farray(query("select count(*) as total from is_atividade where id_tp_atividade in (" . $id_tp_atividade_busca . ") and id_situacao = 1 and id_usuario_resp = '" . $id_operador_escolhido . "'"));

                                        if ((($qtde_ativ_operador_total["total"] * 1) > ($a_operador_escolhido["qtde_max_ativ_aberto"] * 1)) || (($a_distr[$i_distr]["qtde_gerada_tot"] * 1) > ($a_operador_escolhido["qtde_max_ativ_aberto"] * 1))) {
                                            $acao .= 'Operador(a) já possui qtde máx. de Atividades em Aberto, ';
                                            $a_distr[$i_distr]["completo"] = '1';
                                        } else {
                                            if ((($qtde_ativ_operador_total["total"] * 1) == ($a_operador_escolhido["qtde_max_ativ_aberto"] * 1)) || (($a_distr[$i_distr]["qtde_gerada_tot"] * 1) == ($a_operador_escolhido["qtde_max_ativ_aberto"] * 1))) {
                                                $a_distr[$i_distr]["completo"] = '1';
                                            }
                                        }
                                    }
                                    // Checar se ultrapassou qtde maxima de atividades por dia e caso positivo somar 1 dia útil na data
                                    if ($a_operador_escolhido["qtde_max_ativ_dia"] * 1 > 0) {
                                        $qtde_ativ_operador_total_dia = farray(query("select count(*) as total from is_atividade where id_tp_atividade in (" . $id_tp_atividade_busca . ") and id_situacao = 1 and id_usuario_resp = '" . $id_operador_escolhido . "' and dt_prev_fim = '" . $a_distr[$i_distr]["data"] . "'"));
                                        if ((($qtde_ativ_operador_total_dia["total"] * 1) > ($a_operador_escolhido["qtde_max_ativ_dia"] * 1)) || (($a_distr[$i_distr]["qtde_gerada"] * 1) >= ($a_operador_escolhido["qtde_max_ativ_dia"] * 1))) {
                                            $a_distr[$i_distr]["data"] = substr(soma_dias_ut($a_distr[$i_distr]["data"], 1, '1'), 0, 10);
                                            $a_distr[$i_distr]["qtde_gerada"] = 0;
                                        }
                                    }




                                    if (empty($acao)) {
                                        $acao = 'OK, ';
                                    }
                                }
                            } else {
                                $acao = 'Fora da Faixa da Lista (' . $i . '), ';
                            }
                            $acao = substr($acao, 0, strlen($acao) - 2);
                            if ($acao == 'OK') {
                                $sn_atividade_gerada = 'Sim';
                                $bgcolor_td = "#99FF99";
                                $acao = 'OK - Gerado para ' . $a_operador_escolhido["nome_usuario"] . ' - ' . DataGetBD($a_distr[$i_distr]["data"]);
                                $a_distr[$i_distr]["qtde_gerada"] = ($a_distr[$i_distr]["qtde_gerada"] * 1) + 1;
                                $a_distr[$i_distr]["qtde_gerada_tot"] = ($a_distr[$i_distr]["qtde_gerada_tot"] * 1) + 1;

                                // Se é oficial deve gerar as atividades e gravar ultima execução
                                if ($oficial == '1') {
                                    $sql_prog = str_replace("''", "NULL", "insert into is_atividade(id_tp_atividade,assunto,id_pessoa,id_pessoa_contato,id_usuario_resp,dt_inicio,hr_inicio,dt_prev_fim,hr_prev_fim,id_situacao,id_campanha,id_campanha_prog_ativ) values ($id_tp_atividade,'" . $a_campanha_prog_ativ["nome_campanha_prog_ativ"] . "','" . $a_lista_pessoa["id_pessoa"] . "','" . $a_lista_pessoa["id_pessoa_contato"] . "','" . $a_operador_escolhido["id_usuario"] . "','" . $a_distr[$i_distr]["data"] . "','08:00','" . $a_distr[$i_distr]["data"] . "','08:00',1,'" . $a_campanha["numreg"] . "','" . $a_campanha_prog_ativ["numreg"] . "')");
                                    query($sql_prog);
                                }
                                $i_distr++;
                                if ($i_distr > $tot_distr) {
                                    $i_distr = 1;
                                }
                            } else {
                                $sn_atividade_gerada = 'Não';
                                $bgcolor_td = "#F0F0F0";
                            }

                            echo '<tr bgcolor="' . $bgcolor_td . '"><td>' . $a_lista_pessoa["razao_social_nome"] . '</td><td>' . $a_lista_pessoa["nome_contato"] . '</td><td>' . $a_operador_padrao["nome_usuario"] . '</td><td>' . $recencia . '</td><td>' . number_format($a_lista_pessoa["valor"] * 1, 2, ",", ".") . '</td><td>' . $sn_atividade_gerada . '</td><td>' . $a_operador_escolhido["nome_usuario"] . '</td><td>' . $acao . '</td>';
                        }
                        if ($oficial == '1') {
                            query("update is_campanha_prog_ativ set id_usuario_resp = '" . $_SESSION["id_usuario"] . "', dt_ult_execucao='" . date("Y-m-d") . "', hr_ult_execucao = '" . date("H:i") . "' where numreg = $pnumreg");
                        }
                        ?>
                        </table>
                        <hr>
                            <center>
                                <input type="button" value="Imprimir" name="B4" class="botao_form" onclick="javascript:window.print();">
                                    <input type="button" value="Fechar" name="B4" class="botao_form" onclick="javascript:window.close();">
                                        </center>
                                        </div>
                                        </body>
                                        </html>

