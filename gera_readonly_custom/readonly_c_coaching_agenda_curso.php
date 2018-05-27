<?php
/*
 * readonly_c_coaching_agenda_curso.php
 * Autor: Alex
 * 11/05/2012 10:44:03
 */
if($id_funcao == 'c_coaching_agenda_curso'){
    if($qry_cadastro['id_situacao'] == '1' || $qry_cadastro['id_situacao'] == '2'){
        $ArrayCamposEditaveis = array('id_situacao','wcp_sn_lotado','id_estabelecimento','id_pessoa_licenciado','id_modulo','id_instrutor','','id_local_curso','id_hotel','qtde_min_inscricao','qtde_max_inscricao','dt_limite_inscricao','wcp_exibe_site');
        if(!is_int(array_search($id_campo,$ArrayCamposEditaveis))){
            $readonly = 'readOnly="readOnly" style="background-color:#CCCCCC" ';
        }
    }
    elseif($qry_cadastro['id_situacao'] != ''){
        $readonly = 'readOnly="readOnly" style="background-color:#CCCCCC" ';
    }
}
?>