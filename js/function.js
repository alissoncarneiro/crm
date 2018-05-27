/*
 * function.js
 * Vers�o: 4.0
 * 03/10/2011 09:00:00
 */

var HTMLLoadingGeral = '<div align="center"><img src="images/ajax_loading_bar.gif" alt="Carregando..."><br /><strong>Carregando...</strong></div>';
var IMGLoadingGeral = '<img src="images/loading.gif" alt="Carregando...">';
/*
 * TRATAMENTO CADASTRO DE CLIENTES/PROSPECTS
 *
 */
function verificaCPNJCPF(elemento) {
    var verifica = document.getElementById(elemento).value;
    if (verifica == 1 ) {
        validaCNPJ('edtcnpj_cpf');
    } else if (verifica == 2) {
        validaCPF(document.getElementById('edtcnpj_cpf'));
    }
}

function troca_tp_pessoa(id_campo){
    
    /*
     * Fun��o para trocar os campos de CNPJ para CPF e IE para RG e exibir o campo "Fantasia" quando for pessoa jur�dica
     */
    if(gebi(id_campo).value == '1'){
        if(gebi('li_05informacoes_pessoais')){
            gebi('li_05informacoes_pessoais').style.display = 'none';
        }
    }
    else if(gebi(id_campo).value == '2'){
        if(gebi('li_05informacoes_pessoais')){
            gebi('li_05informacoes_pessoais').style.display = '';
        }
    }
}

/* Executar pesquisa */
function executa_pesquisa(pnumreg){
    /*var width = 500;
    var height = 500;
    var ScreenWidth = screen.width;
    var ScreenHeight = screen.height;
    var left = (ScreenWidth - width) / 2;
    var top = (ScreenHeight - height) / 2;
    window.open('modulos/pesquisas/executa_pesquisa.php?pnumreg='+pnumreg,'executando_pesquisa','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1').focus();
    */

    var xmlhttp = XMLHTTPRequest();
    var send    = '';
        send    += 'pnumreg=' + pnumreg;

    xmlhttp.onreadystatechange = function(){
        //valida se a requisicao esta conpleta
        if(xmlhttp.readyState == 4 || xmlhttp.readyState == 0){
            //se estiver ok
            if(xmlhttp.status == 200){
                if(xmlhttp.responseText == 'selecionar'){
                    window.open("modulos/pesquisas/exibe_opcoes_pesquisas.php?"+send);
                } else {
                    window.open("modulos/pesquisas/executa_pesquisa.php?"+send+"&id_script="+xmlhttp.responseText);
                }
            }
        }
    };
    xmlhttp.open('post', 'modulos/pesquisas/varifica_qtde_pesquisa.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send(send);
}

/* Ajax gerar relatorio de pesquisa */
function gera_relatorio_pesquisa(){
		//Executa a fun��o objetoXML()
		var xmlhttp = XMLHTTPRequest();
                var send = '';
                send += '&id_programacao=' + $('#edtid_programacao').val();
                //send += '&id_pesquisa=' + $('#edtid_pesquisa').val();
                xmlhttp.onreadystatechange = function () {
			//Se a requisi��o estiver completada
			if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
			//Se o status da requisi��o estiver OK
				if (xmlhttp.status == 200) {
                                    if(xmlhttp.responseText == 'ok'){
                                        window.open("modulos/relatorios/relatorio_pesquisa_post.php?"+send);
                                        $('#resp').fadeIn(200);
                                        $('#resp').html('Processo finalizado com sucesso!');
                                    } else {
                                        $('#resp').fadeIn(200);
                                        $('#resp').html(xmlhttp.responseText);
                                    }
				}
				document.getElementById('btn_gerar_relatorio_pesquisa').value = 'Gerar Relat�rio';
				document.getElementById('btn_gerar_relatorio_pesquisa').disabled = false;
			}
			else{
                                $('#resp').fadeIn(200);
                                $('#loading_bar').fadeIn(200);
			}
		};

		xmlhttp.open('post', 'modulos/relatorios/relatorio_pesquisa_post.php', true);
		xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		xmlhttp.setRequestHeader("Pragma", "no-cache");
		xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
		xmlhttp.send(send);
}

/* Ajax gerar relacionamento de pesquisa */
/*
function gera_relacionamento_pesquisa(){
        //Executa a fun��o objetoXML()
        var xmlhttp = XMLHTTPRequest();
        var send = '';
        send += 'id_pesquisa=' + $('#edtid_pesquisa').val();
        send += '&id_cad=' + $('#edtid_cad').val();
        //send += '&data_de=' + $('#edtdt_de').val();
        //send += '&data_ate=' + $('#edtdt_ate').val();
        //send += '&formato=' + $('#edtformato').val();
        xmlhttp.onreadystatechange = function () {
                //Se a requisi��o estiver completada
                if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                //Se o status da requisi��o estiver OK
                        if (xmlhttp.status == 200) {
                                $('#resp').fadeIn(200);
                                $('#resp').html(xmlhttp.responseText);
                        }
                }
                else{
                        $('#resp').fadeIn(200);
                        $('#loading_bar').fadeIn(200);
                }
        };

        xmlhttp.open('post', 'modulos/pesquisas/relaciona_script_pesquisa_post.php', true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
        xmlhttp.send(send);
}
*/

function TransformaClienteEmProspect(numreg){
    if(!confirm('Deseja converter este cliente em prospect para corre��o?')){
        return false;
    }
    $.ajax({
        url: "modulos/clientes/libera_alteracao.php",
        global: false,
        type: "POST",
        data: ({
            pnumreg:numreg
        }),
        dataType: "html",
        async: true,
        beforeSend: function(){
            MaskLoading('Mostrar');
        },
        error: function(){
            alert('Erro com a requisi��o');
            MaskLoading('Ocultar');
        },
        success: function(responseText){
            alert(responseText);
            MaskLoading('Ocultar');
            $("#btnfiltrar").click();
        }
    });
    return true;
}

/*
 *Transformando prospect em cliente
 */
function consumidorfinal_para_prospect(numreg){
    if(confirm("Todos os dados n�o salvos ser�o perdidos. Deseja continuar ?")){
        if(numreg == '' || isNaN(numreg)){
            alert('Internal Error: 1001001');
            return false;
        }
        alert("Preencha todos os campos necess�rios e clique em 'Salvar Altera��es' para transformar o consumidor final em prospect.");
        var NovaURL = window.location.href;
        if(NovaURL.indexOf('&ptsep=1') == -1){
            NovaURL = NovaURL + '&ptsep=1';
            window.location.href = NovaURL;
        }
        else{
            window.location.href = NovaURL;
        }
    }
    return true;
}

function atende_chamado_portal(NumregChamado){
    if(!confirm('Deseja atender este chamado?')){
        return false;
    }
    $.ajax({
        url: "modulos/portal/atende_chamado_portal.php",
        global: false,
        type: "POST",
        data: ({
            numreg_chamado:NumregChamado
        }),
        dataType: "html",
        async: true,
        beforeSend: function(){
            MaskLoading('Mostrar');
        },
        error: function(){
            alert('Erro com a requisi��o');
            MaskLoading('Ocultar');
        },
        success: function(responseText){
            alert(responseText);
            MaskLoading('Ocultar');
            $("#btnfiltrar").click();
            window.open('modulos/portal/tela_chamado.php?pnumreg=' + NumregChamado,'chamado_portal'+NumregChamado,'location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=850,height=550,top=100,left=100').focus();
        }
    });
}

function suspect_para_prospect(numreg){
    if(confirm("Todos os dados n�o salvos ser�o perdidos. Deseja continuar ?")){
        if(numreg == '' || isNaN(numreg)){
            alert('Internal Error: 1001001');
            return false;
        }
        alert("Preencha todos os campos necess�rios e clique em 'Salvar Altera��es' para transformar o suspect em prospect.");
        var NovaURL = window.location.href;
        if(NovaURL.indexOf('&ptsep=1') == -1){
            NovaURL = NovaURL + '&ptsep=1';
            window.location.href = NovaURL;
        }
        else{
            window.location.href = NovaURL;
        }
    }
    return true;
}

function prospect_para_cliente(numreg){
    if(confirm("Todos os dados n�o salvos ser�o perdidos. Deseja continuar ?")){
        if(numreg == '' || isNaN(numreg)){
            alert('Internal Error: 1001001');
            return false;
        }
        alert("Preencha todos os campos necessários e clique em 'Salvar Alterações' para transformar o prospect em cliente.");
        var NovaURL = window.location.href;
        if(NovaURL.indexOf('&ptpec=1') == -1){
            NovaURL = NovaURL + '&ptpec=1';
            window.location.href = NovaURL;
        }
        else{
            window.location.href = NovaURL;
        }
    }
    return true;
}

function limpa_formulário_cep() {
    // Limpa valores do formulário de cep.
    $("#edtendereco").val("");
    $("#edtbairro").val("");
    $("#edtcidade").val("");
    $("#edtpais").val("");
}


function pesquisa_cep(TP,TOP,UsaNumero){
    if($("#edtcep"+TP).val() != ''){



        var Mask;
        if($("#modal_mask").length == 0){
            Mask = $("<div id=\"modal_mask\"></div>");
            $("body").append(Mask);
            Mask.css("display", "none");
            Mask = $("#modal_mask");
            Mask.css("background", "#000000");
            Mask.css("position", "fixed");
            Mask.css("z-index", "1000");
            Mask.css("top", "0px");
            Mask.css("left", "0px");
            Mask.css("height", "100%");
            Mask.css("width", "100%");
            Mask.css("opacity", 0.5);
        }
        else{
            Mask = $("#modal_mask");
        }
        Mask.fadeIn();

        UsaNumero = (typeof(UsaNumero) == 'undefined')?true:UsaNumero;
        $.ajax({
            url:'modulos/customizacoes/pesquisa_cep.php',
            global: true,
            type: "POST",
            dataType: "xml",
            async: true,
            data: ({
                edtcep:$("#edtcep"+TP).val()
            }),
            beforeSend:function(){

            },
            error: function(){
                alert('Erro com a requisilção');
                Mask.fadeOut(100);
            },
            success: function(xml){
                var resposta = $(xml).find('resposta');
                var status = resposta.find('status').text();
                var bloqueia = resposta.find('bloqueia').text();
                if(status == 'true'){
                    var id_cep          = resposta.find('id_cep').text();
                    var id_logradouro   = resposta.find('id_logradouro').text();
                    var tipo_endereco   = resposta.find('tipo_endereco').text();
                    var endereco        = resposta.find('endereco').text();
                    var bairro          = resposta.find('bairro').text();
                    var cidade          = resposta.find('cidade').text();
                    var uf              = resposta.find('uf').text();
                    var cep             = resposta.find('cep').text();
                    var pais            = resposta.find('pais').text();

                    $("#edtid_cep"+TP).val(id_cep);
                    $("#edtendereco"+TP).val(((UsaNumero == true)?tipo_endereco + ' ' + endereco:tipo_endereco + ' ' + endereco + ', '));
                    if(id_logradouro != ''){
                        $("#edtendereco"+TP).val(((UsaNumero == true)?endereco:endereco + ', '));
                        $("#edtid_logradouro"+TP).val(id_logradouro);
                    }
                    $("#edtbairro"+TP).val(bairro);
                    $("#edtcidade"+TP).val(cidade);
                    $("#edtuf"+TP).val(uf);
                    $("#edtcep"+TP).val(cep);
                    $("#edtpais"+TP).val(pais);

                    if(UsaNumero == true){
                    //document.getElementById('edtnumero'+TP).focus();
                    }
                    alert('Por favor, preencha o n�mero do endere�o.');
                    if(bloqueia == 'true'){
                        cep_trava_campos(TP,true,UsaNumero);
                    }else{
                        cep_trava_campos(TP,false,UsaNumero);
                    }
                }
                else{
                    alert('CEP n�o encontrado.');
                    $("#edtid_cep"+TP).val('');
                    cep_trava_campos(TP,false,UsaNumero);
                }
                Mask.fadeOut(100);
            }
        });
    }
}

