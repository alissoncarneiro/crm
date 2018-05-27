<?
##########
#ANOTACOES
##########
//Registro 7 - Campo 9 Definido com quantidade de caixas
//Registro 7 - Campo 14 Não Preenchido
//Registro 11 -  Há campos obrigatórios
//Transformar data en para br sem barras

if($inclusao != true){
	require("../conecta.php");
	require("../functions.php");
	$numreg = '14';
}

$ar_diretorio = farray(query("SELECT * FROM is_parametros_sistema WHERE id_parametro = 'EXP_PED_DIR'"));
$param_dir = $ar_diretorio['parametro'];

//Somente inlcui as funcoes se for o primeiro include do p4_salva_dados.php
if($num_include == true){
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

function add_item($id_cliente,$id_pedido_cli,$id_produto,$natureza_operacao){
	$ar_pedido = farray(query("SELECT * FROM is_pedidos WHERE id_pedido_cli = '".$id_pedido_cli."'"));
	$ar_cliente = farray(query("SELECT * FROM is_pessoas WHERE id_pessoa = '".$id_cliente."'"));
	$ar_item = farray(query("SELECT * FROM is_pedidos_itens WHERE id_pedido = '".$id_pedido_cli."' AND id_produto = '".$id_produto."'"));
	$exp .= chr(13).chr(10);
	//echo "SELECT * FROM is_pedidos_itens WHERE id_pedido = '".$id_pedido_cli."' AND id_produto = '".$id_produto."'<br>";
	$exp .= formata_string('07',2); //	1
	$exp .= formata_string($ar_cliente['nome_abreviado'],12); //	2
	$exp .= formata_string($id_pedido_cli,12); //	3
	$exp .= formata_string($ar_item['id_item'],5); //	4
	$exp .= formata_string($ar_item['id_produto'],16); //	5
	$exp .= formata_string('',8); //	6
	$exp .= formata_string('',2); //	7
	$exp .= formata_string('',8); //	8
	$exp .= formata_string(number_format($ar_item['total_unid'],4,'.',''),11); //	9
	$exp .= formata_string(number_format($ar_item['vl_tabela'],5,'.',''),14); //	10
	$exp .= formata_string('',50); //	11
	$exp .= formata_string('',4); //	12
	$exp .= formata_string('',8); //	13
	$exp .= formata_string($ar_pedido['natureza_operacao'],6); //	14
	$exp .= formata_string('',6); //	15
	$exp .= formata_string('',1); //	16
	$exp .= formata_string('',1); //	17
	$exp .= formata_string(dten2brsb($ar_pedido['dt_entrega']),8); //	18
	$exp .= formata_string('',2); //	19
	$exp .= formata_string('',12); //	20
	$exp .= formata_string('',8); //	21
	$exp .= formata_string('',7); //	22
	$exp .= formata_string($ar_item['pct_desconto'],50); //	23
	$exp .= formata_string('',11); //	24
	$exp .= formata_string('',1); //	25
	$exp .= formata_string('',5); //	26
	$exp .= formata_string('',5); //	27
	$exp .= formata_string('',5); //	28
	$exp .= formata_string('',11); //	29
	$exp .= formata_string('',11); //	30
	$exp .= formata_string('',5); //	31
	$exp .= formata_string('',14); //	32
	$exp .= formata_string('',14); //	33
	$exp .= formata_string('',14); //	34
	$exp .= formata_string('',14); //	35
	$exp .= formata_string('',14); //	36
	$exp .= formata_string('',2); //	37
	$exp .= formata_string('',17); //	38
	$exp .= formata_string('',12); //	39
	$exp .= formata_string('',10); //	40
	$exp .= formata_string("LINHA 1:".$ar_pedido['linha1']." LINHA 2:".$ar_pedido['linha2'],2000); //	41

	############################################
	//LINHA - REGISTRO 08
	############################################
	$exp .= chr(13).chr(10);
	$exp .= formata_string('08',2); //	1
	$exp .= formata_string($ar_cliente['nome_abreviado'],12); //	2
	$exp .= formata_string($id_pedido_cli,12); //	3
	$exp .= formata_string($ar_item['id_item'],5); //	4
	$exp .= formata_string($ar_item['id_item'],5); //	5
	$exp .= formata_string($ar_item['id_produto'],16); //	6
	$exp .= formata_string('',8); //	7
	$exp .= formata_string('',1); //	8
	$exp .= formata_string(dten2brsb($ar_pedido['dt_entrega']),8); //	9
	$exp .= formata_string('',6); //	10
	$exp .= formata_string('',8); //	11
	$exp .= formata_string('',6); //	12
	$exp .= formata_string('',11); //	13
	$exp .= formata_string('',1); //	14
	$exp .= formata_string('',2000); //	15
/*
	############################################
	//LINHA - REGISTRO 11
	############################################
	$exp .= chr(13).chr(10);
	$exp .= formata_string('11',2); //	1
	$exp .= formata_string($id_pedido_cli,12); //	2
	$exp .= formata_string($ar_item['id_produto'],16); //	3
	$exp .= formata_string('',8); //	4
	$exp .= formata_string(number_format($ar_item['total_unid'],4,',',''),11); //	5
	$exp .= formata_string('',14); //	6
	$exp .= formata_string('',4); //	7
	$exp .= formata_string($ar_item['id_item'],5); //	8
*/
	return $exp;
}


//Remover acentos
function retiraAcentos($string){
	$array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç" , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
	$array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" , "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
	return str_replace($array1, $array2,$string);
}
function max_id($tabela,$campo,$mais=1){
	$ar_max = farray(query("SELECT MAX(".$campo."*1) AS Max FROM ".$tabela));
	$qry_mais = query("SELECT ".$campo." FROM ".$tabela." WHERE ".$campo." = '".($ar_max['Max'] + $mais)."'");
	if(numrows($qry_mais) == 0){
		return ($ar_max['Max'] + $mais);
	}
	else{
		max_id($tabela,$campo,($ar_max['Max'] + $mais + 1));
	}
}
//Fim do se for o primeiro include
}


