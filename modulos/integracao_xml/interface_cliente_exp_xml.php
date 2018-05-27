<?php
include('../../conecta.php');
include('../../functions.php');
include('../../funcoes.php');
include('../../classes/class.uB.php');
include('../../classes/class.Usuario.php');
include('../../classes/class.GeraLinhaTxt.php');
include('class.ClienteExpXml.php');

$TXT = NULL;

//$NomeArquivo = 'cliente_conteudo.lst';

$ModoExportacao = 1;

if($ModoExportacao == 1){ // Grava o Arquivo no em um diretório parametrizado
    $DiretorioArquivo = GetParam('diretorio_xml_raiz').'crm/clientes/pendentes_integracao/';

    if(!is_dir($DiretorioArquivo)){
        echo 'Diretório parametrizado para geração do arquivo inválido';
        exit;
    }

    $ClienteExpXml = new ClienteExpXml();
    $ClienteExpXml->DiretorioArquivo = $DiretorioArquivo;
    $ClienteExpXml->CarregaClientesBD();
    $TXT = $ClienteExpXml->getTxt();

    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>Integração OASIS</title>
        <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link href="../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />

        <link href="../../css/jquery.autocomplete.css" rel="stylesheet" type="text/css" />
        <link href="../../css/jquery.dlg.css" rel="stylesheet" type="text/css" />
        <link href="../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />

        <link href="estilo_venda.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquery.qtip.js"></script>

        <script type="text/javascript" src="../../js/jquery.dlg.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.easing.js"></script>

        <script type="text/javascript" src="../../js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>

        <script type="text/javascript" src="../../js/jquery.autocomplete.js"></script>

        <script type="text/javascript" src="js/modal_det_pessoa.js"></script>

        <script type="text/javascript" src="js/functions_venda.js"></script>

    </head>

    <body>
        <script>
        $(document).ready(function(){
            $.dlg({
                title: 'Alerta',
                content: '<?php echo $ClienteExpXml->getQuantidadeRegistroExportados()." Registros exportados";?>',
                drag: true,
                focusButton :'ok',
                onComplete: function(){
                    window.opener.focus();
                    window.close();
                }
            });
        });
        </script>
    </body>
</html>
    <?php
}

/*
$ArSqlInsert = array();
$ArSqlInsert['dthr_exportacao'] = date("Y-m-d H:i:s");
$ArSqlInsert['id_usuario'] = $_SESSION['id_usuario'];
$ArSqlInsert['texto'] = $TXT;

$SqlInsert = AutoExecuteSql(TipoBancoDados,'is_cliente_txt_datasul',$ArSqlInsert,'INSERT');
iquery($SqlInsert);
*/
?>