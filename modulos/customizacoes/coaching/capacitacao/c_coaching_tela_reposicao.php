<?php
/*
 * c_coaching_tela_reposicao.php
 * Autor: Alex
 * 23/08/2011 09:50:23
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('../../../../conecta.php');
require('../../../../functions.php');

if(!$_POST){
    BloqueiaAcessoDireto();
}

$_POST['dt_de'] = ($_POST['dt_de'] != '')?$_POST['dt_de']:'01/'.date("m/Y");
$_POST['dt_ate'] = ($_POST['dt_ate'] != '')?$_POST['dt_ate']:cal_days_in_month(CAL_GREGORIAN,date("m"),date("Y")).'/'.date("m/Y");

$SqlAgendaCursoDetalhe = "SELECT * FROM c_coaching_inscricao_curso_detalhe WHERE numreg = '".$_POST['id_agenda_curso_detalhe']."'";
$QryAgendaCursoDetalhe = query($SqlAgendaCursoDetalhe);
$ArAgendaCursoDetalhe = farray($QryAgendaCursoDetalhe);

$SqlGradeCurso = "SELECT
                            t1.numreg,
                            t1.dt_curso,
                            t3.nome_modulo,
                            t4.nome_local_curso,
                            t5.nome_hotel,
                            (SELECT COUNT(DISTINCT t10.id_pessoa) FROM c_coaching_inscricao_curso_detalhe t10 WHERE t10.id_agenda = t2.numreg) AS qtde_inscritos,
                            (CAST(t2.qtde_max_inscricao AS SIGNED INT) - (SELECT qtde_inscritos)) AS vagas_restantes,
                            (SELECT MIN(t11.dt_curso) FROM c_coaching_agenda_curso_detalhe t11 WHERE t11.id_agenda_curso = t1.id_agenda_curso) AS dt_de,
                            (SELECT MAX(t12.dt_curso) FROM c_coaching_agenda_curso_detalhe t12 WHERE t12.id_agenda_curso = t1.id_agenda_curso) AS dt_ate
                            FROM
                                c_coaching_agenda_curso_detalhe t1
                            INNER JOIN
                                c_coaching_agenda_curso t2 ON t2.numreg = t1.id_agenda_curso
                            INNER JOIN
                                c_coaching_modulo t3 ON t2.id_modulo = t3.numreg
                            INNER JOIN
                                c_coaching_local_curso t4 ON t2.id_local_curso = t4.numreg
                            INNER JOIN
                                c_coaching_hotel t5 ON t2.id_hotel = t5.numreg
                            WHERE
                                t2.id_modulo = ".$ArAgendaCursoDetalhe['id_modulo']."
                            AND
                                t2.dt_limite_inscricao >= '".date("Y-m-d")."'";
if($_POST['id_local_curso'] != ''){
    $SqlGradeCurso .= " AND t2.id_local_curso = '".$_POST['id_local_curso']."'";
}
if($_POST['id_hotel'] != ''){
    $SqlGradeCurso .=" AND t2.id_hotel = '".$_POST['id_hotel']."'";
}
if($_POST['dt_de'] != ''){
    $SqlGradeCurso .= " HAVING dt_de >= '".dtbr2en($_POST['dt_de'])." 00:00:00'";
}
if($_POST['dt_ate'] != ''){
    $SqlGradeCurso .= (($_POST['dt_de'] != '')?' AND ':' HAVING ')." dt_ate <= '".dtbr2en($_POST['dt_ate'])." 00:00:00'";
}
$SqlGradeCurso .= " ORDER BY t1.dt_curso ASC";
echo '<strong>Local: </strong>', TabelaParaCombobox('c_coaching_local_curso', 'numreg', 'nome_local_curso', 'edtreposicao_id_local_curso', $_POST['id_local_curso']), '&nbsp;&nbsp;';
echo '<strong>Hotel: </strong>', TabelaParaCombobox('c_coaching_hotel', 'numreg', 'nome_hotel', 'edtreposicao_id_hotel', $_POST['id_hotel']), '&nbsp;&nbsp;';
?>
<strong>Per&iacute;odo:</strong> 
<input type="text" class="c_campo_data" name="edtreposicao_dt_de" id="edtreposicao_dt_de" value="<?php echo $_POST['dt_de']; ?>"/>
<input type="text" class="c_campo_data" name="edtreposicao_dt_ate" id="edtreposicao_dt_ate" value="<?php echo $_POST['dt_ate']; ?>"/>
<input type="button" id="btnfiltrar_grade" class="botao_jquery" value="Filtrar"/>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="c_tabela_itens">
    <tr class="c_titulo_tabela">
        <td width="15">&nbsp;</td>
        <td>Data</td>
        <td>M&oacute;dulo</td>
        <td>Local</td>
        <td>Hotel</td>
        <td>Vagas Restantes</td>
    </tr>
    <?php
    $QryGradeCurso = query($SqlGradeCurso);
    $i = 0;
    while($ArGradeCurso = farray($QryGradeCurso)){
        $bgcolor = ($i % 2 == 0)?'#EAEAEA':'#FFFFFF';
        $i++;
        ?>
        <tr bgcolor="<?php echo $bgcolor; ?>">
            <td align="center"><input type="radio" name="edtreposicao_radio" id="edtreposicao_radio" value="<?php echo $ArGradeCurso['numreg']; ?>" /></td>
            <td><?php echo dten2br($ArGradeCurso['dt_curso']); ?></td>
            <td><?php echo $ArGradeCurso['nome_modulo']; ?></td>
            <td><?php echo $ArGradeCurso['nome_local_curso']; ?></td>
            <td><?php echo $ArGradeCurso['nome_hotel']; ?></td>
            <td><?php echo $ArGradeCurso['vagas_restantes']; ?></td>
        </tr>
    <?php } ?>
</table>
<script type="text/javascript">
    $(document).ready(function(){
        $(".botao_jquery").button();

        $("#edtreposicao_dt_de,#edtreposicao_dt_ate").datepicker({
            showOn: "button",
            buttonImage: "images/agenda.gif",
            buttonImageOnly: true,
            changeMonth:true,
            changeYear:true
        });
        
        $("#btnfiltrar_grade").click(function(){
            var Dialog = $("#jquery-dialog-reposicao-agenda-curso");
            $.ajax({
                url: "modulos/customizacoes/coaching/capacitacao/c_coaching_tela_reposicao.php",
                global: false,
                type: "POST",
                data: ({
                    id_agenda_curso_detalhe: '<?php echo $_POST['id_agenda_curso_detalhe'];?>',
                    id_local_curso: $("#edtreposicao_id_local_curso").val(),
                    id_hotel: $("#edtreposicao_id_hotel").val(),
                    dt_de: $("#edtreposicao_dt_de").val(),
                    dt_ate: $("#edtreposicao_dt_ate").val()
                }),
                dataType: "html",
                async: true,
                beforeSend: function(){
                    Dialog.html(HTMLLoadingGeral);
                },
                error: function(){
                    alert('Erro com a requisição');
                    Dialog.html('');
                },
                success: function(responseText){
                    Dialog.html(responseText);
                }
            });
        });
    });
</script>