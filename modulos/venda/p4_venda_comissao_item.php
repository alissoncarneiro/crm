<?php
/*
 * p4_venda_comissao_item.php
 * Autor: Alex
 * 19/05/2011 09:20:43
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
$IndiceRepresentante = $_POST['indice_representante'];

/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */
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
    } else{
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
    }
    $Venda->pfuncao = $_POST['pfuncao'];
}

?>
<form id="form_comissao_representantexitem" name="form_comissao_representantexitem" action="#" onsubmit="return false;">
    <input type="hidden" name="ptp_venda" value="<?php echo $_POST['ptp_venda'];?>" />
    <input type="hidden" name="pnumreg" value="<?php echo $_POST['pnumreg'];?>" />
    <input type="hidden" name="indice_representante" value="<?php echo $IndiceRepresentante;?>" />
    <table width="100%" class="venda_tabela_itens">
        <tr bgcolor="#DAE8F4" class="tit_tabela">
            <td>#</td>
            <td>C&oacute;d. <br />
            <td>Descri&ccedil;&atilde;o</td>
            <td>% Comiss&atilde;o</td>
        </tr>
        <?php
        foreach($Venda->getItens() as $IndiceItem => $Item){
            $bg_color = ($i % 2 == 0)?'#EAEAEA':'#FFFFFF';
            $i++;
            $PctComissao = 0;
            $ItemComissao = $Item->getItemComissao($IndiceRepresentante);
            if(is_object($ItemComissao)){
                $PctComissao = $ItemComissao->getDadosItemComissao('pct_comissao');
            }
        ?>
            <tr bgcolor="<?php echo $bg_color;?>">
                <td><?php echo $Item->getDadosVendaItem('id_sequencia');?></td>
                <td><?php echo $Item->getCodProdutoERP();?></td>
                <td><?php echo $Item->getNomeProduto('nome_produto');?></td>
                <td><input type="text" name="pct_comissao_<?php echo $Item->getNumregItem();?>" class="venda_campo_comissao" value="<?php echo number_format_min($PctComissao,2,',','.');?>" /></td>
            </tr>
        <?php } ?>
    </table>
    <br />
    <strong>NOTA: Ao alterar os percentuais de comiss&atilde;o manualmente, o c&aacute;lculo autom&aacute;tico ser&aacute; desativado para esta venda.</strong>
</form>