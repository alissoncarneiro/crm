<?php

header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
$odbc_c = true;
$PrefixoIncludes = '../';
include('../includes.php');

function EscreveSQL($campo, $tabela, $validar, $resultado) {
    $sql_func = "select $campo from $tabela where $validar = '$resultado'";
    $exec_func = @mysql_query($sql_func) or die("ERRO MYSQL: " . mysql_error());
    $ver_func = mysql_fetch_array($exec_func);
    return $ver_func[0];
}

if (empty($_REQUEST['pnumreg'])) {
    echo getError('0040010001', getParametrosGerais('RetornoErro'));
    exit;
} else {
    if ($_REQUEST['ptp_venda'] == 1) {
        $Venda = new Orcamento($_REQUEST['ptp_venda'], $_REQUEST['pnumreg'], true, false);
    } else {
        $Venda = new Pedido($_REQUEST['ptp_venda'], $_REQUEST['pnumreg'], true, false);
    }
}

$contagem_item = 1;
define("FPDF_FONTPATH", "../../../bibliotecas/fpdf/font/");
require("../../../bibliotecas/fpdf/fpdf.php");
$pdf = new FPDF();
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

// Texto padr�o
$texto1 = "CONDI��ES DE FORNECIMENTO - IMPORTANTE";
$texto2 = "1-DESCRI��O DO(S) PRODUTO(S) E SERVI�O(S) DESTA PROPOSTA:";
$texto3 = "2-CONDI��O DE PAGAMENTO DESTE OR�AMENTO:";
$texto4 = "3-Prazo de entrega � de 07 dias �teis ap�s aprova��o.";
$texto5 = "4-Valor total desta proposta : R$41.396,00 com todos os impostos inclusos.";
$texto6 = "5-Garantia de 12 (DOZE) meses para PRODUTOS posto fabrica a partir da data da Nota.";
$texto7 = "6-Garantia de 03 (TRES) meses para SERVI�OS posto fabrica a partir da data da Nota
Fiscal.";
$texto8 = "7-Cancelamento do pedido somente com 5 (CINCO) dias de anteced�ncia da entrega.";
$texto9 = "8-O PEDIDO SOMENTE TER� VALIDADE A PARTIR DO ENVIO DOS DOCUMENTOS:
- Proposta Aprovada com Assinatura do Cliente
- Ficha de configura��o totalmente preenchida se o produto suportar
- Requisitos do projeto totalmente preenchidos se o produto suportar";
$texto10 = "9-Acompanha esta proposta o descritivo do produto.";
$texto11 = "10-Validade desta proposta : 7 dias";
$texto12 = "a)Se existir INSTALA��O, dever� ser agendada antecipadamente com a DIGIVOICE.
Consulte-nos no EscreveSQL($campo, $tabela, $validar, $resultado)
fechamento do Pedido.
b)Caso a instala��o do equipamento exigir a presen�a da Mantenedora do PABX, o custo
ser� por conta do cliente.
c)A DIGIVOICE n�o se responsabiliza por danos no equipamento caso a instala��o n�o
seja feita por t�cnico devidamente qualificado.
d)Frete por conta do Cliente, FAVOR INDICAR NO CAMPO ABAIXO DESTA PROPOSTA.
e)Dep�sitos poder�o ser feitos no Banco Bradesco Ag. 1382-0 C/C 18.474-8 ou Banco do
Brasil Ag. 1821-X C/C 190801-4
f)Comprovante (s) de dep�sito (s) dever� (�o) ser enviado (s) via fax (11-3061-3717) para
validar o pagamento.
g) SUPORTE VIA FORUM - INCLUSO
h) OPCIONAL: PARA SUPORTE TELEF�NICO , E-MAIL, INSTALA��O e VISITA T�CNICA (CONSULTE
NOSSOS PRE�OS).
";


// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> CORPO DO DOCUMENTO


// Data do Or�amento
$pdf->SetFont("Arial", "B", 15);
$pdf->ln(20);
$pdf->Cell(0, 5, " DIGIVOICE TECNOLOGIA EM ELETR�NICA LTDA. ", 0, 1, C);

