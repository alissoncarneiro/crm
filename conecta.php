<?php

    set_time_limit(0);
    ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('error_reporting', E_ALL & ~E_NOTICE);
    ini_set('default_charset','UTF-8');
    ini_set('display_errors', 'Off');

    define("RetornoErro",0);
    define("QueryDebug",1);
    define('ROOT_PATH', dirname(__FILE__));
    date_default_timezone_set('America/Sao_Paulo');

    $servidor   			=   "localhost:3306";
    $bd         			=   "followcrm";
    $usuario    			=   "followcrm";
    $senha      			=   "E3j6kEkZ#FuLt";
    $caminho_arquivos 		=   ROOT_PATH ."/arquivos/";
    $caminho_web 			=   "http://crm.icasaimob.com.br/";
    $_SESSION["porta_http"] =   ":".$_SERVER['SERVER_PORT'];
    $tipoBanco 			=   "mysql";
    $pref_db			=   '`';
    $acesso_permitido 		=   false;
    define("TipoBancoDados",$tipoBanco);



    require_once("acesso_bd/".strtolower($tipoBanco).".php");
    $conn = conecta($servidor, $usuario, $senha,$bd) or die("Erro na conexão com o banco de dados");
