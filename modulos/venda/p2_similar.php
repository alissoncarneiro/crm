<?php
/*
 * p2_similar.php
 * Autor: Alex
 * 30/11/2010 13:26
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html;  charset=ISO-8859-1");
session_start();

require('../../conecta.php');
require('../../classes/class.uB.php');
require('../../functions.php');
?>
<script language="JavaScript">
    $(document).ready(function(){
        $(".botao_jquery").button();
        $(".btn_detalhes_produto").click(function(){
            exibe_detalhe_produto($(this).attr('pnumreg_produto_similar'),'<?php echo $_POST['pnumreg'];?>');
            $("#jquery-dialog").dialog("close");
        });
    });
</script>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="venda_tabela_itens">
    <tr class="venda_titulo_tabela">
        <td>C&oacute;d. Produto</td>
        <td>Descri&ccedil;&atilde;o</td>
        <td>&nbsp;</td>
    </tr>
    <?php
    $Qrysimilar = query("SELECT t1.id_produto_similar,t2.id_produto_erp,t2.nome_produto FROM is_produto_similar t1 INNER JOIN is_produto t2 ON t1.id_produto_similar = t2.numreg WHERE t1.id_produto_pai = ".$_POST['pnumreg']);
    while($Arsimilar = farray($Qrysimilar)){
        $i++;
        $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
    ?>
    <tr bgcolor="<?php echo $bgcolor;?>">
        <td><?php echo $Arsimilar['id_produto_erp'];?></td>
        <td><?php echo $Arsimilar['nome_produto'];?></td>
        <td><input type="button" class="botao_jquery btn_detalhes_produto" value="Detalhes" pnumreg_produto_similar="<?php echo $Arsimilar['id_produto_similar'];?>" /></td>
    </tr>
    <?php
    }
    ?>
</table>