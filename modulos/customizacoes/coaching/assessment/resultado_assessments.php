<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>SB Coaching - Relat&oacute;rio </title>
    </head>
    <body>

        <?php
        require('../../../../conecta.php');

        $programa = $_POST['programa'];
        $coachee= $_POST['coachee'];
        $sqlRelatorioCompleto ="SELECT
                              fechamento.*,
                              pergunta.*
                                FROM bd_crm_producao.tb_competencias_coach_coachee_fechamento as fechamento
                                  inner join bd_crm_producao.tb_competecias_grupo_pergunta as pergunta
                                  on pergunta.competecias_grupo_pergunta_numreg = fechamento.fk_grupo_pergunta
                              where fechamento.fk_programa_coach_coachee = '".$programa."' and fechamento.fk_competencias_coachee = '".$coachee."'";
        $qryRelatorioCompleto = mysql_query($sqlRelatorioCompleto);
        while($arRelatorioCompleto = mysql_fetch_assoc($qryRelatorioCompleto)){ ?>
        <div style="display: inline-block;">
                <div style="float: left;"><img src='images/<?=$arRelatorioCompleto['url_imagem']?>' width='120px'></div>
                <div style="padding: 20px 10px; font-weight: bold; display: inline-block;width: 490px;"><?=$arRelatorioCompleto['competecias_grupo_pergunta_nome']?></div>    
                <div style="padding: 0px 10px; display: inline-block;width: 490px;  "><?=$arRelatorioCompleto['descricao']?></div>    
            </div>
            
        <?php } ?>
        
    </body>
</html>    