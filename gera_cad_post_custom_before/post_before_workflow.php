<?php

// WORKFLOW - ATULIZACAO DE CONTEUDO DE CAMPOS E GERACAO DE MENU
if(($id_funcao == 'workflow') && ($opc != 'excluir')){
    $id_cpo_cad = $_POST["edtid_cad"];
    $_POST["edturl_excluir"] = "javascript:gera_cad_excluir(@sfgera_cad_post.php?pfuncao=".$id_cpo_cad."&pnumreg=@pnumreg&popc=excluir@sf);";
    $_POST["edturl_alterar"] = "gera_cad_detalhe.php?pfuncao=".$id_cpo_cad."&pnumreg=@pnumreg";
    $_POST["edtsql_filtro"] = "select * from is_atividade where id_formulario_workflow = @s".$id_cpo_cad."@s";
    $_POST["sql_ordem"] = "order by id_situacao asc, dt_prev_fim desc";
    $_POST["edtnome_tabela"] = 'is_atividade';
    $_POST["edtsn_bloqueia_botao_copia"] = '1';
    $_POST["edtsn_maximizado"] = '1';

    $sql_atualiza = "insert into is_funcoes(id_modulo,id_funcao,nome_funcao,nome_grupo,url_programa,ordem,url_imagem,id_sistema) values ('" . $_POST["edtid_modulo"] . "','" . $id_cpo_cad . "','" . TextoBD($tipoBanco, nl2br($_POST["edttitulo"])) . "','Workflow','<a href= javascript:exibe_programa(@sfgera_cad_lista.php?pfuncao=" . $id_cpo_cad . "@sf); >','1','images/icone_estrutura.png','" . $_SESSION["id_sistema"] . "')";
    query("delete from is_funcoes where id_funcao = '".$id_cpo_cad."'");
    query("delete from is_gera_cad_campos where id_funcao = '".$id_cpo_cad."' and id_campo in ('numreg','assunto','id_usuario_resp','id_fase_workflow','dt_inicio','hr_inicio','dt_prev_fim','hr_prev_fim','id_situacao','dt_real_fim','hr_real_fim','id_tp_atividade')");
    $_sql_add_cpo_wf = "insert into is_gera_cad_campos(
           id_funcao,nome_aba,id_aba,id_fase_workflow,id_campo,nome_campo,tipo_campo,tamanho_campo,exibe_browse,exibe_formulario,exibe_filtro,ordem,quebra_linha,editavel,sql_lupa,id_campo_lupa,campo_descr_lupa,id_sistema,valor_padrao
           )  values";
    query($_sql_add_cpo_wf."('".$id_cpo_cad."','01.Principal','1',NULL,'numreg','Nє Protocolo','int','10','1','0','1','-10000','1','0','','','','CRM','')");
    query($_sql_add_cpo_wf."('".$id_cpo_cad."','01.Principal','1',NULL,'assunto','Assunto','varchar','90','0','0','1','-9995','1','0','','','','CRM','Workflow : ".$_POST["edttitulo"]."')");
    query($_sql_add_cpo_wf."('".$id_cpo_cad."','01.Principal','1',NULL,'id_tp_atividade','Tipo de Atividade','int','10','0','0','1','-9992','1','0','','','','CRM','7')");
    query($_sql_add_cpo_wf."('".$id_cpo_cad."','01.Principal','1',NULL,'id_usuario_resp','Responsбvel Atual','combobox','-9990','1','0','1','-10000','1','0','select * from is_usuario','numreg','nome_usuario','CRM','@vs_id_usuario')");
    query($_sql_add_cpo_wf."('".$id_cpo_cad."','01.Principal','1',NULL,'id_fase_workflow','Fase Atual','combobox','10','1','0','1','-9980','1','0','select * from is_workflow_fase','id_fase','nome_fase','CRM','')");
    query($_sql_add_cpo_wf."('".$id_cpo_cad."','01.Principal','1',NULL,'dt_inicio','Dt.Inнcio','date','10','1','0','1','-9970','1','0','','','','CRM','@vs_dt_hoje')");
    query($_sql_add_cpo_wf."('".$id_cpo_cad."','01.Principal','1',NULL,'hr_inicio','Hr.Inнcio','varchar','5','1','0','1','-9960','1','0','','','','CRM','@vs_hr_hm')");
    query($_sql_add_cpo_wf."('".$id_cpo_cad."','01.Principal','1',NULL,'dt_prev_fim','Dt.Prazo','date','10','1','0','1','-9950','1','0','','','','CRM','@vs_dt_hoje')");
    query($_sql_add_cpo_wf."('".$id_cpo_cad."','01.Principal','1',NULL,'hr_prev_fim','Hr.Prazo','varchar','5','1','0','1','-9940','1','0','','','','CRM','@vs_hr_hm')");
    query($_sql_add_cpo_wf."('".$id_cpo_cad."','01.Principal','1',NULL,'id_situacao','Situaзгo','combobox','10','0','0','1','-9930','1','0','select * from is_situacao','numreg','nome_situacao','CRM','1')");
    query($_sql_add_cpo_wf."('".$id_cpo_cad."','01.Principal','1',NULL,'dt_real_fim','Dt.Fim','date','10','0','0','1','-9920','1','0','','','','CRM','')");
    query($_sql_add_cpo_wf."('".$id_cpo_cad."','01.Principal','1',NULL,'hr_real_fim','Hr.Fim','varchar','5','0','0','1','-9910','1','0','','','','CRM','')");
    query($sql_atualiza);
}
?>