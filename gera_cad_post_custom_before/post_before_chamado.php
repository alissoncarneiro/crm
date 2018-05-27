<?php

if(($id_funcao == "chamado_atec_montagem" || $id_funcao == "chamado_atec" || $id_funcao == "chamado_atec_interno")){
    $result = farray(query('select * from is_posicao_atec where numreg = \''.$_POST['edtid_posicao_atec'].'\''));
    $_POST['edtid_situacao'] = $result['sit_correspondente'];
}
?>