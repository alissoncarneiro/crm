<?php
/*
 * readonly_opo_cad_lista.php
 * Autor: Alex
 * 20/04/2011 09:30
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
if($id_funcao == 'opo_cad_lista'){
    $ArrayNaoEditaveisSeForGeradoApartirDeOrcamento = array('id_pessoa','assunto','id_origem','id_pessoa_indic','id_usuario_resp','id_usuario_gestor','id_representante_principal','id_pessoa_contato','id_tab_preco','id_cond_pagto','dt_inicio','dt_prev_fim','dt_real_fim','valor','obs');
    /* Tratanto os campos que não podem ser editados quando a oportunidade for gerada a partir de um orçamento */
    if($qry_cadastro['id_orcamento_pai'] != '' || $qry_cadastro['id_orcamento_filho'] != ''){
        if(is_int(array_search($id_campo,$ArrayNaoEditaveisSeForGeradoApartirDeOrcamento))){
            $readonly = 'readOnly="readOnly" style="background-color:#CCCCCC" ';
            $qry_gera_cad_campos['sn_obrigatorio'] = 0;
        }
    }
}
?>