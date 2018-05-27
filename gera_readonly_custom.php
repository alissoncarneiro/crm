<?php
//==================================================================================================
// Este programa permite definir se um campo serс exibido e terс ediчуo no formulсrio 
// Vocъ pode utilizar por exemplo o conteњdo das seguinte variсveis :
// - $pnumreg para saber se щ uma inclusуo = -1 ou alteraчуo <> -1
// - $_SESSION["id_usuario"] e $_SESSION["id_perfil"] para customizar direitos de acesso por campo
// - $id_funcao para saber qual щ o cadastro corrente
// - $qry_gera_cad_campos["id_campo"] para saber qual щ o id do campo corrente
// - $qry_cadastro[$qry_gera_cad_campos["id_campo"]] para saber qual щ o conteudo do campo corrente
// - Recomenda-se no arquivo conecta.php setar a variavel $_SESSION["lic_id"] com o id da licenчa do cliente e usс-la neste programa
//  Apѓs checar se o campo deve ou nуo ser editado altere o conteњdo da variсvel $readonly = 'readonly style="background-color:#CCCCCC" ' ou $readonly = '';
//  Apѓs checar se o campo deve ou nуo ser exibido altere o conteњdo da variсvel $exibir_formulario = 'S'; ou $exibir_formulario = 'N';

// Exemplo de Cѓdigo
/* 
 Se for licenca RNL, cadastro de empresas, alteracao de cadastro, perfil operador e campo CPF nуo editar
 if(($_SESSION["lic_id"]=='RNL')&&($id_funcao=='empresas')&&($pnumreg!=-1)&&($_SESSION["id_perfil"]!= 2)&&($qry_gera_cad_campos["id_campo"]=='CPF') {
    $readonly = 'readonly style="background-color:#CCCCCC" ';
 }
*/
//==================================================================================================
$id_campo = $qry_gera_cad_campos["id_campo"];

$dir_gera_cad = 'gera_readonly_custom';
if(is_dir($dir_gera_cad)){
    if($dh = opendir($dir_gera_cad)){
        while(($file = readdir($dh)) !== false){
            if($file != "." && $file != ".." && is_file($dir_gera_cad."/".$file)){
                include($dir_gera_cad."/".$file);
            }
        }
        closedir($dh);
    }
}
$SqlBloqueiosCustom = "SELECT sn_bloqueio_ver,sn_bloqueio_editar FROM is_perfil_funcao_bloqueio_campos WHERE id_cad = '".$id_funcao."' AND id_campo = '".$id_campo."' AND id_perfil = '".$_SESSION['id_perfil']."'";
$QryBloqueiosCustom = query($SqlBloqueiosCustom);
$ArBloqueiosCustom = farray($QryBloqueiosCustom);
if($ArBloqueiosCustom){
    if($ArBloqueiosCustom['sn_bloqueio_ver'] == '1'){
        $exibir_formulario = '0';
        $qry_gera_cad_campos['sn_obrigatorio'] = 0;
    }
    if($ArBloqueiosCustom['sn_bloqueio_editar'] == '1'){
        $readonly = 'readOnly="readOnly" style="background-color:#CCCCCC" ';
    }
}

















/* Desativado, ativar conforme necessсrio
 if(($pnumreg==-1)&&($id_funcao=="atividades_cad_lista")&&($_GET["ativ_tp"]=="RES")) {
	 if ($qry_gera_cad_campos["id_campo"]=='id_tp_atividade') { $vl_campo = "RES"; }
	 if ($qry_gera_cad_campos["id_campo"]=='dt_prev_fim') { $vl_campo = $_GET["ativ_dt"]; }
	 if ($qry_gera_cad_campos["id_campo"]=='hr_inicio') { $vl_campo = substr($_GET["ativ_hr"],0,5); }
	 if ($qry_gera_cad_campos["id_campo"]=='hr_prev_fim') { $vl_campo = substr($_GET["ativ_hr"],0,5); }
	 if ($qry_gera_cad_campos["id_campo"]=='id_produto') { $vl_campo = $_GET["ativ_prod"]; }
	 if ($qry_gera_cad_campos["id_campo"]=='assunto') { $vl_campo = "Reserva de Sala"; }
 }


if(($id_funcao=='empresas_cad_lista' || $id_funcao=='pessoas_cad_lista')){
	if($_SESSION["id_perfil"] == '5' && $pnumreg!='-1'){
		if(	$qry_gera_cad_campos["id_campo"]=='cnpj_cpf' ||
			$qry_gera_cad_campos["id_campo"]=='id_representante'||
			$qry_gera_cad_campos["id_campo"]=='id_usuario_gc'||
			$qry_gera_cad_campos["id_campo"]=='dts_tab_precos'||
			$qry_gera_cad_campos["id_campo"]=='cnpj_cpf'){
			$readonly = 'readonly style="background-color:#CCCCCC" ';
			//$exibir_formulario = 'N';
		}
		if($_SESSION["id_perfil"] != '19' && ($qry_gera_cad_campos["id_campo"]=='ativo' || $qry_gera_cad_campos["id_campo"]=='id_tp_mot_inat_cli')){
			$exibir_formulario = 'N';
		}
	}
	if($_SESSION["id_perfil"] == '5' && $qry_gera_cad_campos["id_campo"]=='nome_abreviado'){
		#$readonly = 'readonly style="background-color:#CCCCCC" ';
		$exibir_formulario = 'N';
	}
}
if(($id_funcao=='empresas_cad_lista' || $id_funcao=='pessoas_cad_lista')){
	if($_SESSION["id_perfil"] != 'admin' && $_SESSION["id_perfil"] != '1' && $_SESSION["id_perfil"] != '10' && $qry_gera_cad_campos["id_campo"]=='id_relac'){
		$readonly = 'readonly style="background-color:#CCCCCC" ';
	}
}

*/
?>