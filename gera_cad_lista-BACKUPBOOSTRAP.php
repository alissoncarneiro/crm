<?php
	@session_start(  );
	header( 'Cache-Control: no-cache' );
        
	$lista_vs_id_usuario        = $_SESSION['id_usuario'];
	$lista_vs_id_perfil         = $_SESSION['id_perfil'];
	$lista_sn_bloquear_leitura  = $_SESSION['sn_bloquear_leitura'];
	$lista_sn_bloquear_exclusao = $_SESSION['sn_bloquear_exclusao'];
        
	require_once( 'conecta.php' );
	require_once( 'funcoes.php' );
	require_once( 'gera_cad_calc_custom.php' );

	if (empty( $lista_vs_id_usuario )) {
            $email_login = $_GET['pemail'];

            if ($email_login) {
                    $qry_login = farray( query( 'select id_usuario from is_usuario where email = \'' . $email_login . '\'' ) );
                    $def_login = $qry_login['id_usuario'];
            }

            echo '<script>alert(\'Sua sessão expirou ! Por favor fazer o login o novamente.\');</script>';
            include( 'index.php' );
            exit(  );
	}

	$lista_id_funcao = $_GET['pfuncao'];
	if (( $_SESSION['ip_consultor'] == '1' && ( ( $lista_id_funcao == 'modulos_cad_lista' || $lista_id_funcao == 'funcoes_cad_lista' ) || $lista_id_funcao == 'gera_cad_sub_lista' ) )) {
		if ($_SESSION['ip_desenvolvedor'] == '') {
			$_GET['pbloqincluir'] = '1';
			$_GET['pbloqexcluir'] = '1';
		}
	}

	$lista_pread            = $_GET['pread'];
	$lista_pdiv             = $_GET['pdiv'];
	$lista_pgetcustom       = $_GET['pgetcustom'];
	$lista_pbloqincluir     = $_GET['pbloqincluir'];
	$lista_pbloqexcluir     = $_GET['pbloqexcluir'];
	$lista_ptitulo          = $_GET['ptitulo'];
	$lista_vs_id_empresa    = $_GET['pid_empresa'];
	$lista_pusuario_filtro  = $_GET['pusuario_filtro'];
	$lista_cbxfiltro        = $_GET['cbxfiltro'];
	$lista_edtfiltro        = $_GET['edtfiltro'];
	$lista_cbxordem         = $_GET['cbxordem'];
        $lista_id_funcao        = $lista_id_funcao == 'pessoa' ? 'todas_contas' :  $lista_id_funcao;     
        
  
	$a_bloqueio_cad         = farray( query( 'select * from is_perfil_funcao_bloqueio_cad where id_perfil = \'' . $_SESSION['id_perfil'] . '\' and id_cad = \'' . $lista_id_funcao . '\'' ) );

	if ($a_bloqueio_cad['sn_bloqueio_ver'] == '1') {
		echo 'Seu perfil de acesso não tem permissão para acessar este cadastro ! Por favor contate o administrador do sistema.';
		exit(  );
	}
	if ($a_bloqueio_cad['sn_bloqueio_editar'] == '1') {
		$lista_pread = '1';
	}
	if ($a_bloqueio_cad['sn_bloqueio_excluir'] == '1') {
		$lista_sn_bloquear_exclusao = '1';
	}
	if (empty( $lista_cbxordem )) {
		$lista_cbxordem = str_replace( '%20', ' ', $_POST['cbxordem'] );
	}
	if (empty( $lista_cbxfiltro )) {
		$lista_cbxfiltro = str_replace( '%20', ' ', $_POST['cbxfiltro'] );
	}
	if (empty( $lista_edtfiltro )) {
		$lista_edtfiltro = utf8_decode( str_replace( '%20', ' ', $_POST['edtfiltro'] ) );
	}

	$lista_sql_filtro       = $_POST['sql_filtro'];
	$lista_descr_filtro     = $_POST['descr_filtro'];
	$lista_psubdet          = $_GET['psubdet'];
	$lista_pnpai            = $_GET['pnpai'];
	$lista_filtro_licenca   = ' and (id_licenca is null or id_licenca = \'\' or id_licenca like \'%PADRAO%\' )';

	if ($lista_descr_filtro == 'limpar') {
		$lista_descr_filtro = '';
	}
	if (( ( empty( $lista_sql_filtro ) || empty( $lista_descr_filtro ) ) && empty( $lista_pnpai ) )) {
            $lista_sql_filtro = $_GET['psql_filtro'];
            $lista_descr_filtro = $_GET['pdescr_filtro'];
	}

	$lista_sql_filtro   = trim( $lista_sql_filtro );
	$lista_pos_ini      = $_GET['pos_ini'];
	$lista_pfixo        = $_GET['pfixo'];
	$lista_ppainel      = $_GET['ppainel'];
	$lista_pchave       = str_replace( '%20', ' ', $_GET['pchave'] );
	$lista_pchave2      = str_replace( '%20', ' ', $_GET['pchave2'] );
	$lista_pexibedet    = $_GET['pexibedet'];

	if (empty( $lista_pchave )) {
            $lista_pchave = 'numreg';
	}
	if (empty( $lista_pexibedet )) {
            $lista_pexibedet = '1';
	}
	if ($lista_ppainel == '1') {
            $lista_maxpag = 5;
	}else {
            $lista_maxpag = 25;
	}

	$lista_ppainel_div  = $_GET['ppainel_div'];
	$lista_pdrilldown   = $_GET['pdrilldown'];
	$lista_plupa        = $_GET['plupa'];

	if ($lista_pnpai) {
            $posicao_janela = '200';
	}else {
            $posicao_janela = '100';
	}

	$lista_sn_paginacao = '1';

	if (empty( $lista_pos_ini )) {
            $lista_pos_ini = '0';
	}
	if (empty( $lista_pread )) {
            $lista_pread = '0';
	}
	if (empty( $lista_id_funcao )) {
            $lista_id_funcao = 'empresas';
	}

	$lista_qry_gera_cad     = farray( query( 'select * from is_gera_cad where id_cad = \'' . $lista_id_funcao . '\'' ) );
	$lista_qry_funcoes      = farray( query( 'select * from is_funcoes where id_funcao = \'' . $lista_id_funcao . '\'' ) );
	$fonte_odbc             = $lista_qry_gera_cad['fonte_odbc'];
        $tabela                 = $lista_qry_gera_cad['nome_tabela'];

	if ($fonte_odbc) {
		$pref_bd_ini = '"';
		$pref_bd_fim = '"';
	}else {
            if ($tipoBanco == 'mysql') {
                    $pref_bd_ini = '`';
                    $pref_bd_fim = '`';
            }
            if ($tipoBanco == 'mssql') {
                    $pref_bd_ini = '[';
                    $pref_bd_fim = ']';
            }
	}

        
                
        if ($fonte_odbc) {
            $lista_cbxfiltro_trat = $lista_cbxfiltro;
	}
	$lista_qry_bloqueios = farray( query( 'select * from is_perfil_funcao_bloqueio where id_perfil = \'' . $lista_vs_id_perfil . '\' and id_funcao = \'' . $lista_id_funcao . '\'' ) );

	if ($lista_qry_bloqueios['sn_bloqueio_editar'] == '1') {
		$lista_pread = '1';
	}

	$lista_sql_bloqueio = '';
	$sqlCamposBloqueados = 'select * from is_perfil_funcao_bloqueio_campos where id_perfil = \'' . $_SESSION['id_perfil'] . '\' and id_cad = \'' . $lista_id_funcao . '\'  and sn_bloqueio_ver = 1';
     
	$q_bloqueio_campos = query($sqlCamposBloqueados);

	while ($a_bloqueio_campos = farray( $q_bloqueio_campos )) {
            $campos_bloqueados = $campos_bloqueados . '\'' . $a_bloqueio_campos['id_campo'] . '\',';
	}

      
        
	if ($campos_bloqueados) {
            $campos_bloqueados = 'and ( not id_campo in (' . substr( $campos_bloqueados, 0, strlen( $campos_bloqueados ) - 1 ) . '))';
	}
        


	$lista_btn_excel    = $_GET['pbtn_excel'];
	$lista_btn_graf     = $_GET['pbtn_graf'];
	$lista_btn_relat    = $_GET['pbtn_relat'];
	$lista_btn_pdf      = $_GET['pbtn_pdf'];
	$lista_btn_ajuda    = $_GET['pbtn_ajuda'];
	$qry_bloqueio_excel = farray( query( 'select * from is_perfil where id_perfil = \'' . $_SESSION['id_perfil'] . '\'' ) );
	$sn_bloquear_excel  = $qry_bloqueio_excel['sn_bloquear_excel'];

	if ($sn_bloquear_excel == '1') {
            $lista_btn_excel = '0';
	}else {
            $lista_btn_excel = '1';
	}

	if (empty( $lista_btn_graf )) {
            if (( $lista_psubdet || $lista_plupa )) {
                $lista_btn_graf = '0';
            }else {
                $lista_btn_graf = '1';
            }
	}

	if (empty( $lista_btn_relat )) {
            $a_layouts = farray( query( 'select * from is_gera_cad_relat where id_cad = \'' . $lista_id_funcao . '\'' ) );

            if (0 < $a_layouts['numreg'] * 1) {
                $lista_btn_relat = '1';
            }else {
                $lista_btn_relat = '0';
            }
	}

	if (empty( $lista_btn_pdf )) {
            $lista_btn_pdf = '0';
	}

	if (empty( $lista_btn_ajuda )) {
            $lista_btn_ajuda = '1';
	}

	require_once( 'gera_cad_bloqueios_custom.php' );

        $lista_filtro_geral = $lista_qry_gera_cad['sql_filtro'];
	if (strpos( $lista_filtro_geral, 'where' ) === false) {
            $lista_clausula = 'where';
	}else {
            $lista_clausula = 'and';
	}

	if (( $lista_cbxfiltro && $lista_edtfiltro )) {
            $lista_qry_gera_cad_campos = farray( query( '(select * from is_gera_cad_campos where id_funcao = \'' . $lista_id_funcao . '\' and id_campo = \'' . $lista_cbxfiltro . '\') union all (select * from is_gera_cad_campos_custom where id_funcao = \'' . $lista_id_funcao . '\' and id_campo = \'' . $lista_cbxfiltro . '\')' ) );

            if (( ( $lista_qry_gera_cad_campos['tipo_campo'] == 'lupa' || $lista_qry_gera_cad_campos['tipo_campo'] == 'combobox' ) || trim( $lista_qry_gera_cad_campos['tipo_campo'] ) == 'lupa_popup' )) {
                if (strpos( $lista_qry_gera_cad_campos['sql_lupa'], 'where' ) === false) {
                    $lista_clausula_lupa = 'where';
                } else {
                    $lista_clausula_lupa = 'and';
                }

                $lista_filtro_lupa = $lista_qry_gera_cad_campos['sql_lupa'] . ' ' . $lista_clausula_lupa . ' ' . $lista_qry_gera_cad_campos['campo_descr_lupa'] . ' like \'%' . $lista_edtfiltro . '%\'';
                $lista_filtro_lupa = str_replace( '@s', '\'', $lista_filtro_lupa );
                $lista_filtro_lupa = str_replace( '@vs_cpo_id_funcao', $lista_qry_cadastro['id_funcao'], $lista_filtro_lupa );
                $lista_filtro_lupa = str_replace( '@vs_id_sistema', $_SESSION['id_sistema'], $lista_filtro_lupa );

                if ($lista_qry_cadastro['id_workflow']) {
                        $lista_filtro_lupa = str_replace( '@vs_cpo_id_workflow', $lista_qry_cadastro['id_workflow'], $lista_filtro_lupa );
                }else {
                    $lista_filtro_lupa = str_replace( '@vs_cpo_id_workflow', $lista_qry_mestre['id_cad'], $lista_filtro_lupa );
                }

                $sql_qry_lupa = query( $lista_filtro_lupa );
                $ids_lup = '';

                while ($qrylup = farray( $sql_qry_lupa )) {
                    $ids_lup = $ids_lup . '\'' . $qrylup[$lista_qry_gera_cad_campos['id_campo_lupa']] . '\',';
                }
                if ($ids_lup) {
                    $ids_lup = '(' . substr( $ids_lup, 0, strlen( $ids_lup ) - 1 ) . ')';
                }else {
                    $ids_lup = '(\'-99\')';
                }

                $lista_pfiltro = ' ' . $lista_clausula . ' ' . $pref_bd_ini . $lista_cbxfiltro . $pref_bd_fim . ' in ' . $ids_lup;
                $lista_descr2_filtro = $lista_qry_gera_cad_campos['nome_campo'] . ' ' . $lista_edtfiltro;
            }else {
                if ($lista_qry_gera_cad_campos['tipo_campo'] == 'date') {
                    $lista_valor_trat = substr( $lista_edtfiltro, 6, 4 ) . '-' . substr( $lista_edtfiltro, 3, 2 ) . '-' . substr( $lista_edtfiltro, 0, 2 );
                    $lista_pfiltro = ' ' . $lista_clausula . ' ' . $pref_bd_ini . $lista_cbxfiltro . $pref_bd_fim . ' = \'' . $lista_valor_trat . '\'';
                }else {
                    if ($lista_qry_gera_cad_campos['tipo_campo'] == 'sim_nao') {
                        if (strtoupper( substr( $lista_edtfiltro, 0, 1 ) ) == '') {
                            $vl_pesquisa = 'NULL';
                        }
                        if (strtoupper( substr( $lista_edtfiltro, 0, 1 ) ) == 'S') {
                                $vl_pesquisa = '1';
                        }
                        if (strtoupper( substr( $lista_edtfiltro, 0, 1 ) ) == 'N') {
                                $vl_pesquisa = '0';
                        }
                        $lista_pfiltro = ' ' . $lista_clausula . ' ' . $pref_bd_ini . $lista_cbxfiltro . $pref_bd_fim . ' = ' . $vl_pesquisa;
                    }else {
                        $lista_pfiltro = ' ' . $lista_clausula . ' ' . $pref_bd_ini . $lista_cbxfiltro . $pref_bd_fim . ' like \'%' . $lista_edtfiltro . '%\'';
                    }
                }
                $lista_descr2_filtro = $lista_qry_gera_cad_campos['nome_campo'] . ' ' . $lista_edtfiltro;
            }
            $lista_clausula = 'and';
	}

	if ($lista_pfixo) {
		$lista_fixo_trat = $lista_pfixo;
		$lista_pfiltro .= ' ' . $lista_clausula . ' ' . $lista_pfixo;
	}
        
        
	$lista_filtro_geral = $lista_filtro_geral . ' ' . $lista_pfiltro . ' ';
	$lista_filtro_geral = str_replace( '@vs_id_usuario', $lista_vs_id_usuario, $lista_filtro_geral );
	$lista_filtro_geral = str_replace( '@vs_id_perfil', $lista_vs_id_perfil, $lista_filtro_geral );
	$lista_filtro_geral = str_replace( '@vs_id_empresa', $lista_vs_id_empresa, $lista_filtro_geral );
	$lista_filtro_geral = str_replace( '@vs_dt_hoje', date( 'Y-m-d' ), $lista_filtro_geral );

	if ($lista_sql_filtro) {
            if (strpos( $lista_filtro_geral, 'where' ) === false) {
                    $lista_clausula = 'where';
            }else {
                    $lista_clausula = 'and';
            }
            $lista_filtro_geral = $lista_filtro_geral . ' ' . $lista_clausula . ' ' . $lista_sql_filtro;
	}

	include( 'gera_cad_lista_sql_custom.php' );

	if ($lista_sql_bloqueio) {
            if (strpos( $lista_filtro_geral, 'where' ) === false) {
                    $lista_clausula = 'where';
            }else {
                    $lista_clausula = 'and';
            }
            $lista_filtro_geral = $lista_filtro_geral . ' ' . $lista_clausula . ' ' . $lista_sql_bloqueio;
	}

	$lista_filtro_geral = trata_tags_sql( $lista_filtro_geral );

	if ($lista_cbxordem) {
            $lista_sql_ordem = $lista_cbxordem;
            $lista_sql_ordem = str_replace( 'order by ', 'order by ' . $pref_bd_ini, $lista_sql_ordem );
            $lista_sql_ordem = str_replace( ' desc', '' . $pref_bd_fim . ' desc', $lista_sql_ordem );
            $lista_sql_ordem = str_replace( ' asc', '' . $pref_bd_fim . ' asc', $lista_sql_ordem );

            if (( ( $lista_sql_ordem == 'order by id_pessoa_erp' || $lista_sql_ordem == 'order by id_pessoa_erp asc' ) || $lista_sql_ordem == 'order by ' . $pref_bd_ini . 'id_pessoa_erp' . $pref_bd_fim . ' asc' )) {
                    $lista_sql_ordem = 'order by (' . $pref_bd_ini . 'id_pessoa_erp' . $pref_bd_fim . ' *1) asc';
            }

            if ($lista_sql_ordem == 'order by ' . $pref_bd_ini . 'id_pessoa_erp' . $pref_bd_fim . ' desc') {
                    $lista_sql_ordem = 'order by (' . $pref_bd_ini . 'id_pessoa_erp' . $pref_bd_fim . ' *1) desc';
            }
	}else {
            $lista_sql_ordem = $lista_qry_gera_cad['sql_ordem'];
	}

	if (strtolower( $tipoBanco ) == 'mysql') {
            $sqlListaCadastro = ' '.$lista_filtro_geral . ' '.$lista_sql_ordem.' LIMIT '.$lista_pos_ini.',  '.$lista_maxpag.'';
            $qryListaCadastro = query($sqlListaCadastro);
	}else {
            $sqlListaCadastro = " $lista_filtro_geral . ' ' . $lista_sql_ordem";
            $qryListaCadastro = query($sqlListaCadastro);
	}
        
