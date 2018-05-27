<?php
//==================================================================================================
// Este programa permite incluir clausulas where no sql do browse para bloqueios de acesso por exemplo
// Voc� pode utilizar por exemplo o conte�do das seguinte vari�veis :
// - $_SESSION["id_usuario"] e $_SESSION["id_perfil"] para customizar direitos de acesso
// - $id_funcao para saber qual � o cadastro corrente
// - $lista_qry_gera_cad["nome_tabela"] para saber qual a tabela
// Exemplo de C�digo
//==================================================================================================
require('classes/class.ControleAcesso.php');
$ControleAcesso = new ControleAcesso($_SESSION['id_usuario'], $lista_qry_gera_cad['id_cad']);
/* Bloqueio de Pedidos e Orçamentos */
if($ControleAcesso->getIdCadastro() == 'pedido' || $ControleAcesso->getIdCadastro() == 'orcamento'){
    if($_SESSION['sn_bloquear_leitura'] != 0){
        if($ControleAcesso->AplicaFiltroBloqueio()){
            $CondicaoFiltroVendedor = $ControleAcesso->GeraSqlBloqueio('id_usuario_cad','');
            $CondicaoFiltroGerenteContasRepresentante = $ControleAcesso->GeraSqlBloqueio('id_representante_pessoa','');

            $lista_sql_bloqueio = '';

            if($CondicaoFiltroVendedor != '' || $CondicaoFiltroGerenteContasRepresentante != ''){
                $lista_sql_bloqueio .= '(';
            }

            if($CondicaoFiltroVendedor != '' && $CondicaoFiltroGerenteContasRepresentante != ''){
                $lista_sql_bloqueio .= $CondicaoFiltroVendedor.' OR '.$CondicaoFiltroGerenteContasRepresentante;
            }
            elseif($CondicaoFiltroVendedor != ''){
                $lista_sql_bloqueio .= $CondicaoFiltroVendedor;
            }
            elseif($CondicaoFiltroGerenteContasRepresentante != ''){
                $lista_sql_bloqueio .= $CondicaoFiltroGerenteContasRepresentante;
            }
            if($CondicaoFiltroVendedor != '' || $CondicaoFiltroGerenteContasRepresentante != ''){
                $lista_sql_bloqueio .= ')';
            }
        }
    }
}
if($lista_qry_gera_cad['nome_tabela'] == 'is_atividade'){
    if($_SESSION['sn_bloquear_leitura'] != 0){
        if($ControleAcesso->AplicaFiltroBloqueio()){
            $BloqueioIdUsuarioResp = $ControleAcesso->GeraSqlBloqueio('id_usuario_resp','OR');
			//$BloqueioIdUsuarioResp .= "AND ((id_usuario_cad = '".$lista_vs_id_usuario."') OR (id_usuario_cad = 140 AND id_usuario_resp = '".$lista_vs_id_usuario."'))";

			
            $lista_sql_bloqueio = "(id_usuario_cad = '".$lista_vs_id_usuario."'";
            if($BloqueioIdUsuarioResp != ''){
                $lista_sql_bloqueio .= $BloqueioIdUsuarioResp;
            }
            $lista_sql_bloqueio .= ')';
        }
    }
}
if($lista_qry_gera_cad['nome_tabela'] == 'is_arquivo'){
    if($_SESSION['sn_bloquear_leitura'] != 0){
        if($ControleAcesso->AplicaFiltroBloqueio()){
            $BloqueioIdVendedorPadrao = $ControleAcesso->GeraSqlBloqueio('id_usuario_resp', '');
            $lista_sql_bloqueio = '('.$BloqueioIdVendedorPadrao.')';
        }
    }
}

if($lista_qry_gera_cad['nome_tabela'] == 'is_pessoa'){
    if($_SESSION['sn_bloquear_leitura'] != 0){
        if($ControleAcesso->AplicaFiltroBloqueio()){
            $BloqueioIdRepresentantePadrao = $ControleAcesso->GeraSqlBloqueio('id_representante_padrao','');
            $BloqueioIdVendedorPadrao = $ControleAcesso->GeraSqlBloqueio('id_vendedor_padrao','OR');
            $BloqueioIdOperadorPadrao = $ControleAcesso->GeraSqlBloqueio('id_operador_padrao','OR');
            $BloqueioIdOperadorPadrao .= (' or numreg = 143983');

            $lista_sql_bloqueio = '('.$BloqueioIdRepresentantePadrao.$BloqueioIdVendedorPadrao.$BloqueioIdOperadorPadrao.')';
//            echo $lista_sql_bloqueio;die;
        }
    }
    /*
     * Bloqueando botão de + Incluir
     */
    $SnIntegradoERP = (GetParam('INT_ERP') == 1)?true:false;
    if(!$SnIntegradoERP){
        $lista_pbloqincluir = 0;
    }
}

