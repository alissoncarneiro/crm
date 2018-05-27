<?php
session_start();
require("../../conecta.php");
require("../../functions.php");
$ArrayFuncoesDesativadas = array('pessoa');
$qry_campos = query("SELECT * FROM is_gera_cad_campos WHERE id_funcao = '".$_POST['edtid_cad']."' ORDER BY nome_grupo ASC, ordem ASC");
while($ar_campo = farray($qry_campos)){
    if(array_search($ar_campo['id_funcao'], $ArrayFuncoesDesativadas) !== false){
        continue;
    }
    $post_ver = $_POST['ver_'.$ar_campo['id_campo']];
    $post_editar = $_POST['editar_'.$ar_campo['id_campo']];
    $post_valor_padrao = $_POST['valor_padrao_'.$ar_campo['id_campo']];
    if(isset($post_ver) && isset($post_editar)){
        $qry_funcao_bloqueio = query("SELECT * FROM is_perfil_funcao_bloqueio_campos WHERE id_cad = '".$ar_campo['id_funcao']."' AND id_campo = '".$ar_campo['id_campo']."' AND id_perfil = '".$_POST['edtid_perfil']."'");

        $num_rows_qry_funcao_bloqueio = numrows($qry_funcao_bloqueio);
        if($num_rows_qry_funcao_bloqueio > 0){
            $ar_campo_bloqueio = farray($qry_funcao_bloqueio);
        }
        $abrir = ($post_ver == 'true')?'0':'1';
        $editar = ($post_editar == 'true')?'0':'1';
        $valor_padrao = trim($post_valor_padrao);
            if($num_rows_qry_funcao_bloqueio > 0){
                $sql = "UPDATE is_perfil_funcao_bloqueio_campos SET sn_bloqueio_ver = '$abrir' , sn_bloqueio_editar = '$editar', valor_padrao = '$valor_padrao' WHERE id_cad = '".$ar_campo['id_funcao']."' AND id_campo = '".$ar_campo['id_campo']."' AND id_perfil = '".$_POST['edtid_perfil']."'";
                query($sql);
            } else{
                $ar_sql_insert = array();
                $ar_sql_insert['dt_cadastro'] = date("Y-m-d");
                $ar_sql_insert['hr_cadastro'] = date("H:i");
                $ar_sql_insert['id_usuario_cad'] = $_SESSION['id_usuario'];
                $ar_sql_insert['dt_alteracao'] = date("Y-m-d");
                $ar_sql_insert['hr_alteracao'] = date("H:i");
                $ar_sql_insert['id_usuario_alt'] = $_SESSION['id_usuario'];
                $ar_sql_insert['id_cad'] = $ar_campo['id_funcao'];
                $ar_sql_insert['id_campo'] = $ar_campo['id_campo'];
                $ar_sql_insert['id_perfil'] = $_POST['edtid_perfil'];
                $ar_sql_insert['sn_bloqueio_ver'] = $abrir;
                $ar_sql_insert['sn_bloqueio_editar'] = $editar;
                $ar_sql_insert['valor_padrao'] = $post_valor_padrao;
                $sql = AutoExecuteSql(TipoBancoDados,'is_perfil_funcao_bloqueio_campos',$ar_sql_insert,'INSERT');
                query($sql);
            }
    }
}
echo "Bloqueios do Perfil alterados com sucesso!";
?>