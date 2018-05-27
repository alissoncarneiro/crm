<?php

/*
 * class.BancoDeDados.php
 * Autor: Alex
 * 27/11/2010 18:42:00
 *
 * Classe respons�vel pelas conex�es com o banco de dados
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */

class BancoDeDados{

    private $TipoBanco;

    public function __construct($TipoBanco){
        switch($TipoBanco){
            case 'mysql':
               $this->TipoBanco = $TipoBanco;
                break;
            case 'mssql':
                $this->TipoBanco = $TipoBanco;
                break;
            case 'progress':
                $this->TipoBanco = $TipoBanco;
                break;
            default :
                echo 'Tipo de Banco de Dados Inv�lido';
                exit;
        }
        
    }

    public function query($Sql){
        switch($this->TipoBanco){
            case 'mysql':
                return mysql_query($Sql);
                break;
            case 'mssql':
                return mssql_query($Sql);
                break;
            case 'progress':
                return mysql_query($Sql);
                break;
        }
    }
}

?>