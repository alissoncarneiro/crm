<?php
/*
 * dado_padrao_conta.php
 * Autor: Alex
 * 04/11/2010 11:07:00
 *
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
/*
header("Content-Type: text/html;  charset=ISO-8859-1");
session_start();

require('../../conecta.php');
require('../../classes/class.Pessoa.php');
require('../../functions.php');

#versao do encoding xml
$DOM = new DOMDocument("1.0","ISO-8859-1");
#retirar os espacos em branco
$DOM->preserveWhiteSpace = false;
#gerar o codigo
$DOM->formatOutput = true;
#criando o n� principal (root)
$Root = $DOM->createElement("resposta");

if(empty($_POST['numreg'])){
    $Status = $DOM->createElement("status",0);
    $Mensagem = $DOM->createElement("campos",TextoParaXML(getError('0040010005',$Retorno)));

    #adiciona o n� contato em (root)
    $Root->appendChild($Status);
    $Root->appendChild($Mensagem);
}
else{
    $Pessoa = new Pessoa($_POST['numreg']);
    $Status = $DOM->createElement("status",1);
    $Campos = $DOM->createElement("campos");
    
    $IdContato = $Pessoa->getDadoPessoa('id_contato');

    $Campos->appendChild($DOM->createElement("id_contato",TextoParaXML($IdContato)));
    if(!empty($IdContato)){
 $QryContato = query("SELECT nome FROM is_contato WHERE numreg = ".$IdContato);
 $ArContato = farray($QryContato);
 $Campos->appendChild($DOM->createElement("nome_contato",TextoParaXML($ArContato['nome'])));
    }
    $IdTransportadora = $Pessoa->getDadoPessoa('id_transportadora_padrao');
    $Campos->appendChild($DOM->createElement("id_transportadora",TextoParaXML($IdTransportadora)));
    if(!empty($IdTransportadora)){
 $QryTransportadora = query("SELECT nome_transportadora FROM is_transportadora WHERE numreg = ".$IdTransportadora);
 $ArTransportadora = farray($QryTransportadora);
 $Campos->appendChild($DOM->createElement("nome_transportadora",TextoParaXML($ArTransportadora['nome_transportadora'])));
    }

    $Campos->appendChild($DOM->createElement("id_cond_pagto",TextoParaXML($Pessoa->getDadoPessoa('id_cond_pagto_padrao'))));
    $Campos->appendChild($DOM->createElement("id_tp_frete",TextoParaXML($Pessoa->getDadoPessoa('id_tp_frete_padrao'))));
    $Campos->appendChild($DOM->createElement("id_tab_preco",TextoParaXML($Pessoa->getDadoPessoa('id_tab_preco_padrao'))));
    $Campos->appendChild($DOM->createElement("sn_faturamento_parcial",TextoParaXML($Pessoa->getDadoPessoa('sn_aceita_faturamento_parcial'))));



    #adiciona o n� contato em (root)
    $Root->appendChild($Status);
    $Root->appendChild($Campos);
}
$DOM->appendChild($Root);
# Para salvar o arquivo, descomente a linha
//$DOM->save("contatos.xml");
#cabe�alho da p�gina
header("Content-Type: text/xml");
# imprime o xml na tela
print $DOM->saveXML();
 */

header("Content-Type: text/html;  charset=ISO-8859-1");
session_start();

require('../../conecta.php');
require('../../classes/class.Pessoa.php');
require('../../functions.php');
require('classes/class.Venda.Parametro.php');


$VendaParametro = new VendaParametro();

$XML = '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.'>'."\n";

$XML .= '<resposta>'."\n";

if(!$VendaParametro->getSnAutocompletaDadosCliente()){
    $XML .= "\t".'<status>3</status>'."\n";
    $XML .= '</resposta>'."\n";
    header("Content-Type: text/xml");
    echo $XML;
    exit;
}


$XML .= "\t".'<status>1</status>'."\n";
$XML .= "\t".'<campos>'."\n";
$Pessoa                 = new Pessoa($_POST['numreg']);
$IdContato              = $Pessoa->getDadoPessoa('id_contato');
$IdTabPreco             = $Pessoa->getDadoPessoa('id_tab_preco_padrao');
$IdCondPagto            = $Pessoa->getDadoPessoa('id_cond_pagto_padrao');
$IdTpFrete              = $Pessoa->getDadoPessoa('id_tp_frete_padrao');
$SnFaturamentoParcial   = $Pessoa->getDadoPessoa('sn_aceita_faturamento_parcial');
$IdRepresentantePadrao  = $Pessoa->getDadoPessoa('id_representante_padrao');

