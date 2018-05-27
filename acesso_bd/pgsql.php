<?php

function conecta($servidor,$usuario,$senha,$bd){
    $conn = pg_connect("host=".$servidor." user=".$usuario." password=".$senha." dbname=".$bd)or die("ERRO NA CONEXÃO");
    return $conn;
}

function getPK($table){
    $table = trim(addslashes($table));
    if($table != ''){
        $qry = pg_query("SELECT ident_current('".$table."') AS PK");
        if(!$qry){
            return false;
        }
        $ar = pg_fetch_array($qry);
        return $ar['PK'];
    }
    else{
        return false;
    }
}

function iquery($Sql){
    global $conn;
    $Sql .= ' SELECT @@IDENTITY AS PK ';
    $Qry = pg_query($Sql,$conn);
    if(!Qry){
        echo $Sql;
        return false;
    }
    $Ar = pg_fetch_array($Qry);
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
    $queryexec = pg_query($queryparam);
    if(!$queryexec && QueryDebug == 1){echo $queryparam;}
    return $queryexec;
}

function farray($queryparam){
    $queryexec = pg_fetch_array($queryparam);
    return $queryexec;
}

function frow($queryparam){
    $queryexec = pg_fetch_row($queryparam);
    return $queryexec;
}

function fassoc($queryparam){
    $queryexec = pg_fetch_assoc($queryparam);
    return $queryexec;
}

function desconecta(){
    pg_close();
}

function fieldname($queryparam){
    $queryexec = pg_field_name($queryparam);
    return $queryexec;
}

function fieldtype($queryparam){
    $queryexec = pg_field_type($queryparam);
    return $queryexec;
}

function numrows($queryparam){
    $queryexec = pg_num_rows($queryparam);
    return $queryexec;
}

function busca($queryparam){
    $buscaretorno = farray(query($queryparam));
    return $buscaretorno;
}

function start_transaction(){
    query("BEGIN TRAN oasis;");
}

function commit_transaction(){
    query("COMMIT TRAN oasis;");
}

function rollback_transaction(){
    query("IF @@TRANCOUNT > 0 ROLLBACK TRAN oasis");
}

?>