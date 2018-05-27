<?
@session_start();
if ($_POST["edtformato"] == 'excel') {

    header("Content-type: application/x-msdownload");
    header("Content-type: application/ms-excel");
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=comissao_" . date("Ymdhis") . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
}

include "../../conecta.php";
include "../../funcoes.php";
include "../../functions.php";

$vendedor = $_POST["edtcod_rep"];
$dt_inicio = dtbr2en($_POST["edtdtini"]);
$dt_fim = dtbr2en($_POST["edtdtfim"]);

$vend = farray(query("SELECT numreg as id_usuario,nome_usuario,id_representante FROM is_usuario  where numreg = '" . $vendedor . "'"));

?>

<table border="0" align="center" cellspacing="2" cellpadding="2" width="100%">
    <tr>
        <td colspan="11" align="left" valign="center" bgcolor="#0000FF">
            <?
            if ($_POST["edtformato"] != 'excel') {
                echo '<img src="../../images/logorel.jpg" border="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            ?>
            <strong><font color="#FFFFFF">Relatório de Comissão por Representante e Período</font></strong></td>
    </tr>
    <tr>
        <td colspan="6" align="left" bgcolor="#CDC9C9"><strong><font color="#000000">Representante : <?php echo $vend['nome_usuario']; ?></font></strong></td>
        <td colspan="5" align="left" bgcolor="#CDC9C9"><strong><font color="#000000">Período de :<?php echo dten2br($dt_inicio) . ' até ' . dten2br($dt_fim); ?></font></strong></td>
    </tr>
    <tr>
        <td align="center" bgcolor="#CDC9C9"><strong>Data</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>Tipo de Operação</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>NF. Nº - Seq.</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>Cliente</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>Descrição Produto</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>Quant.</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>Valor faturado</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>Valor do IPI</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>Valor base para comissão</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>% da comissão</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>Valor da comissão</strong></td>
    </tr>

    <?
    $sql_meta = "
    SELECT
        *
    FROM is_dm_notas
    WHERE dt_emis_nota BETWEEN '" . $dt_inicio . "' AND '" . $dt_fim . "' and cod_repr = '" . $vend["id_representante"] . "'
    ORDER BY dt_emis_nota, nr_nota_fis, nr_seq_fat";

    //echo $sql_meta;

    $q_meta = query($sql_meta);

    while ($a_meta = farray($q_meta)) {

        // Apurando Valores
        $vl_ipi = $a_meta["vl_tot_item"]-$a_meta["vl_merc_sicm"];
        $vl_base = $a_meta["vl_tot_item"]-$vl_ipi;

        // Checando comissao
        $vl_unitario = $vl_base / $a_meta["qt_faturada"];
        $a_produto = farray(query("select numreg from is_produto where id_produto_erp = '".$a_meta["it_codigo"]."'"));
        $a_pct_comissao = farray(query("select pct_comissao from is_meta_faixas_preco_comissao where id_produto = '".$a_produto["numreg"]."' and vl_unit_inicial <= '".$vl_unitario."' and vl_unit_final >= '".$vl_unitario."'"));
        $pct_comissao = $a_pct_comissao["pct_comissao"]*1;
        $vl_comissao = $vl_base * $pct_comissao/100;

        // Totais
        $tot_qt_faturada += $a_meta["qt_faturada"]*1;
        $tot_vl_tot_item += $a_meta["vl_tot_item"]*1;
        $tot_vl_ipi += $vl_ipi;
        $tot_vl_base += $vl_base;
        $tot_vl_comissao += $vl_comissao;

        echo '<tr><td align="left" bgcolor="#FFFFFF">' . dten2br($a_meta["dt_emis_nota"]). '</td>';
        echo '<td align="left" bgcolor="#FFFFFF">' . $a_meta["nat_operacao"]. '</td>';
        echo '<td align="left" bgcolor="#FFFFFF">' . $a_meta["nr_nota_fis"]. ' - '.$a_meta["nr_seq_fat"]. '</td>';
        echo '<td align="left" bgcolor="#FFFFFF">' . $a_meta["nome_emitente"]. '</td>';
        echo '<td align="left" bgcolor="#FFFFFF">' . $a_meta["it_nome"]. '</td>';
        echo '<td align="right" bgcolor="#FFFFFF">' . number_format($a_meta["qt_faturada"]*1, 2, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#FFFFFF">' . number_format($a_meta["vl_tot_item"]*1, 2, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#FFFFFF">' . number_format($vl_ipi, 2, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#FFFFFF">' . number_format($vl_base, 2, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#FFFFFF">' . number_format($pct_comissao, 1, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#FFFFFF">' . number_format($vl_comissao, 2, ",", ".") . '</td>';
        echo '</tr>';
    }

    echo '<tr><td colspan="10" align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF">TOTAL </font></strong></td>';
    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF">R$ ' . number_format($tot_vl_comissao, 2, ",", ".") . '</font></strong></td>';
    echo '</tr>';

/*    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF"></font></td>';
    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF"></font></td>';
    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF"></font></td>';
    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF"></font></td>';
    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF">' . number_format($tot_qt_faturada, 2, ",", ".") . '</font></td>';
    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF">' . number_format($tot_vl_tot_item, 2, ",", ".") . '</font></td>';
    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF">' . number_format($tot_vl_ipi, 2, ",", ".") . '</font></td>';
    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF">' . number_format($tot_vl_base, 2, ",", ".") . '</font></td>';
    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF"></font></strong></td>';
*/
    ?>
</table>