function cep_trava_campos(TP,Trava,UsaNumero){
    UsaNumero = (typeof(UsaNumero) == 'undefined')?true:UsaNumero;
    if(UsaNumero == true){
        $('#edtendereco'+TP).attr("readonly",Trava);
    }
    $('#edtbairro'+TP).attr("readonly",Trava);
    $('#edtcidade'+TP).attr("readonly",Trava);
    $('#edtcidade'+TP).attr("readonly",Trava);
    $('#edtpais'+TP).attr("readonly",Trava);

    var Color = (Trava == true)?"#EAEAEA":"#FFFFFF";
    if(UsaNumero == true){
        $('#edtendereco'+TP).css("background-color",Color);
    }
    $('#edtbairro'+TP).css("background-color",Color);
    $('#edtcidade'+TP).css("background-color",Color);
    $('#edtuf'+TP).css("background-color",Color);
    $('#edtcidade'+TP).css("background-color",Color);
    $('#edtpais'+TP).css("background-color",Color);
    $('#edtuf'+TP).children().each(function(){
        if($('#edtuf'+TP).val() != $(this).val()){
            $(this).attr("disabled",Trava);
        }        
    });
}

function MaskLoading(Acao){
    var Mask;
    if($("#modal_mask_loading").length == 0){
        Mask = $("<div id=\"modal_mask_loading\"></div>");
        $("body").append(Mask);
    }
    else{
        Mask = $("#modal_mask_loading");
    }
    if(Acao == 'Mostrar'){
        Mask.fadeIn();
    }
    else if(Acao == 'Ocultar'){
        Mask.fadeOut();
    }
}

/*
 * Exporta��o de pedidos via arquivo texto
 */
function GeraArquivoTXTPedido(){
    var width = 500;
    var height = 500;
    var ScreenWidth = screen.width;
    var ScreenHeight = screen.height;

    var left = (ScreenWidth - width) / 2;
    var top = (ScreenHeight - height) / 2;

    if(confirm('Exportar os Pedidos ?')){
        window.open('modulos/integracao_datasul/interface_pedido_exp.php','exportacao_pedido','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,width=' + width + ',height=' + height + ',left=' + left + ',top = ' + top).focus();
    }
    return false;
}

/*
 * Exporta��o de ordens de faturamento via arquivo texto
 */
function GeraArquivoTXTOrdemFaturamento(){
    var width = 500;
    var height = 500;
    var ScreenWidth = screen.width;
    var ScreenHeight = screen.height;

    var left = (ScreenWidth - width) / 2;
    var top = (ScreenHeight - height) / 2;

    if(confirm('Exportar as Ordens de Faturamento ?')){
        window.open('exportar_ordem_faturamento_post.php','exportacao_ordemfaturamento','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,width=' + width + ',height=' + height + ',left=' + left + ',top = ' + top).focus();
    }
    return false;
}


/*
 * Exporta��o de pedidos via arquivo xml
 */
function GeraArquivoXMLPedido(){
    var width = 500;
    var height = 500;
    var ScreenWidth = screen.width;
    var ScreenHeight = screen.height;

    var left = (ScreenWidth - width) / 2;
    var top = (ScreenHeight - height) / 2;

    if(confirm('Exportar os Pedidos ?')){
        window.open('modulos/integracao_xml/interface_pedido_exp_xml.php','exportacao_pedido_xml','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,width=' + width + ',height=' + height + ',left=' + left + ',top = ' + top).focus();
    }
    return false;
}

function abre_popup_integracao(Arquivo){
    if(Arquivo == ''){
        alert('Selecione uma op��o!')
        return false;
    }
    var id = Math.floor(Math.random()*1000);
    var width = 700;
    var height = 700;
    var left = ((screen.width - width) / 2);
    var top = ((screen.height - height) / 2);
    window.open(Arquivo,'integracao_'+id,'toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,width='+width+',height='+height+',left = '+left+',top='+top);
}

function getDialog(IdDIV){
    var Dialog;
    if(!IdDIV){
        var IdDIV = 'jquery-dialog';
    }
    if($("#" + IdDIV).length == 0){
        Dialog = $('<div id="jquery-dialog"></div>');
        Dialog.css("display","none");
        $("body").append(Dialog);
    }
    else{
        Dialog = $("#"+IdDIV);
    }
    return Dialog;
}

/* Fun��o de estoque */
function exibe_estoque_produto(IdProduto){
    if(IdProduto == ''){
        alert("Produto n�o informado!");
        return false;
    }
    var Dialog = getDialog();
    Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Estoque');
    Dialog.html(IMGLoadingGeral);
    Dialog.dialog({
        width: 600,
        height: 500,
        buttons:{Fechar: function(){$(this).dialog("close");$(this).dialog("destroy");}},
        open: function(){
            $.ajax({
                url: "modulos/estoque/ConsultaEstoque.php",
                global: false,
                type: "POST",
                data: ({
                    id_estabelecimento:'',
                    id_produto:IdProduto

                }),
                dataType: "html",
                async: true,
                beforeSend: function(){

                },
                error: function(){
                    alert('Erro com a requisi��o');
                },
                success: function(responseText){
                    Dialog.html(responseText);
                }
            });
        },
        modal: true,
        show: "fade",
        hide: "fade"
    });
    return true;
}

function visualizar_resultado_competencias(programa, coachee){
     var visualizar_resultado = getDialog('3');
        visualizar_resultado.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Compet�ncias');
        visualizar_resultado.html(IMGLoadingGeral);
        visualizar_resultado.dialog({
        width: 800,
        height: 500,
        buttons:{
                
                Fechar: function(){
                    $(this).dialog("close");
                    $(this).dialog("destroy");
                }
            },
        open: function(){
            $.ajax({
                url: "modulos/customizacoes/coaching/assessment/visualizar_assessments.php",
                global: false,
                type: "POST",
                data: ({
                    programa:programa,
                    coachee:coachee

                }),
                dataType: "html",
                async: true,
                beforeSend: function(){

                },
                error: function(){
                    alert('Erro com a requisi��o');
                },
                success: function(responseText){
                    visualizar_resultado.html(responseText);
                }
            });
        },
        modal: true,
        show: "fade",
        hide: "fade"
    });
    return true;
}
function competencias_gera_orcamanento (programa, coachee){
    $.ajax({
        url: "modulos/customizacoes/coaching/assessment/competencias_gera_orcamento.php",
        global: false,
        type: "POST",
        data: ({
            programa:programa,
            coachee:coachee

        }),
        dataType: "html",
        async: true,
        beforeSend: function(){

        },
        error: function(){
            alert('Erro ao Gerar Or�amento');
        },
        success: function(responseText){
            var regra = /^[0-9]+$/;
            if (responseText.match(regra)){
                decisao = confirm("Or�amento "+responseText+" Gerado com Sucesso. Deseja Abrir?");
                if (decisao){
                    window.open("http://www.crm.sbcoaching.com.br/modulos/venda/?pfuncao=orcamento&ptp_venda=1&ppagina=p1&pnumreg="+responseText+"&psubdet=&pread=0&pnpai=&pfixo=sn_digitacao_completa=0&pdiv=&pusuario_filtro=&pos_ini=0&cbxfiltro=numreg&edtfiltro=&pgetcustom=&pbloqincluir=&pbloqexcluir=&ptitulo=&cbxordem=order%20by%20numreg%20desc",'_newtab');
                    $(".ui-icon-closethick").click();    
                } 
            }else{
                alert(responseText);
            }
        }
    });
    
}



/* Fun��o de Visualiza Resultado Competencias */
function exibe_resultado_competencias(programa, coachee){
    var Dialog = getDialog('2');
    Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Resultado');
    Dialog.html(IMGLoadingGeral);
    Dialog.dialog({
        width: 800,
        height: 500,
        buttons:{
                
                "Gerar Proposta": function(){
                    competencias_gera_orcamanento(programa,coachee);
                },
                "Visualizar Compet�ncias": function(){
                    visualizar_resultado_competencias(programa,coachee)
                },
                Fechar: function(){
                    $(this).dialog("close");
                    $(this).dialog("destroy");
                }
            },
        open: function(){
            $.ajax({
                url: "modulos/customizacoes/coaching/assessment/resultado_assessments.php",
                global: false,
                type: "POST",
                data: ({
                    programa:programa,
                    coachee:coachee

                }),
                dataType: "html",
                async: true,
                beforeSend: function(){

                },
                error: function(){
                    alert('Erro com a requisi��o');
                },
                success: function(responseText){
                    Dialog.html(responseText);
                }
            });
        },
        modal: true,
        show: "fade",
        hide: "fade"
    });
    return true;
}
/*
 * TRATAMENTO CADASTRO DE ATENDIMENTO
 *
 */
