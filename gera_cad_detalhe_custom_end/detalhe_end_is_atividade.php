<?php


if($id_funcao == 'atividades_cad_lista'){?>
<div id="jquery-dialog-replicar" style="display: none;">
    <input type="text" class="campo_qtde numeric" id="cp_replicar_atividade_qtde" value=""/> atividade(s)  a cada <input type="text" id="cp_replicar_atividade_periodicidade" class="campo_qtde numeric" value=""/> dia(s)
    <hr/>
    <strong>Nota: </strong>Somente será gerada atividade em dias livres e &uacute;teis. Caso a data de in&iacute;cio seja menor que a data atual, ser&aacute; considerada a data atual.
</div>
<style type="text/css">
    #jquery-dialog-replicar .campo_qtde{
        width: 25px;
        text-align: right;
    }
</style>
<script language="javascript" src="js/jquery.meio.mask.min.js"></script>
<script language="javascript">
    $(document).ready(function(){
        $('#edthr_inicio,#edthr_prev_fim').setMask({mask:'99:99',defaultValue:'',
            onInvalid:function(c,nKey){
                alert('Permitido apenas números!');
                return false;
            }
        });
        $('#edtdt_inicio,#edtdt_prev_fim').setMask({mask:'99/99/9999',defaultValue:'',
            onInvalid:function(c,nKey){
                alert('Permitido apenas números!');
                return false;
            }
        });
        $(".numeric").keyup(function () {
            this.value = this.value.replace(/[^0-9\.]/g,'');
        });

        $('#edtdt_inicio').change(function() {
            var val = $(this).val();
              $('#edtdt_prev_fim').val(val);
        });

        /* Função de Replica de Atividade */
        $("#cad input[name=btn_replicar_atividade]").removeAttr("onclick");
        $("#cad input[name=btn_replicar_atividade]").click(function(){
            var Dialog = $("#jquery-dialog-replicar");
            Dialog.attr("title",'Replicar Atividade');
            Dialog.dialog({
                width: 300,
                buttons:{
                    'Confirmar':function(){
                        var QtdeAtividades = $("#cp_replicar_atividade_qtde").val();
                        var Periodicidade = $("#cp_replicar_atividade_periodicidade").val();
                        var IdAtividade = $("#pnumreg").val();
                        if(QtdeAtividades <= 0){
                            alert('Quantidade de atividade(s) deve ser maior que 0!');
                            return false;
                        }
                        if(Periodicidade <= 0){
                            alert('Periodicidade deve ser maior que 0!');
                            return false;
                        }
                        if(confirm("Confirma replicar " + QtdeAtividades + " atividade(s) com periodicidade de " + Periodicidade + " dia(s) ?")){
                            $.ajax({
                                url: "modulos/atividades/replicar_atividade.php",
                                global: false,
                                type: "POST",
                                data: ({
                                   id_atividade: IdAtividade,
                                    qtde_atividades: QtdeAtividades,
                                    periodicidade: Periodicidade
                                }),
                                dataType: "html",
                                async: true,
                                beforeSend: function(){
                                    MaskLoading('Mostrar');
                                },
                                error: function(){
                                    MaskLoading('Ocultar');
                                    alert('Erro com a requisição');
                                },
                                success: function(responseText){
                                    MaskLoading('Ocultar');
                                    alert(responseText);
                                }
                            });
                        }
                    },
                    Fechar: function(){
                        $(this).dialog("close");
                    }
                },
                close: function(){
                    $(this).dialog("destroy");
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