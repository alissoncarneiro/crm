<?php
/*
 * c_coaching_cancelar_agenda_adquirida.php
 * Autor: Alex
 * 29/11/2011 14:49:47
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('../../../../conecta.php');
require('../../../../functions.php');

if(!$_POST){
    BloqueiaAcessoDireto();
}
?>
<fieldset id="fs_cancelar_agenda_adquirida">
    <input type="hidden" name="id_agenda_curso" id="id_agenda_curso" value="<?php echo $_POST['id_agenda_curso'];?>" />
    <input type="hidden" name="id_inscricao" id="id_inscricao" value="<?php echo $_POST['id_inscricao'];?>" />
    <table border="0" cellpadding="2" cellspacing="2">
        <tr>
            <td align="right" class="c_campo_obrigatorio">Data Desistência.:</td>
            <td><input type="text" class="c_campo_dt" name="edtc_dt_desistencia" id="edtc_dt_desistencia" value="<?php echo date("d/m/Y");?>"/></td>
        </tr>
        <tr>
            <td align="right" class="c_campo_obrigatorio">Motivo:</td>
            <td><?php echo TabelaParaCombobox('c_coaching_motivo_desistencia', 'numreg', 'nome_motivo_desistencia', 'edtc_id_motivo_desistencia','');?></td>
        </tr>
        <tr>
            <td align="right" class="c_campo_obrigatorio">Tipo:</td>
            <td><?php echo TabelaParaCombobox('c_coaching_tipo_desistencia', 'numreg', 'nome_tipo_desistencia', 'edtc_id_tipo_desistencia','');?></td>
        </tr>
        <tr>
            <td align="right" class="c_campo_obrigatorio">Valor Devolução:</td>
            <td><input type="text" class="c_campo_vl" name="edtc_vl_devolucao" id="edtc_vl_devolucao" value="0,00"/></td>
        </tr>
        <tr>
            <td align="right">Obs:</td>
            <td><textarea name="edtpagto_obs" id="edtpagto_obs" cols="50" rows="2"><?php echo $ArInscricaoPagto['obs'];?></textarea></td>
        </tr>
    </table>
</fieldset>
<script type="text/javascript">
    $(document).ready(function(){
        $(".c_campo_dt").datepicker({
            showOn: "button",
            buttonImage: "images/agenda.gif",
            buttonImageOnly: true,
            changeMonth:true, 
            changeYear:true,
            minDate:0
        });
    });
</script>