if($lista_qry_gera_cad['nome_tabela'] == 'is_contato'){
    if($_SESSION['sn_bloquear_leitura'] != 0){
        if($ControleAcesso->AplicaFiltroBloqueio()){
            $Bloqueio = $ControleAcesso->GeraSqlBloqueio('id_vendedor','');
            $lista_sql_bloqueio = '('.$Bloqueio.')';
        }
    }
}

if($lista_qry_gera_cad['nome_tabela'] == 'is_oportunidade'){
    if($_SESSION['sn_bloquear_leitura'] != 0){
        if($ControleAcesso->AplicaFiltroBloqueio()){
            
             $BloqueioIdUsuarioResp = $ControleAcesso->GeraSqlBloqueio('id_usuario_resp','');
            $BloqueioIdUsuarioGestor = $ControleAcesso->GeraSqlBloqueio('id_usuario_gestor','OR');
            $lista_sql_bloqueio = '('.$BloqueioIdUsuarioResp.$BloqueioIdUsuarioGestor.')';
//            
//            $BloqueioIdUsuarioResp = $ControleAcesso->GeraSqlBloqueio('id_usuario_resp',' numreg in(151)');
//            $lista_sql_bloqueio = '('.$BloqueioIdUsuarioResp.$BloqueioIdUsuarioGestor.')';
        }
    }
}

if($lista_qry_gera_cad['id_cad'] == 'chamado_portal'){
    $lista_sql_bloqueio = '(id_produto IN(SELECT id_produto FROM is_portal_grupo_usuario_produto WHERE id_grupo_usuario IN(SELECT id_grupo_usuario FROM is_portal_usuario_x_grupo WHERE id_usuario = '.$_SESSION['id_usuario'].')))';
}

if(($lista_qry_gera_cad['nome_tabela'] == 'is_produto') || ($lista_qry_gera_cad['nome_tabela'] == 'is_familia_comercial') || ($lista_qry_gera_cad['nome_tabela'] == 'is_oportunidade') || ($lista_qry_gera_cad['nome_tabela'] == 'is_tab_preco_valor')) {
    $a_usuario_logado = farray(query("select * from is_usuario where numreg = '".$_SESSION['id_usuario']."'"));
    if($a_usuario_logado['sn_bloquear_familias'] == 1){
        if($lista_qry_gera_cad['nome_tabela'] == 'is_produto') {
            $lista_sql_bloqueio = "(id_familia_comercial in (select id_familia_comercial from is_usuarios_acesso_familias where id_usuario_mestre = '".$_SESSION['id_usuario']."'))";
        }
        if($lista_qry_gera_cad['nome_tabela'] == 'is_familia_comercial') {
            $lista_sql_bloqueio = "(numreg in (select id_familia_comercial from is_usuarios_acesso_familias where id_usuario_mestre = '".$_SESSION['id_usuario']."'))";
        }
        if($lista_qry_gera_cad['nome_tabela'] == 'is_oportunidade') {
            $lista_sql_bloqueio = "(numreg in (select t2.id_oportunidade from is_usuarios_acesso_familias t1, is_opor_produto t2, is_produto t3 where t1.id_familia_comercial = t3.id_familia_comercial and t2.id_produto = t3.numreg and t1.id_usuario_mestre = '".$_SESSION['id_usuario']."'))";
        }
        if($lista_qry_gera_cad['nome_tabela'] == 'is_tab_preco_valor') {
            $lista_sql_bloqueio = "(id_produto in (select t2.numreg from is_usuarios_acesso_familias t1, is_produto t2 where t1.id_familia_comercial = t2.id_familia_comercial and t1.id_usuario_mestre = '".$_SESSION['id_usuario']."'))";
        }
    }
}