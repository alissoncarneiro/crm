<?php
function MUDA_FASE( $wf_fase, $wf_usuario, $prazo_horas = 0, $prazo_data = "" )
{
    global $pnumreg;
    global $tipoBanco;
    global $usuario_logado;
    global $qry_atividades;
    global $id_funcao;
    global $pfecha;
    global $dt_hoje;
    $qry_ativ_pendentes = farray( query( "select count(*) as total from is_atividade where id_atividade_pai = '".$qry_atividades['numreg']."' and wf_sn_obrigatorio='1' and ((id_situacao <> '4') or (id_situacao is null))" ) );
    $num_ativ_pendentes = $qry_ativ_pendentes['total'];
    if ( 0 < $num_ativ_pendentes )
    {
        echo "<script language=\"javascript\">alert("."'Existe(m) ".$num_ativ_pendentes." atividade(s) pendente(s) para realizar antes de mudar de fase !');</script>";
        return false;
    }
    $sql_doctos_pendentes = query( "select * from is_workflow_doctos where id_workflow = '".$id_funcao."' and id_fase_workflow = '".$qry_atividades['id_fase_workflow']."' and id_categoria not in (select id_arquivo_categ from is_arquivo where id_atividade = '".$qry_atividades['numreg']."')" );
    $categorias = "";
    while ( $qry_doctos_pendentes = farray( $sql_doctos_pendentes ) )
    {
        $qry_categorias = farray( query( "select * from is_arquivo_categ where numreg = '".$qry_doctos_pendentes['id_categoria']."'" ) );
        $categorias .= $qry_categorias['nome_arquivo_categ'];
    }
    if ( $categorias )
    {
        echo "<script language=\"javascript\">alert("."'Existe(m) documento(s) pendente(s) para incluir antes de mudar de fase ! ".$categorias.".');</script>";
        return false;
    }
    $qry_fases_workflow = farray( query( "select * from is_workflow_fase where id_workflow = '".$id_funcao."' and id_fase='".$wf_fase."'" ) );
    if ( $qry_fases_workflow['tipo_prazo'] == "1" )
    {
        $cDataAtiv = $qry_atividades['dt_prev_fim'];
        $cTimeAtiv = $qry_atividades['hr_prev_fim'];
    }
    else
    {
        $cDataAtiv = Date( "Y-m-d" );
        $cTimeAtiv = Date( "H:i" );
    }
    if ( $prazo_horas )
    {
        $pr_horas = $prazo_horas;
    }
    else
    {
        $pr_horas = $qry_fases_workflow['prazo_horas'];
    }
    $cTime = SomaMinutosUteis( $cTimeAtiv, $cDataAtiv, $pr_horas * 60 );
    $DtPrazo = substr( $cTime, 0, 10 );
    $HrPrazo = substr( $cTime, 11, 5 );
    if ( $prazo_data )
    {
        $DtPrazo = substr( $prazo_data, 6, 4 )."-".substr( $prazo_data, 3, 2 )."-".substr( $prazo_data, 0, 2 );
        $HrPrazo = "18:00";
    }
    query( "UPDATE is_atividade SET  id_fase_workflow = '".$wf_fase."', id_usuario_resp = '".$wf_usuario."', dt_prev_fim = '{$DtPrazo}', hr_prev_fim = '{$HrPrazo}' where numreg = '".$pnumreg."'" );
    if ( $tipoBanco == "mysql" )
    {
        $dt_hoje = date( "Y-m-d" );
    }
    else
    {
        $dt_hoje = date( "Ymd" );
    }
    if ( $tipoBanco == "mysql" )
    {
        $conteudo_log_wf .= "'".date( "Y-m-d" )."','".date( "H:i:s" )."','".$id_usuario."','".date( "Y-m-d" )."','".date( "H:i:s" )."','".$id_usuario."'";
    }
    else
    {
        $conteudo_log_wf .= "'".date( "Ymd" )."','".date( "H:i:s" )."','".$id_usuario."','".date( "Ymd" )."','".date( "H:i:s" )."','".$id_usuario."'";
        $DtPrazo = substr( $DtPrazo, 8, 2 )."/".substr( $DtPrazo, 5, 2 )."/".substr( $DtPrazo, 0, 4 );
    }
    query( "insert into is_workflow_log(id_workflow,id_atividade,id_fase_workflow, dt_inicio, hr_inicio,dt_prazo,hr_prazo,id_usuario_resp,motivo) values ('".$id_funcao."','".$qry_atividades['numreg']."','".$wf_fase."','".$dt_hoje."','".date( "H:i" )."','".$DtPrazo."','".$HrPrazo."','".$wf_usuario."','".$_POST['edtwfcomentario']."')" );
    query( "UPDATE is_workflow_log SET dt_fim = '".$dt_hoje."', hr_fim = '".date( "H:i" )."' where id_fase_workflow = '".$qry_atividades['id_fase_workflow']."' and id_atividade = '".$qry_atividades['numreg']."'" );
    if ( $qry_fases_workflow['sn_email_novo_workflow'] == "1" )
    {
        $qry_email = farray( query( "select email from is_usuario where numreg = '".$wf_usuario."'" ) );
        $emailswf = $qry_email['email'];
        if ( $qry_fases_workflow['email_novo_workflow_copia'] )
        {
            $emailswf .= ";".$qry_fases_workflow['email_novo_workflow_copia'];
        }
        NOVO_WORKFLOW_EMAIL( $emailswf, $pnumreg, $wf_fase );
    }
    $sql_ativ_auto = query( "select * from is_workflow_atividades where id_workflow = '".$id_funcao."' and id_fase_workflow = '".$wf_fase."'" );
    $RESPONSAVEL_DA_ATIVIDADE = $usuario_logado;
    while ( $qry_ativ_auto = farray( $sql_ativ_auto ) )
    {
        if ( $qry_fases_workflow['tipo_prazo'] == "1" )
        {
            $cDataAtiv = $qry_atividades['dt_prev_fim'];
            $cTimeAtiv = $qry_atividades['hr_prev_fim'];
        }
        else
        {
            $cDataAtiv = Date( "Y-m-d" );
            $cTimeAtiv = Date( "H:i" );
        }
        $cTimeAtiv = SomaMinutosUteis( $cTimeAtiv, $cDataAtiv, $qry_ativ_auto['prazo_horas'] * 60 );
        $DtPrazoAtiv = substr( $cTimeAtiv, 0, 10 );
        $HrPrazoAtiv = substr( $cTimeAtiv, 11, 5 );
        $RESPONSAVEL_DA_FASE = $wf_usuario;
        eval( $qry_ativ_auto['regras_negocio'] );
        $sql_in_ativ = "insert into is_atividade(id_situacao,id_atividade,id_atividade_pai,id_tp_atividade,id_pessoa,id_pessoa_contato,assunto,dt_inicio,hr_inicio,dt_prev_fim,hr_prev_fim,id_usuario_resp,wf_sn_obrigatorio) values (1,'WF".$qry_fases_workflow['id_fase'].$qry_atividades['id_atividade'].$qry_ativ_auto['numreg']."','".$qry_atividades['numreg']."','".$qry_ativ_auto['id_tp_atividade']."','".$qry_atividades['id_pessoa']."','".$qry_atividades['id_pessoa_contato']."','".$qry_ativ_auto['assunto']." (Workflow:".$pnumreg." - Fase:".TextoBD( $tipoBanco, $qry_fases_workflow['nome_fase'] ).")"."','".$dt_hoje."','".date( "H:i" )."','".$DtPrazoAtiv."','".$HrPrazoAtiv."','".$RESPONSAVEL_DA_ATIVIDADE."','".$qry_ativ_auto['sn_obrigatorio_wf']."')";
        query( $sql_in_ativ );
    }
    echo "<script language=\"javascript\">alert("."'Fase encerrada com sucesso !');</script>";
    if ( $wf_usuario != "ESCOLHE_MANUAL" )
    {
        $pfecha = "1";
    }
    return true;
}

