<?php

/*

 * detalhe_sub_atividade_cad_lista.php

 * Autor: Eduardo

 * ?

 *

 * Log de Altera��es

 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>

 */

// Regra para n�o permitir altera��o de atividades realizadas

if($qry_gera_cad["nome_tabela"] == 'is_atividade') {

    if($qry_cadastro["id_situacao"]=='4'){

        $url_pread = "&pread=1";

    }

}

?>