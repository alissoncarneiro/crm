<?php
@session_start( );
require_once( "conecta.php" );
require_once( "funcoes.php" );


$programa       = $_GET['programa'];
$pmp            = $_GET['md'];
$sql_filtro     = $_GET['psql_filtro'];
$descr_filtro   = $_GET['pdescr_filtro'];
$cbxfiltro      = $_GET['pcbxfiltro'];
$edtfiltro      = $_GET['pedtfiltro'];
$pchave         = $_GET['pchave'];
$pchave2        = $_GET['pchave2'];
$pfixo          = $_GET['pfixo'];


$sqlLupa = "select nome_funcao from is_funcoes where id_funcao = '".$programa."'" ;
$qryLupa = query($sqlLupa);
$arrLupa = farray( $qryLupa );

$titulo = $arrLupa['nome_funcao'];


$sqlCad = "select fonte_odbc from is_gera_cad where id_cad = '".$programa."'";
$qryCad = query($sqlCad);
$arrCad = farray($qryCad);

$sqlBloqueioCad = "select * from is_perfil_funcao_bloqueio_cad where id_perfil = '".$_SESSION['id_perfil']."' and id_cad = '".$programa."'";
$qryBloqueioCad = query($sqlBloqueioCad);
$arrBloqueioCad = farray($qryBloqueioCad);