$ar_pedido = farray(query("SELECT * FROM is_pedidos WHERE numreg = '".$numreg."'"));
$ar_cliente = farray(query("SELECT * FROM is_pessoas WHERE id_pessoa = '".$ar_pedido['id_empresa']."'"));

$codigo_moeda = search_name('is_parametros_sistema','id_parametro','parametro','PED_EXP_REG01_COD_MOEDA');
$especie_pedido = search_name('is_parametros_sistema','id_parametro','parametro','PED_EXP_REG01_ESP_PED');
$tipo_preco = search_name('is_parametros_sistema','id_parametro','parametro','PED_EXP_REG01_TIPO_PRECO');
$natureza_operacao = $ar_pedido['natureza_operacao'];

//$nome_abreviado_representante = 'RNL';//search_name('is_usuarios','id_pessoa','nome_abreviado',$ar_pedido['id_representante']);
//$ar_pedido['id_representante'] = '99999';


$nome_abreviado_representante = search_name('is_usuarios','id_usuario','nome_abreviado',$ar_pedido['id_representante']);


if(!empty($ar_pedido['id_pedido_repr'])){
	$id_pedido_cli = $ar_pedido['id_pedido_repr'].$tipo_pedido;
}
else{
	$id_pedido_cli = $ar_pedido['id_pedido_cli'].$tipo_pedido;
}



$cnpj = $ar_cliente['cnpj_cpf'];
$cnpj = str_replace('.','',$cnpj);
$cnpj = str_replace('/','',$cnpj);
$cnpj = str_replace('-','',$cnpj);

$exp = '';
############################################
//LINHA - REGISTRO 01
############################################
$exp .= formata_string('01',2); //	1
$exp .= formata_string($ar_cliente['nome_abreviado'],12); //	2
$exp .= formata_string($id_pedido_cli,12); //	3
$exp .= formata_string($ar_cliente['id_pessoa'],9); //	4
$exp .= formata_string($ar_cliente['cnpj_cpf'],19); //	5
$exp .= formata_string('',12); //	6
$exp .= formata_string(dten2brsb($ar_pedido['dt_pedido']),8); //	7
$exp .= formata_string('',8); //	8
$exp .= formata_string('',8); //	9
$exp .= formata_string('',2); //	10
$exp .= formata_string($ar_pedido['id_cond_pagto'],3); //	11
$exp .= formata_string('',8); //	12
$exp .= formata_string('',3); //	13
$exp .= formata_string('',2); //	14
$exp .= formata_string($tipo_preco,2); //	15
$exp .= formata_string($codigo_moeda,2); //	16
$exp .= formata_string('',5); //	17
$exp .= formata_string('',5); //	18
$exp .= formata_string('',2); //	19
$exp .= formata_string('',1); //	20
$exp .= formata_string('',12); //	21
$exp .= formata_string('',12); //	22
$exp .= formata_string($nome_abreviado_representante,12); //	23
$exp .= formata_string('',25); //	24
$exp .= formata_string('',3); //	25
$exp .= formata_string('',12); //	26
$exp .= formata_string('',25); //	27
$exp .= formata_string('',154); //	28
$exp .= formata_string($natureza_operacao,6); //	29
$exp .= formata_string('',5); //	30
$exp .= formata_string('',1); //	31
$exp .= formata_string('',12); //	32
$exp .= formata_string('',50); //	33
$exp .= formata_string('',4); //	34
$exp .= formata_string('',3); //	35
$exp .= formata_string('',8); //	36
$exp .= formata_string($especie_pedido,2); //	37
$exp .= formata_string('',3); //	38
$exp .= formata_string('',3); //	39
$exp .= formata_string('',3); //	40
$exp .= formata_string('',3); //	41
$exp .= formata_string('',7); //	42
$exp .= formata_string('',1); //	43
$exp .= formata_string('',5); //	44
$exp .= formata_string('',50); //	45
$exp .= formata_string('',7); //	46
$exp .= formata_string('',2); //	47
$exp .= formata_string('',1); //	48
$exp .= formata_string('',12); //	49
$exp .= formata_string('',12); //	50
$exp .= formata_string('',5); //	51
$exp .= formata_string('',12); //	52



