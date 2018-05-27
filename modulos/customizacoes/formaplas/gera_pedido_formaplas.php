<?
  @session_start();
  
  $vs_id_usuario = $_SESSION["id_usuario"];
  $vs_id_perfil = $_SESSION["id_perfil"];
  $sn_bloquear_leitura = $_SESSION["sn_bloquear_leitura"];

  @header("Pragma: no-cache");
  @header("Expires: 0");

  require_once("../../../conecta.php");
  require_once("../../../gera_cad_calc_custom.php");  
  require_once("../../../fpdf.php");

  $pnumreg = $_GET["pnumreg"];
  
  $filtro_geral = "select * from is_atividades where numreg = ".$pnumreg;
  // Bloqueio de Acesso a Dados
  $sql_bloqueio = "";
  if ($sn_bloquear_leitura == "S") {
    $sql_bloqueio = " id_usuario_resp = '$vs_id_usuario'"; 
  }
  // Aplicando SQL Bloqueio
  if ($sql_bloqueio) {
    if (strpos($filtro_geral,'where') === false) { $clausula = 'where'; } else { $clausula = 'and'; }
    $filtro_geral = $filtro_geral.' '.$clausula.' '.$sql_bloqueio;
  }
  //echo $filtro_geral;
  $qry_cadastro = farray(query($filtro_geral));
  
  $qry_usuario = farray(query("select * from is_usuarios where id_usuario = '".$qry_cadastro["id_consultor"]."'"));
  //Checando se a fase permite imprimir o pedido
  $qry_fase = farray(query("select nome_fase from is_fases_workflow where id_fase = '".$qry_cadastro["id_fase_workflow"]."'"));
  if (($qry_fase["nome_fase"] < "04") || ($qry_fase["nome_fase"] >= "99")) {
     	echo '<script language="Javascript">alert('."'Não é permitido imprimir o pedido nesta fase!'".");window.close();</script>";
		exit;
  }
  
  // Calculando o Numero do Pedido
  if (empty($qry_cadastro["num_pedido"])) {
	  $qry_ult_ped = farray(query("select max(num_pedido) as ult_ped from is_atividades where num_pedido like '__".date("m").substr(date("Y"),2,2)."'"));
	  if ($qry_ult_ped["ult_ped"]) {
	     $seq = substr($qry_ult_ped["ult_ped"],0,2);
		 $seq = $seq + 1;
	  } else {
		 $seq = 1;
	  }
 	  $num_ped = str_pad(($seq), 2, "0", STR_PAD_LEFT).date("m").substr(date("Y"),2,2);
	  query("update is_atividades set num_pedido = '".$num_ped."' where numreg = ".$qry_cadastro["numreg"]);
  } else {
	  $num_ped = $qry_cadastro["num_pedido"];
  }
  //--------------------------------------------------------------------------------------
  // IMPRESSAO DO RELATORIO
  //--------------------------------------------------------------------------------------
	class PDF extends FPDF
	{
		 function Header() {
		  	  global $num_ped;
		 
			  $this->Image("../../../images/formaplas_pedido.jpg",0,0,210,70);
			  $this->Ln( 38 );  
			  $this->SetFont( "Arial","B",9 ); 
			  $this->Cell( 100,3.8,"",0,0,"L" );$this->Cell( 90,3.8, "Eurocook Comércio de Cozinhas Ltda",0,1,"L" );
			  $this->Cell( 100,3.8,"",0,0,"L" );$this->Cell( 90,3.8, "CNPJ/MF 00.296.375/0001-40",0,1,"L" );
			  $this->Cell( 100,3.8,"",0,0,"L" );$this->Cell( 90,3.8, "Rua Victor Konder, 243 – Centro - Florianópolis - SC",0,1,"L" );
			  $this->Cell( 100,3.8,"",0,0,"L" );$this->Cell( 90,3.8, "CEP: 88015-400 - Fone.: (48) 3224-8022",0,1,"L" );
			  $this->Cell( 100,3.8,"",0,0,"L" );$this->Cell( 90,3.8, "Site: www.formaplas.com.br",0,1,"L" );
			  $this->Ln( 5 );  
			  
			  $this->SetFont( "Arial","B",9 );
			  $this->Cell( 0,7, "PEDIDO N°: ".($num_ped).'FPOLIS',0,1,"R" );
			  $this->SetFont( "Arial","",8 ); 
			  
		 }
	
		 function Footer() {
		      global $qry_usuario;
			  // Definimos a fonte
			  $this->SetFont('Arial','',8);
			  $this->SetXY(9.5,-20);
			  $this->Cell( 20, 5,"",0,0,"C" );
			  $this->Cell( 55, 5,$qry_usuario["nome_usuario"],"T",0,"C" );
			  $this->Cell( 35, 5,"",0,0,"C" );
			  $this->Cell( 55, 5,"ASSINATURA DO CLIENTE - ".date("d/m/Y"),"T",1,"C" );
			  
			  $this->Cell(0,5,'Eurocook Comércio de Cozinhas Ltda. Rua Victor Konder, 243. Tel(48) 3224-8022. Florianópolis - SC. Pág.: '.$this->PageNo(),0,0,'C');
		 }
	}
  
  $pdf=new PDF('P','mm','A4');
  $largura_pdf = 144;
  $contador = 0;
  $pdf->SetAutoPageBreak(true, 25);


  pdf_cabecalho($pdf,$qry_cadastro);
  pdf_corpo($pdf,$qry_cadastro);
  //--------------------------------------------------------------------------------------
  $pdf->Output();
  
