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
        $TpVenda = 'or�amento';
    }
    else{
        $Venda = new Pedido($_REQUEST['ptp_venda'],$_REQUEST['pnumreg'],true,false);
        $TpVenda = 'pedido';
    }
}
$item_cont = 1;			 	// item da lista
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

$apresentativo = "Conforme sua solicita��o, informamos abaixo pre�o e demais condi��es de fornecimento de material a ser
utilizado em seu equipamento ";
// -----------------------------Busca Dados
$voltagem_equip = "";

$frete = $Venda->getDadosVenda('id_tp_frete');
if($frete == 1){
    $local_entrega = "Posto em sua empresa";
} else if($frete == 2) {
    $local_entrega = "Posto em nossa empresa - SP";
}

//-------------------------------------------


// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> CORPO DO DOCUMENTO
$pdf->Image('../../../images/ricall.jpg',20,13,55,25);

// Or�amento numero
$pdf->ln(35);
$pdf->Cell(0, 6, $Venda->getNumregVenda(), 0, 0, R);
// Data do Or�amento
$pdf->ln(4);
$pdf->Cell(0, 6, dten2br($Venda->getDadosVenda('dt_venda')), 0, 0, R);

// Conteudo
$pdf->ln(2);
$pdf->Write(6, "Empresa: ".$Venda->getPessoa()->getDadoPessoa('razao_social_nome'));

$pdf->ln(4);
$Contato = new Contato($Venda->getDadosVenda('id_contato'));
$pdf->Write(6, "A/C - Sr(a). ".$Contato->getNome());

$pdf->ln(4);
$pdf->Write(6, "Fone: ".$Contato->getTel1());

$pdf->SetFont("Arial", "", 10);		$pdf->ln(15);
$pdf->Write(4, $apresentativo.$equipamento.".");


			// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Titulos da Listagem do Servi�o
$pdf->SetFont("Arial", "B", 8);
$pdf->ln(10);	$pdf->Cell(8);
$pdf->Cell(10, 6, "Item.", 1, 0, C, true);
$pdf->Cell(60, 6, "C�d.", 1, 0, C, true);
$pdf->Cell(20, 6, "Qtde.", 1, 0, C, true);
$pdf->Cell(30, 6, "Pre�o Un.", 1, 0, C, true);
$pdf->Cell(35, 6, "Total S/ IPI.", 1, 0, C, true);
$pdf->Cell(20, 6, "IPI", 1, 0, C, true);
$pdf->ln(6);	$pdf->Cell(8);
$pdf->Cell(175, 6, "Descri��o", 1, 0, C, true);


			// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Listagem do Servi�o
$pdf->SetFont("Arial", "", 8);

$i = 0;
$TOTAL = 0;
foreach($Venda->getItens() as $IndiceItem => $Item){
    if($Venda->getTipoVenda() == 1 && $Item->getDadosVendaItem('sn_item_perdido') == 1){
        continue;
    }
    $i++;
    if($i % 2 == 0){
        $cor = true;
    } else {
        $cor = false;
    }
    if(!$Item->getItemComercial()){
        $IPI = '*';
    } else {
        $IPI = str_replace('.',',',$Item->getDadosVendaItem('pct_aliquota_ipi'));
    }

$pdf->ln(6);	$pdf->Cell(8);
$pdf->Cell(10, 6, $item_cont, 1, 0, C, $cor);
$pdf->Cell(60, 6, $Venda->NFQ($Item->getDadosVendaItem('id_produto')), 1, 0, C, $cor);
$pdf->Cell(20, 6, $Venda->NFQ($Item->getDadosVendaItem('qtde')), 1, 0, C, $cor);
$pdf->Cell(30, 6, $Venda->NFV($Item->getDadosVendaItem('vl_unitario_com_descontos')), 1, 0, C, $cor);
$pdf->Cell(35, 6, $Venda->NFV($Item->getDadosVendaItem('vl_total_liquido')), 1, 0, C, $cor);
$pdf->Cell(20, 6, $IPI, 1, 0, C, $cor);
$pdf->ln(6);	$pdf->Cell(8);
$pdf->Cell(175, 6, "- ".$Item->getNomeProduto(), 1, 0, L, $cor);

$TOTAL += $Item->getDadosVendaItem('vl_total_liquido');
$item_cont++;
}


$pdf->ln(6);	$pdf->Cell(8);
$pdf->Cell(110, 6, "Total S/ IPI", 1, 0, C);
$pdf->Cell(65, 6, $Venda->NFV($TOTAL), 1, 0, C);


			// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> FIM DA LISTAGEM


$pdf->SetFont("Arial", "B", 10);

$pdf->ln(15);	$pdf->Cell(0, 6, "Condi��es Gerais de Fornecimento :", 0, 0, C);


