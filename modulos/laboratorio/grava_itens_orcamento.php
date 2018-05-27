<?php
# grava_itens_orcamento
# Expression package is undefined on line 3, column 5 in Templates/Scripting/EmptyPHP.php.
# Autor: Rodrigo Piva
# 30/08/2011
#
# Log de Alterações
# yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
#

include_once('../../conecta.php');
include_once('../../functions.php');
//pre($_POST);

$IdAtendimento = $_GET['pnumreg'];
$SqlComponentesManutencao = "SELECT * FROM is_produto_orcamento_lab WHERE id_atividade = '".$IdAtendimento."'";
$QryComponentesManutencao = query($SqlComponentesManutencao);
$i = 0;
$Salvo = true;
while($ArComponenteManutencao = farray($QryComponentesManutencao)){

    $CobraItem = ($_POST['c'.$i]=='on')?'1':'0';
    $IdMotivo  = $_POST['b'.$i];
    $Obs       = $_POST['obs'.$i];

    $i++;

    $ArUpdate = array(
        'numreg'            => $ArComponenteManutencao['numreg'],
        'sn_orcamento'      => $CobraItem,
        'id_motivo_n_orcto' => $IdMotivo,
        'obs'               => $Obs
    );
    $Sql = AutoExecuteSql(TipoBancoDados,'is_produto_orcamento_lab', $ArUpdate, 'UPDATE', array('numreg'));
    $qry = query($Sql);
    if (!$qry) {
        echo $Sql;
    }
}

if($Salvo){
    echo alert("Registro alterado com sucesso");
}else{
    echo alert("Ocorreu um erro ao salvar o pedido");
}
?>
<script>
    history.back();
</script>


