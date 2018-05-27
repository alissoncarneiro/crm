<?php
/*
 * p3_venda.php
 * Autor: Alex
 * 04/11/2010 21:06
 * -
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */

if(!is_object($Venda)){
    $Venda = new Venda($TipoVenda,$NumregVenda);
    $Campos = new VendaCamposCustom($Venda->pfuncao,$Venda);
    exit;
}
$Venda->ValidaPoliticaComercialDesc();
$Venda->ValidaPoliticaBloqueioFinalizacao();
$QtdeColunasTabelaItens = 0;
?>
<fieldset><legend>Validando cabe&ccedil;alho do <?php echo ucwords($Venda->TituloVenda);?></legend>
<?php
/* Validando o desconto m�dio
//TODO: Construir regra de m�dia considerando o valor de m�dia de desconto corretamente
$ClassDivStatus = ($Venda->getPoliticaComercialDescVendaMedia()->getStatus() /* Se o status estiver OK * / )?'success':'warning';
echo '<div class="'.$ClassDivStatus.' venda_texto_grande_negrito">Desconto M&eacute;dio: '.$Venda->getPoliticaComercialDescVendaMedia()->getStringStatus().'</div>';
 */
/*Validando o desconto de campos fixos - tab. pre�o */
$ClassDivStatus = ($Venda->getPoliticaComercialDescVendaCampoDescontoFixoTabPreco()->getStatus() /* Se o status estiver OK */ )?'success':'warning';
echo '<div class="'.$ClassDivStatus.' venda_texto_grande_negrito">Desc. Tab. Pre&ccedil;o: '.$Venda->getPoliticaComercialDescVendaCampoDescontoFixoTabPreco()->getStringStatus().'</div>';

/*Validando o desconto de campos fixos - pessoa */
$ClassDivStatus = ($Venda->getPoliticaComercialDescVendaCampoDescontoFixoPessoa()->getStatus() /* Se o status estiver OK */ )?'success':'warning';
echo '<div class="'.$ClassDivStatus.' venda_texto_grande_negrito">Desc. Cliente: '.$Venda->getPoliticaComercialDescVendaCampoDescontoFixoPessoa()->getStringStatus().'</div>';

/*Validando o desconto de campos fixos - informado */
$ClassDivStatus = ($Venda->getPoliticaComercialDescVendaCampoDescontoFixoInformado()->getStatus() /* Se o status estiver OK */ )?'success':'warning';
echo '<div class="'.$ClassDivStatus.' venda_texto_grande_negrito">Desc. Informado: '.$Venda->getPoliticaComercialDescVendaCampoDescontoFixoInformado()->getStringStatus().'</div>';


