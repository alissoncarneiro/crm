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
  
  //Checando se a fase permite imprimir o pedido
  $qry_fase = farray(query("select nome_fase from is_fases_workflow where id_fase = '".$qry_cadastro["id_fase_workflow"]."'"));
  if (($qry_fase["nome_fase"] < "03") || ($qry_fase["nome_fase"] >= "99")) {
     	echo '<script language="Javascript">alert('."'Não é permitido imprimir o orçamento nesta fase!'".");window.close();</script>";
		exit;
  }
  
  //--------------------------------------------------------------------------------------
  // IMPRESSAO DO RELATORIO
  //--------------------------------------------------------------------------------------
	class PDF extends FPDF
	{
		 function Header() {
			  $this->Image("../../../images/formaplas_orcamento.jpg",10,10,190,22);
			  $this->Ln( 30 );  
			  pdf_cabecalho($this);
		 }
	
		 function Footer() {
			  // Definimos a fonte
			  $this->SetFont('Arial','',8);
			  $this->SetXY(9.5,-20);
			  $this->Cell( 25, 5,"",0,0,"C" );
			  $this->Cell( 50, 5,"Consultor de Vendas","T",0,"C" );
			  $this->Cell( 40, 5,"",0,0,"C" );
			  $this->Cell( 50, 5,"Cliente","T",1,"C" );
			  
			  $this->Cell(0,5,'Eurocook Comércio de Cozinhas Ltda. Rua Victor Konder, 243. Tel(48) 3224-8022. Florianópolis - SC. Pág.: '.$this->PageNo(),0,0,'C');
		 }
	}
  
  
  $pdf=new PDF('P','mm','A4');
  $largura_pdf = 144;
  $contador = 0;
  $pdf->SetAutoPageBreak(true, 25);

  $pdf->AddPage();

  pdf_corpo($pdf,$qry_cadastro);
  //--------------------------------------------------------------------------------------

  $pdf->Output();
  
//=================================================================================
function pdf_cabecalho($pdf) {

  global $qry_cadastro;

  $qry_pessoa = farray(query("select * from is_pessoas where id_pessoa = '".$qry_cadastro["id_pessoa_contato"]."'"));
  $qry_usuario = farray(query("select * from is_usuarios where id_usuario = '".$qry_cadastro["id_consultor"]."'"));

  // Cabeçalho
  $pdf->Image('../../../images/formaplas_garantia.jpg',160,55,32,15);
  
  $pdf->SetFont( "Arial","B",10 ); $pdf->Cell( 30,5, "CLIENTE",0,0,"L" );
  $pdf->SetFont( "Arial","",10 ); $pdf->Cell( 200,5, $qry_pessoa["razao_social_nome"],0,1,"L" );
  
  $pdf->SetFont( "Arial","B",10 ); $pdf->Cell( 30,5, "ENDEREÇO",0,0,"L" );
  $pdf->SetFont( "Arial","",10 ); $pdf->Cell( 200,5, $qry_pessoa["endereco"]." N.".$qry_pessoa["numero"]." ".$qry_pessoa["complemento"]." - ".$qry_pessoa["cidade"]." - ".$qry_pessoa["uf"],0,1,"L" );
  
  $pdf->SetFont( "Arial","B",10 ); $pdf->Cell( 30,5, "CONSULTOR",0,0,"L" );
  $pdf->SetFont( "Arial","",10 ); $pdf->Cell( 200,5, $qry_usuario["nome_usuario"],0,1,"L" );
  $DtBriefing = $qry_cadastro["dt_inicio"];
  
  $DtBriefing = substr($DtBriefing,8,2).'/'.substr($DtBriefing,5,2).'/'.substr($DtBriefing,0,4);
  $pdf->SetFont( "Arial","B",10 ); $pdf->Cell( 30,5, "DATA",0,0,"L" );
  $pdf->SetFont( "Arial","",10 ); $pdf->Cell( 200,5, $DtBriefing,0,1,"L" );

  $pdf->Ln( 5 );
  
  $pdf->SetFont( "Arial","B",14 );
  $pdf->Cell( 0,7, "ORÇAMENTO N° : ".(0+$qry_cadastro["id_atividade"]),0,1,"L" );

}

