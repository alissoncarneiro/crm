CREATE TABLE  [dbo].[is_candidato] (
  [numreg] int IDENTITY(1, 1) NOT NULL,
  [nome_candidato] varchar(70)  NULL,
  [id_cargo_pretendido] varchar(50)  NULL,
  [pretensao_salarial] decimal(15,2)  NULL,
  [cidade] varchar(100)  NULL,
  [estado] varchar(2)  NULL,
  [nome_status] varchar(25)  NULL,
  [id_empresa_enc] varchar(30)  NULL,
  [texto_curriculo] text,
  [cpf_candidato] varchar(20)  NULL,
  [rg_candidato] varchar(20)  NULL,
  [tel1] varchar(20)  NULL,
  [email_candidato] varchar(30)  NULL,
  [salario_candidato] decimal(15,2)  NULL,
  [cargo_candidato] int  NULL,
  [dt_admissao] datetime  NULL,
  [dt_demissao] datetime  NULL,
  [curriculum] varchar(20)  NULL,
  [sn_ativo] int NULL,
  PRIMARY KEY CLUSTERED ([numreg])
)
GO
delete from is_gera_cad where id_cad = 'candidato_cad'
GO
delete from is_gera_cad_sub where id_funcao_mestre = 'candidato_cad'
GO
delete from is_gera_cad_campos where id_funcao = 'candidato_cad'
GO
delete from is_gera_cad_botoes where id_funcao = 'candidato_cad'
GO
delete from is_workflow_fase where id_workflow = 'candidato_cad'
GO
delete from is_funcoes where id_funcao = 'candidato_cad'
GO
INSERT INTO is_gera_cad ( id_funcao,id_cad,titulo,url_incluir,url_excluir,url_alterar,sql_filtro,sql_ordem,campo_grupo,nome_tabela,validacoes_js,dt_cadastro,hr_cadastro,id_usuario_cad,dt_alteracao,hr_alteracao,id_usuario_alt,id_sistema,textohtm,id_fase_workflow,id_licenca,tipo,id_arquivo,id_modulo,obs,arquivo,ajuda,id_tipo_workflow,fonte_odbc,tam_relatorio,sn_filtro,sn_maximizado,sn_bloqueia_botao_salvar_inc,sn_bloqueia_botao_copia ) values ( 'candidato','candidato_cad','Candidatos','a','javascript:gera_cad_excluir(''gera_cad_post.php?pfuncao=candidato_cad&pnumreg=@pnumreg&popc=excluir'');','gera_cad_detalhe.php?pfuncao=candidato_cad&pnumreg=@pnumreg','select * from is_candidato',NULL,NULL,'is_candidato',NULL,'20070920','09:21:49','admin','20070920','09:24:36','admin','CRM',NULL,NULL,'PADRAO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL )
GO
INSERT INTO is_gera_cad_campos ( id_funcao,id_campo,nome_campo,tipo_campo,tamanho_campo,exibe_browse,exibe_formulario,exibe_filtro,ordem,sql_lupa,id_campo_lupa,campo_descr_lupa,id_funcao_lupa,nome_grupo,sn_obrigatorio,editavel,quebra_linha,valor_padrao,evento_change,id_sistema,sn_painel,textohtm,sn_soma,id_fase_workflow,filtro_fixo,id_licenca,exibe_fases,edita_fases,tam_relatorio,fonte_odbc,ajuda,exibe_titulo,max_carac,sn_campo_chave,nome_aba,id_aba,sn_lupa_bloqueia_incluir,editavel_inclusao,editavel_bloq_detalhe ) values ( 'candidato_cad','nome_candidato','Nome Candidato','varchar',30,'1','1','1',10,NULL,NULL,NULL,NULL,NULL,'1','1','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0',NULL,NULL,'01.Principal',1,NULL,NULL,NULL )
GO
INSERT INTO is_gera_cad_campos ( id_funcao,id_campo,nome_campo,tipo_campo,tamanho_campo,exibe_browse,exibe_formulario,exibe_filtro,ordem,sql_lupa,id_campo_lupa,campo_descr_lupa,id_funcao_lupa,nome_grupo,sn_obrigatorio,editavel,quebra_linha,valor_padrao,evento_change,id_sistema,sn_painel,textohtm,sn_soma,id_fase_workflow,filtro_fixo,id_licenca,exibe_fases,edita_fases,tam_relatorio,fonte_odbc,ajuda,exibe_titulo,max_carac,sn_campo_chave,nome_aba,id_aba,sn_lupa_bloqueia_incluir,editavel_inclusao,editavel_bloq_detalhe ) values ( 'candidato_cad','cpf_candidato','CPF','varchar',20,'1','1','1',20,NULL,NULL,NULL,NULL,NULL,'1','1','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0',NULL,NULL,'01.Principal',1,NULL,NULL,NULL )
GO
INSERT INTO is_gera_cad_campos ( id_funcao,id_campo,nome_campo,tipo_campo,tamanho_campo,exibe_browse,exibe_formulario,exibe_filtro,ordem,sql_lupa,id_campo_lupa,campo_descr_lupa,id_funcao_lupa,nome_grupo,sn_obrigatorio,editavel,quebra_linha,valor_padrao,evento_change,id_sistema,sn_painel,textohtm,sn_soma,id_fase_workflow,filtro_fixo,id_licenca,exibe_fases,edita_fases,tam_relatorio,fonte_odbc,ajuda,exibe_titulo,max_carac,sn_campo_chave,nome_aba,id_aba,sn_lupa_bloqueia_incluir,editavel_inclusao,editavel_bloq_detalhe ) values ( 'candidato_cad','rg_candidato','RG','varchar',20,'1','1','1',30,NULL,NULL,NULL,NULL,NULL,'1','1','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'01.Principal',1,NULL,NULL,NULL )
GO
INSERT INTO is_gera_cad_campos ( id_funcao,id_campo,nome_campo,tipo_campo,tamanho_campo,exibe_browse,exibe_formulario,exibe_filtro,ordem,sql_lupa,id_campo_lupa,campo_descr_lupa,id_funcao_lupa,nome_grupo,sn_obrigatorio,editavel,quebra_linha,valor_padrao,evento_change,id_sistema,sn_painel,textohtm,sn_soma,id_fase_workflow,filtro_fixo,id_licenca,exibe_fases,edita_fases,tam_relatorio,fonte_odbc,ajuda,exibe_titulo,max_carac,sn_campo_chave,nome_aba,id_aba,sn_lupa_bloqueia_incluir,editavel_inclusao,editavel_bloq_detalhe ) values ( 'candidato_cad','tel1','Telefone','varchar',20,'1','1','1',40,NULL,NULL,NULL,NULL,NULL,'0','1','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'15',NULL,'01.Principal',1,NULL,NULL,NULL )
GO
INSERT INTO is_gera_cad_campos ( id_funcao,id_campo,nome_campo,tipo_campo,tamanho_campo,exibe_browse,exibe_formulario,exibe_filtro,ordem,sql_lupa,id_campo_lupa,campo_descr_lupa,id_funcao_lupa,nome_grupo,sn_obrigatorio,editavel,quebra_linha,valor_padrao,evento_change,id_sistema,sn_painel,textohtm,sn_soma,id_fase_workflow,filtro_fixo,id_licenca,exibe_fases,edita_fases,tam_relatorio,fonte_odbc,ajuda,exibe_titulo,max_carac,sn_campo_chave,nome_aba,id_aba,sn_lupa_bloqueia_incluir,editavel_inclusao,editavel_bloq_detalhe ) values ( 'candidato_cad','email_candidato','E-mail','varchar',30,'1','1','1',50,NULL,NULL,NULL,NULL,NULL,'0','1','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'01.Principal',1,NULL,NULL,NULL )
GO
INSERT INTO is_gera_cad_campos ( id_funcao,id_campo,nome_campo,tipo_campo,tamanho_campo,exibe_browse,exibe_formulario,exibe_filtro,ordem,sql_lupa,id_campo_lupa,campo_descr_lupa,id_funcao_lupa,nome_grupo,sn_obrigatorio,editavel,quebra_linha,valor_padrao,evento_change,id_sistema,sn_painel,textohtm,sn_soma,id_fase_workflow,filtro_fixo,id_licenca,exibe_fases,edita_fases,tam_relatorio,fonte_odbc,ajuda,exibe_titulo,max_carac,sn_campo_chave,nome_aba,id_aba,sn_lupa_bloqueia_incluir,editavel_inclusao,editavel_bloq_detalhe ) values ( 'candidato_cad','salario_candidato','Salário (R$)','money',20,'1','1','1',60,NULL,NULL,NULL,NULL,NULL,'0','1','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'01.Principal',1,NULL,NULL,NULL )
GO
INSERT INTO is_gera_cad_campos ( id_funcao,id_campo,nome_campo,tipo_campo,tamanho_campo,exibe_browse,exibe_formulario,exibe_filtro,ordem,sql_lupa,id_campo_lupa,campo_descr_lupa,id_funcao_lupa,nome_grupo,sn_obrigatorio,editavel,quebra_linha,valor_padrao,evento_change,id_sistema,sn_painel,textohtm,sn_soma,id_fase_workflow,filtro_fixo,id_licenca,exibe_fases,edita_fases,tam_relatorio,fonte_odbc,ajuda,exibe_titulo,max_carac,sn_campo_chave,nome_aba,id_aba,sn_lupa_bloqueia_incluir,editavel_inclusao,editavel_bloq_detalhe ) values ( 'candidato_cad','cargo_candidato','Cargo','combobox',20,'1','1','1',70,'select * from is_cargo','numreg','nome_cargo','cargos_cad_lista',NULL,'1','1','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'01.Principal',1,NULL,NULL,NULL )
GO
INSERT INTO is_gera_cad_campos ( id_funcao,id_campo,nome_campo,tipo_campo,tamanho_campo,exibe_browse,exibe_formulario,exibe_filtro,ordem,sql_lupa,id_campo_lupa,campo_descr_lupa,id_funcao_lupa,nome_grupo,sn_obrigatorio,editavel,quebra_linha,valor_padrao,evento_change,id_sistema,sn_painel,textohtm,sn_soma,id_fase_workflow,filtro_fixo,id_licenca,exibe_fases,edita_fases,tam_relatorio,fonte_odbc,ajuda,exibe_titulo,max_carac,sn_campo_chave,nome_aba,id_aba,sn_lupa_bloqueia_incluir,editavel_inclusao,editavel_bloq_detalhe ) values ( 'candidato_cad','dt_admissao','Data Admissão','date',10,'1','1','1',80,NULL,NULL,NULL,NULL,NULL,'1','1','0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'01.Principal',1,NULL,NULL,NULL )
GO
INSERT INTO is_gera_cad_campos ( id_funcao,id_campo,nome_campo,tipo_campo,tamanho_campo,exibe_browse,exibe_formulario,exibe_filtro,ordem,sql_lupa,id_campo_lupa,campo_descr_lupa,id_funcao_lupa,nome_grupo,sn_obrigatorio,editavel,quebra_linha,valor_padrao,evento_change,id_sistema,sn_painel,textohtm,sn_soma,id_fase_workflow,filtro_fixo,id_licenca,exibe_fases,edita_fases,tam_relatorio,fonte_odbc,ajuda,exibe_titulo,max_carac,sn_campo_chave,nome_aba,id_aba,sn_lupa_bloqueia_incluir,editavel_inclusao,editavel_bloq_detalhe ) values ( 'candidato_cad','dt_demissao','Data Demissão','date',10,'1','1','1',90,NULL,NULL,NULL,NULL,NULL,'0','1','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'01.Principal',1,NULL,NULL,NULL )
GO
INSERT INTO is_gera_cad_campos ( id_funcao,id_campo,nome_campo,tipo_campo,tamanho_campo,exibe_browse,exibe_formulario,exibe_filtro,ordem,sql_lupa,id_campo_lupa,campo_descr_lupa,id_funcao_lupa,nome_grupo,sn_obrigatorio,editavel,quebra_linha,valor_padrao,evento_change,id_sistema,sn_painel,textohtm,sn_soma,id_fase_workflow,filtro_fixo,id_licenca,exibe_fases,edita_fases,tam_relatorio,fonte_odbc,ajuda,exibe_titulo,max_carac,sn_campo_chave,nome_aba,id_aba,sn_lupa_bloqueia_incluir,editavel_inclusao,editavel_bloq_detalhe ) values ( 'candidato_cad','curriculum','Udpload C.V.','arquivo',20,'0','1','0',100,NULL,NULL,NULL,NULL,NULL,'0','1','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'01.Principal',1,NULL,NULL,NULL )
GO
INSERT INTO is_gera_cad_campos ( id_funcao,id_campo,nome_campo,tipo_campo,tamanho_campo,exibe_browse,exibe_formulario,exibe_filtro,ordem,sql_lupa,id_campo_lupa,campo_descr_lupa,id_funcao_lupa,nome_grupo,sn_obrigatorio,editavel,quebra_linha,valor_padrao,evento_change,id_sistema,sn_painel,textohtm,sn_soma,id_fase_workflow,filtro_fixo,id_licenca,exibe_fases,edita_fases,tam_relatorio,fonte_odbc,ajuda,exibe_titulo,max_carac,sn_campo_chave,nome_aba,id_aba,sn_lupa_bloqueia_incluir,editavel_inclusao,editavel_bloq_detalhe ) values ( 'candidato_cad','sn_ativo','Ativo','sim_nao',3,'1','1','1',110,NULL,NULL,NULL,NULL,NULL,'1','1','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'01.Principal',1,NULL,NULL,NULL )
GO
INSERT INTO is_funcoes ( id_modulo,id_funcao,nome_funcao,url_imagem,url_programa,nome_grupo,ordem,id_sistema,id_licenca,url_imagem_menu ) values ( '1','candidato_cad','Candidatos','images/icone_pessoas.png','<a href= javascript:exibe_programa(''gera_cad_lista.php?pfuncao=candidato_cad''); >','Contas',70,'INATIVO',NULL,NULL )
GO

