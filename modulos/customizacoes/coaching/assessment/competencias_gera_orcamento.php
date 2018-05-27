<?php
    require('../../../../conecta.php');

    $programa = $_POST['programa'];
    $coachee= $_POST['coachee'];
    session_start();
    $sqlVisualizarCompetencias = "
                           select
                            fechamento.numreg as programa,
                            fechamento.fk_competencias_coach as id_pessoa,
                            fechamento.fk_competencias_coachee,
                            fechamento.fk_grupo_pergunta as id_erp

                                from tb_competencias_coach_coachee_fechamento as fechamento

                                  inner join tb_competencias_produtos as produto
                                  on produto.fk_grupo_pergunta = fechamento.fk_grupo_pergunta
                            where fk_programa_coach_coachee = $programa and fk_competencias_coachee = $coachee group by fechamento.fk_grupo_pergunta ";

    $qryVisualizarCompetencias = mysql_query($sqlVisualizarCompetencias);
    $id_pessoa_coachee = $_SESSION['id_usuario'];
   
    while($arVisualizarCompetencias = mysql_fetch_assoc($qryVisualizarCompetencias )){
        
        $id_familia[] =  $arVisualizarCompetencias ['id_erp'];
    }
    $data = date('Y-m-d');
    $hora = date('H:i');
    $InsertInto = "INSERT INTO `is_orcamento`(`id_estabelecimento`,`id_pessoa`,`id_tp_orcamento`,`id_situacao_orcamento`,`id_cond_pagto`,`id_transportadora`,`id_tp_frete`,`dt_entrega`,`dt_entrega_desejada`,`id_moeda`,`id_usuario_cad`,`sn_digitacao_completa`,`sn_faturamento_parcial`,`sn_antecipa_entrega`,`vl_total_desconto`,`id_destino_mercadoria`,`id_tp_preco`,`dt_orcamento`,`dt_cadastro`,`id_endereco_entrega`,`vl_total_bruto`,`vl_total_liquido`,`vl_total_ipi`,`vl_total_st`,`vl_total_icms`,`vl_total_frete`,`sn_avaliado_credito`,`sn_aprovado_credito`,`sn_avaliado_comercial`,`sn_aprovacao_parcial`,`sn_aprovado_comercial`,`id_grupo_tab_preco`,`sn_em_aprovacao_comercial`,`sn_importado_erp`,`sn_exportado_erp`,`dt_validade_orcamento`,`sn_gerado_bonificacao_auto`,`sn_gerou_pedido_bonificacao`,`sn_gerou_pedido`,`sn_passou_pelo_passo1`,`sn_impresso`,`id_representante_pessoa`,`id_representante_principal`,`pct_desconto_tab_preco`,`pct_desconto_pessoa`,`pct_desconto_informado`,`vl_taxa_financiamento`,`vl_total`,`peso_total`,`sn_gerado_de_clone`,`vl_total_final`,`hr_cadastro`,`sn_permite_copia`,`id_origem_sistema`)        
            VALUES('2','".$coachee."','1','1','2','1','2','".$data."','".$data."','1', '".$id_pessoa_coachee."','0','1','1','0.00','2','1','".$data."','".$data."','1','0.00','0.00','0.00','0.00','0.00','0.00','0','0','0','1','0','2','0','0','0','0','0','0','0','1','0',	'".$id_pessoa_coachee."','".$id_pessoa_coachee."','0.00000','0.00000','0.00000','1.00000','0.00000','0.00000','0','0.00000','".$hora."','1','99')";

    $qryInsertInto = mysql_query($InsertInto);
    $id_orcamento_pedido = mysql_insert_id();
    $valorInsert = $id_orcamento_pedido;
    if(!$qryInsertInto  )
        echo mysql_error();
    else
        $id_familia = implode(",", $id_familia);
        
        $sqlFamilia = "select * from is_familia_comercial where id_familia_erp in($id_familia)";   
        $qryFamilia = mysql_query($sqlFamilia);
        while($arFamilia = mysql_fetch_assoc($qryFamilia)){
            $numregFamilia[] = $arFamilia['numreg'];
        }
        $numregFamilia = implode(',',$numregFamilia);
         
       
        $id_sequencia                   = '0';
        $id_moeda                       = '1';
        $id_tp_preco                    = '1';
        $qtde                           = '';
        $id_situacao_item               = '1';
        $pct_desconto_base              = '0.00000';
        $vl_unitario_com_desconto_base	= '0.00000';
        $vl_unitario_com_descontos	= $valor;
        $vl_unitario_ipi                = '0.00000';
        $vl_unitario_icms               = '0.00000';
        $vl_unitario_st                 = '0.00000';
        $vl_total_bruto                 =  $valor ;
        $vl_total_liquido               =  $valor;
        $pct_desconto_total             = '0.00000';
        $dt_cadastro                    = $data;
        $id_usuario_cad                 = $id_pessoa_coachee;
        $pct_aliquota_ipi              = '0.00000';	
        $pct_aliquota_iva               = '0.00000';
        $id_cfop                        = '4';
        $id_unid_medida                 = '3';
        $vl_total_ipi                   = '0.00000';
        $sn_item_comercial              = '1';
        $vl_total_st                    = '0.00';
        $pct_comissao                   = '0.00';   
        $vl_total_comissao              = '0.00';
        $qtde_por_unid_medida           = '1.00000';
        $total_unidades                 = $valor;
        $vl_unitario_tabela_original    = $valor;
        $vl_cotacao                     = '1.00000';
        $sn_cotacao_fixa                = '0';
        $sn_vl_unitario_sugestao_nf     = '0';
        $vl_unitario_base_calculo	= $valor;
        $vl_unitario_convertido         = $valor;
        $vl_total_bruto_base_calculo    = $valor;
        $vl_total_liquido_base_calculo	= $valor;
        $pct_desconto_tab_preco         = '0.00000';
        $qtde_por_qtde_informada	= $qtde;
        $qtde_base_calculo              = $valor;
        $fator_conv_qtde_base_calculo	= '1.00000';
        $peso_total                     = '0.00000';
        $vl_total_frete                 = '0.00000';
        $sn_possui_st                   = '0';

        $sqlInsertOrcamentoInto= "INSERT INTO `is_orcamento_item`(`id_orcamento`,`id_sequencia`,`id_moeda`,`id_tp_preco`,`qtde`,`id_situacao_item`,`pct_desconto_base`,`vl_unitario_com_desconto_base`,`vl_unitario_com_descontos`,`vl_unitario_ipi`,`vl_unitario_icms`,`vl_unitario_st`,`vl_total_bruto`,`vl_total_liquido`,`pct_desconto_total`,`dt_cadastro`,`id_usuario_cad`,`pct_aliquota_ipi`,`pct_aliquota_iva`,`id_cfop`,`id_unid_medida`,`vl_total_ipi`,`sn_item_comercial`,`vl_total_st`,`pct_comissao`, `vl_total_comissao`,`qtde_por_unid_medida`,`total_unidades`,`vl_unitario_tabela_original`,`vl_cotacao`,`sn_cotacao_fixa`,`sn_vl_unitario_sugestao_nf`,`vl_unitario_base_calculo`,`vl_unitario_convertido`,`vl_total_bruto_base_calculo`,`vl_total_liquido_base_calculo`,`pct_desconto_tab_preco`,`qtde_por_qtde_informada`,`qtde_base_calculo`,`fator_conv_qtde_base_calculo`,`peso_total`,`vl_total_frete`,`sn_possui_st`,`id_produto`) VALUES";                    
        $sqlProduto = "select produto.numreg, nome_produto, preco.vl_unitario
                        from is_produto as produto
                          inner join is_tab_preco_valor as preco
                          on preco.id_produto = produto.numreg
                        where produto.id_familia_comercial in($numregFamilia)";
        $qryProduto = mysql_query($sqlProduto);
        while($arProduto = mysql_fetch_assoc($qryProduto)){
            $valor                          = $arProduto['vl_unitario'];
            $qtde                           = '1';
            $vl_total_bruto                 = $valor ;
            $vl_total_liquido               = $valor;
            $total_unidades                 = $valor;
            $vl_total_bruto_base_calculo    = $valor;
            $vl_total_liquido_base_calculo  = $valor;
            $qtde_base_calculo              = $valor;
            $vl_unitario_tabela_original    = $valor;
            $vl_unitario_com_descontos      = $valor;
            $vl_unitario_base_calculo       = $valor;
            $vl_unitario_convertido         = $valor;
            $id_produto                     = $arProduto['numreg'];
            $qtde_por_qtde_informada        = $qtde;
            $id_sequencia                   = $id_sequencia + 10;
            
            $values[] = "(
                            '$id_orcamento_pedido',
                            '$id_sequencia',          
                            '$id_moeda', 
                            '$id_tp_preco',
                            '$qtde',
                            '$id_situacao_item',
                            '$pct_desconto_base',
                            '$vl_unitario_com_desconto_base',
                            '$vl_unitario_com_descontos',
                            '$vl_unitario_ipi',   
                            '$vl_unitario_icms',  
                            '$vl_unitario_st', 
                            '$vl_total_bruto',
                            '$vl_total_liquido',
                            '$pct_desconto_total',
                            '$dt_cadastro',
                            '$id_usuario_cad',
                            '$pct_aliquota_ipi',	
                            '$pct_aliquota_iva',
                            '$id_cfop',
                            '$id_unid_medida',
                            '$vl_total_ipi',
                            '$sn_item_comercial',
                            '$vl_total_st',
                            '$pct_comissao',  
                            '$vl_total_comissao',
                            '$qtde_por_unid_medida',
                            '$total_unidades',
                            '$vl_unitario_tabela_original',
                            '$vl_cotacao',
                            '$sn_cotacao_fixa',
                            '$sn_vl_unitario_sugestao_nf',
                            '$vl_unitario_base_calculo',
                            '$vl_unitario_convertido',  
                            '$vl_total_bruto_base_calculo',  
                            '$vl_total_liquido_base_calculo',	
                            '$pct_desconto_tab_preco', 
                            '$qtde_por_qtde_informada',
                            '$qtde_base_calculo',   
                            '$fator_conv_qtde_base_calculo',	
                            '$peso_total',  
                            '$vl_total_frete', 
                            '$sn_possui_st',                   
                            '$id_produto'          
                )";
        }
        
        $values = implode(',',$values);
        $qryItem = $sqlInsertOrcamentoInto.$values;
        if(!mysql_query($qryItem )){
            echo mysql_error();
        }else{
          
            echo $id_orcamento_pedido ;
           
            
        }
?>