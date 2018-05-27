<?php
/*
 * c_coaching_grade_pagto_custom.php
 * Autor: Alisson
 * 18/12/2012 16:03:16
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header('Content-Type: text/html; charset=utf-8');

require('../../conecta.php');
require('../../functions.php');

if(!$_POST){
    BloqueiaAcessoDireto();
}
$numreg = $_POST['numreg'];
?>
<style>
	.venda_tabela_itens input{ width:150px}
	.venda_tabela_itens select{ width:156px !important}
	#edtpagto_dt_primeiro_pagto { width:136px !important}
</style>


<fieldset id="fs_pagto" class="venda_tabela_itens">
    <input type="hidden" name="pagto_id_requisicao" id="pagto_id_requisicao" value="1" />
    <input type="hidden" name="id_parcela" id="id_parcela" value="<?=$id_parcela?>" />
    <table border="0" align="left" cellpadding="2" cellspacing="0">
        <tr>
            <td width="120" align="left" valign="top" class="c_campo_obrigatorio">Vl. Parcela.:</td>
            <td width="180"><input type="text" class="c_campo_vl" name="edtpagto_vl_parcela" id="edtpagto_vl_parcela" value=""/></td>
            <td width="158" align="left" valign="top" class="c_campo_obrigatorio">Forma Pagto.:</td>
            <td width="202"><?php echo TabelaParaCombobox('is_forma_pagto', 'numreg', 'nome_forma_pagto', 'edtpagto_id_forma_pagto');?></td>
        </tr>
        <tr>
            <td align="left" valign="top" class="c_campo_obrigatorio">N&deg; Parcela.:</td>
            <td><?php echo TabelaParaCombobox('is_cond_pagto', 'numreg', 'nome_cond_pagto', 'edtpagto_id_cond_pagto');?></td>
            <td align="left" valign="top" class="c_campo_obrigatorio">Vencto. 1&ordf; Parcela:</td>
          <td><input type="text" class="c_campo_data" readonly="readonly" name="edtpagto_dt_primeiro_pagto" id="edtpagto_dt_primeiro_pagto" value="" /></td>
        </tr>
        <tr>
          <td align="left" valign="top" class="c_campo_obrigatorio">Tipo Pagto:</td>
          <td><?php echo TabelaParaCombobox('c_coaching_tp_pagto', 'numreg', 'nome_tp_pagto', 'edtpagto_id_tp_pagto');?></td>
          <td colspan="2" align="left" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" valign="top" class="c_campo_obrigatorio">Obs:</td>
            <td colspan="3"><textarea name="edtpagto_obs" id="edtpagto_obs" cols="50" rows="2"></textarea></td>
        </tr>
        	<td align="left" valign="top"></td>
            <td colspan="3"><input type="button" class="botao_jquery" id="btn_add_pagto" value="Adicionar Pagamento"></td>
        <tr>
        </tr>
    </table>
</fieldset>
<br>

<div class="div_grade_pagto"></div>

<script type="text/javascript">
	IMGLoadingGeralCustom = '<div align="center"><img src="../../images/ajax_loading_bar.gif" alt="Carregando..." /><br /><strong>Carregando...</strong></div>';
	$(document).ready(function(){
		RecarregaGradePagto();
		$(".botao_jquery").button();
		$("#btn_add_pagto").click(function(){
			if($("#edtpagto_vl_parcela").val() == ''){
				alert('Valor Total deve ser preenchido!');
				return false;
			}
			else if($("#edtpagto_id_forma_pagto").val() == ''){
				alert('Forma de Pagto. deve ser preenchido!');
				return false;
			}
			else if($("#edtpagto_id_cond_pagto").val() == ''){
				alert('Cond. Pagto. deve ser preenchido!');
				return false;
			}
			else if($("#edtpagto_id_tp_pagto").val() == ''){
				alert('Tipo deve ser preenchido!');
				return false;
			}			
			$.ajax({
				url: "c_coaching_grade_pagto_custom_post.php",
				global: false,
				type: "POST",
				data: ({
					numreg: $("#pnumreg").val(),
					ptp_venda: $("#ptp_venda").val(),
					id_parcela: $("#id_parcela").val(),
					pagto_id_requisicao: $("#pagto_id_requisicao").val(),
					edtpagto_vl_parcela: $("#edtpagto_vl_parcela").val(),
					edtpagto_id_forma_pagto: $("#edtpagto_id_forma_pagto").val(),
					edtpagto_id_cond_pagto: $("#edtpagto_id_cond_pagto").val(),
					edtpagto_dt_primeiro_pagto: $("#edtpagto_dt_primeiro_pagto").val(),
					edtpagto_id_tp_pagto: $("#edtpagto_id_tp_pagto").val(),
					edtpagto_obs: escape($("#edtpagto_obs").val())
				}),
				dataType: "html",
				async: true,
				beforeSend: function(){
					//Dialog.html(IMGLoadingGeralCustom);
				},
				error: function(){
					alert("Erro com a requisição");
				},
				success: function(responseText){
					if(responseText == 'Registro inserido com sucesso!'){
						alert(responseText);
						$('#pagto_id_requisicao').val('1');
					}else if(responseText == 'Registro atualizado com sucesso!'){
						alert(responseText);
						$('#pagto_id_requisicao').val('1');
					}else{
						alert(responseText);
					}
					RecarregaGradePagto();
					LimpaCampo();
				}
			});
		});
	});
	
	function LimpaCampo(){
		$('#edtpagto_vl_parcela').val('');
		$('#edtpagto_id_forma_pagto').val('');
		$('#edtpagto_id_cond_pagto').val('');
		$('#edtpagto_dt_primeiro_pagto').val('');
		$('#edtpagto_id_tp_pagto').val('');
		$('#edtpagto_obs').val('');
	}
	
	
	function RecarregaGradePagto(){
		$.ajax({
			url: "c_coaching_grade_pagto_custom_lista.php",
			global: false,
			type: "POST",
			data: ({
				numreg: '<?php echo $numreg;?>',
				ptp_venda: $("#ptp_venda").val()
			}),
			dataType: "html",
			async: true,
			beforeSend: function(){
				$(".div_grade_pagto").html(IMGLoadingGeralCustom);
			},
			error: function(){
				$(".div_grade_pagto").html('');
			},
			success: function(responseText){
				$(".div_grade_pagto").html(responseText);
			}
		});
	}
	$("#edtpagto_dt_primeiro_pagto").datepicker({
		showOn: "button",
		buttonImage: "../../images/agenda.gif",
		buttonImageOnly: true,
		changeMonth:true, 
		changeYear:true
	});
</script>