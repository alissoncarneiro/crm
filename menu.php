<?php
@session_start( );
@header( "Content-Type: text/html;  charset=utf-8", true );

$id_usuario 	= $_SESSION['id_usuario'] ;
$nome_usuario 	= $_SESSION['nome_usuario'] ;
$id_perfil 		= $_SESSION['id_perfil'] ;
$nome_perfil 	= $_SESSION['nome_perfil'] ;
$url_logotipo 	= $_SESSION['url_logotipo'] ;
$id_sistema     = "CRM";
$_SESSION['id_sistema'] = 'CRM';
?>
<div id="principal" >

    <div id="topo">
        <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
            <tr>
                <td align="left" valign="top">
                    <img src="images/rosa_dos_ventos_topo.png" />
                </td>
                <td align="right" valign="top">
                    
                </td>
            </tr>
        </table>
    </div>
    <div id="menu_horiz">
        <div style="padding-top:5px">
            <table width="100%" height="19" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="left">&nbsp;
                        <img src="images/icones_menu.jpg" width="14" height="12" align="absmiddle" />

                        <?php
                        $hora_atual = gmdate( "H", time( ) + 3600 * ( 0 - 2 ) );
                        if ( 0 <= $hora_atual && $hora_atual <= 12 ){
                            $saudacao = "Bom dia";
                        }
                        if ( 13 <= $hora_atual && $hora_atual <= 18 ){
                            $saudacao = "Boa tarde";
                        }
                        if ( 19 <= $hora_atual && $hora_atual <= 24 ){
                            $saudacao = "Boa noite";
                        }
                        echo "&nbsp;".$saudacao." ".$nome_usuario." ! Perfil de Usuário : ".trim( $nome_perfil );

                        ?>
                    </td>
                    <td align="right">&nbsp;
                        <?php
                        include( "menu_texto_custom.php" );
                        ?>
                        &nbsp;
                    </td>
                    <td align="right">&nbsp;<img src="images/btn_home.jpg" width="14" height="12" align="absmiddle" />
                        <a href="javascript:exibe_programa('painel_inicial_crm.php')">Abrir Página Inicial</a>&nbsp;&nbsp;
                    </td>
                    <?php
                    if ( $_SESSION['sn_usa_autenticacao_ad'] != "1" ){ ?>
                        <td align="right">
                            &nbsp;<img src="images/btn_senha.jpg" width="14" height="12" align="absmiddle" />
                            <a href="javascript:exibe_programa('muda_senha.php');">Alterar Senha</a>&nbsp;&nbsp;
                        </td>
                        <?php
                    }
                    ?>
                    <td align="right">&nbsp;<img src="images/btn_logoff.jpg" width="14" height="12" align="absmiddle" />
                        <a href="index.php?sistema=crm">Fazer Logoff</a>&nbsp;&nbsp;
                    </td>
                </tr>
            </table>
        </div>
    </div>



    <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="133" height="100%" valign="top" bgcolor="#CCC" class="cinza">
                <div name="div_sub_menu" id="div_sub_menu" style="width:140px;">
                    <?php

                    require_once( "conecta.php" );
                    $filtro_licenca = " and (id_licenca is null or id_licenca = 'CRM' or id_licenca like '%PADRAO%' or id_licenca like '%CRM%')";
                    $edtusuario = $_POST['edtusuario'];
                    $sql_modulos = query( "select * from is_modulos where id_sistema like '%".$_SESSION['id_sistema']."%' {$filtro_licenca} order by ordem" );
                    $i = 1;
                    while ( $qry_modulos = farray( $sql_modulos ) ){
                        if ( $_SESSION['ip_consultor'] == "" && $qry_modulos['id_modulo'] == "8" ){
                            $qry_conta_funcoes_estrutura = farray( query( "select count(*) as total from is_funcoes where id_modulo = '".$qry_modulos['id_modulo']."' and id_sistema like '%".$_SESSION['id_sistema']."%' and nome_grupo = 'Estrutura'" ) );
                            $funcoes_implantador = $qry_conta_funcoes_estrutura['total'] * 1;
                        }else						    {
                            $funcoes_implantador = 0;
                        }

                        $qry_conta_bloqueios = farray( query( "select count(*) as total from is_perfil_funcao_bloqueio where id_modulo = '".$qry_modulos['id_modulo']."' and sn_bloqueio_abrir = '1' and id_perfil= '{$id_perfil}' " ) );
                        $qry_conta_funcoes = farray( query( "select count(*) as total from is_funcoes where id_modulo = '".$qry_modulos['id_modulo']."' and id_sistema like '%".$_SESSION['id_sistema']."%' " ) );
                        $totb = $qry_conta_bloqueios['total'];
                        $totb = $totb + $funcoes_implantador;
                        $totf = $qry_conta_funcoes['total'];
                        if ( $totb < $totf ){
                            ?>
                            <div id="menu_btn_grupo" width="14">
                                <a href="javascript:void(escondediv(<?php echo $i;?>,n_divs))" class="link_menu" >
                                    <img src="images/icon-menu-principal.png"  />
                                </a>
                                <a href="javascript:void(escondediv(<?php echo $i ;?>,n_divs))" class="link_menu" >
                                    <div><?php echo $qry_modulos['nome_modulo'];?></div>
                                </a>
                            </div>

                            <div id="mdiv<?php echo $i;?>" style="display:none">
                                <?php
                                $p_modulo = $qry_modulos['id_modulo'];
                                $i = $i + 1;
                                include("sub_menu.php");
                                ?>
                            </div>
                            <?php
                        }
                    }?>
                </div>
            </td>
            <td width="100%" height="100%" valign="top">
                <div name="div_programa" id="div_programa">
                    <?php
                    if ( $pfuncaoini ){
                        $ref = "gera_cad_detalhe.php?pfuncao=".$pfuncaoini."&pnumreg=".$pnumregini."&psubdet=&pnpai=&pemail=".$pemail;
                        $url_open = "javascript:window.open('".$ref."',this.target,'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=50,left=50'); return false;";
                        echo '<br><br><br><br><br><br><center><a href="#" onclick="'.$url_open.'"><b>CLIQUE AQUI para abrir a atividade...</b></a></center>';
                    }else{
                        require_once( "painel_inicial_crm.php" );
                    }
                    ?>
                </div>
            </td>
        </tr>
    </table>
</div>

