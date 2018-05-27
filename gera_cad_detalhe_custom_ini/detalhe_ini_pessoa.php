<?php
if($_GET['pfuncao'] == 'pessoa'){
    /* Tratamento para quando for transformar um prospect em cliente e suspect em prospect */
    if($_GET['ptpec'] == '1'){
        /* Tratamento se o usuário não tem permissão */
        if(!$Usuario->getPermissao('sn_trans_prospect_cliente')){
            echo alert(getError('0010020019',getParametrosGerais('RetornoErro')));
            echo historyBack(1);
            exit;
        }
    }
    if($_GET['ptsep'] == '1'){
        if(!$Usuario->getPermissao('sn_trans_suspect_prospect')){
            echo alert(getError('0010020022',getParametrosGerais('RetornoErro')));
            echo historyBack(1);
            exit;
        }
    }
}
?>