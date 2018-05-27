<?php

/*
 * p2_adiciona_item_por_linha_familia_tabela.php
 * Autor: Alex
 * 01/06/2011 12:32:12
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('includes.php');

$Usuario = new Usuario($_SESSION['id_usuario']);
if($_POST['ptp_venda'] == 1 || $_POST['ptp_venda'] == 2){
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
    }
    elseif($_POST['ptp_venda'] == 2){
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
    }
}
else{
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
$VendaParametro = new VendaParametro();

$IdFamiliaComercial = $_POST['id_familia_comercial'];

if($VendaParametro->getSnPermiteAdicionarItemRepetido()){
    $SqlProduto = "SELECT numreg,id_produto_erp,nome_produto FROM is_produto WHERE sn_ativo = 1 AND id_familia_comercial = '".$IdFamiliaComercial."' ORDER BY nome_produto";
}
else{
    $SqlProduto = "SELECT t1.numreg,t1.id_produto_erp,t1.nome_produto FROM is_produto t1 WHERE t1.sn_ativo = 1 AND t1.id_familia_comercial = '".$IdFamiliaComercial."' AND NOT t1.numreg IN(SELECT t2.id_produto FROM ".$Venda->getTabelaVendaItem()." t2 WHERE t2.".$Venda->getCampoChaveTabelaVendaItem()." = '".$Venda->getNumregVenda()."') ORDER BY t1.nome_produto";
}
$QryProduto = query($SqlProduto);
$NumRowsProduto = numrows($QryProduto);
if($NumRowsProduto <= 0){
    echo '<div class="error">Nenhum produto encontrado.</div>';
    exit;
}
$i=0;
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="venda_tabela_itens">
    <tr class="venda_titulo_tabela">
        <td>C&oacute;d. Produto</td>
        <td>Descri&ccedil;&atilde;o</td>
        <td>Qtde.</td>
        <?php if($Venda->isPrecoInformado()){ ?>
        <td>Vl. Unit.</td>
        <?php } ?>
        <td>Unid. Medida</td>
        <?php if($Venda->getSnExibeEmbalagem()){ ?>
        <td>Embalagem</td>
        <?php } ?>
        <?php if($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem()){ ?>
        <td>Tab. Pre&ccedil;o</td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <?php
    while($ArProduto = farray($QryProduto)){
        $Produto = new Produto($ArProduto['numreg']);
        if(!$VendaParametro->getSnPermiteAddProdNaoFat() && !$Produto->isFaturavel($Venda->getIdEstabelecimento())){
            continue;
        }
        elseif($Venda->getSnExibeEmbalagem() && count($Produto->getArEmbalagem()) == 0){
            continue;
        }
        $i++;
        $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
        
        /* Imagem */
        $UrlImagemProduto = 'img/produto_sem_imagem.gif';
        $Url = array(
            0 => '../../imagem_produto/'.$Produto->getDadosProduto('id_produto_erp').'.jpg',
            1 => '../../imagem_produto/'.$Produto->getDadosProduto('id_produto_erp').'.gif',
            2 => '../../imagem_produto/'.$Produto->getDadosProduto('id_produto_erp').'.png'
        );
        if(file_exists($Url[0])){
            $UrlImagemProduto = $Url[0];
        }
        elseif(file_exists($Url[1])){
            $UrlImagemProduto = $Url[1];
        }
        elseif(file_exists($Url[2])){
            $UrlImagemProduto = $Url[2];
        }
        
    ?>
    <tr bgcolor="<?php echo $bgcolor;?>" id="tr_<?php echo $ArProduto['numreg'];?>">
        <td><?php echo $ArProduto['id_produto_erp'];?></td>
        <td><?php echo $ArProduto['nome_produto'];?></td>
        <td><input type="text" size="10" class="quantidade venda_campo_qtde" CasasDecimais="<?php echo $Venda->getPrecisaoQtde();?>" name="Adicionar_qtde_<?php echo $ArProduto['numreg'];?>" id="Adicionar_qtde_<?php echo $ArProduto['numreg'];?>" value="<?php echo $Qtde;?>"/></td>
        <?php if($Venda->isPrecoInformado()){ ?>
        <td><input type="text" size="10" class="monetario venda_campo_vl_unitario" CasasDecimais="<?php echo $Venda->getPrecisaoValor();?>" name="Adicionar_vl_unitario_<?php echo $ArProduto['numreg'];?>" id="Adicionar_vl_unitario_<?php echo $ArProduto['numreg'];?>" value="<?php echo $VlUnitario;?>"/></td>
        <?php } ?>
        <td>
        <select id="Adicionar_id_unid_medida_<?php echo $ArProduto['numreg'];?>" name="Adicionar_id_unid_medida_<?php echo $ArProduto['numreg'];?>">
        <?php
        $QryUnidMedida = query("SELECT numreg, nome_unid_medida FROM is_unid_medida ORDER BY nome_unid_medida ASC");
        $Options = '';
        if($Venda->getVendaParametro()->getModoUnidMedida() == '3'){
            $IdUnidaMedida = $Produto->getIdUnidMedidaAtacadoVarejo($Venda->getGrupoTabPreco());
        }
        else{
            $IdUnidaMedida = $Produto->getIdUnidMedidaPadrao();
        }
        if($IdUnidaMedida === false){
            $IdUnidaMedida = $Produto->getIdUnidMedidaPadrao();
        }
        while($ArUnidMedida = farray($QryUnidMedida)){
            $ArUnidMedida['nome_unid_medida'] = ($Venda->isAtacado())?'CX':$ArUnidMedida['nome_unid_medida'];
            if($ArUnidMedida['numreg'] == $IdUnidaMedida){
                $Options .= '<option value="'.$ArUnidMedida['numreg'].'" selected="selected">'.$ArUnidMedida['nome_unid_medida'].'</option>';
            }
        }
        if($Options != ''){
            echo $Options;
        }
        else{
            echo '<option value="">&nbsp;</option>';
        }
        ?></select>
        </td>
        <?php if($Venda->getSnExibeEmbalagem()){ ?>
        <td><?php echo $Produto->MontaHTMLSelectEmbalagem('Adicionar_id_produto_embalagem_'.$ArProduto['numreg']);?></td>
        <?php } ?>
        <?php if($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem()){?>
        <td>
        <select id="Adicionar_id_tab_preco_<?php echo $ArProduto['numreg'];?>" name="Adicionar_id_tab_preco_<?php echo $ArProduto['numreg'];?>">
        <?php
        $Options = '';
        if($Venda->getUsuario()->getPermissao('sn_permite_alterar_tb_preco_it')){ /* Se o usuário tem permissão para informar a tabela de preço do item */
            foreach($ArTabPrecos as $k => $v){
                $Selected = ($v[0] == $Produto->getDadosProduto('id_tab_preco_padrao'))?' selected="selected" ':'';
                $Options .= '<option value="'.$v[0].'" '.$Selected.'>'.$v[1].'</option>';
            }
        }
        else{
            foreach($ArTabPrecos as $k => $v){
                if($v[0] == $Produto->getDadosProduto('id_tab_preco_padrao')){
                    $Options .= '<option value="'.$v[0].'">'.$v[1].'</option>';
                    break;
                }
            }
        }
        if($Options != ''){
            echo $Options;
        }
        else{
            echo '<option value="">&nbsp;</option>';
        }
        ?></select>
        </td>
        <?php } ?>
        <td>
            <div style="width: 150px;">
            <?php if($VendaParametro->getModoImgProd() != '3') {?>
            <img src="img/estoque_pequeno.gif" alt="Ver Estoque" title="Ver Estoque" class="btn_tela_familia_estoque" numreg_prod="<?php echo $Produto->getNumregProduto();?>"/>
            <?php } ?>
            <?php if($VendaParametro->getSnConsultaEstoque()){ ?>
            <img src="img/icon_image.jpg" alt="Imagem do produto" title="Imagem do produto" class="btn_imagem_produto_tela_familia" url_imagem="<?php echo $UrlImagemProduto;?>" />
            <?php } ?>
            <input type="button" class="botao_jquery btn_add_produto" value="Adicionar" NumregProduto="<?php echo $ArProduto['numreg'];?>" />
            <div>
        </td>
    </tr>
    <?php } ?>
