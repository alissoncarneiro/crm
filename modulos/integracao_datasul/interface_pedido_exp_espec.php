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
		$returno = $string ;
	}
	return $retorno;
}
//Remover acentos
function retiraAcentos($string){
	$array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç" , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
	$array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" , "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
	return str_replace($array1, $array2,$string);
}
function max_id($tabela,$campo,$mais=1){
	$ar_max = mysql_fetch_array(mysql_query("SELECT MAX(".$campo."*1) AS Max FROM ".$tabela));
	$qry_mais = mysql_query("SELECT ".$campo." FROM ".$tabela." WHERE ".$campo." = '".($ar_max['Max'] + $mais)."'");
	if(numrows($qry_mais) == 0){
		return ($ar_max['Max'] + $mais);
	}
	else{
		max_id($tabela,$campo,($ar_max['Max'] + $mais + 1));
	}
}

?>
<form id="form1" name="form1" method="get" action="">
	<table width="100%%" border="1" cellspacing="5" cellpadding="0">
		<tr>
			<td colspan="9"><h3>Digite o Numero dos Pedido separados Por V&iacute;rgula.<br />
				</h3>
					<h3>
						<textarea name="list_ped" cols="80" rows="6" id="list_ped"><?=$_GET['list_ped'];?></textarea>
						<br />
						<input type="submit" value="Exportar" />
				</h3></td>
		</tr>
	</table>
</form>
<p>&nbsp;</p>
<table width="100%%" border="1" cellspacing="5" cellpadding="0">
	<tr>
		<td colspan="9"><h3>Relat&oacute;rio de Pedidos Exportados Pelo CRM </h3></td>
	</tr>
	
</table>

<?
require("../conecta.php");
require("../functions.php");

set_time_limit(0);




//$qry_pedido = query("SELECT * FROM is_pedidos ORDER BY numreg ASC LIMIT 2");
//$qry_pedido = query("SELECT * FROM is_pedidos WHERE dt_pedido > '2009-01-19' AND id_usuario_cad <> 'IMPORTDTS' AND obs_nf IS NULL ORDER BY dt_pedido,id_pedido ASC");
//$qry_pedido = query("SELECT * FROM is_pedidos WHERE dt_cadastro > '2009-01-24' ORDER BY dt_pedido,id_pedido ASC LIMIT 10");
if(!empty($_GET['list_ped'])){
	$in_list_ped = '';
	$prefix_ped = '';
	$ar_list_ped = explode(',',$_GET['list_ped']);
	for($p=0;$p<count($ar_list_ped);$p++){
		if(!empty($ar_list_ped[$p])){
			$in_list_ped .= "'".$ar_list_ped[$p]."',";
			$prefix_ped .= $ar_list_ped[$p]."_";
		}
	}
	$in_list_ped = substr($in_list_ped,0,strlen($in_list_ped)-1);
	$qry_pedido_exp = query("SELECT * FROM is_pedidos WHERE id_pedido IN($in_list_ped)");
	echo $in_list_ped;
}
else{
	exit;
}

$ar_diretorio = mysql_fetch_array(mysql_query("SELECT * FROM is_parametros_sistema WHERE id_parametro = 'EXP_PED_DIR'"));
$param_dir = $ar_diretorio['parametro'].date("Ymd_His");

$num_include = true;
while($ar_pedido_exp = farray($qry_pedido_exp)){
	$ar_pedido = farray(query("SELECT * FROM is_pedidos WHERE numreg = '".$ar_pedido_exp['numreg']."'"));
	$qry_itens_pedido = query("SELECT numreg FROM is_pedidos_itens WHERE id_pedido = '".$ar_pedido['id_pedido']."'");
	if(numrows($qry_itens_pedido) > 0){
		echo '<div style="border: 1px #000000 dashed;">';
		$numreg = $ar_pedido['numreg'];
		$tipo_pedido = '';
		$inclusao = true;
		include("interface_pedido_exp.php");
		//query("UPDATE is_pedidos SET exportado_erp = 'S' WHERE numreg = '$numreg'");
		$num_include = false;
		//echo $numreg;
		$num_pedido_exportado = $num_pedido_exportado * 1 + 1;
		echo '</div>';
		if($exportado == 'OK'){
			//query("UPDATE is_exp_ped SET exportado = 'S' WHERE numreg = '".$ar_pessoa['numreg']."' LIMIT 1");
			query("UPDATE is_pessoas SET exportado_erp = 'S',data_exportacao=NOW() WHERE id_pessoa = '".$ar_pessoa['id_pessoa']."' LIMIT 1");
		}

	}
	else{
		echo '<div style="height:30px; border: 1px dashed #FF0000;background:#C1FFC1"><h3>Pedido Não Exportado Por Não Haver Itens; Exportado Complemento e Brinde</h3>';
		echo '<br><strong>Cód Pedido:</strong> '.$ar_pedido['id_pedido'];
		echo '<br><strong>Cód Pedido Propagandista:</strong> '.$ar_pedido['id_pedido_repr'];
		echo '<br><strong>Cód Cliente:</strong> '.$ar_pedido['id_empresa'];
		echo '<br><strong>Cód Cliente:</strong> '.search_name('is_pessoas','id_pessoa','razao_social_nome',$ar_pedido['id_empresa']);
		
		echo '</div>';
	}
}

//$arquivo = fopen($param_dir."pedidos_varios.lst","w+");

//fwrite($arquivo,$exportar);

//fclose($arquivo);
?>
<table width="100%%" border="1" cellspacing="5" cellpadding="0">
	<tr>
		<td colspan="9"><h3>Total de Pedidos Exportados: <?=$num_pedido_exportado;?></h3> </td>
	</tr>
	
</table>
