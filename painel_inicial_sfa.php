<?php
	@session_start(  );
	@header( 'Content-Type: text/html;  charset=ISO-8859-1', true );
	require_once( 'conecta.php' );
	$cur_dir = dirname( $_SERVER[PHP_SELF] );
	ini_set( 'include_path', get_include_path(  ) . ';../../;' . $cur_dir );
	$sql_mensagens = query( 'select * from is_mensagens where id_usuario like \'%' . $_SESSION['id_usuario'] . ',%\'' );
	while ($qry_mensagens = farray( $sql_mensagens )) {
		echo $qry_mensagens['textohtm'];
	}

?>