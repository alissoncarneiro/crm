<?php

/*
 * class.BancoDeDados.php
 * Autor: Alex
 * 27/11/2010 18:42:00
 *
 * Classe responsável pelas conexões com o banco de dados
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
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
                echo 'Tipo de Banco de Dados Inválido';
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