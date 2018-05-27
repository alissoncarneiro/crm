<?php
/*
 * c_coaching_lista_presenca_impressao.php
 * Autor: Alex
 * 03/01/2012 09:50:00
 */

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('../../../../conecta.php');
require('../../../../functions.php');
require('../../../../funcoes.php');
require('../../../../mpdf/mpdf.php');
ob_start();
$texto = '<style type="text/css" media="all">
            #etiqueta{
                width:286px;
                height:400px;
                background-image: url(\'etiqueta_crm.png\');
		position: relative;
                display: table-cell;
		font-family:Verdana;
                
            }
            .etiqueta_personalizada{
                font-family:Verdana;
                font-size:50pt;
		padding-left: 30% ;
                padding: 50% 34%;

            }
            .etiqueta_personalizada_sobrenome{
                font-family:Verdana;
		font-size:35pt;
            }
			
			
          </style>';

$numreg = $_GET['numreg'];
$snConta  = $_GET['sn_conta'];

if($snConta == 'S'){
    $SqlClientes = "SELECT wcp_nome_cracha FROM is_pessoa WHERE  numreg = '".$numreg."' order by razao_social_nome";
}else{
    $SqlClientes = "SELECT DISTINCT t1.id_pessoa,t2.wcp_nome_cracha FROM c_coaching_inscricao_curso_detalhe t1 INNER JOIN is_pessoa t2 ON t1.id_pessoa = t2.numreg WHERE t1.id_agenda = '".$numreg."' AND t2.wcp_nome_cracha <> ''  order by t2.razao_social_nome";
}

$QryClientes = query($SqlClientes);
while($ArClientes = farray($QryClientes)){
	
		$wcp_nome = $ArClientes['wcp_nome_cracha'];
		$wcp_nome_separado = explode(' ',$wcp_nome);

		$texto .= '<div id="etiqueta">';
			$texto .= '<table align="center"><tr text-rotate="90"><td class="etiqueta_personalizada" >';
			$texto .= utf8_encode($wcp_nome_separado[0]);
			$texto .= '</td></tr>';
			$texto .= '<td text-rotate="90" class="etiqueta_personalizada_sobrenome" >';
			$texto .= utf8_encode($wcp_nome_separado[1]);
			$texto .= " ";
			$texto .= utf8_encode($wcp_nome_separado[2]);
			$texto .= '</td></tr></table>';
		$texto .= '</div>';

		$texto .= '<div id="etiqueta">';
			$texto .= '<table align="center"><tr text-rotate="90"><td class="etiqueta_personalizada" >';
			$texto .= utf8_encode($wcp_nome_separado[0]);
			$texto .= '</td></tr>';
			$texto .= '<td text-rotate="90" class="etiqueta_personalizada_sobrenome" >';
			$texto .= utf8_encode($wcp_nome_separado[1]);
			$texto .= " ";
			$texto .= utf8_encode($wcp_nome_separado[2]);
			$texto .= '</td></tr></table>';
		$texto .= '</div>';

}

//echo $texto;
//$mpdf = new mPDF('ISO-8859-1', array(54,101));

$mpdf = new mPDF('en-x', array(54,101), '', '', 0, 0, 0, 0, 1, 1, 'p');
$mpdf->WriteHTML($texto);
$mpdf->Output();
?>
<span onmouseover="_tipon(this)" onmouseout="_tipoff()">
    <span class="google-src-text" style="direction: ltr; text-align: left">-moz-transform:rotate(120deg);</span> -Moz-transform: rotate (120DEG);
</span>