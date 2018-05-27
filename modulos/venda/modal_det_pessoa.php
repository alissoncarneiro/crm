<?php
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
if(!isset($_POST['numreg'])){
    echo 'Conta não encontrada';
    exit;
}
require('../../conecta.php');
require('../../functions.php');
require('../../classes/class.Pessoa.php');

$NumregPessoa = $_POST['numreg'];
$Pessoa = new Pessoa($NumregPessoa);
?>
<span>
    <h3><strong><em><?php echo $Pessoa->getDadoPessoa('razao_social_nome');?></em></strong></h3>
    <h2>Informações Estratégicas:</h2>
    Score: <strong><?php echo $Pessoa->getDadoPessoa('score');?></strong><br />
    Satisfação do Cliente: <strong><?php echo $Pessoa->getDadoPessoa('satisfacao');?></strong><br /><br />
    Recência: <strong><?php echo $Pessoa->getDadoPessoa('recencia');?></strong><br />
    Frequência: <strong><?php echo $Pessoa->getDadoPessoa('frequencia');?></strong><br />
    Valor: <strong><?php echo number_format($Pessoa->getDadoPessoa('valor'),2,',','.');?></strong><br /><br />
    Atendimentos Pendentes: <strong><?php echo $Pessoa->getAtendimentosPendentes();?></strong><br />
</span>