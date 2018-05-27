<?php // gera ocoach?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: Gera Ocoach</title>
        <link href="../../../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link href="../../../../estilos_css/estilo_aba.css" rel="stylesheet" type="text/css" />
        <link href="../../../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../../../../estilos_css/cadastro.css">
            <style type="text/css">
                body {
                    margin-left: 0px;
                    margin-top: 0px;
                    margin-right: 0px;
                    margin-bottom: 0px;
                }
			.search-background{position:absolute;width:100%; height:100%; z-index:9000;background-color:#FFF; display:none; top:0}
				
            </style>
            <script language="JavaScript" src="../../../../js/ajax_usuario.js"></script>
            <script language="JavaScript" src="../../../../js/ajax_menus.js"></script>
            <script language="JavaScript" src="../../../../js/ajax_gera_cad.js"></script>
            <script language="JavaScript" src="../../../../js/valida.js"></script>
            <script type="text/javascript" src="../../../../js/function.js"></script>
            <script type="text/javascript" src="../../../../js/jquery.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
            <script language="JavaScript">
   				 jQuery(document).ready(function(){ 

					function loading_show(){
						$('.search-background').fadeIn(1000);
						$('.search-background').fadeTo("slow",0.8);	
					}
					function loading_hide(){
						$('.search-background').fadeOut(10);
			
					}   
										   
					jQuery('#formulario').submit(function(){  
						if(
						   ($("#nome").val() == "") ||
						   ($("#email").val() == "") ||
						   ($("#nome_site").val() == "") ||
						   ($("#curso").val() == "")
						   ){
							alert("Por favor, preencha todos os campos!");
							return false;
						}	
						var dados = jQuery( this ).serialize(); 
						var resultado;
						$('form')[0].reset();
						loading_show(); 
						jQuery.ajax({  
							type: "POST",  
							url: "envia_email_ocoach.php",  
							data: dados,
							
							success: function( data ){
							alert(data);
							loading_hide();
							window.opener.location.reload();
							window.close();

							}  
						});  
						return false;  
					}); 
				});  
        </script>
	<?php
		header("Content-Type: text/html; charset=ISO-8859-1");
		include("../../../../conecta.php");
		$pnumreg = $_REQUEST['pnumreg'];
		$numreg= $_REQUEST['pnpai'];

		$SqlPessoa = "SELECT * FROM IS_PESSOA WHERE numreg = $numreg";
		$QryPessoa = mysql_query($SqlPessoa);
		$ArQryPessoa = mysql_fetch_assoc($QryPessoa);

		if($pnumreg > '0'){

			$SqlOcoach = "SELECT * FROM is_gera_ocoach WHERE numreg =  $pnumreg";
			$QrySqlOcoach = mysql_query($SqlOcoach);
			$ArQrySqlOcoach= mysql_fetch_array($QrySqlOcoach);
	
			$nome_site = $ArQrySqlOcoach['nome_site'];
			$nome_site_update = $ArQrySqlOcoach['nome_site'];
			$email = $ArQrySqlOcoach['email_pessoa_site'];
		}
		else{
			$email = $ArQryPessoa['email'];
		}
		$nome = $ArQryPessoa['razao_social_nome'];
	?>
    </head>
    <body  bgcolor="#F2F2F2" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" onbeforeunload="msg = alteracoes(); if (msg != '') { return msg; }">
    
        <div class="search-background" style=" text-align:center; padding-top:240px">
                <img src="http://www.sbcoaching.com.br/blog/images/loader12.gif" />
        </div> 
            
        <div id="principal_detalhes" >
            <div id="menu_horiz">
                <table>
                    <tr>
                        <td>&nbsp;
                            <span style="font-size:16px; font-weight: bold;">
                               Gera Ocoach</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="conteudo_detalhes">
		<form id="formulario" action="javascript:function()" method="post">
        <input type="hidden" name="numreg" id="numreg" value="<?=$numreg;?>" />    
        <input type="hidden" name="pnumreg" id="pnumreg" value="<?=$pnumreg;?>" />    
        <input type="hidden" name="nome_site_update" id="nome_site_update" value="<?=$nome_site_update;?>" />    

<table width="100%"  border="0" cellspacing="1" cellpadding="1" bgcolor="#FFFFFF"  height=500>

<tr>

<td width=100 align="left" valign="top" bgcolor="#345C7D" >

<div id="menu_btn">

<a href="">

<img border=0 src="../../../../images/menu_principal.png" align="middle" width="14" height="13" style="padding-right:3px; padding-left:3px;" />

Cad.Principal</a></div>

</div>



<div id="menu_btn">

<a href="javascript:window.close();">

<img  border=0 src="../../../../images/menu_sair.png" align="middle" width="14" height="13" style="padding-right:3px; padding-left:3px;" />

Fechar</a></div>

</div>



</td>

<td valign="top">

<div name="div_programa" id="div_programa" class="tabbed_box" >

<div class="tabbed_area">

<ul class="tabs">

<li id="li_01principal"><a href="content_01principal" title="01.Principal" class="tab active">01.Principal</a></li>
</ul>


<div id="content_01principal" class="content">
    <table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#FFFFFF"><tr><td bgcolor="#dbe9f4" class="sub_tit" colspan="4">
    <div align="left">Gera Ocoach</div></td></tr><tr><td colspan="4"  >&nbsp;
    
    </td>
    </tr>
    
    <tr id="tr_id_pessoa">
        <td >&nbsp;</td>
        <td align="right" width="18%" >
            <div id="div_lblid_pessoa" style="display: inline;">
                <b><span style="color:#0000FF"  title="Ordem = 20">Nome</span></b>
            </div>
        </td>
        <td width="1%" >&nbsp;</td>
        <td width="76%" >

            <div align="left" id="div_edtid_pessoa" style="display: inline;">
                <font face="Verdana" size="1">
                    <input type="text" name="nome" id="nome" size="62" value="<?=$nome?>" readonly style="background-color:#CCCCCC" > 
                </font>
            </div>
        </td>
    </tr>
    
    <tr id="tr_id_relac">
        <td >&nbsp;</td>
        <td align="right" width="18%" ><div id="div_lblid_relac" style="display: inline;"><b><span style="color:#0000FF"  title="Ordem = 22">Email</span></b></div></td><td width="1%" >&nbsp;</td>
        <td width="76%" >
            <div align="left" id="div_edtid_relac" style="display: inline;"><div id="divid_relac" style="display: inline;">
                    <input type="text" name="email" id="email" size="62" value="<?=$email?>" > 
            </div>
            <font face="Verdana" size="1">&nbsp;</font>
            </div>
        </td>
    </tr>
    <tr id="tr_id_pessoa_dest">
        <td >&nbsp;</td>
        <td align="right" width="18%" >
        <div id="div_lblid_pessoa_dest" style="display: inline;"><b><span style="color:#0000FF"  title="Ordem = 23">Nome do site</span></b>
        </div>
        </td>
        <td width="1%" >&nbsp;</td>
        <td width="76%" >
        <div align="left" id="div_edtid_pessoa_dest" style="display: inline;">
            <input type="text" name="nome_site" id="nome_site" size="62" value="<?=$nome_site?>"  > 
        </div>
        </td>
    </tr>
    
    <tr id="tr_id_pessoa_dest">
        <td >&nbsp;</td>
        <td align="right" width="18%" >
        <div id="div_lblid_pessoa_dest" style="display: inline;"><b><span style="color:#0000FF"  title="Ordem = 23">Curso</span></b>
        </div>
        </td>
        <td width="1%" >&nbsp;</td>
        <td width="76%" >
        <?php $ArSqlCurso="
				SELECT cInscricao.id_pessoa, cCurso.nome_curso, cCurso.numreg
					FROM c_coaching_inscricao AS cInscricao
					INNER JOIN c_coaching_curso AS cCurso
				ON cInscricao.id_curso = cCurso.numreg
				WHERE cInscricao.id_pessoa = $numreg";
			  $QryArSqlCurso= mysql_query($ArSqlCurso);
		?>
        <div align="left" id="div_edtid_pessoa_dest" style="display: inline;">
      <?php  echo $ArQryArSqlCurso['nome_curso'];?>
        	<select name="curso" id="curso" >
            <option value='Personal & Professional Coaching'>Selecione...</option>
            <?php
			while($ArQryArSqlCurso= mysql_fetch_array($QryArSqlCurso)){
				echo '<option value='.$ArQryArSqlCurso['nome_curso'].'>'.$ArQryArSqlCurso['nome_curso'].'</option>';
			}?>
			</select> 
          
        </div>
        </td>
    </tr>    
    
    </div>
    </table>
    </div>
    <table width="100%" border="0" cellspacing="2" cellpadding="2" bgcolor="#FFFFFF">
        <tr>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="4" align="center">
                <div style="height:1px; width:100%; background-color:#E0E0E0; margin-top:2px; margin-bottom:2px;">
                </div>
                <input name="btnSubmit" type="submit" class="botao_form" value="Salvar" style="cursor:pointer;" /> 
                &nbsp;
                <input type="button" value="Fechar" name="B4" class="botao_form" onclick="javascript:window.close();" style="cursor:pointer;" > 
                <div style="height:1px; width:100%; background-color:#E0E0E0; margin-top:2px; margin-bottom:2px;">
                </div>
            </td>
        </tr>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
        </tr>
        </td>
        </tr>
        </div>
    </table>
    
</table>
</div>
</form>
</body>
</html>