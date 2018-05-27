<?php
/*
 * p4_venda_frete.php
 * Autor: Alex
 * 01/07/2011 14:49:09
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('includes.php');

$Usuario = ($_SESSION['id_usuario'] != '')?new Usuario($_SESSION['id_usuario']):null;
/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */
if($_POST['ptp_venda'] != 1 && $_POST['ptp_venda'] != 2){
    echo getError('0040010001', getParametrosGerais('RetornoErro'));
    exit;
}
elseif(empty($_POST['pnumreg'])){
    echo getError('0040010001', getParametrosGerais('RetornoErro'));
    exit;
}
else{
    if($_POST['ptp_venda'] == 1){
        $Venda = new Orcamento($_POST['ptp_venda'], $_POST['pnumreg']);
    }
    else{
        $Venda = new Pedido($_POST['ptp_venda'], $_POST['pnumreg']);
    }
    /* Tratando os campos */
    $Venda->pfuncao = $_POST['pfuncao'];
}
?>
<fieldset><legend>Valor Frete do <?php echo $Venda->getTituloVenda(); ?></legend>
    <form id="form_itens_frete" name="form_itens_frete" action="#" onsubmit="return false;">
        <input type="hidden" name="ptp_venda" value="<?php echo $_POST['ptp_venda']; ?>" />
        <input type="hidden" name="pnumreg" value="<?php echo $_POST['pnumreg']; ?>" />
        <strong>Peso Total: </strong><?php echo number_format_min($Venda->getDadosVenda('peso_total'), 1, ',', '.'); ?> Kg<br/>
        <strong>Valor Frete: </strong><input type="text" name="vl_total_frete" id="vl_total_frete" class="venda_campo_vl" <?php echo (($Venda->getDigitacaoCompleta())?' readonly="readonly"':'');?> value="<?php echo number_format_min($Venda->getVlTotalVendaFrete(),2,',','.');?>" />
        <?php if(!$Venda->getDigitacaoCompleta()){ ?>
        <img src="img/calculadora.png" alt="Calcular Frete" title="Calcular Frete" id="btn_calcular_frete" />
        <br/>
        <input type="button" id="btn_salvar_frete" class="botao_jquery" value="Salvar" />
        <?php } ?>
    </form>
</fieldset>
<script type="text/javascript">
    $(document).ready(function(){
        $(".botao_jquery").button();

        $("#btn_salvar_frete").click(function(){
            $.ajax({
                url: "p4_venda_frete_post.php",
                global: false,
                type: "POST",
                data: ({
                    ptp_venda:$("#ptp_venda").val(),
                    pnumreg:'<?php echo $Venda->getNumregVenda();?>',
                    vl_total_frete: $("#vl_total_frete").val()
                }),
                dataType: "html",
                async: true,
                beforeSend: function(){

                },
                error: function(){
                    alert('Erro com a requisição');
                },
                success: function(responseText){
                    alert(responseText);
                }
            });
        });
        
        $("#btn_calcular_frete").click(function(){
            $.ajax({
                url: "p4_venda_frete_calc.php",
                global: false,
                type: "POST",
                data: ({
                    ptp_venda:$("#ptp_venda").val(),
                    pnumreg:'<?php echo $Venda->getNumregVenda();?>'
                }),
                dataType: "xml",
                async: true,
                beforeSend: function(){
                    MaskLoading('Mostrar');
                },
                error: function(){
                    MaskLoading('Ocultar');
                    alert('Erro com a requisição');
                },
                success: function(xml){
                    MaskLoading('Ocultar');
                    var Resposta = $(xml).find("resposta");
                    var Status = Resposta.find("status").text();
                    var Acao = Resposta.find("acao").text();
                    var Mensagem = Resposta.find("Mensagem");
                    
                    if(Status == '1'){
                        var VlTotalFrete = Resposta.find("vl_total_frete").text();
                        $("#vl_total_frete").val(VlTotalFrete);
                    }
                    else{
                        if(Acao == 1){
                            
                        }
                    }
                }
            });
        }).css("cursor","pointer");
    });
</script>