<?php

// CAMPANHA
if($id_funcao == 'campanhas_cad' && $opc != 'excluir'){
    // Alimenta a tabela de origens com a campanha
    $a_existe_origem = farray(query("select id_campanha from is_origem_conta where id_campanha = '" . $pnumreg . "'"));
    if ($a_existe_origem["id_campanha"]) {
        query("update is_origem_conta set nome_origem_conta = '".($_POST["edtnome_campanha"])."' where id_campanha = '" . $pnumreg . "'");
    } else {
        query("insert into is_origem_conta(nome_origem_conta,id_campanha) values ('".($_POST["edtnome_campanha"])."','" . $pnumreg . "')");
    }
}

?>
