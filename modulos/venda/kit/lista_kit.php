<?php
session_start();
$PrefixoIncludes = '../';
include('../includes.php');
$VendaParametro = new VendaParametro();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-5589-1" />
<title>Adicionar Múltiplos Produtos</title>
<link rel="stylesheet" type="text/css" href="../../../estilos_css/estilo.css" />
<link rel="stylesheet" type="text/css" href="../../../estilos_css/cadastro.css" />
<link href="../estilo_venda.css" rel="stylesheet" type="text/css" />
<script src="../../../js/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){

        $("#ulMenuBarraLateral li a").click(function(){
                        $(this).next().toggle("fast");
                        return false;
        });
        $("#busca_kit").click(function(){
            var id_kit = $("#edtid_kit").val();
            $.ajax({
                url: "mostra_produto_kit.php",
                global: false,
                type: "POST",
                data: ({
                    id_kit: id_kit,
                    numreg: '<?php echo $_GET['pnumreg'];?>',
                    ptp_venda: '<?php echo $_GET['ptp_venda'];?>',
                    tab_preco: '<?php echo $_GET['tab_preco'];?>',
                    pfuncao: '<?php echo $_GET['pfuncao'];?>'
                }),
                dataType: "html",
                async: true,
                beforeSend: function(){
                    $("#Conteudo").html('<p align="center"><img src="../img/ajax_loading_bar.gif" border="0" /></p>');
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
<style type="text/css">
	body{
		font-family:Arial, Helvetica, sans-serif;
		font-size:12px;
	}
	#BarraLateral ul{
                text-align: center;
		list-style:none;
		margin:0;
		padding:0 0 0 5px;
		background-color:#365D7C;
	}
	#BarraLateral ul li{
		font-weight:bold;
		padding: 4px 4px 4px 4px;
		border-bottom: 1px solid #C7E1F8;
	}
	#BarraLateral ul li ul li{
		font-weight:normal;
		background-color:#C7E1F8;
		display:block;
		border-bottom: 1px solid #365D7C;
		padding: 2px 2px 2px 2px;
	}
	#BarraLateral a{
		text-decoration:none;
		color:#FFFFFF;
	}
	#BarraLateral ul li ul li a{
		color:#365D7C;
	}
	#Loading{
		text-align:center;
	}
	#Conteudo{
            text-align: center;
		display:block;
		padding: 20px 0 0 0;
	}
</style>
</head>

<body>
<div id="barra_topo"></div>
<div id="BarraLateral">
<ul id="ulMenuBarraLateral">
    <li><a href="#">Nome dos kits</a>
        <ul>
            <?php
            $sql_lupa_popup =   'SELECT numreg from is_gera_cad_campos where
                                id_funcao IS NULL AND
                                id_campo = \'id_kit\' AND
                                nome_campo = \'Kit\' AND
                                tipo_campo = \'lupa_popup\' AND
                                sql_lupa = \'select * from is_kit\' AND
                                id_campo_lupa = \'numreg\' AND
                                campo_descr_lupa = \'nome_kit\' AND
                                id_funcao_lupa = \'kit\' AND
                                editavel = 1 AND
                                tamanho_campo IS NULL AND
                                exibe_browse IS NULL AND
                                exibe_formulario IS NULL AND
                                exibe_filtro IS NULL AND
                                ordem IS NULL AND
                                quebra_linha IS NULL AND
                                sn_obrigatorio IS NULL AND
                                nome_aba IS NULL AND
                                id_aba IS NULL AND
                                sn_lupa_bloqueia_incluir IS NULL';
            $qry_lupa_popup = query($sql_lupa_popup);
            $nrows_lupa_popup = numrows($qry_lupa_popup);
            if($nrows_lupa_popup == 1){
                $ar_lupa_popup = farray($qry_lupa_popup);
            ?>
            <li>
                <div align="left" id="div_edtid_kit" style="display: inline;">
                    <input type="text" name="edtid_kit" id="edtid_kit" readonly size="10" value="" style="background-color:#CCCCCC" />&nbsp;-<font face="Verdana" size="1">&nbsp;&nbsp;<input type="text" name="edtdescrid_kit" id="edtdescrid_kit" size="62" value="" readonly style="background-color:#CCCCCC" />
                        <a href="#" onclick="javascript:window.open('../../../gera_cad_lista.php?pfuncao=kit&pdrilldown=1&plupa=<?php echo $ar_lupa_popup['numreg'];?>&pfixo=','kitid_kit','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=650,height=350,top=250,left=250').focus(); return false;"><img border=0 width=15 height=15 src="../../../images/btn_busca.PNG" alt="Buscar" /></a>&nbsp;
                        <!--<a href="#" onclick="javascript:document.getElementById('edtid_kit').value=''; document.getElementById('edtdescrid_kit').value=''; ; return false;"><img border=0 width=15 height=15 src="../../../images/btn_eraser.PNG" alt="Limpar" /></a>--></font>
                    <br /><br /><input style="border: 1px solid #365D7C; background-color: #C0000; cursor: pointer; padding: 2px;" name="busca_kit" type="submit" id="busca_kit" value="Buscar Kit"/>
                </div>
            </li>
            <?php
            } else {
                echo '<li>Erro ao processar SQL, favor entrar em contato com o administrador do sistema.</li>';
            }?>
        </ul>
    </li>
</ul>

</div>
<div id="Conteudo">Escolha uma família para carregar os produtos disponíveis.</div>

</body>
</html>