?>
</fieldset>
<fieldset><legend>Validando Itens do <?php echo ucwords($Venda->TituloVenda);?></legend>
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
                <td>#</td><?php $QtdeColunasTabelaItens++;?>
                <td>C&oacute;d. <br />Produto</td><?php $QtdeColunasTabelaItens++;?>
                <td>Descri&ccedil;&atilde;o</td><?php $QtdeColunasTabelaItens++;?>
                <?php if($VendaParametro->getSnUsaDescTabPrecoItem()){ ?>
                <td><?php echo $VendaParametro->getNomeCampoDescTabPrecoItem();?></td><?php $QtdeColunasTabelaItens++;?>
                <?php } ?>
                <?php
                foreach($Venda->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){?>
                <td><?php echo $CampoDesconto['nome_campo'];?></td><?php $QtdeColunasTabelaItens++;?>
                <?php
                }
                ?>
                <td>Status Pol&iacute;tica Item</td><?php $QtdeColunasTabelaItens++;?>
                <td>Status Pol&iacute;tica Bloqueio Finaliza&ccedil;&atilde;o</td><?php $QtdeColunasTabelaItens++;?>
            </tr>
            <?php
            foreach($Venda->getItens() as $IndiceItem => $Item){
                $bgcolor = ($Item->getPoliticaComercialDescVendaItemMedia()->getStatus())?'#DFF2BF':'#FFBABA';
                $ArrayPoliticaComercialDescCampoDesconto = array();
                $StatusCampoDesconto = true;
                foreach($Venda->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
                    $Item->ValidaPoliticaComercialDescVendaItemCampoDesconto($IndiceCampoDesconto);
                    $ArrayPoliticaComercialDescCampoDesconto[$IndiceCampoDesconto] = $Item->getPoliticaComercialDescVendaItemCampoDesconto()->getStringStatus();
                    if(!$Item->getPoliticaComercialDescVendaItemCampoDesconto()->getStatus()){
                        $StatusCampoDesconto = false;
                    }
                }
                $bgcolor = ($StatusCampoDesconto)?$bgcolor:'#FFBABA';
            ?>
            <tr id="tabela_item_tr_<?php echo $Item->getNumregItem();?>" bgcolor="<?php echo $bgcolor;?>">
                <td><?php echo $Item->getDadosVendaItem('id_sequencia');?></td>
                <td><?php echo $Item->getCodProdutoERP();?></td>
                <td><?php echo $Item->getNomeProduto('nome_produto');?></td>
                <?php if($VendaParametro->getSnUsaDescTabPrecoItem()){ ?>
                <td><?php echo $Item->getPoliticaComercialDescVendaItemCampoDescontoFixoTabPreco()->getStringStatus();?></td>
                <?php } ?>
                <?php
                foreach($Venda->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
                ?>
                <td><?php echo $ArrayPoliticaComercialDescCampoDesconto[$IndiceCampoDesconto];?></td>
                <?php
                }
                ?>
                <td><?php echo $Item->getPoliticaComercialDescVendaItemMedia()->getStringStatus();?></td>
                <td><?php echo $Item->getPoliticaComercialBloqueioFinalizacao()->getStringStatus();?></td>
            </tr>
            <?php
            }
            ?>
            <tr id="tr_total_sem_ipi">
                <td colspan="<?php echo $QtdeColunasTabelaItens;?>" bgcolor="#CCCCCC" align="right">Total sem IPI <strong><?php echo $Venda->NFV($Venda->getVlTotalVendaLiquido());?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr id="tr_total_com_ipi">
                <td colspan="<?php echo $QtdeColunasTabelaItens;?>" bgcolor="#CCCCCC" align="right">Total com IPI <strong><?php echo $Venda->NFV($Venda->getVlTotalVendaLiquido()+$Venda->getVlTotalVendaIPI());?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr id="tr_total_com_ipi_e_st">
                <td colspan="<?php echo $QtdeColunasTabelaItens;?>" bgcolor="#CCCCCC" align="right">Total com IPI e ST <strong><?php echo $Venda->NFV($Venda->getVlTotalVendaLiquido()+$Venda->getVlTotalVendaIPI()+$Venda->getVlTotalVendaST());?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            </tr>
        </table>
    </form>
</fieldset>
<?php if($Venda->getEmAprovacao() || $Venda->getAvaliadoComercial()){ ?>
<hr size="1" />
<fieldset><legend>Justificativa Vendedor</legend>
    <?php echo nl2br(htmlentities($Venda->getDadosVenda('justificativa_em_aprov_com')));?>
</fieldset>
<?php } ?>
<?php if($Venda->getAvaliadoComercial()){ ?>
<hr size="1" />
<fieldset><legend>Justificativa Aprovador</legend>
    <?php
    $Avaliador = new Usuario($Venda->getDadosVenda('id_usuario_avaliador_comercial'));
    ?>
    <strong>Avaliador: </strong><?php echo $Avaliador->getNome();?><br />
    <strong>Data: </strong><?php echo uB::DataEn2Br($Venda->getDadosVenda('dt_avaliacao_comercial'));?><br />
    <strong>Justificativa: </strong><?php echo nl2br(htmlentities($Venda->getDadosVenda('justificativa_aprov_reprov_com')));?>
</fieldset>
<?php } ?>
<?php
    $Url = new Url();
    $Url->setUrl(curPageURL());
?>
<div align="center" style="text-align: center;">
    <a href="<?php $Url->AlteraParam('ppagina','p2'); echo $Url->getUrl();?>" class="dicn_medium">
    <img src="img/voltar_pequeno.png" width="64" height="64" alt="&lt;&lt; Passo Anterior" title="&lt;&lt; Passo Anterior" />
    <p>&lt;&lt; Passo Anterior</p>
    </a>

    <a href="<?php $Url->AlteraParam('ppagina','p4'); echo $Url->getUrl();?>" class="dicn_medium">
    <img src="img/avancar_pequeno.png" width="64" height="64" alt="&gt;&gt; Pr&oacute;ximo Passo" title="&gt;&gt; Pr&oacute;ximo Passo" />
    <p>&gt;&gt; Pr&oacute;ximo Passo</p>
    </a>
</div>