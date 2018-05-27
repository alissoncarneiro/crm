<?php

function save_xml_file( $filename, $xml_file )
{
    global $app_strings;
    if ( !( $handle = @fopen( $filename, "w" ) ) )
    {
        return;
    }

    if ( fwrite( $handle,  $xml_file  ) === FALSE && fwrite( $handle, $xml_file ) === FALSE )
    {
        return false;
    }
    fclose( $handle );
    return true;
}

session_start( );
header('Content-Type: text/html; charset=utf-8');
require_once( "../../conecta.php" );
require_once( "../../funcoes.php" );
$sql_filtro = $_POST['sql_filtro'];
if ( $sql_filtro ){
    $clausula = "@and";
}else{
    $sql_filtro = "";
}
$id_cadastro = "opo_cad_lista";
$lista_filtro_geral = str_replace( "@igual", "=", $sql_filtro );
$lista_filtro_geral = str_replace( "@dif", "<>", $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@in", " in ", $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@maior", ">", $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@menor", "<", $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@sf", "'", $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@s", "'", $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@sd@", "\"", $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@and", " and ", $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@or", " or ", $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@between", " between ", $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@pctlike", "%", $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@like", " like ", $lista_filtro_geral );
$lista_filtro_geral = str_replace( "@mais@", " + ", $lista_filtro_geral );
if ( $lista_filtro_geral )
{
    $lista_filtro_geral = " and ".$lista_filtro_geral;
}
$sql = "select is_oportunidade.id_opor_ciclo_fase, is_opor_fase.nome_opor_fase as NOME, is_opor_ciclo_fase.probabilidade, ";
if ( $tipoBanco == "mssql" ){
    $sql .= "'id_opor_ciclo_fase = @sf'+CAST(is_oportunidade.id_opor_ciclo_fase as VARCHAR)+'@sf' as IDDR, ";
}else{
    $sql .= "CONCAT('id_opor_ciclo_fase = @sf',is_oportunidade.id_opor_ciclo_fase,'@sf') as IDDR, ";
}

$sql .= "sum(valor) as CONTA, ";
$sql .= "count(*) as CONTA2 ";
$sql .= "from is_oportunidade , is_opor_fase , is_opor_ciclo_fase  where is_opor_ciclo_fase.probabilidade <> 0 and is_oportunidade.id_opor_ciclo_fase = is_opor_fase.numreg and is_oportunidade.id_opor_ciclo = is_opor_ciclo_fase.id_opor_ciclo and is_oportunidade.id_opor_ciclo_fase = is_opor_ciclo_fase.id_opor_fase".$lista_filtro_geral;



$id_perfil = $_SESSION['id_perfil'];
$id_usuario = $_SESSION['id_usuario'];
$sqlBloqueioPerfil = "select sn_bloquear_leitura from is_perfil where id_perfil = ".$id_perfil ;
$qryBloqueioPerfil = query($sqlBloqueioPerfil);
$arrBloqueioPerfil = farray($qryBloqueioPerfil);

if($arrBloqueioPerfil['sn_bloquear_leitura'] == 1){
$sql .= " and is_oportunidade.id_usuario_resp = $id_usuario";
}


if ( $tipoBanco == "mssql" ){
    $sql .= " group by is_oportunidade.id_opor_ciclo_fase, is_opor_fase.nome_opor_fase, is_opor_ciclo_fase.probabilidade ";
    $sql .= " order by is_opor_ciclo_fase.probabilidade desc";
}else{
    $sql .= " group by 1,2,3 ";
    $sql .= " order by is_opor_ciclo_fase.probabilidade desc";
}
$titulo_programa = "Funil de Vendas";
$url_det = "../../gera_cad_lista.php?pfuncao=".$id_cadastro;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
          <meta http-equiv="content-type" content="text/html;charset=utf-8" />
            <title>Funil de Vendas</title>
            <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
            <link rel="stylesheet" type="text/css" href="../../estilos_css/cadastro.css">
            <link rel="stylesheet" type="text/css" media="all" href="../../estilos_css/calendar-blue.css" title="win2k-cold-1" />
            <style type="text/css">
                <!--body {margin-left: 0px;margin-top: 0px;margin-right: 0px;margin-bottom: 0px;}-->
            </style>
        <body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
        <center>
            <div id="principal_detalhes">
                <div id="topo_detalhes">
                    <div id="logo_empresa"></div>
                </div>
                <form method="POST" name="cad" id="cad" action="funil_de_vendas.php" enctype='multipart/form-data'>
                    <a href="#" onclick="javascript:window.open('../../gera_filtro_detalhe.php?pfuncao=<?php echo $id_cadastro; ?>&pnumreg=-1','filtroanalise','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=650,height=450,top=100,left=100'); return false;">( CLIQUE AQUI para filtros avancados... )</a>
                    <input type="hidden" name="sql_filtro" id="sql_filtro" value="<?php echo $sql_filtro;?>">
                    <input type="hidden" name="descr_filtro" id="descr_filtro" value="<?php echo $descr_filtro;?>">
                    <input name="btnSubmit" type="submit" class="botao_form" value="Filtrar" /><br><br>
                </form>
                <div id="conteudo_detalhes">
                    <script language='Javascript' src='../../js/function.js'></script>
                    <?php
                    $color = array( "00FF00", "FF0000", "0000FF", "FF6600", "42FF8E", "6600FF", "FFFF00", "00FFFF", "FF00FF", "66FF00", "0066FF", "FF0066", "CC0000", "00CC00", "0000CC", "CC6600", "00CC66", "6600CC", "CCCC00", "00CCCC", "990000", "990066", "9900FF", "9999FF", "99FFFF", "000099", "006666", "009933", "33FF99", "CCFF00", "FF9900", "336699" );
                    $queryGrafico = $sql;
                    $qry2 = query( $sql );
                    $i = 0;
                    $tot = 0;
                    $xml_data = "<graph caption='Funil de Vendas' xAxisName='Fase' yAxisName='R\$' showNames='1' decimalPrecision='0' formatNumberScale='0'>";
                    while ( $r = @farray( $qry2 ) )
                    {
                        $querybusca[$i] = $r['IDDR'];
                        $action[$i] = "javascript:abre_tela_nova('".$url_det."&pdrilldown=1&pfixo=".$querybusca[$i].$clausula.$sql_filtro."','grafdet','900','590','1');";
                        $descricao[$i] = number_format( $r['CONTA'], 2, ",", "." )." : ".$r['NOME'];
                        $xml_data .= "<set name='". utf8_decode($r['NOME'])." : ".number_format( $r['CONTA'], 2, ",", "." )."' value='".$r['CONTA2']."' color='".$color[$i]."' alpha='85' link="."\"".$action[$i]."\""." />";
                        $i++;
                        $res = $r['CONTA'];
                        $tot = $tot + $res;
                    }
                    $xml_data .= "</graph>";
                    save_xml_file( "./Data".$_SESSION['id_usuario'].".xml", $xml_data );
                    ?>
                    <span class="tit_detalhes"><?php echo $titulo_programa ;?> <?php echo number_format( $tot, 2, ",", "." )?></span>
                    <br>
                    <OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase=http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="500" height="350" id="funil" >
                        <param name="movie" value="../../FusionCharts/FCF_Funnel.swf" />
                        <param name="FlashVars" value="&dataURL=Data<?php echo $_SESSION['id_usuario'];?>.xml&chartWidth=500&chartHeight=350" />
                        <param name="quality" value="high" />
                        <embed src="../../FusionCharts/FCF_Funnel.swf" flashVars="&dataURL=Data<?php echo $_SESSION['id_usuario'];?>.xml&chartWidth=500&chartHeight=350" quality="high" width="500" height="350" name="funil" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
                    </object>
                    <?php
                    $total_geral = $tot;
        ?>
                    <hr>
        <center>
            <input type="button" value="Imprimir" name="B4" class="botao_form" onclick="javascript:window.print();">
            <input type="button" value="Fechar" name="B4" class="botao_form" onclick="javascript:window.close();">
        </center>
                </div>
        </body>
</html>

