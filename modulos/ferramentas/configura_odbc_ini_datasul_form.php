<?php
/*
 * configura_odbc_ini_datasul_form.php
 * Autor: Anderson JS
 * 23/02/2011 12:45
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html; charset=ISO-8859-1;");
include('../../conecta.php');
$ArrayConf = parse_ini_file('../../conecta_odbc_erp_datasul.ini',true);
?>
<style type="text/css">
    #configura_odbc_ini{
        margin: 10px;
    }
    #configura_odbc_ini legend{
        font-size: 16px;
        font-weight: bold;
    }
</style>
<div id="configura_odbc_ini">
<fieldset>
    <legend>Preenche ODBC INI Datasul</legend>
    Alias 1 - <input type="text" id="alias_odbc1" id="alias_odbc1" value="<?php echo $ArrayConf['Alias'][1];?>" /><span id="status_alias_odbc1"></span><br/>
    Alias 2 - <input type="text" id="alias_odbc2" id="alias_odbc2" value="<?php echo $ArrayConf['Alias'][2];?>" /><span id="status_alias_odbc2"></span><br/>
    Alias 3 - <input type="text" id="alias_odbc3" id="alias_odbc3" value="<?php echo $ArrayConf['Alias'][3];?>" /><span id="status_alias_odbc3"></span><br/>
    Alias 4 - <input type="text" id="alias_odbc4" id="alias_odbc4" value="<?php echo $ArrayConf['Alias'][4];?>" /><span id="status_alias_odbc4"></span><br/>
    Alias 5 - <input type="text" id="alias_odbc5" id="alias_odbc5" value="<?php echo $ArrayConf['Alias'][5];?>" /><span id="status_alias_odbc5"></span><br/>
    Alias 6 - <input type="text" id="alias_odbc6" id="alias_odbc6" value="<?php echo $ArrayConf['Alias'][6];?>" /><span id="status_alias_odbc6"></span><br/>
    <br/>
    <input type="button" class="botao_jquery" id="btn_verificar_alias" value="Verificar Alias" />
    <input type="button" class="botao_jquery" id="btn_verificar_tabelas" value="Verificar Tabelas" />
    <input type="button" class="botao_jquery" id="btn_gerar_arquivo" value="Gerar Arquivo INI" />
</fieldset>
<div id="configura_odbc_ini_lista_tabelas"></div>
</div>
<script>
$(document).ready(function(){
    var ImgOk = "&nbsp;<img src=\"images/btn_verde.png\" alt=\"Ok\" /> Conectado com sucesso!";
    var ImgErro = "&nbsp;<img src=\"images/btn_vermelho.png\" alt=\"Erro\" /> Conex&atilde;o n&atilde;o estabelecida!";
    $(".botao_jquery").button();
    $("#btn_verificar_alias").click(function(){
        var Mask = $("<div id=\"modal_mask_loading\"></div>");
        $("body").append(Mask);
        Mask.css("opacity", 0.5);
        Mask.fadeIn();
        $.ajax({
            url: "modulos/ferramentas/configura_odbc_ini_datasul_verifica_conexao.php",
            global: false,
            type: "POST",
            data: ({
                alias1:$("#alias_odbc1").val(),
                alias2:$("#alias_odbc2").val(),
                alias3:$("#alias_odbc3").val(),
                alias4:$("#alias_odbc4").val(),
                alias5:$("#alias_odbc5").val(),
                alias6:$("#alias_odbc6").val()
            }),
            dataType: "xml",
            async: true,
            beforeSend: function(){

            },
            error: function(){
                alert('Erro com a requisição');
                Mask.fadeOut(100);
            },
            success: function(xml){
                Resposta = $(xml).find("resposta");
                Resposta.find('conexao').each(function(){
                    var NumeroConexao = $(this).find("numero_conexao").text();
                    var StatusConexao = $(this).find("status_conexao").text();
                    if($("#alias_odbc"+NumeroConexao).val() == ''){
                        $("#status_alias_odbc"+NumeroConexao).html('');
                    }
                    else if(StatusConexao == 'true'){
                        $("#status_alias_odbc"+NumeroConexao).html(ImgOk);
                    }
                    else{
                        $("#status_alias_odbc"+NumeroConexao).html(ImgErro);
                    }
                });
                Mask.fadeOut(100);
            }
        });
    });

    $("#btn_verificar_tabelas").click(function(){
        var Mask = $("<div id=\"modal_mask_loading\"></div>");
        $("body").append(Mask);
        Mask.css("opacity", 0.5);
        Mask.fadeIn();
        $.ajax({
            url: "modulos/ferramentas/configura_odbc_ini_datasul_verifica_tabelas.php",
            global: false,
            type: "POST",
            data: ({
                alias1:$("#alias_odbc1").val(),
                alias2:$("#alias_odbc2").val(),
                alias3:$("#alias_odbc3").val(),
                alias4:$("#alias_odbc4").val(),
                alias5:$("#alias_odbc5").val(),
                alias6:$("#alias_odbc6").val()
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
                $("#configura_odbc_ini_lista_tabelas").html(responseText);
                Mask.fadeOut(100);
            }
        });
    });

    $("#btn_gerar_arquivo").click(function(){
        var Mask = $("<div id=\"modal_mask_loading\"></div>");
        $("body").append(Mask);
        Mask.css("opacity", 0.5);
        Mask.fadeIn();
        $.ajax({
            url: "modulos/ferramentas/configura_odbc_ini_datasul_verifica_tabelas.php",
            global: false,
            type: "POST",
            data: ({
                alias1:$("#alias_odbc1").val(),
                alias2:$("#alias_odbc2").val(),
                alias3:$("#alias_odbc3").val(),
                alias4:$("#alias_odbc4").val(),
                alias5:$("#alias_odbc5").val(),
                alias6:$("#alias_odbc6").val(),
                sn_gerar_arquivo:1
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
                $("#configura_odbc_ini_lista_tabelas").html(responseText);
                Mask.fadeOut(100);
            }
        });
    });
});
</script>