//=================================================================================
function pdf_cabecalho($pdf,$qry_cadastro) {
	global $num_ped;

  $qry_pessoa = farray(query("select * from is_pessoas where id_pessoa = '".$qry_cadastro["id_pessoa_contato"]."'"));
  $qry_arquiteto = farray(query("select * from is_pessoas where id_pessoa = '".$qry_cadastro["id_arquiteto"]."'"));

  // Cabeçalho
  $pdf->AddPage();
  
  $pdf->SetFont( "Arial","",8 );
  
  
  $pdf->Cell( 140,5, "CLIENTE: ".$qry_pessoa["razao_social_nome"],1,0,"L" );  $pdf->Cell( 50,5, "CPF/CNPJ: ".$qry_pessoa["cnpj_cpf"],1,1,"L" );
  $pdf->Cell( 140,5, "END.COBRANÇA/RES: ".$qry_pessoa["endereco"]." N.".$qry_pessoa["numero"]." ".$qry_pessoa["complemento"],1,0,"L" );  $pdf->Cell( 50,5, "RG/IE: ".$qry_pessoa["ie_rg"],1,1,"L" );
  $pdf->Cell( 70,5, "BAIRRO: ".$qry_pessoa["bairro"],1,0,"L" );  $pdf->Cell( 70,5, "CIDADE/UF: ".$qry_pessoa["cidade"]."/".$qry_pessoa["uf"],1,0,"L" );  $pdf->Cell( 50,5, "CEP: ".$qry_pessoa["cep"],1,1,"L" );
  $pdf->Cell( 70,5, "FONE RES: ".$qry_pessoa["telres"],1,0,"L" );  $pdf->Cell( 70,5, "FONE COM: ".$qry_pessoa["telcom"],1,0,"L" );  $pdf->Cell( 50,5, "DATA NASCIMENTO: ".$qry_pessoa["dianascto"]."/".$qry_pessoa["mesnascto"]."/".$qry_pessoa["anonascto"],1,1,"L" );
  $pdf->Cell( 0,5, "END.ENTREGA: ".$qry_pessoa["endereco_cob"]." N.".$qry_pessoa["numero_cob"]." ".$qry_pessoa["complemento_cob"],1,1,"L" );  
  $pdf->Cell( 70,5, "BAIRRO: ".$qry_pessoa["bairro_cob"],1,0,"L" );  $pdf->Cell( 70,5, "CIDADE/UF: ".$qry_pessoa["cidade_cob"]."/".$qry_pessoa["uf_cob"],1,0,"L" );  $pdf->Cell( 50,5, "CEP: ".$qry_pessoa["cep_cob"],1,1,"L" );
  $pdf->Cell( 70,5, "FONE: ".$qry_pessoa["telcel"],1,0,"L" ); $pdf->Cell( 120,5, "REFERÊNCIA: ",1,1,"L" );
  $pdf->Cell( 0,5, "E-MAIL: ".$qry_pessoa["email_prof"],1,1,"L" );
  $pdf->Cell( 0,5, "ARQUITETO: ".$qry_arquiteto["razao_social_nome"]." ".$qry_arquiteto["telcom"]." ".$qry_arquiteto["email_prof"],1,1,"L" );
	
//  $pdf->Ln(  );
}

