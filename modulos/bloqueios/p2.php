<?php
require("../../conecta.php");
session_start();


query("DELETE FROM is_perfil_funcao_bloqueio WHERE id_perfil = '".$_POST['edtid_perfil']."'");
$qry_funcoes = query("SELECT * FROM is_funcoes t1 LEFT JOIN is_modulos t2 ON t1.id_modulo = t2.id_modulo WHERE t2.id_sistema LIKE '%CRM%'  AND t1.id_sistema LIKE '%CRM%' AND t1.nome_grupo <> 'Estrutura' ORDER BY t1.id_modulo,t1.nome_grupo,t1.ordem ASC");
while($ar_funcao = farray($qry_funcoes)){
    $post_ver = $_POST['ver_'.$ar_funcao['id_funcao']];
    $post_editar = $_POST['editar_'.$ar_funcao['id_funcao']];
    if(isset($post_ver) && isset($post_editar)){
        $qry_funcao_bloqueio = query("SELECT * FROM is_perfil_funcao_bloqueio WHERE id_perfil = '".$_POST['edtid_perfil']."' AND id_funcao = '".$ar_funcao['id_funcao']."'");

        $num_rows_qry_funcao_bloqueio = numrows($qry_funcao_bloqueio);
        if($num_rows_qry_funcao_bloqueio > 0){
            $ar_funcao_bloqueio = farray($qry_funcao_bloqueio);
        }
        $abrir = ($post_ver == 'true')?'0':'1';
        $editar = ($post_editar == 'true')?'0':'1';
        if($post_ver == 'false' || $post_editar == 'false'){
            if($num_rows_qry_funcao_bloqueio > 0){
                $sql = "UPDATE is_perfil_funcao_bloqueio SET sn_bloqueio_abrir = '$abrir' , sn_bloqueio_editar = '$editar' WHERE id_perfil = '".$_POST['edtid_perfil']."' AND id_funcao = '".$ar_funcao['id_funcao']."'";
                query($sql);
            } else{
                $sql = "INSERT INTO is_perfil_funcao_bloqueio(dt_cadastro,hr_cadastro,id_usuario_cad,dt_alteracao,hr_alteracao,id_usuario_alt,
				id_perfil,id_modulo,id_funcao,bloqueios,sn_bloqueio_abrir,sn_bloqueio_editar)
				VALUES ('".date("Y-m-d")."','".date("H:i")."','".$_SESSION['id_usuario']."','".date("Y-m-d")."','".date("H:i")."','".$_SESSION['id_usuario']."',
				'".$_POST['edtid_perfil']."','".$ar_funcao['id_modulo']."','".$ar_funcao['id_funcao']."','"."a"."','".$abrir."','".$editar."')";
                query($sql);
            }
        } else{
            if($num_rows_qry_funcao_bloqueio > 0){
                $sql = "DELETE FROM is_perfil_funcao_bloqueio WHERE id_perfil = '".$_POST['edtid_perfil']."' AND id_funcao = '".$ar_funcao['id_funcao']."'";
                query($sql);
            }
        }
    }
}
echo "Bloqueios do Perfil alterados com sucesso!";
?>