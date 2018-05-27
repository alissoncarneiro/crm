<?php
require('../../../../conecta.php');

$programa = $_POST['programa'];
$coachee= $_POST['coachee'];

$sqlVisualizarCompetencias = "
                            select
                            macro.competencias_macro_nome as macro,
                            CONCAT('array(',  group_concat(DISTINCT \"'\",micro.competencias_micro_numreg, \"'=>'\",   micro.competencias_micro_nome,  \"'\"   ),\") \") as micro,

                            produto.competencias_produtos_nome as nome_produto,
                            produto.competencias_produtos_numreg as numreg
                                from tb_competencias_coach_coachee_fechamento as fechamento

                                  inner join tb_competencias_produtos as produto
                                  on produto.fk_grupo_pergunta = fechamento.fk_grupo_pergunta

                                  inner join tb_competencias_prudutos_micro_competencias as micro_competencias
                                  on micro_competencias.fk_competencias_produtos_numreg = produto.competencias_produtos_numreg

                                  inner join tb_competencias_micro as micro
                                  on micro.competencias_micro_numreg = micro_competencias.fk_competencias_micro_numreg

                                  inner join tb_competencias_macro as macro
                                  on macro.competencias_macro_numreg = micro.fk_competencias_macro_numreg

                            where fk_programa_coach_coachee = $programa and fk_competencias_coachee = $coachee
                            group by macro.competencias_macro_nome";

$qryVisualizarCompetencias = mysql_query($sqlVisualizarCompetencias);
while($arVisualizarCompetencias = mysql_fetch_assoc($qryVisualizarCompetencias)){
    eval("\$arVisualizarCompetenciasMicro = ".$arVisualizarCompetencias['micro'].";");
    echo "<div style='padding:2px 0px; background-color:#A9A9AA'><p style='margin:0px 8px;'>".utf8_encode($arVisualizarCompetencias['macro'])."</p></div></br>";
    foreach($arVisualizarCompetenciasMicro as $key => $val){
        echo "<div style='padding-left:20px'>".$val."</div><br>";
    }

    
}