function set_valor_padrao(tipo_solicitacao,produto,sn_lupa_popup){
    var AJAX = XMLHTTPRequest();
    var send = '';
    send += 'tipo_solicitacao=' + tipo_solicitacao;
    send += '&produto=' + produto;

    AJAX.open("POST", "modulos/atividades/atendimento_automacao.php", false);
    AJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    AJAX.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    AJAX.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    AJAX.setRequestHeader("Pragma", "no-cache");
    AJAX.send(send);
    var resp = AJAX.responseText;
    var ar_resp = resp.split(';');
    if (sn_lupa_popup=='1') {
        window.opener.document.getElementById('edtacao_id_usuario_resp').value = ar_resp[0];
        window.opener.document.getElementById('edtacao_id_prioridade').value = ar_resp[1];
        window.opener.document.getElementById('edtacao_id_tp_atividade').value = ar_resp[2];
        window.opener.document.getElementById('edtacao_assunto').value = ar_resp[3];
        window.opener.document.getElementById('edtacao_sn_gerar_oportunidade').value = ar_resp[4];
        window.opener.document.getElementById('edtacao_sn_gerar_orcamento').value = ar_resp[5];
    } else {
        document.getElementById('edtacao_id_usuario_resp').value = ar_resp[0];
        document.getElementById('edtacao_id_prioridade').value = ar_resp[1];
        document.getElementById('edtacao_id_tp_atividade').value = ar_resp[2];
        document.getElementById('edtacao_assunto').value = ar_resp[3];
        document.getElementById('edtacao_sn_gerar_oportunidade').value = ar_resp[4];
        document.getElementById('edtacao_sn_gerar_orcamento').value = ar_resp[5];
    }
;
}

//=======================================================================================================================================================================
//ARQUIVO N�O CORRIGIDO
//=======================================================================================================================================================================
/*===================================
Fun��es para automatizacao das atividades
====================================*/
function set_cb_generic(acao,valor,campo_filho){
    var AJAX = XMLHTTPRequest();
    var send = '';
    send += 'acao=' + acao;
    send += '&valor=' + valor;
    AJAX.open("POST", "modulos/atividades/ativ_automacao.php", true);
    AJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    AJAX.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    AJAX.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    AJAX.setRequestHeader("Pragma", "no-cache");
    AJAX.send(send);
    AJAX.onreadystatechange = function() {
        if (AJAX.readyState == 4) {
            resp = AJAX.responseText;
            document.getElementById(campo_filho).value = resp;
        }
    }
;
}
function set_cb_gc_linha_fabr_prod(acao,valor){
    var AJAX = XMLHTTPRequest();
    var send = '';
    send += 'acao=' + acao;
    send += '&valor=' + valor;
    AJAX.open("POST", "modulos/atividades/ativ_automacao.php", true);
    AJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    AJAX.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    AJAX.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    AJAX.setRequestHeader("Pragma", "no-cache");
    AJAX.send(send);
    AJAX.onreadystatechange = function() {
        if (AJAX.readyState == 4) {
            var resp = AJAX.responseText;
            var ar_resp = resp.split(';');
            document.getElementById('edtid_usuario_gp').value = ar_resp[0];
            document.getElementById('edtlinha').value = ar_resp[1];
            document.getElementById('edtid_fabrica').value = ar_resp[2];
            document.getElementById('edtdescrid_fabrica').value = ar_resp[3];
        }
    }
;
}

/*
* Funcoes Padr�o
*/
function gebi(id) {
    return document.getElementById(id);
}

/*
* CLIENTES
*/


//=======================================================================================================================================================================
//OASIS
//=======================================================================================================================================================================
function dtbr2en(dt){
    if(dt!=''){
        var Dia = dt.substring(0,2);
        var Mes = dt.substring(3,5);
        var Ano = dt.substring(6,10);
        var Data = Ano+'-'+Mes+'-'+Dia;
    }
    else{
        var Data = '';
    }
    return Data;
}
function openModal(pUrl, pWidth, pHeight) {
    if (window.showModalDialog) {
        return window.showModalDialog(pUrl, window,
            "dialogWidth:" + pWidth + "px;dialogHeight:" + pHeight + "px");
    } else {
        try {
            netscape.security.PrivilegeManager.enablePrivilege(
                "UniversalBrowserWrite");
            window.open(pUrl, "wndModal", "width=" + pWidth
                + ",height=" + pHeight + ",resizable=no,modal=yes");
            return true;
        }
        catch (e) {
            alert("Script n�o confi�vel, n�o � poss�vel abrir janela modal.");
            return false;
        }
    }
}
function abre_tela_nova(arquivo,janela,width,height,status) {
    //openModal(arquivo,width,height);
    window.open(arquivo, janela,'toolbar=0,scrollbars=1,location=0,status='+ status +',menubar=0,resizable=1,width='+width+',height='+height+',left = '+(screen.width - width)/2+',top = '+(screen.height - height)/2);
}
function agenda_convida_participante_ajax(){
    //Executa a fun��o objetoXML()
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {
                alert(xmlhttp.responseText);
            }
        }
    };
    xmlhttp.open('post', 'workflow/agenda/convida_part_auto_email.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send('id_atividade=' + document.getElementById('edtid_atividade').value);
    return false;
}
function valida_cad_ajax(){
    var send = '';
    for(i=0;i<document.cad.elements.length;i++){
        send += document.cad.elements[i].name + '=' + document.cad.elements[i].value + '&';
    }
    //Executa a fun��o objetoXML()
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {

                if(xmlhttp.responseText != ''){
                    Submit = "N";
                    Break = "N";
                    eval(xmlhttp.responseText);
                    if(Submit == "S"){
                        document.cad.submit();
                    }
                }
                else{
                    document.cad.submit();
                }
            }
        }
    };
    xmlhttp.open('post', 'valida_cad_ajax.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send(send);
    return false;
}
function convida_part_ativ(id_atividade){
    //Executa a fun��o objetoXML()
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {
                alert(xmlhttp.responseText);
            }
        }
    };
    xmlhttp.open('post', 'workflow/agenda/convida_part_auto_email.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send(send);
    return false;
}
function gera_id_protocolo(){
    //Executa a fun��o objetoXML()
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {
                if(xmlhttp.responseText.length < 15){
                    document.getElementById('edtnumero_protocolo').value = xmlhttp.responseText;
                }
                else{
                    document.getElementById('edtnumero_protocolo').value = '';
                }
            }
        }
    };
    xmlhttp.open('post', 'agenda/gera_id_protocolo.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send('tipo=' + document.getElementById('edtid_categoria_documento').value + '&numreg=' + document.getElementById('pnumreg').value);
    return false;
}
function valida_horas(Campo){
    horario = Campo.value.split(":");
    var horas = horario[0];
    var minutos = horario[1];
    var segundos = horario[2];
    if(Campo.value < 5 && Campo.value){
        alert("Hora inv�lida");
        Campo.value = '00:00';
        event.returnValue=false;
        Campo.style.background = "#EAEAEA"
        Campo.focus();
    }
    else if(horas > 23){ //para rel�gio de 12 horas altere o valor aqui
        alert("A hora n�o pode ser maior que 23");
        event.returnValue=false;
        Campo.style.background = "#EAEAEA"
        Campo.focus();

    }
    else if(minutos > 59){
        alert("O minuto n�o pode ser maior que 59");
        event.returnValue=false;
        Campo.style.background = "#EAEAEA"
        Campo.focus();
    }
    else{
        Campo.style.background = '';
    }
}
//=======================================================================================================================================================================
//OASIS-SFA
//=======================================================================================================================================================================
function doc_word(){
    var tipo = document.getElementById('edtTipo_Formulario').value;
    var link_ = '';
    if(tipo == ''){
        alert('Selecione o tipo de Formul�rio');
    }
    else if(tipo == '1' || tipo == '3'){
        link_ = 'equipamentos';
    }
    else if(tipo == '2' || tipo == '4'){
        link_ = 'pecas';
    }
    if(tipo != '' && link_ != ''){
        //alert("Por favor, aguarde o download do arquivo");
        window.location = 'modulos/customizacoes/proposta_safety_' + link_ + '.php?pid_cotacao=' + document.getElementById('edtNo_').value;
    }
}
//FUN��O AVAN�A EST�GIO OPORTUNIDADE
function ajax_avanca_oportunidade(campos){

    var agree = confirm("Confirme os dados para avan�ar o est�gio:\n -Valor estimado: " + document.getElementById('edtcalc_valor').value +"\n -Data Prev. Fechamento: " + document.getElementById('edtcalc_dtprev').value + "\n -% Probabilidade: " + document.getElementById('edtcalc_prob').value);
    if(agree){
        //Executa a fun��o objetoXML()
        var xmlhttp = XMLHTTPRequest();
        //Se o objeto de 'xmlhttp' n�o estiver true
        if (!xmlhttp) {
            //Insere no 'elemento' o texto atribu�do
            alert('N�o foi poss�vel avan�ar o est�gio da oportunidade');
            return;
        }
        xmlhttp.onreadystatechange = function () {
            //Se a requisi��o estiver completada
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                //Se o status da requisi��o estiver OK
                if (xmlhttp.status == 200) {
                    alert(xmlhttp.responseText);
                    exibe_programa('gera_cad_lista.php?pfuncao=rep_opportunity_entry&pexibedet=N&pread=S&pfixo=[Opportunity%20No_]@igual@s' + document.getElementById('edtNo_').value + '@s&pdrilldown=1&psubdet=28&pnpai=');
                //window.location.reload()
                }
                else {
                    //Insere no 'elemento' o texto atribu�do
                    alert('N�o foi poss�vel avan�ar o est�gio da oportunidade');
                }
            }
        }
        //Abre a p�gina que receber� os campos do formul�rio
        xmlhttp.open('POST', 'modulos/customizacoes/avan_est_opor.php?'+campos, true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
        xmlhttp.send(campos);

    //Fim do if Agree
    }
}
function setarCampos() {
    campos = "numreg="+document.getElementById('edtNo_').value
    campos += "&estagio_atual="+document.getElementById('edtcalc_estagio').value
    campos += "&valor_estimado="+document.getElementById('edtcalc_valor').value
    campos += "&dt_prev_fechamento="+document.getElementById('edtcalc_dtprev').value
    campos += "&prob="+document.getElementById('edtcalc_prob').value
}
function calc_desc_iten_cot(){
    var v1 = 100-document.getElementById('edtcalc_desc_1').value*1;
    var v2 = 100-document.getElementById('edtcalc_desc_2').value*1;
    var v3 = 100-document.getElementById('edtcalc_desc_3').value*1;
    var Valor = document.getElementById('edtUnitary_Basis').value.replace(/\./gi, "").replace(/,/gi, "\.")*1;

    if(v1=='' || isNaN(v1)){
        v1 = 100;
    }
    if(v2=='' || isNaN(v2)){
        v2 = 100;
    }
    if(v3=='' || isNaN(v3)){
        v3 = 100;
    }
    if(Valor=='' || isNaN(Valor)){
        Valor = 0;
    }

    Valor_desc = (v1/100)*Valor;

    Valor_desc2 = (v2/100)*Valor_desc;

    Valor_desc3 = (v3/100)*Valor_desc2;

    Diferenca = Valor - Valor_desc3;

    Desc_final_pct = (Diferenca*100)/Valor;
    Desc_final_pct = Desc_final_pct.toFixed(2)

    if(!isNaN(Desc_final_pct)){
        document.getElementById('edtLine_Discount_%').value = Desc_final_pct.replace(".",",");
    }
}

