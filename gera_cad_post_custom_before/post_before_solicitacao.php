<?php

if(($id_funcao == 'is_atividade_solicitacao_com') || ($id_funcao == 'is_atividade_solicitacao_pos') || ($id_funcao == 'is_atividade_solicitacao_atec')){
    // Consitência de Valores Obrigatórios Configurados nos Tipos de Solicitação
    $msg_obrig_solic = "";
    $msg_obrig_solic_2o_nivel = "";
    $a_obr_solic = farray(query("select * from is_tp_motivo_atend where numreg = '".$_POST['edtid_tp_motivo_atend']."'"));
    if(($a_obr_solic["sn_obrig_produto"] == '1') && ((empty($_POST['edtid_produto']) && (empty($_POST['produto_nao_cadastrado']))))){
        $msg_obrig_solic .= "Produto, ";
    }
    if(($a_obr_solic["sn_obrig_qtde"] == '1') && (empty($_POST['edtqtde']))){
        $msg_obrig_solic .= "Qtde, ";
    }
    if(($a_obr_solic["sn_obrig_id_nf_erp"] == '1') && (empty($_POST['edtid_nf_erp']))){
        $msg_obrig_solic .= "NF, ";
    }
    if(($a_obr_solic["sn_obrig_id_pedido_erp"] == '1') && (empty($_POST['edtid_pedido_erp']))){
        $msg_obrig_solic .= "Pedido, ";
    }
    if(($a_obr_solic["sn_obrig_id_pedido_ou_nf_erp"] == '1') && ((empty($_POST['edtid_pedido_erp'])) && (empty($_POST['edtid_nf_erp'])))){
        $msg_obrig_solic .= "Pedido ou NF, ";
    }
    if(($a_obr_solic["sn_obrig_id_titulo"] == '1') && (empty($_POST['edtid_titulo_erp']))){
        $msg_obrig_solic .= "Título, ";
    }
    if(($a_obr_solic["sn_obrig_num_serie"] == '1') && (empty($_POST['edtid_produto']))){
        $msg_obrig_solic .= "N.Série, ";
    }
    if(($a_obr_solic["sn_obrig_obs"] == '1') && (empty($_POST['edtobs']))){
        $msg_obrig_solic .= "Descrição da Solicitação, ";
    }
    if(($a_obr_solic["sn_usa_dt_desejada"] == '1') && (empty($_POST['edtacao_dt_desejada']))){
        $msg_obrig_solic .= "Data Desejada, ";
    }
    // Consitência de Valores Obrigatórios no Caso de Gerar Ação de 2o Nivel
    if(($_POST["edtacao_id_tp_atividade"]) && (empty($_POST['edtacao_id_usuario_resp']))){
        $msg_obrig_solic_2o_nivel .= "Responsável, ";
    }
    if(($_POST["edtacao_id_tp_atividade"]) && (empty($_POST['edtacao_assunto']))){
        $msg_obrig_solic_2o_nivel .= "Assunto, ";
    }
    if(($_POST["edtacao_id_tp_atividade"]) && (empty($_POST['edtacao_id_prioridade']))){
        $msg_obrig_solic_2o_nivel .= "Prioridade, ";
    }
    if((($_POST["edtacao_sn_gerar_oportunidade"] == '1') || ($_POST["edtacao_sn_gerar_orcamento"] == '1')) && (empty($_POST['edtacao_id_tab_preco']))){
        // Se for vazio tenta recuperar do cliente
        if (empty($_POST['edtacao_id_tab_preco'])) {
            $a_tab_preco_cliente = farray(query("select id_tab_preco_padrao from is_pessoa where numreg = '".$_POST['edtid_pessoa']."'"));
            if ($a_tab_preco_cliente["id_tab_preco_padrao"]) {
                $_POST['edtacao_id_tab_preco'] = $a_tab_preco_cliente["id_tab_preco_padrao"];
            } else {
                $msg_obrig_solic_2o_nivel .= "Tabela de Preço, ";
            }
        }
    }
    if($msg_obrig_solic){
        $msg_obrig_solic = "Por favor preencher o(s) campo(s) ".substr($msg_obrig_solic,0,strlen($msg_obrig_solic) - 2)." !\\n";
    }
    if($msg_obrig_solic_2o_nivel){
        $msg_obrig_solic .= "Para gerar Ação de 2o Nível, por favor preencher o(s) campo(s) ".substr($msg_obrig_solic_2o_nivel,0,strlen($msg_obrig_solic_2o_nivel) - 2)." !";
    }
    if($msg_obrig_solic){
        $Url->AlteraParam('ppostback',$numreg_postback);
        $url_retorno = $Url->getUrl();
        echo "<script language=\"javascript\">alert('".$msg_obrig_solic."'); window.location.href = '".$url_retorno."';</script>";
        exit;
    }

    // Recuperando Fornecedor, Linha e familia
    $a_prod_oport = farray(query("select id_fornecedor, id_linha, id_familia_comercial from is_produto WHERE numreg = ".$_POST['edtid_produto']));
    $_POST['edtid_fornecedor'] = $a_prod_oport['id_fornecedor'];
    $_POST['edtid_linha'] = $a_prod_oport['id_linha'];
    $_POST['edtid_familia_comercial'] = $a_prod_oport['id_familia_comercial'];
}
?>