<?php

function conecta($servidor,$usuario,$senha,$bd){

    $conn = mssql_connect($servidor,$usuario,$senha)
            or die("ERRO NA CONEX�O");

    $db = mssql_select_db($bd,$conn)
            or die("ERRO NA SELE��O DO DATABASE");
    return $conn;
}

function getPK($table){
    $table = trim(addslashes($table));
    if($table != ''){
        $qry = mssql_query("SELECT ident_current('".$table."') AS PK");
        if(!$qry){
            return false;
        }
        $ar = mssql_fetch_array($qry);
        return $ar['PK'];
    }
    else{
        return false;
    }
}

function iquery($Sql){
    global $conn;
    $Sql .= ' SELECT @@IDENTITY AS PK ';
    $Qry = mssql_query($Sql,$conn);
    if(!Qry){
        echo $Sql;
        return false;
    }
    $Ar = mssql_fetch_array($Qry);
    return $Ar['PK'];
}

function query($queryparam,$x=false){
    /* Linha comentada por Alex (29-09-2010 11:45:00) para resilver problema de gravar a string NULL no banco quando era digitado apostrofo
     * $queryparam = str_replace("''","NULL",$queryparam);
     */
    $queryparam = str_replace(", ,",", NULL,",$queryparam);
    $queryparam = str_replace(",,",", NULL,",$queryparam);
    $queryparam = str_replace(",)",", NULL)",$queryparam);
    $queryparam = str_replace(", )",", NULL)",$queryparam);
    $queryparam = str_replace("(,","( NULL,",$queryparam);
    $queryparam = str_replace("( ,","( NULL,",$queryparam);
    $queryparam = str_replace("()","( NULL)",$queryparam);
    $queryparam = str_replace("( )","( NULL)",$queryparam);
    #echo $queryparam;
    $queryexec = mssql_query($queryparam);
    if(!$queryexec && QueryDebug == 1){echo $queryparam;}
    return $queryexec;
}

function farray($queryparam){
    $queryexec = mssql_fetch_array($queryparam);
    return $queryexec;
}

function frow($queryparam){
    $queryexec = mssql_fetch_row($queryparam);
    return $queryexec;
}

function fassoc($queryparam){
    $queryexec = mssql_fetch_assoc($queryparam);
    return $queryexec;
}

function desconecta(){
    mssql_close();
}

function fieldname($queryparam){
    $queryexec = mssql_field_name($queryparam);
    return $queryexec;
}

function fieldtype($queryparam){
    $queryexec = mssql_field_type($queryparam);
    return $queryexec;
}

function numrows($queryparam){
    $queryexec = mssql_num_rows($queryparam);
    return $queryexec;
}

function dataseek($sql_seek, $pos_ini_seek) {
	$queryexec = mssql_data_seek($sql_seek, $pos_ini_seek);
	return $queryexec;
}

function busca($queryparam){
    $buscaretorno = farray(query($queryparam));
    return $buscaretorno;
}

function start_transaction(){
    return query("BEGIN TRAN oasis");
}

function commit_transaction(){
    return query("COMMIT TRAN oasis");
}

function rollback_transaction(){
    return query("IF @@TRANCOUNT > 0 ROLLBACK TRAN oasis");
}

?>