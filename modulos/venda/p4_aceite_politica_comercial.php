<?php
/*
 * p4_aceite_politica_comercial.php
 * Autor: Alex
 * 19/11/2010 15:51:00
 * Arquivo inicial do pedido ou or�amento
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
header("Content-Type: text/html;  charset=ISO-8859-1");
session_start();
require('includes.php');

/*
 * Verifica se a v�ri�vel de tipo da venda foi preenchida.
 */
if($_POST['ptp_venda'] != 1 && $_POST['ptp_venda'] != 2){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
elseif(empty($_POST['pnumreg'])){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
else{
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
    }
    else{
        $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
    }
    /*
     * Tratando os campos
     */
    $Venda->pfuncao = $_POST['pfuncao'];
}
$Venda->RecarregaValorUnitarioItensDB();

$Usuario = new Usuario($_SESSION['id_usuario']);
?>
<script>
    $(document).ready(function(){
        $(".botao_jquery").button();
        $("#btn_confirmar_aceite_politica_comercial").click(function(){
            if($("#chk_confirmar_aceite_politica_comercial").attr('checked')){
                alert('1');
            }
            else{
                alert('2');
            }
        });
    });
</script>
    <div id="venda_conteudo_aceite_politica_comercial">
        <h2>An�lise Comercial</h2>
        O <?php echo ucwords($Venda->getTituloVenda());?> ser� enviado para aprova��o comercial. <br />
        <input type="checkbox" id="chk_aceite_politica_comercial" />&nbsp;Estou de acordo<br /><br />
        <span style="font-size: 14px;font-weight: bold;">Justificativa:</span><br />
        <br />
        Este campo deve ser utilizado APENAS para justificativa.<br />
        <textarea cols="80" rows="6"></textarea><br /><br />
        <input type="button" id="btn_confirmar_aceite_politica_comercial" value="Confirmar" class="botao_jquery"/>
    </div>