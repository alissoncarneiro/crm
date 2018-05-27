<?php
/*
 * post_after_opo_cad_lista.php
 * Autor: Alex
 * 11/12/2012 10:03:34
 */
if($id_funcao == 'opo_cad_lista'){
    if($opc == 'alterar'){
        $ArOportunidade = farray(query("SELECT id_orcamento_pai,id_orcamento_filho FROM is_oportunidade WHERE numreg = ".$pnumreg));
        $IdOrcamento = ($ArOportunidade['id_orcamento_pai'] != '')?$ArOportunidade['id_orcamento_pai']:$ArOportunidade['id_orcamento_filho'];
        if($IdOrcamento != ''){
            $SqlUpdateOrcamento = "UPDATE is_orcamento SET id_fase = '".$_POST['edtid_opor_ciclo_fase']."' WHERE numreg = '".$IdOrcamento."'";
            query($SqlUpdateOrcamento);
        }
    }
}