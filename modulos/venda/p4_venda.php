<?php
/*
 * p4_venda.php
 * Autor: Alex
 * 04/11/2010 21:06
 */
/* Definindo o tamanho padrao dos icones */
$IconeTamanho = 128;
$IconeClasse = 'dicn';

$Url = new Url();
$Url->setUrl(curPageURL());
if(!is_object($Venda)){
    $Venda = new Venda($TipoVenda,$NumregVenda);
    exit;
}
if(!$Venda->getDigitacaoCompleta()){
    $Venda->ValidaPoliticaComercialDesc();
}
elseif($Venda->getTipoVenda() == 1 && !$Venda->getEmAprovacao() && !$Venda->getGerouPedido() && $Venda->getDigitacaoCompleta()){
    $Venda->ValidaPoliticaComercialDesc();
}
/*
 * Calculando a comissão caso ainda não esteja finalizado
 */
if(!$Venda->getDigitacaoCompleta()){
    $Venda->CalculaComissaoItens();
    $Venda->AtualizaComissaoItensBD();
    $Venda->ValidaPoliticaBloqueioFinalizacao();
}

$PermiteSalvarTransformarEmPedido = true; //Variável que define se o pedido poderá ser salvo ou não
$PermiteFinalizarOrcamento = true; //Variável que define se o orçamento poderá finalizado
$MsgBotaoFinalizar = '';
$MsgAlertaBotaoFinalizar = '';
if($Venda->getQtdeItens() == 0){ /* Validando se possui itens */
    $PermiteSalvarTransformarEmPedido = false;
    $PermiteFinalizarOrcamento = false;
    $MsgBotaoFinalizar = 'Não permitido';
    $MsgAlertaBotaoFinalizar = ucwords($Venda->getTituloVenda(false)).' não possui itens. Não é permitido salvar sem itens.';
}
elseif($Venda->isTipoBonificacao() && !$Venda->getStatusPoliticaComercialDesc() && !$VendaParametro->getSnPermiteEnviarPedidoBonificacaoParaAprovacao()){ /* Validando se é bonificação e se esta dentro da politica comercial */
    $PermiteSalvarTransformarEmPedido = false;
    $MsgBotaoFinalizar = 'Não permitido';
    $MsgAlertaBotaoFinalizar = 'Pedido está fora da política comercial e não é permitido enviar pedidos de bonificação para aprovação.';
}
elseif($Venda->isTipoBonificacao() && $Venda->getDadosVenda('sn_gerado_bonificacao_auto') == '1' && !$Venda->VerificaItensObrigatoriosBonificacao()){
    $PermiteSalvarTransformarEmPedido = false;
    $MsgBotaoFinalizar = 'Não permitido';
    $MsgAlertaBotaoFinalizar = 'Pedido não possui todos os itens obrigatórios.';
}
elseif($Venda->PossuiItensSemCFOP()){
    $PermiteSalvarTransformarEmPedido = false;
    $PermiteFinalizarOrcamento = false;
    $MsgBotaoFinalizar = 'Não permitido';
    $MsgAlertaBotaoFinalizar = ucwords($Venda->getTituloVenda(false)).' possui itens sem Cód. CFOP. Não é permitido nestas condições.';
}
elseif($Venda->PossuiItensSemReferencia() && !$VendaParametro->getPermiteAdicionarItemSemReferencia()){
    $PermiteSalvarTransformarEmPedido = false;
    $MsgBotaoFinalizar = 'Não permitido';
    $MsgAlertaBotaoFinalizar = ucwords($Venda->getTituloVenda(false)).' possui itens sem Cód. de Referência. Não é permitido nestas condições.';
}
elseif($Venda->PossuiItensSemPreco()){
    $PermiteSalvarTransformarEmPedido = false;
    $PermiteFinalizarOrcamento = false;
    $MsgBotaoFinalizar = 'Não permitido';
    $MsgAlertaBotaoFinalizar = ucwords($Venda->getTituloVenda(false)).' possui itens sem preço. Não é permitido nestas condições.';
}
elseif($Venda->PossuiItensInativos()){
    $PermiteSalvarTransformarEmPedido = false;
    $PermiteFinalizarOrcamento = false;
    $MsgBotaoFinalizar = 'Não permitido';
    $MsgAlertaBotaoFinalizar = ucwords($Venda->getTituloVenda(false)).' possui itens inativos. Não é permitido nestas condições.';
}
elseif($Venda->isTipoBonificacao() && $Venda->getDadosVenda('sn_gerado_bonificacao_auto') == '1' && $Venda->getVlTotalVendaLiquido() > $Venda->getVlBonificacaoComTolerancia()){
    $PermiteSalvarTransformarEmPedido = false;
    $MsgBotaoFinalizar = 'Não permitido';
    $MsgAlertaBotaoFinalizar = ucwords($Venda->getTituloVenda(false)).' possui o valor de '.$Venda->NFV($Venda->getVlTotalVendaLiquido()).' que é superior ao máximo permitido('.$Venda->NFV($Venda->getVlBonificacaoComTolerancia()).').';
}
elseif(!$Venda->getPessoa()->getPermiteFazerPedido()){
    $PermiteSalvarTransformarEmPedido = false;
    $MsgBotaoFinalizar = 'Não permitido';
    $MsgAlertaBotaoFinalizar = 'A conta selecionada não pode fazer pedidos.';
}
elseif($Venda->getPessoa()->isSuspect() || $Venda->getPessoa()->isProspect()){
    $PermiteSalvarTransformarEmPedido = false;
    $MsgBotaoFinalizar = 'Não permitido';
    $MsgAlertaBotaoFinalizar = 'A conta selecionada não possui o status de cliente. Transforme a conta em um cliente para poder Salvar/Finalizar a venda.<hr size="1"/>';
    $MsgAlertaBotaoFinalizar .= 'Clique <a href="#" onClick="javascript:link_abrir_detalhe_conta('.$Venda->getPessoa()->getNumregPessoa().');">aqui</a> para abrir a conta';
}
elseif(!$Venda->getPermiteFinalizar()){
    $PermiteSalvarTransformarEmPedido = false;
    $PermiteFinalizarOrcamento = false;
    $MsgBotaoFinalizar = 'Não permitido';
    $MsgAlertaBotaoFinalizar = 'Não permitido. Por favor verificar a política comercial no passo 3.';
}
else{
    if(!$Venda->getStatusPoliticaComercialDesc()){
        $MsgBotaoFinalizar = 'Fora da política comercial.';
    }
}

