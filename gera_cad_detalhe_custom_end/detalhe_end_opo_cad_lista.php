<?php
/*
 * detalhe_end_opo_cad_lista.php
 * Autor: Alex
 * 20/04/2011 14:39:37
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
if($id_funcao == 'opo_cad_lista'){?>
<script type="text/javascript">
    $(document).ready(function(){
        $("[name=btnorc]").click(function(){
            if($("#pnumreg").val() == '-1'){
                return false;
            }
            if(!confirm('Deseja gerar um orçamento ?')){
                return false;
            }
            $.ajax({
                url:'modulos/oportunidades/oport_gerar_orcamento.php',
                global: true,
                type: "POST",
                dataType: "html",
                async: false,
                data: ({
                    id_oportunidade:$("#pnumreg").val()
                }),
                beforeSend:function(){
                    MaskLoading('Mostrar');
                },
                error: function(){
                    alert('Erro com a requisição');
                    MaskLoading('Ocultar');
                },
                success: function(responseText){
                    MaskLoading('Ocultar');
                    alert(responseText);
                    window.location.href = window.location.href;
                }
            });
        });
    });
</script>
<?php } ?>