//=======================================================================================================================================================================
//OASIS-PROJECT
//=======================================================================================================================================================================
function validaCNPJ(elemento) {

    CNPJ = document.getElementById(elemento).value;
    CNPJCampo = document.getElementById(elemento);
    if (CNPJ != ''){
        var a = [];
        var b = new Number;
        var c = [6,5,4,3,2,9,8,7,6,5,4,3,2];
        for (i=0; i<12; i++){
            a[i] = CNPJ.charAt(i);
            b += a[i] * c[i+1];
        }
        if ((x = b % 11) < 2) {
            a[12] = 0
        }else{
            a[12] = 11-x
        }
        b = 0;
        for (y=0; y<13; y++) {
            b += (a[y] * c[y]);
        }
        if ((x = b % 11) < 2) {
            a[13] = 0;

        }else{
            a[13] = 11-x;
        }
        if ((CNPJ.charAt(12) != a[12]) || (CNPJ.charAt(13) != a[13]) || CNPJ.length != 14 ){
            alert('CNPJ inv�lido \n\n');
            setTimeout("document.getElementById('" + elemento + "').focus();", 50);
            return false;
        }
        return true;
    }
}
function validaCPF(cpf){
    erro = new String;
    if (cpf.value.length == 11){
        cpf.value = cpf.value.replace('.', '');
        cpf.value = cpf.value.replace('.', '');
        cpf.value = cpf.value.replace('-', '');

        var nonNumbers = /\D/;

        if (nonNumbers.test(cpf.value)){
            erro = "A verificacao de CPF suporta apenas n�meros!";
        } else {
            if (cpf.value == "00000000000" ||
                cpf.value == "11111111111" ||
                cpf.value == "22222222222" ||
                cpf.value == "33333333333" ||
                cpf.value == "44444444444" ||
                cpf.value == "55555555555" ||
                cpf.value == "66666666666" ||
                cpf.value == "77777777777" ||
                cpf.value == "88888888888" ||
                cpf.value == "99999999999") {

                erro = "N�mero de CPF inv�lido!"
            }

            var a = [];
            var b = new Number;
            var c = 11;

            for (i=0; i<11; i++){
                a[i] = cpf.value.charAt(i);
                if (i < 9) b += (a[i] * --c);
            }

            if ((x = b % 11) < 2) {
                a[9] = 0
            } else {
                a[9] = 11-x
            }
            b = 0;
            c = 11;

            for (y=0; y<10; y++) b += (a[y] * c--);

            if ((x = b % 11) < 2) {
                a[10] = 0;
            } else {
                a[10] = 11-x;
            }

            if ((cpf.value.charAt(9) != a[9]) || (cpf.value.charAt(10) != a[10])) {
                erro = "N�mero de CPF inv�lido.";
            }
        }
    } else {
        if(cpf.value.length == 0) {
            return false
        } else {
            erro = "N�mero de CPF inv�lido.";
        }
    }
    if (erro.length > 0) {
        alert(erro);
        setTimeout("document.getElementById('" + cpf.id + "').focus();", 50);
        return false;
    }
    return true;
}
function valida_cnpj_cpf_ajax(tp_doc){
    //Executa a fun��o objetoXML()
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {
                if(xmlhttp.responseText != ''){
                    alert('Este ' + tp_doc + ' j� est� cadastrado.');
                    //alert(xmlhttp.responseText);
                    setTimeout("document.getElementById('edtcnpj_cpf').focus();", 50);
                    return false;
                }
            }
        }
    };
    xmlhttp.open('post', 'modulos/customizacoes/valida_cnpj_cpf.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send('edtcnpj_cpf=' + document.getElementById('edtcnpj_cpf').value + '&edtid_pessoa=' + document.getElementById('edtid_pessoa').value);
    return false;
}
function gera_acao_lista_segmentacao(){
    //Executa a fun��o objetoXML()
    var xmlhttp = XMLHTTPRequest();
    var id_lista = document.getElementById('edtid_lista').value;
    var id_acao = document.getElementById('edtid_acao').value;
    var qtdemaxdia = document.getElementById('edtqtdemaxdia').value;
    var qtdemaxusuario = document.getElementById('edtqtdemaxusuario').value;
    var sn_resp_div_vend = document.getElementById('edtsn_resp_div_vend').value;
    var sn_duplicar_atividade = document.getElementById('edtsn_duplicar_atividade').value;
    var id_usuarios = '';
    for(i=0;i<document.forms[0].elements.length;i++){
        if(document.forms[0].elements[i].type == "checkbox"){
            if(document.forms[0].elements[i].checked == true){
                id_usuarios += document.forms[0].elements[i].value;
                if((i > 0) && (i < (document.forms[0].elements.length-1))){
                    id_usuarios += ";";
                }
            }
        }
    }

    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {
                if(xmlhttp.responseText != ''){
                    alert(xmlhttp.responseText);
                    return false;
                }
            }
        }
    };
    xmlhttp.open('post', 'modulos/gera_acao_lista_segmentacao_post.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send('id_lista=' + id_lista + '&id_acao=' + id_acao + '&qtdemaxdia=' + qtdemaxdia + '&qtdemaxusuario=' + qtdemaxusuario + '&sn_resp_div_vend=' + sn_resp_div_vend + '&sn_duplicar_atividade=' + sn_duplicar_atividade + '&id_usuarios=' + id_usuarios);
    return false;
}
function bloqueios_campos_custom_p1(){
    //Executa a fun��o objetoXML()
    send = '';
    for(i=0;i<document.forms[0].elements.length;i++){
        send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].value + '&';
    }
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {
                document.getElementById('div_cont_bloqueio').innerHTML = (xmlhttp.responseText);
            }
        }
        else{
            document.getElementById('div_cont_bloqueio').innerHTML = 'Carregando...';
        }
    };
    xmlhttp.open('post', 'modulos/bloqueios/campos_p1.php', true);
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send(send);
    return false;
}
function bloqueios_mestre_det_custom_p1(){
    //Executa a fun��o objetoXML()
    send = '';
    for(i=0;i<document.forms[0].elements.length;i++){
        send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].value + '&';
    }
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {
                document.getElementById('div_cont_bloqueio').innerHTML = (xmlhttp.responseText);
            }
        }
        else{
            document.getElementById('div_cont_bloqueio').innerHTML = 'Carregando...';
        }
    };
    xmlhttp.open('post', 'modulos/bloqueios/mestre_det_p1.php', true);
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send(send);
    return false;
}

function bloqueios_custom_p1(){
    //Executa a função objetoXML()
    send = '';
    for(i=0;i<document.forms[0].elements.length;i++){
        send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].value + '&';
    }
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {
                document.getElementById('div_cont_bloqueio').innerHTML = (xmlhttp.responseText);
            }
        }
        else{
            document.getElementById('div_cont_bloqueio').innerHTML = 'Carregando...';
        }
    };
    xmlhttp.open('post', 'modulos/bloqueios/p1.php', true);
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send(send);
    return false;
}

function bloqueios_custom_p2(){
    if(confirm("Confirma alterações ?")){
        //Executa a fun��o objetoXML()
        send = '';
        for(i=0;i<document.forms[0].elements.length;i++){
            if(document.forms[0].elements[i].type == 'checkbox'){
                send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].checked + '&';
            }
            else{
                send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].value + '&';
            }
        }
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function () {
            //Se a requisi��o estiver completada
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                //Se o status da requisi��o estiver OK
                if (xmlhttp.status == 200) {
                    alert(xmlhttp.responseText);
                }
            }
        };
        xmlhttp.open('post', 'modulos/bloqueios/p2.php', true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
        xmlhttp.send(send);
    }
    return false;
}


function bloqueios_cad_custom_p1(){
    //Executa a fun��o objetoXML()
    send = '';
    for(i=0;i<document.forms[0].elements.length;i++){
        send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].value + '&';
    }
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {
                document.getElementById('div_cont_bloqueio').innerHTML = (xmlhttp.responseText);
            }
        }
        else{
            document.getElementById('div_cont_bloqueio').innerHTML = 'Carregando...';
        }
    };
    xmlhttp.open('post', 'modulos/bloqueios/cad_p1.php', true);
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send(send);
    return false;
}

function bloqueios_campos_custom_p2(){
    if(confirm("Confirma altera��es ?")){
        //Executa a fun��o objetoXML()
        send = '';
        for(i=0;i<document.forms[0].elements.length;i++){
            if(document.forms[0].elements[i].type == 'checkbox'){
                send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].checked + '&';
            }
            else{
                send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].value + '&';
            }
        }
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function () {
            //Se a requisi��o estiver completada
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                //Se o status da requisi��o estiver OK
                if (xmlhttp.status == 200) {
                    alert(xmlhttp.responseText);
                }
            }
        };
        xmlhttp.open('post', 'modulos/bloqueios/campos_p2.php', true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
        xmlhttp.send(send);
    }
    return false;
}
function bloqueios_mestre_det_custom_p2(){
    if(confirm("Confirma altera��es ?")){
        //Executa a fun��o objetoXML()
        send = '';
        for(i=0;i<document.forms[0].elements.length;i++){
            if(document.forms[0].elements[i].type == 'checkbox'){
                send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].checked + '&';
            }
            else{
                send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].value + '&';
            }
        }
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function () {
            //Se a requisi��o estiver completada
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                //Se o status da requisi��o estiver OK
                if (xmlhttp.status == 200) {
                    alert(xmlhttp.responseText);
                }
            }
        };
        xmlhttp.open('post', 'modulos/bloqueios/mestre_det_p2.php', true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
        xmlhttp.send(send);
    }
    return false;
}
function bloqueios_cad_custom_p2(){
    if(confirm("Confirma altera��es ?")){
        //Executa a fun��o objetoXML()
        send = '';
        for(i=0;i<document.forms[0].elements.length;i++){
            if(document.forms[0].elements[i].type == 'checkbox'){
                send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].checked + '&';
            }
            else{
                send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].value + '&';
            }
        }
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function () {
            //Se a requisi��o estiver completada
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                //Se o status da requisi��o estiver OK
                if (xmlhttp.status == 200) {
                    alert(xmlhttp.responseText);
                }
            }
        };
        xmlhttp.open('post', 'modulos/bloqueios/cad_p2.php', true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
        xmlhttp.send(send);
    }
    return false;
}

