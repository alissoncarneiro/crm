<?
@session_start();

if ($_POST["edtformato"] == 'excel') {

    header("Content-type: application/x-msdownload");
    header("Content-type: application/ms-excel");
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=metas_" . date("Ymdhis") . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
}

$vendedor = $_POST["edtcod_rep"];
$periodo = $_POST["edtperiodo"];
$dt_inicio = substr($periodo, 0, 10);
$dt_fim = substr($periodo, 10, 10);

include "../../conecta.php";
include "../../funcoes.php";
include "../../functions.php";

$vend = farray(query("SELECT numreg as id_usuario,nome_usuario,id_representante FROM is_usuario  where numreg = '" . $vendedor . "'"));
?>

<table border="0" align="center" cellspacing="2" cellpadding="2">
    <tr>
        <td colspan="10" align="left" valign="center" bgcolor="#000080">
            <?
            if ($_POST["edtformato"] != 'excel') {
                echo '<img src="../../images/logorel.jpg" border="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            ?>
            <strong><font color="#FFFFFF">Controle de Metas e Resultados Individuais dos Coordenadores</font></strong></td>
    </tr>
    <tr>
        <td colspan="10" align="center" bgcolor="#FF8C00"><strong><font color="#FFFFFF">Período :<?php echo dten2br($dt_inicio) . ' até ' . dten2br($dt_fim); ?> / Coordenador(a) : <?php echo $vend['nome_usuario']; ?></font></strong></td>
    </tr>
    <tr>
        <td align="center" rowspan="2" bgcolor="#CDC9C9"><strong>Família de Produto</strong></td>
        <td align="center" colspan="3" bgcolor="#CDC9C9"><strong>Meta Quantidade</strong></td>
        <td align="center" colspan="2" bgcolor="#CDC9C9"><strong>Faturamento NET</strong></td>
        <td align="center" colspan="3" bgcolor="#CDC9C9"><strong>Meta Preço NET de Venda</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>Bonificação</strong></td>
    </tr>
    <tr>
        <td align="center" bgcolor="#FFFF00"><strong>Previsto</strong></td>
        <td align="center" bgcolor="#FFFF00"><strong>Realizado</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>%Atingido</strong></td>
        <td align="center" bgcolor="#FFFF00"><strong>Previsto</strong></td>
        <td align="center" bgcolor="#FFFF00"><strong>Realizado</strong></td>
        <td align="center" bgcolor="#FFFF00"><strong>Previsto</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>Realizado</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>%Atingido</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>%Atingida</strong></td>
    </tr>

    <?
    $sql_meta = "
    SELECT
        t1.*,
        t2.nome_familia_comercial,
        t2.id_familia_erp
    FROM is_meta_avancada_gerenc t1
    LEFT JOIN is_familia_comercial t2 ON t1.id_familia_comercial = t2.numreg
    WHERE dt_inicio = '" . $dt_inicio . "' AND dt_fim = '" . $dt_fim . "' and id_usuario = '" . $vendedor . "'
    ORDER BY t2.nome_familia_comercial";

//    echo $sql_meta;

    $q_meta = query($sql_meta);

    $familia_atual = '@';

    while ($a_meta = farray($q_meta)) {

        $a_real = farray(query("SELECT sum(t1.qt_faturada) as real_qtde, sum(t1.vl_merc_sicm-(t1.vl_tot_item-t1.vl_merc_liq)) as real_valor FROM is_dm_notas t1 WHERE t1.dt_emis_nota BETWEEN '" . $dt_inicio . "' AND '" . $dt_fim . "' AND t1.nome_familia_com  = '" . ($a_meta["nome_familia_comercial"]) . "'"));
        // Apurando Valores
        $meta_qtde = $a_meta['meta_qtde'];
        $meta_vl_unitario = $a_meta['meta_vl_unitario'];
        $real_qtde = $a_real['real_qtde'];
        $real_valor = $a_real['real_valor'];
        $peso_meta_qtde = $a_meta['peso_meta_qtde'] / 100;
        $peso_meta_valor = $a_meta['peso_meta_valor'] / 100;

        $pct_atingido_qtde = ($real_qtde / $meta_qtde) * 100;
        $previsto_valor = ($meta_qtde * $meta_vl_unitario);
        $real_vl_unitario = ($real_valor / $real_qtde);
        $pct_atingido_vl_unitario = ($real_vl_unitario / $meta_vl_unitario) * 100;
        $pct_atingido_bonificacao = ($pct_atingido_qtde * $peso_meta_qtde) + ($pct_atingido_vl_unitario * $peso_meta_valor);


        // Acumulados Total
        $acum_tot_meta_qtde += $meta_qtde;
        $acum_tot_meta_vl_unitario += $meta_vl_unitario;
        $acum_tot_real_qtde += $real_qtde;
        $acum_tot_real_valor += $real_valor;
        $acum_tot_previsto_valor += $previsto_valor;

        echo '<tr><td align="left" bgcolor="#EEE9E9">' . $a_meta["nome_familia_comercial"] . '</td>';
        echo '<td align="right" bgcolor="#EEE9E9">' . number_format($meta_qtde, 2, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#EEE9E9">' . number_format($real_qtde, 2, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#EEE9E9">' . number_format($pct_atingido_qtde, 1, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#EEE9E9">' . number_format($previsto_valor, 2, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#EEE9E9">' . number_format($real_valor, 2, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#EEE9E9">' . number_format($meta_vl_unitario, 2, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#EEE9E9">' . number_format($real_vl_unitario, 2, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#EEE9E9">' . number_format($pct_atingido_vl_unitario, 1, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#EEE9E9">' . number_format($pct_atingido_bonificacao, 1, ",", ".") . '</td>';
        echo '</tr>';
    }


    $acum_tot_meta_vl_unitario = $acum_tot_previsto_valor / $acum_tot_meta_qtde;
    $acum_tot_real_vl_unitario = $acum_tot_real_valor / $acum_tot_real_qtde;
    $acum_tot_pct_atingido_qtde = ($acum_tot_real_qtde / $acum_tot_meta_qtde) * 100;
    $acum_tot_pct_atingido_vl_unitario = ($acum_tot_real_vl_unitario / $acum_tot_meta_vl_unitario) * 100;
    $acum_tot_pct_atingido_bonificacao = ($acum_tot_pct_atingido_qtde * $peso_meta_qtde) + ($acum_tot_pct_atingido_vl_unitario * $peso_meta_valor);

    echo '<tr><td colspan="1" bgcolor="#000080"><strong><font color="#FFFFFF">TOTAL GERAL :</font></strong></td>';
    echo '<td align="right" bgcolor="#000080"><strong><font color="#FFFFFF">' . number_format($acum_tot_meta_qtde, 2, ",", ".") . '</td>';
    echo '<td align="right" bgcolor="#000080"><strong><font color="#FFFFFF">' . number_format($acum_tot_real_qtde, 2, ",", ".") . '</font></strong></td>';
    echo '<td align="right" bgcolor="#000080"><strong><font color="#FFFFFF">' . number_format($acum_tot_pct_atingido_qtde, 1, ",", ".") . '</font></strong></td>';
    echo '<td align="right" bgcolor="#000080"><strong><font color="#FFFFFF">' . number_format($acum_tot_previsto_valor, 2, ",", ".") . '</font></strong></td>';
    echo '<td align="right" bgcolor="#000080"><strong><font color="#FFFFFF">' . number_format($acum_tot_real_valor, 2, ",", ".") . '</font></strong></td>';
    echo '<td align="right" bgcolor="#000080"><strong><font color="#FFFFFF">' . number_format($acum_tot_meta_vl_unitario, 2, ",", ".") . '</font></strong></td>';
    echo '<td align="right" bgcolor="#000080"><strong><font color="#FFFFFF">' . number_format($acum_tot_real_vl_unitario, 2, ",", ".") . '</font></strong></td>';
    echo '<td align="right" bgcolor="#000080"><strong><font color="#FFFFFF">' . number_format($acum_tot_pct_atingido_vl_unitario, 1, ",", ".") . '</font></strong></td>';
    echo '<td align="right" bgcolor="#000080"><strong><font color="#FFFFFF">' . number_format($acum_tot_pct_atingido_bonificacao, 1, ",", ".") . '</font></strong></td>';
    echo '</tr>';
    ?>
</table>


