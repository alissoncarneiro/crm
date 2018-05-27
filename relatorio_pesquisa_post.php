<?php
	header("Content-Type: text/html;  charset=ISO-8859-1");
	require_once("../../conecta.php");
	if(!empty ($_POST['id_pesquisa'])){
?>  
<div class="textbox">
<?php
	$id_pesquisa = $_POST['id_pesquisa'];
	$titulo_relatorio = "Pesquisa";
	
	
		$sqlPerguntas = "select 
							pesquisa.nome_script as nome_pesquisa,    
							pergunta.ordem as numero_pergunta,
							pergunta.pergunta as nome_pergunta,
							pergunta.numreg as id_pergunta
							from
								is_script as pesquisa
									inner join
								is_script_pergunta as pergunta ON pergunta.id_script = pesquisa.numreg
							where
								id_script = $id_pesquisa";
						
	$qryPerguntas = mysql_query($sqlPerguntas);
	$numRows = mysql_num_rows($qryPerguntas);
	if($numRows == 0){
		echo "<p class='sem_pergunta'>Nenhum registro encontrado</p>";
		exit;
	}
?>
	
    <a href="#" class="print"><img src="images/icon_print.png" alt="Imprimir"></a>
<?php
	$n=0;

	while($arPerguntas = mysql_fetch_assoc($qryPerguntas)){
		$nome_pesquisa = $arPerguntas['nome_pesquisa'];
		$numero_pergunta = $arPerguntas['numero_pergunta'];
		$nome_pergunta = $arPerguntas['nome_pergunta'];
		$id_pergunta = $arPerguntas['id_pergunta'];
		
		if($n == 0){
			$nome_pesquisa = $arPerguntas['nome_pesquisa'];
			$nome_pessoa = $arPerguntas['nome_pessoa'];
			?>
			<h1><?=$nome_pesquisa?></h1>
			<p class="nome_pessoa"><strong>Relatório: </strong><?=$titulo_relatorio?></p>
			<hr>
			<?php	
		}
		
		
?>	
	<p class="texto_pergunta" style="font-size:18px; font-weight:bold"><?=$numero_pergunta?>) <?=$nome_pergunta?></p>
	<?php
		$n++;

/////////////////// RESPOSTAS
	$sqlResposta = "select 
						teste.nome_resposta,
						count(teste.nome_resposta) as total_resposta
					from
						is_script_realizado as teste
					where
						id_script = $id_pesquisa and id_pergunta = $id_pergunta
					group by teste.nome_resposta
					order by teste.numreg";
	$qryResposta = mysql_query($sqlResposta);
	while($arResposta = mysql_fetch_assoc($qryResposta)){
		$resposta = str_replace("|SEP|",", ",$arResposta['nome_resposta']);
		$total_resposta = $arResposta['total_resposta'];
		$arGrafico[] = array(
                resposta => 'resposta',
                totalResposta => 'total'
            );
?>
           
<?php
	}
?>
        <script type="text/javascript">
		$(".textbox").css({
				"width": "1100px",
				"background-size": "1100px"
			});
            var chart;
            var legend;
			var chartData = [<?=json_encode($arGrafico)?>];
                chart = new AmCharts.AmPieChart();
                chart.dataProvider = chartData;
                chart.titleField = "resposta";
                chart.valueField = "totalResposta";
				chart.labelRadius = -30;
				chart.labelText = "[[percents]]%";
				chart.depth3D = 10;
				chart.angle = 10;
				chart.color = "#000000";
				chart.titulo = "<?=$nome_pergunta?>";
				
				// LEGEND
		        var legend = new AmCharts.AmLegend();
                legend.position = "bottom";
                legend.borderAlpha = 0.3;
                legend.horizontalGap = 10;
                legend.switchType = "v";
                chart.addLegend(legend)
				
                 chart.write("grafico<?=$id_pergunta?>");
				
        </script>
 
    <div id="grafico<?=$id_pergunta?>" style="width:1000px; height:400px; margin-left:30px;"></div>
    
<hr />
    	
<?php }?>
</div>



 
<?php
	}
?>