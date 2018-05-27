<?
@session_start();

header("Content-type: application/x-msdownload");
header("Content-type: application/ms-excel");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=metas_".date("Ymdhis").".xls");
header("Pragma: no-cache");
header("Expires: 0");

$vendedor = $_POST["edtcod_rep"];
$ano = $_POST["edtano"];

include ("../../conecta.php");
include ("../../funcoes.php");
include ("../../functions.php");

$vend = mysql_fetch_array(mysql_query("SELECT id_usuario,nome_usuario,id_representante FROM is_usuarios WHERE id_usuario = '".$vendedor."'"));

//$meta = mysql_fetch_array(mysql_query("SELECT * FROM is_metas WHERE ano = '".$ano."' AND id_representante = '".$vendedor."'"));

$usuarios = mysql_query("SELECT * FROM is_metas WHERE ano = '".$ano."' AND id_representante = '".$vend['id_usuario']."'");

//while($forn = mysql_fetch_array($fornecedores)){
while($vend = mysql_fetch_array($vendedores)){
	$fornecedor[$forn['id_representante']] = $forn['id_representante'];
	$metas_fornecedor[1][$forn['id_representante']] = $forn['mes_1']+$forn['mes_2']+$forn['mes_3'];
	$metas_fornecedor[2][$forn['id_representante']] = $forn['mes_4']+$forn['mes_5']+$forn['mes_6'];
	$metas_fornecedor[3][$forn['id_representante']] = $forn['mes_7']+$forn['mes_8']+$forn['mes_9'];
	$metas_fornecedor[4][$forn['id_representante']] = $forn['mes_10']+$forn['mes_12']+$forn['mes_12'];
}

$soma_atingido = $atingido = $soma_metas = array();

foreach($fornecedor as $k => $v){
for($i=1;$i<=4;$i++) {
	$at = mysql_fetch_array(mysql_query("SELECT sum(qtde*valor) as total FROM is_sales WHERE vendedor = '".$vend['nome_usuario']."' AND trim = '".$i."'"));
    //$at = mysql_fetch_array(mysql_query("SELECT sum(qt_faturada*vl_tot_item) as total FROM is_dm_notas WHERE dt_emis_nota BETWEEN '".$dt_inicio."' AND '".$dt_fim."' AND cod_repr = '".$vend['id_representante']."'"));
    $atingido[$i][$v] = $at['total'];
    $soma_atingido[$i] += $at['total'];
    $soma_metas[$i] += $metas_fornecedor[$i][$v];
	
	$soma_metas_geral += $soma_metas[$i];
	$soma_atingido_geral += $soma_atingido[$i];
}
}