// Identifica��o doc
$pdf->SetFont("Arial", "B", 10);
$pdf->ln(2);
$pdf->Cell(0, 5, "R_ORCACODSDOC", 0, 1, R);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0, 5, date('d/m/Y'), 0, 1, R);

$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(0, 5, "CNPJ:                         66.705.674/0001-08", 0, 0, L);
$pdf->Cell(-100, 5, "INSC:                         206.070.410.112", 0, 1, C);
$endereco = "ALAMEDA JURU�, 159 - CENTRO EMPRESARIAL ALPHAVILLE
                       ALPHAVILLE
                       BARUERI      SP       06455-010";
$pdf->Write(5, "ENDERE�O: ".$endereco, L);
$pdf->ln();
$pdf->Cell(0, 5, "TELEFONES: (11)3081 8877 -Depto Vendas / (11)2191 6365 -Depto Tecnico", 0, 1, L);
$pdf->Cell(0, 5, "E-MAIL: ", 0, 1, L);

// Conteudo
$pdf->SetFont("Arial", "B", 17);
$pdf->ln(10);
$pdf->Cell(0, 5, "Or�amentos/Reserva", 0, 1, C);
$pdf->ln(3);


// Or�amento numero
$pdf->ln(2);
$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(0, 5, "N�mero: " . $Venda->getNumregVenda(), 0, 0, L);
$pdf->Cell(-220, 5, "Vendedor: " . EscreveSQL('nome_usuario', 'is_usuario', 'id_usuario', $Venda->getNumregVenda('id_usuario_cad')), 0, 0, C);
$pdf->Cell(0, 5, "DATA: " . dten2br($Venda->getDadosVenda('dt_venda')), 0, 1, R);
$pdf->Cell(0, 5,  EscreveSQL('numreg', 'is_pessoa', 'razao_social_nome', $Venda->getPessoa()->getDadoPessoa('razao_social_nome'))." - ".$Venda->getPessoa()->getDadoPessoa('razao_social_nome'), 0, 1, L);
$pdf->Cell(0, 5, "CNPJ:     " . $Venda->getPessoa()->getDadoPessoa('cnpj_cpf'), 0, 1, L);
$pdf->Cell(0, 5, "Endere�o:     " . $Venda->getDadosEnderecoEntrega('endereco'), 0, 1, L);
$pdf->Cell(0, 5, $Venda->getDadosEnderecoEntrega('bairro'), 0, 1, l);
$pdf->Cell(0, 5, $Venda->getDadosEnderecoEntrega('cidade') . "              " . $Venda->getDadosEnderecoEntrega('uf') . "             " .  $Venda->getPessoa()->getDadoPessoa('cep'), 0, 1, L);
$pdf->Cell(-220, 5, "Telefones:     " . $Venda->getPessoa()->getDadoPessoa('tel1') ."          " . $Venda->getPessoa()->getDadoPessoa('tel2'), 0, 1, C);
$pdf->Cell(0, 5, "Parcelamento:   " . $Venda->getDadosCondPagto('nome_cond_pagto'), 0, 1, R);

// Descricao do produto
$pdf->ln(5);
$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(15, 6, "Qtde.", 1, 0, C);
$pdf->Cell(90, 6, "Descri��o", 1, 0, C);
$pdf->Cell(25, 6, "Un. medida", 1, 0, C);
$pdf->Cell(20, 6, "Vl. unit", 1, 0, C);
$pdf->Cell(20, 6, "Desc.", 1, 0, C);
$pdf->Cell(25, 6, "Vl. total", 1, 0, C);
// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Listagem do Servi�o
$pdf->SetFont("Arial", "", 7);

