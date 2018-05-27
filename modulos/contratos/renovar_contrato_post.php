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
        <title>:: OASIS :: Contratos com Renovação Automática - Atualização de Vigência</title>
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
                        $sql_contratos = query("select * from is_contrato where sn_ativo = 1 and sn_renova_auto = 1 and dt_fim between '" . $dt_ini . "' and '".$dt_fim."' order by nr_contrato");

                        if ($simulado == "N") {
                            echo '<hr><b>PROCESSAMENTO OFICIAL - RENOVAÇÃO DE CONTRATO</b><hr>';
                        } else {
                            echo '<hr><b>PROCESSAMENTO SIMULADO - RENOVAÇÃO DE CONTRATO</b><hr>';
                        }


                        echo '<table border="0">';
                        echo '<tr bgcolor="#dae8f4"><td>Contrato</td><td>Cliente</td><td>Produto / Serviço</td><td>Vigência Atual</td><td>Nova Vigência</td></tr>';

                        while ($qry_contratos = farray($sql_contratos)) {

                            $qry_cli = farray(query("select * from is_pessoa where numreg = '" . $qry_contratos["id_pessoa"] . "'"));
                            $qry_prod = farray(query("select * from is_produto where numreg = '" . $qry_contratos["id_produto_rec"] . "'"));
                            $nova_data = soma_meses_data(12, $qry_contratos["dt_fim"]);
                            echo "<tr><td>" . $qry_contratos["nr_contrato"] . "</td><td>" . $qry_cli["razao_social_nome"] . "</td><td>" . $qry_prod["nome_produto"] . "</td><td>" . DataGetBD($qry_contratos["dt_fim"]) . "</td><td>" . DataGetBD($nova_data) . "</td></tr>";
                            if ($simulado == "N") {
                                query("update is_contrato set dt_fim = '" . $nova_data . "' where numreg = " . $qry_contratos["numreg"]);
                            }
                            $count_contr++;
                        }
                        echo "</table><br><hr>";
                        echo "Contratos Ativos Processados e Atualizados : " . $count_contr . "<br>";
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