function RECUSAR( )
{
    global $pnumreg;
    global $tipoBanco;
    global $usuario_logado;
    global $qry_atividades;
    global $id_funcao;
    global $pfecha;
    $sql_log = query( "select * from is_workflow_log where id_atividade = '".$qry_atividades['numreg']."' order by numreg desc" );
    $qry_log_atual = farray( $sql_log );
    $qry_log_ultimo = farray( $sql_log );
    if ( empty( $qry_log_ultimo['id_fase_workflow'] ) )
    {
        $qry_log_ultimo = $qry_log_atual;
    }
    $DtPrazo = $qry_log_ultimo['dt_prazo'];
    if ( $tipoBanco == "mysql" )
    {
        $dt_hoje = date( "Y-m-d" );
    }
    else
    {
        $dt_hoje = date( "Ymd" );
    }
    query( "UPDATE is_workflow_log SET dt_fim = '".$dt_hoje."', hr_fim = '".date( "H:i" )."', sn_recusado = 1, motivo = '".TextoBD( $tipoBanco, $_POST['edtwfcomentario'] )."' where numreg = '".$qry_log_atual['numreg']."'" );
    query( "insert into is_workflow_log(id_workflow,id_atividade,id_fase_workflow, dt_inicio, hr_inicio,dt_prazo,hr_prazo,id_usuario_resp) values ('".$id_funcao."','".$qry_atividades['numreg']."','".$qry_log_ultimo['id_fase_workflow']."','".$dt_hoje."','".date( "H:i" )."','".$DtPrazo."','".$qry_log_ultimo['hr_prazo']."','".$qry_log_ultimo['id_usuario_resp']."')" );
    query( "UPDATE is_atividade SET  id_fase_workflow = '".$qry_log_ultimo['id_fase_workflow']."', id_usuario_resp = '".$qry_log_ultimo['id_usuario_resp']."', dt_prev_fim = '{$DtPrazo}', hr_prev_fim = '".$qry_log_ultimo['hr_prazo']."' where numreg = '".$qry_atividades['numreg']."'" );
    $pfecha = "1";
    return true;
}

function FINALIZA_WORKFLOW( )
{
    global $pnumreg;
    global $tipoBanco;
    global $usuario_logado;
    global $qry_atividades;
    global $id_funcao;
    global $pfecha;
    $qry_ativ_pendentes = farray( query( "select count(*) as total from is_atividade where id_atividade_pai = '".$qry_atividades['numreg']."' and wf_sn_obrigatorio=1 and ((id_situacao <> '4') or (id_situacao is null))" ) );
    $num_ativ_pendentes = $qry_ativ_pendentes['total'];
    if ( 0 < $num_ativ_pendentes )
    {
        echo "<script language=\"javascript\">alert("."'Existe(m) ".$num_ativ_pendentes." atividade(s) pendente(s) para realizar antes de mudar de fase !');</script>";
        return false;
    }
    $sql_doctos_pendentes = query( "select * from is_workflow_doctos where id_workflow = '".$id_funcao."' and id_fase_workflow = '".$qry_atividades['id_fase_workflow']."' and id_categoria not in (select id_arquivo_categ from is_arquivo where id_atividade = '".$qry_atividades['numreg']."')" );
    $categorias = "";
    while ( $qry_doctos_pendentes = farray( $sql_doctos_pendentes ) )
    {
        $qry_categorias = farray( query( "select * from is_arquivo_categ where numreg = '".$qry_doctos_pendentes['id_categoria']."'" ) );
        $categorias .= $qry_categorias['nome_arquivo_categ'];
    }
    if ( $categorias )
    {
        echo "<script language=\"javascript\">alert("."'Existe(m) documento(s) pendente(s) para incluir antes de mudar de fase ! ".$categorias.".');</script>";
        return false;
    }
    if ( $tipoBanco == "mysql" )
    {
        $dt_hoje = date( "Y-m-d" );
    }
    else
    {
        $dt_hoje = date( "Ymd" );
    }
    query( "UPDATE is_workflow_log SET dt_fim = '".$dt_hoje."', hr_fim = '".date( "H:i" )."' where id_fase_workflow = '".$qry_atividades['id_fase_workflow']."' and id_atividade = '".$qry_atividades['numreg']."'" );
    query( "UPDATE is_atividade SET  id_situacao = '4', dt_real_fim = '{$dt_hoje}', hr_real_fim = '".date( "H:i" )."' where numreg = '".$pnumreg."'" );
    echo "<script language=\"javascript\">alert("."'Workflow finalizado !');</script>";
    $pfecha = "1";
    return true;
}

