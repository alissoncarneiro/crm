<?php
/*
 * relatorio_pedidos_post.php
 * Autor: Alex
 * 16/07/2012 10:28:53
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");
require('../../conecta.php');
require('../../functions.php');

$DtInicio = dtbr2en($_GET['dti']);
$DtFim = dtbr2en($_GET['dtf']);
$IdRepresentantePrincipal = $_GET['repp'];
$IdSituacaoPedido = $_GET['ids'];

$SqlPedidos = "SELECT
                    t1.numreg,
                    t2.razao_social_nome,
                    t1.id_pedido_cliente,
                    t3.nome_usuario,
                    t1.dt_pedido,
                    t1.dt_entrega,
                    t4.nome_situacao_pedido,
                    t1.vl_total
                FROM
                    is_pedido t1
                INNER JOIN
                    is_pessoa t2 ON t1.id_pessoa = t2.numreg
                INNER JOIN
                    is_usuario t3 ON t1.id_representante_principal = t3.numreg
                INNER JOIN
                    is_situacao_pedido t4 ON t1.id_situacao_pedido = t4.numreg
                WHERE
                    t1.sn_digitacao_completa = 1
                AND
                    t1.sn_em_aprovacao_comercial = 0
                AND
                    t1.dt_pedido >= '".$DtInicio."' AND t1.dt_pedido <= '".$DtFim."'";
if($IdRepresentantePrincipal != ''){
    $SqlPedidos .= " AND t1.id_representante_principal = '".$IdRepresentantePrincipal."'";
}
if($IdSituacaoPedido != ''){
    $SqlPedidos .= " AND t1.id_situacao_pedido = '".$IdSituacaoPedido."'";
}
$QryPedidos = query($SqlPedidos);
if(!$QryPedidos){
    echo 'Erro SQL';
    echo mysql_error();
    exit;
}
?>
<style>
    *{
        font-family: Arial;
        font-size: 12px;
    }
</style>
<table cellspacing="2" cellpadding="2" border="1">
    <tr style="font-weight: bold;">
        <td>N° Pedido</td>
        <td>Conta</td>
        <td>Nº Pedido Cliente</td>
        <td><?php echo DeparaCodigoDescricao('is_gera_cad_campos', array('nome_campo'), array('id_funcao' => 'pedido', 'id_campo' => 'id_representante_principal'));?></td>
        <td>Data do Pedido</td>
        <td>Data de Entrega</td>
        <td>Situação do Pedido</td>
        <td>Valor Total</td>
    </tr>
    <?php while($ArPedidos = farray($QryPedidos)){ ?>
    <tr>
        <td><?php echo $ArPedidos['numreg'];?></td>
        <td><?php echo $ArPedidos['razao_social_nome'];?></td>
        <td><?php echo $ArPedidos['id_pedido_cliente'];?></td>
        <td><?php echo $ArPedidos['nome_usuario'];?></td>
        <td><?php echo dten2br($ArPedidos['dt_pedido']);?></td>
        <td><?php echo dten2br($ArPedidos['dt_entrega']);?></td>
        <td><?php echo $ArPedidos['nome_situacao_pedido'];?></td>
        <td align="right"><?php echo number_format_min($ArPedidos['vl_total'],2,',','.');?></td>
    </tr>
    <?php } ?>
</table>