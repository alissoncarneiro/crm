<?php

if(($id_funcao == 'is_pessoa_sicode') && ($opc != 'excluir')){
    $a_sic_sg = farray(query("select * from is_sicode_segmento where numreg = '".$_POST["edtid_sicode_segmento"]."'"));
    $a_sic_pt = farray(query("select * from is_sicode_porte where numreg = '".$_POST["edtid_sicode_porte"]."'"));
    $a_sic_gr = farray(query("select * from is_sicode_grupo_aplic where numreg = '".$_POST["edtid_sicode_grupo_aplic"]."'"));
    $a_sic_ap = farray(query("select * from is_sicode_aplic where numreg = '".$_POST["edtid_sicode_aplic"]."'"));
    $_POST["edtnr_sicode"] = $a_sic_sg["id_sicode_segmento"];
    if($a_sic_pt["id_sicode_porte"]){
        $_POST["edtnr_sicode"] .= ".".$a_sic_pt["id_sicode_porte"];
        if($a_sic_gr["id_sicode_grupo_aplic"]){
            $_POST["edtnr_sicode"] .= ".".$a_sic_gr["id_sicode_grupo_aplic"];
            if($a_sic_ap["id_sicode_aplic"]){
                $_POST["edtnr_sicode"] .= ".".$a_sic_ap["id_sicode_aplic"];
            }
        }
    }
}
?>
