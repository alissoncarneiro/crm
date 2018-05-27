<?php
/*
 * index.php
 * Autor: Alex
 * 18/10/2010 15:02:00
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header('Content-Type: text/html; charset=utf-8');


session_start();
if($_GET['debug'] == 'true'){ $_SESSION['debug'] = true; } else { unset($_SESSION['debug']); }
if($_SESSION['id_usuario'] == ''){
    include('nao_logado.php');
    exit;
}

require('includes.php');

$Usuario = new Usuario($_SESSION['id_usuario']);

/*
 * Verifica se a variável de tipo da venda foi preenchida.
 */
if($_GET['ptp_venda'] == 1 || $_GET['ptp_venda'] == 2){
    if(empty($_GET['pnumreg']) || $_GET['pnumreg'] == '-1'){
        if($_GET['ptp_venda'] == 1){
            $Venda = new Orcamento($_GET['ptp_venda']);
        }
        elseif($_GET['ptp_venda'] == 2){
            $Venda = new Pedido($_GET['ptp_venda']);
        }
        if($Venda->getNumregVenda() == ''){
            echo $Venda->getMensagem();
            if($_SESSION['debug'] === true){
                echo $Venda->getMensagemDebug();
            }
            exit;
        }

        $Url = new Url();
        $Url->setUrl(curPageURL());
        $Url->AlteraParam('pnumreg',$Venda->getNumregVenda());
        $Url->AlteraParam('ppagina','p1');
        header("Location:".$Url->getUrl());
        exit;
    }
    else{
        $VisualizarRevisao = ($_GET['pvisualizar_revisao'] == '1')?true:false;
        if($_GET['ptp_venda'] == 1){
            $Venda = new Orcamento($_GET['ptp_venda'],$_GET['pnumreg'],true,true,$VisualizarRevisao);
        }
        elseif($_GET['ptp_venda'] == 2){
            $Venda = new Pedido($_GET['ptp_venda'],$_GET['pnumreg'],true,true,$VisualizarRevisao);
        }
    }
} else{
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}

/*
 * Validando se a data de validade já expirou, caso sim, recalcula
 */
$ExibeMensagemVendaAtualizada = false;
if($Venda->isOrcamento() && !$Venda->getDigitacaoCompleta() && $_GET['ppagina'] == 'p1'){ /* Se ? um or?amento e a venda ainda nao est? completa, e ? a pagina 1 */
    $MkTimeHoje = make_time(date("Y-m-d H:i:s"));
    $MkTimeDtValidade = make_time($Venda->getDadosVenda('dt_validade_venda'));
    if($MkTimeHoje > $MkTimeDtValidade){ /* Se a data de hoje for maior que a data de validade da venda faz os rec?lculos da venda */
        if($Venda->GeraRevisaoVenda() && $Venda->RecalculaVenda()){
            $Mensagem = '<div class="error"><span style="font-size:18px;color:#FF0000;"><span style="font-size:22px;">ATENÇÃO</span><br/>A validade do orçamento expirou, foi gerada uma nova revisão e todos os dados foram recalculados com base na data atual.</span></div>';
            $AtualizarPagina = true;
        }
        else{
            $Venda->ApagaRevisaoBD($Venda->NumregRevisaoGerada);
            $Mensagem = '<div class="error">A data de validade do orçamento expirou. Porém houve erros ao gerar a revisão e os recalculos. O Orçamento não poderá ser finalizado até que os recálculos sejam efetuados.</div>';
            if(QueryDebug == 1){
                $Mensagem .= $Venda->getMensagemDebug();
            }
            $AtualizarPagina = false;
        }
        $ExibeMensagemVendaAtualizada = true;
    }
}
$VendaParametro = new VendaParametro();

