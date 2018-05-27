<?
function dten2brsb($dt){
	return substr($dt,6,2).substr($dt,6,2).substr($dt,0,2);
}
function dtbr2enimp($dt){
	if($dt != ''){
		return substr($dt,4,4).'-'.substr($dt,2,2).'-'.substr($dt,0,2);
	}
	else{
		return '';
	}
}
function tratavlimp($vl){
	$vl = str_replace('.','',$vl);
	$vl = str_replace(',','.',$vl);
	return $vl;
}
$z = 1;
set_time_limit(0);
include"../conecta.php";
require("../functions.php");
$ar_diretorio = farray(mysql_query("SELECT * FROM is_parametros_sistema WHERE id_parametro = 'IMP_NF_DIR'"));
$param_dir = $ar_diretorio['parametro'];
$ar_diretorio_move = farray(mysql_query("SELECT * FROM is_parametros_sistema WHERE id_parametro = 'IMP_NF_DIR_MOVE'"));
$param_dir_move = $ar_diretorio_move['parametro'];
$diretorio = opendir($param_dir);
$erro = '';
while ($arquivo = readdir($diretorio)) {
    if(!is_dir($arquivo)){
		if(file_exists($param_dir.$arquivo)) {
			$linha = file($param_dir.$arquivo);
			for($i=0;$i<count($linha);$i++){
				$id_pedido = trim(substr($linha[$i],10-$z,12));
				if(empty($id_pedido)){
					$erro .= 'Pedido Deve ser Informado Linha: '.($i+1).chr(13).chr(10);
					continue;
				}
				$total_nf_importadas = $total_nf_importadas + 1;
				$n_nota_fiscal = trim(substr($linha[$i],22-$z,7));
				$dt_nota_fiscal = dtbr2enimp(trim(substr($linha[$i],79-$z,8)));
				$transportadora = trim(substr($linha[$i],87-$z,12));
				$dt_cancelamento = trim(substr($linha[$i],99-$z,8));
				$id_estabelecimento = trim(substr($linha[$i],29-$z,3));
				$valor = tratavlimp(trim(substr($linha[$i],32-$z,17)));
				$peso_bruto = tratavlimp(trim(substr($linha[$i],49-$z,15)));
				$peso_liquido = tratavlimp(trim(substr($linha[$i],64-$z,15)));
			
				if($dt_cancelamento == '? ?   ?'){
					$dt_cancelamento = '';
				}
				else{
					$dt_cancelamento = dtbr2enimp($dt_cancelamento);
				}
				$ar_nf = array(
				'dt_cadastro' => date("Y-m-d"),
				'hr_cadastro' => date("H:i"),
				'id_usuario_cad' => 'IMPORT',
				'id_nf' => $n_nota_fiscal,
				'id_pedido' => $id_pedido,
				'dt_emissao' => $dt_nota_fiscal,
				'id_transporte' => $transportadora,
				'dt_cancelamento' => $dt_cancelamento,
				'id_estabelecimento' => $id_estabelecimento,
				'valor' => $valor,
				'peso_bruto' => $peso_bruto,
				'peso_liquido' => $peso_liquido
				);
				
				$campos = array();
				$valores = array();
				$campos_valores = array();
				foreach($ar_nf as $k => $v){
					if($v != ''){
						$campos[] = $k;
						$valores[]= $v;
						$campos_valores[] = "`".$k."` = '".$v."'";
					}
				}
				$qry_nf_existe = mysql_query("SELECT * FROM is_nf WHERE id_nf = '".$n_nota_fiscal."'");
				if(mysql_num_rows($qry_nf_existe) == 0){
					$sql = "INSERT INTO is_nf (`".implode('`,`',$campos)."`) VALUES ('".implode("','",$valores)."')";
				}
				else{
					$sql = "UPDATE is_nf SET ".implode(",",$campos_valores)." WHERE id_nf = '".$n_nota_fiscal."'";
				}
				#echo $sql."<hr>";
				$qry = mysql_query($sql);
				if(!$qry){
					echo mysql_error();
				}
			}
			#rename($param_dir.$arquivo,$param_dir_move."NF_IMPORTADO ".date("Ymd_his"));
		}
	}
}
?>