if ( $arrBloqueioCad['sn_bloqueio_ver'] == "S" )
{
    echo "Seu perfil de acesso não tem permissão para acessar este cadastro ! Por favor contate o administrador do sistema.";
    exit( );
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
    <head>
        <title>:: FOLLOW CRM :: </title>
        <link href="estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="estilos_css/cadastro.css">
        <link rel="stylesheet" type="text/css" media="all" href="estilos_css/calendar-blue.css" title="win2k-cold-1" />
        <script type="text/javascript" src="js/function.js"></script>
        <script language="JavaScript" src="js/ajax_menus.js"></script>
        <script type="text/javascript" src="js/calendario/calendario.js"></script>
        <script type="text/javascript" src="js/calendario/calendario-pt.js"></script>
        <script type="text/javascript" src="js/calendario/calendario-config.js"></script>
    </head>
    <body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
        <center>
            <div id="principal_detalhes">
                <div id="topo_detalhes">
                    <div id="logo_empresa"></div></div>
                    <div id="conteudo_detalhes">
                        <form method="POST" name="cad" id="cad" action="gera_cad_graf_post.php">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="5%"></td>
                                    <td colspan="3">
                                        <br>
                                            <div align="left">
                                                <img src="images/seta.gif" width="4" height="7" />
                                                <span class="tit_detalhes">Gráfico : <?php echo $titulo; ?></span>
                                            </div>
                                            <br>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="99%" colspan="3">
                                        <input type="hidden" name="programa" value="<?php echo $programa;?>">
                                        <input type="hidden" name="modulo" value="<?php echo $md;?>">
                                        <input type="hidden" name="prog_titulo" value="<?php echo $titulo;?>">
                                        <input type="hidden" name="sql_filtro" id="sql_filtro" value="<?php echo $sql_filtro;?>">
                                        <input type="hidden" name="descr_filtro" id="descr_filtro" value="<?php echo $descr_filtro;?>">
                                        <input type="hidden" name="cbxfiltro" id="cbxfiltro" value="<?php echo $cbxfiltro;?>">
                                        <input type="hidden" name="edtfiltro" id="edtfiltro" value="<?php echo $edtfiltro;?>">
                                        <input type="hidden" name="pfixo" id="pfixo" value="<?php echo $pfixo;?>">
                                    </td>
                                </tr>
                            <?php   
                            $campos_bloqueados = "";
                            $sqlBloqueiosCampos = "select * from is_perfil_funcao_bloqueio_campos where id_perfil = '".$_SESSION['id_perfil']."' and id_cad = '".$programa."' and sn_bloqueio_ver = '1'";
                            $qryBloqueiosCampos = query($sqlBloqueiosCampos);

                            while ( $a_bloqueio_campos = farray( $qryBloqueiosCampos ) )
                            {
                                $campos_bloqueados = $campos_bloqueados."'".$a_bloqueio_campos['id_campo']."',";
                            }
                            if ( $campos_bloqueados )
                            {
                                $campos_bloqueados = "and ( not id_campo in (".substr( $campos_bloqueados, 0, strlen( $campos_bloqueados ) - 1 )."))";
                            }
                            $filtro_licenca = " and (id_licenca is null or id_licenca = '' or id_licenca like '%PADRAO%' or id_licenca like '%".$_SESSION['lic_id']."%')";

                            $sqlGeraCadCampos = "(select * from is_gera_cad_campos where id_funcao = '{$programa}' and (exibe_formulario = 1 or exibe_browse = 1 or exibe_filtro = 1) {$filtro_licenca} {$campos_bloqueados}) union all (select * from is_gera_cad_campos_custom where id_funcao = '{$programa}' and (exibe_formulario = 1 or exibe_browse = 1 or exibe_filtro = 1) {$filtro_licenca} {$campos_bloqueados}) order by ordem" ;
                            $qryGeraCadCampos = query($sqlGeraCadCampos);
      
                            while ( $qry_gera_cad_campos = farray( $qryGeraCadCampos ) ){
                                $arrCampos[] = array(
                                    'tipo_campo'    => $qry_gera_cad_campos['tipo_campo'],
                                    'id_campo'      => $qry_gera_cad_campos['id_campo'],
                                    'nome_campo'    => $qry_gera_cad_campos['nome_campo']
                                );
                            }


                            ?>
                            
                            <tr>
                                <td>&nbsp;</td>
                                <td width="18%">
                                    <div align="right">Visualizar :</div>
                                </td>
                                <td width="1%">&nbsp;</td>
                                <td width="76%">
                                    <div align="left">
                                        <select name="edtcampo" id="edtcampo">
                                            <option value="count(*)" selected>Quantidade de Registros</option>
                                            <?php
                                            foreach($arrCampos as $k => $v)
                                            {
                                                if ( $v['tipo_campo'] == "int" || $v['tipo_campo'] == "real" || $v['tipo_campo'] == "double" || $v['tipo_campo'] == "float" || $v['tipo_campo'] == "money" )
                                                {
                                                    echo "<option value=\"SUM(".$v['id_campo'].")\">Somatória de ".$v['nome_campo']."</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td width="18%">
                                    <div align="right">Por :</div>
                                </td>
                                <td width="1%">&nbsp;</td>
                                <td width="76%">
                                    <div align="left">
                                        <select name="edtgrupo" id="edtgrupo">
                                            <?php
                                            foreach($arrCampos as $k => $v)
                                            {
                                                $selecionado = "";
                                                if ( ( $v['tipo_campo'] == "lupa" || $v['tipo_campo'] == "lupa_popup" || $v['tipo_campo'] == "combobox" ) && empty( $selecionado ) )
                                                {
                                                    $selecionado = "selected";
                                                }
                                                if ( $v['tipo_campo'] != "text" && $v['tipo_campo'] != "memo" )
                                                {
                                                    if ( $v['tipo_campo'] == "date" || $v['tipo_campo'] == "date_time" )
                                                    {
                                                        echo "<option value=\"".$v['id_campo'].")\">".$v['nome_campo']."</option>";
                                                        echo "<option value=\"day(".$v['id_campo'].")\">".$v['nome_campo']."(Dia)</option>";
                                                        echo "<option value=\"month(".$v['id_campo'].")\">".$v['nome_campo']."(Mês)</option>";
                                                        echo "<option value=\"year(".$v['id_campo'].")\">".$v['nome_campo']."(Ano)</option>";
                                                    }
                                                    else
                                                    {
                                                        echo "<option ".$selecionado." value=\"".$v['id_campo']."\">".$v['nome_campo']."</option>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr><td>&nbsp;</td>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr><td>&nbsp;</td>
                                <td width="18%">
                                    <div align="right">Tipo de Gráfico :</div>
                                </td>
                                <td width="1%">&nbsp;</td>
                                <td width="76%">
                                    <div align="left">
                                        <select name="edtgrafico" id="edtgrafico">
                                            <option value="pie" selected>Pizza</option>
                                            <option value="barsV" >Barras Verticais</option>
                                            <option value="barsH" >Barras Horizontais</option>
                                            <option value="line" >Pontos</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td width="18%">
                                    <div align="right">Ordenar por :</div>
                                </td>
                                <td width="1%">&nbsp;</td>
                                <td width="76%">
                                    <div align="left">
                                        <select name="edtordem" id="edtordem">
                                            <option value="1" selected>Campo de agrupamento crescente</option>
                                            <option value="1 desc">Campo de agrupamento decrescente</option>
                                            <option value="3">Valores crescente</option>
                                            <option value="3 desc">Valores decrescente</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <?php
                            if ( $tipoBanco == "mysql" && empty( $arrCad['fonte_odbc'] ) ) { ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td width="18%">
                                    <div align="right">Limite de Dados :</div>
                                </td>
                                <td width="1%">&nbsp;</td>
                                <td width="76%">
                                    <div align="left">
                                        <input name="edtlimite" id="edtlimite" value="" size="5">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <div align="left">
                                            <input name="Submit" type="submit" class="botao_form" value="Confirmar" />
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
