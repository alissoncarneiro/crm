<?php
/*
 * resumo_ultimos_pedidos.php
 * Autor: Alex
 * 06/06/2011 17:34:05
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html; charset=ISO-8859-1",true);
session_start();
require('includes.php');

$IdPessoa = base64_decode($_POST['id_pessoa']);
$IdPessoaERP = base64_decode($_POST['id_pessoa_erp']);

if($IdPessoaERP == ''){
    echo 'Cliente n&atilde;o informado.';
    exit;
}
$Data = date("Y-m-d");
?>
<fieldset>
    <legend>Resumo dos &uacute;ltimos pedidos</legend>
    <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="venda_tabela_itens">
        <tr class="venda_titulo_tabela">
            <td>Per&iacute;odo</td>
            <td>Total NF</td>
            <td>Total Pedidos</td>
        </tr>
        <?php for($i=0;$i<=11;$i++){
            $DataDe = substr($Data,0,7).'-01';
            $DataAte = substr($Data,0,7).'-'.cal_days_in_month(CAL_GREGORIAN, substr($Data,5,2), substr($Data,0,4));
            $QryNF = query("SELECT SUM(vl_tot) AS vl_total FROM is_dm_notas_cab WHERE id_pessoa = '".$IdPessoa."' AND dt_emis_nota >= '".$DataDe."' AND dt_emis_nota <= '".$DataAte."'");
            $ArNF = farray($QryNF);
            $QryPedidos = query("SELECT SUM(vl_total) AS vl_total FROM is_pedido WHERE id_situacao_pedido IN(1,2,3) AND id_pessoa = '".$IdPessoa."' AND dt_pedido >= '".$DataDe."' AND dt_pedido <= '".$DataAte."'");
            $ArPedidos = farray($QryPedidos);
            $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
            $ArrayNomeData = DataHora::getStringDataHora($Data);
            $Data = date("Y-m-d",strtotime($Data." - 1 month"));
        ?>
        <tr bgcolor="<?php echo $bgcolor;?>">
            <td align="left"><?php echo $ArrayNomeData['nome_mes'],'/',$ArrayNomeData['ano'];?></td>
            <td align="center"><?php echo number_format($ArNF['vl_total'],2,',','.');?></td>
            <td align="center"><?php echo number_format($ArPedidos['vl_total'],2,',','.');?></td>
        </tr>
        <?php } ?>
    </table>
</fieldset>