<?
require_once("../../conecta.php");
require_once("../../functions.php");
set_time_limit(0);
function formata_string($string, $tamanho_campo ){

	//conta tamanho string
	$tamanho_string = strlen($string);  
	
	
	if ($tamanho_string  >= $tamanho_campo){   
	
		$campo = substr($string,0,$tamanho_campo);  
		$retorno = $campo  ;							
	}
	elseif ($tamanho_string <= $tamanho_campo){           
	
		$conta = ($tamanho_campo - $tamanho_string);
		for ( $i = 0; $i < $conta ; $i++){
		
			$string .= " ";
		}
		$retorno = $string;
	}
	elseif ($tamanho_string == 0){
	
		for ( $i = 0; $i < $tamanho_campo ; $i++){
		$string .= " ";
		}
		$returno = $string ;
	}

return $retorno;
}

function trata_dt_exp_dts($dt){
	$dtt = str_replace('/','',$dt);
	$dtt = str_replace('-','',$dtt);
	return $dtt;
}
//Remover acentos
function retiraAcentos($string){
$array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç" , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
$array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" , "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
return str_replace($array1, $array2,$string);
}
$ar_diretorio = mysql_fetch_array(mysql_query("SELECT * FROM is_parametros_sistema WHERE id_parametro = 'EXP_CLI_DIR'"));
$param_dir = $ar_diretorio['parametro'];

if(file_exists($param_dir."cliente_conteudo.lst")){
	rename($param_dir."cliente_conteudo.lst",$param_dir.date("dmY_His")."cliente_conteudo.lst");
}
//$qry_cliente_exp = query("SELECT * FROM is_exp_cli ORDER BY numreg ASC");
$qry_cliente_exp = query("SELECT t1.* FROM is_exp_cli t1 LEFT JOIN is_pessoas t2 ON t1.numreg_pessoa = t2.numreg WHERE t1.exportado = 'N' ORDER BY t1.numreg ASC");
while($ar_cliente_exp = farray($qry_cliente_exp)){
	$numreg = $ar_cliente_exp['numreg_pessoa'];
	
	if($ar_cliente_exp['tipo_registro'] == '1'){
		$tipo_registro = '1';
		$tipo_implantacao = 'novo';
	}
	else{
		$tipo_registro = '2';
		$tipo_implantacao = 'alteracao';
	}
	include("interface_cliente_exp.php");
	if($exportado == 'OK'){
		$ar_pessoa = farray(query("SELECT * FROM is_pessoas WHERE numreg = '$numreg'"));
		query("UPDATE is_exp_cli SET exportado = 'S',data_exportacao=NOW() WHERE numreg = '".$ar_cliente_exp['numreg']."' LIMIT 1");
		query("UPDATE is_pessoas SET exportado_erp = 'S',data_exportacao=NOW() WHERE id_pessoa = '".$ar_pessoa['id_pessoa']."' LIMIT 1");
	}
}
?>
<table width="100%%" border="1" cellspacing="5" cellpadding="0">
	<tr>
		<td colspan="9"><h3>Total de Clientes Exportados: <?=$total?></h3> </td>
	</tr>
	
</table>
