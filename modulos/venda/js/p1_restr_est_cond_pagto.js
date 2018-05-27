$(document).ready(function(){
    $('option', $("#edtid_forma_pagto")).each(function() {
        if(!$(this).attr("selected")){
            $(this).remove();
        }
    });

    $('option', $("#edtid_cond_pagto")).each(function() {
        if(!$(this).attr("selected")){
            $(this).remove();
        }
    });

    $("#edtid_estabelecimento").change(function(){
        $.ajax({
            url: "p1_estab_x_forma_pagto_x_cond_pagto.php",
            global: "false",
            type: "POST",
            data: {
                id_estabelecimento: $("#edtid_estabelecimento").val(),
                tipo_consulta: 1
            },
            dataType: "xml",
            async: true,
            beforeSend: function(){
                MaskLoading("Mostrar");
            },
            error: function(){
                alert('Erro com a requisição');
                MaskLoading('Ocultar');
            },
            success: function(xml){
                var Value,Descricao,NewOption;
                var select_forma_pagto = $("#edtid_forma_pagto");
                var select_cond_pagto = $("#edtid_cond_pagto");
                select_forma_pagto.children().remove().end();//Remove todas as opcoes
                select_cond_pagto.children().remove().end();//Remove todas as opcoes
                var Options = $(xml).find("options");
                Options.find("option").each(function(){
                    Value = $(this).attr("value");
                    Descricao = $(this).text();
                    NewOption = $("<option value=\"" + Value + "\">" + Descricao + "</option>");
                    select_forma_pagto.append(NewOption);
                    MaskLoading('Ocultar');
                });
            }
        });
    });

    $("#edtid_forma_pagto").change(function(){
        $.ajax({
            url: "p1_estab_x_forma_pagto_x_cond_pagto.php",
            global: "false",
            type: "POST",
            data: {
                id_forma_pagamento: $("#edtid_forma_pagto").val(),
                id_estabelecimento: $("#edtid_estabelecimento").val(),
                tipo_consulta: 2
            },
            dataType: "xml",
            async: true,
            beforeSend: function(){
                MaskLoading("Mostrar");
            },
            error: function(){
                alert('Erro com a requisição');
                MaskLoading('Ocultar');
            },
            success: function(xml){
                var Value,Descricao,NewOption;
                var select_cond_pagto = $("#edtid_cond_pagto");
                select_cond_pagto.children().remove().end();//Remove todas as opcoes
                var Options = $(xml).find("options");
                Options.find("option").each(function(){
                    Value = $(this).attr("value");
                    Descricao = $(this).text();
                    NewOption = $("<option value=\"" + Value + "\">" + Descricao + "</option>");
                    select_cond_pagto.append(NewOption);
                    MaskLoading('Ocultar');
                });
            }
        });
    });
});