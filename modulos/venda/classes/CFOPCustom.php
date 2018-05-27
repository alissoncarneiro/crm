<?php
/*
 * CFOPCustom.php
 * Autor: Anderson
 * 29/11/2010 16:16
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

/*
 * Para AlphaPrint
 */
$ArValores['id_tp_item'] = $this->getProduto()->getIdTpProduto();
$ArValores['id_tp_cliente'] = $this->getObjVenda()->getPessoa()->getIdTpPessoa();
?>