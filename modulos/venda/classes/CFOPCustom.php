<?php
/*
 * CFOPCustom.php
 * Autor: Anderson
 * 29/11/2010 16:16
 * -
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */

/*
 * Para AlphaPrint
 */
$ArValores['id_tp_item'] = $this->getProduto()->getIdTpProduto();
$ArValores['id_tp_cliente'] = $this->getObjVenda()->getPessoa()->getIdTpPessoa();
?>