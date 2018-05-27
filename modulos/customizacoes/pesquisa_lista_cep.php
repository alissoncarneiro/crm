<?php
/*
 * pesquisa_lista_cep.php
 * Autor: Alex
 * 11/09/2012 15:01:37
 */
header("Content-Type: text/html;  charset=ISO-8859-1");
require('../../conecta.php');
require('../../functions.php');
require('../../classes/class.uB.php');
uB::UrlDecodePost($_POST);
if(!$_POST || $_POST['edtpesq_cep_uf'] == ''){
    ?>
    <form id="form_pesq_cep">
        <input type="hidden" name="TP" value="<?php echo $_POST['TP'];?>"/>
        <table border="0" cellpadding="2" cellspacing="2">
            <tr>
                <td><b>Estado:</b></td>
                <td><?php echo TabelaParaCombobox('is_estados_uf', 'uf', 'uf', 'edtpesq_cep_uf', '', '', ' ORDER BY uf'); ?></td>
            </tr>
            <tr>
                <td>Endere&ccedil;o:</td>
                <td><input type="text" name="edtpesq_cep_endereco" id="edtpesq_cep_endereco" style="width:225px;" /></td>
            </tr>
            <tr>
                <td>Bairro:</td>
                <td><input type="text" name="edtpesq_cep_bairro" id="edtpesq_cep_bairro" style="width:125px;" /></td>
            </tr>
            <tr>
                <td>Cidade:</td>
                <td><input type="text" name="edtpesq_cep_cidade" id="edtpesq_cep_cidade" style="width:125px;" /></td>
            </tr>
        </table>
    </form>
    <div id="div_pesq_cep_resultado" style="max-height: 200px; overflow: auto;"></div>
    <?php
}
else{
    $Bairro = $_POST['edtpesq_cep_bairro'];
    $Cidade = $_POST['edtpesq_cep_cidade'];
    $UF = $_POST['edtpesq_cep_uf'];
    $Endereco = $_POST['edtpesq_cep_endereco'];

    if($UF == ''){
        echo '<h2>Campo estado é obrigatorio!</h2>';
        exit;
    }
    elseif($Endereco == '' && $Bairro == ''){
        echo '<h2>Informe o endere&ccedil;o ou bairro!</h2>';
        exit;
    }
    $CnxCEP = conecta($cnx_servidor_cep, $cnx_usuario_cep, $cnx_senha_cep, $cnx_bd_cep) or die("Erro na conexão com banco de dados de CEP");
    ?>
    <table border="0" align="center" width="100%" cellspacing="2" cellpadding="2" class="bordatabela">
        <tr>
            <td class="tit_tabela" bgcolor="#dae8f4" width="5">&nbsp;</td>
            <td class="tit_tabela" bgcolor="#dae8f4">Endere&ccedil;o</td>
            <td class="tit_tabela" bgcolor="#dae8f4">Bairro</td>
            <td class="tit_tabela" bgcolor="#dae8f4">Cidade</td>
            <td class="tit_tabela" bgcolor="#dae8f4">CEP</td>
        </tr>
        <?php
        $SqlCEP = "SELECT cep,endereco,bairro,cidade FROM ceps WHERE estado = '".TrataApostrofoBD($UF)."'";
        if($Bairro != ''){
            $SqlCEP .= " AND bairro LIKE '%".TrataApostrofoBD($Bairro)."%'";
        }
        if($Cidade != ''){
            $SqlCEP .= " AND cidade LIKE '%".TrataApostrofoBD($Cidade)."%'";
        }
        if($Endereco != ''){
            $SqlCEP .= " AND endereco LIKE '%".TrataApostrofoBD($Endereco)."%'";
        }
        $SqlCEP .= " LIMIT 50";
        $QryCEP = query($SqlCEP, $CnxCEP);
        $i = 0;
        while($ArCEP = farray($QryCEP)){
            $i++;
            $BgColor = ($i%2==0)?' bgcolor="#EBEBEB"':'';
            ?>
            <tr <?php echo $BgColor;?>>
                <td><img src="images/btn_modulo.PNG" class="btn_seleciona_cep" cep="<?php echo $ArCEP['cep'];?>"/></td>
                <td><?php echo $ArCEP['endereco']; ?></td>
                <td><?php echo $ArCEP['bairro']; ?></td>
                <td><?php echo $ArCEP['cidade']; ?></td>
                <td><?php echo substr($ArCEP['cep'], 0, 5).'-'.substr($ArCEP['cep'], 5, 3); ?></td>
            </tr>
        <?php } ?>
    </table>
    <script>
        $(document).ready(function(){
            $(".btn_seleciona_cep").click(function(){
                $("#edtcep<?php echo $_POST['TP'];?>").val($(this).attr("cep"));
                var Dialog = $("#jquery-dialog-pesq-cep");
                Dialog.dialog("close");
                pesquisa_cep('<?php echo $_POST['TP'];?>','500');
            }).css("cursor","pointer");
        });
    </script>
    <?php
}
?>