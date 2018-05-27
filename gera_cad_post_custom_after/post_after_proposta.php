<?php

// Itens da Oportunidade
if ($id_funcao == 'is_propostas_prod') {
    $a_total_opor_produto = farray(query("SELECT sum(valor_total) AS total FROM is_proposta_prod WHERE id_proposta = " . $proposta_itens_id));
    query("update is_proposta set valor = " . ($a_total_opor_produto["total"] * 1) . " WHERE numreg = " . $proposta_itens_id);
}

?>
