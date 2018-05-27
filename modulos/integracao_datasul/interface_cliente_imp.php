<?
function dataTrat( $data ) { 
  if (strlen($data) != 8 ) { $data = ''; }
  if ($data) { $data = substr($data,8,2).'-'.substr($data,3,2).'-'.substr($data,0,2); }
  return $data;
}
function yesnoTrat( $valor ) {
	//Função desativada por motivo de ajuste no layour por parte da Datasul
  //if ($valor == 'y') { $valor = '1'; } else  { $valor = '2'; } 
  return $valor;
}

set_time_limit(0);
include"../conecta.php";
$ar_diretorio = farray(query("SELECT * FROM is_parametros_sistema WHERE id_parametro = 'IMP_CLI_DIR'"));
$param_dir = $ar_diretorio['parametro']; // 'c:/appserv/www/oasis-rnl/erptxt/';
$ar_diretorio_move = farray(query("SELECT * FROM is_parametros_sistema WHERE id_parametro = 'CLI_IMP_MOVE_DIR'"));
$param_dir_move = $ar_diretorio_move['parametro'];
$diretorio = opendir($param_dir);
$total_insert = 0;
$total_update = 0;
$total_n_imp = 0;
$reg_n_imp = '';
$qtde_arquivos = 0;
while ($arquivo = readdir($diretorio)) {
    if(!is_dir($arquivo)){
		if(file_exists($param_dir.$arquivo)) {
			$qtde_arquivos = $qtde_arquivos + 1;
			$linha = file($param_dir.$arquivo);
			$ql = count($linha);
			$new_line = '';
			for($i2=0;$i2<$ql;$i2++){
				$new_line .= $linha[$i2];
			}
			$new_line = str_replace(chr(13),' ',$new_line);
			$new_line = str_replace(chr(10),'',$new_line);
			$linha[0] = $new_line;
			//Verfica se há texto no arquivo
            $id_empresa = trim(substr($linha[7],1009,9));
			if($ql > 0){
				$quant_insert = 0;
				$quant_update = 0;
				$linhai = '';
				$i = 0; $z = 1;

				$pais = trim(addslashes(substr($linha[$i],201-$z,20)));
				$dts_cod_transportador_padrao = trim(substr($linha[$i],255-$z,5));
				$ramo = trim(substr($linha[$i],270-$z,12));
				$dts_compras_no_periodo = dataTrat(trim(substr($linha[$i],325-$z,10)));
				$dts_contribuinte_icms = yesnoTrat(trim(substr($linha[$i],339-$z,1)));
				$dts_categoria = trim(substr($linha[$i],342-$z,3));
				$dts_bonificacao_desc_pdr_cli = trim(substr($linha[$i],352-$z,5));
				$dts_abr_ava_credito = trim(substr($linha[$i],357-$z,1));
				$dts_limite_credito = trim(substr($linha[$i],360-$z,11));
				$dts_dt_limite_credito = dataTrat(trim(substr($linha[$i],371-$z,10)));
				$dts_pct_max_fat_por_periodo = trim(substr($linha[$i],379-$z,3));
				$dts_portador = trim(substr($linha[$i],382-$z,5));
				$dts_modalidade = trim(substr($linha[$i],387-$z,2));
				$dts_aceita_faturamento_parcial = yesnoTrat(trim(substr($linha[$i],389-$z,1)));
				$dts_indicador_credito = trim(substr($linha[$i],390-$z,1));
				$dts_agencia_cliente_fornecedor = trim(substr($linha[$i],605-$z,7));
				$dts_num_titulos = trim(substr($linha[$i],613-$z,8));
				$dts_num_dias = trim(substr($linha[$i],621-$z,8));
				$dts_pct_max_canel_qtde_aberto = trim(substr($linha[$i],629-$z,4));
				$dts_dt_ult_nota_fiscal_emitida = dataTrat(trim(substr($linha[$i],633-$z,8)));
				$dts_emite_bloquete_titulo = yesnoTrat(trim(substr($linha[$i],641-$z,1)));
				$dts_emite_etiq_correspondencia = yesnoTrat(trim(substr($linha[$i],642-$z,1)));
				$dts_valores_recebimento = yesnoTrat(trim(substr($linha[$i],643-$z,1)));
				$dts_gera_aviso_debito = yesnoTrat(trim(substr($linha[$i],644-$z,1)));
				$dts_portador_preferencial = trim(substr($linha[$i],645-$z,5));
				$dts_modalidade_preferencial = trim(substr($linha[$i],650-$z,2));
				$dts_baixa_nao_acatada = trim(substr($linha[$i],652-$z,3));
				$dts_ct_corrente_cliente_fornecedor = trim(substr($linha[$i],655-$z,10));
				$dts_dgt_conta_corrente_cliente_fornecedor = trim(substr($linha[$i],665-$z,2));
				$dts_gera_dif_preco = trim(substr($linha[$i],717-$z,1));
				$dts_tab_precos = trim(substr($linha[$i],718-$z,8));
				$dts_indicador_avaliacao = trim(substr($linha[$i],726-$z,1));
				$dts_usuario_libera_credito = trim(substr($linha[$i],727-$z,12));
				$dts_vencimento_domingo = trim(substr($linha[$i],739-$z,1));
				$dts_vencimento_sabado = trim(substr($linha[$i],740-$z,1));
				$cnpj_cpf_cob = trim(substr($linha[$i],741-$z,19));
				$dts_cx_postal_cob = trim(substr($linha[$i],871-$z,10));
				$ie_rg_cob = trim(substr($linha[$i],881-$z,19));
				$dts_banco_cliente_fornecedor = trim(substr($linha[$i],900-$z,3));
				$dts_prox_aviso_debito = trim(substr($linha[$i],903-$z,6));
				$dts_vencimento_feriado = trim(substr($linha[$i],910-$z,1));
				$dts_tipo_pagamento = trim(substr($linha[$i],911-$z,2));
				$dts_tipo_cobranca_despesas = trim(substr($linha[$i],913-$z,1));
				$dts_im = trim(substr($linha[$i],914-$z,19));
				$dts_tipo_desp_padrao = trim(substr($linha[$i],933-$z,3));
				$dts_tipo_receita_padrao = trim(substr($linha[$i],936-$z,3));
				$dts_cond_pagto = trim(substr($linha[$i],963-$z,3));
				$dts_num_meses_inativo = trim(substr($linha[$i],996-$z,2));
				$dts_instituicao_bancaria = trim(substr($linha[$i],998-$z,3));
				$dts_instituicao_bancaria2 = trim(substr($linha[$i],1001-$z,3));
				$dts_natureza_interestadual = trim(substr($linha[$i],1004-$z,6));
				$dts_id_pessoa_cob = trim(substr($linha[$i],1019-$z,9));
				$dts_indicador_avaliacao_embarque = trim(substr($linha[$i],1075-$z,1));
				$dts_canal_venda = trim(substr($linha[$i],1076-$z,3));
				$pais_cob = trim(substr($linha[$i],5079-$z,20));
				$dts_vencimento_igual_dt_fluxo = trim(substr($linha[$i],5125-$z,1));
				$dts_natureza_operacao = trim(substr($linha[$i],1004-$z,6));
				$dts_meio_emissao_pedido_compra = '';
				$dts_cx_postal = '';
				
				
				

				$razao = trim(addslashes(substr($linha[$i],39,40)));
				$cnpj = trim(substr($linha[$i],18,19));
				$id_relac = trim(substr($linha[$i],37,1));
				$nome_abreviado = trim(addslashes(substr($linha[$i],6,12)));
				$natureza = trim(substr($linha[$i],38,1));
				$grupo_cliente = trim(substr($linha[$i],357,2));
				$razao_social = trim(addslashes(substr($linha[$i],39,40)));
				$endereco = trim(addslashes(substr($linha[$i],79,40)));
				$bairro = trim(addslashes(substr($linha[$i],119,30)));
				$cidade = trim(addslashes(substr($linha[$i],149,25)));
				$uf = trim(substr($linha[$i],174,4));
				$cep = trim(substr($linha[$i],178,8));
				$id_ramo = trim(substr($linha[$i],269,12));
				$fax= trim(substr($linha[$i],281,15));
				$cep_cob = trim(substr($linha[$i],759,8));
				$uf_cob = trim(substr($linha[$i],771,2));
				$cidade_cob = trim(addslashes(substr($linha[$i],775,25)));
				$bairro_cob = trim(addslashes(substr($linha[$i],800,30)));
				$endereco_cob = trim(addslashes(substr($linha[$i],830,40)));
				$ie_rg = trim(substr($linha[$i],220,19));
				$tel1 = trim(substr($linha[$i],965,15));
				$tel2 = trim(substr($linha[$i],980,15));
				$id_representante = trim(substr($linha[$i],344,5));
				$id_empresa = trim(substr($linha[$i],1009,9));
				$id_empresa_cob = trim(substr($linha[$i],1018,9));
				$tipo = trim(substr($linha[$i],38,1));
				if($natureza == '1'){
					$tipo_natureza = 'F';
				}
				elseif($natureza == '2'){
					$tipo_natureza = 'J';
				}
				
				if($id_relac == '1'){
					$id_relac = 'CLI';
				}
				elseif($id_relac == '2'){
					continue;
				}
				elseif($id_relac == '3'){
					$id_relac = 'AMB';
				}
				
				$sql = mysql_query("SELECT * FROM is_pessoas WHERE NOT ISNULL(cnpj_cpf) AND cnpj_cpf <> '' AND cnpj_cpf = '".$cnpj."'") or die (mysql_error());
				$num_rows = mysql_num_rows($sql);
				if($cnpj == '' || $id_empresa == ''){
					$relatorio .= "Cliente Não Importado:-->".$id_empresa." CNPJ ou Cód Cliente Em Branco\n";
					$exec = false;
					$total_n_imp = $total_n_imp*1+1;
				}
				//elseif($id_relac != '1'){
					//$total_n_imp++;
					//$reg_n_imp .= $razao_social." ponto_virgula Empresa n&atilde;o &eacute; cliente separator_| ";
				//}
				elseif($cnpj != '' && $num_rows == 1){
					$sql = "UPDATE is_pessoas set id_usuario_alt = 'IMPORT*', dt_alteracao = '".date("Y-m-d")."', hr_alteracao = '".date("H:i")."', fantasia_apelido = '$razao', nome_abreviado = '$nome_abreviado', cnpj_cpf = '$cnpj',  id_relac = '$id_relac',  razao_social_nome = '$razao_social', endereco = '$endereco', bairro = '$bairro', cidade = '$cidade', uf = '$uf', cep = '$cep', id_ramo = '$id_ramo', fax = '$fax', cep_cob = '$cep_cob', uf_cob = '$uf_cob', cidade_cob = '$cidade_cob', bairro_cob = '$bairro_cob', endereco_cob = '$endereco_cob', ie_rg = '$ie_rg', tel1 = '$tel1', tel2 = '$tel2', id_pessoa = '$id_empresa' , tipo_pessoa = '$tipo_natureza', id_grupo_cliente = '$grupo_cliente' ".", pais = '$pais',
				dts_cod_transportador_padrao = '$dts_cod_transportador_padrao',
				dts_compras_no_periodo = '$dts_compras_no_periodo',
				dts_contribuinte_icms = '$dts_contribuinte_icms',
				dts_categoria = '$dts_categoria',
				dts_bonificacao_desc_pdr_cli = '$dts_bonificacao_desc_pdr_cli',
				dts_abr_ava_credito = '$dts_abr_ava_credito',
				dts_limite_credito = '$dts_limite_credito',
				dts_dt_limite_credito = '$dts_dt_limite_credito',
				dts_pct_max_fat_por_periodo = '$dts_pct_max_fat_por_periodo',
				dts_portador = '$dts_portador',
				dts_modalidade = '$dts_modalidade',
				dts_aceita_faturamento_parcial = '$dts_aceita_faturamento_parcial',
				dts_indicador_credito = '$dts_indicador_credito',
				dts_agencia_cliente_fornecedor = '$dts_agencia_cliente_fornecedor',
				dts_num_titulos = '$dts_num_titulos',
				dts_num_dias = '$dts_num_dias',
				dts_pct_max_canel_qtde_aberto = '$dts_pct_max_canel_qtde_aberto',
				dts_dt_ult_nota_fiscal_emitida = '$dts_dt_ult_nota_fiscal_emitida',
				dts_emite_bloquete_titulo = '$dts_emite_bloquete_titulo',
				dts_emite_etiq_correspondencia = '$dts_emite_etiq_correspondencia',
				dts_valores_recebimento = '$dts_valores_recebimento',
				dts_gera_aviso_debito = '$dts_gera_aviso_debito',
				dts_portador_preferencial = '$dts_portador_preferencial',
				dts_modalidade_preferencial = '$dts_modalidade_preferencial',
				dts_baixa_nao_acatada = '$dts_baixa_nao_acatada',
				dts_ct_corrente_cliente_fornecedor = '$dts_ct_corrente_cliente_fornecedor',
				dts_dgt_conta_corrente_cliente_fornecedor = '$dts_dgt_conta_corrente_cliente_fornecedor',
				dts_gera_dif_preco = '$dts_gera_dif_preco',
				dts_tab_precos = '$dts_tab_precos',
				dts_indicador_avaliacao = '$dts_indicador_avaliacao',
				dts_usuario_libera_credito = '$dts_usuario_libera_credito',
				dts_vencimento_domingo = '$dts_vencimento_domingo',
				dts_vencimento_sabado = '$dts_vencimento_sabado',
				cnpj_cpf_cob = '$cnpj_cpf_cob',
				dts_cx_postal_cob = '$dts_cx_postal_cob',
				ie_rg_cob = '$ie_rg_cob',
				dts_banco_cliente_fornecedor = '$dts_banco_cliente_fornecedor',
				dts_prox_aviso_debito = '$dts_prox_aviso_debito',
				dts_vencimento_feriado = '$dts_vencimento_feriado',
				dts_tipo_pagamento = '$dts_tipo_pagamento',
				dts_tipo_cobranca_despesas = '$dts_tipo_cobranca_despesas',
				dts_im = '$dts_im',
				dts_tipo_desp_padrao = '$dts_tipo_desp_padrao',
				dts_tipo_receita_padrao = '$dts_tipo_receita_padrao',
				dts_cond_pagto = '$dts_cond_pagto',
				dts_num_meses_inativo = '$dts_num_meses_inativo',
				dts_instituicao_bancaria = '$dts_instituicao_bancaria',
				dts_instituicao_bancaria2 = '$dts_instituicao_bancaria2',
				dts_natureza_interestadual = '$dts_natureza_interestadual',
				dts_id_pessoa_cob = '$dts_id_pessoa_cob',
				dts_indicador_avaliacao_embarque = '$dts_indicador_avaliacao_embarque',
				dts_canal_venda = '$dts_canal_venda',
				pais_cob = '$pais_cob',
				dts_vencimento_igual_dt_fluxo = '$dts_vencimento_igual_dt_fluxo',
				dts_natureza_operacao = '$dts_natureza_operacao',
				dts_meio_emissao_pedido_compra = '$dts_meio_emissao_pedido_compra',
				dts_cx_postal = '$dts_cx_postal'"." WHERE cnpj_cpf = '".$cnpj."'";
				
					$qry_exec = query($sql);
					if(!$qry_exec){
						$relatorio .= "Cliente Não Atualizado:-->".$id_empresa." SQL Executada".$sql."\n";
						$exec = false;
						$total_n_imp = $total_n_imp*1+1;
					}
					else{
						$exec = true;
						$total_update = $total_update*1+1;
						$total_clientes = $total_clientes*1+1;
					}
				}
				elseif($cnpj != '' && $num_rows == 0){
					$sql = "INSERT INTO is_pessoas (dt_cadastro,hr_cadastro,dt_alteracao,hr_alteracao,id_usuario_cad,id_usuario_alt,fantasia_apelido,cnpj_cpf, id_relac, razao_social_nome,nome_abreviado, endereco, bairro, cidade, uf, cep, id_ramo, cep_cob, uf_cob, cidade_cob, bairro_cob, endereco_cob, ie_rg, tel1, tel2, id_pessoa ,tipo_pessoa,id_representante,id_grupo_cliente,pais,dts_cod_transportador_padrao,dts_compras_no_periodo,dts_contribuinte_icms, dts_categoria, dts_bonificacao_desc_pdr_cli, 
dts_abr_ava_credito, dts_limite_credito, dts_dt_limite_credito, 	dts_pct_max_fat_por_periodo, dts_portador, dts_modalidade, 
dts_aceita_faturamento_parcial, dts_indicador_credito, dts_agencia_cliente_fornecedor, dts_num_titulos, dts_num_dias,	dts_pct_max_canel_qtde_aberto, 
dts_dt_ult_nota_fiscal_emitida,dts_emite_bloquete_titulo, dts_emite_etiq_correspondencia, dts_valores_recebimento, 	dts_gera_aviso_debito, 
dts_portador_preferencial, dts_modalidade_preferencial, dts_baixa_nao_acatada, dts_ct_corrente_cliente_fornecedor, 
dts_dgt_conta_corrente_cliente_fornecedor, dts_gera_dif_preco, dts_tab_precos, dts_indicador_avaliacao, dts_usuario_libera_credito, 
dts_vencimento_domingo, dts_vencimento_sabado, cnpj_cpf_cob, dts_cx_postal_cob,	ie_rg_cob, dts_banco_cliente_fornecedor, 
dts_prox_aviso_debito, dts_vencimento_feriado, dts_tipo_pagamento, dts_tipo_cobranca_despesas, dts_im, 
dts_tipo_desp_padrao, 	dts_tipo_receita_padrao, dts_cond_pagto, dts_num_meses_inativo, dts_instituicao_bancaria, 
dts_instituicao_bancaria2,	dts_natureza_interestadual, dts_id_pessoa_cob, dts_indicador_avaliacao_embarque, 
dts_canal_venda, pais_cob, dts_vencimento_igual_dt_fluxo, dts_natureza_operacao, dts_meio_emissao_pedido_compra, dts_cx_postal)values('".date("Y-m-d")."','".date("H:i")."','".date("Y-m-d")."','".date("H:i")."','IMPORT*','IMPORT*','$razao', '$cnpj', '$id_relac', '$razao_social', '$nome_abreviado' , '$endereco', '$bairro', '$cidade', '$uf', '$cep', '$id_ramo', '$cep_cob', '$uf_cob', '$cidade_cob', '$bairro_cob', '$endereco_cob', '$ie_rg', '$tel1', '$tel2', '$id_empresa','$tipo_natureza','$id_representante','$grupo_cliente'".",'$pais','$dts_cod_transportador_padrao','$dts_compras_no_periodo','$dts_contribuinte_icms','$dts_categoria','$dts_bonificacao_desc_pdr_cli','$dts_abr_ava_credito','$dts_limite_credito','$dts_dt_limite_credito','$dts_pct_max_fat_por_periodo','$dts_portador','$dts_modalidade','$dts_aceita_faturamento_parcial','$dts_indicador_credito','$dts_agencia_cliente_fornecedor','$dts_num_titulos','$dts_num_dias','$dts_pct_max_canel_qtde_aberto','$dts_dt_ult_nota_fiscal_emitida','$dts_emite_bloquete_titulo','$dts_emite_etiq_correspondencia','$dts_valores_recebimento','$dts_gera_aviso_debito','$dts_portador_preferencial','$dts_modalidade_preferencial','$dts_baixa_nao_acatada','$dts_ct_corrente_cliente_fornecedor',' $dts_dgt_conta_corrente_cliente_fornecedor','$dts_gera_dif_preco','$dts_tab_precos','$dts_indicador_avaliacao','$dts_usuario_libera_credito','$dts_vencimento_domingo','$dts_vencimento_sabado','$cnpj_cpf_cob','$dts_cx_postal_cob','$ie_rg_cob','$dts_banco_cliente_fornecedor','$dts_prox_aviso_debito','$dts_vencimento_feriado','$dts_tipo_pagamento','$dts_tipo_cobranca_despesas','$dts_im','$dts_tipo_desp_padrao','$dts_tipo_receita_padrao','$dts_cond_pagto','$dts_num_meses_inativo','$dts_instituicao_bancaria','$dts_instituicao_bancaria2','$dts_natureza_interestadual','$dts_id_pessoa_cob','$dts_indicador_avaliacao_embarque','$dts_canal_venda','$pais_cob','$dts_vencimento_igual_dt_fluxo','$dts_natureza_operacao','$dts_meio_emissao_pedido_compra','$dts_cx_postal'".")";
					$qry_exec = query($sql);
					
					if(!$qry_exec){
						$relatorio .= "Cliente Não Importado:--> ".$id_empresa." SQL Executada".$sql."\n";
						$exec = false;
						$total_n_imp = $total_n_imp*1+1;
					}
					else{
						$exec = true;
						$total_insert = $total_insert*1+1;
						$total_clientes = $total_clientes*1+1;
					}
				}
			}
			if($exec != false){
				if(!file_exists($param_dir_move.$arquivo)){
					rename($param_dir.$arquivo,$param_dir_move.$arquivo);
				}
				else{
					rename($param_dir.$arquivo,$param_dir_move.date("YmdHis").$arquivo);
				}
			}
			$exec = true;
		}
	}
}
if(!empty($relatorio)){
	$ar_diretorio_relat = farray(query("SELECT * FROM is_parametros_sistema WHERE id_parametro = 'CLI_IMP_RELAT_DIR'"));
	$param_dir_relat = $ar_diretorio_relat['parametro'];
	$fp = fopen($param_dir_relat."Erro_Importacao_Cliente_".date("Ymd_His").".txt","w+");
	fwrite($fp,$relatorio);
	fclose($fp);
}
?>