foreach ($Venda->getItens() as $IndiceItem => $Item) {
    if ($Venda->getTipoVenda() == 1 && $Item->getDadosVendaItem('sn_item_perdido') == 1) {
        continue;
    }
    $pdf->ln(6);
    $pdf->Cell(15, 6, $Venda->NFQ($Item->getDadosVendaItem('qtde')), 1, 0, C);
    $total_item = $total_item + $Item->getDadosVendaItem('qtde');
    $pdf->Cell(90, 6, $Item->getNomeProduto(), 1, 0, L);
    $pdf->Cell(25, 6, EscreveSQL("nome_unid_medida", "is_unid_medida", "numreg", $Item->getDadosVendaItem('id_unid_medida')), 1, 0, C);
    $pdf->Cell(20, 6, $Venda->NFV($Item->getDadosVendaItem('vl_unitario_com_descontos')), 1, 0, C);
    $pdf->Cell(20, 6, "", 1, 0, C);
    $vl_total = $vl_total + $Item->getDadosVendaItem('vl_unitario_com_descontos');
    $pdf->Cell(25, 6, $Venda->NFV($vl_total), 1, 0, C);
    $vl_total_itens = $vl_total_itens + $vl_total;
    $total_imposto = $total_imposto + $Item->getDadosVendaItem('pct_aliquota_ipi');


    foreach($Venda->getArrayCamposDescontos() as $IndiceCampoDesconto => $CampoDesconto){
                //$Item->ValidaPoliticaComercialCampoDesconto($IndiceCampoDesconto);
                //$Item->setPctDescontoItemDesconto($IndiceCampoDesconto,$Item->getPoliticaComercialCampoDesconto()->getPctMaxCampoDescontoItem());
                //$Item->setPctDescontoItemDesconto($IndiceCampoDesconto,$Item->getPoliticaComercialCampoDesconto()->getPctMaxCampoDescontoItem());
        
            $desconto = $desconto + $Item->getDescontoItem($IndiceCampoDesconto);

        }


}

    // Valores somados
    $pdf->ln(7);
    $pdf->Cell(15, 6, $Venda->NFQ($total_item), 1, 0, C);
    $pdf->SetFont("Arial", "B", 10);
    $pdf->Cell(0, 6,"Total dos produtos:       " . str_replace(".", ",", $vl_total_itens), 0, 1, R);
    $pdf->Cell(0, 6,"Total dos servi�os:        " . str_replace(".", ",", $vl_total_itens), 0, 1, R);
    $pdf->Cell(15, 6,"Validade:       " . $Venda->getNumregVenda('dt_entrega'), 0, 0, C);
    $pdf->Cell(0, 6,"Imposto:                         " . $Venda->NFV($total_imposto), 0, 1, R);
    $pdf->Cell(0, 6,"Desconto:                              " . $Venda->NFV($desconto), 0, 1, R);
    $vl_total_orcamento = $vl_total_itens + $total_imposto + $desconto;
    $pdf->Cell(0, 6,"Total:                       R$ " . $Venda->NFV($vl_total_orcamento), 0, 1, R);
    $pdf->ln(5);
    $pdf->Cell(0, 5, "CATEGORIA: " . EscreveSQL("nome_destino_mercadoria", "is_destino_mercadoria", "numreg", $Venda->getDadosVenda('id_destino_mercadoria')), 0, 1, L);
    $pdf->Cell(0, 5, "TIPO VENDA/DESTINO: REVENDER", 0, 1, L);

// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> FIM DA LISTAGEM
// exibindo texto padr�o
$texto1 = "
Demais condi��es.:
===================
1-Prazo de entrega � de ( 07 DDL )dias �teis ap�s aprova��o.
2-Garantia de 12 (DOZE) meses para PRODUTOS posto fabrica a partir da data da Nota Fiscal.
3-Garantia de 03 (TRES) meses para SERVI�OS posto fabrica a partir da data da Nota Fiscal.
4-Cancelamento do pedido somente com 5 (CINCO) dias de anteced�ncia da entrega.
5-O PEDIDO SOMENTE TER� VALIDADE A PARTIR DO ENVIO DOS DOCUMENTOS:
- Proposta Aprovada com Assinatura do Cliente
- Ficha de configura��o totalmente preenchida se o produto suportar
- Requisitos do projeto totalmente preenchidos se o produto suportar
6-Acompanha esta proposta o descritivo do produto.
7-Validade desta proposta ser� at�: ( 07 ) dias
* Condi��o de pagamento deste or�amento s� com cadastro APROVADO e para valores acima de R$600,00.
* LOCU��O FEMININA - QUALQUER ALTERA��O FEITA NO TEXTO AP�S SUA GRAVA��O TER� UM TAXA
ADICIONAL NO VALOR DE R$ 100,00
OUTRAS OBSERVA��ES.:
=====================
a)Se existir INSTALA��O, dever� ser agendada antecipadamente com a DIGIVOICE. Consulte-nos no fechamento do Pedido.
b)Caso a instala��o do equipamento exigir a presen�a da Mantenedora do PABX, o custo ser� por conta do cliente.
c)A DIGIVOICE n�o se responsabiliza por danos no equipamento caso a instala��o n�o seja feita por t�cnico devidamente
qualificado.
d)Frete FOB, por conta do Cliente, FAVOR INDICAR NO CAMPO ABAIXO DESTA PROPOSTA.
e)Dep�sitos poder�o ser feitos no Banco Bradesco Ag. 1382-0 C/C 18.474-8 ou Banco do Brasil Ag. 1821-X C/C 190801-4
f)Comprovante (s) de dep�sito (s) dever� (�o) ser enviado (s) via fax ( 11)-3061-3717, para validar o pagamento.
G) SUPORTE VIA F�RUM.
H) OPCIONAL.: SUPORTE VIA E-MAIL, TELEFONE E VISITA T�CNICA. (SOLICITAR COTA��O).
I) N�O INCLUSO INSTALA��O.
J) A Nota Fiscal � desmembrada em Hardware e software, sendo Nota fiscal de venda para Hardware e nota fiscal de servi�o para
Software.
";

$pdf->SetFont("Arial", "", 10);
$pdf->ln(10);
$pdf->Write(5, $texto1, L);

// Informa��es finais
$pdf->SetFont("Arial", "", 10);
$pdf->ln(10);
$pdf->Cell(0, 5, "TRANSPORTADORA PARA ENVIO DO MATERIAL: __________________________________________________", 0, 1, L);
$pdf->SetFont("Arial", "", 8);
$pdf->Cell(0, 5, "OBS.: N�o enviamos material de venda por Correio (Sedex)", 0, 1, L);

$pdf->ln(2);
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 5, "TELEFONE DA TRANSPORTADORA: (___)_________________________", 0, 1, L);

$pdf->ln(3);
$pdf->Cell(0, 5, "DE ACORDO:", 0, 1, L);

$pdf->ln(2);
$pdf->Cell(0, 5, "DATA APROVA��O: ______/ _______/______________", 0, 1, L);

$pdf->ln(10);
$pdf->Cell(0, 5, "ASSINATURA E CARIMBO: ___________________________________________________________________", 0, 1, L);

$pdf->ln(2);
$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(0, 5, "Obs. Enviar via fax (11) 3061-3717 - ap�s APROVA��O deste or�amento.", 0, 1, L);

$pdf->ln(30);
$pdf->Write(5, "Digivoice Tecnologia em Eletr�nica Ltda.                                                               CNPJ: 66.705.674/00001-08
Rua: Mateus Grou, 109 ? 1o e 2o andar ? Pinheiros ? SP ? 05415-050                I.E.: 206.070.410.112
Fone: (011) 3016-5200 Fax : 3061-3717 ? www.digivoice.com.br", L);


// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> FIM DO CORPO DO DOCUMENTO
$nome_arquivo_envio = $_REQUEST['ptp_venda'] . '_' . $Venda->getNumregVenda() . '.pdf';
if ($envio_em_anexo > 0) {
    if (file_exists('arquivos_gerados/' . $nome_arquivo_envio)) {
        unlink('arquivos_gerados/' . $nome_arquivo_envio);
    }
    $pdf->Output('arquivos_gerados/' . $nome_arquivo_envio, 'F');
} else {
    $pdf->Output($nome_arquivo_envio, 'I');
}
?>