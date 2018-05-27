<?php

if((($id_funcao == 'chamado_atec') || ($id_funcao == 'chamado_atec_interno') || ($id_funcao == 'chamado_atec_montagem')) && ($opc != 'excluir')){
    // Se tiver SLA deve prevalecer sobre a prioridade e o prazo deverá ser respeitado
    $cons_final = farray(query('select sn_consumidor_final from is_pessoa where numreg = \''.$_POST["edtid_pessoa"].'\''));
    if($cons_final['sn_consumidor_final'] == '1'){
        if($_POST["edtid_revenda"] != ''){
            $revenda = farray(query('select numreg from is_rede_relac where id_pessoa = \''.$_POST["edtid_pessoa"].'\'AND id_pessoa_dest = \''.$_POST["edtid_revenda"].'\''));
            if($revenda['numreg']==''){
                query('INSERT INTO is_rede_relac (id_pessoa, id_relac, id_pessoa_dest) VALUES (\''.$_POST["edtid_pessoa"].'\',\'10\',\''.$_POST["edtid_revenda"].'\')');
            }
        }
    }
}
?>