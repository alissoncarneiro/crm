<?php

@session_start( );

header( "Cache-Control: no-cache" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=utf-8');

$lista_vs_id_usuario            = $_SESSION['id_usuario'];
$lista_vs_id_perfil             = $_SESSION['id_perfil'];
$lista_sn_bloquear_leitura      = $_SESSION['sn_bloquear_leitura'];
$lista_sn_bloquear_exclusao     = $_SESSION['sn_bloquear_exclusao'];


require_once( "conecta.php" );
require_once( "funcoes.php" );
require_once( "functions.php" );
require_once( "gera_cad_calc_custom.php" );

if ( empty( $lista_vs_id_usuario ) ){
    $email_login = $_GET['pemail'];
    if ( $email_login )    {
        $qry_login = farray( query( "select id_usuario from is_usuario where email = '{$email_login}'" ) );
        $def_login = $qry_login['id_usuario'];
    }
    echo "<script>alert('Sua sessão expirou ! Por favor fazer o login o novamente.');</script>";
    include( "index.php" );
    exit( );
}
$lista_id_funcao = $_GET['pfuncao'];
if ( $_SESSION['ip_consultor'] == "1" && ( $lista_id_funcao == "modulos_cad_lista" || $lista_id_funcao == "funcoes_cad_lista" || $lista_id_funcao == "gera_cad_sub_lista" ) && $_SESSION['ip_desenvolvedor'] == "" ){
    $_GET['pbloqincluir'] = "1";
    $_GET['pbloqexcluir'] = "1";
}
//echo "<pre>";
//print_r($GLOBALS);die();
$lista_pread                = $_GET['pread'];
$lista_pdiv                 = $_GET['pdiv'];
$lista_pgetcustom           = $_GET['pgetcustom'];
$lista_pbloqincluir         = $_GET['pbloqincluir'];
$lista_pbloqexcluir         = $_GET['pbloqexcluir'];
$lista_ptitulo              = $_GET['ptitulo'];
$lista_vs_id_empresa        = $_GET['pid_empresa'];
$lista_pusuario_filtro      = $_GET['pusuario_filtro'];
$lista_cbxfiltro            = $_GET['cbxfiltro'];
$lista_edtfiltro            = $_GET['edtfiltro'];
$lista_cbxordem             = $_GET['cbxordem'];

$a_bloqueio_cad = farray( query( "select * from is_perfil_funcao_bloqueio_cad where id_perfil = '".$_SESSION['id_perfil']."' and id_cad = '".$lista_id_funcao."'" ) );
if ( $a_bloqueio_cad['sn_bloqueio_ver'] == "1" ){
    echo "Seu perfil de acesso não tem permissão para acessar este cadastro ! Por favor contate o administrador do sistema.";
    exit( );
}
if ( $a_bloqueio_cad['sn_bloqueio_editar'] == "1" ){
    $lista_pread = "1";
}
if ( $a_bloqueio_cad['sn_bloqueio_excluir'] == "1" ){
    $lista_sn_bloquear_exclusao = "1";
}
if ( empty( $lista_cbxordem ) ){
//    $lista_cbxordem = str_replace( "%20", " ", $_POST['cbxordem'] );
    $lista_cbxordem = rawurldecode($_POST['cbxordem']);
}
if ( empty( $lista_cbxfiltro ) ){
//    $lista_cbxfiltro = str_replace( "%20", " ", $_POST['cbxfiltro'] );
    $lista_cbxfiltro = rawurldecode($_POST['cbxfiltro']);
}
if ( empty( $lista_edtfiltro ) ){
    //$lista_edtfiltro = utf8_decode( str_replace( "%20", " ", $_POST['edtfiltro'] ) );
    $lista_edtfiltro =rawurldecode($_POST['edtfiltro']) ;
}

$lista_sql_filtro   = $_POST['sql_filtro'];
$lista_descr_filtro = $_POST['descr_filtro'] ;
$lista_psubdet      = $_GET['psubdet'];
$lista_pnpai        = $_GET['pnpai'];

$lista_filtro_licenca = " and (id_licenca is null or id_licenca = '' or id_licenca like '%PADRAO%' or id_licenca like '%".$_SESSION['lic_id']."%')";
if ( $lista_descr_filtro == "limpar" ){
    $lista_descr_filtro = "";
}
if ( ( empty( $lista_sql_filtro ) || empty( $lista_descr_filtro ) ) && empty( $lista_pnpai ) ){
    $lista_sql_filtro   = $_GET['psql_filtro'];
    $lista_descr_filtro = $_GET['pdescr_filtro'];
}

$lista_sql_filtro = trim( $lista_sql_filtro );

//echo $lista_sql_filtro."aki";exit;
$lista_pos_ini              = $_GET['pos_ini'];
$lista_pfixo                = $_GET['pfixo'];
$lista_ppainel              = $_GET['ppainel'];
//$lista_pchave               = str_replace( "%20", " ", $_GET['pchave'] );
$lista_pchave               = rawurldecode($_GET['pchave'] );
//$lista_pchave2              = str_replace( "%20", " ", $_GET['pchave2'] );

$lista_pchave2              = rawurldecode($_GET['pchave2'] );
$lista_pexibedet            = $_GET['pexibedet'];

if ( empty( $lista_pchave ) ){
    $lista_pchave = "numreg";
}
if ( empty( $lista_pexibedet ) ){
    $lista_pexibedet = "1";
}

if ( $lista_ppainel == "1" ){
    $lista_maxpag = 5;
}else{
    $lista_maxpag = 25;
}

$lista_ppainel_div = $_GET['ppainel_div'];
$lista_pdrilldown = $_GET['pdrilldown'];
$lista_plupa = $_GET['plupa'];

if ( $lista_pnpai ){
    $posicao_janela = "200";
}else{
    $posicao_janela = "100";
}

$lista_sn_paginacao = "1";
if ( empty( $lista_pos_ini ) ){
    $lista_pos_ini = "0";
}
if ( empty( $lista_pread ) ){
    $lista_pread = "0";
}
if ( empty( $lista_id_funcao ) ){
    $lista_id_funcao = "empresas";
}

$lista_qry_gera_cad = farray( query( "select * from is_gera_cad where id_cad = '{$lista_id_funcao}'" ) );
$lista_qry_funcoes = farray( query( "select * from is_funcoes where id_funcao = '{$lista_id_funcao}'" ) );
$fonte_odbc = $lista_qry_gera_cad['fonte_odbc'];

if ( $fonte_odbc ){
    $pref_bd_ini = "\"";
    $pref_bd_fim = "\"";
}else{
    if ( $tipoBanco == "mysql" ){
        $pref_bd_ini = "`";
        $pref_bd_fim = "`";
    }
    if ( $tipoBanco == "mssql" ){
        $pref_bd_ini = "[";
        $pref_bd_fim = "]";
    }
}

if ( $fonte_odbc ){
    $lista_cbxfiltro_trat = $lista_cbxfiltro;
}

$lista_qry_bloqueios = farray( query( "select * from is_perfil_funcao_bloqueio where id_perfil = '{$lista_vs_id_perfil}' and id_funcao = '{$lista_id_funcao}'" ) );
if ( $lista_qry_bloqueios['sn_bloqueio_editar'] == "1" ){
    $lista_pread = "1";
}
$lista_sql_bloqueio = "";
$campos_bloqueados = "";
$q_bloqueio_campos = query( "select * from is_perfil_funcao_bloqueio_campos where id_perfil = '".$_SESSION['id_perfil']."' and id_cad = '".$lista_id_funcao."'  and sn_bloqueio_ver = 1" );

while ( $a_bloqueio_campos = farray( $q_bloqueio_campos ) ){
    $campos_bloqueados = $campos_bloqueados."'".$a_bloqueio_campos['id_campo']."',";
}

if ( $campos_bloqueados ){
    $campos_bloqueados = "and ( not id_campo in (".substr( $campos_bloqueados, 0, strlen( $campos_bloqueados ) - 1 )."))";
}

$lista_btn_excel    = $_GET['pbtn_excel'];
$lista_btn_graf     = $_GET['pbtn_graf'];
$lista_btn_relat    = $_GET['pbtn_relat'];
$lista_btn_pdf      = $_GET['pbtn_pdf'];
$lista_btn_ajuda    = $_GET['pbtn_ajuda'];

$qry_bloqueio_excel = farray( query( "select * from is_perfil where id_perfil = '".$_SESSION['id_perfil']."'" ) );
$sn_bloquear_excel = $qry_bloqueio_excel['sn_bloquear_excel'];

if ( $sn_bloquear_excel == "1" ){
    $lista_btn_excel = "0";
}else{
    $lista_btn_excel = "1";
}

if ( empty( $lista_btn_graf ) ){
    if ( $lista_psubdet || $lista_plupa )    {
        $lista_btn_graf = "0";
    }
    else{
        $lista_btn_graf = "1";
    }
}

if ( empty( $lista_btn_relat ) ){
    $a_layouts = farray( query( "select * from is_gera_cad_relat where id_cad = '".$lista_id_funcao."'" ) );
    if ( 0 < $a_layouts['numreg'] * 1 ){
        $lista_btn_relat = "1";
    }else{
        $lista_btn_relat = "0";
    }
}

if ( empty( $lista_btn_pdf ) ){
    $lista_btn_pdf = "0";
}

if ( empty( $lista_btn_ajuda ) ){
    $lista_btn_ajuda = "1";
}

require_once( "gera_cad_bloqueios_custom.php" );

$lista_filtro_geral = $lista_qry_gera_cad['sql_filtro'];


if ( strpos( $lista_filtro_geral, "where" ) === false ){
    $lista_clausula = "where";
}else{
    $lista_clausula = "and";
}

if ( $lista_cbxfiltro && $lista_edtfiltro ){
    $lista_qry_gera_cad_campos = farray( query( "(select * from is_gera_cad_campos where id_funcao = '{$lista_id_funcao}' and id_campo = '{$lista_cbxfiltro}') union all (select * from is_gera_cad_campos_custom where id_funcao = '{$lista_id_funcao}' and id_campo = '{$lista_cbxfiltro}')" ) );
    if ( $lista_qry_gera_cad_campos['tipo_campo'] == "lupa" || $lista_qry_gera_cad_campos['tipo_campo'] == "combobox" || trim( $lista_qry_gera_cad_campos['tipo_campo'] ) == "lupa_popup" )    {
        if ( strpos( $lista_qry_gera_cad_campos['sql_lupa'], "where" ) === false ){
            $lista_clausula_lupa = "where";
        }else{
            $lista_clausula_lupa = "and";
        }
        $lista_filtro_lupa = $lista_qry_gera_cad_campos['sql_lupa']." ".$lista_clausula_lupa." ".$lista_qry_gera_cad_campos['campo_descr_lupa']." like '%".$lista_edtfiltro."%'";
        $lista_filtro_lupa = str_replace( "@s", "'", $lista_filtro_lupa );
        $lista_filtro_lupa = str_replace( "@vs_cpo_id_funcao", $lista_qry_cadastro['id_funcao'], $lista_filtro_lupa );
        $lista_filtro_lupa = str_replace( "@vs_id_sistema", $_SESSION['id_sistema'], $lista_filtro_lupa );
        if ( $lista_qry_cadastro['id_workflow'] ){
            $lista_filtro_lupa = str_replace( "@vs_cpo_id_workflow", $lista_qry_cadastro['id_workflow'], $lista_filtro_lupa );
        }else{
            $lista_filtro_lupa = str_replace( "@vs_cpo_id_workflow", $lista_qry_mestre['id_cad'], $lista_filtro_lupa );
        }
        $sql_qry_lupa = query( $lista_filtro_lupa );
        $ids_lup = "";
        while ( $qrylup = farray( $sql_qry_lupa ) ){
            $ids_lup = $ids_lup."'".$qrylup[$lista_qry_gera_cad_campos['id_campo_lupa']]."',";
        }
        if ( $ids_lup ){
            $ids_lup = "(".substr( $ids_lup, 0, strlen( $ids_lup ) - 1 ).")";
        }else{
            $ids_lup = "('-99')";
        }
        $lista_pfiltro = " {$lista_clausula} {$pref_bd_ini}{$lista_cbxfiltro}{$pref_bd_fim} in ".$ids_lup;
        $lista_descr2_filtro = $lista_qry_gera_cad_campos['nome_campo']." ".$lista_edtfiltro;
    }else{
        if ( $lista_qry_gera_cad_campos['tipo_campo'] == "date" ){
            $lista_valor_trat = substr( $lista_edtfiltro, 6, 4 )."-".substr( $lista_edtfiltro, 3, 2 )."-".substr( $lista_edtfiltro, 0, 2 );
            $lista_pfiltro = " {$lista_clausula} {$pref_bd_ini}{$lista_cbxfiltro}{$pref_bd_fim} = '{$lista_valor_trat}'";
        }else if ( $lista_qry_gera_cad_campos['tipo_campo'] == "sim_nao" ){
            if ( strtoupper( substr( $lista_edtfiltro, 0, 1 ) ) == "" ){
                $vl_pesquisa = "NULL";
            }
            if ( strtoupper( substr( $lista_edtfiltro, 0, 1 ) ) == "S" ){
                $vl_pesquisa = "1";
            }
            if ( strtoupper( substr( $lista_edtfiltro, 0, 1 ) ) == "N" ){
                $vl_pesquisa = "0";
            }
            $lista_pfiltro = " {$lista_clausula} {$pref_bd_ini}{$lista_cbxfiltro}{$pref_bd_fim} = {$vl_pesquisa}";
        }else{
            $lista_pfiltro = " {$lista_clausula} {$pref_bd_ini}{$lista_cbxfiltro}{$pref_bd_fim} like '%{$lista_edtfiltro}%'";
        }
        $lista_descr2_filtro = $lista_qry_gera_cad_campos['nome_campo']." ".$lista_edtfiltro;
    }
    $lista_clausula = "and";
}
if ( $lista_pfixo ){
    $lista_fixo_trat = $lista_pfixo;
    $lista_pfiltro .= " {$lista_clausula} {$lista_pfixo}";
}

$lista_filtro_geral = $lista_filtro_geral." ".$lista_pfiltro." ";
$lista_filtro_geral = str_replace( "@vs_id_usuario", $lista_vs_id_usuario, $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@vs_id_perfil", $lista_vs_id_perfil, $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@vs_id_empresa", $lista_vs_id_empresa, $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@vs_dt_hoje", date( "Y-m-d" ), $lista_filtro_geral );

if ( $lista_sql_filtro ){
    if ( strpos( $lista_filtro_geral, "where" ) === false ){
        $lista_clausula = "where";
    }else{
        $lista_clausula = "and";
    }
    $lista_filtro_geral = $lista_filtro_geral." ".$lista_clausula." ".$lista_sql_filtro;
}

include( "gera_cad_lista_sql_custom.php" );


if ( $lista_sql_bloqueio ){
    if ( strpos( $lista_filtro_geral, "where" ) === false ){
        $lista_clausula = "where";
    }else{
        $lista_clausula = "and";
    }
    $lista_filtro_geral = $lista_filtro_geral." ".$lista_clausula." ".$lista_sql_bloqueio;
}


$lista_filtro_geral = trata_tags_sql( $lista_filtro_geral );

if ( $lista_cbxordem ){
    $lista_sql_ordem = $lista_cbxordem;
    $lista_sql_ordem = str_replace( "order by ", "order by {$pref_bd_ini}", $lista_sql_ordem );
    $lista_sql_ordem = str_replace( " desc", "{$pref_bd_fim} desc", $lista_sql_ordem );
    $lista_sql_ordem = str_replace( " asc", "{$pref_bd_fim} asc", $lista_sql_ordem );


    if ( $lista_sql_ordem == "order by id_pessoa_erp" || $lista_sql_ordem == "order by id_pessoa_erp asc" || $lista_sql_ordem == "order by ".$pref_bd_ini."id_pessoa_erp".$pref_bd_fim." asc" )    {
        $lista_sql_ordem = "order by (".$pref_bd_ini."id_pessoa_erp".$pref_bd_fim." *1) asc";
    }
    if ( $lista_sql_ordem == "order by ".$pref_bd_ini."id_pessoa_erp".$pref_bd_fim." desc" )    {
        $lista_sql_ordem = "order by (".$pref_bd_ini."id_pessoa_erp".$pref_bd_fim." *1) desc";
    }
}else{
    $lista_sql_ordem = $lista_qry_gera_cad['sql_ordem'];
}

if (strtolower( $tipoBanco ) == "mysql" ){

    $lista_sql_cadastro = query( $lista_filtro_geral." ".$lista_sql_ordem." LIMIT ".$lista_pos_ini.", ".$lista_maxpag, 1, $fonte_odbc );
}else{
    $lista_sql_cadastro = query( $lista_filtro_geral." ".$lista_sql_ordem, 1, $fonte_odbc );
}

if ( $fonte_odbc ){
    $sql_tot = query( str_replace( "select *", "select count(*) as total ", $lista_filtro_geral ), 1, $fonte_odbc );
    odbc_fetch_row( $sql_tot );
    $qry_tot = odbc_result( $sql_tot, "total" );
    $lista_tot = $qry_tot;
    if ( $lista_tot < $lista_pos_ini ){
        $lista_pos_ini = 0;
    }
    if ( 0 < $lista_tot ){
        odbc_fetch_row( $lista_sql_cadastro, $lista_pos_ini );
    }
}else if ( strtolower( $tipoBanco ) == "mysql" ){
    $npos_from = strpos( strtolower( $lista_filtro_geral ), " from" );
    $sql_tot = substr( $lista_filtro_geral, $npos_from, strlen( $lista_filtro_geral ) - $npos_from );
    $sql_tot = "select count(*) as total ".$sql_tot;
    $qry_tot = farray( query( $sql_tot ) );
    $lista_tot = $qry_tot['total'];
    if ( $lista_tot < $lista_pos_ini ){
        $lista_pos_ini = 0;
    }
}else{
    $lista_tot = numrows( $lista_sql_cadastro );
    if ( $lista_tot < $lista_pos_ini )    {
        $lista_pos_ini = 0;
    }
    if ( 0 < $lista_tot )    {
        dataseek( $lista_sql_cadastro, $lista_pos_ini );
    }
}


//echo $lista_filtro_geral;

if ( $lista_pdrilldown ){ ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
    if ( $lista_ptitulo ) {
        echo "<title> ".$lista_ptitulo."</title>";
    }else{
        echo "<title> ".$lista_qry_gera_cad['titulo']."</title>";
    }
    ?>

    <style type="text/css">
    body {margin-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px; }
    table{ white-space: nowrap;}
    </style>

    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="//code.jquery.com/jquery-migrate-1.4.1.js"></script>
    <script language="JavaScript" src="js/ajax_menus.js"></script>
    <script language="JavaScript" src="js/ajax_gera_cad.js"></script>
    <script language="JavaScript" src="js/function.js"></script>



    </head>
    <body>
    <div name="div_programa" id="div_programa">
<?php }
echo "\n<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"2\" cellspacing=\"2\">\n    <tr>\n        <td colspan=\"3\" valign=\"center\">\n            <div align=\"left\" valign=\"center\">\n                &nbsp;<img src=\"images/seta.gif\" width=\"4\" height=\"7\" />";
echo "<span class=\"tit_detalhes\">";

if ( $lista_ptitulo ){
    echo $lista_ptitulo;
}else{
    echo $lista_qry_gera_cad['titulo'];
}

echo "</span>&nbsp;\n                ";
echo $lista_tot." registro(s) encontrado(s). Pág.: ";
if ( $lista_sn_paginacao == "1" ){
    $lista_rd = $lista_tot / $lista_maxpag;
    $lista_tp = ceil( $lista_rd );
    $lista_pag = 1;
    if ( $lista_ppainel == "1" )    {
        $lista_conta_pag_max = 10;
    }else{
        $lista_conta_pag_max = 30;
    }

    while ( $lista_pag <= $lista_tp && $lista_pag <= $lista_conta_pag_max ){
        if ( ( $lista_pag - 1 ) * $lista_maxpag == $lista_pos_ini ){
            echo "<b><font face=\"Verdana\" size=\"3\">";
        }

        if ( $lista_ppainel == "1" ){
            $lista_pag_url = "javascript:exibe_programa('gera_cad_lista.php?pfuncao=".$lista_id_funcao."&operacao=filtrar&pos_ini=".( $lista_pag - 1 ) * $lista_maxpag."&pnpai=".$lista_pnpai."&pnsubdet=".$lista_pnsubdet."&pfixo=".$lista_pfixo."&pdrilldown=".$lista_pdrilldown."&ppainel=".$lista_ppainel."&ppainel_div=".$lista_ppainel_div."','".$lista_ppainel_div."&plupa=".$lista_plupa."&ordem=".$lista_cbxordem."&pdiv=".$lista_pdiv."&pusuario_filtro=".$lista_pusuario_filtro."&cbxfiltro=".$lista_cbxfiltro."&edtfiltro=".$lista_edtfiltro."&cbxordem=".$lista_cbxordem."&pgetcustom=".$lista_pgetcustom."&pbloqincluir=".$lista_pbloqincluir."&pbloqexcluir=".$lista_pbloqexcluir."&ptitulo=".$lista_ptitulo."&pchave=".$lista_pchave."&pchave2=".$pchave2."');";
        }else{
            $lista_pag_url = "javascript:exibe_programa('gera_cad_lista.php?pfuncao=".$lista_id_funcao."&operacao=filtrar&pos_ini=".( $lista_pag - 1 ) * $lista_maxpag."&pnpai=".$lista_pnpai."&pnsubdet=".$lista_pnsubdet."&pfixo=".$lista_pfixo."&pdrilldown=".$lista_pdrilldown."&plupa=".$lista_plupa."&ordem=".$lista_cbxordem."&pdiv=".$lista_pdiv."&pusuario_filtro=".$lista_pusuario_filtro."&cbxfiltro=".$lista_cbxfiltro."&edtfiltro=".$lista_edtfiltro."&cbxordem=".$lista_cbxordem."&pgetcustom=".$lista_pgetcustom."&pbloqincluir=".$lista_pbloqincluir."&pbloqexcluir=".$lista_pbloqexcluir."&ptitulo=".$lista_ptitulo."&pchave=".$lista_pchave."&pchave2=".$pchave2."');";
        }

        echo "<a href=\"".$lista_pag_url."\">".$lista_pag."</a> ";
        if ( ( $lista_pag - 1 ) * $lista_maxpag == $lista_pos_ini ){
            echo "</b></font>";
        }
        $lista_pag = $lista_pag + 1;
    }
    if ( 30 < $lista_tp ){
        echo "...";
    }
}

if ( $lista_descr2_filtro ){
    $lista_descr2_filtro = " (".$lista_descr2_filtro.")";
}

if ( $lista_descr_filtro || $lista_descr2_filtro ){
    echo "&nbsp;&nbsp;<span style=\"color:black;\">* Busca Ativa : ".$lista_descr_filtro;
    echo $lista_descr2_filtro;
    echo "</span>";
}

echo "<div style=\"height:1px; width:100%; background-color:#E0E0E0; margin-top:2px; margin-bottom:2px;\"></div>";
$lista_ref = str_replace( "@s", "'", str_replace( "@pnumreg", "-1", $lista_qry_gera_cad['url_alterar'] ) )."&psubdet=".$lista_psubdet."&pnpai=".$lista_pnpai."&pfixo=".$lista_pfixo."&pgetcustom=".$lista_pgetcustom."&pbloqincluir=".$lista_pbloqincluir."&pbloqexcluir=".$lista_pbloqexcluir."&ptitulo=".$lista_ptitulo."&pchave=".$lista_pchave."&pchave2=".$lista_pchave2;
$npos_programa = strpos( $lista_ref, ".php" );
$nome_programa = substr( $lista_ref, 0, $npos_programa );
$lista_ref = "gera_filtro_detalhe.php?pfuncao=".$lista_id_funcao;
$lista_url_open = "javascript:window.open('".$lista_ref."','".$lista_id_funcao."1','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=750,height=450,top=".$posicao_janela.",left=".$posicao_janela."').focus(); return false;";
if ( empty( $lista_ppainel ) || $lista_ppainel == "0" ){
    if ( $lista_sql_filtro ){
        echo "<a href=\"#\" onclick=\"".$lista_url_open."\" title=\"Clique aqui para Busca Avançada...\">Busca *</a> : ";
    }else{
        echo "<a href=\"#\" onclick=\"".$lista_url_open."\" title=\"Clique aqui para Busca Avançada...\">Busca</a> : ";
    }

    echo "<select size=\"1\" name=\"cbxfiltro\" id=\"cbxfiltro\">\n                    ";
    $lista_sql_gera_cad_campos = query( "(select * from is_gera_cad_campos where exibe_filtro =1 and id_funcao = '{$lista_id_funcao}' {$lista_filtro_licenca} {$campos_bloqueados} ) union all (select * from is_gera_cad_campos_custom where exibe_filtro =1 and id_funcao = '{$lista_id_funcao}' {$lista_filtro_licenca} {$campos_bloqueados} ) order by ordem" );
    $options = "";
    while ( $lista_qry_gera_cad_campos = farray( $lista_sql_gera_cad_campos ) )    {
        if ( $lista_cbxfiltro == $lista_qry_gera_cad_campos['id_campo'] )        {
            $options .= "<option selected value=\"".$lista_qry_gera_cad_campos['id_campo']."\">".$lista_qry_gera_cad_campos['nome_campo']."</option>";
        }else{
            $options .= "<option value=\"".$lista_qry_gera_cad_campos['id_campo']."\">".$lista_qry_gera_cad_campos['nome_campo']."</option>";
        }
    }
    echo $options;
    echo "                </select>\n\n                ";
    $lista_url_filtro = "javascript:exibe_programa('gera_cad_lista.php?pfuncao=".$lista_id_funcao."&operacao=filtrar&pfixo=".$lista_pfixo."&pdrilldown=".$lista_pdrilldown."&pnpai=".$lista_pnpai."&psubdet=".$lista_psubdet."&pgetcustom=".$lista_pgetcustom."&pbloqincluir=".$lista_pbloqincluir."&pbloqexcluir=".$lista_pbloqexcluir."&ptitulo=".$lista_ptitulo."&pchave=".$lista_pchave."&pchave2=".$lista_pchave2."&pread=".$lista_pread."&plupa=".$lista_plupa."&ordem=".$lista_cbxordem."&pos_ini=".$lista_pos_ini."');";
    $lista_url_exportar = "gera_cad_exporta.php?pfuncao=".$lista_id_funcao."&operacao=filtrar&pfixo=".$lista_pfixo."&psql_filtro=".$lista_sql_filtro."&pdescr_filtro=".$lista_descr_filtro.$lista_descr2_filtro."&pcbxfiltro=".$lista_cbxfiltro."&pedtfiltro=".$lista_edtfiltro."&pdrilldown=".$lista_pdrilldown."&pnpai=".$lista_pnpai."&psubdet=".$lista_psubdet."&pexporta=S"."&ordem=".$lista_cbxordem;


    $lista_url_grafico = "gera_cad_graf.php?programa=".$lista_id_funcao."&pfixo=".$lista_pfixo."&psql_filtro=".$lista_sql_filtro."&pdescr_filtro=".$lista_descr_filtro.$lista_descr2_filtro."&pcbxfiltro=".$lista_cbxfiltro."&pedtfiltro=".$lista_edtfiltro."&ordem=".$lista_cbxordem;
    $lista_url_open_grafico = "window.open('".$lista_url_grafico."','graf','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=".$posicao_janela.",left=".$posicao_janela."').focus(); return false;";



    $lista_url_ajuda = "gera_cad_ajuda.php?ptipo=cadastro&pfuncao=".$lista_id_funcao;
    $lista_url_open_ajuda = "window.open('".$lista_url_ajuda."','ajuda','location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=500,height=300,top=200,left=200').focus(); return false;";
    $lista_url_relatorio = "gera_relatorio.php?pfuncao=".$lista_id_funcao."&operacao=filtrar&pfixo=".$lista_pfixo."&psql_filtro=".$lista_sql_filtro."&pdescr_filtro=".$lista_descr_filtro.$lista_descr2_filtro."&pcbxfiltro=".$lista_cbxfiltro."&pedtfiltro=".$lista_edtfiltro."&pdrilldown=".$lista_pdrilldown."&pnpai=".$lista_pnpai."&psubdet=".$lista_psubdet."&pexporta=S"."&ordem=".$lista_cbxordem;
    $lista_url_open_relatorio = "window.open('".$lista_url_relatorio."','gerador_relat','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=".$posicao_janela.",left=".$posicao_janela."').focus(); return false;";
    echo "                    <input type=\"hidden\" name=\"sql_filtro\" id=\"sql_filtro\" value=\"";
    echo $lista_sql_filtro;
    echo "\"/>\n                    <input type=\"hidden\" name=\"descr_filtro\" id=\"descr_filtro\" value=\"";
    echo $lista_descr_filtro;
    echo "\"/>\n                    <input type=\"hidden\" name=\"url_filtro\" id=\"url_filtro\" value=\"";
    echo $lista_url_filtro;
    echo "\"/>\n                    <input type=\"text\" name=\"edtfiltro\" id=\"edtfiltro\" size=\"15\" value=\"";
    echo $lista_edtfiltro;
    echo "\" onkeypress=\"javascript: if (event.keyCode == 13) { btnfiltrar.focus();  btnfiltrar.click(); return false; }\"/>\n                    ";
    echo "<s";
    echo "elect size=\"1\" name=\"cbxordem\" id=\"cbxordem\"  title=\"Ordenar o resultado pela coluna\">\n                    ";
    $lista_sql_gera_cad_campos = query( "(select * from is_gera_cad_campos where exibe_browse =1 and id_funcao = '{$lista_id_funcao}' {$lista_filtro_licenca} {$campos_bloqueados} ) union all (select * from is_gera_cad_campos_custom where exibe_browse =1 and id_funcao = '{$lista_id_funcao}' {$lista_filtro_licenca} {$campos_bloqueados} ) order by ordem" );
    if ( $lista_cbxordem == "" ){
        $options = "<option selected value=\"\">Ordernar por</option>";
    }else{
        $options = "<option  value=\"\">Ordenar por</option>";
    }
    while ( $lista_qry_gera_cad_campos = farray( $lista_sql_gera_cad_campos ) ){
        if ( $lista_qry_gera_cad_campos['tipo_campo'] == "lupa" || $lista_qry_gera_cad_campos['tipo_campo'] == "combobox" || $lista_qry_gera_cad_campos['tipo_campo'] == "lupa_popup" ){
            $prefixo = "Cod.";
        }else{
            $prefixo = "";
        }

        if ( $lista_cbxordem == "order by ".$lista_qry_gera_cad_campos['id_campo']." asc" ){
            $options .= "<option selected value=\"order by ".$lista_qry_gera_cad_campos['id_campo']." asc\">&darr; ".$prefixo.$lista_qry_gera_cad_campos['nome_campo']."</option>";
            $options .= "<option value=\"order by ".$lista_qry_gera_cad_campos['id_campo']." desc\">&uarr; ".$prefixo.$lista_qry_gera_cad_campos['nome_campo']."</option>";
        }else if ( $lista_cbxordem == "order by ".$lista_qry_gera_cad_campos['id_campo']." desc" )        {
            $options .= "<option value=\"order by ".$lista_qry_gera_cad_campos['id_campo']." asc\">&darr; ".$prefixo.$lista_qry_gera_cad_campos['nome_campo']."</option>";
            $options .= "<option selected value=\"order by ".$lista_qry_gera_cad_campos['id_campo']." desc\">&uarr; ".$prefixo.$lista_qry_gera_cad_campos['nome_campo']."</option>";
        }else{
            $options .= "<option value=\"order by ".$lista_qry_gera_cad_campos['id_campo']." asc\">&darr; ".$prefixo.$lista_qry_gera_cad_campos['nome_campo']."</option>";
            $options .= "<option value=\"order by ".$lista_qry_gera_cad_campos['id_campo']." desc\">&uarr; ".$prefixo.$lista_qry_gera_cad_campos['nome_campo']."</option>";
        }
    }
    echo $options;
    echo "                </select>\n\n                <input type=\"button\" value=\"Filtrar\" name=\"btnfiltrar\"  id=\"btnfiltrar\" title=\"Aplicar Filtro...\" onclick=\"";
    echo $lista_url_filtro;
    echo "\" class=\"botao_form\" />\n                <input type=\"button\"  value=\"Limpar\" name=\"btnlimpar\"  title=\"Limpar Filtro...\" onclick=\"javascript:document.getElementById('sql_filtro').value=''; document.getElementById('descr_filtro').value='limpar'; document.getElementById('edtfiltro').value=''; ";
    echo $lista_url_filtro;
    echo "\" class=\"botao_form\" />\n                ";
}
$lista_ref = str_replace( "@s", "'", str_replace( "@pnumreg", "-1", $lista_qry_gera_cad['url_alterar'] ) )."&psubdet=".$lista_psubdet."&pnpai=".$lista_pnpai."&pfixo=".$lista_pfixo."&pdiv=".$lista_pdiv."&pusuario_filtro=".$lista_pusuario_filtro."&cbxfiltro=".$lista_cbxfiltro."&edtfiltro=".$lista_edtfiltro."&pos_ini=".$lista_pos_ini."&pgetcustom=".$lista_pgetcustom."&pbloqincluir=".$lista_pbloqincluir."&pbloqexcluir=".$lista_pbloqexcluir."&ptitulo=".$lista_ptitulo."&cbxordem=".$lista_cbxordem;
$lista_url_open = "javascript:window.open('".$lista_ref."','".$lista_id_funcao."3','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=810,height=550,top=".$posicao_janela.",left=".$posicao_janela."').focus(); return false;";
if ( $lista_pread == "0" && $a_bloqueio_cad['sn_bloqueio_incluir'] != "1" && $lista_pbloqincluir != "1" ){
    echo "<input type=\"button\" value=\"+ Incluir\" name=\"btnincluir\" title=\"Incluir um novo registro...\" onclick=\"".$lista_url_open."\" class=\"botao_form\"/>";
}

if ( empty( $lista_ppainel ) || $lista_ppainel == "0" ){
//    echo "<hr>".$lista_filtro_geral."<hr>";
//    echo $lista_url_grafico."<hr>".$lista_url_open_grafico; exit;
    if ( $lista_btn_graf != "0" ){ ?>
        <a href="<?php echo $lista_url_grafico;?>" target="_blank" onclick="javascript:<?php echo $lista_url_open_grafico;?>" >
            <img src="images/btn_grafico.jpg" border=0 alt="Gerar Gráficos da seleção abaixo..." width="18" height="18" />
        </a>
    <?php }
    if ( $lista_btn_excel != "0" ){
        echo "<a href='$lista_url_exportar' target='_blank'><img src='images/btn_excel.jpg' border=0 alt='Exportar seleção abaixo no formato XLS do excel...' width='18' height='18' /></a>\n";
    }
    if ( $lista_btn_relat != "0" ){
        echo "<a href='$lista_url_relatorio' target='_blank' onclick='javascript:$lista_url_open_relatorio' ><img src='images/btn_print.jpg' border=0 alt='Gerador de Relatório...' width='18' height='18' /></a>\n";
    }
    if ( $lista_btn_pdf != "0" ){
        echo "<a href='$lista_url_relatorio' target='_blank'><img src='images/btn_pdf.jpg' border=0  alt='Gerar Relatório em PDF da seleção abaixo...' width='17' height='17' /></a>\n";
    }
    if ( $lista_btn_ajuda != "0" && $lista_qry_gera_cad['ajuda'] )
    {
        echo "<a href='$lista_url_ajuda' target='_blank' onclick='javascript:$lista_url_open_ajuda'><img src='images/btn_ajuda.jpg' border=0 alt='Exibir Ajuda' width='18' height='18' /></a>\n";
    }
    echo "\n";
    if ( $lista_pdrilldown == "1" && empty( $lista_plupa ) && empty( $lista_pdiv ) )
    {
        echo "&nbsp;<input type=\"button\" value=\"Fechar\" name=\"btnfechar\" onclick=\"javascript:window.close()\" class=\"botao_form\"/>";
    }
}
echo "            </div>\n            ";
echo "<div style=\"height:1px; width:100%; background-color:#E0E0E0; margin-top:2px; margin-bottom:2px;\"></div>";
echo "            </td>\n            <td colspan=\"1\" align=\"right\">\n            ";
if ( $lista_qry_funcoes['url_imagem'] )
{
    echo "<img src=\"".$lista_qry_funcoes['url_imagem']."\" width=\"40\" height=\"40\" />";
}
echo "            </td>\n\n        </tr>\n    </table>\n    <table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"2\" cellspacing=\"2\" class=\"bordatabela\">\n    ";
echo "<tr>";
echo "<td bgcolor=\"#dae8f4\" class=\"tit_tabela\" width=\"15\">&nbsp;</td>";
if ( $lista_plupa )
{
    echo "<td bgcolor=\"#dae8f4\" class=\"tit_tabela\" width=\"15\">&nbsp;</td>";
}
if ( $lista_qry_gera_cad['nome_tabela'] == "is_atividade" )
{
    echo "<td bgcolor=\"#dae8f4\" class=\"tit_tabela\" width=\"15\">Tp.</td>";
}
if ( $lista_ppainel == "1" )
{
    $lista_filtrapainel = "and sn_painel = 1";
}
else
{
    $lista_filtrapainel = "";
}
$lista_sql_gera_cad_campos = query( "(select * from is_gera_cad_campos where exibe_browse = 1 and id_funcao = '{$lista_id_funcao}' ".$lista_filtrapainel." ".$lista_filtro_licenca." {$campos_bloqueados} ) union all (select * from is_gera_cad_campos_custom where exibe_browse = 1 and id_funcao = '{$lista_id_funcao}' ".$lista_filtrapainel." ".$lista_filtro_licenca." {$campos_bloqueados} ) order by ordem" );

while ( $lista_qry_gera_cad_campos = farray( $lista_sql_gera_cad_campos ) ){
    echo "<td bgcolor=\"#dae8f4\" class=\"tit_tabela\" >".$lista_qry_gera_cad_campos['nome_campo'];
    if ( $lista_qry_gera_cad_campos['sn_soma'] == "1" ){
        $sql_soma = substr_replace( $lista_filtro_geral, "select sum(".$lista_qry_gera_cad_campos['id_campo'].") as TOTAL", 0, 8 );
        $lista_qry_soma = farray( query( $sql_soma ) );
        if ( $lista_qry_gera_cad_campos['tipo_campo'] == "money" )        {
            echo "<br>Total:R\$".number_format( $lista_qry_soma['TOTAL'], 2, ",", "." );
        }else{
            echo "<br>Total:".number_format( $lista_qry_soma['TOTAL'], 2, ",", "." );
        }
    }
    echo "</td>";
}

if ( $lista_pread == "0" && $lista_sn_bloquear_exclusao != "1" && $lista_pbloqexcluir != "1" ){
    echo "<td bgcolor=\"#dae8f4\" class=\"tit_tabela\" width=\"15\">Excluir</td>";
}echo "</tr>";
$lista_campo_grupo = $lista_qry_gera_cad['campo_grupo'];

if ( $lista_campo_grupo ){
    $lista_quebra = "";
}

while ( $lista_qry_cadastro = farray( $lista_sql_cadastro, $fonte_odbc ) ){
    if ( $lista_campo_grupo && $lista_quebra != $lista_qry_cadastro[$lista_campo_grupo] ){
        $lista_quebra = $lista_qry_cadastro[$lista_campo_grupo];
        $lista_qry_gera_cad_campos = farray( query( "(select * from is_gera_cad_campos where id_funcao = '{$lista_id_funcao}' and id_campo = '{$lista_campo_grupo}') union all (select * from is_gera_cad_campos_custom where id_funcao = '{$lista_id_funcao}' and id_campo = '{$lista_campo_grupo}')" ) );
        if ( $lista_qry_gera_cad_campos['tipo_campo'] == "lupa" || $lista_qry_gera_cad_campos['tipo_campo'] == "combobox" || $lista_qry_gera_cad_campos['tipo_campo'] == "lupa_popup" ){
            if ( strpos( $lista_qry_gera_cad_campos['sql_lupa'], "where" ) === false ){
                $lista_clausula = "where";
            }else{
                $lista_clausula = "and";
            }
            $lista_filtro_lupa = $lista_qry_gera_cad_campos['sql_lupa']." ".$lista_clausula." ".$pref_bd_ini.$lista_qry_gera_cad_campos['id_campo_lupa'].$pref_bd_fim." = '".$lista_qry_cadastro[$lista_qry_gera_cad_campos['id_campo']]."'";
            $lista_filtro_lupa = str_replace( "@s", "'", $lista_filtro_lupa );
            $lista_filtro_lupa = str_replace( "@vs_cpo_id_funcao", $lista_qry_cadastro['id_funcao'], $lista_filtro_lupa );
            $lista_filtro_lupa = str_replace( "@vs_cpo_id_workflow", $lista_qry_cadastro['id_workflow'], $lista_filtro_lupa );
            $lista_filtro_lupa = str_replace( "@vs_id_sistema", $_SESSION['id_sistema'], $lista_filtro_lupa );
            if ( $lista_qry_cadastro['id_workflow'] ){
                $lista_filtro_lupa = str_replace( "@vs_cpo_id_workflow", $lista_qry_cadastro['id_workflow'], $lista_filtro_lupa );
            }else{
                $lista_filtro_lupa = str_replace( "@vs_cpo_id_workflow", $lista_qry_mestre['id_cad'], $lista_filtro_lupa );
            }
            $lista_qry_lupa = farray( query( $lista_filtro_lupa ) );
            $lista_lupa_descr = " - <i>".str_replace( "\"", " ", $lista_qry_lupa[$lista_qry_gera_cad_campos['campo_descr_lupa']] )."</i>";
        }
        echo "<tr>";
        echo "<td bgcolor=\"#dae8f4\" width=\"100%\" colspan=20><font face=\"Verdana\" size=\"1\"><b>".$lista_qry_gera_cad_campos['nome_campo']." : ".$lista_quebra.$lista_lupa_descr."</b></font></td>";
        echo "</tr>";
    }
    if ( $tr_color == "#EBEBEB" ){
        $tr_color = "#FFFFFF";
    }else{
        $tr_color = "#EBEBEB";
    }
    $lista_tdstyle = "";
    $lista_primeira_coluna = "1";
    $lista_url_edita = "1";
    echo "<tr style=\"background:".$tr_color."\" id=\"linha".$lista_qry_cadastro[$lista_pchave];
    echo "\" onmouseover=\"this.style.background="."'lightblue';"."\" onmouseout=\"this.style.background="."'".$tr_color."';"."\">";
    $lista_sql_gera_cad_campos = query( "(select * from is_gera_cad_campos where exibe_browse = 1 and id_funcao = '{$lista_id_funcao}' ".$lista_filtrapainel." ".$lista_filtro_licenca." {$campos_bloqueados} ) union all (select * from is_gera_cad_campos_custom where exibe_browse = 1 and id_funcao = '{$lista_id_funcao}' ".$lista_filtrapainel." ".$lista_filtro_licenca." {$campos_bloqueados} ) order by ordem" );
    while ( $lista_qry_gera_cad_campos = farray( $lista_sql_gera_cad_campos ) ){
        $lista_url_open = "window.open(this.href,'".$lista_id_funcao.$lista_qry_cadastro[$lista_pchave]."','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=850,height=550,top=".$posicao_janela.",left=".$posicao_janela."').focus(); return false;";
        $lista_abre_funcao = "<a href=\"".str_replace( "@s", "'", str_replace( "@pnumreg", $lista_qry_cadastro[$lista_pchave], $lista_qry_gera_cad['url_alterar'] ) )."&psubdet=".$lista_psubdet."&pread=".$lista_pread."&pnpai=".$lista_pnpai."&pfixo=".$lista_pfixo."&pdiv=".$lista_pdiv."&pusuario_filtro=".$lista_pusuario_filtro."&pos_ini=".$lista_pos_ini."&cbxfiltro=".$lista_cbxfiltro."&edtfiltro=".$lista_edtfiltro."&pgetcustom=".$lista_pgetcustom."&pbloqincluir=".$lista_pbloqincluir."&pbloqexcluir=".$lista_pbloqexcluir."&ptitulo=".$lista_ptitulo."&cbxordem=".$lista_cbxordem."\" onclick=\"".$lista_url_open."\" ".$lista_tdstyle.">";
        $lista_url_open = "window.open(this.href,'".$lista_id_funcao.$lista_qry_cadastro[$lista_pchave2].$lista_qry_cadastro[$lista_pchave]."','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=850,height=550,top=".$posicao_janela.",left=".$posicao_janela."').focus(); return false;";
        $lista_abre_funcao = "<a href=\"".str_replace( "@s", "'", str_replace( "@pnumreg2", $lista_qry_cadastro[$lista_pchave2], $lista_qry_gera_cad['url_alterar'] ) );
        $lista_abre_funcao = str_replace( "@pnumreg", $lista_qry_cadastro[$lista_pchave], $lista_abre_funcao )."&psubdet=".$lista_psubdet."&pread=".$lista_pread."&pnpai=".$lista_pnpai."&pfixo=".$lista_pfixo."&pdiv=".$lista_pdiv."&pusuario_filtro=".$lista_pusuario_filtro."&cbxfiltro=".$lista_cbxfiltro."&edtfiltro=".$lista_edtfiltro."&pos_ini=".$lista_pos_ini."&pgetcustom=".$lista_pgetcustom."&pbloqincluir=".$lista_pbloqincluir."&pbloqexcluir=".$lista_pbloqexcluir."&ptitulo=".$lista_ptitulo."&cbxordem=".$lista_cbxordem."\" onclick=\"".$lista_url_open."\" ".$lista_tdstyle.">";
        if ( $lista_id_funcao == "propostas_cad" || $lista_id_funcao == "nf_pdf" ){
            $lista_abre_funcao = "<a href=\"".str_replace( "@s", "'", str_replace( "@pnumreg", $lista_qry_cadastro[$lista_pchave], $lista_qry_gera_cad['url_alterar'] ) )."&psubdet=".$lista_psubdet."&pread=".$lista_pread."&pnpai=".$lista_pnpai."&pgetcustom=".$lista_pgetcustom."&pbloqincluir=".$lista_pbloqincluir."&pbloqexcluir=".$lista_pbloqexcluir."&ptitulo=".$lista_ptitulo."&pfixo=".$lista_pfixo."\" ".$lista_tdstyle." target=\"_blank\">";
        }

        include( "gera_cad_cores_custom.php" );
        if ( $lista_qry_gera_cad['nome_tabela'] == "is_atividade" && $lista_primeira_coluna == "1" ){
            $texto_img = "";
            $lista_color2 = $lista_color;
            if ( $lista_qry_cadastro['id_formulario_workflow'] ){
                $qry_wf = farray( query( "select * from is_gera_cad where id_cad='".$lista_qry_cadastro['id_formulario_workflow']."'" ) );
                $texto_img = "Workflow : ".$qry_wf['titulo'];
                $lista_color2 = "bgcolor=\"#000000\"";
            }else if ( substr( $lista_qry_cadastro['id_atividade'], 0, 2 ) == "WF" ){
                $texto_img = "Atividade Gerada por Workflow : ".$lista_qry_cadastro['id_atividade_pai'];
                $lista_color2 = "bgcolor=\"#FF9900\"";
            }else{
                $texto_img = $lista_qry_img['nome_tp_atividade'];
            }

            $lista_qry_img = farray( query( "select nome_tp_atividade, url_imagem from is_tp_atividade where numreg = '".$lista_qry_cadastro['id_tp_atividade']."'" ) );
            $troca_funcao = "";
            if ( $lista_qry_cadastro['id_tp_atividade'] == "OPOR" ){
                $troca_funcao = "opo_cad_lista";
            }
            if ( $lista_qry_cadastro['id_tp_atividade'] == "SAC" ){
                $troca_funcao = "sac_cad_lista";
            }
            if ( $lista_qry_cadastro['id_formulario_workflow'] ){
                $troca_funcao = $lista_qry_cadastro['id_formulario_workflow'];
            }
        }
        if ( $troca_funcao ){
            $lista_abre_funcao = str_replace( "atividades_cad_lista", $troca_funcao, $lista_abre_funcao );
        }
        if ( $lista_plupa && $lista_primeira_coluna == "1" ){
            echo "<td ".$lista_color.">";
            $lista_qry_plupa = farray( query( "(select * from is_gera_cad_campos where numreg = '".$lista_plupa."') union all (select * from is_gera_cad_campos_custom where numreg = '".$lista_plupa."')" ) );
            $cpid_campo = $lista_qry_plupa['id_campo'];
            $cpid_lupa = $lista_qry_plupa['id_campo_lupa'];
            $cpdescr_lupa = $lista_qry_plupa['campo_descr_lupa'];
            $cpchange = $lista_qry_plupa['evento_change'];
            $url_plupa = "javascript:window.opener.document.getElementById('edt".$cpid_campo."').value"." = '".$lista_qry_cadastro[$cpid_lupa]."'; ";
            $url_plupa .= "window.opener.document.getElementById('edtdescr".$cpid_campo."').value"." = '".$lista_qry_cadastro[$cpdescr_lupa]."'; ".$cpchange." window.close()";
            echo "<a href=\"#\" onclick=\"".$url_plupa."\">";
            echo "<img border=\"0\" width=15 height=15 alt=\"Selecionar...\" src=\"images/btn_modulo.png\">";
            echo "</a></td>";
        }
        if ( $lista_primeira_coluna == "1" && $lista_pexibedet == "1" && $lista_url_edita == "1" ){
            $lista_url_edita = "0";
            echo "<td width=\"15\" align=\"center\">";
            echo $lista_abre_funcao;
            echo "<img border=\"0\" alt=\"Clique aqui para ver detalhes...\" src=\"images/btn_det.png\"></a></td>";
        }
        if ( $lista_primeira_coluna == "1" && $lista_qry_gera_cad['nome_tabela'] == "is_atividade" ){
            echo "<td ".$lista_color2.">";
            echo "<img border=\"0\" width=15 height=15 title=\"".$texto_img."\" src=\"".$lista_qry_img['url_imagem']."\">";
            echo "</td>";
        }

        $lista_primeira_coluna = "0";
        if ( $lista_qry_gera_cad_campos['tipo_campo'] == "money" || $lista_qry_gera_cad_campos['tipo_campo'] == "calculado" || $lista_qry_gera_cad_campos['tipo_campo'] == "int" || $lista_qry_gera_cad_campos['tipo_campo'] == "float" || $lista_qry_gera_cad_campos['tipo_campo'] == "real" ){
            echo "<td align=\"right\" ".$lista_color.">";
        }else{
            echo "<td align=\"left\" ".$lista_color.">";
        }

        if ( $lista_qry_gera_cad_campos['tipo_campo'] == "lupa" || $lista_qry_gera_cad_campos['tipo_campo'] == "combobox" || $lista_qry_gera_cad_campos['tipo_campo'] == "lupa_popup" ){
            if ( strpos( $lista_qry_gera_cad_campos['sql_lupa'], "where" ) === false ){
                $lista_clausula = "where";
            }else{
                $lista_clausula = "and";
            }
            $lista_filtro_lupa = $lista_qry_gera_cad_campos['sql_lupa']." ".$lista_clausula." ".$pref_bd_ini.$lista_qry_gera_cad_campos['id_campo_lupa'].$pref_bd_fim." = '".$lista_qry_cadastro[$lista_qry_gera_cad_campos['id_campo']]."'";
            $lista_filtro_lupa = str_replace( "@s", "'", $lista_filtro_lupa );
            $lista_filtro_lupa = str_replace( "@vs_cpo_id_funcao", $lista_qry_cadastro['id_funcao'], $lista_filtro_lupa );
            $lista_lupa_wf = $lista_qry_cadastro['id_workflow'];
            if ( empty( $lista_lupa_wf ) ){
                $lista_lupa_wf = $lista_qry_cadastro['id_formulario_workflow'];
            }
            $lista_filtro_lupa = str_replace( "@vs_cpo_id_workflow", $lista_lupa_wf, $lista_filtro_lupa );
            $lista_filtro_lupa = str_replace( "@vs_id_sistema", $_SESSION['id_sistema'], $lista_filtro_lupa );
            $lista_qry_lupa = farray( query( $lista_filtro_lupa ) );
            echo str_replace( "\"", " ", $lista_qry_lupa[$lista_qry_gera_cad_campos['campo_descr_lupa']] );
        }
        else if ( $lista_qry_gera_cad_campos['tipo_campo'] == "date" ){
            $lista_vl_campo = str_replace( "\"", " ", $lista_qry_cadastro[$lista_qry_gera_cad_campos['id_campo']] );
            if ( $lista_vl_campo ){
                $lista_vl_campo_trat = substr( $lista_vl_campo, 8, 2 )."/".substr( $lista_vl_campo, 5, 2 )."/".substr( $lista_vl_campo, 0, 4 );
            }else{
                $lista_vl_campo_trat = "";
            }
            if ( $lista_vl_campo_trat == "01/01/1753" ){
                $lista_vl_campo_trat = "";
            }
            echo $lista_vl_campo_trat;
        }else if ( $lista_qry_gera_cad_campos['tipo_campo'] == "int" ){
            $lista_vl_campo = str_replace( "\"", " ", $lista_qry_cadastro[$lista_qry_gera_cad_campos['id_campo']] );
            if ( $lista_vl_campo ){
                echo number_format( $lista_vl_campo, 0, ",", "." );
            }
        }
        else if ( $lista_qry_gera_cad_campos['tipo_campo'] == "float" || $lista_qry_gera_cad_campos['tipo_campo'] == "real" ){
            $lista_vl_campo = str_replace( "\"", " ", $lista_qry_cadastro[$lista_qry_gera_cad_campos['id_campo']] );
            if ( $lista_vl_campo ){
                echo number_format( $lista_vl_campo, 2, ",", "." );
            }
        }else if ( $lista_qry_gera_cad_campos['tipo_campo'] == "money" ){
            $lista_vl_campo = str_replace( "\"", " ", $lista_qry_cadastro[$lista_qry_gera_cad_campos['id_campo']] );
            if ( $lista_vl_campo ){
                echo "R\$".number_format( $lista_vl_campo, 2, ",", "." );
            }
        }else if ( $lista_qry_gera_cad_campos['tipo_campo'] == "calculado" ){
            echo campo_calculado( $lista_qry_gera_cad_campos['id_funcao'], $lista_qry_gera_cad_campos['id_campo'], $lista_qry_cadastro );
        }
        else if ( $lista_qry_gera_cad_campos['tipo_campo'] == "memo" ){
            echo substr( $lista_qry_cadastro[$lista_qry_gera_cad_campos['id_campo']], 0, 30 )."...";
        }else if ( $lista_qry_gera_cad_campos['tipo_campo'] == "sim_nao" ){
            $lista_vl_campo = $lista_qry_cadastro[$lista_qry_gera_cad_campos['id_campo']];
            if ( $lista_vl_campo == "1" ){
                echo "S";
            }
            if ( $lista_vl_campo == "0" ){
                echo "N";
            }
        }else{
            echo $lista_qry_cadastro[$lista_qry_gera_cad_campos['id_campo']];
        }

        if ( $lista_pexibedet == "1" ){
            echo "</a>";
        }
        echo "</font></td>";
    }
    if ( $lista_pread == "0" && $lista_sn_bloquear_exclusao != "1" && $lista_pbloqexcluir != "1" ){
        echo "<td ".$lista_color."width=\"15\" align=\"center\">";
        echo "<a href=\"javascript:if(confirm(";
        echo "'Confirma exclusão deste registro ?')) { ";
        $url_exc = str_replace( "@sf", "'", str_replace( "@pnumreg2", $lista_qry_cadastro[$lista_pchave2], $lista_qry_gera_cad['url_excluir'] ) );
        $url_exc = str_replace( "@s", "'", $url_exc )." ".$lista_url_filtro;
        $url_exc = str_replace( "@pnumreg", $lista_qry_cadastro[$lista_pchave], $url_exc )." ".$lista_url_filtro;
        echo $url_exc;
        echo " }\"><img border=\"0\" alt=\"Clique aqui para excluir...\" src=\"images/btn_del.png\"></a></td>";
    }
    echo "</tr>";
    $lista_contador = $lista_contador + 1;
    if ( !( $lista_maxpag <= $lista_contador ) ){
        continue;
    }
    $lista_contador = 0;
    break;
    break;
}
echo "\n\n            </table>\n\n";

if ( $_SESSION['ip_desenvolvedor'] == "1" ){
    $lista_qrycad = farray( query( "select numreg from is_gera_cad where id_cad = '{$lista_id_funcao}'" ) );
    $lista_cad_url = "gera_cad_detalhe.php?pfuncao=cadastros_cad_lista&pnumreg=".$lista_qrycad['numreg'];
    $lista_url_open = "window.open(this.href,'".$lista_id_funcao.$lista_qrycad['numreg']."conf','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=850,height=550,top=".$posicao_janela.",left=".$posicao_janela."').focus(); return false;";
    echo "<br><center><a href=\"".$lista_cad_url."\" onclick=\"".$lista_url_open."\" ".$lista_tdstyle."><b>configurar Tela</b></a></center>";
}

if ( $psubdet ){
    echo "<div style=\"height:1px; width:100%; background-color:#E0E0E0; margin-top:2px; margin-bottom:2px;\"></div>";
}
echo "\n\n\n";
