$(document).ready(function(){
    /*
     * Adicionando ações no campo de pesquisa
     */
    $("#span_selec_it_pesq").click(function(){
        pesquisa_selec_it_pesq();
    });
    $("#edttexto_filtro_1").keypress(function(event){
        if(event.keyCode == '13'){
            pesquisa_selec_it_pesq();
        }
    });
    $("#edttexto_filtro_2").keypress(function(event){
        if(event.keyCode == '13'){
            pesquisa_selec_it_pesq();
        }
    });
    $("#edttexto_filtro_3").keypress(function(event){
        if(event.keyCode == '13'){
            pesquisa_selec_it_pesq();
        }
    });
    $("#edttexto_filtro_4").keypress(function(event){
        if(event.keyCode == '13'){
            pesquisa_selec_it_pesq();
        }
    });

    /*
     *Auto Complete
     */
    //pesquisa_produto_autocomplete.php
    $("#edttexto_filtro_1").autocomplete({
        serviceUrl:'pesquisa_produto/pesquisa_produto_autocomplete.php',
        minChars:2,
        maxHeight:400,
        width:300,
        zIndex: 9999,
        deferRequestBy: 500,
        params: {
            campo:'edttexto_filtro_1',
            ptp_venda: $("#ptp_venda").val(),
            pnumreg: $("#pnumreg").val()
        },
        noCache: true,
        onSelect: function(value, data){ exibe_detalhe_produto(data); }
    });

    $("#edttexto_filtro_2").autocomplete({
        serviceUrl:'pesquisa_produto/pesquisa_produto_autocomplete.php',
        minChars:2,
        maxHeight:400,
        width:300,
        zIndex: 9999,
        deferRequestBy: 500,
        params: {
            campo:'edttexto_filtro_2',
            ptp_venda: $("#ptp_venda").val(),
            pnumreg: $("#pnumreg").val()
        },
        noCache: true,
        onSelect: function(value, data){ exibe_detalhe_produto(data); }
    });

    $("#edttexto_filtro_3").autocomplete({
        serviceUrl:'pesquisa_produto/pesquisa_produto_autocomplete.php',
        minChars:2,
        maxHeight:400,
        width:300,
        zIndex: 9999,
        deferRequestBy: 500,
        params: {
            campo:'edttexto_filtro_3',
            ptp_venda: $("#ptp_venda").val(),
            pnumreg: $("#pnumreg").val()
        },
        noCache: true,
        onSelect: function(value, data){ exibe_detalhe_produto(data); }
    });

    $("#edttexto_filtro_4").autocomplete({
        serviceUrl:'pesquisa_produto/pesquisa_produto_autocomplete.php',
        minChars:2,
        maxHeight:400,
        width:300,
        zIndex: 9999,
        deferRequestBy: 0,
        params: {
            campo:'edttexto_filtro_4',
            ptp_venda: $("#ptp_venda").val(),
            pnumreg: $("#pnumreg").val(),
            id_pessoa:$("#edttexto_filtro_4").attr("id_pessoa")
        },
        noCache: true,
        onSelect: function(value, data){ exibe_detalhe_produto(data); }
    });

    /*
     * Tratamento para os campo combobox da pesquisa por família
     */
    $("#edtselec_it_linha_familia_linha").change(function(){
        if($(this).val() != ''){
            var HTML = $("#edtselec_it_linha_familia_familia").html();
            $.ajax({
                url: "pesquisa_produto/pesquisa_produto_linha_familia.php",
                global: false,
                type: "POST",
                data: ({
                    ptp_venda: $("#ptp_venda").val(),
                    pnumreg: $("#pnumreg").val(),
                    prequisicao: 'familia',
                    ptp_venda: $("#ptp_venda").val(),
                    pnumreg: $("#pnumreg").val(),
                    pid_linha: $(this).val()
                }),
                dataType: "xml",
                async: true,
                beforeSend: function(){
                    $("#edtselec_it_linha_familia_familia").html(HTMLLoading);
                    $("#edtselec_it_linha_familia_familia").empty();
                    $("#edtselec_it_linha_familia_produto").empty();
                },
                error: function(){
                    alert('Erro com a requisição');
                },
                success: function(xml){
                    $("#edtselec_it_linha_familia_familia").html(HTML);
                    var options = '';
                    $(xml).find('option').each(function(){
                        options += '<option value="'+$(this).attr("value")+'">'+$(this).text()+'</option>';
                    });
                    $("#edtselec_it_linha_familia_familia").html(options);
                    $("#edtselec_it_linha_familia_produto").empty();
                    $("#edtselec_it_linha_familia_produto").html('<option value="">--Selecione uma família--</option>');
                }
            });
        }
        else{
            $("#edtselec_it_linha_familia_familia").empty();
            $("#edtselec_it_linha_familia_familia").html('<option value="">--Selecione uma linha--</option>');
            $("#edtselec_it_linha_familia_produto").empty();
            $("#edtselec_it_linha_familia_produto").html('<option value="">--Selecione uma família--</option>');
        }
        return;
    });

    $("#edtselec_it_linha_familia_familia").change(function(){
        if($(this).val() != ''){
            $.ajax({
                url: "pesquisa_produto/pesquisa_produto_linha_familia.php",
                global: false,
                type: "POST",
                data: ({
                    ptp_venda: $("#ptp_venda").val(),
                    pnumreg: $("#pnumreg").val(),
                    prequisicao: 'produto',
                    ptp_venda: $("#ptp_venda").val(),
                    pnumreg: $("#pnumreg").val(),
                    pid_familia_comercial: $(this).val()
                }),
                dataType: "xml",
                async: true,
                beforeSend: function(){
                    $("#edtselec_it_linha_familia_produto").empty();
                },
                error: function(){
                    alert('Erro com a requisição');
                },
                success: function(xml){
                    var options = '';
                    $(xml).find('option').each(function(){
                        options += '<option value="'+$(this).attr("value")+'">'+$(this).text()+'</option>';
                    });
                    $("#edtselec_it_linha_familia_produto").html(options);
                }
            });
        }
        else{
            $("#edtselec_it_linha_familia_produto").empty();
            $("#edtselec_it_linha_familia_produto").html('<option value="">--Selecione uma família--</option>');
        }
        return;
    });
});
/*
 * Função que executa a pesquisa
 */

