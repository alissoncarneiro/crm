<?php
/*
 * carga_padrao1x.php
 * Autor: Alex
 * 27/01/2011 14:59:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
set_time_limit(500);
/*
 * Básicas
 */
include_once("is_canal_venda.php");
include_once("is_cond_pagto.php");
include_once("is_estabelecimento.php");
include_once("is_estados_uf.php");
include_once("is_grupo_cliente.php");
include_once("is_transportadora.php");

/*
 * Impostos
 */
include_once("is_cfop.php");
include_once("is_icms_produto_diferenciado.php");
include_once("is_icms_uf_excecoes.php");

/*
 * Produtos, Família
 */
include_once("is_familia.php");
include_once("is_familia_comercial.php");
include_once("is_unid_medida.php");
include_once("is_produto.php");
include_once("is_embalagem.php");
include_once("is_produto_embalagem.php");
include_once("is_produto_estabelecimento.php");
include_once("is_produto_pessoa.php");
include_once("is_produto_uf.php");
/*
 * Preços
 */
include_once("is_moeda.php");
include_once("is_tab_preco.php");
include_once("is_tab_preco_valor.php");
include_once("is_cotacao.php");
?>