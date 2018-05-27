<?php
session_start();
require("../../conecta.php");
require("../../functions.php");
$qry_mestre_det = query("SELECT t1.numreg,t1.nome_sub,t2.titulo as titulo_mestre,t3.titulo as titulo_detalhe 
								FROM is_gera_cad_sub t1 
								INNER JOIN is_gera_cad t2 ON t1.id_funcao_mestre = t2.id_cad
								INNER JOIN is_gera_cad t3 ON t1.id_funcao_detalhe = t3.id_cad
								ORDER BY t1.nome_sub");
while($ar_mestre_det = farray($qry_mestre_det)){
    $post_ver = $_POST['ver_'.$ar_mestre_det['numreg']];
    $post_editar = $_POST['editar_'.$ar_mestre_det['numreg']];
    if(isset($post_ver) && isset($post_editar)){
        $qry_funcao_bloqueio = query("SELECT * FROM is_perfil_funcao_bloqueio_mestre_det WHERE id_perfil = '".$_POST['edtid_perfil']."' AND numreg_sub = '".$ar_mestre_det['numreg']."'");

        $num_rows_qry_funcao_bloqueio = numrows($qry_funcao_bloqueio);
        if($num_rows_qry_funcao_bloqueio > 0){
            $ar_mestre_det_bloqueio = farray($qry_funcao_bloqueio);
        }
        $abrir = ($post_ver == 'true')?'0':'1';
        $editar = ($post_editar == 'true')?'0':'1';
        if($post_ver == 'false' || $post_editar == 'false'){
            if($num_rows_qry_funcao_bloqueio > 0){
                $sql = "UPDATE is_perfil_funcao_bloqueio_mestre_det SET sn_bloqueio_ver = '$abrir' , sn_bloqueio_editar = '$editar' WHERE id_perfil = '".$_POST['edtid_perfil']."' AND numreg_sub = '".$ar_mestre_det['numreg']."'";
                query($sql);
            } else{
                $ar_sql_insert = array();
                $ar_sql_insert['dt_cadastro'] = date("Y-m-d");
                $ar_sql_insert['hr_cadastro'] = date("H:i");
                $ar_sql_insert['id_usuario_cad'] = $_SESSION['id_usuario'];
                $ar_sql_insert['dt_alteracao'] = date("Y-m-d");
                $ar_sql_insert['hr_alteracao'] = date("H:i");
                $ar_sql_insert['id_usuario_alt'] = $_SESSION['id_usuario'];
                $ar_sql_insert['numreg_sub'] = $ar_mestre_det['numreg'];
                $ar_sql_insert['id_perfil'] = $_POST['edtid_perfil'];
                $ar_sql_insert['sn_bloqueio_ver'] = $abrir;
                $ar_sql_insert['sn_bloqueio_editar'] = $editar;
                $sql = AutoExecuteSql(TipoBancoDados,'is_perfil_funcao_bloqueio_mestre_det',$ar_sql_insert,'INSERT');
                query($sql);
            }
        } else{
            if($num_rows_qry_funcao_bloqueio > 0){
                $sql = "DELETE FROM is_perfil_funcao_bloqueio_mestre_det WHERE id_perfil = '".$_POST['edtid_perfil']."' AND numreg_sub = '".$ar_mestre_det['numreg']."'";
                query($sql);
            }
        }
    }
}
echo "Bloqueios do Perfil alterados com sucesso!";
?>