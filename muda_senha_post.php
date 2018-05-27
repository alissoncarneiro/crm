<?php
	@session_start(  );
	@header( 'Content-Type: text/html;  charset=ISO-8859-1', true );
	require_once( 'conecta.php' );
	require_once( 'funcoes.php' );
	$edtsenha = $_POST['edtsenha'];
	$edtsenhanova = $_POST['edtsenhanova'];
	$edtsenhaconf = $_POST['edtsenhaconf'];
	$data = date( 'Y-m-d' );
	$hora = date( 'H:i:s' );
	$id_usuario = $_SESSION['id_usuario'];
	$qry_usuarios = farray( query( 'select * from is_usuario where numreg = \'' . $id_usuario . '\' and senha = \'' . $edtsenha . '\'' ) );

	if ($qry_usuarios['nome_usuario']) {
		if ($edtsenhanova == $edtsenhaconf) {
			$sql = 'UPDATE is_usuario set senha = \'' . $edtsenhanova . '\' where numreg = \'' . $id_usuario . '\'';
			$rq = @query( $sql );

			if ($rq == '1') {
				echo 'Senha atualiza com sucesso ' . $qry_usuarios['nome_usuario'] . ' !';
				return 1;
			}
		}else {
			echo 'Nova senha não confere !';
			exit(  );
			return 1;
		}
	}
	echo 'Senha atual incorreta !';
	exit(  );
?>