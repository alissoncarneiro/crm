<?
@session_start();
if (!isset($_SESSION['id_usuario']) || empty($_SESSION['id_usuario'])) {
    echo "<script> alert('Por favor:\\nFaça login antes de acessar este recurso.'); window.opener.location.reload(true); window.close(); </script>";
    exit;
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Oasis :: Gráfico </title>
        <link rel="stylesheet" type="text/css" href="../estilos_css/estilo.css" />
        <link rel="stylesheet" type="text/css" href="../estilos_css/cadastro.css" />
        <script language="JavaScript" src="../js/ajax_gera_cad.js"></script>
        <script type="text/javascript" src="../js/function.js"></script>
        <script type="text/javascript" src="../js/calendario/calendario.js"></script>
        <script type="text/javascript" src="../js/calendario/calendario-pt.js"></script>
        <script type="text/javascript" src="../js/calendario/calendario-config.js"></script>
        <script language="javascript" type="text/javascript">
            function maximizar() {
                window.moveTo (-4,-4);
                window.resizeTo (screen.availWidth + 8, screen.availHeight + 8);
            }
            maximizar();
                        
            //Função que exibe a dica
            function dica(strDica, e){
                var posx = 0;
                var posy = 0;
                var alt_tela = screen.height;
	
                if (!e) var e = window.event;
                if (e.pageX || e.pageY) 	{
                    posx = e.pageX;
                    posy = e.pageY;
                }
                else if (e.clientX || e.clientY) 	{
                    posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
                    posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
                }

                var divDica = document.getElementById("dica");

                if(strDica=='') //Esconde a DIV
                {
                    divDica.style.visibility = "hidden";
                    divDica.style.display = "none";

                }
                else { //Exibe a DIV
   
                    divDica.style.left = posx - 125 + "px";
                    divDica.style.display = "block";
                    divDica.innerHTML = strDica;
		
                    if(alt_tela - posy - 100 > document.getElementById("dica").offsetHeight){
                        divDica.style.top = posy + 20 + "px";
			
                    }
                    else{
                        divDica.style.top = posy - (5+document.getElementById("dica").offsetHeight) + "px";
                    }
		
                    divDica.style.visibility = "visible";
                }
            }
            window.onscroll = atualiza_cal;
            function atualiza_cal(){
                var cal_top = document.getElementById('div_cal');
                cal_top.style.top = window.document.body.scrollTop + 110;
                var header = document.getElementById('div_header');
                header.style.left = document.body.scrollLeft;
                header.style.top = window.document.body.scrollTop;
                document.getElementById('div_loading').style.left = '';
            }
<? if (isset($_GET['id_versao'])) { ?>
        top.window.moveTo(0,0);
        if (document.all) {
            top.window.resizeTo(screen.availWidth,screen.availHeight);
        }
        else if (document.layers||document.getElementById) {
            if (top.window.outerHeight<screen.availHeight||top.window.outerWidth<screen.availWidth){
                top.window.outerHeight = screen.availHeight;
                top.window.outerWidth = screen.availWidth;
            }
        }
<? } ?>
        </script>
    </head>
    <?
    require_once "../conecta.php";
    require_once "../functions.php";

//Definindo arrays
    $semana = array("Sun" => "D", "Mon" => "S", "Tue" => "T", "Wed" => "Q", "Thu" => "Q", "Fri" => "S", "Sat" => "S");
    $semana_name = array("Sun" => "Domingo", "Mon" => "Segunda-Feira", "Tue" => "Ter&ccedil;a-Feira", "Wed" => "Quarta-Feira", "Thu" => "Quinta-Feira", "Fri" => "Sexta-Feira", "Sat" => "S&aacute;bado");
    $semana_n = array("Sun" => "1", "Mon" => "2", "Tue" => "3", "Wed" => "4", "Thu" => "5", "Fri" => "6", "Sat" => "7");
    $mes_nome = array("01" => "Jan", "02" => "Fev", "03" => "Mar", "04" => "Abr", "05" => "Mai", "06" => "Jun", "07" => "Jul", "08" => "Ago", "09" => "Set", "10" => "Out", "11" => "Nov", "12" => "Dez");
    ?>
    <div id="dica" class="toolTip"></div>
    <style type="text/css">
        <!--
        body{
            margin:0px;
            padding:0px;
            background:url(../img/project/bg_table.png) #F2F2F2;
        }
        .toolTip{
            font-family:Verdana;
            font-size:10px;
            text-align:left;
            position:absolute;
            display: none;
            visibility: hidden;
            z-index: 90;
            border: 1px solid #DBE9F4;
            background-color:#F4F4FF;
            padding: 10px;
            width: 300px;
        }
        .cal_week{
            border: 1px solid #ACC6DB;
            font-family:Arial;
            font-size:12px;
            font-weight:bold;
            text-align:left;
            width:105px;
            height:15px;
            position:absolute;
            background:#C9DEEF;
        }
        .cal_day{
            border: 1px solid #ACC6DB;
            font-family:Arial;
            font-size:12px;
            font-weight:bold;
            text-align:center;
            width:15px;
            height:15px;
            position:absolute;
            background:#C9DEEF;
            cursor:help;

        }
        .row{
            height:16px;
            position:absolute;
        }
        .txt_nd{
            font-size:1px;
        }
        -->
    </style>
    <?
//DEFINIDNO FILTRO
    if (isset($_GET['q'])) {
        $filtro_projeto_where = "WHERE nome_projeto LIKE '%" . addslashes(trim($_GET['q'])) . "%'";
        $filtro_projeto_and = "AND nome_projeto LIKE '%" . addslashes(trim($_GET['q'])) . "%'";
    } else {
        $filtro_projeto_where = "";
        $filtro_projeto_and = "";
    }




//PEGANDO A MENOR DATA ENTRE OS REGISTRO DAS ATIVIDADES
    $min_dt_i = farray(query("SELECT MIN(dt_inicio) as dt_i FROM is_projeto " . $filtro_projeto_where));
    $min_dt_i = $min_dt_i['dt_i'];
//PEGANDO A MAIOR DATA ENTRE OS REGISTRO DAS ATIVIDADES
    $max_dt_f = farray(query("SELECT MAX(dt_prev_fim) as dt_f FROM is_projeto " . $filtro_projeto_where));
    $max_dt_f = $max_dt_f['dt_f'];
//PEGANDO TODOS OS PROJETOS OU TAREFAS
    $query = query("SELECT * FROM is_projeto " . $filtro_projeto_where . " ORDER BY dt_prev_fim ASC");

    if (diferenca_dt($min_dt_i, $max_dt_f) < 85) {
        $min_dt_i = date("Y-m-d", strtotime($min_dt_i . " - 5 weeks"));
        $max_dt_f = date("Y-m-d", strtotime($max_dt_f . " + 5 weeks"));
    }
//FAZENDO CALCULO PARA SEMANA INICIAR EM DOMINGO E UMA SEMANA ANTES DO PRIMEIRO PROJETO
//PEGANDO EM NÚMERO O DIA DA SEMANA DA DATA MENOR DO PROJETOS
    $dia_semana_i = $semana_n[date("D", strtotime($min_dt_i))];

    if ($dia_semana_i != "Sun") {
        $dt_cal_i = date("Y-m-d", strtotime($min_dt_i . " - " . ($dia_semana_i - 1) . " days - 1 weeks"));
    } else {
        $dt_cal_i = date("Y-m-d", strtotime($min_dt_i . " - " . $dia_semana_i . " days - 2 weeks"));
    }
//DEFININDO A DATA QUE INICIOU-SE O CALENDÁRIO
    $dt_inicio = $dt_cal_i;

//VERIFICANDO SE HÁ PROJETOS ATRASADOS PARA DEFINIR A DATA DE FIM DO CALENDÁRIO
    $qry_projeto_atrasado = query("SELECT * FROM is_projeto WHERE dt_prev_fim < NOW() AND (id_situacao != '4')" . $filtro_projeto_and);
    $nr_projeto_atrasado = numrows($qry_projeto_atrasado);
    $ar_projeto_atrasado = farray($qry_projeto_atrasado);
//Se não houver projetos atrasado
    if ($nr_projeto_atrasado != 0 && $max_dt_f < date("Y-m-d")) {
        $dt_cal_f = date("Y-m-d", strtotime(date("Y-m-d") . " + 1 week"));
    } else {
        $dt_cal_f = $max_dt_f;
    }
//FAZENDO CALCULO PARA SEMANA TERMINAR EM SÁBADO E UMA SEMANA DEPOIS DO ULTIMO PROJETO
    $dia_semana_f = $semana_n[date("D", strtotime($dt_cal_f))];
    if ($dia_semana_f != "Sat") {
        $dt_cal_f = date("Y-m-d", strtotime($dt_cal_f . " - " . ($dia_semana_f) . " days + 2 week"));
    } else {
        $dt_cal_f = date("Y-m-d", strtotime($dt_cal_f . " + 1 week"));
    }

//DIFERENÇA DE DIAS ENTRE A MAIOR E A MENOR DATA
    $diferenca_dias_cal = diferenca_dt($dt_cal_i, $dt_cal_f);
    ?>
    <body>
        <div id="div_loading" style="position:absolute;width:200px;height:100px;border:1px solid #000000;background-color:#FFFFFF;z-index:101;display:none; text-align:center; vertical-align:middle;"><table height="100%" cellpadding="0" cellspacing="0" border="0"><tr><td valign="middle" align="center"><img src="../images/wait.gif"></td></tr><tr><td align="center">Por favor aguarde<br />Processando...</td></tr></table></div>
        <div id="div_header" style="position:absolute; top:0px; width:100%; height:110px; overflow:hidden; z-index:5; background-color:#FFFFFF;">
            <form method="get" action="">
                <table width="100%" height="101" border="0" cellpadding="0" cellspacing="5" bgcolor="#FFFFFF">
                    <tr>
                        <td width="94%" height="19"><strong><span style="font-size:16px;font-weight:bold;">OASIS PROJECT</span></strong></td>
                    </tr>
                    <tr>
                        <td height="38"><strong>
                                Filtar Projeto:<br>
                                <input name="q" type="text" id="q" size="50" value="<?= $_GET['q']; ?>">
                                <input type="submit" class="botao_form" value="Filtrar" />
                            </strong></td>
                    </tr>
                    <tr>
                        <td height="24"><strong class="bold_txt_azul">
                                <input type="button" value="+ Incluir" name="btnincluir" onClick="javascript:window.open('../gera_cad_detalhe.php?pfuncao=projetos_cad&pnumreg=-1&psubdet=&pnpai=&pfixo=','projetos_cad3','location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=100,left=100'); return false;" class="botao_form"/>
                            </strong><strong><input name="button2" type="button" class="botao_form" onClick="javascript:window.print();" value="Imprimir" />
                                <input name="button" type="button" class="botao_form" onClick="javascript:window.close(); window.opener.focus();" value="Fechar" />
                            </strong></td>
                    </tr>
                </table>
            </form>
        </div>
        <div id="div_cal" style="position: absolute; top:110px; z-index:6">
            <?
//
////
///// TABELA DO CALENDÁRIO
////
//
//CRIANDO LINHA COM DIVIDIDA EM SEMANAS
            $dt_cal_semana = $dt_cal_i;
            $dt_cal = $dt_cal_i;
            $week_pleft = 0;
            for ($i = 0; $i <= $diferenca_dias_cal; $i++) {
                $d_semana = ( $i % 7 == 0 ) ? true : false;
                if ($d_semana == true) {
                    echo "<div class='cal_week' style='left:" . $week_pleft . "px'>";
                    echo date("d", strtotime($dt_cal_semana)) . " " . $mes_nome[date("m", strtotime($dt_cal_semana))] . " '" . date("y", strtotime($dt_cal_semana));
                    $dt_cal_semana = date("Y/m/d", strtotime($dt_cal_semana . " + 7 days"));
                    echo "</div>";
                    $week_pleft = $week_pleft + 105;
                    $size_cal = $size_cal + 105;
                }
            }
//CRIANDO LINHA DIVIDIDA POR DIAS
            $day_pleft = 0;
            for ($i = 0; $i <= $diferenca_dias_cal; $i++) {
                echo "<div class='cal_day' style='left:" . $day_pleft . "px;top:15px' title='" . $semana_name[date("D", strtotime($dt_cal))] . "\n" . date("d/m/Y", strtotime($dt_cal)) . "'>";
                echo $semana[date("D", strtotime($dt_cal))];
                $dt_cal = date("Y/m/d", strtotime($dt_cal . " + 1 day"));
                echo "</div>";
                $day_pleft = $day_pleft + 15;
            }
            ?>
        </div>
        <?
            $r_top = 145;
            while ($ar = farray($query)) {
                //P = Pendente
                //R = Realizado
                //A = Anadamento
                //G = Aguardo
                //DEFININDO SE O PROJETO ESTÁ ATRASADO
                if ($ar['dt_prev_fim'] < date("Y-m-d") && $ar['id_situacao'] != '4') {
                    $atrasado = true;
                } else {
                    $atrasado = false;
                }

                //DEFININDO QUANTIDADE DE DIAS DO PROJETO
                $diferenca_dias_g = diferenca_dt($ar['dt_inicio'], $ar['dt_prev_fim']);

                //SÓ MOSTRA 170 CARACTERS NAS OBSERVAÇÕES
                if (strlen($ar['obs']) > 170) {
                    $obs = substr($ar['obs'], 0, 170) . "...";
                } else {
                    $obs = $ar['obs'];
                }
                //DEFININDO A DIV DE INFORMAÇÕES
                $div_info =
                        "<strong>Projeto: </strong>"
                        . ucwords(strtolower($ar['nome_projeto'])) . "<hr noshade=noshade size=1 />"
                        . "<strong>Situa&ccedil;&atilde;o: </strong>";

                if ($atrasado == true) {
                    $div_info.= "Atrasado à <font color=red><strong>" . diferenca_dt($ar['dt_prev_fim'], date("Y-m-d")) . "</strong></font> dia(s)";
                } else {
                    $div_info .= @ mysql_result(query("SELECT nome_situacao FROM is_situacao WHERE numreg ='" . $ar['id_situacao'] . "'"), 0, 'nome_situacao');
                }

                $div_info .= "<hr noshade=noshade size=1 />"
                        . "<strong>Observa&ccedil;&otilde;es: </strong><br>"
                        . ucwords(strtolower($obs)) . "<hr noshade=noshade size=1 />"
                        . "<strong>Data Prevista de In&iacute;cio: </strong>"
                        . date("d/m/Y", strtotime($ar['dt_inicio'])) . "<hr noshade=noshade size=1 />"
                        . "<strong>Data Prevista de T&eacute;rmino: </strong>"
                        . date("d/m/Y", strtotime($ar['dt_prev_fim'])) . "<hr noshade=noshade size=1 />"
                        . "<strong>Repons&aacute;vel: </strong>"
                        . ucwords(strtolower(@mysql_result(query("SELECT nome_usuario FROM is_usuario WHERE numreg ='" . $ar['id_usuario_resp'] . "'"), 0, 'nome_usuario'))) . "<hr noshade=noshade size=1 />"
                        . "Clique para ver as atividades do projeto";

                //DEFINE O LINK PARA DETALHES DA ATIVIDADE
                $link_detalhes = "javascript: window.location = 'grafico_detalhe.php?id_projeto=" . $ar['id_projeto'] . "';";

                //
                //DEFININDO TAMANHO DA DIV CASO ESTEJA ATRASADA
                if ($atrasado == false) {
                    $r_size = ((round(diferenca_dt($ar['dt_inicio'], $ar['dt_prev_fim']), 0) + 1) * 15);
                    $ar['cor'] = "light_green";
                } else {
                    $r_size = ((round(diferenca_dt($ar['dt_inicio'], date("Y-m-d")), 0) + 1) * 15);
                    $ar['cor'] = "red";
                }
                if ($ar['id_situacao'] == '4') {
                    $ar['cor'] = "gray";
                }
                echo "<div class='row' style='height:16px;width:" . $r_size . "px;top:" . $r_top . "px;left:" . (diferenca_dt($dt_cal_i, $ar['dt_inicio']) * 15) . "px;'>";
        ?>
                <table width="<?= $r_size ?>" border="0" cellpadding="0" cellspacing="0" class="txt_nd" onClick="<?= $link_detalhes; ?>" style="cursor:pointer" onMouseMove="dica('<?= $div_info; ?>', event)" onMouseOut="dica('',event)">
                    <tr height="16">
                        <td width="8" background="../img/project/<?= $ar['cor']; ?>_l.gif">&nbsp;</td>
                        <td background="../img/project/<?= $ar['cor']; ?>_c.gif">&nbsp;</td>
                <? if ($atrasado == false) {
                ?>
                    <td width="8" background="../img/project/<?= $ar['cor']; ?>_r.gif">&nbsp;</td>
                <? } else {
                ?>
                    <td style="border-left:1px solid #FFFFFF;" width="<?= (round(diferenca_dt($ar['dt_prev_fim'], date("Y-m-d")), 0) * 15) - 8; ?>" background="../img/project/red_c.gif">&nbsp;</td>
                    <td width="8" background="../img/project/red_r.gif">&nbsp;</td>
                <? } ?>
            </tr>
        </table><div style="position:relative;z-index:4;font-family:Arial;font-size:12px; font-weight:"><?= $ar['nome_projeto']; ?></div>
        <p>
            <?
                echo "</div>";
                $r_top = $r_top + 28;
            }
            ?>
        </p>
    </body>
</html>
