<?php
/*
 * c_coaching_lista_presenca_impressao.php
 * Autor: Alex
 * 17/08/2011 16:30:00
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

session_start();
if($_SESSION['id_usuario'] == ''){
    echo 'Usuário não logado.';
    exit;
}

require('../../../../conecta.php');
require('../../../../functions.php');
require('../../../../classes/class.DataHora.php');

$IdAgenda = $_GET['id_agenda'];

$DataHora = new DataHora();

$QryAgenda = query("SELECT  t1.*,
                            t2.nome_usuario AS nome_instrutor,
                            t3.nome_curso,
                            t4.nome_local_curso
                        FROM 
                            c_coaching_agenda_curso t1 
                        INNER JOIN 
                            is_usuario t2 ON t1.id_instrutor = t2.numreg 
                        INNER JOIN 
                            c_coaching_curso t3 ON t1.id_curso = t3.numreg
                        INNER JOIN 
                            c_coaching_local_curso t4 ON t1.id_local_curso = t4.numreg
                        WHERE 
                            t1.numreg = '".$IdAgenda."'");
$ArAgenda = farray($QryAgenda);

$ArrayDatas = array();
$QryAgendaDatas = query("SELECT dt_curso FROM c_coaching_agenda_curso_detalhe WHERE id_agenda_curso = '".$IdAgenda."'");
while($ArAgendaDatas = farray($QryAgendaDatas)){
    $ArrayDatas[] = $ArAgendaDatas['dt_curso'];
}

$ArrayClientes = array();
$SqlClientes = "SELECT DISTINCT t1.id_pessoa,t2.razao_social_nome FROM c_coaching_inscricao_curso_detalhe t1 INNER JOIN is_pessoa t2 ON t1.id_pessoa = t2.numreg WHERE t1.id_agenda = '".$IdAgenda."' ORDER BY t2.razao_social_nome ASC";
$QryClientes = query($SqlClientes);
while($ArClientes = farray($QryClientes)){
    $ArrayClientes[$ArClientes['id_pessoa']] = $ArClientes['razao_social_nome'];
}

$ArrayStaff = array();
$SqlStaff = "SELECT DISTINCT t2.razao_social_nome FROM c_coaching_agenda_curso_staff t1 INNER JOIN is_pessoa t2 ON t1.id_pessoa_staff = t2.numreg WHERE t1.id_agenda = '".$IdAgenda."' ORDER BY t2.razao_social_nome ASC";
$QryStaff = query($SqlStaff);
while($ArStaff = farray($QryStaff)){
    $ArrayStaff[] = $ArStaff['razao_social_nome'];
}
$i = 0;
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>SB Coaching - Lista de Presen&ccedil;a</title>
        <link href="../../../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link href="../../../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />
        <link href="c_style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <table width="95%" class="c_tabela_lista_presenca">
            <tr>
                <td rowspan="2" colspan="1" width="470"><img src="../../../../images/logo_coaching.png"/></td>
                <td colspan="<?php echo count($ArrayDatas);?>" align="center" class="c_title"><?php echo strtoupper($ArAgenda['nome_curso'].'<br/>'.$ArAgenda['nome_local_curso']);?></td>
            </tr>
            <tr>
                <td colspan="<?php echo count($ArrayDatas)+1;?>" align="center" class="c_title"><?php echo $ArAgenda['nome_instrutor'];?></td>
            </tr>
            <tr>
                <td rowspan="2" colspan="1" class="c_title">NOME CLIENTE</td>
                <td colspan="<?php echo count($ArrayDatas);?>" align="center" class="c_title">ASSINATURA</td>
            </tr>
            <tr>
                <?php foreach($ArrayDatas as $Data){ 
                $StringData = $DataHora->getStringDataHora($Data);
                ?>
                <td class="c_title" align="center"><?php echo strtoupper($StringData['nome_dia_semana'].' '.$StringData['dia'].' DE '.$StringData['nome_mes']);?></td>
                <?php } ?>
            </tr>
            <?php foreach($ArrayClientes as $NumregCliente => $Cliente){ 
                $i++;
                ?>
                <tr style="height: 27px;">
                    
                    <td><?php echo ucwords(strtolower($Cliente));?></td>
                    <?php foreach($ArrayDatas as $Data){
                    $SqlAgendaDetalhe = "SELECT numreg,sn_realocada FROM c_coaching_inscricao_curso_detalhe WHERE id_agenda= '".$IdAgenda."' AND id_pessoa = '".$NumregCliente."' AND dt_curso = '".$Data."'";
                    $QryAgendaDetalhe = query($SqlAgendaDetalhe);
                    $ArAgendaDetalhe = farray($QryAgendaDetalhe);
                    if($ArAgendaDetalhe['sn_realocada'] == '1'){
                        $ConteudoTD = 'R';
                    }
                    else{
                        $ConteudoTD = '&nbsp;';
                    }
                    ?>
                    <td align="left"><?php echo $ConteudoTD;?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
                <tr style="height: 27px;">
                   
                    <td>STAFF</td>
                    <?php foreach($ArrayDatas as $Data){?>
                    <td align="left">&nbsp;</td>
                    <?php } ?>
                </tr>
            <?php foreach($ArrayStaff as $Staff){?>
                <tr style="height: 27px;">
                  
                    <td><?php echo ucwords(strtolower($Staff));?></td>
                    <?php foreach($ArrayDatas as $Data){?>
                    <td align="left">&nbsp;</td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </body>
</html>