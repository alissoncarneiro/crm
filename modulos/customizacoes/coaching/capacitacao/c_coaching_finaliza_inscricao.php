<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <?php 
	header("Content-Type: text/html; charset=ISO-8859-1");
	header("Pragma: no-cache");
	$IconeTamanho = 100;
	$IconeClasse = 'dicn';
	$id_pessoa = $_POST['id_pessoa'];
	$id_inscricao= $_POST['id_inscricao'];
	?>
	<link href="../../../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />
	<link href="../../../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
	<!--<script type="text/javascript" src="../../../../js/jquery.js"></script>-->
	<script type="text/javascript" src="../../../../js/jquery-ui-1.8.5.custom.min.js"></script>
	<script type="text/javascript" src="../../../../js/function.js"></script>
	<? require("../../../../conecta.php");?>
	<script>
    $(document).ready(function(){
        $("#btn_documentos").click(function(){
            <?php
            $SqlNumregMestreDetalheAtividade = "SELECT numreg FROM is_gera_cad_sub WHERE id_funcao_mestre = 'c_coaching_inscricao' AND id_funcao_detalhe = 'arquivos_cad'";
            $QryNumregMestreDetalheAtividade = mysql_query($SqlNumregMestreDetalheAtividade);
            $ArNumregMestreDetalheAtividade = mysql_fetch_array($QryNumregMestreDetalheAtividade);
            $NumregMestreDetalheAtividade = $ArNumregMestreDetalheAtividade['numreg'];
            ?>											
            var Url = '../../../../gera_cad_lista.php?pfuncao=arquivos_cad&pfixo=id_inscricao@igual@s<?php echo $id_inscricao;?>@s&psubdet=<?php echo $NumregMestreDetalheAtividade;?>&pnpai=<?php echo $id_inscricao;?>&pdrilldown=1';
            window.open(Url,'arquivos_<?php echo $id_inscricao;?>','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=800,height=600,top=250,left=250');
        });

		$("#btn_atividades").click(function(){
            <?php
            $SqlNumregMestreDetalheAtividade = "SELECT numreg FROM is_gera_cad_sub WHERE id_funcao_mestre = 'c_coaching_inscricao' AND id_funcao_detalhe = 'atividades_cad_lista'";
            $QryNumregMestreDetalheAtividade = query($SqlNumregMestreDetalheAtividade);
            $ArNumregMestreDetalheAtividade = farray($QryNumregMestreDetalheAtividade);
            $NumregMestreDetalheAtividade = $ArNumregMestreDetalheAtividade['numreg'];
            ?>
            var Url = '../../../../gera_cad_lista.php?pfuncao=atividades_cad_lista&pfixo=id_inscricao@igual@s<?php echo $id_inscricao;?>@s&psubdet=<?php echo $NumregMestreDetalheAtividade;?>&pnpai=<?php echo $id_inscricao;?>&pdrilldown=1';
            window.open(Url,'atividades_<?php echo $id_inscricao;?>','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=800,height=600,top=250,left=250');			
        });
		
		$("#btn_imprimir").click(function(event){
			var Dialog = $("#jquery-imprimir");
			Dialog.attr("title",'<span style="float:left;" ></span>&nbsp;Selecionar Modelo');
			Dialog.dialog({
				zIndex: 1002, 			  
				buttons:{
					'Selecionar':function(){
						var width=700;
						var height=600;
						var left=100;
						var top=100;
						window.open('../../../../modulos/venda/gera_modelo/c_coaching_exibe_modelo_impressao.php?pnumreg=<?php echo $id_inscricao;?>&id_modelo='+$("#select_id_modelo").val(),'modelo_impressao','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,width=' + width + ',height=' + height + ',left=' + left + ',top = ' + top).focus();
						
					},
					Fechar: function(){
						$("#jquery-imprimir").dialog("close");
					}
				},
				open: function(){
					$.ajax({
						url: "c_coaching_seleciona_modelo_impressao.php",
						global: false,
						type: "POST",
						data: ({
							pnumreg:'<?php echo $id_inscricao;?>'
						}),
						dataType: "html",
						async: true,
						beforeSend: function(){

						},
						error: function(){
							alert('Erro com a requisição');
						},
						success: function(responseText){
							Dialog.html(responseText);
						}
					});
				},
				modal: true,
				show: "fade",
				hide: "fade",
				close: function(){
					$("#jquery-imprimir").dialog("destroy");
					$("finaliza_inscricao").remove();
				}
			});
        });
        
	$("#btn_enviar_email").click(function(event){
			var Dialog = $("#jquery-envia-email");
		Dialog.attr("title",'<span style="float:left;" ></span>&nbsp;Selecionar Modelo de impressão no e-mail');
		Dialog.dialog({
			zIndex: 1001, 
			width: 846,
			height: 560,
			buttons:{
				'Enviar E-mail':function(){
					$("#form_envio_email").submit();
				},
				Fechar: function(){
					$("#jquery-envia-email").dialog("close");
				}        
			},
			open: function(){
				$.ajax({
					url: "c_coaching_seleciona_envio_email.php",
					global: false,
					type: "POST",
					data: ({
						pnumreg:'<?php echo $id_inscricao;?>',
						id_pessoa:'<?php echo $id_pessoa;?>'
					}),
					dataType: "html",
					async: true,
					beforeSend: function(){
	
					},
					error: function(){
						alert('Erro com a requisição');
					},
					success: function(responseText){
						Dialog.html(responseText);
					}
				});
			},
			modal: true,
			show: "fade",
			hide: "fade",
			close: function(){$("#jquery-envia-email").dialog("destroy");}
			});
		});
	
	$("#btn_workflow").click(function(event){
		var Dialog = $("#jquery-agendamento");
		Dialog.attr("title",'<span style="float:left;" ></span>&nbspAgendamento de E-mails');
		Dialog.dialog({
			zIndex: 1010, 
			width: 690,
			height: 400,
			buttons:{
				'Salvar WorkFlow':function(){
					$("#form_workflow").submit();
				},
				Fechar: function(){
					$("#jquery-agendamento").dialog("close");
				}        
			},
			open: function(){
				$.ajax({
					url: "c_coaching_workflow_email.php",
					global: false,
					type: "POST",
					data: ({
						pnumreg:'<?php echo $id_inscricao;?>',
						id_pessoa:'<?php echo $id_pessoa;?>'
					}),
					dataType: "html",
					async: true,
					beforeSend: function(){
	
					},
					error: function(){
						alert('Erro com a requisição');
					},
					success: function(responseText){
						Dialog.html(responseText);
					}
				});
			},
			modal: true,
			show: "fade",
			hide: "fade",
			close: function(){$("#jquery-agendamento").dialog("destroy");}
			});
		});	
				
		
		$("#btn_sair").click(function(event){
			$("#finaliza_inscricao").dialog("destroy");
			
		 });
    });
