<?php

/*
 * c_coaching_form_pagto.php
 * Autor: Alex
 * 11/08/2011 12:02:24
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('../../../../conecta.php');
require('../../../../functions.php');

if(!$_POST){
    BloqueiaAcessoDireto();
}

$IdInscricao = $_POST['id_inscricao'];
$IdInscricaoPagto = $_POST['id_inscricao_pagto'];
/*
$id_curso = $_POST['id_curso'];
$sqlCondicao = $id_curso == 1 ? ' where numreg in(1,3)' : ' where numreg in(2)';
*/
	
if($IdInscricaoPagto != ''){
    $QryInscricaoPagto = query("SELECT * FROM c_coaching_inscricao_pagto WHERE numreg = '".$IdInscricaoPagto."'");
    $ArInscricaoPagto = farray($QryInscricaoPagto);

}
else{
    $ArInscricaoPagto = array();
}
?>
<fieldset id="fs_pagto">
    <input type="hidden" name="pagto_id_requisicao" id="pagto_id_requisicao" value="<?php echo (($IdInscricaoPagto == '')?'1':'2');?>" />
    <table border="0" cellpadding="2" cellspacing="2">
        <tr>
            <td align="right" class="c_campo_obrigatorio">Valor Parcela.:</td>
            <td><input type="text" class="c_campo_vl" name="edtpagto_vl_parcela" id="edtpagto_vl_parcela" value="<?php echo number_format($ArInscricaoPagto['vl_parcela'],2,',','.');?>"/></td>
        </tr>
        <tr>
            <td align="right" class="c_campo_obrigatorio">Forma Pagto.:</td>
            <td><?php echo TabelaParaCombobox('is_forma_pagto', 'numreg', 'nome_forma_pagto', 'edtpagto_id_forma_pagto',$ArInscricaoPagto['id_forma_pagto']);?></td>
        </tr>
        <tr>
            <td align="right" class="c_campo_obrigatorio">Nº Parcela.:</td>
            <td><?php echo TabelaParaCombobox('is_cond_pagto', 'numreg', 'nome_cond_pagto', 'edtpagto_id_cond_pagto',$ArInscricaoPagto['id_cond_pagto']);?></td>
        </tr>
        <tr>
            <td align="right" class="c_campo_obrigatorio">Vencto. 1&ordf; Parcela:</td>
            <td><input type="text" class="c_campo_data" readonly name="edtpagto_dt_primeiro_pagto" id="edtpagto_dt_primeiro_pagto" value="<?php echo dten2br($ArInscricaoPagto['dt_primeiro_pagto']);?>" /></td>
        </tr>
        <tr>
            <td align="right" class="c_campo_obrigatorio">Tipo Pagto:</td>
            <td><?php echo TabelaParaCombobox('c_coaching_tp_pagto', 'numreg', 'nome_tp_pagto', 'edtpagto_id_tp_pagto',$ArInscricaoPagto['id_tp_pagto']);?></td>
        </tr>
       <!-- <tr>
          <td align="right" class="c_campo_obrigatorio">Estabelecimento:</td>
          										
           <td><?php //echo TabelaParaCombobox('is_estabelecimento','numreg', 'nome_estabelecimento', 'edtpagto_id_estabelecimento',$ArInscricaoPagto['id_estabelecimento'], $sqlCondicao   );?></td>
        </tr>-->
        <tr>
            <td align="right">Obs:</td>
            <td><textarea name="edtpagto_obs" id="edtpagto_obs" cols="52" rows="2"><?php echo $ArInscricaoPagto['obs'];?></textarea></td>
        </tr>
    </table>
</fieldset>
<script type="text/javascript">
    $(document).ready(function(){
        $("#edtpagto_dt_primeiro_pagto").datepicker({
            showOn: "button",
            buttonImage: "../../../../images/agenda.gif",
            buttonImageOnly: true,
            changeMonth:true, 
            changeYear:true
        });
    });
</script>