function REGISTRA_LOG( )
{
    global $pnumreg;
    global $opc;
    global $id_funcao;
    global $qry_gera_cad;
    global $filtro_licenca;
    global $campos_bloqueados;
    global $qry_gera_cad_campos_log;
    global $qry_cadastro_compara;
    $filtro_geral = $qry_gera_cad['sql_filtro'];
    $filtro_geral = str_replace( "@vs_id_usuario", $vs_id_usuario, $filtro_geral );
    $filtro_geral = str_replace( "@vs_id_perfil", $vs_id_perfil, $filtro_geral );
    $filtro_geral = str_replace( "@vs_id_empresa", $vs_id_empresa, $filtro_geral );
    $filtro_geral = str_replace( "@vs_dt_hoje", date( "Y-m-d" ), $filtro_geral );
    $filtro_geral = str_replace( "@igual", "=", $filtro_geral );
    $filtro_geral = str_replace( "@dif", "<>", $filtro_geral );
    $filtro_geral = str_replace( "@in", " in ", $filtro_geral );
    $filtro_geral = str_replace( "@maior", ">", $filtro_geral );
    $filtro_geral = str_replace( "@menor", "<", $filtro_geral );
    $filtro_geral = str_replace( "@sf", "'", $filtro_geral );
    $filtro_geral = str_replace( "@s", "'", $filtro_geral );
    $filtro_geral = str_replace( "@and", " and ", $filtro_geral );
    $filtro_geral = str_replace( "@or", " or ", $filtro_geral );
    $filtro_geral = str_replace( "@between", " between ", $filtro_geral );
    $filtro_geral = str_replace( "@pctlike", "%", $filtro_geral );
    $filtro_geral = str_replace( "@like", " like ", $filtro_geral );
    if ( strpos( $filtro_geral, "where" ) === false )
    {
        $clausula = "where";
    }
    else
    {
        $clausula = "and";
    }
    $qry_cadastro_compara = farray( query( $filtro_geral." {$clausula} numreg = '{$pnumreg}'" ) );
    $texto_log = "";
    $sql_gera_cad_campos_log = query( "(select * from is_gera_cad_campos where id_funcao = '{$id_funcao}' and tipo_campo <> 'calculado'  {$filtro_licenca} {$campos_bloqueados} and id_campo <> 'numreg' and exibe_formulario = 1) union all (select * from is_gera_cad_campos_custom where id_funcao = '{$id_funcao}' and tipo_campo <> 'calculado'  {$filtro_licenca} {$campos_bloqueados} and id_campo <> 'numreg' and exibe_formulario = 1) order by ordem" );
    while ( $qry_gera_cad_campos_log = farray( $sql_gera_cad_campos_log ) )
    {
        $vl_anterior_log = TRATAMENTO_CAMPOS_LOG( $qry_cadastro_compara[$qry_gera_cad_campos_log['id_campo']], "BD" );
        $vl_novo_log = TRATAMENTO_CAMPOS_LOG( $_POST["edt".$qry_gera_cad_campos_log['id_campo']], "TELA" );
        if ( $opc == "incluir" )
        {
            $texto_log .= $qry_gera_cad_campos_log['nome_campo']." : ".$vl_novo_log."<br />";
        }
        if ( !( $opc == "alterar" ) && !( $vl_anterior_log != $vl_novo_log ) )
        {
            $texto_log .= $qry_gera_cad_campos_log['nome_campo']." : ".$vl_anterior_log." => ".$vl_novo_log."<br />";
        }
    }
    if ( $opc == "excluir" )
    {
        $texto_log = "excluido";
    }
    if ( $opc == "incluir" || $opc == "excluir" || $opc == "alterar" && $texto_log )
    {
        $data = date( "Y-m-d" );
        $hora = date( "H:i" );
        $id_usuario=$_SESSION['id_usuario'];
        query( "insert into is_log(id_cad,numreg_cadastro,dt_log,hr_log,id_usuario,operacao,texto_log) 
values (\"$id_funcao\",\"$pnumreg\",\"$data\",\"$hora\",\"$id_usuario\",\"$opc\",\"$texto_log\")" );
    }
}

function TRATAMENTO_CAMPOS_LOG( $vl_conteudo_cadastro, $origem_log_tela_bd )
{
    global $qry_gera_cad_campos_log;
    global $qry_cadastro_compara;
    if ( $qry_gera_cad_campos_log['tipo_campo'] == "lupa" || $qry_gera_cad_campos_log['tipo_campo'] == "combobox" || $qry_gera_cad_campos_log['tipo_campo'] == "lupa_popup" )
    {
        if ( strpos( $qry_gera_cad_campos_log['sql_lupa'], "where" ) === false )
        {
            $lista_clausula = "where";
        }
        else
        {
            $lista_clausula = "and";
        }
        $lista_filtro_lupa = $qry_gera_cad_campos_log['sql_lupa']." ".$lista_clausula." ".$pref_bd_ini.$qry_gera_cad_campos_log['id_campo_lupa'].$pref_bd_fim." = '".$vl_conteudo_cadastro."'";
        $lista_filtro_lupa = str_replace( "@s", "'", $lista_filtro_lupa );
        $lista_filtro_lupa = str_replace( "@vs_cpo_id_funcao", $qry_cadastro_compara['id_funcao'], $lista_filtro_lupa );
        $lista_lupa_wf = $qry_cadastro_compara['id_workflow'];
        $lista_lupa_wf = $qry_cadastro_compara['id_formulario_workflow'];
        $lista_filtro_lupa = str_replace( "@vs_cpo_id_workflow", $lista_lupa_wf, $lista_filtro_lupa );
        $lista_filtro_lupa = str_replace( "@vs_id_sistema", $_SESSION['id_sistema'], $lista_filtro_lupa );
        $lista_qry_lupa = farray( query( $lista_filtro_lupa ) );
        $lista_vl_campo_trat = str_replace( "\"", " ", $lista_qry_lupa[$qry_gera_cad_campos_log['campo_descr_lupa']] );
    }
    else if ( $qry_gera_cad_campos_log['tipo_campo'] == "date" )
    {
        if ( $origem_log_tela_bd == "TELA" )
        {
            $lista_vl_campo_trat = $vl_conteudo_cadastro;
        }
        else
        {
            $lista_vl_campo = str_replace( "\"", " ", $vl_conteudo_cadastro );
            if ( $lista_vl_campo )
            {
                $lista_vl_campo_trat = substr( $lista_vl_campo, 8, 2 )."/".substr( $lista_vl_campo, 5, 2 )."/".substr( $lista_vl_campo, 0, 4 );
            }
            else
            {
                $lista_vl_campo_trat = "";
            }
            if ( $lista_vl_campo_trat == "01/01/1753" )
            {
                $lista_vl_campo_trat = "";
            }
        }
    }
    else if ( $qry_gera_cad_campos_log['tipo_campo'] == "int" )
    {
        $lista_vl_campo = str_replace( "\"", " ", $vl_conteudo_cadastro );
        $lista_vl_campo_trat = number_format( $lista_vl_campo, 0, ",", "." );
    }
    else if ( $qry_gera_cad_campos_log['tipo_campo'] == "float" || $qry_gera_cad_campos_log['tipo_campo'] == "real" )
    {
        if ( $origem_log_tela_bd == "TELA" )
        {
            $lista_vl_campo_trat = $vl_conteudo_cadastro;
        }
        else
        {
            $lista_vl_campo = str_replace( "\"", " ", $vl_conteudo_cadastro );
            $lista_vl_campo_trat = number_format( $lista_vl_campo, 2, ",", "." );
        }
        if ( $lista_vl_campo_trat == "0.00" )
        {
            $lista_vl_campo_trat = "0,00";
        }
    }
    else if ( $qry_gera_cad_campos_log['tipo_campo'] == "money" )
    {
        if ( $origem_log_tela_bd == "TELA" )
        {
            $lista_vl_campo_trat = $vl_conteudo_cadastro;
        }
        else
        {
            $lista_vl_campo = str_replace( "\"", " ", $vl_conteudo_cadastro );
            $lista_vl_campo_trat = number_format( $lista_vl_campo, 2, ",", "." );
        }
        if ( $lista_vl_campo_trat == "0.00" )
        {
            $lista_vl_campo_trat = "0,00";
        }
    }
    else if ( $qry_gera_cad_campos_log['tipo_campo'] == "calculado" )
    {
        $lista_vl_campo_trat = campo_calculado( $qry_gera_cad_campos_log['id_funcao'], $qry_gera_cad_campos_log['id_campo'], $qry_cadastro_compara );
    }
    else if ( $qry_gera_cad_campos_log['tipo_campo'] == "memo" )
    {
        $lista_vl_campo_trat = $vl_conteudo_cadastro;
    }
    else if ( $qry_gera_cad_campos_log['tipo_campo'] == "sim_nao" )
    {
        $lista_vl_campo = $vl_conteudo_cadastro;
        if ( $lista_vl_campo == "1" )
        {
            $lista_vl_campo_trat = "S";
        }
        if ( $lista_vl_campo == "0" )
        {
            $lista_vl_campo_trat = "N";
        }
    }
    else
    {
        $lista_vl_campo_trat = $vl_conteudo_cadastro;
    }
    return $lista_vl_campo_trat;
}

@session_start( );
header('Content-Type: text/html; charset=utf-8');
@header( "Pragma: no-cache" );
@header( "Cache-Control: no-store, no-cache, must-revalidate" );
@header( "Cache-Control: post-check=0, pre-check=0", false );
require_once( "conecta.php" );
require_once( "funcoes.php" );
require_once( "classes/class.smtp.php" );
require_once( "modulos/workflow/email_novo_workflow.php" );

$id_usuario = $_SESSION['id_usuario'];
$usuario_logado = $_SESSION['id_usuario'];
$nome_usuario = $_SESSION['nome_usuario'];
$id_perfil = $_SESSION['id_perfil'];
$pread = $_POST['pread'];
$id_funcao = $_POST['pfuncao'];
$pnumreg = $_POST['pnumreg'];
$pnumreg2 = $_POST['pnumreg2'];
$pfixo = $_POST['pfixo'];
$psubdet = $_POST['psubdet'];
$pnpai = $_POST['pnpai'];
$pidlupa = $_POST['pidlupa'];
$opc = $_POST['popc'];
$pchave = $_POST['pchave'];
$pchave_original = $_POST['pchave'];
$pchave2 = $_POST['pchave2'];
$pchave2_original = $_POST['pchave2'];
$prefpai = $_POST['prefpai'];
$pdiv = $_POST['pdiv'];
$pfecha = $_POST['pfecha'];
$psnincluirnovo = $_POST['snincluirnovo'];
$pusuario_filtro = $_POST['pusuario_filtro'];
$cbxfiltro = $_POST['pcbxfiltro'];
$edtfiltro = $_POST['pedtfiltro'];
$pos_ini = $_POST['ppos_ini'];
$cbxordem = $_POST['pcbxordem'];




if ( empty( $pdiv ) )
{
    $pdiv = "div_programa";
}
if ( empty( $pchave ) )
{
    $pchave = $_GET['pchave'];
    $pchave2 = $_GET['pchave2'];
    $pnumreg2 = $_GET['pnumreg2'];
    if ( empty( $pchave ) )
    {
        $pchave = "numreg";
    }
    else
    {
        $pchave = str_replace( "%20", " ", $pchave );
        if ( $pchave2 )
        {
            $pchave2 = str_replace( "%20", " ", $pchave2 );
        }
    }
}
else
{
    $pchave = str_replace( "%20", " ", $pchave );
    if ( $pchave2 )
    {
        $pchave2 = str_replace( "%20", " ", $pchave2 );
    }
}
if ( empty( $prefpai ) )
{
    $prefpai = "1";
}
if ( empty( $id_funcao ) )
{
    $id_funcao = $_GET['pfuncao'];
    $pnumreg = $_GET['pnumreg'];
    $opc = $_GET['popc'];
}




$url_retorno = $_POST['url_retorno'];
$qry_gera_cad = farray( query( "select * from is_gera_cad where id_cad = '{$id_funcao}'" ) );



$filtro_licenca = " and (id_licenca is null or id_licenca = '' or id_licenca like '%PADRAO%' or id_licenca like '%".$_SESSION['lic_id']."%')";
$campos_bloqueados = "";
$q_bloqueio_campos = query( "select * from is_perfil_funcao_bloqueio_campos where id_perfil = '".$_SESSION['id_perfil']."' and id_cad = '".$id_funcao."' and sn_bloqueio_ver = 1" );



while ( $a_bloqueio_campos = farray( $q_bloqueio_campos ) )
{
    $campos_bloqueados = $campos_bloqueados."'".$a_bloqueio_campos['id_campo']."',";
}
if ( $campos_bloqueados )
{
    $campos_bloqueados = "and ( not id_campo in (".substr( $campos_bloqueados, 0, strlen( $campos_bloqueados ) - 1 )."))";
}
if ( $opc == "excluir" )
{
    $operacao = "E";
    require_once( "gera_cad_post_custom_before.php" );
    if ( $qry_gera_cad['nome_tabela'] == "is_atividade" )
    {
        $qry_atividades = farray( query( "select * from is_atividade WHERE {$pchave} = '{$pnumreg}'" ) );
        if ( $qry_atividades['wf_sn_obrigatorio'] == "1" )
        {
            echo "Acesso negado ! Esta atividade é obrigatória para o Workflow : ".$qry_atividades['id_atividade_pai']." !";
            exit( );
        }
    }
    if ( $pchave2 )
    {
        $sqlexec = "DELETE FROM ".$pref_bd.$qry_gera_cad['nome_tabela'].$pref_bd." WHERE ".$pref_bd.$pchave.$pref_bd." = '{$pnumreg}' and ".$pref_bd.$pchave2.$pref_bd." = '{$pnumreg2}'";
    }
    else
    {
        $sqlexec = "DELETE FROM ".$pref_bd.$qry_gera_cad['nome_tabela'].$pref_bd." WHERE ".$pref_bd.$pchave.$pref_bd." = '{$pnumreg}'";
    }
    $rq = query( $sqlexec );
    if ( $rq == "1" )
    {
        echo "Excluído com sucesso !";
        REGISTRA_LOG( );
    }
    else
    {
        echo "Erro na exclusão !";
    }
    require_once( "gera_cad_post_custom_after.php" );
    exit( );
}
if ( $opc == "alterar" )
{
    if ( $pnumreg == "-1" )
    {
        
      

        $operacao = "I";
        $opc = "incluir";
        require_once( "gera_cad_post_custom_before.php" );
        
        
        $sql_insert = "INSERT INTO ".$qry_gera_cad['nome_tabela']." ( ";
        $sql_gera_cad_campos = query( "(select * from is_gera_cad_campos where id_funcao = '{$id_funcao}' and tipo_campo <> 'calculado'  {$filtro_licenca} {$campos_bloqueados} and id_campo <> 'numreg') union all (select * from is_gera_cad_campos_custom where id_funcao = '{$id_funcao}' and tipo_campo <> 'calculado'  {$filtro_licenca} {$campos_bloqueados} and id_campo <> 'numreg') order by ordem" );
        $campos = "";
        $conteudo = "";
        while ( $qry_gera_cad_campos = farray( $sql_gera_cad_campos ) )
        {
            $campos .= $pref_bd.$qry_gera_cad_campos['id_campo'].$pref_bd.",";
            $id_campo = str_replace( " ", "_", $qry_gera_cad_campos['id_campo'] );
            $valor = $_POST["edt".$id_campo];
            if ( $qry_gera_cad_campos['tipo_campo'] == "arquivo" )
            {
                $temp = $_FILES["edt".$id_campo]['tmp_name'];

                $name = $_FILES["edt".$id_campo]['name'];
                $size = $_FILES["edt".$id_campo]['size'];
                $type = $_FILES["edt".$id_campo]['type'];
                $qrymax = farray( query( "select max(".$pchave.") as maxid from ".$qry_gera_cad['nome_tabela'] ) );
                $stmax = preg_replace( "([^0-9])", "", $qrymax['maxid'] );
                if ( empty( $stmax ) )
                {
                    $stmax = 0;
                }
                $stmax = $stmax + 1;
                $conteudo .= "'".$stmax.$name."',";
                copy( $temp, "/".$caminho_arquivos.$stmax.$name );

            }
            else if ( $qry_gera_cad_campos['tipo_campo'] == "date" || $qry_gera_cad_campos['tipo_campo'] == "datetime" )
            {
                $valor = trim( $_POST["edt".$id_campo] );
                if ( $valor )
                {
                    $valor_trat = substr( $valor, 6, 4 )."-".substr( $valor, 3, 2 )."-".substr( $valor, 0, 2 );
                    $conteudo .= "'".$valor_trat."',";
                }
                else
                {
                    $padrao = trim( $qry_gera_cad_campos['valor_padrao'] );
                    if ( $padrao )
                    {
                        $padrao = str_replace( "@vs_id_usuario", $vs_id_usuario, $padrao );
                        $padrao = str_replace( "@vs_id_perfil", $vs_id_perfil, $padrao );
                        $padrao = str_replace( "@vs_dt_hoje", date( "Y-m-d" ), $padrao );
                        $padrao = str_replace( "@vs_hr_hms", date( "H:i:s" ), $padrao );
                        $padrao = str_replace( "@vs_hr_hm", date( "H:i" ), $padrao );
                        $conteudo .= "'{$padrao}',";
                    }
                    else
                    {
                        $conteudo .= "null,";
                    }
                }
            }
            else if ( $valor || $valor == "0" )
            {
                if ( $qry_gera_cad_campos['exibe_formulario'] == "1" )
                {
                    if ( $qry_gera_cad_campos['tipo_campo'] == "money" || $qry_gera_cad_campos['tipo_campo'] == "real" || $qry_gera_cad_campos['tipo_campo'] == "float" )
                    {
                        $valor = str_replace( ",", ".", str_replace( ".", "", $valor ) );
                    }
                    if ( $qry_gera_cad_campos['tipo_campo'] == "multicheck" && isset( $_POST["edt".$id_campo] ) )
                    {
                        foreach ( $_POST["edt".$id_campo] as $ids_check )
                        {
                            $valor .= $ids_check.",";
                        }
                        $valor = substr( $valor, 5, strlen( $valor ) - 6 );
                    }
                }
                $conteudo .= "'".TextoBD( $tipoBanco, nl2br( $valor ) )."',";
            }
            else
            {
                $padrao = trim( $qry_gera_cad_campos['valor_padrao'] );
                if ( $padrao )
                {
                    $padrao = str_replace( "@vs_id_usuario", $vs_id_usuario, $padrao );
                    $padrao = str_replace( "@vs_id_perfil", $vs_id_perfil, $padrao );
                    $padrao = str_replace( "@vs_dt_hoje", date( "Y-m-d" ), $padrao );
                    $padrao = str_replace( "@vs_hr_hms", date( "H:i:s" ), $padrao );
                    $padrao = str_replace( "@vs_hr_hm", date( "H:i" ), $padrao );
                    $pos_padrao = strpos( $padrao, "@vs_max_numreg" );
                    if ( $pos_padrao === false )
                    {
                    }
                    else
                    {
                        $qrymax = farray( query( "select max(".$pchave.") as maxid from ".$qry_gera_cad['nome_tabela'] ) );
                        $stmax = preg_replace( "([^0-9])", "", $qrymax['maxid'] );
                        if ( empty( $stmax ) )
                        {
                            $stmax = 0;
                        }
                        $novo = $stmax + 1;
                        $padrao = str_replace( "@vs_max_numreg", $novo, $padrao );
                        $pnummax = $padrao;
                    }
                    $pos_padrao = strpos( $padrao, "@vs_max_cotacao" );
                    if ( $pos_padrao === false )
                    {
                    }
                    else
                    {
                        $qrymax = farray( query( "select max(No_) as maxid from ".$qry_gera_cad['nome_tabela']." where No_ like 'CV%'" ) );
                        $stmax = preg_replace( "([^0-9])", "", $qrymax['maxid'] );
                        if ( empty( $stmax ) )
                        {
                            $stmax = 0;
                        }
                        $novo = $stmax + 1;
                        $padrao = str_replace( "@vs_max_cotacao", $novo, $padrao );
                        $pnummax = $padrao;
                    }
                    $pos_padrao = strpos( $padrao, "@mestre_" );
                    if ( $pos_padrao === false )
                    {
                    }
                    else
                    {
                        $padrao = "";
                    }
                    if ( $padrao )
                    {
                        $conteudo .= "'{$padrao}',";
                    }
                    else
                    {
                        $conteudo .= "Null,";
                    }
                }
                else
                {
                    $conteudo .= "Null,";
                }
            }
        }
        $conteudo = substr( $conteudo, 0, strlen( $conteudo ) - 1 );
        $campos = substr( $campos, 0, strlen( $campos ) - 1 );
        $sql_insert .= $campos.") values (".$conteudo.")";
        $rq = query( $sql_insert );
        if ( $rq == "1" )
        {
            $qry_max_numreg = farray( query( "select max(".$pchave.") as ultimo from ".$qry_gera_cad['nome_tabela'] ) );
            $pnumreg = $qry_max_numreg['ultimo'];
            REGISTRA_LOG( );
            echo "<script language=\"JavaScript\" src=\"js/ajax_menus.js\"></script>";
            require_once( "gera_cad_post_custom_after.php" );
            if ( strtoupper( $qry_gera_cad['tipo'] ) == "WORKFLOW" )
            {
                $qry_fases_workflow = farray( query( "select * from is_workflow_fase where id_workflow = '".$id_funcao."' order by nome_fase" ) );
                $cData = Date( "Y-m-d" );
                $cTime = Date( "H:i" );
                $cTime = SomaMinutosUteis( $cTime, $cData, $qry_fases_workflow['prazo_horas'] * 60 );
                $DtPrazo = substr( $cTime, 0, 10 );
                $HrPrazo = substr( $cTime, 11, 5 );
                $dt_hoje = date( "Y-m-d" );
                query( "UPDATE is_atividade SET id_formulario_workflow = '".$id_funcao."', id_fase_workflow = '".$qry_fases_workflow['id_fase']."', id_tp_atividade = '".$qry_gera_cad['id_tipo_workflow']."', id_usuario_resp = '".$id_usuario."', assunto = 'Workflow : ".$pnumreg."', dt_prev_fim = '{$DtPrazo}', hr_prev_fim = '{$HrPrazo}' where numreg = '".$pnumreg."'" );
                query( "insert into is_workflow_log(id_workflow,id_atividade,id_fase_workflow, dt_inicio, hr_inicio,dt_prazo,hr_prazo,id_usuario_resp,motivo) values ('".$id_funcao."','".$pnumreg."','".$qry_fases_workflow['id_fase']."','".$dt_hoje."','".date( "H:i" )."','".$DtPrazo."','".$HrPrazo."','".$id_usuario."','".$_POST['edtwfcomentario']."')" );
                $sql_ativ_auto = query( "select * from is_workflow_atividades where id_workflow = '".$id_funcao."' and id_fase_workflow = '".$qry_fases_workflow['id_fase']."'" );
                $RESPONSAVEL_DA_ATIVIDADE = $usuario_logado;
                while ( $qry_ativ_auto = farray( $sql_ativ_auto ) )
                {
                    $cDataAtiv = Date( "Y-m-d" );
                    $cTimeAtiv = Date( "H:i" );
                    $cTimeAtiv = SomaMinutosUteis( $cTimeAtiv, $cDataAtiv, $qry_ativ_auto['prazo_horas'] * 60 );
                    $DtPrazoAtiv = substr( $cTimeAtiv, 0, 10 );
                    $HrPrazoAtiv = substr( $cTimeAtiv, 11, 5 );
                    $RESPONSAVEL_DA_FASE = $id_usuario;
                    $ev_regras = $qry_ativ_auto['regras_negocio'];
                    $ev_regras = str_replace( "@sf", "'", $ev_regras );
                    eval( $ev_regras );
                    $sql_in_ativ = "insert into is_atividade(id_situacao,id_atividade,id_atividade_pai,id_tp_atividade,id_empresa_contato,id_pessoa_contato,assunto,dt_inicio,hr_inicio,dt_prev_fim,hr_prev_fim,id_usuario_resp,wf_sn_obrigatorio) values ('1','WF".$qry_fases_workflow['id_fase'].$pnumreg.$qry_ativ_auto['numreg']."','".$pnumreg."','".$qry_ativ_auto['id_tp_atividade']."','".$_POST['edtid_pessoa']."','".$_POST['edtid_pessoa_contato']."','".$qry_ativ_auto['assunto']." (Workflow:".$pnumreg." - Fase:".TextoBD( $tipoBanco, $qry_fases_workflow['nome_fase'] ).")"."','".$dt_hoje."','".date( "H:i" )."','".$DtPrazoAtiv."','".$HrPrazoAtiv."','".$RESPONSAVEL_DA_ATIVIDADE."','".$qry_ativ_auto['sn_obrigatorio_wf']."')";
                    query( $sql_in_ativ );
                }

            }
            echo "<script language=\"Javascript\"> ";
            if ( $prefpai == "1" )
            {
                echo "  exibe_programa_pai('',null,'".$pdiv."');";
            }
            if ( $pfecha == "1" )
            {
                echo " window.setTimeout( \""."window.close()"."\", 100);";
                echo "</script>";
                echo "<br><br><div align=\"center\" valign=\"center\"><img src=\"images/wait.gif\" align=\"absmiddle\" /></div>";
            }
            else
            {
                echo "window.alert('Registro incluido com sucesso !'); ";
                if ( $psnincluirnovo != "1" )
                {
                    $url_retorno = str_replace( "pnumreg=-1", "pnumreg=".$pnumreg, $url_retorno );
                }
                echo "window.location = '".$url_retorno."'; ";
                echo "</script>";
            }
        }
    }
    else
    {
        if ( $pchave2 ){
            $a_busca_numreg_exclusao = farray( query( "select numreg from ".$pref_bd.$qry_gera_cad['nome_tabela'].$pref_bd." WHERE ".$pref_bd.$pchave.$pref_bd." = '{$pnumreg}' and ".$pref_bd.$pchave2.$pref_bd." = '{$pnumreg2}'" ) );
        }else{
            $a_busca_numreg_exclusao = farray( query( "select numreg from ".$pref_bd.$qry_gera_cad['nome_tabela'].$pref_bd." WHERE ".$pref_bd.$pchave.$pref_bd." = '{$pnumreg}'" ) );
        }
        if ( $a_busca_numreg_exclusao['numreg'] * 1 == 0 ){
            echo "<script language=\"Javascript\"> ";
            echo "window.alert('Atenção este registro foi excluído durante sua edição e não foi encontrado no banco de dados ! O numero do registro é : ".$pnumreg." e poderá ser consultado no LOG do sistema.');";
            echo " window.setTimeout( \""."window.close()"."\", 100);";
            echo "</script>";
            exit( );
        }
        $operacao = "A";
        require_once( "gera_cad_post_custom_before.php" );
        $sql_update = "UPDATE ".$qry_gera_cad['nome_tabela']." SET ";
        $sql_gera_cad_campos = query( "(select * from is_gera_cad_campos where id_funcao = '{$id_funcao}' and tipo_campo <> 'calculado'   {$filtro_licenca} {$campos_bloqueados} and id_campo <> 'numreg') union all (select * from is_gera_cad_campos_custom where id_funcao = '{$id_funcao}' and tipo_campo <> 'calculado'   {$filtro_licenca} {$campos_bloqueados} and id_campo <> 'numreg') order by ordem" );
        while ( $qry_gera_cad_campos = farray( $sql_gera_cad_campos ) )
        {
            $id_campo = str_replace( " ", "_", $qry_gera_cad_campos['id_campo'] );
            if ( $qry_gera_cad_campos['tipo_campo'] == "arquivo" )
            {
                $temp = $_FILES["edt".$id_campo]['tmp_name'];
                $name = $_FILES["edt".$id_campo]['name'];
                $size = $_FILES["edt".$id_campo]['size'];
                $type = $_FILES["edt".$id_campo]['type'];
                if ( $name )
                {
                    $qrymax = farray( query( "select max(".$pchave.") as maxid from ".$qry_gera_cad['nome_tabela'] ) );
                    $stmax = preg_replace( "([^0-9])", "", $qrymax['maxid'] );
                    if ( empty( $stmax ) )
                    {
                        $stmax = 0;
                    }
                    $stmax = $stmax + 1;
                    $conteudo = "'".$stmax.$name."'";
                    copy( $temp, $caminho_arquivos.$stmax.$name );
                    $sql_update .= $qry_gera_cad_campos['id_campo']." = ".$conteudo.", ";
                }
            }
            else
            {
                if ( $qry_gera_cad_campos['tipo_campo'] == "date" || $qry_gera_cad_campos['tipo_campo'] == "datetime" )
                {
                    $valor = trim( substr( $_POST["edt".$id_campo], 0, 10 ) );
                    if ( $valor ){
                        $valor_trat = substr( $valor, 6, 4 )."-".substr( $valor, 3, 2 )."-".substr( $valor, 0, 2 );
                        $conteudo = "'".$valor_trat."'";
                    }else{
                        $padrao = $qry_gera_cad_campos['valor_padrao'];
                        if ( $padrao ){
                            $padrao = str_replace( "@vs_id_usuario", $vs_id_usuario, $padrao );
                            $padrao = str_replace( "@vs_id_perfil", $vs_id_perfil, $padrao );
                            if ( $tipoBanco == "mysql" ){
                                $padrao = str_replace( "@vs_dt_hoje", date( "Ymd" ), $padrao );
                            }else{
                                $padrao = str_replace( "@vs_dt_hoje", date( "Ymd" ), $padrao );
                            }

                            $padrao = str_replace( "@vs_hr_hms", date( "H:i:s" ), $padrao );
                            $padrao = str_replace( "@vs_hr_hm", date( "H:i" ), $padrao );
                            $conteudo = "'{$padrao}'";
                            if ( $conteudo == "''" ){
                                $conteudo = "Null";
                            }
                            $pos_padrao = strpos( $padrao, "@mestre_" );
                            if ( $pos_padrao === false ){
                            }else{
                                $padrao = "";
                            }
                        }else{
                            $conteudo = "null";
                        }
                    }
                }
                else if ( $qry_gera_cad_campos['tipo_campo'] == "memo" ){
                    $conteudo = "'".TextoBD( $tipoBanco, nl2br( $_POST["edt".$id_campo] ) )."'";
                    if ( $conteudo == "''" )
                    {
                        $conteudo = "Null";
                    }
                    $conteudo = str_replace( "../images/upload", "images/upload", $conteudo );
                    $conteudo = str_replace( "alt=\"width=\"", " ", $conteudo );
                    $conteudo = str_replace( "background-position: 0% 0%", "background-position: top", $conteudo );
                }else{
                    $vl_campo = $_POST["edt".$id_campo];

                    if ( $qry_gera_cad_campos['exibe_formulario'] == "1" ){
                        if ( $qry_gera_cad_campos['tipo_campo'] == "money" || $qry_gera_cad_campos['tipo_campo'] == "real" || $qry_gera_cad_campos['tipo_campo'] == "float" ){
                            $vl_campo = str_replace( ",", ".", str_replace( ".", "", $vl_campo ) );
                        }
                        if ( $qry_gera_cad_campos['tipo_campo'] == "multicheck" ){
                            $vl_campo = "";
                            if ( isset( $_POST["edt".$id_campo] ) )
                            {
                                $a_campo_checks = $_POST["edt".$id_campo.""];
                                foreach ( $a_campo_checks as $key_checks => $ids_check )
                                {
                                    $vl_campo .= $ids_check.",";
                                }
                                $vl_campo = substr( $vl_campo, 0, strlen( $vl_campo ) - 1 );
                            }
                        }
                    }
                    $conteudo = "'".TextoBD( $tipoBanco, $vl_campo )."'";

                    if ( $conteudo == "''" )
                    {
                        $conteudo = "Null";
                    }
                }
                $sql_update .= $pref_bd.$qry_gera_cad_campos['id_campo'].$pref_bd." = ".$conteudo.", ";
            }
        }
        $sql_update = substr( $sql_update, 0, strlen( $sql_update ) - 2 );

        if ( $pchave2 )
        {
            $sql_update .= " WHERE {$pchave} = '{$pnumreg}' and {$pchave2} = '{$pnumreg2}'";
        }
        else
        {
            $sql_update .= " WHERE {$pchave} = '{$pnumreg}'";
        }

        REGISTRA_LOG( );
        $rq = query( $sql_update );
        if ( $rq == "1" )
        {
            echo "<script language=\"JavaScript\" src=\"js/ajax_menus.js\"></script>";
            require_once( "gera_cad_post_custom_after.php" );
            if ( strtoupper( $qry_gera_cad['tipo'] ) == "WORKFLOW" )
            {
                $qry_atividades = farray( query( "select * from is_atividade WHERE {$pchave} = '{$pnumreg}'" ) );
                if ( $_POST['edtescolhemanual'] )
                {
                    $qry_utl_log = farray( query( "select * from is_workflow_log where id_atividade = '".$qry_atividades['numreg']."' order by numreg desc" ) );
                    query( "update is_atividade set id_usuario_resp = '".$_POST['edtescolhemanual']."' where {$pchave} = '{$pnumreg}'" );
                    query( "update is_workflow_log set id_usuario_resp = '".$_POST['edtescolhemanual']."' where numreg = '".$qry_utl_log['numreg']."'" );
                }
                else
                {
                    $qry_fases_workflow = farray( query( "select * from is_workflow_fase where id_workflow = '".$id_funcao."' and id_fase='".$qry_atividades['id_fase_workflow']."'" ) );
                    if ( $_POST['cbxopworkflow'] == "avancar" )
                    {
                        $regras_negocio = $qry_fases_workflow['regras_negocio'];
                        $regras_negocio = str_replace( "FIMSE", "}", $regras_negocio );
                        $regras_negocio = str_replace( "SENAO", "} else {", $regras_negocio );
                        $regras_negocio = str_replace( "SE ", "if( ", $regras_negocio );
                        $regras_negocio = str_replace( "ENTAO", ") {", $regras_negocio );
                        $regras_negocio = str_replace( "CAMPO_DA_TELA", "\$_POST", $regras_negocio );
                        $regras_negocio = str_replace( "<br />", " ", $regras_negocio );
                        $regras_negocio = str_replace( "@sf", "'", $regras_negocio );
                        eval( $regras_negocio );
                    }
                    if ( $_POST['cbxopworkflow'] == "recusar" )
                    {
                        RECUSAR( );
                    }
                }
            }
            echo "<script language=\"Javascript\"> ";
            if ( $prefpai == "1" ){
                echo "  exibe_programa_pai('',null,'".$pdiv."');";
            }
            if ( $pfecha == "1" ){
                echo " window.setTimeout( \""."window.close()"."\", 100);";
                echo "</script>";
                echo "<br><br><div align=\"center\" valign=\"center\"><img src=\"images/wait.gif\" align=\"absmiddle\" /></div>";
            }
            else{
                echo "window.alert('Alterações aplicadas com sucesso !'); ";
                if ( $psnincluirnovo == "1" ){
                    $url_retorno = str_replace( "pnumreg=".$pnumreg, "pnumreg=-1", $url_retorno );
                }
                else{
                    $url_retorno = str_replace( "pnumreg=-1", "pnumreg=".$pnumreg, $url_retorno );
                }
                echo "window.location = '".$url_retorno."'; ";
                echo "</script>";
            }
        }
    }
}