</script>

<style>
#acoes_finaliza a{display:inline-block; vertical-align:middle; margin:20px; text-decoration:none;  color:#000; border:solid 1px #CCC; padding:20px; width:150px; height:150px; font-size:12px}
</style>
</head>
<body>


<div id="jquery-dialog"></div>
<div id="jquery-envia-email"></div>
<div id="jquery-imprimir"></div>
<div id="jquery-agendamento"></div>

<input type="hidden" name="pnumreg" id="pnumreg" value="<?php echo $_POST['id_pessoa'];?>" />
<fieldset style="text-align:center;"><legend>A&ccedil;&otilde;es da Inscri&ccedil;&atilde;o</legend>
<div id="acoes_finaliza">
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_enviar_email">
        <img src="../../../../modulos/venda/img/enviar_email.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Enviar por E-Mail" title="Enviar por E-Mail" />
        <p>Enviar por E-Mail</p>
    </a>

    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_imprimir">
        <img src="../../../../modulos/venda/img/imprimir.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Impress&atilde;o da Inscrição" title="Impress&atilde;o da Inscrição" />
        <p>Impress&atilde;o da Inscri&ccedil;&atilde;o</p>
    </a>


    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_atividades">
    <img src="../../../../modulos/venda/img/atividades.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Atividades Relacionadas" title="Atividades Relacionadas" />
    <p>Atividades Relacionadas</p>
    </a>
    
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_documentos">
        <img src="../../../../modulos/venda/img/documentos.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Documentos Relacionados" title="Documentos Relacionados" />
    <p>Documentos Relacionados</p>
    </a>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_workflow">
        <img src="../../../../modulos/venda/img/iconWorkFlow.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Agendamento E-mails" title="Agendamento E-mails" />
    <p>Agendamento E-mails</p>
    </a>    

	    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_sair">
        <img src="../../../../modulos/venda/img/sair.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Sair" title="Sair" />
        <p>Sair</p>
    </a>
</div>
</fieldset>
</body>
</html>