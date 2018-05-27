<?php
/*
 * oportunidade_exibe_kit.php
 * Autor: Alex
 * 18/12/2012 10:53:06
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

session_start();

require('../../conecta.php');
require('../../functions.php');
$IdKIT = $_POST['id_kit'];
?>
<input type="hidden" name="oik_ci_id_kit" id="oik_ci_id_kit" value="<?php echo $IdKIT;?>" />
<fieldset>
    <legend>Itens do KIT</legend>
    <table border="0" align="left" width="100%" cellpadding="2" cellspacing="2" class="bordatabela">
        <tr bgcolor="#DAE8F4" class="tit_tabela">
            <td>&nbsp;</td>
            <td>Produto</td>
            <td>Qtde.</td>
            <td>Valor Unit.</td>
            <td>Obs.</td>            
        </tr>
        <?php
        $i=0;
        $SqlItensKIT = "SELECT t1.numreg,t2.numreg AS id_produto, t2.id_produto_erp, t2.nome_produto, t1.qtde FROM is_kit_produto t1 INNER JOIN is_produto t2 ON t1.id_produto = t2.numreg WHERE t1.id_kit = '".$IdKIT."'";
        $QryItensKIT = query($SqlItensKIT);
        while($ArItensKIT = farray($QryItensKIT)){
            /* Verificando se o produto já está incluído */
            $SqlVerificaItem = "SELECT COUNT(*) AS CNT FROM is_opor_produto WHERE id_oportunidade = '".$_POST['id_oportunidade']."' AND id_produto = '".$ArItensKIT['id_produto']."'";
            $QryVerificaItem = query($SqlVerificaItem);
            $ArVerificaItem  = farray($QryVerificaItem);
            if($ArVerificaItem['CNT'] > 0){
                $ImgItemExiste = '&nbsp;<img src="images/btn_alerta.png" alt="Este item já está adicionado." title="Este item já está adicionado." style="cursor:help;" />';
                $CHKGravar = '';
            }
            else{
                $ImgItemExiste = '';
                $CHKGravar = ' checked="checked"';
            }

            $i++;
            $NumregItemKIT = $ArItensKIT['numreg'];
            $BgColor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
            ?>
            <tr bgcolor="<?php echo $BgColor;?>">
                <td><input type="checkbox" name="oik_chk_gravar_<?php echo $NumregItemKIT;?>" id="oik_chk_gravar_<?php echo $NumregItemKIT;?>" value="1" <?php echo $CHKGravar;?> /><?php echo $ImgItemExiste;?></td>
                <td><?php echo $ArItensKIT['nome_produto'];?></td>
                <td><input type="text" name="oik_qtde_<?php echo $NumregItemKIT;?>" class="numeric campo_qtde" value="<?php echo $ArItensKIT['qtde'];?>" /></td>
                <td><input type="text" size="10" class="monetario campo_vl_unitario" CasasDecimais="<?php echo getParametrosVenda('precisao_valor');?>" name="vl_unitario_<?php echo $NumregItemKIT;?>" id="vl_unitario_<?php echo $NumregItemKIT;?>" value="0"/></td>
                <td><input type="text" name="oik_obs_<?php echo $NumregItemKIT;?>" id="oik_obs_<?php echo $NumregItemKIT;?>" style="width:250px;" /></td>
            </tr>
        <?php } ?>
    </table>    
</fieldset>
<script>
    $(document).ready(function(){
        $(".numeric").keypress(function(event){
            if(event.charCode && (event.charCode < 48 || event.charCode > 57)){
                event.preventDefault();
            }
        });
        $(".date").datepicker({
            showOn: "button",
            buttonImage: "images/agenda.gif",
            buttonImageOnly: true,
            changeMonth:true,
            changeYear:true,
            minDate:0
        });
    });
</script>