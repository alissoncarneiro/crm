<?php
/*
 * seleciona_modelo_impressao.php
 * Autor: Monica
 * 27/11/2010 18:11:00
 *
 * Arquivo que exibe o saldo de estoque com detalhes no produto - Modelo da AlphaPrint
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

header("Content-Type: text/html; charset=ISO-8859-1");
include('../../conecta.php');
?>
<div align="center">
<select id="select_id_modelo" name="select_id_modelo" style="font-size: 14px; height: 20px">
    <option value="">Selecione um modelo</option>
<?php

$QryModelo = query("SELECT numreg,nome_modelo_orcamento FROM is_modelo_orcamento ORDER BY nome_modelo_orcamento ASC");
while ($ArModelo = farray($QryModelo)) {
        echo '<option value="' . $ArModelo['numreg'] . '">' . $ArModelo['nome_modelo_orcamento'] . '</option>';
}
?></select>
</div>
        
<!--<input type="button" value="Selecionar" id="btn_seleciona_modelo" />

<script>
    $("#btn_seleciona_modelo").click(function(){
        if($("#select_id_modelo").val() != ''){
            var width=700;
            var height=550;
            var left=100;
            var top=100;
            window.open('gera_modelo/exibe_modelo_impressao.php?pnumreg=<?php echo $_POST['pnumreg'];?>&ptp_venda=<?php echo $_POST['ptp_venda'];?>&id_modelo='+$("#select_id_modelo").val(),'modelo_impressao','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,width=' + width + ',height=' + height + ',left=' + left + ',top = ' + top).focus();
       } else {
           alert('modelo não escolhido, favor escolher um modelo para impressão.')
       }
    });

</script>-->