<?php

header('Content-Type: text/html; charset=utf-8');
@session_start( );
require_once( "conecta.php" );
require_once( "funcoes.php" );

$id_funcao      = $_GET['pfuncao'];
$pfiltrosub     = $_GET['pfiltrosub'];
$pnumreg        = $_GET['pnumreg'];
$psubdet        = $_GET['psubdet'];
$pnpai          = $_GET['pnpai'];
$pcalc          = $_GET['pcalc'];
$pcampocustom   = $_GET['pcampocustom'];
$id_usuario     = $_SESSION['id_usuario'];
$nome_usuario   = $_SESSION['nome_usuario'];
$id_perfil      = $_SESSION['id_perfil'];
$pchave         = $_GET['pchave'];

if ( empty( $pchave ) ){
    $pchave = "numreg";
}else{
    $pchave = str_replace( "%20", " ", $pchave );
}

$qry_gera_cad = farray( query( "select * from is_gera_cad where id_cad = '{$id_funcao}'" ) );
$qry_funcoes = farray( query( "select * from is_funcoes where id_funcao = '{$id_funcao}'" ) );
$filtro_geral = str_replace( "@vs_id_usuario", $vs_id_usuario, $filtro_geral );
$filtro_geral = str_replace( "@vs_id_perfil", $vs_id_perfil, $filtro_geral );
$filtro_geral = str_replace( "@vs_id_empresa", $vs_id_empresa, $filtro_geral );
$filtro_geral = str_replace( "@vs_dt_hoje", date( "Y-m-d" ), $filtro_geral );
$filtro_geral = str_replace( "@sf", "'", $filtro_geral );

if ( strpos( $filtro_geral, "where" ) === false ){
    $clausula = "where";
}else{
    $clausula = "and";
}

$a_bloqueio_cad = @farray( @query( "select * from is_perfil_funcao_bloqueio_cad where id_perfil = '".$_SESSION['id_perfil']."' and id_cad = '".$id_funcao."'" ) );
if ( $a_bloqueio_cad['sn_bloqueio_ver'] == "1" ){
    echo "Seu perfil de acesso não tem permissão para acessar este cadastro ! Por favor contate o administrador do sistema.";
    exit( );
}
$campos_bloqueados = "";
$q_bloqueio_campos = @query( "select * from is_perfil_funcao_bloqueio_campos where id_perfil = '".$_SESSION['id_perfil']."' and id_cad = '".$id_funcao."' and sn_bloqueio_ver = 1" );

while ( $a_bloqueio_campos = @farray( $q_bloqueio_campos ) ){
    $campos_bloqueados = $campos_bloqueados."'".$a_bloqueio_campos['id_campo']."',";
}

