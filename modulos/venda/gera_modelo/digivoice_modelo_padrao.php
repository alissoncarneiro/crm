<?php

echo $_GET["tipo"];

$html_inicio = '
<html>
<head>
<body>
';
$cabecalho = '
<table width="100%" height="100px" border="0">
  <tr>
    <td align="right"><img src="cabecalho_orcamento.png"></td>
  </tr>
</table>
';
$corpo = '
<table width="100%" height="100px" border="0">
  <tr>
    <td align="center"><b><u>OR&Ccedil;AMENTO / RESERVA</u></b></td>
  </tr>
</table>
<br>
<br>
<table width="100%" border="0">
  <tr>
    <td><strong>DATA: {VS_DATA} </strong></td>
    <td><strong>OR&Ccedil;AMENTO N&Uacute;MERO: {VS_NUMERO}</strong></td>
  </tr>
  <tr>
    <td colspan="2"><strong>CLIENTE: {VS_CLIENTE} </strong></td>
  </tr>
  <tr>
    <td colspan="2"><strong>ENDERE&Ccedil;O.: {VS_ENDERECO}</strong></td>
  </tr>
  <tr>
    <td><strong>CIDADE: {VS_CIDADE}</strong></td>
    <td><strong>CEP.: </strong> <strong> {VS_CEP} UF: {VS_UF}</strong></td>
  </tr>
  <tr>
    <td><strong>CNPJ: {VS_CNPJ}</strong></td>
    <td><strong>INSC. EST.: {VS_IE}</strong></td>
  </tr>
  <tr>
    <td><strong>A/C: {VS_CONTATO}</strong></td>
    <td><strong>FONE: {VS_FONE}</strong></td>
  </tr>
  <tr>
    <td><strong>E-MAIL: {VS_EMAIL}</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><strong>CATEGORIA CLIENTE: {VS_CATEGORIA}</strong></td>
  </tr>
  <tr>
    <td colspan="2"><strong>TIPO VENDA/ DESTINO :&nbsp; {VS_TIPO_VENDA}</strong></td>
  </tr>
  <tr>
    <td><strong>Gerente de contas</strong> <strong>: {VS_GERENTE_CONTA_NOME}</strong></td>
    <td><strong>e-mail</strong>: <strong>{VS_GERENTE_CONTA_EMAIL}</strong></td>
  </tr>
  <tr>
    <td><strong>Fone: {VS_GERENTE_CONTA_FONE}</strong></td>
    <td><strong>celular</strong>: <strong>{VS_GERENTE_CONTA_CELULAR}</strong></td>
  </tr>
  <tr>
    <td><strong>Assistente de vendas: {VS_ASSISTENTE_NOME}</strong></td>
    <td><strong>e-mail</strong>: <strong>{VS_ASSISTENTE_EMAIL}</strong></td>
  </tr>
  <tr>
    <td><strong>Fone: {VS_ASSISTENTE_FONE}</strong></td>
    <td><strong>Fax: {VS_ASSISTENTE_FAX}</strong></td>
  </tr>
</table>
<br>
';
$tabela_itens_cab = '
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
  <tr>
    <td width="5%" bgcolor="#CCCCCC"><strong>Qtde.</strong></td>
    <td bgcolor="#CCCCCC"><strong>Descri&ccedil;&atilde;o do(s) Produto(s) e Servi&ccedil;o(s)</strong></td>
    <td width="5%" bgcolor="#CCCCCC"><div align="center"><strong>UN</strong></div></td>
    <td width="13%" bgcolor="#CCCCCC"><strong>Valor</strong><strong>Unit&aacute;rio(R$)</strong></td>
    <td width="14%" bgcolor="#CCCCCC"><strong>Valor </strong><strong>Total</strong><strong>(R$)</strong></td>
  </tr>
';
$tabela_itens_det = '
  <tr>
    <td><div align="right">{VS_ITEM_QTDE}</div></td>
    <td>{VS_ITEM_DESCRICAO}</td>
    <td><div align="center">{VS_ITEM_UN}</div></td>
    <td align="right"><div align="right">{VS_ITEM_UNITARIO}</div></td>
    <td align="right"><div align="right">{VS_ITEM_TOTAL}</div></td>
  </tr>
