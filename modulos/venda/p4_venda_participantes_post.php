<?php

/*
 * particiantes_venda_excluir.php
 * Autor: Lucas
 * 30/11/2010 16:13:00
 * -
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
header("content-type: text/xml");
session_start();
require('includes.php');

$_POST = uB::UrlDecodePost($_POST);
/*
 * Verifica se a v�ri�vel de tipo da venda foi preenchida.
 */
if(empty($_POST['ptp_venda']) || empty($_POST['pnumreg'])){
    echo getError('0040030001',getParametrosGerais('RetornoErro'));
    exit;
}
if($_POST['ptp_venda'] == 1 || $_POST['ptp_venda'] == 2){
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
    }
    elseif($_POST['ptp_venda'] == 2){
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
    }
}
$Acao = $_POST['Acao'];

/*
 * Adicionando um participante
 */
if($Acao == 'adicionar'){
    if(TrataFloatPost($_POST['pct_comissao']) >= 100){
        $Status = 'false';
        $Mensagem = 'Comiss�o n�o pode ser superior � 100%';
    }
    else{
        if($Venda->AdicionaRepresentanteBD($_POST['id_tp_participacao_venda'],$_POST['id_participante'],$_POST['pct_comissao'])){
            $Status = 'true';
            $Mensagem = 'Participante inserido com sucesso!';
        }
        else{
            $Mensagem = TextoParaXML($Venda->getMensagem());
        }
    }
}
/*
 * Alterando o participante
 */
elseif($Acao == 'alterar'){
    if($Venda->AtualizaRepresentanteBD($_POST['NumregVendaRepresentante'],$_POST['pct_comissao'],0)){
        $Status = 'true';
        $Mensagem = 'Representante atualizado com sucesso!';
    }
    else{
        $Mensagem = TextoParaXML($Venda->getMensagem());
    }
}
/*
 * Alterando a comiss�o do participante
 */
elseif($Acao == 'alterar_comissao'){
    if(TrataFloatPost($_POST['pct_comissao']) >= 100){
        $Status = 'false';
        $Mensagem = 'Comiss�o n�o pode ser superior � 100%';
    }
    else{
        if($Venda->AtualizaRepresentanteBD($_POST['NumregVendaRepresentante'],$_POST['pct_comissao'],1,$_POST['pjustificativaalteracacomissao'])){// Terceiro par�metro 1, indica que foi alterado manualmente. Esta flag indica que nao deve ser recalculado
            $Status = 'true';
            $Mensagem = 'Comiss�o atualizada com sucesso!';
        }
        else{
            $Mensagem = TextoParaXML($Venda->getMensagem());
        }
    }
}
/*
 * Excluindo o participante
 */
elseif($Acao == 'excluir'){
    if($Venda->RemoveRepresentanteBD($_POST['NumregVendaRepresentante'])){
        $Status = 'true';
        $Mensagem = 'Representante atualizado com sucesso!';
    }
    else{
        $Mensagem = TextoParaXML($Venda->getMensagem());
    }
}
header("Content-Type: text/xml");
echo '<?'.'xml version="1.0" encoding="ISO-8859-1"'.'?>'."\n";
echo '<root>'."\n";
echo "\t".'<status>'.$Status.'</status>'."\n";
echo "\t".'<mensagem>';
echo $Mensagem;
echo '</mensagem>'."\n";
echo '</root>';
?>