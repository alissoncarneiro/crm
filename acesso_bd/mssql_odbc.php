<?php

function conecta($servidor,$usuario,$senha,$bd){

    $conn = odbc_connect($servidor,$usuario,$senha)
            or die("ERRO NA CONEXÃO");
    return $conn;
}

function getPK($table){
    global $conn;
    $table = trim(addslashes($table));
    if($table != ''){
        $qry = odbc_exec($conn,"SELECT ident_current('".$table."') AS PK");
        if(!$qry){
            return false;
        }
        $ar = odbc_fetch_array($qry);
        return $ar['PK'];
    }
    else{
        return false;
    }
}

function iquery($Sql){
    global $conn;
    $Sql .= ' SELECT @@IDENTITY AS PK ';
    $Qry = odbc_exec($conn,$Sql);
    if(!Qry){
        echo $Sql;
        return false;
    }
    $Ar = odbc_fetch_array($Qry);
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
    $queryexec = odbc_exec($conn,$queryparam);
    if(!$queryexec && QueryDebug == 1){echo $queryparam;}
    return $queryexec;
}

function farray($queryparam){
    $queryexec = odbc_fetch_array($queryparam);
    return $queryexec;
}

function frow($queryparam){
    $queryexec = odbc_fetch_row($queryparam);
    return $queryexec;
}

function fassoc($queryparam){
    $queryexec = odbc_fetch_assoc($queryparam);
    return $queryexec;
}

function desconecta(){
    odbc_close();
}

function fieldname($queryparam){
    $queryexec = odbc_field_name($queryparam);
    return $queryexec;
}

function fieldtype($queryparam){
    $queryexec = odbc_field_type($queryparam);
    return $queryexec;
}

function numrows($queryparam){
    $queryexec = odbc_num_rows($queryparam);
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