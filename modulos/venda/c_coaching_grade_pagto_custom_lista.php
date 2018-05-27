<?php

/*
 * c_coaching_grade_pagto_custom_lista.php
 * Autor: Alisson
 * 18/12/2013 17:14:09
 */

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header('Content-Type: text/html; charset=utf-8');
session_start();
if($_SESSION['id_usuario'] == ''){
    echo 'Usuário não logado.';
    exit;
}

require('../../conecta.php');
require('../../functions.php');
require('../../classes/class.uB.php');
if(!$_POST){
    BloqueiaAcessoDireto();
}
$_POST = uB::UrlDecodePost($_POST);
$numreg    = trim($_POST['numreg']);
$ptp_venda = trim($_POST['ptp_venda']);
$ptp_venda = '1' ? 'id_orcamento' :'id_pedido';
?>

<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="venda_tabela_itens">
  <tr class="venda_titulo_tabela">
        <td width="15">&nbsp;</td>
        <td width="15">#</td>
        <td>Parcelas</td>
        <td>Valor Total</td>
        <td>Forma Pagto.</td>
        <td>Cond. Pagto.</td>
        <td>1 &ordm; Vencimento</td>
        <td>Tipo Pagto.</td>
        <td>Obs</td>
        <td width="15">&nbsp;</td>
    </tr>
    <?php
	
    $VlTotalPagtos = 0;
    $SqlGradePagto = "SELECT
                            t1.numreg,
                            t1.vl_pagto,
                            t1.vl_parcela,
                            t1.qtde_parcelas,
                            t1.obs,
                            t2.nome_forma_pagto,
                            t3.nome_cond_pagto,
                            t4.nome_tp_pagto,
                            t1.dt_primeiro_pagto
                            FROM
                                c_coaching_inscricao_pagto_orcamento_pedido t1
                            INNER JOIN
                                is_forma_pagto t2 ON t1.id_forma_pagto = t2.numreg
                            INNER JOIN
                                is_cond_pagto t3 ON t1.id_cond_pagto = t3.numreg
                            INNER JOIN c_coaching_tp_pagto t4 ON t1.id_tp_pagto = t4.numreg
                            WHERE
                                t1.$ptp_venda = ".$numreg." ";
    $QryGradePagto = query($SqlGradePagto);
    $i = 0;
    while($ArGradePagto = farray($QryGradePagto)){
        $bgcolor = ($i % 2 == 0)?'#EAEAEA':'#FFFFFF';
        $i++;
        $VlTotalPagtos += $ArGradePagto['vl_pagto'];
        ?>
        <tr bgcolor="<?php echo $bgcolor; ?>" id="<?php echo $ArGradePagto['numreg'];?>" class="div_grade_pagto">
          <td><img src="../../images/btn_det.PNG" width="15" class="btn_edit_inscricao_pagto" numreg="<?php echo $ArGradePagto['numreg'];?>" /></td>
            <td><?php echo $i; ?></td>
            <td class="vl_parcela"><?php echo round($ArGradePagto['qtde_parcelas']).' x <span>'.number_format($ArGradePagto['vl_parcela'],2,',','.').'</span>';?></td>
            <td class="vl_pagto"><?php echo number_format($ArGradePagto['vl_pagto'],2,',','.');?></td>
            <td class="nome_forma_pagto"><?php echo $ArGradePagto['nome_forma_pagto'];?></td>
            <td class="nome_cond_pagto"><?php echo $ArGradePagto['nome_cond_pagto'];?></td>
            <td class="dt_primeiro_pagto"><?php echo dten2br($ArGradePagto['dt_primeiro_pagto']);?></td>
            <td class="nome_tp_pagto"><?php echo $ArGradePagto['nome_tp_pagto'];?></td>
            <td class="obs"><?php echo $ArGradePagto['obs'];?></td>
            <td align="center"><img src="../../images/btn_del.png" class="btn_del_inscricao_pagto" numreg="<?php echo $ArGradePagto['numreg'];?>" /></td>
  	</tr>
	<?php } ?>
    <tr>
        <td colspan="9"><strong>Total: </strong><?php echo number_format($VlTotalPagtos,2,',','.');?></td>
    </tr>
</table>
<script type="text/javascript">
	IMGLoadingGeralCustom = '<div align="center"><img src="../../images/ajax_loading_bar.gif" alt="Carregando..." /><br /><strong>Carregando...</strong></div>';
	$(document).ready(function(){
       $(".btn_edit_inscricao_pagto").click(function(){
            var id_parcela = $(this).attr("numreg");
            $('#edtpagto_vl_parcela').val($('#'+id_parcela+' .vl_parcela span').text());
			//$('#edtpagto_id_forma_pagto').val( $('#'+Numreg+' .nome_forma_pagto').text());
			$("select#edtpagto_id_forma_pagto option").each(function () {
				if($('#'+id_parcela+' .nome_forma_pagto').text() == $(this).text()){
					$(this).attr('selected', 'selected');
				}
			});
			//$('#edtpagto_id_cond_pagto').val($('#'+Numreg+' .vl_pagto').text());
			$("select#edtpagto_id_cond_pagto option").each(function () {
				if($('#'+id_parcela+' .nome_cond_pagto').text() == $(this).text()){
					$(this).attr('selected', 'selected');
				}
			});			
			$('#edtpagto_dt_primeiro_pagto').val($('#'+id_parcela+' .dt_primeiro_pagto').text());
			//$('#edtpagto_id_tp_pagto').val($('#'+Numreg+' .nome_tp_pagto').text());
			$("select#edtpagto_id_tp_pagto option").each(function () {
				if($('#'+id_parcela+' .nome_tp_pagto').text() == $(this).text()){
					$(this).attr('selected', 'selected');
				}
			});	
			$('#edtpagto_obs').val($('#'+id_parcela+' .obs').text());
			$('#pagto_id_requisicao').val('2');
			$('#id_parcela').val(id_parcela);
        })

        $(".btn_del_inscricao_pagto").click(function(){
            var id_parcela = $(this).attr("numreg");
            if(confirm('Excluir o registro ?')){
                $.ajax({
                    url: "c_coaching_grade_pagto_custom_post.php",
                    global: false,
                    type: "POST",
                    data: ({
                        id_parcela: id_parcela,
                        pagto_id_requisicao: 3
                    }),
                    dataType: "html",
                    async: true,
                    beforeSend: function(){
                        
                    },
                    error: function(){
                        alert("Erro com a requisição");
                    },
                    success: function(responseText){
                        alert(responseText);
						$('#pagto_id_requisicao').val('1');
                        RecarregaGradePagto();
                    }
                });
            }
        }).css("cursor","pointer");
	});
</script>