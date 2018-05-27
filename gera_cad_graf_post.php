<?php



require_once( "conecta.php" );
require_once( "funcoes.php" );
$programa = $_POST['programa'];
$modulo = $_POST['modulo'];
$titulo = $_POST['prog_titulo'];
$pfixo = $_POST['pfixo'];
$titulo_programa = $_POST['prog_titulo'];
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n<head>\r\n<title>:: FOLLOW CRM :: ";
echo "</title>\r\n<link href=\"estilos_css/estilo.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n<link rel=\"stylesheet\" type=\"text/css\" href=\"estilos_css/cadastro.css\">\r\n<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"estilos_css/calendar-blue.css\" title=\"win2k-cold-1\" />\r\n";
echo "<s";
echo "tyle type=\"text/css\">\r\n<!--\r\nbody {\r\n\tmargin-left: 0px;\r\n\tmargin-top: 0px;\r\n\tmargin-right: 0px;\r\n\tmargin-bottom: 0px;\r\n}\r\n-->\r\n</style>\r\n<body topmargin=\"0\" leftmargin=\"0\" rightmargin=\"0\" bottommargin=\"0\">\r\n<center>\r\n<div id=\"principal_detalhes\">\r\n   <div id=\"topo_detalhes\">\r\n   <div id=\"logo_empresa\"></div>\r\n   <!--logo -->\r\n   </div><!--topo -->\r\n   <div id=\"conteudo_detalhes\">\r\n\r\n\r\n";
$descr_tot = "Qtde";
$edtcampo = $_POST['edtcampo'];
$edtgrupo = $_POST['edtgrupo'];
$pchave = $_POST['pchave'];
$pchave2 = $_POST['pchave2'];
$edtgrafico = $_POST['edtgrafico'];
$edtlimite = $_POST['edtlimite'];
$edtordem = $_POST['edtordem'];
$sql_filtro = $_POST['sql_filtro'];
$descr_filtro = $_POST['descr_filtro'];
$lista_qry_gera_cad = farray( query( "select * from is_gera_cad where id_cad = '{$programa}'" ) );
$fonte_odbc = $lista_qry_gera_cad['fonte_odbc'];
if ( $fonte_odbc )
{
    $pref_bd_ini = "\"";
    $pref_bd_fim = "\"";
}
else
{
    if ( $tipoBanco == "mysql" )
    {
        $pref_bd_ini = "`";
        $pref_bd_fim = "`";
    }
    if ( $tipoBanco == "mssql" )
    {
        $pref_bd_ini = "[";
        $pref_bd_fim = "]";
    }
    $lista_cbxfiltro_trat = $lista_cbxfiltro;
}
if ( $edtcampo != "count(*)" )
{
    $edtcampo = str_replace( "SUM(", "ROUND(SUM(".$pref_bd_ini, str_replace( ")", $pref_bd_fim."),2)", $_POST['edtcampo'] ) );
}
$tabela = trim( $lista_qry_gera_cad['nome_tabela'] );
if ( $lista_qry_gera_cad['nome_visao'] )
{
    $tabela = $pref_bd_ini.trim( $lista_qry_gera_cad['nome_visao'] ).$pref_bd_fim;
}
$sql_mestre = trim( $lista_qry_gera_cad['sql_filtro'] );
if ( strpos( $sql_mestre, "where" ) === false )
{
    if ( strpos( $filtro_geral, "where" ) === false )
    {
        $clausula = "where";
    }
    else
    {
        $clausula = "and";
    }
    $where_mestre = "";
}
else
{
    $clausula = "and";
    $where_mestre = substr( $sql_mestre, strpos( $sql_mestre, "where" ), strlen( $sql_mestre ) - strpos( $sql_mestre, "where" ) );
}
$filtro_geral = $where_mestre;
if ( $cbxfiltro && $edtfiltro )
{
    $qry_gera_cad_campos = farray( query( "(select * from is_gera_cad_campos where id_funcao = '{$programa}' and id_campo = '{$cbxfiltro}') union all (select * from is_gera_cad_campos_custom where id_funcao = '{$programa}' and id_campo = '{$cbxfiltro}')" ) );
    if ( $qry_gera_cad_campos['tipo_campo'] == "lupa" || $qry_gera_cad_campos['tipo_campo'] == "combobox" || trim( $qry_gera_cad_campos['tipo_campo'] ) == "lupa_popup" )
    {
        if ( strpos( $qry_gera_cad_campos['sql_lupa'], "where" ) === false )
        {
            $clausula_lupa = "where";
        }
        else
        {
            $clausula_lupa = "and";
        }
        $filtro_lupa = $qry_gera_cad_campos['sql_lupa']." ".$clausula_lupa." ".$qry_gera_cad_campos['campo_descr_lupa']." like '%".$edtfiltro."%'";
        $filtro_lupa = str_replace( "@s", "'", $filtro_lupa );
        $filtro_lupa = str_replace( "@vs_cpo_id_funcao", $qry_cadastro['id_funcao'], $filtro_lupa );
        $filtro_lupa = str_replace( "@vs_id_sistema", $_SESSION['id_sistema'], $filtro_lupa );
        if ( $qry_cadastro['id_workflow'] )
        {
            $filtro_lupa = str_replace( "@vs_cpo_id_workflow", $qry_cadastro['id_workflow'], $filtro_lupa );
        }
        else
        {
            $filtro_lupa = str_replace( "@vs_cpo_id_workflow", $qry_mestre['id_cad'], $filtro_lupa );
        }
        $sql_qry_lupa = query( $filtro_lupa );
        $ids_lup = "";
        while ( $qrylup = farray( $sql_qry_lupa ) )
        {
            $ids_lup = $ids_lup."'".$qrylup[$qry_gera_cad_campos['id_campo_lupa']]."',";
        }
        if ( $ids_lup )
        {
            $ids_lup = "(".substr( $ids_lup, 0, strlen( $ids_lup ) - 1 ).")";
        }
        else
        {
            $ids_lup = "('@@@')";
        }
        $pfiltro = " {$clausula} {$tabela}.{$pref_bd_ini}{$cbxfiltro}{$pref_bd_fim} in ".$ids_lup;
        $descr2_filtro = $qry_gera_cad_campos['nome_campo']." ".$edtfiltro;
    }
    else
    {
        if ( $qry_gera_cad_campos['tipo_campo'] == "date" )
        {
            $valor_trat = substr( $edtfiltro, 6, 4 )."-".substr( $edtfiltro, 3, 2 )."-".substr( $edtfiltro, 0, 2 );
            $pfiltro = " {$clausula} {$tabela}.{$pref_bd_ini}{$cbxfiltro}{$pref_bd_fim} = '{$valor_trat}'";
        }
        else
        {
            $pfiltro = " {$clausula} {$tabela}.{$pref_bd_ini}{$cbxfiltro}{$pref_bd_fim} like '%{$edtfiltro}%'";
        }
        $descr2_filtro = $qry_gera_cad_campos['nome_campo']." ".$edtfiltro;
    }
    $clausula = "and";
}
if ( $pfixo )
{
    $fixo_trat = $pfixo;
    $pfiltro .= " {$clausula} {$fixo_trat}";
}
$filtro_geral = $filtro_geral." ".$pfiltro." ";
$filtro_geral = str_replace( "@vs_id_usuario", $vs_id_usuario, $filtro_geral );
$filtro_geral = str_replace( "@vs_id_perfil", $vs_id_perfil, $filtro_geral );
$filtro_geral = str_replace( "@vs_id_empresa", $vs_id_empresa, $filtro_geral );
$filtro_geral = str_replace( "@vs_dt_hoje", date( "Y-m-d" ), $filtro_geral );
if ( $sql_filtro )
{
    if ( strpos( $filtro_geral, "where" ) === false )
    {
        $clausula = "where";
    }
    else
    {
        $clausula = "and";
    }
    $filtro_geral = $filtro_geral." ".$clausula." ".$sql_filtro." ";
}
$sql_bloqueio = "";
if ( $sn_bloquear_leitura == "S" )
{
    if ( $qry_gera_cad['nome_tabela'] == "is_atividades" )
    {
        $sql_bloqueio = " id_usuario_resp = '{$vs_id_usuario}'";
    }
    if ( $qry_gera_cad['nome_tabela'] == "is_empresas" )
    {
        $sql_bloqueio = " id_usuario_gc = '{$vs_id_usuario}'";
    }
    if ( $qry_gera_cad['nome_tabela'] == "is_pessoas" )
    {
        $sql_bloqueio = " id_usuario_gc = '{$vs_id_usuario}'";
    }
}
if ( $sql_bloqueio )
{
    if ( strpos( $filtro_geral, "where" ) === false )
    {
        $clausula = "where";
    }
    else
    {
        $clausula = "and";
    }
    $filtro_geral = $filtro_geral." ".$clausula." ".$sql_bloqueio;
}
$filtro_geral_old = $filtro_geral;
$filtro_geral = str_replace( "@igual", "=", $filtro_geral );
$filtro_geral = str_replace( "@dif", "<>", $filtro_geral );
$filtro_geral = str_replace( "@sd@", "\"", $filtro_geral );
$filtro_geral = str_replace( "@sf", "'", $filtro_geral );
$filtro_geral = str_replace( "@s", "'", $filtro_geral );
$filtro_geral = str_replace( "@and", " and ", $filtro_geral );
$filtro_geral = str_replace( "@in", " in ", $filtro_geral );
$filtro_geral = str_replace( "@or", " or ", $filtro_geral );
$filtro_geral = str_replace( "@between", " between ", $filtro_geral );
$filtro_geral = str_replace( "@pctlike", "%", $filtro_geral );
$filtro_geral = str_replace( "@like", " like ", $filtro_geral );
$filtro_geral_old = $filtro_geral;
$edtgrupo2 = $edtgrupo;
$edtgrupo2 = str_replace( "day(", "", $edtgrupo2 );
$edtgrupo2 = str_replace( "month(", "", $edtgrupo2 );
$edtgrupo2 = str_replace( "year(", "", $edtgrupo2 );
$edtgrupo2 = str_replace( ")", "", $edtgrupo2 );
$qry_gera_cad_campos = farray( query( "(select * from is_gera_cad_campos where id_funcao = '{$programa}' and id_campo = '{$edtgrupo2}') union all (select * from is_gera_cad_campos_custom where id_funcao = '{$programa}' and id_campo = '{$edtgrupo2}')" ) );
$edtgrupo = trim( $edtgrupo );
if ( $qry_gera_cad_campos['tipo_campo'] == "lupa" || $qry_gera_cad_campos['tipo_campo'] == "lupa_popup" || $qry_gera_cad_campos['tipo_campo'] == "combobox" )
{
    $sql_lupa = strtolower( $qry_gera_cad_campos['sql_lupa'] );
    if ( strpos( $sql_lupa, "where" ) === false )
    {
        $tabela_lupa = substr( $sql_lupa, strpos( $sql_lupa, "from" ) + 4, strlen( $sql_lupa ) - strpos( $sql_lupa, "from" ) );
    }
    else
    {
        $tabela_lupa = substr( $sql_lupa, strpos( $sql_lupa, "from" ) + 4, strpos( $sql_lupa, "where" ) - strpos( $sql_lupa, "from" ) - 4 );
    }
    $descr_graf2 = $qry_gera_cad_campos['campo_descr_lupa'];
    $descr_graf = $tabela_lupa.".".$qry_gera_cad_campos['campo_descr_lupa'];
    $cod_graf = $tabela_lupa.".".$qry_gera_cad_campos['id_campo_lupa'];
    if ( strpos( $filtro_geral, "where" ) === false )
    {
        $clausula = "where";
    }
    else
    {
        $clausula = "and";
    }
    $edtgrupo = trim( $tabela.".".$pref_bd_ini.$edtgrupo.$pref_bd_fim );
    $filtro_geral = $filtro_geral." ".$clausula." ".$cod_graf." = ".$edtgrupo;
    $tabela_lupa = ", ".$tabela_lupa;
}
else if ( $qry_gera_cad_campos['tipo_campo'] == "date" || $qry_gera_cad_campos['tipo_campo'] == "datetime" )
{
    $descr_graf2 = $edtgrupo;
    $descr_graf = $edtgrupo;
    $cod_graf = $edtgrupo;
}
else
{
    $descr_graf2 = $edtgrupo;
    $edtgrupo = trim( $pref_bd_ini.$edtgrupo.$pref_bd_fim );
    $descr_graf = $edtgrupo;
    $cod_graf = $edtgrupo;
}
if ( $tipoBanco == "mysql" && trim( $edtlimite ) )
{
    $limite_sql = "LIMIT 0,".$edtlimite;
}
if ( $tipoBanco == "mssql" )
{
    if ( $tabela == trim( str_replace( ",", "", $tabela_lupa ) ) )
    {
        $sql = "select ".$descr_graf." , ' ".$edtgrupo." @igual @s' + CAST(".$edtgrupo." as VARCHAR) + '@s' as IDDR, ".$edtcampo." as CONTA ";
        $sql .= "from ".$tabela." ".$filtro_geral." group by ".$descr_graf.", ' ".$edtgrupo." @igual @s' + CAST(".$edtgrupo." as VARCHAR) + '@s' order by ".$edtordem." ".$limite_sql;
    }
    else
    {
        $sql = "select ".$descr_graf." , ' ".$edtgrupo." @igual @s' + CAST(".$edtgrupo." as VARCHAR) + '@s' as IDDR, ".$edtcampo." as CONTA ";
        $sql .= "from ".$tabela." ".$tabela_lupa." "." ".$filtro_geral." group by ".$descr_graf.", ' ".$edtgrupo." @igual @s' + CAST(".$edtgrupo." as VARCHAR) + '@s' order by ".$edtordem." ".$limite_sql;
    }
}
else if ( $tabela == trim( str_replace( ",", "", $tabela_lupa ) ) )
{
    $sql = "select ".$descr_graf." , CONCAT(CONCAT(' ".$edtgrupo." @igual @s', ".$edtgrupo."),'@s') as IDDR, ".$edtcampo." as CONTA ";
    $sql .= "from ".$tabela." ".$filtro_geral." group by ".$descr_graf.", CONCAT(CONCAT(' ".$edtgrupo." @igual @s', ".$edtgrupo."),'@s') order by ".$edtordem." ".$limite_sql;
}
else
{
    $sql = "select ".$descr_graf." , CONCAT(CONCAT(' ".$edtgrupo." @igual @s', ".$edtgrupo."),'@s') as IDDR, ".$edtcampo." as CONTA ";
    $sql .= "from ".$tabela." ".$tabela_lupa." "." ".$filtro_geral." group by ".$descr_graf.", CONCAT(CONCAT(' ".$edtgrupo." @igual @s', ".$edtgrupo."),'@s') order by ".$edtordem." ".$limite_sql;
}
$url_det = "gera_cad_lista.php?pfuncao=".$programa;
if ( $edtgrafico == "pie" )
{
    $tipograf = "pie";
    $tipografdet = "pieF";
}
if ( $edtgrafico == "barsV" )
{
    $tipograf = "bars";
    $tipografdet = "vBarF";
}
if ( $edtgrafico == "barsH" )
{
    $tipograf = "bars";
    $tipografdet = "hBarF";
}
if ( $edtgrafico == "line" )
{
    $tipograf = "bars";
    $tipografdet = "lineF";
}
$xlengthgrafdet = "20";
echo "<span class=\"tit_detalhes\">".$titulo."</span>";
echo "<br>".$descr_filtro;
echo "<script language='Javascript' src='js/function.js'></script>";
$queryGrafico = $sql;
$qry2 = query( $sql, 1, $fonte_odbc );
$i = 0;
$tot = 0;
while ( $r = @farray( $qry2, $fonte_odbc ) )
{
    $querybusca[$i] = $r['IDDR'];
    $filtro_url = $filtro_geral_old;
    $filtro_url = str_replace( "=", "@igual", $filtro_url );
    $filtro_url = str_replace( "<>", "@dif", $filtro_url );
    $filtro_url = str_replace( "'", "@s", $filtro_url );
    $filtro_url = str_replace( " in ", "@in", $filtro_url );
    $filtro_url = str_replace( " and ", "@and", $filtro_url );
    $filtro_url = str_replace( " or ", "@or", $filtro_url );
    $filtro_url = str_replace( " between ", "@between", $filtro_url );
    $filtro_url = str_replace( "%", "@pctlike", $filtro_url );
    $filtro_url = str_replace( " like ", "@like", $filtro_url );
    $filtro_url = str_replace( "where", "", $filtro_url );
    if ( $fonte_odbc )
    {
        $action[$i] = "";
        $descricao[$i] = $r['CONTA']." : ".$r[$descr_graf2]." ";
    }
    else
    {
        $action[$i] = "javascript: abre_tela_nova('".$url_det."&pdrilldown=1&pfixo=".$querybusca[$i]."&psql_filtro=".$filtro_url."&pdescr_filtro=".$descr_filtro."','grafdet','900','590','1');";
        $descricao[$i] = $r['CONTA']." : ".$r[$descr_graf2]." - "."Clique para exibir os detalhes";
    }
    ++$i;
    $res = $r['CONTA'];
    $tot = $tot + $res;
}
$width = 700;
$height = 360;
$titulo = "Total ".number_format( $tot, 2, ",", "." );
$ylengthgrafdet = $tot;
$coltitulo = $descr_graf2;
$coltitulo2 = "NOME2";
$colvalorx = "CONTA";
$caminhobase = "";
$nomearq = "gsituacao";
$corfundo = "ffffff";
include( "graficoflash.php" );
echo $desenhografico;
if ( 0 < $i )
{
    $qry2 = query( $sql, 1, $fonte_odbc );
    $i = 0;
    $tot = 0;
    echo "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"100%\">";
    echo "<tr><td colspan=\"3\" bgcolor=\"dae8f4\"><b>".$titulo_programa." ( ".$descr_tot." )</b></td></tr>";
    while ( $r = farray( $qry2, $fonte_odbc ) )
    {
        $querybusca[$i] = str_replace( " ", "%20", $r['IDDR'] );
        $filtro_url = $filtro_geral;
        $filtro_url = str_replace( "=", "@igual", $filtro_url );
        $filtro_url = str_replace( "<>", "@dif", $filtro_url );
        $filtro_url = str_replace( "'", "@s", $filtro_url );
        $filtro_url = str_replace( " and ", "@and", $filtro_url );
        $filtro_url = str_replace( " or ", "@or", $filtro_url );
        $filtro_url = str_replace( " between ", "@between", $filtro_url );
        $filtro_url = str_replace( "%", "@pctlike", $filtro_url );
        $filtro_url = str_replace( " like ", "@like", $filtro_url );
        $filtro_url = str_replace( "where", "", $filtro_url );
        $url_action = "javascript: abre_tela_nova('".$url_det."&pdrilldown=1&pfixo=".$querybusca[$i].$fixo_trat."&psql_filtro=".$filtro_url."&pdescr_filtro=".$descr_filtro."','grafdet','900','590','1');";
        echo "<tr><td width=\"50%\">".$r[$descr_graf2]."</td><td width=\"50%\" align=\"right\">";
        if ( $fonte_odbc )
        {
            echo "<a href=\"#\" onclick=\"".$url_action."\">";
        }
        echo number_format( $r['CONTA'], 2, ",", "." );
        if ( $fonte_odbc )
        {
            echo "</a>";
        }
        echo "</td></tr>";
        ++$i;
    }
    echo "</table>";
    echo "<br>";
}
echo "<hr>\r\n<input type=\"button\" value=\"Voltar\" name=\"B3\" class=\"botao_form\" onclick=\"javascript:window.history.go(-1);\">\r\n<input type=\"button\" value=\"Imprimir\" name=\"B4\" class=\"botao_form\" onclick=\"javascript:window.print();\">\r\n<input type=\"button\" value=\"Fechar\" name=\"B4\" class=\"botao_form\" onclick=\"javascript:window.close();\">\r\n\r\n</div>\r\n</body>\r\n</html>\r\n\r\n";
?>
