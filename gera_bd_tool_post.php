<?php

@session_start();
require_once("conecta.php");
require_once("funcoes.php");
header('Content-Type: text/html; charset=utf-8');

$id_usuario = $_SESSION["id_usuario"];
$nome_usuario = $_SESSION["nome_usuario"];
$id_perfil = $_SESSION["id_perfil"];

$data = date("Y-m-d");
$hora = date("H:i:s");

$id_funcao = $_POST["edtid_funcao"];

if (empty($id_funcao)) {
    $qf = farray(query("select id_cad from is_gera_cad where numreg = '" . $_GET["pnumreg"] . "'"));
    $id_funcao = $qf["id_cad"];
}

if (empty($id_funcao)) {
    echo 'Favor preencher os campos corretamente !';
    exit;
}

$qry_gera_cad = farray(query("select * from is_gera_cad where id_cad = '$id_funcao'"));
$qry_funcoes = farray(query("select * from is_funcoes where id_funcao = '$id_funcao'"));

$tabela = $qry_gera_cad["nome_tabela"];

executa_atualiza_bd($tabela,$id_funcao);

if ($tabela == 'is_gera_cad_campos') {
    executa_atualiza_bd('is_gera_cad_campos_custom',$id_funcao);
}

function executa_atualiza_bd($tabela,$id_funcao) {

    global $tipoBanco;

    $sqlSchema = "SELECT COUNT(*) as total FROM information_schema.tables WHERE table_schema = '".$GLOBALS['bd']."' AND table_name = '".$tabela."'";
    $qrySchema = query($sqlSchema);
    $arrSchema = farray($qrySchema);



    if ($arrSchema['total'] <> 0)  {
        $tabela_existe = "S";
    } else {
        $tabela_existe = "N";
    }
    $at = "";

    if ($tabela_existe == "N") {
        if ($tipoBanco == "mysql") {
            $sql = "CREATE TABLE `$tabela` ( ";
            $sql .= "  `numreg` int NOT NULL auto_increment,";
            $sql .= "  PRIMARY KEY  (`numreg`)";
            $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
        }
        if ($tipoBanco == "mssql") {
            $sql = "CREATE TABLE [$tabela] (";
            $sql .= "  [numreg] int IDENTITY(1, 1) NOT NULL,";
            $sql .= "  UNIQUE ([numreg])";
            $sql .= ")";
        }
        $rq = @query($sql);
        if ($rq == "1") {
            $at .= "Criação da ";
        }
    }

    $sql_gera_cad_campos = query("(select * from is_gera_cad_campos where id_funcao = '$id_funcao' and tipo_campo <> 'calculado' order by ordem) union (select * from is_gera_cad_campos_custom where id_funcao = '$id_funcao' order by ordem)");

    $at .= "Tabela $tabela - Campos Atualizados : ";

    while ($qry_gera_cad_campos = farray($sql_gera_cad_campos)) {
        $tipo = $qry_gera_cad_campos["tipo_campo"];
        if (($tipo == "varchar") || ($tipo == "senha") || ($tipo == "string") || ($tipo == "arquivo") || ($tipo == "multicheck")) {
            $qry_gera_cad_campos["tamanho_campo"] = $qry_gera_cad_campos["tamanho_campo"] == '' ? '255' : $qry_gera_cad_campos["tamanho_campo"];
            if ($tipoBanco == "mysql") {
                $tipo_tamanho = "varchar(" . $qry_gera_cad_campos["tamanho_campo"] . ")";
            }
            if ($tipoBanco == "mssql") {
                $tipo_tamanho = "varchar(" . $qry_gera_cad_campos["tamanho_campo"] . ") COLLATE SQL_Latin1_General_CP1_CI_AI";
            }
        }
        if (($tipo == "combobox") || ($tipo == "lupa") || ($tipo == "lupa_popup")) {
            $tipo_tamanho = 'int';
        }
        if (($tipo == "sim_nao")) {
            $tipo_tamanho = 'tinyint';
        }
        if (($tipo == "bigint") || ($tipo == "int") || ($tipo == "double")) {
            $tipo_tamanho = 'int';
        }
        if (($tipo == "money") || ($tipo == "real") || ($tipo == "float")) {
            $tipo_tamanho = "numeric(15,2)";
        }
        if (($tipo == "memo") || ($tipo == "blob")) {
            $tipo_tamanho = "text";
        }
        if (($tipo == "date") || ($tipo == "datetime")) {
            if ($tipoBanco == "mysql") {
                $tipo_tamanho = "date";
            }
            if ($tipoBanco == "mssql") {
                $tipo_tamanho = "datetime";
            }
        }
        if ($qry_gera_cad_campos["sn_obrigatorio"] == 'S') {
            $nulo = "NOT NULL";
        } else {
            $nulo = "";
        }

        $sql = "ALTER TABLE $tabela ADD " . $qry_gera_cad_campos["id_campo"] . " " . $tipo_tamanho . " "; //.$nulo;

        $rq = @query($sql);
        if ($rq == "1") {
            $at .= $qry_gera_cad_campos["id_campo"] . ' ';
        }
    }
    echo $at;
}