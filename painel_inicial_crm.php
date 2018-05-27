<?php
@session_start( );
//@header( "Content-Type: text/html;  charset=ISO-8859-1", true );
$cur_dir = dirname( $_SERVER['PHP_SELF'] );
ini_set( "include_path", get_include_path( ).";../../;".$cur_dir );
include( "calendario_agenda.php" );
