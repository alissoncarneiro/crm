<?php
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('includes.php');

$Usuario = new Usuario($_SESSION['id_usuario']);
/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */
if($_POST['ptp_venda'] != 1 && $_POST['ptp_venda'] != 2){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
} elseif(empty($_POST['pnumreg'])){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
} else{
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
    } else{
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
    }
    /*
     * Tratando os campos
     */
    $Venda->pfuncao = $_POST['pfuncao'];
}
if($_POST['pincluir'] == 'true'){
?>
<fieldset>
    <legend>Novo Participante da Venda</legend>
    <strong>Tipo Participa&ccedil;&atilde;o:</strong><br />
    <select id="id_tp_participacao_venda" name="id_tp_participacao_venda">
        <option value="">--Selecione--</option>
        <?php
        $SqlTipoParticipacaoVenda = "SELECT numreg,nome_tp_participacao_venda FROM is_tp_participacao_venda ORDER BY numreg";
        $QryTipoParticipacaoVenda = query($SqlTipoParticipacaoVenda);
        while($ArTipoParticipacaoVenda = farray($QryTipoParticipacaoVenda)){
            echo '<option value="'.$ArTipoParticipacaoVenda['numreg'].'">'.$ArTipoParticipacaoVenda['nome_tp_participacao_venda'].'</option>';
        }
        ?>
    </select>
    <br />
    <strong>Participante:</strong><br />
    <select id="id_participante" name="id_participante">
        <option value="">--Selecione--</option>
        <?php
        $SqlRepresentantes = "SELECT numreg,nome_usuario FROM is_usuario WHERE id_representante != '' AND NOT numreg  IN(SELECT DISTINCT id_representante FROM ".$Venda->getTabelaVendaRepresentante()." WHERE ".$Venda->getCampoChaveTabelaVendaRepresentante()." = '".$Venda->getNumregVenda()."')";
        $QryRepresentantes = query($SqlRepresentantes);
        while($ArRepresentantes = farray($QryRepresentantes)){
            echo '<option value="'.$ArRepresentantes['numreg'].'">'.$ArRepresentantes['nome_usuario'].'</option>';
        }
        ?>
    </select>
    <br />
    <strong>% de Comissão:</strong><br />
    <input type="text" id="pct_comissao" class="venda_campo_desconto">
    <hr size="1" />
    <input type="button" class="botao_jquery" value="Adicionar" id="btn_adicionar_representante" style="cursor: pointer"/>
</fieldset>
<script>
    $(document).ready(function(){
        $(".botao_jquery").button();
        $("#btn_adicionar_representante").click(function(event){
            var id_tp_participacao_venda    = $("#id_tp_participacao_venda").val();
            var id_participante             = $("#id_participante").val();
            var pct_comissao                = $("#pct_comissao").val();
            var ptp_venda                   = $("#ptp_venda").val();
            var pnumreg                     = $("#pnumreg").val();

            if(id_tp_participacao_venda == ''){
                alert('Selecione o tipo de participação.');
                event.preventDefault();
                return false;
            }
            if(id_participante == ''){
                alert('Selecione um participante.');
                event.preventDefault();
                return false;
            }
            if(pct_comissao == ''){
                alert('Preencha uma comissão válida.');
                event.preventDefault();
                return false;
            }
            if(confirm('Deseja adicionar esse representante ?')){
                $.ajax({
                    url: "p4_venda_participantes_post.php",
                    global: false,
                    type: "POST",
                    data: ({
                        Acao:'adicionar',
                        id_tp_participacao_venda: id_tp_participacao_venda,
                        id_participante: id_participante,
                        pct_comissao: pct_comissao,
                        ptp_venda: ptp_venda,
                        pnumreg: pnumreg
                    }),
                    dataType: "xml",
                    async: true,
                    beforeSend: function(){

                    },
                    error: function(){
                        alert('Erro com a requisição');
                        $(this).dialog("close");
                    },
                    success: function(xml){
                        var Status = $(xml).find('status').text();
                        if(Status == 'false'){
                            alert($(xml).find('mensagem').text());
                        }
                        else{
                            $("#notify-container").notify("create",{
                                title: 'Alerta',
                                text: $(xml).find('mensagem').text()
                            },{
                                expires: 5000,
                                speed: 500,
                                sticky:true,
                                stack: "above"
                            });
                        }
                        $("#jquery-dialog").dialog("close");
                        $("#jquery-dialog1").dialog("close");
                        $("#btn_representantes_venda").click();
                    }
                });
            }
        }).css("cursor","pointer");
    });
</script>
<?php
}
else{
?>
<form name="form_participantes_venda" action="#" onsubmit="return false;">
    <table width="100%">
        <tr bgcolor="#DAE8F4" class="tit_tabela">
            <td>Participante</td>
            <td>% Comissão</td>
            <td>Vl. Comissão</td>
            <td>Tipo Participa&ccedil;&atilde;o</td>
            <td colspan="2">&nbsp;</td>
        </tr>
        <?php
        foreach($Venda->getVendaRepresentantes() as $IndiceRepresentante => $Representante){
            $bg_color = ($i % 2 == 0)?'#EAEAEA':'#FFFFFF';
            $i++;
        ?>
            <tr bgcolor="<?php echo $bg_color;?>">
                <td><?php echo $Representante->getRepresentanteUsuario()->getNome();?></td>
                <td style="vertical-align: middle;">
                    <?php if((!$Venda->getDigitacaoCompleta() || $Venda->getEmAprovacao()) && $Usuario->getPermissao('sn_permite_alterar_comis_part')){ /* Se possui permissão para alterar a comissão */?>
                    <input type="text" id="pct_comissao_<?php echo $Representante->getDadosVendaRepresentante('numreg');?>" class="venda_campo_comissao" value="<?php echo $Venda->NFD($Representante->getDadosVendaRepresentante('pct_comissao'));?>">%
                    <img src="img/salvar_pequeno.png" alt="Alterar" title="Alterar" class="venda_btn_alterar_representante" NumregVendaRepresentante="<?php echo $Representante->getDadosVendaRepresentante('numreg');?>" />
                    <?php } else { echo $Venda->NFD($Representante->getDadosVendaRepresentante('pct_comissao')).'%'; } ?>
                </td>
                <td><?php echo number_format_min($Representante->getVlComissao(),2,',','.');?></td>
                <td><?php echo $Representante->getDescricaoTipoParticipacaoVenda();?></td>
                <td><?php if((!$Venda->getDigitacaoCompleta() || $Venda->getEmAprovacao()) && $Usuario->getPermissao('sn_permite_alterar_comis_part')){ ?><img src="img/crifao_pequeno.png" alt="Comissão Representante x Item" title="Comissão Representante x Item" class="venda_btn_comissao_representantexitem" NumregVendaRepresentante="<?php echo $Representante->getDadosVendaRepresentante('numreg');?>" /><?php } else { echo '&nbsp;';} ?></td>
                <td><?php if(!$Representante->isPrincipal() && ((!$Venda->getDigitacaoCompleta() || $Venda->getEmAprovacao()) && $Usuario->getPermissao('sn_permite_add_particip_venda'))){?><img src="img/btn_apagar.png" alt="Excluir" title="Excluir" class="venda_btn_excluir_representante" NumregVendaRepresentante="<?php echo $Representante->getDadosVendaRepresentante('numreg');?>" /><?php } else { echo '&nbsp;';} ?></td>
            </tr>
        <?php } ?>
    </table>
</form>
<div id="jquery-dialog1"></div>
<div id="jquery-dialog2"></div>
<div id="jquery-dialog3"></div>
<script>
    $(document).ready(function(){
        $(".venda_btn_alterar_representante").click(function(){
            var NumregVendaRepresentante = $(this).attr("NumregVendaRepresentante");
            var pct_comissao = $("#pct_comissao_"+NumregVendaRepresentante).val();
            var MensagemAlerta;

            var Dialog = $("#jquery-dialog2");
            Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Alerta');

            MensagemAlerta = '<h2 style="font-size:14px;">Alteração de Comissão<h2>';
            MensagemAlerta += 'Justificativa:<br /><textarea id="textarea_justificativa_alt_comis" style="width:270px;" cols="45" rows="3"></textarea><br />';

            Dialog.html(MensagemAlerta);
            Dialog.dialog({
                buttons:{
                    "Alterar Comissão": function(){
                        if($("#textarea_justificativa_alt_comis").val() == ''){
                            alert('Justificativa é obrigatória!');
                            return false;
                        }
                        if(confirm("Confirma alteração da comissão ?")){
                            Dialog.dialog("close");
                            $.ajax({
                                url: "p4_venda_participantes_post.php",
                                global: false,
                                type: "POST",
                                data: ({
                                    Acao:'alterar_comissao',
                                    NumregVendaRepresentante: NumregVendaRepresentante,
                                    pct_comissao: pct_comissao,
                                    pjustificativaalteracacomissao: escape($("#textarea_justificativa_alt_comis").val()),
                                    pnumreg: $("#pnumreg").val(),
                                    ptp_venda: $("#ptp_venda").val()
                                }),
                                dataType: "xml",
                                async: true,
                                beforeSend: function(){

                                },
                                error: function(){
                                    alert('Erro com a requisição');
                                    Dialog.dialog("close");
                                },
                                success: function(xml){
                                    var Status = $(xml).find('status').text();
                                    if(Status == 'false'){
                                        alert($(xml).find('mensagem').text());
                                    }
                                    else{
                                        $("#notify-container").notify("create",{
                                            title: 'Alerta',
                                            text: $(xml).find('mensagem').text()
                                        },{
                                            expires: 5000,
                                            speed: 500,
                                            sticky:true,
                                            stack: "above"
                                        });
                                    }
                                    $("#jquery-dialog").dialog("close");
                                    setTimeout('$("#btn_representantes_venda").click();',1000);
                                }
                            });
                        }
                    },
                    Fechar: function(){
                        $(this).dialog("close");
                    }
                },
                modal: true,
                show: "fade",
                hide: "fade"
            });
        }).css("cursor","pointer");

        $(".venda_btn_excluir_representante").click(function(){
            if(confirm('Deseja excluir o participante ?')){
                var NumregVendaRepresentante = $(this).attr("NumregVendaRepresentante");
                var pct_comissao = $("#pct_comissao_"+NumregVendaRepresentante).val();
                $.ajax({
                    url: "p4_venda_participantes_post.php",
                    global: false,
                    type: "POST",
                    data: ({
                        Acao:'excluir',
                        NumregVendaRepresentante: NumregVendaRepresentante,
                        pct_comissao: pct_comissao,
                        pnumreg: $("#pnumreg").val(),
                        ptp_venda: $("#ptp_venda").val()
                    }),
                    dataType: "xml",
                    async: true,
                    beforeSend: function(){

                    },
                    error: function(){
                        alert('Erro com a requisição');
                        $(this).dialog("close");
                    },
                    success: function(xml){
                        $("#notify-container").notify("create",{
                            title: 'Alerta',
                            text: $(xml).find('mensagem').text()
                        },{
                            expires: 5000,
                            speed: 500,
                            sticky:true,
                            stack: "above"
                        });
                        $("#jquery-dialog").dialog("close");
                        $("#btn_representantes_venda").click();
                    }
                });
            }
        }).css("cursor","pointer");

        $(".venda_btn_comissao_representantexitem").click(function(){
            var IndiceRepresentante = $(this).attr("NumregVendaRepresentante");
            var Dialog = $("#jquery-dialog2");
            Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Comiss&atilde;o Representante x Item');
            Dialog.html(HTMLLoading);
            Dialog.dialog({
                width: 600,
                height: 600,
                buttons:{
                    "Alterar Comissões": function(){
                        var Dialog2 = $("#jquery-dialog3");
                        Dialog2.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Alerta');

                        MensagemAlerta = '<h2 style="font-size:14px;">Alteração de Comissão<h2>';
                        MensagemAlerta += 'Justificativa:<br /><textarea id="textarea_justificativa_alt_comis_item" style="width:270px;" cols="45" rows="3"></textarea><br />';

                        Dialog2.html(MensagemAlerta);
                        Dialog2.dialog({
                            buttons:{
                                "Confirmar": function(){
                                    if($("#textarea_justificativa_alt_comis_item").val() == ''){
                                        alert('Justificativa é obrigatória!');
                                        return false;
                                    }
                                    if(confirm("Confirma alteração da comissão ?")){
                                        Dialog.dialog("close");
                                        Dialog2.dialog("close");
                                        $.ajax({
                                            url: "p4_venda_comissao_item_post.php",
                                            global: false,
                                            type: "POST",
                                            data: $("#form_comissao_representantexitem").serialize() + '&pjustificativaalteracacomissaoitem=' + escape($("#textarea_justificativa_alt_comis_item").val()),
                                            dataType: "xml",
                                            async: true,
                                            beforeSend: function(){

                                            },
                                            error: function(){
                                                alert('Erro com a requisição');
                                                Dialog2.dialog("close");
                                            },
                                            success: function(xml){
                                                $("#notify-container").notify("create",{
                                                    title: 'Alerta',
                                                    text: $(xml).find('mensagem').text()
                                                },{
                                                    expires: 5000,
                                                    speed: 500,
                                                    sticky:true,
                                                    stack: "above"
                                                });
                                                Dialog.dialog("close");
                                                Dialog2.dialog("close");
                                                $("#jquery-dialog").dialog("close");
                                                $("#btn_representantes_venda").click();
                                            }
                                        });
                                    }
                                },
                                Fechar: function(){
                                    $(this).dialog("close");
                                }
                            },
                            modal: true,
                            show: "fade",
                            hide: "fade"
                        });
                    },
                    Fechar: function(){
                        $(this).dialog("close");
                    }
                },
                open: function(){
                    $.ajax({
                        url: "p4_venda_comissao_item.php",
                        global: false,
                        type: "POST",
                        data: ({
                            ptp_venda:$("#ptp_venda").val(),
                            pnumreg:'<?php echo $Venda->getNumregVenda();?>',
                            indice_representante:IndiceRepresentante
                        }),
                        dataType: "html",
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
        }).css("cursor","pointer");
    });
</script>
<?php } ?>