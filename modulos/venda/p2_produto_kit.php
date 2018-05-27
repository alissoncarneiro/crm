<?php
/*
 * p2_produto_kit.php
 * Autor: Alex
 * 20/06/2011 13:14:15
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

$IdKIT = $_POST['id_kit'];

$QryProdutoKIT = query("SELECT * FROM is_kit_produto WHERE id_kit = '".$IdKIT."' ORDER BY ordem");
?>
<script language="javascript">
    $(document).ready(function(){
        $(".botao_jquery").button();
        
        $("#qtde_kit").keypress(function(event){
            if(event.keyCode == '13'){
                $("#btn_adicionar_kit").click();
            }
        }).focus();
        
        $("#btn_adicionar_kit").click(function(){
            var QtdeKIT = $("#qtde_kit").val();
            QtdeKIT = QtdeKIT.replace(/\,/, '', /g/);
            QtdeKIT = QtdeKIT.replace(/\,/, '.', /g/);
            if(QtdeKIT <= 0 || isNaN(QtdeKIT)){
                alert('Quantidade do KIT deve ser maior que 0.');
                return false;
            }
            if(!confirm('Todos os dados estão corretos ?')){
                return false;
            }
            var Data = $("#form_produtos_adicionar_kit").serialize();
            $.ajax({
                url: "p2_produto_kit_post.php",
                global: false,
                type: "POST",
                data: Data,
                dataType: "xml",
                async: true,
                beforeSend: function(){
                    //$("#p2_adiciona_kit_div_produtos").html(HTMLLoading);
                },
                error: function(){
                    alert('Erro com a requisição');
                },
                success: function(xml){
                    var resposta    = $(xml).find('resposta');
                    var status      = resposta.find('status').text();
                    var Acao        = resposta.find('acao').text();
                    if(status == 1){
                        if(Acao == 1){
                            exibe_tabela_item();
                            alert(resposta.find('mensagem').text());
                            $("#p2_adiciona_kit_div_produtos").html('Selecione um KIT.');
                        }
                    }
                    else if(status == 2){
                        alert(resposta.find('mensagem').text());
                    }
                }
            });
        });
    });
</script>
<form onsubmit="return false;" id="form_produtos_adicionar_kit">
<input type="hidden" name="id_kit" id="id_kit" value="<?php echo $IdKIT;?>"/>
<input type="hidden" name="ptp_venda" id="ptp_venda" value="<?php echo $_POST['ptp_venda'];?>"/>
<input type="hidden" name="pnumreg" id="pnumreg" value="<?php echo $_POST['pnumreg'];?>"/>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="venda_tabela_itens">
    <tr class="venda_titulo_tabela">
        <td width="10">&nbsp;</td>
        <td>C&oacute;d. Produto</td>
        <td>Descri&ccedil;&atilde;o</td>
        <td>Qtde.</td>
        <td>Vl. Unit.</td>
        <td>Unid. Medida</td>
        <?php if($Venda->getSnExibeEmbalagem()){ ?>
        <td>Embalagem</td>
        <?php } ?>
        <?php if($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem()){ ?>
        <td>Tab. Pre&ccedil;o</td>
        <?php } ?>
    </tr>
    <?php
    
    $ArTabPrecos = $Venda->getArTabPrecos();
    
    while($ArProdutoKIT = farray($QryProdutoKIT)){
        $i++;
        $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
        $IdProduto = $ArProdutoKIT['id_produto'];
        $Produto = new Produto($IdProduto);
        
        $Qtde = $ArProdutoKIT['qtde'];
        
        $ProdutoVlUnitario = 0;
        if(!$Venda->isPrecoInformado()){
            $PrecoProduto = new PrecoProduto($IdProduto,$Venda->getGrupoTabPreco(), $Venda->getIdTabPreco());
            $ProdutoVlUnitario = $PrecoProduto->getPreco();
        }
        
    ?>
    <tr bgcolor="<?php echo $bgcolor;?>" id="tr_<?php echo $IdProduto;?>">
        <td>
            <input type="checkbox" name="Adicionar_chk_item_kit_<?php echo $IdProduto;?>" id="Adicionar_chk_item_kit_<?php echo $IdProduto;?>" value="1" <?php echo (($ArProdutoKIT['sn_obrigatorio'] == '1')?' disabled="disabled" checked="checked"':'');?>/>
            <input type="hidden" name="Adicionar_id_item_kit_<?php echo $IdProduto;?>" id="Adicionar_id_item_kit_<?php echo $IdProduto;?>" value="<?php echo $ArProdutoKIT['numreg'];?>"/>
        </td>
        <td><?php echo $Produto->getDadosProduto('id_produto_erp');?></td>
        <td><?php echo $Produto->getDadosProduto('nome_produto');?></td>
        <td align="right">
        <?php if($ArProdutoKIT['sn_permite_alterar_qtde'] == '1'){ ?>
        <input type="text" size="10" class="quantidade venda_campo_qtde" CasasDecimais="<?php echo $Venda->getPrecisaoQtde();?>" name="Adicionar_qtde_<?php echo $IdProduto;?>" id="Adicionar_qtde_<?php echo $IdProduto;?>" value="<?php echo number_format_min($Qtde,0,',','.');?>"/></td>
        <?php } else { ?>
        <?php echo number_format_min($Qtde,0,',','.');?>
        <input type="hidden" name="Adicionar_qtde_<?php echo $IdProduto;?>" id="Adicionar_qtde_<?php echo $IdProduto;?>" value="<?php echo number_format_min($Qtde,0,',','.');?>"/></td>
        <?php } ?>
        <?php if($Venda->isPrecoInformado()){ ?>
        <td><input type="text" size="10" class="monetario venda_campo_vl_unitario" CasasDecimais="<?php echo $Venda->getPrecisaoValor();?>" name="Adicionar_vl_unitario_<?php echo $IdProduto;?>" id="Adicionar_vl_unitario_<?php echo $IdProduto;?>" value="<?php echo number_format_min($ProdutoVlUnitario,2,',','.');?>"/></td>
        <?php } else { ?>
        <td><?php echo number_format_min($ProdutoVlUnitario,2,',','.');?></td>
        <?php } ?>
        <td>
        <select id="Adicionar_id_unid_medida_<?php echo $IdProduto;?>" name="Adicionar_id_unid_medida_<?php echo $IdProduto;?>">
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
        <td><?php echo $Produto->MontaHTMLSelectEmbalagem('Adicionar_id_produto_embalagem_'.$IdProduto);?></td>
        <?php } ?>
        <?php if($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem()){?>
        <td>
        <select id="Adicionar_id_tab_preco_<?php echo $IdProduto;?>" name="Adicionar_id_tab_preco_<?php echo $IdProduto;?>">
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
    </tr>
<?php } ?>
</table>
<p>
<strong>Quantidade do KIT:</strong> <input type="text" class="venda_campo_qtde" name="qtde_kit" id="qtde_kit" />&nbsp;<input type="button" class="botao_jquery" id="btn_adicionar_kit" value="Adicionar KIT" />
</p>
</form>