<?php
/*
 * c_coaching_lista_presenca.php
 * Autor: Alex
 * 17/08/2011 16:30:00
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");
setlocale(LC_ALL, 'ptb', 'pt_BR', 'portuguese-brazil', 'bra', 'brazil', 'pt_BR.utf-8', 'pt_BR.iso-8859-1', 'br');
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

$ArrayStaffs= array();
$SqlStaffs = "SELECT DISTINCT t1.id_pessoa_staff,t2.razao_social_nome FROM c_coaching_agenda_curso_staff t1 INNER JOIN is_pessoa t2 ON t1.id_pessoa_staff = t2.numreg WHERE t1.id_agenda = '".$IdAgenda."' ORDER BY t2.razao_social_nome ASC";
$QryStaffs = query($SqlStaffs);
while($ArStaffs = farray($QryStaffs)){
    $ArrayStaffs[$ArStaffs['id_pessoa_staff']] = $ArStaffs['razao_social_nome'];
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
        <link href="../../../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../../js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $(".botao_jquery").button();

                $("#btn_confirmar_lista_presenca").click(function(){
                    if(confirm("Salvar a lista de presença ?")){
                        $("form").submit();
                    }
                });
                <?php foreach($ArrayDatas as $k => $Data){?>
                $("#chk_todos_<?php echo $k;?>").click(function(){
                    $(".chk_l<?php echo $k;?>").attr("checked",$(this).attr("checked"));
                });
                <?php } ?>
            });
        </script>
    </head>
    <body>
        <form action="c_coaching_lista_presenca_post.php" method="POST">
            <input type="hidden" name="id_agenda" id="id_agenda" value="<?php echo $IdAgenda;?>" />
        <table width="920" class="c_tabela_lista_presenca">
            <tr>
                <td rowspan="2" colspan="2" width="470"><img src="../../../../images/logo_coaching.png" alt="Sociedade Brasileira de Coaching"/></td>
                <td colspan="<?php echo count($ArrayDatas);?>" align="center" class="c_title"><?php echo strtoupper($ArAgenda['nome_curso'].'<br/>'.$ArAgenda['nome_local_curso']);?></td>
            </tr>
            <tr>
                <td colspan="<?php echo count($ArrayDatas)+1;?>" align="center" class="c_title"><?php echo $ArAgenda['nome_instrutor'];?></td>
            </tr>
            <tr>
                <td rowspan="2" colspan="2" class="c_title">NOME CLIENTE</td>
                <td colspan="<?php echo count($ArrayDatas);?>" align="center" class="c_title">PRESENTE</td>
            </tr>
            <tr>
                <?php foreach($ArrayDatas as $Data){
                $StringData = $DataHora->getStringDataHora($Data);
                ?>
                <td class="c_title" align="center"><?php echo strtoupper($StringData['nome_dia_semana'].' '.$StringData['dia'].' DE '.$StringData['nome_mes']);?></td>
                <?php } ?>
            </tr>
            <tr>
                <td class="c_title">&nbsp;</td>
                <td>&nbsp;</td>
                <?php foreach($ArrayDatas as $k => $Data){
                    $ConteudoTD = '<input type="checkbox" id="chk_todos_'.$k.'" />';
                ?>
                <td align="center"><?php echo $ConteudoTD;?> Marcar Todos</td>
                <?php } ?>
            </tr>
            <?php foreach($ArrayClientes as $NumregCliente => $Cliente){
                $i++;
                ?>
                <tr>
                    <td class="c_title"><?php echo $i;?></td>
                    <td><?php echo ucwords(strtolower($Cliente));?></td>
                    <?php foreach($ArrayDatas as $k => $Data){
                    $SqlAgendaDetalhe = "SELECT numreg,sn_realocada,sn_presente FROM c_coaching_inscricao_curso_detalhe WHERE id_agenda= '".$IdAgenda."' AND id_pessoa = '".$NumregCliente."' AND dt_curso = '".$Data."'";
                    $QryAgendaDetalhe = query($SqlAgendaDetalhe);
                    $ArAgendaDetalhe = farray($QryAgendaDetalhe);

                    if($ArAgendaDetalhe['numreg'] != ''){
                        $ConteudoTD = '<input type="checkbox" class="chk_l'.$k.'" name="chk_presente_'.$ArAgendaDetalhe['numreg'].'" id="chk_presente_'.$ArAgendaDetalhe['numreg'].'"'.(($ArAgendaDetalhe['sn_presente'] != '0')?' checked="checked"':'').' value="1" />';
                        if($ArAgendaDetalhe['sn_realocada'] == '1'){
                            $ConteudoTD .= '(Reposi&ccedil;&atilde;o)';
                        }
                    }
                    else{
                        $ConteudoTD = '&nbsp;';
                    }
                    ?>
                    <td align="center"><?php echo $ConteudoTD;?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
                <tr>
                    <td class="c_title">&nbsp;</td>
                    <td><strong>STAFF</strong></td>
                    <?php foreach($ArrayDatas as $Data){?>
                    <td align="left">&nbsp;</td>
                    <?php } ?>
                </tr>
            <?php $i=0; foreach($ArrayStaffs as $NumregStaff => $Staff){
                $i++;
                ?>
                <tr>
                    <td class="c_title"><?php echo $i;?></td>
                    <td><?php echo ucwords(strtolower($Staff));?></td>
                    <?php foreach($ArrayDatas as $k => $Data){
                    $SqlAgendaDetalhe = "SELECT numreg,sn_presente FROM c_coaching_agenda_staff_lista_presenca WHERE id_agenda= '".$IdAgenda."' AND id_pessoa_staff = '".$NumregStaff."' AND dt_curso = '".$Data."'";
                    $QryAgendaDetalhe = query($SqlAgendaDetalhe);
                    $ArAgendaDetalhe = farray($QryAgendaDetalhe);

                    if($ArAgendaDetalhe['numreg'] != ''){
                        $ConteudoTD = '<input type="checkbox" class="chk_l'.$k.'" name="chk_presente_staff_'.$ArAgendaDetalhe['numreg'].'" id="chk_presente_staff_'.$ArAgendaDetalhe['numreg'].'"'.(($ArAgendaDetalhe['sn_presente'] != '0')?' checked="checked"':'').' value="1" />';
                    }
                    else{
                        $ConteudoTD = '&nbsp;';
                    }
                    ?>
                    <td align="center"><?php echo $ConteudoTD;?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
        <p>
            <input type="button" class="botao_jquery" id="btn_confirmar_lista_presenca" value="Confirmar Lista de Presença">
        </p>
        </form>
    </body>
</html>