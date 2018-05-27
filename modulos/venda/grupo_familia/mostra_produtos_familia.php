<?php
session_start();
header('Content-Type: text/html; charset=iso-8859-1');
$PrefixoIncludes = '../';
include('../includes.php');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>Multiplos Itens por pedido</title>
        <link href="../../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link href="../../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />

        <link href="../../../css/jquery.autocomplete.css" rel="stylesheet" type="text/css" />
        <link href="../../../css/jquery.dlg.css" rel="stylesheet" type="text/css" />
        <link href="../../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />

        <link href="../estilo_venda.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../js/jquery.qtip.js"></script>

        <script type="text/javascript" src="../../../js/jquery.dlg.min.js"></script>
        <script type="text/javascript" src="../../../js/jquery.easing.js"></script>

        <script type="text/javascript" src="../../../js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="../../../js/jquery.ui.datepicker-pt-BR.js"></script>

        <script type="text/javascript" src="../../../js/jquery.autocomplete.js"></script>

        <script type="text/javascript" src="../js/modal_det_pessoa.js"></script>

        <script type="text/javascript" src="../js/functions_venda.js"></script>

    </head>

    <body>
<?php
$id_familia = $_POST['id_familia'];
$numreg = $_POST['numreg'];
$tp_venda = $_POST['ptp_venda'];
$tab_preco = $_POST['tab_preco'];
$pfuncao = $_POST['pfuncao'];
$QtdeColunasTabelaItens = 0;