//=======================================================================================================================================================================
//RELAT�RIOS
//=======================================================================================================================================================================
function ajax_gera_relatorio_prod_ranking(){
    //window.open('modulos/relatorio_pedidos_post_colgate.php?dt_de=' + document.getElementById('edtdt_de').value + '&dt_ate=' + document.getElementById('edtdt_ate').value, 'relatorio_de_pedidos_colgate','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=0,width=750,height=550').focus();
    var GetUrl = '?dt_de=' + document.getElementById('edtdt_de').value;
    GetUrl += '&dt_ate=' + document.getElementById('edtdt_ate').value;
    GetUrl += '&formato=' + document.getElementById('edtformato').value;
    GetUrl += '&id_cliente=' + document.getElementById('edtid_empresa').value;
    window.open('relatorios/pedidos/relatorio_prod_ranking_post.php' + GetUrl, 'relatorio_prod_ranking_post','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=0,width=750,height=550').focus();
}
function ajax_gera_relatorio_pedido_periodo(){
    window.open('modulos/relatorio_pedidos_post.php?dt_de=' + document.getElementById('edtdt_de').value + '&dt_ate=' + document.getElementById('edtdt_ate').value, 'relatorio_de_pedidos','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=0,width=750,height=550').focus();
}
function ajax_gera_relatorio_pedido_pedido(){
    window.open('modulos/relatorio_pedidos_post.php?n_pedidos=' + document.getElementById('edtn_pedidos').value, 'relatorio_de_pedidos','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=0,width=750,height=550').focus();
}
function ajax_gera_relatorio_pedido_colgate(){
    //window.open('modulos/relatorio_pedidos_post_colgate.php?dt_de=' + document.getElementById('edtdt_de').value + '&dt_ate=' + document.getElementById('edtdt_ate').value, 'relatorio_de_pedidos_colgate','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=0,width=750,height=550').focus();
    var GetUrl = '?dt_de=' + document.getElementById('edtdt_de').value;
    GetUrl += '&dt_ate=' + document.getElementById('edtdt_ate').value;
    GetUrl += '&formato=' + document.getElementById('edtformato').value;
    GetUrl += '&id_representante=' + document.getElementById('edtid_representante').value;
    window.open('modulos/relatorio_pedidos_post_colgate.php' + GetUrl, 'relatorio_de_pedidos_colgate','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=0,width=750,height=550').focus();
}
function ajax_gera_relatorio_pedido_ativ_rec(){
    window.open('modulos/relatorio_pedidos_ativ_rec_post.php?dt_de=' + document.getElementById('edtdt_de').value + '&dt_ate=' + document.getElementById('edtdt_ate').value + '&id_vendedor=' + document.getElementById('edtid_vendedor').value, 'relatorio_de_pedidos_ativ_rec','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=0,width=750,height=550').focus();
}
function ajax_gera_relatorio_cliente_colgate(){
    var GetUrl = '?dt_de=' + document.getElementById('edtdt_de').value + '&dt_ate=' + document.getElementById('edtdt_ate').value;
    GetUrl += '&formato=' + document.getElementById('edtformato').value;
    GetUrl += '&situacao=' + document.getElementById('edtsituacao').value;
    GetUrl += '&id_representante=' + document.getElementById('edtid_representante').value;
    window.open('modulos/relatorio_clientes_colgate_post.php' + GetUrl, 'relatorio_de_clientes_colgate','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=0,width=750,height=550').focus();
}

//=======================================================================================================================================================================
//OASIS-PROJECT
//=======================================================================================================================================================================

function simular_atividade(){
    var dt_inicio = gebi('edtdt_inicio').value
    var dt_prev_fim = gebi('edtdt_prev_fim').value
    dt_incio = dt_inicio.substring(6,10) + '-' + dt_inicio.substring(3,5) + '-' + dt_inicio.substring(0,2);
    dt_prev_fim = dt_prev_fim.substring(6,10) + '-' + dt_prev_fim.substring(3,5) + '-' + dt_prev_fim.substring(0,2);

    var Link = '';
    Link += 'project/grafico_detalhe.php?';
    Link += 'acao=T';
    Link += '&id_atividade_pai=' + gebi('edtid_projeto').value;
    Link += '&id_usuario_resp=' + gebi('edtid_usuario_resp').value;
    Link += '&dt_inicio=' + dt_incio;
    Link += '&dt_prev_fim=' + dt_prev_fim;
    Link += '&id_situacao=' + gebi('edtid_situacao').value;
    Link += '&id_projeto=' + gebi('edtid_projeto').value;
    Link += '&id_macro_atividade=' + gebi('edtid_macro_atividade').value;
    Link += '&id_atividade=' + gebi('edtid_atividade').value;
    window.open(Link, 'grafico_simulacao','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=1,width=500,height=500').focus();
}
function abre_tela_grafico_projeto() {
    //openModal(arquivo,width,height);
    var Window = window.open('project/grafico_projetos.php', 'Gr�fico','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=1,width=500,height=500');
    Window.focus();
}
function exibir_versao_projeto(){
    var Link = "project/grafico_detalhe.php?id_projeto=" + document.getElementById('edtid_projeto').value + "&id_versao=" + document.getElementById('edtversao').value;
    window.open(Link, 'grafico_simulacao','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=1,width=500,height=500');
}
function backup_projeto(id_projeto){
    Agree = confirm('Voc� deseja armazenar uma vers�o deste projeto ?');
    if(Agree){
        //Executa a fun��o objetoXML()
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function () {
            //Se a requisi��o estiver completada
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                //Se o status da requisi��o estiver OK
                document.getElementById('div_loading').style.display = 'none';
                if (xmlhttp.status == 200) {
                    alert(xmlhttp.responseText);
                }

            }
            else {//if(xmlhttp.readyState == 4){
                document.getElementById('div_loading').style.left = (((screen.width - 200) / 2) + document.body.scrollLeft);
                document.getElementById('div_loading').style.top = ((((screen.height - 100) / 2) - 100) + document.body.scrollTop);
                document.getElementById('div_loading').style.display = 'block';

            }
        };
        xmlhttp.open('post', 'guardar_projeto.php', true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
        xmlhttp.send('id_projeto=' + id_projeto);
    }

    return false;
}
//=======================================================================================================================================================================
//OASIS-AGENDA PROTOTIPO
//=======================================================================================================================================================================
function funcao(){
    var qst = 'O Diretor Geral já tem um compromisso marcado para o período selecionado.\n';
    qst += 'A próxima data de disponibilidade de todos é dia sexta-feira 14 de novembro de 2008.\n'
    qst += '\nIncluir este compromisso assim mesmo?';
    var agree = confirm(qst);
}
function dica(strDica, e){
    var posx = 0;
    var posy = 0;
    var alt_tela = screen.height;

    if (!e) var e = window.event;
    if (e.pageX || e.pageY) 	{
        posx = e.pageX;
        posy = e.pageY;
    }else if (e.clientX || e.clientY) 	{
        posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
        posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop -500;
    }

    var divDica = document.getElementById("dica");

    if(strDica=='') //Esconde a DIV
    {
        divDica.style.visibility = "hidden";
        divDica.style.display = "none";

    }else { //Exibe a DIV

        divDica.style.left = posx - 125 + "px";
        divDica.style.display = "block";
        divDica.innerHTML = strDica;
        


        divDica.style.visibility = "visible";

    }
}
function abrir_pop_up(){
    window.open('agenda/detalhe_compromisso_novo.html','agenda_compromissos3','location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=100,left=100');
}
function Executa_JS_Ajax(Divfrom,Divto){
    // Pegando os valores das Tags <script> que est�o na p�gina carregada pelo AJAX
    var scripts = document.getElementById(Divfrom).getElementsByTagName("script");
    // Aki, vamos inserir o conte�do da tag <script> que pegamos na linha acima
    for(i = 0; i < scripts.length; i++){
        // Pegando a div que recebr� o JavaScript
        var conteudo = document.getElementById(Divto);
        // Declarando a cria��o de uma nova tag <script>
        var newElement = document.createElement("script");
        newElement.text = scripts[i].innerHTML;
        conteudo.appendChild(newElement);
    }
// Agora, inserimos a nova tag <script> dentro da div na p�gina inicial
}
function gera_campo_atualizacao_global(Id_Campo){
    //Executa a fun��o objetoXML()
    var xmlhttp = XMLHTTPRequest();
    document.getElementById('divid_campo').innerHTML = 'Carregando...';
    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {
                document.getElementById('divid_campo').innerHTML = xmlhttp.responseText;
                Executa_JS_Ajax('divid_campo','divid_campo_script');
            }
        }
    };
    xmlhttp.open('post', 'modulos/gera_campo_atualizacao_global.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send('id_campo=' + Id_Campo);
    return false;
}
function atualizacao_global_post(id_lista,id_campo,valor){
    Agree1 = confirm('Voc� confirma altera��o de todas as pessoas da lista selecionada ?');
    if(Agree1){
        Agree2 = confirm('N�o ser� poss�vel desfazer esta a��o.\nRealmente deseja continuar ?');
    }
    if(Agree1 && Agree2){
        alert('Por favor aguarde o processamento.');
        //Executa a fun��o objetoXML()
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function () {
            //Se a requisi��o estiver completada
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                //Se o status da requisi��o estiver OK
                if (xmlhttp.status == 200) {
                    if(xmlhttp.responseText != ''){
                        alert(xmlhttp.responseText);
                    }
                }
            }
        };
        xmlhttp.open('post', 'modulos/atualizacao_global_post.php', true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
        xmlhttp.send('id_lista=' + id_lista + '&id_campo=' + id_campo + '&valor=' + valor);
    }
    return false;
}
function relatorio_cliche_p1(){
    //Executa a fun��o objetoXML()
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {
                if(xmlhttp.responseText != ''){
                    document.getElementById('div_conteudo').innerHTML = xmlhttp.responseText;
                }
            }
        }
        else{
            document.getElementById('div_conteudo').innerHTML = '<img src="images/wait.gif">';
        }
    };
    xmlhttp.open('post', 'modulos/customizacoes/cliches/gera_lista_cliche.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send('dt_de=' + document.getElementById('edtdt_de').value + '&dt_ate=' + document.getElementById('edtdt_ate').value);
    return false;
}
function relatorio_cliche_p2(){
    //Executa a fun��o objetoXML()
    var send = '';
    for(i=0;i<document.forms[0].elements.length;i++){
        if(document.forms[0].elements[i].type == "checkbox"){
            send += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].checked + '&';
        }
        else{
            NewString = document.forms[0].elements[i].value;
            NewString = NewString.replace(/\&/g, "edte_comercial");
            NewString = NewString.replace(/\+/g, "edtmais");
            NewString = NewString.replace(/\=/g, "edtigual");
            send += document.forms[0].elements[i].name + '=' + NewString + '&';
        }
    }
    //send = send.substr(0,(send.length-1));
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        //Se a requisi��o estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisi��o estiver OK
            if (xmlhttp.status == 200) {
                if(xmlhttp.responseText != ''){
                    eval(xmlhttp.responseText);
                //document.getElementById('div_conteudo').innerHTML = xmlhttp.responseText;
                }
            //window.open('modulos/customizacoes/cliches/cliche_download.php', 'Download1','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=0,width=750,height=550').focus();
            }
        }
    };
    xmlhttp.open('post', 'modulos/customizacoes/cliches/gera_doc_cliche.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
    xmlhttp.send(send);
    return false;
}
function Contar_Cliche(fd,tamanho,Mostrar){
    var texto = '|';
    var numero = fd.value.length;
    if(numero < 10){
        texto += '0' + numero;
    }
    else{
        texto += numero;
    }
    texto += '|';
    document.getElementById(Mostrar).childNodes[0].data=texto;
}
function ajax_export_pedido_post(){
    Agree1 = confirm('Exportar os Pedidos ?');
    if(Agree1){
        alert('Por favor aguarde o processamento.');
        //Executa a fun��o objetoXML()
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function () {
            //Se a requisi��o estiver completada
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                //Se o status da requisi��o estiver OK
                if (xmlhttp.status == 200) {
                    if(xmlhttp.responseText != ''){
                        alert(xmlhttp.responseText);
                    }
                }
            }
        };
        xmlhttp.open('post', 'modulos/integracao_datasul/html_export_pedido_post.php', true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
        xmlhttp.send(null);
    }
    return false;
}
function ajax_export_cliente_post(){
    Agree1 = confirm('Exportar os Clientes ?');
    if(Agree1){
        alert('Por favor aguarde o processamento.');
        //Executa a fun��o objetoXML()
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function () {
            //Se a requisi��o estiver completada
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                //Se o status da requisi��o estiver OK
                if (xmlhttp.status == 200) {
                    if(xmlhttp.responseText != ''){
                        alert(xmlhttp.responseText);
                    }
                }
            }
        };
        xmlhttp.open('post', 'modulos/integracao_datasul/html_export_cliente_post.php', true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        //Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
        xmlhttp.send(null);
    }
    return false;
}

