<?php
@header( 'Pragma: no-cache' );
@header( 'Cache-Control: no-store, no-cache, must-revalidate' );
@header( 'Cache-Control: post-check=0, pre-check=0', false );
@session_start(  );
require_once( 'conecta.php' );
require_once( 'funcoes.php' );
require_once( 'functions.php' );
require_once( 'gera_cad_calc_custom.php' );



$sn_oculta_codigo_lupa_popup = getparam( 'oculta_codigo_lupa_popup' );

$a_postback = farray( query( 'select * from is_postback where numreg = \'' . $_GET['ppostback'] . '\'' ) );
$q_postback = farray( query( 'select * from is_postback where numreg = \'' . $_GET['ppostback'] . '\'' ) );

if ($q_postback['numreg']) {
    $a_postback = unserialize( $q_postback['post'] );
}

$id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : '';

if (empty($id_usuario)){
    $email_login = isset($_GET['pemail']) ? $_GET['pemail']: '' ;
    if ($email_login){
        $qry_login = farray(query( "select id_usuario from is_usuario where email = '{$email_login}'" ));
        $def_login = $qry_login['id_usuario'];
    }
    echo "<script>alert('Sua sessão expirou ! Por favor fazer o login o novamente.');</script>";
    include( "index.php" );
    exit( );
}

include( "gera_cad_detalhe_custom_ini.php" );

$filtro_licenca = " and (id_licenca is null or id_licenca = '' or id_licenca like '%PADRAO%' or id_licenca like '%".isset($_SESSION['lic_id'])."%')";

$nome_usuario 			= isset($_SESSION['nome_usuario'])			? $_SESSION['nome_usuario'] 		: '';
$id_perfil 				= isset($_SESSION['id_perfil'])				? $_SESSION['id_perfil'] 			: '';
$sn_bloquear_leitura 	= isset($_SESSION['sn_bloquear_leitura'])	? $_SESSION['sn_bloquear_leitura'] 	: '';
$sn_bloquear_edicao 	= isset($_SESSION['sn_bloquear_edicao'])	? $_SESSION['sn_bloquear_edicao'] 	: '';
$pread 					= isset($_GET['pread'])						? $_GET['pread'] 			: '';
$id_funcao 				= isset($_GET['pfuncao'])					? $_GET['pfuncao'] 			: '';
$pnumreg 				= isset($_GET['pnumreg'])					? $_GET['pnumreg'] 			: '';
$pnumreg2 				= isset($_GET['pnumreg2'])					? $_GET['pnumreg2'] 		: '';
$psubdet 				= isset($_GET['psubdet'])					? $_GET['psubdet'] 			: '';
$pfixo 					= isset($_GET['pfixo'])						? $_GET['pfixo'] 			: '';
$pnpai 					= isset($_GET['pnpai'])						? $_GET['pnpai'] 			: '';
$pidlupa 				= isset($_GET['pidlupa'])					? $_GET['pidlupa'] 			: '';
$pchave 				= isset($_GET['pchave'])					? $_GET['pchave'] 			: '';
$pchave_original 		= isset($_GET['pchave'])					? $_GET['pchave'] 			: '';
$pchave2 				= isset($_GET['pchave2'])					? $_GET['pchave2'] 			: '';
$pchave_original2 		= isset($_GET['pchave2'])					? $_GET['pchave2'] 			: '';
$pdiv 					= isset($_GET['pdiv'])						? $_GET['pdiv'] 			: '';
$pfecha 				= isset($_GET['pfecha'])					? $_GET['pfecha'] 			: '';
$pusuario_filtro 		= isset($_GET['pusuario_filtro'])			? $_GET['pusuario_filtro'] 	: '';
$cbxfiltro 				= isset($_GET['cbxfiltro'])					? $_GET['cbxfiltro'] 		: '';
$edtfiltro 				= isset($_GET['edtfiltro'])					? $_GET['edtfiltro'] 		: '';
$pos_ini 				= isset($_GET['pos_ini'])					? $_GET['pos_ini'] 			: '';
$cbxordem 				= isset($_GET['cbxordem'])					? $_GET['cbxordem'] 		: '';
$pgetcustom 			= isset($_GET['pgetcustom'])				? $_GET['pgetcustom'] 		: '';
$pbloqincluir 			= isset($_GET['pbloqincluir'])				? $_GET['pbloqincluir'] 	: '';
$ptitulo 				= isset($_GET['ptitulo'])					? $_GET['ptitulo'] 			: '';
$prefpai 				= isset($_GET['prefpai'])					? $_GET['prefpai'] 			: '';

if (empty($pchave)){
    $pchave = "numreg";
    $pchave_original = "numreg";
}else{
    $pchave = rawurldecode($pchave);
}

if (empty($pchave2)){
    $pchave2 = "";
    $pchave_original2 = "";
}else{
    $pchave2 = rawurldecode($pchave2);
}

//$url_retorno = "http://".$_SERVER['SERVER_NAME'].$_SESSION['porta_http'].$_SERVER['REQUEST_URI'];
$url_retorno = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].$_SESSION['porta_http'].$_SERVER['REQUEST_URI'];


$sql_bloqueio_cad = query("select * from is_perfil_funcao_bloqueio_cad where id_perfil = '".$id_perfil."' and id_cad = '".$id_funcao."' ");
$a_bloqueio_cad	  = farray($sql_bloqueio_cad);

if ( $a_bloqueio_cad['sn_bloqueio_ver'] == "1" ){
    echo "Seu perfil de acesso não tem permissão para acessar este cadastro ! Por favor contate o administrador do sistema.";
    exit( );
}
if ( $a_bloqueio_cad['sn_bloqueio_incluir'] == "1" && $pnumreg == "-1" ){
    echo "Seu perfil de acesso não tem permissão para incluir neste cadastro ! Por favor contate o administrador do sistema.";
    exit( );
}
if ( $a_bloqueio_cad['sn_bloqueio_editar'] == "1" ){
    $pread = "1";
}

$qry_gera_cad = farray( query( "select * from is_gera_cad where id_cad = '{$id_funcao}'" ) );
$qry_funcoes = farray( query( "select * from is_funcoes where id_funcao = '{$id_funcao}'" ) );
$qry_wf = farray( query( "select * from is_workflow_fase where id_workflow = '{$id_funcao}' order by nome_fase" ) );
$fonte_odbc = $qry_gera_cad['fonte_odbc'];

if ($fonte_odbc) {
    if ($cbxfiltro) {
        $cbxfiltro_trat = '"'.$cbxfiltro.'"';
    }
    if ($pchave) {
        $pchave_trat = 	'"'.$pchave.'"';
    }
    if ($pchave2) {
        $pchave2_trat = '"'.$pchave2.'"';
    }
}else{
    $cbxfiltro_trat = $cbxfiltro;
    if ($pchave) {
        $pchave_trat = $pchave;
    }
    if ($pchave2) {
        $pchave2_trat = $pchave2;
    }
}

if ($pidlupa) {
    $qry_numreg = farray( query( 'select ' . $pchave . ' from ' . $qry_gera_cad['nome_tabela'] . ( ' where ' . $pref_bd . $pidlupa . $pref_bd . ' = \'' . $pnumreg . '\'' ) ) );
    $pnumreg = $qry_numreg[str_replace( '`', '', $pchave_original )];
}



