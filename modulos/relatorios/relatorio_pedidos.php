<?php
/*
 * relatorio_pedidos.php
 * Autor: Alex
 * 16/07/2012 09:20:11
 */
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");
require('../../conecta.php');
require('../../functions.php');
require('../../classes/class.ControleAcesso.php');

$ControleAcesso = new ControleAcesso($_SESSION['id_usuario'],'relatorio_pedido');
$SqlBloqueio = ($ControleAcesso->AplicaFiltroBloqueio())?$ControleAcesso->GeraSqlBloqueio('numreg',' WHERE '):'';

?>
<style type="text/css">
    #relatorio_custom{
        margin: 10px;
    }
    #relatorio_custom h3{
        font-size: 18px;
    }
    #relatorio_custom .linha_form{
        margin: 3px 0 3px 0;
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
        Per&iacute;odo: <input type="text" id="dti" style="width:70px;"/>&nbsp;<input type="text" id="dtf" style="width:70px;"/>
    </div>
    <div class="linha_form">
        <?php echo DeparaCodigoDescricao('is_gera_cad_campos', array('nome_campo'), array('id_funcao' => 'pedido', 'id_campo' => 'id_representante_principal'));?>: <?php echo TabelaParaCombobox('is_usuario', 'numreg', 'nome_usuario', 'repp','',$SqlBloqueio,' ORDER BY nome_usuario');?>
    </div>
    <div class="linha_form">
        <?php echo DeparaCodigoDescricao('is_gera_cad_campos', array('nome_campo'), array('id_funcao' => 'pedido', 'id_campo' => 'id_situacao_pedido'));?>: <?php echo TabelaParaCombobox('is_situacao_pedido', 'numreg', 'nome_situacao_pedido', 'ids','',' WHERE numreg IN(1,2,3,6) ',' ORDER BY numreg');?>
    </div>
    </fieldset>
    <div id="fm-submit" class="fm-req">
      <input type="button" class="botao_jquery" id="btn_gera_relatorio" value="Gerar Relatório" />
    </div>
</div>
<script language="JavaScript">
    $(document).ready(function(){
        $("#dti,#dtf").datepicker({
            showOn: "button",
            buttonImage: "images/agenda.gif",
            buttonImageOnly: true,
            changeMonth:true,
            changeYear:true
        });

        $(".botao_jquery").button();

        $("#dti,#dtf").val("<?php echo date("d/m/Y");?>");

        $("#btn_gera_relatorio").click(function(){
            window.open('modulos/relatorios/relatorio_pedidos_post.php?dti=' + $("#dti").val() + '&dtf=' + $("#dtf").val() + '&ids=' + $("#ids").val() + '&repp=' + $("#repp").val(),'relatorio_pedidos','width=800,height=600, scrollbars=yes, resizable=yes');
        });
    });
</script>