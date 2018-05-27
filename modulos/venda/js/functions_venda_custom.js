$(document).ready(function(){
    $("#btn_c_coaching_id_ect_etc").click(function(){
        $('<div>', {
            id: 'grade-pagto',
        }).appendTo("body");

        $.ajax({
            url: "c_coaching_grade_pagto_custom.php",
            global: false,
            type: "POST",
            data: ({
                numreg: $("#edtnumreg").val(),
            }),
            dataType: "html",
            async: false,
            beforeSend: function(){
                MaskLoading("Mostrar");
            },
            error: function(){
                alert('Erro com a requisição');
                MaskLoading("Ocultar");
            },
            success: function(data){
                $("#grade-pagto").html(data);
                MaskLoading("Ocultar");
            }
        });
        var Dialog = $("#grade-pagto");
        Dialog.dialog({
            resizable: true,
            draggable: true,
            stack: false,
            modal: true,
            width: 644,
            height: 400,
            show: "fade",
            hide: "fade",
            title: 'Pagamento especial',
            buttons:{
                "Fechar": function(){
                    $('#grade-pagto').dialog('close');
                },
            },
            close: function(event, ui) {
                $("#grade-pagto").remove();
            }
        });
    });
});