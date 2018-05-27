<?php
/*
 * log_integracao_txt.php
 * Autor: Alex
 * 29/09/2011 11:32:38
 */
session_start();
header("Content-Type: text/html; charset=ISO-8859-1");
if($_SESSION['id_usuario'] == ''){
    echo '<script type="text/javascript"> alert(\'Usuário não está logado.\'); window.location.href = window.location.href; </script>';
    exit;
}
include('../../conecta.php');
include('../../functions.php');

$DataBase = date("Y-m-d");
if($_GET['dt_base'] != ''){
    $DataBase = dtbr2en($_GET['dt_base']);
}
?>
<style type="text/css">
.fs_custom legend{
    font-weight:bold;
    font-size:14px;
}
.fs_custom table{
    border: 1px solid #ACC6DB;
}
.fs_custom table th{
    font-weight: bold;
    color: #345c7d;
    text-align: left;
    padding-left: 5px;
    background-color: #DAE8F4;
}
.campo_data{
    width:65px;
    text-align: center;
}
</style>
<fieldset class="fs_custom"><legend>Log Integração Por Data</legend>
    Data Base: <input type="text" class="campo_data" id="edtdt_base" value="<?php echo dten2br($DataBase);?>" />
    <input type="button" class="botao_jquery" id="btn_recarregar_tela" value="Recarregar" />
    <table width="100%" border="0" cellspacing="2" cellpadding="0">
        <tr>
            <th>&nbsp;</th>
            <th>Status</th>
            <th>Data Inicio</th>
            <th>Hora Inicio</th>
            <th>Data Fim</th>
            <th>Hora Fim</th>
            <th>Carga</th>
            <th>Usuário</th>
            <th>Tempo (Segundos)</th>
            <th>Qtde Registros Incluídos</th>
            <th>Qtde Registros Atualizados</th>
            <th>Qtde Registros Com Erro</th>
            <th>Qtde Registros Ignorados</th>
            <th>Qtde Registros Processados</th>
        </tr>
        <?php
        $SqlLog = "SELECT * FROM is_log_integracao_txt_erp_datasul WHERE dt_inicio = '".$DataBase."' ORDER BY dt_inicio, hr_inicio";
        $QryLog = query($SqlLog);
        $i = 0;
        while($ArLog = farray($QryLog)){
            $i++;
            $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
        ?>
        <tr bgcolor="<?php echo $bgcolor;?>">
            <td align="center"><img src="images/btn_det.PNG" class="btn_det" NumregLog="<?php echo $ArLog['numreg'];?>"/></td>
            <td align="center"><img src="images/btn_<?php echo (($ArLog['qtde_registros_erro'] > 0)?'vermelho':'verde');?>.png" /></td>
            <td><?php echo dten2br($ArLog['dt_inicio']);?></td>
            <td><?php echo $ArLog['hr_inicio'];?></td>
            <td><?php echo dten2br($ArLog['dt_fim']);?></td>
            <td><?php echo $ArLog['hr_fim'];?></td>
            <td><?php echo DeparaCodigoDescricao('is_log_integracao_txt_erp_datasul_tabelas', array('descricao'), array('nome_tabela' => $ArLog['nome_tabela']));?></td>
            <td><?php echo DeparaCodigoDescricao('is_usuario', array('nome_usuario'), array('numreg' => $ArLog['id_usuario']));?></td>
            <td align="right"><?php echo $ArLog['tempo_gasto'];?></td>
            <td align="right"><?php echo $ArLog['qtde_registros_criados'];?></td>
            <td align="right"><?php echo $ArLog['qtde_registros_atualizados'];?></td>
            <td align="right"><?php echo $ArLog['qtde_registros_erro'];?></td>
            <td align="right"><?php echo $ArLog['qtde_registros_ignorados'];?></td>
            <td align="right"><?php echo $ArLog['qtde_registros_processados'];?></td>
        </tr>
        <?php } ?>
        <tr>
            <th colspan="18" style="font-size:16px;">Cargas N&atilde;o Executadas</th>
        </tr>
        <?php
        $SqlLog = "SELECT descricao FROM is_log_integracao_txt_erp_datasul_tabelas WHERE sn_ativo = 1 AND NOT nome_tabela IN(SELECT nome_tabela FROM is_log_integracao_txt_erp_datasul WHERE dt_inicio = '".$DataBase."')";
        $QryLog = query($SqlLog);
        $i = 0;
        while($ArLog = farray($QryLog)){
            $i++;
            $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
        ?>
        <tr bgcolor="<?php echo $bgcolor;?>">
            <td colspan="18" style="font-size:16px;"><?php echo $ArLog['descricao'];?></td>
        </tr>
        <?php } ?>
    </table>
</fieldset>
<script type="text/javascript">
    $(document).ready(function(){
        $(".botao_jquery").button();

        $("#btn_recarregar_tela").click(function(){
            var Url = 'modulos/integracao_datasul/log_integracao_txt.php';
            Url = Url + '?dt_base=' + $("#edtdt_base").val();
            exibe_programa(Url);
        });

        $("#edtdt_base").datepicker({
            showOn: "button",
            buttonImage: "images/agenda.gif",
            buttonImageOnly: true,
            changeMonth:true,
            changeYear:true
        });
        
        $(".btn_det").click(function(){
            window.open('gera_cad_detalhe.php?pfuncao=log_integracao_txt_erp_datasul&pread=1&pnumreg=' + $(this).attr("NumregLog"),'log_integracao_txt_erp_datasul'+$(this).attr("NumregLog"),'toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1').focus();
        }).css("cursor","pointer");
    });
</script>