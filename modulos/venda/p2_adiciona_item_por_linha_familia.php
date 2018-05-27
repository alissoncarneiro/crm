<?php
/*
 * p2_adiciona_item_por_linha_familia.php
 * Autor: Alex
 * 01/06/2011 12:18:36
 */
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('includes.php');

$Usuario = new Usuario($_SESSION['id_usuario']);
$VendaParametro = new VendaParametro();
?>
<style type="text/css">
    #BarraLateralLinhaFamilia{
        width:200px;
        height:auto;
        border: 1px solid #000000;
    }
    #BarraLateralLinhaFamilia ul{
        list-style:none;
        margin:0;
        padding:0 0 0 0px;
        background-color:#365D7C;
    }
    #BarraLateralLinhaFamilia ul li{
        font-weight:bold;
        padding: 4px 4px 4px 4px;
        border-bottom: 1px solid #C7E1F8;
    }
    #BarraLateralLinhaFamilia ul li ul li{
        font-weight:normal;
        background-color:#C7E1F8;
        display:block;
        border-bottom: 1px solid #365D7C;
        padding: 2px 2px 2px 2px;
    }
    #BarraLateralLinhaFamilia a{
        text-decoration:none;
        color:#FFFFFF;
    }
    #BarraLateralLinhaFamilia ul li ul li a{
        color:#365D7C;
    }
</style>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr style="vertical-align:top;">
        <td width="200">
            <div id="BarraLateralLinhaFamilia">
            <ul id="ulMenuBarraLateral">
                <?php
                if($VendaParametro->getUtilizaLinhaProduto()){
                    $SqlLinhaProduto = 'SELECT * FROM is_produto_linha WHERE sn_ativo = 1';                    
                    if($VendaParametro->getSnUsaBloqueioRepxFam()){
                        $SqlLinhaProduto .= " AND numreg IN(SELECT tsq2.id_produto_linha FROM is_familia_comercial tsq2 WHERE tsq2.numreg IN(SELECT DISTINCT tsq1.id_familia_comercial FROM is_param_representantexfamilia_comercial tsq1 WHERE tsq1.id_representante = '".$_SESSION['id_usuario']."' AND tsq1.sn_ativo = 1 AND tsq1.dthr_validade_ini <= '".date("Y-m-d")."' AND tsq1.dthr_validade_fim >= '".date("Y-m-d")."'))";
                    }
                    $SqlLinhaProduto .= " ORDER BY nome_produto_linha";
                    
                    $QryLinhaProduto = query($SqlLinhaProduto);
                    while($ArLinhaProduto = farray($QryLinhaProduto)){
                ?>
                <li><a href="#"><?php echo $ArLinhaProduto['nome_produto_linha'];?></a>
                    <ul>
                        <?php
                        $SqlFamiliaComercial = 'SELECT * FROM is_familia_comercial WHERE id_produto_linha = '.$ArLinhaProduto['numreg'];
                        if($VendaParametro->getSnUsaBloqueioRepxFam()){
                            $SqlFamiliaComercial .= " AND numreg IN(SELECT DISTINCT tsq1.id_familia_comercial FROM is_param_representantexfamilia_comercial tsq1 WHERE tsq1.id_representante = '".$_SESSION['id_usuario']."' AND tsq1.sn_ativo = 1 AND tsq1.dthr_validade_ini <= '".date("Y-m-d")."' AND tsq1.dthr_validade_fim >= '".date("Y-m-d")."')";
                        }
                        $SqlFamiliaComercial .= " ORDER BY nome_familia_comercial";
                        $QryFamiliaComercial = query($SqlFamiliaComercial);
                        while($ArFamiliaComercial = farray($QryFamiliaComercial)){
                        ?>
                            <li><a href="#" id_familia_comercial="<?php echo $ArFamiliaComercial['numreg'];?>"><?php echo $ArFamiliaComercial['nome_familia_comercial'];?></a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                    <?php } ?>
                <?php } else { ?>
                <li><a href="#">Fam&iacute;lias Comercial</a>
                    <ul>
                        <?php
                        $SqlFamiliaComercial = 'SELECT * FROM is_familia_comercial';
                        if($VendaParametro->getSnUsaBloqueioRepxFam()){
                            $SqlFamiliaComercial .= " AND numreg IN(SELECT DISTINCT tsq1.id_familia_comercial FROM is_param_representantexfamilia_comercial tsq1 WHERE tsq1.id_representante = '".$_SESSION['id_usuario']."' AND tsq1.sn_ativo = 1 AND tsq1.dthr_validade_ini <= '".date("Y-m-d")."' AND tsq1.dthr_validade_fim >= '".date("Y-m-d")."')";
                        }
                        $SqlFamiliaComercial .= " ORDER BY nome_familia_comercial";
                        $QryFamiliaComercial = query($SqlFamiliaComercial);
                        while($ArFamiliaComercial = farray($QryFamiliaComercial)){
                        ?>
                            <li><a href="#" id_familia_comercial="<?php echo $ArFamiliaComercial['numreg'];?>"><?php echo $ArFamiliaComercial['nome_familia_comercial'];?></a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php }?>
            </ul>
        </div>
        </td>
        <td id="Conteudo">&nbsp;</td>
    </tr>
</table>
<script type="text/javascript">
$(document).ready(function(){
    <?php if($VendaParametro->getUtilizaLinhaProduto()){ ?>
    $("#ulMenuBarraLateral li ul").hide();
    <?php } ?>
    $("#ulMenuBarraLateral li a").click(function(){
        $(this).next().toggle("fast");
        return false;
    });
    $("#ulMenuBarraLateral li ul a").click(function(){
        var id_familia_comercial = $(this).attr('id_familia_comercial');
        $.ajax({
            url: "p2_adiciona_item_por_linha_familia_tabela.php",
            global: false,
            type: "POST",
            data: ({
                id_familia_comercial: id_familia_comercial,
                pnumreg: '<?php echo $_POST['pnumreg'];?>',
                ptp_venda: '<?php echo $_POST['ptp_venda'];?>'
            }),
            dataType: "html",
            async: true,
            beforeSend: function(){
                $("#Conteudo").html(HTMLLoading);
            },
            error: function(){
                alert('Erro com a requisição');
            },
            success: function(responseText){
                $("#Conteudo").html(responseText);
            }
        });
        return false;
    });
});
</script>