$filtro_geral = $qry_gera_cad['sql_filtro'];
$filtro_geral = str_replace( "@vs_id_usuario"		, $vs_id_usuario		, $filtro_geral );
$filtro_geral = str_replace( "@vs_id_perfil"		, $vs_id_perfil			, $filtro_geral );
$filtro_geral = str_replace( "@vs_id_empresa"		, $vs_id_empresa		, $filtro_geral );
$filtro_geral = str_replace( "@vs_dt_hoje"			, date( "Y-m-d" )		, $filtro_geral );
$filtro_geral = str_replace( "@igual"				, "="					, $filtro_geral );
$filtro_geral = str_replace( "@dif"					, "<>"					, $filtro_geral );
$filtro_geral = str_replace( "@in"					, " in "				, $filtro_geral );
$filtro_geral = str_replace( "@maior"				, ">"					, $filtro_geral );
$filtro_geral = str_replace( "@menor"				, "<"					, $filtro_geral );
$filtro_geral = str_replace( "@sf"					, "'"					, $filtro_geral );
$filtro_geral = str_replace( "@s"					, "'"					, $filtro_geral );
$filtro_geral = str_replace( "@and"					, " and "				, $filtro_geral );
$filtro_geral = str_replace( "@or"					, " or "				, $filtro_geral );
$filtro_geral = str_replace( "@between"				, " between "			, $filtro_geral );
$filtro_geral = str_replace( "@pctlike"				, "%"					, $filtro_geral );
$filtro_geral = str_replace( "@like"				, " like "				, $filtro_geral );
if (strpos( $filtro_geral, 'where' ) === false) {
    $clausula = 'where';
}else{
    $clausula = 'and';
}
if ($pchave2) {
    $qry_cadastro = farray( query( $filtro_geral . ( ' ' . $clausula . ' ' . $pchave_trat . ' = \'' . $pnumreg . '\' and ' . $pchave2_trat . ' = \'' . $pnumreg2 . '\'' ), 1, $fonte_odbc ), $fonte_odbc );
}else {
    $qry_cadastro = farray( query( $filtro_geral . ( ' ' . $clausula . ' ' . $pchave_trat . ' = \'' . $pnumreg . '\'' ), 1, $fonte_odbc ), $fonte_odbc );
}

$qry_bloqueios = farray( query( 'select * from is_perfil_funcao_bloqueio where id_perfil = \'' . $id_perfil . '\' and id_funcao = \'' . $id_funcao . '\'' ) );
if ($qry_bloqueios['sn_bloqueio_editar'] == '1') {
    $pread = '1';
}
if ($qry_bloqueios['sn_bloqueio_abrir'] == '1') {
    exit(  );
}
$sql_bloqueio = '';
if (( $pnumreg != '-1' && $sn_bloquear_edicao == '1' )) {
    if (( $qry_gera_cad['nome_tabela'] == 'is_atividade' && $qry_cadastro['id_usuario_resp'] != $id_usuario )) {
        $pread = '1';
    }
}

$campos_bloqueados = '';
$q_bloqueio_campos = query( 'select * from is_perfil_funcao_bloqueio_campos where id_perfil = \'' . $_SESSION['id_perfil'] . '\' and id_cad = \'' . $id_funcao . '\' and sn_bloqueio_ver = 1' );

while ($a_bloqueio_campos = farray( $q_bloqueio_campos )) {
    $campos_bloqueados = $campos_bloqueados . '\'' . $a_bloqueio_campos['id_campo'] . '\',';
}

if ($campos_bloqueados) {
    $campos_bloqueados = 'and ( not id_campo in (' . substr( $campos_bloqueados, 0, strlen( $campos_bloqueados ) - 1 ) . '))';
}

$titulo_tela = '';
$sql_gera_cad_campos = query( '(select * from is_gera_cad_campos where id_funcao = \'' . $id_funcao . '\' and exibe_titulo = 1 ' . $filtro_licenca . ' ' . $campos_bloqueados . ' ) union all (select * from is_gera_cad_campos_custom where id_funcao = \'' . $id_funcao . '\' and exibe_titulo = 1 ' . $filtro_licenca . ' ' . $campos_bloqueados . ' ) order by ordem' );

while ($qry_gera_cad_campos = farray( $sql_gera_cad_campos )) {
    $titulo_tela .= $qry_cadastro[$qry_gera_cad_campos['id_campo']] . ' ';
}

if ($psubdet) {
    $qry_gera_cad_sub = farray( query( 'select * from is_gera_cad_sub where numreg = \'' . $psubdet . '\'' ) );
    if ($qry_gera_cad_sub['numreg']) {
        $qry_gera_cad_mestre = farray( query( 'select * from is_gera_cad where id_cad = \'' . $qry_gera_cad_sub['id_funcao_mestre'] . '\'' ) );
    } else {
        $qry_gera_cad_mestre = farray( query( 'select * from is_gera_cad where id_cad = \'' . $psubdet . '\'' ) );
    }
    $qry_mestre = farray( query( 'select * from ' . $qry_gera_cad_mestre['nome_tabela'] . ' where numreg = \'' . $pnpai . '\'' ) );
    $qry_gera_cad_mestre_cpo = farray( query( '(select id_campo from is_gera_cad_campos where exibe_titulo = 1 and id_funcao = \'' . $qry_gera_cad_sub['id_funcao_mestre'] . '\') union all (select id_campo from is_gera_cad_campos where exibe_titulo = 1 and id_funcao = \'' . $qry_gera_cad_sub['id_funcao_mestre'] . '\')' ) );
    $qry_mestre = farray( query( 'select * from ' . $qry_gera_cad_mestre['nome_tabela'] . ' where numreg = ' . $pnpai ) );

    if ($qry_gera_cad_mestre_cpo['id_campo']) {
        $titulo_mestre = ' - ' . $qry_mestre[$qry_gera_cad_mestre_cpo['id_campo']];
    }
    $def_sub_campo_mestre = $qry_gera_cad_sub['campo_mestre'];
    $def_sub_campo_detalhe = $qry_gera_cad_sub['campo_detalhe'];
}

$campos_textohtm = '';
$sql_gera_cad_campos_htm = query( '(select * from is_gera_cad_campos where id_funcao = \'' . $id_funcao . '\' ' . $filtro_licenca . ' ' . $campos_bloqueados . '  and id_campo like \'textohtm%\') union all (select * from is_gera_cad_campos_custom where id_funcao = \'' . $id_funcao . '\' ' . $filtro_licenca . ' ' . $campos_bloqueados . ' and id_campo like \'textohtm%\') order by  nome_aba, exibe_formulario, nome_grupo, ordem' );

while ($qry_gera_cad_campos_htm = farray( $sql_gera_cad_campos_htm )) {
    $campos_textohtm .= 'edt' . $qry_gera_cad_campos_htm['id_campo'] . ',';
}