/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */
if($tp_venda  != 1 && $tp_venda  != 2){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
elseif(empty($numreg)){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
else{
    if($tp_venda == 1){
        $Venda = new Orcamento($tp_venda,$numreg);
    }
    else{
        $Venda = new Pedido($tp_venda,$numreg);
    }
    /*
     * Tratando os campos
     */
    $Venda->pfuncao = $pfuncao;
}

$VendaParametro = new VendaParametro();

if(isset($_POST['add_item'])){
    $ErrorInsereProduto = NULL;
    $ContaItens = 0;
    
    foreach($Venda->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
	$IdCampoDesconto = 'tabela_item_desc_'.$IndiceCampoDesconto;
                $NomeDesconto[] = $IdCampoDesconto;
                $Descontos[]    = $_POST[$IdCampoDesconto];
    }

    $quantidade = $_POST['qtde'];
    $id_unid_medida = $_POST['adicionar_id_unid_medida'];
    $id_referencia = $_POST['adicionar_id_referencia'];
    $id_moeda = $_POST['adicionar_id_moeda'];
    $qtde_por_unid_medida = $_POST['adicionar_qtde_por_unid_medida'];

    foreach($_POST['cod_produto'] as $k => $v){
        $qtde = trim($quantidade[$k]);
        $ArDados = array();
        $ArDadosItem = array();
        if(!empty($qtde)){
            $ContaItens++;
            $ArDados['pnumreg']                     = $numreg;
            $ArDados['ptp_venda']                   = $tp_venda;
            $ArDados['id_produto']                  = $v;
            $ArDados['qtde']                        = $quantidade[$k];
            $ArDados['id_moeda']                    = $id_moeda[$k];
            $ArDados['id_unid_medida']              = $id_unid_medida[$k];
            $ArDados['id_referencia']               = $id_referencia[$k];
            $ArDados['vl_unitario']                 = $vl_unitario[$k];
            $ArDados['qtde_por_unid_medida']        = $qtde_por_unid_medida[$k];

            $NumregItem = $Venda->AdicionaItemBD($ArDados['id_produto'],$ArDados);

            if($NumregItem !== false){

                $Item = $Venda->getItem($NumregItem);
                $ArDadosItem['tabela_item_qtde_'.$NumregItem] = $ArDados['qtde'];
                $ArDadosItem['tabela_item_id_unid_medida_'.$NumregItem] = $ArDados['id_unid_medida'];
                $ArDadosItem['tabela_item_obs_'.$NumregItem] = '';
                $ArDadosItem['tabela_item_id_cfop_'.$NumregItem] = $Item->getDadosVendaItem('id_cfop');
                $ArDadosItem['tabela_item_vl_unitario_'.$NumregItem] = $ArDados['vl_unitario'];
                foreach($Descontos as $desc => $desc_valor){
                    $IndiceCampoDesconto = str_replace('tabela_item_desc_','',$NomeDesconto[$desc]);
                    $vl_desc_item = trim($desc_valor[$k]);
                    $vl_desc_item = (empty($vl_desc_item))?0:$vl_desc_item;
                    $ArDadosItem['tabela_item_desc_'.$NumregItem.'_'.$IndiceCampoDesconto] = $vl_desc_item;
                }
                $Item->setDadosItemPOST($ArDadosItem);
                $AtualizaItem = $Item->AtualizaItemBD();
                //pre($ArDadosItem);exit;
                $Produto = new Produto($ArDados['id_produto']);
                
                if($AtualizaItem === false){//Se houve algum erro, informa ao usuário
                    $Venda->RemoveItem($NumregItem);
                    $ContaItens--;
                }
                    $Venda->CalculaTotaisVenda();
                    $Venda->AtualizaTotaisVendaBD();
            } else {
                $Produto = new Produto($ArDados['id_produto']);
                $ErrorInsereProduto .= '<strong style="color:#FF0000;">Produto</strong><i> '. $Produto->getDadosProduto('id_produto_erp') . ' - ' . $Produto->getDadosProduto('nome_produto') . '</i><br /><strong style="color:#FF0000;">Não foi inserido!</strong><hr>';
            }
        }
    }
    if(empty($ErrorInsereProduto) && $ContaItens > 1){ //se mais de 01 item foi inserido com sucesso e não existem itens com erro
        $ErrorInsereProduto = $ContaItens. '<strong>Itens foram inseridos com sucesso.</strong><hr>';
    } else if(empty($ErrorInsereProduto) && $ContaItens == 1){ //se 01 item foi inserido com sucesso e não existem itens com erro
        $ErrorInsereProduto = '<strong>Item foi inserido com sucesso.</strong><hr>';
    } else if(empty($ErrorInsereProduto)) { //se nenhum item foi inserido no sistema e não existem itens com erro
        $ErrorInsereProduto = '<strong>Nenhum item foi inserido.</strong><hr>';
    }
    ?>
    <script>
        $(document).ready(function(){
            $.dlg({
                title: 'Alerta - <?php echo ucwords(strsadds($Venda->getTituloVenda(false)));?>',
                content: '<?php echo $ErrorInsereProduto.$Venda->getMensagemAtualizacaoItens();?>',
                drag: true,
                focusButton :'ok',
                onComplete: function(){
                    window.opener.location.reload();
                    window.location.assign('<?php echo $_POST['url_retorno'];?>');
                }
            });
        });
    </script>
<?php
    exit;
}
?>
<?php
$sql_produto = 'SELECT numreg FROM is_produto WHERE id_familia_comercial = '.$id_familia;

$qry_produto = query($sql_produto);
$nrows_produto = numrows($qry_produto);
if($nrows_produto > 0){
    $contador = 1;
?>
<form method="POST" action="mostra_produtos_familia.php">
    <input type="hidden" name="url_retorno" id="url_retorno" value="<?php echo $_SERVER['HTTP_REFERER'];?>" />
<?php
foreach($_POST as $campo => $valor){
    echo '<input type="hidden" name="'.$campo.'" id="'.$campo.'" value="'.$valor.'">';
}
?>
<table width="auto" border="0" cellspacing="2" cellpadding="2" class="bordatabela">
  <tr bgcolor="#DAE8F4" >
    <td class="tit_tabela">Cód. Produto</td><?php $QtdeColunasTabelaItens++;?>
    <td class="tit_tabela">Produto</td><?php $QtdeColunasTabelaItens++;?>
    <td class="tit_tabela">Valor Unit&aacute;rio</td><?php $QtdeColunasTabelaItens++;?>
    <td class="tit_tabela">Qtde.</td><?php $QtdeColunasTabelaItens++;?>
    <td class="tit_tabela">Uni. Medida</td><?php $QtdeColunasTabelaItens++;?>
    <td class="tit_tabela">Referência</td><?php $QtdeColunasTabelaItens++;?>
    <?php if($Venda->getSnExibeEmbalagem()){ ?>
    <td class="tit_tabela">Qtde. por Unid. Medida</td><?php $QtdeColunasTabelaItens++;?>
    <?php } ?>
<?php
foreach($Venda->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){?>
    <td class="tit_tabela"><?php echo $CampoDesconto['nome_campo'];?></td><?php $QtdeColunasTabelaItens++;?>
<?php
}
?>
  </tr>
  <?php
  while ($ar_produto = farray($qry_produto)){
    $Produto = new Produto($ar_produto['numreg']);
    $Estoque = new ConsultaEstoqueCustom($VendaParametro);
    $Estoque->setIdProduto($Produto->getNumregProduto());
    $Estoque->setIdEstabelecimento($Venda->getDadosEstabelecimento('id_estabelecimento_erp'));
    $ArReferencia = $Estoque->getArReferencia();
    if((!$VendaParametro->getPermiteAdicionarItemSemPreco() && !$Produto->getVlUnitarioTabelaBD($tab_preco)) || (!$VendaParametro->getPermiteAdicionarItemSemReferencia() && count($ArReferencia) == 0)){
      continue;
    }
    $bgcolor = (($contador%2)==0)?' bgcolor="#EAEAEA"':' bgcolor="#FFFFFF"';
    if(!$VendaParametro->getSnPermiteAdicionarItemRepetido() && $Venda->VerificaSeExisteProduto($ar_produto['numreg'])){
        $bgcolor = ' bgcolor="#FFBABA"';
        $PermiteProdutoRepetido = false;
    } else {
        $bgcolor = ($Venda->VerificaSeExisteProduto($ar_produto['numreg']))?' bgcolor="#FFCC66"':$bgcolor;
        $PermiteProdutoRepetido = true;
    }
    $VlUnitario = number_format($Produto->getVlUnitarioTabelaBD($tab_preco),2,',','.');
  ?>
      <tr<?php echo $bgcolor;?>>
        <?php if(!$PermiteProdutoRepetido) {?>
        <td><?php echo $Produto->getDadosProduto('id_produto_erp');?></td>
        <td colspan="<?php echo ($QtdeColunasTabelaItens-1);?>"><?php echo $Produto->getDadosProduto('nome_produto');?></td>
        <?php } else {?>
        <td><input type="hidden" id="cod_produto" name="cod_produto[]" value="<?php echo $Produto->getNumregProduto();?>"><input type="hidden" name="adicionar_id_moeda[]" id="adicionar_id_moeda" value="1" /><?php echo $Produto->getDadosProduto('id_produto_erp');?></td>
        <td><?php echo $Produto->getDadosProduto('nome_produto');?></td>
        <td align="right"><input type="hidden" id="vl_unitario" name="vl_unitario[]" value="<?php echo $VlUnitario;?>"><?php echo $VlUnitario;?></td>
        <td align="right"><input type="text" name="qtde[]" id="qtde" class="venda_campo_qtde" /></td>
        <td><select id="adicionar_id_unid_medida" name="adicionar_id_unid_medida[]">
                    <?php
                    $QryUnidMedida = query("SELECT numreg, nome_unid_medida FROM is_unid_medida ORDER BY nome_unid_medida ASC");
                    $Options = '';
                    while($ArUnidMedida = farray($QryUnidMedida)){
                        if($ArUnidMedida['numreg'] == $Produto->getDadosProduto('id_unid_medida_padrao')){
                            $Options .= '<option value="'.$ArUnidMedida['numreg'].'" selected="selected">'.$ArUnidMedida['nome_unid_medida'].'</option>';
                        }
                    }
                    if($Options != ''){
                        echo $Options;
                    }
                    else{
                        echo '<option value="">&nbsp;</option>';
                    }
                    ?></select></td>
        <td><select id="adicionar_id_referencia" name="adicionar_id_referencia[]">
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
                    ?></select></td>
        <?php if($Venda->getSnExibeEmbalagem()){ ?>
        <td><?php echo $Produto->MontaHTMLSelectEmbalagem('adicionar_qtde_por_unid_medida[]');?></td>
        <?php } ?>
        <?php
        foreach($Venda->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
            $IdCampoDesconto = 'tabela_item_desc_'.$IndiceCampoDesconto;
        ?>
        <td align="right">
        <?php if(!$Venda->getDigitacaoCompleta() && $CampoDesconto['sn_editavel'] == 1){ /* Se a venda não estiver completa e o campo for editavel */?>
            <input type="text" size="3" id="<?php echo $IdCampoDesconto;?>" name="<?php echo $IdCampoDesconto;?>[]" class="venda_campo_desconto" value="" />%
        <?php } else { ?>
            <input type="hidden" id="<?php echo $IdCampoDesconto;?>" name="<?php echo $IdCampoDesconto;?>[]" class="venda_campo_desconto venda_campo_desconto<?php echo $CampoDesconto['numreg'];?>" value="0" />
        <?php }?>
        </td>
        <?php
        }
        }
        ?>
      </tr>
  <?php
    $contador++;
  }?>
  <tr>
      <td colspan="<?php echo $QtdeColunasTabelaItens;?>" align="center" bgcolor="#AEAEAE"><input style="border: 1px solid #365D7C; background-color: #C7E1F8; cursor: pointer; padding: 5px;" name="add_item" type="submit" id="add_item" value="Adicionar Itens"></td>
  </tr>
</table>
</form>
<?php
} else {
    echo 'Não existe produto cadastrado para esta família';
}
?>
    </body>
</html>
