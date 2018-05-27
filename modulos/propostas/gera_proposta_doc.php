<?php

session_start();

include("../../conecta.php");
include("../../functions.php");
include("../../funcoes.php");
include("../../mpdf/mpdf.php");

if (empty($_GET['pnumreg'])) {
    echo 'Proposta inválida !';
    exit;
}

$a_proposta = farray(query("select * from is_proposta where numreg = '" . $_GET['pnumreg'] . "'"));
$a_modelo = farray(query("select * from is_modelo_proposta where numreg = '" . $a_proposta['id_modelo_proposta'] . "'"));

$html_inicio = '
<html>
<head>
<body>
';
$cabecalho = str_replace("img/upload/", "../../img/upload/", $a_modelo["textohtm_cab"]);
$corpo = str_replace("img/upload/", "../../img/upload/", $a_modelo["textohtm_inicio"]);
$produtos = str_replace("img/upload/", "../../img/upload/", $a_modelo["textohtm_produtos"]);


// LISTA DE PRODUTOS SIMPLIFICADA
$tabela_itens_cab = '
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
  <tr>
    <td bgcolor="#CCCCCC"><strong>Descri&ccedil;&atilde;o do(s) Produto(s) e Servi&ccedil;o(s)</strong></td>
    <td width="14%" bgcolor="#CCCCCC"><strong>Valor </strong><strong>Total</strong><strong>(R$)</strong></td>
  </tr>
';
$tabela_itens_det = '
  <tr>
    <td>{VS_ITEM_DESCRICAO}</td>
    <td align="right"><div align="right">{VS_ITEM_TOTAL}</div></td>
  </tr>
';
$tabela_itens_tot = '
  <tr>
    <td colspan="1"><strong>Total :</strong></td>
    <td align="right"><div align="right">{VS_TOTAL_PROD}</div></td>
  </tr>
</table>
';

// LISTA DE PRODUTOS COMPLETA
$tabela_itens_cab2 = '
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
  <tr>
    <td width="5%" bgcolor="#CCCCCC"><strong>Qtde.</strong></td>
    <td bgcolor="#CCCCCC"><strong>Descri&ccedil;&atilde;o do(s) Produto(s) e Servi&ccedil;o(s)</strong></td>
    <td width="13%" bgcolor="#CCCCCC"><strong>Valor</strong><strong>Unit&aacute;rio(R$)</strong></td>
    <td width="13%" bgcolor="#CCCCCC"><strong>(%) Desc.</strong><strong>Unit&aacute;rio(R$)</strong></td>
    <td width="14%" bgcolor="#CCCCCC"><strong>Valor </strong><strong>Total</strong><strong>(R$)</strong></td>
  </tr>
';
$tabela_itens_det2 = '
  <tr>
    <td><div align="right">{VS_ITEM_QTDE}</div></td>
    <td>{VS_ITEM_DESCRICAO}</td>
    <td align="right"><div align="right">{VS_ITEM_UNITARIO}</div></td>
    <td align="right"><div align="right">{VS_PCT_DESCONTO}</div></td>
    <td align="right"><div align="right">{VS_ITEM_TOTAL}</div></td>
  </tr>
';
$tabela_itens_tot2 = '
  <tr>
    <td colspan="4"><strong>Total :</strong></td>
    <td align="right"><div align="right">{VS_TOTAL_PROD}</div></td>
  </tr>
</table>
';



$obs_padrao = str_replace("img/upload/", "../../img/upload/", $a_modelo["textohtm_fim"]);

$rodape = str_replace("img/upload/", "../../img/upload/", $a_modelo["textohtm_rodape"]);

$html_fim = '
</body>
</html>
';


$a_pessoa = farray(query("select * from is_pessoa where numreg = '" . $a_proposta['id_pessoa'] . "'"));
$a_contato = farray(query("select * from is_contato where numreg = '" . $a_proposta['id_pessoa_contato'] . "'"));
$a_gc = farray(query("select * from is_usuario where numreg = '" . $a_proposta['id_usuario_resp'] . "'"));
$a_cond_pagto = farray(query("select nome_cond_pagto from is_cond_pagto where numreg = '" . $a_proposta['id_cond_pagto'] . "'"));


$tabela_itens_tratada = "";
$tabela_itens_tratada2 = "";
$tabela_itens_tratada3 = "";
$tabela_itens_tratada4 = "";
$total_produtos = 0;
$total_servicos = 0;