';
$tabela_itens_tot = '
  <tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="2"><strong>Total dos Produtos:</strong></td>
    <td align="right"><div align="right">{VS_TOTAL_PROD}</div></td>
  </tr>
  <tr>
    <td colspan="2"><strong>PAGAMENTO/PRAZO/VALIDADE</strong></td>
    <td colspan="2"><strong>Total dos Servi&ccedil;os:</strong></td>
    <td align="right"><div align="right">{VS_TOTAL_SERVICO}</div></td>
  </tr>
  <tr>
    <td colspan="2"><strong>Condi&ccedil;&otilde;es de Pagamento: </strong></td>
    <td colspan="2"><strong>Total IPI:</strong></td>
    <td align="right"><div align="right">{VS_TOTAL_IPI}</div></td>
  </tr>
  <tr>
    <td colspan="2"><strong>Prazo de entrega &eacute; de </strong>XX <strong>dias &uacute;teis ap&oacute;s aprova&ccedil;&atilde;o</strong></td>
    <td colspan="2"><strong>Total ICMS-ST:</strong></td>
    <td align="right"><div align="right">{VS_TOTAL_ST}</div></td>
  </tr>
  <tr>
    <td colspan="2"><strong>Validade deste Or&ccedil;amento: </strong></td>
    <td colspan="2"><strong>Total do Or&ccedil;amento:</strong></td>
    <td align="right"><div align="right">{VS_TOTAL}</div></td>
  </tr>
</table>
';
$obs_padrao = '
<br>
<table width="100%" height="100px" border="0">
  <tr>
    <td align="center"><b><u>OUTRAS ONSERVA&Ccedil;&Otilde;ES</u></b></td>
  </tr>
</table>
<br>
<strong>1- Condi&ccedil;&otilde;es de  Pagamento <u>s&oacute; com cadastro APROVADO e&nbsp;  para valores acima de R$600,00</u></strong><br>
          <strong>2</strong>- Garantia de 36 (TRINTA E SEIS) meses  para PRODUTOS COMO PLACA DE VOZ para os demais produtos garantia de 12(DOZE)  MESES, posto fabrica a partir da data da Nota Fiscal.<br>
          <strong>3- </strong>Garantia de 03 (TRES) meses para  SERVI&Ccedil;OS posto fabrica a partir da data da Nota Fiscal.<br>
          <strong>4</strong>- Cancelamento do pedido somente com 5  (CINCO) &nbsp;dias de anteced&ecirc;ncia da entrega.<br>
          <strong>5</strong>- O PEDIDO SOMENTE TER&Aacute; VALIDADE A  PARTIR DO ENVIO DOS DOCUMENTOS:<br>
  &nbsp;&nbsp;&nbsp;&nbsp;  a) Proposta Aprovada com Assinatura do Cliente<br>
  &nbsp;&nbsp;&nbsp; &nbsp;b)  Ficha de configura&ccedil;&atilde;o totalmente preenchida se o produto suportar<br>
  &nbsp;&nbsp;&nbsp;&nbsp;  c) Requisitos do projeto totalmente preenchidos se o produto suportar &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br>
  <strong>6</strong>- Acompanha esta proposta o descritivo  do produto.<br>
  <strong>7- </strong>Se existir <strong>INSTALA&Ccedil;&Atilde;O</strong>, dever&aacute; ser agendada antecipadamente com a DIGIVOICE.<br>
  <strong>8- </strong>Caso a instala&ccedil;&atilde;o do equipamento  exigir a presen&ccedil;a da <strong>Mantenedora do PABX</strong>,  o custo ser&aacute; por conta do &nbsp;cliente.<br>
  <strong>9- </strong>A DIGIVOICE n&atilde;o se responsabiliza por  danos no equipamento caso a instala&ccedil;&atilde;o n&atilde;o seja feita por t&eacute;cnico devidamente  qualificado.<br>
  <strong>10 -</strong>Dep&oacute;sitos no <strong>Banco Bradesco</strong> Ag. 1382-0 C/C 18.474-8 ou <strong>Banco do Brasil</strong> Ag. 1821-X C/C 190801-4<br>
  <strong>11- </strong>Comprovante (s) de dep&oacute;sito (s) dever&aacute;  (&atilde;o) ser enviado (s) via fax (11-3061-3717) para validar o pagamento.<br><br>
    <strong>12  -</strong>A Nota Fiscal &eacute;  desmembrada em Hardware e software, sendo Nota fiscal de venda para Hardware e  nota fiscal de servi&ccedil;o para Software.<br>
      <strong>13  -LOCU&Ccedil;&Atilde;O FEMININA</strong> - qualquer  altera&ccedil;&atilde;o feita no texto ap&oacute;s sua grava&ccedil;&atilde;o ter&aacute; um taxa adicional no valor de R$  140,00 &ndash; (N&atilde;o enviamos a mensagem gravada para aprova&ccedil;&atilde;o, neste caso  solicitamos que seja revista a mensagem antes nos enviar para a devida grava&ccedil;&atilde;o)<br>
      <strong>14  -</strong>Frete por conta do  Cliente, <strong>FAVOR INDICAR NO CAMPO ABAIXO DESTA  PROPOSTA</strong>
    <br><br>_______________________        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong> <strong> </strong><br>
        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong> <br>
    OBS.: FAVOR CONFERIR OS DADOS  CADASTRAIS DA SUA EMPRESA CONSTANTES NESTA PROPOSTA COMERCIAL (CASO O ENDERE&Ccedil;O  DE ENTREGA SEJA DIFERENTE AO DO CADASTRO NOS INFORMAR), POIS N&Atilde;O NOS  RESPONSABILIZAMOS PELOS DADOS INFORMADOS ACIMA E NEM PELAS ALTERA&Ccedil;&Otilde;ES SOFRIDAS  JUNTO AOS &Oacute;RG&Atilde;OS DO SINTEGRA E RECEITA FEDERAL.
    <br>&nbsp;<br>
        <strong><u><center>DADOS COMPLEMENTARES</center></u></strong>
    <br>
    <br>- A SUA EMPRESA EST&Aacute; HABILITADA NO  SINTEGRA?________________________________________
    <br>
    <br>- A SUA EMPRESA &Eacute; CONTRIBUINTE DO ICMS? QUAL O REGIME DE  APURA&Ccedil;&Atilde;O?&nbsp; _______________
    <br>&nbsp;
    <br>- TRANSPORTADORA PARA ENVIO DO  MATERIAL: __________________________________________
    <br>&nbsp;
    <br>- TELEFONE DA TRANSPORTADORA: (____)___________________
    <br>&nbsp;
    <br><strong><u>DE ACORDO</u></strong>
    <br>&nbsp;
    <br>DATA APROVA&Ccedil;&Atilde;O: ______/______/__________
    <br>&nbsp;
    <br>ASSINATURA E CARIMBO_________________________________
    <br>&nbsp;
    <br>Obs. Enviar via fax (11) 3016-5200 -  OP&Ccedil;&Atilde;O 7 OU (11) 3061-3717 - ap&oacute;s APROVA&Ccedil;&Atilde;O deste or&ccedil;amento ou&nbsp; enviar via e-mail escaneado - Confirmar via  telefone com a assistente de vendas o recebimento do mesmo.
  
