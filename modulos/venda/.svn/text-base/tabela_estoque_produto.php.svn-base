<?php
/*
 * MostraSaldoEstoquePedido.php
 * Autor: Lucas
 * 24/11/2010 18:11:00
 *
 * Arquivo que exibe o saldo de estoque com detalhes no produto - Modelo da AlphaPrint
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

$odbc_c = true;
include('../../conecta.php');

include('../../classes/class.ConsultaEstoque.php');
include('../../classes/class.ConsultaEstoqueCustom.php');
include('../../classes/class.Ub.php');

$SaldoEstoque = new ConsultaEstoqueCustom();

//Para AlphaPrint

$SaldoEstoque->setCNX($cnx2); //creio que esse valor nao deve mudar, ele somente é necessario se a onsulta for feita no ERP que use ODBC
$SaldoEstoque->setTpConsulta('erp'); //deve ser pego por parametro
$SaldoEstoque->setIdEstabelecimento($_POST['id_estabelecimento']); //substituir pela variavel do estabelecimento do pedido
$SaldoEstoque->setIdProduto($_POST['id_produto']); //substituir pela variavel do item do pedido
$ar_SaldoEstoque = $SaldoEstoque->getSaldoEstoque();

//Fim para AlphaPrint
?>
    <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="bordatabela">
        <tr>
            <td bgcolor="#DAE8F4" class="tit_tabela">Estabelecimento</td>
            <td bgcolor="#DAE8F4" class="tit_tabela">Qtde. em Estoque</td>
            <td bgcolor="#DAE8F4" class="tit_tabela">Lote</td>
            <td bgcolor="#DAE8F4" class="tit_tabela">Validade</td>
            <td bgcolor="#DAE8F4" class="tit_tabela">C&oacute;d. Refer.</td>
        </tr>
<?php
$i_estoque = 0;

foreach($ar_SaldoEstoque as $k => $v){
    $bg_color = ($i_estoque % 2 == 0) ? '#EAEAEA' : '#FFFFFF';
    $i_estoque++;
?>
        <tr bgcolor="<?php echo $bg_color; ?>">
            <td><?php echo $v[0]; ?></td>
            <td><?php echo number_format($v[1],2,',','.'); ?></td>
            <td><?php echo $v[2]; ?></td>
            <td><?php echo uB::DataEn2Br($v[3]); ?></td>
            <td><?php echo $v[4]; ?></td>
        </tr>
        <?php }?>
    </table>
    <input type="hidden" value="<?php echo $ar_SaldoEstoque[0][1]; ?>" name="validaestoque" id="validaestoque" />