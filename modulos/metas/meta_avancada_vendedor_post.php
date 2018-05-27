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
            <strong><font color="#FFFFFF">Controle de Metas e Resultados Individuais da �rea Comercial Interna</font></strong></td>
    </tr>
    <tr>
        <td colspan="10" align="center" bgcolor="#FF8C00"><strong><font color="#FFFFFF">Per�odo :<?php echo dten2br($dt_inicio) . ' at� ' . dten2br($dt_fim); ?> / Vendedor : <?php echo $vend['nome_usuario']; ?></font></strong></td>
    </tr>
    <tr>
        <td align="center" rowspan="2" bgcolor="#CDC9C9"><strong>Produtos</strong></td>
        <td align="center" colspan="3" bgcolor="#CDC9C9"><strong>Meta Quantidade</strong></td>
        <td align="center" colspan="2" bgcolor="#CDC9C9"><strong>Faturamento NET</strong></td>
        <td align="center" colspan="3" bgcolor="#CDC9C9"><strong>Meta Pre�o NET de Venda</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>Bonifica��o</strong></td>
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
        t3.id_produto_erp,
        t3.nome_produto
    FROM is_meta_avancada_vend t1
    LEFT JOIN is_familia_comercial t2 ON t1.id_familia_comercial = t2.numreg
    LEFT JOIN is_produto t3 ON t1.id_produto = t3.numreg
    WHERE dt_inicio = '" . $dt_inicio . "' AND dt_fim = '" . $dt_fim . "' and id_usuario = '" . $vendedor . "'
    ORDER BY t2.nome_familia_comercial, t3.nome_produto";

    //echo $sql_meta;

    $q_meta = query($sql_meta);

    $familia_atual = '@';

    while ($a_meta = farray($q_meta)) {

        if ($familia_atual != $a_meta["nome_familia_comercial"]) {
            // Se n�o for a primeira deve exibir o ultimo total por familia
            if ($familia_atual != '@') {
                sub_total_familia();
            }

            $familia_atual = $a_meta["nome_familia_comercial"];
            echo '<tr><td colspan="10" align="left" bgcolor="#CDC9C9"><strong>' . $a_meta["nome_familia_comercial"] . '</strong></td></tr>';
        }

        $a_real = farray(query("SELECT sum(qt_faturada) as real_qtde, sum(vl_merc_sicm-(vl_tot_item-vl_merc_liq)) as real_valor FROM is_dm_notas WHERE dt_emis_nota BETWEEN '" . $dt_inicio . "' AND '" . $dt_fim . "' AND cod_repr = '" . $vend['id_representante'] . "' and it_codigo = '" . $a_meta["id_produto_erp"] . "'"));

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

        // Acumulados Familia
        $acum_fam_meta_qtde += $meta_qtde;
        $acum_fam_meta_vl_unitario += $meta_vl_unitario;
        $acum_fam_real_qtde += $real_qtde;
        $acum_fam_real_valor += $real_valor;
        $acum_fam_previsto_valor += $previsto_valor;

        // Acumulados Total
        $acum_tot_meta_qtde += $meta_qtde;
        $acum_tot_meta_vl_unitario += $meta_vl_unitario;
        $acum_tot_real_qtde += $real_qtde;
        $acum_tot_real_valor += $real_valor;
        $acum_tot_previsto_valor += $previsto_valor;

        echo '<tr><td align="left" bgcolor="#EEE9E9">' . $a_meta["nome_produto"] . '</td>';
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
    if ($familia_atual != '@') {
        sub_total_familia();
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


<?

    function sub_total_familia() {

        global $acum_fam_meta_qtde, $acum_fam_meta_qtde, $acum_fam_real_qtde, $acum_fam_real_valor, $acum_fam_previsto_valor, $peso_meta_qtde, $peso_meta_valor;

        $acum_fam_meta_vl_unitario = $acum_fam_previsto_valor / $acum_fam_meta_qtde;
        $acum_fam_real_vl_unitario = $acum_fam_real_valor / $acum_fam_real_qtde;
        $acum_fam_pct_atingido_qtde = ($acum_fam_real_qtde / $acum_fam_meta_qtde) * 100;
        $acum_fam_pct_atingido_vl_unitario = ($acum_fam_real_vl_unitario / $acum_fam_meta_vl_unitario) * 100;
        $acum_fam_pct_atingido_bonificacao = ($acum_fam_pct_atingido_qtde * $peso_meta_qtde) + ($acum_fam_pct_atingido_vl_unitario * $peso_meta_valor);

        echo '<tr><td colspan="1" bgcolor="#EEE9E9"><strong>TOTAL :</strong></td>';
        echo '<td align="right" bgcolor="#EEE9E9"><strong>' . number_format($acum_fam_meta_qtde, 2, ",", ".") . '</strong></td>';
        echo '<td align="right" bgcolor="#EEE9E9"><strong>' . number_format($acum_fam_real_qtde, 2, ",", ".") . '</strong></td>';
        echo '<td align="right" bgcolor="#EEE9E9"><strong>' . number_format($acum_fam_pct_atingido_qtde, 1, ",", ".") . '</strong></td>';
        echo '<td align="right" bgcolor="#EEE9E9"><strong>' . number_format($acum_fam_previsto_valor, 2, ",", ".") . '</strong></td>';
        echo '<td align="right" bgcolor="#EEE9E9"><strong>' . number_format($acum_fam_real_valor, 2, ",", ".") . '</strong></td>';
        echo '<td align="right" bgcolor="#EEE9E9"><strong>' . number_format($acum_fam_meta_vl_unitario, 2, ",", ".") . '</strong></td>';
        echo '<td align="right" bgcolor="#EEE9E9"><strong>' . number_format($acum_fam_real_vl_unitario, 2, ",", ".") . '</strong></td>';
        echo '<td align="right" bgcolor="#EEE9E9"><strong>' . number_format($acum_fam_pct_atingido_vl_unitario, 1, ",", ".") . '</strong></td>';
        echo '<td align="right" bgcolor="#EEE9E9"><strong>' . number_format($acum_fam_pct_atingido_bonificacao, 1, ",", ".") . '</strong></td>';
        echo '</tr>';
        echo '<tr><td colspan="10" bgcolor="#FFFFFF"> </td></tr>';
        $acum_fam_meta_qtde = 0;
        $acum_fam_meta_qtde = 0;
        $acum_fam_real_qtde = 0;
        $acum_fam_real_valor = 0;
        $acum_fam_previsto_valor = 0;
    }

?>