if ( $campos_bloqueados ){
    $campos_bloqueados = "and ( not id_campo in (".substr( $campos_bloqueados, 0, strlen( $campos_bloqueados ) - 1 )."))";
}?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo $qry_gera_cad['titulo'];?></title>
    <link href="estilos_css/estilo.css" rel="stylesheet" type="text/css" />
    <link href="css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="estilos_css/cadastro.css">
    <link rel="stylesheet" type="text/css" media="all" href="estilos_css/calendar-blue.css" title="win2k-cold-1" />
    <style type="text/css">
        body {
            margin-left: 0px;
            margin-top: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
        }
        .linhafiltrodetails{
            height: 22px;
        }
    </style>
    <script language="JavaScript" src="js/ajax_usuario.js"></script>
    <script language="JavaScript" src="js/ajax_menus.js"></script>
    <script language="JavaScript" src="js/ajax_gera_cad.js"></script>
    <script language="JavaScript" src="js/valida.js"></script>
    <script type="text/javascript" src="js/function.js"></script>
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.5.custom.min.js"></script>
    <script type="text/javascript" src="js/jquery.ui.datepicker-pt-BR.js"></script>
    <script language="JavaScript">
        $(document).ready(function(){
            $.datepicker.setDefaults(
                $.datepicker.regional['pt-BR']
            );
        });
    </script>
    <script language="JavaScript">
        function verificar() {
            var mensagem = " ";
            if (mensagem == " ") {
                return true;
            } else {
                alert(mensagem);
                return false;
            }
        }
    </script>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<center>
    <form method="POST" name="cad" id="cad" action="gera_filtro_post.php" onsubmit="return verificar();">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="5%">
                    <?php
                    if ( $qry_funcoes['url_imagem'] ) : ?>
                        <img src="<?php echo  $qry_funcoes['url_imagem'];?>" width="40" height="40" />
                    <?php endif; ?>
                    <input type="hidden" name="pfuncao"         value="<?php echo $id_funcao;?>">
                    <input type="hidden" name="pfiltrosub"      value="<?php echo $pfiltrosub;?>">
                    <input type="hidden" name="pnumreg"         value="<?php echo $pnumreg;?>">
                    <input type="hidden" name="pcalc"           value="<?php echo $pcalc;?>">
                    <input type="hidden" name="pcampocustom"    value="<?php echo $pcampocustom;?>">
                    <input type="hidden" name="popc"            value="alterar">
                </td>
                <td colspan="3">
                    <div align="left">&nbsp;&nbsp;<img src="images/seta.gif" width="4" height="7" />
                        <span class="tit_detalhes"><?php echo $qry_gera_cad['titulo'];?> : Opções de Filtro</span>
                    </div>
                </td>
            </tr>
            <?php
            $sql_gera_cad_campos = query( "(select * from is_gera_cad_campos where id_funcao = '{$id_funcao}' and exibe_filtro = 1 {$campos_bloqueados}) union all (select * from is_gera_cad_campos_custom where id_funcao = '{$id_funcao}' and exibe_filtro = 1 {$campos_bloqueados}) order by exibe_formulario, nome_grupo, ordem" );
            $nome_grupo = "";
            $quebra_linha = "";
            $old_quebra_linha = "1";
            $primeira_quebra = "1";
            while ( $qry_gera_cad_campos = farray( $sql_gera_cad_campos ) ){
                if ( $nome_grupo != $qry_gera_cad_campos['nome_grupo'] ){
                    $nome_grupo = $qry_gera_cad_campos['nome_grupo'];
                    if ( $primeira_quebra != "1" ){
                        echo "<tr><td colspan=\"4\">&nbsp;</td></tr>";
                    }else{
                        $primeira_quebra = "0";
                    }
                    echo "<tr><td bgcolor=\"#dbe9f4\" class=\"sub_tit\" colspan=\"4\"><div align=\"center\">";
                    echo $nome_grupo;
                    echo "</div></td></tr>";
                    echo "<tr><td colspan=\"4\">&nbsp;</td></tr>";
                }
                if ( $old_quebra_linha == "1" )
                {
                    echo "<tr class='linhafiltrodetails'><td>&nbsp;</td>";
                    echo "<td width=\"18%\"><div align=\"right\">";
                }else{
                    echo "&nbsp;&nbsp;";
                }
                echo $qry_gera_cad_campos['nome_campo']." :";

                if ( $old_quebra_linha == "0" ){
                    echo "&nbsp;&nbsp;";
                }
                if ( $old_quebra_linha == "1" ){
                    echo "</div></td>";
                    echo "<td width=\"1%\">&nbsp;</td>";
                    echo "<td width=\"76%\"><div align=\"left\">";
                }
                $readonly = "";
                if ( $qry_gera_cad_campos['evento_change'] ){
                    $evento_change = " onchange=\"".str_replace( "<br />", " ", $qry_gera_cad_campos['evento_change'] )."\"";
                }else{
                    $evento_change = "";
                }

                $tipo = $qry_gera_cad_campos['tipo_campo'];
                $id_campo = str_replace( " ", "_", $qry_gera_cad_campos['id_campo'] );
                $fonte_odbc_lupa = $qry_gera_cad_campos['fonte_odbc'];

                switch ( $tipo ) {
                    case "lupa_popup" :
                        do {
                            echo "<select name=\"opc" . $qry_gera_cad_campos['id_campo'] . "\" id=\"opc" . $qry_gera_cad_campos['id_campo'] . "\">";
                            echo "<option selected value=\"\">Igual</option>";
                            echo "<option value=\"not\">Diferente</option>";
                            echo "</select>&nbsp;";
                            echo "<input type=\"text\" name=\"edt" . $qry_gera_cad_campos['id_campo'] . "\" id=\"edt" . $qry_gera_cad_campos['id_campo'] . "\" readonly size=\"10\" value=\"" . $vl_campo . "\" style=\"background-color:#CCCCCC\">";
                            echo "&nbsp;-";
                            $sql_lupa = $qry_gera_cad_campos['sql_lupa'];
                            if (strpos($sql_lupa, "where") === false) {
                                $clausula_lupa = " where";
                            } else {
                                $clausula_lupa = " and";
                            }
                            if (!$fonte_odbc_lupa) {
                                break;
                            } else {
                                $qry_lupa = @farray(@query($sql_lupa . $clausula_lupa . " \"" . $qry_gera_cad_campos['id_campo_lupa'] . "\"" . " = '" . $vl_campo . "'", 1, $fonte_odbc_lupa), $fonte_odbc_lupa);
                            }
                        } while (0);
                        $qry_lupa = farray(query($sql_lupa . $clausula_lupa . " " . $qry_gera_cad_campos['id_campo_lupa'] . " = '" . $vl_campo . "'", 1, $fonte_odbc_lupa), $fonte_odbc_lupa);

                        echo "<input type=\"text\" name=\"edtdescr" . $qry_gera_cad_campos['id_campo'] . "\" id=\"edtdescr" . $qry_gera_cad_campos['id_campo'] . "\" size=\"40\" value=\"" . str_replace("\"", " ", $qry_lupa[$qry_gera_cad_campos['campo_descr_lupa']]) . "\" readonly style=\"background-color:#CCCCCC\" " . $evento_change . " " . $evento_keypress . ">";
                        $qry_gera_cad_lupa = farray(query("select * from is_gera_cad where id_cad = '" . $qry_gera_cad_campos['id_funcao_lupa'] . "'"));
                        $qry_funcao_lupa = farray(query("select * from is_gera_cad where id_funcao = '" . $qry_gera_cad_campos['id_funcao_lupa'] . "'"));
                        $ref = "gera_cad_lista.php?pfuncao=" . $qry_gera_cad_campos['id_funcao_lupa'] . "&pdrilldown=1&plupa=" . $qry_gera_cad_campos['numreg'] . "&pfixo=" . str_replace("@gfi", "'+document.getElementById('", str_replace("@gff", "').value+'", str_replace("@sf", "'", $qry_gera_cad_campos['filtro_fixo'])));
                        $url_open = "javascript:window.open('" . $ref . "','" . $id_funcao . $qry_gera_cad_campos['id_campo'] . "','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=650,height=350,top=250,left=250'); return false;";
                        echo " <a href=\"#\" onclick=\"" . $url_open . "\"><img border=0 width=15 height=15 src=\"images/btn_busca.PNG\" alt=\"Buscar\"></a>";

                    break;
                    case "combobox" : ?>
                        <div style="zIndex: 2;" id="div<?php echo $qry_gera_cad_campos['id_campo']; ?>">
                            <select name="opc<?php echo $qry_gera_cad_campos['id_campo']; ?>"
                                    id="opc<?php echo $qry_gera_cad_campos['id_campo']; ?>">
                                <option selected value="">Igual</option>
                                <option value="not">Diferente</option>
                            </select>&nbsp;
                            <select name="edt<?php echo $id_campo; ?>" id="edt<?php echo $id_campo; ?>" <?php echo $readonly . " " . $evento_change; ?>>
                                <option value=""></option>
                                <?php
                                $filtro_lupa = $qry_gera_cad_campos['sql_lupa'] . " order by " . $qry_gera_cad_campos['campo_descr_lupa'];
                                $filtro_lupa = str_replace("@vs_id_usuario", $vs_id_usuario, $filtro_lupa);
                                $filtro_lupa = str_replace("@vs_id_perfil", $vs_id_perfil, $filtro_lupa);
                                $filtro_lupa = str_replace("@vs_id_empresa", $qry_cadastro['id_empresa'], $filtro_lupa);
                                $filtro_lupa = str_replace("@vs_dt_hoje", date("Y-m-d"), $filtro_lupa);
                                $sql_lupa = query($filtro_lupa, 1, $fonte_odbc_lupa);
                                while ($qry_lupa = farray($sql_lupa, $fonte_odbc_lupa)) {
                                    if ($qry_lupa[$qry_gera_cad_campos['id_campo_lupa']] == $vl_campo) {
                                        echo "<option value=\"" . $qry_lupa[$qry_gera_cad_campos['id_campo_lupa']] . "\" selected>" . $qry_lupa[$qry_gera_cad_campos['campo_descr_lupa']] . "</option>";
                                    } else {
                                        echo "<option value=\"" . $qry_lupa[$qry_gera_cad_campos['id_campo_lupa']] . "\">" . $qry_lupa[$qry_gera_cad_campos['campo_descr_lupa']] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                    <?php break;
                    case "sim_nao" : ?>
                        <div style="zIndex: 3;" id="div<?php echo $qry_gera_cad_campos['id_campo']; ?>">
                            <select name="opc<?php echo $qry_gera_cad_campos['id_campo'];?>" id="opc<?php echo $qry_gera_cad_campos['id_campo'];?>">
                                <option selected value="">Igual</option>
                                <option value="not">Diferente</option>
                            </select>&nbsp;
                            <select name="edt<?php echo $id_campo; ?>"
                                    id="edt<?php echo $id_campo; ?>" <?php echo $readonly . " " . $evento_change; ?> >
                                <option value="" selected></option>
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>

                    <?php break;
                    case "date" : ?>
                        <select name="opc<?php echo $qry_gera_cad_campos['id_campo']; ?>" id="opc<?php echo $qry_gera_cad_campos['id_campo']; ?>">
                            <option selected value="">No Período</option>
                            <option value="not">Fora do Período</option>
                        </select>&nbsp;
                        <?php $vl_campo_trat = ""; ?>
                            <input maxlength=10 readOnly="readOnly" type="text" name="edt<?php echo $id_campo; ?>de" id="edt<?php echo $id_campo; ?>de" <?php echo $evento_change; ?> size="9" <?php echo $readonly; ?> value="<?php echo $vl_campo_trat; ?>">
                            <script language="JavaScript">
                                $("#edt<?php echo $id_campo;?>de").datepicker({
                                    showOn: "button",
                                    buttonImage: "images/agenda.gif",
                                    buttonImageOnly: true,
                                    changeMonth: true,
                                    changeYear: true
                                });
                                $("#edt<?php echo $id_campo;?>de").datepicker(
                                    "option", "dateFormat", "dd/mm/yy"
                                );
                            </script>
                            &nbsp;até&nbsp;
                            <input maxlength=10 readOnly="readOnly" type="text" name="edt<?php echo $id_campo; ?>ate" id="edt<?php echo $id_campo; ?>ate" <?php echo $evento_change; ?> size="9" <?php echo $readonly; ?> value="<?php echo $vl_campo_trat; ?>">
                            <script language="JavaScript">
                                $("#edt<?php echo $id_campo;?>ate").datepicker({
                                    showOn: "button",
                                    buttonImage: "images/agenda.gif",
                                    buttonImageOnly: true,
                                    changeMonth: true,
                                    changeYear: true
                                });
                                $("#edt<?php echo $id_campo;?>ate").datepicker(
                                    "option", "dateFormat", "dd/mm/yy"
                                );
                            </script>

                    <?php break;
                    case "senha" : ?>
                        <input type="password" name="edt<?php echo $id_campo; ?>" id="edt<?php echo $id_campo; ?>" size="<?php echo $qry_gera_cad_campos['tamanho_campo']; ?>" value="<?php echo $vl_campo ?>" <?php echo $readonly . " " . $evento_change; ?> >

                    <?php break;
                    default:
                        if ($qry_gera_cad_campos['tamanho_campo'] < 50) {
                            $tamanho = $qry_gera_cad_campos['tamanho_campo'];
                        } else {
                            $tamanho = 50;
                        }

                        if ($qry_gera_cad_campos['tipo_campo'] == "int" || $qry_gera_cad_campos['tipo_campo'] == "real" || $qry_gera_cad_campos['tipo_campo'] == "double" || $qry_gera_cad_campos['tipo_campo'] == "float" || $qry_gera_cad_campos['tipo_campo'] == "money") { ?>
                            <select name="opc<?php echo $qry_gera_cad_campos['id_campo'];?>" id="opc<?php echo$qry_gera_cad_campos['id_campo'];?>">
                                <option selected value="">Na faixa</option>
                                <option value="not">Fora da Faixa</option>
                            </select>&nbsp;

                            <input type="text" name="edt<?php echo $id_campo;?>de" id="edt<?php echo $id_campo;?>de" size="<?php echo $tamanho;?>" value="<?php echo $vl_campo;?>" <?php echo $readonly . " " . $evento_change;?>>
                                &nbsp;até&nbsp;
                            <input type="text" name="edt<?php echo $id_campo;?>ate" id="edt<?php echo $id_campo;?>ate" size="<?php echo $tamanho;?>" value="<?php echo $vl_campo;?>" <?php echo $readonly . " " . $evento_change;?>>

                        <?php } else if ($qry_gera_cad_campos['filtro_multi'] == "1") {
                            $ref = "multi_filtro/multi_filtro.php?pnumreg=" . $qry_gera_cad_campos['numreg'];
                            $url_open = "javascript:window.open('" . $ref . "','" . $id_funcao . $qry_gera_cad_campos['id_campo'] . "','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=650,height=420,top=250,left=250'); return false;";
                            echo " <a href=\"#\" onclick=\"" . $url_open . "\"><img border=0 width=15 height=15 src=\"images/btn_funcao.PNG\" alt=\"Selecionar\"></a>";
                        }else{ ?>
                            <select name="opc<?php echo $qry_gera_cad_campos['id_campo'];?>" id="opc<?php echo $qry_gera_cad_campos['id_campo'];?>">
                                <option selected value="">Igual</option>
                                <option value="not">Diferente</option>
                            </select>&nbsp;
                            <input type="text" name="edt<?php echo $id_campo;?>" id="edt<?php echo $id_campo;?>" size="<?php echo $tamanho;?>" value="<?php echo $vl_campo;?>" <?php echo $readonly . " " . $evento_change ;?>>

                        <?php }
                    break;
                }
                echo "</div></td></tr>";
            }
            echo "<tr><td>&nbsp;</td><td colspan=\"3\"></td></tr>";
            $filtros_sub = "";
            $detalhes_bloqueados = "";
            $q_bloqueio_det = query( "select * from is_perfil_funcao_bloqueio_mestre_det where id_perfil = '".$_SESSION['id_perfil']."' and sn_bloqueio_ver = 1" );
            while ( $a_bloqueio_det = farray( $q_bloqueio_det ) )
            {
                $detalhes_bloqueados = $detalhes_bloqueados."'".$a_bloqueio_det['numreg_sub']."',";
            }
            if ( $detalhes_bloqueados )
            {
                $detalhes_bloqueados = "and ( not numreg in (".substr( $detalhes_bloqueados, 0, strlen( $detalhes_bloqueados ) - 1 )."))";
            }
            $sql_gera_cad_sub = query( "select * from is_gera_cad_sub where (not campo_detalhe is null) and (campo_detalhe <> '') and id_funcao_mestre = '{$id_funcao}' and (exibir_apos_campo is NULL or exibir_apos_campo = '') {$filtro_licenca} {$detalhes_bloqueados} and id_sistema like '%".$_SESSION['id_sistema']."%' order by ordem" );
            $primeira_sub = 0;
            while ( $qry_gera_cad_sub = farray( $sql_gera_cad_sub ) )
            {
                include( "gera_cad_detalhe_custom_sub_read.php" );
                if ( $exibe_mestre_detalhe != "N" )
                {
                    $lista_ref = "gera_filtro_detalhe.php?pfuncao=".$qry_gera_cad_sub['id_funcao_detalhe']."&pfiltrosub=".$qry_gera_cad_sub['numreg'];
                    $lista_url_open = "javascript:window.open('".$lista_ref."','".$qry_gera_cad_sub['id_funcao_detalhe']."1','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=750,height=450,top=10,left=10').focus(); return false;";
                    $filtros_sub .= "<tr><td></td><td align=\"right\"><img border=0 src=\"images/btn_modulo.png\" align=\"middle\" width=\"14\" height=\"13\" style=\"padding-right:3px; padding-left:3px;\" />";
                    $filtros_sub .= "<a href=\"#\" onclick=\"".$lista_url_open."\">";
                    $filtros_sub .= $qry_gera_cad_sub['nome_sub']." : </a></td><td></td>";
                    $filtros_sub .= "<td><input type=\"hidden\" id=\"sql_filtro".$qry_gera_cad_sub['numreg']."\"  name=\"sql_filtro".$qry_gera_cad_sub['numreg']."\" value=\"\">";
                    $filtros_sub .= "<input type=\"text\" style=\"background-color:#CCCCCC\" readonly size=\"90\" id=\"descr_filtro".$qry_gera_cad_sub['numreg']."\" name=\"descr_filtro".$qry_gera_cad_sub['numreg']."\" value=\"\"></td></tr>";
                }
            }
            if ( $filtros_sub )
            {
                echo "<tr class='linhafiltrodetails'><td bgcolor=\"#dbe9f4\" class=\"sub_tit\" colspan=\"4\"><div align=\"center\">";
                echo "Cadastros Relacionados";
                echo "</div></td></tr>";
                echo "<tr><td colspan=\"4\"><br><br><center>".$filtros_sub."</center><br><br></td></tr>";
            }

            ?>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>
                    <div align="left">
                        <input name="Submit" type="submit" class="botao_form" value="Gerar Filtro" />
                        <input type="button" value="Fechar" name="B4" class="botao_form" onclick="javascript:window.close();">
                    </div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
        </table>
    </form>
</body>
</html>