<?php
/*
 * detalhe_ini_opo_cad_lista.php
 * Autor: Alex
 * 20/04/2011 10:30
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
if($_GET['pfuncao'] == 'opo_cad_lista'){
    if($qry_cadastro['id_situacao'] == 3 || $qry_cadastro['id_situacao'] == 4){/* Se a situação for fechada ou perdida trava todos os mestre detalhes */
        $url_pread = "&pread=1";
        if($_SESSION['id_perfil'] == 12){
            $url_pread = "&pread=0";

        }
    }
    elseif($qry_cadastro['id_orcamento_filho'] != '' || $qry_cadastro['id_orcamento_pai'] != ''){ /* Se foi gerada a partir de um or�amento, ou gerou um or�amento */
        $ArrayTravarSeExistirOrcamentoRelacionado = array('opor_itens','orcamento');
        if(is_int(array_search($qry_gera_cad_sub['id_funcao_detalhe'],$ArrayTravarSeExistirOrcamentoRelacionado))){
            $url_pread = "&pread=1";
        }
        else{
            $url_pread = '';
        }
    }
    if($qry_gera_cad_sub['id_funcao_detalhe'] == 'orcamento'){ /* O cadastro de orçamento sempre será bloqueado */
        $url_pread = "&pread=1";
    }
}
