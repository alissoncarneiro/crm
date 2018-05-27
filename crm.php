<div class="metro" style="background-color: #fdfdfd !important;min-height: 100px;">
    <div class="fluent-menu" data-role="fluentmenu" style="width: 100%; display: block;">
    <?php
		
		session_start();

		$filtro_licenca = " and (id_licenca is null or id_licenca = '' or id_licenca like '%PADRAO%' or id_licenca like '%".$_SESSION['lic_id']."%')";
		$edtusuario = $_POST['edtusuario'];
		
		/* Bloqueios de Menu e Sub */
		$sqlBloqueiosMenuSub = "select * from is_perfil_funcao_bloqueio where id_perfil= ".$_SESSION['id_perfil'];
		$qryBloqueios = query($sqlBloqueiosMenuSub);
		while($arBloqueios = farray($qryBloqueios)){
			$arrayBloqueios[$arBloqueios['id_modulo']][$arBloqueios['id_funcao']] = array(
					'numreg'		=>	$arBloqueios['numreg']				!= '' ? $arBloqueios['numreg'] 				: 'NULL',
					'id_perfil'		=>	$arBloqueios['id_perfil']			!= '' ? $arBloqueios['id_perfil'] 			: 'NULL',
					'id_modulo'		=>	$arBloqueios['id_modulo']			!= '' ? $arBloqueios['id_modulo'] 			: 'NULL',
					'id_funcao'		=>	$arBloqueios['id_funcao']			!= '' ? $arBloqueios['id_funcao'] 			: 'NULL',
					'bloqueios'		=>	$arBloqueios['bloqueios']			!= '' ? $arBloqueios['bloqueios'] 			: 'NULL',
					'sn_bloqueio_abrir' 	=>	$arBloqueios['sn_bloqueio_abrir']	!= '' ? $arBloqueios['sn_bloqueio_abrir'] 	: 'NULL',
					'sn_bloqueio_editar'	=>	$arBloqueios['sn_bloqueio_editar']	!= '' ? $arBloqueios['sn_bloqueio_editar'] 	: 'NULL'
		
			);
		}
		
		
		$sqlModulos = "select * from is_modulos where id_sistema like '%".$_SESSION['id_sistema']."%' {$filtro_licenca} order by ordem";
		$qryModulos = query($sqlModulos);
		while($arModulos = farray($qryModulos)){
			$arrayModulo[$arModulos['id_modulo']]= array(
					'numreg'	=> $arModulos['numreg'],
					'id_modulo'	=> $arModulos['id_modulo'],
					'nome_modulo'	=> $arModulos['nome_modulo'],
					'url_imagem'	=> $arModulos['url_imagem'],
					'id_sistema'	=> $arModulos['id_sistema'],
					'ordem'		=> $arModulos['ordem'],
					'id_licenca'	=> $arModulos['id_licenca']
			);
		}

	
		/* Sub Menu */
		$sqlFuncoes = "select * from is_funcoes where id_sistema like '%".$_SESSION['id_sistema']."%' {$filtro_licenca} order by ordem" ;
		$qryFuncoes = mysql_query($sqlFuncoes);

		while($arFuncoes = mysql_fetch_assoc( $qryFuncoes )){
				

			
			if($arrayBloqueios[$arFuncoes['id_modulo']][$arFuncoes['id_funcao']]['sn_bloqueio_abrir'] != 1)
				$arrayFuncoes[$arFuncoes['id_modulo']]['modulo'] = $arrayModulo[$arFuncoes['id_modulo']];
				if($_SESSION['ip_desenvolvedor'] != 1){
					if($arFuncoes['nome_grupo'] != 'Estrutura'){
						$arrayFuncoes[$arFuncoes['id_modulo']]['menu'][$arFuncoes['nome_grupo']][$arFuncoes['id_funcao']] = array(
								'numreg'		=> $arFuncoes['numreg'] 		!= '' ? $arFuncoes['numreg'] 			: 'NULL',
								'id_modulo'		=> $arFuncoes['id_modulo']		!= '' ? $arFuncoes['id_modulo'] 		: 'NULL',
								'id_funcao'		=> $arFuncoes['id_funcao']		!= '' ? $arFuncoes['id_funcao'] 		: 'NULL',
								'nome_funcao'		=> $arFuncoes['nome_funcao']	!= '' ? $arFuncoes['nome_funcao'] 		: 'NULL',
								'url_imagem'		=> $arFuncoes['url_imagem']		!= '' ? $arFuncoes['url_imagem'] 		: 'NULL',
								'url_programa'		=> $arFuncoes['url_programa']	,
								'nome_grupo'		=> $arFuncoes['nome_grupo']		!= '' ? $arFuncoes['nome_grupo'] 		: 'NULL',
								'ordem'			=> $arFuncoes['ordem']			!= '' ? $arFuncoes['ordem']	 			: 'NULL',
								'id_sistema'		=> $arFuncoes['id_sistema']		!= '' ? $arFuncoes['id_sistema'] 		: 'NULL',
								'id_licenca'		=> $arFuncoes['id_licenca']		!= '' ? $arFuncoes['id_licenca'] 		: 'NULL',
								'url_imagem_menu'	=> $arFuncoes['url_imagem_menu']!= '' ? $arFuncoes['url_imagem_menu'] 	: 'NULL',
						);
					}
				}else{
					$arrayFuncoes[$arFuncoes['id_modulo']]['menu'][$arFuncoes['nome_grupo']][$arFuncoes['id_funcao']] = array( 
						'numreg'		=> $arFuncoes['numreg'] 		!= '' ? $arFuncoes['numreg'] 			: 'NULL',
						'id_modulo'		=> $arFuncoes['id_modulo']		!= '' ? $arFuncoes['id_modulo'] 		: 'NULL',
						'id_funcao'		=> $arFuncoes['id_funcao']		!= '' ? $arFuncoes['id_funcao'] 		: 'NULL',
						'nome_funcao'		=> $arFuncoes['nome_funcao']	!= '' ? $arFuncoes['nome_funcao'] 		: 'NULL',
						'url_imagem'		=> $arFuncoes['url_imagem']		!= '' ? $arFuncoes['url_imagem'] 		: 'NULL',
						'url_programa'		=> $arFuncoes['url_programa']	,
						'nome_grupo'		=> $arFuncoes['nome_grupo']		!= '' ? $arFuncoes['nome_grupo'] 		: 'NULL',
						'ordem'			=> $arFuncoes['ordem']			!= '' ? $arFuncoes['ordem']	 			: 'NULL',
						'id_sistema'		=> $arFuncoes['id_sistema']		!= '' ? $arFuncoes['id_sistema'] 		: 'NULL',
						'id_licenca'		=> $arFuncoes['id_licenca']		!= '' ? $arFuncoes['id_licenca'] 		: 'NULL',
						'url_imagem_menu'	=> $arFuncoes['url_imagem_menu']!= '' ? $arFuncoes['url_imagem_menu'] 	: 'NULL',
					);
				}
			}

			/*Monta Menu*/?>
			<ul class="tabs-holder"><?php 
			foreach($arrayFuncoes as $keyMenu => $valMenu){
				if($valMenu['modulo']['numreg'] != ''){?>
					<li>
						<a href="<?php echo "#". $valMenu['modulo']['numreg']?>">
							<?php echo $valMenu['modulo']['nome_modulo'] ;?>
						</a>
					</li><?php 
				}
			}?>
			</ul>
			<div class="tabs-content" style="width: 100%;display: inline-block;">
				<?php 
					foreach($arrayFuncoes as $keyMenu => $valMenu){ 
						if($valMenu['modulo']['numreg'] != ''){?>
							<div class="tab-panel" id="<?php echo $valMenu['modulo']['numreg'] ;?>"  style="width: 100%; white-space: nowrap; display: block; overflow-x: scroll; overflow-y: hidden;"> <?php 
								foreach($valMenu['menu'] as $keyM => $valM){ ?>
									<div class="tab-panel-group" style="display: inline-block; float: none !important;" >
										<div class="tab-group-content"><?php 
										foreach($valM as $k =>$v){ ?>
										<?php echo str_replace( "@sf", "'", $v['url_programa'] );?>
											<div class="tab-content-segment" >
												<button class="fluent-big-button">
													<span><img src="<?php echo $v['url_imagem_menu'] ;?>" /></span>
													<span class="button-label"><?php echo $v['nome_funcao'] ;?></span>
												</button>
											</div>
										<?php } ?>
											<div class="tab-group-caption"> <?php echo $keyM ;?></div>
										</div>
										</a>
									</div>
								<?php }?>
							</div>
							<?php 
						} 
					}?>
				
			</div>
		</div>	
	</div>		
	
	

