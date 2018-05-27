<?php
/*
 * post_before_venda.php
 * Autor: Alex
 * 01/12/2011 16:03:13
 */
if($id_funcao == 'orcamento' && $opc == 'excluir'){
    echo 'Não é permitido excluir orçamentos!';
    exit;
}
if($id_funcao == 'pedido' && $opc == 'excluir'){
    echo 'Não é permitido excluir pedidos!';
    exit;
}
?>