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

// Texto padrão
$texto1 = "CONDIÇÕES DE FORNECIMENTO - IMPORTANTE";
$texto2 = "1-DESCRIÇÃO DO(S) PRODUTO(S) E SERVIÇO(S) DESTA PROPOSTA:";
$texto3 = "2-CONDIÇÃO DE PAGAMENTO DESTE ORÇAMENTO:";
$texto4 = "3-Prazo de entrega é de 07 dias úteis após aprovação.";
$texto5 = "4-Valor total desta proposta : R$41.396,00 com todos os impostos inclusos.";
$texto6 = "5-Garantia de 12 (DOZE) meses para PRODUTOS posto fabrica a partir da data da Nota.";
$texto7 = "6-Garantia de 03 (TRES) meses para SERVIÇOS posto fabrica a partir da data da Nota
Fiscal.";
$texto8 = "7-Cancelamento do pedido somente com 5 (CINCO) dias de antecedência da entrega.";
$texto9 = "8-O PEDIDO SOMENTE TERÁ VALIDADE A PARTIR DO ENVIO DOS DOCUMENTOS:
- Proposta Aprovada com Assinatura do Cliente
- Ficha de configuração totalmente preenchida se o produto suportar
- Requisitos do projeto totalmente preenchidos se o produto suportar";
$texto10 = "9-Acompanha esta proposta o descritivo do produto.";
$texto11 = "10-Validade desta proposta : 7 dias";
$texto12 = "a)Se existir INSTALAÇÃO, deverá ser agendada antecipadamente com a DIGIVOICE.
Consulte-nos no
fechamento do Pedido.
b)Caso a instalação do equipamento exigir a presença da Mantenedora do PABX, o custo
será por conta do cliente.
c)A DIGIVOICE não se responsabiliza por danos no equipamento caso a instalação não
seja feita por técnico devidamente qualificado.
d)Frete por conta do Cliente, FAVOR INDICAR NO CAMPO ABAIXO DESTA PROPOSTA.
e)Depósitos poderão ser feitos no Banco Bradesco Ag. 1382-0 C/C 18.474-8 ou Banco do
Brasil Ag. 1821-X C/C 190801-4
f)Comprovante (s) de depósito (s) deverá (ão) ser enviado (s) via fax (11-3061-3717) para
validar o pagamento.
g) SUPORTE VIA FORUM - INCLUSO
h) OPCIONAL: PARA SUPORTE TELEFÔNICO , E-MAIL, INSTALAÇÃO e VISITA TÉCNICA (CONSULTE
NOSSOS PREÇOS).
";


// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> CORPO DO DOCUMENTO

$pdf->Image('../../../images/logo_login.png', 150, 5, 50);

// Data do Orçamento
$pdf->SetFont("Arial", "B", 12);
$pdf->ln(20);
$pdf->Cell(0, 5, "DATA: " . dten2br($Venda->getDadosVenda('dt_venda')) . "", 0, 1, L);

// Orçamento numero
$pdf->ln(2);
$pdf->Cell(0, 5, "PROPOSTA NUMERO: " . $Venda->getNumregVenda(), 0, 1, L);

