<?php

if ((($id_funcao == 'atividades_cad_lista') || ($id_funcao == 'sac_cad_lista') || ($id_funcao == 'visitas_cad_lista') || ($id_funcao == 'visitastec_cad_lista') || ($id_funcao == 'assist_cad_chamados') || ($id_funcao == 'acoes_atec')) && $opc != 'excluir') {
	// Valida sexo pessoa
	$q_sexo_pessoa = farray(query("select id_sexo from is_pessoa where numreg =" . $_POST["edtid_pessoa"] ));
	if($q_sexo_pessoa['id_sexo'] == "" && $q_sexo_pessoa['edtid_tp_pessoa'] == 2){
        $Url->AlteraParam('ppostback', $numreg_postback);
        $url_retorno = $Url->getUrl();
        echo "<script language=\"javascript\">alert('Favor atualizar o sexo da pessoa. Registro não salvo !'); window.location.href = '" . $url_retorno . "';</script>";
        exit;
	}
	
    // Consistências
    $dt_inicio_trat = DataSetBD($_POST["edtdt_inicio"]) . $_POST["edthr_inicio"];
    $dt_prev_fim_trat = DataSetBD($_POST["edtdt_prev_fim"]) . $_POST["edthr_prev_fim"];
    if ($dt_inicio_trat > $dt_prev_fim_trat) {
        $Url->AlteraParam('ppostback', $numreg_postback);
        $url_retorno = $Url->getUrl();
        echo "<script language=\"javascript\">alert('Data/Hora de Início não pode ser maior que Data/Hora de Prazo !'); window.location.href = '" . $url_retorno . "';</script>";
        exit;
    }
    // Atualiza Data de Realização
    if (($_POST["edtid_situacao"] == '4') && (empty($_POST["edtdt_real_fim"]))) {
        $_POST["edtdt_real_fim"] = date("d/m/Y");
        $_POST["edthr_real_fim"] = date("H:i");
    }
}

if ((($id_funcao == 'atividades_cad_lista') || ($id_funcao == 'visitas_cad_lista') || ($id_funcao == 'visitastec_cad_lista')) && $opc != 'excluir') {
    // Checa se já existe outra atividade no mesmo horário e alerta o usuário
    $sn_atividade_consiste_horario = GetParam('atividade_consiste_horario') * 1;
    if ($sn_atividade_consiste_horario > 0) {
        $valor_trat_ativ1 = dtbr2en($_POST["edtdt_inicio"]) . ' ' . $_POST["edthr_inicio"] . ':00';
        $valor_trat_ativ2 = dtbr2en($_POST["edtdt_prev_fim"]) . ' ' . $_POST["edthr_prev_fim"] . ':00';
        $q_existe_ativ = farray(query("select * from is_atividade where dthr_inicio <= '" . $valor_trat_ativ2 . "' and dthr_prev_fim >= '" . $valor_trat_ativ1 . "' and numreg <> '" . $pnumreg . "'"));
        $msg_atividade_consiste_horario = "Atenção já existe outra atividade neste intervalo de horário : " . $q_existe_ativ["assunto"] . " - " . dten2br($q_existe_ativ["dt_inicio"]) . ' ' . $q_existe_ativ["hr_inicio"] . ' a ' . dten2br($q_existe_ativ["dt_prev_fim"]) . ' ' . $q_existe_ativ["hr_prev_fim"] . "!";
        if (($q_existe_ativ["numreg"] * 1) > 0) {
            if ($sn_atividade_consiste_horario == 2) {
                $Url->AlteraParam('ppostback', $numreg_postback);
                $url_retorno = $Url->getUrl();
                echo "<script language=\"javascript\">javascript:alert('".$msg_atividade_consiste_horario."'); window.location.href = '" . $url_retorno . "';</script>";
                exit;
            } else {
                echo "<script language=\"javascript\">javascript:alert('".$msg_atividade_consiste_horario."');</script>";;
            }
        }
    }
}


if (($id_funcao == 'ativ_despesa')) {
    // Verifica se o trejto de ida já não foi contabilizado no trajeto de volta da visita anterior
    $q_ativ_traj = farray(query("select * from is_atividade where numreg = '" . $_POST["edtid_atividade"] . "'"));
    $dt_trat_ativ = $q_ativ_traj["dt_inicio"];
    $q_exite_volta = farray(query("select a.* from is_atividade_despesa d, is_atividade a where a.id_usuario_resp = '" . $q_ativ_traj["id_usuario_resp"] . "' and a.dt_inicio = '" . $dt_trat_ativ . "' and d.id_trajeto_volta = '" . $_POST["edtid_trajeto_ida"] . "' and a.numreg = d.id_atividade"));
    if ($q_exite_volta["numreg"] * 1 > 0) {
        echo "<script>alert('Este trajeto de ida já foi contabilizado como trajeto de volta na visita anterior : " . $q_exite_volta["hr_inicio"] . " a " . $q_exite_volta["hr_prev_fim"] . " - " . $q_exite_volta["assunto"] . " e será removido deste lançamento !');</script>";
        $_POST["edtid_trajeto_ida"] = "";
    }

    // Calcula KM
    $q_ida = farray(query("select * from is_tabela_km where numreg = '" . $_POST["edtid_trajeto_ida"] . "'"));
    $q_volta = farray(query("select * from is_tabela_km where numreg = '" . $_POST["edtid_trajeto_volta"] . "'"));

    $qt_km = ($q_ida["km"] * 1) + ($q_volta["km"] * 1);
    $vl_km = $qt_km * str_replace(',', '.', GetParam('VL_KM'));
    $vl_total = (str_replace(",", ".", $_POST["edtvl_estac"]) * 1) + (str_replace(",", ".", $_POST["edtvl_pedagio"]) * 1) + (str_replace(",", ".", $_POST["edtvl_aliment"]) * 1) + (str_replace(",", ".", $_POST["edtvl_aliment2"]) * 1) + (str_replace(",", ".", $_POST["edtvl_outros"]) * 1) + (str_replace(",", ".", $_POST["edtvl_conducao"]) * 1) + $vl_km;

    $_POST["edtvl_total"] = str_replace(".", ",", $vl_total);
    $_POST["edtqt_km"] = str_replace(".", ",", $qt_km);
    $_POST["edtvl_km"] = str_replace(".", ",", $vl_km);
}

if ($id_funcao == 'visitaprojeto_cad_lista') {
    if ($_POST['edtcod_atendimento'] == '' || $_POST['edtcod_atendimento'] < 1) {
        $max_id = farray(query('SELECT cod_atendimento FROM is_atividade WHERE id_tp_atividade = 23 AND cod_atendimento >0 ORDER BY cod_atendimento desc LIMIT 1'));
        $_POST['edtcod_atendimento'] = $max_id['cod_atendimento'] + 1;
    }
}
?>