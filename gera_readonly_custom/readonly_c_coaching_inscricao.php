<?php
/*
 * readonly_c_coaching_inscricao.php
 * Autor: Alex
 * 18/18/2011 11:35:00
 */
if($id_funcao == 'c_coaching_inscricao'){
    /*
     * Personal Coaching
     */
    if($qry_cadastro['id_curso'] == '1'){
        $ArrayCamposOcultar = array('c2_sn_form_certificao_alpha','c2_calc_frequencia','c2_sn_comprovacao_cientifica','c2_sn_entrega_trab_grupo','c2_sn_pagto_quitado','c2_sn_proposta_comercial');
        if(is_int(array_search($id_campo,$ArrayCamposOcultar))){
            $exibir_formulario = '0';
            $qry_gera_cad_campos['sn_obrigatorio'] = 0;
        }
    }
    /*
     * Executive Coaching
     */
    elseif($qry_cadastro['id_curso'] == '2'){
        $ArrayCamposOcultar = array('c1_calc_frequencia','c1_sn_pagto_quitado','c1_sn_projeto_incompleto', 'sn_area_restrita');
        if(is_int(array_search($id_campo,$ArrayCamposOcultar))){
            $exibir_formulario = '0';
            $qry_gera_cad_campos['sn_obrigatorio'] = 0;
        }
    }
    else{
        $ArrayCamposCertificacao = array('c2_sn_form_certificao_alpha', 'c2_sn_comprovacao_cientifica','c2_sn_entrega_trab_grupo', 'c2_calc_frequencia', 'c2_nota_final', 'c2_sn_entrega_trab_grupo','c2_sn_pagto_quitado', 'c2_sn_proposta_comercial', 'sn_area_restrita');
        if(is_int(array_search($id_campo,$ArrayCamposCertificacao))){
            $exibir_formulario = '0';
            $qry_gera_cad_campos['sn_obrigatorio'] = 0;
        }
    }
}
?>