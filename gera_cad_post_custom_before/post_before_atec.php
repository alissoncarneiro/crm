<?php

if((($id_funcao == 'chamado_atec')||($id_funcao == 'chamado_atec_interno')||($id_funcao == 'chamado_atec_montagem')) && ($opc != 'excluir')){
    // Se tiver SLA deve prevalecer sobre a prioridade e o prazo deverá ser respeitado
    if($_POST["edtacao_id_prioridade_sla"]){
        $_POST["edtid_prioridade"] = $_POST["edtacao_id_prioridade_sla"];
        $cDataAtiv_OS = DataSetBD($_POST["edtdt_inicio"]);
        $cTimeAtiv_OS = $_POST["edthr_inicio"];
        $a_prioridade_OS = farray(query("select qtde_horas_prz from is_prioridade where numreg = '".$_POST["edtid_prioridade"]."'"));
        $cTimeAtiv_OS = SomaMinutosUteis($cTimeAtiv_OS,$cDataAtiv_OS,( $a_prioridade_OS["qtde_horas_prz"] * 60));
        $_POST["edtdt_prev_fim"] = DataGetBD(substr($cTimeAtiv_OS,0,10));
        $_POST["edthr_prev_fim"] = substr($cTimeAtiv_OS,11,5);
    }
}
?>