<?php
/*
 * gera_end_pessoa.php
 * Autor: Alex
 * 21/10/2010 16:40
 * - Arquivo respons�vel para tratar o cadastro de contatos
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
if($id_funcao == 'pessoa'){?>
    <div id="jquery-dialog-pesq-cep" style="display: none;"></div>
    <script language="javascript" src="js/jquery.meio.mask.min.js"></script>
    <script language="javascript">
        $(document).ready(function(){
            troca_tp_pessoa('edtid_tp_pessoa');

            $("#btn_pesq_cep,#btn_pesq_cep_cob").css("cursor","pointer").
            click(function(event){
                var TP = ($(this).attr("TP"))?$(this).attr("TP"):'';

                if($("#edtcep"+TP).val() != ''){
                    var cep = $("#edtcep"+TP).val();
                    $.getJSON("//viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                        if (!("erro" in dados)) {
                            console.log(dados);
                            //Atualiza os campos com os valores da consulta.
                            $("#edtendereco").val(dados.logradouro);
                            $("#edtbairro").val(dados.bairro);
                            $("#edtcidade").val(dados.localidade);
                            $('#edtuf option[value='+dados.uf+']').attr('selected','selected');
                        }
                        else {
                            alert("CEP não encontrado.");
                        }
                    });


                    return true;
                }                
                var Dialog = $("#jquery-dialog-pesq-cep");
                Dialog.attr("title",'Pesquisa de CEP');
                Dialog.html(HTMLLoadingGeral);
                Dialog.dialog({
                    width: 550,
                    height: "auto",
                    buttons:{
                        'Pesquisar':function(){
                            $.ajax({
                                url: "modulos/customizacoes/pesquisa_lista_cep.php",
                                global: false,
                                type: "POST",
                                data: $("#form_pesq_cep").serialize(),
                                dataType: "html",
                                async: true,
                                beforeSend: function(){
                                    $("#div_pesq_cep_resultado").html(HTMLLoadingGeral); 
                                },
                                error: function(){
                                    alert('Erro com a requisição');
                                },
                                success: function(responseText){
                                    $("#div_pesq_cep_resultado").html(responseText);                                    
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
                            url: "modulos/customizacoes/pesquisa_lista_cep.php",
                            global: false,
                            type: "POST",
                            dataType: "html",
                            data:{TP:TP},
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
    <?php if(GetParam('CLI_SN_USA_MASCARA_TEL_FAX_CEP') == '1'){ ?>
    <script language="javascript">
        $(document).ready(function(){
            troca_tp_pessoa('edtid_tp_pessoa');
            $('#edttel1').setMask({mask:'(99)99999-9999',defaultValue:'',
                onInvalid:function(c,nKey){
                    alert('Permitido apenas números!');
                    return false;
                }
            });
            $('#edttel2').setMask({mask:'(99)99999-9999',defaultValue:'',
                onInvalid:function(c,nKey){
                    alert('Permitido apenas números!');
                    return false;
                }
            });

            $('edtwcp_tel3').setMask({mask:'(99)99999-9999',defaultValue:'',
                onInvalid:function(c,nKey){
                    alert('Permitido apenas números!');
                    return false;
                }
            });
            $('#edtfax').setMask({mask:'(99)99999-9999',defaultValue:'',
                onInvalid:function(c,nKey){
                    alert('Permitido apenas números!');
                    return false;
                }
            });
            $('#edtcep').setMask({mask:'99999999',defaultValue:'',autoTab: false,
                onInvalid:function(c,nKey){
                    alert('Permitido apenas números!');
                    return false;
                },
                onValid: function(c,nKey){},
                onOverflow: function(c,nKey){}
            });
            if($('#edtid_cep').val() != ''){
                cep_trava_campos('',true,true);
            }
        });
    </script>
    <?php } ?>
<?php
}
?>