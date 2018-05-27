<?php

session_start();
$p_perfil = $_SESSION['id_perfil'];
if ( empty( $p_modulo ) ){
    $p_modulo = "1";
}

$filtro_licenca = " and (id_licenca is null or id_licenca = '' or id_licenca like '%PADRAO%' or id_licenca like '%".$_SESSION['lic_id']."%')";
$sql_funcoes = query( "select * from is_funcoes where id_modulo = '{$p_modulo}' and id_sistema like '%".$_SESSION['id_sistema']."%' {$filtro_licenca} order by ordem" );
$nome_grupo = "";
while ( $qry_funcoes = farray( $sql_funcoes ) ){
    $qry_bloqueios = farray( query( "select * from is_perfil_funcao_bloqueio where id_perfil= '{$p_perfil}' and id_modulo = '{$p_modulo}' and id_funcao = '".$qry_funcoes['id_funcao']."'" ) );
    if ( $qry_bloqueios['sn_bloqueio_abrir'] == "1" ){
    }
    else{
        $sn_pode_exibir_submenu = 1;
        if ( $qry_funcoes['nome_grupo'] == "Estrutura" ){
            $sn_pode_exibir_submenu = 0;
            if ( $_SESSION['ip_desenvolvedor'] == "1" ){
                $sn_pode_exibir_submenu = 1;
            }
            if ( $_SESSION['ip_consultor'] == "1" && ( $qry_funcoes['id_funcao'] == "modulos_cad_lista" || $qry_funcoes['id_funcao'] == "funcoes_cad_lista" || $qry_funcoes['id_funcao'] == "gera_cad_sub_lista" ) ){
                $sn_pode_exibir_submenu = 1;
            }
        }
        if ( $nome_grupo != $qry_funcoes['nome_grupo'] ){
            $nome_grupo = $qry_funcoes['nome_grupo'];
            if ( $sn_pode_exibir_submenu == 1 ){
				$x = 1;
			?>
                <div class="titulo_menu_btn">
                    <img src="images/seta-grupo.gif"  />
                    <div><?php echo $nome_grupo; ?></div>
                </div>
            <?php
            }
        }
        if ( $sn_pode_exibir_submenu == 1 ){
			//par impar
			$x++;
			$x % 2 == 0 ? $fundo_menu = "menu_btn_impar" : $fundo_menu = "menu_btn_par";			
		?>
                <div class="menu_btn <?php echo $fundo_menu ;?>">
                    <?php
                        if ( empty( $qry_funcoes['url_imagem_menu'] ) ){
                            $ico_img = "images/menu_cadastro.png";
                        }
                        else{
                            $ico_img = $qry_funcoes['url_imagem_menu'];
                        }
                    ?>
                        <?php echo str_replace( "@sf", "'", $qry_funcoes['url_programa'] )?>
                        <img src="<?php echo $ico_img;?>" align="middle" width="14" height="13"/>

                    </a>

                        <?php echo str_replace( "@sf", "'", $qry_funcoes['url_programa'] )?>
                        <div><?php echo $qry_funcoes['nome_funcao'] ;?></div>
                    </a>
                </div>
        <?php
        }
    }
}