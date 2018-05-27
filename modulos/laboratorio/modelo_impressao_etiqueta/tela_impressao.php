<?php
# tela_impressao
# Expression package is undefined on line 3, column 5 in Templates/Scripting/EmptyPHP.php.
# Autor: Rodrigo Piva
# 11/10/2011
# 
# Log de Alterações
# yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
#

?>
<script type="text/javascript" src="../../../js/function.js"></script>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<link href="../../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
<link href="../../../estilos_css/estilo_aba.css" rel="stylesheet" type="text/css" />
<link href="../../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../../../estilos_css/cadastro.css">

<script type="text/javascript">
    $(document).ready(function(){
        $('#btn_imprimir').click(function(){
            var Pular = $('input[name=radio]:checked').val();
            window.open('modelo_etiqueta.php?pnumreg=<?php echo $_GET['pnumreg']; ?>&pular=' + Pular,'Impressao');
        });
    });

</script>


<style type="text/css">
.FontTitulo {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 24px;
}
.fs_custom {
    text-align: center;
}
.fs_custom legend{
    font-weight:bold;
    font-size:14px;
}
.fs_custom table{
    border: 1px solid #ACC6DB;
}
.fs_custom table th{
    font-weight: bold;
    color: #345c7d;
    text-align: left;
    padding-left: 5px;
    background-color: #DAE8F4;
}
.campo_data{
    width:65px;
    text-align: center;
}



</style>
<fieldset class="fs_custom"><legend>Formulário</legend>
    <table width="400px" border="1" cellspacing="0" cellpadding="3" align="center">
  <tr>
    <td width="50%" height="73" align="center" valign="middle" ><span class="FontTitulo">1</span>
      <input type="radio" name="radio" id="id_1" value="1" />
    <label for="id_1"></label></td>
    <td width="50%" align="center" valign="middle"><span class="FontTitulo">2
        <input type="radio" name="radio" id="id_2" value="2" />
        <label for="id_2"></label>
    </span></td>
  </tr>
  <tr>
    <td width="50%" height="73" align="center" valign="middle" ><span class="FontTitulo">3
        <input type="radio" name="radio" id="id_3" value="3" />
        <label for="id_3"></label>
    </span></td>
    <td align="center" valign="middle"><span class="FontTitulo">4
        <input type="radio" name="radio" id="id_4" value="4" />
        <label for="id_4"></label>
    </span></td>
  </tr>
  <tr>
    <td width="50%" height="73" align="center" valign="middle" ><span class="FontTitulo">5
        <input type="radio" name="radio" id="id_5" value="5" />
        <label for="id_5"></label>
    </span></td>
    <td align="center" valign="middle"><span class="FontTitulo">6
        <input type="radio" name="radio" id="id_6" value="6" />
        <label for="id_6"></label>
    </span></td>
  </tr>
  <tr>
    <td width="50%" height="73" align="center" valign="middle" ><span class="FontTitulo">7
        <input type="radio" name="radio" id="id_7" value="7" />
        <label for="id_7"></label>
    </span></td>
    <td align="center" valign="middle"><span class="FontTitulo">8
        <input type="radio" name="radio" id="id_8" value="8" />
        <label for="id_8"></label>
    </span></td>
  </tr>
  <tr>
    <td width="50%" height="73" align="center" valign="middle" ><span class="FontTitulo">9
        <input type="radio" name="radio" id="id_9" value="9" />
        <label for="id_9"></label>
    </span></td>
    <td align="center" valign="middle"><span class="FontTitulo">10
        <input type="radio" name="radio" id="id_10" value="10" />
        <label for="id_10"></label>
    </span></td>
  </tr>
</table>
    <p></p>
  <input type="button" class="botao_form" name="btn_imprimir" id="btn_imprimir" value="Imprimir" />
  <input type="button" class="botao_form" name="btn_fechar" onclick="javascript:window.close();" id="btn_fechar" value="Fechar" />
</fieldset>
