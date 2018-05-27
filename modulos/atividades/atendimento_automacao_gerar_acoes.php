<?

@header("Content-Type: text/html;  charset=ISO-8859-1", true);
@session_start();
$PrefixoIncludes = '../venda/';
require('../venda/includes.php');

require_once("../../conecta.php");
require_once("../../funcoes.php");
require_once("../../functions.php");

$pnumreg = $_GET["pnumreg"];
$id_usuario = $_SESSION["id_usuario"];

$a_atend = farray(query("select * from is_atividade where numreg = '" . $pnumreg . "'"));
$msg_alert = '';

//=======================================================================
// Checar se deve gerar Atividades
//=======================================================================
$q_solic_distinct = query("select distinct id_tp_grupo_motivo_atend, id_tp_motivo_atend, acao_id_tp_atividade, acao_id_usuario_resp, acao_id_prioridade, acao_dt_desejada from is_atividade_solicitacao where id_atividade = '" . $pnumreg . "' and (acao_id_atividade_gerada is null) and (not acao_id_tp_atividade is null) order by id_tp_grupo_motivo_atend, id_tp_motivo_atend, acao_id_tp_atividade, acao_id_usuario_resp, acao_id_prioridade");
$n_acoes = 0;
// Para cada Solicitação, Tipo de Atividade, Responsável e Prioridade distinta deve gerar uma ação
while ($a_solic_distinct = farray($q_solic_distinct)) {
    if(trim($a_solic_distinct['acao_id_tp_atividade']) == ''){
        continue;
    }
    // Montando o conteúdo do campo observações
    $obs = "";
    $acao_assunto = "";
    $dt_desejada = "";
    $q_solic = query("select * from is_atividade_solicitacao where id_atividade = '" . $pnumreg . "' and (acao_id_atividade_gerada is null) and (not acao_id_tp_atividade is null) and id_tp_grupo_motivo_atend='" . $a_solic_distinct["id_tp_grupo_motivo_atend"] . "' and id_tp_motivo_atend='" . $a_solic_distinct["id_tp_motivo_atend"] . "' and acao_id_tp_atividade = '" . $a_solic_distinct["acao_id_tp_atividade"] . "' and acao_id_usuario_resp = '" . $a_solic_distinct["acao_id_usuario_resp"] . "' and acao_id_prioridade = '" . $a_solic_distinct["acao_id_prioridade"] . "'");
    while ($a_solic = farray($q_solic)) {
        if ($a_solic["obs"]) {
            $obs .= $a_solic["obs"] . '<br />========================================<br/>';
        }
        $dt_desejada = $a_solic["acao_dt_desejada"];
        $acao_assunto = $a_solic["acao_assunto"];
    }

    // Calculando Data e Hora de Prazo de Acordo com a Prioridade ou Parametro de Uso da Data Desejada
    $a_tp_motivo_atend = farray(query("select * from is_tp_motivo_atend where numreg = " . $a_solic_distinct["id_tp_motivo_atend"]));

    if (($a_tp_motivo_atend["sn_usa_dt_desejada"] == '1') && ($dt_desejada)) {
        $DtPrazoAtiv = $dt_desejada;
        $HrPrazoAtiv = Date("H:i");
    } else {
        $cDataAtiv = Date("Y-m-d");
        $cTimeAtiv = Date("H:i");
        $a_prioridade = farray(query("select qtde_horas_prz from is_prioridade where numreg = " . $a_solic_distinct["acao_id_prioridade"]));
        $cTimeAtiv = SomaMinutosUteis($cTimeAtiv, $cDataAtiv, ( $a_prioridade["qtde_horas_prz"] * 60));
        $DtPrazoAtiv = substr($cTimeAtiv, 0, 10);
        $HrPrazoAtiv = substr($cTimeAtiv, 11, 5);
    }

    //
    // Processo de Inclusão da Ação
    $n_acoes++;
    $sql_insert = "INSERT INTO is_atividade  (
        id_tp_atividade,
        assunto,
        id_pessoa, 
        id_pessoa_contato,
        id_usuario_resp,
        id_usuario_cad,
        dt_cadastro,
        hr_cadastro,
        dt_inicio,
        hr_inicio,
        dt_prev_fim,
        hr_prev_fim,
        id_situacao,
        id_atividade_pai,
        obs
        ) values ( ";
    $sql_insert .= $a_solic_distinct["acao_id_tp_atividade"] . ",";
    $sql_insert .= "'" . $acao_assunto . "',";
    $sql_insert .= ( $a_atend["id_pessoa"] * 1) . ",";
    $sql_insert .= ( $a_atend["id_pessoa_contato"] * 1) . ",";
    $sql_insert .= $a_solic_distinct["acao_id_usuario_resp"] . ",";
    $sql_insert .= $_SESSION["id_usuario"] . ",";
    $sql_insert .= "'" . date("Y-m-d") . "',";
    $sql_insert .= "'" . date("H:i") . "',";
    $sql_insert .= "'" . $DtPrazoAtiv . "',";
    $sql_insert .= "'" . $HrPrazoAtiv . "',";
    $sql_insert .= "'" . $DtPrazoAtiv . "',";
    $sql_insert .= "'" . $HrPrazoAtiv . "',";
    $sql_insert .= "1,";
    $sql_insert .= $pnumreg . ",'";
    $sql_insert .= $obs . "')";
    //echo $sql_insert . '<br>';
    query($sql_insert);
    $a_max_numreg_ativ_gerada = farray(query("select max(numreg) as ultimo from is_atividade where id_atividade_pai = " . $pnumreg . " and id_tp_atividade = " . $a_solic_distinct["acao_id_tp_atividade"]));
    query("update is_atividade_solicitacao set acao_id_atividade_gerada = '" . $a_max_numreg_ativ_gerada["ultimo"] . "' where id_atividade = " . $pnumreg . "  and (acao_id_atividade_gerada is null) and (not acao_id_tp_atividade is null) and id_tp_grupo_motivo_atend='" . $a_solic_distinct["id_tp_grupo_motivo_atend"] . "' and id_tp_motivo_atend='" . $a_solic_distinct["id_tp_motivo_atend"] . "' and acao_id_tp_atividade = '" . $a_solic_distinct["acao_id_tp_atividade"] . "' and acao_id_usuario_resp = '" . $a_solic_distinct["acao_id_usuario_resp"] . "' and acao_id_prioridade = '" . $a_solic_distinct["acao_id_prioridade"] . "'");
}