';
$obs_usuario = '
<br>
<table width="100%" border="0">
  <tr>
    <td>{VS_OBSERVACAO}</td>
  </tr>
  </tr>
</table>
';

$rodape = '
<br>
<table width="100%" border="0">
  <tr>
    <td><img src="rodape_orcamento.png" >{PAGENO}/{nb}</td>
  </tr>
</table>
';

$html_fim = '
</body>
</html>
';

session_start();

$PrefixoIncludes = '../';
include('../includes.php');

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

$a_contato = farray(query("select nome from is_contato where numreg = '" . $Venda->getDadosVenda('id_contato') . "'"));
$a_aplicacao = farray(query("select nome_destino_mercadoria from is_destino_mercadoria where numreg = '" . $Venda->getDadosVenda('id_destino_mercadoria') . "'"));
$a_gc = farray(query("select * from is_usuario where numreg = '" . $Venda->getDadosVenda('id_representante') . "'"));
$a_av = farray(query("select * from is_usuario where numreg = '" . $Venda->getDadosVenda('id_usuario_cad') . "'"));

$corpo = str_replace("{VS_DATA}", dten2br($Venda->getDadosVenda('dt_venda')), $corpo);
$corpo = str_replace("{VS_NUMERO}", $Venda->getNumregVenda(), $corpo);
$corpo = str_replace("{VS_CLIENTE}", utf8_encode($Venda->getPessoa()->getDadoPessoa('razao_social_nome')), $corpo);
$corpo = str_replace("{VS_ENDERECO}", utf8_encode($Venda->getDadosEnderecoEntrega('endereco')), $corpo);
$corpo = str_replace("{VS_CIDADE}", utf8_encode($Venda->getDadosEnderecoEntrega('cidade')), $corpo);
$corpo = str_replace("{VS_CEP}", $Venda->getDadosEnderecoEntrega('cep'), $corpo);
$corpo = str_replace("{VS_UF}", $Venda->getDadosEnderecoEntrega('uf'), $corpo);
$corpo = str_replace("{VS_CNPJ}", $Venda->getPessoa()->getDadoPessoa('cnpj_cpf'), $corpo);
$corpo = str_replace("{VS_IE}", $Venda->getPessoa()->getDadoPessoa('ie_rg'), $corpo);
$corpo = str_replace("{VS_CONTATO}", utf8_encode(addslashes($a_contato["nome"])), $corpo);
$corpo = str_replace("{VS_FONE}", $Venda->getPessoa()->getDadoPessoa('tel1'), $corpo);
$corpo = str_replace("{VS_EMAIL}", $Venda->getPessoa()->getDadoPessoa('email'), $corpo);
$corpo = str_replace("{VS_CATEGORIA}", 'LEVANTAR', $corpo);
$corpo = str_replace("{VS_TIPO_VENDA}", utf8_encode($a_aplicacao["nome_destino_mercadoria"]), $corpo);
$corpo = str_replace("{VS_GERENTE_CONTA_NOME}", utf8_encode($a_gc["nome_usuario"]), $corpo);
$corpo = str_replace("{VS_GERENTE_CONTA_EMAIL}", $a_gc["email"], $corpo);
$corpo = str_replace("{VS_GERENTE_CONTA_FONE}", $a_gc["tel1"], $corpo);
$corpo = str_replace("{VS_GERENTE_CONTA_CELULAR}", $a_gc["tel2"], $corpo);
$corpo = str_replace("{VS_ASSISTENTE_NOME}", utf8_encode($a_av["nome_usuario"]), $corpo);
$corpo = str_replace("{VS_ASSISTENTE_EMAIL}", $a_av["email"], $corpo);
$corpo = str_replace("{VS_ASSISTENTE_FONE}", $a_av["tel1"], $corpo);
$corpo = str_replace("{VS_ASSISTENTE_FAX}", $a_av["tel2"], $corpo);

