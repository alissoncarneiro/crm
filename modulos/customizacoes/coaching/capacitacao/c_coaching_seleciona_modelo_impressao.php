<?php
/*
 * c_coaching_seleciona_modelo_impressao.php
 * Autor: Alisson
 * 13/08/2012 09:11:00
 *
*/

header("Content-Type: text/html; charset=ISO-8859-1");
include('../../../../conecta.php');
?>
<div align="center">
<select id="select_id_modelo" name="select_id_modelo" style="font-size: 14px; height: 20px">
    <option value="">Selecione um modelo</option>
<?php

$QryModelo = query("SELECT numreg,nome_modelo_orcamento FROM is_modelo_orcamento ORDER BY nome_modelo_orcamento ASC");
while ($ArModelo = farray($QryModelo)) {
        echo '<option value="' . $ArModelo['numreg'] . '">' . $ArModelo['nome_modelo_orcamento'] . '</option>';
}
?></select>
</div>