//=======================================================================
// Checar se deve gerar oportunidade
//=======================================================================
$n_oport = 0;
// Recuperando Tabela de Preço Padrão, Cidade, UF e Região para Gravar na Oportunidade facilitando Gestão posterior
$a_pessoa_oport = farray(query("select id_tab_preco_padrao, cidade, uf, id_regiao from is_pessoa WHERE numreg = '" . $a_atend["id_pessoa"]."'"));

// Conferir se existe solicitacao sem tabela de preco no caso da conta não ter tabela de preço associada
$a_consiste_tab_preco = farray(query("select numreg from is_atividade_solicitacao where id_atividade = '" . $pnumreg . "' and (acao_id_oportunidade_gerada is null) and (acao_sn_gerar_oportunidade = 1) and (acao_id_tab_preco is null)"));

if ((empty($a_pessoa_oport["id_tab_preco_padrao"])) && ($a_consiste_tab_preco["numreg"])) {
    $msg_alert .= 'Não foi possível gerar a(s) oportunidade(s) pois existem solicitações \\n sem tabela de preço associada e a conta não possui tabela de preço padrão !\\n';
} else {
    $q_solic_distinct = query("select distinct acao_id_usuario_resp, acao_id_tab_preco from is_atividade_solicitacao where id_atividade = '" . $pnumreg . "' and (acao_id_oportunidade_gerada is null) and (acao_sn_gerar_oportunidade = 1)");
    // Para cada Responsável distinto deve gerar uma oportunidade
    while ($a_solic_distinct = farray($q_solic_distinct)) {
        if (empty($a_solic_distinct['acao_id_tab_preco'])) {
            $tab_preco = $a_pessoa_oport["id_tab_preco_padrao"];
        } else {
            $tab_preco = $a_solic_distinct['acao_id_tab_preco'];
        }
        // Processo de Inclusão da Oportunidade
        $n_oport++;
        $ArInsertOportunidade = array(
            'assunto'               => 'Oportunidade gerada pelo Atendimento: '.$pnumreg,
            'id_pessoa'             => $a_atend["id_pessoa"],
            'id_pessoa_contato'     => $a_atend["id_pessoa_contato"],
            'id_usuario_resp'       => $a_solic_distinct["acao_id_usuario_resp"],
            'dt_inicio'             => date("Y-m-d"),
            'hr_inicio'             => date("H:i"),
            'dt_prev_fim'           => date("Y-m-d"),
            'hr_prev_fim'           => date("H:i"),
            'id_origem'             => $a_atend["atend_id_origem"],
            'cidade'                => $a_pessoa_oport['cidade'],
            'uf'                    => $a_pessoa_oport['uf'],
            'id_regiao'             => $a_pessoa_oport['id_regiao'],
            'id_tab_preco'          => $tab_preco,
            'id_atividade_pai'      => $a_atend["numreg"]
        );
        $sql_insert = AutoExecuteSql(TipoBancoDados, 'is_oportunidade', $ArInsertOportunidade, 'INSERT');
        //echo $sql_insert . '<br>';
        query($sql_insert);
        $a_max_numreg_ativ_gerada = farray(query("select max(numreg) as ultimo from is_oportunidade where id_atividade_pai = '" . $pnumreg. "'"));

        // Adicionando Itens
        $q_solic = query("select * from is_atividade_solicitacao where id_atividade = '" . $pnumreg . "' and (acao_id_oportunidade_gerada is null) and (acao_sn_gerar_oportunidade = 1) and acao_id_usuario_resp='" . $a_solic_distinct["acao_id_usuario_resp"] . "'");
        while ($a_solic = farray($q_solic)) {
            $a_prod_oport = farray(query("select id_fornecedor, id_linha, id_familia_comercial from is_produto WHERE numreg = '" . $a_solic['id_produto']."'"));
            $a_prod_preco = farray(query("select vl_unitario from is_tab_preco_valor WHERE id_tab_preco = '" . $tab_preco . "' and id_produto = '" . $a_solic['id_produto'] . "'"));
            $tot_item = $a_prod_preco["vl_unitario"] * $a_solic["qtde"];

            $ArSqlInsert = array(
                'id_produto'            => $a_solic["id_produto"],
                'id_tab_preco'          => $tab_preco,
                'id_fornecedor'         => $a_prod_oport['id_fornecedor'],
                'id_linha'              => $a_prod_oport['id_linha'],
                'id_familia_comercial'  => $a_prod_oport['id_familia_comercial'],
                'qtde'                  => $a_solic["qtde"],
                'valor'                 => $a_prod_preco["vl_unitario"],
                'valor_total'           => $tot_item,
                'id_oportunidade'       => $a_max_numreg_ativ_gerada["ultimo"]
            );
            $sql_insert = AutoExecuteSql(TipoBancoDados, 'is_opor_produto', $ArSqlInsert, 'INSERT');
            query($sql_insert);
        }
        query("update is_atividade_solicitacao set acao_id_oportunidade_gerada = '" . $a_max_numreg_ativ_gerada["ultimo"] . "' where id_atividade = '" . $pnumreg . "' and (acao_id_oportunidade_gerada is null) and (acao_sn_gerar_oportunidade = 1) and acao_id_usuario_resp='" . $a_solic_distinct["acao_id_usuario_resp"] . "'");
        $a_total_opor_produto = farray(query("SELECT sum(valor_total) AS total FROM is_opor_produto WHERE id_oportunidade = '" . $a_max_numreg_ativ_gerada["ultimo"] . "'"));
        query("update is_oportunidade set valor = " . ($a_total_opor_produto["total"] * 1) . " WHERE numreg = '" . $a_max_numreg_ativ_gerada["ultimo"]."'");
    }
}


