<?php

if ( $texto_empresa == "vogler" )
{
    $ap = farray( query( "select * from is_dm_param" ) );
    $ems2uni = $ap['odbc_moeda'];
    $id_moeda = $ap['id_moeda'];
    if ( !( $cnx_moeda = odbc_connect( $ems2uni, "sysprogress", "sysprogress" ) ) )
    {
        exit( "Erro na conexÃo com o Database" );
    }
    $dt_refer = date( "Y-m-d" );
    $a_moeda = odbc_fetch_array( odbc_exec( $cnx_moeda, "SELECT cotacao FROM pub.cotacao WHERE \"mo-codigo\"='".$id_moeda."' AND \"ano-periodo\" = '".substr( $dt_refer, 0, 4 ).substr( $dt_refer, 5, 2 )."'" ) );
    $str_cotacoes = $a_moeda['cotacao'];
    $array_cotacoes = explode( ";", $str_cotacoes );
    $ind_array_cotacoes = substr( $dt_refer, 8, 2 ) * 1 - 1;
    $cotacao_dia = $array_cotacoes[$ind_array_cotacoes] * 1;
    if ( $cotacao_dia <= 0 )
    {
        $cotacao_dia = 1;
    }
    echo "Taxa Dólar em ".date( "d/m" )." : ".$cotacao_dia;
}
?>
