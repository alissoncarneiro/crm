<?php

/*
 * class.ConsultaEstoqueCustom.php
 * Autor: Lucas
 * 24/11/2010 17:55:00
 *
 * Classe respons�vel por extender a ConsultaEstoque, onde podem ser criados m�todos espec�ficos para cada cliente. A baixo dois exemplos:
 * 1 - padr�o de retorno vazio(pega apenas o getSaldoEstoque da classe principal
 * 2 - getSaldoEstoque da AlphaPrint (o RETORNO � parecido com a consulta da Vogler, por�m a forma de chegar no valor � diferente)
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */

class ConsultaEstoqueCustom extends ConsultaEstoque{
    public function __construct($VendaParametro){
        parent::__construct($VendaParametro);
    }
}
?>