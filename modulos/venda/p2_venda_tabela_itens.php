<?php
/*
 * p2_venda_tabela_itens.php
 * Autor: Alex
 * 05/11/2010 14:50
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html;  charset=ISO-8859-1");
session_start();
require('includes.php');

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
    $VisualizarRevisao = ($_POST['pvisualizar_revisao'] == '1')?true:false;
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg'],true,true,$VisualizarRevisao);
    }
    else{
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg'],true,true,$VisualizarRevisao);
    }
    /*
     * Tratando os campos
     */
    $Venda->pfuncao = $_POST['pfuncao'];
}
if(!$Venda->getDigitacaoCompleta()){
    $Venda->RecarregaValorUnitarioItensDB();
}

$VendaParametro = new VendaParametro();

$Usuario = new Usuario($_SESSION['id_usuario']);
$QtdeColunasTabelaItens = 0;

$ExibeCFOP = false;
$EditaCFOP = false;

if(!$Venda->getDigitacaoCompleta() && $VendaParametro->getPermiteAlterarCFOPItem() && $Usuario->getPermissao('sn_permite_alterar_cfop_item')){
    $ExibeCFOP = true;
    $EditaCFOP = true;
}
if($VendaParametro->getSnExibeCFOPItem()){
    $ExibeCFOP = true;
}

$ArrayNumregItens = array();
foreach($Venda->getItens() as $IndiceItem => $Item){
    $ArrayNumregItens[] = $Item->getNumregItem();
}

