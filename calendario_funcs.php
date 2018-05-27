<?php
include "conecta.php";
@header( "Content-Type: text/html;  charset=utf-8", true );




session_start();

	function calcularDiaSemana($dia,$mes,$ano)
 {
  $s=(int)($ano / 100);
  $a=$ano % 100;

  if($mes<=2)
  {
   $mes+=10;
   $a--;
  }
  else $mes-=2;

  $ival=(int)(2.6*$mes-0.1);
  $q1=(int)($s / 4);
  $q2=(int)($a / 4);

  $dia_semana=($ival + $dia + $a + $q1 + $q2 - 2 * $s) % 7;

  if($dia_semana<0) $dia_semana+=7;

  return($dia_semana);
 }

 function gerarCalendario($mes,$ano,$usu_sel,$nmeses,$ncols,$datas,$rodapes=0,$leg=0)//$feriados,$marcados,$rodapes)
 {
  if(!($mes>0 && $mes<=12 && ($nmeses>0 && $nmeses<=12) &&
      ($ncols>0 && $ncols<=12) && ($mes+$nmeses<=13)))
  {
   $tabela="Erro ao gerar calendário: [mês=".$mes."] [ano=".$ano.
           "] [número de meses=".$nmeses."] [tabelas por linha=".$ncols."]<br>";
  }
  else
  {

   echo "<style type='text/css'>".$dados."</style>";

   //Calcula em que dia da semana � o dia 1/$mes/$ano
   $dia_semana=calcularDiaSemana(1,$mes,$ano);
   $bisexto=(($ano % 4 ==0) || ($ano % 100==0)); //Verifica se o ano � bisexto
   $ndias=array(31,($bisexto ? 29 : 28),31,30,31,30,31,31,30,31,30,31); //Vetor com o n�mero de dias de cada m�s
   $meses=array("Janeiro","Fevereiro","Março","Abril","Maio","Junho",
                "Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
   $dias=array("Dom.","Seg.","Ter.","Qua.","Qui.","Sex.","Sab.");

   $idx=$mes-1;
   $total=$idx+$nmeses; //Total de meses a serem considerados
   $dia=$daux=$dia_semana;

    for($i=0;$i<count($datas);$i++)
     $qtd[$i]=count($datas[$i]);

   $nq=count($qtd);

   $tabela="<table width='100%'>"; //Inicia a tabela geral (que suportar� as demais tabelas de meses)

   while($idx<$total)
   {
    $tabela=$tabela."<tr>";
    for($ms=0; $ms<$ncols && $idx<$total; $ms++)
    {

     $temp_tb="<td valign='top'><table class='tabela' border='0' cellpadding='2' cellspacing='2' width='100%' height='380'>
              <tr><td colspan=7  class='cabecalho'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$meses[$idx].              "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td></tr><tr>"; //Cria uma tabela para o m�s atual

     for($idx2=0;$idx2<7;$idx2++) //Gera o cabe�alho da tabela do m�s atual
      $temp_tb=$temp_tb."<td class='td_semana'><b>".$dias[$idx2]."</b></td>";
     $temp_tb=$temp_tb."</tr>"; //Fecha o cabe�alho

     $cnt_dias=1; //Inicializa o contador de dias
     $temp_ln="";
     $nl=0;

     while($cnt_dias<=$ndias[$idx])
     {
      $temp_ln=$temp_ln."<tr>"; //Cria uma linha da tabela do m�s atual
      for($d=0;$d<7 && $cnt_dias<=$ndias[$idx];$d++)
      {
       if($d>=$dia || $dia==0)
       {
        $classe="";
	$maux=$idx+1;

	//A rotina abaixo verifica se o dia atual � um feriado ou um dia marcado
	//onde $datas cont�m os dois vetores $feriados e $marcados
	for($i=0;$i<$nq && $classe=="";$i++)
	{
	 for($i1=0;$i1<$qtd[$i] && $classe=="";$i1++)
	 {
	  //Caso seja um intervalo de dias
	  if(strpos($datas[$i][$i1],"-")==2)
	  {
	   $d1=substr($datas[$i][$i1],0,2); //Obt�m o primeiro dia
	   $d2=substr($datas[$i][$i1],3,2); //Obt�m o segundo dia
	   $m=substr($datas[$i][$i1],6,2); //Obt�m o m�s do intervalo
	  }
	  else //Caso seja um dia
	  {
	   $d1=substr($datas[$i][$i1],0,2); //Obt�m o dia
  	   $d2=0;
	   $m=substr($datas[$i][$i1],3,2); //Obt�m o m�s
	  }

	  //Atribui uma classe CSS � c�lula (dia) atual da tabela caso
	  //o m�s atual $maux seja igual ao m�s obtido de um dos vetores $m ($feriado ou $marcado)
	  //Verifica se o dia atual $cnt_dias est� no intervalo de dias ou se � igual
	  //ao dia obtido

   	  if($m==$maux && (($cnt_dias>=$d1 && $cnt_dias<=$d2) ||
	    ($cnt_dias==$d1))) $classe="td_marcado".($i+1);//$valor[$i];
	 }
	}

	if($classe=="") //Caso a classe ainda n�o esteja definida ap�s o for acima
	 $classe=($d==0 ? "td_marcado0" : "td_dia");

	if(strlen($cnt_dias) <= 1) { $cnt_diass = "0" . $cnt_dias; } else { $cnt_diass = $cnt_dias; }
	if(strlen($mes) <= 1) { $mes = "0" . $mes; }
	$data_query =  $ano.'-'.$mes.'-'.$cnt_diass;

    $lista_sn_bloquear_leitura = $_SESSION["sn_bloquear_leitura"];
    if($_POST['usu_sel'] != '' && $lista_sn_bloquear_leitura == "N"){
		$lista_vs_id_usuario = $_POST['usu_sel'];
	}
	else{
		$lista_vs_id_usuario = $_SESSION['id_usuario'];
	}
	//echo $lista_vs_id_usuario;
	//echo "SELECT * FROM is_atividade i WHERE dt_prev_fim = '$data_query' and id_usuario_resp = '$lista_vs_id_usuario' order by dt_prev_fim";

	$sql_qry = "SELECT * FROM is_atividade i WHERE dt_prev_fim = '$data_query' and id_usuario_resp = '$usu_sel' order by dt_prev_fim";
    $calendario_query = query($sql_qry);
	//Cria a c�lula referente ao dia atual
	$temp_ln = $temp_ln."<td class='".$classe."'>";
	$temp_ln = $temp_ln.'<div name="div_dt" id="div_dt" style="width:100%; height:80px;overflow:auto;">';
	$temp_ln = $temp_ln.'<b>'.$cnt_dias++.'</b><br>';

	while($calendario_rows = farray($calendario_query)) {
		$atividade_rows = farray(query("SELECT assunto, id_pessoa, hr_prev_fim, hr_inicio FROM is_atividade WHERE numreg = '".$calendario_rows["numreg"]."'"));
		$atividade_emp = farray(query("SELECT * FROM is_pessoa WHERE numreg = '".$calendario_rows['id_pessoa']."'"));
		$atividade_pes = farray(query("SELECT * FROM is_pessoa WHERE numreg = '".$calendario_rows['id_pessoa_contato']."'"));

		// Cores
	   $lista_tdstyle= 'style="color: #EBEBEB;"';
	   if (($calendario_rows["id_situacao"] != '4') && ($calendario_rows["id_situacao"] != '5')) {
		   if (($calendario_rows["dt_prev_fim"] < date("Y-m-d"))) {
			   $lista_tdstyle= 'style="color: #FF0000;"';
		   }
		   if (($calendario_rows["dt_prev_fim"] == date("Y-m-d"))) {
			   $lista_tdstyle= 'style="color: #000000;"';
		   }
		   if (($calendario_rows["dt_prev_fim"] > date("Y-m-d"))) {
			   $lista_tdstyle= 'style="color: #00FF00;"';
		   }
	   } else {
		   $lista_tdstyle= 'style="color: #C0C0C0;"';
	   }

		$temp_ln .= "<a href='#' title="."'".$atividade_rows["hr_inicio"].'-'.$atividade_rows["hr_prev_fim"]."'"." onclick=\"javascript:window.open('gera_cad_detalhe.php?pfuncao=atividades_cad_lista&pnumreg=" . $calendario_rows["numreg"] . "&psubdet=&pread=N&pnpai=&pfixo=&prefpai=N', 'Agenda', 'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=50,left=50');\">" . "<span ".$lista_tdstyle.">".$atividade_rows["hr_inicio"].' '.$atividade_rows["assunto"]." (" . $atividade_emp["razao_social_nome"]. " " . ")</span></a>";

		$temp_ln .= "<br><br>";

	}

	$temp_ln .= "</div></td>";



        $daux++;
        if($daux>6) $daux=0;
       }
       else $temp_ln=$temp_ln."<td>&nbsp;</td>";
      }
      $nl++;
      $temp_ln=$temp_ln."</tr>";
      $dia=0;
     }
     if($nl==5) $temp_ln=$temp_ln."<tr><td colspan=7>&nbsp;</td></tr>";
     $temp_tb=$temp_tb.$temp_ln;

     /*$k=$idx-($mes-1);
     if($rodapes[$k]!="") //Gera um rodap� para a tabela de m�s
     {
      $temp_tb=$temp_tb."<tr><td colspan=7 class='rodape'>".$rodapes[$k].
               "</td></tr></table><br></td>";
     }
     else $temp_tb=$temp_tb."</table></td>";*/

     $tabela=$tabela.$temp_tb;
     $dia=$daux;
     $idx++; //Passa para o pr�ximo m�s
    }
    //$tabela=$tabela."</tr>";
   }
   $tabela=$tabela."</table>";
  }
  return($tabela);
 }
?>