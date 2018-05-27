<?php
session_start();
$PrefixoIncludes = '../';
include('../includes.php');
$VendaParametro = new VendaParametro();
$_GET['pnumreg'];
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
    <?php
    if($VendaParametro->getUtilizaLinhaProduto()){?>
        $("#ulMenuBarraLateral li ul").hide();
    <?php
    }?>

        $("#ulMenuBarraLateral li a").click(function(){
                        $(this).next().toggle("fast");
                        return false;
        });
        $("#ulMenuBarraLateral li ul a").click(function(){
            var id_familia = $(this).attr('id_familia');
            $.ajax({
                url: "mostra_produtos_familia.php",
                global: false,
                type: "POST",
                data: ({
                    id_familia: id_familia,
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
	#BarraLateral{
		width:200px;
		height:auto;
		border: 1px solid #000000;
		position:absolute;
		left:0;
	}
	#BarraLateral ul{
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
		display:block;
		padding: 15px 0 0 210px;
	}
</style>
</head>

<body>
<div id="barra_topo"></div>
<div id="BarraLateral">

    <ul id="ulMenuBarraLateral">
        <?php
        if($VendaParametro->getUtilizaLinhaProduto()){
            $sql_linha_produto = 'SELECT * FROM is_produto_linha WHERE sn_ativo = 1 order by nome_produto_linha';
            $qry_linha_produto = query($sql_linha_produto);
            while($ar_linha_produto = farray($qry_linha_produto)){
        ?>
    	<li><a href="#"><?php echo $ar_linha_produto['nome_produto_linha'];?></a>
            <ul>
                <?php
                $sql_familia = 'SELECT * FROM is_familia_comercial WHERE id_produto_linha = '.$ar_linha_produto['numreg'];
                $qry_familia = query($sql_familia);
                while($ar_familia = farray($qry_familia)){
                ?>
                    <li><a href="#" id_familia="<?php echo $ar_familia['numreg'];?>"><?php echo $ar_familia['nome_familia_comercial'];?></a></li>
                <?php
                }
                ?>
            </ul>
        </li>
            <?php }?>
        <?php } else {?>
        <li><a href="#">Famílias Comercial</a>
            <ul>
                <?php
                $sql_familia = 'SELECT * FROM is_familia_comercial order by nome_familia_comercial';
                $qry_familia = query($sql_familia);
                while($ar_familia = farray($qry_familia)){
                ?>
                    <li><a href="#" id_familia="<?php echo $ar_familia['numreg'];?>"><?php echo $ar_familia['nome_familia_comercial'];?></a></li>
                <?php
                }
                ?>
            </ul>
        </li>
        <?php }?>
    </ul>
    
</div>
<div id="Conteudo" align="center">Escolha uma família para carregar os produtos disponíveis.</div>

</body>
</html>
