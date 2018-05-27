<?php

/*
 * class.ConsultaEstoqueCustom.php
 * Autor: Lucas
 * 24/11/2010 17:55:00
 *
 * Classe responsável por extender a ConsultaEstoque, onde podem ser criados métodos específicos para cada cliente. A baixo dois exemplos:
 * 1 - padrão de retorno vazio(pega apenas o getSaldoEstoque da classe principal
 * 2 - getSaldoEstoque da AlphaPrint (o RETORNO é parecido com a consulta da Vogler, porém a forma de chegar no valor é diferente)
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class ConsultaEstoqueCustom extends ConsultaEstoque{
    public function __construct($VendaParametro){
        parent::__construct($VendaParametro);
    }
}
?>