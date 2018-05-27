<?
if(
	$id_funcao == 'suspects_cad_lista' || 
	$id_funcao == 'prospects_cad_lista' || 
	$id_funcao == 'suspects_cad_lista' || 
	$id_funcao == 'empresas_cad_lista' || 
	$id_funcao == 'pessoas_cad_lista' || 
	$id_funcao == 'representantes_cad_lista' || 
	$id_funcao == 'fornecedores_cad_lista' || 
	$id_funcao == 'parceiros_cad_lista' || 
	$id_funcao == 'concorrentes_cad_lista'){
	if($opc == 'incluir'){
		mysql_query("UPDATE is_pessoas SET id_pessoa = numreg WHERE numreg = ".$pnumreg);
	}
}
?>