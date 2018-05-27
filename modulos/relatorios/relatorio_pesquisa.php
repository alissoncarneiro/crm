<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>OASIS CRM - Sistemas de Gestão</title>
<link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
<link href="../../estilos_css/estilo_menu.css" rel="stylesheet" type="text/css" />
<link href="../../estilos_css/calendar-blue.css" rel="stylesheet" type="text/css" media="all" title="win2k-cold-1" />
<link href="../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />

<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	width: 100%
}

-->
</style>
<script language="JavaScript" src="../../js/ajax_usuario.js"></script>
<script language="JavaScript" src="../../js/ajax_menus.js"></script>
<script language="JavaScript" src="../../js/ajax_gera_cad.js"></script>
<script language="JavaScript" src="../../js/function.js"></script>
<script language="JavaScript" src="../../js/menu.js"></script>
<script type="text/javascript" src="../../js/calendario/calendario.js"></script>
<script type="text/javascript" src="../../js/calendario/calendario-pt.js"></script>
<script type="text/javascript" src="../../js/calendario/calendario-config.js"></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.8.5.custom.min.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
<script src="../../amcharts/amcharts.js" type="text/javascript"></script>        
<script type="text/javascript" >
	function maximizar() {
    	window.moveTo (-4,-4);
        window.resizeTo(screen.availWidth + 8, screen.availHeight + 8);
    }
    maximizar();
</script>
	
</head>

<body>
<?php
/*
 * Programa Responsavel exibir os parametros para agerar o relatorio de pesquisa gerada
 *
 * Resp: Vitor SBC
 * Data: 27-11-2012 
*/

header("Content-Type: text/html;  charset=ISO-8859-1");
session_start();

require_once("../../conecta.php");
require_once("../../functions.php");

?>
    <div id="div_rec_js"></div>
    <div id="div_relatorio_pedidos">
        <script type="text/javascript" >
			$(document).ready(function(e) {
				$('#resp').hide();
				$('#loading_bar').hide();
            });
			function gera_relatorio_pesquisa(){
				$.ajax({
					url:'relatorio_pesquisa_post.php',
					global: true,
					type: "POST",
					dataType: "html",
					async: true,
					data: ({
						id_pesquisa: $("#edtid_programacao").val()
					}),
					error: function(){
						alert('Erro com a requisição');
						Mask.fadeOut(100);
					},
					success: function(data){
						$('#resp').html(data);
						$('#resp').fadeIn(200);
					}
				});
				
			}
        </script>

        
       <div id="conteudo_detalhes" style=" width: 98%; margin:5px; padding:5px; border:1px solid #43a;">
           <form method=""></form>
            <table width="100%" border="0" cellspacing="2" cellpadding="0">
            <tr>
             <td width="1%"></td>
             <td colspan="3"><br />
                 <div align="left"><img alt="seta"  src="../../images/seta.gif" width="4" height="7" /><span class="tit_detalhes">Relatório de Pesquisa</span></div>
                    <hr style="border:1px solid #eee" />
             </td>
            </tr>
            <tr>
                    <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><div align="right">Pesquisa :</div></td>
                <td>&nbsp;</td>
                <td>
                    <select name="edtid_programacao" id="edtid_programacao">
                        <option value="">Selecione...</option>
                        <?php
                        $SqlProgramacao = "SELECT numreg, nome_script FROM is_script";
                        $QryProgramacao = query($SqlProgramacao);
                        while($ArProgramacao = farray($QryProgramacao)){
                            echo "<option value='".$ArProgramacao['numreg']."'>".$ArProgramacao['nome_script']."</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td width="24%">&nbsp;</td>
                <td width="2%">&nbsp;</td>
                <td width="73%">
                    <div align="left">
                        <input name="btn_gerar_relatorio_pesquisa" type="button" class="botao_form" id="btn_gerar_relatorio_pesquisa" onclick="javascript:gera_relatorio_pesquisa();" value="Gerar Relatório" />
                    </div>
                </td>
                <td>&nbsp;</td><td colspan="3">&nbsp;</td>
           </tr>
           <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
            </tr>
        </table>
           <div id="resp" style="padding: 5px; border: 1px solid #eee; text-align: center;">
               <div id="loading_bar">
                   <img alt="loading"  src="../../images/ajax_loading_bar.gif" />
               </div>
           </div>
    </div>
        
    </div>
</body>
</html>    