<?php
/*
 * p1_sugere_cfop_cliente.php
 * Autor: Alex
 * 24/03/2011 11:00:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
require('includes.php');

$IdEstabelecimento  = trim($_POST['id_estabelecimento']);
$IdPessoa           = trim($_POST['id_pessoa']);
$IdEnderecoEntrega  = trim($_POST['id_endereco_entrega']);

if($IdEstabelecimento == '' || $IdPessoa == '' || $IdEnderecoEntrega == ''){
    $Status = 'false';
    $Mensagem = 'Estabelecimento, Conta e Endereço Entrega devem ser informados.';
}
else{
    /*
     * Definindo parâmetros de estado e pais de origem e destino
     */
    $Estabelecimento = new Estabelecimento($IdEstabelecimento);
    $Pessoa = new Pessoa($IdPessoa);
    $PessoaEndereco = new PessoaEndereco($IdEnderecoEntrega);

    $UFEstabelecimento      = $Estabelecimento->getDadosEstabelecimento('uf');
    $UFEstabelecimento      = trim(strtoupper($UFEstabelecimento));
    $PaisEstabelecimento    = $Estabelecimento->getDadosEstabelecimento('pais');
    $PaisEstabelecimento    = trim(strtoupper($PaisEstabelecimento));

    $UFEnderecoEntrega      = $PessoaEndereco->getDadosPessoaEndereco('uf');
    $UFEnderecoEntrega      = trim(strtoupper($UFEnderecoEntrega));
    $PaisEnderecoEntrega    = $PessoaEndereco->getDadosPessoaEndereco('pais');
    $PaisEnderecoEntrega    = trim(strtoupper($PaisEnderecoEntrega));

    if($UFEstabelecimento == '' || $PaisEstabelecimento == '' || $UFEnderecoEntrega == '' || $PaisEnderecoEntrega == ''){/* Se algum parâmetros estiver em branco retornal false */
        $Mensagem = 'Cálculo de CFOP (Linha:'.__LINE__.'): UF ou Pais de origem ou destino em branco.';
        $Status = 'false';
    }
    else{
        if($PaisEnderecoEntrega != $PaisEstabelecimento || $UFEnderecoEntrega != $UFEstabelecimento){ /* Se o pais for diferente usa a CFOP internacinal ou Se o estado for diferente usa o interestadual */
            $IdCFOP = $Pessoa->getDadoPessoa('cfop_interestadual_padrao');
        }
        elseif($UFEnderecoEntrega == $UFEstabelecimento){ /* Se o estado for igual usa o estadual */
            $IdCFOP = $Pessoa->getDadoPessoa('cfop_estadual_padrao');
        }
        $Status = 'true';
    }
}
header("content-type: text/xml");
echo '<?'.'xml version="1.0" encoding="ISO-8859-1"'.'?>'."\n";
echo '<root>'."\n";
echo "\t".'<status>'.$Status.'</status>'."\n";
echo "\t".'<mensagem>'.$Mensagem.'</mensagem>'."\n";
echo "\t".'<id_cfop>'.$IdCFOP.'</id_cfop>'."\n";
echo '</root>';
?>