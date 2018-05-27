<?php
session_start();
require("../../conecta.php");
require("../../functions.php");
$qry_cad = query("SELECT * FROM is_gera_cad ORDER BY titulo ASC");
while($ar_cad = farray($qry_cad)){
    $post_ver = $_POST['ver_'.$ar_cad['id_cad']];
    $post_editar = $_POST['editar_'.$ar_cad['id_cad']];
    $post_incluir = $_POST['incluir_'.$ar_cad['id_cad']];
    $post_excluir = $_POST['excluir_'.$ar_cad['id_cad']];
    if(isset($post_ver) && isset($post_editar) && isset($post_incluir) && isset($post_excluir)){
        $qry_cad_bloqueio = query("SELECT * FROM is_perfil_funcao_bloqueio_cad WHERE id_perfil = '".$_POST['edtid_perfil']."' AND id_cad = '".$ar_cad['id_cad']."'");
        $num_rows_qry_cad_bloqueio = numrows($qry_cad_bloqueio);
        if($num_rows_qry_cad_bloqueio > 0){
            $ar_cad_bloqueio = farray($qry_cad_bloqueio);
        }
        $abrir = ($post_ver == 'true')?'0':'1';
        $editar = ($post_editar == 'true')?'0':'1';
        $incluir = ($post_incluir == 'true')?'0':'1';
        $excluir = ($post_excluir == 'true')?'0':'1';
        if($post_ver == 'false' || $post_editar == 'false' || $post_incluir == 'false' || $post_excluir == 'false'){
            if($num_rows_qry_cad_bloqueio > 0){
                $sql = "UPDATE is_perfil_funcao_bloqueio_cad SET sn_bloqueio_ver = '$abrir' , sn_bloqueio_editar = '$editar', sn_bloqueio_incluir = '$incluir', sn_bloqueio_excluir = '$excluir' WHERE id_perfil = '".$_POST['edtid_perfil']."' AND id_cad = '".$ar_cad['id_cad']."'";
                query($sql);
            }
            else{
                $ar_sql_insert = array();
                $ar_sql_insert['dt_cadastro'] = date("Y-m-d");
                $ar_sql_insert['hr_cadastro'] = date("H:i");
                $ar_sql_insert['id_usuario_cad'] = $_SESSION['id_usuario'];
                $ar_sql_insert['dt_alteracao'] = date("Y-m-d");
                $ar_sql_insert['hr_alteracao'] = date("H:i");
                $ar_sql_insert['id_usuario_alt'] = $_SESSION['id_usuario'];
                $ar_sql_insert['id_cad'] = $ar_cad['id_cad'];
                $ar_sql_insert['id_perfil'] = $_POST['edtid_perfil'];
                $ar_sql_insert['sn_bloqueio_ver'] = $abrir;
                $ar_sql_insert['sn_bloqueio_editar'] = $editar;
                $ar_sql_insert['sn_bloqueio_incluir'] = $incluir;
                $ar_sql_insert['sn_bloqueio_excluir'] = $excluir;
                $sql = autoExecuteSql(TipoBancoDados,'is_perfil_funcao_bloqueio_cad',$ar_sql_insert,'INSERT');
                query($sql);
            }
        }
        else{
            if($num_rows_qry_cad_bloqueio > 0){
                $sql = "DELETE FROM is_perfil_funcao_bloqueio_cad WHERE id_perfil = '".$_POST['edtid_perfil']."' AND id_cad = '".$ar_cad['id_cad']."'";
                query($sql);
            }
        }
    }
}
echo "Bloqueios do Perfil alterados com sucesso!";
?>