<?
/*
 * relatorio_pedido.php
 * Autor: Alex
 * 14/01/2011 10:25:00
 * Programa de geração de relatório específico da GrampLine
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");
?>
<style type="text/css">
    #relatorio_custom{
        margin: 10px;
    }
    #relatorio_custom h3{
        font-size: 18px;
    }
    #relatorio_custom .linha_form label{
        padding-right: 5px;
        display: inline-block;
        width:100px;
        text-align: right;
    }
    #relatorio_custom legend{
        font-size: 16px;
    }
</style>
<div id="relatorio_custom">
    <h3>Relatório de Pedidos</h3>
    <fieldset>
    <legend>Parâmetros</legend>
    <div class="linha_form">
      <label for="edtdt_inicio">Data Início:</label>
      <input type="text" name="edtdt_inicio" id="edtdt_inicio"/>
      <img border="0" width="15" height="15" style="cursor:pointer;" id="btn_limpardt_inicio" src="../../../images/btn_eraser.PNG" alt="Limpar" title="Limpar">
    </div>
    <div class="linha_form">
      <label for="edtdt_fim">Data Fim:</label>
      <input type="text" name="edtdt_fim" id="edtdt_fim"/>
      <img border="0" width="15" height="15" style="cursor:pointer;" id="btn_limpardt_fim" src="../../images/btn_eraser.PNG" alt="Limpar" title="Limpar">
    </div>
    </fieldset>
    <div id="fm-submit" class="fm-req">
      <input name="Submit" value="Gerar Relatório" type="submit" />
    </div>
</div>
<script language="JavaScript">
    $(document).ready(function(){
        $("#edtdt_inicio,#edtdt_fim").datepicker({
            showOn: "button",
            buttonImage: "../../../images/agenda.gif",
            buttonImageOnly: true,
            changeMonth:true,
            changeYear:true
        });
        $("#edtdt_inicio,#edtdt_fim").val("<?php echo date("d/m/Y");?>");

        $("#btn_limpardt_inicio").click(function(){
            $("#edtdt_inicio").val("");
        });
        $("#btn_limpardt_fim").click(function(){
            $("#edtdt_fim").val("");
        });
    });
</script>