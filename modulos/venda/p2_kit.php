<?php
/*
 * p2_kit.php
 * Autor: Alex
 * 20/06/2011 13:00:49
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('includes.php');

$Usuario = new Usuario($_SESSION['id_usuario']);
$VendaParametro = new VendaParametro();
?>
<script language="javascript">
    $(document).ready(function(){
        $(".botao_jquery").button();
        $("#p2_adiciona_kit_btn_exibe_kit").click(function(){
            $.ajax({
                url: "p2_produto_kit.php",
                global: false,
                type: "POST",
                data: ({
                    id_kit: $("#p2_adiciona_kit_id_kit").val(),
                    pnumreg: '<?php echo $_POST['pnumreg'];?>',
                    ptp_venda: '<?php echo $_POST['ptp_venda'];?>'
                }),
                dataType: "html",
                async: true,
                beforeSend: function(){
                    $("#p2_adiciona_kit_div_produtos").html(HTMLLoading);
                },
                error: function(){
                    alert('Erro com a requisi��o');
                },
                success: function(responseText){
                    $("#p2_adiciona_kit_div_produtos").html(responseText);
                }
            });
        });
    });
</script>
<fieldset><legend>Selecione o KIT</legend>
    KIT: <select name="p2_adiciona_kit_id_kit" id="p2_adiciona_kit_id_kit">
        <?php 
        $QryKIT = query("SELECT * FROM is_kit WHERE sn_ativo = 1 ORDER BY nome_kit ASC");
        while($ArKIT = farray($QryKIT)){
        echo '<option value="'.$ArKIT['numreg'].'">'.$ArKIT['nome_kit'].'</option>';
        } ?>
    </select>
    <input type="button" value="Exibir" id="p2_adiciona_kit_btn_exibe_kit" class="botao_jquery"/>
</fieldset>
<fieldset><legend>Produtos do KIT</legend>
    <div id="p2_adiciona_kit_div_produtos">Selecione um KIT.</div>
</fieldset>