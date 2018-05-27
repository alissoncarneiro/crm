<?php 
	flush();	
	set_time_limit(0);

	//ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING);
	//ini_set('error_reporting', E_ALL & ~E_NOTICE);
	//ini_set('display_errors', 'on');
	$TempoInicio = microtime(get_as_float);
	$conexaoOasis= mysql_connect('192.168.0.252', 'alisson', 'ALS0215') or die("Erro ao se conectar");
	$dbOasisProducao = mysql_select_db('bd_oasis_producao');
	
	
   //Limpa Tabela Titulos Oasis
	$Delete = "Delete from is_titulo";
        $dbOasisProducao = mysql_select_db('bd_oasis_producao');
	if($QryDelete = mysql_query($Delete)){
		echo "Base Limpa <br>";
	}else{
		echo mysql_error();
		exit;
	}
	
	//Ajusta Inadimplentes
	$SqlUpdateInadimplente ="UPDATE is_pessoa set sn_inadimplente=0, qtde_titulos_em_atraso = 0 WHERE sn_cliente = 1";
        $dbOasisProducao = mysql_select_db('bd_oasis_producao');
	if($QryDelete = mysql_query($SqlUpdateInadimplente)){
		echo "Update Executado nos Inadimplentes <br>";
	}else{
		echo mysql_error();
		exit;
	}
	
	$SqlInsert = "INSERT INTO is_titulo (
	id_titulo_erp,
	id_pessoa_erp,
	n_parcela,
	dt_emissao,
	dt_vencimento,
	dt_pagamento,
	vl_titulo,
	vl_saldo,
	id_tp_situacao_titulo,
	id_pessoa,
	id_estabelecimento_erp,
	dt_ult_pagamento
	)
	SELECT
	tituloFinanceiro.id_titulo_erp,
	tituloFinanceiro.id_pessoa_erp,
	tituloFinanceiro.n_parcela,
	DATE_FORMAT(tituloFinanceiro.dt_emissao,    '%Y-%m-%d %H:%i:%s')    as dt_emissao,
	DATE_FORMAT(tituloFinanceiro.dt_vencimento, '%Y-%m-%d %H:%i:%s') as dt_vencimento,
	DATE_FORMAT(tituloFinanceiro.dt_pagamento,  '%Y-%m-%d %H:%i:%s')  as dt_pagamento,
	tituloFinanceiro.vl_titulo,
	tituloFinanceiro.vl_sado,
	IF(tituloFinanceiro.id_tp_situacao_titulo = 'ABERTO',
		IF(tituloFinanceiro.dt_vencimento < now('Y-m-d h:i:s'),
			IF(tituloFinanceiro.dt_pagamento = '1900-01-01 00:00:00','4',
				IF(tituloFinanceiro.dt_pagamento is null,'4','4')
			)
		,'1')
	 ,'2') as id_tp_situacao_titulo,
	pessoa.numreg as id_pessoa,
	tituloFinanceiro.id_estabelecimento_erp,
	DATE_FORMAT(tituloFinanceiro.dt_ultimo_pagamento, '%Y-%m-%d %H:%i:%s') as dt_ultimo_pagamento
	FROM c_coaching_titulo_financeiro as tituloFinanceiro
	INNER join is_pessoa as pessoa
	on tituloFinanceiro.id_pessoa like concat('%', pessoa.email ,'%')
	where pessoa.sn_cliente = 1
	order by tituloFinanceiro.dt_vencimento asc";

	if($QrySqlInsert = mysql_query($SqlInsert)){
		$total = mysql_affected_rows();
		mysql_free_result($QrySqlInsert);
		
		$SqlQrySqlTituloAtrasadosInadimplentes = "UPDATE is_pessoa pessoa
									  INNER JOIN (
										SELECT id_pessoa, COUNT(*) as total
										  FROM is_titulo WHERE id_tp_situacao_titulo = 4 GROUP BY id_pessoa) titulo
										  ON titulo.id_pessoa = pessoa.numreg
										  AND pessoa.sn_cliente='1'
										SET pessoa.qtde_titulos_em_atraso = titulo.total, pessoa.sn_inadimplente=1, pessoa.sn_grupo_inadimplente=1,
										 pessoa.qtde_max_titulos_em_atraso =
										   IF(pessoa.qtde_max_titulos_em_atraso < pessoa.qtde_titulos_em_atraso,
											  pessoa.qtde_titulos_em_atraso,
											  pessoa.qtde_max_titulos_em_atraso)";
																																					
		$QrySqlTituloAtrasadosInadimplentes = mysql_query($SqlQrySqlTituloAtrasadosInadimplentes);
		$totalInadimplentes = mysql_affected_rows();
		mysql_free_result($QrySqlTituloAtrasadosInadimplentes);
		
	}else{
		echo mysql_error();
	}
	mysql_close($conexaoOasis);	
	
	$TempoFinal     = microtime(get_as_float);
	$Diferenca      = $TempoFinal - $TempoInicio;
	
	echo "Registro(s) Processado(s)". $total . "<br>";
	echo "Registro(s) Inadimplente(s)". $totalInadimplentes . "<br>";
	echo "Tempo Gasto: " . round($Diferenca/100 *100, 3). " Segundos<br>";
	echo 'Fim : ' . date("d/m/Y") . '-' . date("H:i:s") . '<br>';
?>