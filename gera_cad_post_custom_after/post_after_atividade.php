<?php

// ==================================================================
// Calcula Qt de Horas nas Atividades
if ((($id_funcao == 'atividades_cad_lista') || ($id_funcao == 'visitas_cad_lista') || ($id_funcao == 'visitastec_cad_lista') || ($id_funcao == 'acoes_atec') || ($id_funcao == 'visitaprojeto_cad_lista')) && $opc != 'excluir') {

    // Se inicia e termina no mesmo dia o sistema gera o apontamento
    if ($_POST["edtdt_inicio"] == $_POST["edtdt_prev_fim"]) {
        query("delete from is_atividade_apontamento where id_atividade = '" . $pnumreg . "'");
        query(str_replace("''","NULL",
                "insert into is_atividade_apontamento(
                        id_tp_atividade,
                        id_pessoa,
                        id_atividade,
                        id_usuario_resp,
                        dt_inicio,
                        hr_inicio,
                        hr_prev_fim,
                        tempo_intervalo,
                        tempo_real,
                        obs,
                        id_oportunidade_pai,
                        id_atividade_pai,
                        id_projeto_pai,
                        id_atividade_filha
                ) values (
                '" . $_POST["edtid_tp_atividade"] . "',
                '" . $_POST["edtid_pessoa"] . "',
                '" . $pnumreg . "',
                '" . $_POST["edtid_usuario_resp"] . "',
                '" . dtbr2en($_POST["edtdt_inicio"]) . "',
                '" . $_POST["edthr_inicio"] . "',
                '" . $_POST["edthr_prev_fim"] . "',
                '" . str_replace(",",".",$_POST["edttempo_intervalo"]) . "',
                '0',
                '" . $_POST["edtobs"] . "',
                '" . $_POST["edtid_oportunidade_pai"] . "',
                '" . $_POST["edtid_atividade_pai"] . "',
                '" . $_POST["edtid_projeto_pai"] . "',
                '" . $_POST["edtid_atividade_filha"] . "'
                )"));
    }
    // Atualizando Campos Datetime
    query("update is_atividade set dthr_inicio = '" . ( dtbr2en($_POST["edtdt_inicio"]).' '.$_POST["edthr_inicio"].':00') . "', dthr_prev_fim = '" . ( dtbr2en($_POST["edtdt_prev_fim"]).' '.$_POST["edthr_prev_fim"].':00')."' where numreg = '" . $pnumreg . "'");
}
if ((($id_funcao == 'is_atividade_apontamento') || ($id_funcao == 'atividades_cad_lista') || ($id_funcao == 'visitas_cad_lista') || ($id_funcao == 'visitastec_cad_lista') || ($id_funcao == 'acoes_atec') || ($id_funcao == 'visitaprojeto_cad_lista')) && $opc != 'excluir') {
    // Atualizando Tempo Real nos Apontamentos
    if ($id_funcao == 'is_atividade_apontamento') {
        $campo_busca_apontamento = $_POST["edtid_atividade"];
        $campo_busca_projeto = $_POST["edtid_projeto_pai"];
    } else {
        $campo_busca_apontamento = $pnumreg;
        $campo_busca_projeto = $_POST["edtid_projeto_pai"];
    }
    $q_apontamento_calculo = query("select * from is_atividade_apontamento where id_atividade = '" . $campo_busca_apontamento . "'");
    while ($a_apontamento_calculo = farray($q_apontamento_calculo)) {
        $qt_intervalo = str_replace(",", ".", $a_apontamento_calculo["tempo_intervalo"]) * 1;
        $qt_horas = ((diferenca_hr($a_apontamento_calculo["hr_inicio"], $a_apontamento_calculo["hr_prev_fim"], 'S', 1) * 1) - $qt_intervalo) * 1;
        query("update is_atividade_apontamento set tempo_real = '" . $qt_horas . "' where numreg = '" . $a_apontamento_calculo["numreg"] . "'");
    }
    $a_apontamento_calculo_tot = farray(query("select sum(tempo_real) as total from is_atividade_apontamento where id_atividade = '" . $campo_busca_apontamento . "'"));
    query("update is_atividade set tempo_real = '" . ($a_apontamento_calculo_tot["total"] * 1) . "' where numreg = '" . $campo_busca_apontamento . "'");

    // Se esta vinculado a um projeto deve atualizar o tempo real tambйm
    if ($campo_busca_projeto) {
        $a_apontamento_calculo_tot_proj = farray(query("select sum(tempo_real) as total_real, sum(tempo_prev) as total_prev from is_atividade where id_projeto_pai = '" . $campo_busca_projeto . "'"));
        query("update is_projeto set tempo_real = '" . ($a_apontamento_calculo_tot_proj["total_real"] * 1) . "', tempo_prev = '" . ($a_apontamento_calculo_tot_proj["total_prev"] * 1) . "' where numreg = '" . $campo_busca_projeto . "'");
    }
}
// ==================================================================


// WORKFLOW - AUTOMACAO DE ATIVIDADES (Telemarketing, Televendas, Telecobranзa, Visita Comercial e Visita Tйcnica) : Resultado x Prуxima Aзгo
if ((($id_funcao == 'televendas_cad') || ($id_funcao == 'telemarketing_cad') || ($id_funcao == 'cobrancas_cad') || ($id_funcao == 'visitas_cad_lista') || ($id_funcao == 'visitastec_cad_lista')) && ($opc != 'excluir')) {
    if ($_POST['edtid_resultado']) {
        $a_wf_resultado = farray(query("select * from is_workflow_resultado_atividade where numreg = " . $_POST['edtid_resultado']));
        $a_acao_resultado = farray(query("select * from is_workflow_resultado_atividade_acao where numreg = " . $a_wf_resultado["id_acao"]));

        // Definindo se a data serб a definida pelo usuбrio ou calculada em dias ъteis
        if ($a_acao_resultado["sn_data_definida_usu"] == '1') {
            $wf_res_data = DataSetBD($_POST["edtdt_retornar"]);
        } else {
            $wf_res_data = soma_dias_ut(Date("Y-m-d"), $a_wf_resultado["n_dias"] * 1, '1');
        }
        $wf_res_hora = $_POST["edthr_prev_fim"];
        if (empty($wf_res_hora)) {
            $wf_res_hora = '08:00';
        }

        // Checando se deve copiar os dados (assunto e obs ) para a nova atividade
        if ($a_acao_resultado["sn_copia_dados"] == '1') {
            $wf_res_assunto = $_POST["edtassunto"];
            $wf_res_obs = $_POST["edtobs"];
        } else {
            $wf_res_assunto = $a_wf_resultado["assunto"];
        }

        // Checando se deve usar responsavel da atividade ou o que foi definido no parametro
        if ($a_wf_resultado["sn_responsavel"] == '1') {
            $wf_res_id_usuario = $_POST["edtid_usuario_resp"];
        } else {
            $wf_res_id_usuario = $a_wf_resultado["id_usuario"];
        }
        if (empty($wf_res_id_usuario)) {
            $wf_res_id_usuario = $_SESSION["id_usuario"];
        }

        // Checando se deve usar tipo da atividade ou a que foi definida no parametro
        if ($a_wf_resultado["id_tp_atividade_duplicar"]) {
            $wf_res_id_tp_atividade = $a_wf_resultado["id_tp_atividade_duplicar"];
        } else {
            $wf_res_id_tp_atividade = $_POST["edtid_tp_atividade"];
        }

        // Criando nova atividade
        if($a_acao_resultado["sn_cria_nova"] == '1'){
            $a_wf_existe_ativ = farray(query("select numreg from is_atividade where id_atividade_pai = ".$pnumreg));
            if(($a_wf_existe_ativ["numreg"] * 1) == 0){
                $ArInsertAtividade = array(
                    'id_tp_atividade'           => $wf_res_id_tp_atividade,
                    'assunto'                   => $wf_res_assunto,
                    'id_pessoa'                 => $_POST['edtid_pessoa'],
                    'id_pessoa_contato'         => $_POST['edtid_pessoa_contato'],
                    'id_usuario_resp'           => $wf_res_id_usuario,
                    'dt_inicio'                 => $wf_res_data,
                    'hr_inicio'                 => $wf_res_hora,
                    'dt_prev_fim'               => $wf_res_data,
                    'hr_prev_fim'               => $wf_res_hora,
                    'id_situacao'               => '1',
                    'id_atividade_pai'          => $pnumreg,
                    'obs'                       => $wf_res_obs,
                    'id_usuario_cad'            => $_SESSION["id_usuario"],
                    'dt_cadastro'               => date("Y-m-d"),
                    'hr_cadastro'               => date("H:i"),
                    'id_campanha'               => $_POST['edtid_campamha'],
                    'id_campanha_prog_ativ'     => $_POST['edtid_campanha_prog_ativ']
                );
                $wf_res_sql = AutoExecuteSql(TipoBancoDados, 'is_atividade', $ArInsertAtividade, 'INSERT');
                query($wf_res_sql);
            }
        }

        // Realizando atividade atual
        if ($a_acao_resultado["sn_realiza_atividade"] == '1') {
            query("UPDATE is_atividade SET id_situacao = 4 where numreg = " . $pnumreg);
        }
    }
}
?>