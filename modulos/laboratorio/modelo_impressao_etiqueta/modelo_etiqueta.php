<?php
# modelo_etiqueta
# Expression package is undefined on line 3, column 5 in Templates/Scripting/EmptyPHP.php.
# Autor: Rodrigo Piva
# 10/10/2011
# 
# Log de Alterações
# yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
#
header("Content-Type: text/html;  charset=ISO-8859-1",true);

include("../../../conecta.php");
include("../../../functions.php");
include("../../../funcoes.php");
include("../../../mpdf/mpdf.php");



$busca = query("select * from is_atividade where numreg = '".$_GET["pnumreg"]."'");

$pular = $_GET["pular"];
$texto = '<style type="text/css">
.styBorda{
	background: url(Borda.png) no-repeat;
	width:200;
	height:35;
}
.styBorda2{
	background: url(Borda2.png) no-repeat;
	width:200;
	height:39;
}

.rotulo{
	font:"Arial Black", Gadget, sans-serif;
	font-size:12px;
}
.conteudo{
	font:Arial, Helvetica, sans-serif;
	font-size: 16px;
}

.colunaEsq {
        margin: 1px;
	width: 387px;
	float: left;
	height: 195px;
        
}
.colunaDir {
        margin: 1px;
	width: 387px;
	float: left;
	left: 387px;
	height: 195px;
        padding-left: 22px;
        
}

</style>';

// Variaveis de Tamanho
$i = 1;
$coluna = 0;
while ($i < 11){
    $colunaStyle = ($i%2==0)?'colunaDir':'colunaEsq';
        
    $texto .= '<div class="'.$colunaStyle.'">';


    //MONTA A ARRAY PARA ETIQUETAS
    if($pular == $i){
        $dados = farray($busca);

                $texto .= '<table width="387px" border="0" cellspacing="0" cellpadding="3">';
                $texto .= '<tr>';
                $texto .= '<td colspan="2" Style="background: url(Borda2.png) no-repeat;width:200;height:39;">&nbsp;<span class="rotulo">C&oacute;digo:&nbsp;</span><span class="conteudo">'.deparaIdErpCrm($dados['id_produto'],'id_produto_erp','numreg','is_produto').'</span></td>';
                $texto .= '<td width="127px"  rowspan="3" align="center" valign="middle"> <img src="logo_nepos.png" width="124" height="50" /></td>';
                $texto .= '</tr>';
                $texto .= '<tr>';
                $texto .= '<td colspan="2" Style="background: url(Borda2.png) no-repeat;width:200;height:39;">&nbsp;<span class="rotulo">Descri&ccedil;&atilde;o:&nbsp;</span><span class="conteudo">'.deparaIdErpCrm($dados['id_produto'],'nome_produto','numreg','is_produto').'</span></td>';
                $texto .= '</tr>';
                $texto .= '<tr>';
                $texto .= '<td colspan="2" Style="background: url(Borda2.png) no-repeat;width:200;height:39;">&nbsp;<span class="rotulo">Cliente:&nbsp;</span><span class="conteudo">'.deparaIdErpCrm($dados['id_pessoa'],'razao_social_nome','numreg','is_pessoa').'</span></td>';
                $texto .= '</tr>';
                $texto .= '<tr>';
                $texto .= '<td width="127px" Style="background: url(Borda.png) no-repeat;width:200;height:39;">&nbsp;<span class="rotulo">N&ordm; Serie:&nbsp;</span><span class="conteudo">'.$dados['nr_serie_produto'].'</span></td>';
                $texto .= '<td width="127px" Style="background: url(Borda.png) no-repeat;width:200;height:39;">&nbsp;<span class="rotulo">Data:&nbsp;</span><span class="conteudo">'.dten2br($dados['dt_inicio']).'</span></td>';
                $texto .= '<td Style="background: url(Borda.png) no-repeat;width:200;height:39;">&nbsp;<span class="rotulo">Qtde:&nbsp;</span><span class="conteudo">'.$dados['qtde'].'</span></td>';
                $texto .= '</tr>';
                $texto .= '<tr>';
                $texto .= '<td Style="background: url(Borda.png) no-repeat;width:200;height:39;">&nbsp;<span class="rotulo">OS:&nbsp;</span><span class="conteudo">'.$dados['id_atividade'].'</span></td>';
                $texto .= '<td Style="background: url(Borda.png) no-repeat;width:200;height:39;">&nbsp;<span class="rotulo">Estoque:&nbsp;</span><span class="conteudo">'.deparaIdErpCrm($dados['id_estoque'],'nome_estoque','numreg','is_estoque').'</span></td>';
                $texto .= '<td Style="background: url(Borda.png) no-repeat;width:200;height:39;">&nbsp;<span class="rotulo">N.F.:&nbsp;</span><span class="conteudo">'.$dados['nr_nota'].'</span></td>';
                $texto .= '</tr>';
                $texto .= '</table>';
    }
    $texto .= '</div>';
    
    $i++;
}

//$mpdf = new mPDF('en-x', 'A4', '', '', 15, 15, 40, 30, 10, 10);

$mpdf = new mPDF('en-x', 'LETTER', '', '', 1, 1, 11, 6, 1, 1);
$mpdf->WriteHTML($texto);
$mpdf->Output();
?>