$tabela_itens_tratada = "";

foreach ($Venda->getItens() as $IndiceItem => $Item) {
    if ($Venda->getTipoVenda() == 1 && $Item->getDadosVendaItem('sn_item_perdido') == 1) {
        continue;
    }
    $UN = "";
    if ($Venda->getDigitacaoCompleta() || 1 == 1) { //Se a venda já estiver completa FIXO TEXTO
        if ($Item->getDadosVendaItem('id_unid_medida') != '') {
            $QryUnidMedida = query("SELECT numreg, nome_unid_medida FROM is_unid_medida WHERE numreg = " . $Item->getDadosVendaItem('id_unid_medida'));
            $ArUnidMedida = farray($QryUnidMedida);
            $UN = $ArUnidMedida['nome_unid_medida'];
        }
    }
    $tabela_itens_det_trat = $tabela_itens_det;
    $tabela_itens_det_trat = str_replace("{VS_ITEM_QTDE}", $Venda->NFQ($Item->getDadosVendaItem('qtde')), $tabela_itens_det_trat);
    $tabela_itens_det_trat = str_replace("{VS_ITEM_DESCRICAO}", utf8_encode($Item->getCodProdutoERP() . '-' . $Item->getNomeProduto()), $tabela_itens_det_trat);
    $tabela_itens_det_trat = str_replace("{VS_ITEM_UN}", $UN, $tabela_itens_det_trat);
    $tabela_itens_det_trat = str_replace("{VS_ITEM_UNITARIO}", $Venda->NFV($Item->getDadosVendaItem('vl_unitario_com_descontos')), $tabela_itens_det_trat);
    $tabela_itens_det_trat = str_replace("{VS_ITEM_TOTAL}", $Venda->NFV($Item->getDadosVendaItem('vl_total_liquido')), $tabela_itens_det_trat);
    $tabela_itens_tratada .= $tabela_itens_det_trat;
}

$tabela_itens_tot = str_replace("{VS_TOTAL_PROD}", $Venda->NFV($Venda->getVlTotalVendaLiquido()), $tabela_itens_tot);
$tabela_itens_tot = str_replace("{VS_TOTAL_SERVICO}", '0,00', $tabela_itens_tot);
$tabela_itens_tot = str_replace("{VS_TOTAL_IPI}",$Venda->NFV( $Venda->getVlTotalVendaIPI()), $tabela_itens_tot);
$tabela_itens_tot = str_replace("{VS_TOTAL_ST}", $Venda->NFV($Venda->getVlTotalVendaST()), $tabela_itens_tot);
$tabela_itens_tot = str_replace("{VS_TOTAL}", $Venda->NFV($Venda->getVlTotalVendaLiquido()+$Venda->getVlTotalVendaIPI()+$Venda->getVlTotalVendaST()), $tabela_itens_tot);

$obs_usuario = str_replace("{VS_OBSERVACAO}", utf8_encode(chrbr(strsadds($Venda->getDadosVenda('obs')))), $obs_usuario);;

$texto_completo = $corpo;
$texto_completo .= $tabela_itens_cab;
$texto_completo .= $tabela_itens_tratada;
$texto_completo .= $tabela_itens_tot;
$texto_completo .= $obs_padrao;
$texto_completo .= $obs_usuario;

include("../../../mpdf/mpdf.php");

$mpdf=new mPDF('en-x','A4','','',15,15,40,30,10,10);

$header = $cabecalho;
$footer = $rodape;

$mpdf->SetHTMLHeader($header);
$mpdf->SetHTMLFooter($footer);

$html = $texto_completo;

$mpdf->WriteHTML($html);

$mpdf->Output();
exit;

?>
