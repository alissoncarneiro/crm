<?php
/*
 * solicitacao_comercial_grava_itens_kit.php
 * Autor: Alex
 * 15/03/2011 15:14
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
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

$IdKIT = $_POST['sck_ci_id_kit'];

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
        if($_POST['sck_chk_gravar_'.$ArItensKIT['numreg']] != '1'){
            continue;
        }

        $ArSqlInsert = array();

        $ArSqlInsert['id_produto']                  = $ArItensKIT['id_produto'];

        $ArSqlInsert['id_atividade']                = $_POST['sck_id_atividade'];
        $ArSqlInsert['id_tp_grupo_motivo_atend']    = $_POST['sck_id_tp_grupo_motivo_atend'];
        $ArSqlInsert['atend_id_forma_contato']      = $_POST['sck_atend_id_forma_contato'];
        $ArSqlInsert['atend_id_origem']             = $_POST['sck_atend_id_origem'];
        $ArSqlInsert['dt_inicio']                   = dtbr2en($_POST['sck_dt_inicio']);
        $ArSqlInsert['id_tp_atividade']             = $_POST['sck_id_tp_atividade'];
        $ArSqlInsert['obs']                         = $_POST['sck_obs'];

        $ArSqlInsert['id_tp_motivo_atend']          = $_POST['sck_tp_motivo_atend_'.$ArItensKIT['numreg']];
        $ArSqlInsert['acao_id_tab_preco']           = $_POST['sck_acao_id_tab_preco_'.$ArItensKIT['numreg']];
        $ArSqlInsert['qtde']                        = $_POST['sck_qtde_'.$ArItensKIT['numreg']];
        $ArSqlInsert['acao_dt_desejada']            = dtbr2en($_POST['sck_acao_dt_desejada_'.$ArItensKIT['numreg']]);

        /* Buscando na tabela de parâmetros as informações automáticas do formulário */
        $SqlParamSolCom = "SELECT * FROM is_atividade_solicitacao_param
                                WHERE
                                    (id_tp_motivo_atend IS NULL OR id_tp_motivo_atend = '".$ArSqlInsert['id_tp_motivo_atend']."')
                                AND
                                    (id_produto is null or id_produto = '".$ArSqlInsert['id_produto']."')
                                ORDER BY
                                    id_tp_motivo_atend desc, id_produto DESC";
        $QryParamSolCom = query($SqlParamSolCom);
        $ArParamSolCom = farray($QryParamSolCom);

        $ArSqlInsert['acao_id_tp_atividade']        = $ArParamSolCom['id_tp_atividade'];
        $ArSqlInsert['acao_id_usuario_resp']        = $ArParamSolCom['id_usuario_resp_padrao'];
        $ArSqlInsert['acao_id_prioridade']          = $ArParamSolCom['id_prioridade_padrao'];
        $ArSqlInsert['acao_assunto']                = $ArParamSolCom['assunto_padrao'];
        $ArSqlInsert['acao_sn_gerar_oportunidade']  = $ArParamSolCom['sn_gerar_oportunidade'];
        $ArSqlInsert['acao_sn_gerar_orcamento']     = $ArParamSolCom['sn_gerar_orcamento'];

        /* Aplicando consistencias */
        if(trim($ArSqlInsert['qtde']) == ''){
            $Status = 2;
            $Acao = 2;
            $Mensagem .= 'Campo Qtde. deve ser informado.';
            break;
        }
        if(trim($ArSqlInsert['acao_id_tab_preco']) == ''){
            $Status = 2;
            $Acao = 2;
            $Mensagem .= 'Campo Tab. Preço deve ser informado.';
            break;
        }

        $ArraySQL[$ArItensKIT['nome_produto']] = AutoExecuteSql(TipoBancoDados, 'is_atividade_solicitacao', $ArSqlInsert, 'INSERT');
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
?>