<?

@session_start();
@header("Content-Type: text/html;  charset=ISO-8859-1", true);
require_once("conecta.php");
require_once("funcoes.php");

$id_usuario = $_SESSION["id_usuario"];
$nome_usuario = $_SESSION["nome_usuario"];
$id_perfil = $_SESSION["id_perfil"];

$data = date("Y-m-d");
$hora = date("H:i:s");

$id_funcao = $_POST["edtid_funcao"];
$banco = $_POST["edtbanco"];
if (empty($id_funcao)) {
    echo 'Favor preencher os campos corretamente !';
    exit;
}

if($banco == 'mysql'){
    $SufixoSql = ";\n";
}
elseif($banco == 'mssql'){
    $SufixoSql = "\nGO\n";
}

$sql_gera_cad = query("select * from is_gera_cad where id_cad = '$id_funcao'");
$sql_funcoes = query("select * from is_funcoes where id_funcao = '$id_funcao'");
$sql_gera_cad_sub = query("select * from is_gera_cad_sub where id_funcao_mestre = '$id_funcao'");
$sql_fases_workflow = query("select * from is_workflow_fase where id_workflow = '$id_funcao'");
$sql_gera_cad_botoes = query("select * from is_gera_cad_botoes where id_funcao = '$id_funcao'");
$sql_gera_cad_campos = query("select * from is_gera_cad_campos where id_funcao = '$id_funcao' order by ordem");


/* Apagando registros anteriores */
$sql_insert = "";
$sql_insert .= "delete from is_gera_cad where id_cad = '$id_funcao'".$SufixoSql;
$sql_insert .= "delete from is_gera_cad_sub where id_funcao_mestre = '$id_funcao'".$SufixoSql;
$sql_insert .= "delete from is_gera_cad_campos where id_funcao = '$id_funcao'".$SufixoSql;
$sql_insert .= "delete from is_gera_cad_botoes where id_funcao = '$id_funcao'".$SufixoSql;
$sql_insert .= "delete from is_workflow_fase where id_workflow = '$id_funcao'".$SufixoSql;
$sql_insert .= "delete from is_funcoes where id_funcao = '$id_funcao'".$SufixoSql;


/* Exportando Cadastro - Cabeçalho */
while ($qry_gera_cad = farray($sql_gera_cad)) {

    $i = 0;
    $nome_campos = "";
    $conteudos = "";

    while ($i < num_fields($sql_gera_cad, $tipoBanco)) {
        $meta = fetch_field($sql_gera_cad, $i, $tipoBanco);
        $tipo = $meta->type;
        if ($meta->name != "numreg") {
            $nome_campos .= $meta->name . ',';
            if (($tipo == "bigint") || ($tipo == "int") || ($tipo == "double") || ($tipo == "real") || ($tipo == "float")) {
                if ($qry_gera_cad[$meta->name]) {
                    $conteudos .= $qry_gera_cad[$meta->name] . ',';
                } else {
                    $conteudos .= 'NULL,';
                }
            } else {
                if (($tipo == "date") || ($tipo == "datetime")) {
                    $vl = $qry_gera_cad[$meta->name];
                    if ($vl == '0000-00-00') {
                        $vl = date("Y-m-d");
                    }
                    if ($banco == "mysql") {
                        $conteudos .= "'" . substr($vl, 0, 4) . "-" . substr($vl, 5, 2) . "-" . substr($vl, 8, 2) . "',";
                    } else {
                        $conteudos .= "'" . substr($vl, 0, 4) . substr($vl, 5, 2) . substr($vl, 8, 2) . "',";
                    }
                } else {
                    $conteudos .= "'" . str_replace("'", "\'", $qry_gera_cad[$meta->name]) . "',";
                }
            }
        }
        $i = $i + 1;
    }
    $sql_insert .= "INSERT INTO is_gera_cad ( " . substr($nome_campos, 0, strlen($nome_campos) - 1) . " ) values ( " . substr($conteudos, 0, strlen($conteudos) - 1) . " )".$SufixoSql;
}