//=======================================================================
// Checar se deve gerar Orçamento
//=======================================================================
$n_orc = 0;
// Recuperando Tabela de Preço Padrão
$a_pessoa_oport = farray(query("select id_tab_preco_padrao from is_pessoa WHERE numreg = " . $a_atend["id_pessoa"]));
// Conferir se existe solicitacao sem tabela de preco no caso da conta não ter tabela de preço associada
$a_consiste_tab_preco = farray(query("select numreg from is_atividade_solicitacao where id_atividade = '" . $pnumreg . "' and (acao_id_orcamento_gerado is null) and (acao_sn_gerar_orcamento = 1) and (acao_id_tab_preco is null)"));
if ((empty($a_pessoa_oport["id_tab_preco_padrao"])) && ($a_consiste_tab_preco["numreg"])) {
    $msg_alert .= 'Não foi possível gerar o(s) orçamento(s) pois existem solicitações \\n sem tabela de preço associada e a conta não possui tabela de preço padrão !\\n';
} else {
    $q_solic_distinct = query("select distinct acao_id_usuario_resp, acao_id_tab_preco from is_atividade_solicitacao where id_atividade = '" . $pnumreg . "' and (acao_id_orcamento_gerado is null) and (acao_sn_gerar_orcamento = 1)");
    // Para cada Responsável distinto deve gerar um orçamento
    while ($a_solic_distinct = farray($q_solic_distinct)) {
        if (empty($a_solic_distinct['acao_id_tab_preco'])) {
            $tab_preco = $a_pessoa_oport["id_tab_preco_padrao"];
        } else {
            $tab_preco = $a_solic_distinct['acao_id_tab_preco'];
        }
        // Processo de Inclusão da Orçamento
        $n_orc++;
        $IdUsuarioCad = ($a_solic_distinct["acao_id_usuario_resp"] == '')?$_SESSION['id_usuario']:$a_solic_distinct["acao_id_usuario_resp"];
        $Venda = new Orcamento(1,NULL);
        $Venda->setDadoVenda('id_pessoa', ( $a_atend["id_pessoa"] * 1) );
        $Venda->setDadoVenda('id_contato',( $a_atend["id_pessoa_contato"] * 1) );
        $Venda->setDadoVenda('id_usuario_cad', $IdUsuarioCad);
        $Venda->setDadoVenda('dt_cadastro',  date("Y-m-d") );
        $Venda->setDadoVenda('dt_venda', date("Y-m-d") );
        $Venda->setDadoVenda('id_origem',  $a_atend["atend_id_origem"]  );
        $Venda->setDadoVenda('id_tab_preco', $tab_preco );
        $Venda->setDadoVenda('id_atividade_pai', ( $a_atend["numreg"] * 1) );
        $Venda->AtualizaDadosVendaBD();

        query("update is_atividade_solicitacao set acao_id_orcamento_gerado = '" . $Venda->getNumregVenda() . "' where id_atividade = '" . $pnumreg . "' and (acao_id_orcamento_gerado is null) and (acao_sn_gerar_orcamento = 1) and acao_id_usuario_resp='" . $a_solic_distinct["acao_id_usuario_resp"] . "'");
    }
}

