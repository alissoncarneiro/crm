<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <?php header("Content-Type: text/html; charset=ISO-8859-1");?>
    <script type="text/javascript" src="../../../../js/jquery.qtip.js"></script>
    <script type="text/javascript" src="../../../../js/jquery.dlg.min.js"></script>
	<script type="text/javascript" src="../../../../tinymcecustom/jscripts/tiny_mce/jquery.tinymce.js"></script>

	<script>
        $(document).ready(function(){
            $("#text_area_corpo_email").tinymce({
                script_url : '../../../../tinymcecustom/jscripts/tiny_mce/tiny_mce.js',
				theme : "advanced",
              plugins : "pagebreak,style,layer,table,advhr,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,|,fullscreen",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
		content_css : "../../../../tinymcecustom/css/content.css",


            });
			
        });
    </script>

	<script type="text/javascript">

			var modelo_ordem;
			
			
			function inseriHtml(ordem){	
			
				modelo_ordem = ordem;
			
			
				$.post('../../../../modulos/customizacoes/coaching/email_pessoa/retorno_peca.php',                          // executa o arquivo php via $_POST..
					   ({
							acao: ordem,
							id_pessoa:<?=$_REQUEST['id_pessoa']?>
						}),                              												//.. passando a ação e as variaveis
					   function(retorno){                                       								// pega o que veio em retorno
							if (retorno !='') {																	// teve algum problema .. então                            
								alert('Preencha as Informações abaixo');
								$("#text_area_corpo_email").val(retorno);
							}
				});
				
				$.post('../../../../modulos/customizacoes/coaching/email_pessoa/c_coaching_combo.php',                          // executa o arquivo php via $_POST..
					   ({
							acao: ordem,
							id_pessoa: <?=$_REQUEST['id_pessoa']?>
						}),                              												//.. passando a ação e as variaveis
					   function(retorno){                                       								// pega o que veio em retorno
							if ((retorno != '') && ((ordem == '2') || (ordem == '4') || (ordem == '6'))) {
								$("#combo").html(retorno);
							}else{
								$("#combo").html("");
							}
				});
				
			}
			function atualizaHtml(numregModelo){
					$.post('../../../../modulos/customizacoes/coaching/email_pessoa/retorno_peca.php',                          // executa o arquivo php via $_POST..
					({
						numregModelo: numregModelo,
						acao: modelo_ordem,
						id_pessoa:<?=$_REQUEST['id_pessoa']?>
					}),                              												//.. passando a ação e as variaveis
					function(retorno){                                       								// pega o que veio em retorno
						if (retorno !='') {																	// teve algum problema .. então                            
							alert('Atualizado com Sucesso');
							$("#text_area_corpo_email").val(retorno);
						}
					});
				
			}

	</script>
   
    <script type="text/javascript" src="../../../../js/jquery.easing.js"></script>
    <script type="text/javascript" src="../../../../js/jquery-ui-1.8.5.custom.min.js"></script>
    <script type="text/javascript" src="../../../../js/jquery.notify.js"></script>
    <script type="text/javascript" src="../../../../js/AIM.js"></script>
    <script type="text/javascript" src="../../../../js/jquery.meio.mask.min.js"></script>
    <script type="text/javascript" src="../../../../js/function.js"></script>    
    
    </head>
    <body>	

<div id="ret"> </div>
    <form action="../../../../modulos/venda/envio_email/executa_envio_email_custom.php" id="form_envio_email" name="form" method="post" enctype="multipart/form-data" onsubmit="return AIM.submit(form_envio_email, {'onStart' : UploadstartCallback, 'onComplete' : UploadcompleteCallback});">
    <table border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td>Modelo do envio</td>
          <td><select id="select_id_modelo_email" name="select_id_modelo_email"  onChange="inseriHtml(this.value);">
            <option value="" >Selecione um modelo</option>
            <?php
                    include('../../../../conecta.php');
					$sql_modelo = "select * from is_modelo_orcamento where sn_envia_email=1";
					$qry_modelo = query($sql_modelo); 
					while($ar_modelo = farray($qry_modelo)){
                        echo '<option value="'.$ar_modelo['numreg'].'">'.$ar_modelo['nome_modelo_orcamento'].'</option>';
                    }
                    ?>
          </select>
          
        <span id="combo"></span>
          </td>         
          
          
        </tr>
    
        <tr>
          <td>Assunto</td>
          <td><input type="text" name="assunto"  size="52" /></td>
        </tr>
        <tr>
          <td valign="top">Enviar cópia (separar por ;)</td>
          <td><textarea name="cc_emails" id="cc_emails"  rows="2" cols="80"></textarea></td>
        </tr>
        <tr>
            <td valign="top"> Cópia oculta (separar por ;)</td>
            <td><textarea name="cco_emails" id="cco_emails"  rows="2" cols="80"></textarea></td>
        </tr>
        <tr>
            <td valign="top">Texto no corpo do e-mail</td>
            <td colspan="2"><textarea name="text_area_corpo_email" id="text_area_corpo_email" class="tinymce" style="width:100%; height:300px;"></textarea></td>
        </tr>
        <tr>
            <td>Anexar Arquivo</td>
            <td><div id="div_campo_arquivo_anexo"><input id="file_master" type="file" /></div></td>
        </tr>
    </table>

    <fieldset>
      <legend>Arquivos Anexos</legend>
        <ul id="ul_arquivos_anexos"></ul>
    </fieldset>
    <input type="hidden" name="pnumreg" id="pnumreg" value="<?php echo $_REQUEST['id_pessoa'];?>" />

    
    </form>

 
    <script>
        NumeroArquivo = 0;
        function CarregaCampoDeAnexo(){
            $("#file_master").change(function(){
                var CaminhoArquivo = $(this).val();
                /* Criando botão de excluir anexo */
                var DOMHTMLExcluir = $('<img src="modulos/venda/img/btn_apagar.png" alt="Excluir" title="Excluir" NumeroArquivo="' + NumeroArquivo + '"/>');
                DOMHTMLExcluir.click(function(){
                    if(confirm("Remover anexo ?")){
                        var NArquivo  =$(this).attr("NumeroArquivo");
                        $("#campo_arquivo_"+NArquivo).remove();
                        $("#li_arquivo_"+NArquivo).remove();
                    }
                }).css("cursor","pointer");
    
                /* Adicionando 'li' com o nome do arquivo */
                $("#ul_arquivos_anexos").append("<li id=\"li_arquivo_" + NumeroArquivo + "\"></li>");
                $("#li_arquivo_" + NumeroArquivo).append(DOMHTMLExcluir);
                $("#li_arquivo_" + NumeroArquivo).append(CaminhoArquivo);
                $(this).attr("name","arquivo_anexo[]");
                $(this).attr("id","campo_arquivo_" + NumeroArquivo);
                $(this).css("display","none");
    
                var NovoCampoArquivo = $("<input>");
                NovoCampoArquivo.attr("type","file");
                NovoCampoArquivo.attr("id","file_master");
                $("#div_campo_arquivo_anexo").append(NovoCampoArquivo);
                NumeroArquivo = NumeroArquivo +1;
                CarregaCampoDeAnexo();
            });
        }
        function UploadstartCallback(){
            MaskLoading('Mostrar');
            return true;
        }
        function UploadcompleteCallback(Resposta){
            MaskLoading('Ocultar');
        }
        $(document).ready(function(){
            CarregaCampoDeAnexo();
        });
    </script>


    </body>
    </html>