$XML .= "\t"."\t".'<id_contato>'.TextoParaXML($IdContato).'</id_contato>'."\n";
if(!empty($IdContato)){
    $QryContato = query("SELECT nome FROM is_contato WHERE numreg = ".$IdContato);
    $ArContato = farray($QryContato);
$XML .= "\t"."\t".'<nome_contato>'.TextoParaXML($ArContato['nome']).'</nome_contato>'."\n";
}
$IdTransportadora = $Pessoa->getDadoPessoa('id_transportadora_padrao');
$XML .= "\t"."\t".'<id_transportadora>'.TextoParaXML($IdTransportadora).'</id_transportadora>'."\n";
if($IdTransportadora != ''){
    $QryTransportadora = query("SELECT nome_transportadora FROM is_transportadora WHERE numreg = ".$IdTransportadora);
    $ArTransportadora = farray($QryTransportadora);
    $XML .= "\t"."\t".'<nome_transportadora>'.TextoParaXML($ArTransportadora['nome_transportadora']).'</nome_transportadora>'."\n";
}
if($IdCondPagto != ''){
    $QryCondPagto = query("SELECT nome_cond_pagto FROM is_cond_pagto WHERE numreg = ".$IdCondPagto);
    $ArCondPagto = farray($QryCondPagto);
    $XML .= "\t"."\t".'<nome_cond_pagto>'.TextoParaXML($ArCondPagto['nome_cond_pagto']).'</nome_cond_pagto>'."\n";
}
if($IdTabPreco != ''){
    $QryTabPreco = query("SELECT nome_tab_preco FROM is_tab_preco WHERE numreg = ".$IdTabPreco);
    $ArTabPreco = farray($QryTabPreco);
    $XML .= "\t"."\t".'<nome_tab_preco>'.TextoParaXML($ArTabPreco['nome_tab_preco']).'</nome_tab_preco>'."\n";
}

if($IdTpFrete != ''){
    $QryTpFrete = query("SELECT nome_tp_frete FROM is_tp_frete WHERE numreg = ".$IdTpFrete);
    $ArTpFrete = farray($QryTpFrete);
    $XML .= "\t"."\t".'<nome_tp_frete>'.TextoParaXML($ArTpFrete['nome_tp_frete']).'</nome_tp_frete>'."\n";
}

if($SnFaturamentoParcial != ''){
    if($SnFaturamentoParcial == '1'){
        $XML .= "\t"."\t".'<nome_sn_faturamento_parcial>Sim</nome_sn_faturamento_parcial>'."\n";
    }
    else{
        $XML .= "\t"."\t".'<nome_sn_faturamento_parcial>N�o</nome_sn_faturamento_parcial>'."\n";
    }
}

if($IdRepresentantePadrao != ''){
    $XML .= "\t"."\t".'<id_representante_principal>'.TextoParaXML($IdRepresentantePadrao).'</id_representante_principal>'."\n";
    $QryRepresentantePadrao = query("SELECT nome_usuario FROM is_usuario WHERE numreg = ".$IdRepresentantePadrao);
    $ArRepresentantePadrao = farray($QryRepresentantePadrao);
    $XML .= "\t"."\t".'<nome_representante_principal>'.TextoParaXML($ArRepresentantePadrao['nome_usuario']).'</nome_representante_principal>'."\n";
}

$SqlEnderecoEntrega = "SELECT numreg,endereco FROM is_pessoa_endereco WHERE id_pessoa = '".$Pessoa->getNumregPessoa()."'";
$QryEnderecoEntrega = query($SqlEnderecoEntrega);
$NumRowsEnderecoEntrega = numrows($QryEnderecoEntrega);
if($NumRowsEnderecoEntrega == 1){
    $ArEnderecoEntrega = farray($QryEnderecoEntrega);
    $XML .= "\t"."\t".'<id_endereco_entrega>'.$ArEnderecoEntrega['numreg'].'</id_endereco_entrega>'."\n";
    $XML .= "\t"."\t".'<nome_endereco_entrega>'.TextoParaXML($ArEnderecoEntrega['endereco']).'</nome_endereco_entrega>'."\n";
}

$XML .= "\t"."\t".'<id_cond_pagto>'.TextoParaXML($IdCondPagto).'</id_cond_pagto>'."\n";
$XML .= "\t"."\t".'<id_tp_frete>'.TextoParaXML($IdTpFrete).'</id_tp_frete>'."\n";
$XML .= "\t"."\t".'<id_tab_preco>'.TextoParaXML($IdTabPreco).'</id_tab_preco>'."\n";
$XML .= "\t"."\t".'<sn_faturamento_parcial>'.TextoParaXML($SnFaturamentoParcial).'</sn_faturamento_parcial>'."\n";
$XML .= "\t".'</campos>'."\n";

$XML .= "\t".'<campos_atualizar>'.$VendaParametro->getCampoAtualizarPasso1().'</campos_atualizar>'."\n";
$XML .= '</resposta>'."\n";
header("Content-Type: text/xml");
echo $XML;
?>