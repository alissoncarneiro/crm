<?php
if(!is_object($Venda)){
    $Venda = new Venda($TipoVenda,$NumregVenda);
    exit;
}
$Campos = new VendaCamposCustom($Venda->pfuncao,$Venda,$_GET);
$Campos->setCaminhoBase('../../');
if(isset($_GET['ppostback']) && $_GET['ppostback'] != '' && is_numeric($_GET['ppostback'])){
    $PostBack = $Campos->loadPostBack($_GET['ppostback']);
}
else{
    $PostBack = false;
}
?>
<script src="js/p1_venda.js"></script>
<?php echo($VendaParametro->getSnUsaRestrEstFCondPagto())?'<script src="js/p1_restr_est_cond_pagto.js"></script>':'' ?>
<?php require('p1_venda_custom.php');?>
<form name="form_p1" id="form_p1" action="p1_venda_post.php" method="POST">
<input type="hidden" name="ptp_venda" id="ptp_venda" value="<?php echo $_GET['ptp_venda'];?>" />
<input type="hidden" name="pnumreg" id="pnumreg" value="<?php echo $_GET['pnumreg'];?>" />
<input type="hidden" name="pfuncao" id="pfuncao" value="<?php echo $_GET['pfuncao'];?>" />
<input type="hidden" name="ppostback" id="ppostback" value="<?php echo $_GET['ppostback'];?>" />
<input type="hidden" name="url_retorno" id="url_retorno" value="<?php echo curPageURL();?>" />
<input type="hidden" name="pvisualizar_revisao" id="pvisualizar_revisao" value="<?php echo $_GET['pvisualizar_revisao'];?>" />
<?php
$DadosVenda = $Venda->decodeDeParaCamposValor($Venda->DadosVenda);
foreach($Campos->ArrayCampos as $k => $v){
    if($Campos->ArrayCampos[$k]['exibe_formulario'] == 0){
        if($PostBack !== false){
            $ValorPadrao = $Campos->getPostBack($Campos->getPrefixoCampo().$Campos->ArrayCampos[$k]['id_campo']);
        }
        else{
            $ValorPadrao = $Campos->ValorCustom($Campos->getIdCadastro(),$Campos->ArrayCampos[$k]['id_campo'],$DadosVenda[$k]);
            $ValorPadrao = $Campos->encodeValor($Campos->ArrayCampos[$k]['id_campo'],$ValorPadrao);
        }
        echo $Campos->getHTMLCampo($k,$ValorPadrao)."\n";
    }
}
?>
<fieldset><legend>Dados do <?php echo $Venda->getTituloVenda();?></legend>
<table width="100%" border="0" cellspacing="5" cellpadding="0">
<?php
$QuebraLinha = true;
foreach($Campos->ArrayCampos as $k => $v){
    if($Campos->ArrayCampos[$k]['exibe_formulario'] == 0){
       continue; 
    }
    if($QuebraLinha == true){?>
    <tr>
        <td align="right" valign="top"><?php echo $Campos->getLabelCampo($Campos->ArrayCampos[$k]['id_campo']);?></td><td><?php }
        if($QuebraLinha == false){
            echo '&nbsp;&nbsp;'.$Campos->getLabelCampo($Campos->ArrayCampos[$k]['id_campo']).'&nbsp;';
        }
        if($Campos->ArrayCampos[$k]['quebra_linha'] == 0){

            $QuebraLinha = false;
        }
        else{
            $QuebraLinha = true;
        }
        
        if($PostBack !== false){
            $ValorPadrao = $Campos->getPostBack($Campos->getPrefixoCampo().$Campos->ArrayCampos[$k]['id_campo']);
        }
        else{
            $ValorPadrao = $Campos->ValorCustom($Campos->getIdCadastro(),$Campos->ArrayCampos[$k]['id_campo'],$DadosVenda[$k]);
            $ValorPadrao = $Campos->encodeValor($Campos->ArrayCampos[$k]['id_campo'],$ValorPadrao);
        }

        echo $Campos->getHTMLCampo($k,$ValorPadrao);
        if($QuebraLinha == true){?></td>
    </tr>
<?php
    }
}
?>
    <tr>
        <td>&nbsp;</td>
        <td>
            <a href="#" id="btn_submit_p1" class="dicn_medium">
            <img src="img/avancar_pequeno.png" width="64" height="64" alt="Pr&oacute;ximo Passo" title="Pr&oacute;ximo Passo" />
            <p>Pr&oacute;ximo Passo</p>
            </a>
        </td>
    </tr>
</table>
</fieldset>
</form>