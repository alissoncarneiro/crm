<?php
/*
 * oportunidade_grava_itens_kit.php
 * Autor: Alex
 * 18/12/2012 10:53:21
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

session_start();

require('../../conecta.php');
require('../../functions.php');

$Status     = 1;
$Acao       = 1;
$Mensagem   = '';
$Url        = '';

$IdKIT = $_POST['oik_ci_id_kit'];

if(trim($IdKIT) == ''){
    $Status = 2;
    $Acao = 2;
    $Mensagem .= 'Por favor selecione um KIT.';
}
else{
    $SqlItensKIT = "SELECT t1.numreg,t2.numreg AS id_produto, t2.id_produto_erp, t2.nome_produto FROM is_kit_produto t1 INNER JOIN is_produto t2 ON t1.id_produto = t2.numreg WHERE t1.id_kit = '".$IdKIT."'";
    $QryItensKIT = query($SqlItensKIT);
    $ArraySQL = array();
    while($ArItensKIT = farray($QryItensKIT)){

        /* Se o checkbox não estiver marcado ignora o item */
        if($_POST['oik_chk_gravar_'.$ArItensKIT['numreg']] != '1'){
            continue;
        }

        $ArSqlInsert = array();

        $ArSqlInsert['id_produto']                  = $ArItensKIT['id_produto'];
        $ArSqlInsert['id_oportunidade']             = $_POST['oik_id_oportunidade'];
        $ArSqlInsert['obs']                         = $_POST['oik_obs_'.$ArItensKIT['numreg']];
        $ArSqlInsert['qtde']                        = $_POST['oik_qtde_'.$ArItensKIT['numreg']];
        $ArSqlInsert['valor']                       = TrataFloatPost($_POST['oik_vl_unitario_'.$ArItensKIT['numreg']]);
        $ArSqlInsert['valor_total']                 = $ArSqlInsert['qtde'] * $ArSqlInsert['valor'];
        
        /* Aplicando consistencias */
        if(trim($ArSqlInsert['qtde']) == ''){
            $Status = 2;
            $Acao = 2;
            $Mensagem .= 'Campo Qtde. deve ser informado.';
            break;
        }
        
        $ArraySQL[$ArItensKIT['nome_produto']] = AutoExecuteSql(TipoBancoDados, 'is_opor_produto', $ArSqlInsert, 'INSERT');
    }
    /* Executando as querys se não houve nenhum problema de consistencia */
    if($Status == 1){
        foreach($ArraySQL as $NomeProduto => $SQL){
            if(query($SQL)){
                $Mensagem .= $NomeProduto.' inserido com sucesso.'."\r\n";
            }
            else{
                $Mensagem .= 'Erro ao inserir item '.$NomeProduto.'.'."\r\n";
            }
        }
    }
}

$XML = '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.'>
    <resposta>
        <status>{!STATUS!}</status>
            <acao>{!ACAO!}</acao>
            <url>{!URL!}</url>
            <mensagem><![CDATA[{!MENSAGEM!}]]></mensagem>
    </resposta>
';
$XML = str_replace('{!STATUS!}',$Status,$XML);
$XML = str_replace('{!ACAO!}',$Acao,$XML);
$XML = str_replace('{!URL!}',$Url,$XML);
$XML = str_replace('{!MENSAGEM!}',$Mensagem,$XML);
header("Content-Type: text/xml");
echo $XML;