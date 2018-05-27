<?php

/*
 * gera_cad_post_custom_before.php
 * Versão 4.0
 * 23/09/2010 17:31:00
 */
include_once('functions.php');
include_once('classes/class.Pessoa.php');
include_once('classes/class.DataHora.php');
include_once('classes/class.GeraCadPost.php');
include_once('classes/class.Usuario.php');
include_once('classes/class.Url.php');
include_once('classes/class.uB.php');
include_once('classes/class.GeraPontuacaoParametro.php');

include_once('classes/class.smtp.php');
include_once('classes/class.phpmailer.php');
include_once('classes/class.Email.php');



if($_POST){
    foreach($_POST as $k => $v){
        if(is_array($v)){
            foreach($v as $k2 => $v2){
                $_POST[$k][$k2] = trim($v2);
            }
        }
        else{
            $_POST[$k] = trim($v);
        }
    }
}
if($opc != 'excluir'){
  
    $geraCadPost = new GeraCadPost();
    
    $numreg_postback = $geraCadPost->backupPost($_POST);
    

    $Url = new Url();
    $Url->setUrl($_POST['url_retorno']);
    $Url->RemoveParam('ppostback');
    $url_retorno = $Url->getUrl();
}

$dir_gera_cad = 'gera_cad_post_custom_before';
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
 * Desativados, descomentar caso necessário
 *
  if($id_funcao == 'pessoas_cad_lista'){
  if($opc == 'incluir'){
  $sql_pessoa = 'SELECT * FROM is_pessoas WHERE cnpj_cpf = \''.$_POST['edtcnpj_cpf'].'\'';
  $qry_pessoa = mysql_query($sql_pessoa);
  $nrows_pessoa = mysql_num_rows($qry_pessoa);
  if($nrows_pessoa != 0){
  echo "<script>alert('Cadastro já existe na base, o mesmo não será inserido.');window.close();</script>";
  }
  }
  }


  // WORKFLOW - ATULIZACAO DE CONTEUDO DE CAMPOS E GERACAO DE MENU
  if($id_funcao == 'workflow'){
  $id_cpo_cad = $_POST["edtid_cad"];
  $_POST["edturl_excluir"] = "javascript:gera_cad_excluir(@sfgera_cad_post.php?pfuncao=".$id_cpo_cad."&pnumreg=@pnumreg&popc=excluir@sf);";
  $_POST["edturl_alterar"] = "gera_cad_detalhe.php?pfuncao=".$id_cpo_cad."&pnumreg=@pnumreg";
  $_POST["sql_filtro"] = "select * from is_atividades where id_formulario_workflow = @s".$id_cpo_cad."@s";

  $sql_atualiza = "insert into is_funcoes(id_modulo,id_funcao,nome_funcao,nome_grupo,url_programa,ordem,url_imagem,id_sistema,dt_cadastro,hr_cadastro,id_usuario_cad,dt_alteracao,hr_alteracao,id_usuario_alt) values ('".$_POST["edtid_modulo"]."','".$id_cpo_cad."','".TextoBD($tipoBanco,nl2br($_POST["edttitulo"]))."','Workflow','<a href= javascript:exibe_programa(@sfgera_cad_lista.php?pfuncao=".$id_cpo_cad."@sf); >','1','images/icone_estrutura.png','".$_SESSION["id_sistema"]."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."')";
  //echo $sql_atualiza;
  query("delete from is_funcoes where id_funcao = '".$id_cpo_cad."'");
  query($sql_atualiza);
  }


  #Tratamento de Ralacionamentos do Cadastro do cliente
  if($id_funcao == 'empresas_cad_lista' || $id_funcao == 'representantes_cad_lista'){
  if(isset($_POST['edtid_relac_outros'])){
  $_POST['edtsn_contato'] = 'N';
  $_POST['edtsn_representante'] = 'N';
  $_POST['edtsn_parceiro'] = 'N';
  $_POST['edtsn_fornecedor'] = 'N';
  $_POST['edtsn_concorrente'] = 'N';
  foreach($_POST['edtid_relac_outros'] as $k => $v){
  if($v == '1'){
  $_POST['edtsn_contato'] = 'S';
  } elseif($v == '2'){
  $_POST['edtsn_representante'] = 'S';
  } elseif($v == '3'){
  $_POST['edtsn_parceiro'] = 'S';
  } elseif($v == '4'){
  $_POST['edtsn_fornecedor'] = 'S';
  } elseif($v == '5'){
  $_POST['edtsn_concorrente'] = 'S';
  }
  }
  if($id_funcao == 'representantes_cad_lista'){
  $_POST['edtsn_representante'] = 'S';
  }
  }
  }

  if($id_funcao == 'empresas_cad_lista' || $id_funcao == 'prospects_cad_lista'){
  if($_SESSION['id_perfil'] == 5){
  echo "<script>alert('VocÃª nÃ£o tem permissÃ£o de transformar um prospect em cliente.\nAs alteraÃ§Ãµes foram salvas.');</script>";
  //echo "<script>document.getElementById('edtid_relac').value = '2';</script>";
  $_POST['edtid_relac'] = '2';
  //  exit;
  }
  }

  if(($id_funcao == 'atividades_cad_lista') || ($id_funcao == 'resp_sac') || ($id_funcao == 'acoes_atividades_cad_lista') || ($id_funcao == 'visitastec_cad_lista') || ($id_funcao == 'visitas_cad_lista')){
  // Calcula Qt de Horas nas Atividades
  $qt_intervalo = str_replace(",",".",$_POST["edttempo_intervalo"]) * 1;
  $qt_horas = ((diferenca_hr($_POST["edthr_inicio"],$_POST["edthr_prev_fim"],'S',1) * 1) - $qt_intervalo) * 1;
  $_POST["edttempo_real"] = number_format($qt_horas,2,',','.');

  // Checa se já existe outra atividade no mesmo horário
  $valor_trat_ativ = substr($_POST["edtdt_inicio"],6,4).'-'.substr($_POST["edtdt_inicio"],3,2).'-'.substr($_POST["edtdt_inicio"],0,2);
  $q_existe_ativ = farray(query("select * from is_atividades where id_usuario_resp = '".$_POST["edtid_usuario_resp"]."' and dt_inicio = '".$valor_trat_ativ."' and ((hr_inicio < '".$_POST["edthr_inicio"]."' and hr_prev_fim > '".$_POST["edthr_inicio"]."') or (hr_inicio < '".$_POST["edthr_prev_fim"]."' and hr_prev_fim > '".$_POST["edthr_prev_fim"]."')) and id_atividade <> '".$_POST["edtid_atividade"]."'"));
  if($q_existe_ativ["id_atividade"]){
  echo "<script>javascript:alert('Atenção já existe outra atividade neste intervalo de horário : ".$q_existe_ativ["assunto"]." - ".$q_existe_ativ["hr_inicio"].' a '.$q_existe_ativ["hr_prev_fim"].". Por favor preencha corretamente.');</script>";
  $_POST["edthr_inicio"] = '07:00';
  $_POST["edthr_prev_fim"] = '07:00';
  }
  }


  if(($id_funcao == 'ativ_despesa')){
  // Verifica se o trejto de ida já não foi contabilizado no trajeto de volta da visita anterior
  $q_ativ_traj = farray(query("select * from is_atividades where id_atividade = '".$_POST["edtid_atividade"]."'"));
  $dt_trat_ativ = $q_ativ_traj["dt_inicio"];
  $q_exite_volta = farray(query("select a.* from is_ativ_despesa d, is_atividades a where a.id_usuario_resp = '".$q_ativ_traj["id_usuario_resp"]."' and a.dt_inicio = '".$dt_trat_ativ."' and d.id_trajeto_volta = '".$_POST["edtid_trajeto_ida"]."' and a.id_atividade = d.id_atividade"));
  if($q_exite_volta["id_atividade"]){
  echo "<script>alert('Este trajeto de ida já foi contabilizado como trajeto de volta na visita anterior : ".$q_exite_volta["hr_inicio"]." a ".$q_exite_volta["hr_prev_fim"]." - ".$q_exite_volta["assunto"]." e será removido deste lançamento !');</script>";
  $_POST["edtid_trajeto_ida"] = "";
  }

  // Calcula KM
  $q_ida = farray(query("select * from is_tabela_km where id_trajeto = '".$_POST["edtid_trajeto_ida"]."'"));
  $q_volta = farray(query("select * from is_tabela_km where id_trajeto = '".$_POST["edtid_trajeto_volta"]."'"));

  $qt_km = ($q_ida["km"] * 1) + ($q_volta["km"] * 1);
  $vl_km = $qt_km * 0.45;
  $vl_total = (str_replace(",",".",$_POST["edtvl_estac"]) * 1) + (str_replace(",",".",$_POST["edtvl_pedagio"]) * 1) + (str_replace(",",".",$_POST["edtvl_aliment"]) * 1) + (str_replace(",",".",$_POST["edtvl_aliment2"]) * 1) + (str_replace(",",".",$_POST["edtvl_outros"]) * 1) + $vl_km;

  $_POST["edtvl_total"] = str_replace(".",",",$vl_total);
  $_POST["edtqt_km"] = str_replace(".",",",$qt_km);
  $_POST["edtvl_km"] = str_replace(".",",",$vl_km);
  }
 *
 */
?>
