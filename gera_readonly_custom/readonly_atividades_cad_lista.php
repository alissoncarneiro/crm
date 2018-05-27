<?php

// Regra para não permitir alteração de data/hora de inicio/prazo quando uma atividade for cadastrada por outro usuário (exceto quando usuário tiver permissão)
if ($id_funcao == 'atividades_cad_lista') {
    if (($qry_gera_cad_campos["id_campo"] == 'dt_prev_fim') || ($qry_gera_cad_campos["id_campo"] == 'hr_prev_fim') || ($qry_gera_cad_campos["id_campo"] == 'dt_inicio') || ($qry_gera_cad_campos["id_campo"] == 'hr_inicio')) {
        if ($_SESSION["id_usuario"] <> $qry_cadastro["id_usuario_cad"]) {
            $a_permissao_altera_prazo = farray(query("select sn_permite_alterar_prazo_todos from is_usuario where numreg = " . $_SESSION["id_usuario"]));
            if ($a_permissao_altera_prazo["sn_permite_alterar_prazo_todos"] <> '1') {
                $readonly = 'readonly style="background-color:#CCCCCC" ';
            }
        }
    }
}

// Regra para não permitir alteração de atividades realizadas
if ($qry_gera_cad["nome_tabela"] == 'is_atividade') {
        if ($qry_cadastro["id_situacao"]=='4') {
                $readonly = 'readonly style="background-color:#CCCCCC" ';
        }
}

?>