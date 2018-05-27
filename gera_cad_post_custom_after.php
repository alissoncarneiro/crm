<?php
/*
 * gera_cad_post_custom_afters.php
 * Versão 4.0
 * 29/09/2010 16:16:00
 */
$dir_gera_cad = 'gera_cad_post_custom_after';
if(is_dir($dir_gera_cad)){
    if($dh = opendir($dir_gera_cad)){
        while(($file = readdir($dh)) !== false){
            if($file != "." && $file != ".." && is_file($dir_gera_cad."/".$file)){
                include_once($dir_gera_cad."/".$file);
            }
        }
        closedir($dh);
    }
}

/*
 * Tratamento para quando for cadastrado um registro a partir de um campo lupa_popup
 */
if($_POST['plupa'] != ''){

    $custom_qry_gera_cad_campo = query("SELECT id_campo,id_funcao_lupa,id_campo_lupa,campo_descr_lupa FROM is_gera_cad_campos WHERE numreg = ".$_POST['plupa']);
    if(numrows($custom_qry_gera_cad_campo) == 1){
        $custom_ar_gera_cad_campo = farray($custom_qry_gera_cad_campo);
        $custom_qry_gera_cad = query("SELECT nome_tabela FROM is_gera_cad WHERE id_cad = '".$_POST['pfuncao']."'");
        $custom_ar_gera_cad = farray($custom_qry_gera_cad);

        $custom_qry_gera_cad_lupa = query("SELECT nome_tabela FROM is_gera_cad WHERE id_cad = '".$custom_ar_gera_cad_campo['id_funcao_lupa']."'");
        $custom_ar_gera_cad_lupa = farray($custom_qry_gera_cad_lupa);

        $custom_qry_gera_cad_dados = query("SELECT ".$custom_ar_gera_cad_campo['id_campo_lupa']." FROM ".$custom_ar_gera_cad['nome_tabela']." WHERE numreg = ".$pnumreg);

        $custom_ar_gera_cad_dados = farray($custom_qry_gera_cad_dados);

        $custom_qry_gera_cad_dados_lupa = query("SELECT ".$custom_ar_gera_cad_campo['id_campo_lupa'].",".$custom_ar_gera_cad_campo['campo_descr_lupa']." FROM ".$custom_ar_gera_cad['nome_tabela']." WHERE ".$custom_ar_gera_cad_campo['id_campo_lupa']." = '".$custom_ar_gera_cad_dados[$custom_ar_gera_cad_campo['id_campo_lupa']]."'");

        $custom_ar_gera_cad_dados_lupa = farray($custom_qry_gera_cad_dados_lupa);


        echo "<script>
                if(window.opener.document.getElementById('edt".$custom_ar_gera_cad_campo['id_campo']."')){
                    window.opener.document.getElementById('edt".$custom_ar_gera_cad_campo['id_campo']."').value = '".$custom_ar_gera_cad_dados_lupa[$custom_ar_gera_cad_campo['id_campo_lupa']]."';
                    window.opener.document.getElementById('edtdescr".$custom_ar_gera_cad_campo['id_campo']."').value = '".$custom_ar_gera_cad_dados_lupa[$custom_ar_gera_cad_campo['campo_descr_lupa']]."';
                }
            </script>";
    }
}