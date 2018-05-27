<?php
/*
 * c_coaching_grade_pagto.php
 * Autor: Alex
 * 01/08/2011 09:03:16
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
	 <!--<td>Estabelecimento</td>-->
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
                            t1.dt_primeiro_pagto,
							t1.id_estabelecimento
                            FROM
                                c_coaching_inscricao_pagto t1
                            INNER JOIN
                                is_forma_pagto t2 ON t1.id_forma_pagto = t2.numreg
                            INNER JOIN
                                is_cond_pagto t3 ON t1.id_cond_pagto = t3.numreg
                            INNER JOIN c_coaching_tp_pagto t4 ON t1.id_tp_pagto = t4.numreg
                            WHERE
                                t1.id_inscricao = ".$IdInscricao."
                            AND 
                                t1.id_venda IS NULL";
    $QryGradePagto = query($SqlGradePagto);
	
	$QryEstabelecimento = query("SELECT * FROM is_estabelecimento");
	while($ArBuscaEstabelecimento = mysql_fetch_assoc($QryEstabelecimento)){
		$arEstabelecimento[$ArBuscaEstabelecimento['numreg']] = $ArBuscaEstabelecimento['nome_estabelecimento'];
	}
	
    $i = 0;
    while($ArGradePagto = farray($QryGradePagto)){
	echo '<pre>';
	echo '</pre>';
        $bgcolor = ($i % 2 == 0)?'#EAEAEA':'#FFFFFF';
        $i++;
        $VlTotalPagtos += $ArGradePagto['vl_pagto'];
		$estabelecimento = $arEstabelecimento[$ArGradePagto['id_estabelecimento']];
        ?>
        <tr bgcolor="<?php echo $bgcolor; ?>">
            <td><img src="../../../../images/btn_det.PNG" width="15" class="btn_edit_inscricao_pagto" numreg="<?php echo $ArGradePagto['numreg'];?>" /></td>
            <td><?php echo $i; ?></td>
            <td><?php echo round($ArGradePagto['qtde_parcelas']).' x '.number_format($ArGradePagto['vl_parcela'],2,',','.');?></td>
            <td><?php echo number_format($ArGradePagto['vl_pagto'],2,',','.');?></td>
            <td><?php echo $ArGradePagto['nome_forma_pagto'];?></td>
            <td><?php echo $ArGradePagto['nome_cond_pagto'];?></td>
            <td><?php echo dten2br($ArGradePagto['dt_primeiro_pagto']);?></td>
            <td><?php echo $ArGradePagto['nome_tp_pagto'];?></td>
            <!--<td><?php echo $estabelecimento;?></td>-->
            <td><?php echo $ArGradePagto['obs'];?></td>
            <td align="center"><img src="../../../../images/btn_del.png" class="btn_del_inscricao_pagto" numreg="<?php echo $ArGradePagto['numreg'];?>" /></td>
        </tr>
<?php } ?>
        <tr>
            <td colspan="10"><strong>Total: </strong><?php echo number_format($VlTotalPagtos,2,',','.');?></td>
        </tr>
</table>
<input type="button" class="botao_jquery" id="btn_add_pagto" value="Incluir Pagamento">
<script type="text/javascript">
    $(document).ready(function(){
        $(".botao_jquery").button();
        $("#btn_add_pagto").click(function(){
            var Dialog = $("#jquery-dialog");
            Dialog.attr("title","Pagamentos")
            Dialog.dialog({
                width:'560',
				height: '330',
                modal: true,
                buttons:{
                    "Salvar": function(){
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
                        else if($("#edtpagto_id_estabelecimento").val() == ''){
                            alert('Estabelecimento deve ser preenchido!');
                            return false;
                        }
                        $.ajax({
                            url: "c_coaching_form_pagto_post.php",
                            global: false,
                            type: "POST",
                            data: ({
                                id_inscricao: $("#pnumreg").val(),
                                id_curso: $("#edtid_curso").val(),
                                pagto_id_requisicao: $("#pagto_id_requisicao").val(),
                                edtpagto_vl_parcela: $("#edtpagto_vl_parcela").val(),
                                edtpagto_id_forma_pagto: $("#edtpagto_id_forma_pagto").val(),
                                edtpagto_id_cond_pagto: $("#edtpagto_id_cond_pagto").val(),
                                edtpagto_dt_primeiro_pagto: $("#edtpagto_dt_primeiro_pagto").val(),
                                edtpagto_id_tp_pagto: $("#edtpagto_id_tp_pagto").val(),
                                edtpagto_id_estabelecimento: $("#edtpagto_id_estabelecimento").val(),
                                edtpagto_obs: escape($("#edtpagto_obs").val())
                            }),
                            dataType: "html",
                            async: true,
                            beforeSend: function(){
                                Dialog.html(IMGLoadingGeralCustom);
                            },
                            error: function(){
                                alert("Erro com a requisição");
                                Dialog.dialog("close");
                            },
                            success: function(responseText){
                                RecarregaGradePagto();
                                Dialog.dialog("close");
                                alert(responseText);
                            }
                        });
                    },
                    Fechar: function(){$(this).dialog("close");}
                },
                open: function(){
                    $.ajax({
                        url: "c_coaching_form_pagto.php",
                        global: false,
                        type: "POST",
                        data: ({
                            id_inscricao: $("#pnumreg").val(),
							id_curso: $("#edtid_curso").val()
                        }),
                        dataType: "html",
                        async: true,
                        beforeSend: function(){
                            Dialog.html(IMGLoadingGeralCustom);
                        },
                        error: function(){
                            alert("Erro com a requisição");
                        },
                        success: function(responseText){
                            Dialog.html(responseText);
                        }
                    });
                },
                show: "fade",
                hide: "fade",
                close: function(){$(this).dialog("destroy");}
            });
        });
        
        $(".btn_edit_inscricao_pagto").click(function(){
            var Numreg = $(this).attr("numreg");
            var Dialog = $("#jquery-dialog");
            Dialog.attr("title","Pagamentos")
            Dialog.dialog({
                width:'560',
				height: '330',
                modal: true,
                buttons:{
                    "Salvar": function(){
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
                        else if($("#edtpagto_id_estabelecimento").val() == ''){
                            alert('Estabelecimento deve ser preenchido!');
                            return false;
                        }
                        $.ajax({
                            url: "c_coaching_form_pagto_post.php",
                            global: false,
                            type: "POST",
                            data: ({
                                id_inscricao: $("#pnumreg").val(),
                                id_curso: $("#edtid_curso").val(),
                                id_inscricao_pagto: Numreg,
                                pagto_id_requisicao: $("#pagto_id_requisicao").val(),
                                edtpagto_vl_parcela: $("#edtpagto_vl_parcela").val(),
                                edtpagto_id_forma_pagto: $("#edtpagto_id_forma_pagto").val(),
                                edtpagto_id_cond_pagto: $("#edtpagto_id_cond_pagto").val(),
                                edtpagto_dt_primeiro_pagto: $("#edtpagto_dt_primeiro_pagto").val(),
                                edtpagto_id_tp_pagto: $("#edtpagto_id_tp_pagto").val(),
                                edtpagto_id_estabelecimento: $("#edtpagto_id_estabelecimento").val(),
                                edtpagto_obs: escape($("#edtpagto_obs").val())
                            }),
                            dataType: "html",
                            async: true,
                            beforeSend: function(){
                                Dialog.html(IMGLoadingGeralCustom);
                            },
                            error: function(){
                                alert("Erro com a requisição");
                                Dialog.dialog("close");
                            },
                            success: function(responseText){
                                RecarregaGradePagto();
                                Dialog.dialog("close");
                                alert(responseText);
                            }
                        });
                    },
                    Fechar: function(){$(this).dialog("close");}
                },
                open: function(){
                    $.ajax({
                        url: "c_coaching_form_pagto.php",
                        global: false,
                        type: "POST",
                        data: ({
                            id_inscricao: $("#pnumreg").val(),
                            id_inscricao_pagto: Numreg,
							 id_curso: $("#edtid_curso").val()
                        }),
                        dataType: "html",
                        async: true,
                        beforeSend: function(){
                            Dialog.html(IMGLoadingGeralCustom);
                        },
                        error: function(){
                            alert("Erro com a requisição");
                        },
                        success: function(responseText){
                            Dialog.html(responseText);
                        }
                    });
                },
                show: "fade",
                hide: "fade",
                close: function(){$(this).dialog("destroy");}
            });
        }).css("cursor","pointer");

        $(".btn_del_inscricao_pagto").click(function(){
            var Numreg = $(this).attr("numreg");
            if(confirm('Excluir o registro ?')){
                $.ajax({
                    url: "c_coaching_form_pagto_post.php",
                    global: false,
                    type: "POST",
                    data: ({
                        id_inscricao: $("#pnumreg").val(),
                        id_inscricao_pagto: Numreg,
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
                        RecarregaGradePagto();
                    }
                });
            }
        }).css("cursor","pointer");
    });
</script>