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

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: Reajustar Contratos</title>
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
                        $count_contr = 0;
                        $sql_contratos = query("select * from is_contrato where sn_ativo = 1 and dt_reajuste between '" . $dt_ini . "' and '".$dt_fim."' order by nr_contrato");

                        if ($simulado == "N") {
                            echo '<hr><b>PROCESSAMENTO OFICIAL - REAJUSTE DE CONTRATO</b><hr>';
                        } else {
                            echo '<hr><b>PROCESSAMENTO SIMULADO - REAJUSTE DE CONTRATO</b><hr>';
                        }


                        echo '<table border="0">';
                        echo '<tr bgcolor="#dae8f4"><td>Contrato</td><td>Cliente</td><td>Produto / Serviço</td><td>Índice</td><td>% Índice</td><td>Valor Atual</td><td>Valor Reajuste</td><td>Novo Valor</td><td>Próx.Reajuste</td></tr>';
                        $tot_vl_atual = 0;
                        $tot_vl_reajuste = 0;
                        $tot_vl_novo = 0;

                        while ($qry_contratos = farray($sql_contratos)) {

                            $qry_cli = farray(query("select * from is_pessoa where numreg = '" . $qry_contratos["id_pessoa"] . "'"));
                            $qry_prod = farray(query("select * from is_produto where numreg = '" . $qry_contratos["id_produto_rec"] . "'"));
                            $a_indice = farray(query("select * from is_indice where numreg = '" . $qry_contratos["id_indice"] . "'"));
                            $a_pct_indice = farray(query("select * from is_indice_pct where id_indice = '" . $qry_contratos["id_indice"] . "' and ano = '" . date("Y") . "' and mes = '" . date("m") . "'"));

                            $valor_novo = 0;
                            $valor_reajuste = 0;
                            $sql_contratos_obj = query("select * from is_contrato_obj where sn_ativo_rec = 1 and id_contrato = " . $qry_contratos["numreg"]);
                            while ($qry_contratos_obj = farray($sql_contratos_obj)) {
                                $vl_unitario_reajuste_obj = (($qry_contratos_obj["vl_unitario_rec"]) * ($a_pct_indice["pct_reajuste"]) / 100);
                                $vl_unitario_novo_obj = $vl_unitario_reajuste_obj + ($qry_contratos_obj["vl_unitario_rec"] * 1);
                                $valor_novo_obj = $vl_unitario_novo_obj * ($qry_contratos_obj["qtde_rec"] * 1);
                                $valor_novo += number_format($valor_novo_obj, 2, '.', '');
                                $valor_reajuste += number_format(($vl_unitario_reajuste_obj * ($qry_contratos_obj["qtde_rec"] * 1)), 2, '.', '');
                                if ($simulado == "N") {
                                    query("update is_contrato_obj set vl_unitario_rec = '" . number_format($vl_unitario_novo_obj, 2, '.', '') . "', valor_rec = '" . number_format($valor_novo_obj, 2, '.', '') . "' where numreg = " . $qry_contratos_obj["numreg"]);
                                }
                            }

                            $nova_data = soma_meses_data(12, $qry_contratos["dt_reajuste"]);

                            $tot_vl_atual += ( $qry_contratos["valor_rec"] * 1);
                            $tot_vl_reajuste += $valor_reajuste;
                            $tot_vl_novo += $valor_novo;
                            echo "<tr><td>" . $qry_contratos["nr_contrato"] . "</td><td>" . $qry_cli["razao_social_nome"] . "</td><td>" . $qry_prod["nome_produto"] . "</td><td>" . $a_indice["nome_indice"] . "</td><td>" . number_format($a_pct_indice["pct_reajuste"] * 1, 4, ',', '.') . "</td><td>" . number_format($qry_contratos["valor_rec"] * 1, 2, ',', '.') . "</td><td>" . number_format($valor_reajuste, 2, ',', '.') . "</td><td>" . number_format($valor_novo, 2, ',', '.') . "</td><td>" . DataGetBD($nova_data) . "</td></tr>";
                            if ($simulado == "N") {
                                query("update is_contrato set dt_reajuste = '" . $nova_data . "', valor_rec = '" . number_format($valor_novo, 2, '.', '') . "' where numreg = " . $qry_contratos["numreg"]);
                            }

                            $count_contr++;
                        }
                        echo "</table><br><hr>";
                        echo "Contratos Ativos Processados e Reajustados : " . $count_contr . "<br>";
                        echo "Total Valor Atual : " . number_format($tot_vl_atual * 1, 2, ',', '.') . "<br>";
                        echo "Total de Reajustes : " . number_format($tot_vl_reajuste * 1, 2, ',', '.') . "<br>";
                        echo "total Valor Novo : " . number_format($tot_vl_novo * 1, 2, ',', '.') . "<br>";
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