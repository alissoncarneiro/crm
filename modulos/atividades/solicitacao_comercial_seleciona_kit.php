<?php
/*
 * solicitacao_comercial_seleciona_kit.php
 * Autor: Alex
 * 15/03/2011 12:03
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
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
    <select id="select_sol_com_itens_kit">
        <?php
        $SqlProdutosKIT = "SELECT numreg,nome_kit FROM is_kit WHERE sn_ativo = 1 ORDER BY nome_kit";
        $QryProdutosKIT = query($SqlProdutosKIT);
        while($ArProdutosKIT = farray($QryProdutosKIT)){
            echo "<option value=".$ArProdutosKIT['numreg'].">".$ArProdutosKIT['nome_kit']."</option>";
        }
        ?>        
    </select>&nbsp;<input type="button" id="btn_sol_com_itens_kit_selecionar_kit" value="Selecionar" class="botao_jquery" />
</fieldset>
<form name="form_sck" id="form_sck" method="post" action="modulos/atividades/solicitacao_comercial_grava_itens_kit.php">
    <input type="hidden" name="sck_id_atividade" id="sck_id_atividade" value="<?php echo $_POST['edtid_atividade'];?>" />
    <input type="hidden" name="sck_id_tp_grupo_motivo_atend" id="sck_id_tp_grupo_motivo_atend" value="<?php echo $_POST['edtid_tp_grupo_motivo_atend'];?>" />
    <input type="hidden" name="sck_id_pessoa" id="sck_id_pessoa" value="<?php echo $_POST['edtid_pessoa'];?>" />
    <input type="hidden" name="sck_atend_id_forma_contato" id="sck_atend_id_forma_contato" value="<?php echo $_POST['edtatend_id_forma_contato'];?>" />
    <input type="hidden" name="sck_atend_id_origem" id="sck_atend_id_origem" value="<?php echo $_POST['edtatend_id_origem'];?>" />
    <input type="hidden" name="sck_dt_inicio" id="sck_dt_inicio" value="<?php echo $_POST['edtdt_inicio'];?>" />
    <input type="hidden" name="sck_id_tp_atividade" id="sck_id_tp_atividade" value="<?php echo $_POST['edtid_tp_atividade'];?>" />
    <div id="div_sol_com_itens_kit"></div>
</form>
<script>
    $(document).ready(function(){
        $(".botao_jquery").button();

        $("#btn_sol_com_itens_kit_selecionar_kit").click(function(){
            var IdKIT = $("#select_sol_com_itens_kit").val();
            $.ajax({
                url: "modulos/atividades/solicitacao_comercial_exibe_kit.php",
                global: false,
                type: "POST",
                data: ({
                    id_kit:IdKIT,
                    id_atividade:'<?php echo $_POST['edtid_atividade'];?>'
                }),
                dataType: "html",
                async: true,
                beforeSend: function(){
                    $("#div_sol_com_itens_kit").html(HTMLLoadingGeral);
                },
                error: function(){
                    alert('Erro com a requisição');
                },
                success: function(responseText){
                    $("#div_sol_com_itens_kit").html(responseText);
                }
            });
        });
    });
</script>