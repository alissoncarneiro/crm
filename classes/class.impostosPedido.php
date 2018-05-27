<?php
/*
 * class.impostosPedido.php
 * Autor: Alex
 * 23/09/2010 14:20:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class impostosPedido{
	public function getValorST($vl_unitario,$id_produto,$uf_entrega){
		/*
		 * Reunindo os dados necessÃ¡rios para os cÃ¡lculos
		 * caso algum dos valores nÃ£o seja econtrado, retorna o valor 0,00
		 */
		$qry_produto = mysql_query("SELECT id_classificacao_fiscal FROM is_produtos WHERE id_produto = '".$id_produto."'"); 
		if(mysql_num_rows($qry_produto) == 0){
			return 0;	
		}
		$ar_produto = mysql_fetch_array($qry_produto);
		$qry_icms = mysql_query("SELECT pct_icms_interno,pct_icms_externo FROM is_aliquota_icms WHERE uf = '".$uf_entrega."'");
		if(mysql_num_rows($qry_icms) == 0){
			return 0;	
		}
		$ar_icms = mysql_fetch_array($qry_icms);
		$qry_iva = mysql_query("SELECT pct_iva FROM is_aliquota_iva WHERE id_classificacao_fiscal = '".$ar_produto['id_classificacao_fiscal']."'"); 
		if(mysql_num_rows($qry_iva) == 0){
			return 0;	
		}
		$ar_iva = mysql_fetch_array($qry_iva);
		/*
		 * Iniciando os cÃ¡lculos
		 */
		$pct_iva = $ar_iva['pct_iva'];
		$pct_icms_interno = $ar_icms['pct_icms_interno'];
		$pct_icms_externo = $ar_icms['pct_icms_externo'];
		
		$vl_unitario_com_iva = uM::uMath_vl_mais_pct($pct_iva,$vl_unitario);
		$vl_icms_interno = uM::uMath_pct_de_valor($pct_icms_interno,$vl_unitario_com_iva);
		$vl_icms_externo = uM::uMath_pct_de_valor($pct_icms_externo,$vl_unitario);
		
		$vl_st = $vl_icms_interno - $vl_icms_externo;

		return $vl_st;
	}
}
?>