echo '<script language="Javascript"> ';
// Deve pegar o prazo maximo das ações e atualizar o prazo o atendimento.
$a_max_prazo_ativ_gerada = farray(query("select dt_prev_fim, hr_prev_fim from is_atividade where id_atividade_pai = '" . $pnumreg . "' order by dt_prev_fim desc, hr_prev_fim desc"));
if ($a_max_prazo_ativ_gerada["dt_prev_fim"]) {
    query("update is_atividade set dt_prev_fim = '" . $a_max_prazo_ativ_gerada["dt_prev_fim"] . "', hr_prev_fim = '" . $a_max_prazo_ativ_gerada["hr_prev_fim"] . "' where numreg = " . $pnumreg);
    echo " window.opener.document.getElementById('edtdt_prev_fim').value = '" . DataGetBD($a_max_prazo_ativ_gerada["dt_prev_fim"]) . "';";
    echo " window.opener.document.getElementById('edthr_prev_fim').value = '" . $a_max_prazo_ativ_gerada["hr_prev_fim"] . "';";
}
echo "window.alert('".$msg_alert."Processamento concluído :\\n " . $n_acoes . " atividade(s) gerada(s) ! \\n " . $n_oport . " oportunidade(s) gerada(s) ! \\n " . $n_orc . " orçamento(s) gerado(s) !'); ";
echo ' window.setTimeout( "' . "window.close()" . '", 100);';
echo '</script>';
?>