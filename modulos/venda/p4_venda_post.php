<?php
/*
 * p4_venda_post.php
 * Autor: Alex
 * 05/11/2010 14:50
 */
header("Content-Type: text/html; charset=ISO-8859-1",true);
session_start();
require('includes.php');
/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */

if($_POST['ptp_venda'] == 1 || $_POST['ptp_venda'] == 2){
        if($_POST['ptp_venda'] == 1){
            $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
        }
        elseif($_POST['ptp_venda'] == 2){
            $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
        }
} else{
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}

$Usuario = new Usuario($_SESSION['id_usuario']);

$_POST = uB::UrlDecodePost($_POST);

$XML = '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.'>
    <resposta>
        <status>{!STATUS!}</status>
            <acao>{!ACAO!}</acao>
            <url><![CDATA[{!URL!}]]></url>
            <mensagem><![CDATA[{!MENSAGEM!}]]></mensagem>
    </resposta>
';

$IdRepresentantePrincipal = $Venda->getRepresentantePrincipal();

$VendaParametro = new VendaParametro();

$Status = '';
$Mensagem = '';

if($_POST['prequisicao'] == 'finaliza_orcamento'){
    if($Venda->PossuiItensSemPreco()){
        $Status = 2;
        $Mensagem = ucwords($Venda->getTituloVenda(false)).' possui itens sem preço. Não é permitido nestas condições.';
    }
    else{
        /* Finalizando o orçamento */
        if($Venda->CompletaDigitacaoVendaBD()){
            $Status = 1;
            $Acao = 1;
            $Mensagem = 'Orçamento finalizado com sucesso.';
            $Venda->GeraAtualizaOportunidadePaiEFilha();
            $Mensagem .= $Venda->getMensagem();
        }
        else{
            $Status = 2;
            $Mensagem = 'Erro ao finalizar o '.$Venda->getTituloVenda().'.';
        }
    }
}
elseif($_POST['prequisicao'] == 'reabre_orcamento'){

    if($Venda->GeraRevisaoVenda()){
        if($Venda->ReabreDigitacaoVendaBD()){
            $Status = 1;
            $Acao = 3;
            $Mensagem = 'Orçamento reaberto com sucesso. Foi gerada a revisão Nº '.$Venda->NumeroRevisaoGerada.'.';
            $Venda->GravaLogBD(15, 'Orçamento reaberto');
            $Url = new Url();
            $Url->setUrl($_POST['url_retorno']);
            $Url->AlteraParam('ppagina','p1');
            $Url = $Url->getUrl();
        }
        else{
            $Status = 2;
            $Mensagem = 'Erro ao Reabrir.';            
        }
    }
    else{
        $Status = 2;
        $Mensagem = $Venda->getMensagemDebug(true);        
    }
}
elseif($_POST['prequisicao'] == 'clona_venda'){
    if($Venda->GeraClone()){
        $Status = 1;
        $Acao = 3;
        $Url = new Url();
        $Url->setUrl($_POST['url_retorno']);
        $Url->AlteraParam('pnumreg',$Venda->NumregCloneGerado);
        $Url->AlteraParam('ppagina','p1');
        $Url = $Url->getUrl();
    }
    else{
        $Status = 2;
    }
    $Mensagem = $Venda->getMensagem();
}
elseif($Venda->PossuiItensNaoComerciais()){
    $Status = 2;
    $Mensagem = "Não permitido quando possui itens não comerciais.";
}
elseif($Venda->getQtdeRepresentantes() == 0){ /* Validando se possui representantes */
    $Status = 2;
    $Mensagem = ucwords($Venda->getTituloVenda(false)).' não possui nenhum representante/vendedor. Não é permitido salvar sem representante/vendedor.';
}
elseif($_POST['prequisicao'] == 'finaliza_venda'){
    $Venda->ValidaPoliticaComercialDesc();
    if(!$Venda->getStatusPoliticaComercialDesc()){//Se não está na política comercial envia para aprovação
        $Venda->EnviaParaAprovacao($_POST['pjustificativaemaprovacaocomercial']);

        $Status = 1;
        if($Usuario->getPermissao('sn_permite_aprovar_venda') || $Usuario->getPermissao('sn_permite_reprovar_venda')){
            $Acao = 1;
        }
        
        $Mensagem = $Venda->getMensagem();
    }
    else{
        if($Venda->isOrcamento()){//Se for um orçamento
            $Venda->GravaCFOPVendaBD();

            if($Venda->getSnCondPagtoProgramado()){
                $Venda->AtualizaCondPagtoProgramadoBD();
            }
            $NumregNovoPedido = $Venda->TransformarEmPedidoBD();//Criando o pedido
            if(is_numeric($NumregNovoPedido)){//Se foi gerado um pedido
				// 
				$Venda->TransformaCondPagtoEmPedidoBD($NumregNovoPedido);			
                $Venda->CompletaDigitacaoVendaBD();
                $Venda->GeraAtualizaOportunidadePaiEFilha();
                $Venda->AlteraStatusAtendimentoLaboratorio();

                /* Carregando pedido Gerado e calculando a bonificação para o mesmo */
                $VendaGerada = new Pedido(2,$NumregNovoPedido);
                $VendaGerada->CalculaValorBonificacao();
                if($Venda->getVendaParametro()->getSnExportaPedidoAoFinalizar()){
                    $VendaGerada->ExportaPedido();
                }
                $Status = 1;
                $Mensagem = $Venda->getMensagem();
            }
            else{//Se não foi gerado o pedido
                $Status = 1;
                $Mensagem = $Venda->getMensagem();
            }
        }
        else{
            $Acao = 1;
            #$Venda->setDadoVenda('sn_reprovado_comercial', 0);
            $Venda->setDadoVenda('sn_em_aprovacao_comercial', 0);
            if($Venda->getSnCondPagtoProgramado()){
                $Venda->AtualizaCondPagtoProgramadoBD();
            }
            $Venda->GravaCFOPVendaBD();
            $Venda->PreencheIdVendaClienteBD();
            $Venda->CompletaDigitacaoVendaBD();
            $Venda->GeraAtualizaOportunidadePaiEFilha();
            $Venda->AlteraStatusAtendimentoLaboratorio();
            if($Venda->getDadosVenda('id_tp_venda') == '1'){
                $Venda->CalculaValorBonificacao();
            }
            
            if($Venda->getVendaParametro()->getSnExportaPedidoAoFinalizar()){
                $Venda->ExportaPedido();
            }
            $Status = 1;
            $Mensagem = 'Pedido finalizado. Nº '.$Venda->getDadosVenda('id_venda_cliente');
        }
        if($Status == 1 && $Venda->isOrcamento()){
            $Acao = 3;
            $Url = new Url();
            $Url->setUrl($_POST['url_retorno']);
            $Url->AlteraParam('ptp_venda',2);
            $Url->AlteraParam('pnumreg',((is_numeric($NumregNovoPedido))?$NumregNovoPedido:$Venda->getNumregVenda()));
            $Url->AlteraParam('pfuncao','pedido');
            $Url->AlteraParam('ppagina','p4');
            $Url = $Url->getUrl();
        }
        VendaCallBackCustom::ExecutaVenda($Venda, 'Passo4_finaliza_venda', 'Final',array('status' => $Status));
    }
}
elseif($_POST['prequisicao'] == 'aprovar'){
    $Venda->ValidaPoliticaComercialDesc();
    if($Venda->getStatusPoliticaComercialDesc()){//Se a venda está dentro da política comercial, não permite aprovação){
        $Status = 2;
        $Mensagem = 'Não é permitido aprovar um '.$Venda->getTituloVenda(false,true).' que não esteja em aprovação comercial!';
    }
    elseif(!$Usuario->getPermissao('sn_permite_aprovar_venda')){//Se o usuário não possui permissão para aprovar a venda
        $Status = 2;
        $Mensagem = 'Usuário logado não possui permissão para aprovar o '.$Venda->getTituloVenda(false,true);
    }
    else{//Aprovando a venda
        $Venda->GravaCFOPVendaBD();
        if($Venda->AprovaVendaBD($_POST['pjustificativaaprovreprovcomercial'])){
            $Venda->GeraAtualizaOportunidadePaiEFilha();
            $Venda->AlteraStatusAtendimentoLaboratorio();            
            $Status = 1;
			$insertPrograma = "insert into tb_competencias_coach_coachee_programa (`fk_coach`,`fk_coachee`,`fk_tipo_assessments`,`competencias_coach_coachee_programa_numreg_situacao`, `competencias_coach_coachee_programa_numreg_situacao_data`) values ";

			$fk_coach = $_SESSION["id_usuario"];
			$fk_coachee = $Venda->getDadosVenda("id_pessoa");
			$data = date('Y-m-d H:i:s');
			$id_sistema = $Venda->getDadosVenda("id_origem_sistema");
			
			$insertPrograma .= "('$fk_coach','$fk_coachee','1','1','$data'),('$fk_coach','$fk_coachee','2','1','$data')";
			
			if($id_sistema == "99")
				mysql_query($insertPrograma);
        }
        else{
            $Status = 2;
        }        
        $Mensagem = $Venda->getMensagem();
    }
}
elseif($_POST['prequisicao'] == 'reprovar'){
    $Venda->ValidaPoliticaComercialDesc();
    if($Venda->getStatusPoliticaComercialDesc()){//Se a venda está dentro da política comercial, não permite reprovação){
        $Status = 2;
        $Mensagem = 'Não é permitido reprovar um '.$Venda->getTituloVenda(false,true).' que não esteja em aprovação comercial!';

    }
    elseif(!$Usuario->getPermissao('sn_permite_reprovar_venda')){//Se o usuário não possui permissão para reprovar a venda
        $Status = 2;
        $Mensagem = 'Usuário logado não possui permissão para reprovar o '.$Venda->getTituloVenda(false,true);
    }
    else{//Reprovando a venda
        if($Venda->ReprovaVendaBD($_POST['pjustificativaaprovreprovcomercial'])){
            $Venda->GeraAtualizaOportunidadePaiEFilha();
            $Status = 1;
        }
        else{
            $Status = 2;
        }
        $Status = $Status;
        $Mensagem = $Venda->getMensagem();
    }
}
elseif($_POST['prequisicao'] == 'cria_pedido_bonificacao'){
    $Status = $Venda->CriaPedidoBonificacao();
    $Status = ($Status === true)?1:2;
    $Mensagem = $Venda->getMensagem();
    $Acao = 2;
    $Url = new Url();
    $Url->setUrl($_POST['url_retorno']);
    $Url->AlteraParam('ptp_venda',2);
    $Url->AlteraParam('pnumreg',$Venda->getNumregPedidoBonificacao());
    $Url->AlteraParam('pfuncao','pedido');
    $Url->AlteraParam('ppagina','p2');
    $Url = $Url->getUrl();
}
elseif($_POST['prequisicao'] == 'cancelar_bonificacao'){
    $Status = $Venda->CancelaBonificacao();
    $Status = ($Status === true)?1:2;
    $Mensagem = $Venda->getMensagem();
    $Acao = 2;
    $Url = new Url();
    $Url->setUrl($_POST['url_retorno']);
    $Url = $Url->getUrl();
}
/* Desativado até que seja tratada a integridade dos dados
elseif($_POST['prequisicao'] == 'restaura_revisao'){
    if($Venda->RestauraRevisao($_POST['pnumreg_revisao'])){
        $Status = 1;
        $Acao = 1;
        $Mensagem = TextoParaXML('Revisão restaurada com sucesso.');
    }
    else{
        $Status = 2;
        $Mensagem = TextoParaXML($Venda->getMensagem().'Erro com a requisição.');
    }
}
*/
elseif($_POST['prequisicao'] == 'perder_orcamento'){
    if($Venda->PerdeOrcamento($_POST['id_motivo_cancelamento'])){
        $Venda->AlteraStatusAtendimentoLaboratorio();
        $Status = 1;
        $Acao = 1;
        $Mensagem = 'Orçamento perdido.';
    }
    else{
        $Status = 2;
        $Mensagem = $Venda->getMensagem().'Erro com a requisição.';
    }
}
elseif($_POST['prequisicao'] == 'cancelar_pedido'){
    if($Venda->CancelaVenda($_POST['id_motivo_cancelamento'])){
        $Status = 1;
        $Acao = 1;
        $Mensagem = 'Pedido cancelado.';
    }
    else{
        $Status = 2;
        $Mensagem = $Venda->getMensagem().'Erro com a requisição.';
    }
}
elseif($_POST['prequisicao'] == 'exportar_pedido'){
    if($Venda->isPedido()){
        $Venda->ExportaPedido();
        $Status = 1;
        $Acao = 1;
        $Mensagem = 'Pedido Exportado.';
    }
    else{
        $Status = 2;
        $Mensagem = 'Este registro não pode ser exportado.';
    }
}
else{
    $Status = 2;
    $Mensagem = $Venda->getMensagem().'Nenhuma ação foi selecionada.';
}
if($Venda->Debug){
    $Mensagem .= $Venda->getMensagemDebug();
}


$XML = str_replace('{!STATUS!}',$Status,$XML);
$XML = str_replace('{!ACAO!}',$Acao,$XML);
$XML = str_replace('{!URL!}',$Url,$XML);
$Mensagem = ($Mensagem == '')?($Venda->getMensagem()):$Mensagem;
$XML = str_replace('{!MENSAGEM!}',$Mensagem,$XML);
header("Content-Type: text/xml");
echo $XML;
?>