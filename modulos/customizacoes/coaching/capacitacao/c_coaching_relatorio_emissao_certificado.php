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

require('../../../../conecta.php');
require('../../../../functions.php');

?>
<style type="text/css">
    #relatorio_custom{
        margin: 10px;
    }
    #relatorio_custom h3{
        font-size: 18px;
    }
    #relatorio_custom .linha_form {
		padding:10px;
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
    <h3>Relat&oacute;rio Emiss&atilde;o de Certificado</h3>
    <fieldset>
    <legend>Par&acirc;metros</legend>
    <div class="linha_form">
      <label for="edtid_curso">Curso:</label>
      <select name="edtid_curso" id="edtid_curso">
          <option value="">Todos</option>
          <?php
          $QryCurso = query("SELECT * FROM c_coaching_curso ORDER BY nome_curso");
          while($ArCurso = farray($QryCurso)){?>
          <option value="<?php echo $ArCurso['numreg'];?>"><?php echo $ArCurso['nome_curso'];?></option>
          <?php } ?>
      </select>      
    </div>
    <div class="linha_form">
      <label for="edtid_agenda">Agenda:</label>
      <select name="edtid_agenda" id="edtid_agenda">
          <option value="">Todos</option>
          <?php
          $QryCurso = query("select numreg from c_coaching_agenda_curso where id_parte in(2,7,8,9,11,12,15,17,18,19,21)");
          while($ArCurso = farray($QryCurso)){?>
          <option value="<?php echo $ArCurso['numreg'];?>"><?php echo $ArCurso['numreg'];?></option>
          <?php } ?>
      </select>      
    </div>    
    <div class="linha_form">
        <label for="edtid_situacao">Situa&ccedil;&atilde;o:</label>
        <select name="edtid_situacao" id="edtid_situacao">
            <option value="">Todas</option>
            <option value="1">Aptos</option>
            <option value="2">Inaptos</option>
        </select>
    </div>
    </fieldset>
    <div id="fm-submit" class="fm-req">
      <input type="button" id="btn_gerar_relatorio" class="botao_jquery" value="Gerar Relatório" />
    </div>
</div>
<script language="JavaScript">
    $(document).ready(function(){
        $(".botao_jquery").button();
        $("#btn_gerar_relatorio").click(function(){
            abre_tela_nova('modulos/customizacoes/coaching/capacitacao/c_coaching_relatorio_emissao_certificado_post.php?id_curso=' + $("#edtid_curso").val() + '&id_situcao=' + $("#edtid_situacao").val()+ '&id_agenda=' + $("#edtid_agenda").val(),'c_coaching_relatorio_emissao_certificado',750,550,1);
        });
    });
</script>