
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: Carga de Dados CSV - Faixas de Preço x % Comissão</title>
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
    </head>
    <body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
        <center>
            <div id="principal_detalhes">
                <div id="topo_detalhes">
                    <div id="logo_empresa"></div>
                    <!--logo -->
                </div><!--topo -->
                <div id="conteudo_detalhes">
                    <span class="tit_detalhes">Carga de Dados CSV - Faixas de Preço x % Comissão</span><br><br>
                            <?
                            @session_start();
                            @header("Content-Type: text/html;  charset=ISO-8859-1", true);
                            @header("Pragma: no-cache");
                            @header("Cache-Control: no-store, no-cache, must-revalidate");
                            @header("Cache-Control: post-check=0, pre-check=0", false);

                            include "../../conecta.php";
                            include "../../funcoes.php";

                            /* ----------------------------------------------------------------------------
                              RECEBE E COPIA O ARQUIVO
                              ----------------------------------------------------------------------------- */

                            $temp = $_FILES['edtarq']["tmp_name"];
                            $nome_arquivo = $_FILES['edtarq']["name"];
                            $size = $_FILES['edtarq']["size"];
                            $type = $_FILES['edtarq']["type"];

                            if ($nome_arquivo) {
                                copy($temp, $caminho_arquivos . "carga_faixa_comiss.csv");
                            }


                            $conteiner = file($caminho_arquivos . 'carga_faixa_comiss.csv');
                            $file = count($conteiner);

                            $nome_tabela = 'is_meta_faixas_preco_comissao';

                            $id_usuario = $_SESSION["id_usuario"];

                            $registro = '';
                            $f = -1;
                            $contador = 0;
                            for ($i = 0; $i < $file; $i++) {
                                $a = explode(';', $conteiner[$i]);
                                $b = count($a);

                                if ($i == 0) {
                                    // se for a primeira linha deve montar os nomes dos campos
                                    $campos = 'id_tab_preco,id_produto,vl_unit_inicial,vl_unit_final,pct_comissao,sn_preco_default,id_usuario_cad,dt_log,hr_log,sn_importado';
                                } else {
                                    // senao deve montar os conteúdos
                                    $conteudos = "";
                                    // Tabela de Preço
                                    $a_tab_preco = farray(query("select numreg from is_tab_preco where id_tab_preco_erp = '" . $a[0] . "'"));
                                    $id_tab_preco = $a_tab_preco["numreg"] * 1;
                                    // Produto
                                    $a_produto = farray(query("select numreg from is_produto where id_produto_erp = '" . $a[1] . "'"));
                                    $id_produto = $a_produto["numreg"] * 1;
                                    // Valor Unitário Inicial
                                    $vl_unit_inicial = str_replace(",", ".", str_replace(".", "", $a[2])) * 1;
                                    // Valor Unitário Final
                                    $vl_unit_final = str_replace(",", ".", str_replace(".", "", $a[3])) * 1;
                                    // % de Comissão
                                    $pct_comissao = str_replace(",", ".", str_replace(".", "", $a[4])) * 1;
                                    // Preço Default ?
                                    if (trim($a[5]) == 'S') {
                                        $sn_preco_default = 1;
                                    } else {
                                        $sn_preco_default = 0;
                                    }

                                    $conteudos = "'" . $id_tab_preco . "',
                                                    '" . $id_produto . "',
                                                    '" . $vl_unit_inicial . "',
                                                    '" . $vl_unit_final . "',
                                                    '" . $pct_comissao . "',
                                                    '" . $sn_preco_default . "',
                                                    '" . $id_usuario . "',
                                                    '" . date("Y-m-d") . "',
                                                    '" . date("H:i") . "',
                                                    '1'";

                                    $msg_erro = "";
                                    if ($id_tab_preco == 0) {
                                        $msg_erro .= "Tabela de Preço incorreta. ";
                                    }
                                    if ($id_produto == 0) {
                                        $msg_erro .= "Tabela de Preço incorreta. ";
                                    }
                                    if ($vl_unit_inicial > $vl_unit_final) {
                                        $msg_erro .= "Valor Inicial maior que Final. ";
                                    }

                                    if ($msg_erro) {
                                        echo "LINHA " . ($i + 1) . " - ERRO : " . $msg_erro . "<br>";
                                    } else {
                                        query("delete from  " . $nome_tabela . " where id_tab_preco = '".$id_tab_preco."' and id_produto = '".$id_produto."' and vl_unit_inicial = '".$vl_unit_inicial."'");
                                        $sql = "insert into " . $nome_tabela . "(" . $campos . ") values (" . $conteudos . ")";
                                        $rq = query($sql);
                                        if ($rq != 1) {
                                            echo "LINHA " . ($i + 1) . " - ERRO SQL : " . $sql . "<br>";
                                        } else {
                                            $contador++;
                                        }
                                    }
                                }
                            }

                            /* ----------------------------------------------------------------------------
                             * ***********************         FIM              *****************************
                              ----------------------------------------------------------------------------- */
                            echo "<br>Total de Registros Importados : " . $contador . "<br>";
                            ?>
                            </div>
                            </div>
                            <br>
                                <input type="button" value="Fechar"  class="botao_form"  onclick="javascript:window.close();">
                                    </center>
                                    </body>
                                    </html>