//=======================================================================================================================================================================
//OASIS EXTERNAL FUNCTIONS
//=======================================================================================================================================================================
function openMeuModal(pUrl, pWidth, pHeight, pDados, pIdProduto) {
    window.open(pUrl + pDados + '&prodid=' + pIdProduto, 'codint','toolbar=0,scrollbars=1,location=0,status=1,menubar=0,resizable=1,width='+pWidth+',height='+pHeight+',left = '+(screen.width - pWidth)/2+',top = '+(screen.height - pHeight)/2);
}

function window_open_relatorio(){
    var GetUrl = '?';
    for(i=0;i<document.forms[0].elements.length;i++){
        GetUrl += document.forms[0].elements[i].name + '=' + document.forms[0].elements[i].value + '&';
    }
    var WindowName = document.getElementById('edtprelatorio').value;
    window.open('modulos/relatorios/relat_post.php' + GetUrl, WindowName,'toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=0,width=750,height=550').focus();
}

function opor_busca_preco_produto(id_tab_preco,id_campo_destino){
    var AJAX = XMLHTTPRequest();
    var send = '';
    send += 'id_produto=' + window.opener.document.getElementById('edtid_produto').value;
    send += '&id_tab_preco=' + id_tab_preco;
    AJAX.open("POST", "modulos/oportunidades/busca_preco_produto.php", false);
    AJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    AJAX.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    AJAX.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    AJAX.setRequestHeader("Pragma", "no-cache");
    AJAX.send(send);
    resp = AJAX.responseText;
    window.opener.document.getElementById(id_campo_destino).value = resp;
    window.opener.document.getElementById('edtvalor_total').value = '';
}
function opo_calcula_item(campo){
    var qtde = real2float(document.getElementById('edtqtde').value);
    var vl_desc = real2float(document.getElementById('edtpct_desc').value);
    var vl_unit = real2float(document.getElementById('edtvalor').value);
    var vl_total = (qtde * vl_unit) - ((vl_desc * (qtde * vl_unit))/100);
    if(campo.id == 'edtpct_desc'){
        if(vl_desc > 100){
            alert('O valor do desconto n�o pode ser superior a 100%');
            campo.value = '0,00';
            return false;
        }
    }
    document.getElementById('edtvalor_total').value = float2real(roundNumber(vl_total,2));
}
function opo_calcula_item_lupa(campo){
    var qtde = real2float(window.opener.document.getElementById('edtqtde').value);
    var vl_desc = real2float(window.opener.document.getElementById('edtpct_desc').value);
    var vl_unit = real2float(window.opener.document.getElementById('edtvalor').value);
    var vl_total = (qtde * vl_unit) - ((vl_desc * (qtde * vl_unit))/100);
    window.opener.document.getElementById('edtvalor_total').value = float2real(roundNumber(vl_total,2));
}
function float2real(valor){
    var valor = new String(valor);
    var retorno = valor.replace(/\./gi, ",");
    return retorno;
}
function real2float(valor){
    var retorno = valor.replace(/\./gi, "");
    retorno = retorno.replace(/,/gi, ".");
    return retorno;
}
function roundNumber(rnum, rlength) { // Arguments: number to round, number of decimal places
    var newnumber = Math.round(rnum*Math.pow(10,rlength))/Math.pow(10,rlength);
    return newnumber; // Output the result to the form field (change for your purposes)
}
function Executa_JS_Ajax(){
    var scripts = document.getElementById('div_conteudo').getElementsByTagName("script");
    for(i = 0; i < scripts.length; i++){
        var conteudo = document.getElementById("javascripts");
        var newElement = document.createElement("script");
        newElement.text = scripts[i].innerHTML;
        conteudo.appendChild(newElement);
    }
}
function add_item(id_session,campo,tabela,pfixo){
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            if (xmlhttp.status == 200) {
                ajax_exibe_itens(id_session);
                document.getElementById('edtdescrid_produto').value = '';
                document.getElementById('edtid_produto').value = '';
            }
        }
    };
    xmlhttp.open('post', 'modulos/itens/add_item.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlhttp.send('edtid_session='+id_session+'&campo='+campo+'&valor='+document.getElementById('edtid_produto').value+'&tabela='+tabela+'&pfixo='+pfixo);
}
function change_session_itens(session){
    var url = 'modulos/itens/change_session.php';
    var send = '';
    send = 'session=' + session;
    send += '&';
    send += 'id_session=' + document.getElementById('edtid_session').value;
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            if (xmlhttp.status == 200) {
            //document.getElementById('itens').innerHTML = xmlhttp.responseText;
            }else{
                alert('Falha ao atualizar campo.');
            }
        }
    };

    xmlhttp.open('post', url, true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlhttp.send(send);
}
function deletar_itens(id_produto,id_session){
    Agree = confirm('Deletar este produto?');
    if(Agree){
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                if (xmlhttp.status == 200) {
                    ajax_exibe_itens(document.getElementById('edtid_session').value);
                }
            }
        };
        xmlhttp.open('post', 'modulos/itens/remove_item.php', true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send('id_produto_delete=' + id_produto + '&edtid_session=' + id_session);
    }
}
function ajax_exibe_itens(id_session){
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            if (xmlhttp.status == 200) {
                document.getElementById('itens').innerHTML = xmlhttp.responseText;
            }
        }

        else{
            document.getElementById('itens').innerHTML = '<div align="center" valign="center"><img src="images/wait.gif" align="absmiddle" /></div>';
        }
    };
    xmlhttp.open('post', 'modulos/itens/tabela_itens.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlhttp.send('edtid_session=' + id_session + '&pread=S');
}
function atualizar_campo_din(id_session,campo,valor,val_chave){
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            if (xmlhttp.status == 200) {
        }
        }
    };
    xmlhttp.open('post', 'modulos/itens/atualiza_itens.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlhttp.send('edtid_session='+id_session+'&campo='+campo+'&valor='+valor+'&val_chave='+val_chave);
}
//------------------------------------------------------------------------------

function transforma_cliente(){
    var erro ='';
    var obrigatorios = new Array("edtcnpj_cpf","edtie_rg","edtnome_abreviado","edtcep","edtendereco","edtbairro","edtcidade","edtuf","edtid_grupo_cliente");
    var obrigatorios_c = new Array("CNPJ/CPF","Ie/Rg","Nome Abreviado","CEP","Endere�o","Bairro","Cidade","Estado","Segmento");
    if(document.getElementById('edtid_relac').value == '2'){
        for(var i=0;i<obrigatorios.length;i++){
            if(document.getElementById(obrigatorios[i]).value=="" && obrigatorios_c[i]!="undefined"){
                erro = erro + ' ' + obrigatorios_c[i] + ', ';
            // alert(obrigatorios_c[i] + i);
            }
        }
    }
    if(erro != ""){
        erro = "O(s) campo(s) " + erro + " devem ser preenchidos para o Prospect ser transformado em Cliente."
        alert(erro);
    } else {
        document.getElementById('edtid_relac').value = '4';
        document.cad.submit();
    //document.getElementById('snsalvar').value = 'S'; verificar();
    }
//    alert(document.getElementById('edtid_relac').value);
}

//------------------------------------------------------------------------------

function Executa_JS_Ajax(Divfrom,Divto){
    var scripts = document.getElementById(Divfrom).getElementsByTagName("script");
    for(i = 0; i < scripts.length; i++){
        var conteudo = document.getElementById(Divto);
        var newElement = document.createElement("script");
        newElement.text = scripts[i].innerHTML;
        conteudo.appendChild(newElement);
    }
}
function prop_calcula_item(campo){
    var qtde = real2float(document.getElementById('edtqtde_parcela').value);
    var vl_desc = real2float(document.getElementById('edtpct_desc').value);
    var vl_unit = real2float(document.getElementById('edtvalor').value);
    var vl_total = (qtde * vl_unit) - ((vl_desc * (qtde * vl_unit))/100);
    if(campo.id == 'edtpct_desc'){
        if(vl_desc > 100){
            alert('O valor do desconto n�o pode ser superior a 100%');
            campo.value = '0,00';
            return false;
        }
    }
    document.getElementById('edtvalor_total').value = float2real(roundNumber(vl_total,2));
}
function atualiza_tela_one(url,quant){
    if( quant <= 0){
        exibe_programa(url);
    } else {
        window.opener.atualiza_tela_one(url,quant-1);
    }
}
function Abre_Popup(arquivo,janela,width,height,status){
    //openModal(arquivo,width,height);
    window.open(arquivo, janela,'toolbar=0,scrollbars=1,location=0,status='+ status +',menubar=0,resizable=0,width=700,height=300,left = '+(screen.width - width)/2+',top = '+(screen.height - height)/2);

}

