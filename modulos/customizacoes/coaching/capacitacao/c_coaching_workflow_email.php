<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
		<?php header("Content-Type: text/html; charset=ISO-8859-1");?>
		<script language="JavaScript">
			$(document).ready(function(){ 
				$(".calendario").datepicker({
									   showOn: "button",
									   buttonImage: "../../../../images/agenda.gif",
									   buttonImageOnly: true,
									   changeMonth:true, 
									   changeYear:true
									   });
				$(".calendario").datepicker("option", "dateFormat", "dd/mm/yy"); 
				$(".calendario").val('');
			}); 
		</script>
		 <script type="text/javascript" src="../../../../js/jquery.easing.js"></script>
        <script type="text/javascript" src="../../../../js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="../../../../js/jquery.notify.js"></script>
        <script type="text/javascript" src="../../../../js/AIM.js"></script>
        <script type="text/javascript" src="../../../../js/function.js"></script>                      
	</head>
    <body>
    <form action="c_coaching_workflow_email_post.php" id="form_workflow" name="formWorkflow" method="post" enctype="multipart/form-data" onsubmit="return AIM.submit(this, {'onStart' : UploadstartCallback, 'onComplete' : UploadcompleteCallback});">    
    <table width="100%" border="0" cellspacing="4" cellpadding="6">
          <tr>
            <td width="18%" valign="top" align="center"><img src="imagens/mini.jpg" /></td>
            <td width="21%" valign="top" align="center"><img src="imagens/retencao.jpg" /></td>
            <td width="20%" valign="top" align="center"><img src="imagens/mensagem_do_presidente.png" /></td>
            <td width="19%" valign="top" align="center"><img src="imagens/blog_facebook.jpg" /></td>
            <td width="22%" valign="top" align="center"><img src="imagens/diferenciais_carreira.jpg" /></td>
          </tr>
          <tr>
            <td align="center" valign="middle">
                <label>
                    <input type="checkbox" class="530" name="ola_coach" value="1" id="ola_coach" /><br />
                    Ola Coach
                </label>
                
            </td>
            <td align="center" valign="middle">
                <label>
                    <input type="checkbox" name="lembrete_vencimento" value="1" id="lembrete_vencimento" /><br />
                    Lembrete Vencimento Inscrição
                </label>
            </td>
            <td align="center" valign="middle">
              <label>
                    <input type="checkbox" name="mensagem_presidente" value="1" id="mensagem_presidente" /><br />
               		 Mensagem Presidente</label>
            </td>
            <td align="center" valign="middle">
                <label>
                    <input type="checkbox" name="blog_facebook" value="1" id="blog_facebook" /><br />
                    Blog e Facebook 40 dias
                </label>
            </td>
            <td align="center" valign="middle">
                <label>
                    <input type="checkbox" name="diferenciais" value="1" id="diferenciais" /><br />
                        Diferenciais
                 </label>
             para Carreira</td>
          </tr>
          <tr>
            <td align="center">  
                <input maxlength=10 type="text" name="ola_coach_data" id="" class="calendario"  size="9"  value=""/><br /> 
            </td>
            <td align="center">   
                <input maxlength=10 type="text" name="lembrete_vencimento_data" id="" class="calendario" size="9"  value="" /> 
            </td>
            <td align="center">   
                <input maxlength=10 type="text" name="mensagem_presidente_data" id="" class="calendario"  size="9"  value="" /> 
            </td>
            <td align="center">   
                <input maxlength=10 type="text" name="blog_facebook_data" id="" class="calendario"  size="9"  value=""/> 
            </td>
            <td align="center">   
                <input maxlength=10 type="text" name="diferenciais_data" id=""  class="calendario" size="9"  value="" /> 
            </td>
          </tr>
    </table>
    <input type="hidden" name="inscricao" value="<?=$_POST['pnumreg']?>" />
    <input type="hidden" name="id_pessoa" value="<?=$_POST['id_pessoa']?>" />
</form>

    <script>     
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