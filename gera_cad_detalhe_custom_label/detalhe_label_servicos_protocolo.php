<?php

// Calcula e atualiza último protocolo do SAC, CRC, Help Desk e O.S para exibir no momento da inclusão.

if ((($id_funcao == 'chamado_atec') || ($id_funcao == 'chamado_atec_interno') || ($id_funcao == 'chamado_atec_montagem') || ($id_funcao == 'assist_cad_chamados') || ($id_funcao == 'chamado_atec_lab')|| ($id_funcao == 'is_ativ_crc') || ($id_funcao == 'assist_cad_chamados') || ($id_funcao == 'is_help_desk')) && ($qry_gera_cad_campos["id_campo"] == 'id_atividade') && ($pnumreg == '-1') && ($aba_ativa)) {
    
    if (($id_funcao == 'chamado_atec') || ($id_funcao == 'chamado_atec_interno') || ($id_funcao == 'chamado_atec_montagem')) {
        $tipo_protocolo = 'OS';
    }
    if ($id_funcao == 'is_help_desk') {
        $tipo_protocolo = 'HELP_DESK';
    }
    
    if (($id_funcao == 'assist_cad_chamados') || ($id_funcao == 'is_ativ_crc')) {
        $tipo_protocolo = 'SAC';
    }
    if ($id_funcao == 'chamado_atec_lab') {
        $tipo_protocolo = 'LABORATORIO';
    }
    
    $a_param_ultimo_protocolo = farray(query("select parametro from is_parametros_sistema where id_parametro = 'ULTIMO_PROTOCOLO_" . $tipo_protocolo . "'"));
    $ultimo_protocolo = ($a_param_ultimo_protocolo["parametro"]+1);
    query("update is_parametros_sistema set parametro = '" . $ultimo_protocolo . "' where id_parametro = 'ULTIMO_PROTOCOLO_" . $tipo_protocolo . "'");
    $vl_campo = $ultimo_protocolo;
  }
?>