//=================================================================================
function pdf_corpo($pdf, $qry_cadastro) {

  $tot_orcamentos = 0;
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
      $pdf->Cell( 0, 5,"Quantidade : ".$qry_produtos["qtde"]." - Valor Unitário: ".'R$'.number_format(($qry_produtos["valor"]),2,',','.'),"TB",1,"R" );
	  
	  
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
    	  $vl_acessorios =  $vl_acessorios + ($qry_acessorios["qtde"]*$qry_acessorios["valor"]);
	  }
	  if ($primeira=="N") {
	      $pdf->Cell( 0, 5,"Valor dos Acessórios : ".'R$'.number_format(($vl_acessorios),2,',','.'),0,1,"R" );
	  }
	  $pdf->SetFont( "Arial","B",8 );
	    
      $pdf->Cell( 0, 5,"Total do Item : ".'R$'.number_format(($qry_produtos["qtde"]*($qry_produtos["valor"])),2,',','.'),"TB",1,"R" );
	  $pdf->SetFont( "Arial","",8 );  
	  
	  $tot_orcamentos = $tot_orcamentos + ($qry_produtos["qtde"]*($qry_produtos["valor"]-$vl_acessorios));

  }
  

  $pdf->Ln( 5 );
  $pdf->SetFont( "Arial","B",9 );  
  $pdf->Cell( 40, 5,"Valor total do orçamento","TB",0,"R" );
  $pdf->Cell( 150, 5,'R$ '.number_format($tot_orcamentos,2,',','.'),"TB",1,"R" );
  if (($qry_cadastro["vl_desconto"]*1)>0) {
   $pdf->SetTextColor(255,0,0);
   $pdf->Cell( 40, 5,"Desconto especial","TB",0,"L" );
   $pdf->Cell( 150, 5,'R$ '.number_format($qry_cadastro["vl_desconto"],2,',','.'),"TB",1,"R" );
   $pdf->SetTextColor(0,0,0);  
   $pdf->Cell( 40, 5,"Valor do investimento","TB",0,"L" );
   $pdf->Cell( 150, 5,'R$ '.number_format($tot_orcamentos-$qry_cadastro["vl_desconto"],2,',','.'),"TB",1,"R" );
  }

  query("update is_atividades set receita_prev = ".number_format($tot_orcamentos,2,'.','')." where numreg = ".$qry_cadastro["numreg"]);
  
  $DtValidade = $qry_cadastro["dt_validade_orcamento"];
  $DtValidade = substr($DtValidade,8,2).'/'.substr($DtValidade,5,2).'/'.substr($DtValidade,0,4);
  
  
  $pdf->Ln( 10 );
  $pdf->SetFont( "Arial","B",8 );  
  $pdf->SetTextColor(255,0,0);  
  $pdf->Cell( 0, 5,"PROPOSTA VÁLIDA ATÉ ".$DtValidade,"TB",0,"C" );
  $pdf->Ln( 10 );
  $pdf->Cell( 0, 5,"PRAZO DE ENTREGA: ".$qry_cadastro["id_prev_entreg"]." DIAS APÓS A ASSINATURA DO PROJETO EXECUTIVO","TB",0,"C" );
  $pdf->Ln( 10 );
  $pdf->SetTextColor(0,0,0);  
  $pdf->SetFont( "Arial","",8 );  
  $obs = "Observação : O projeto definitivo só poderá ser executado depois da definição dos eletrodomésticos e da obra estar em perfeitas condições de medição. O prazo médio de elaboração é de 8 dias úteis";
  $pdf->MultiCell( 0, 5,$obs,0,"L" );
  $pdf->Ln( 5 );
  $pdf->MultiCell( 0, 5,"Itens não inclusos no valor do orçamento: madeira de demolição, tampos de corian, cubas, misturadores, eletrodomésticos, metais diversos e decoração.",0,"L" );
  
  $pdf->Ln( 5 );
  $pdf->SetFont( "Arial","B",9 );  
  $pdf->Cell( 100, 5,"Eletros considerados para execução dos projetos acima: ",0,1,"L" );

  $sql_equipamentos = query("select * from is_briefing_equipamentos where id_atividade = '".$qry_cadastro["id_atividade"]."'");
  $pdf->SetFont( "Arial","B",8 );  
  $pdf->Cell( 30, 5,"Equipamento",1,0,"L" );
  $pdf->Cell( 10, 5,"cliente",1,0,"C" );
  $pdf->Cell( 10, 5,"loja",1,0,"C" );
  $pdf->Cell( 100, 5,"Marca/modelo",1,0,"L" );
  $pdf->Cell( 40, 5,"Dimensões: L X H X P",1,1,"L" );
  $pdf->SetFont( "Arial","",8 );  
  
  while ($qry_equipamentos = farray($sql_equipamentos)) {
	  $qry_eq_descr = farray(query("select * from is_tp_briefing where id_opc = '000030' and id_tp_briefing = '".$qry_equipamentos["id_equipamento"]."'"));
  
	  $pdf->Cell( 30, 5,$qry_eq_descr["nome_tp_briefing"],1,0,"L" );
	  
	  if ($qry_equipamentos["Loja"] == "S") { $loja = "X"; $cli = "";}	else { $loja = ""; $cli = "X";}
	  $pdf->Cell( 10, 5,$cli,1,0,"C" );
	  $pdf->Cell( 10, 5,$loja,1,0,"C" );
	  $pdf->Cell( 100, 5,$qry_equipamentos["marca"],1,0,"L" );
	  $pdf->Cell( 40, 5,$qry_equipamentos["largura"]." X ".$qry_equipamentos["altura"]." X ".$qry_equipamentos["profundidade"],1,1,"L" );
  
  
  }


}



?>




