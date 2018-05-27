<?php
/*
 * c_coaching_tela_inscricao_post.php
 * Autor: Alex
 * 27/07/2011 10:22:29
 */

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

session_start();
if($_SESSION['id_usuario'] == ''){
    echo 'Usuário não logado.';
    exit;
}

require('../../../../conecta.php');
require('../../../../functions.php');
require('../../../../classes/class.uB.php');
require('../../../../classes/class.Url.php');
require('../../../../classes/class.Pessoa.php');
require('c_coaching.class.Inscricao.php');
require('c_coaching.class.InscricaoCurso.php');

if(!$_POST){
    BloqueiaAcessoDireto();
}

$XML = '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.'>
    <resposta>
        <status>{!STATUS!}</status>
            <acao>{!ACAO!}</acao>
            <url><![CDATA[{!URL!}]]></url>
            <mensagem><![CDATA[{!MENSAGEM!}]]></mensagem>
    </resposta>
';

$_POST = uB::UrlDecodePost($_POST);

$IdRequisicao = $_POST['id_requisicao'];
$IdInscricao    = trim($_POST['pnumreg']);
/*$id_curso =  trim($_POST['id_curso']);*/

if($IdRequisicao != 1 && $IdInscricao != ''){
    $Inscricao = new Inscricao($IdInscricao);
}

if($IdRequisicao == 1){
    $IdCurso            = trim($_POST['id_curso']);
    $IdPessoa           = trim($_POST['id_pessoa']);
    $IdPessoaFinanceiro = trim($_POST['id_pessoa_financeiro']);
    $DtInscricao        = dtbr2en(trim($_POST['dt_inscricao']));
    $HrInscricao        = trim($_POST['hr_inscricao']);
    $IdVendedor         = trim($_POST['id_vendedor']);

    if($IdCurso == '' || $IdPessoa == '' || $DtInscricao == '' || $HrInscricao == ''){
        $Status = 2;
        $Mensagem = 'Parâmetros inválidos';
    }
    else{

        $Inscricao = new Inscricao(NULL);

        $Inscricao->setDadoInscricao('id_curso',$IdCurso);
        $Inscricao->setDadoInscricao('id_pessoa',$IdPessoa);
        $Inscricao->setDadoInscricao('id_pessoa_financeiro',$IdPessoaFinanceiro);
        $Inscricao->setDadoInscricao('dt_inscricao',$DtInscricao);
        $Inscricao->setDadoInscricao('hr_inscricao',$HrInscricao);
        $Inscricao->setDadoInscricao('id_vendedor',$IdVendedor);
        $Inscricao->setDadoInscricao('sn_dados_confirmados',1);

        $Inscricao->AtualizaDadosBD();

        $Status = 1;
        $Mensagem = 'Dados confirmados com sucesso!';
        $Acao = 2;        
        
        $Url = new Url();
        $Url->setUrl($_POST['url_retorno']);
        $Url->AlteraParam('pnumreg', $Inscricao->getNumregInscricao());
        $Url = $Url->getUrl();
    }
}
elseif($IdRequisicao == 2){
    $IdAgenda = $_POST['id_agenda'];
    $Inscricao->AdicionaInscricaoCurso($IdAgenda);
}
elseif($IdRequisicao == 3){
    $IdAgenda = $_POST['id_agenda'];
    $Inscricao->ExcluiInscricaoCurso($IdAgenda);
}
elseif($IdRequisicao == 4){
    $VlTotalInscricao   = TrataFloatPost($_POST['vl_total_inscricao']);
    $Obs                = $_POST['obs'];
    $IdAgenda           = $_POST['id_agenda'];
    
    $Pessoa = new Pessoa($Inscricao->getDadosInscricao('id_pessoa'));
    if(!$Pessoa->isCliente()){
        $Status = 2;
        $Acao = 1;
        $Mensagem = 'Para finalizar a Inscri&ccedil;&atilde;o a conta deve possuir status de cliente!';
    }
    elseif(!$Inscricao->ValidaValorTotalPagamento($VlTotalInscricao)){
        $Status = 2;
        $Acao = 1;
        $Mensagem = 'Valor total da Inscri&ccedil;&atilde;o diferente da soma dos valores dos pagamentos!';
    }
	/*elseif($id_curso == 1){
			$sqlDividePagamento = "SELECT 
										sum(pagto.vl_pagto) as valor,
										estabelecimento.nome_estabelecimento 
									FROM 
										c_coaching_inscricao_pagto  as pagto
									INNER JOIN
										 is_estabelecimento as estabelecimento on estabelecimento.numreg = pagto.id_estabelecimento
									WHERE
										pagto.id_inscricao = $IdInscricao
									GROUP BY 
										id_estabelecimento 
									ORDER BY pagto.vl_parcela desc";
									
			$qryDividePagamento = query($sqlDividePagamento);
			while($arDividePagamento = farray($qryDividePagamento)){
				$vl[] = array(
					'nome_estabelecimento'=> $arDividePagamento['nome_estabelecimento'],
					'valor'=> $arDividePagamento['valor']
				);
			}
			$diferenca = $vl[1]['valor'] - $vl[0]['valor'];
			if($vl[0]['valor'] != $vl[1]['valor']){
				$Status = 2;
				$Acao = 1;
				$Mensagem = "<p>Valor total da Inscri&ccedil;&atilde;o diferente da soma dos valores dos pagamentos!</p><br />
							 <p>".$vl[0]['nome_estabelecimento']." = R$ ".number_format($vl[0]['valor'],2,",",".")."</p>
							 <p>".$vl[1]['nome_estabelecimento']." = R$ ".number_format($vl[1]['valor'],2,",",".")."</p>
							 <p>Diferen&ccedil;a = R$ ".number_format($diferenca,2,",",".")."</p>";
			}else{
				$Inscricao->FinalizaInscricao($VlTotalInscricao,$Obs);
				$Status = 1;
				$Acao = 3;
				$Mensagem = 'Inscri&ccedil;&atilde;o finalizada com sucesso!';
			}
	}*/
	else{
			$Inscricao->FinalizaInscricao($VlTotalInscricao,$Obs);
			$Status = 1;
			$Acao = 3;
			$Mensagem = 'Inscri&ccedil;&atilde;o finalizada com sucesso!';
	}
}
else{
    $Status = 2;
    $Mensagem = 'Parâmetros inválidos';
}


$XML = str_replace('{!STATUS!}',$Status,$XML);
$XML = str_replace('{!ACAO!}',$Acao,$XML);
$XML = str_replace('{!URL!}',$Url,$XML);
$XML = str_replace('{!MENSAGEM!}',$Mensagem,$XML);
header("Content-Type: text/xml");
echo $XML;
?>