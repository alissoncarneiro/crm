<?php
/*
 * gera_orcamento_atend_lab_post.php
 * Autor: Alex
 * 05/09/2011 14:55:16
 */
session_start();
header("Content-Type: text/html; charset=ISO-8859-1");
if($_SESSION['id_usuario'] == ''){
    echo '<script type="text/javascript"> alert(\'Usuário não está logado.\'); window.location.href = window.location.href; </script>';
    exit;
}
$PrefixoIncludes = '../venda/';
require('../venda/includes.php');

$Orcamento = new Orcamento(1,NULL);
$Orcamento->setDadoVenda('id_pessoa', $_POST['id_pessoa']);
$Orcamento->setDadoVenda('id_origem_sistema', 1);
$Orcamento->AtualizaDadosVendaBD();

$IdOrcamento = $Orcamento->getNumregVenda();

$SqlAtendimentos = "SELECT numreg FROM is_atividade WHERE id_tp_atividade = 55 AND id_status_reparo = 8 AND id_pessoa = ".$_POST['id_pessoa']." AND id_fabricante = '".$_POST['id_fabricante']."' AND id_orcamento IS NULL";
$QryAtendimentos = query($SqlAtendimentos);
while($ArAtendimentos = farray($QryAtendimentos)){
    if($_POST['edtchk_atendimento_'.$ArAtendimentos['numreg']] != '1'){
        continue;
    }
    $SqlAtendimentoItens = "SELECT * FROM is_produto_orcamento_lab WHERE id_atividade = '".$ArAtendimentos['numreg']."' AND sn_orcamento = 1";
    $QryAtendimentoItens = query($SqlAtendimentoItens);
    $QtdeItens = 0;
    while($ArAtendimentoItens = farray($QryAtendimentoItens)){
        $ArSqlInsertItem = array(
            'id_orcamento'      => $IdOrcamento,
            'id_produto'        => $ArAtendimentoItens['id_produto'],
            'id_unid_medida'    => $ArAtendimentoItens['id_unid_medida'],
            'qtde'              => $ArAtendimentoItens['qtde'],
            'obs'               => $ArAtendimentoItens['obs']
        );
        $SqlInsertItem = AutoExecuteSql(TipoBancoDados, 'is_orcamento_item_pre', $ArSqlInsertItem, 'INSERT');
        $QryInsertItem = query($SqlInsertItem);
        if($QryInsertItem){
            $QtdeItens++;
        }
    }
    if($QtdeItens > 0){
        $ArSqlUpdateAtendimento = array('numreg' => $ArAtendimentos['numreg'], 'id_orcamento' => $IdOrcamento);
        $SqlUpdateAtendimento = AutoExecuteSql(TipoBancoDados, 'is_atividade', $ArSqlUpdateAtendimento, 'UPDATE', array('numreg'));
        $QryUpdateAtendimento = query($SqlUpdateAtendimento);
    }
}
GravaLogEvento(500, true, 'Geração de orçamento através de atendimento de laboratório. Orçamento gerado Nº '.$IdOrcamento);
echo 'Orçamento Nº '.$IdOrcamento.' gerado com sucesso!';
?>