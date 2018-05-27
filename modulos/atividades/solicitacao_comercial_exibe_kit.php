<?php
/*
 * solicitacao_comercial_exibe_kit.php
 * Autor: Alex
 * 15/03/2011 12:03
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

session_start();

require('../../conecta.php');
$IdKIT = $_POST['id_kit'];
/* Montando o HTML do tipo da Solicitação */
$HTMLSelectTipoSolicitacao = '<select>';
$QrySelectTipoSolicitacao = query("SELECT numreg,nome_tp_motivo_atend FROM is_tp_motivo_atend WHERE id_tp_grupo_motivo_atend = 1 ORDER BY nome_tp_motivo_atend");
while($ArSelectTipoSolicitacao = farray($QrySelectTipoSolicitacao)){
    $Selected = ($ArSelectTipoSolicitacao['numreg'] == '1')?' selected="selected"':'';
    $ArHTMLSelectTipoSolicitacao[] = '<option value="'.$ArSelectTipoSolicitacao['numreg'].'"'.$Selected.'>'.$ArSelectTipoSolicitacao['nome_tp_motivo_atend'].'</option>';
}

/* Montando o HTML da Tab de Preço */
$HTMLSelectTabPreco = '<select>';
$QrySelectTabPreco = query("SELECT numreg,id_tab_preco_erp,nome_tab_preco FROM is_tab_preco WHERE sn_ativa = 1 ORDER BY nome_tab_preco");
while($ArSelectTabPreco = farray($QrySelectTabPreco)){
    $Selected = '';
    $ArHTMLSelectTabPreco[] = '<option value="'.$ArSelectTabPreco['numreg'].'"'.$Selected.'>'.$ArSelectTabPreco['nome_tab_preco'].'</option>';
}
?>
<input type="hidden" name="sck_ci_id_kit" id="sck_ci_id_kit" value="<?php echo $IdKIT;?>" />
<fieldset>
    <legend>Itens do KIT</legend>
    <table border="0" align="left" width="100%" cellpadding="2" cellspacing="2" class="bordatabela">
        <tr bgcolor="#DAE8F4" class="tit_tabela">
            <td>&nbsp;</td>
            <td>Tipo da Solicita&ccedil;&atilde;o</td>
            <td>Produto</td>
            <td>Qtde.</td>
            <td>Tab. Pre&ccedil;os</td>
            <td>Dt. Desejada</td>
        </tr>
        <?php
        $i=0;
        $SqlItensKIT = "SELECT t1.numreg,t2.numreg AS id_produto, t2.id_produto_erp, t2.nome_produto, t1.qtde FROM is_kit_produto t1 INNER JOIN is_produto t2 ON t1.id_produto = t2.numreg WHERE t1.id_kit = '".$IdKIT."'";
        $QryItensKIT = query($SqlItensKIT);
        while($ArItensKIT = farray($QryItensKIT)){
            /* Verificando se o produto já está incluído */
            $SqlVerificaItem = "SELECT COUNT(*) AS CNT FROM is_atividade_solicitacao WHERE id_atividade = '".$_POST['id_atividade']."' AND id_produto = '".$ArItensKIT['id_produto']."'";
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
                <td><input type="checkbox" name="sck_chk_gravar_<?php echo $NumregItemKIT;?>" id="sck_chk_gravar_<?php echo $NumregItemKIT;?>" value="1" <?php echo $CHKGravar;?> /><?php echo $ImgItemExiste;?></td>
                <td><select name="sck_tp_motivo_atend_<?php echo $NumregItemKIT;?>" style="width:200px;"><?php echo implode('',$ArHTMLSelectTipoSolicitacao);?></select></td>
                <td><?php echo $ArItensKIT['nome_produto'];?></td>
                <td><input type="text" name="sck_qtde_<?php echo $NumregItemKIT;?>" class="numeric campo_qtde" value="<?php echo $ArItensKIT['qtde'];?>" /></td>
                <td><select name="sck_acao_id_tab_preco_<?php echo $NumregItemKIT;?>"><?php echo implode('',$ArHTMLSelectTabPreco);?></select></td>
                <td><input type="text" name="sck_acao_dt_desejada_<?php echo $NumregItemKIT;?>" class="date" /></td>
            </tr>
        <?php } ?>
    </table>
    <p>Descri&ccedil;&atilde;o da Solicita&ccedil;&atilde;o: <br/><textarea rows="7" cols="70" id="sck_obs" name="sck_obs" style="font-family: Courier New; font-size: 12px;"></textarea></p>
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