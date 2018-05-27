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

<table border="0" align="center" cellspacing="2" cellpadding="2" width="100%">
    <tr>
        <td colspan="4" align="left" valign="center" bgcolor="#0000FF">
            <?
            if ($_POST["edtformato"] != 'excel') {
                echo '<img src="../../images/logorel.jpg" border="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            ?>
            <strong><font color="#FFFFFF">Controle de Metas - Representantes</font></strong></td>
    </tr>
    <tr>
        <td colspan="4" align="center" bgcolor="#CDC9C9"><strong><font color="#000000">Período :<?php echo dten2br($dt_inicio) . ' até ' . dten2br($dt_fim); ?> / Representante : <?php echo $vend['nome_usuario']; ?></font></strong></td>
    </tr>
    <tr>
        <td align="center" colspan="1" rowspan="2" bgcolor="#CDC9C9"><strong>Grupos / Produtos</strong></td>
        <td align="center" colspan="3" bgcolor="#CDC9C9"><strong>Metas de Vendas em Quantidade</strong></td>
    </tr>
    <tr>
        <td align="center" bgcolor="#CDC9C9"><strong>Previsto</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>Realizado</strong></td>
        <td align="center" bgcolor="#CDC9C9"><strong>%Atingido</strong></td>
    </tr>

    <?
    $sql_meta = "
    SELECT
        t1.*,
        t2.nome_familia_comercial,
        t2.id_familia_erp,
        t3.id_produto_erp,
        t3.nome_produto
    FROM is_meta_avancada_repr t1
    LEFT JOIN is_familia_comercial t2 ON t1.id_familia_comercial = t2.numreg
    LEFT JOIN is_produto t3 ON t1.id_produto = t3.numreg
    WHERE dt_inicio = '" . $dt_inicio . "' AND dt_fim = '" . $dt_fim . "' and id_usuario = '" . $vendedor . "'
    ORDER BY t2.nome_familia_comercial, t3.nome_produto";

    //echo $sql_meta;

    $q_meta = query($sql_meta);

    $familia_atual = '@';

    while ($a_meta = farray($q_meta)) {

        if ($familia_atual != $a_meta["nome_familia_comercial"]) {
            // Se não for a primeira deve exibir o ultimo total por familia
            if ($familia_atual != '@') {
                sub_total_familia();
            }

            $familia_atual = $a_meta["nome_familia_comercial"];
            echo '<tr><td colspan="4" align="left" bgcolor="#ADD8E6"><strong>Grupo : ' . $a_meta["id_familia_erp"].' - '.$a_meta["nome_familia_comercial"] . '</strong></td></tr>';
        }

        $a_real = farray(query("SELECT sum(qt_faturada) as real_qtde, sum(vl_merc_sicm-(vl_tot_item-vl_merc_liq)) as real_valor FROM is_dm_notas WHERE dt_emis_nota BETWEEN '" . $dt_inicio . "' AND '" . $dt_fim . "' AND cod_repr = '" . $vend['id_representante'] . "' and it_codigo = '" . $a_meta["id_produto_erp"] . "'"));

        // Apurando Valores
        $meta_qtde = $a_meta['meta_qtde'];
        $real_qtde = $a_real['real_qtde'];
        $pct_atingido_qtde = ($real_qtde / $meta_qtde) * 100;

        // Acumulados Familia
        $acum_fam_meta_qtde += $meta_qtde;
        $acum_fam_real_qtde += $real_qtde;

        // Acumulados Total
        $acum_tot_meta_qtde += $meta_qtde;
        $acum_tot_real_qtde += $real_qtde;

        echo '<tr><td align="left" bgcolor="#FFFFFF">' . $a_meta["nome_produto"] . '</td>';
        echo '<td align="right" bgcolor="#FFFFFF">' . number_format($meta_qtde, 2, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#FFFFFF">' . number_format($real_qtde, 2, ",", ".") . '</td>';
        echo '<td align="right" bgcolor="#FFFFFF">' . number_format($pct_atingido_qtde, 1, ",", ".") . '</td>';
        echo '</tr>';
    }
    if ($familia_atual != '@') {
        sub_total_familia();
    }


    $acum_tot_pct_atingido_qtde = ($acum_tot_real_qtde / $acum_tot_meta_qtde) * 100;

    echo '<tr><td colspan="1" bgcolor="#0000FF"><strong><font color="#FFFFFF">TOTAL GERAL :</font></strong></td>';
    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF">' . number_format($acum_tot_meta_qtde, 2, ",", ".") . '</td>';
    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF">' . number_format($acum_tot_real_qtde, 2, ",", ".") . '</font></strong></td>';
    echo '<td align="right" bgcolor="#0000FF"><strong><font color="#FFFFFF">' . number_format($acum_tot_pct_atingido_qtde, 1, ",", ".") . '</font></strong></td>';
    echo '</tr>';
    ?>
</table>


<?

    function sub_total_familia() {

        global $acum_fam_meta_qtde, $acum_fam_real_qtde;

        $acum_fam_pct_atingido_qtde = ($acum_fam_real_qtde / $acum_fam_meta_qtde) * 100;

        echo '<tr><td colspan="1" bgcolor="#FFFFFF"><strong>TOTAL :</strong></td>';
        echo '<td align="right" bgcolor="#FFFFFF"><strong>' . number_format($acum_fam_meta_qtde, 2, ",", ".") . '</strong></td>';
        echo '<td align="right" bgcolor="#FFFFFF"><strong>' . number_format($acum_fam_real_qtde, 2, ",", ".") . '</strong></td>';
        echo '<td align="right" bgcolor="#FFFFFF"><strong>' . number_format($acum_fam_pct_atingido_qtde, 1, ",", ".") . '</strong></td>';
        echo '</tr>';
        echo '<tr><td colspan="4" bgcolor="#FFFFFF"> </td></tr>';
        $acum_fam_meta_qtde = 0;
        $acum_fam_real_qtde = 0;
    }

?>

