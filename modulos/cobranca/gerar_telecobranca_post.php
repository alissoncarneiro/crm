<?php
@header("Content-Type:text/html; charset=iso-8859-1;");
@session_start();
$id_usuario = $_SESSION["id_usuario"];

include "../../conecta.php";
include "../../funcoes.php";
include "../../functions.php";

$simulado = $_POST["edtsimulado"];
$dt_ini = DataSetBD($_POST["edtde"]);
$dt_fim = DataSetBD($_POST["edtate"]);

if (empty($simulado)) { $simulado = '0'; }
if (empty($dt_ini)) { $dt_ini = '2000-01-01'; }
if (empty($dt_fim)) { $dt_fim = date("Y-m-d"); }


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: Gerar / Atualizar Telecobrança</title>
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
                        if ($simulado == "N") {
                            echo '<hr><b>PROCESSAMENTO OFICIAL - TELECOBRANÇA</b><hr>';
                        } else {
                            echo '<hr><b>PROCESSAMENTO SIMULADO - TELECOBRANÇA</b><hr>';
                        }


                        echo '<table border="0">';
                        echo '<tr bgcolor="#dae8f4"><td>Título</td><td>Parcela</td><td>Cliente</td><td>Vencimento</td><td align="right">Valor Título</td><td align="right">Saldo</td></tr>';
                        // Buscando titulos que não estão em atraso mas com telecobrança em aberto
                        $count = 0;
                        $count_realizado = 0;
                        $tot_realizado = 0;
                        $tot_aberto = 0;
                        $sql = query("select * from is_atividade where id_tp_atividade = 8 and id_situacao = 1 order by cobr_dt_vencimento");
                        while ($qry = farray($sql)) {
                            $qry_titulo = farray(query("select * from is_titulo where id_pessoa =  '" . $qry["id_pessoa"] . "' and id_titulo_erp =  '" . $qry["cobr_id_titulo_erp"] . "'"));
                            $qry_cli = farray(query("select * from is_pessoa where numreg = '" . $qry["id_pessoa"] . "'"));
                            $qry_sit = farray(query("select * from is_tp_situacao_titulo where numreg = '" . $qry_titulo["id_tp_situacao_titulo"] . "'"));
                            if ($qry_titulo["id_tp_situacao_titulo"] != '4') {
                                echo '<tr bgcolor="#E8E8E8"><td>' . $qry_titulo["id_titulo_erp"] . " (".$qry_sit["nome_tp_situacao_titulo"].")</td><td>" . $qry_titulo["n_parcela"] . "</td><td>" . $qry_cli["razao_social_nome"] . "</td><td>" . DataGetBD($qry_titulo["dt_vencimento"]) . '</td><td align="right">' . number_format($qry_titulo["vl_titulo"] * 1, 2, ',', '.') . '</td><td align="right">' . number_format($qry_titulo["vl_saldo"] * 1, 2, ',', '.') . "</td></tr>";
                                if ($simulado == "N") {
                                    query(str_replace("'9999-12-31'", "NULL", str_replace("''", "NULL",
                                           "update is_atividade set 
                                           cobr_dt_vencimento='" . $qry_titulo["dt_vencimento"] . "',
                                           cobr_dt_pagamento='" . $qry_titulo["dt_pagamento"] . "',
                                           cobr_dt_ult_pagamento='" . $qry_titulo["dt_ult_pagamento"] . "',
                                           cobr_vl_titulo='" . $qry_titulo["vl_titulo"] . "',
                                           cobr_vl_saldo='" . $qry_titulo["vl_saldo"] . "',
                                           id_situacao = '4',
                                           assunto = 'Telecobrança realizada pelo sistema'
                                           where numreg = " . $qry["numreg"])));
                                }
                                $tot_realizado += ($qry_titulo["vl_saldo"]*1);
                                $count_realizado++;
                            }
                        }

                        // Buscando titulos atrasados para gerar / atualizar telecobranças
                        $sql = query("select * from is_titulo where id_tp_situacao_titulo = 4 and dt_vencimento between '" . $dt_ini . "' and '" . $dt_fim . "' order by dt_vencimento");

                        while ($qry = farray($sql)) {

                            $qry_cli = farray(query("select * from is_pessoa where numreg = '" . $qry["id_pessoa"] . "'"));
                            $qry_existe = farray(query("select numreg from is_atividade where id_pessoa =  '" . $qry["id_pessoa"] . "' and cobr_id_titulo_erp =  '" . $qry["id_titulo_erp"] . "'"));
                            echo "<tr><td>" . $qry["id_titulo_erp"] . "</td><td>" . $qry["n_parcela"] . "</td><td>" . $qry_cli["razao_social_nome"] . "</td><td>" . DataGetBD($qry["dt_vencimento"]) . '</td><td align="right">' . number_format($qry["vl_titulo"] * 1, 2, ',', '.') . '</td><td align="right">' . number_format($qry["vl_saldo"] * 1, 2, ',', '.') . "</td></tr>";
                            if ($simulado == "N") {
                                if ($qry_existe["numreg"] * 1 == 0) {
                                    query(str_replace("'9999-12-31'", "NULL", str_replace("''", "NULL",
                                           "insert into is_atividade (
                                           id_tp_atividade,
                                           id_pessoa,
                                           id_usuario_resp,
                                           assunto,
                                           cobr_id_titulo_erp,
                                           cobr_n_parcela,
                                           cobr_dt_emissao,
                                           cobr_dt_vencimento,
                                           cobr_dt_vencimento_original,
                                           cobr_dt_pagamento,
                                           cobr_dt_ult_pagamento,
                                           cobr_vl_titulo,
                                           cobr_vl_saldo,
                                           dt_inicio,
                                           hr_inicio,
                                           dt_prev_fim,
                                           hr_prev_fim,
                                           id_situacao) values (
                                           '8'," .
                                            "'" . $qry["id_pessoa"] . "'," .
                                            "'" . $_SESSION["id_usuario"] . "'," .
                                            "'Telecobraça'," .
                                            "'" . $qry["id_titulo_erp"] . "'," .
                                            "'" . $qry["n_parcela"] . "'," .
                                            "'" . $qry["dt_emissao"] . "'," .
                                            "'" . $qry["dt_vencimento"] . "'," .
                                            "'" . $qry["dt_vencimento_original"] . "'," .
                                            "'" . $qry["dt_pagamento"] . "'," .
                                            "'" . $qry["dt_ult_pagamento"] . "'," .
                                            "'" . $qry["vl_titulo"] . "'," .
                                            "'" . $qry["vl_saldo"] . "'," .
                                            "'" . date("Y-m-d") . "'," .
                                            "'" . date("H:i") . "'," .
                                            "'" . soma_dias_ut(date("Y-m-d"), 1, '1') . "'," .
                                            "'09:00'," .
                                            "'1'" .
                                            " )")));
                                } else {
                                    query(str_replace("'9999-12-31'", "NULL", str_replace("''", "NULL",
                                            "update is_atividade set 
                                           cobr_dt_vencimento='" . $qry["dt_vencimento"] . "',
                                           cobr_dt_pagamento='" . $qry["dt_pagamento"] . "',
                                           cobr_dt_ult_pagamento='" . $qry["dt_ult_pagamento"] . "',
                                           cobr_vl_titulo='" . $qry["vl_titulo"] . "',
                                           cobr_vl_saldo='" . $qry["vl_saldo"] . "'
                                           where numreg = " . $qry_existe["numreg"])));
                                }
                            }
                            $tot_aberto += ($qry["vl_saldo"]*1);
                            $count++;
                        }
                        echo "</table><br><hr>";
                        echo "Títulos Realizados : " . $count_realizado . " (".number_format($tot_realizado * 1, 2, ',', '.').")<br>";
                        echo "Títulos Atrasados : " . $count . " (".number_format($tot_aberto * 1, 2, ',', '.').")<br>";
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