//==========================================================================================================================================
// CARREGA OS DADOS DO "USU�RIO ECF" PELO CNPJ OU PELO C�DIGO DO MESMO ( CASO TENHA SIDO DIGITADO E/OU CASO ESTEJA CORRETO )
//
// OBS.: FUN��O DESENVOLVIDA PARA LIBERMAC ( FORMUL�RIO DE CADASTRO DE ATESTADO DE INTERVEN��O )
//
//==========================================================================================================================================

function Carrega_Usuario_ECF(Valor_Buscado,Tipo_Busca){
    if(Valor_Buscado.length < 1){
        //Usu�rio ECF
        document.getElementById('edtid_pessoa_ue').value = '';
        document.getElementById('edtdescrid_pessoa_ue').value = '';
        // Equipamento ECF
        document.getElementById('edtid_equipamento_ecf').value = '';
        document.getElementById('edtdescrid_equipamento_ecf').value = '';
        document.getElementById('edtn_mfd_retirada').value = '';
        document.getElementById('edtdt_inicio_intervencao').value = '';
        document.getElementById('edtdt_term_intervencao').value = '';
        //Lacres
        document.getElementById('edtn_lacre_dispositivo_armazenamento_sof_basic_atual').value = '';
        document.getElementById('edtdescrn_lacre_dispositivo_armazenamento_sof_basic_atual').value = '';
        document.getElementById('edtn_lacre_ecf_atual_1').value = '';
        document.getElementById('edtdescrn_lacre_ecf_atual_1').value = '';
        document.getElementById('edtn_lacre_ecf_atual_2').value = '';
        document.getElementById('edtdescrn_lacre_ecf_atual_2').value = '';
        document.getElementById('edtn_lacre_ecf_atual_3').value = '';
        document.getElementById('edtdescrn_lacre_ecf_atual_3').value = '';
        //Numero de Etiquetas ou Selos
        document.getElementById('edtsoftware_basico_pcf').value = '';
        document.getElementById('edtcabo_pcf_mf_lado_pcf').value = '';
        document.getElementById('edtcabo_pcf_mf_lado_mf').value = '';
        document.getElementById('edtpcf_gabinete').value = '';
        document.getElementById('edtmem_fita_detalhe').value = '';
        document.getElementById('edtdescrmem_fita_detalhe').value = '';
        return false;
    }

    if(Tipo_Busca=='edtcnpj_busca_usuario_ecf'){
        var Mensagem = "CNPJ";
    }
    if(Tipo_Busca=='edtcod_busca_usuario_ecf'){
        var Mensagem = "valor";
    }

    if(Valor_Buscado.length > 0){
        if(Tipo_Busca=='edtcnpj_busca_usuario_ecf'){
            if(Valor_Buscado.length < 14){
                alert('O '+ Mensagem +' "'+ Valor_Buscado +'" � inv�lido');
                //Usu�rio ECF
                document.getElementById('edtcnpj_busca_usuario_ecf').value = '';
                document.getElementById('edtid_pessoa_ue').value = '';
                document.getElementById('edtdescrid_pessoa_ue').value = '';
                // Equipamento ECF
                document.getElementById('edtid_equipamento_ecf').value = '';
                document.getElementById('edtdescrid_equipamento_ecf').value = '';
                document.getElementById('edtn_mfd_retirada').value = '';
                document.getElementById('edtdt_inicio_intervencao').value = '';
                document.getElementById('edtdt_term_intervencao').value = '';
                //Lacres
                document.getElementById('edtn_lacre_dispositivo_armazenamento_sof_basic_atual').value = '';
                document.getElementById('edtdescrn_lacre_dispositivo_armazenamento_sof_basic_atual').value = '';
                document.getElementById('edtn_lacre_ecf_atual_1').value = '';
                document.getElementById('edtdescrn_lacre_ecf_atual_1').value = '';
                document.getElementById('edtn_lacre_ecf_atual_2').value = '';
                document.getElementById('edtdescrn_lacre_ecf_atual_2').value = '';
                document.getElementById('edtn_lacre_ecf_atual_3').value = '';
                document.getElementById('edtdescrn_lacre_ecf_atual_3').value = '';
                //Numero de Etiquetas ou Selos
                document.getElementById('edtsoftware_basico_pcf').value = '';
                document.getElementById('edtcabo_pcf_mf_lado_pcf').value = '';
                document.getElementById('edtcabo_pcf_mf_lado_mf').value = '';
                document.getElementById('edtpcf_gabinete').value = '';
                document.getElementById('edtmem_fita_detalhe').value = '';
                document.getElementById('edtdescrmem_fita_detalhe').value = '';
                return false;
            }
        }

        var url = 'modulos/Atestado_Intervencao/Pesquisa_Usuario_ECF.php';
        var send = '';
        send += 'Valor_Buscado=' + Valor_Buscado;
        send += '&';
        send += 'Tipo_Busca=' + Tipo_Busca;
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function (){
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0){
                if (xmlhttp.status == 200){
                    var xmlDoc = xmlhttp.responseXML;
                    var Resposta = xmlDoc.getElementsByTagName("Resposta_Busca")[0].firstChild.nodeValue;
                    if(Resposta=="TRUE"){
                        //Usu�rio ECF
                        document.getElementById('edtid_pessoa_ue').value = xmlDoc.getElementsByTagName("ID_Pessoa")[0].firstChild.nodeValue;
                        document.getElementById('edtdescrid_pessoa_ue').value = xmlDoc.getElementsByTagName("Razao_Social_Nome")[0].firstChild.nodeValue;
                        // Equipamento ECF
                        document.getElementById('edtid_equipamento_ecf').value = '';
                        document.getElementById('edtdescrid_equipamento_ecf').value = '';
                        document.getElementById('edtn_mfd_retirada').value = '';
                        document.getElementById('edtdt_inicio_intervencao').value = '';
                        document.getElementById('edtdt_term_intervencao').value = '';
                        //Lacres
                        document.getElementById('edtn_lacre_dispositivo_armazenamento_sof_basic_atual').value = '';
                        document.getElementById('edtdescrn_lacre_dispositivo_armazenamento_sof_basic_atual').value = '';
                        document.getElementById('edtn_lacre_ecf_atual_1').value = '';
                        document.getElementById('edtdescrn_lacre_ecf_atual_1').value = '';
                        document.getElementById('edtn_lacre_ecf_atual_2').value = '';
                        document.getElementById('edtdescrn_lacre_ecf_atual_2').value = '';
                        document.getElementById('edtn_lacre_ecf_atual_3').value = '';
                        document.getElementById('edtdescrn_lacre_ecf_atual_3').value = '';
                        //Numero de Etiquetas ou Selos
                        document.getElementById('edtsoftware_basico_pcf').value = '';
                        document.getElementById('edtcabo_pcf_mf_lado_pcf').value = '';
                        document.getElementById('edtcabo_pcf_mf_lado_mf').value = '';
                        document.getElementById('edtpcf_gabinete').value = '';
                        document.getElementById('edtmem_fita_detalhe').value = '';
                        document.getElementById('edtdescrmem_fita_detalhe').value = '';
                    //document.getElementById('edtn_mfd_retirada').disabled=true;
                    }
                    if(Resposta=="FALSE"){
                        //Usu�rio ECF
                        document.getElementById('edtcnpj_busca_usuario_ecf').value = '';
                        document.getElementById('edtid_pessoa_ue').value = '';
                        document.getElementById('edtdescrid_pessoa_ue').value = '';
                        // Equipamento ECF
                        document.getElementById('edtid_equipamento_ecf').value = '';
                        document.getElementById('edtdescrid_equipamento_ecf').value = '';
                        document.getElementById('edtn_mfd_retirada').value = '';
                        document.getElementById('edtdt_inicio_intervencao').value = '';
                        document.getElementById('edtdt_term_intervencao').value = '';
                        //Lacres
                        document.getElementById('edtsoftware_basico_pcf').value = '';
                        document.getElementById('edtcabo_pcf_mf_lado_pcf').value = '';
                        document.getElementById('edtcabo_pcf_mf_lado_mf').value = '';
                        document.getElementById('edtpcf_gabinete').value = '';
                        document.getElementById('edtmem_fita_detalhe').value = '';
                        alert('Usu�rio ECF n�o localizado');
                    //document.getElementById('edtn_mfd_retirada').disabled=true;
                    }
                } else{
                    alert('O servidor n�o responde.'+"\n"+'Por favor tente novamente.');
                }
            }
        };
        xmlhttp.open('post', url, true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xmlhttp.send(send);
    }
}