?>
<form name="form_tabela_itens" id="form_tabela_itens" action="p2_venda_atualiza_itens.php" method="post">
<input type="hidden" name="ptp_venda" id="ptp_venda" value="<?php echo $Venda->getTipoVenda();?>" />
<input type="hidden" name="pnumreg" id="pnumreg" value="<?php echo $Venda->getNumregVenda();?>" />
<input type="hidden" name="url_retorno" id="url_retorno" value="<?php echo curPageURL();?>" />
<input type="hidden" name="controle_item_alterado" id="controle_item_alterado" value="0" />
<?php
foreach($Venda->getItens() as $IndiceItem => $Item){
?>
<input type="hidden" name="controle_alteracao_<?php echo $Item->getNumregItem();?>" id="controle_alteracao_<?php echo $Item->getNumregItem();?>" value="0" />
<?php
}
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="venda_tabela_itens">
    <tr class="venda_titulo_tabela">
        <td>&nbsp;</td><?php $QtdeColunasTabelaItens++;?>
        <td>#</td><?php $QtdeColunasTabelaItens++;?>
        <td>C&oacute;d. <br />Produto</td><?php $QtdeColunasTabelaItens++;?>
        <td>Descri&ccedil;&atilde;o</td><?php $QtdeColunasTabelaItens++;?>
        <td>Unid. <br />Med.</td><?php $QtdeColunasTabelaItens++;?>
        <td>Qtde.</td><?php $QtdeColunasTabelaItens++;?>
        <?php if($VendaParametro->getSnExibeDescontoCapaNoItem()){ ?>
        <td>Desc. Cab.</td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <?php if($VendaParametro->getSnUsaDescTabPrecoItem()){ ?>
        <td><?php echo $VendaParametro->getNomeCampoDescTabPrecoItem();?></td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <?php
        foreach($Venda->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){?>
        <td>
            <?php echo $CampoDesconto['nome_campo'];?>
            <?php if(!$Venda->getDigitacaoCompleta() && $CampoDesconto['sn_editavel'] == 1){?>
            <img src="img/atualizar_pequeno_15.png" class="venda_btn_atualizar_todos_descontos" IdCampoDesconto="<?php echo $IndiceCampoDesconto;?>"/>
            <?php } ?>
        </td>
        <?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <?php if($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem()){ /* Se trabalha com tabela de preco por item(Moeda no item)*/ ?>
        <td>Moeda</td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <td>Vl. Unit.<br/>de Venda</td><?php $QtdeColunasTabelaItens++;?>
        <td>Vl. Unit.<br/>L&iacute;q.</td><?php $QtdeColunasTabelaItens++;?>
        <?php if($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem()){ /* Se trabalha com tabela de preco por item(Moeda no item)*/ ?>
        <td>Vl. Total<br/>Venda Liq.</td><?php $QtdeColunasTabelaItens++;?>
        <td>Cotação</td><?php $QtdeColunasTabelaItens++;?>
        <td>Cotação<br/>Fixa</td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <?php if($VendaParametro->getSnExibeIPI()){ ?>
        <td>% IPI</td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <?php if($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem()){ /* Se trabalha com tabela de preco por item(Moeda no item)*/ ?>
        <td>Vl. <br />Unit.</td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <td>Vl. Tot.<br/>c/ Desc.</td><?php $QtdeColunasTabelaItens++;?>
        <?php if($VendaParametro->getSnExibeValorSTItem()){ /* Se o parametro para exibir coluna de ST estiver ATIVO */?>
        <td>Vl. ST</td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <td>Situa&ccedil;&atilde;o</td><?php $QtdeColunasTabelaItens++;?>
        <?php if($Venda->getDigitacaoCompleta()){?>
        <td>Qtde. Faturada</td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <td>Obs</td><?php $QtdeColunasTabelaItens++;?>
        <?php if($VendaParametro->getSnAlterarDtEntPorItem()){?>
        <td>Dt. Entrega</td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <?php if($VendaParametro->getUsaPerdaItemOrcamento() && $Venda->getTipoVenda() == 1 && !$Venda->getDigitacaoCompleta()){ /* Se o parâmetro de uso de perda de item em orçamento esta como sim, se é um orçamento, e se ainda não esta com a digitacao completa */?>
        <td>Aprovado<br/>Cliente</td><?php $QtdeColunasTabelaItens++;?>
        <?php }?>
        <?php if($ExibeCFOP){ ?>
        <td>CFOP</td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <?php if($Venda->getEmAprovacao() || $Venda->getAvaliadoComercial()){ /* Se esta em aprovação ou se ja foi avaliado pelo comercial */ ?>
        <td>Status Comercial</td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <?php if(!$Venda->getDigitacaoCompleta()){ /* Se a venda não estiver completa */?>
        <td>Excluir</td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <?php if($Venda->Debug){?>
        <td>Log</td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
    </tr>
    <?php
    $i = 0;

    $ClassBotaoAprovarReprovarAlterar = 'tabela_item_aprovar_reprovar_alterar';

    foreach($Venda->getItens() as $IndiceItem => $Item){
        $i++;
        $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
        /*
         * Se o item está cancelado coloca o fundo de vermelho
         */
        $bgcolor                    = ($Item->getDadosVendaItem('id_situacao_item') == 6)?'#FFBABA':$bgcolor;
        $IdCampoQtde                = 'tabela_item_qtde_'.$Item->getNumregItem();
        $IdCampoIdMoeda             = 'tabela_item_id_moeda_'.$Item->getNumregItem();
        $IdCampoIdUnidMedida        = 'tabela_item_id_unid_medida_'.$Item->getNumregItem();
        $IdCampoVlUnitario          = 'tabela_item_vl_unitario_'.$Item->getNumregItem();
        $IdCheckboxCotacaoFixa      = 'tabela_item_chk_cotacao_fixa_'.$Item->getNumregItem();
        $IdCampoObs                 = 'tabela_item_obs_'.$Item->getNumregItem();
        $IdCampoDtEntrega           = 'tabela_item_dt_entrega_'.$Item->getNumregItem();
        $IdCampoDescontoTabPreco    = 'tabela_item_pct_desconto_tab_preco_'.$Item->getNumregItem();

        $IdCampoIdCFOP              = 'tabela_item_id_cfop_'.$Item->getNumregItem();

        /* Calculando o percentual máximo de desconto de tab de preço */
        if($VendaParametro->getSnUsaDescTabPrecoItem()){
            $RetornoValidacao = $Item->ValidaPoliticaComercialDescVendaItemCampoDescontoFixoTabPreco();
            $StringPctMaxDescTabPreco = '<span class="venda_span_desc_max_permitido" title="Desconto máximo permitido">';
            $StringPctMaxDescTabPreco .= number_format_min($Item->getPoliticaComercialDescVendaItemCampoDescontoFixoTabPreco()->getPctMaxCampoDescontoItem(),0,',','.');
            $StringPctMaxDescTabPreco .= '</span>';
        }
        $ArrayCampos = array();
        $ArrayCampos['qtde']['editavel'] = 1;

    ?>
    <tr id="tabela_item_tr_<?php echo $Item->getNumregItem();?>" bgcolor="<?php echo $bgcolor;?>">
        <td>&nbsp;<input type="hidden" id="<?php echo $IdCampoIdMoeda;?>" name="<?php echo $IdCampoIdMoeda;?>" value="1" />
        <?php if(!$Item->getItemComercial()){ ?>
        <img src="img/ajuda_pequeno.png" class="venda_btn_info_item_nao_comercial" tooltip="Item n&atilde;o comercial">
        <?php } ?>
        </td>
        <td><?php echo $Item->getDadosVendaItem('id_sequencia');?></td>
        <td><?php echo $Item->getCodProdutoERP();?></td>
        <td><?php echo $Item->getNomeProduto();?></td>
        <td>
        <?php if($Venda->getDigitacaoCompleta() || 1==1){ //Se a venda já estiver completa FIXO TEXTO
            if($Venda->isAtacado()){
                echo 'CX';
            }
            elseif($Item->getDadosVendaItem('id_unid_medida') != ''){
                $QryUnidMedida = query("SELECT numreg, nome_unid_medida FROM is_unid_medida WHERE numreg = ".$Item->getDadosVendaItem('id_unid_medida'));
                $ArUnidMedida = farray($QryUnidMedida);
                echo $ArUnidMedida['nome_unid_medida'];
            }
            echo '<input type="hidden" id="'.$IdCampoIdUnidMedida.'" name="'.$IdCampoIdUnidMedida.'" value="'.$Item->getDadosVendaItem('id_unid_medida').'" />';
        }
        else{ ?>
        <select id="<?php echo $IdCampoIdUnidMedida;?>" name="<?php echo $IdCampoIdUnidMedida;?>">
        <?php
        $QryUnidMedida = query("SELECT numreg, nome_unid_medida FROM is_unid_medida ORDER BY nome_unid_medida ASC");
        while($ArUnidMedida = farray($QryUnidMedida)){
            if($ArUnidMedida['numreg'] == $Item->getDadosVendaItem('id_unid_medida')){
                echo '<option value="'.$ArUnidMedida['numreg'].'" selected="selected">'.$ArUnidMedida['nome_unid_medida'].'</option>';
            }
        }
        ?></select>
        <?php } ?>
        </td>
        <td align="right">
        <?php if(!$Venda->getDigitacaoCompleta()){ /* Se a venda não estiver completa */?>
        <input type="text" id="<?php echo $IdCampoQtde;?>" name="<?php echo $IdCampoQtde;?>" class="quantidade venda_campo_qtde" CasasDecimais="<?php echo $Venda->getPrecisaoQtde();?>" value="<?php echo $Venda->NFQ($Item->getDadosVendaItem('qtde'));?>" />
        <?php } else { echo $Venda->NFQ($Item->getDadosVendaItem('qtde')); }?>
        <?php if($Venda->getSnExibeQtdePorUnidMedida()){?>x<span class="venda_qtde_por_unid_medida"><?php echo $Item->getDadosVendaItem('qtde_por_qtde_informada')*$Item->getDadosVendaItem('qtde_por_unid_medida');?></span>=<?php echo number_format_min($Item->getDadosVendaItem('total_unidades'),0,',','.'); } ?>
        </td>
        <?php if($VendaParametro->getSnExibeDescontoCapaNoItem()){ ?>
        <td align="right">
        <?php echo number_format_min($Venda->getPctDescontoTabPreco(),0,',','.'),' + ',number_format_min($Venda->getPctDescontoPessoa(),0,',','.'),' + ',number_format_min($Venda->getPctDescontoInformado(),0,',','.'); ?>
        </td>
        <?php } ?><?php if($VendaParametro->getSnUsaDescTabPrecoItem()){ ?>
        <td align="right">
        <?php if($VendaParametro->getSnExibePctMaxDescItem() && ($Venda->getEmAprovacao() || !$Venda->getDigitacaoCompleta())){ echo $StringPctMaxDescTabPreco; } ?>
        <?php if(!$Venda->getDigitacaoCompleta()){ ?>
            <input type="text" id="<?php echo $IdCampoDescontoTabPreco;?>" <?php $CallBackCustomCampoDescTabPreco = VendaCallBackCustom::ExecutaVenda($Venda, 'Passo2CampoDescTabPreco',''); if($CallBackCustomCampoDescTabPreco !== true){ echo $CallBackCustomCampoDescTabPreco; }?> name="<?php echo $IdCampoDescontoTabPreco;?>" class="venda_campo_desconto" value="<?php echo $Venda->NFD($Item->getDadosVendaItem('pct_desconto_tab_preco'),$Venda->getPrecisaoDesconto());?>" />%
        <?php } else { ?>
        <input type="hidden" id="<?php echo $IdCampoDescontoTabPreco;?>" name="<?php echo $IdCampoDescontoTabPreco;?>" value="<?php echo $Venda->NFD($Item->getDadosVendaItem('pct_desconto_tab_preco'),$Venda->getPrecisaoDesconto());?>" />
        <?php echo $Venda->NFD($Item->getDadosVendaItem('pct_desconto_tab_preco'),$Venda->getPrecisaoDesconto());?>%
        <?php } ?>
        </td>
        <?php } ?>
        <?php
        /* Campos de desconto */
        foreach($Venda->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
            $IdCampoDesconto = 'tabela_item_desc_'.$Item->getNumregItem().'_'.$IndiceCampoDesconto;
        ?>
        <td align="right">
        <?php if(!$Venda->getDigitacaoCompleta() && $CampoDesconto['sn_editavel'] == 1){ /* Se a venda não estiver completa e o campo for editavel */?>
        <input type="text" id="<?php echo $IdCampoDesconto;?>" name="<?php echo $IdCampoDesconto;?>" class="venda_campo_desconto venda_campo_desconto<?php echo $CampoDesconto['numreg'];?>" value="<?php echo $Venda->NFD($Item->getDescontoItem($IndiceCampoDesconto),$Venda->getVendaDescontoPrecisao($IndiceCampoDesconto));?>" />%
        <?php if($VendaParametro->getSnUsaCalcDescontoItem()){ ?>
        <img src="img/calculadora.png" IdCampoDesconto="<?php echo $IdCampoDesconto;?>" PctDescontoAtual="<?php echo $Item->getDescontoItem($IndiceCampoDesconto);?>" NumregItem="<?php echo $Item->getNumregItem();?>" VlTotalLiquido="<?php echo $Item->getDadosVendaItem('vl_total_liquido');?>" class="venda_btn_calcula_pct_desconto" />
        <?php } ?>
        <?php } else { ?>
        <input type="hidden" id="<?php echo $IdCampoDesconto;?>" name="<?php echo $IdCampoDesconto;?>" class="venda_campo_desconto venda_campo_desconto<?php echo $CampoDesconto['numreg'];?>" value="<?php echo $Venda->NFD($Item->getDescontoItem($IndiceCampoDesconto),$Venda->getVendaDescontoPrecisao($IndiceCampoDesconto));?>" />
        <?php echo $Venda->NFD($Item->getDescontoItem($IndiceCampoDesconto),$Venda->getVendaDescontoPrecisao($IndiceCampoDesconto)).'%';}?>
        </td>
        <?php } ?>
        <?php if($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem()){ /* Se trabalha com tabela de preco por item(Moeda no item)*/ ?>
        <td><?php echo $Venda->getDadosMoeda($Item->getDadosTabPreco('id_moeda'),'sigla');?></td>
        <?php } ?>
        <td align="right"><?php if((!$Venda->getDigitacaoCompleta() && $Usuario->getPermissao('sn_permite_alterar_preco_venda') && $Venda->isPrecoInformado()) || (!$Venda->getDigitacaoCompleta() && !$Item->getItemComercial())){?>
            <input type="text" id="<?php echo $IdCampoVlUnitario;?>" name="<?php echo $IdCampoVlUnitario;?>" class="venda_campo_vl_unitario" value="<?php echo $Venda->NFV($Item->getVlUnitarioBaseCalculo());?>" />
            <?php } else { ?>
            <input type="hidden" id="<?php echo $IdCampoVlUnitario;?>" name="<?php echo $IdCampoVlUnitario;?>" value="<?php echo $Venda->NFV($Item->getVlUnitarioBaseCalculo());?>" />
            <?php echo $Venda->NFV($Item->getVlUnitarioBaseCalculo());?>
        <?php } ?>
        </td>
        <td align="right"><?php echo number_format_min($Item->getVlUnitarioComDescontos(),2,',','.');?></td>
        <?php if($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem()){ /* Se trabalha com tabela de preco por item(Moeda no item)*/ ?>
        <td align="right"><?php echo $Venda->NFV($Item->getDadosVendaItem('vl_total_liquido_base_calculo'),2);?></td>
        <td align="right"><?php echo number_format_min($Item->getDadosVendaItem('vl_cotacao'),2);?></td>
        <td align="center"><input type="checkbox" id="<?php echo $IdCheckboxCotacaoFixa;?>" name="<?php echo $IdCheckboxCotacaoFixa;?>" value="1" <?php echo (($Item->getDadosVendaItem('sn_cotacao_fixa') == '1')?' checked="checked"':'');?> <?php echo (($Venda->getDigitacaoCompleta())?' disabled="disabled"':'');?>></td>
        <?php } ?>
        <?php if($VendaParametro->getSnExibeIPI()){ ?>
        <td align="right"><?php echo number_format_min($Item->getDadosVendaItem('pct_aliquota_ipi'),',','.');?></td>
        <?php } ?>
        <?php if($Venda->getVendaParametro()->getSnUsaTabPrecoPorItem()){ /* Se trabalha com tabela de preco por item(Moeda no item)*/ ?>
        <td>R$ <?php echo $Venda->NFV($Item->getVlUnitarioConvertido());?></td>
        <?php } ?>
        <td align="right">R$ <?php echo $Venda->NFV($Item->getDadosVendaItem('vl_total_liquido'));?></td>
        <?php if($VendaParametro->getSnExibeValorSTItem()){ /* Se o parametro para exibir coluna de ST estiver ATIVO */?>
        <td align="right">R$ <?php echo $Venda->NFV($Item->getDadosVendaItem('vl_total_st'));?></td>
        <?php } ?>
        <td><?php $QrySituacaoItem = query("SELECT nome_situacao_item FROM is_situacao_item WHERE numreg = ".$Item->getDadosVendaItem('id_situacao_item'));
                  $ArSituacaoItem = farray($QrySituacaoItem);
                  echo $ArSituacaoItem['nome_situacao_item'];?></td>
        <?php if($Venda->getDigitacaoCompleta()){?>
        <td align="right"><?php echo $Venda->NFQ($Item->getDadosVendaItem('qtde_faturada'));?></td>
        <?php } ?>
        <td>
        <?php if(!$Venda->getDigitacaoCompleta()){ /* Se a venda não estiver completa */?>
        <input type="text" id="<?php echo $IdCampoObs;?>" name="<?php echo $IdCampoObs;?>" class="venda_campo_obs" value="<?php echo strsadds($Item->getDadosVendaItem('obs'));?>" />
        <?php } else { echo $Item->getDadosVendaItem('obs'); }?>
        </td>
        <?php if($VendaParametro->getSnAlterarDtEntPorItem()){?>
        <td>
        <?php if(!$Venda->getDigitacaoCompleta()){?>
        <input type="text" name="<?php echo $IdCampoDtEntrega;?>" id="<?php echo $IdCampoDtEntrega;?>" maxlength="10" readOnly="readOnly" class="venda_campo_data" value=""/>
        <script language="JavaScript">
        $(document).ready(function(){
            $("#<?php echo $IdCampoDtEntrega;?>").datepicker({
                showOn: "button",
                buttonImage: "../../images/agenda.gif",
                buttonImageOnly: true,
                changeMonth:true,
                changeYear:true,
                minDate: +<?php echo DiferencaEntreDatas(date("Y-m-d"),$Venda->getDadosVenda('dt_entrega'));?>,
                onSelect: function(dateText, inst) { p2_venda_marca_como_alterado(<?php echo $Item->getNumregItem(); ?>) }
            });
            $("#<?php echo $IdCampoDtEntrega;?>").val("<?php echo uB::DataEn2Br($Item->getDadosVendaItem('dt_entrega'),false);?>");
        });
        </script>
        <?php } else { ?>
        <?php echo uB::DataEn2Br($Item->getDadosVendaItem('dt_entrega'),false);?>
        <?php } ?>
        </td><?php $QtdeColunasTabelaItens++;?>
        <?php } ?>
        <?php if($VendaParametro->getUsaPerdaItemOrcamento() && $Venda->getTipoVenda() == 1 && !$Venda->getDigitacaoCompleta()){?>
        <td align="center">
        <?php if(strsadds($Item->getDadosVendaItem('sn_item_perdido')) == 1){?>
            <img src="img/rejeitar_sit_item.png" alt="Aprovar item do orçamento" class="venda_btn_ganha_item" NumregItem="<?php echo $Item->getNumregItem();?>" />
        <?php } else { ?>
            <img src="img/aprovar_sit_item.png" alt="Aprovar item do orçamento" class="venda_btn_perde_item" NumregItem="<?php echo $Item->getNumregItem();?>" />
        <?php } ?>
        </td>
        <?php }?>
        <?php if($ExibeCFOP){ ?>
        <td>
        <?php
        $IdCFOP = $Item->getDadosVendaItem('id_cfop');
        if(!$Item->getItemComercial()){
            echo '&nbsp;';
        }
        elseif($Venda->getDigitacaoCompleta()){
            echo $Item->getIdCFOPErp();
        }
        elseif(!$EditaCFOP){
            echo $Item->getIdCFOPErp();
            echo '<input type="hidden" id="'.$IdCampoIdCFOP.'" name="'.$IdCampoIdCFOP.'" value="'.$Item->getDadosVendaItem('id_cfop').'" />';
        }
        else{ ?>
            <select id="<?php echo $IdCampoIdCFOP;?>" name="<?php echo $IdCampoIdCFOP;?>">
                <option value="">&nbsp;</option>
            <?php
            $QryCfop = query("SELECT numreg, id_cfop_erp FROM is_cfop ORDER BY nome_cfop ASC");
            while($ArCfop = farray($QryCfop)){
                $Selected = ($ArCfop['numreg'] == $Item->getDadosVendaItem('id_cfop'))?' selected="selected"':'';
                echo '<option value="'.$ArCfop['numreg'].'"'.$Selected.'>'.$ArCfop['id_cfop_erp'].'</option>';
            }
            ?></select>
        <?php } ?>
        </td>
        <?php } ?>
        <?php if($Venda->getEmAprovacao() || $Venda->getAvaliadoComercial()){ /* Se esta em aprovação ou se ja foi avaliado pelo comercial */ ?>
        <td align=left">
            <?php if($Item->getSnReprovadoComercial()){ /* Se o item está reprovado */ ?>
            <img src="img/reprovar_pequeno.png" class="aprovar_reprovar_justificativa_info" tooltip="<?php echo htmlentities(trim($Item->getDadosVendaItem('justificativa_reprov_com')));?>">
            <?php } elseif(!$Item->getSnReprovadoComercial()){ /* Se o item está aprovado */?>
            <img src="img/aprovar_pequeno.png" class="aprovar_reprovar_justificativa_info" tooltip="<?php echo htmlentities(trim($Item->getDadosVendaItem('justificativa_reprov_com')));?>">
            <?php } ?>
            <?php if(!$Venda->getAprovadoComercial() && (($Usuario->getPermissao('sn_permite_aprovar_venda') && $Item->getSnReprovadoComercial()) || ($Usuario->getPermissao('sn_permite_reprovar_venda') && !$Item->getSnReprovadoComercial()))){ /* Se o usuário tem permissão para aprovar ou reprovar um item */?>
            <img src="img/editar_pequeno.png" alt="Alterar" title="Alterar" class="<?php echo $ClassBotaoAprovarReprovarAlterar;?>" NumregItem="<?php echo $Item->getNumregItem();?>">
            <?php } ?>
        </td>
        <?php } ?>
        <?php if(!$Venda->getDigitacaoCompleta()){ /* Se a venda não estiver completa */?>
        <td align="center"><img src="img/btn_apagar.png" alt="Excluir Item" title="Excluir Item" class="venda_btn_rem_produto" NumregItem="<?php echo $Item->getNumregItem();?>"></td>
        <?php } ?>
        <?php if($Venda->Debug){?>
        <td><img src="img/btn_log_pequeno.png" width="15" height="15" alt="Exibir Log do Item" title="Exibir Log do Item" class="venda_btn_log_item" NumregItem="<?php echo $Item->getNumregItem();?>"></td>
        <?php } ?>
    </tr>
    <?php
    }
    ?>
    <tr id="tr_total_sem_ipi">
        <td colspan="<?php echo $QtdeColunasTabelaItens;?>" bgcolor="#CCCCCC" align="right">Total <?php echo (($VendaParametro->getSnExibeIPI())?'sem IPI ':'');?><strong>R$ <?php echo $Venda->NFV($Venda->getVlTotalVendaLiquido());?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    <?php if($VendaParametro->getSnExibeIPI()){ ?>
    <tr id="tr_total_com_ipi">
        <td colspan="<?php echo $QtdeColunasTabelaItens;?>" bgcolor="#CCCCCC" align="right">Total com IPI <strong>R$ <?php echo $Venda->NFV($Venda->getVlTotalVendaLiquido()+$Venda->getVlTotalVendaIPI());?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    <?php } ?>
    <?php if($VendaParametro->getSnExibeST()){ ?>
    <tr id="tr_total_com_ipi_e_st">
        <td colspan="<?php echo $QtdeColunasTabelaItens;?>" bgcolor="#CCCCCC" align="right">Total com IPI e ST <strong>R$ <?php echo $Venda->NFV($Venda->getVlTotalVendaLiquido()+$Venda->getVlTotalVendaIPI()+$Venda->getVlTotalVendaST());?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    <?php } ?>
    <?php echo $Venda->VendaCustomizacoes->getHtmlLinhaRodapeItens($QtdeColunasTabelaItens); ?>
</table>
</form>
<?php if($VendaParametro->getSnUsaCalcDescontoItem()){ ?>
<div id="jquery-dialog-calculadora-pct-desconto" style="display: none;">
    <strong>Valor Total L&iacute;quido Base C&aacute;lculo: </strong><span id="span_calc_pct_desconto_valor_base" style="font-size:14px; color:#FF0000;"></span><br/>
    <strong>Valor Desconto &agrave; Aplicar: </strong><input type="text" name="calc_pct_desconto_vl_desconto" id="calc_pct_desconto_vl_desconto" class="venda_campo_vl_unitario" /><br/>
    <strong>Nota: </strong>Devido ao arredondamento, nem sempre o valor de desconto ser&aacute; preciso.
</div>
<?php } ?>
<script>
$(document).ready(function(){
    var ArrayNumregItens = [<?php echo implode(',',$ArrayNumregItens);?>];
    $('.aprovar_reprovar_justificativa_info').each(function(){
        if($(this).attr('tooltip') != ''){
            $(this).qtip({
                content: $(this).attr('tooltip'), // Use the tooltip attribute of the element for the content
                position: {
                    corner: {
                    tooltip: 'rightMiddle', // Use the corner...
                    target: 'leftMiddle' // ...and opposite corner
                }
                },
                style: 'red' // Give it a crea mstyle to make it stand out
            });
        }
    });
    $('.venda_btn_info_item_nao_comercial').each(function(){
        if($(this).attr('tooltip') != ''){
            $(this).qtip({
                content: $(this).attr('tooltip'), // Use the tooltip attribute of the element for the content
                position: {
                    corner: {
                    tooltip: 'leftMiddle', // Use the corner...
                    target: 'rightMiddle' // ...and opposite corner
                }
                },
                style: 'blue' // Give it a crea mstyle to make it stand out
            });
        }
    });

    $("#url_retorno").val(window.location);
    $(".venda_btn_rem_produto").click(function(){
        var NumregItem = $(this).attr("NumregItem");
        $("#jquery-dialog").attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Alerta');
        $("#jquery-dialog").html('Esta ação não pode ser desfeita. Você confirma a exclusão do item ?');
        $("#jquery-dialog").dialog({
            buttons:{
                "Confirmar": function(){
                $.ajax({
                    url: "p2_remove_item.php",
                    global: false,
                    type: "POST",
                    data: ({
                        pnumreg: $("#pnumreg").val(),
                        ptp_venda: $("#ptp_venda").val(),
                        NumregItem: NumregItem

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
                        exibe_tabela_item();
                    }
                });
                $(this).dialog("close");
            },
            Cancelar: function(){$(this).dialog("close");}},
            close: function(){$(this).dialog("destroy");},
            modal: true,
            show: "fade",
            hide: "fade"
        });
    });
    $(".<?php echo $ClassBotaoAprovarReprovarAlterar;?>").click(function(){
        var NumregItem = $(this).attr("NumregItem");
        $("#jquery-dialog").attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Aprovar/Reprovar Item');
        $("#jquery-dialog").html('Status: <br /><select id="status_aprovacao_item"><option value="0">Aprovar</option><option value="1">Reprovar</option></select><br />Justificativa:<br /><textarea id="justificativa_aprovacao_item" cols="40" rows="3"></textarea>');
        $("#jquery-dialog").dialog({
            buttons:{
                "Confirmar": function(){
                if(($("#status_aprovacao_item").val() == 0) || ($("#status_aprovacao_item").val() == 1 && $("#justificativa_aprovacao_item").val() != '')){
                    $.ajax({
                        url: "p2_aprovar_reprovar_item.php",
                        global: false,
                        type: "POST",
                        data: ({
                            pnumreg: $("#pnumreg").val(),
                            ptp_venda: $("#ptp_venda").val(),
                            NumregItem: NumregItem,
                            status_aprovacao_item: $("#status_aprovacao_item").val(),
                            justificativa_aprovacao_item: escape($("#justificativa_aprovacao_item").val())

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
                            exibe_tabela_item();
                        }
                    });
                    $(this).dialog("close");
                }
                else{
                    alert('Justificativa é obrigatória para reprovar o item!');
                    return false;
                }
            },
            Cancelar: function(){$(this).dialog("close");}},
            close: function(){$(this).dialog("destroy");},
            modal: true,
            show: "fade",
            hide: "fade"
        });

    }).css("cursor","pointer");
    $(".venda_btn_perde_item").click(function(){
        var NumregItem = $(this).attr("NumregItem");
        var status_aprovacao_item = '1';
        $("#jquery-dialog").attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Perder Item');
        $("#jquery-dialog").html('Motivo da perda: <br /><?php echo TabelaParaCombobox('is_opor_motivo', 'numreg', 'nome_opor_motivo', 'motivo_perda','','',' ORDER BY nome_opor_motivo');?><br />Nome do Concorrente:<br /><?php echo TabelaParaCombobox('is_concorrente', 'numreg', 'razao_social_nome', 'nome_concorrente','','',' ORDER BY razao_social_nome');?><br />Valor concorrente:<br /><input type="text" id="vl_concorrente" /><br />Observa&ccedil;&otilde;es gerais:<br /><textarea id="obs_geral" rows="5" cols="60"></textarea>');
        $("#jquery-dialog").dialog({
            width: 360,
            height: 300,
            buttons:{
                "Confirmar": function(){
                if(($("#motivo_perda").val() != '') || ($("#status_aprovacao_item").val() == 1 && $("#justificativa_aprovacao_item").val() != '')){
                    $.ajax({
                        url: "p2_item_reprovado.php",
                        global: false,
                        type: "POST",
                        data: ({
                            pnumreg: $("#pnumreg").val(),
                            ptp_venda: $("#ptp_venda").val(),
                            NumregItem: NumregItem,
                            status_aprovacao_item: status_aprovacao_item,
                            motivo_perda: $("#motivo_perda").val(),
                            nome_concorrente: $("#nome_concorrente").val(),
                            vl_concorrente: $("#vl_concorrente").val(),
                            obs_geral: escape($("#obs_geral").val())

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
                            exibe_tabela_item();
                        }
                    });
                    $(this).dialog("close");
                }
                else{
                    alert('Motivo da perda é obrigatório para reprovar o item!');
                    return false;
                }
            },
            Cancelar: function(){$(this).dialog("close");}},
            close: function(){$(this).dialog("destroy");},
            modal: true,
            show: "fade",
            hide: "fade"
        });

    }).css("cursor","pointer");

    $(".venda_btn_ganha_item").click(function(){
        var NumregItem = $(this).attr("NumregItem");
        var status_aprovacao_item = '0';
        $.ajax({
            url: "p2_item_reprovado.php",
            global: false,
            type: "POST",
            data: ({
                pnumreg: $("#pnumreg").val(),
                ptp_venda: $("#ptp_venda").val(),
                NumregItem: NumregItem,
                status_aprovacao_item: status_aprovacao_item,
                motivo_perda: '',
                nome_concorrente: '',
                vl_concorrente: '',
                obs_geral: ''

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
                exibe_tabela_item();
            }
        });
    }).css("cursor","pointer");

    $('.venda_btn_atualizar_todos_descontos[IdCampoDesconto]').each(function(){
        var IdCampoDesconto = $(this).attr("IdCampoDesconto");
        var qtip = $(this).qtip({
            content: '<input type="text" class="venda_campo_desconto" id="venda_campo_desconto_aplicar_todos' + IdCampoDesconto + '" /><img src="img/editar_pequeno_16.png" IdCampoDesconto="' + IdCampoDesconto + '" width="15" height="15" id="btn_venda_aplicar_todos_descontos' + IdCampoDesconto + '"/>',
            style:'blue',
            show: { when: { event: 'click' } },
            hide:{
                fixed:true,
                delay:2000
            },
            api:{
                onRender: function(){
                    $("#btn_venda_aplicar_todos_descontos" + IdCampoDesconto).click(function(){
                        var IdCampo = ".venda_campo_desconto" + IdCampoDesconto;
                        var NovoValor = $("#venda_campo_desconto_aplicar_todos" + IdCampoDesconto).val();
                        $(IdCampo).each(function(){
                           $(this).val(NovoValor);
                        });
                        qtip.qtip("hide");
                        for(i=0;i<ArrayNumregItens.length;i++){
                            p2_venda_marca_como_alterado(ArrayNumregItens[i]);
                        }
                    }).css("cursor","pointer");

                    $("#venda_campo_desconto_aplicar_todos" + IdCampoDesconto).keypress(function(event){
                        if(event.keyCode == '13'){
                            var IdCampo = ".venda_campo_desconto" + IdCampoDesconto;
                            var NovoValor = $("#venda_campo_desconto_aplicar_todos" + IdCampoDesconto).val();
                            $(IdCampo).each(function(){
                               $(this).val(NovoValor);
                            });
                            qtip.qtip("hide");
                            for (i=0;i<ArrayNumregItens.length;i++){
                                p2_venda_marca_como_alterado(ArrayNumregItens[i]);
                            }
                        }
                    });
                },
                onShow: function(){
                    $("#venda_campo_desconto_aplicar_todos" + IdCampoDesconto).focus();
                }
            }
        }).css("cursor","pointer");
    });
    <?php if($VendaParametro->getSnUsaCalcDescontoItem()){ ?>
    $('.venda_btn_calcula_pct_desconto').each(function(){
        $(this).click(function(){
            var NumregItem = $(this).attr("NumregItem");
            var IdCampoDesconto = $(this).attr("IdCampoDesconto");
            var VlTotalItem = parseFloat($(this).attr("VlTotalLiquido"));
            var PctDescontoAtual = parseFloat($(this).attr("PctDescontoAtual"));
            var Dialog = $("#jquery-dialog-calculadora-pct-desconto");
            var Precisao = <?php $PrecisaoDesconto = getParametrosVenda('precisao_desconto'); echo (($PrecisaoDesconto=='')?0:$PrecisaoDesconto)?>;
            if(PctDescontoAtual > 0){
                VlTotalItem = (VlTotalItem / (1 - (PctDescontoAtual / 100)));
                VlTotalItem = Math.round( Math.round( VlTotalItem * Math.pow( 10, Precisao + 1 ) ) / Math.pow( 10, 1 ) ) / Math.pow(10,Precisao);
            }
            $("#span_calc_pct_desconto_valor_base").html(float2real(VlTotalItem));
            Dialog.attr("title","Calculadora Desconto por Valor")
            Dialog.dialog({
                buttons:{
                "Confirmar": function(){
                    var VlDesconto = real2float($("#calc_pct_desconto_vl_desconto").val());
                    var PctDescontoCalculado = (VlDesconto / VlTotalItem) * 100;
                    var PctDescontoArredondado = Math.round( Math.round( PctDescontoCalculado * Math.pow( 10, Precisao + 1 ) ) / Math.pow( 10, 1 ) ) / Math.pow(10,Precisao);
                    var PctDescontoFinal = PctDescontoArredondado;
                    $("#"+IdCampoDesconto).val(float2real(PctDescontoFinal));
                    p2_venda_marca_como_alterado(NumregItem);
                    $(this).dialog("close");
                },
                Cancelar: function(){$(this).dialog("close");}},
                close: function(){$(this).dialog("destroy");},
                modal: true,
                show: "fade",
                hide: "fade"
            });
            $("#calc_pct_desconto_vl_desconto").focus();
        });
        $(this).css("cursor","pointer");
    });
    <?php } ?>
    $(".venda_btn_log_item").click(function(){
        var NumregItem = $(this).attr("NumregItem");
        $.ajax({
            url: "log_calculo_item.php",
            global: false,
            type: "POST",
            data: ({
                pnumreg: $("#pnumreg").val(),
                ptp_venda: $("#ptp_venda").val(),
                NumregItem: NumregItem
            }),
            dataType: "html",
            async: true,
            beforeSend: function(){

            },
            error: function(){
                alert('Erro com a requisição');
            },
            success: function(responseText){
                $("#jquery-dialog").attr("title",'LOG');
                $("#jquery-dialog").html(responseText);
                $("#jquery-dialog").dialog({
                    width: 500,
                    height: 500,
                    buttons:{
                        "Reload": function(){
                            $(this).dialog("close");
                            $(".venda_btn_log_item[NumregItem="+NumregItem+"]").click();
                        },
                        "Fechar": function(){
                            $(this).dialog("close");
                        }
                    },
                    close: function(){$(this).dialog("destroy");},
                    modal: true,
                    show: "fade",
                    hide: "fade"
                });
            }
        });
    }).css("cursor","pointer");
    if($(".venda_qtde_por_unid_medida").length > 0){
        $(".venda_qtde_por_unid_medida").css("cursor","help");
        $(".venda_qtde_por_unid_medida").css("display","inline-block");
        $(".venda_qtde_por_unid_medida").css("width","25px");
        $(".venda_qtde_por_unid_medida").qtip({content: 'Qtde. p/ Unid. Medida',style: 'blue'});
    }
<?php
foreach($Venda->getItens() as $IndiceItem => $Item){
    $ArCamposAlterarItem = array();
    $ArCamposAlterarItem[] = '#tabela_item_qtde_'.$Item->getNumregItem();
    $ArCamposAlterarItem[] = '#tabela_item_pct_desconto_tab_preco_'.$Item->getNumregItem();
    $ArCamposAlterarItem[] = '#tabela_item_id_moeda_'.$Item->getNumregItem();
    $ArCamposAlterarItem[] = '#tabela_item_id_unid_medida_'.$Item->getNumregItem();
    $ArCamposAlterarItem[] = '#tabela_item_vl_unitario_'.$Item->getNumregItem();
    $ArCamposAlterarItem[] = '#tabela_item_obs_'.$Item->getNumregItem();
    $ArCamposAlterarItem[] = '#tabela_item_id_cfop_'.$Item->getNumregItem();
    $ArCamposAlterarItem[] = '#tabela_item_chk_cotacao_fixa_'.$Item->getNumregItem();
    //Tratando os campos de desconto
    foreach($Venda->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
        $ArCamposAlterarItem[] = '#tabela_item_desc_'.$Item->getNumregItem().'_'.$IndiceCampoDesconto;
        ?>
        $(".venda_campo_desconto<?php echo $CampoDesconto['numreg'];?>").blur(function(){

        });
        <?php
    }
    ?>
    $("<?php echo implode(',',$ArCamposAlterarItem);?>").change(function(){
        p2_venda_marca_como_alterado(<?php echo $Item->getNumregItem();?>);
    });
<?php
}
?>
    $(".numeric").keypress(function(event){
        if(event.charCode && (event.charCode < 48 || event.charCode > 57)){
            event.preventDefault();
        }
    });
    /* Aplicando a máscara de valor */
    VendaAplicaMascaraDecimal();
});
function p2_venda_marca_como_alterado(NumregItem){
    $("#tabela_item_tr_"+NumregItem).addClass("venda_tr_item_alterado");
    $("#controle_alteracao_"+NumregItem).val(1);
    $("#controle_item_alterado").val(1);
}
</script>
<?php if($_SESSION['debug'] === true){ ?>
<fieldset>
    <legend>Debug</legend>
    <?php echo $Venda->getMensagemDebug(false,'<br/>');?>
</fieldset>
<?php } ?>