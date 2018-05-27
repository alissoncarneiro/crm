<?php
/*
 * detalhe_end_opor_itens.php
 * Autor: Alex
 * 18/12/2012 10:49:45
 */
if($id_funcao == 'opor_itens'){ ?>
    <div id="jquery-dialog-opor-itens-kit" style="display: none;"></div>
    <style type="text/css">
        #jquery-dialog-opor-itens-kit legend{
            font-size: 16px;
            font-weight: bold;
        }
        #jquery-dialog-opor-itens-kit .campo_qtde{
            width: 40px;
            text-align: right;
        }
        #jquery-dialog-opor-itens-kit .date{
            width:65px;
            text-align: center;
        }
        #jquery-dialog-opor-itens-kit .campo_vl_unitario{
            width: 50px;
            text-align: right;
        }
    </style>
    <script language="javascript" src="js/jquery.meio.mask.min.js"></script>
    <script>
        function monetario_mascara(){
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
            
        $(document).ready(function(){
            $("#cad input[name=btn_adicionar_kit]").removeAttr("onclick");
            $("#cad input[name=btn_adicionar_kit]").click(function(){
                var Dialog = $("#jquery-dialog-opor-itens-kit");
                Dialog.attr("title",'Incluir KIT de Produtos');
                Dialog.html(HTMLLoadingGeral);
                Dialog.dialog({
                    width: 800,
                    height: "auto",
                    buttons:{
                        'Adicionar Itens':function(){
                            $.ajax({
                                url: "modulos/oportunidades/oportunidade_grava_itens_kit.php",
                                global: false,
                                type: "POST",
                                data: $("#form_oik").serialize(),
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
                            url: "modulos/oportunidades/oportunidade_seleciona_kit.php",
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
                                monetario_mascara();
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
<?php
}