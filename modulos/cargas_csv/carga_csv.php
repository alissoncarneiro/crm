<?php
header("Content-Type: text/html; charset=ISO-8859-1");
include('../../conecta.php');
include('../../functions.php');
$ArSelectCarga = array();
$ArSelectCarga[] = array('is_param_cfop.php','Par&acirc;metros CFOP');
$ArSelectCarga[] = array('is_aliquota_iva.php','Par&acirc;metros Aliquotas IVA');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>OASIS :: Cargas de Arquivos CSV</title>
<link rel="stylesheet" type="text/css" href="../../estilos_css/estilo.css" />
<link rel="stylesheet" type="text/css" href="../../estilos_css/cadastro.css" />
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#btn_submit").click(function(){
           if(confirm('Todos os parâmetros estão corretos ?')){
               $("#form_carga_csv").attr("action",$("#edtarquivovargacsv").val());
               $("#form_carga_csv").submit();
           }
        });
    });
</script>
<style type="text/css">
<!--
body {
    margin-left: 0px;
    margin-top: 0px;
    margin-right: 0px;
    margin-bottom: 0px;
}
legend{
    font-weight:bold;
    font-size:14px;
}
span{
    color:#F00;
    font-size:18px;
    font-weight:bold;
}
-->
</style>
</head>
<body>
<div id="menu_horiz">
    <span style="font-size:16px; font-weight: bold; color:#000000;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Importação CSV</span></div>
<hr size="1" noshade="noshade" />
<form action="" method="post" enctype="multipart/form-data" name="form_carga_csv" id="form_carga_csv">
<fieldset>
    <legend>Par&acirc;metros</legend>
    Selecione a importa&ccedil;&atilde;o: <br /><select name="edtarquivovargacsv" id="edtarquivovargacsv"><option value="" selected="selected"></option><?php foreach($ArSelectCarga as $k => $v){?><option value="<?php echo $v[0];?>"><?php echo $v[1];?></option><?php } ?></select>
        <br />
        Arquivo CSV: <br /><input type="file" name="edtarquivo_csv" id="edtarquivo_csv" />
        <br />
        Limpar Tabela: <input type="checkbox" name="edtlimpa_tabela" id="edtlimpa_tabela" value="1" />
        <br />
        <input type="button" value="Executar" class="botao_form" id="btn_submit" />
</fieldset>
</form>
<hr size="1" noshade="noshade" />
</body>
</html>