<div id="menu_horiz" style="padding-top:5px">
	<table width="100%" height="19" border="0" cellpadding="0" cellspacing="0" >
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
				<?php include( "menu_texto_custom.php" );?>
           	</td>
           	<td align="right">&nbsp;<img src="images/btn_home.jpg" width="14" height="12" align="absmiddle" />
            	<a href="javascript:exibe_programa('painel_inicial_<?php strtolower($_SESSION['id_sistema']).".php"?>')">Abrir Página Inicial</a>&nbsp;&nbsp;
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
            	<a href="index.php?sistema=<?php echo $_SESSION['id_sistema'] ;?>">Fazer Logoff</a>&nbsp;&nbsp;
            </td>
		</tr>
	</table>
</div>

<div name="div_programa" id="div_programa">
    <?php
    if ( $pfuncaoini ){
        $ref = "gera_cad_detalhe.php?pfuncao=".$pfuncaoini."&pnumreg=".$pnumregini."&psubdet=&pnpai=&pemail=".$pemail;
        $url_open = "javascript:window.open('".$ref."',this.target,'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=50,left=50'); return false;";
       ?>
        <a href="#" onclick="<?php	echo $url_open ;?>">
               <b>CLIQUE AQUI para abrir a atividade...</b>
       </a>
    <?php 
    }else{
            require_once( "painel_inicial_crm.php" );
    }
    ?>
</div>