</table>
<script type="text/javascript">
$(document).ready(function(){
    $(".botao_jquery").button();
    
    /* Imagem do Produto */
    $(".btn_imagem_produto_tela_familia").each(function(){
        var UrlImagem = $(this).attr("url_imagem");
        $(this).css({"cursor":"pointer","vertical-align":"bottom"});
        $(this).qtip({
            content:{
                text: '<img src="' + UrlImagem + '" style="max-width: 350px;" />',
                title: {text: 'Imagem do Produto',button: 'Fechar'}
            },
            style:{
                width: 'auto',
                height: 'auto'
            },
            position: {corner: {target: 'topMiddle',tooltip: 'bottomMiddle'},adjust: {screen: true}},
            show: {when: 'click',solo: true},
            hide: 'unfocus'
        });
    });
    
    $(".btn_tela_familia_estoque").click(function(){
        IdProduto = $(this).attr("numreg_prod");
        if(IdProduto == ''){
            alert("Produto não informado!");
            return false;
        }
        $.ajax({
            url: "../estoque/consulta_estoque_simples.php",
            global: false,
            type: "POST",
            data: ({
                pid_produto:IdProduto
            }),
            dataType: "html",
            async: true,
            beforeSend: function(){

            },
            error: function(){
                alert('Erro com a requisição');
            },
            success: function(responseText){
                alert(responseText + ' unidade(s) em estoque.');
            }
        });
        return true;
    }).css("cursor","pointer");

    $(".btn_add_produto").click(function(event){
        var NumregProduto = $(this).attr("NumregProduto");
        if($("#Adicionar_qtde_"+NumregProduto).val() == ''){
            event.preventDefault();
            alert("Informe a quantidade.");
            $("#Adicionar_qtde_"+NumregProduto).focus();
            return false;
        }
        $(this).button("option","disabled",true);
        $.ajax({
            url: "p2_adiciona_item.php",
            global: false,
            type: "POST",
            data: ({
                pnumreg: $("#pnumreg").val(),
                ptp_venda: $("#ptp_venda").val(),
                id_produto: NumregProduto,
                qtde: $("#Adicionar_qtde_"+NumregProduto).val(),
                id_unid_medida: $("#Adicionar_id_unid_medida_"+NumregProduto).val(),
                id_referencia: escape($("#Adicionar_id_referencia_"+NumregProduto).val()),
                id_produto_embalagem: $("#Adicionar_id_produto_embalagem_"+NumregProduto).val(),
                id_tab_preco: $("#Adicionar_id_tab_preco_"+NumregProduto).val(),
                vl_unitario: $("#Adicionar_vl_unitario_"+NumregProduto).val()
            }),
            dataType: "xml",
            async: true,
            beforeSend: function(){

            },
            error: function(){
                alert('Erro com a requisição');
                $(this).button("option","disabled",false);
            },
            success: function(xml){
               if($(xml).find('status').text() == 2){
                   var Dialog = $("#jquery-dialog2");
                    Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Alerta');
                    Dialog.html($(xml).find('mensagem').text());
                    Dialog.dialog({
                        width:400,
                        height:300,
                        buttons:{Fechar: function(){$(this).dialog("close");}},
                        modal: true,
                        show: "fade",
                        hide: "fade"
                    });
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
                    $("#tr_"+NumregProduto).hide();
                    exibe_tabela_item();
                }
            }
        });
    });
});
</script>