$pdf->SetFont("Arial", "", 10);

$pdf->ln(10);
$pdf->Write(6, '� Condi��es de Pagamento : '.$Venda->getDadosCondPagto('nome_cond_pagto').' (Mediante aprova��o do dept financeiro).');

$pdf->SetFont("Arial", "", 10);
$pdf->ln(6);
$pdf->Write(6, '� Prazo de Entrega: 30 dias*, a contar da data da confirma��o de pedido.');
//dten2br($Venda->getDadosVenda('dt_entrega'))
$pdf->SetFont("Arial", "B", 6);
$pdf->ln(4);
$pdf->Write(6, '     (* podendo variar de acordo com a disponibilidade de estoque do fabricante, libera��o alfandeg�ria ou de qualquer outro motivo alheio a nossa interven��o direta.).');

$pdf->SetFont("Arial", "", 10);
$pdf->ln(6);
$pdf->Write(6, '� Local de Entrega: '.$local_entrega);

if($_REQUEST['ptp_venda'] == 1){
    $pdf->ln(6);
    $pdf->Write(6, '� Validade da Proposta: '.dten2br($Venda->getDadosVenda('dt_validade_orcamento')));
}

$pdf->ln(6);
$pdf->Write(6, '� Para Servi�o em Cilindro ou Resist�ncia, favor informar a Voltagem de seu Equipamento : '.$voltagem_equip);

$pdf->ln(6);
$pdf->Write(6, '� Garantia: 30 (trinta) dias ap�s entrega. ');
$pdf->SetFont("Arial", "B", 6);		$pdf->ln(4);
$pdf->Write(4, '     (1- Caso constatado defeito de fabrica��o;  2 - GARANTIA - exceto consum�veis - Se instalado por um t�cnico autorizado)).');

$pdf->SetFont("Arial", "", 10);		$pdf->ln(6);
$pdf->Write(4, '� M�o de obra: N�o inclusa.');
$pdf->SetFont("Arial", "B", 6);		$pdf->ln(4);
$pdf->Write(3, '     Quando necess�rio a interven��o de nossos profissionais, para remo��o ou instala��o de componentes e/ou equipamentos, o atendimento t�cnico � cobrado separadamente. Favor contatar o nosso departamento de Assist�ncia T�cnica.');
$pdf->ln(4);
$pdf->Write(3, '     A m�o de obra descrita neste '.$TpVenda.' refere-se aos trabalhos de interven��o do equipamento dentro de nossa f�brica.**');

$pdf->ln(8);
$pdf->Write(6, '     ** Sujeito a Altera��o');


$pdf->ln(10);
$pdf->Write(6, 'Atenciosamente, ');


// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>		ASSINATURAS

$pdf->SetFont("Arial", "B", 10);
$pdf->ln(10);
$pdf->Cell(4);
$Usuario = new Usuario($Venda->getDadosVenda('id_usuario_cad'));
$pdf->Cell(125, 4, $Usuario->getNome(), 0, L);
$pdf->Cell(100, 4, "Selma Espadaro", 0, L);

$pdf->ln(4);
$pdf->Cell(4);
$pdf->Cell(125, 4, 'Vendas - Depto. Pe�as', 0, L);
$pdf->Cell(100, 4, 'Coordenadora - Depto. Pe�as', 0, L);

$pdf->ln(4);
$pdf->Cell(4);
if($Usuario->getDadosUsuario('tel1')) {
   $pdf->Cell(125, 4, 'Fone: '.$Usuario->getDadosUsuario('tel1'), 0, L);
}else {
   $pdf->Cell(125, 4, "Fax: (11) 2164-1972", 0, L);
}
$pdf->Cell(100, 4, "Fone: (11) 2164-1948", 0, L);

$pdf->ln(4);
$pdf->Cell(4);
if($Usuario->getDadosUsuario('tel1')) {
   $pdf->Cell(125, 4, "Fax: (11) 2164-1972", 0, L);
   $mail = true;
} else {
   $pdf->Cell(125, 4, $Usuario->getDadosUsuario('email'), 0, L);
   $mail = false;
}
$pdf->Cell(100, 4, "Fax.: (11) 2164-1972", 0, L);

$pdf->ln(4);
$pdf->Cell(4);
if($Usuario->getDadosUsuario('email') && $mail) {
   $pdf->Cell(125, 4, $Usuario->getDadosUsuario('email'), 0, L);
} else {
   $pdf->Cell(125, 4, "", 0, L);
}
$pdf->Cell(100, 4, "selma@alphaprint.com.br", 0, L);

$pdf->ln(8);
$pdf->Cell(0,4,'Rua Dona Ana Neri, 697 - CAMBUCI - S�o Paulo - S.P - Cep. 01522-000', 0, 0, C);

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