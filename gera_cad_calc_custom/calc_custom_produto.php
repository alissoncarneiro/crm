<?php
/*
 * calc_custom_produto.php
 * Autor: Alex
 * 27/04/2011 10:34:54
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
if($id_funcao == 'produtos_cad'){
    if($id_campo == 'calc_estoque'){
        include_once('modulos/venda/classes/class.Venda.Parametro.php');
        $VendaParametro = new VendaParametro();
        if($VendaParametro->getSnConsultaEstoque()){
            $ret = '<img src="modulos/venda/img/estoque_pequeno.gif" alt="Visualizar estoque" title="Visualizar estoque" style="cursor:pointer" onclick="javascript:exibe_estoque_produto('.$qry_cadastro['numreg'].');" />';
        }
        else{
            $ret  = '';
        }
    }
}
?>