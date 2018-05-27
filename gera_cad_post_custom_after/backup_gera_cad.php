<?php
/*
 * backup_gera_cad.php
 * Autor: Alex
 * 24/11/2010 15:02:00
 * Classe respons�vel por tratar os pedidos e or�amentos
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */

if($pfuncao == 'modulos_cad_lista' || $pfuncao == 'funcoes_cad_lista' || $pfuncao == 'cadastros_cad_lista' || $pfuncao == 'campos_cad_lista' || $pfuncao == 'gera_cad_sub_lista'){
    $ArInsertLogGeraCad = array();
    $ArInsertLogGeraCad['dthr_cadastro'] = date("Y-m-d H:i:s");
    $ArInsertLogGeraCad['id_usuario'] = $_SESSION['id_usuario'];
    $ArInsertLogGeraCad['ip'] = $_SERVER['REMOTE_ADDR'];
    if($opc == 'excluir'){
        $ArInsertLogGeraCad['sql'] = stripslashes($sqlexec);
    }
    elseif($opc == 'incluir'){
        $ArInsertLogGeraCad['sql'] = stripslashes($sql_insert);
    }
    elseif($opc == 'alterar'){
        $ArInsertLogGeraCad['sql'] = stripslashes($sql_update);
    }
    $SqlInsertLogGeraCad = AutoExecuteSql(TipoBancoDados,'is_log_gera_cad',$ArInsertLogGeraCad,'INSERT');
    query($SqlInsertLogGeraCad);
}

?>