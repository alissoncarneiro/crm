<?php
/*
 * form.php
 * Autor: Alex
 * 09/05/2012 11:35:18
 */
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

if($_SESSION['id_usuario'] == ''){
    exit;
}

require('../../../conecta.php');
require('../../../functions.php');
?>
<script language="JavaScript">
    function RecarregaCampoFamilia(){
        $("#edtid_familia_on,#edtid_familia_off").children().remove().end();
        $.ajax({
            url: "modulos/parametros/pol_rep_x_fm_com/form_post.php",
            global: false,
            type: "POST",
            data: ({
                "requisicao":'recarrega_familia',
                "id_representante":$("#edtid_representante").val()
            }),
            dataType: "xml",
            async: true,
            beforeSend: function(){
                $("#divloading").html(HTMLLoadingGeral);
            },
            error: function(){
                $("#divloading").html('');
                alert('Erro com a requisição');                        
            },
            success: function(xml){
                $("#divloading").html('');
                var Status = $(xml).find('status').text();
                var Mensagem = $(xml).find('mensagem').text();
                if(Status == '1'){
                    var XMLCampoFamiliaOn = $(xml).find('campo_familia_on');
                    XMLCampoFamiliaOn.find('option').each(function(){
                        NewOption = $("<option value=\"" + $(this).attr("value") + "\">" + $(this).text() + "</option>");
                        $("#edtid_familia_on").append(NewOption);
                    });
                    var XMLCampoFamiliaOff = $(xml).find('campo_familia_off');
                    XMLCampoFamiliaOff.find('option').each(function(){
                        NewOption = $("<option value=\"" + $(this).attr("value") + "\">" + $(this).text() + "</option>");
                        $("#edtid_familia_off").append(NewOption);
                    });
                }
                else{
                    alert(Mensagem);
                }
            }
        });
    }

    $(document).ready(function(){
        $("#edtid_representante").change(function(){
            RecarregaCampoFamilia();
        });

        $("#btn_add").click(function(){
            var IdFamilia = $("#edtid_familia_off").val();
            if(!IdFamilia || IdFamilia == '' || IdFamilia == null){
                alert('Nenhuma família selecionada!');
                return false;
            }
            $.ajax({
                url: "modulos/parametros/pol_rep_x_fm_com/form_post.php",
                global: false,
                type: "POST",
                data: ({
                    "requisicao":'add_familia',
                    "id_representante":$("#edtid_representante").val(),
                    "id_familia":IdFamilia
                }),
                dataType: "xml",
                async: true,
                beforeSend: function(){

                },
                error: function(){
                    alert('Erro com a requisição');
                },
                success: function(xml){
                    //alert($(xml).find("mensagem").text());
                    RecarregaCampoFamilia();
                }
            });
        });
        $("#btn_del").click(function(){
            var IdFamilia = $("#edtid_familia_on").val();
            if(!IdFamilia || IdFamilia == '' || IdFamilia == null){
                alert('Nenhuma família selecionada!');
                return false;
            }
            $.ajax({
                url: "modulos/parametros/pol_rep_x_fm_com/form_post.php",
                global: false,
                type: "POST",
                data: ({
                    "requisicao":'del_familia',
                    "id_representante":$("#edtid_representante").val(),
                    "id_familia":IdFamilia
                }),
                dataType: "xml",
                async: true,
                beforeSend: function(){

                },
                error: function(){
                    alert('Erro com a requisição');
                },
                success: function(xml){
                    //alert($(xml).find("mensagem").text());
                    RecarregaCampoFamilia();
                }
            });
        });
    });
</script>
<table cellpadding="2" cellspacing="2">
    <tr>
        <td colspan="2" align="center" class="tit_detalhes"><img width="4" height="7" src="images/seta.gif"/>Parametriza&ccedil;&atilde;o Fam&iacute;lia x Representante</td>
    </tr>
    <tr>
        <td colspan="2" align="center">Representante: <?php echo TabelaParaCombobox('is_usuario', 'numreg', 'nome_usuario', 'edtid_representante',NULL,'','ORDER BY nome_usuario');?><div id="divloading"></div></td>
    </tr>
    <tr>
        <td align="center">Fam&iacute;lias Comerciais</td>
        <td align="center">Fam&iacute;lias Comerciais Permitidas</td>
    </tr>
    <tr>
        <td><select multiple="multiple" id="edtid_familia_off" size="20" style="width:350px;"></select></td>
        <td><select multiple="multiple" id="edtid_familia_on" size="20" style="width:350px;"></select></td>
    </tr>
    <tr>
        <td align="right"><input type="button" id="btn_add" class="botao_form" value=">>" style="width:80px;" /></td>
        <td align="left"><input type="button" id="btn_del" class="botao_form" value="<<" style="width:80px;" /></td>
    </tr>
</table>