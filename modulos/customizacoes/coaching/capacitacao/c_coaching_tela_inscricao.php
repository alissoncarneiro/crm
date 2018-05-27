<?php
/*
 * c_coaching_tela_inscricao.php
 * Autor: Alex
 * 20/06/2011 10:52:01
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

session_start();
if($_SESSION['id_usuario'] == ''){
    echo 'Usuário não logado.';
    exit;
}

require('../../../../conecta.php');
require('../../../../functions.php');
require('../../../../classes/class.GeraCadCampos.php');
require('../../../../classes/class.Url.php');
require('c_coaching.class.Inscricao.php');

$IdInscricao = ($_GET['pnumreg'] == '-1')?'':$_GET['pnumreg'];

$Inscricao = new Inscricao($IdInscricao);

class c_GeraCadCampos extends GeraCadCampos{
    
    private $ObjInscricao;
    
    public function __construct($IdCadastro, $ArGET,Inscricao $ObjInscricao){
        $this->ObjInscricao = $ObjInscricao;
        parent::__construct($IdCadastro, $ArGET);
    }
    
    public function CampoCustom($IdCampo,$IdCadastro){
        if($IdCampo == 'dt_inscricao'){
            $this->setPropriedadeCampo($IdCampo,'maxdate','0');
        }
        if($IdCampo == 'id_curso' || $IdCampo == 'id_pessoa'|| $IdCampo == 'id_pessoa_financeiro' || $IdCampo == 'dt_inscricao' || $IdCampo == 'hr_inscricao'){
            if($this->ObjInscricao->getDadosInscricao('sn_dados_confirmados') == 1){        
                $this->setPropriedadeCampo($IdCampo, 'editavel', 0);
            }
            else{
                $this->setPropriedadeCampo($IdCampo, 'editavel', 1);                
            }
        }
        if($IdCampo == 'id_vendedor' && $this->ObjInscricao->getDadosInscricao('sn_dados_confirmados') == 0){
            $this->setPropriedadeCampo($IdCampo, 'editavel', 1);
            $this->setPropriedadeCampo($IdCampo, 'sql_lupa', 'select numreg,nome_usuario from is_usuario where id_perfil in (3,4,13) or numreg = 1');
        }        
    }
}

$CadastroAgendaCurso = new c_GeraCadCampos('c_coaching_inscricao',$_GET,$Inscricao);
$CadastroAgendaCurso->setCaminhoBase('../../../../');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>Incluir Inscri&ccedil;&atilde;o</title>
        <link href="../../../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link href="../../../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />
        <link href="../../../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../../../css/ui.notify.css" rel="stylesheet" type="text/css" />
        <link href="../../../../css/enhanced.css" rel="stylesheet" type="text/css" />
        <link href="c_style.css" rel="stylesheet" type="text/css" />
        <link href="../../../venda/estilo_venda.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../../js/jquery.qtip.js"></script>
        <script type="text/javascript" src="../../../../js/jquery.easing.js"></script>
        <script type="text/javascript" src="../../../../js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="../../../../js/jquery.ui.datepicker-pt-BR.js"></script>
        <script type="text/javascript" src="../../../../js/jquery.notify.js"></script>
        <script type="text/javascript" src="../../../../js/jquery.meio.mask.min.js"></script>
        <script type="text/javascript" src="../../../../js/function.js"></script>
        <script type="text/javascript">
            IMGLoadingGeralCustom = '<div align="center"><img src="../../../../images/ajax_loading_bar.gif" alt="Carregando..." /><br /><strong>Carregando...</strong></div>';
            function RecarregaGradeAgendas(IdCurso,IdParte,IdModulo,IdLocalCurso,IdHotel,IdInstrutor,DtDe,DtAte){
                $.ajax({
                    url: "c_coaching_grade_agenda.php",
                    global: false,
                    type: "POST",
                    data: ({
                        id_inscricao: '<?php echo $IdInscricao;?>',
                        id_curso: IdCurso,
                        id_parte: IdParte,
                        id_modulo: IdModulo,
                        id_local_curso: IdLocalCurso,
                        id_hotel: IdHotel,
                        id_instrutor: IdInstrutor,
                        dt_de: DtDe,
                        dt_ate: DtAte
                    }),
                    dataType: "html",
                    async: true,
                    beforeSend: function(){
                        $("#div_grade_agendas").html(IMGLoadingGeralCustom);
                    },
                    error: function(){
                        alert("Erro com a requisição");
                        $("#div_grade_agendas").html(IMGLoadingGeralCustom);
                    },
                    success: function(responseText){
                        $("#div_grade_agendas").html(responseText);
                    }
                });
                return;
            }
            
            function RecarregaGradeAgendasSelecionadas(){
                $.ajax({
                    url: "c_coaching_grade_agenda_selecionada.php",
                    global: false,
                    type: "POST",
                    data: ({
                        id_inscricao: '<?php echo $IdInscricao;?>'
                    }),
                    dataType: "html",
                    async: true,
                    beforeSend: function(){
                        $("#div_grade_agendas_selecionadas").html(IMGLoadingGeralCustom);
                    },
                    error: function(){
                        alert("Erro com a requisição");
                        $("#div_grade_agendas_selecionadas").html('');
                    },
                    success: function(responseText){
                        $("#div_grade_agendas_selecionadas").html(responseText);
                    }
                });
                return;
            }
            
            function RecarregaGradePagto(){
                $.ajax({
                    url: "c_coaching_grade_pagto.php",
                    global: false,
                    type: "POST",
                    data: ({
                        id_inscricao: '<?php echo $IdInscricao;?>'
                    }),
                    dataType: "html",
                    async: true,
                    beforeSend: function(){
                        $("#div_grade_pagto").html(IMGLoadingGeralCustom);
                    },
                    error: function(){
                        alert("Erro com a requisição");
                        $("#div_grade_pagto").html('');
                    },
                    success: function(responseText){
                        $("#div_grade_pagto").html(responseText);
                    }
                });
                return;
            }
			
			
			function acoes_finaliza_inscricao(){
				btn_acoes = '<input type="button" id="acoes_inscricao" onclick="acoes_finaliza_inscricao()" class="botao_jquery" value="Ações Inscrição" />';
				
				
				var Dialog = $("#finaliza_inscricao");
				$("#btn_acoes_inscricao").html(btn_acoes);
				$(".botao_jquery").button();
				$("#finaliza_inscricao").attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Finaliza Inscrição');
				
				$.ajax({
					url: "c_coaching_finaliza_inscricao.php",
					global: false,
					type: "POST",
					data: ({
						id_inscricao: '<?php echo $IdInscricao;?>',
						id_pessoa: $("#edtid_pessoa").val()														
						
					}),
					dataType: "html",
					async: true,

					 beforeSend: function(){
                        $("#finaliza_inscricao").html(IMGLoadingGeralCustom);
                    },
                    error: function(){
                        alert("Erro com a requisição");
                        $("#finaliza_inscricao").html('');
                    },
                    success: function(responseText){
                        $("#finaliza_inscricao").html(responseText);
                    }					
				});	
				
				$("#finaliza_inscricao").dialog({
					zIndex: 1,
					height: 536,
					width:  776,													
					modal: true,
					show: "fade",
					hide: "fade",
					close: function(){$("#finaliza_inscricao").dialog("destroy");}					
				 });				
				
			}
            
            $(document).ready(function(){
                $(".botao_jquery").button();
                
                $("#btn_confirmar_cliente").click(function(){
                    var IdCurso,IdPessoa,IdPessoaFinanceiro,OptionCurso;
                    IdCurso = $("#edtid_curso").val();
                    IdPessoa = $("#edtid_pessoa").val();
                    IdPessoaFinanceiro = $("#edtid_pessoa_financeiro").val();
                    if(IdCurso == ''){
                        alert('Curso deve ser informado!');
                        return false;
                    }
                    if(IdPessoa == '' || IdPessoa == 0){
                        alert('Cliente deve ser informado!');
                        return false;
                    }
                    $.ajax({
                        url: "c_coaching_tela_inscricao_post.php",
                        global: false,
                        type: "POST",
                        data: ({
                            id_requisicao: 1,
                            id_curso: $("#edtid_curso").val(),
                            id_pessoa: $("#edtid_pessoa").val(),
                            id_pessoa_financeiro: $("#edtid_pessoa_financeiro").val(),
                            dt_inscricao: $("#edtdt_inscricao").val(),
                            hr_inscricao: $("#edthr_inscricao").val(),
                            id_vendedor: $("#edtid_vendedor").val(),
                            url_retorno: escape($("#url_retorno").val())
                        }),
                        dataType: "xml",
                        async: true,
                        beforeSend: function(){

                        },
                        error: function(){
                            alert("Erro com a requisição");
                        },
                        success: function(xml){
                            var resposta    = $(xml).find('resposta');
                            var Status      = resposta.find('status').text();
                            var Acao        = resposta.find('acao').text();
                            var Url         = resposta.find('url').text();
                            var Mensagem    = resposta.find('mensagem').text();
                            if(Status == 1){
                                $("#jquery-dialog").attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Alerta');
                                $("#jquery-dialog").html(Mensagem);
                                $("#jquery-dialog").dialog({
                                    buttons:{Ok: function(){
                                            $(this).dialog("close");
                                            MaskLoading('Mostrar');
                                            if(Acao == 2){
                                                window.location = Url;
                                            }
                                    }},
                                    modal: true,
                                    show: "fade",
                                    hide: "fade"
                                });
                            }
                            else if(Status == 2){
                                $("#jquery-dialog").attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Alerta');
                                $("#jquery-dialog").html(Mensagem);
                                $("#jquery-dialog").dialog({
                                    buttons:{Ok: function(){$(this).dialog("close");}},
                                    modal: true,
                                    show: "fade",
                                    hide: "fade",
									width: 500
                                });
                            }
                        }
                    });
                });
                
                $("#btn_confirmar_inscricao").click(function(){
                    if(confirm("Todos os dados estão corretos ?")){
                        $.ajax({
                            url: "c_coaching_tela_inscricao_post.php",
                            global: false,
                            type: "POST",
                            data: ({
                                pnumreg: $("#pnumreg").val(),
                                id_requisicao: 4,
                                vl_total_inscricao: $("#edtvl_total_inscricao").val(),
								id_curso: $("#edtid_curso").val(),
                                obs: escape($("#edtobs").val())
                            }),
                            dataType: "xml",
                            async: true,
                            beforeSend: function(){
                                
                            },
                            error: function(){
                                alert("Erro com a requisição");
                                
                            },
                            success: function(xml){
                                var resposta    = $(xml).find('resposta');
                                var Status      = resposta.find('status').text();
                                var Acao        = resposta.find('acao').text();
                                var Url         = resposta.find('url').text();
                                var Mensagem    = resposta.find('mensagem').text();
                                if(Status == 1){
                                    $("#jquery-dialog").attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Alerta');
                                    $("#jquery-dialog").html(Mensagem);
                                    $("#jquery-dialog").dialog({
                                        buttons:{Ok: function(){
											$(this).dialog("close");
											if(Acao == 3){
												acoes_finaliza_inscricao();	
											}
                                        }},
										
                                        modal: true,
                                        show: "fade",
                                        hide: "fade"
                                    });
										
                                }
                                else if(Status == 2){
                                    $("#jquery-dialog").attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Alerta');
                                    $("#jquery-dialog").html(Mensagem);
                                    $("#jquery-dialog").dialog({
                                        buttons:{Ok: function(){$(this).dialog("close");}},
                                        modal: true,
                                        show: "fade",
                                        hide: "fade",
										width: 500
                                    });
                                }
                            }
                        });
                    }
                });
                    
                <?php if($Inscricao->getDadosInscricao('sn_dados_confirmados') == 1){?>
                RecarregaGradeAgendas($("#edtid_curso").val(),'','','');
                RecarregaGradeAgendasSelecionadas();
                RecarregaGradePagto();
                <?php } ?>
            });
        </script>
    </head>

    <body>
        <div id="jquery-dialog"></div>
        <input type="hidden" name="pnumreg" id="pnumreg" value="<?php echo $IdInscricao;?>"/>
        <input type="hidden" name="url_retorno" id="url_retorno" value="<?php echo curPageURL();?>"/>
        <fieldset><legend>Incluir Inscri&ccedil;&atilde;o</legend>
            <table cellpadding="0" cellspacing="2" width="100%">
                <tr>
                    <td align="right" width="100"><strong><?php echo $CadastroAgendaCurso->getLabelCampo('id_curso');?></strong></td>
                    <td>
                        <?php echo $CadastroAgendaCurso->getHTMLCampo('id_curso',$Inscricao->getDadosInscricao('id_curso'));?>
                        &nbsp;<strong><?php echo $CadastroAgendaCurso->getLabelCampo('dt_inscricao');?></strong>
                        <?php echo $CadastroAgendaCurso->getHTMLCampo('dt_inscricao',dten2br($Inscricao->getDadosInscricao('dt_inscricao')));?>
                        &nbsp;<strong><?php echo $CadastroAgendaCurso->getLabelCampo('hr_inscricao');?></strong>
                        <?php echo $CadastroAgendaCurso->getHTMLCampo('hr_inscricao',$Inscricao->getDadosInscricao('hr_inscricao'));?>
                    </td>
                </tr>
                <tr>
                    <td align="right"><strong><?php echo $CadastroAgendaCurso->getLabelCampo('id_pessoa');?></strong></td>
                    <td><?php echo $CadastroAgendaCurso->getHTMLCampo('id_pessoa',$Inscricao->getDadosInscricao('id_pessoa'));?></td>
                </tr>
                <tr>
                    <td align="right"><strong><?php echo $CadastroAgendaCurso->getLabelCampo('id_pessoa_financeiro');?></strong></td>
                    <td><?php echo $CadastroAgendaCurso->getHTMLCampo('id_pessoa_financeiro',$Inscricao->getDadosInscricao('id_pessoa_financeiro'));?></td>
                </tr>
                <tr>
                    <td align="right"><strong><?php echo $CadastroAgendaCurso->getLabelCampo('id_vendedor');?></strong></td>
                    <td><?php echo $CadastroAgendaCurso->getHTMLCampo('id_vendedor',$Inscricao->getDadosInscricao('id_vendedor'));?> <strong><?php echo $CadastroAgendaCurso->getLabelCampo('id_usuario_cad');?></strong> <?php echo $CadastroAgendaCurso->getHTMLCampo('id_usuario_cad',$Inscricao->getDadosInscricao('id_usuario_cad'));?></td>
                </tr>
                <?php if($Inscricao->getDadosInscricao('sn_dados_confirmados') == 0){?>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="button" id="btn_confirmar_cliente" class="botao_jquery" value="Confirmar Dados"/></td>
                </tr>
                <?php } ?>
                <tr>
                    <td colspan="2">
                        <fieldset><legend>Filtrar Agendas</legend>
                            <div id="div_grade_agendas"></div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                <tr>
                    <td colspan="2">
                        <fieldset><legend>Agendas Selecionadas</legend>
                            <div id="div_grade_agendas_selecionadas"></div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <fieldset><legend>Pagamento</legend>
                            <p><strong>Valor Total: </strong><input type="text" class="c_campo_vl" name="edtvl_total_inscricao" id="edtvl_total_inscricao"/></p>
                            <div id="div_grade_pagto"></div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td align="right"><strong>Observa&ccedil;&atilde;o:</strong></td>
                    <td><textarea cols="70" rows="4" name="edtobs" id="edtobs"></textarea></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                  <td id="btn_acoes_inscricao"><input type="button" id="btn_confirmar_inscricao" class="botao_jquery" value="Confirmar Inscrição" /></td>
                </tr>
            </table>
        </fieldset>
        <div id="finaliza_inscricao"></div>    </body>
</html>