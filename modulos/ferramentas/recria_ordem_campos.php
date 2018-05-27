<?php
/*
 * recria_ordem_campos_post.php
 * Autor: Anderson JS
 * 17/12/2010 08:32
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html;  charset=ISO-8859-1");
include('../../conecta.php');

 $sql_qry = query("SELECT numreg, id_cad, titulo FROM is_gera_cad ORDER BY titulo");

?>
<fieldset>
    <legend style="font-size: 16px; font-weight:  bold;">Recria ordem dos campos</legend>
        <ul style="list-style: none;">
            <li>Selecionar cadastro:</li>
            <li>
                <select id="edtid_cad" name="edtid_cad" style=" border: 1px solid #009; margin: 3px 0; padding: 1px;">
                    <?php
                    while($ar_qry = farray($sql_qry)){
                    ?>
                    <option value="<?php echo $ar_qry['id_cad'];?>"><?php echo $ar_qry['titulo'].' - '.$ar_qry['id_cad']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </li>
            <li><input type="button" name="recria_ordem_submit" id="recria_ordem_submit" value="Ordenar" class="botao_form" /></li>
        </ul>
</fieldset>
<script>
$(document).ready(function(){
    $("#recria_ordem_submit").click(function(){
        var Mask = $("<div id=\"modal_mask_loading\"></div>");
        $("body").append(Mask);
        Mask.css("opacity", 0.5);
        Mask.fadeIn();
        $.ajax({
            url: "modulos/ferramentas/recria_ordem_campos_post.php",
            global: false,
            type: "POST",
            data: ({
                edtid_cad: $("#edtid_cad").val()
            }),
            dataType: "html",
            async: true,
            beforeSend: function(){

            },
            error: function(){
                alert('Erro com a requisição');
                Mask.fadeOut(100);
            },
            success: function(responseText){
                alert(responseText);
                Mask.fadeOut(100);
            }
        });
    });
});
</script>