// Listagem de Itens

$q_itens = query("select * from is_proposta_prod where id_proposta = '" . $a_proposta["numreg"] . "'");
while ($a_itens = farray($q_itens)) {
    $a_produto = farray(query("select nome_produto, textohtm from is_produto where numreg = '" . $a_itens["id_produto"] . "'"));
        $qtde = number_format(($a_itens['qtde']), 0, ',', '.');
    // SIMPLIFICADA
    $tabela_itens_det_trat = $tabela_itens_det;
    $tabela_itens_det_trat = str_replace("{VS_ITEM_QTDE}", $qtde, $tabela_itens_det_trat);
    $tabela_itens_det_trat = str_replace("{VS_ITEM_DESCRICAO}", utf8_encode($a_produto["nome_produto"]), $tabela_itens_det_trat);
    $tabela_itens_det_trat = str_replace("{VS_ITEM_UNITARIO}", number_format(($a_itens['valor'] * 1), 2, ',', '.'), $tabela_itens_det_trat);
    $tabela_itens_det_trat = str_replace("{VS_PCT_DESCONTO}", number_format(($a_itens['pct_desc'] * 1), 2, ',', '.'), $tabela_itens_det_trat);
    $tabela_itens_det_trat = str_replace("{VS_ITEM_TOTAL}", number_format(($a_itens['valor_total'] * 1), 2, ',', '.'), $tabela_itens_det_trat);
    $tabela_itens_tratada .= $tabela_itens_det_trat;
    // COMPLETA
    $tabela_itens_det_trat2 = $tabela_itens_det2;
    $tabela_itens_det_trat2 = str_replace("{VS_ITEM_QTDE}", $qtde, $tabela_itens_det_trat2);
    $tabela_itens_det_trat2 = str_replace("{VS_ITEM_DESCRICAO}", utf8_encode($a_produto["nome_produto"]), $tabela_itens_det_trat2);
    $tabela_itens_det_trat2 = str_replace("{VS_ITEM_UNITARIO}", number_format(($a_itens['valor'] * 1), 2, ',', '.'), $tabela_itens_det_trat2);
    $tabela_itens_det_trat2 = str_replace("{VS_PCT_DESCONTO}", number_format(($a_itens['pct_desc'] * 1), 2, ',', '.'), $tabela_itens_det_trat2);
    $tabela_itens_det_trat2 = str_replace("{VS_ITEM_TOTAL}", number_format(($a_itens['valor_total'] * 1), 2, ',', '.'), $tabela_itens_det_trat2);
    $tabela_itens_tratada2 .= $tabela_itens_det_trat2;
    // DESCRICAO DE PRODUTOS
    $tabela_itens_tratada3 .= '<b>'.$a_produto["nome_produto"].'</b><br><br>'.$a_produto["textohtm"].'<br><br>';
    // OBSERVAÇÕES DE PRODUTOS POR ITEM    
 $tabela_itens_tratada4 .= utf8_encode(chrbr(strsadds($a_itens["obs"]))).'<br><br>';
    
    $total_produtos = $total_produtos + ($a_itens['valor_total'] * 1);
}

$tabela_itens_tot = str_replace("{VS_TOTAL_PROD}", number_format($total_produtos, 2, ',', '.'), $tabela_itens_tot);
$tabela_itens_tot2 = str_replace("{VS_TOTAL_PROD}", number_format($total_produtos, 2, ',', '.'), $tabela_itens_tot2);

$LISTA_PRODUTOS_SIMPLES = $tabela_itens_cab.$tabela_itens_tratada.$tabela_itens_tot;
$LISTA_PRODUTOS_COMPLETA = $tabela_itens_cab2.$tabela_itens_tratada2.$tabela_itens_tot2;
$PRODUTOS_DESCRICAO = $tabela_itens_tratada3;
$produtos = str_replace("{VS_LISTA_PRODUTOS_SIMPLES}", $LISTA_PRODUTOS_SIMPLES, $produtos);
$produtos = str_replace("{VS_LISTA_PRODUTOS_COMPLETA}", $LISTA_PRODUTOS_COMPLETA, $produtos);
$produtos = str_replace("{VS_PRODUTOS_DESCRICAO}", $PRODUTOS_DESCRICAO, $produtos);
                         
