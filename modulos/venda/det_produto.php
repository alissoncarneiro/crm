<?php
/*
 * p2_det_produto.php
 * Autor: Alex
 * 01/11/2010 09:42:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
require('includes.php');

$Produto = new Produto($_POST['pid_produto']);
$IdProduto = $Produto->getNumregProduto();
/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */

if($_POST['ptp_venda'] == 1 || $_POST['ptp_venda'] == 2){
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
    } elseif($_POST['ptp_venda'] == 2){
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
    }
}
else{
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
$RecarregarPagina = false;
$Qtde = '';
$VlUnitario = '';
$IdItemPre = '';
/* Definindo se há algum valor padrão */
if($_POST['pQtde'] != ''){
    $Qtde = $_POST['pQtde'];
}
if($_POST['pVlUnitario'] != ''){
    $VlUnitario = $_POST['pVlUnitario'];
}
if($_POST['pRecarregarPagina'] == 'true'){
    $RecarregarPagina = true;
}
if($_POST['pIdItemPre'] != ''){
    $IdItemPre = $_POST['pIdItemPre'];
}

$VendaParametro = new VendaParametro();
if($Venda->isPrecoInformado()){ /* Se a venda é preco informado */
    $PrecoProduto = new PrecoProduto($IdProduto, $Venda->getGrupoTabPreco(), NULL, NULL, NULL);

    /* Tratamento para quando usa sugetsão de preço por NF */
    if($VendaParametro->getSnUsaSugestaoDePrecoDeNF()){
        $ArDadosPrecoNF                 = array();
        $ArDadosPrecoNF['uf']           = $Venda->getDadosEnderecoEntrega('uf');
        $ArDadosPrecoNF['id_produto']   = $IdProduto;
        $PrecoProduto->CalculaSugestaoDePrecoDeNF($ArDadosPrecoNF);
    }

    $ProdutoVlUnitario = $PrecoProduto->getPreco();
}
else{
    $PrecoProduto = new PrecoProduto($IdProduto,$Venda->getGrupoTabPreco(), $Venda->getIdTabPreco());
    $ProdutoVlUnitario = $PrecoProduto->getPreco();
}

$ConsultaEstoque = $VendaParametro->getSnConsultaEstoque();
$ConsultaEstoqueCallBack = VendaCallBackCustom::ExecutaVenda($Venda, 'Passo2_DetItem_CalcEstoque', '');
if($ConsultaEstoqueCallBack !== true){
    if($ConsultaEstoqueCallBack == '1'){
        $ConsultaEstoque = true;
    }
    elseif($ConsultaEstoqueCallBack == '2'){
        $ConsultaEstoque = false;
    }
}

if($ConsultaEstoque){
    if($VendaParametro->getSnUsaURLEstoqXmlDatasul() && $VendaParametro->getURLEstoqueXmlErpDatasul() != ''){
        $ConsultaEstoqueErpDatasul      = new ConsultaEstoqueXMLErpDatasul($VendaParametro,$IdProduto,$IdEstabelecimento);
        $QuantidadeDisponivel           = $ConsultaEstoqueErpDatasul->getQuantidadeDisponivel();
        $SaldoEstoqueTotal              = $ConsultaEstoqueErpDatasul->getQuantidadeAtual();
        $PedidosNaoFaturadosErpTotal    = $ConsultaEstoqueErpDatasul->getQuantidadeNaoFaturada();
        $PedidosNaoIntegradosTotal      = $ConsultaEstoqueErpDatasul->getQuantidadeNaoIntegrada();
        $ArReferencia                   = $ConsultaEstoqueErpDatasul->getArReferencia();
    }
    else{
        //ESTOQUE
        $SaldoEstoque = new ConsultaEstoqueCustom($VendaParametro);
        $SaldoEstoque->setIdProduto($IdProduto);
        $SaldoEstoque->setIdEstabelecimento($IdEstabelecimento);
        $QuantidadeDisponivel           = $SaldoEstoqueTotal - $PedidosNaoFaturadosErpTotal - $PedidosNaoIntegradosTotal;
        $SaldoEstoqueTotal              = $SaldoEstoque->getSaldoEstoqueTotal();
        $PedidosNaoFaturadosErpTotal    = $SaldoEstoque->getPedidosNaoFaturadosErpTotal();
        $PedidosNaoIntegradosTotal      = $SaldoEstoque->getPedidosNaoIntegradosTotal();

        $ArReferencia                   = $SaldoEstoque->getArReferencia();
    }
    $QtdeEmEstoque = $QuantidadeDisponivel - $PedidosNaoFaturadosErpTotal - $PedidosNaoIntegradosTotal;
}
else{
    $QtdeEmEstoque = 0;
    $ArReferencia = array();
}
if($Venda->getSnExibeEmbalagem()){
    $ArEmbalagem = $Produto->getArEmbalagem();
}

$ArTabPrecos = $Venda->getArTabPrecos();

$BloqueiaAdicionarItem = false;
$BloqueiaAdicionarItemMensagem = NULL;
/*
 * Validando se há algo que impessa a inclusão do item
 */
if(empty($ProdutoVlUnitario) && !$VendaParametro->getPermiteAdicionarItemSemPreco()){//Se o produto não tem preço e o parâmetro de venda não permite adicionar produto sem preço
    $BloqueiaAdicionarItem = true;
    $BloqueiaAdicionarItemMensagem .= '<div class="error">Não é permitido adicionar itens sem preço. Entre em contato com o Administrador.</div>';
}
elseif(count($ArReferencia) == 0 && ($Venda->isPedido() && !$VendaParametro->getPermiteAdicionarItemSemReferencia()) || ($Venda->isOrcamento() && !$VendaParametro->getPermiteAdicionarItemSemReferenciaOrcamento())){
    $BloqueiaAdicionarItem = true;
    $BloqueiaAdicionarItemMensagem .= '<div class="error">Não é permitido adicionar itens sem referência. Entre em contato com o Administrador.</div>';
}
elseif($Venda->getSnExibeEmbalagem() && count($ArEmbalagem) == 0){
    $BloqueiaAdicionarItem = true;
    $BloqueiaAdicionarItemMensagem .= '<div class="error">Para vendas no atacado, não é permitido adicionar itens sem embalagem. Entre em contato com o Administrador.</div>';
}
elseif(!$VendaParametro->getSnPermiteAdicionarItemRepetido() && $Venda->VerificaSeExisteProduto($Produto->getNumregProduto())){
    $BloqueiaAdicionarItem = true;
    $BloqueiaAdicionarItemMensagem .= '<div class="error">Não é permitido adicionar itens repetidos.</div>';
}
/* Se usa tabela de preço por item e o produto não possui uma tabela de preço padrão e o usuário não tem permissão para alterar a tabela de preço */
elseif($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem() && $Produto->getDadosProduto('id_tab_preco_padrao') == '' && !$Venda->getUsuario()->getPermissao('sn_permite_alterar_tb_preco_it')){
    $BloqueiaAdicionarItem = true;
    $BloqueiaAdicionarItemMensagem .= '<div class="error">Produto não possui uma Tabela de Preço/Moeda associada. Entre em contato com o administrador do sistema.</div>';
}
elseif(!$VendaParametro->getSnPermiteAddProdNaoFat() && !$Produto->isFaturavel($Venda->getIdEstabelecimento())){
    $BloqueiaAdicionarItem = true;
    $BloqueiaAdicionarItemMensagem .= '<div class="error">Produto n&atilde;o &eacute; fatur&aacute;vel para o estabelecimento.</div>';
}
elseif(!$Produto->getSnAtivo()){
    $BloqueiaAdicionarItem = true;
    $BloqueiaAdicionarItemMensagem .= '<div class="error">Produto est&aacute; inativo.</div>';
}

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
else{
    if($Produto->getDadosProduto('arquivo_imagem') != ''){
        $UrlImagem = '../../arquivos/'.$Produto->getDadosProduto('arquivo_imagem');
        if(file_exists($UrlImagem)){
            $UrlImagemProduto = $UrlImagem;
        }
    }
}
?>
<img src="<?php echo $UrlImagemProduto;?>" style="display:none;" />
<input type="hidden" name="Adicionar_id_produto" id="Adicionar_id_produto" value="<?php echo $Produto->getDadosProduto('numreg');?>" />
<input type="hidden" name="Adicionar_id_item_pre" id="Adicionar_id_item_pre" value="<?php echo $IdItemPre;?>" />
<fieldset style="background-color:#EAEAEA;"><legend>Produto</legend>
    <table width="100%" border="0" cellspacing="2" cellpadding="0">
        <tr>
            <td width="40%">
                <div class="venda_subtitulo"><?php echo $Produto->getDadosProduto('id_produto_erp').' - '.$Produto->getDadosProduto('nome_produto');?></div><br />
                <?php if($BloqueiaAdicionarItem === false){?>
                <input type="hidden" name="Adicionar_id_moeda" id="Adicionar_id_moeda" value="1" />
                <span class="venda_span_preco_produto">Valor: <span id="preco"><?php echo $PrecoProduto->getStringPreco(true);?></span></span>
                <br />
                <?php if($ConsultaEstoque) { ?>
                <div><strong><?php echo number_format($QtdeEmEstoque,2,',','.'); ?></strong> unidade(s) em estoque.</div>
                <?php } ?>
                <div align="left">
                    <strong>Quantidade</strong><br />
                    <input type="text" size="10" class="quantidade venda_campo_qtde" CasasDecimais="<?php echo $Venda->getPrecisaoQtde();?>" name="Adicionar_qtde" id="Adicionar_qtde" value="<?php echo $Qtde;?>"/>
                    <?php if($Venda->isPrecoInformado()){ ?>
                    <input type="text" size="10" class="monetario venda_campo_vl_unitario" CasasDecimais="<?php echo $Venda->getPrecisaoValor();?>" name="Adicionar_vl_unitario" id="Adicionar_vl_unitario" value="<?php echo $VlUnitario;?>"/>
                    <?php } ?>
                    <select id="Adicionar_id_unid_medida" name="Adicionar_id_unid_medida">
                    <?php
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
                    
                    if($Venda->getVendaParametro()->getModoUnidMedida() == '2'){
                        $SqlUnidMedida = "SELECT numreg, nome_unid_medida FROM is_unid_medida WHERE (numreg = ".$IdUnidaMedida." OR numreg IN(SELECT id_unid_medida FROM is_produto_fator_conversao WHERE id_produto = ".$Produto->getNumregProduto().")) ";
                        $BloqueioUnidMedida = VendaCallBackCustom::ExecutaVenda($Venda, 'Passo2_SQLUnidMedidaDetProd', '');
                        $SqlUnidMedida = ($BloqueioUnidMedida !== true)?$SqlUnidMedida.$BloqueioUnidMedida:$SqlUnidMedida;
                        $SqlUnidMedida .= " ORDER BY nome_unid_medida ASC";
                        $QryUnidMedida = query($SqlUnidMedida);
                        while($ArUnidMedida = farray($QryUnidMedida)){
                            $Selected = ($ArUnidMedida['numreg'] == $IdUnidaMedida)?' selected="selected"':'';
                            $Options .= '<option value="'.$ArUnidMedida['numreg'].'"'.$Selected.'>'.$ArUnidMedida['nome_unid_medida'].'</option>';                            
                        }
                    }
                    else{
                        $QryUnidMedida = query("SELECT numreg, nome_unid_medida FROM is_unid_medida ORDER BY nome_unid_medida ASC");
                        while($ArUnidMedida = farray($QryUnidMedida)){
                            $ArUnidMedida['nome_unid_medida'] = ($Venda->isAtacado())?'CX':$ArUnidMedida['nome_unid_medida'];
                            if($ArUnidMedida['numreg'] == $IdUnidaMedida){
                                $Options .= '<option value="'.$ArUnidMedida['numreg'].'" selected="selected">'.$ArUnidMedida['nome_unid_medida'].'</option>';
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
                    <?php if($Venda->getSnExibeEmbalagem()){ ?>
                    <?php echo $Produto->MontaHTMLSelectEmbalagem('Adicionar_id_produto_embalagem');?>
                    <?php } ?>
                    <?php if(count($ArReferencia) > 0){ ?>
                    <select id="Adicionar_id_referencia" name="Adicionar_id_referencia">
                    <?php
                    $Options = '';
                    foreach($ArReferencia as $k => $v){
                        if($k == $Produto->getDadosProduto('id_referencia')){
                            $Options .= '<option value="'.$k.'" selected="selected">'.$v.'</option>';
                        }
                        else{
                            $Options .= '<option value="'.$k.'">'.$v.'</option>';
                        }
                    }
                    if($Options != ''){
                        echo $Options;
                    }
                    else{
                        echo '<option value="">&nbsp;</option>';
                    }
                    ?></select>
                    <?php } else { ?>
                    <input type="hidden" name="Adicionar_id_referencia" id="Adicionar_id_referencia" value="" />
                    <?php  } ?>
                    <?php if($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem()){?>
                    <select id="Adicionar_id_tab_preco" name="Adicionar_id_tab_preco">
                    <?php
                    $Options = '';
                    if($Venda->getUsuario()->getPermissao('sn_permite_alterar_tb_preco_it')){ /* Se o usuário tem permissão para informar a tabela de preço do item */
                        foreach($ArTabPrecos as $k => $v){
                            if($VendaParametro->getSnVendaMoedaUnica()){
                                if($Venda->getDadosVenda('id_moeda') != $v[2]){
                                    continue;
                                }
                            }
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
                    <?php } ?>
                    <input type="button" value="Adicionar" id="btn_add_produto" class="botao_jquery" RecarregarPagina="<?php echo (($RecarregarPagina)?'true':'false');?>">
                    <?php } ?>
                    <?php if($Produto->getPossuiSimilar()){ /* Se o produto possui produtos similares cadastrados */?>
                    <input type="button" value="Similares" class="botao_jquery" id="btn_exibir_produto_similar"/>
                    <?php } ?>
                    <?php if($Produto->getPossuiCrossSelling()){ /* Se o produto possui produtos de cross selling cadastrados */?>
                    <input type="button" value="Cross-Selling" class="botao_jquery" id="btn_exibir_produto_crossselling"/>
                    <?php } ?>
                    <?php if($VendaParametro->getSnConsultaEstoque()){ ?>
                    <input type="button" value="Ver Estoque" id="venda_btn_ver_estoque" class="botao_jquery" />
                    <?php } ?>
                    <?php if($VendaParametro->getModoImgProd() == '1'){ ?>
                    <img src="img/icon_image.jpg" alt="Imagem do produto" title="Imagem do produto" id="btn_imagem_produto" />
                    <?php }elseif($VendaParametro->getModoImgProd() == '2'){ ?>
                    <img src="<?php echo $UrlImagemProduto;?>" alt="Imagem do produto" title="Imagem do produto" />
                    <?php } ?>
                </div>
                <?php
                if($BloqueiaAdicionarItem === true){
                    echo $BloqueiaAdicionarItemMensagem;
                }
                ?>
            </td>
        </tr>
    </table>
    <div id="div_venda_tabela_faixa_preco_comissao"></div>
</fieldset>
<script language="javascript">
$(document).ready(function(){
    $(".numeric").keypress(function(event){
        if(event.charCode && (event.charCode < 48 || event.charCode > 57)){
            event.preventDefault();
        }
    });

    $("#btn_add_produto").click(function(event){
        var RecarregarPagina    = $(this).attr("RecarregarPagina");
        RecarregarPagina= (RecarregarPagina == 'true')?RecarregarPagina:'false';

        if($("#Adicionar_qtde").val() == ''){
            event.preventDefault();
            alert("Informe a quantidade.");
            $("#Adicionar_qtde").focus();
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
                id_produto: $("#Adicionar_id_produto").val(),
                id_produto_pai: '<?php echo $_POST['pid_produto_pai'];?>',
                qtde: $("#Adicionar_qtde").val(),
                id_moeda: $("#Adicionar_id_moeda").val(),
                id_unid_medida: $("#Adicionar_id_unid_medida").val(),
                id_referencia: escape($("#Adicionar_id_referencia").val()),
                id_produto_embalagem: $("#Adicionar_id_produto_embalagem").val(),
                id_tab_preco: $("#Adicionar_id_tab_preco").val(),
                vl_unitario: $("#Adicionar_vl_unitario").val(),
                id_item_pre: $("#Adicionar_id_item_pre").val()
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
                   var Dialog = $("#jquery-dialog");
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
                    var pid_produto = $(xml).find('pid_produto_pai').text()
                    if(pid_produto != undefined && pid_produto != 'null' && pid_produto != ''){
                        exibe_detalhe_produto(pid_produto);
                    }
                    else{
                        $("#div_det_produto").html('');
                    }

                    if(RecarregarPagina == 'true'){
                        window.location.reload();
                    }
                    else{
                        exibe_tabela_item();
                    }
                }
            }
        });
    });

    $("#btn_exibir_produto_crossselling").click(function(){
        var Dialog = $("#jquery-dialog");
        Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Selecionar Modelo');
        Dialog.html(HTMLLoading);
        Dialog.dialog({
            width:400,
            height:300,
            buttons:{Fechar: function(){$(this).dialog("close");}},
            open: function(){
                $.ajax({
                    url: "p2_crossselling.php",
                    global: false,
                    type: "POST",
                    data: ({
                        pnumreg: '<?php echo $Produto->getNumregProduto();?>'
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
            hide: "fade",
            close: function(){$(this).dialog("destroy");}
        });
    });

    $("#btn_exibir_produto_similar").click(function(){
        var Dialog = $("#jquery-dialog");
        Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Selecionar Modelo');
        Dialog.html(HTMLLoading);
        Dialog.dialog({
            width:400,
            height:300,
            buttons:{Fechar: function(){$(this).dialog("close");}},
            open: function(){
                $.ajax({
                    url: "p2_similar.php",
                    global: false,
                    type: "POST",
                    data: ({
                        pnumreg: '<?php echo $Produto->getNumregProduto();?>'
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
            hide: "fade",
            close: function(){$(this).dialog("destroy");}
        });
    });

    $("#Adicionar_qtde,#Adicionar_vl_unitario").keypress(function(event){
        if(event.keyCode == '13'){
            $("#btn_add_produto").click();
        }
    });
    $("#Adicionar_qtde").focus();
    $(".botao_jquery").button();

    $("#venda_btn_ver_estoque").click(function(){
        var Dialog = $("#jquery-dialog");
        Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Estoque');
        Dialog.html(HTMLLoading);
        Dialog.dialog({
            width: 600,
            height: 500,
            buttons:{Fechar: function(){$(this).dialog("close");}},
            open: function(){
                $.ajax({
                    url: "../estoque/ConsultaEstoque.php",
                    global: false,
                    type: "POST",
                    data: ({
                        id_estabelecimento:'<?php echo $Venda->getDadosVenda('id_estabelecimento');?>',
                        id_produto:'<?php echo $Produto->getNumregProduto();?>'

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
            close: function(){$(this).dialog("destroy")},
            modal: true,
            show: "fade",
            hide: "fade"
        });
    });

    $("#Adicionar_id_tab_preco,#Adicionar_id_unid_medida,#Adicionar_id_produto_embalagem").change(function(event){
        ReexibePreco();
    });
    <?php if(!$VendaParametro->getSnExibeFaixaPrecoComissao()){ ?>
    $("#Adicionar_id_tab_preco").change(function(event){
        VendaExibeTabelaFaixaPrecoComissao($(this).val(),<?php echo $Produto->getDadosProduto('numreg');?>);
    });
    <?php } ?>
    ReexibePreco();
    /*
     * Temporariamente desativado até que o BUG do tip ficar fixo na tela quando o elemento é destruído e não recebe mais onblur
    $('#Adicionar_qtde').qtip({content: 'Quantidade',style: 'blue',show:{when:{event:'focus'}},hide:{when:{event:'mouseleave keypress'}}});
    $('#Adicionar_vl_unitario').qtip({content: 'Valor Unitário',style: 'blue'});
    $('#Adicionar_id_unid_medida').qtip({content: 'Unidade de Medida',style: 'blue'});
    $('#Adicionar_id_referencia').qtip({content: 'Referência',style: 'blue'});
    $('#Adicionar_id_referencia').qtip({content: 'Referência',style: 'blue'});
    $("#Adicionar_id_produto_embalagem").qtip({content: 'Qtde por Unid. Medida',style: 'blue'});
    $("#Adicionar_id_tab_preco").qtip({content: 'Tabela de Preço / Moeda',style: 'blue'});
    */
    /* Imagem do Produto */
    $("#btn_imagem_produto").css({"cursor":"pointer","vertical-align":"bottom"}).qtip({
        content:{
            text: '<img src="<?php echo $UrlImagemProduto;?>" style="max-width: 500px; height: auto;" />',
            title: {text: 'Imagem do Produto',button: 'Fechar'}
        },
        style:{
            width: 'auto',
            height: 'auto'
        },
        position: {corner: {target: 'topMiddle',tooltip: 'bottomMiddle'},adjust: {screen: true}},
        show: {when: 'click',solo: true},
        hide: 'unfocus'
    }).qtip({content: $(this).attr("alt"),style: 'blue'});

    /* Aplicando a máscara de valor */
    VendaAplicaMascaraDecimal();
    <?php if($VendaParametro->getSnExibeFaixaPrecoComissao()) { ?>
    var IdTabPreco;
    <?php if($VendaParametro->getSnUsaTabPrecoPorItem()){ ?>
    IdTabPreco = $("#Adicionar_id_tab_preco").val();
    <?php } else { ?>
    IdTabPreco = '<?php echo $Venda->getIdTabPreco(); ?>';
    <?php } ?>
    VendaExibeTabelaFaixaPrecoComissao(IdTabPreco,<?php echo $Produto->getDadosProduto('numreg');?>);
    <?php } ?>
});
</script>