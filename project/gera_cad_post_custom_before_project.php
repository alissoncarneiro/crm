<?
if($opc == 'alterar' || $opc == 'incluir') {
	if($id_funcao == 'atividades_cad_lista'){
		$ar_ativ_before = farray(query("SELECT id_atividade,dt_inicio,dt_prev_fim FROM is_atividades WHERE numreg='".$pnumreg."'"));
	}
}
?>