$produtos = str_replace("{VS_PRODUTOS_OBSERVACAO}", $tabela_itens_tratada4, $produtos);

$texto_completo = aplica_variaveis_texto_htm($corpo,$a_proposta, $a_pessoa, $a_contato, $a_gc, $a_cond_pagto);
$texto_completo .= aplica_variaveis_texto_htm($produtos, $a_proposta, $a_pessoa, $a_contato, $a_gc, $a_cond_pagto);
$texto_completo .= aplica_variaveis_texto_htm($obs_padrao, $a_proposta, $a_pessoa, $a_contato, $a_gc, $a_cond_pagto);
$mpdf = new mPDF('en-x', 'A4', '', '', 15, 15, 40, 30, 10, 10);

if ($a_modelo["imagem_fundo"] ) {
    $mpdf->SetWatermarkImage('../../arquivos/'.$a_modelo["imagem_fundo"]);
    $mpdf->showWatermarkImage = true;
}

$header = aplica_variaveis_texto_htm($cabecalho, $a_proposta, $a_pessoa);
$footer = aplica_variaveis_texto_htm($rodape, $a_proposta, $a_pessoa);

$mpdf->SetHTMLHeader($header);
$mpdf->SetHTMLFooter($footer);

$html = $texto_completo;

$mpdf->WriteHTML($html);
$mpdf->Output();

exit;

function aplica_variaveis_texto_htm($textohtm, $a_proposta, $a_pessoa, $a_contato, $a_gc, $a_cond_pagto) {
    $textohtm = str_replace("{VS_DATA}", dten2br($a_proposta['dt_proposta']), $textohtm);
    $textohtm = str_replace("{VS_NUMERO}", $a_proposta['id_proposta'], $textohtm);
    $textohtm = str_replace("{VS_REVISAO}", $a_proposta['revisao'], $textohtm);
    $textohtm = str_replace("{VS_CONTA}", utf8_encode($a_pessoa['razao_social_nome']), $textohtm);
    $textohtm = str_replace("{VS_ENDERECO}", utf8_encode($a_pessoa['endereco']), $textohtm);
    $textohtm = str_replace("{VS_CIDADE}", utf8_encode($a_pessoa['cidade']), $textohtm);
    $textohtm = str_replace("{VS_BAIRRO}", utf8_encode($a_pessoa['bairro']), $textohtm);
    $textohtm = str_replace("{VS_CEP}", $a_pessoa['cep'], $textohtm);
    $textohtm = str_replace("{VS_UF}", $a_pessoa['uf'], $textohtm);
    $textohtm = str_replace("{VS_CNPJ}", $a_pessoa['cnpj_cpf'], $textohtm);
    $textohtm = str_replace("{VS_IE}", $a_pessoa['ie_rg'], $textohtm);
    $textohtm = str_replace("{VS_FONE}", $a_pessoa['tel1'], $textohtm);
    $textohtm = str_replace("{VS_EMAIL}", $a_pessoa['email'], $textohtm);
    $textohtm = str_replace("{VS_CONTATO}", utf8_encode(addslashes($a_contato["nome"])), $textohtm);
    $textohtm = str_replace("{VS_GERENTE_CONTA_NOME}", utf8_encode($a_gc["nome_usuario"]), $textohtm);
    $textohtm = str_replace("{VS_GERENTE_CONTA_EMAIL}", $a_gc["email"], $textohtm);
    $textohtm = str_replace("{VS_GERENTE_CONTA_FONE}", $a_gc["tel1"], $textohtm);
    $textohtm = str_replace("{VS_GERENTE_CONTA_CELULAR}", $a_gc["tel2"], $textohtm);
    $textohtm = str_replace("{VS_COND_PAGTO}", $a_cond_pagto["nome_cond_pagto"], $textohtm);
    $textohtm = str_replace("{VS_PRAZO_ENTREGA}", dten2br($a_proposta['dt_entrega_desejada']), $textohtm);
    $textohtm = str_replace("{VS_VALIDADE}", dten2br($a_proposta['dt_validade']), $textohtm);
    $textohtm = str_replace("{VS_NOVA_PAGINA}", "<pagebreak />", $textohtm);
    $textohtm = str_replace("{VS_OBSERVACAO}", utf8_encode(chrbr(strsadds($a_proposta['obs']))), $textohtm);
    return $textohtm;
}

?>