// Conteudo
$pdf->Cell(0, 5, "CLIENTE: " . $Venda->getPessoa()->getDadoPessoa('razao_social_nome'), 0, 1, L);
$pdf->Cell(0, 5, "END.: " . $Venda->getDadosEnderecoEntrega('endereco') . ' - ' . $Venda->getDadosEnderecoEntrega('bairro'), 0, 1, L);
$pdf->Cell(0, 5, "CIDADE.: " . $Venda->getDadosEnderecoEntrega('cidade') . ' / ' . $Venda->getDadosEnderecoEntrega('uf'), 0, 1, L);
$pdf->Cell(0, 5, "CEP.: " . $Venda->getPessoa()->getDadoPessoa('cep'), 0, 1, L);
$pdf->Cell(0, 5, "CNPJ: " . $Venda->getPessoa()->getDadoPessoa('cnpj_cpf'), 0, 0, L);
$pdf->Cell(-220, 5, "INSC: " . $Venda->getPessoa()->getDadoPessoa('ie_rg'), 0, 1, C);
$pdf->Cell(0, 5, "A/C: " . $Venda->getPessoa()->getDadoPessoa('fantasia_apelido'), 0, 0, L);
$pdf->Cell(-220, 5, "FONE: " . $Venda->getPessoa()->getDadoPessoa('tel1'), 0, 1, C);
$pdf->Cell(0, 5, "CATEGORIA: " . EscreveSQL("nome_destino_mercadoria", "is_destino_mercadoria", "numreg", $Venda->getDadosVenda('id_destino_mercadoria')), 0, 1, L);
$pdf->Cell(0, 5, "TIPO VENDA/DESTINO: ORÇAMENTO", 0, 1, L);


// Responsavel
$Responsavel = farray(mysql_query("select * from is_usuario where id_usuario = ".$_SESSION['id_usuario']));

$pdf->ln(5);
$pdf->SetFont("Arial", "", 9);
$pdf->Cell(0, 5, "GERENTE DE CONTAS: " , 0, 0, L);
$pdf->Cell(-140, 5, "E-MAIL: " , 0, 1, C);
$pdf->Cell(0, 5, "FONE: " , 0, 0, L);
$pdf->Cell(-140, 5, "CEL: " , 0, 1, C);

$pdf->ln(2);
$pdf->SetFont("Arial", "", 9);
$pdf->Cell(0, 5, "CONSLTOR DE VENDAS: " . $Responsavel['nome_usuario'], 0, 0, L);
$pdf->Cell(-140, 5, "E-MAIL: " . $Responsavel['email'], 0, 1, C);
$pdf->Cell(0, 5, "FONE: " . $Responsavel['tel1'], 0, 0, L);
$pdf->Cell(-140, 5, "CEL: " . $Responsavel['tel2'], 0, 1, C);


// exibindo texto padrão
$pdf->ln(10);
$pdf->SetFont("Arial", "B", 18);
$pdf->Cell(0, 5, $texto1, 0, 1, L);
$pdf->SetFont("Arial", "", 10);
$pdf->ln(1);
$pdf->Cell(0, 5, $texto2, 0, 1, L);

// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Titulos da Listagem do Serviço
$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(2);
$pdf->Cell(180, 7, "PRODUTO", 1, 1, C);
$pdf->Cell(2);
$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(10, 6, "#", 1, 0, C, true);
$pdf->Cell(25, 6, "Qtde.", 1, 0, C, true);
$pdf->Cell(115, 6, "Descrição", 1, 0, C, true);
//$pdf->Cell(30, 6, "Part Number", 1, 0, C, true);
$pdf->Cell(30, 6, "Preço Unit.", 1, 0, C, true);
//$pdf->Cell(15, 6, "% IPI", 1, 0, C, true);
// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Listagem do Serviço
$pdf->SetFont("Arial", "", 8);

foreach ($Venda->getItens() as $IndiceItem => $Item) {
    if ($Venda->getTipoVenda() == 1 && $Item->getDadosVendaItem('sn_item_perdido') == 1) {
        continue;
    }
    $pdf->ln(6);
    $pdf->Cell(2);
    $pdf->Cell(10, 6, $contagem_item++, 1, 0, C);
    $pdf->Cell(25, 6, $Venda->NFQ($Item->getDadosVendaItem('qtde')), 1, 0, C);
    $pdf->Cell(115, 6, $Item->getNomeProduto(), 1, 0, L);
    //$pdf->Cell(30, 6, $partnumber, 1, 0, C);
    $pdf->Cell(30, 6, $Venda->NFV($Item->getDadosVendaItem('vl_unitario_com_descontos')), 1, 0, C);
    /*
      if(!$Item->getItemComercial()){
      $IPI = '*';
      } else {
      $IPI = str_replace('.',',',$Item->getDadosVendaItem('pct_aliquota_ipi'));
      }
      $pdf->Cell(15, 6, $IPI, 1, 0, C);
     */
}
/*
  $pdf->ln(6);$pdf->Cell(2);
  $pdf->Cell(145, 6, 'Valor Total', 1, 0, R);
  $pdf->Cell(35, 6, $Venda->NFV($Venda->getVlTotalVendaLiquido()), 1, 0, C);
 */
// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> FIM DA LISTAGEM
// exibindo texto padrão
$pdf->SetFont("Arial", "", 10);
$pdf->ln(10);
$pdf->Cell(0, 5, $texto3, 0, 1, L);

// Forma de pagamento
$pdf->ln(1);
$pdf->Cell(0, 5, "FORMA DE PAGAMENTO:" . $Venda->getDadosCondPagto('nome_cond_pagto'), 0, 1, L);

// Condição para exibir OBS
if ($Venda->getAprovadoComercial() === true) {
    $pdf->ln(5);
    $pdf->Write(6, 'OBS: ' . $Venda->getDadosVenda('obs'));
}

// exibindo texto padrão
$pdf->SetFont("Arial", "", 10);
$pdf->ln(10);
$pdf->Cell(0, 5, $texto4, 0, 1, L);

$pdf->ln(5);
$pdf->Cell(0, 5, $texto5, 0, 1, L);

$pdf->ln(5);
$pdf->Cell(0, 5, $texto6, 0, 1, L);

$pdf->ln(5);
$pdf->Cell(0, 5, $texto7, 0, 1, L);

$pdf->ln(5);
$pdf->Cell(0, 5, $texto8, 0, 1, L);

$pdf->ln(5);
$pdf->Write(5, $texto9);

$pdf->ln(10);
$pdf->Cell(0, 5, $texto10, 0, 1, L);

$pdf->ln(5);
$pdf->Cell(0, 5, $texto11, 0, 1, L);

$pdf->SetFont("Arial", "B", 11);
$pdf->ln(10);
$pdf->Cell(180, 5, "OUTRAS OBSERVAÇÕES", 0, 1, C);

$pdf->SetFont("Arial", "", 10);
$pdf->ln(2);
$pdf->Write(5, $texto12);


// Informações finais
$pdf->SetFont("Arial", "", 10);
$pdf->ln(10);
$pdf->Cell(0, 5, "TRANSPORTADORA PARA ENVIO DO MATERIAL: __________________________________________________", 0, 1, L);
$pdf->SetFont("Arial", "", 8);
$pdf->Cell(0, 5, "OBS.: Não enviamos material de venda por Correio (Sedex)", 0, 1, L);

$pdf->ln(2);
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, 5, "TELEFONE DA TRANSPORTADORA: (___)_________________________", 0, 1, L);

$pdf->ln(3);
$pdf->Cell(0, 5, "DE ACORDO:", 0, 1, L);

$pdf->ln(2);
$pdf->Cell(0, 5, "DATA APROVAÇÃO: ______/ _______/______________", 0, 1, L);

$pdf->ln(10);
$pdf->Cell(0, 5, "ASSINATURA E CARIMBO: ___________________________________________________________________", 0, 1, L);

$pdf->ln(2);
$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(0, 5, "Obs. Enviar via fax (11) 3061-3717 - após APROVAÇÃO deste orçamento.", 0, 1, L);

$pdf->ln(25);
$pdf->Write(5, "Digivoice Tecnologia em Eletrônica Ltda.                                                               CNPJ: 66.705.674/00001-08
Rua: Mateus Grou, 109 | 1º e 2º andar | Pinheiros | SP | 05415-050                I.E.: 206.070.410.112
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