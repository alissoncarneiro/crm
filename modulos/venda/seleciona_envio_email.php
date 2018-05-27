<?php
header("Content-Type: text/html; charset=ISO-8859-1");
require('includes.php');
/* Verifica se a váriável de tipo da venda foi preenchida */
if($_POST['ptp_venda'] != 1 && $_POST['ptp_venda'] != 2){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
elseif(empty($_POST['pnumreg'])){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
else{
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
    }
    else{
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
    }
}

$sql_modelo = "select * from is_modelo_orcamento where sn_envia_email=1";
$qry_modelo = query($sql_modelo);
?>
<style type="text/css">
#div_arquivos_anexos span{
    display: inline-block;
    margin: 1px;
    padding: 2px;
    border:1px solid #eee;
    text-align:center;
    cursor: pointer;
    height: 20px;
}
#div_arquivos_anexos span:hover{
    border:1px solid #999;
    background:#eee;
}
</style>
<form action="envio_email/executa_envio_email.php" id="form_envio_email" method="post" enctype="multipart/form-data" onsubmit="return AIM.submit(this, {'onStart' : UploadstartCallback, 'onComplete' : UploadcompleteCallback});">
<input type="hidden" name="pnumreg" id="pnumreg" value="<?php echo $_POST['pnumreg'];?>" />
<input type="hidden" name="ptp_venda" id="ptp_venda" value="<?php echo $_POST['ptp_venda'];?>" />
<table border="0" cellpadding="5" cellspacing="0">
    <tr>
        <td>Modelo do envio</td>
        <td>
            <select id="select_id_modelo" name="select_id_modelo">
                <option value="">Selecione um modelo</option>
                <?php
                while($ar_modelo = farray($qry_modelo)){
                    echo '<option value="'.$ar_modelo['numreg'].'">'.$ar_modelo['nome_modelo_orcamento'].'</option>';
                }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>Anexar Arquivo</td>
        <td><div id="div_campo_arquivo_anexo"><input id="file_master" type="file" /></div></td>
    </tr>
    <tr>
        <td colspan="2"><span style="font-weight:bold;font-size:14px;">Arquivos Anexos</span><div id="div_arquivos_anexos"><?php echo $Venda->VendaCustomizacoes->getAnexoEnvioEmail(); ?></div></td>
    </tr>
    <tr>
        <td>Enviar cópia (separar por ;)</td>
        <td><textarea name="cc_emails" id="cc_emails" rows="4" cols="50" style="width:600px; height:30px;"></textarea></td>
    </tr>
    <tr>
        <td>Texto no corpo do e-mail</td>
        <td colspan="2"><textarea name="text_area_corpo_email" id="text_area_corpo_email" style="width:100%; height:150px;"></textarea></td>
    </tr>
</table>
</form>
<script>
    NumeroArquivo = 0;
    function CarregaCampoDeAnexo(){
        $("#file_master").change(function(){
            var CaminhoArquivo = $(this).val().replace('C:\\fakepath\\','');
            /* Criando botão de excluir anexo */
            var DOMHTMLExcluir = $('<img src="img/btn_apagar.png" alt="Excluir" title="Excluir" NumeroArquivo="' + NumeroArquivo + '"/>');
            DOMHTMLExcluir.click(function(){
                if(confirm("Remover anexo ?")){
                    var NArquivo  =$(this).attr("NumeroArquivo");
                    $("#campo_arquivo_"+NArquivo).remove();
                    $("#span_arquivo_"+NArquivo).remove();
                }
            }).css("cursor","pointer");

            /* Adicionando 'span' com o nome do arquivo */
            $("#div_arquivos_anexos").append("<span id=\"span_arquivo_" + NumeroArquivo + "\"></span>");
            $("#span_arquivo_" + NumeroArquivo).append(CaminhoArquivo);
            $("#span_arquivo_" + NumeroArquivo).append(DOMHTMLExcluir);
            $(this).attr("name","arquivo_anexo[]");
            $(this).attr("id","campo_arquivo_" + NumeroArquivo);
            $(this).css("display","none");

            var NovoCampoArquivo = $("<input>");
            NovoCampoArquivo.attr("type","file");
            NovoCampoArquivo.attr("id","file_master");
            $("#div_campo_arquivo_anexo").append(NovoCampoArquivo);
            NumeroArquivo = NumeroArquivo +1;
            CarregaCampoDeAnexo();
        });
    }
    function UploadstartCallback(){
        MaskLoading('Mostrar');
        return true;
    }
    function UploadcompleteCallback(Resposta){
        MaskLoading('Ocultar');
    }
    $(document).ready(function(){
        CarregaCampoDeAnexo();
    });
    $('#text_area_corpo_email').tinymce({
            script_url : 'tinymce/jscripts/tiny_mce/tiny_mce.js',
            theme : "advanced",
            width : 600,
            height : 350,
            plugins : "pagebreak,style,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
            theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,preview,|,forecolor,backcolor,charmap,fullscreen",
            theme_advanced_toolbar_location : "top",theme_advanced_toolbar_align : "left",theme_advanced_statusbar_location : "bottom",theme_advanced_resizing : false,content_css : "css/content.css",template_external_list_url : "lists/template_list.js",external_link_list_url : "lists/link_list.js",external_image_list_url : "lists/image_list.js",media_external_list_url : "lists/media_list.js"
    });
</script>