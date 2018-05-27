$(document).ready(function(){
    $("#btn_submit_p1").click(function(){
        $("#form_p1").submit();
        return false;
    });
    
    if($("#edtid_ciclo")){
        if(!$("#edtid_ciclo").hasClass("venda_input_readonly")){
            $("#edtid_ciclo").change(function(){
                $.ajax({
                    url: "p1_fase.php",
                    global: "false",
                    type: "POST",
                    data: {
                        id_ciclo: $("#edtid_ciclo").val()                    
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
                        var Value,Descricao,NewOption,select_fase,Options;
                        select_fase = $("#edtid_fase");
                        select_fase.children().remove().end();//Remove todas as opcoes
                        Options = $(xml).find("options");
                        Options.find("option").each(function(){
                            Value = $(this).attr("value");
                            Descricao = $(this).text();
                            NewOption = $("<option value=\"" + Value + "\">" + Descricao + "</option>");
                            select_fase.append(NewOption);                        
                        });
                        MaskLoading('Ocultar');
                    }
                });
            });
        }
    }

    $("#btn_sugerir_cfop_cliente").click(function(){
        $.ajax({
            url: "p1_sugere_cfop_cliente.php",
            global: false,
            type: "POST",
            data: ({
                id_pessoa: $("#edtid_pessoa").val(),
                id_estabelecimento: $("#edtid_estabelecimento").val(),
                id_endereco_entrega: $("#edtid_endereco_entrega").val()
            }),
            dataType: "xml",
            async: true,
            beforeSend: function(){
                MaskLoading("Mostrar");
            },
            error: function(){
                alert('Erro com a requisição');
                MaskLoading("Ocultar");
            },
            success: function(xml){
                var Status      = $(xml).find('status').text();
                var Mensagem    = $(xml).find('mensagem').text();
                var IdCFOP      = $(xml).find('id_cfop').text();
                if(Status != 'true'){
                    alert(Mensagem);
                }
                else{
                    $("#edtid_cfop").val(IdCFOP);
                    alert('Nova CFOP: ' + $('#edtid_cfop').find('option').filter(':selected').text());
                }
                MaskLoading("Ocultar");
            }
        });
    }).qtip({
        content: $(this).attr("alt"),
        style: 'blue'
    });

    $(".btn_sugerir_desconto_fixo").click(function(){
        $.ajax({
            url: "calcula_desconto_venda_fixo.php",
            global: false,
            type: "POST",
            data: $("#form_p1").serialize()+"&id_campo_desconto="+$(this).attr("id_campo_desconto"),
            dataType: "xml",
            async: true,
            beforeSend: function(){
                MaskLoading("Mostrar");
            },
            error: function(){
                alert('Erro com a requisição');
                MaskLoading("Ocultar");
            },
            success: function(xml){
                var Campo       = $(xml).find('campo_desconto');
                $("#"+Campo.attr("id_campo")).val(Campo.attr("pct_desconto"));
                MaskLoading("Ocultar");
            }
        });
    }).qtip({
        content: $(this).attr("alt"),
        style: 'blue'
    });
});