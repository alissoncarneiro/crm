<?php
	header("Content-Type: text/html; charset=ISO-8859-1",true);
	require_once("../../conecta.php");
	if(!empty ($_POST['id_pesquisa'])){
?>  
<div class="textbox" style="width:100%; text-align:left">
<?php
		$id_pesquisa = $_POST['id_pesquisa'];
		$titulo_relatorio = "Pesquisa";
		
		$sqlNumeroRespostas = "
								select 
									count(*) as numero_respostas
								from
									is_script_realizado
								where
									id_script = 4
								group by id_pergunta
								limit 1
							";

		$qryNumeroRespostas = mysql_query($sqlNumeroRespostas);	
		$arNumeroRespostas = mysql_fetch_assoc($qryNumeroRespostas);
		$numeroRespostas = $arNumeroRespostas['numero_respostas']; 
	
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
								id_script = $id_pesquisa
							order by pergunta.ordem";
						
		$qryPerguntas = mysql_query($sqlPerguntas);
		$numRows = mysql_num_rows($qryPerguntas);
		if($numRows == 0){
			echo "<p class='sem_pergunta'>Nenhum registro encontrado</p>";
			exit;
		}

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
				<p class="nome_pessoa"><strong>Relat&oacute;rio: </strong><?=$titulo_relatorio?></p>
				<p class="nome_pessoa"><strong>Pessoas que responderam à pesquisa: </strong><?=$numeroRespostas?></p>
				<hr>
				<?php	
			}
		
		
?>	
            <p class="texto_pergunta" style="font-size:18px; font-weight:bold"><?=$numero_pergunta?>) <?=$nome_pergunta?></p>
			<div style="position:relative; float:left; width:100%">
<?php
			$n++;
		
		/////////////////// RESPOSTAS
			$sqlResposta = "select
								teste.id_resposta, 
								teste.nome_resposta,
								(select count(*) from is_script_realizado where id_script = teste.id_script and id_pergunta = teste.id_pergunta) as total_pessoas
							from
								is_script_realizado as teste
							where
								id_script = $id_pesquisa and id_pergunta = $id_pergunta
							
							order by teste.numreg";

			$qryResposta = mysql_query($sqlResposta);
			$totalRow = mysql_num_rows($qryResposta);
			$arRespostaGrafico = "";
			if($totalRow > 0 ){
				$nPessoa = 0;
				while($arResposta = mysql_fetch_assoc($qryResposta)){
					$totalPessoaPergunta = $arResposta['total_pessoas'];
					$arIdResposta = explode("||",$arResposta['id_resposta']);
					$arNomeResposta = explode("||",$arResposta['nome_resposta']);
					if(count($arIdResposta) > 1){
						foreach($arIdResposta as $key => $valResposta){
							$arRespostaGrafico[] = array(
														"id_resposta" => trim($valResposta), 
														"nome_resposta" => trim($arNomeResposta[$key]), 
													);
						}
					}else{
						$arRespostaGrafico[] = $arResposta;					
					}
					
				}

				$arGrafico = "";
				$arAgrupaResposta = "";
				$arGraficoTotal = "";

				foreach($arRespostaGrafico as $key => $arResposta){
					$resposta = $arResposta['nome_resposta'];
					$id_resposta = $arResposta['id_resposta'];
					$arAgrupaResposta[] = $id_resposta;
					$total_resposta = contaValor($arRespostaGrafico,$id_resposta);
					$arGraficoTotal[$id_resposta] = array(
										resposta => htmlentities($resposta),
										totalResposta => $total_resposta,
										id_resposta => $id_resposta,
										resposta_original => htmlentities(getResposta($id_resposta))
									);
				}
				
				foreach($arGraficoTotal as $val){
					$arGrafico[] = $val;
				}
				
				$arLegenda = "";
?>
				 <script type="text/javascript" language="javascript">
                    var chart, titulo, cor;
                    var chartData = <?=html_entity_decode(json_encode($arGrafico))?>;
        
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
                    chart.marginLeft = 0;
                    chart.position = "left";
                    
                    chart.addListener("clickSlice", function(e) { 
                        abre_tela_nova('../../gera_cad_lista.php?pfuncao=script_realizado&pdrilldown=1&pfixo=`id_resposta`@like@s@pctlike'+e.dataItem.dataContext.id_resposta+'@pctlike@s@andid_pergunta=<?=$id_pergunta?>','grafdet','1200','590','1')
                    });																							
                    
                    chart.write("grafico<?=$id_pergunta?>");
        
                    for (var i in chart.chartData){
                        resposta_original = chart.chartData[i].dataContext.resposta_original;
                        cor = chart.chartData[i].color;
                        total = chart.chartData[i].dataContext.totalResposta;
                      //  console.log(chart.chartData[i]);				
                        $("#legenda<?=$id_pergunta?>").append('<p style="vertical-align:middle"><span style="font-size:36px; color:'+cor+'; vertical-align:middle">&#8226;</span> '+total+' - '+resposta_original+'<p>');
                    }
                        
                </script>
        
<?php 			print_v($arLegenda); ?>
 
                <div id="grafico<?=$id_pergunta?>" style="width:420px; height:350px; position:relative; float:left"></div>
                <div id="legenda<?=$id_pergunta?>" style="width:67%; border:#D9E6FF solid 1px; text-align:left; float:left; "></div>
                </div>                
<?php		
					if($nPessoa == 0){
						echo "<p>Total de pessoas que responderam esta pergunta: <strong>$totalPessoaPergunta</strong>";	
					}
					$nPessoa++;
					echo "<hr />";

			}else{
				echo "<p>Não há resposta.</p>";	
			}
		}
?>
        </div>
<?php
	}
	
	function contaValor($matriz, $valor){
		$n = 0;
		foreach($matriz as $array){
			foreach($array as $val){
				if($val == $valor) $n++;
			}
		}
		return $n;
	}	
	
	function print_v($ar){
		echo '<pre style="text-align:left">';
		print_r($ar);
		echo '</pre>';	
	}
	
	function getResposta($id){
		$sql = "select * from is_script_resposta where numreg = $id";
		$qry = mysql_query($sql);
		$ar = mysql_fetch_assoc($qry);
		return $ar['resposta'];	
	}
	
?>