//ECHO $sqlListaCadastro;DIE;

        $sqlGeraCadCampos = '(select * from is_gera_cad_campos where id_funcao = \'' . $lista_id_funcao . '\' ' . $lista_filtrapainel . ' ' . $lista_filtro_licenca . ( ' ' . $campos_bloqueados . ' ) union all (select * from is_gera_cad_campos_custom where id_funcao = \'' . $lista_id_funcao . '\' ' ) . $lista_filtrapainel . ' ' . $lista_filtro_licenca . ( ' ' . $campos_bloqueados . ' ) order by ordem' ) ;
        $qryGeraCadCampos = query($sqlGeraCadCampos);
        $arrCamposBrowser = array();
        while($arrGeraCadCampos = farray($qryGeraCadCampos)){            
            
            if($arrGeraCadCampos['exibe_browse'] == 1){
                $arrCamposBrowser['exibe_browse'][] = array(
                    'id_campo'            =>   $arrGeraCadCampos['id_campo'],
                    'id_funcao'           =>   $arrGeraCadCampos['id_funcao'],
                    'nome_campo'          =>   $arrGeraCadCampos['nome_campo'],
                    'tipo_campo'          =>   $arrGeraCadCampos['tipo_campo'],
                    'tamanho_campo'       =>   $arrGeraCadCampos['tamanho_campo'],
                    'exibe_browse'        =>   $arrGeraCadCampos['exibe_browse'],
                    'exibe_formulario'    =>   $arrGeraCadCampos['exibe_formulario'],
                    'exibe_filtro'        =>   $arrGeraCadCampos['exibe_filtro'],
                    'ordem'               =>   $arrGeraCadCampos['ordem'],
                    'sql_lupa'            =>   $arrGeraCadCampos['sql_lupa'],
                    'id_campo_lupa'       =>   $arrGeraCadCampos['id_campo_lupa'],
                    'campo_descr_lupa'    =>   $arrGeraCadCampos['campo_descr_lupa'],
                    'id_funcao_lupa'      =>   $arrGeraCadCampos['id_funcao_lupa'],                
                    'nome_grupo'          =>   $arrGeraCadCampos['nome_grupo'],
                    'sn_obrigatorio'      =>   $arrGeraCadCampos['sn_obrigatorio'],
                    'editavel'            =>   $arrGeraCadCampos['editavel'],
                    'quebra_linha'        =>   $arrGeraCadCampos['quebra_linha'],
                    'valor_padrao'        =>   $arrGeraCadCampos['valor_padrao'],
                    'evento_change'       =>   $arrGeraCadCampos['evento_change'],
                    'id_sistema'          =>   $arrGeraCadCampos['id_sistema'],
                    'sn_painel'           =>   $arrGeraCadCampos['sn_painel'],
                    'textohtm'            =>   $arrGeraCadCampos['textohtm'],
                    'sn_soma'             =>   $arrGeraCadCampos['sn_soma'],
                    'id_fase_workflow'    =>   $arrGeraCadCampos['id_fase_workflow'],
                    'filtro_fixo'         =>   $arrGeraCadCampos['filtro_fixo'],
                    'id_licenca'          =>   $arrGeraCadCampos['id_licenca'],
                    'id_fase_workflow'    =>   $arrGeraCadCampos['id_fase_workflow'],
                    'exibe_fases'         =>   $arrGeraCadCampos['exibe_fases'],
                    'edita_fases'         =>   $arrGeraCadCampos['edita_fases'],
                    'tam_relatorio'       =>   $arrGeraCadCampos['tam_relatorio'],
                    'fonte_odbc'          =>   $arrGeraCadCampos['fonte_odbc'],
                    'exibe_titulo'        =>   $arrGeraCadCampos['exibe_titulo'],
                    'max_carac'           =>   $arrGeraCadCampos['max_carac'],
                    'sn_campo_chave'      =>   $arrGeraCadCampos['sn_campo_chave'],
                    'nome_aba'            =>   $arrGeraCadCampos['nome_aba'],
                    'id_aba'              =>   $arrGeraCadCampos['id_aba'],
                    'sn_lupa_bloqueia_incluir' =>   $arrGeraCadCampos['sn_lupa_bloqueia_incluir'],
                    'editavel_inclusao'   =>   $arrGeraCadCampos['editavel_inclusao'],               
                    'editavel_bloq_detalhe' =>   $arrGeraCadCampos['editavel_bloq_detalhe']  
                  );
            }
            
            if($arrGeraCadCampos['exibe_filtro'] == 1){
                $arrCamposBrowser['exibe_filtro'][] = array(
                    'id_campo'            =>   $arrGeraCadCampos['id_campo'],
                    'id_funcao'           =>   $arrGeraCadCampos['id_funcao'],
                    'nome_campo'          =>   $arrGeraCadCampos['nome_campo'],
                    'tipo_campo'          =>   $arrGeraCadCampos['tipo_campo'],
                    'tamanho_campo'       =>   $arrGeraCadCampos['tamanho_campo'],
                    'exibe_browse'        =>   $arrGeraCadCampos['exibe_browse'],
                    'exibe_formulario'    =>   $arrGeraCadCampos['exibe_formulario'],
                    'exibe_filtro'        =>   $arrGeraCadCampos['exibe_filtro'],
                    'ordem'               =>   $arrGeraCadCampos['ordem'],
                    'sql_lupa'            =>   $arrGeraCadCampos['sql_lupa'],
                    'id_campo_lupa'       =>   $arrGeraCadCampos['id_campo_lupa'],
                    'campo_descr_lupa'    =>   $arrGeraCadCampos['campo_descr_lupa'],
                    'id_funcao_lupa'      =>   $arrGeraCadCampos['id_funcao_lupa'],                
                    'nome_grupo'          =>   $arrGeraCadCampos['nome_grupo'],
                    'sn_obrigatorio'      =>   $arrGeraCadCampos['sn_obrigatorio'],
                    'editavel'            =>   $arrGeraCadCampos['editavel'],
                    'quebra_linha'        =>   $arrGeraCadCampos['quebra_linha'],
                    'valor_padrao'        =>   $arrGeraCadCampos['valor_padrao'],
                    'evento_change'       =>   $arrGeraCadCampos['evento_change'],
                    'id_sistema'          =>   $arrGeraCadCampos['id_sistema'],
                    'sn_painel'           =>   $arrGeraCadCampos['sn_painel'],
                    'textohtm'            =>   $arrGeraCadCampos['textohtm'],
                    'sn_soma'             =>   $arrGeraCadCampos['sn_soma'],
                    'id_fase_workflow'    =>   $arrGeraCadCampos['id_fase_workflow'],
                    'filtro_fixo'         =>   $arrGeraCadCampos['filtro_fixo'],
                    'id_licenca'          =>   $arrGeraCadCampos['id_licenca'],
                    'id_fase_workflow'    =>   $arrGeraCadCampos['id_fase_workflow'],
                    'exibe_fases'         =>   $arrGeraCadCampos['exibe_fases'],
                    'edita_fases'         =>   $arrGeraCadCampos['edita_fases'],
                    'tam_relatorio'       =>   $arrGeraCadCampos['tam_relatorio'],
                    'fonte_odbc'          =>   $arrGeraCadCampos['fonte_odbc'],
                    'exibe_titulo'        =>   $arrGeraCadCampos['exibe_titulo'],
                    'max_carac'           =>   $arrGeraCadCampos['max_carac'],
                    'sn_campo_chave'      =>   $arrGeraCadCampos['sn_campo_chave'],
                    'nome_aba'            =>   $arrGeraCadCampos['nome_aba'],
                    'id_aba'              =>   $arrGeraCadCampos['id_aba'],
                    'sn_lupa_bloqueia_incluir' =>   $arrGeraCadCampos['sn_lupa_bloqueia_incluir'],
                    'editavel_inclusao'   =>   $arrGeraCadCampos['editavel_inclusao'],               
                    'editavel_bloq_detalhe' =>   $arrGeraCadCampos['editavel_bloq_detalhe']  
                  );
            }
            
        }
        usort($arrCamposBrowser['exibe_filtro'], function ($a, $b) { 
            return strnatcmp($a['ordem'], $b['ordem']); 
        });

        usort($arrCamposBrowser['exibe_browse'], function ($a, $b) { 
            return strnatcmp($a['ordem'], $b['ordem']); 
        });


	if ($fonte_odbc) {
            $sql_tot = query( str_replace( 'select *', 'select count(*) as total ', $lista_filtro_geral ), 1, $fonte_odbc );
            odbc_fetch_row( $sql_tot );
            $qry_tot = odbc_result( $sql_tot, 'total' );
            $lista_tot = $qry_tot;

            if ($lista_tot < $lista_pos_ini) {
                $lista_pos_ini = 0;
            }
            if (0 < $lista_tot) {
                odbc_fetch_row( $lista_sql_cadastro, $lista_pos_ini );
            }
	}else {
            if (strtolower( $tipoBanco ) == 'mysql') {
                $npos_from = strpos( strtolower( $lista_filtro_geral ), ' from' );
                $sql_tot = substr( $lista_filtro_geral, $npos_from, strlen( $lista_filtro_geral ) - $npos_from );
		$sql_tot = 'select count(*) as total ' . $sql_tot;
		$qry_tot = farray( query( $sql_tot ) );
		$lista_tot = $qry_tot['total'];
                if ($lista_tot < $lista_pos_ini) {
                    $lista_pos_ini = 0;
                }
            }else {
                $lista_tot = numrows( $lista_sql_cadastro );

                if ($lista_tot < $lista_pos_ini) {
                    $lista_pos_ini = 0;
                }
                if (0 < $lista_tot) {
                    dataseek( $lista_sql_cadastro, $lista_pos_ini );
                }
            }
	}


	if ($lista_pdrilldown) { ?>
            <div name="div_programa" id="div_programa">
	<?php } ?>

                
                
        
                
                <style>
                    .table-striped>tbody>tr:nth-child(odd)>td, 
                    .table-striped>tbody>tr:nth-child(odd)>th {
                        background-color: #DAE8F4; 
                        padding: 4px 14px;
                        vertical-align: middle;
                     }
                </style>
                    
    <div class="container-fluid">
    
        <div class="col-md-8 text-right">

            <nav aria-label="Page navigation">
                <ul class="pagination">
                  <li>
                    <a href="#" aria-label="Previous">
                      <span aria-hidden="true">&laquo;</span>
                    </a>
                  </li>
                  <li><a href="#">1</a></li>
                  <li><a href="#">2</a></li>
                  <li><a href="#">3</a></li>
                  <li><a href="#">4</a></li>
                  <li><a href="#">5</a></li>
                  <li>
                    <a href="#" aria-label="Next">
                      <span aria-hidden="true">&raquo;</span>
                    </a>
                  </li>
                </ul>
              </nav>
        </div>
    </div>
                
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover    ">
            <thead>
                <tr><td>#</td>
                    <?php foreach($arrCamposBrowser['exibe_browse'] as $keyHeader => $valHeader) : ?>
                    <th>
                        
                            <?php echo $valHeader['nome_campo']; ?>
                    </th>
                    <?php endforeach; ?>    
                </tr>
            </thead>
            <tbody>
                <?php while ($lista_qry_cadastro = farray( $qryListaCadastro, $fonte_odbc )) : ?>
                <tr><td>EDITAR</td>
                   
                    <?php foreach($arrCamposBrowser['exibe_browse'] as $keyHeader => $valueCampo) : ?>
                        <td>
                            <?php 
                            if (( ( $valueCampo['tipo_campo'] == 'lupa' || $valueCampo['tipo_campo'] == 'combobox' ) || $valueCampo['tipo_campo'] == 'lupa_popup' )) {
                                if (strpos( $valueCampo['sql_lupa'], 'where' ) === false) {
                                    $lista_clausula = 'where';
                                }else {
                                    $lista_clausula = 'and';
                                }
                                $lista_filtro_lupa = $valueCampo['sql_lupa'] . ' ' . $lista_clausula . ' ' . $pref_bd_ini . $valueCampo['id_campo_lupa'] . $pref_bd_fim . ' = \'' . $lista_qry_cadastro[$valueCampo['id_campo']] . '\'';
                                $lista_filtro_lupa = str_replace( '@s', '\'', $lista_filtro_lupa );
                                $lista_filtro_lupa = str_replace( '@vs_cpo_id_funcao', $lista_qry_cadastro['id_funcao'], $lista_filtro_lupa );
                                $lista_lupa_wf = $lista_qry_cadastro['id_workflow'];

                                if (empty( $lista_lupa_wf )) {
                                    $lista_lupa_wf = $lista_qry_cadastro['id_formulario_workflow'];
                                }

                                $lista_filtro_lupa = str_replace( '@vs_cpo_id_workflow', $lista_lupa_wf, $lista_filtro_lupa );
                                $lista_filtro_lupa = str_replace( '@vs_id_sistema', $_SESSION['id_sistema'], $lista_filtro_lupa );
                                $lista_qry_lupa = farray( query( $lista_filtro_lupa ) );
                                echo str_replace( '"', ' ', $lista_qry_lupa[$valueCampo['campo_descr_lupa']] );
    
                            }else {
                                if ($valueCampo['tipo_campo'] == 'date') {
                                    $lista_vl_campo = str_replace( '"', ' ', $lista_qry_cadastro[$valueCampo['id_campo']] );
                                    if ($lista_vl_campo) {
                                        $lista_vl_campo_trat = substr( $lista_vl_campo, 8, 2 ) . '/' . substr( $lista_vl_campo, 5, 2 ) . '/' . substr( $lista_vl_campo, 0, 4 );
                                    }else {
                                        $lista_vl_campo_trat = '';
                                    }

                                    if ($lista_vl_campo_trat == '01/01/1753') {
                                        $lista_vl_campo_trat = '';
                                    }

                                    echo $lista_vl_campo_trat;
                                }else {
                                    if ($value == 'int') {
                                        $lista_vl_campo = str_replace( '"', ' ', $lista_qry_cadastro[$valueCampo['id_campo']] );
                                        if ($lista_vl_campo) {
                                            echo number_format( $lista_vl_campo, 0, ',', '.' );
                                        }
                                    } else {
                                        if (( $value == 'float' || $value == 'real' )) {
                                            $lista_vl_campo = str_replace( '"', ' ', $lista_qry_cadastro[$valueCampo['id_campo']] );
                                            if ($lista_vl_campo) {
                                                echo number_format( $lista_vl_campo, 2, ',', '.' );
                                            }
                                        } else {
                                            if ($value == 'money') {
                                                $lista_vl_campo = str_replace( '"', ' ', $lista_qry_cadastro[$valueCampo['id_campo']] );

                                                if ($lista_vl_campo) {
                                                    echo 'R$' . number_format( $lista_vl_campo, 2, ',', '.' );
                                                }
                                            } else {
                                                if ($value == 'calculado') {
                                                    echo campo_calculado( $valueCampo['id_funcao'], $valueCampo['id_campo'], $lista_qry_cadastro );
                                                } else {
                                                    if ($value == 'memo') {
                                                        echo substr( $lista_qry_cadastro[$valueCampo['id_campo']], 0, 30 ) . '...';
                                                    } else {
                                                        if ($value == 'sim_nao') {
                                                            $lista_vl_campo = $lista_qry_cadastro[$valueCampo['id_campo']];

                                                            if ($lista_vl_campo == '1') {
                                                                echo 'S';
                                                            }
                                                            if ($lista_vl_campo == '0') {
                                                                echo 'N';
                                                            }
                                                        } else {
                                                            echo $lista_qry_cadastro[$valueCampo['id_campo']];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            } ?>
                        </td>
                    <?php endforeach; ?>    
                         
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>            
                
                <?php die;?>
                
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="bordatabela">
            <tr>
                <td bgcolor="#dae8f4" class="tit_tabela" width="15">&nbsp;</td>
                <?php
                if ($lista_plupa) {
                    echo '<td bgcolor="#dae8f4" class="tit_tabela" width="15">&nbsp;</td>';
                }
                if ($lista_qry_gera_cad['nome_tabela'] == 'is_atividade') {
                    echo '<td bgcolor="#dae8f4" class="tit_tabela" width="15">Tp.</td>';
                }
                if ($lista_ppainel == '1') {
                    $lista_filtrapainel = 'and sn_painel = 1';
                }else {
                    $lista_filtrapainel = '';
                }
                
                   
                $sqlGeraCadCampo = '(select * from is_gera_cad_campos where exibe_browse = 1 and id_funcao = \'' . $lista_id_funcao . '\' ' . $lista_filtrapainel . ' ' . $lista_filtro_licenca . ( ' ' . $campos_bloqueados . ' )  union all (select * from is_gera_cad_campos_custom where exibe_browse = 1 and id_funcao = \'' . $lista_id_funcao . '\' ' ) . $lista_filtrapainel . ' ' . $lista_filtro_licenca . ( ' ' . $campos_bloqueados . ' ) order by ordem' );
        	$qryGeraCadCampo = query($sqlGeraCadCampo );
                
                //MONTA CABEÇALHO
                while ($lista_qry_gera_cad_campos = farray( $qryGeraCadCampo )) {
                    echo '<td bgcolor="#dae8f4" class="tit_tabela" >' . $lista_qry_gera_cad_campos['nome_campo'];

                    if ($lista_qry_gera_cad_campos['sn_soma'] == '1') {
                        $sql_soma = substr_replace( $lista_filtro_geral, 'select sum(' . $lista_qry_gera_cad_campos['id_campo'] . ') as TOTAL', 0, 8 );
			$lista_qry_soma = farray( query( $sql_soma ) );

			if ($lista_qry_gera_cad_campos['tipo_campo'] == 'money') {
                            echo '<br>Total:R$' . number_format( $lista_qry_soma['TOTAL'], 2, ',', '.' );
			}else {
                            echo '<br>Total:' . number_format( $lista_qry_soma['TOTAL'], 2, ',', '.' );
			}
                    }
                    echo '</td>';
                }
                if (( ( $lista_pread == '0' && $lista_sn_bloquear_exclusao != '1' ) && $lista_pbloqexcluir != '1' )) {
                    echo '<td bgcolor="#dae8f4" class="tit_tabela" width="15">Excluir</td>';
                }?>
            </tr>
            <?php
            $lista_campo_grupo = $lista_qry_gera_cad['campo_grupo'];
            if ($lista_campo_grupo) {
                $lista_quebra = '';
            }
            // monta grid
            while ($lista_qry_cadastro = farray( $qryListaCadastro, $fonte_odbc )) {

		if ($lista_campo_grupo) {
                    
                    
              
                    if ($lista_quebra != $lista_qry_cadastro[$lista_campo_grupo]) {
                        $lista_quebra = $lista_qry_cadastro[$lista_campo_grupo];
                        
                        
        // foreach($arrCamposBrowser as $keyCampo => $valueCampo){                        
                       
                        if (( ( $valueCampo['tipo_campo'] == 'lupa' || $valueCampo['tipo_campo'] == 'combobox' ) || $valueCampo['tipo_campo'] == 'lupa_popup' )) {
                            if (strpos( $valueCampo['sql_lupa'], 'where' ) === false) {
                                $lista_clausula = 'where';
                            }else {
                                $lista_clausula = 'and';
                            }
                            $lista_filtro_lupa = $valueCampo['sql_lupa'] . ' ' . $lista_clausula . ' ' . $pref_bd_ini . $valueCampo['id_campo_lupa'] . $pref_bd_fim . ' = \'' . $lista_qry_cadastro[$valueCampo['id_campo']] . '\'';
                            $lista_filtro_lupa = str_replace( '@s', '\'', $lista_filtro_lupa );
                            $lista_filtro_lupa = str_replace( '@vs_cpo_id_funcao', $lista_qry_cadastro['id_funcao'], $lista_filtro_lupa );
                            $lista_filtro_lupa = str_replace( '@vs_cpo_id_workflow', $lista_qry_cadastro['id_workflow'], $lista_filtro_lupa );
                            $lista_filtro_lupa = str_replace( '@vs_id_sistema', $_SESSION['id_sistema'], $lista_filtro_lupa );

                            if ($lista_qry_cadastro['id_workflow']) {
                                $lista_filtro_lupa = str_replace( '@vs_cpo_id_workflow', $lista_qry_cadastro['id_workflow'], $lista_filtro_lupa );
                            }else {
                                $lista_filtro_lupa = str_replace( '@vs_cpo_id_workflow', $lista_qry_mestre['id_cad'], $lista_filtro_lupa );
                            }
                            $lista_qry_lupa = farray( query( $lista_filtro_lupa ) );
                            $lista_lupa_descr = ' - <i>' . str_replace( '"', ' ', $lista_qry_lupa[$valueCampo['campo_descr_lupa']] ) . '</i>';
                        }
         //}
                        
                        
			echo '<tr>';
                        echo '<td bgcolor="#dae8f4" width="100%" colspan=20><font face="Verdana" size="1"><b>' . $valueCampo['nome_campo'] . ' : ' . $lista_quebra . $lista_lupa_descr . '</b></font></td>';
                        echo '</tr>';
                    }
		}

		if ($tr_color == '#EBEBEB') {
                    $tr_color = '#FFFFFF';
		}else {
                    $tr_color = '#EBEBEB';
		}

		$lista_tdstyle = '';
		$lista_primeira_coluna = '1';
		$lista_url_edita = '1';
		echo '<tr style="background:' . $tr_color . '" id="linha' . $lista_qry_cadastro[$lista_pchave];
		echo '" onmouseover="this.style.background=' . '\'lightblue\';' . '" onmouseout="this.style.background=' . '\'' . $tr_color . '\';' . '">';
                

         foreach($arrCamposBrowser as $keyCampo => $valueCampo){
		
                        if (empty( $lista_pchave2 )) {
                            $lista_url_open = 'window.open(this.href,\'' . $lista_id_funcao . $lista_qry_cadastro[$lista_pchave] . '\',\'location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=850,height=550,top=' . $posicao_janela . ',left=' . $posicao_janela . '\').focus(); return false;';
                            $lista_abre_funcao = '<a href="' . str_replace( '@s', '\'', str_replace( '@pnumreg', $lista_qry_cadastro[$lista_pchave], $lista_qry_gera_cad['url_alterar'] ) ) . '&psubdet=' . $lista_psubdet . '&pread=' . $lista_pread . '&pnpai=' . $lista_pnpai . '&pfixo=' . $lista_pfixo . '&pdiv=' . $lista_pdiv . '&pusuario_filtro=' . $lista_pusuario_filtro . '&pos_ini=' . $lista_pos_ini . '&cbxfiltro=' . $lista_cbxfiltro . '&edtfiltro=' . $lista_edtfiltro . '&pgetcustom=' . $lista_pgetcustom . '&pbloqincluir=' . $lista_pbloqincluir . '&pbloqexcluir=' . $lista_pbloqexcluir . '&ptitulo=' . $lista_ptitulo . '&cbxordem=' . $lista_cbxordem . '" onclick="' . $lista_url_open . '" ' . $lista_tdstyle . '>';
                        }else {
                            $lista_url_open = 'window.open(this.href,\'' . $lista_id_funcao . $lista_qry_cadastro[$lista_pchave2] . $lista_qry_cadastro[$lista_pchave] . '\',\'location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=850,height=550,top=' . $posicao_janela . ',left=' . $posicao_janela . '\').focus(); return false;';
                            $lista_abre_funcao = '<a href="' . str_replace( '@s', '\'', str_replace( '@pnumreg2', $lista_qry_cadastro[$lista_pchave2], $lista_qry_gera_cad['url_alterar'] ) );
                            $lista_abre_funcao = str_replace( '@pnumreg', $lista_qry_cadastro[$lista_pchave], $lista_abre_funcao ) . '&psubdet=' . $lista_psubdet . '&pread=' . $lista_pread . '&pnpai=' . $lista_pnpai . '&pfixo=' . $lista_pfixo . '&pdiv=' . $lista_pdiv . '&pusuario_filtro=' . $lista_pusuario_filtro . '&cbxfiltro=' . $lista_cbxfiltro . '&edtfiltro=' . $lista_edtfiltro . '&pos_ini=' . $lista_pos_ini . '&pgetcustom=' . $lista_pgetcustom . '&pbloqincluir=' . $lista_pbloqincluir . '&pbloqexcluir=' . $lista_pbloqexcluir . '&ptitulo=' . $lista_ptitulo . '&cbxordem=' . $lista_cbxordem . '" onclick="' . $lista_url_open . '" ' . $lista_tdstyle . '>';
                        }
                        if (( $lista_id_funcao == 'propostas_cad' || $lista_id_funcao == 'nf_pdf' )) {
                            $lista_abre_funcao = '<a href="' . str_replace( '@s', '\'', str_replace( '@pnumreg', $lista_qry_cadastro[$lista_pchave], $lista_qry_gera_cad['url_alterar'] ) ) . '&psubdet=' . $lista_psubdet . '&pread=' . $lista_pread . '&pnpai=' . $lista_pnpai . '&pgetcustom=' . $lista_pgetcustom . '&pbloqincluir=' . $lista_pbloqincluir . '&pbloqexcluir=' . $lista_pbloqexcluir . '&ptitulo=' . $lista_ptitulo . '&pfixo=' . $lista_pfixo . '" ' . $lista_tdstyle . ' target="_blank">';
                        }

                        include( 'gera_cad_cores_custom.php' );

                        if ($lista_qry_gera_cad['nome_tabela'] == 'is_atividade') {
                            if ($lista_primeira_coluna == '1') {
                                $texto_img = '';
                                $lista_color2 = $lista_color;

                                if ($lista_qry_cadastro['id_formulario_workflow']) {
                                    $qry_wf = farray( query( 'select * from is_gera_cad where id_cad=\'' . $lista_qry_cadastro['id_formulario_workflow'] . '\'' ) );
                                    $texto_img = 'Workflow : ' . $qry_wf['titulo'];
                                    $lista_color2 = 'bgcolor="#000000"';
                                }else {
                                    if (substr( $lista_qry_cadastro['id_atividade'], 0, 2 ) == 'WF') {
                                        $texto_img = 'Atividade Gerada por Workflow : ' . $lista_qry_cadastro['id_atividade_pai'];
                                        $lista_color2 = 'bgcolor="#FF9900"';
                                    } else {
                                        $texto_img = $lista_qry_img['nome_tp_atividade'];
                                    }
                                }
                                $lista_qry_img = farray( query( 'select nome_tp_atividade, url_imagem from is_tp_atividade where numreg = \'' . $lista_qry_cadastro['id_tp_atividade'] . '\'' ) );
                                $troca_funcao = '';

                                if ($lista_qry_cadastro['id_tp_atividade'] == 'OPOR') {
                                    $troca_funcao = 'opo_cad_lista';
                                }

                                if ($lista_qry_cadastro['id_tp_atividade'] == 'SAC') {
                                    $troca_funcao = 'sac_cad_lista';
                                }

                                if ($lista_qry_cadastro['id_formulario_workflow']) {
                                    $troca_funcao = $lista_qry_cadastro['id_formulario_workflow'];
                                }
                            }
                        }


                        if ($troca_funcao) {
                            $lista_abre_funcao = str_replace( 'atividades_cad_lista', $troca_funcao, $lista_abre_funcao );
                        }

                        if (( $lista_plupa && $lista_primeira_coluna == '1' )) {
                            echo '<td ' . $lista_color . '>';
                            $lista_qry_plupa = farray( query( '(select * from is_gera_cad_campos where numreg = \'' . $lista_plupa . '\') union all (select * from is_gera_cad_campos_custom where numreg = \'' . $lista_plupa . '\')' ) );
                            $cpid_campo = $lista_qry_plupa['id_campo'];
                            $cpid_lupa = $lista_qry_plupa['id_campo_lupa'];
                            $cpdescr_lupa = $lista_qry_plupa['campo_descr_lupa'];
                            $cpchange = $lista_qry_plupa['evento_change'];
                            $url_plupa = 'javascript:window.opener.document.getElementById(\'edt' . $cpid_campo . '\').value' . ' = \'' . $lista_qry_cadastro[$cpid_lupa] . '\'; ';
                            $url_plupa .= 'window.opener.document.getElementById(\'edtdescr' . $cpid_campo . '\').value' . ' = \'' . $lista_qry_cadastro[$cpdescr_lupa] . '\'; ' . $cpchange . ' window.close()';
                            echo '<a href="#" onclick="' . $url_plupa . '">';
                            echo '<img border="0" width=15 height=15 alt="Selecionar..." src="images/btn_modulo.png">';
                            echo '</a></td>';
                        }


                        if (( ( $lista_primeira_coluna == '1' && $lista_pexibedet == '1' ) && $lista_url_edita == '1' )) {
                            $lista_url_edita = '0';
                            echo '<td width="15" align="center">';
                            echo $lista_abre_funcao;
                            echo '<img border="0" alt="Clique aqui para ver detalhes..." src="images/btn_det.png"></a></td>';
                        }

                        if (( $lista_primeira_coluna == '1' && $lista_qry_gera_cad['nome_tabela'] == 'is_atividade' )) {
                                echo '<td ' . $lista_color2 . '>';
                                echo '<img border="0" width=15 height=15 title="' . $texto_img . '" src="' . $lista_qry_img['url_imagem'] . '">';
                                echo '</td>';
                        }

                        $lista_primeira_coluna = '0';
                    
                    
                        
                        //if (( ( ( ( $lista_qry_gera_cad_campos['tipo_campo'] == 'money' || $lista_qry_gera_cad_campos['tipo_campo'] == 'calculado' ) || $lista_qry_gera_cad_campos['tipo_campo'] == 'int' ) || $lista_qry_gera_cad_campos['tipo_campo'] == 'float' ) || $lista_qry_gera_cad_campos['tipo_campo'] == 'real' )) {
                        if (( ( ( ( $valueCampo['tipo_campo'] == 'money' || $valueCampo['tipo_campo'] == 'calculado' ) || $valueCampo['tipo_campo'] == 'int' ) || $valueCampo['tipo_campo'] == 'float' ) || $valueCampo['tipo_campo'] == 'real' )) {
                            echo '<td align="right" ' . $lista_color . '>';
                        } else {
                            echo '<td align="left" ' . $lista_color . '>';
                        }
                    
                
                          
                        
                                                    };  
                        if ($lista_pexibedet == '1') {
                            echo '</a>';
                        }
                        echo '</font></td>';
                    //}
                              

		if (( ( $lista_pread == '0' && $lista_sn_bloquear_exclusao != '1' ) && $lista_pbloqexcluir != '1' )) {
                    echo '<td ' . $lista_color . 'width="15" align="center">';
                    echo '<a href="javascript:if(confirm(';
                    echo '\'Confirma exclusão deste registro ?\')) { ';
                    $url_exc = str_replace( '@sf', '\'', str_replace( '@pnumreg2', $lista_qry_cadastro[$lista_pchave2], $lista_qry_gera_cad['url_excluir'] ) );
                    $url_exc = str_replace( '@s', '\'', $url_exc ) . ' ' . $lista_url_filtro;
                    $url_exc = str_replace( '@pnumreg', $lista_qry_cadastro[$lista_pchave], $url_exc ) . ' ' . $lista_url_filtro;
                    echo $url_exc;
                    echo ' }"><img border="0" alt="Clique aqui para excluir..." src="images/btn_del.png"></a></td>';
		}

		echo '</tr>';
		$lista_contador = $lista_contador + 1;

		if ($lista_maxpag <= $lista_contador) {
			$lista_contador = 0;
			break;
		}
            } ?>
	</table>
           
        <?php
	$_SESSION['ip_usuario_development'] = '1';

	if (( $_SESSION['ip_consultor'] == '1'  )) {
		if (( ( ( ( ( ( ( ( ( ( ( ( ( ( ( $lista_id_funcao == 'modulos_cad_lista' || $lista_id_funcao == 'funcoes_cad_lista' ) || $lista_id_funcao == 'cadastros_cad_lista' ) || $lista_id_funcao == 'cadastros_cad_lista_custom' ) || $lista_id_funcao == 'campos_cad_lista_custom' ) || $lista_id_funcao == 'campos_cad_lista' ) || $lista_id_funcao == 'gera_cad_sub_lista' ) || $lista_id_funcao == 'graf_cad_lista' ) || $lista_id_funcao == 'tool_rec_tabela' ) || $lista_id_funcao == 'gera_bd_tool' ) || $lista_id_funcao == 'gera_copia_tool' ) || $lista_id_funcao == 'botoes_cad_lista' ) || $lista_id_funcao == 'gera_tool_extract' ) || $lista_id_funcao == 'extract_cad' ) || $lista_id_funcao == 'botoes_cad' ) || $lista_id_funcao == 'recria_ordem_campos' )) {
		} else {
			$lista_qrycad = farray( query( 'select numreg from is_gera_cad where id_cad = \'' . $lista_id_funcao . '\'' ) );
			$lista_cad_url = 'gera_cad_detalhe.php?pfuncao=cadastros_cad_lista_custom&pbloqincluir=1&pbloqexcluir=1&pread=1&pnumreg=' . $lista_qrycad['numreg'];
			$lista_url_open = 'window.open(this.href,\'' . $lista_id_funcao . $lista_qrycad['numreg'] . 'conf\',\'location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=850,height=550,top=' . $posicao_janela . ',left=' . $posicao_janela . '\').focus(); return false;';
			echo '<br><center><a href="' . $lista_cad_url . '" onclick="' . $lista_url_open . '" ' . $lista_tdstyle . '>FOLLOW CRM Development - (Campos Complementares)</a></center>';
		}
	}


	if ($_SESSION['ip_desenvolvedor'] == '1') {
		$lista_qrycad = farray( query( 'select numreg from is_gera_cad where id_cad = \'' . $lista_id_funcao . '\'' ) );
		$lista_cad_url = 'gera_cad_detalhe.php?pfuncao=cadastros_cad_lista&pnumreg=' . $lista_qrycad['numreg'];
		$lista_url_open = 'window.open(this.href,\'' . $lista_id_funcao . $lista_qrycad['numreg'] . 'conf\',\'location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=850,height=550,top=' . $posicao_janela . ',left=' . $posicao_janela . '\').focus(); return false;';
		echo '<br><center><a href="' . $lista_cad_url . '" onclick="' . $lista_url_open . '" ' . $lista_tdstyle . '><b>FOLLOW CRM - Configurar Tela</b></a></center>';
	}


	if ($psubdet) {
		echo '<div style="height:1px; width:100%; background-color:#E0E0E0; margin-top:2px; margin-bottom:2px;"></div>';
	}

?>