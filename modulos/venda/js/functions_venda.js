/*
 * functions_venda.js
 * Autor: Alex
 * 01/11/2010 09:29:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
var HTMLLoading = '<div align="center"><img src="img/ajax_loading_bar.gif" alt="Carregando..."><br /><strong>Carregando...</strong></div>';
var IMGLoading = '<img src="img/loading.gif" alt="Carregando...">';

function p1_preenche_dados_conta(){
    MaskLoading('Mostrar');
    $.ajax({
        url: "dado_padrao_conta.php",
        global: false,
        type: "POST",
        data: ({
            numreg: $("#edtid_pessoa").val()
        }),
        dataType: "xml",
        async: true,
        beforeSend: function(){

        },
        error: function(){
            alert("Erro com a requisição");
            MaskLoading('Ocultar');
        },
        success: function(xml){
            MaskLoading('Ocultar');
            var Status = $(xml).find("status").text();
            if(Status == "1"){

                var Campos                          = $(xml).find("campos");
                var CamposAtualizar                 = $(xml).find("campos_atualizar").text();

                var ArrayCamposAtualizar = CamposAtualizar.split(',');

                var id_cond_pagto                   = Campos.find("id_cond_pagto").text();
                var nome_cond_pagto                 = Campos.find("nome_cond_pagto").text();
                var id_tab_preco                    = Campos.find("id_tab_preco").text();
                var nome_tab_preco                  = Campos.find("nome_tab_preco").text();
                var id_contato                      = Campos.find("id_contato").text();
                var nome_contato                    = Campos.find("nome_contato").text();
                var id_transportadora               = Campos.find("id_transportadora").text();
                var nome_transportadora             = Campos.find("nome_transportadora").text();
                var id_tp_frete                     = Campos.find("id_tp_frete").text();
                var sn_faturamento_parcial          = Campos.find("sn_faturamento_parcial").text();
                var id_representante_principal      = Campos.find("id_representante_principal").text();
                var nome_representante_principal    = Campos.find("nome_representante_principal").text();
                var id_endereco_entrega             = Campos.find("id_endereco_entrega").text();
                var nome_endereco_entrega           = Campos.find("nome_endereco_entrega").text();

                var NewOption;

                $("#edtid_endereco_entrega").val("");
                $("#edtdescrid_endereco_entrega").val("");

                if($.inArray('1', ArrayCamposAtualizar) != '-1'){
                    /* Tratamento para Cond. Pagto */
                    var CampoCondPagto = $("#edtid_cond_pagto");
                    if(CampoCondPagto.hasClass("venda_input_readonly") && id_cond_pagto != ''){
                        CampoCondPagto.children().remove().end();
                        NewOption = $("<option value=\"" + id_cond_pagto + "\">" + nome_cond_pagto + "</option>");
                        CampoCondPagto.append(NewOption);
                        $("#edtid_cond_pagto option:first").attr("selected","selected");
                    }
                    else{
                        CampoCondPagto.val(id_cond_pagto);
                    }
                }

                if($.inArray('2', ArrayCamposAtualizar) != '-1'){
                    /* Tratamento para Tabela de Preço */
                    var CampoTabPreco = $("#edtid_tab_preco");
                    if(CampoTabPreco.hasClass("venda_input_readonly") && id_tab_preco != ''){
                        CampoTabPreco.children().remove().end();
                        NewOption = $("<option value=\"" + id_tab_preco + "\">" + nome_tab_preco + "</option>");
                        $("#edtid_tab_preco").append(NewOption);
                        $("#edtid_tab_preco option:first").attr("selected","selected");
                    }
                    else{
                        $("#edtid_tab_preco").val(id_tab_preco);
                    }
                }

                if($.inArray('3', ArrayCamposAtualizar) != '-1'){
                    if(id_endereco_entrega != ''){
                        $("#edtid_endereco_entrega").val(id_endereco_entrega);
                        $("#edtdescrid_endereco_entrega").val(nome_endereco_entrega);
                    }
                }

                if($.inArray('4', ArrayCamposAtualizar) != '-1'){
                    $("#edtid_contato").val(id_contato);
                    $("#edtdescrid_contato").val(nome_contato);
                }

                if($.inArray('5', ArrayCamposAtualizar) != '-1'){
                    $("#edtid_transportadora").val(id_transportadora);
                    $("#edtdescrid_transportadora").val(nome_transportadora);
                }

                if($.inArray('6', ArrayCamposAtualizar) != '-1'){
                    /* Tratamento para Tipo de Frete */
                    var CampoTpFrete = $("#edtid_tp_frete");
                    if(CampoTpFrete.hasClass("venda_input_readonly") && id_tp_frete != ''){
                        CampoTpFrete.children().remove().end();
                        NewOption = $("<option value=\"" + id_tp_frete + "\">" + nome_tp_frete + "</option>");
                        CampoTpFrete.append(NewOption);
                        $("#edtid_tp_frete option:first").attr("selected","selected");
                    }
                    else{
                        CampoTpFrete.val(id_tp_frete);
                    }
                }

                if($.inArray('7', ArrayCamposAtualizar) != '-1'){
                    /* Tratamento para Aceita Faturamento Parcial */
                    var CampoSnFaturamentoParcial = $("#edtsn_faturamento_parcial");
                    if(CampoSnFaturamentoParcial.hasClass("venda_input_readonly") && sn_faturamento_parcial != ''){
                        CampoSnFaturamentoParcial.children().remove().end();
                        NewOption = $("<option value=\"" + sn_faturamento_parcial + "\">" + nome_sn_faturamento_parcial + "</option>");
                        CampoSnFaturamentoParcial.append(NewOption);
                        $("#edtsn_faturamento_parcial option:first").attr("selected","selected");
                    }
                    else{
                        CampoSnFaturamentoParcial.val(sn_faturamento_parcial);
                    }
                }

                if($.inArray('8', ArrayCamposAtualizar) != '-1'){
                    /* Tratamento para campo Representante principal */
                    var CampoIdRepresentantePrincipal = $("#edtid_representante_principal");
                    if(CampoIdRepresentantePrincipal.hasClass("venda_input_readonly") && id_representante_principal != ''){
                        CampoIdRepresentantePrincipal.children().remove().end();
                        NewOption = $("<option value=\"" + id_representante_principal + "\">" + nome_representante_principal + "</option>");
                        CampoIdRepresentantePrincipal.append(NewOption);
                        $("#edtid_representante_principal option:first").attr("selected","selected");
                    }
                    else{
                        CampoIdRepresentantePrincipal.val(id_representante_principal);
                    }
                }
                return true;
            }
            else if(Status == "2"){
                var Mensagem = $(xml).find("mensagem").text();
                alert(Mensagem);
                return false;
            }
            else if(Status == "3"){
                // Não faz nada
            }
        }
    });
    if(typeof p1_preenche_dados_conta_custom == 'function') {
        p1_preenche_dados_conta_custom();
    }
    return;
}
function exibe_tabela_item(){
    $.ajax({
        url: "p2_venda_tabela_itens.php",
        global: false,
        type: "POST",
        data: ({
            pnumreg: $("#pnumreg").val(),
            ptp_venda: $("#ptp_venda").val(),
            pvisualizar_revisao: $("#pvisualizar_revisao").val()
        }),
        dataType: "html",
        async: true,
        beforeSend: function(){
            $("#div_tabela_item").html(HTMLLoading);
        },
        error: function(){
            alert("Erro com a requisição");
        },
        success: function(responseText){
            $("#div_tabela_item").html(responseText);
        }
    });
}
function ReexibePreco(){
    $.ajax({
        url: "p2_vl_unitario.php",
        global: false,
        type: "POST",
        data: ({
            pnumreg: $("#pnumreg").val(),
            ptp_venda: $("#ptp_venda").val(),
            id_produto: $("#Adicionar_id_produto").val(),
            id_tab_preco: $("#Adicionar_id_tab_preco").val(),
            id_unid_medida: $("#Adicionar_id_unid_medida").val(),
            id_produto_embalagem: $("#Adicionar_id_produto_embalagem").val()
        }),
        dataType: "xml",
        async: true,
        beforeSend: function(){
            $("#preco").html(IMGLoading);
        },
        error: function(){
            alert("Erro com a requisição");
        },
        success: function(xml){
            $("#preco").html($(xml).find("preco").text());
        }
    });
}

