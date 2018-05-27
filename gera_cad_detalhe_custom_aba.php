<?php
// pode-se usar as variáveis $id_funcao, $a_abas["nome_aba"], $_SESSION
// mudar variavel $exibe_aba = "N" quando desejar inibir uma aba;
$exibe_aba  = "S";
if($id_funcao == 'pessoa'){
    if($_GET['ptpec'] != 1 && ($qry_cadastro['sn_suspect'] == 1 || $qry_cadastro['sn_prospect'] == 1 || $_GET['pgetcustom'] == 'suspect' || $_GET['pgetcustom'] == 'prospect')){
        if($a_abas['nome_aba'] == '04.Informações Financeiras'){
            $exibe_aba = "N";
        }
    }
}
?>