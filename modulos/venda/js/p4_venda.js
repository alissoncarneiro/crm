function link_abrir_detalhe_conta(Numreg){
    var width   = 700;
    var height  = 550;
    var left    = 100;
    var top     = 100;
    window.open('../../gera_cad_detalhe.php?pfuncao=pessoa&pnumreg=' + Numreg + '&pdrilldown=1&ptpec=1&pfrom=venda','pessoa_' + Numreg,'toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,width=' + width + ',height=' + height + ',left=' + left + ',top = ' + top).focus();
    return false;
};

function p4_venda_post(Acao,NumregRevisao,IdMotivoCancelamento){
    var MensagemAlerta;
    var prequisicao;
    if(Acao == 'finaliza_orcamento'){
        MensagemAlerta = 'Todos os dados estão corretos ?';
    }
    else if(Acao == 'reabre_orcamento'){
        MensagemAlerta = 'Ao reabrir o orçamento para alterações, será gerada uma revisão. Deseja continuar ?';
    }
    else if(Acao == 'finaliza_venda'){
        MensagemAlerta = 'Todos os dados estão corretos ?';
    }
    else if(Acao == 'aprovar'){
        MensagemAlerta = '<h2 style="font-size:14px;">Aprovação<h2>Justificativa:<br /><textarea id="textarea_justificativa_aprov_reprov" style="width:270px;" cols="45" rows="3"></textarea><br /><br />';
    }
    else if(Acao == 'reprovar'){
        MensagemAlerta = '<h2 style="font-size:14px;">Reprovação<h2>Justificativa:<br /><textarea id="textarea_justificativa_aprov_reprov" style="width:270px;" cols="45" rows="3"></textarea><br /><br />';
    }
    else if(Acao == 'cria_pedido_bonificacao'){
        MensagemAlerta = 'Criar pedido de bonifica&ccedil;&atilde;o';
    }
    else if(Acao == 'restaura_revisao'){
        MensagemAlerta = 'Resturar para esta revisão ?';
    }
    else if(Acao == 'perder_orcamento'){
        MensagemAlerta = 'Deseja perder este orçamento ?';
    }
    else if(Acao == 'cancelar_pedido'){
        MensagemAlerta = 'Deseja cancelar este pedido ?';
    }
    else if(Acao == 'clona_venda'){
        MensagemAlerta = 'Deseja efetuar uma cópia ?';
    }
    else if(Acao == 'exportar_pedido'){
        MensagemAlerta = 'Deseja exportar o pedido ?';
    }
    else if(Acao == 'cancelar_bonificacao'){
        MensagemAlerta = 'Esta ação não pode ser desfeita, deseja cancelar a bonificação ?';
    }
    prequisicao = Acao;
    $("#jquery-dialog").attr("title","Alerta");
    $("#jquery-dialog").html(MensagemAlerta);
    $("#jquery-dialog").dialog({
        dialogClass: 'jquery-dialog',
        position: 'center',
        resizable: false,
        buttons:{
            "Confirmar": function(){
                $.ajax({
                    url:'p4_venda_post.php',
                    global: true,
                    type: "POST",
                    dataType: "xml",
                    async: false,
                    data: ({
                        prequisicao:prequisicao,
                        ptp_venda:$("#ptp_venda").val(),
                        pnumreg:$("#pnumreg").val(),
                        pjustificativaemaprovacaocomercial:escape($("#textarea_aceite_politica_comercial").val()),
                        pjustificativaaprovreprovcomercial:escape($("#textarea_justificativa_aprov_reprov").val()),
                        id_motivo_cancelamento:escape(IdMotivoCancelamento),
                        url_retorno:escape($("#url_retorno").val()),
                        pnumreg_revisao:NumregRevisao
                    }),
                    beforeSend:function(){

                    },
                    error: function(){
                        alert('Erro com a requisição');
                        $("#jquery-dialog").dialog("close");
                    },
                    success: function(xml){
                        
                        var resposta    = $(xml).find('resposta');
                        var Status      = resposta.find('status').text();
                        var Acao        = resposta.find('acao').text();
                        var Url         = resposta.find('url').text();
                        var Mensagem    = resposta.find('mensagem').text();
                        //Mensagem = Mensagem.replace(/&&/gi,"&");
                        if(Status == 1){
                            $("#jquery-dialog").attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Alerta');
                            $("#jquery-dialog").html(Mensagem);
                            $("#jquery-dialog").dialog({
                                buttons:{Ok: function(){
                                        $(this).dialog("close");
                                        MaskLoading('Mostrar');
                                        if(Acao == 1){
                                            window.location.reload();
                                            if(window.opener.document.getElementById("btnfiltrar")){
                                                window.opener.document.getElementById("btnfiltrar").click();
                                            }
                                        }
                                        else if(Acao == 2){
                                            window.location = Url;
                                        }
                                        else if(Acao == 3){
                                            window.location = Url;
                                            if(window.opener.document.getElementById("btnfiltrar")){
                                                window.opener.document.getElementById("btnfiltrar").click();
                                            }
                                        }
                                        else{
                                            window.close();
                                            if(window.opener.document.getElementById("btnfiltrar")){
                                                window.opener.document.getElementById("btnfiltrar").click();
                                            }
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
                                hide: "fade"
                            });
                        }
                    }
                });
            },
            Cancelar: function(){$(this).dialog("close");}},
        modal: true,
        show: "fade",
        hide: "fade"
    });
}