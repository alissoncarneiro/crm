<?php
/*
 * is_produto_estrutura.php
 * Autor: Rodrigo Piva
 * 22/07/2011 10:00
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
require("../../conecta.php");
require "../../funcoes.php";
require "../../functions.php";

$SqlIsPessoaEquipamento = "SELECT * FROM is_pessoa_equipamento WHERE numreg = ".$_GET['pnumreg']."";

$QryIsPessoaEquipamento = query($SqlIsPessoaEquipamento);
$ArIsPessoaEquipamento  = farray($QryIsPessoaEquipamento);

//verifica se há mais valores com o numreg da classe pai
$SqlEncontraProdutoFilho = "select count(numreg) as quantidade from is_pessoa_equipamento where id_pessoa_equipamento_pai = '".$_GET['pnumreg']."'";
$QryEncontraProdutoFilho = query($SqlEncontraProdutoFilho);
$ArEncontraProdutoFilho = farray($QryEncontraProdutoFilho);

if($ArIsPessoaEquipamento['id_produto'] != ''){

    if($ArEncontraProdutoFilho['quantidade'] == 0){
        
        $SqlProdutoEstrutura = "SELECT * FROM is_produto_estrutura WHERE id_produto_pai = '".$ArIsPessoaEquipamento['id_produto']."'";
        $QryProdutoEstrutura = query($SqlProdutoEstrutura);
        $i = 1;
        while($ArProdutoEstrutura = farray($QryProdutoEstrutura)){

            $ArInsert = array(
            'nr_serie'                     => $ArIsPessoaEquipamento['nr_serie']."_".$i,
            'id_fornecedor'                => $ArIsPessoaEquipamento['id_fornecedor'],
            'modelo'                       => $ArIsPessoaEquipamento['modelo'],
            'dt_fabricacao'                => $ArIsPessoaEquipamento['dt_fabricacao'],
            'id_pessoa'                    => $ArIsPessoaEquipamento['id_pessoa'],
            'obs'                          => $ArIsPessoaEquipamento['obs'],
            'informacao_complementar'      => $ArIsPessoaEquipamento['informacao_complementar'],
            'dt_instalacao'                => $ArIsPessoaEquipamento['dt_instalacao'],
            'id_grupo_cal'                 => $ArIsPessoaEquipamento['id_grupo_cal'],
            'id_pessoa_contato_padrao'     => $ArIsPessoaEquipamento['id_pessoa_contato_padrao'],
            'id_pessoa_contato_secundario' => $ArIsPessoaEquipamento['id_pessoa_contato_secundario'],
            'id_produto'                   => $ArProdutoEstrutura ['id_produto_filho'],
            'id_produto_modelo'            => $ArIsPessoaEquipamento['id_produto_modelo'],
            'id_pom_erp'                   => $ArIsPessoaEquipamento['id_pom_erp'],
            'endereco'                     => $ArIsPessoaEquipamento['endereco'],
            'bairro'                       => $ArIsPessoaEquipamento['bairro'],
            'cidade'                       => $ArIsPessoaEquipamento['cidade'],
            'uf'                           => $ArIsPessoaEquipamento['uf'],
            'cep'                          => $ArIsPessoaEquipamento['cep'],
            'numero'                       => $ArIsPessoaEquipamento['numero'],
            'complemento'                  => $ArIsPessoaEquipamento['complemento'],
            'pais'                         => $ArIsPessoaEquipamento['pais'],
            'id_cep'                       => $ArIsPessoaEquipamento['id_cep'],
            'id_produto_tipo'              => $ArIsPessoaEquipamento['id_produto_tipo'],
            'nr_nota'                      => $ArIsPessoaEquipamento['nr_nota'],
            'id_grupo_cal'                 => $ArIsPessoaEquipamento['id_grupo_cal'],
            'dt_nota'                      => $ArIsPessoaEquipamento['dt_nota'],
            'id_pessoa_equipamento_pai'    => $ArIsPessoaEquipamento['numreg']
            );

            $SqlInsert = AutoExecuteSql(TipoBancoDados,'is_pessoa_equipamento', $ArInsert, 'INSERT');
            $qry = query($SqlInsert);

            if (!$qry) {
                echo $SqlInsert;            
            }
            $i++;
        }
        echo '<center><h3>Quantidade de registros importados:'.$i.' </h3></center>';
    }
    else{
        echo '<center><h3>Estrutura já importada</h3></center>';
    }
}
else{
    echo '<center><h3>Não foi relacionado produto</h3></center>';
}
?>
<center><a href="javascript:window.close()">Fechar</a></center>