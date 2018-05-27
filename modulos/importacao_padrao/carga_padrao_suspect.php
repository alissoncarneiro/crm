<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);
set_time_limit(0);
require("../../conecta.php");
require("../../funcoes.php");
/*if($_POST && $_FILES){

}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: Carga de Suspects</title>
        <link rel="stylesheet" type="text/css" href="../../estilos_css/estilo.css" />
        <link rel="stylesheet" type="text/css" href="../../estilos_css/cadastro.css" />
        <link rel="stylesheet" type="text/css" href="../../estilos_css/calendar-blue.css" title="win2k-cold-1" />
        <style type="text/css">
            <!--

            body {margin-left: 0px;margin-top: 0px;margin-right: 0px;margin-bottom: 0px;}
            legend {font-size: 16px; font-weight: bold; color: #004499;}
            -->
        </style>
        <script src="../../js/function.js" language="JavaScript"></script>
        <script src="../../js/ajax_menus.js" language="JavaScript"></script>
        <script src="../../js/calendario/calendario.js" language="JavaScript"></script>
        <script src="../../js/calendario/calendario-pt.js" language="JavaScript"></script>
        <script src="../../js/calendario/calendario-config.js" language="JavaScript"></script>
        <script src="../../js/jquery.js" language="JavaScript"></script>
                <script>
function zebra(id, classe) {
var tabela = document.getElementById(id);
var linhas = tabela.getElementsByTagName("tr");
	for (var i = 0; i < linhas.length; i++) {
	((i%2) == 0) ? linhas[i].className = classe : void(0);
	}
}
</script>
<style>
* { font-family:Arial, Helvetica, sans-serif; font-size:11px; }
h1 { font-size:16px; color:#FF0000; text-align: center; }
th, td { padding:6px; border-bottom:1px solid #ddd; text-align:left; }
tr.zb td { background:#eee; }
</style>
    </head>
    <body>
        <div id="principal_detalhes">
            <div id="topo_detalhes"><!--topo -->
                <div id="logo_empresa"><!--logo --></div>
            </div>
            <div id="conteudo_detalhes">
                <form method="POST" name="form_carga" id="form_carga" action="carga_padrao_suspect.php" enctype='multipart/form-data'>
                    <fieldset><legend>Carga de Suspects</legend>
                        <fieldset><legend>Par&acirc;metros</legend>
                            <table cellpadding="2" cellspacing="2" border="0">
                                <tr>
                                    <td align="right"><strong>Arquivo: </strong></td>
                                    <td><input type="file" name="edtarquivo" id="edtarquivo" size="50" /></td>
                                </tr>
                                <tr>
                                    <td align="right"><strong>CNPJ/CPF: </strong></td>
                                    <td><input type="checkbox" name="edtchkrazao_social_cnpj_cpf_obrigatorio" id="edtchkrazao_social_cnpj_cpf_obrigatorio"<?php echo (isset($_POST['edtchkrazao_social_cnpj_cpf_obrigatorio']))?' checked="checked"':'';?> /> Obrigat&oacute;rio</td>
                                </tr>
                                <tr>
                                    <td align="right"><strong>Razão Social/Nome: </strong></td>
                                    <td><input type="checkbox" name="edtchkrazao_social_nome_duplicidade" id="edtchkrazao_social_nome_duplicidade"<?php echo (isset($_POST['edtchkrazao_social_nome_duplicidade']))?' checked="checked"':'';?> /> Permite Duplicidade</td>
                                </tr>
                                <tr>
                                    <td align="right"><strong>E-mail: </strong></td>
                                    <td><input type="checkbox" name="edtchkemail_obrigatorio" id="edtchkemail_obrigatorio"<?php echo (isset($_POST['edtchkemail_obrigatorio']))?' checked="checked"':'';?> /> Obrigat&oacute;rio
                                        <input type="checkbox" name="edtchkemail_duplicidade" id="edtchkemail_duplicidade"<?php echo (isset($_POST['edtchkemail_duplicidade']))?' checked="checked"':'';?> /> Permite Duplicidade</td>
                                </tr>
                                <tr>
                                    <td align="right"><strong>Telefone: </strong></td>
                                    <td><input type="checkbox" name="edtchktelefone_obrigatorio" id="edtchktelefone_obrigatorio"<?php echo (isset($_POST['edtchktelefone_obrigatorio']))?' checked="checked"':'';?> /> Obrigat&oacute;rio
                                        <input type="checkbox" name="edtchktelefone_duplicidade" id="edtchktelefone_duplicidade"<?php echo (isset($_POST['edtchktelefone_duplicidade']))?' checked="checked"':'';?> /> Permite Duplicidade</td>
                                </tr>
                                <tr>
                                    <td align="right"><strong>Exibir Relatório de Divergências: </strong></td>
                                    <td><input type="checkbox" name="edtconfirmacao" id="edtconfirmacao"<?php echo (isset($_POST['edtconfirmacao']))?' checked="checked"':'';?> /></td>
                                </tr>
                                <tr>
                                    <td align="right"><strong>Último Cód. Mailing: </strong></td>
                                    <td><?php $QryMaxMailing = query("SELECT MAX(id_mailing) AS maior FROM is_pessoa"); $ArMaxMailing = farray($QryMaxMailing); echo $ArMaxMailing['maior'];?></td>
                                </tr>
                            </table>
                        </fieldset>
                        <div align="center"><a href="suspect.csv">Baixar arquivo modelo</a></div>
                        <hr size="1"/>
                        <input name="Submit" type="submit" class="botao_form" value="Confirmar Par&acirc;metros" />
                        <hr size="1"/>
                        <p>&nbsp;</p>
                        <?php
                        if($_POST && $_FILES && (isset($_POST['edtconfirmacao']) && $_POST['edtconfirmacao'] == 'on')){?>
                        <fieldset><legend>Análise</legend><?php include('carga_padrao_suspect_relatorio.php');?></fieldset>
                        <?php } else if($_POST) { ?>
                        <fieldset><legend>Importação</legend><?php include('carga_padrao_suspect_post.php');?></fieldset>
                        <?php
                        }?>
                    </fieldset>
                </form>
            </div>
        </div>
    </body>
</html>