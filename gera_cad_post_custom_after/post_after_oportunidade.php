<?php

// Itens da Oportunidade
if ($id_funcao == 'opor_itens') {
    $a_total_opor_produto = farray(query("SELECT sum(valor_total) AS total FROM is_opor_produto WHERE id_oportunidade = " . $opor_itens_id_opor));
    query("update is_oportunidade set valor = " . ($a_total_opor_produto["total"] * 1) . " WHERE numreg = " . $opor_itens_id_opor);
}
?>
