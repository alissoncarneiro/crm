<?php

/*

 * detalhe_sub_atividade_cad_lista.php

 * Autor: Eduardo

 * ?

 *

 * Log de Alterações

 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>

 */

// Regra para não permitir alteração de atividades realizadas

if($qry_gera_cad["nome_tabela"] == 'is_atividade') {

    if($qry_cadastro["id_situacao"]=='4'){

        $url_pread = "&pread=1";

    }

}

?>