$SnPermiteClonarVenda = true;
if(!$VendaParametro->getSnPermiteClonarVenda() || $Venda->isTipoBonificacao()){
    $SnPermiteClonarVenda = false;
}
elseif($Venda->getDadosVenda('sn_permite_copia') == '0'){
    $SnPermiteClonarVenda = false;
}
if($Venda->getEmAprovacao()){
    $MsgAprovacao = '';
    $PermiteAprovarVenda = $Venda->getPermiteAprovar();
    if(!$PermiteAprovarVenda){
        $MsgAprovacao = 'Não é permitido aprovar devido os descontos serem maiores que o permitido para o usuário logado.';
    }
}

/* Definições customizadas de finalização */
$ArrayDefinicoes = array(
    'PermiteSalvarTransformarEmPedido'  => $PermiteSalvarTransformarEmPedido,
    'PermiteFinalizarOrcamento'         => $PermiteFinalizarOrcamento,
    'MsgBotaoFinalizar'                 => $MsgBotaoFinalizar,
    'MsgAlertaBotaoFinalizar'           => $MsgAlertaBotaoFinalizar
);
$DefinicoesCustom = VendaCallBackCustom::ExecutaVenda($Venda, 'DefinicoesBotoesPasso4', '',array('ArrayDefinicoes' => $ArrayDefinicoes));
if($DefinicoesCustom !== true){
    $PermiteSalvarTransformarEmPedido   = (array_key_exists('PermiteSalvarTransformarEmPedido',$DefinicoesCustom))?$DefinicoesCustom['PermiteSalvarTransformarEmPedido']:$PermiteSalvarTransformarEmPedido;
    $PermiteFinalizarOrcamento          = (array_key_exists('PermiteFinalizarOrcamento',$DefinicoesCustom))?$DefinicoesCustom['PermiteFinalizarOrcamento']:$PermiteFinalizarOrcamento;
    $MsgBotaoFinalizar                  = (array_key_exists('MsgBotaoFinalizar',$DefinicoesCustom))?$DefinicoesCustom['MsgBotaoFinalizar']:$MsgBotaoFinalizar;
    $MsgAlertaBotaoFinalizar            = (array_key_exists('MsgAlertaBotaoFinalizar',$DefinicoesCustom))?$DefinicoesCustom['MsgAlertaBotaoFinalizar']:$MsgAlertaBotaoFinalizar;
}
?>
<script src="js/p4_venda.js"></script>
<script>
    $(document).ready(function(){
        /*
         * Botão de Finalizar Venda ou Transformar em Pedido
         */
    <?php if(!$Venda->getStatusPoliticaComercialDesc() && $PermiteSalvarTransformarEmPedido === true){ /* Se permite que seja salvo e se está fora da política comercial abre a tela de justificativa antes de salvar */ ?>
        $("#venda_div_aceite_politica_comercial").attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Alerta');
        $("#venda_div_aceite_politica_comercial").dialog({
            autoOpen: false,
            width:'500px',
            buttons:{
                "Confirmar": function(){
                    if(!$("#chk_aceite_politica_comercial").attr('checked')){
                        alert("É necessário que a opção 'Estou de acordo' esteja marcada para prosseguir.");
                        return false;
                    }
                    else if($("#textarea_aceite_politica_comercial").val() == ''){
                        alert("Justificativa é obrigatória.");
                        return false;
                    }
                    p4_venda_post('finaliza_venda');
                    $(this).dialog("close");
                },
                Fechar: function(){$(this).dialog("close");}},
                modal: true,
                show: "fade",
                hide: "fade",
                close: function(){$(this).dialog("destroy");}
        });
        $("#btn_finaliza_venda, #btn_transformar_em_pedido").click(function(event){
            $("#venda_div_aceite_politica_comercial").dialog("open");
        });
        <?php } elseif($PermiteSalvarTransformarEmPedido === true) { /* Se esta dentro da política e permite que seja salvo */ ?>
        $("#btn_finaliza_venda, #btn_transformar_em_pedido").click(function(event){
            p4_venda_post('finaliza_venda');
            event.preventDefault();
        });
        <?php } else { ?>
        $("#btn_finaliza_venda, #btn_transformar_em_pedido").click(function(event){
            var Dialog = $("#jquery-dialog");
            Dialog.attr("title",'Alerta');
            Dialog.html('<strong><?php echo $MsgAlertaBotaoFinalizar ;?></strong>');
            Dialog.dialog({
                buttons:{Fechar: function(){$(this).dialog("close");}},
                modal: true,
                show: "fade",
                hide: "fade"
            });
            event.preventDefault();
        });
        <?php } ?>
        $("#btn_finaliza_orcamento").click(function(event){
            p4_venda_post('finaliza_orcamento');
            event.preventDefault();
        });
        $("#btn_reabre_orcamento").click(function(event){
            p4_venda_post('reabre_orcamento');
            event.preventDefault();
        });
        $("#btn_aprovar").click(function(event){
			p4_venda_post('aprovar');
            event.preventDefault();
        });
        $("#btn_reprovar").click(function(event){
            p4_venda_post('reprovar');
            event.preventDefault();
        });
        $("#btn_perder_orcamento").click(function(event){
            var Dialog = $("#jquery-dialog");
            Dialog.attr("title",'Alerta');
            Dialog.html('<h2 style="font-size:14px;">Perda Orçamento<h2>Informe o Motivo:<br /><?php echo TabelaParaCombobox('is_opor_motivo', 'numreg', 'nome_opor_motivo', 'id_motivo_cancelamento','','',' ORDER BY nome_opor_motivo');?><br />');
            Dialog.dialog({
                buttons:{
                    "Confirmar Perda": function(){
                        var IdMotivoCancelamento = $("#id_motivo_cancelamento").val();
                        if(IdMotivoCancelamento == ''){
                            alert("Informe um motivo");
                            event.preventDefault();
                            return false;
                        }

                        $(this).dialog("close");
                        p4_venda_post('perder_orcamento','',IdMotivoCancelamento);
                        event.preventDefault();
                    },
                    Fechar: function(){$(this).dialog("close");}},
                modal: true,
                show: "fade",
                hide: "fade"
            });
            event.preventDefault();
        });
        $("#btn_cancela_bonificacao").click(function(event){
            p4_venda_post('cancelar_bonificacao');
            event.preventDefault();
        });
        $("#btn_exporta_pedido").click(function(event){
            p4_venda_post('exportar_pedido');
            event.preventDefault();
        });
        $("#btn_cancelar_pedido").click(function(event){
            var Dialog = $("#jquery-dialog");
            Dialog.attr("title",'Alerta');
            Dialog.html('<h2 style="font-size:14px;">Cancelamento<h2>Informe o Motivo:<br /><?php echo TabelaParaCombobox('is_motivo_cancelamento_pedido','numreg','nome_motivo_cancelamento','id_motivo_cancelamento');?><br /><br />');
            Dialog.dialog({
                buttons:{
                    "Confirmar Cancelamento": function(){
                        var IdMotivoCancelamento = $("#id_motivo_cancelamento").val();
                        if(IdMotivoCancelamento == ''){
                            alert("Informe um motivo");
                            event.preventDefault();
                            return false;
                        }

                        $(this).dialog("close");
                        p4_venda_post('cancelar_pedido','',IdMotivoCancelamento);
                        event.preventDefault();
                    },
                    Fechar: function(){$(this).dialog("close");}},
                modal: true,
                show: "fade",
                hide: "fade"
            });
            event.preventDefault();
        });
        $("#btn_clonar").click(function(event){
            p4_venda_post('clona_venda');
            event.preventDefault();
        });

        $("#btn_resumo_ult_ped").click(function(){
            var Dialog = $("#jquery-dialog");
            Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Resumo dos últimos pedidos');
            Dialog.html(HTMLLoading);
            Dialog.dialog({
                width: 320,
                height: 250,
                buttons:{
                    Fechar: function(){
                        $(this).dialog("close");
                    }
                },
                open: function(){
                    $.ajax({
                        url: "resumo_ultimos_pedidos.php",
                        global: false,
                        type: "POST",
                        data: ({
                            id_pessoa:'<?php echo base64_encode($Venda->getDadosVenda('id_pessoa'));?>',
                            id_pessoa_erp:'<?php echo base64_encode($Venda->getPessoa()->getDadoPessoa('id_pessoa_erp'));?>'
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
                close: function(){$(this).dialog("destroy");}
            });
        });

        $("#btn_frete").click(function(){
            var Dialog = $("#jquery-dialog-frete");
            Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Frete');
            Dialog.html(HTMLLoading);
            Dialog.dialog({
                width: 320,
                height: 250,
                buttons:{
                    Fechar: function(){
                        $(this).dialog("close");
                    }
                },
                open: function(){
                    $.ajax({
                        url: "p4_venda_frete.php",
                        global: false,
                        type: "POST",
                        data: ({
                            ptp_venda:$("#ptp_venda").val(),
                            pnumreg:'<?php echo $Venda->getNumregVenda();?>'
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
                close: function(){$(this).dialog("destroy");}
            });
        });

        $("#btn_imprimir").click(function(event){
            var Dialog = $("#jquery-dialog");
            Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Selecionar Modelo');
            Dialog.html(HTMLLoading);
            Dialog.dialog({
                buttons:{
                    'Selecionar':function(){
                        var width=700;
                        var height=550;
                        var left=100;
                        var top=100;
                        window.open('gera_modelo/exibe_modelo_impressao.php?pnumreg=<?php echo $Venda->getNumregVenda();?>&ptp_venda=<?php echo $_REQUEST['ptp_venda'];?>&id_modelo='+$("#select_id_modelo").val()+'&pid_usuario=<?php echo $_SESSION['id_usuario'];?>','modelo_impressao','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,width=' + width + ',height=' + height + ',left=' + left + ',top = ' + top).focus();
                    },
                    Fechar: function(){
                        $(this).dialog("close");
                    }
                },
                open: function(){
                    $.ajax({
                        url: "seleciona_modelo_impressao.php",
                        global: false,
                        type: "POST",
                        data: ({
                            ptp_venda:$("#ptp_venda").val(),
                            pnumreg:'<?php echo $Venda->getNumregVenda();?>'
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
                close: function(){$(this).dialog("destroy");}
            });
        });

        $("#btn_representantes_venda").click(function(event){
            var Dialog = $("#jquery-dialog");
            Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Participantes');
            Dialog.html(HTMLLoading);
            Dialog.dialog({
                width: 600,
                height: 250,
                buttons:{
                    <?php if(!$Venda->getDigitacaoCompleta() && $Usuario->getPermissao('sn_permite_add_particip_venda')){ /* Se possui permissão incluir */ ?>
                    '+Incluir':function(){
                    var DialogAdicionar = $("#jquery-dialog1");
                    DialogAdicionar.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Novo participante');
                    DialogAdicionar.html(HTMLLoading);
                    DialogAdicionar.dialog({
                        width: 600,
                        height: 290,
                        buttons:{Fechar: function(){$(this).dialog("close");}},
                        open: function(){
                            $.ajax({
                                url: "p4_venda_participantes.php",
                                global: false,
                                type: "POST",
                                data: ({
                                    pincluir:'true',
                                    ptp_venda:$("#ptp_venda").val(),
                                    pnumreg:'<?php echo $Venda->getNumregVenda();?>'
                                }),
                                dataType: "html",
                                async: true,
                                beforeSend: function(){

                                },
                                error: function(){
                                    alert('Erro com a requisição');
                                },
                                success: function(responseText){
                                    DialogAdicionar.html(responseText);
                                }
                            });
                        },
                        modal: true,
                        show: "fade",
                        hide: "fade",
                        close: function(){$(this).dialog("destroy");}
                    });
                },
                <?php } ?>
                Fechar: function(){$(this).dialog("close");}},
                open: function(){
                    $.ajax({
                        url: "p4_venda_participantes.php",
                        global: false,
                        type: "POST",
                        data: ({
                            ptp_venda:$("#ptp_venda").val(),
                            pnumreg:'<?php echo $Venda->getNumregVenda();?>'
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
                close: function(){$(this).dialog("destroy");}
            });
        });

        $("#btn_cria_pedido_bonificacao").click(function(event){
            p4_venda_post('cria_pedido_bonificacao');
            event.preventDefault();
        });

        $("#btn_enviar_email").click(function(event){
            var Dialog = $("#jquery-dialog");
            Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Selecionar Modelo de impressão no e-mail');
            Dialog.html(HTMLLoading);
            Dialog.dialog({
                width: 800,
                height: 700,
                buttons:{
                    'Enviar E-mail':function(){
                        $("#form_envio_email").submit();
                    },
                    Fechar: function(){
                        $(this).dialog("close");
                    }
                },
                open: function(){
                    $.ajax({
                        url: "seleciona_envio_email.php",
                        global: false,
                        type: "POST",
                        data: ({
                            ptp_venda:$("#ptp_venda").val(),
                            pnumreg:'<?php echo $Venda->getNumregVenda();?>'
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
                close: function(){$(this).dialog("destroy");}
            });
        });

        $("#btn_lista_revisoes").click(function(event){
            var Dialog = $("#div_lista_revisoes");
            Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Revisões');
            Dialog.dialog({
                width: 500,
                height: 350,
                buttons:{
                Fechar: function(){$(this).dialog("close");}},
                modal: true,
                show: "fade",
                hide: "fade",
                close: function(){$(this).dialog("destroy");}
            });
        });
        $("#btn_documentos").click(function(){
            <?php
            $SqlNumregMestreDetalheAtividade = "SELECT numreg FROM is_gera_cad_sub WHERE id_funcao_mestre = '".(($Venda->isOrcamento())?'orcamento':'pedido')."' AND id_funcao_detalhe = 'arquivos_cad'";
            $QryNumregMestreDetalheAtividade = query($SqlNumregMestreDetalheAtividade);
            $ArNumregMestreDetalheAtividade = farray($QryNumregMestreDetalheAtividade);
            $NumregMestreDetalheAtividade = $ArNumregMestreDetalheAtividade['numreg'];
            ?>
            var Url = '../../gera_cad_lista.php?pfuncao=arquivos_cad&pfixo=id_<?php echo (($Venda->isPedido())?'pedido':'orcamento');?>@igual@s<?php echo $Venda->getNumregVenda();?>@s&psubdet=<?php echo $NumregMestreDetalheAtividade;?>&pnpai=<?php echo $Venda->getNumregVenda();?>&pdrilldown=1';
            window.open(Url,'arquivos_<?php echo $Venda->getNumregVenda();?>','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=800,height=600,top=250,left=250');
        });
        $("#btn_atividades").click(function(){
            <?php
            $SqlNumregMestreDetalheAtividade = "SELECT numreg FROM is_gera_cad_sub WHERE id_funcao_mestre = '".(($Venda->isOrcamento())?'orcamento':'pedido')."' AND id_funcao_detalhe = 'atividades_cad_lista'";
            $QryNumregMestreDetalheAtividade = query($SqlNumregMestreDetalheAtividade);
            $ArNumregMestreDetalheAtividade = farray($QryNumregMestreDetalheAtividade);
            $NumregMestreDetalheAtividade = $ArNumregMestreDetalheAtividade['numreg'];
            ?>
            var Url = '../../gera_cad_lista.php?pfuncao=atividades_cad_lista&pfixo=id_<?php echo (($Venda->isPedido())?'pedido':'orcamento');?>@igual@s<?php echo $Venda->getNumregVenda();?>@s&psubdet=<?php echo $NumregMestreDetalheAtividade;?>&pnpai=<?php echo $Venda->getNumregVenda();?>&pdrilldown=1';
            window.open(Url,'atividades_<?php echo $Venda->getNumregVenda();?>','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=800,height=600,top=250,left=250');
        });

        <?php if(!$PermiteAprovarVenda){ ?>
        $('#btn_aprovar').unbind('click');
        $('#btn_aprovar').click(function(){
            var Dialog = $("#jquery-dialog");
            Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Alerta');
            Dialog.html('<strong><?php echo $MsgAprovacao;?></strong>');
            Dialog.dialog({
                buttons:{
                Fechar: function(){$(this).dialog("close");}},
                modal: true,
                show: "fade",
                hide: "fade",
                close: function(){$(this).dialog("destroy");}
            });
        });
        <?php } ?>
    });
</script>
<input type="hidden" name="ptp_venda" id="ptp_venda" value="<?php echo $_GET['ptp_venda'];?>" />
<input type="hidden" name="pnumreg" id="pnumreg" value="<?php echo $_GET['pnumreg'];?>" />
<input type="hidden" name="pfuncao" id="pfuncao" value="<?php echo $_GET['pfuncao'];?>" />
<input type="hidden" name="url_retorno" id="url_retorno" value="<?php echo curPageURL();?>" />
<fieldset style="text-align:center;"><legend>A&ccedil;&otilde;es do <?php echo ucwords($Venda->getTituloVenda());?></legend>
<?php if(!$Venda->VisualizarRevisao){ /* Se está apenas visualizando os dados(revisao) exibe apenas o botão de fechar */?>
<div>
<?php if($Venda->isOrcamento() && !$Venda->getGerouPedido() && !$Venda->getDigitacaoCompleta() && $PermiteFinalizarOrcamento){ /* Se é um orçamento e ainda não gerou pedido e está com digitação incompleta */ ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_finaliza_orcamento">
        <img src="img/finalizar.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Finalizar Or&ccedil;amento" title="Finalizar Or&ccedil;amento" />
        <p>Finalizar Or&ccedil;amento</p>
        <span>&nbsp;</span>
    </a>
<?php } ?>
<?php if($Venda->isOrcamento() && !$Venda->getEmAprovacao() && !$Venda->getGerouPedido() && $Venda->getDigitacaoCompleta() && !$Venda->getSnOrcamentoPerdido()){ /* Se é um orçamento e ainda não gerou pedido e está com digitação completa e não está com status de perdido */ ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_reabre_orcamento">
        <img src="img/reabrir.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Reabrir Or&ccedil;amento" title="Reabrir Or&ccedil;amento" />
        <p>Reabrir Or&ccedil;amento</p>
        <span>&nbsp;</span>
    </a>
<?php } ?>
<?php if($Venda->getTipoVenda() == 2 && !$Venda->getDigitacaoCompleta()){ /* Se for um pedido e ainda não estiver completo e não está com status de perdido */?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_finaliza_venda">
        <img src="img/salvar.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Finalizar" title="Finalizar" />
        <p>Finalizar</p>
        <span>&nbsp;<?php echo $MsgBotaoFinalizar; ?></span>
    </a>
<?php } ?>
<?php if($Venda->getTipoVenda() == 1 && !$Venda->getGerouPedido() && !$Venda->getEmAprovacao() && !$Venda->getSnOrcamentoPerdido()){ /* Se é um orçamento e ainda não gerou pedido e não esta em aprovação e não é um orçamento perdido */ ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_transformar_em_pedido">
        <img src="img/transformar_pedido.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Transformar em Pedido" title="Transformar em Pedido" />
        <p>Transformar em Pedido</p>
        <span>&nbsp;<?php echo $MsgBotaoFinalizar; ?></span>
    </a>
<?php } ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_representantes_venda">
        <img src="img/participantes.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Participantes da Venda" title="Participantes da Venda" />
        <p>Participantes da Venda</p>
        <span>&nbsp;</span>
    </a>
<?php if($Venda->getDigitacaoCompleta() && !$Venda->getSnOrcamentoPerdido()){ /* Se a digitação está completa e não é um orçamento perdido */ ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_enviar_email">
        <img src="img/enviar_email.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Enviar por E-Mail" title="Enviar por E-Mail" />
        <p>Enviar por E-Mail</p>
        <span>&nbsp;<?php if($Venda->getDadosVenda('sn_email_enviado')==1){ echo 'E-mail enviado anteriormente.';}?></span>
    </a>
<?php } ?>
<?php if($Venda->getDigitacaoCompleta() && !$Venda->getSnOrcamentoPerdido()){ /* Se a digitação está completa e não é um orçamento perdido */ ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_imprimir">
        <img src="img/imprimir.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Impress&atilde;o do <?php echo ucwords($Venda->getTituloVenda());?>" title="Impress&atilde;o do <?php echo ucwords($Venda->getTituloVenda());?>" />
        <p>Impress&atilde;o do <?php echo ucwords($Venda->getTituloVenda());?></p>
        <span>&nbsp;</span>
    </a>
<?php } ?>
<?php if($Venda->getEmAprovacao()){ /* Se ainda não está completo e está em aprovação */ ?>
    <?php if($Usuario->getPermissao('sn_permite_aprovar_venda')){ ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_aprovar">
        <img src="img/aprovar.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Aprovar" title="Aprovar" />
        <p>Aprovar</p>
        <span>&nbsp;
        <?php
            if($Venda->VerificaSeHaItemReprovado()){
                echo 'Há itens reprovados';
            }
            if(!$PermiteAprovarVenda){
                echo 'Não Permitido';
            }
        ?>
        </span>
    </a>
    <?php } ?>
    <?php if($Usuario->getPermissao('sn_permite_reprovar_venda')){ ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_reprovar">
        <img src="img/reprovar.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Reprovar" title="Reprovar" />
        <p>Reprovar</p>
        <span>&nbsp;</span>
    </a>
    <?php } ?>
<?php } ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_atividades">
        <img src="img/atividades.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Atividades Relacionadas" title="Atividades Relacionadas" />
        <p>Atividades Relacionadas</p>
        <span>&nbsp;</span>
    </a>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_documentos">
        <img src="img/documentos.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Documentos Relacionados" title="Documentos Relacionados" />
        <p>Documentos Relacionados</p>
        <span>&nbsp;</span>
    </a>
<?php if($VendaParametro->getSnUsaCalculoDeFrete()){ ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_frete">
        <img src="img/frete.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Frete" title="Frete" />
        <p>Frete</p>
        <span>&nbsp;</span>
    </a>
<?php } ?>
<?php if(!$Venda->getSnGerouPedidoBonificacao() && !$Venda->isCancelado()){
    $VlTotalBonificacao = $Venda->getDadosVenda('vl_total_bonificacao');
    if($VlTotalBonificacao > 0 && $Venda->isTipoVenda()){ /* Se o valor de bonificação é maior que 0, e é um pedido do tipo venda */?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_cria_pedido_bonificacao">
        <img src="img/bonificacao.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Criar Pedido de Bonifica&ccedil;&atilde;o" title="Criar Pedido de Bonifica&ccedil;&atilde;o" />
        <p>Criar Pedido de Bonifica&ccedil;&atilde;o</p>
        <span><?php echo $Venda->NFV($VlTotalBonificacao);?> para bonificar</span>
    </a>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_cancela_bonificacao">
        <img src="img/bonificacao_cancelar.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Cancelar Bonifica&ccedil;&atilde;o" title="Cancelar Bonifica&ccedil;&atilde;o" />
        <p>Cancelar Bonifica&ccedil;&atilde;o</p>
        <span>&nbsp;</span>
    </a>
    <?php } } elseif($Venda->getSnGerouPedidoBonificacao()) {
    $UrlPedidoBonificacao = new Url();
    $UrlPedidoBonificacao->setUrl(curPageURL());
    $UrlPedidoBonificacao->AlteraParam('pnumreg', $Venda->getDadosVenda('id_venda_bonificacao'));
    $UrlPedidoBonificacao->AlteraParam('ptpvenda', 2);
    $UrlPedidoBonificacao->AlteraParam('pfuncao', 'pedido');
    $UrlPedidoBonificacao->AlteraParam('ppagina', 'p2');
    ?>
    <a href="<?php echo $UrlPedidoBonificacao->getUrl();?>" class="<?php echo $IconeClasse;?>">
        <img src="img/bonificacao.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Abrir Pedido de Bonifica&ccedil;&atilde;o" title="Abrir Pedido de Bonifica&ccedil;&atilde;o" />
        <p>Abrir Pedido de Bonifica&ccedil;&atilde;o</p>
        <span>&nbsp;</span>
    </a>
<?php } ?>
<?php if($SnPermiteClonarVenda){?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_clonar">
        <img src="img/clonar.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Criar C&oacute;pia" title="Criar C&oacute;pia" />
        <p>Criar C&oacute;pia</p>
        <span>&nbsp;</span>
    </a>
<?php } ?>
<?php if($Venda->getDadosVenda('id_revisao') >= 1){ ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_lista_revisoes">
        <img src="img/revisao.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Revis&otilde;es" title="Revis&otilde;es" />
        <p>Visualizar Revis&otilde;es</p>
        <span>&nbsp;</span>
    </a>
<?php } ?>
<?php if($Venda->isOrcamento() && !$Venda->getGerouPedido() && !$Venda->getEmAprovacao() && !$Venda->getSnOrcamentoPerdido()){ /* Se for um orcamento, e ainda não foi transformado em pedido e não está em aprovação e não é um orçamento perdido */?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_perder_orcamento">
        <img src="img/perder_orcamento.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Perder Or&ccedil;amento" title="Perder Or&ccedil;amento" />
        <p>Perder Or&ccedil;amento</p>
        <span>&nbsp;</span>
    </a>
<?php } ?>
<?php if($Venda->isPedido() && $Venda->getDadosVenda('sn_importado_erp') != '1' && $Venda->getDadosVenda('id_situacao_venda') != '6' && $Usuario->getPermissao('sn_permite_cancelar_venda')){ ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_cancelar_pedido">
        <img src="img/perder_orcamento.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Cancelar Pedido" title="Cancelar Pedido" />
        <p>Cancelar Pedido</p>
        <span>&nbsp;</span>
    </a>
<?php } ?>
<?php if($Venda->isPedido() && $Venda->getDadosVenda('sn_digitacao_completa') == '1' && $Venda->getDadosVenda('sn_exportado_erp') == '0' && $VendaParametro->getSnExportaPedidoAoFinalizar() && $_SESSION['id_usuario'] == '1'){?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_exporta_pedido">
        <img src="img/atualizar.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Exportar Pedido" title="Exportar Pedido" />
        <p>Exportar Pedido</p>
        <span>&nbsp;</span>
    </a>
<?php } ?>
<?php if($Venda->getEmAprovacao()){ ?>
        <a href="#" class="<?php echo $IconeClasse;?>" id="btn_resumo_ult_ped">
            <img src="img/notas_fiscais.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Exibir Resumo &Uacute;ltimos Pedidos" title="Exibir Resumo &Uacute;ltimos Pedidos" />
            <p>Exibir Resumo &Uacute;lt. Ped.</p>
            <span>&nbsp;</span>
        </a>
<?php } ?>
<?php include('p4_venda_botoes_custom.php');?>
<?php if($Venda->getEmAprovacao() || !$Venda->getDigitacaoCompleta()){ ?>
    <a href="<?php $Url->AlteraParam('ppagina','p3'); echo $Url->getUrl();?>" class="<?php echo $IconeClasse;?>">
        <img src="img/voltar.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="&lt;&lt; Passo Anterior" title="&lt;&lt; Passo Anterior" />
        <p>&lt;&lt; Passo Anterior</p>
        <span>&nbsp;</span>
    </a>
<?php } else { ?>
    <a href="<?php $Url->AlteraParam('ppagina','p2'); echo $Url->getUrl();?>" class="<?php echo $IconeClasse;?>">
        <img src="img/voltar.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="&lt;&lt; Passo Anterior" title="&lt;&lt; Passo Anterior" />
        <p>&lt;&lt; Passo Anterior</p>
        <span>&nbsp;</span>
    </a>
<?php } ?>
    <a href="#" class="<?php echo $IconeClasse;?>" id="btn_sair">
        <img src="img/sair.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Sair" title="Sair" />
        <p>Sair</p>
        <span>&nbsp;</span>
    </a>
</div>
</fieldset>
<?php if(!$Venda->getStatusPoliticaComercialDesc()){ ?>
<div id="venda_div_aceite_politica_comercial" style="display: none;">
    <h2>Análise Comercial</h2>
    <?php
    $Texto = $VendaParametro->getTextoVendaForaPolitica();
    $Texto = str_replace('{NOME_VENDA}', ucwords($Venda->getTituloVenda()) ,$Texto);
    echo $Texto;
    ?><br />
    <h1>A comissão pode ser reduzida.</h1>
    <input type="checkbox" id="chk_aceite_politica_comercial" />&nbsp;Estou de acordo<br /><br />
    <span style="font-size: 14px;font-weight: bold;">Justificativa:</span><br />
    <br />
    Este campo deve ser utilizado APENAS para justificativa.<br />
    <textarea id="textarea_aceite_politica_comercial" cols="80" rows="6"></textarea><br /><br />
</div>
<?php } ?>
<div id="div_lista_revisoes" style="display:none;">
    <fieldset>
        <legend>Revis&otilde;es</legend>
        <?php
        $ArRevisoes = $Venda->getArRevisoes();
        foreach($ArRevisoes as $Numreg => $DadosRevisao){
            echo '<span class="venda_subtitulo">Revisão '.$DadosRevisao['id_revisao'].' - '.uB::DataEn2Br($DadosRevisao['dt_revisao']).'</span>&nbsp;&nbsp;<input type="button" numreg="'.$Numreg.'" value="Visualizar" class="btn_revisao_visualizar botao_jquery" />&nbsp;';
            //if(!$Venda->getEmAprovacao() && !$Venda->getSnOrcamentoPerdido()){
            //    echo '<input type="button" numreg="'.$Numreg.'" value="Restaurar" class="btn_revisao_restaurar botao_jquery" />';
            //}
            echo '<hr size="1"/>';
        }
        $UrlRevisao = new Url();
        $UrlRevisao->setUrl(curPageURL());
        $UrlRevisao->RemoveParam('pvisualizar_revisao');
        $UrlRevisao->RemoveParam('pnumreg');
        $UrlRevisao->AlteraParam('ppagina', 'p2');
        $StringUrlRevisao = $UrlRevisao->getUrl();
        ?>
        <script>
            $(document).ready(function(){
                $(".btn_revisao_visualizar").click(function(){
                    window.open('<?php echo $StringUrlRevisao;?>&pvisualizar_revisao=1&pnumreg='+$(this).attr('numreg'),'revisao_<?php echo $Venda->getNumregVenda();?>_'+$(this).attr('numreg'),'toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1').focus();
                });
                //$(".btn_revisao_restaurar").click(function(){
                    //p4_venda_post('restaura_revisao',$(this).attr('numreg'));
                //});
            });
        </script>
    </fieldset>
</div>

<?php } else {  ?>
    <div>
        <a href="#" class="<?php echo $IconeClasse;?>" id="btn_sair">
            <img src="img/sair.png" width="<?php echo $IconeTamanho;?>" height="<?php echo $IconeTamanho;?>" alt="Sair" title="Sair" />
            <p>Sair</p>
            <span>&nbsp;</span>
        </a>
    </div>
    
<?php } ?>
<div id="jquery-dialog-frete"></div>