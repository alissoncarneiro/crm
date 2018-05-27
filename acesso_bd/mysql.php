<?php
function conecta($servidor, $usuario, $senha, $bd) {
	$conn    =   mysql_pconnect($servidor, $usuario, $senha)
             or die("ERRO NA CONEXÃO");

         $db      =   mysql_select_db($bd, $conn)
             or die("ERRO NA SELEÇÃO DO DATABASE");
	
    mysql_query('SET character_set_connection=utf8');
    mysql_query('SET character_set_client=utf8');
    mysql_query('SET character_set_results=utf8');

   return $conn;
}

function conecta_odbc($fonte_odbc) {
    global $usuario_odbc,$senha_odbc;
    $cn_odbc = odbc_connect($fonte_odbc,$usuario_odbc,$senha_odbc) or die("Erro na conexão com o ODBC ".$fonte_odbc); 
       return $cn_odbc;
}

function iquery($Sql){
    global $conn;
    $Qry = mysql_query($Sql,$conn);
    if(!$Qry){
        if(QueryDebug == 1){
            echo $Sql.'<br/>';
            echo mysql_errno().mysql_error();
        }
        return false;
    }
    return mysql_insert_id($conn);
}

function query($queryparam,$x=false){
    /* Linha comentada por Alex (29-09-2010 11:45:00) para resolver problema de gravar a string NULL no banco quando era digitado apostrofo
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

    $queryexec = mysql_query($queryparam);
    
    if(!$queryexec){
        if(QueryDebug == 1){
            echo $queryparam.'<br>';
            echo mysql_errno().mysql_error();
        }
        return false;
    }
    return $queryexec;
}

function farray($queryparam,$fonte_odbc="") {
    if($fonte_odbc) {
		$queryexec = odbc_fetch_array($queryparam);
		return $queryexec;
	} else {
		$queryexec = mysql_fetch_array($queryparam);
		return $queryexec;
	}
}

function frow($queryparam) {
	$queryexec = mysql_fetch_row($queryparam);
	return $queryexec;
}

function fassoc($queryparam) {
	$queryexec = mysql_fetch_assoc($queryparam);
	return $queryexec;
}

function desconecta() {
global $conn;
	mysql_close($conn);
}

function fieldname($queryparam, $coluna) {
	$queryexec = mysql_field_name($queryparam, $coluna);
	return $queryexec;
}

function fieldtype($queryparam, $coluna) {
	$queryexec = mysql_field_type($queryparam, $coluna);
	return $queryexec;
}

function numrows($queryparam) {
	$queryexec = mysql_num_rows($queryparam);
	return $queryexec;
}

function dataseek($sql_seek, $pos_ini_seek) {
	$queryexec = mysql_data_seek($sql_seek, $pos_ini_seek);
	return $queryexec;
}

function busca($queryparam){
	$buscaretorno = farray(query($queryparam));
	return $buscaretorno;
}

function start_transaction() {
	query("START TRANSACTION WITH CONSISTENT SNAPSHOT");
        query("SET autocommit=0");
}
function commit_transaction() {
	return query("COMMIT");
}
function rollback_transaction() {
	return query("ROLLBACK");
}

?>