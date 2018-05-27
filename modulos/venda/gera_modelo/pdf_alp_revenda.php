<?php
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
$odbc_c = true;
$PrefixoIncludes = '../';
include('../includes.php');


if(empty($_REQUEST['pnumreg'])){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
else{
    if($_REQUEST['ptp_venda'] == 1){
        $Venda = new Orcamento($_REQUEST['ptp_venda'],$_REQUEST['pnumreg'],true,false);
        $TpVenda = 'orçamento';
    }
    else{
        $Venda = new Pedido($_REQUEST['ptp_venda'],$_REQUEST['pnumreg'],true,false);
        $TpVenda = 'pedido';
    }
}
$contagem_item = 1;
define("FPDF_FONTPATH", "../../../bibliotecas/fpdf/font/");
require("../../../bibliotecas/fpdf/fpdf.php");
$pdf   =   new FPDF();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(237, 251, 252);

$ar_month = array(
'01' => 'Janeiro',
'02' => 'Fevereiro',
'03' => 'Mar&ccedil;o',
'04' => 'Abril',
'05' => 'Maio',
'06' => 'Junho',
'07' => 'Julho',
'08' => 'Agosto',
'09' => 'Setembro',
'10' => 'Outubro',
'11' => 'Novembro',
'12' => 'Dezembro'
);
// Texto padrão
$apresentativo = "Atendendo solicitação, informamos abaixo o preço e demais condições de fornecimento para as peças a serem utilizadas em seu equipamento.";



// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> CORPO DO DOCUMENTO
$pdf->Image('../../../images/logo_login.jpg',20,13,60,20);

/* Se for um pedido exibe o número de pedido do cliente, caso contrário (orçamento) exibe o numreg do registro */
$NumeroVenda = ($Venda->isPedido())?$Venda->getDadosVenda('id_venda_cliente'):'Nº '.$Venda->getNumregVenda();

// Orçamento numero
$pdf->ln(35);
$pdf->Cell(0, 6, $NumeroVenda, 0, 0, R);
// Data do Orçamento
$pdf->ln(4);
$pdf->Cell(0, 6, "".dten2br($Venda->getDadosVenda('dt_venda'))."", 0, 0, R);

$pdf->ln(4);
$pdf->Cell(0, 6, "CNPJ: ".$Venda->getPessoa()->getDadoPessoa('cnpj_cpf'), 0, 0, R);

// Conteudo
$pdf->ln(2);
$pdf->Write(4, "Empresa: ".$Venda->getPessoa()->getDadoPessoa('razao_social_nome'));

$pdf->ln(4);
$pdf->Cell(18, 4, "Endereço: ". $Venda->getDadosEnderecoEntrega('endereco'). ' - ' .$Venda->getDadosEnderecoEntrega('bairro'). ' - '. $Venda->getDadosEnderecoEntrega('uf'). ' - '. $Venda->getDadosEnderecoEntrega('cep'), 0, L);

$pdf->ln(4);
$Contato = new Contato($Venda->getDadosVenda('id_contato'));
$pdf->Write(4, "Contato: ".$Contato->getNome());

$pdf->ln(4);
$pdf->Write(4, "Fone: ".$Contato->getTel1());

$pdf->SetFont("Arial", "", 10);

$pdf->ln(15);
$pdf->Write(4, $apresentativo.$equipamento.".");


			// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Titulos da Listagem do Serviço
$pdf->SetFont("Arial", "B", 10);
$pdf->ln(10);
$pdf->Cell(2);
$pdf->Cell(5, 6, "#", 1, 0, C, true);
$pdf->Cell(10, 6, "Qtde.", 1, 0, C, true);
$pdf->Cell(85, 6, "Descrição", 1, 0, C, true);
$pdf->Cell(30, 6, "Part Number", 1, 0, C, true);
$pdf->Cell(20, 6, "Dt. Entrega", 1, 0, C, true);
$pdf->Cell(20, 6, "Preço Unit.", 1, 0, C, true);
$pdf->Cell(10, 6, "% IPI", 1, 0, C, true);



			// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Listagem do Serviço
$pdf->SetFont("Arial", "", 8);
foreach($Venda->getItens() as $IndiceItem => $Item){
    if($Venda->getTipoVenda() == 1 && $Item->getDadosVendaItem('sn_item_perdido') == 1){
        continue;
    }
    if($Item->getItemComercial()){
        $PartNumber = farray(query('select * from is_produto_cod_compl where id_produto=\''.$Item->getDadosVendaItem('id_produto').'\''));
        $partnumber = $PartNumber['id_produto_cod_compl'];
    } else {
        $partnumber = $Item->getDadosVendaItem('inc_cod_compl');
    }
    $pdf->ln(6);
    $pdf->Cell(2);
    $pdf->Cell(5, 6, $contagem_item++, 1, 0, C);
    $pdf->Cell(10, 6, $Venda->NFQ($Item->getDadosVendaItem('qtde')), 1, 0, C);
    $pdf->Cell(85, 6, $Item->getNomeProduto(), 1, 0, L);
    $pdf->Cell(30, 6, $partnumber, 1, 0, C);
    $pdf->Cell(20, 6, uB::DataEn2Br($Item->getDadosVendaItem('dt_entrega'),false).'*', 1, 0, C);
    $pdf->Cell(20, 6, $Venda->NFV($Item->getDadosVendaItem('vl_unitario_com_descontos')), 1, 0, C);
    if(!$Item->getItemComercial()){
        $IPI = '*';
    } else {
        $IPI = str_replace('.',',',$Item->getDadosVendaItem('pct_aliquota_ipi'));
    }

    $pdf->Cell(10, 6, $IPI, 1, 0, C);
}
$pdf->ln(6);$pdf->Cell(2);
$pdf->Cell(150, 6, 'Valor Total s/ IPI', 1, 0, R);
$pdf->Cell(30, 6, $Venda->NFV($Venda->getVlTotalVendaLiquido()), 1, 0, C);

			// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> FIM DA LISTAGEM


$pdf->SetFont("Arial", "B", 10);
$pdf->ln();
$pdf->Cell(0, 6, "IPI não incluso a ser informado na ocasião do pedido(nacionalização do item)", 0, 0, C);
$pdf->ln(15);
$pdf->Cell(0, 6, "Condições Gerais de Fornecimento:", 0, 0, C);


$pdf->SetFont("Arial", "", 10);
$pdf->ln(10);
$pdf->Write(6, '× Condições de Pagamento: '.$Venda->getDadosCondPagto('nome_cond_pagto'));
$pdf->SetFont("Arial", "B", 6);
$pdf->ln(4);
$pdf->Write(6, '     (Mediante aprovação do dept financeiro).');


$pdf->SetFont("Arial", "", 10);
$pdf->ln(6);
$pdf->Write(6, '× Prazo de Entrega: * a contar da data da confirmação de pedido.');
//dten2br($Venda->getDadosVenda('dt_entrega'))
$pdf->SetFont("Arial", "B", 6);
$pdf->ln(4);
$pdf->Write(6, '     (* podendo variar de acordo com a disponibilidade de estoque do fabricante, liberação alfandegária ou de qualquer outro motivo alheio a nossa intervenção direta.).');

for($x = 0; $x < $nrows; $x++){
	//$pdf->Write(7.5, '     (A partir do pedido).');
	$pdf->ln(3);
	$itnumber = $x+1;
	$pdf->Write(7, ' N. do item:	'.$itnumber);
	$sql_itens_data = 'SELECT * FROM '.$tabela_itens_datas.' WHERE id_pedido = \''.$ar_pedido['id_pedido'].'\' AND id_produto = \''.$ar_item_data[$x].'\'';
	$qry_itens_data = mysql_query($sql_itens_data);
	while($ar_itens_data = mysql_fetch_array($qry_itens_data)){
		$pdf->ln(3);
		$pdf->Write(7.5, '		Qtd: '.str_pad($ar_itens_data['qtd'], 30, ' ', STR_PAD_RIGHT).'Data Prevista: '.$ar_itens_data['datas']);
	}
}

$pdf->SetFont("Arial", "", 10);
if($_REQUEST['ptp_venda'] == 1){
    $pdf->ln(6);
    $pdf->Write(6, '× Validade da Proposta: '.dten2br($Venda->getDadosVenda('dt_validade_orcamento')));
}

$pdf->ln(6);
$pdf->Write(6, '× Garantia: 30 (trinta) dias. A contar da data da entrega. ');
$pdf->SetFont("Arial", "B", 8);
$pdf->ln(5); $pdf->Cell(5);
$pdf->MultiCell(180, 3, '(1- GARANTIA - Caso seja constatado defeito de fabricação; 2- GARANTIA - Exceto consumíveis; 3- GARANTIA - Caso o item seja instalado por um técnico autorizado ALPHAPRINT).', 0);

$pdf->SetFont("Arial", "", 10);
if($Venda->getDadosVenda('id_tp_frete') != ''){
    $pdf->ln(6);
    $pdf->Write(4, '× Frete');
    $pdf->SetFont("Arial", "B", 8);
    $pdf->ln(5);$pdf->Cell(5);
    if($Venda->getDadosVenda('id_tp_frete') == '1'){ /* CIF */
        $pdf->MultiCell(180, 3, 'posto/empresa', 0);
    }
    elseif($Venda->getDadosVenda('id_tp_frete') == '2'){/* FOB */
        $pdf->MultiCell(180, 3, 'posto nossa empresa', 0);
    }
}
$pdf->SetFont("Arial", "", 10);
$pdf->ln(6);
$pdf->Write(4, '× Mão de obra não inclusa');
$pdf->SetFont("Arial", "B", 8);
$pdf->ln(5);$pdf->Cell(5);
$pdf->MultiCell(180, 3, 'Quando necessário à intervenção de nossos profissionais, para remoção ou instalação de componentes e/ou equipamentos, o atendimento técnico é cobrado separadamente. Favor contatar nosso departamento de Assistência Técnica, para formalização do '.$TpVenda.' de mão de obra, bem como, agendar a data da visita.', 0);

$pdf->ln(10);
$pdf->Write(6, 'Atenciosamente, ');


// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>		ASSINATURAS

$Usuario = new Usuario($Venda->getDadosVenda('id_usuario_cad'));

$ArrayAssinaturaRodapeEsquerda = array();
$ArrayAssinaturaRodapeEsquerda[1] = $Usuario->getNome();
$ArrayAssinaturaRodapeEsquerda[2] = 'Vendas - Depto. Peças';
if($Usuario->getDadosUsuario('tel1') == ''){
    $ArrayAssinaturaRodapeEsquerda[3] = 'Fax.: (11) 2164-1972';
    $ArrayAssinaturaRodapeEsquerda[4] = $Usuario->getDadosUsuario('email');
}
else{
    $ArrayAssinaturaRodapeEsquerda[3] = 'Fone: '.$Usuario->getDadosUsuario('tel1');
    $ArrayAssinaturaRodapeEsquerda[4] = 'Fax.: (11) 2164-1972';
    $ArrayAssinaturaRodapeEsquerda[5] = $Usuario->getDadosUsuario('email');
}

$ArrayAssinaturaRodapeDireita = array(
    '1' => 'Selma Espadaro',
    '2' => 'Coordenadora - Depto. Peças',
    '3' => 'Fone: (11) 2164-1948',
    '4' => 'Fax.: (11) 2164-1972',
    '5' => 'selma@alphaprint.com.br'
);

$pdf->SetFont("Arial", "B", 10);
$pdf->ln(10);
$pdf->Cell(4);
$pdf->Cell(125, 4, $ArrayAssinaturaRodapeEsquerda[1], 0, L);
$pdf->Cell(100, 4, $ArrayAssinaturaRodapeDireita[1], 0, L);
$pdf->ln(4);
$pdf->Cell(4);
$pdf->Cell(125, 4, $ArrayAssinaturaRodapeEsquerda[2], 0, L);
$pdf->Cell(100, 4, $ArrayAssinaturaRodapeDireita[2], 0, L);
$pdf->ln(4);
$pdf->Cell(4);
$pdf->Cell(125, 4, $ArrayAssinaturaRodapeEsquerda[3], 0, L);
$pdf->Cell(100, 4, $ArrayAssinaturaRodapeDireita[3], 0, L);
$pdf->ln(4);
$pdf->Cell(4);
$pdf->Cell(125, 4, $ArrayAssinaturaRodapeEsquerda[4], 0, L);
$pdf->Cell(100, 4, $ArrayAssinaturaRodapeDireita[4], 0, L);
$pdf->ln(4);
$pdf->Cell(4);
$pdf->Cell(125, 4, $ArrayAssinaturaRodapeEsquerda[5], 0, L);
$pdf->Cell(100, 4, $ArrayAssinaturaRodapeDireita[5], 0, L);

// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> FIM DO CORPO DO DOCUMENTO
$nome_arquivo_envio = $_REQUEST['ptp_venda'].'_'.$Venda->getNumregVenda().'.pdf';
if($envio_em_anexo > 0){
    if(file_exists('arquivos_gerados/'.$nome_arquivo_envio)){
        unlink('arquivos_gerados/'.$nome_arquivo_envio);
    }
    $pdf->Output('arquivos_gerados/'.$nome_arquivo_envio, 'F');
} else {
    $pdf->Output($nome_arquivo_envio, 'I');
}
?>