$campos_textohtm .= 'edtpeca';
?>
    <!DOCTYPE HTML>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php	include( "gera_cad_detalhe_custom_head.php" );	?>
        <title>:: CRM ICASA::
            <?php
            echo isset($ptitulo) ? $ptitulo." ": $qry_gera_cad['titulo']." ";
            echo $pnumreg != "-1" ? $titulo_tela." (".$pnumreg.")" : "Inclusão";
            echo $titulo_mestre;
            ?>
        </title>
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />

        <link href="estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link href="estilos_css/estilo_aba.css" rel="stylesheet" type="text/css" />
        <link href="css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="estilos_css/cadastro.css">


        <style type="text/css">
            body {margin-left: 0px;	margin-top: 0px;	margin-right: 0px;	margin-bottom: 0px;}
            fieldset.section {padding: 10px 15px;}
            fieldset.section>legend {margin: 0;line-height: 1.1;font-size: 12px;}
            section {margin-bottom: 40px;}
            fieldset {border: 1.5px solid lightgray;}
            legend{display: block;width: auto;padding: 0;margin-bottom: 20px;line-height: inherit;color: #333;border: 0;}
        </style>




        <script language="JavaScript" src="js/ajax_usuario.js"></script>
        <script language="JavaScript" src="js/ajax_menus.js"></script>
        <script language="JavaScript" src="js/ajax_gera_cad.js"></script>
        <script language="JavaScript" src="js/valida.js"></script>
        <script type="text/javascript" src="js/function.js"></script>
        <script src="//code.jquery.com/jquery-1.12.4.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.4.1.js"></script>

        <script type="text/javascript" src="js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="js/jquery.ui.datepicker-pt-BR.js"></script>
        <script language="javascript" src="tinymce/jscripts/tiny_mce/tiny_mce_gzip.php"></script>
        <script language="JavaScript">
            $(document).ready(function(){
                $.datepicker.setDefaults($.datepicker.regional['pt-BR']);
                var expanded = false;
                $(".content").hide();
                $(".content:first").show();
                $("a.tab").dblclick(function () {
                    $(".active").removeClass("active");
                    $(this).addClass("active");
                    var content_show = $(this).attr("href");
                    $("#"+content_show).fadeIn();
                    return false;
                });
                $("a.tab").click(function () {
                    if($(this).attr("href") != 'expand_all'){
                        $(".active").removeClass("active");
                        $(this).addClass("active");
                        $(".content").hide();
                        var content_show = $(this).attr("href");
                        $("#"+content_show).fadeIn();
                        expanded = false;
                    }else{
                        if(expanded == false){
                            expanded = true;
                            $(".content").fadeIn();
                        }else{
                            expanded = false;
                            $(".content").fadeOut(function(){$(".content:first").show(); $(".active").removeClass("active"); $(".tabs a:first").addClass("active");});
                        }
                    }
                    return false;
                });
                if (<?php echo $qry_gera_cad['sn_maximizado'];?> == "1" ){
                    function maximizar() {
                        window.moveTo (-4,-4);
                        window.resizeTo (screen.availWidth + 8, screen.availHeight + 8);
                    }
                    maximizar();
                }
            });
        </script>
        <script language="javascript" type="text/javascript">
            tinyMCE.init({
                mode : "exact",
                elements : "<?php echo $campos_textohtm;?>",
                theme : "advanced",
                plugins : "style,layer,table,save,advhr,advimage,advlink,insertdatetime,preview,zoom,searchreplace,contextmenu,paste",
                theme_advanced_buttons1_add_before : "newdocument,separator",
                theme_advanced_buttons1_add : "fontselect,fontsizeselect,separator,forecolor,backcolor",
                theme_advanced_buttons2_add : "separator,",
                theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator",
                theme_advanced_buttons3_add_before : "tablecontrols,separator,search,replace,separator,preview",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                content_css : "",
                plugi2n_insertdate_dateFormat : "%d-%m-%Y",
                plugi2n_insertdate_timeFormat : "%H:%M:%S",
                external_link_list_url : "example_link_list.js",
                external_image_list_url : "example_image_list.js",
                flash_external_list_url : "example_flash_list.js",
                file_browser_callback : "fileBrowserCallBack",
                paste_use_dialog : false,
                theme_advanced_resizing : true,
                theme_advanced_resize_horizontal : false,
                theme_advanced_link_targets : "ifrm=ifrm",
                paste_auto_cleanup_on_paste : true,
                paste_convert_headers_to_strong : false,
                paste_strip_class_attributes : "all",
                paste_remove_spans : false,
                force_br_newlines : true,
                force_p_newlines : false,
                forced_root_block : '',
                language : "pt_br"
            });
            function fileBrowserCallBack(field_name, url, type, win) {
                alert("Filebrowser callback: field_name: " + field_name + ", url: " + url + ", type: " + type);
                win.document.forms[0].elements[field_name].value = "someurl.htm";
            }
        </script>
        <script language="JavaScript">
            function verificar() {
                var mensagem = "Atenção : ";
                <?php echo str_replace( '<br />', '', $qry_gera_cad['validacoes_js'] );
                if (strtoupper( $qry_gera_cad['tipo'] ) == 'WORKFLOW') {
                    echo 'var oWF = document.getElementById(\'cbxopworkflow\'); ';
                }
                $sql_gera_cad_campos = query( "(select * from is_gera_cad_campos where id_funcao = '{$id_funcao}' {$filtro_licenca} {$campos_bloqueados} ) union all (select * from is_gera_cad_campos_custom where id_funcao = '{$id_funcao}' {$filtro_licenca} {$campos_bloqueados} ) order by  nome_aba, exibe_formulario, nome_grupo, ordem" );
                while($qry_gera_cad_campos = farray($sql_gera_cad_campos)){
                    $lbl_nome_campo = $qry_gera_cad_campos['nome_campo'];
                    $lbl_nome_aba 	= $qry_gera_cad_campos['nome_aba'];
                    include("gera_cad_detalhe_custom_label.php");
                    include("gera_readonly_custom.php");
                    $exibir_formulario = $qry_gera_cad_campos['exibe_formulario'];
                    if (( $exibir_formulario == '1' && ( ( ( $qry_gera_cad_campos['tipo_campo'] == 'money' || $qry_gera_cad_campos['tipo_campo'] == 'real' ) || $qry_gera_cad_campos['tipo_campo'] == 'float' ) || $qry_gera_cad_campos['tipo_campo'] == 'int' ) )) {
                        echo 'var valid_' . $qry_gera_cad_campos['id_campo'] . ' = document.getElementById("edt' . $qry_gera_cad_campos['id_campo'] . '").value; valid_' . $qry_gera_cad_campos['id_campo'] . ' = valid_' . $qry_gera_cad_campos['id_campo'] . '.replace(/[.]/g,""); valid_' . $qry_gera_cad_campos['id_campo'] . ' = valid_' . $qry_gera_cad_campos['id_campo'] . '.replace(",","."); if(isNaN(valid_' . $qry_gera_cad_campos['id_campo'] . ')) {mensagem = mensagem + ' . '\'Campo ' . $lbl_nome_campo . '(Aba ' . $lbl_nome_aba . ') com valor incorreto. ' . '\'; }';
                    }
                    if (( strtoupper( $qry_gera_cad['tipo'] ) == 'WORKFLOW' && $exibir_formulario == '1' )) {
                        if ($pnumreg == '-1') {
                            $fase_compara = $qry_wf['id_fase'];
                        } else {
                            $fase_compara = $qry_cadastro['id_fase_workflow'];
                        }
                        $exibe_fase_wf = strpos( $qry_gera_cad_campos['exibe_fases'] . ',', $fase_compara . ',' );
                        if ($exibe_fase_wf === false) {
                            $exibir_formulario = '0';
                        }
                    }
                    if (( $exibir_formulario == '1' && $qry_gera_cad_campos['sn_obrigatorio'] == '1' )) {
                        if (strtoupper( $qry_gera_cad['tipo'] ) == 'WORKFLOW') {
                            if ($pnumreg != '-1') {
                                echo 'if ((oWF.checked == false) && (document.getElementById(\'edt' . str_replace( ' ', '_', $qry_gera_cad_campos['id_campo'] ) . '\')' . '.value == "")) { mensagem = mensagem + "' . 'Campo ' . $lbl_nome_campo . '(Aba ' . $lbl_nome_aba . ') em branco. "; } ';
                                continue;
                            }
                            continue;
                        }
                        echo 'if (document.getElementById(\'edt' . str_replace( ' ', '_', $qry_gera_cad_campos['id_campo'] ) . '\')' . '.value == "") { mensagem = mensagem + "' . 'Campo ' . $lbl_nome_campo . '(Aba ' . $lbl_nome_aba . ') em branco. "; } ';
                        continue;
                    }
                }
                ?>
                if (mensagem == "Atenção : "){
                    document.cad.submit();
                    return true;
                }else{
                    alert(mensagem);
                    return false;
                }
            }
        </script>
        <script language="JavaScript">
            function alteracoes() {
                var mensagem = "As alterações serão perdidas : ";
                if (mensagem == "As alterações serão perdidas : ") {
                    return "";
                }else{
                    if (document.getElementById('snsalvar').value == '') {
                        return mensagem+' !';
                    } else {
                        return "";
                    }
                }
            }
        </script>
    </head>
<body  bgcolor="#F2F2F2" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" onbeforeunload="msg = alteracoes(); if (msg != '') { return msg; }">
<div id="principal_detalhes" >
    <div id="menu_horiz">
        <table>
            <tr>
                <td>&nbsp;
                    <span style="font-size:16px; font-weight: bold;">
                                    <?php
                                    if ($ptitulo){echo $ptitulo; }else{ echo $qry_gera_cad['titulo'];}
                                    echo " : ".$titulo_mestre;
                                    if ($titulo_mestre){ echo " - "; }
                                    $pnumreg != "-1" ? $titulo_tela : "Inclusão";
                                    ?>
                            </span>
                </td>
            </tr>
        </table>
    </div>
    <div id="conteudo_detalhes"> </div>
    <form class="form-group" method="post" name="cad" id="cad" action="gera_cad_post.php" enctype='multipart/form-data' onsubmit="return verificar();">
<?php
require_once( 'gera_cad_detalhe_custom_hidden.php' );?>
    <input type="hidden" name="snsalvar" 		id="snsalvar" 		value=" " />
    <input type="hidden" name="pgetcustom" 		id="pgetcustom" 	value="<?php echo $pgetcustom;?>" />
    <input type="hidden" name="pbloqincluir" 	id="pbloqincluir" 	value="<?php echo $pbloqincluir;?>" />
    <input type="hidden" name="pbloqexcluir" 	id="pbloqexcluir" 	value="<?php echo $pbloqexcluir;?>" />
    <input type="hidden" name="ptitulo" 		id="ptitulo" 		value="<?php echo $ptitulo;?>" />
    <input type="hidden" name="snincluirnovo" 	id="snincluirnovo"	value="0" />
    <input type="hidden" name="pread" 			id="pread" 			value="<?php echo $pread;?>" />
    <input type="hidden" name="pfuncao" 		id="pfuncao"  		value="<?php echo $id_funcao;?>" />
    <input type="hidden" name="pnumreg" 		id="pnumreg" 		value="<?php echo $pnumreg;?>" />
    <input type="hidden" name="pnumreg2" 		id="pnumreg2" 		value="<?php echo $pnumreg2;?>" />
    <input type="hidden" name="psubdet" 		id="psubdet" 		value="<?php echo $psubdet;?>" />
    <input type="hidden" name="pfixo" 			id="pfixo" 			value="<?php echo $pfixo;?>" />
    <input type="hidden" name="pnpai" 			id="pnpai" 			value="<?php echo $pnpai;?>" />
    <input type="hidden" name="pidlupa" 		id="pidlupa" 		value="<?php echo $pidlupa;?>" />
    <input type="hidden" name="pchave" 			id="pchave" 		value="<?php echo $pchave_original;?>" />
    <input type="hidden" name="pchave2" 		id="pchave2" 		value="<?php echo $pchave_original2;?>" />
    <input type="hidden" name="prefpai" 		id="prefpai" 		value="<?php echo $prefpai;?>" />
    <input type="hidden" name="pdiv" 			id="pdiv" 			value="<?php echo $pdiv;?>" />
    <input type="hidden" name="pfecha" 			id="pfecha" 		value="<?php echo $pfecha;?>" />
    <input type="hidden" name="pusuario_filtro" id="pusuario_filtro" value="<?php echo $pusuario_filtro;?>" />
    <input type="hidden" name="pcbxfiltro" 		id="pcbxfiltro" 	value="<?php echo $cbxfiltro;?>" />
    <input type="hidden" name="pedtfiltro" 		id="pedtfiltro" 	value="<?php echo $edtfiltro;?>" />
    <input type="hidden" name="ppos_ini" 		id="ppos_ini" 		value="<?php echo $pos_ini;?>" />
    <input type="hidden" name="pcbxordem" 		id="pcbxordem" 		value="<?php echo $cbxordem;?>" />
    <input type="hidden" name="url_retorno" 	id="url_retorno" 	value="<?php echo $url_retorno;?>" />
    <input type="hidden" name="popc" 			id="popc"			 value="alterar" />
    <table width="100%"  border="0" cellspacing="1" cellpadding="1" bgcolor="#FFFFFF"  height=500>
    <tr>
    <td width=100 align="left" valign="top" bgcolor="#345C7D" >
        <div id="menu_btn">
            <a href="javascript:window.location = '<?php echo $url_retorno ;?>' ">
                <img border=0 src="images/menu_principal.png" align="middle" width="14" height="13" style="padding-right:3px; padding-left:3px;" />
                Cad.Principal
            </a>
        </div>
        <?php
        $detalhes_bloqueados = '';
        $q_bloqueio_det = query( 'select * from is_perfil_funcao_bloqueio_mestre_det where id_perfil = \'' . $_SESSION['id_perfil'] . '\' and sn_bloqueio_ver = 1' );
        while ($a_bloqueio_det = farray( $q_bloqueio_det )) {
            $detalhes_bloqueados = $detalhes_bloqueados . '\'' . $a_bloqueio_det['numreg_sub'] . '\',';
        }
        if ($detalhes_bloqueados) {
            $detalhes_bloqueados = 'and ( not numreg in (' . substr( $detalhes_bloqueados, 0, strlen( $detalhes_bloqueados ) - 1 ) . '))';
        }
        $sql_gera_cad_sub = query( 'select * from is_gera_cad_sub where id_funcao_mestre = \'' . $id_funcao . '\' and (exibir_apos_campo is NULL or exibir_apos_campo = \'\') ' . $filtro_licenca . ' ' . $detalhes_bloqueados . ' and id_sistema like \'%' . $_SESSION['id_sistema'] . '%\' order by ordem' );
        $primeira_sub = 0;
        while ($qry_gera_cad_sub = farray( $sql_gera_cad_sub )) {
            $url_pread = '';
            $a_bloqueio_det_edit = farray( query( 'select * from is_perfil_funcao_bloqueio_mestre_det where id_perfil = \'' . $_SESSION['id_perfil'] . '\' and numreg_sub = \'' . $qry_gera_cad_sub['numreg'] . '\' and sn_bloqueio_editar = 1' ) );
            if ($a_bloqueio_det_edit['numreg_sub']) {
                $url_pread = '&pread=S';
            }
            include( 'gera_cad_detalhe_custom_sub_read.php' );
            if ($exibe_mestre_detalhe != '0') {
                if ($pnumreg != 0 - 1) {
                    $filtro_det = str_replace( '@cpomestre', removeacentos( $qry_cadastro[$qry_gera_cad_sub['campo_mestre']] ), $qry_gera_cad_sub['filtro_detalhe'] );
                    $url_sub = 'javascript: exibe_programa(\'' . $filtro_det . '&pdrilldown=1&psubdet=' . $qry_gera_cad_sub['numreg'] . '&pnpai=' . $qry_cadastro[str_replace( '%20', ' ', $pchave_original )] . $url_pread . '\');';
                    $cor_sub = '#345C7D';
                    $primeira_sub = 1;
                }else {
                    $filtro_det = str_replace( '@cpomestre', $qry_cadastro[$qry_gera_cad_sub['campo_mestre']], $qry_gera_cad_sub['filtro_detalhe'] );
                    $url_sub = 'javascript: alert(\'Por favor, primeiro você deve salvar o registro antes de executar esta função !\');';
                    $cor_sub = '#C0C0C0';
                }
                ?>
                <div id="menu_btn">
                    <?php
                    if (empty( $qry_funcoes['url_imagem_menu'] )) {
                        $ico_img = 'images/menu_sub.png';
                    }else {
                        $ico_img = $qry_funcoes['url_imagem_menu'];
                    } ?>
                    <a href="<?php echo $url_sub ;?>">
                    <span style="font-family:Verdana; 	font-size:10px; color:'<?php echo $cor_sub ;?>">
                            <img border="0" src="<?php echo $ico_img  ;?>" align="middle" width="14" height="13" style="padding-right:3px; padding-left:3px;" />
                        <?php echo $qry_gera_cad_sub['nome_sub'] ;?>
                    </span>
                    </a>
                </div>
                <?php continue;
            }
        }?>
        <div id="menu_btn">
            <a href="javascript:window.close();">
                <img  border=0 src="images/menu_sair.png" align="middle" width="14" height="13" style="padding-right:3px; padding-left:3px;" />
                Fechar
            </a>
        </div>
    </td>
    <td valign="top">
    <div name="div_programa" id="div_programa" class="tabbed_box" >
    <div class="tabbed_area">
    <ul class="tabs">
        <?php
        $aba_ativa = 'tab active';
        $q_abas = query( '(select distinct nome_aba from is_gera_cad_campos where id_funcao = \'' . $id_funcao . '\' and exibe_formulario = 1 ' . $filtro_licenca . ' ' . $campos_bloqueados . ' ) union all (select distinct nome_aba from is_gera_cad_campos_custom where id_funcao = \'' . $id_funcao . '\' and exibe_formulario = 1 ' . $filtro_licenca . ' ' . $campos_bloqueados . ' ) order by nome_aba' );
        $nome_aba_atual = '@';
        while ($a_abas = farray( $q_abas )) {
            include( 'gera_cad_detalhe_custom_aba.php' );
            if ($exibe_aba != 'N') {
                $nome_aba_trat = strtolower( str_replace( '-', '', str_replace( '.', '', str_replace( ' ', '_', tirarAcentos( $a_abas['nome_aba'] ) ) ) ) );
                //echo $nome_aba_trat;die
                if ($nome_aba_atual != $a_abas['nome_aba']) {
                    echo '<li id="li_' . $nome_aba_trat . '"><a href="content_' . $nome_aba_trat . '" title="' . $a_abas['nome_aba'] . '" class="' . $aba_ativa . '">' . $a_abas['nome_aba'] . '</a></li>';
                    $nome_aba_atual = $a_abas['nome_aba'];
                }
                $aba_ativa = 'tab';
                continue;
            }
        } ?>
    </ul>
<?php
$sql_gera_cad_campos = query( '(select * from is_gera_cad_campos where id_funcao = \'' . $id_funcao . '\' ' . $filtro_licenca . ' ' . $campos_bloqueados . ' ) union all (select * from is_gera_cad_campos_custom where id_funcao = \'' . $id_funcao . '\' ' . $filtro_licenca . ' ' . $campos_bloqueados . ' ) order by  nome_aba, exibe_formulario, nome_grupo, ordem' );
$nome_aba = '';
$nome_grupo = '';
$id_fase_workflow = '';
$quebra_linha = '';
$old_quebra_linha = '1';
$primeira_quebra = '1';
while ($qry_gera_cad_campos = farray( $sql_gera_cad_campos )) {
    if ($nome_aba != $qry_gera_cad_campos['nome_aba']) {
        if ($nome_aba) {
            echo '</table></div>';
        }
        $nome_aba_trat = strtolower( str_replace( '-', '', str_replace( '.', '', str_replace( ' ', '_', tirarAcentos( $qry_gera_cad_campos['nome_aba'] ) ) ) ) );
        echo '<div id="content_' . $nome_aba_trat . '" class="content"><table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#FFFFFF">';
        $nome_aba = $qry_gera_cad_campos['nome_aba'];
    }
    $vl_campo = str_replace( '"', '&quot;', $qry_cadastro[$qry_gera_cad_campos['id_campo']] );
    if ($q_postback['numreg']) {
        $vl_campo = str_replace( '"', '&quot;', $a_postback['edt' . $qry_gera_cad_campos['id_campo']] );
    }
    if (( empty( $vl_campo ) && $vl_campo != '0' )) {
        $padrao = $qry_gera_cad_campos['valor_padrao'];
        if ($padrao) {
            $padrao = str_replace( '@vs_id_usuario', $id_usuario, $padrao );
            $padrao = str_replace( '@vs_id_perfil', $id_perfil, $padrao );
            $padrao = str_replace( '@vs_dt_hoje', date( 'Y-m-d' ), $padrao );
            $padrao = str_replace( '@vs_hr_hms', date( 'H:i:s' ), $padrao );
            $padrao = str_replace( '@vs_hr_hm', date( 'H:i' ), $padrao );
            $padrao = trim( $padrao );
            $pos_padrao = strpos( $padrao, '@vs_max_numreg' );
            if ($pos_padrao === false) {
            }else {
                $padrao = '';
            }
            $pos_padrao = strpos( $padrao, '@vs_max_cotacao' );
            if ($pos_padrao === false) {
            } else {
                $padrao = '';
            }
            $pos_padrao = strpos( $padrao, '@mestre_' );
            if ($pos_padrao === false) {
            } else {
                if ($psubdet) {
                    $cpomestre = str_replace( '@mestre_', '', $padrao );
                    if ($cpomestre == $def_sub_campo_detalhe) {
                        $padrao = $qry_mestre[$def_sub_campo_mestre];
                    } else {
                        $padrao = $qry_mestre[$cpomestre];
                    }
                } else {
                    $padrao = '';
                }
            }
        }
        $vl_campo = $padrao;
    }
    $tipo = $qry_gera_cad_campos['tipo_campo'];
    $id_campo = str_replace( ' ', '_', $qry_gera_cad_campos['id_campo'] );
    $q_bloqueio_campos_editar = farray( query( 'select * from is_perfil_funcao_bloqueio_campos where id_perfil = \'' . $_SESSION['id_perfil'] . '\' and id_cad = \'' . $id_funcao . '\' and id_campo = \'' . $id_campo . '\'' ) );
    if (empty( $vl_campo )) {
        if ($q_bloqueio_campos_editar['valor_padrao']) {
            $vl_campo = $q_bloqueio_campos_editar['valor_padrao'];
        }
    }
    $exibir_formulario = $qry_gera_cad_campos['exibe_formulario'];
    $editavel = $qry_gera_cad_campos['editavel'];
    if (( $qry_gera_cad_campos['editavel_inclusao'] == '1' && $pnumreg != '-1' )) {
        $editavel = '0';
    }
    if (( $qry_gera_cad_campos['editavel_bloq_detalhe'] == '1' && $pnpai != '' )) {
        $editavel = '0';
    }
    if (( strtoupper( $qry_gera_cad['tipo'] ) == 'WORKFLOW' && $exibir_formulario == '1' )) {
        if ($pnumreg == '-1') {
            $fase_compara = $qry_wf['id_fase'];
        } else {
            $fase_compara = $qry_cadastro['id_fase_workflow'];
        }
        $exibe_fase_wf = strpos( $qry_gera_cad_campos['exibe_fases'] . ',', $fase_compara . ',' );
        if ($exibe_fase_wf === false) {
            $exibir_formulario = '0';
        }
        $edita_fase_wf = strpos( $qry_gera_cad_campos['edita_fases'] . ',', $fase_compara . ',' );
        if ($edita_fase_wf === false) {
            $editavel = '0';
        }
    }
    $q_bloqueio_campos_editar = farray( query( 'select * from is_perfil_funcao_bloqueio_campos where id_perfil = \'' . $_SESSION['id_perfil'] . '\' and id_cad = \'' . $id_funcao . '\' and id_campo = \'' . $id_campo . '\' and sn_bloqueio_editar = 1' ) );
    if ($q_bloqueio_campos_editar['id_campo']) {
        $editavel = '0';
    }
    if ($editavel == '1') {
        $readonly = '';
    } else {
        $readonly = 'readonly style="background-color:#CCCCCC" ';
    }
    include( 'gera_readonly_custom.php' );
    if (( $_SESSION['ip_consultor'] == '1' && ( ( $id_funcao == 'modulos_cad_lista' || $id_funcao == 'funcoes_cad_lista' ) || $id_funcao == 'gera_cad_sub_lista' ) )) {
        if ($_SESSION['ip_desenvolvedor'] == '') {
            if ($qry_gera_cad_campos['id_campo'] != 'id_sistema') {
                $readonly = 'readonly style="background-color:#CCCCCC" ';
                $exibir_formulario = '0';
            }
        }
    }
    if (( ( ( $_SESSION['sn_usa_autenticacao_ad'] == '1' && $id_funcao == 'usuario_cad_lista' ) && $_SESSION['ip_desenvolvedor'] == '' ) && $_SESSION['ip_consultor'] == '' )) {
        if ($qry_gera_cad_campos['id_campo'] == 'senha') {
            $readonly = 'readonly style="background-color:#CCCCCC" ';
            $exibir_formulario = '0';
        }
    }
    if ($pread == '1') {
        $readonly = 'readonly style="background-color:#CCCCCC" ';
    }
    if ($exibir_formulario == '1') {
        $cor_linha = '';
        if ($id_fase_workflow != $qry_gera_cad_campos['id_fase_workflow']) {
            $id_fase_workflow = $qry_gera_cad_campos['id_fase_workflow'];
            if ($primeira_quebra != '1') {
                echo '<tr><td colspan="4" bgcolor="#FFFFFF">&nbsp;</td></tr>';
            } else {
                $primeira_quebra = '0';
            }
            $qry_fase_wf = farray( query( 'select * from is_workflow_fase where id_fase=\'' . $id_fase_workflow . '\' and id_workflow = \'' . $id_funcao . '\'' ) );
            $qry_log_wf = farray( query( 'select * from is_workflow_log where id_atividade=\'' . $qry_cadastro['numreg'] . '\' and id_fase_workflow = \'' . $id_fase_workflow . '\'' ) );
            $qry_usu_wf = farray( query( 'select * from is_usuario where numreg=\'' . $qry_log_wf['id_usuario_resp'] . '\'' ) );
            if ($qry_fase_wf['nome_fase']) {
                echo '<tr><td bgcolor="#345C7D" colspan="4"><div align="center"> <span style="font-size:11px; color:#FFFFFF"' . ' title="' . 'Dt.Início : ' . datagetbd( $qry_log_wf['dt_inicio'] ) . ' ' . $qry_log_wf['hr_inicio'] . ' - Prazo : ' . datagetbd( $qry_log_wf['dt_prazo'] ) . ' ' . $qry_log_wf['hr_prazo'] . ' - Dt.Conclusão : ' . datagetbd( $qry_log_wf['dt_fim'] ) . ' ' . $qry_log_wf['hr_fim'] . '">';
                echo 'Fase : ' . $qry_fase_wf['nome_fase'] . '&nbsp;- Responsável : ' . $qry_usu_wf['nome_usuario'];
                echo '</span></div></td></tr>';
                echo '<tr><td colspan="4" bgcolor="#FFFFFF">&nbsp;</td></tr>';
            }
        }
        if ($nome_grupo != $qry_gera_cad_campos['nome_grupo']) {
            $nome_grupo = $qry_gera_cad_campos['nome_grupo'];
            if ($primeira_quebra != '1') {
                echo '<tr><td colspan="4" bgcolor="#FFFFFF">&nbsp;</td></tr>';
            } else {
                $primeira_quebra = '0';
            }
            echo '<tr><td bgcolor="#dbe9f4" class="sub_tit" colspan="4"><div align="left">';
            echo '&nbsp;&nbsp;' . $nome_grupo;
            echo '</div></td></tr>';
            echo '<tr><td colspan="4" ' . $cor_linha . ' >&nbsp;</td></tr>';
        }
        if ($old_quebra_linha == '1') {
            echo '<tr id="tr_' . $qry_gera_cad_campos['id_campo'] . '"><td ' . $cor_linha . '>&nbsp;</td>';
            echo '<td align="right" width="18%" ' . $cor_linha . '><div id="div_lbl' . $qry_gera_cad_campos['id_campo'] . '" style="display: inline;">';
        } else {
            echo '&nbsp;&nbsp;<div id="div_lbl' . $qry_gera_cad_campos['id_campo'] . '" style="display: inline; margin:0px 4px">';
        }
        if (trim( $qry_gera_cad_campos['textohtm'] )) {
            $lista_url_ajuda = 'gera_cad_ajuda.php?ptipo=campo&pfuncao=' . $id_funcao . '&pcampo=' . $qry_gera_cad_campos['id_campo'];
            $lista_url_open_ajuda = 'window.open(\'' . $lista_url_ajuda . '\',\'ajuda\',\'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=500,height=300,top=200,left=200\'); return false;';
            echo '<a href="' . $lista_url_ajuda . '" target="_blank" onclick="javascript:' . $lista_url_open_ajuda . '" ><img src="images/btn_ajuda.jpg" border=0 alt="Exibir Ajuda" width="10" height="10" /></a> ';
        }
        $lbl_nome_campo = $qry_gera_cad_campos['nome_campo'];
        include( 'gera_cad_detalhe_custom_label.php' );
        $texto_ordem_development = '';
        if ($_SESSION['ip_usuario_development'] == '1') {
            $texto_ordem_development = ' title="Ordem = ' . $qry_gera_cad_campos['ordem'] . '"';
        }
        if (substr( $qry_gera_cad_campos['id_campo'], 0, 4 ) == 'wcp_') {
            //$cor_label = '<span style="color:#008B00" ' . $texto_ordem_development . '><u>';
	    $cor_label = '<label style="color:#008B00"><u>';


        } else {
            //$cor_label = '<span style="color:#000000" '. $texto_ordem_development .'>';
	    $cor_label = '<label for="">';
        }
        if ($qry_gera_cad_campos['sn_obrigatorio'] == '1') {
            if (substr( $qry_gera_cad_campos['id_campo'], 0, 4 ) == 'wcp_') {
                //$cor_label = '<span style="color:#008B00" ' . $texto_ordem_development . '><u>';
		$cor_label = '<label style="color:#008B00"  for=""> <u>';
            } else {
                //$cor_label = '<span style="color:#0000FF" ' . $texto_ordem_development . '>';
		 $cor_label = '<label for="">';

            }
            echo '<b>' . $cor_label . $lbl_nome_campo . ' *</label ></b>';
        } else {
            echo $cor_label . $lbl_nome_campo . ' :</label>';
            if ($old_quebra_linha == '0') {
                echo '&nbsp;&nbsp;';
            }
        }
        if ($old_quebra_linha == '1') {
            echo '</div></td>';
            echo '<td width="1%" ' . $cor_linha . '>&nbsp;</td>';
            echo '<td width="100%" ' . $cor_linha . '><div align="left" id="div_edt' . $qry_gera_cad_campos['id_campo'] . '" style="display: inline;">';
        } else {
            echo '</div>';
            echo '<div align="left" id="div_edt' . $qry_gera_cad_campos['id_campo'] . '" style="display: inline;">';
        }

        if ($qry_gera_cad_campos['evento_change']) {
            $evento_change = str_replace( '<br />', ' ', $qry_gera_cad_campos['evento_change'] );
        } else {
            $evento_change = '';
        }
        if ($qry_gera_cad_campos['evento_keypress']) {
            $evento_keypress = ' onkeypress="' . str_replace( '<br />', ' ', $qry_gera_cad_campos['evento_keypress'] ) . '"';
        } else {
            $evento_keypress = '';
        }
        if ($qry_gera_cad_campos['max_carac']) {
            $max_carac = ' maxlength="' . $qry_gera_cad_campos['max_carac'] . '"';
        } else {
            $max_carac = '';
        }
        if ($readonly) {
            $evento_change = '';
        }
        $fonte_odbc_lupa = $qry_gera_cad_campos['fonte_odbc'];
        require("campos.php");
        if ($qry_gera_cad_campos['quebra_linha'] == '1') {
            echo '</div><div ></div></td></tr>';
        } else {
            echo '</div>';
        }
        $old_quebra_linha = $qry_gera_cad_campos['quebra_linha'];
    } else {
        if (( $tipo == 'date' || $tipo == 'datetime' )) {
            $vl_campo_trat = datagetbd( $vl_campo );
            echo '<input type="hidden" name="edt' . $id_campo . '" id="edt' . $id_campo . '" value="' . $vl_campo_trat . '">';
        } else {
            echo '<input type="hidden" name="edt' . $id_campo . '" id="edt' . $id_campo . '" value="' . $vl_campo . '">';
        }
    }
    $qry_gera_cad_sub = farray( query( 'select * from is_gera_cad_sub where id_funcao_mestre = \'' . $id_funcao . '\' and exibir_apos_campo = \'' . $qry_gera_cad_campos['id_campo'] . '\'' ) );
    if (( $pnumreg != 0 - 1 && $qry_gera_cad_sub['id_sub'] )) {
        $filtro_det = str_replace( '@cpomestre', $qry_cadastro[$qry_gera_cad_sub['campo_mestre']], $qry_gera_cad_sub['filtro_detalhe'] );
        $pos_read = strpos( $filtro_det, 'pread' );
        if ($pos_read === false) {
            $url_pread = '&pread=' . $pread;
        } else {
            $url_pread = '';
        }
        $url_sub = 'javascript: exibe_programa(\'' . $filtro_det . '&pdrilldown=1&psubdet=' . $qry_gera_cad_sub['numreg'] . '&pnpai=' . $qry_cadastro[str_replace( '%20', ' ', $pchave_original )] . $url_pread . '&pdiv=sub' . $qry_gera_cad_sub['id_sub'] . '\',\'sub' . $qry_gera_cad_sub['id_sub'] . '\');';
        $campo_sub = $qry_gera_cad_sub['exibir_apos_campo'];
        $url_campo_sub = '<div id="sub' . $qry_gera_cad_sub['id_sub'] . '"><a href="' . $url_sub . '">' . $qry_gera_cad_sub['nome_sub'] . '</a></div>';
        echo '<td colspan="4">&nbsp;</td>';
        echo '<tr><td bgcolor="#dbe9f4" class="sub_tit" colspan="4"><div align="center">';
        echo '</div></td></tr>';
        echo '<tr>';
        echo '<td colspan="4"><br>' . $url_campo_sub . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="4">&nbsp;</td>';
        echo '</tr>';
        echo '<tr><td bgcolor="#dbe9f4" class="sub_tit" colspan="4"><div align="center">';
        echo '</div></td></tr>';
        echo '<tr>';
        echo '<td colspan="4">&nbsp;</td>';
        echo '</tr>';
        continue;
    }
}
echo '</div></table>';
echo '</div><table width="100%" border="0" cellspacing="2" cellpadding="2" bgcolor="#FFFFFF">';
echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td>';
echo ' </tr><tr>';
echo '<td colspan="4" align="center">';
if ($id_usuario) {
    if (( strtoupper( $qry_gera_cad['tipo'] ) == 'WORKFLOW' && $pnumreg != '-1' )) {
        $qry_fs_workflow = farray( query( 'select * from is_workflow_fase where id_workflow = \'' . $id_funcao . '\' and id_fase =\'' . $qry_cadastro['id_fase_workflow'] . '\'' ) );
        $qry_us_workflow = farray( query( 'select * from is_usuario where numreg = \'' . $qry_cadastro['id_usuario_resp'] . '\'' ) );
        echo '<div style="height:1px; width:100%; background-color:#E0E0E0; margin-top:2px; margin-bottom:2px;"></div>';
        $DtPrazoTela = $qry_cadastro['dt_prev_fim'];
        $DtPrazoTela = substr( $DtPrazoTela, 8, 2 ) . '/' . substr( $DtPrazoTela, 5, 2 ) . '/' . substr( $DtPrazoTela, 0, 4 );
        echo '<br><b>Workflow</b><br><b>Fase Atual: ' . $qry_fs_workflow['nome_fase'] . ' - Responsável: ' . $qry_us_workflow['nome_usuario'] . ' - Prazo: ' . $DtPrazoTela . ' ' . $qry_cadastro['hr_prev_fim'] . '</b>';
        if ($qry_cadastro['id_situacao'] != '4') {
            echo '<br>Ação: <input type="radio" name="cbxopworkflow" id="cbxopworkflow" value="sem_acao" checked>Permanecer nesta Fase&nbsp;&nbsp;';
            echo '<input type="radio" name="cbxopworkflow" id="cbxopworkflow" value="avancar">Concluir a Fase&nbsp;&nbsp;';
            if (( $qry_cadastro['id_fase_workflow'] != $qry_wf['id_fase'] && $qry_cadastro['id_fase_workflow'] )) {
                echo '<input type="radio" name="cbxopworkflow" id="cbxopworkflow" value="recusar">Recusar pelo motivo : ';
                echo '&nbsp;&nbsp;<input type="text" name="edtwfcomentario" id="edtwfcomentario" size=30>';
            }
        }
        echo '<br><br>';
        $url_wf_historico = 'javascript: abre_tela_nova(\'gera_cad_lista.php?pfuncao=workflow_log&pread=S&pdrilldown=1&pfixo=id_atividade@igual@s' . $qry_cadastro['numreg'] . '@s\',\'wfhistorico\',\'750\',\'390\',\'1\');';
        echo '<input type="button" value="Histórico" name="btnwfhistorico"  onclick="' . $url_wf_historico . '" class="botao_form" />';
        $url_wf_tarefas = 'javascript: abre_tela_nova(\'gera_cad_lista.php?pfuncao=atividades_cad_lista&psubdet=421&pnpai=' . $qry_cadastro['numreg'] . '&pdrilldown=1&pfixo=id_atividade_pai@igual@s' . $qry_cadastro['numreg'] . '@s\',\'wftarefas\',\'850\',\'450\',\'1\');';
        echo '&nbsp;<input type="button" value="Tarefas" name="btnwtarefas"  onclick="' . $url_wf_tarefas . '" class="botao_form" />';
        $url_wf_doctos = 'javascript: abre_tela_nova(\'gera_cad_lista.php?pfuncao=arquivos_cad&pnpai=' . $qry_cadastro['numreg'] . '&psubdet=39&pdrilldown=1&pfixo=id_atividade@igual@s' . $qry_cadastro['numreg'] . '@s\',\'wfdoctos\',\'800\',\'350\',\'1\');';
        echo '&nbsp;<input type="button" value="Documentos" name="btnwfdoctos"  onclick="' . $url_wf_doctos . '" class="botao_form" /><br>';
    }
    if ($pread != '1') {
        echo '<div style="height:1px; width:100%; background-color:#E0E0E0; margin-top:2px; margin-bottom:2px;"></div>';
        $btn_salvar = '<input name="btnSubmit" type="button" class="botao_form" onclick="javascript:document.getElementById(' . '\'snsalvar\').value = \'1\'; ' . 'document.getElementById(' . '\'snincluirnovo\').value = \'0\';' . 'verificar();' . '" value="Salvar" /> ';
        if ($pbloqincluir != '1') {
            $btn_salvar_incluir = '<input name="btnSubmit" type="button" class="botao_form" onclick="javascript:document.getElementById(' . '\'snsalvar\').value = \'1\'; ' . 'document.getElementById(' . '\'snincluirnovo\').value = \'1\';' . 'verificar();' . '" value="Salvar e Incluir Novo..." /> ';
            if ($pnumreg != '-1') {
                $btn_criar_copia = '<input name="btnSubmit" type="button" class="botao_form" onclick="javascript:if(confirm(' . '\'Deseja criar cópia deste registro ?\')){ document.getElementById(' . '\'pnumreg\').value = \'-1\';' . 'document.getElementById(' . '\'snincluirnovo\').value = \'0\'; alert(\'Cópia realizada com sucesso !\');' . '}" value="Criar Cópia" /> ';
            }
        }
        $btn_salvar_fechar = '<input name="btnSubmit" type="button" class="botao_form" onclick="javascript:document.getElementById(' . '\'snsalvar\').value = \'1\'; ' . 'document.getElementById(' . '\'pfecha\').value = \'1\';' . 'verificar();' . '" value="Salvar e Fechar" /> ';
    }
    if (0 < $pnumreg) {
        $a_layouts = farray( query( 'select * from is_gera_cad_relat where id_cad = \'' . $id_funcao . '\'' ) );
        if (0 < $a_layouts['numreg'] * 1) {
            $url_relatorio = 'gera_relatorio.php?pfuncao=' . $id_funcao . '&operacao=filtrar&pfixo=numreg@igual' . $pnumreg;
            $url_open_relatorio = 'window.open(\'' . $url_relatorio . '\',\'gerador_relat\',\'location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=' . $posicao_janela . ',left=' . $posicao_janela . '\').focus(); return false;';
            $btn_imprimir = '<input type="button" value="Relatório" name="btnimprimir"  onclick="javascript:' . $url_open_relatorio . '" class="botao_form" />';
        } else {
            $btn_imprimir = '<input type="button" value="Imprimir" name="btnimprimir"  onclick="javascript:window.print();" class="botao_form" />';
        }
    }
}
$btn_customs = '';
$sql_gera_cad_botoes = query( '(select *, 1 as tipo from is_gera_cad_botoes where id_funcao = \'' . $id_funcao . '\') union all (select *, 2 as tipo from is_gera_cad_botoes_custom where id_funcao = \'' . $id_funcao . '\') order by ordem' );
while ($qry_gera_cad_botoes = farray( $sql_gera_cad_botoes )) {
    $input_botao_custom = '&nbsp;';
    if ($pnumreg != 0 - 1) {
        $url_acao = str_replace( '@pnumreg2', $qry_cadastro['numreg2'], $qry_gera_cad_botoes['url_acao'] );
        $url_acao = str_replace( '@pnumreg', $qry_cadastro['numreg'], $url_acao );
        $url_acao = str_replace( '@sf', '\'', $url_acao );
    } else {
        $url_acao = 'javascript: alert(\'Por favor, primeiro você deve salvar o registro antes de executar esta função !\');';
    }
    if ($qry_gera_cad_botoes['tipo'] == '1') {
        $input_botao_custom .= '<input type="button" value="';
        $input_botao_custom .= $qry_gera_cad_botoes['nome_sub'];
        $input_botao_custom .= '" name="' . $qry_gera_cad_botoes['id_botao'] . '" class="botao_form" onclick="' . $url_acao . '">';
    } else {
        $input_botao_custom .= '<input type="button" value="';
        $input_botao_custom .= $qry_gera_cad_botoes['nome_sub'];
        $input_botao_custom .= '" name="' . $qry_gera_cad_botoes['id_botao'] . '" class="botao_form" style="color:#008B00;" onclick="' . $url_acao . '">';
    }
    include( 'gera_cad_detalhe_custom_botao.php' );
    $btn_customs .= $input_botao_custom;
}
echo $btn_salvar . $btn_salvar_incluir . $btn_salvar_fechar . $btn_criar_copia . $btn_imprimir . $btn_customs;
if ($qry_gera_cad['nome_tabela'] == 'is_atividade') {
    if ($qry_cadastro['pecahtm']) {
        echo '&nbsp;<input name="btnpeca" type="button" class="botao_form" onclick="javascript:window.open(' . '\'exibe_peca.php?numreg=' . $qry_cadastro['numreg'] . '\').focus();' . '" value="Exibir Peça de MKT" /> ';
    }
}
echo '&nbsp;<input type="button" value="Fechar" name="B4" class="botao_form" onclick="javascript:window.close();"> ';
echo '<div style="height:1px; width:100%; background-color:#E0E0E0; margin-top:2px; margin-bottom:2px;"></div>';
echo '</td></tr>';
echo '</td></tr>';
echo '<tr><td>&nbsp;</td><td colspan="3">&nbsp;</td>';
echo '</tr>';
if (( $id_funcao != 'is_log' && $pnumreg != '-1' )) {
    echo '<tr><td colspan="4"><div align="center">';
    $log_cad_url = 'gera_cad_lista.php?pfuncao=is_log&pfixo=numreg_cadastro@igual' . $pnumreg . '@andid_cad@igual@sf' . $id_funcao . '@sf&pdrilldown=1&pread=1';
    $log_url_open = 'window.open(this.href,\'' . $id_funcao . $pnumreg . 'log\',\'location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=850,height=550,top=10,left=10\').focus(); return false;';
    echo '<center><a href="' . $log_cad_url . '" onclick="' . $log_url_open . '" >Consultar LOG</a></center>';
    echo '</b> </div></td></tr>';
}
echo '</td></tr>';
echo '</div></table></table>';
echo '</div>';
include( 'gera_cad_detalhe_custom_end.php' );
echo '</form></body></html>';