function pesquisa_selec_it_pesq(){
    $.ajax({
        url: "pesquisa_produto/pesquisa_produto.php",
        global: false,
        type: "POST",
        data: ({
            ptp_venda: $("#ptp_venda").val(),
            pnumreg: $("#pnumreg").val(),
            edttp_filtro: $("#edttp_filtro").val(),
            edttexto_filtro_1: $("#edttexto_filtro_1").val(),
            edttexto_filtro_2: $("#edttexto_filtro_2").val(),
            edttexto_filtro_3: $("#edttexto_filtro_3").val(),
            edttexto_filtro_4: $("#edttexto_filtro_4").val(),
            edtid_pessoa: $("#edttexto_filtro_4").attr("id_pessoa")
        }),
        dataType: "html",
        async: true,
        success: function(responseText){
            $("#div_resut_selec_it_pesq").html(responseText);
        }
    });
    return;
}

function exibe_detalhe_produto(pid_produto,pid_produto_pai,pRecarregarPagina,pQtde,pVlUnitario,pIdItemPre){
    $.ajax({
        url: "det_produto.php",
        global: false,
        type: "POST",
        data: ({
            ptp_venda: $("#ptp_venda").val(),
            pnumreg: $("#pnumreg").val(),
            pid_produto: pid_produto,
            pid_produto_pai: pid_produto_pai,
            pRecarregarPagina:pRecarregarPagina,
            pQtde:pQtde,
            pVlUnitario:pVlUnitario,
            pIdItemPre:pIdItemPre
        }),
        dataType: "html",
        async: true,
        beforeSend: function(){
            $("#div_det_produto").html(HTMLLoading);
        },
        error: function(){
            alert('Erro com a requisição');
            $("#div_det_produto").html('');
        },
        success: function(responseText){
            $("#div_det_produto").html(responseText);
            if($("#edtqtde_det_produto").length){
                $("#edtqtde_det_produto").focus();
            }
        }
    });
    return;
}