?>
<table width="200" border="1">
  <tr>
    <td colspan="6" align="center"><strong>Metas - <?php echo $ano;?></strong></td>
    <td width="4">&nbsp;</td>
    <td colspan="2" align="center"><strong>Metas - <?php echo $ano;?></strong></td>
    <td width="4">&nbsp;</td>
    <td colspan="2" align="center"><strong>Metas - <?php echo $ano;?></strong></td>
    <td width="4">&nbsp;</td>
    <td colspan="2" align="center"><strong>Metas - <?php echo $ano;?></strong></td>
    <td width="4">&nbsp;</td>
    <td colspan="2" align="center"><strong>Metas - <?php echo $ano;?></strong></td>
    <td width="4">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="6" align="center"><strong>Total</strong></td>
    <td>&nbsp;</td>
    <td colspan="2" align="center"><strong>1&deg; Trimestre = 20%</strong></td>
    <td>&nbsp;</td>
    <td colspan="2" align="center"><strong>2&deg; Trimestre = 20%</strong></td>
    <td>&nbsp;</td>
    <td colspan="2" align="center"><strong>3&deg; Trimestre = 20%</strong></td>
    <td>&nbsp;</td>
    <td colspan="2" align="center"><strong>4&deg; Trimestre = 20%</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="65" align="center"><strong>Vendedor</strong></td>
    <td width="37" align="center"><strong>Meta</strong></td>
    <td width="66" align="center"><strong>Realizado</strong></td>
    <td width="66" align="center"><strong>% Realizado</strong></td>
    <td width="50" align="center"><strong>A realizar</strong></td>
    <td width="50" align="center"><strong>% A realizar</strong></td>
    <td>&nbsp;</td>
    <td width="66" align="center"><strong>Realizado</strong></td>
    <td width="66" align="center"><strong>% Realizado</strong></td>
    <td>&nbsp;</td>
    <td align="center"><strong>Realizado</strong></td>
    <td align="center"><strong>% Realizado</strong></td>
    <td>&nbsp;</td>
    <td align="center"><strong>Realizado</strong></td>
    <td align="center"><strong>% Realizado</strong></td>
    <td>&nbsp;</td>
    <td width="66" align="center"><strong>Realizado</strong></td>
    <td width="66" align="center"><strong>% Realizado</strong></td>
    <td>&nbsp;</td>
  </tr>
  <?php foreach($fornecedor as $k => $v){?>
  <tr>
    <td><strong><?php echo $v;?></strong></td>
    <td align="right"><?php echo @number_format($metas_fornecedor[1][$v],2,",",".");?></td>
    <td align="right"><?php echo @number_format($atingido[1][$v],2,",",".");?></td>
    <td align="right"><?php echo @number_format(($atingido[1][$v]/$metas_fornecedor[1][$v])*100,2,",",".");?></td>
    <td align="right"><?php echo @number_format($metas_fornecedor[1][$v] - $atingido[1][$v],2,",",".");?></td>
    <td align="right"><?php echo @number_format((($metas_fornecedor[1][$v] - $atingido[1][$v])/$metas_fornecedor[1][$v])*100,2,",",".");?></td>
    <td>&nbsp;</td>
    <td><?php echo @number_format($atingido[1][$v],2,",",".");?></td>
    <td align="right"><?php echo @number_format(($atingido[1][$v]/$metas_fornecedor[1][$v])*100,2,",",".");?></td>
    <td>&nbsp;</td>
    <td><?php echo @number_format($atingido[2][$v],2,",",".");?></td>
    <td align="right"><?php echo @number_format(($atingido[2][$v]/$metas_fornecedor[2][$v])*100,2,",",".");?></td>
    <td>&nbsp;</td>
    <td><?php echo @number_format($atingido[3][$v],2,",",".");?></td>
    <td align="right"><?php echo @number_format(($atingido[3][$v]/$metas_fornecedor[3][$v])*100,2,",",".");?></td>
    <td>&nbsp;</td>
    <td><?php echo @number_format($atingido[4][$v],2,",",".");?></td>
    <td align="right"><?php echo @number_format(($atingido[4][$v]/$metas_fornecedor[4][$v])*100,2,",",".");?></td>
    <td>&nbsp;</td>
  </tr>
  <?php }?>
  <tr>
    <td bgcolor="#99CCFF"><strong>Total:</strong></td>
    <td align="right" bgcolor="#99CCFF"><?php echo @number_format($soma_metas_geral,2,",",".");?></td>
    <td align="right" bgcolor="#99CCFF"><?php echo @number_format($soma_atingido_geral,2,",",".");?></td>
    <td align="right" bgcolor="#99CCFF"><?php echo @number_format(($soma_atingido_geral/$soma_metas_geral)*100,2,",",".");?></td>
    <td align="right" bgcolor="#99CCFF"><?php echo @number_format($soma_metas_geral-$soma_atingido_geral,2,",",".");?></td>
    <td align="right" bgcolor="#99CCFF"><?php echo @number_format((($soma_metas_geral-$soma_atingido_geral)/$soma_metas_geral)*100,2,",",".");?></td>
    <td>&nbsp;</td>
    <td bgcolor="#99CCFF"><?php echo @number_format($soma_atingido[1],2,",",".");?></td>
    <td align="right" bgcolor="#99CCFF"><?php echo @number_format(($soma_atingido[1]/$soma_metas[1])*100,2,",",".");?></td>
    <td>&nbsp;</td>
    <td bgcolor="#99CCFF"><?php echo @number_format($soma_atingido[2],2,",",".");?></td>
    <td align="right" bgcolor="#99CCFF"><?php echo @number_format(($soma_atingido[2]/$soma_metas[2])*100,2,",",".");?></td>
    <td>&nbsp;</td>
    <td bgcolor="#99CCFF"><?php echo @number_format($soma_atingido[3],2,",",".");?></td>
    <td align="right" bgcolor="#99CCFF"><?php echo @number_format(($soma_atingido[3]/$soma_metas[3])*100,2,",",".");?></td>
    <td>&nbsp;</td>
    <td bgcolor="#99CCFF"><?php echo @number_format($soma_atingido[4],2,",",".");?></td>
    <td align="right" bgcolor="#99CCFF"><?php echo @number_format(($soma_atingido[4]/$soma_metas[4])*100,2,",",".");?></td>
    <td>&nbsp;</td>
  </tr>
</table>