/* Exportando Cadastro - Campos */
while ($qry_gera_cad_campos = farray($sql_gera_cad_campos)) {

    $i = 0;
    $nome_campos = "";
    $conteudos = "";

    while ($i < num_fields($sql_gera_cad_campos, $tipoBanco)) {
        $meta = fetch_field($sql_gera_cad_campos, $i, $tipoBanco);
        $tipo = $meta->type;
        if ($meta->name != "numreg") {
            $nome_campos .= $meta->name . ',';
            if (($tipo == "bigint") || ($tipo == "int") || ($tipo == "double") || ($tipo == "real") || ($tipo == "float")) {
                if ($qry_gera_cad_campos[$meta->name]) {
                    $conteudos .= $qry_gera_cad_campos[$meta->name] . ',';
                } else {
                    $conteudos .= 'NULL,';
                }
            } else {
                if (($tipo == "date") || ($tipo == "datetime")) {
                    $vl = $qry_gera_cad_campos[$meta->name];
                    if ($vl == '0000-00-00') {
                        $vl = date("Y-m-d");
                    }
                    if ($banco == "mysql") {
                        $conteudos .= "'" . substr($vl, 0, 4) . "-" . substr($vl, 5, 2) . "-" . substr($vl, 8, 2) . "',";
                    } else {
                        $conteudos .= "'" . substr($vl, 0, 4) . substr($vl, 5, 2) . substr($vl, 8, 2) . "',";
                    }
                } else {
                    $conteudos .= "'" . str_replace("'", "\'", $qry_gera_cad_campos[$meta->name]) . "',";
                }
            }
        }
        $i = $i + 1;
    }
    $sql_insert .= "INSERT INTO is_gera_cad_campos ( " . substr($nome_campos, 0, strlen($nome_campos) - 1) . " ) values ( " . substr($conteudos, 0, strlen($conteudos) - 1) . " )".$SufixoSql;
}

/* Exportando Cadastro - Funções */
while ($qry_funcoes = farray($sql_funcoes)) {

    $i = 0;
    $nome_campos = "";
    $conteudos = "";

    while ($i < num_fields($sql_funcoes, $tipoBanco)) {
        $meta = fetch_field($sql_funcoes, $i, $tipoBanco);
        $tipo = $meta->type;
        if ($meta->name != "numreg") {
            $nome_campos .= $meta->name . ',';
            if (($tipo == "bigint") || ($tipo == "int") || ($tipo == "double") || ($tipo == "real") || ($tipo == "float")) {
                if ($qry_funcoes[$meta->name]) {
                    $conteudos .= $qry_funcoes[$meta->name] . ',';
                } else {
                    $conteudos .= 'NULL,';
                }
            } else {
                if (($tipo == "date") || ($tipo == "datetime")) {
                    $vl = $qry_funcoes[$meta->name];
                    if ($vl == '0000-00-00') {
                        $vl = date("Y-m-d");
                    }
                    if ($banco == "mysql") {
                        $conteudos .= "'" . substr($vl, 0, 4) . "-" . substr($vl, 5, 2) . "-" . substr($vl, 8, 2) . "',";
                    } else {
                        $conteudos .= "'" . substr($vl, 0, 4) . substr($vl, 5, 2) . substr($vl, 8, 2) . "',";
                    }
                } else {
                    $conteudos .= "'" . str_replace("'", "\'", $qry_funcoes[$meta->name]) . "',";
                }
            }
        }
        $i = $i + 1;
    }
    $sql_insert .= "INSERT INTO is_funcoes ( " . substr($nome_campos, 0, strlen($nome_campos) - 1) . " ) values ( " . substr($conteudos, 0, strlen($conteudos) - 1) . " )".$SufixoSql;
}

/* Exportando Cadastro - Mestre Detalhe */
while ($qry_gera_cad_sub = farray($sql_gera_cad_sub)) {

    $i = 0;
    $nome_campos = "";
    $conteudos = "";

    while ($i < num_fields($sql_gera_cad_sub, $tipoBanco)) {
        $meta = fetch_field($sql_gera_cad_sub, $i, $tipoBanco);
        $tipo = $meta->type;
        if ($meta->name != "numreg") {
            $nome_campos .= $meta->name . ',';
            if (($tipo == "bigint") || ($tipo == "int") || ($tipo == "double") || ($tipo == "real") || ($tipo == "float")) {
                if ($qry_gera_cad_sub[$meta->name]) {
                    $conteudos .= $qry_gera_cad_sub[$meta->name] . ',';
                } else {
                    $conteudos .= 'NULL,';
                }
            } else {
                if (($tipo == "date") || ($tipo == "datetime")) {
                    $vl = $qry_gera_cad_sub[$meta->name];
                    if ($vl == '0000-00-00') {
                        $vl = date("Y-m-d");
                    }
                    if ($banco == "mysql") {
                        $conteudos .= "'" . substr($vl, 0, 4) . "-" . substr($vl, 5, 2) . "-" . substr($vl, 8, 2) . "',";
                    } else {
                        $conteudos .= "'" . substr($vl, 0, 4) . substr($vl, 5, 2) . substr($vl, 8, 2) . "',";
                    }
                } else {
                    $conteudos .= "'" . str_replace("'", "\'", $qry_gera_cad_sub[$meta->name]) . "',";
                }
            }
        }
        $i = $i + 1;
    }
    $sql_insert .= "INSERT INTO is_gera_cad_sub ( " . substr($nome_campos, 0, strlen($nome_campos) - 1) . " ) values ( " . substr($conteudos, 0, strlen($conteudos) - 1) . " )".$SufixoSql;
}

