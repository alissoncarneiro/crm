<?php

if (($id_funcao == 'campos_cad_lista_custom') && $opc != 'excluir') {
    // Colocar sempre prefixo "comp_" no id do campo
    if (substr($_POST["edtid_campo"], 0, 4) != 'wcp_') {
        $_POST["edtid_campo"] = 'wcp_' . $_POST["edtid_campo"];
    }
    // Trocar texto seu_id_campo no valor default do SQL de combobox/lupa/etc
    $_POST["edtsql_lupa"] = str_replace('seu_id_campo', $_POST["edtid_campo"], $_POST["edtsql_lupa"]);

}
?>
