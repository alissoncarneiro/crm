<?php
/*
 * oportunidade_seleciona_kit.php
 * Autor: Alex
 * 18/12/2012 10:53:34
 */

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

session_start();

require('../../conecta.php');
require('../../functions.php');
?>
<fieldset>
    <legend>Selecione o KIT</legend>
    <select id="select_opor_itens_itens_kit">
        <?php
        $SqlProdutosKIT = "SELECT numreg,nome_kit FROM is_kit WHERE sn_ativo = 1 ORDER BY nome_kit";
        $QryProdutosKIT = query($SqlProdutosKIT);
        while($ArProdutosKIT = farray($QryProdutosKIT)){
            echo "<option value=".$ArProdutosKIT['numreg'].">".$ArProdutosKIT['nome_kit']."</option>";
        }
        ?>        
    </select>&nbsp;<input type="button" id="btn_opor_itens_itens_kit_selecionar_kit" value="Selecionar" class="botao_jquery" />
</fieldset>
<form name="form_oik" id="form_oik" method="post" action="modulos/oportunidades/oportunidade_grava_itens_kit.php">
    <input type="hidden" name="oik_id_oportunidade" id="oik_id_oportunidade" value="<?php echo $_POST['edtid_oportunidade'];?>" />
    <div id="div_opor_itens_itens_kit"></div>
</form>
<script>
    $(document).ready(function(){
        $(".botao_jquery").button();

        $("#btn_opor_itens_itens_kit_selecionar_kit").click(function(){
            var IdKIT = $("#select_opor_itens_itens_kit").val();
            $.ajax({
                url: "modulos/oportunidades/oportunidade_exibe_kit.php",
                global: false,
                type: "POST",
                data: ({
                    id_kit:IdKIT,
                    id_atividade:'<?php echo $_POST['edtid_atividade'];?>'
                }),
                dataType: "html",
                async: true,
                beforeSend: function(){
                    $("#div_opor_itens_itens_kit").html(HTMLLoadingGeral);
                },
                error: function(){
                    alert('Erro com a requisição');
                },
                success: function(responseText){
                    $("#div_opor_itens_itens_kit").html(responseText);
                    monetario_mascara();
                }
            });
        });
    });
</script>