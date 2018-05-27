<?php
/*
 * calc_custom_pessoa.php
 * Autor: Alex
 * 11/09/2012 14:49:29
 */
if($id_funcao == 'pessoa'){
    if($id_campo == 'calc_pesq_cep'){
        $ret = '<img src="images/btn_busca.PNG" id="btn_pesq_cep" alt="Pesquisar CEP" title="Pesquisar CEP"/>';
    }
    elseif($id_campo == 'calc_pesq_cep_cob'){
        $ret = '<img src="images/btn_busca.PNG" id="btn_pesq_cep_cob" TP="_cob" alt="Pesquisar CEP" title="Pesquisar CEP"/>';
    }
}
?>