<?php
set_time_limit(7200);

echo "*============================================================*<br>";
echo "Atualizar RFV, Score, Maior Compra e Status de Frequência <br>";
echo "*============================================================*<br>";
session_start();
require("../../conecta.php");
require("../../funcoes.php");
require("../../functions.php");

// Carregando Parâmetros
$ap = farray(query("select * from is_dm_param"));

$qtde_dias = $ap["qtde_dias_rfv"]*1;
$sn_calcula_score_soma = $ap["sn_calcula_score_soma"]*1;
$sn_calcula_score_peso = $ap["sn_calcula_score_peso"]*1;
$peso_recencia = $ap["peso_recencia"]*1;
$peso_frequencia = $ap["peso_frequencia"]*1;
$peso_valor = $ap["peso_valor"]*1;
$peso_inadimplencia = $ap["peso_inadimplencia"]*1;

if (empty($qtde_dias)) {
    $qtde_dias = 365;
}

$MicroTimeInicio = microtime(true);
$QtdeErro = 0;
$qtde_processada = 0;
$NumregLog = CriaLog('p_calculo_rfv');

$data_base = soma_dias($qtde_dias * -1);

echo "Data Base = " . $data_base . "<br>";
/* =========================================================================================================== */
// Importa Clientes
/* =========================================================================================================== */

echo "Buscando Registros " . date("H:i:s") . '<br>';
query("UPDATE is_pessoa SET valor = '0', frequencia = '0', recencia = '0', dt_ult_nf_emitida = NULL, id_status_frequencia = 6  where sn_cliente = 1");

$u = 0;
$i = 0;
$q_rfv = query("select cd_emitente, sum(vl_tot_item) as valor, max(dt_emis_nota) as data, count(distinct nr_nota_fis) as qtde from is_dm_notas where dt_emis_nota >= '" . $data_base . "' group by cd_emitente");

while ($a_rfv = farray($q_rfv)) {

    //echo $a_rfv["cd_emitente"] . '<br>';

    // Calculando RFV
    $valor = number_format($a_rfv["valor"] * 1, 2, ".", "");
    $qtde = $a_rfv["qtde"] * 1;
    $recencia = count_days($a_rfv["data"], date("Y-m-d"));

    //Calculando Score
    $a_pessoa = farray(query("select sn_inadimplente from is_pessoa where id_pessoa_erp = '" . $a_rfv["cd_emitente"] . "'"));
    $a_faixa_recencia = farray(query("select pontos_score from is_score_faixa_recencia where faixa_de <= '$recencia ' and faixa_ate >= '$recencia'"));
    $a_faixa_recencia = farray(query("select pontos_score from is_score_faixa_recencia where faixa_de <= '$recencia ' and faixa_ate >= '$recencia'"));
    $a_faixa_freq = farray(query("select pontos_score from is_score_faixa_freq where faixa_de <= '$qtde ' and faixa_ate >= '$qtde'"));
    $a_faixa_valor = farray(query("select pontos_score from is_score_faixa_valor where faixa_de <= '$valor ' and faixa_ate >= '$valor'"));
    $score_recencia = $a_faixa_recencia["pontos_score"]*1;
    $score_freq = $a_faixa_freq["pontos_score"]*1;
    $score_valor = $a_faixa_valor["pontos_score"]*1;
    if ($sn_calcula_score_peso == '1') {
      $score_recencia = $score_recencia * $peso_recencia;
      $score_freq = $score_freq * $peso_frequencia;
      $score_valor = $score_valor * $peso_valor;
    }
    if($sn_calcula_score_soma=='1') {
      $score_total =  $score_recencia + $score_freq + $score_valor;
    } else {
      $score_total =  $score_recencia.$score_freq.$score_valor;
    }
    $peso_inadimplencia = $a_pessoa["sn_inadimplente"]*$ap["peso_inadimplencia"];
    $score_total = $score_total - $peso_inadimplencia;

    // Definindo faixa de Pré-Inatividade
    $a_status_freq = farray(query("select numreg from is_pessoa_status_frequencia where qtde_dias_de <= '$recencia' and qtde_dias_ate >= '$recencia'"));
    if ($a_status_freq["numreg"]) {
        $status_freq = $a_status_freq["numreg"];
    } else {
        $status_freq = 'NULL';
    }

    $sql = "UPDATE is_pessoa SET valor = '" . $valor . "', frequencia = '" . $qtde . "', recencia = '" . $recencia . "', dt_ult_nf_emitida = '" . $a_rfv["data"] . "', id_status_frequencia = ".$status_freq.", score = '".$score_total."' where id_pessoa_erp = '" . $a_rfv["cd_emitente"] . "'";

//	  echo $sql;


    $i = $i + 1;
    $rq = query($sql);

    if ($rq != "1") {
        if(TipoBancoDados == 'mysql'){
            $MensagemErro = mysql_error();
        }
        elseif(TipoBancoDados == 'mssql'){
            $MensagemErro = mssql_get_last_message();
        }
        else{
            $MensagemErro = '';
        }
        $QtdeErro++;
        GravaLogDetalhe($NumregLog,$sql,'Erro SQL: '.$MensagemErro,print_r($a_rfv,true),'Erro');
        echo $sql;
    }
    $qtde_processada++;
}

// Atualizando campos de Data e Valor da Maior Compra
$q_maior_compra = query("select cd_emitente, max(vl_tot) as valor from is_dm_notas_cab group by cd_emitente");
while ($a_maior_compra = farray($q_maior_compra)) {
    $a_dt_maior_compra = farray(query("select dt_emis_nota as data from is_dm_notas_cab where cd_emitente = '".$a_maior_compra["cd_emitente"]."' and vl_tot = '".$a_maior_compra["valor"]."'"));
    $sql = "UPDATE is_pessoa SET dt_maior_compra = '" . $a_dt_maior_compra["data"] . "', vl_maior_compra = '" . $a_maior_compra["valor"] . "' where id_pessoa_erp = '" . $a_maior_compra["cd_emitente"] . "'";
    $rq = query($sql);
    if ($rq != "1") {
        echo $sql;
    }
}
/* =========================================================================================================== */
// Fecha Conexões 
/* =========================================================================================================== */

echo 'Fim do Processamento : Total' . ($i) . ' ' . date("H:i:s");

FinalizaLog($NumregLog,$MicroTimeInicio,0,0,$QtdeErro,0,$qtde_processada);

function count_days($beginDate, $endDate) {

    $date1 = explode("-", $beginDate);
    $date2 = explode("-", $endDate);

    $begin = mktime(12, 0, 0, $date1[1], $date1[2], $date1[0]);
    $end = mktime(12, 0, 0, $date2[1], $date2[2], $date2[0]);

    return number_format(( ( $end - $begin ) / 84600), 0, "", ""); // 84600 = numero de segundos num dia
}

?>