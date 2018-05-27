<?php
switch ($tipo) {
	case 'memo': {
		if ($qry_gera_cad_campos['tamanho_campo'] == '777') {
			echo '<textarea style="font-family:Courier New; font-size:12px;" name="edt' . $id_campo . '" id="edt' . $id_campo . '" cols=90 rows=28 ' . $readonly . ' ' . $evento_change . ' ' . $evento_keypress . '>' . str_replace( '<br/>', '<br />', str_replace( '\\ ', '"', $vl_campo ) ) . '</textarea>';
		} else {
			echo '<textarea style="font-family:Courier New; font-size:12px;" name="edt' . $id_campo . '" id="edt' . $id_campo . '" cols=70 rows=7 ' . $readonly . ' ' . $evento_change . ' ' . $evento_keypress . '>' . str_replace( '<br/>', chr( 13 ), str_replace( '<br />', chr( 13 ), $vl_campo ) ) . '</textarea>';
		}
		break;
	}
	case 'lupa': {
		echo '<input type="text" name="edt' . $id_campo . '" id="edt' . $id_campo . '" size="10" ' . $readonly . ' value="' . $vl_campo . '">';
		if (empty( $readonly )) {
			echo ' <a href="javascript:lupa(';
			echo '\'' . $id_campo . '\',\'' . $id_funcao . '<cod>\');';
			echo '">';
			echo '<img border=0 width=15 height=15 src="images/btn_busca.PNG" alt="Buscar">';
			echo '</a>';
		}
		$sql_lupa = $qry_gera_cad_campos['sql_lupa'];
		if (strpos( $sql_lupa, 'where' ) === false) {
			$clausula_lupa = ' where';
		} else {
			$clausula_lupa = ' and';
		}
		$sql_lupa .= $clausula_lupa . ' ' . $pref_bd . $qry_gera_cad_campos['id_campo_lupa'] . $pref_bd . ' = \'' . $vl_campo . '\'';
		$qry_lupa = farray( query( $sql_lupa ) );
		echo '<font face="Verdana" size="1">&nbsp;&nbsp;'; ?>
		<input type="text" name="edtdescr'<?php echo $id_campo;?>" id="edtdescr'<?php echo $id_campo;?>'" size="62" value="'<?php echo str_replace('"', ' ', $qry_lupa[$qry_gera_cad_campos['campo_descr_lupa']] ) ?>" '<?php echo  $readonly ;?>' '<?php echo $evento_change?>' '<?php echo $evento_keypress ?>'>
        <?php
        $qry_gera_cad_lupa = farray( query( 'select * from is_gera_cad where id_cad = \'' . $qry_gera_cad_campos['id_funcao_lupa'] . '\'' ) );
		$qry_funcao_lupa = farray( query( 'select * from is_funcoes where id_funcao = \'' . $qry_gera_cad_campos['id_funcao_lupa'] . '\'' ) );
		if (empty( $readonly )) { ?>
			<a href="javascript:lupa('<?php echo $id_campo; ?> <?php echo $id_funcao ?>')'">
			<img border=0 width=15 height=15 src="images/btn_busca.PNG" alt="Buscar">
			</a>
            <?php
			if (strpos( $qry_funcao_lupa['url_programa'], 'pread=S' ) === false) {
				$lupa_read = '0';
			} else {
				$lupa_read = '1';
			}
			if (( $qry_funcao_lupa['id_funcao'] && $lupa_read == '0' )) {
				$ref = 'gera_cad_lista.php?pfuncao=' . $qry_funcao_lupa['id_funcao'] . '&pdrilldown=1';
				$url_open = 'javascript:window.open(\'' . $ref . '\',\'' . $id_funcao . $qry_gera_cad_campos['id_campo'] . '\',\'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=900,height=550,top=50,left=50\').focus(); return false;';
				echo ' <a href="#" onclick="' . $url_open . '"><img border=0 width=15 height=15 src="images/btn_add.PNG" alt="+ Incluir"></a>';
			}
        }
		if ($qry_gera_cad_lupa['url_alterar']) {
            $ref = str_replace( '@pnumreg', '\\\'+document.getElementById(\'edt' . $id_campo . '\').value+\'', $qry_gera_cad_lupa['url_alterar'] ) . '&pidlupa=' . $qry_gera_cad_campos['id_campo_lupa'];
			$url_open = 'javascript:window.open(\'' . $ref . '\',\'' . $id_funcao . $qry_gera_cad_campos['id_campo'] . '\',\'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=50,left=50\').focus(); return false;';
			echo ' <a href="#" onclick="' . $url_open . '"><img border=0 width=15 height=15 src="images/btn_det.PNG" alt="Ver Detalhes"></a>';
        }
		echo '</font>';
		break;
	}
	case 'combobox': {
		$combo_sql_lupa = $qry_gera_cad_campos['sql_lupa'];


		if (trim( $qry_gera_cad_campos['filtro_fixo'] )) {

			if (strpos( $combo_sql_lupa, 'where' ) === false) {

				$combo_sql_lupa .= ' where ' . $qry_gera_cad_campos['filtro_fixo'];

			} else {

				$combo_sql_lupa .= ' and ' . $qry_gera_cad_campos['filtro_fixo'];

			}

		}



		$filtro_lupa = trata_tags_sql( $combo_sql_lupa ) . ' order by ' . $qry_gera_cad_campos['campo_descr_lupa'];

		$filtro_lupa = str_replace( '@vs_id_usuario', $vs_id_usuario, $filtro_lupa );

		$filtro_lupa = str_replace( '@vs_id_perfil', $vs_id_perfil, $filtro_lupa );

		$filtro_lupa = str_replace( '@vs_dt_hoje', date( 'Y-m-d' ), $filtro_lupa );

		$filtro_lupa = str_replace( '@sf', '\'', $filtro_lupa );

		$filtro_lupa = str_replace( '@s', '\'', $filtro_lupa );

		$filtro_lupa = str_replace( '@vs_id_sistema', $_SESSION['id_sistema'], $filtro_lupa );

		$filtro_lupa = str_replace( '@vs_cpo_id_funcao', $qry_cadastro['id_funcao'], $filtro_lupa );



		if ($qry_cadastro['id_workflow']) {

			$filtro_lupa = str_replace( '@vs_cpo_id_workflow', $qry_cadastro['id_workflow'], $filtro_lupa );

		} else {

			$filtro_lupa = str_replace( '@vs_cpo_id_workflow', $qry_mestre['id_cad'], $filtro_lupa );

		}



		$filtro_lupa = str_replace( '@vs_id_pessoa', $qry_cadastro['id_pessoa'], $filtro_lupa );

		$filtro_lupa = str_replace( '@mestre_id_pessoa', $qry_mestre['id_pessoa'], $filtro_lupa );

		$sql_lupa = query( $filtro_lupa, 1, $fonte_odbc_lupa );

		echo '<div id="div' . $id_campo . '" style="display: inline;">';

		echo '<select class="form-control-combo" name="edt' . $id_campo . '" id="edt' . $id_campo . '" ' . $readonly . ' ' . $evento_change . ' ' . $evento_keypress . '>';



		if ($readonly) {

			$combo_disable = 'disabled';

		} else {

			$combo_disable = '';

		}



		echo '<option ' . $combo_disable . ' value=""></option>';



		while ($qry_lupa = farray( $sql_lupa, $fonte_odbc_lupa )) {

			if ($qry_lupa[$qry_gera_cad_campos['id_campo_lupa']] == $vl_campo) {

				echo '<option value="' . $qry_lupa[$qry_gera_cad_campos['id_campo_lupa']] . '" selected>' . $qry_lupa[$qry_gera_cad_campos['campo_descr_lupa']] . '</option>';

				continue;

			}



			echo '<option ' . $combo_disable . ' value="' . $qry_lupa[$qry_gera_cad_campos['id_campo_lupa']] . '">' . $qry_lupa[$qry_gera_cad_campos['campo_descr_lupa']] . '</option>';

		}



		echo '</select>';



		if (empty( $readonly )) {

			$qry_gera_cad_lupa = farray( query( 'select * from is_gera_cad where id_cad = \'' . $qry_gera_cad_campos['id_funcao_lupa'] . '\'' ) );



			if ($qry_gera_cad_lupa['url_alterar']) {

				if ($qry_gera_cad_campos['sn_lupa_bloqueia_incluir'] == '0') {

					$ref = str_replace( '@pnumreg', '-1', $qry_gera_cad_lupa['url_alterar'] );

					$url_open = 'javascript:window.open(\'' . $ref . '\',\'' . $id_funcao . $qry_gera_cad_campos['id_campo'] . '\',\'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=50,left=50\').focus(); return false;';

					echo ' <a href="#" onclick="' . $url_open . '"><img border=0 width=15 height=15 src="images/btn_add.PNG" alt="+ Incluir"></a>';

				}



				$ref = str_replace( '@pnumreg', '\\\'+document.getElementById(\'edt' . $id_campo . '\').value+\'', $qry_gera_cad_lupa['url_alterar'] ) . '&pidlupa=' . $qry_gera_cad_campos['id_campo_lupa'];

				

				$url_open = 'javascript:window.open(\'' . $ref . '\',\'' . $id_funcao . $qry_gera_cad_campos['id_campo'] . '\',\'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=50,left=50\').focus(); return false;';

				echo ' <a href="#" onclick="' . $url_open . '"><img border=0 width=15 height=15 src="images/btn_det.PNG" alt="Ver Detalhes"></a>';

			}

		}



		echo '</div><font face="Verdana" size="1">&nbsp;</font>';

		break;

	}
	case 'multicheck': {

		echo '<div id="div' . $id_campo . '" style="display: inline;">';

		$checkall = '';

		$combo_sql_lupa = $qry_gera_cad_campos['sql_lupa'];



		if (trim( $qry_gera_cad_campos['filtro_fixo'] )) {

			if (strpos( $combo_sql_lupa, 'where' ) === false) {

				$combo_sql_lupa .= ' where ' . $qry_gera_cad_campos['filtro_fixo'];

			} else {

				$combo_sql_lupa .= ' and ' . $qry_gera_cad_campos['filtro_fixo'];

			}

		}



		$filtro_lupa = trata_tags_sql( $combo_sql_lupa ) . ' order by ' . $qry_gera_cad_campos['campo_descr_lupa'];

		$filtro_lupa = str_replace( '@vs_id_usuario', $vs_id_usuario, $filtro_lupa );

		$filtro_lupa = str_replace( '@vs_id_perfil', $vs_id_perfil, $filtro_lupa );

		$filtro_lupa = str_replace( '@vs_id_empresa_contato', $qry_cadastro['id_empresa_contato'], $filtro_lupa );

		$filtro_lupa = str_replace( '@vs_id_empresa', $qry_cadastro['id_empresa'], $filtro_lupa );

		$filtro_lupa = str_replace( '@vs_dt_hoje', date( 'Y-m-d' ), $filtro_lupa );

		$filtro_lupa = str_replace( '@vs_cpo_id_funcao', $qry_cadastro['id_funcao'], $filtro_lupa );

		$filtro_lupa = str_replace( '@vs_id_sistema', $_SESSION['id_sistema'], $filtro_lupa );

		$filtro_lupa = str_replace( '@s', '\'', $filtro_lupa );

		$filtro_lupa = str_replace( '@sf', '\'', $filtro_lupa );

		$sql_lupa = query( $filtro_lupa );

		$num_col = 1;



		while ($qry_lupa = farray( $sql_lupa )) {

			++$num_check;

			$npos_check = strpos( $vl_campo . ',', $qry_lupa[$qry_gera_cad_campos['id_campo_lupa']] . ',' );



			if ($npos_check === false) {

				if (empty( $readonly )) {

					echo '<input type="checkbox" name="edt' . $id_campo . '[]" id="edt' . $id_campo . '" value="' . $qry_lupa[$qry_gera_cad_campos['id_campo_lupa']] . '">' . $qry_lupa[$qry_gera_cad_campos['campo_descr_lupa']] . '<br>';

					$num_col = $num_col + 1;

				}

			} else {

				echo '<input type="checkbox" name="edt' . $id_campo . '[]" id="edt' . $id_campo . '" value="' . $qry_lupa[$qry_gera_cad_campos['id_campo_lupa']] . '" checked="yes">' . $qry_lupa[$qry_gera_cad_campos['campo_descr_lupa']] . '<br>';

				$num_col = $num_col + 1;

			}





			if (3 < $num_col) {

				$num_col = 1;

			}



			$checkall .= 'document.getElementById(\'edt' . $id_campo . $num_check . '\').checked = this.checked; ';

		}



		echo '</div><font face="Verdana" size="1">&nbsp;</font>';

		break;

	}

//	case 'lupa_popup': {
//		if ($sn_oculta_codigo_lupa_popup == '1') {
//			echo '<input type="hidden" name="edt' . $qry_gera_cad_campos['id_campo'] . '" id="edt' . $qry_gera_cad_campos['id_campo'] . '" value="' . $vl_campo . '">';
//		} else {
//			echo '<input type="text" name="edt' . $qry_gera_cad_campos['id_campo'] . '" id="edt' . $qry_gera_cad_campos['id_campo'] . '" readonly size="10" value="' . $vl_campo . '" style="background-color:#CCCCCC">';
//			echo '&nbsp;-';
//		}
//
//		$sql_lupa = $qry_gera_cad_campos['sql_lupa'];
//		if (strpos( $sql_lupa, 'where' ) === false) {
//			$clausula_lupa = ' where';
//		} else {
//			$clausula_lupa = ' and';
//		}
//		if ($fonte_odbc_lupa) {
//			$qry_lupa = farray( @query( $sql_lupa . $clausula_lupa . ' "' . $qry_gera_cad_campos['id_campo_lupa'] . '"' . ' = \'' . $vl_campo . '\'', 1, $fonte_odbc_lupa ), $fonte_odbc_lupa );
//		} else {
//			$qry_lupa = farray( query( $sql_lupa . $clausula_lupa . ' ' . $qry_gera_cad_campos['id_campo_lupa'] . ' = \'' . $vl_campo . '\'', 1, $fonte_odbc_lupa ), $fonte_odbc_lupa );
//		}
//
//		if ($sn_oculta_codigo_lupa_popup != '1') {
//			echo '<font face="Verdana" size="1">&nbsp;&nbsp;';
//		}
//
//		echo '<input type="text" name="edtdescr' . $qry_gera_cad_campos['id_campo'] . '" id="edtdescr' . $qry_gera_cad_campos['id_campo'] . '" size="62" value="' . str_replace( '"', ' ', $qry_lupa[$qry_gera_cad_campos['campo_descr_lupa']] ) . '" readonly style="background-color:#CCCCCC" >';
//
//		$qry_gera_cad_lupa = farray( query( 'select * from is_gera_cad where id_cad = \'' . $qry_gera_cad_campos['id_funcao_lupa'] . '\'' ) );
//		$qry_funcao_lupa = farray( query( 'select * from is_gera_cad where id_funcao = \'' . $qry_gera_cad_campos['id_funcao_lupa'] . '\'' ) );
//		$pnpai_lupa_filtro_fixo = $qry_gera_cad_campos['filtro_fixo'];
//		$pnpai_lupa_filtro_fixo_pos_ini = strpos( $pnpai_lupa_filtro_fixo, '@gfi' );
//
//		if (!( $pnpai_lupa_filtro_fixo_pos_ini === false )) {
//			$pnpai_lupa_filtro_fixo_pos_fim = strpos( $pnpai_lupa_filtro_fixo, '@gff' );
//			$pnpai_lupa_filtro_fixo = substr( $pnpai_lupa_filtro_fixo, $pnpai_lupa_filtro_fixo_pos_ini + 4, $pnpai_lupa_filtro_fixo_pos_fim - ( $pnpai_lupa_filtro_fixo_pos_ini + 4 ) );
//			if ($pnpai_lupa_filtro_fixo) {
//				$pnpai_lupa_filtro_fixo_campo_det = substr( $qry_gera_cad_campos['filtro_fixo'], 0, strpos( $qry_gera_cad_campos['filtro_fixo'], '@igual' ) );
//				$a_pnpai_lupa_filtro_fixo_sub = farray( query( 'select numreg from is_gera_cad_sub where id_funcao_detalhe = \'' . $qry_gera_cad_campos['id_funcao_lupa'] . '\' and campo_detalhe = \'' . $pnpai_lupa_filtro_fixo_campo_det . '\'' ) );
//				$pnpai_lupa_filtro_fixo = '\'&psubdet=' . $a_pnpai_lupa_filtro_fixo_sub['numreg'] . '&pnpai=\'+document.getElementById(\'' . $pnpai_lupa_filtro_fixo . '\').value';
//			}
//		} else {
//			$pnpai_lupa_filtro_fixo = "'";
//		}
//
//		if (empty( $readonly )) {
//			$ref = '\'gera_cad_lista.php?pfuncao=' . $qry_gera_cad_campos['id_funcao_lupa'] . '&pdrilldown=1&plupa=' . $qry_gera_cad_campos['numreg'] . '&pfixo=' . str_replace( '@gfi', '\\\'+document.getElementById(\'', str_replace( '@gff', '\\\').value+\'', str_replace( '@sf', '\'', $qry_gera_cad_campos['filtro_fixo'] ) ) ) . '\'+' . $pnpai_lupa_filtro_fixo;
//			$url_open = 'javascript:window.open(' . $ref . ',\'' . $id_funcao . $qry_gera_cad_campos['id_campo'] . '\',\'location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=650,height=350,top=250,left=250\').focus(); return false;';
//
//			echo ' <a href="#" onclick="'.$url_open.'"><img border=0 width=15 height=15 src="images/btn_busca.PNG" alt="Buscar"></a>';
//			$ref = 'document.getElementById(' . '\'edt' . $qry_gera_cad_campos['id_campo'] . '\').value=\'\'; ' . 'document.getElementById(' . '\'edtdescr' . $qry_gera_cad_campos['id_campo'] . '\').value=\'\'; ';
//			$url_open = 'javascript:' . $ref . '; return false;';
//
//			echo ' <a href="#" onclick="' . $url_open . '"><img border=0 width=15 height=15 src="images/btn_eraser.PNG" alt="Limpar"></a>';
//			if (( empty( $qry_gera_cad_campos['id_funcao_lupa'] ) || $qry_gera_cad_campos['sn_lupa_bloqueia_incluir'] == '0' )) {
//				if ($qry_gera_cad_lupa['url_alterar']) {
//					$ref = '\'' . str_replace( '@pnumreg', '-1', $qry_gera_cad_lupa['url_alterar'] ) . '&plupa=' . $qry_gera_cad_campos['numreg'] . '\'+' . $pnpai_lupa_filtro_fixo;
//					$url_open = 'javascript:window.open(' . $ref . ',\'' . $id_funcao . $qry_gera_cad_campos['id_campo'] . '\',\'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=750,height=550,top=50,left=50\').focus(); return false;';
//					echo ' <a href="#" onclick="' . $url_open . '"><img border=0 width=15 height=15 src="images/btn_add.PNG" alt="+ Incluir"></a>';
//				}
//			}
//		}
//		if ($qry_gera_cad_lupa['url_alterar']) {
//			$ref = '\'' . str_replace( '@pnumreg', '\\\'+document.getElementById(\'edt' . $qry_gera_cad_campos['id_campo'] . '\').value+\'', $qry_gera_cad_lupa['url_alterar'] ) . '&pidlupa=' . $qry_gera_cad_campos['id_campo_lupa'] . $pread_campo_lupa . '\'+' . $pnpai_lupa_filtro_fixo;
//			$url_open = 'javascript:window.open(' . $ref . ',\'' . $id_funcao . $qry_gera_cad_campos['id_campo'] . '\',\'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=750,height=550,top=50,left=50\').focus(); return false;';
//			echo ' <a href="#" onclick="' . $url_open . '"><img border=0 width=15 height=15 src="images/btn_det.PNG" alt="Ver Detalhes"></a>';
//		}
//		echo '</font>';
//		break;
//	}
    case "lupa_popup" :{
        do{
            do{
                if ( $sn_oculta_codigo_lupa_popup == "1" )                {
                    echo "<input type=\"hidden\" name=\"edt".$qry_gera_cad_campos['id_campo']."\" id=\"edt".$qry_gera_cad_campos['id_campo']."\" value=\"".$vl_campo."\">";
                }else{
                    echo "<input class=\"form-control-lupa_id \" type=\"text\" name=\"edt".$qry_gera_cad_campos['id_campo']."\" id=\"edt".$qry_gera_cad_campos['id_campo']."\" readonly size=\"10\" value=\"".$vl_campo."\" style=\"background-color:#CCCCCC\">";
                    echo "&nbsp;-";
                }
                $sql_lupa = $qry_gera_cad_campos['sql_lupa'];
                if ( strpos( $sql_lupa, "where" ) === false ){
                    $clausula_lupa = " where";
                }else{
                    $clausula_lupa = " and";
                }

                if ( $fonte_odbc_lupa ){
                    $qry_lupa = @farray( @query( $sql_lupa.$clausula_lupa." \"".$qry_gera_cad_campos['id_campo_lupa']."\""." = '".$vl_campo."'", 1, $fonte_odbc_lupa ), $fonte_odbc_lupa );
                }else{
                    $qry_lupa = farray( query( $sql_lupa.$clausula_lupa." ".$qry_gera_cad_campos['id_campo_lupa']." = '".$vl_campo."'", 1, $fonte_odbc_lupa ), $fonte_odbc_lupa );
                }

                if ( $sn_oculta_codigo_lupa_popup != "1" ){
                    echo "<font face=\"Verdana\" size=\"1\">&nbsp;&nbsp;";
                }

                echo "<input class=\"form-control_lupa\" type=\"text\" name=\"edtdescr".$qry_gera_cad_campos['id_campo']."\" id=\"edtdescr".$qry_gera_cad_campos['id_campo']."\" size=\"62\" value=\"".str_replace( "\"", " ", $qry_lupa[$qry_gera_cad_campos['campo_descr_lupa']] )."\" readonly style=\"background-color:#CCCCCC\" >";
                $qry_gera_cad_lupa = farray( query( "select * from is_gera_cad where id_cad = '".$qry_gera_cad_campos['id_funcao_lupa']."'" ) );
                $qry_funcao_lupa = farray( query( "select * from is_gera_cad where id_funcao = '".$qry_gera_cad_campos['id_funcao_lupa']."'" ) );
                $pnpai_lupa_filtro_fixo = $qry_gera_cad_campos['filtro_fixo'];
                $pnpai_lupa_filtro_fixo_pos_ini = strpos( $pnpai_lupa_filtro_fixo, "@gfi" );
                if ( ( $pnpai_lupa_filtro_fixo_pos_ini === false ) ){
                    break;
                }else{
                    $pnpai_lupa_filtro_fixo_pos_fim = strpos( $pnpai_lupa_filtro_fixo, "@gff" );
                    $pnpai_lupa_filtro_fixo = substr( $pnpai_lupa_filtro_fixo, $pnpai_lupa_filtro_fixo_pos_ini + 4, $pnpai_lupa_filtro_fixo_pos_fim - ( $pnpai_lupa_filtro_fixo_pos_ini + 4 ) );
                    if ( !$pnpai_lupa_filtro_fixo ){
                        break;
                    }else{
                        $pnpai_lupa_filtro_fixo_campo_det = substr( $qry_gera_cad_campos['filtro_fixo'], 0, strpos( $qry_gera_cad_campos['filtro_fixo'], "@igual" ) );
                        $a_pnpai_lupa_filtro_fixo_sub = farray( query( "select numreg from is_gera_cad_sub where id_funcao_detalhe = '".$qry_gera_cad_campos['id_funcao_lupa']."' and campo_detalhe = '".$pnpai_lupa_filtro_fixo_campo_det."'" ) );
                        $pnpai_lupa_filtro_fixo = "'&psubdet=".$a_pnpai_lupa_filtro_fixo_sub['numreg']."&pnpai='+document.getElementById('".$pnpai_lupa_filtro_fixo."').value";
                    }
                }
                break;
            } while ( 0 );
            $pnpai_lupa_filtro_fixo = "''";
        } while ( 0 );
        if ( empty( $readonly ) ){
            $ref = "'"."gera_cad_lista.php?pfuncao=".$qry_gera_cad_campos['id_funcao_lupa']."&pdrilldown=1&plupa=".$qry_gera_cad_campos['numreg']."&pfixo=".str_replace( "@gfi", "'+document.getElementById('", str_replace( "@gff", "').value+'", str_replace( "@sf", "'", $qry_gera_cad_campos['filtro_fixo'] ) ) )."'+".$pnpai_lupa_filtro_fixo;
            $url_open = "javascript:window.open(".$ref.",'".$id_funcao.$qry_gera_cad_campos['id_campo']."','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=650,height=350,top=250,left=250').focus(); return false;";
            echo " <a href=\"#\" onclick=\"".$url_open."\"><img border=0 width=15 height=15 src=\"images/btn_busca.PNG\" alt=\"Buscar\"></a>";
            $ref = "document.getElementById("."'edt".$qry_gera_cad_campos['id_campo']."').value=''; "."document.getElementById("."'edtdescr".$qry_gera_cad_campos['id_campo']."').value=''; ";
            $url_open = "javascript:".$ref."; return false;";
            echo " <a href=\"#\" onclick=\"".$url_open."\"><img border=0 width=15 height=15 src=\"images/btn_eraser.PNG\" alt=\"Limpar\"></a>";
            if ( ( empty( $qry_gera_cad_campos['id_funcao_lupa'] ) || $qry_gera_cad_campos['sn_lupa_bloqueia_incluir'] == "0" ) && $qry_gera_cad_lupa['url_alterar'] )
            {
                $ref = "'".str_replace( "@pnumreg", "-1", $qry_gera_cad_lupa['url_alterar'] )."&plupa=".$qry_gera_cad_campos['numreg']."'+".$pnpai_lupa_filtro_fixo;
                $url_open = "javascript:window.open(".$ref.",'".$id_funcao.$qry_gera_cad_campos['id_campo']."','location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=750,height=550,top=50,left=50').focus(); return false;";
                echo " <a href=\"#\" onclick=\"".$url_open."\"><img border=0 width=15 height=15 src=\"images/btn_add.PNG\" alt=\"+ Incluir\"></a>";
            }
        }
        if ( $qry_gera_cad_lupa['url_alterar'] ){
            $ref = "'".str_replace( "@pnumreg", "'+document.getElementById('edt".$qry_gera_cad_campos['id_campo']."').value+'", $qry_gera_cad_lupa['url_alterar'] )."&pidlupa=".$qry_gera_cad_campos['id_campo_lupa'].$pread_campo_lupa."'+".$pnpai_lupa_filtro_fixo;
            $url_open = "javascript:window.open(".$ref.",'".$id_funcao.$qry_gera_cad_campos['id_campo']."','location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=750,height=550,top=50,left=50').focus(); return false;";
            echo " <a href=\"#\" onclick=\"".$url_open."\"><img border=0 width=15 height=15 src=\"images/btn_det.PNG\" alt=\"Ver Detalhes\"></a>";
        }
        echo "</font>";
        break;
    }
	case 'sim_nao': {

		echo '<div id="div' . $id_campo . '" style="display: inline;">';

		echo '<select class="form-control-combo" name="edt' . $id_campo . '" id="edt' . $id_campo . '" ' . $readonly . ' ' . $evento_change . ' ' . $evento_keypress . '>';



		if ($vl_campo == '') {

			echo '<option value="" selected></option>';

		} else {

			if (empty( $readonly )) {

				echo '<option value=""></option>';

			}

		}





		if ($vl_campo == '1') {

			echo '<option value="1" selected>Sim</option>';

		} else {

			if (empty( $readonly )) {

				echo '<option value="1">Sim</option>';

			}

		}





		if ($vl_campo == '0') {

			echo '<option value="0" selected>Não</option>';

		} else {

			if (empty( $readonly )) {

				echo '<option value="0">Não</option>';

			}

		}



		echo '</select></div><font face="Verdana" size="1">&nbsp;</font>';

		break;

	}

    case 'sexo': {

		echo '<div id="div' . $id_campo . '" style="display: inline;">';

		echo '<select class="form-control-combo" name="edt' . $id_campo . '" id="edt' . $id_campo . '" ' . $readonly . ' ' . $evento_change . ' ' . $evento_keypress . '>';

		echo '<option value=""';



		if ($vl_campo == '') {

			echo ' selected ';

		}



		echo '></option>';

		echo '<option value="M"';



		if ($vl_campo == 'M') {

			echo ' selected ';

		}



		echo '>Masculino</option>';

		echo '<option  value="F"';



		if ($vl_campo == 'F') {

			echo ' selected ';

		}



		echo '>Feminino</option>';

		echo '</select></div><font face="Verdana" size="1">&nbsp;</font>';

		break;

	}

	case 'date': {

		if (empty( $q_postback['numreg'] )) {

			if ($vl_campo == '17530101') {

				$vl_campo = '';

			}





			if (trim( $vl_campo )) {

				$vl_campo_trat = substr( $vl_campo, 8, 2 ) . '/' . substr( $vl_campo, 5, 2 ) . '/' . substr( $vl_campo, 0, 4 );

			} else {

				$vl_campo_trat = '';

			}





			if ($vl_campo_trat == '01/01/1753') {

				$vl_campo_trat = '';

			}

		} else {

			$vl_campo_trat = $vl_campo;

		}





		if ($readonly) {

			echo '<input  class="form-control" maxlength=10 readOnly="readOnly" type="text" name="edt' . $id_campo . '" id="edt' . $id_campo . '" ' . $evento_change . ' size="9" ' . $readonly . ' value="' . $vl_campo_trat . '"> ';

		} else {

			echo '<input class="form-control" maxlength=10 type="text" name="edt' . $id_campo . '" id="edt' . $id_campo . '" ' . $evento_change . ' size="9" ' . $readonly . ' value=""> ';

			echo '<script language="JavaScript">$(document).ready(function(){ $("#edt' . $id_campo . '") . datepicker({showOn: "button",buttonImage: "images/agenda.gif",buttonImageOnly: true,changeMonth:true, changeYear:true});$("#edt' . $id_campo . '") . datepicker("option", "dateFormat", "dd/mm/yy"); $("#edt' . $id_campo . '").val(' . '\'' . $vl_campo_trat . '\');}); </script>';

		}



		break;

	}

	case 'senha': {

		echo '<input class="form-control" type="password" name="edt' . $id_campo . '" id="edt' . $id_campo . '" size="' . $qry_gera_cad_campos['tamanho_campo'] . '" value="' . $vl_campo . '" ' . $readonly . ' ' . $evento_change . ' ' . $evento_keypress . '>';

		break;

	}

	case 'arquivo': {

		echo '<input class="form-control" type="file" name="edt' . $id_campo . '" id="edt' . $id_campo . '" size="' . $qry_gera_cad_campos['tamanho_campo'] . '" ' . $evento_change . ' ' . $evento_keypress . '>';



		if ($vl_campo) {

			echo '<br><a href="./arquivos/' . $vl_campo . '" target="_blank"><b>' . $vl_campo . ' - Clique aqui para abrir...</b></a>';

			echo $url_down;

		}



		break;

	}

	case 'calculado': {

		$vl_campo = campo_calculado( $qry_gera_cad_campos['id_funcao'], $qry_gera_cad_campos['id_campo'], $qry_cadastro );



		if ($readonly) {

			echo $vl_campo;

		} else {

			echo '<input class="form-control" type="text" name="edt' . $id_campo . '" id="edt' . $id_campo . '" size="' . $qry_gera_cad_campos['tamanho_campo'] . '" value="' . $vl_campo . '" ' . $readonly . ' ' . $evento_change . ' ' . $evento_keypress . '>';

		}



		break;

	}

	default: {

		if (( ( $tipo == 'money' || $tipo == 'real' ) || $tipo == 'float' )) {

			if (empty( $q_postback['numreg'] )) {

				if ($vl_campo) {

					$vl_campo = number_format( $vl_campo, 2, ',', '.' );

				}

			}

		}





		if ($tipo == 'int') {

			if (empty( $q_postback['numreg'] )) {

				if ($vl_campo) {

					$vl_campo = $vl_campo * 1;

				}

			}

		}



		echo '<input type="text" name="edt' . $id_campo . '" class="form-control"  id="edt' . $id_campo . '" ' . $max_carac . ' size="' . $qry_gera_cad_campos['tamanho_campo'] . '" value="' . $vl_campo . '" ' . $readonly . ' ' . $evento_change . ' ' . $evento_keypress . '>';

		break;

	}

}

