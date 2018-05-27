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
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> FIM MY SQL

$apresentativo = "Conforme solicita��o de v.sas., informamos abaixo, para importa��o direta por v.sas., pre�o e demais condi��es de fornecimento para as pe�as a serem utilizadas em seu equipamento ";
// -----------------------------Busca Dados



//-------------------------------------------


// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> CORPO DO DOCUMENTO
$pdf->Image('../../../images/logo_login.jpg',20,13,60,20);

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
$pdf->SetFont("Arial", "B", 10);
$pdf->ln(10);
$pdf->Cell(2);
$pdf->Cell(10, 6, "#", 1, 0, C, true);
$pdf->Cell(10, 6, "Qtde.", 1, 0, C, true);
$pdf->Cell(85, 6, "Descri��o", 1, 0, C, true);
$pdf->Cell(35, 6, "Part Number", 1, 0, C, true);
$pdf->Cell(20, 6, "Pre�o Unit.", 1, 0, C, true);
$pdf->Cell(25, 6, "Pre�o Total", 1, 0, C, true);

$i = 0;
$TOTAL = 0;
foreach($Venda->getItens() as $IndiceItem => $Item){
    if($Venda->getTipoVenda() == 1 && $Item->getDadosVendaItem('sn_item_perdido') == 1){
        continue;
    }
    $i++;
    if($Item->getItemComercial()){
        $PartNumber = farray(query('select * from is_produto_cod_compl where id_produto=\''.$Item->getDadosVendaItem('id_produto').'\''));
        $partnumber = $PartNumber['id_produto_cod_compl'];
    } else {
        $partnumber = $Item->getDadosVendaItem('inc_cod_compl');
    }
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Listagem do Servi�o
    $pdf->SetFont("Arial", "", 8);
    $pdf->ln(6);
    $pdf->Cell(2);
    $pdf->Cell(10, 6, $i, 1, 0, C);
    $pdf->Cell(10, 6, $Venda->NFQ($Item->getDadosVendaItem('qtde')), 1, 0, C);
    $pdf->Cell(85, 6, $Item->getNomeProduto(), 1, 0, L);
    $pdf->Cell(35, 6, $partnumber, 1, 0, C);
    $pdf->Cell(20, 6, $Venda->NFV($Item->getDadosVendaItem('vl_unitario_com_descontos')), 1, 0, C);
    $pdf->Cell(25, 6, $Venda->NFV($Item->getDadosVendaItem('vl_total_liquido')), 1, 0, C);
    $TOTAL += $Item->getDadosVendaItem('vl_total_liquido');
}

$gasto_fca = 0; $gasto_fca = $ar_pedido['gasto_fca'];
$tot_fca = 0; $tot_fca = $ar_pedido['tot_fca'];

$pdf->ln(6);$pdf->Cell(2);
$pdf->Cell(155, 6, "GASTOS FCA  (PACKING + HANDLING):", 1, 0, R);
$pdf->Cell(30, 6, number_format($gasto_fca,2,',','.'), 1, 0, C);
$pdf->ln(6);$pdf->Cell(2);
$pdf->Cell(155, 6, "TOTAL FCA: ", 1, 0, R);
$pdf->Cell(30, 6, number_format($tot_fca,2,',','.'), 1, 0, C);

$vl_total = $TOTAL+$tot_fca;

$pdf->ln(6);$pdf->Cell(2);
$pdf->Cell(155, 6, 'Valor Total:', 1, 0, R);
$pdf->Cell(30, 6, number_format($vl_total,2,',','.'), 1, 0, C);

			// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> FIM DA LISTAGEM


$pdf->SetFont("Arial", "B", 10);

$pdf->ln(15);	$pdf->Cell(0, 6, "Condi��es Gerais de Fornecimento :", 0, 0, C);


$pdf->SetFont("Arial", "", 10);

$pdf->ln(10);
$pdf->Write(6, '� Prazo de Embarque: '.$pz_embarque);

$pdf->ln(6);
$pdf->Write(6, '� Condi��es de Pagamento : '.$Venda->getDadosCondPagto('nome_cond_pagto'));

$pdf->ln(6);
$pdf->Write(6, '� Validade da Proposta: '.dten2br($Venda->getDadosVenda('dt_validade_orcamento')));

$pdf->ln(6);
$pdf->Write(6, '� Garantia: 30 (trinta) dias da data do embarque; ');
$pdf->SetFont("Arial", "B", 6);		$pdf->ln(4);
$pdf->Write(6, '     (1- GARANTIA FCA - caso constatado defeito de fabrica��o;  2 - GARANTIA - exceto consum�veis).');

$pdf->SetFont("Arial", "", 10);		$pdf->ln(6);
$pdf->Write(6, '� Favor nos enviar as instru��es de embarque na confirma��o do pedido;');
$pdf->SetFont("Arial", "B", 6);		$pdf->ln(4);
$pdf->Write(6, '     Ser� necess�rio o uso do agente de cargas do fabricante.');

$pdf->SetFont("Arial", "", 10);		$pdf->ln(6);
$pdf->Write(4, '� Quando necess�rio � interven��o de nossos profissionais, para remo��o ou instala��o de componentes e/ou equipamentos, o atendimento t�cnico � cobrado separadamente. Favor contatar nosso departamento de Assist�ncia T�cnica, para formaliza��o do '.$TpVenda.' de m�o de obra, bem como, agendar a data da visita.');

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