############################################
//LINHA - REGISTRO 02
############################################
$exp .= chr(13).chr(10);
$exp .= formata_string('02',2); //	51
$exp .= formata_string('',2000); //	52



############################################
//LINHA - REGISTRO 03
############################################
$exp .= chr(13).chr(10);
$exp .= formata_string('03',2); //	51
$exp .= formata_string('',2000); //	52


############################################
//LINHA - REGISTRO 04
############################################
$exp .= chr(13).chr(10);
$exp .= formata_string('04',2); //	53
$exp .= formata_string($ar_pedido['obs'],2000); //	54


############################################
//LINHA - REGISTRO 05
############################################
$exp .= chr(13).chr(10);
$exp .= formata_string('05',2); //	1
$exp .= formata_string('',2); //	2
$exp .= formata_string('',8); //	3
$exp .= formata_string('',8); //	4
$exp .= formata_string('',8); //	5
$exp .= formata_string('',8); //	6
$exp .= formata_string('',8); //	7
$exp .= formata_string('',8); //	8
$exp .= formata_string('',11); //	9
$exp .= formata_string('',11); //	10
$exp .= formata_string('',11); //	11
$exp .= formata_string('',11); //	12
$exp .= formata_string('',11); //	13
$exp .= formata_string('',11); //	14
$exp .= formata_string('',2000); //	15



############################################
//LINHA - REGISTRO 06
############################################
$exp .= chr(13).chr(10);
$exp .= formata_string('06',2); //	1
$exp .= formata_string('',9); //	2
$exp .= formata_string('',3); //	3
$exp .= formata_string('',1); //	4
$exp .= formata_string('',8); //	5
$exp .= formata_string('',5); //	6
$exp .= formata_string('',11); //	7
$exp .= formata_string('',2); //	8
$exp .= formata_string('',3); //	9
$exp .= formata_string('',2000); //	10

$qry_item = query("SELECT * FROM is_pedidos_itens WHERE id_pedido = '".$ar_pedido['id_pedido']."' ORDER BY id_item ASC") or die (mysql_error());
while($ar_item = farray($qry_item)){
	############################################
	//LINHA - REGISTRO 07
	############################################
	//echo $id_pedido_cli;
	if($ar_item['id_item'] != "018005" && $ar_item['id_item'] != "018002"){
		$exp .= add_item($ar_cliente['id_pessoa'],$ar_pedido['id_pedido'],$ar_item['id_produto'],$natureza_operacao);
	}
}


############################################
//LINHA - REGISTRO 09
###########################################
$exp .= chr(13).chr(10);
$exp .= formata_string('09',2); //	1
$exp .= formata_string($id_pedido_cli,12); //	2
$exp .= formata_string($ar_pedido['id_representante'],5); //	3
$exp .= formata_string('',5); //	4
$exp .= formata_string('',5); //	5
$exp .= formata_string('1',1); //	6


############################################
//LINHA - REGISTRO 10
############################################
$exp .= chr(13).chr(10);
$exp .= formata_string('10',2); //	1
$exp .= formata_string($id_pedido_cli,12); //	2
$exp .= formata_string('',7); //	3
$exp .= formata_string('',2); //	4
$exp .= formata_string('',3); //	5
$exp .= formata_string('',3); //	6
$exp .= formata_string('',8); //	7



if($inclusao != true){
	$arquivo = fopen($param_dir."pedido_".$ar_pedido['id_pedido'].".lst","w+");
}
else{
	$arquivo = fopen($param_dir."pedido_".$ar_pedido['id_pedido'].".lst","w+");
}

fwrite($arquivo,$exp.chr(13).chr(10));

fclose($arquivo);
?>