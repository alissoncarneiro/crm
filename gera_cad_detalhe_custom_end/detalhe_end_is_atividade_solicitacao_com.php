<?php
/*
 * detalhe_end_is_atividade_solicitacao_com.php
 * Autor: Alex
 * 15/03/2011 11:48
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
if($_GET['pfuncao'] == 'is_atividade_solicitacao_com'){ ?>
<div id="jquery-dialog-sol-com-kit" style="display: none;"></div>
<style type="text/css">
    #jquery-dialog-sol-com-kit legend{
        font-size: 16px;
        font-weight: bold;
    }
    #jquery-dialog-sol-com-kit .campo_qtde{
        width: 40px;
        text-align: right;
    }
    #jquery-dialog-sol-com-kit .date{
        width:65px;
        text-align: center;
    }
</style>
<script>
    $(document).ready(function(){
        $("#cad input[name=btn_adicionar_kit]").removeAttr("onclick");
        $("#cad input[name=btn_adicionar_kit]").click(function(){
            var Dialog = $("#jquery-dialog-sol-com-kit");
            Dialog.attr("title",'Incluir KIT de Produtos');
            Dialog.html(HTMLLoadingGeral);
            Dialog.dialog({
                width: 800,
                height: 500,
                buttons:{
                    'Adicionar Itens':function(){
                        $.ajax({
                            url: "modulos/atividades/solicitacao_comercial_grava_itens_kit.php",
                            global: false,
                            type: "POST",
                            data: $("#form_sck").serialize(),
                            dataType: "xml",
                            async: true,
                            beforeSend: function(){
                                MaskLoading('mostrar');
                            },
                            error: function(){
                                alert('Erro com a requisição');
                            },
                            success: function(xml){
                                var resposta    = $(xml).find('resposta');
                                var status      = resposta.find('status').text();
                                var Acao        = resposta.find('acao').text();
                                var Url         = resposta.find('url').text();
                                
                                if(status == '1'){
                                    alert(resposta.find('mensagem').text());
                                }
                                else{
                                    alert(resposta.find('mensagem').text());
                                }

                                if(Acao == '1'){
                                    if(window.opener.document.getElementById('btnfiltrar')){
                                        window.opener.document.getElementById('btnfiltrar').click();
                                    }
                                    window.opener.focus();
                                    window.close();
                                }
                                MaskLoading('ocultar');
                            }
                        });
                        
                    },
                    Fechar: function(){
                        $(this).dialog("close");
                        $(this).dialog("destroy");
                    }
                },
                open: function(){
                    $.ajax({
                        url: "modulos/atividades/solicitacao_comercial_seleciona_kit.php",
                        global: false,
                        type: "POST",
                        data: $("#cad").serialize(),
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
                hide: "fade"
            });
        });
    });
</script>
<?php } ?>