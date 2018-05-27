<?
function dten2brsb($dt){
	return substr($dt,8,2).substr($dt,5,2).substr($dt,0,4);
}
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
		$returno = $string;
	}
	return $retorno;
}
//Remover acentos
function retiraAcentos($string){
	$array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç" , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
	$array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" , "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
	return str_replace($array1, $array2,$string);
}
$texto_out = '<table width="100%%" border="1" cellspacing="5" cellpadding="0">
	<tr>
		<td colspan="9"><h3>Relat&oacute;rio de Pedidos Exportados Pelo CRM </h3></td>
	</tr>
	
</table>';
$odbc_c = true;
require("../../conecta.php");
require("../../functions.php");
set_time_limit(0);
$ar_diretorio = mysql_fetch_array(mysql_query("SELECT * FROM is_parametros_sistema WHERE id_parametro = 'EXP_PED_DIR'"));
$param_dir = $ar_diretorio['parametro'];
if(file_exists($param_dir."pedido_conteudo.lst")){
	rename($param_dir."pedido_conteudo.lst",$param_dir.date("dmY_His")."RENOMEADO"."pedido_conteudo.lst");
}
/*
$qry_pedido_exp = query("SELECT * FROM is_exp_ped WHERE exportado = 'N'");
$num_include = true;
while($ar_pedido_exp = farray($qry_pedido_exp)){
*/
$num_include = true;

$dt_ontem = date("Y-m-d",strtotime(date("Y-m-d").""));
$qry_pedido = query("SELECT * FROM is_pedidos WHERE id_sit_ped = '1' AND (exportado_erp = 'N' OR exportado_erp IS NULL) AND importado_erp IS NULL ORDER BY numreg");
while($ar_pedido = farray($qry_pedido)){
	$qry_itens_pedido = query("SELECT numreg FROM is_pedidos_itens WHERE id_pedido = '".$ar_pedido['id_pedido']."'");
	if(mysql_num_rows($qry_itens_pedido) > 0){
		$texto_out .= '<div style="border: 1px #000000 dashed;">';
		$numreg = $ar_pedido['numreg'];
		$tipo_pedido = '';
		$inclusao = true;
		include("interface_pedido_exp.php");
		$num_include = false;
		$num_pedido_exportado = $num_pedido_exportado * 1 + 1;
		$texto_out .= '</div>';
		//query("UPDATE is_exp_ped SET exportado = 'S',data_exportacao = NOW() WHERE numreg_pedido = '".$ar_pedido['numreg']."' LIMIT 1");
		query("UPDATE is_pedidos SET exportado_erp = 'S',data_exportacao=NOW() WHERE id_pedido = '".$ar_pedido['id_pedido']."' LIMIT 1");
	}
	else{
		$texto_out .= '<div style="height:30px; border: 1px dashed #FF0000;background:#C1FFC1"><h3>Pedido Não Exportado Por Não Haver Itens; Exportado Complemento e Brinde</h3>';
		$texto_out .= '<br><strong>Cód Pedido:</strong> '.$ar_pedido['id_pedido'];
		$texto_out .= '<br><strong>Cód Pedido Propagandista:</strong> '.$ar_pedido['id_pedido_repr'];
		$texto_out .= '<br><strong>Cód Cliente:</strong> '.$ar_pedido['id_empresa'];
		$texto_out .= '<br><strong>Cód Cliente:</strong> '.search_name('is_pessoas','id_pessoa','razao_social_nome',$ar_pedido['id_empresa']);
		$texto_out .= '</div>';
	}
}
$texto_out .= '<table width="100%%" border="1" cellspacing="5" cellpadding="0">
	<tr>
		<td colspan="9"><h3>Total de Pedidos Exportados: '.$num_pedido_exportado.'</h3><br><h3>Total de Itens Exportados: '.$total_itens_exportados.'</h3></td>
	</tr>
</table>';
#echo $texto_out;
echo "Foram exportado(s) ".$num_pedido_exportado." pedido(s).";
$file_log = fopen($param_dir."LOG_EXPORTACAO".date("Ymd_His").".htm","w+");
fwrite($file_log,$texto_out.chr(13).chr(10));
fclose($file_log);
?>