//=======================================================================================================================================================================
//VERIFICA SE A DATA 1 NAO � MAIOR QUE A DATA 2 NO CADASTRO DE ATESTADO DE INTERVENCAO
//
// OBS.: FUN��O DESENVOLVIDA PARA LIBERMAC ( FORMUL�RIO DE CADASTRO DE ATESTADO DE INTERVEN��O )
//
//=======================================================================================================================================================================
function verificaDataMaior(a, b){
    var data_inicial = document.getElementById(a);
    var data_final = document.getElementById(b);

    str_data_inicial = data_inicial.value;
    str_data_final   = data_final.value;
    dia_inicial      = data_inicial.value.substr(0,2);
    dia_final        = data_final.value.substr(0,2);
    mes_inicial      = data_inicial.value.substr(3,2);
    mes_final        = data_final.value.substr(3,2);
    ano_inicial      = data_inicial.value.substr(6,4);
    ano_final        = data_final.value.substr(6,4);
    if(ano_inicial > ano_final){
        alert("A data inicial deve ser menor que a data final.");
        data_inicial.focus();
        return false
    }else{
        if(ano_inicial == ano_final){
            if(mes_inicial > mes_final){
                alert("A data inicial deve ser menor que a data final.");
                data_final.focus();
                return false
            }else{
                if(mes_inicial == mes_final){
                    if(dia_inicial > dia_final){
                        alert("A data inicial deve ser menor que a data final.");
                        data_final.focus();
                        return false
                    }
                }
            }
        }
    }
    return true;
}
//==========================================================================================================================================
// PEGA O VALOR DO "ATESTADO ANTERIOR" ( CASO TENHA SIDO DIGITADO... ), VERIFICA NO BANCO DE DADOS E DEVOLVE OS VALORES PRO FORM
//
// OBS.: FUN��O DESENVOLVIDA PARA LIBERMAC ( FORMUL�RIO DE CADASTRO DE ATESTADO DE INTERVEN��O )
//
//==========================================================================================================================================
function Pesquisa_Atestado_Anterior(ID_Atestado_Anterior){
    if(ID_Atestado_Anterior==''){
        //Usu�rio ECF
        document.getElementById('edtid_pessoa_ue').value = '';
        document.getElementById('edtdescrid_pessoa_ue').value = '';
        //Equipamento ECF
        document.getElementById('edtid_equipamento_ecf').value = '';
        document.getElementById('edtdescrid_equipamento_ecf').value = '';
        document.getElementById('edtn_mfd_retirada').value = '';
        document.getElementById('edtdt_inicio_intervencao').value = '';
        document.getElementById('edtdt_term_intervencao').value = '';
        //Lacres
        document.getElementById('edtn_lacre_dispositivo_armazenamento_sof_basic_atual').value = '';
        document.getElementById('edtdescrn_lacre_dispositivo_armazenamento_sof_basic_atual').value = '';
        document.getElementById('edtn_lacre_ecf_atual_1').value = '';
        document.getElementById('edtdescrn_lacre_ecf_atual_1').value = '';
        document.getElementById('edtn_lacre_ecf_atual_2').value = '';
        document.getElementById('edtdescrn_lacre_ecf_atual_2').value = '';
        document.getElementById('edtn_lacre_ecf_atual_3').value = '';
        document.getElementById('edtdescrn_lacre_ecf_atual_3').value = '';
        //Numero de Etiquetas ou Selos
        document.getElementById('edtsoftware_basico_pcf').value = '';
        document.getElementById('edtcabo_pcf_mf_lado_pcf').value = '';
        document.getElementById('edtcabo_pcf_mf_lado_mf').value = '';
        document.getElementById('edtpcf_gabinete').value = '';
        document.getElementById('edtmem_fita_detalhe').value = '';
        document.getElementById('edtdescrmem_fita_detalhe').value = '';
        //document.getElementById('edtn_mfd_retirada').disabled=false;
        return false;
    }
    if(ID_Atestado_Anterior!=''){
        var url = 'modulos/Atestado_Intervencao/Pesquisa_Dados_Atestado_Anterior.php';
        var send = '';
        send += 'ID_Atestado_Anterior=' + ID_Atestado_Anterior;
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function (){
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0){
                if (xmlhttp.status == 200){
                    var xmlDoc = xmlhttp.responseXML;
                    var Resposta = xmlDoc.getElementsByTagName("Resposta_Busca")[0].firstChild.nodeValue;
                    if(Resposta=="TRUE"){
                        //Usu�rio ECF
                        document.getElementById('edtid_pessoa_ue').value = xmlDoc.getElementsByTagName("ID_Pessoa")[0].firstChild.nodeValue;
                        document.getElementById('edtdescrid_pessoa_ue').value = xmlDoc.getElementsByTagName("Razao_Social_Nome")[0].firstChild.nodeValue;
                        //Equipamento ECF
                        document.getElementById('edtid_equipamento_ecf').value = xmlDoc.getElementsByTagName("ID_Equipamento")[0].firstChild.nodeValue;
                        document.getElementById('edtdescrid_equipamento_ecf').value = xmlDoc.getElementsByTagName("Modelo_Equipamento")[0].firstChild.nodeValue;
                        document.getElementById('edtdt_inicio_intervencao').value = xmlDoc.getElementsByTagName("Data_Inicio_Intervencao")[0].firstChild.nodeValue;
                        document.getElementById('edtdt_term_intervencao').value = xmlDoc.getElementsByTagName("Data_Termino_Intervencao")[0].firstChild.nodeValue;
                        document.getElementById('edtn_mfd_retirada').value = xmlDoc.getElementsByTagName("MFD_Retirada")[0].firstChild.nodeValue;
                        //Lacres
                        document.getElementById('edtn_lacre_dispositivo_armazenamento_sof_basic_atual').value = xmlDoc.getElementsByTagName("n_lacre_dispositivo_armazenamento_software_basico")[0].firstChild.nodeValue;
                        document.getElementById('edtdescrn_lacre_dispositivo_armazenamento_sof_basic_atual').value = xmlDoc.getElementsByTagName("Numero_Lacre_Sof_Basic")[0].firstChild.nodeValue;
                        document.getElementById('edtn_lacre_ecf_atual_1').value =  xmlDoc.getElementsByTagName("n_lacre_ecf_1")[0].firstChild.nodeValue;
                        document.getElementById('edtdescrn_lacre_ecf_atual_1').value =  xmlDoc.getElementsByTagName("ECF_1")[0].firstChild.nodeValue;
                        document.getElementById('edtn_lacre_ecf_atual_2').value =  xmlDoc.getElementsByTagName("n_lacre_ecf_2")[0].firstChild.nodeValue;
                        document.getElementById('edtdescrn_lacre_ecf_atual_2').value =  xmlDoc.getElementsByTagName("ECF_2")[0].firstChild.nodeValue;
                        document.getElementById('edtn_lacre_ecf_atual_3').value =  xmlDoc.getElementsByTagName("n_lacre_ecf_3")[0].firstChild.nodeValue;
                        document.getElementById('edtdescrn_lacre_ecf_atual_3').value =  xmlDoc.getElementsByTagName("ECF_3")[0].firstChild.nodeValue;
                        //Numero de Etiquetas ou Selos
                        document.getElementById('edtsoftware_basico_pcf').value = xmlDoc.getElementsByTagName("Sof_Bas_PCF_AI")[0].firstChild.nodeValue;
                        document.getElementById('edtcabo_pcf_mf_lado_pcf').value = xmlDoc.getElementsByTagName("CAB_PCF_L_PCF")[0].firstChild.nodeValue;
                        document.getElementById('edtcabo_pcf_mf_lado_mf').value = xmlDoc.getElementsByTagName("CAB_PCF_L_MF")[0].firstChild.nodeValue;
                        document.getElementById('edtpcf_gabinete').value = xmlDoc.getElementsByTagName("PCF_Gab")[0].firstChild.nodeValue;
                        document.getElementById('edtmem_fita_detalhe').value = xmlDoc.getElementsByTagName("Mem_Fit_Det")[0].firstChild.nodeValue;
                        document.getElementById('edtdescrmem_fita_detalhe').value = xmlDoc.getElementsByTagName("Mem_Fit_Det_Detalhe")[0].firstChild.nodeValue;
                    //document.getElementById('edtn_mfd_retirada').disabled=true;
                    }
                    if(Resposta=="FALSE"){
                        document.getElementById('edtid_atestado_anterior').value = '';
                        //Usu�rio ECF
                        document.getElementById('edtid_pessoa_ue').value = '';
                        document.getElementById('edtdescrid_pessoa_ue').value = '';
                        //Equipamento ECF
                        document.getElementById('edtid_equipamento_ecf').value = '';
                        document.getElementById('edtdescrid_equipamento_ecf').value = '';
                        document.getElementById('edtdt_inicio_intervencao').value = '';
                        document.getElementById('edtdt_term_intervencao').value = '';
                        document.getElementById('edtn_mfd_retirada').value = '';
                        //Lacres
                        document.getElementById('edtn_lacre_dispositivo_armazenamento_sof_basic_atual').value = '';
                        document.getElementById('edtdescrn_lacre_dispositivo_armazenamento_sof_basic_atual').value = '';
                        document.getElementById('edtn_lacre_ecf_atual_1').value = '';
                        document.getElementById('edtdescrn_lacre_ecf_atual_1').value = '';
                        document.getElementById('edtn_lacre_ecf_atual_2').value = '';
                        document.getElementById('edtdescrn_lacre_ecf_atual_2').value = '';
                        document.getElementById('edtn_lacre_ecf_atual_3').value = '';
                        document.getElementById('edtdescrn_lacre_ecf_atual_3').value = '';
                        //Numero de Etiquetas ou Selos
                        document.getElementById('edtsoftware_basico_pcf').value = '';
                        document.getElementById('edtcabo_pcf_mf_lado_pcf').value = '';
                        document.getElementById('edtcabo_pcf_mf_lado_mf').value = '';
                        document.getElementById('edtpcf_gabinete').value = '';
                        document.getElementById('edtmem_fita_detalhe').value = '';
                        document.getElementById('edtdescrmem_fita_detalhe').value = '';
                        alert('Atestado de Interven��o n�o localizado');
                    //document.getElementById('edtn_mfd_retirada').disabled=true;
                    }
                }else{
                    alert('O servidor n�o responde.'+"\n"+'Por favor tente novamente.');
                }
            }
        };
        xmlhttp.open('post', url, true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xmlhttp.send(send);
    }
}

/* Ajax gerar relacionamento de pesquisa */
function gera_relacionamento_pesquisa(){
        //Executa a fun��o objetoXML()
        var xmlhttp = XMLHTTPRequest();
        var send = '';
        send += 'id_pesquisa=' + $('#edtid_pesquisa').val();
        send += '&id_cad=' + $('#edtid_cad').val();
        //send += '&data_de=' + $('#edtdt_de').val();
        //send += '&data_ate=' + $('#edtdt_ate').val();
        //send += '&formato=' + $('#edtformato').val();
        xmlhttp.onreadystatechange = function () {
                //Se a requisi��o estiver completada
                if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                //Se o status da requisiçãoo estiver OK
                        if (xmlhttp.status == 200) {
                                $('#resp').fadeIn(200);
                                $('#resp').html(xmlhttp.responseText);
                        }
                }
                else{
                        $('#resp').fadeIn(200);
                        $('#loading_bar').fadeIn(200);
                }
        };

        xmlhttp.open('post', 'modulos/pesquisas/relaciona_script_pesquisa_post.php', true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //Envia o formulário com dados da variável 'campos' (passado por parâmetro)
        xmlhttp.send(send);
}

function loading_show(div){
	div.html('<div class="loading"><img src="images/wait.gif" /></div>');

	$('.loading').fadeIn(1000);
	$('.loading').fadeTo("slow",0.8);	
}

function loading_hide(){
	$('.loading').fadeOut(10);
	$(".loading").remove();
}

function customToLowerCase(text,campo){
    var loweredText = text.toLowerCase();
    var words = loweredText.split(" ");
    for (var a = 0; a < words.length; a++) {
        var w = words[a];

        var firstLetter = w[0];
// aqui abaixo alterei

        if( w.length > 2){
            w = firstLetter.toUpperCase() + w.slice(1);
        } else {
            w = firstLetter + w.slice(1);
        }

        words[a] = w;
    }
    xpto = words.join(" ");
    $("#"+campo).val(xpto);

}
