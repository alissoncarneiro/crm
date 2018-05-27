<?php
	
	set_time_limit(0);
	
	//formata datas SQL server para Mysql
	function converteData($data_sqlserver)
	{
	if ($data_sqlserver == ""){
		return "0000-00-00 00:00:00";
		
		} else{
	
		$dtTimeStamp = strtotime($data_sqlserver);
		$dataConvertida = date('Y-m-d H:i:s', $dtTimeStamp);
	
		return $dataConvertida;
	
		}
	
	}
	
	// ano mes dia, referentes a data completa aaaa-mm-dd hh:mm:ss
	function amd($data){
	
		$data_aniver = converteData($data);		
		$amd = explode("-",$data_aniver);		
		$dia = explode(" ",$amd[2]);
		return $amd[0]."-".$amd[1]."-".$dia[0];
	}
	//----------------------------------

	//Conecta no banco de dados SQL Server da Serve Festa(local)
	mssql_connect("FELIPE-TOSHIBA\SQLEXPRESS", "sa", "sa");
	mssql_select_db("SF");
	
	//Conecta no banco de dados MySQL do CRM (local)
	mysql_connect("localhost", "root", "root");
	mysql_select_db("db_marketing");
	
	//Array com as condições para o valor ser NULL
	$cond = array('_:_',
				  '__:__',
				  ':',
				  '-',
				  ' ',
				  '',				  
				  '0000-00-00 00:00:00',
				  '00:00:00',
				  '0000-00-00',
				  ';',
				  '--',
				  '0-;-;-',
				  '-   -;-;-',
				  '-;-;-',
				  '.0',
				  '0.0',
				  '0',
				  '00',
				  '000',
				  '0000',
				  '00000000000000'
		);

	if($_POST['confirma_clientes']!=""){
	
	mysql_query("TRUNCATE TABLE emitente");
	mysql_query("TRUNCATE TABLE is_emitente_comp");
	
	$query_cadcliente = mssql_query("SELECT * FROM CAD_Cliente");
	
	while($res = mssql_fetch_array($query_cadcliente)){

	$array = array(
		'cod-emitente' =>		$res['codigo'],
		'cgc' =>				$res['cpf'],
		'nome-emit' =>			$res['nome'],
		'endereco' =>			$res['endereco']." ".$res['numero']." ".$res['complemento'],
		'bairro' =>				$res['bairro'],
		'cidade' =>				$res['cidade'],
		'estado' =>				$res['uf'],
		'cep' =>				$res['CEP'],					
		'ins-estadual' =>		$res['rg'],
		'contato' =>			$res['contato'],
		'telefone' =>			$res['ddd1']."-".$res['fone1'].";".$res['ddd2']."-".$res['fone2'].";".$res['ddd_cel']."-".$res['num_cel'],
		'ramal' =>				$res['ramal1'].";".$res['ramal2'],
		'telefax' =>			$res['fax'],
		'ramal-fax' =>			$res['ramal3'],
		'lim-credito' =>		$res['credito_limite'],
		'observacoes' =>		$res['obs'],
		'cgc-cob' =>			$res['CGC'],
		'cep-cob' =>			$res['cep_com'],
		'estado-cob' =>			$res['UF_com'],
		'cidade-cob' =>			$res['cid_com'],
		'bairro-cob' =>			$res['bai_com'],
		'endereco-cob' =>		$res['end_com']." ".$res['num_com'],					
		'ins-est-cob' =>		$res['Insc_Est'],
		'e-mail' =>				$res['email']
	);

	$data_aniver = converteData($res['dt_nasc']);
	$amd = explode("-",$data_aniver);
	$dia = explode(" ",$amd[2]);
	
	$cad = explode("-",converteData($res['dt_cad']));
	$cad_dia = explode(" ",$cad[2]);
	$valores = $campos = array();
	
	$array_comp = array(
		'numreg' =>	            	"",
		'id_usuario_cad' =>			$res['usuario'],
		'dt_cadastro' =>			amd($res['dt_cad']),
		'hr_cadastro' =>			$cad_dia[1],
		'importado_data' =>			date("Y-m-d"),
		'importado_hora' =>			date("H:i"),
		'cod-emitente' =>			$res['codigo'],
		'dianascto' =>				$dia[0],
		'mesnascto' =>				$amd[1],
		'anonascto' =>				$amd[0],
		'estcivil' =>				$res['Est_civil'],
		'sexo' =>					$res['sexo'],
		'fantasia_apelido' =>		$res['Nome_Fantasia'],
		'ult_mov_balcao'=>			amd($res['ult_mov_balcao']),
		'ult_mov_festa'=>			amd($res['ult_mov_festa']),
		'churrascard'=>				$res['churrascard'],
		'data_churrascard'=>		amd($res['data_churrascard']),
		'credito_usado'=>			$res['credito_usado'],
		'qtd_orcam'=>				$res['qtd_orcam'],
		'qtd_ped'=>					$res['qtd_ped'],
		'NM_REGISTRO_VENDEDOR'=>	$res['NM_REGISTRO_VENDEDOR'],
		'profissao'=>				$res['profissao'],
		'proprietario'=>			$res['Proprietario'],
		'end_propr'=>				$res['End_propr'],
		'nome_empresa'=>			$res['Nome_Empresa'],
		'qtd_dependente'=>			$res['qtd_depende'],
		'grupo'=>					$res['grupo'],
		'bloqueado'=>				$res['bloqueado'],
		'etiqueta'=>				$res['etiqueta'],
		'cod_vendedor'=>			$res['Codigo_Vendedor']

);

	foreach($array as $k => $v){
	
		if($v!=""){
		$res = array_search(trim($v),$cond);
			if($res!="") {	
				$v = 'NULL';		
				$valores[] = $v;
			} else{
				$valores[] = "'".addslashes(trim($v))."'";
			}
		$campos[] = $k;
		}
	}
		
		$valores2 = $campos2 = array();
		
	foreach($array_comp as $k2 => $v2){

		if($v2!=""){
				$res = array_search(trim($v2),$cond);
			if($res!="") {	
					$v2 = 'NULL';
					$valores2[] = $v2;
				} else {
					 $valores2[] = "'".addslashes(trim($v2))."'";
					}
			 $campos2[] = $k2;
		 }
	}
		
		$sql12 = ("INSERT INTO emitente (`".implode('`,`',$campos)."`) VALUES(".implode(",",$valores).")");
		mysql_query($sql12) or die($sql12."<hr>".mysql_error());
		
		$sql11 = ("INSERT INTO is_emitente_comp (`".implode('`,`',$campos2)."`) VALUES(".implode(",",$valores2).")");
		mysql_query($sql11) or die($sql11."<hr>".mysql_error());
		
		$array = $array_comp = array();
		}
	}
	
	if($_POST['confirma_pedidos']!=""){

	$query_pedidos = mssql_query("SELECT * FROM VEN_PEDIDO");

		mysql_query("TRUNCATE TABLE `ped-venda`");
		mysql_query("TRUNCATE TABLE is_ped_venda_comp");
		mysql_query("TRUNCATE TABLE is_emitente_prospect");
		mysql_query("TRUNCATE TABLE is_emitente_prospect_comp");		
	
		while($ar_pedidos = mssql_fetch_array($query_pedidos)){

	$array_ped = array(
		'cod-emitente' =>				$ar_pedidos['FKND_CODCLI'],
		'nome-abrev' =>					$ar_pedidos['ATSV_NOME_CLI_TMP'],
		'nr-pedcli' =>					$ar_pedidos['PKND_PEDIDO'],
		'dt-emissao' =>					amd($ar_pedidos['ATDT_ORCAM']),
		'dt-entrega' =>					amd($ar_pedidos['ATDT_FESTA']),
		'local-entreg' =>				$ar_pedidos['ATSV_LOCAL'],
		'cod-sit-ped' =>				$ar_pedidos['ATSV_STATUS'],
		'observacoes' =>				$ar_pedidos['ATSV_OBS'],
		'vl-tot-ped' =>					$ar_pedidos['ATND_VALOR_PEDIDO'],
		'desc-reativa' =>				$ar_pedidos['ATSV_CREDITO'],
		'e-mail' =>						$ar_pedidos['ATSV_CONT_CLI_TMP'],
	);

	$array_ped_comp = array(		
		'cod-emitente' =>			$ar_pedidos['FKND_CODCLI'],		
		'numreg' =>					"",
		'importado_data' =>			date("Y-m-d"),
		'importado_hora' =>			date("H:i"),
		'retorno_festa' =>			amd($ar_pedidos['ATDT_RETORNO_FESTA']),
		'margem' =>					$ar_pedidos['ATND_MARGEM'],
		'valor_sugerido' =>			$ar_pedidos['ATND_VALOR_SUGERIDO'],
		'val_adicional' =>			$ar_pedidos['ATND_VAL_ADICIONAL'],
		'gerencia' =>				$ar_pedidos['ATSV_GERENCIA'],
		'saida_festa' =>			amd($ar_pedidos['ATDT_SAIDA_FESTA']),
		'cobrar' =>					$ar_pedidos['ATND_COBRAR'],
		'venc_orcam' =>				amd($ar_pedidos['ATDT_VCTO_ORCAM']),
		'atendente_desconto' =>		$ar_pedidos['ATND_DESCONTO'],
		'telefone_cliente' =>		$ar_pedidos['ATSV_FONE_CLI_TMP'],
		'id_cond_pagto' =>			$ar_pedidos['FKND_CODPAD'],
		'custo_tot' =>				$ar_pedidos['ATND_CUSTO_TOT'],
		'valor_tot' =>				$ar_pedidos['ATND_VALOR_TOT'],
		'tp_desc' =>				$ar_pedidos['ATND_TP_DESCONTO'],
		'hora_inicio' =>			$ar_pedidos['ATSV_HORA_INI'],
		'hora_fim' =>				$ar_pedidos['ATSV_HORA_FIM'],
		'atn_mot' =>				$ar_pedidos['ATND_MOTIVO'],
		'id_vendedor' =>			$ar_pedidos['FKND_VENDEDOR'],
		'perda_comissao' =>			$ar_pedidos['ATND_PERC_COMISSAO'],
		'valor_comissao' =>			$ar_pedidos['ATND_VALOR_COMISSAO'],
		'codevt' =>					$ar_pedidos['FKND_CODEVT'],
		'numpes' =>					$ar_pedidos['ATND_NUMPES'],
		'propaganda' =>				$ar_pedidos['ATND_PROPAGANDA'],
		'periocidade' =>			$ar_pedidos['ATND_PERIODICIDADE'],
		'atend_telemarketing' =>	amd($ar_pedidos['ATDT_TELEMARK']),
		'texto_pagto' =>			$ar_pedidos['ATSV_TEXTO_PAGTO'],
		'aprovacao' =>				amd($ar_pedidos['ATDT_APROVACAO']),
		'pess_repr' =>				$ar_pedidos['ATND_PESS_REPR'],
		'usuario_orcam' =>			$ar_pedidos['ATSV_USUARIO_ORCAM'],
		'usuario_pedido' =>			$ar_pedidos['ATSV_USUARIO_PEDIDO'],
		'orcam_antigo' =>			$ar_pedidos['ATND_ORCAM_ANTIGO'],
		'codocor' =>				$ar_pedidos['FKND_CODOCOR'],
		'contato_tmp' =>			$ar_pedidos['ATSV_CONTATO_TMP']
	);
		
		$valores3 = $campos3 = array();
		$valores4 = $campos4 = array();
		
	foreach($array_ped as $k3 => $v3){
		
		if($v3!=""){
			$res = array_search(trim($v3),$cond);
			if($res!="") {	
				$v3 = 'NULL';
				$valores3[] = $v3;
			} else{
			 	$valores3[] = "'".addslashes(trim($v3))."'";
			}
		 $campos3[] = $k3;
		 }
	}
		
	foreach($array_ped_comp as $k4 => $v4){
		
		if($v4!=""){
			$res = array_search(trim($v4),$cond);
			if($res!="") {	
				$v4 = 'NULL';
				$valores4[] = $v4;
			} else{
			 	$valores4[] = "'".addslashes(trim($v4))."'";
			}
		 $campos4[] = $k4;
		 }
	}		
			
	$array_prospects = array(
		'cod-emitente' =>		$ar_pedidos['FKND_CODCLI'],																											
		'nome-emit' =>			$ar_pedidos['ATSV_NOME_CLI_TMP'],							
		'e-mail' =>				$ar_pedidos['ATSV_CONT_CLI_TMP'],
		'telefone' =>  			$ar_pedidos['ATSV_FONE_CLI_TMP']							

	);
		
		$array_prospects_comp = array(
		'cod-emitente' =>			$ar_pedidos['FKND_CODCLI'],
		'numreg' =>	            	""

		);
		
		$valores5 = $campos5 = array();
		$valores6 = $campos6 = array();
		
	foreach($array_prospects as $k5 => $v5){
		
		if($v5!=""){
			$res = array_search(trim($v5),$cond);
			if($res!="") {	
				$v5 = 'NULL';
				$valores5[] = $v5;
			} else{
			 	$valores5[] = "'".addslashes(trim($v5))."'";
		}	
		 $campos5[] = $k5;
	 }
}
		
	foreach($array_prospects_comp as $k6 => $v6){
		
		if($v6!=""){
			$res = array_search(trim($v6),$cond);

		if($res!="") {	
				$v6 = 'NULL';
				$valores6[] = $v6;
			} else{
			 	$valores6[] = "'".addslashes(trim($v6))."'";
			}
		 $campos6[] = $k6;
		 }	
	}		

		if($valores4[0]=="'99999'"){
			
			//$emitente = mysql_fetch_array(mysql_query("SELECT `cod-emitente` FROM emitente ORDER BY `cod-emitente` DESC"));
			
			$prospects = mysql_fetch_array(mysql_query("SELECT `cod-emitente` FROM is_emitente_prospect ORDER BY `cod-emitente` DESC"));
			
			if($prospects['cod-emitente']==""){$valores3[0] = $valores4[0] = $valores5[0] = $valores6[0] = 100000;} else{
			
			$valores3[0] = $valores4[0] = $valores5[0] = $valores6[0] = "'".($prospects['cod-emitente']+1)."'"; }
			
				/* if($emitente['cod-emitente'] > $prospects['cod-emitente']){
					
					$valores3[0] = $valores4[0] = $valores5[0] = $valores6[0] = "'".($emitente['cod-emitente']+1)."'";	
					
					} else {
					
					$valores3[0] = $valores4[0] = $valores5[0] = $valores6[0] = "'".($prospects['cod-emitente']+1)."'";
					
				  }*/
			
			$sql55 = ("INSERT INTO is_emitente_prospect (`".implode('`,`',$campos5)."`) VALUES(".implode(",",$valores5).")");
			mysql_query($sql55) or die($sql55."<hr>".mysql_error());				
				
			$sql66 = ("INSERT INTO is_emitente_prospect_comp (`".implode('`,`',$campos6)."`) VALUES(".implode(",",$valores6).")");
			mysql_query($sql66) or die($sql66."<hr>".mysql_error());	
			
			$array_prospects_comp = $array_prospects = array();		
			
			$cont_prospect++;	
			
		}
			$sql33 = ("INSERT INTO `ped-venda` (`".implode('`,`',$campos3)."`) VALUES(".implode(",",$valores3).")");
			mysql_query($sql33) or die($sql33."<hr>".mysql_error());

			$sql44 = ("INSERT INTO is_ped_venda_comp (`".implode('`,`',$campos4)."`) VALUES(".implode(",",$valores4).")");
			mysql_query($sql44) or die($sql44."<hr>".mysql_error());
	
			$array_ped_comp = $array_ped = $array_prospects = $array_prospects_comp = array();
	
		}
		 
	}

	if($_POST['confirma_pedidos_itens']!=""){

	mysql_query("TRUNCATE TABLE `ped-item`");
	$max_sql = mssql_fetch_array(mssql_query("select max(PKND_pedido) as max from ven_pedido_itens"));
	$qtde_sql = mssql_fetch_array(mssql_query("select count(*) as cnt from ven_pedido_itens"));
	$max = $max_sql['max']; 
	$fator = 30;
	$qtdloop = $max/$fator;
		
	for($i=0;$i<$qtde_sql['cnt'];$i++){
		$partes1 = NULL;
		$inicio = $i*$fator;
		$fim = $fator + $inicio;
		$inicio += 1;	
		if($inicio > $max){
			break;
		}
	
		$query = mssql_query("SELECT PKND_PEDIDO,PKND_CODPRO,ATND_QTD,ATND_QTD_RETORNO,ATND_PRECO_UNIT,ATND_CUSTO_UNIT,ATND_QTD_CONSIG FROM VEN_PEDIDO_ITENS WHERE PKND_PEDIDO BETWEEN $inicio AND $fim");
		while($ar = mssql_fetch_array($query)){
			$array_ped_itens = array(		
			'nr-pedcli' =>				$ar['PKND_PEDIDO'],
			'it-codigo' =>				$ar['PKND_CODPRO'],
			'qt-pedida' =>				$ar['ATND_QTD'],
			'qt-devolvida' =>		    $ar['ATND_QTD_RETORNO'],
			'vl-pretab' =>				$ar['ATND_PRECO_UNIT'],
			'vl-preuni' =>				$ar['ATND_CUSTO_UNIT'],
			'qtd-alocar' =>				$ar['ATND_QTD_CONSIG']
			);
			$valores7 = array();
			foreach($array_ped_itens as $k7 => $v7){
				$res = array_search(trim($v7),$cond);
				if($res!="") {	
					$v7 = 'NULL';
					$valores7[] = $v7;
				}
				else{
					$valores7[] = "'".addslashes(trim($v7))."'";
				}
			}
			$partes1 .= "(".implode(",",$valores7)."),";
		}
		
		$partes1 = substr($partes1, 0, (strlen($partes1)-1));
		$sqle = "INSERT INTO `ped-item` (`nr-pedcli`,`it-codigo`,`qt-pedida`,`qt-devolvida`,`vl-pretab`,`vl-preuni`,`qtd-alocar`) VALUES ".$partes1.";";
		mysql_query($sqle) or die($sqle);
		}
	
	}

	if($_POST['confirma_produtos']!=""){

	$query_produtos = mssql_query("SELECT * FROM CAD_PRO");	

		mysql_query("TRUNCATE TABLE is_produtos");
	
		while($res4 = mssql_fetch_array($query_produtos)){
			
			$sql4 = ("INSERT INTO is_produtos (
			cod_produto, 
			descricao, 
			cod_familia, 
			unidade, 
			manautcus, 
			manaut, 
			manaut2, 
			custo, 
			valor_atacado, 
			valor_varejo, 
			saldo, 
			estoque_minimo, 
			ativo, 
			ultima, 
			tipo_prod, 
			tab_preco,
			int_ext

			) VALUES (
			".(($res4['CODPRO']!="") ? "'".$res4['CODPRO']."'" : 'NULL').", 
			".(($res4['DESCPRO']!="") ? "'".$res4['DESCPRO']."'" : 'NULL').", 
			".(($res4['CODFAM']!="") ? "'".$res4['CODFAM']."'" : 'NULL').", 
			".(($res4['UNDMED']!="") ? "'".$res4['UNDMED']."'" : 'NULL').", 
			".(($res4['MANAUTCUS']!="") ? "'".$res4['MANAUTCUS']."'" : 'NULL').", 
			".(($res4['MANAUT']!="") ? "'".$res4['MANAUT']."'" : 'NULL').", 
			".(($res4['MANAUT2']!="") ? "'".$res4['MANAUT2']."'" : 'NULL').", 
			".(($res4['CUSTO']!="") ? "'".$res4['CUSTO']."'" : 'NULL').", 
			".(($res4['PR_ATAC']!="") ? "'".$res4['PR_ATAC']."'" : 'NULL').", 
			".(($res4['PR_VAREJO']!="") ? "'".$res4['PR_VAREJO']."'" : 'NULL').", 
			".(($res4['SALDO']!="") ? "'".$res4['SALDO']."'" : 'NULL').", 
			".(($res4['ESTMIN']!="") ? "'".$res4['ESTMIN']."'" : 'NULL').",
			".(($res4['ATIVO']!="") ? "'".$res4['ATIVO']."'" : 'NULL').",
			".(($res4['ULTIMA']!="") ? "'".$res4['ULTIMA']."'" : 'NULL').", 
			".(($res4['TIPO_PROD']!="") ? "'".$res4['TIPO_PROD']."'" : 'NULL').", 
			".(($res4['TAB_PRECO']!="") ? "'".$res4['TAB_PRECO']."'" : 'NULL').", 
			".(($res4['INT_EXT']!="") ? "'".$res4['INT_EXT']."'" : 'NULL').")");
				
			$cont_produtos++;
			
			mysql_query($sql4) or die($sql4."<hr>".mysql_error());
		} 
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Carga de dados</title>
</head>
<body>
<form id="form1" name="form1" method="post" action="">
  <table border="1" align="center" bordercolor="#000000">
    <tr>
      <td colspan="6"><div align="center"><strong>Carga - Serve Festas</strong>
</div></td>
    </tr>
    <tr>
      <td colspan="6"><div align="center"><strong>Tabelas</strong></div></td>
    </tr>
    <tr>
      <td colspan="6"><div align="center"><?php 
	  
	  if($_POST['confirma_clientes']!=""){ echo "Clientes inseridos com exito!";}
	  
	  	else if($_POST['confirma_pedidos']!=""){ echo "Pedidos inseridos com exito!";}
	  
	  		else if($_POST['confirma_pedidos_itens']!=""){ echo "Itens inseridos com exito!";}
	  
	  			else { echo "Selecione uma opção abaixo.";}
	  
	  ?></div></td>
    </tr>
    <tr>
      <td><div align="center"><strong>Dados</strong></div></td>
      <td><div align="center"><strong>Clientes</strong></div></td>
      <td><strong>Prospects</strong></td>
      <td><div align="center"><strong>Pedidos</strong></div></td>
      <td><div align="center"><strong>Itens do pedido</strong></div></td>
    </tr>
    <tr>
      <td><div align="right">&nbsp;</div></td>
      <td>
        <div align="center">
          <input type="submit" name="confirma_clientes" id="confirma_clientes" value="Buscar dados" />
        </div></td>
      <td>&nbsp;</td>
      <td>
        <div align="center">
          <input type="submit" name="confirma_pedidos" id="confirma_pedidos" value="Buscar dados" />
        </div></td>
      
      <td>
        <div align="center">
          <input type="submit" name="confirma_pedidos_itens" id="confirma_pedidos_itens" value="Buscar dados" />
        </div></td>
    </tr>
    <tr>
      <td><strong>SQL-Server:</strong></td>	
      <td>
        <div align="center">
        <?php $mssql_tot1 = mssql_fetch_array(mssql_query("SELECT count(*) as tot FROM CAD_Cliente")); echo $mssql_tot1['tot'];?>
        </div></td>
      <td><div align="center">
   <?php $mssql_tot_prospects = mssql_fetch_array(mssql_query("SELECT count(*) as tot FROM VEN_PEDIDO WHERE FKND_CODCLI = '99999'"));echo $mssql_tot_prospects['tot'];?>
      </div></td>
      <td>
        <div align="center">
          <?php $mssql_tot2 = mssql_fetch_array(mssql_query("SELECT count(*) as tot FROM VEN_PEDIDO"));echo $mssql_tot2['tot'];?>
        </div></td>
      <td>
        <div align="center">
          <?php $mssql_tot4 = mssql_fetch_array(mssql_query("SELECT count(*) as tot FROM VEN_PEDIDO_ITENS"));echo $mssql_tot4['tot'];?>
        </div></td>
    </tr>
    <tr>
      <td><strong>MySQL:</strong></td>
      <td>
        <div align="center">
          <?php $mysql_tot1 = mysql_fetch_array(mysql_query("SELECT count(*) as tot FROM emitente")); echo $mysql_tot1['tot'];?>
        </div></td>
      <td><div align="center">
        <?php $mysql_totp = mysql_fetch_array(mysql_query("SELECT count(*) as tot FROM is_emitente_prospect")); echo $mysql_totp['tot'];?>
      </div></td>
      <td>
        <div align="center">
          <?php $mysql_tot2 = mysql_fetch_array(mysql_query("SELECT count(*) as tot FROM `ped-venda`")); echo $mysql_tot2['tot'];?>
        </div></td>
      <td>
        <div align="center">
          <?php $mysql_tot4 = mysql_fetch_array(mysql_query("SELECT count(*) as tot FROM `ped-item`")); echo $mysql_tot4['tot'];?>
        </div></td>
    </tr>
    <tr>
      <td><strong>Dados inseridos: </strong></td>
      <td><div align="center"><?php echo $cont_clientes;?>&nbsp;</div></td>
      <td><div align="center"><?php echo $cont_prospect;?>&nbsp;</div></td>
      <td><div align="center"><?php echo $cont_pedidos;?>&nbsp;</div></td>
      <td><div align="center"><?php echo $cont_pedidos_itens;?>&nbsp;</div></td>
    </tr>
  </table> 
</form>
</body>
</html>