/**
 * Aplica mascara de valor decimal em todos os campos com a classe .monetario
 * Depende do Script MeioMask/Jquery
 */
function VendaAplicaMascaraDecimal(){
    $(".monetario").each(function(){
        var CasasDecimais = parseInt($(this).attr("CasasDecimais"));
        var Mascara = "";
        for(i=0;i<CasasDecimais;i++){
            Mascara = Mascara + "9";
        }
        Mascara = Mascara + ",999.999.999.999";
        $(this).setMask({mask:Mascara,type:"reverse",defaultValue:"000"});
    });
}

function VendaExibeTabelaFaixaPrecoComissao(IdTabPreco,IdProduto){
    $.ajax({
        url: "p2_venda_tabela_faixa_preco_comissao.php",
        global: false,
        type: "POST",
        data: ({
            id_tab_preco: IdTabPreco,
            id_produto: IdProduto
        }),
        dataType: "xml",
        async: true,
        beforeSend: function(){
            $("#div_venda_tabela_faixa_preco_comissao").html(IMGLoading);
        },
        error: function(){
            alert("Erro com a requisição");
        },
        success: function(xml){
            var Html,VlUnitInicial,VlUnitFinal,PctComissao,SnPrecoDefault,LinhaPreco,Style;
            Html = '<h3>Faixas de Pre&ccedil;o x Comiss&atilde;o</h3>';
            $(xml).find("registros").find("registro").each(function(){
                VlUnitInicial = $(this).find("vl_unit_inicial").text();
                VlUnitFinal = $(this).find("vl_unit_final").text();
                PctComissao = $(this).find("pct_comissao").text();
                SnPrecoDefault = $(this).find("sn_preco_default").text();
                LinhaPreco = VlUnitInicial + ' at&eacute; ' + VlUnitFinal + ' = ' + PctComissao + '% de comiss&atilde;o.<br/>';
                Style = '';
                if(SnPrecoDefault == '1'){
                    Style = ' style="font-weight:bold;color: #003366;"';
                    $("#Adicionar_vl_unitario").val(VlUnitFinal);
                }
                Html += '<span' + Style + '>' + LinhaPreco + '</span>';
            });
            $("#div_venda_tabela_faixa_preco_comissao").html(Html);
        }
    });
}