//=================================================================================
function pdf_corpo($pdf, $qry_cadastro) {

  $tot_orcamentos = 0;
  $contador = 0;
  


  $total_orcamento = $qry_cadastro["receita_prev"];


  if ($total_orcamento <= 0) {
	  $qry_prod_tot = farray(query("select sum(qtde*valor) as total from is_opor_itens where id_atividade = '".$qry_cadastro["id_atividade"]."'"));
	  $qry_ace_tot = farray(query("select sum(qtde*valor) as total from  is_opor_acessorios where id_atividade = '".$qry_cadastro["id_atividade"]."'"));
	  $total_orcamento = $qry_prod_tot["total"]+$qry_ace_tot["total"];
	  query("update is_atividades set receita_prev = ".number_format($total_orcamento,2,'.','')." where numreg = ".$qry_cadastro["numreg"]);
  }


  $vl_descto = $qry_cadastro["vl_desconto"];

  if ($total_orcamento != 0) {
    $pct_descto = 1-($vl_descto/$total_orcamento);
  } else {
    $pct_descto = 1;
  
  }
  
  $sql_produtos = query("select * from is_opor_itens where id_atividade = '".$qry_cadastro["id_atividade"]."'");
  while ($qry_produtos = farray($sql_produtos)) {
	  $pdf->Ln( 5 );
	  $pdf->SetFont( "Arial","B",9 );  
      $pdf->Cell( 0, 5,$qry_produtos["descr_orcamento"],1,1,"L" );
	  $pdf->SetFont( "Arial","",8 );  
	  $descricao = "";
	  if ($qry_produtos["padrao_laminado"]) {
		  $qry_padrao_laminado = farray(query("select * from is_padrao_laminados where id_padrao_laminado = '".$qry_produtos["padrao_laminado"]."'"));
		  $descricao .= "Gabinetes modelo ".$qry_padrao_laminado["nome_padrao_laminado"]." - EXCLUSIVO - ";
	  }
	  if ($qry_produtos["padrao_portas"]) {
		  $qry_padrao_laminado = farray(query("select * from is_padrao_laminados where id_padrao_laminado = '".$qry_produtos["padrao_portas"]."'"));
		  $descricao .= "com portas modelo ".$qry_padrao_laminado["nome_padrao_laminado"]." - EXCLUSIVO - ";
	  }
	  $vidro = "";
	  if ($qry_produtos["padrao_portas_vidro"]) {
		  $qry_cores_vidro = farray(query("select * from is_cores_vidro where id_cor_vidro = '".$qry_produtos["padrao_portas_vidro"]."'"));
		  $qry_perfis_cinex = farray(query("select * from is_perfis_cinex where id_perfil_cinex = '".$qry_produtos["perfil_porta_vidro"]."'"));
		  $vidro = "Porta de vidro ".$qry_cores_vidro["nome_cor_vidro"]." com ".$qry_perfis_cinex["nome_perfil_cinex"]." em alumínio -CINEX-.";
	  }

	  $puxadores = "";
	  if ($qry_produtos["puxadores_porta_laminado"]) {
		  $qry_puxadores_laminado = farray(query("select * from is_puxadores_madeira where id_puxador_madeira = '".$qry_produtos["puxadores_porta_laminado"]."'"));
		  $puxadores = "Puxadores ".$qry_puxadores_laminado["nome_puxador_madeira"]." para portas de madeira";
		  
	  }
	  if ($qry_produtos["puxadores_porta_vidro"]) {
		  $qry_puxadores_vidro = farray(query("select * from is_puxadores_vidro where id_puxador_vidro = '".$qry_produtos["puxadores_porta_vidro"]."'"));
		  $puxadores .= " e ".$qry_puxadores_vidro["nome_puxador_vidro"]." nas portas de vidro.";
		  
	  }
	  $gavetas = "";
	  if ($qry_produtos["linha_gavetas"]) {
		  $qry_gavetas = farray(query("select * from is_linhas_gavetas where id_linha_gaveta = '".$qry_produtos["linha_gavetas"]."'"));
		  $gavetas = "Gavetas ".$qry_gavetas["nome_linha_gaveta"];
	  }
	  
      $pdf->MultiCell( 0, 5,$descricao,0,1,"L" );
	  if ($vidro) { $pdf->MultiCell( 0, 5,$vidro,0,1,"L" ); }
      if ($puxadores) { $pdf->MultiCell( 0, 5,$puxadores,0,1,"L" ); }
      if ($gavetas) { $pdf->MultiCell( 0, 5,$gavetas,0,1,"L" ); }
      $pdf->Cell( 0, 5,"Quantidade : ".$qry_produtos["qtde"]." - Valor Unitário: ".'R$'.number_format(($qry_produtos["valor"]*$pct_descto),2,',','.'),"TB",1,"R" );
	  
	  
	  $vl_acessorios = 0;
	  $sql_acessorios = query("select * from is_opor_acessorios where id_atividade = '".$qry_cadastro["id_atividade"]."' and id_produto = '".$qry_produtos["id_produto"]."'");
	  
	  $primeira = "S"; 
	  while ($qry_acessorios = farray($sql_acessorios)) {
	  	  if ($primeira=="S") {
			  $pdf->SetFont( "Arial","B",8 );  
			  $pdf->Cell( 0, 5,"Acessórios",0,1,"L" );
			  $pdf->SetFont( "Arial","",8 );  
			  $primeira = "N";
		  }
		  $qry_ace_descr = farray(query("select * from is_acessorios where id_acessorio = '".$qry_acessorios["id_acessorio"]."'"));
	      $pdf->Cell( 0, 5,$qry_acessorios["qtde"]." - ".$qry_ace_descr["nome_acessorio"],0,1,"L" );
    	  $vl_acessorios =  $vl_acessorios + ($qry_acessorios["qtde"]*$qry_acessorios["valor"]*$pct_descto);
	  }
	  if ($primeira=="N") {
	      $pdf->Cell( 0, 5,"Valor dos Acessórios : ".'R$'.number_format(($vl_acessorios),2,',','.'),0,1,"R" );
	  }
	  $pdf->SetFont( "Arial","B",8 );
	    
      $pdf->Cell( 0, 5,"Total do Item : ".'R$'.number_format(($qry_produtos["qtde"]*($vl_acessorios+($qry_produtos["valor"]*$pct_descto))),2,',','.'),"TB",1,"R" );
	  $pdf->SetFont( "Arial","",8 );  
	  
	  $tot_orcamentos = $tot_orcamentos + ($qry_produtos["qtde"]*($vl_acessorios+($qry_produtos["valor"]*$pct_descto)));

  }
  $pdf->Ln( 5 );
  $pdf->SetFont( "Arial","B",9 );  
  $pdf->Cell( 40, 5,"VALOR TOTAL DO PEDIDO R$ ","TB",0,"L" );
  $pdf->Cell( 150, 5,'R$ '.number_format($tot_orcamentos,2,',','.'),"TB",1,"R" );

  query("update is_atividades set receita_real = ".number_format($tot_orcamentos,2,'.','')." where numreg = ".$qry_cadastro["numreg"]);
  
  $pdf->Ln( 5 );
  $pdf->SetFont( "Arial","B",9 );  
  $pdf->Cell( 100, 5,"CONDIÇÕES DE PAGAMENTO",0,1,"L" );

  $sql_parcelas = query("select * from is_opor_parcelas where id_atividade = '".$qry_cadastro["id_atividade"]."'");
  $pdf->SetFont( "Arial","B",8 );  
  $pdf->Cell( 40, 5,"PARCELA",1,0,"L" );
  $pdf->Cell( 50, 5,"DATA VENCIMENTO",1,0,"C" );
  $pdf->Cell( 50, 5,"FORMA DE PAGAMENTO",1,0,"C" );
  $pdf->Cell( 50, 5,"VALOR",1,1,"R" );
  
  $pdf->SetFont( "Arial","",8 );  

  $tot_parcelas = 0;  
  while ($qry_parcelas = farray($sql_parcelas)) {
	  $qry_fp_descr = farray(query("select * from is_forma_pagto where id_forma_pagto = '".$qry_parcelas["id_forma_pagto"]."'"));
	  $pdf->Cell( 40, 5,$qry_parcelas["id_parcela"],1,0,"L" );
	  $DtVencto = $qry_parcelas["dt_vencimento"];
	  $DtVencto = substr($DtVencto,8,2).'/'.substr($DtVencto,5,2).'/'.substr($DtVencto,0,4);
	  $pdf->Cell( 50, 5,$DtVencto,1,0,"C" );
	  $pdf->Cell( 50, 5,$qry_fp_descr["nome_forma_pagto"],1,0,"C" );
	  $pdf->Cell( 50, 5,"R$ ".number_format($qry_parcelas["valor"],2,',','.'),1,1,"R" );
	  $tot_parcelas = $tot_parcelas + $qry_parcelas["valor"];
  }
  $pdf->SetFont( "Arial","B",9 );  
  $pdf->Cell( 40, 5,"Valor Total das Parcelas R$ ","TB",0,"L" );
  $pdf->Cell( 150, 5,'R$ '.number_format($tot_parcelas,2,',','.'),"TB",1,"R" );
  $pdf->SetFont( "Arial","",8 );  

  
  $DtEntrega = $qry_cadastro["dt_prev_entrega"];
  $DtEntrega = substr($DtEntrega,8,2).'/'.substr($DtEntrega,5,2).'/'.substr($DtEntrega,0,4);
  
  $pdf->Ln( 5 );
  $pdf->Cell( 0, 5,"PRAZO DE ENTREGA: ".$qry_cadastro["id_prev_entreg"]." DIAS CORRIDOS A PARTIR DA ASSINATURA DO PROJETO EXECUTIVO.",1,1,"C" );

  $pdf->Ln( 5 );
  $pdf->Cell( 0, 5,"AUTORIZO EXECUTAR O PRESENTE PROJETO E PEDIDO, CONFORME CONDIÇÕES GERAIS, DAS QUAIS DECLARO QUE TENHO CIÊNCIA",1,1,"C" );
  $pdf->Ln( 10 );
  $pdf->Cell( 0, 5,"RECIBO",1,1,"C" );
  $pdf->Cell( 0, 5,"RECEBEMOS DO CLIENTE ACIMA, A IMPORTÂNCIA DE R$                              . REFERENTE A ENTRADA DE NEGÓCIO.",1,1,"C" );



  $pdf->AddPage();
  $pdf->SetFont( "Arial","B",12 );  
  $pdf->Cell( 00, 5,"CONDIÇÕES GERAIS DE VENDA",0,1,"C" );
  $pdf->SetFont( "Arial","",8 );  
  $pdf->Ln( 3 );
  $pdf->MultiCell( 0,4,"1-A Reference Comércio de Cozinhas e Representações Ltda., denominada VENDEDORA, se obriga a prestar ao(a) CLIENTE, os serviços de, planejamento, execução de plantas, assessoramento técnico e instalação do objeto deste pedido, no local indicado, conforme planta(s) de detalhe(s) aprovada(s) e assinada(s), que passa(m) a integrar o presente pedido.",0,1,"L" );

  $pdf->MultiCell( 0,4,"2-Da mesma forma a VENDEDORA, se obriga a fornecer ao(a) CLIENTE, por venda, armário(s) e acessórios de acordo com as especificações,conforme planta(s) de detalhe(s) mencionada(s) no item 1.",0,1,"L" );
  $pdf->MultiCell( 0,4,"3-O CLIENTE se obriga a pagar à VENDEDORA o preço estipulado no presente pedido, nos prazos e condições descritos no mesmo.",0,1,"L" );
  $pdf->MultiCell( 0,4,"3.1 A(s) parcela(s) não paga(s) no(s) respectivo(s) vencimento(s) será(ão) acrescida(s) do(s) de juros moratorios e multa sobre o valor de débito, desde a data de vencimento até a data do efetivo pagamento.
",0,1,"L" );
  $pdf->MultiCell( 0,4,"4-O CLIENTE deverá efetuar os pagamentos no local estabelecido pela VENDEDORA.",0,1,"L" );
  $pdf->MultiCell( 0,4,"5-A entrega, instalação e conclusão do(s) serviço(s) ficam condicionados ao pagamento em dia da(s) parcela(s).",0,1,"L" );
  $pdf->MultiCell( 0,4,"6-A data inicialmente fixada para entrega, é a determinada na frente do presente pedido, desde que a obra se encontre nas condições estipuladas no item 7, ou em 65 (sessenta e cinco) dias corridos a contar da data em que o (a) CLIENTE entregar a obra como descrito no item 7, desde que a obra se encontre nas condições estipuladas no item , 7.1, 7.2 e 7.3.",0,1,"L" );
  $pdf->MultiCell( 0,4,"6.1   Para efeito do prazo de entrega, poderá ocorrer antecipação ou dilatação do prazo de 10(dez) dias corridos.",0,1,"L" );
  $pdf->MultiCell( 0,4,"6.2   No caso de convulsões sociais, greves, catástrofes, casos fortuitos e de força maior, a entrega estará sujeita à restauração da ordem ou ao restabelecimento das condições normais.",0,1,"L" );
  $pdf->MultiCell( 0,4,"7-O local da obra, onde serão instalados e prestados os demais serviços, deve estar pronto e em condições para instalação dos produtos objeto deste pedido, conforme planta(s) de detalhe(s) aprovada pelas partes. As medidas e dimensões, os pontos elétricos, hidráulicos, de gás e outros devidamente especificados, deverão estar rigorosamente de acordo com a(s) planta(s) de detalhe(s) aprovada(s) e assinada(s) pelo(a) CLIENTE. Os revestimentos dos pisos, paredes, e teto deverão estar concluídos e as aberturas colocadas. Os ambientes não devem ter pessoas trabalhando e/ou circulando além dos instaladores da FORMAPLAS. É necessário também termos disponível energia elétrica nos ambientes e que os mesmos se encontrem limpos.",0,1,"L" );
  $pdf->MultiCell( 0,4,"7.1  A responsabilidade pela execução e exatidão destes serviços é do CLIENTE.",0,1,"L" );
  $pdf->MultiCell( 0,4,"7.2  Os materiais e serviços decorrentes de instalações hidráulicas, elétricas, gás e alvenaria, bem como torneiras, registros de qualquer tipo, demais acabamentos, material de esgoto, antes e depois da instalação, bem como outros matériais e serviços que não constem na(s)planta(s) de detalhe(s) referente ao presente pedido, são de responsabilidade do CLIENTE.",0,1,"L" );
  $pdf->MultiCell( 0,4,"7.3  A(s) planta(s) de detalhe(s) será(ão) encaminhada(s)ao(a) cliente posteriormente ao fechamento da venda e deste pedido, em tempo hábil para a execução da obra.",0,1,"L" );
  $pdf->MultiCell( 0,4,"8-Na hipótese do local da obra não se encontrar nas condições estipuladas conforme item 7, o(a) CLIENTE deverá informar por escrito à VENDEDORA, uma nova data para entrega, observando as condições descritas no item 6.",0,1,"L" );
  $pdf->MultiCell( 0,4,"9-Nos pagamentos contra entrega, no caso da obra não reunir as condições conforme item 7, ou ocorrer qualquer atraso ou evento de responsabilidade do(a) CLIENTE, que concorram para que a VENDEDORA não possa entregar o objeto do pedido no prazo estabelecido, o pagamento se dará independentemente da entrega.",0,1,"L" );
  $pdf->MultiCell( 0,4,"10-Eventuais alterações na(s) planta(s) de detalhe(s) aprovada(s) ou na obra de responsabilidade do CLIENTE, dependerão de consentimento prévio das partes. Â não comunicação exonera a parte prejudicada de qualquer responsabilidade.",0,1,"L" );
  $pdf->MultiCell( 0,4,"11-Qualquer acordo verbal com nossos vendedores não será considerado. As eventuais anotações ou detalhes deverão constar sempre por escrito.",0,1,"L" );
  $pdf->MultiCell( 0,4,"12-Ultrapassando o período de desistência previsto em lei, a parte que desistir do presente pedido, ficará obrigada ao pagamento de multa de 20% (vinte porcento) sobre o valor do total do pedido a título de ressarcimento por perdas e danos. Sendo a rescisão requerida pelo CLIENTE, a VENDEDORA terá o direito de descontar a multa dos valores efetivamente pagos.",0,1,"L" );


}



?>




