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

include "../../conecta.php";
include "../../funcoes.php";
include "../../functions.php";

$vend = farray(query("SELECT numreg as id_usuario,nome_usuario,id_representante FROM is_usuario  where numreg = '".$vendedor."'"));

$meta = farray(query("SELECT * FROM is_meta WHERE ano = '".$ano."' AND id_representante = '".$vendedor."'"));

for($i=1;$i<=12;$i++) {
//    $dt_inicio = $ano."-".$i."-01";
//    $dt_fim = $ano."-".$i."-31";

	$dt_inicio = $ano."-".$i."-01";;
	$dt_fim = date("Y-m-d", mktime(0,0,0,$i+1,0,$ano) );

    $at = farray(query("SELECT sum(vl_tot_item) as total FROM is_dm_notas WHERE dt_emis_nota BETWEEN '".$dt_inicio."' AND '".$dt_fim."' AND cod_repr = '".$vend['id_representante']."'"));

    $atingido['mes_'.$i] = $at['total'];
    $soma_atingido += $at['total'];
    $soma_metas += $meta["mes_".$i];

	// Meta Diária
	$mes = $i;
	$dias = dias_uteis_no_mes($mes,$ano);
	$a_dias['mes_'.$i] = $dias;
}

?>

<table border="1" align="center">
   <tr>
     <td colspan="8" align="center"><strong>Metas - <?php echo $ano;?></strong></td>
   </tr>
   <tr>
     <td colspan="8" align="center"><strong><?php echo $vend['nome_usuario'];?></strong></td>
   </tr>
   <tr>
     <td align="center"><strong>Mês</strong></td>
     <td align="center"><strong>Meta</strong></td>
     <td align="center"><strong>Realizado</strong></td>
     <td align="center"><strong>% Realizado</strong></td>
     <td align="center"><strong>A realizar</strong></td>
     <td align="center"><strong>% A realizar</strong></td>
     <td align="center"><strong>Meta por Dia</strong></td>
     <td align="center"><strong>Meta até o Dia</strong></td>
   </tr>

   <?
    $nome_mes = array("1" => "Janeiro","2" => "Fevereiro","3" => "Março","4" => "Abril","5" => "Maio","6" => "Junho",
					  "7" => "Julho","8" => "Agosto","9" => "Setembro","10" => "Outubro","11" => "Novembro","12" => "Dezembro");
	for($i=1;$i<=12;$i++) {

		$mes_a_realizar = $meta['mes_'.$i] - $atingido['mes_'.$i];
		if ($mes_a_realizar < 0) { $mes_a_realizar = 0; }
		$mes_pct_a_realizar = (($meta['mes_'.$i] - $atingido['mes_'.$i])/$meta['mes_'.$i])*100;
		if ($mes_pct_a_realizar < 0) { $mes_pct_a_realizar = 0; }

		// Meta Até o dia
	    $mes_2_dig = str_pad($i,2,"0",STR_PAD_LEFT);
		if (date("Ym") == $ano.$mes_2_dig) {
			$ate_a_data_dias = dias_uteis_no_periodo($ano.'-'.$mes_2_dig.'-01',$ano.'-'.$mes_2_dig.'-'.date("d"));
			$meta_ate_o_dia = ($meta['mes_'.$i]/$a_dias['mes_'.$i])*$ate_a_data_dias;
		} else {
			if (date("Ym") > $ano.$mes_2_dig) {
				$meta_ate_o_dia = $meta['mes_'.$i];
			} else {
				$meta_ate_o_dia = 0;
			}
		}

		echo '<tr><td><strong>'.$nome_mes[$i].'</strong></td>';
	    echo '<td align="right">'.number_format($meta['mes_'.$i],2,",",".").'</td>';
        echo '<td align="right">'.number_format($atingido['mes_'.$i],2,",",".").'</td>';
        echo '<td align="right">'.number_format(($atingido['mes_'.$i]/$meta['mes_'.$i])*100,2,",",".").'</td>';
        echo '<td align="right">'.number_format($mes_a_realizar,2,",",".").'</td>';
        echo '<td align="right">'.number_format($mes_pct_a_realizar,2,",",".").'</td>';

        echo '<td align="right">'.number_format($meta['mes_'.$i]/$a_dias['mes_'.$i],2,",",".").'</td>';
;
        echo '<td align="right">'.number_format($meta_ate_o_dia,2,",",".").'</td>';

        echo '</tr>';
    }
	?>
   <tr>
     <td bgcolor="#99CCFF"><strong>Total:</strong></td>
     <td align="right" bgcolor="#99CCFF"><?php echo @number_format($soma_metas,2,",",".");?></td>
     <td align="right" bgcolor="#99CCFF"><?php echo @number_format($soma_atingido,2,",",".");?></td>
     <td align="right" bgcolor="#99CCFF"><?php echo @number_format(($soma_atingido/$soma_metas)*100,2,",",".");?></td>
     <td align="right" bgcolor="#99CCFF"><?php echo @number_format($soma_metas-$soma_atingido,2,",",".");?></td>
     <td align="right" bgcolor="#99CCFF"><?php echo @number_format((($soma_metas-$soma_atingido)/$soma_metas)*100,2,",",".");?></td>
     <td align="right" bgcolor="#99CCFF">&nbsp;</td>
     <td align="right" bgcolor="#99CCFF">&nbsp;</td>
   </tr>
</table>

<?

function dias_uteis_no_mes($mes,$ano) {

	// Feriados de Novembro
	$feriados = array();

	// Total de dias no mês
	$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

	$dias_letivos = 0;

	for($d=1; $d<=$dias_do_mes; $d++) {
		$dia_da_semana = jddayofweek(cal_to_jd(CAL_GREGORIAN, $mes, $d, $ano) , 0);

		// 0 = domingo e 6 = sábado
		if (!($dia_da_semana == 0 || $dia_da_semana == 6 || in_array($d, $feriados))) {
			$dias_letivos++;
		}
	}

	return $dias_letivos;
}


function dias_uteis_no_periodo($dtini,$dtfim) {
  $contador=0;

  $a_ini = explode("-",$dtini);
  $a_fim = explode("-",$dtfim);

  $dtini = mktime(0,0,0,$a_ini[1],$a_ini[2],$a_ini[0]);
  $dtfim = mktime(0,0,0,$a_fim[1],$a_fim[2],$a_fim[0]);

  if ($dtini > $dtfim) {
      return 0;
  } else {

    while ($dtini <= $dtfim) {
      if ((gmdate('w',$dtini) <> "0") && (gmdate('w',$dtini) <> "6")) {
		$contador++;
	  }
  	  $dtini = strtotime('+1 day', $dtini);
    }
    return $contador;
  }
}



?>