$Venda->pfuncao = $_GET['pfuncao'];
$Venda->PassoAtual = $_GET['ppagina'];
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo ucwords($Venda->getTituloVenda());?> N&ordm; <?php echo $Venda->getDadosVenda('id_venda_cliente');?> <?php echo $Venda->getNumregVenda();?></title>
        <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link href="../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />
        <link href="../../css/jquery.autocomplete.css" rel="stylesheet" type="text/css" />
        <link href="../../css/jquery.dlg.css" rel="stylesheet" type="text/css" />
        <link href="../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/ui.notify.css" rel="stylesheet" type="text/css" />

        <link href="../../css/enhanced.css" rel="stylesheet" type="text/css" />

        <link href="estilo_venda.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquery.qtip.js"></script>

        <script type="text/javascript" src="../../js/jquery.dlg.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.easing.js"></script>

        <script type="text/javascript" src="../../js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>

        <script type="text/javascript" src="../../js/jquery.notify.js"></script>

        <script type="text/javascript" src="../../js/jquery.autocomplete.js"></script>

        <script type="text/javascript" src="../../js/AIM.js"></script>

        <script type="text/javascript" src="../../js/jquery.meio.mask.min.js"></script>

        <script type="text/javascript" src="js/modal_det_pessoa.js"></script>

        <script type="text/javascript" src="js/functions_venda.js"></script>
        <script type="text/javascript" src="js/functions_venda_custom.js"></script>

        <script type="text/javascript" src="../../js/jquery.fileinput.js"></script>

        <script type="text/javascript" src="../../js/function.js"></script>

        <script language="JavaScript">
            function maximizar() {
                window.moveTo (0,0);
                window.resizeTo (screen.availWidth, screen.availHeight);
            }
            maximizar();
            $(document).ready(function(){
                $.datepicker.setDefaults($.datepicker.regional['pt-BR']);

                $(".botao_jquery").button();

                $(".numeric").keyup(function () {
                    this.value = this.value.replace(/[^0-9\.]/g,'');
                });

                $(".btn_passo").click(function(){
                    window.location.href = $(this).attr("href");
                });

                $(".btn_sair,#btn_sair").click(function(){
                    $("#jquery-dialog").attr("title","Alerta");
                    $("#jquery-dialog").html('Deseja sair ?');
                    $("#jquery-dialog").dialog({
                        dialogClass: 'jquery-dialog',
                        position: 'center',
                        resizable: false,
                        buttons:{
                            "Confirmar": function(){
                                window.opener.focus();
                                window.close();
                            },
                            Cancelar: function(){$(this).dialog("close");}},
                        modal: true,
                        show: "fade",
                        hide: "fade"
                    });
                });

                $("#notify-container").notify();
                $('<div id="qtip-blanket"></div>').css({
                    position: 'fixed',
                    top: $(document).scrollTop(),
                    left: 0,
                    height: '100%',
                    width: '100%',
                    opacity: 0.7,
                    backgroundColor: 'black',
                    zIndex: 5000
                }).appendTo(document.body).hide();

                <?php if($ExibeMensagemVendaAtualizada === true){?>
                $("#jquery-dialog").attr("title",'Alerta');
                $("#jquery-dialog").html('<?php echo $Mensagem;?>');
                $("#jquery-dialog").dialog({
                    buttons:{OK: function(){<?php if($AtualizarPagina){echo 'window.location.reload();';} else { echo '$(this).dialog("close");';}?>}},
                    modal: true,
                    show: "fade",
                    hide: "fade"
                });
                <?php } ?>
            });
        </script>
        <script language="javascript" src="tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
    </head>

    <body>
        <div id="notify-container" style="display: none;">
            <div id="basic-template">
                <a class="ui-notify-cross ui-notify-close" href="#">x</a>
                <span style="float:left; margin:2px 5px 0 0;" class="ui-icon ui-icon-alert"></span>
                <h1>#{title}</h1>
                <p>#{text}</p>
            </div>
        </div>
        <div id="jquery-dialog" style="display: none;"></div>
        <div id="jquery-dialog-alert" style="display: none;"></div>
        <div id="venda_topo">
            <div id="menu_horiz"></div>
        </div>
        <?php echo $Venda->getTitleBar();?>
        <div>
        <?php
        switch($_GET['ppagina']){
            case 'p1':
                include('p1_venda.php');
                break;
            case 'p2':
                include('p2_venda.php');
                break;
            case 'p3':
                include('p3_venda.php');
                break;
            case 'p4':
                include('p4_venda.php');
                break;
            default :
                include('p1_venda.php');
                break;
        }
        ?>
        </div>
        <?php if($_SESSION['debug'] === true){ ?>
        <fieldset>
            <legend>Debug</legend>
            <?php echo $Venda->getMensagemDebug(false,'<br/>');?>
        </fieldset>
        <?php } ?>
    </body>
</html>