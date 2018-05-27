<?php
/*
 * c_coaching_entrega_projeto_certificacao.php
 * Autor: Alisson
 * 14/05/2011 16:30:00
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
$QryAgendaDatas = query("SELECT dt_curso, day(MIN(dt_curso)) AS data1, day(max(dt_curso)) as data2 FROM c_coaching_agenda_curso_detalhe WHERE id_agenda_curso = '".$IdAgenda."'");

while($ArAgendaDatas = farray($QryAgendaDatas)){
    $ArrayDatas[] = $ArAgendaDatas['dt_curso'];
	$dataTratada = $ArAgendaDatas['data1'].' à '.$ArAgendaDatas['data2'];
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
        <table width="94%" class="c_tabela_lista_presenca">
            <tr>
                <td height="83" colspan="2"><img src="../../../../images/logo_coaching.png"/></td>
                <td width="460" colspan="<?php echo count($ArrayDatas);?>" align="center" class="c_title">
					<?php echo strtoupper($ArAgenda['nome_curso']);?> <br/><br/>
                    ENTREGA DE PROJETO DE CERTIFICAÇÃO <br/>
                    <?php echo $ArAgenda['nome_instrutor'];?>
                    
                </td>
            </tr>
            <tr>
                <td rowspan="2" colspan="2" class="c_title" align="center">NOME CLIENTE</td>
                <td colspan="<?php echo count($ArrayDatas);?>" align="center" class="c_title">
				<?php echo strtoupper($ArAgenda['nome_local_curso'].' - '.$dataTratada);?></td>
            </tr>
            <tr>
                <td height="25" align="center"><span class="c_title">ASSINATURA</span></td>
            </tr>   
             
            <?php foreach($ArrayClientes as $NumregCliente => $Cliente){ 
                $i++;
                ?>
                <tr style="height: 27px;">
                    
                    <td width="490" colspan="2"><?php echo ucwords(strtolower($Cliente));?></td>
                    <?php foreach($ArrayDatas as $Data){
                    $SqlAgendaDetalhe = "SELECT numreg,sn_realocada FROM c_coaching_inscricao_curso_detalhe
										WHERE id_agenda= '".$IdAgenda."' AND id_pessoa = '".$NumregCliente."' AND dt_curso = '".$Data."'";
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
                        </table>
    </body>
</html>