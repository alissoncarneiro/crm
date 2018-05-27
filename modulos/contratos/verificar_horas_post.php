<?php
@header("Content-Type:text/html; charset=iso-8859-1;");
@session_start();
$simulado = $_POST["edtsimulado"];
$mesano = $_POST["edtmesano"];
$contrato = $_POST["edtcontrato"];
$contrato_fim = $_POST["edtcontrato_fim"];
$tipo = $_POST["edttipo"];
$id_usuario = $_SESSION["id_usuario"];

include "../../conecta.php";
include "../../funcoes.php";
include "../../functions.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: Gerar Ordem de Faturamento</title>
        <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../../estilos_css/cadastro.css" />
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

                    <?php
                    $valor_total = 0;
                    $count_ordem = 0;
                    $count_contr = 0;
                    $sql_filtro_contrato = "";
                    if($contrato){
                        $sql_filtro_contrato = " and a.nr_contrato >= '".$contrato."' and a.nr_contrato <= '".$contrato_fim."'";
                    }
                    if($tipo){
                        $sql_filtro_contrato = " and a.id_tp_contrato = '".$tipo."'";
                    }
                    $sql = "select a.*,b.*,a.numreg as numreg_contrato,sum(b.horas_utilizadas) as soma, ((sum(b.horas_utilizadas)-a.horas_contratadas)*a.vl_hr_adicional) as diferenca from is_contrato as a
                    INNER JOIN is_atividade as b on a.numreg = b.atend_id_contrato
                    WHERE a.sn_ativo = 1 ".$sql_filtro_contrato . " and (a.dt_inicio <= '" . date("Y-m-d") . "' and a.dt_fim >= '" . date("Y-m-d") . "')
                    AND (a.horas_contratadas is not NULL OR a.horas_contratadas >0)
                    group by a.numreg
                    having a.horas_contratadas< soma
                    ORDER BY a.nr_contrato";
                    //echo $sql;
                    //exit;
                    $sql_contratos = query($sql);

                    if($simulado == "N"){
                        echo '<hr><b>PROCESSAMENTO OFICIAL - GERAR ORDEM DE FATURAMENTO para HORAS EXCEDENTES</b><hr>';
                    }else{
                        echo '<hr><b>PROCESSAMENTO SIMULADO - GERAR ORDEM DE FATURAMENTO para HORAS EXCEDENTES</b><hr>';
                    }

                    echo '<table border="0">';
                    echo '<tr bgcolor="#dae8f4"><td>Ordem de Faturamento</td><td>Cliente</td><td>Produto</td><td>Valor</td></tr>';

                    while($qry_contratos = farray($sql_contratos)){
                        //echo "select numreg from is_ordem_faturamento where nr_pedido_exporta = '".substr($qry_contratos["nr_contrato"],0,1).substr($qry_contratos["nr_contrato"],3,4).substr($qry_contratos["nr_contrato"],9,2).substr($mesano,0,2).substr($mesano,5,2)."H'";
                        $qry_cli = farray(query("select * from is_pessoa where numreg = '".$qry_contratos["id_pessoa"]."'"));
                        $qry_prod = farray(query("select * from is_produto where numreg = '".$qry_contratos["id_produto_rec"]."'"));
                        $qry_ordem_fat = farray(query("select numreg from is_ordem_faturamento where nr_pedido_exporta = '".substr($qry_contratos["nr_contrato"],0,1).substr($qry_contratos["nr_contrato"],3,4).substr($qry_contratos["nr_contrato"],9,2).substr($mesano,0,2).substr($mesano,5,2)."H'"));

                        if($qry_ordem_fat["numreg"] * 1 == 0){
                            $valor = $qry_contratos["diferenca"];
                            $sn_data_permitida = 0;
                            if(($qry_contratos["sn_primeira_rec"] == '1') && ($qry_contratos["dt_primeira_rec"])){
                                $dt_vencimento = $qry_contratos["dt_primeira_rec"];
                                $sn_data_permitida = 1;
                            }else{
                                $a_periodo = farray(query("select qtde_dias_periodo from is_periodo where numreg = ".$qry_contratos["id_periodo_rec"]));
                                $qtde_dias = $a_periodo["qtde_dias_periodo"] * 1;
                                $dt_ultima_rec = $qry_contratos["dt_ultima_rec"];
                                $dt_comparacao = soma_dias_data($qtde_dias,$dt_ultima_rec);
                                $dt_vencimento = date("Y-m-d",mktime(0,0,0,substr($mesano,0,2) + 1,$qry_contratos["dia_bom_rec"],substr($mesano,3,4)));
                                // Deve checar se o mes ano está dentro da periodicidade - Pois existem contratos bimestrais, anuais, etc
                                if(substr($dt_comparacao,0,7) <= substr($dt_vencimento,0,7)){
                                    $sn_data_permitida = 1;
                                }
                            }

                            if($sn_data_permitida == 1){
                                // SOLICITADO PARA GERAR NUMERO DO PEDIDO DO CLIENTE COM LCCCCFFMMAA (L=TIPO CONTRATO, CCCC= NUM CONTRATO, FF = FILIAL, MMAA = MES ANO REFERENCIA
                                $nr_pedido_exporta = ''.substr($qry_contratos["nr_contrato"],0,1).substr($qry_contratos["nr_contrato"],3,4).substr($qry_contratos["nr_contrato"],9,2).substr($mesano,0,2).substr($mesano,5,2).'H';
                                $obs_item = 'CONTRATO NO. '.$qry_contratos["nr_contrato"].' REF. AO MÊS DE '.$mesano;

                                if($simulado == "N"){
                                    $sql_insert = "insert into is_ordem_faturamento(
                                                    id_contrato,
                                                    mesano,
                                                    id_tp_contrato,
                                                    id_pessoa,
                                                    id_estabelecimento,
                                                    id_produto,
                                                    dt_vencimento,
                                                    valor,
                                                    obs_item,
                                                    id_cfop,
                                                    nr_pedido_exporta,
                                                    sn_exportado_erp,
                                                    id_usuario,
                                                    dt_ordem,
                                                    hr_ordem
                                                    ) values (".
                                            "'".$qry_contratos["numreg_contrato"]."',".
                                            "'".$mesano."',".
                                            "'".$qry_contratos["id_tp_contrato"]."',".
                                            "'".$qry_contratos["id_pessoa"]."',".
                                            "'".$qry_contratos["id_estabelecimento"]."',".
                                            "'".$qry_contratos["id_produto_rec"]."',".
                                            "'".$dt_vencimento."',".
                                            "'".$qry_contratos["diferenca"]."',".
                                            "'".$obs_item."',".
                                            "'".$qry_contratos["id_cfop"]."',".
                                            "'".$nr_pedido_exporta."',".
                                            "0,".
                                            "'".$_SESSION["id_usuario"]."',".
                                            "'".date("Y-m-d")."',".
                                            "'".date("H:i")."'".
                                            ")";
                                    //echo $sql_insert;
                                    $rq = query($sql_insert);
                                    if($rq == "1"){
                                        query("update is_contrato set sn_primeira_rec = 0, dt_ultima_rec = '".$dt_vencimento."' where numreg = ".$qry_contratos["numreg_contrato"]);
                                    }
                                }
                                $valor_total = $valor_total + $valor;
                                $count_ordem++;
                                echo "<tr><td>".$nr_pedido_exporta."</td><td>".$qry_cli["razao_social_nome"]."</td><td>".$qry_prod["nome_produto"]."</td><td>".number_format($valor,2,',','.')."</td></tr>";

                                $count_contr++;
                            }
                        }
                    }
                    echo "</table><br><hr>";
                    echo "Contratos Ativos Processados : ".$count_contr."<br>";
                    echo "Ordens de Faturamento Geradas : ".$count_ordem."<br>";
                    echo "Valot Total das Ordens de Faturamento Geradas : ".number_format($valor_total,2,',','.')."<br>";
                    ?>
                    <hr/>
                    <center>
                        <input type="button" value="Imprimir" name="B4" class="botao_form" onclick="javascript:window.print();"/>
                        <input type="button" value="Voltar" name="B4" class="botao_form" onclick="javascript:history.back(1);"/>
                        <input type="button" value="Fechar" name="B4" class="botao_form" onclick="javascript:window.close();"/>
                    </center>
                </div>
        </body>
</html>