/* Exportando Fases */
while ($qry_fases_workflow = farray($sql_fases_workflow)) {

    $i = 0;
    $nome_campos = "";
    $conteudos = "";

    while ($i < num_fields($sql_fases_workflow, $tipoBanco)) {
        $meta = fetch_field($sql_fases_workflow, $i, $tipoBanco);
        $tipo = $meta->type;
        if ($meta->name != "numreg") {
            $nome_campos .= $meta->name . ',';
            if (($tipo == "bigint") || ($tipo == "int") || ($tipo == "double") || ($tipo == "real") || ($tipo == "float")) {
                if ($qry_fases_workflow[$meta->name]) {
                    $conteudos .= $qry_fases_workflow[$meta->name] . ',';
                } else {
                    $conteudos .= 'NULL,';
                }
            } else {
                if (($tipo == "date") || ($tipo == "datetime")) {
                    $vl = $qry_fases_workflow[$meta->name];
                    if ($vl == '0000-00-00') {
                        $vl = date("Y-m-d");
                    }
                    if ($banco == "mysql") {
                        $conteudos .= "'" . substr($vl, 0, 4) . "-" . substr($vl, 5, 2) . "-" . substr($vl, 8, 2) . "',";
                    } else {
                        $conteudos .= "'" . substr($vl, 0, 4) . substr($vl, 5, 2) . substr($vl, 8, 2) . "',";
                    }
                } else {
                    $conteudos .= "'" . str_replace("'", "\'", $qry_fases_workflow[$meta->name]) . "',";
                }
            }
        }
        $i = $i + 1;
    }
    $sql_insert .= "INSERT INTO is_workflow_fase ( " . substr($nome_campos, 0, strlen($nome_campos) - 1) . " ) values ( " . substr($conteudos, 0, strlen($conteudos) - 1) . " )".$SufixoSql;
}


/* Exportando Botoes */
while ($qry_gera_cad_botoes = farray($sql_gera_cad_botoes)) {

    $i = 0;
    $nome_campos = "";
    $conteudos = "";

    while ($i < num_fields($sql_gera_cad_botoes, $tipoBanco)) {
        $meta = fetch_field($sql_gera_cad_botoes, $i, $tipoBanco);
        $tipo = $meta->type;
        if ($meta->name != "numreg") {
            $nome_campos .= $meta->name . ',';
            if (($tipo == "bigint") || ($tipo == "int") || ($tipo == "double") || ($tipo == "real") || ($tipo == "float")) {
                if ($qry_gera_cad_botoes[$meta->name]) {
                    $conteudos .= $qry_gera_cad_botoes[$meta->name] . ',';
                } else {
                    $conteudos .= 'NULL,';
                }
            } else {
                if (($tipo == "date") || ($tipo == "datetime")) {
                    $vl = $qry_gera_cad_botoes[$meta->name];
                    if ($vl == '0000-00-00') {
                        $vl = date("Y-m-d");
                    }
                    if ($banco == "mysql") {
                        $conteudos .= "'" . substr($vl, 0, 4) . "-" . substr($vl, 5, 2) . "-" . substr($vl, 8, 2) . "',";
                    } else {
                        $conteudos .= "'" . substr($vl, 0, 4) . substr($vl, 5, 2) . substr($vl, 8, 2) . "',";
                    }
                } else {
                    $conteudos .= "'" . str_replace("'", "\'", $qry_gera_cad_botoes[$meta->name]) . "',";
                }
            }
        }
        $i = $i + 1;
    }
    $sql_insert .= "INSERT INTO is_gera_cad_botoes ( " . substr($nome_campos, 0, strlen($nome_campos) - 1) . " ) values ( " . substr($conteudos, 0, strlen($conteudos) - 1) . " )".$SufixoSql;
}



$sql_insert = str_replace("'--'",'NULL',$sql_insert);
$sql_insert = str_replace("''",'NULL',$sql_insert);
$sql_insert = str_replace(",,",'NULL',$sql_insert);
$sql_insert = str_replace("\'","''",$sql_insert);

echo $sql_insert;


function num_fields($query, $bd) {
    if ($bd == "mysql") {
        return mysql_num_fields($query);
    } else {
        return mssql_num_fields($query);
    }
}

function fetch_field($query, $i, $bd) {
    if ($bd == "mysql") {
        return mysql_fetch_field($query, $i);
    } else {
        return mssql_fetch_field($query, $i);
    }
}

?>
