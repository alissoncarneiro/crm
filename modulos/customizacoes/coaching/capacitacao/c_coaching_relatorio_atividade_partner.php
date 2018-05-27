<?

/*\
|*| 	Arquivo: c_coaching_exportacao_contatos_partner.php
|*| 	Autor: Alisson
|*| 	Início: 19/07/2013 15:17 
|*| 	Fim: xx/xx/xx 00:00
|*| 	Programa de Exportação de contatos recebidos para Partner
|*| 	Log de Alterações
|*| 	yyyy-mm-dd hh:mm <Pessoa responsável> <Descrição das alterações>
\*/

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('../../../../conecta.php');
require('../../../../functions.php');


?>

<style type="text/css">
    #relatorio_custom{ margin:10px; }
    #relatorio_custom .line_form { padding:10px; }	
    #relatorio_custom legend{ font-size:16px; font-size:18px; font-weight:bold; padding:0 5px; }
    #btn_exportar_contatos_career{ margin-top:10px; }
</style>

<div id="relatorio_custom">

    <form id="form_exportacao_contatos_partner" name="form_exportacao_contatos_partner" method="post">
        <fieldset>
            <legend class="legend_form">Contatos x Atividades</legend>
            
            <div id="form_prospect">
                <div class="line_form">
                    <label>
                        <input type="radio" name="date" id="date" class="date" value="1" checked="checked" /> Todos registros
                    </label>
                </div>
                <div class="line_form">
                    <label>
                        <input type="radio" name="date" id="date" class="date" value="2" />
                        <input type="text" size="12" name="date_ini" id="date_ini" value="" placeholder="dd/mm/aaaa" />
                        at�
                        <input type="text" size="12" name="date_end" id="date_end" value="<?=date("d/m/Y")?>" />
                    </label>
                </div>
            </div>
        </fieldset>
        <input type="button" id="btn_exportar_contatos_partner" class="botao_jquery" value="Exportar (Excel)" />
    </form>

</div>
<script language="JavaScript">
$(document).ready(function(){
    $(".botao_jquery").button();
    $("#btn_exportar_contatos_partner").click(function(){
        var data = $("#form_exportacao_contatos_partner").serialize();
        location.href="modulos/customizacoes/coaching/capacitacao/c_coaching_relatorio_atividade_partner_post.php?"+data;
    });
});
$(function() {
    $( "#date_ini" ).datepicker({
        defaultDate: "",
        changeMonth: true,
        numberOfMonths: 3,
        dateFormat: 'yy-mm-dd',
        onClose: function( selectedDate ) {
                $("#date_ini").datepicker( "option", selectedDate );
        },
        onSelect: function(){
            /*if($("#date_ini").val() != '' && $("#to").val() != ''){
                    $('#mes').val('');
            }*/
        }			
    });
    $( "#date_end" ).datepicker({
        defaultDate: "",
        changeMonth: true,
        dateFormat: 'yy-mm-dd',
        
        numberOfMonths: 3,
        onClose: function( selectedDate ) {
                $( "#from" ).datepicker( "option", selectedDate );
        },
        onSelect: function(){
        /*if($("#date_ini").val() != '' && $("#date_end").val() != ''){
                $('#mes').val('');
        }*/
        }
    });
});
</script>