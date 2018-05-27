<?php

/*\
|*| class.VendaCallBackCustom.php
|*| Autor: Alex
|*| 08/02/2012 10:26:37
|*| Modificaзгo: 23/07/2013 11:00 - Felipe Campos
\*/
class VendaCallBackCustom{ 
    
    /*\
    |*| Declaraзгo das variбveis globais
    \*/
    static private $id_pedido = 0;
    static private $id_pessoa = 0;
    static private $id_produto = 0;
    static private $id_curso_online = array();
    static private $arrCamposCursosOnline = array();
    static private $arrProdutosQueCompoeCursoOnline = array("83", "91", "121", "122", "123", "124", "125");
    
    /*\
    |*| Get das variбveis globais
    \*/
    static function getIdPedido(){
        return self::$id_pedido;
    }
    static function getIdPessoa(){
        return self::$id_pessoa;
    }
    static function getIdCursoOnline(){
        return self::$id_curso_online;
    }
    static function getIdProdutosQueCompoeCursoOnline(){
        return self::$arrProdutosQueCompoeCursoOnline;
    }
    
    /*\
    |*| Mйtodo de callback executado
    |*| @param Venda $ObjVenda
    |*| @param string $Metodo
    |*| @param string $Parte 
    |*| @param array $Dados 
    |*| @return bool Sempre retornar true ou false
    \*/
    static function ExecutaVenda(Venda $ObjVenda,$Metodo,$Parte,$Dados=array()){

        /*\
        |*| Se for pedido executa funзгo curso Online 
        \*/
        if($Metodo == 'Passo4_finaliza_venda' && $Parte == 'Final')
            self::ExecutaCursoOnline($ObjVenda,$Metodo,$Parte,$Dados);
        
        return true; //Nгo remover
    }
    
    static function ExecutaVendaItem(){
        /*
         * C?digo Personalizado deve ser colocado aqui
         */
    }
    
    static function ExecutaCursoOnline(Venda $ObjVenda,$Metodo,$Parte,$Dados=array()){

        /*\
        |*| Atribui valores as variбveis globais
        \*/
        self::$id_pedido = $ObjVenda->getNumregVenda();
        self::$id_pessoa = $ObjVenda->getPessoa()->getNumregPessoa();
        
        /*\
        |*| Limitando as variбveis que serгo atribuidos valores
        \*/
        $arrVariaveisGlobais = array(
            "sn_ead_completo_master",
            "sn_ead_completo_master_data",
            "sn_avaliacao_online_realizada_master",
            "sn_avaliacao_online_realizada_master_data",
            "sn_certificado_emitido_master",
            "sn_certificado_emitido_master_data",
            "sn_pagamento_quitado_master",
            "sn_pagamento_quitado_master_data",
            "sn_projeto_comprovacao_cientifica_ppc_master",
            "sn_projeto_comprovacao_cientifica_ppc_master_data",
            "sn_projeto_comprovacao_cientifica_executive_master",
            "sn_projeto_comprovacao_cientifica_executive_m_data",
            "sn_projeto_comprovacao_cientifica_xtreme_master",
            "sn_projeto_comprovacao_cientifica_xtreme_m_data",
            "sn_avaliacao_enviada_master",
            "sn_avaliacao_enviada_master_data",
            "sn_material_didatico_enviado_master",
            "sn_material_didatico_enviado_master_data",
            "sn_ead_completo_career",
            "sn_ead_completo_career_data",
            "sn_projeto_certificacao_entregue_career",
            "sn_projeto_certificacao_entregue_career_data",
            "sn_avaliacao_online_realizada_career",
            "sn_avaliacao_online_realizada_career_data",
            "sn_certificado_emitido_career",
            "sn_certificado_emitido_career_data",
            "sn_pagamento_quitado_career",
            "sn_pagamento_quitado_career_data",
            "sn_avaliacao_enviada_career",
            "sn_avaliacao_enviada_career_data",
            "sn_material_didatico_enviado_career",
            "sn_material_didatico_enviado_career_data",
            "sn_ppc_finalizado",
            "sn_ead_enviado_career",
            "ead_enviado_career_data",
            "sn_ead_enviado_master",
            "ead_enviado_master_data",
			"observacao"
        );
        
        /*\
        |*| Pega itens que estгo no pedido
        \*/
        foreach($ObjVenda->getItens() as $meusItens){
               $arrItens = $meusItens->getDadosVendaItem();
               $itens []= $arrItens["id_produto"];
        }

        /*\
        |*| Compara se os itens do pedido sгo curso online, se sim: inclui curso online, se nгo e existir curso online: apaga curso online
        \*/
        foreach(self::$arrProdutosQueCompoeCursoOnline as $produtosQueCompoeCursoOnline){
            
            /*\
            |*| Atribui id_produto atual
            \*/
            self::$id_produto = $produtosQueCompoeCursoOnline;
            
            if( in_array($produtosQueCompoeCursoOnline, $itens) ){
                
                /*\
                |*| Verifica se existe Curso Online nesse id_pedido, id_produto e id_pessoa
                |*| Atribui valores ao array global $arrCamposCursosOnline
                \*/
                foreach(array_shift(self::SelecionaCursosOnline()) as $keyArrCursosOnline => $valueArrCursosOnline)
                    if(in_array($keyArrCursosOnline, $arrVariaveisGlobais))
                        self::$arrCamposCursosOnline[$keyArrCursosOnline] = $valueArrCursosOnline;
                
                /*\
                |*| Verifica se jб existe algum curso online registrado com esse id_pedido, id_produto e id_pessoa, se nгo houver inseri curso online
                |*| Se existir verifica se o pedido foi cancelado, se sim: exclui curso online
                \*/
                if(count(self::$arrCamposCursosOnline) == 0){
                    self::$id_curso_online []= self::InserirCursosOnline();
                    self::InserirAtividade();
                }else
                    if($ObjVenda->isCancelado()){
                        self::DeletaCursosOnline();
                        self::DeletaAtividade();
                    }
                
            }else{
                self::DeletaCursosOnline();
                self::DeletaAtividade();
            }
            
            self::$arrCamposCursosOnline = array();
        }

    }
        
    static function InserirCursosOnline(){
        $sqlCursosOnline = "    
			INSERT INTO c_coaching_cursos_online 
				(
					sn_ead_completo_master, 
					sn_ead_completo_master_data, 
					sn_avaliacao_online_realizada_master, 
					sn_avaliacao_online_realizada_master_data, 
					sn_certificado_emitido_master, 
					sn_certificado_emitido_master_data, 
					sn_pagamento_quitado_master, 
					sn_pagamento_quitado_master_data, 
					sn_projeto_comprovacao_cientifica_ppc_master, 
					sn_projeto_comprovacao_cientifica_ppc_master_data, 
					sn_projeto_comprovacao_cientifica_executive_master, 
					sn_projeto_comprovacao_cientifica_executive_m_data, 
					sn_projeto_comprovacao_cientifica_xtreme_master, 
					sn_projeto_comprovacao_cientifica_xtreme_m_data, 
					sn_avaliacao_enviada_master, 
					sn_avaliacao_enviada_master_data, 
					sn_material_didatico_enviado_master, 
					sn_material_didatico_enviado_master_data, 
					sn_ead_completo_career, 
					sn_ead_completo_career_data, 
					sn_projeto_certificacao_entregue_career, 
					sn_projeto_certificacao_entregue_career_data, 
					sn_avaliacao_online_realizada_career, 
					sn_avaliacao_online_realizada_career_data, 
					sn_certificado_emitido_career, 
					sn_certificado_emitido_career_data, 
					sn_pagamento_quitado_career, 
					sn_pagamento_quitado_career_data, 
					sn_avaliacao_enviada_career, 
					sn_avaliacao_enviada_career_data, 
					sn_material_didatico_enviado_career, 
					sn_material_didatico_enviado_career_data, 
					id_pedido, 
					id_pessoa, 
					id_produto,
					sn_ppc_finalizado,
					sn_ead_enviado_career,
					ead_enviado_career_data,
					sn_ead_enviado_master,
					ead_enviado_master_data
				) 
			VALUES 
				(
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'0', 
					NULL, 
					'".(self::$id_pedido)."', 
					'".(self::$id_pessoa)."',
					'".(self::$id_produto)."',
					'0',
					'0',
					NULL,
					'0',
					NULL
				)
			;
        ";
        mysql_query($sqlCursosOnline);
		
        $ultimo_id_inserido = mysql_insert_id();
        $resultadoInsertCursoOnline = mysql_affected_rows();

        /*\ Inserindo sincronizaзгo \*/
        $opc = "incluir";
        $data = date("Y-m-d H:i");
        $registro_chave = $ultimo_id_inserido;

        $insertSinc = 'INSERT INTO 
                            c_coaching_sincronizacao_oasis_king 
                                (`data_log`, `operacao`, `campo_chave`, `registro_chave`, `tabela`, `sn_integrado`) 
                            VALUES
                                ( "'.$data.'", "'.$opc.'", "numreg", "'.$registro_chave.'", "c_coaching_cursos_online", "0")';

        mysql_query($insertSinc);
        /*\ End sincronizaзгo \*/
		
        return ($resultadoInsertCursoOnline > 0) ? $ultimo_id_inserido : false;
    }
    
    static function SelecionaCursosOnline(){
        $sqlCursosOnline = "SELECT * FROM c_coaching_cursos_online WHERE id_pedido = '".(self::$id_pedido)."' AND id_pessoa = '".(self::$id_pessoa)."' AND id_produto = '".(self::$id_produto)."';";
        $qryCursosOnline = mysql_query($sqlCursosOnline);
        while($arCursosOnline = mysql_fetch_assoc($qryCursosOnline))
            $arrCursosOnline []= $arCursosOnline;
        return $arrCursosOnline;
    }
    
	static function InserirAtividade(){
		
		if(self::$id_produto == 83)
			$nome_produto = "Master";
		elseif(self::$id_produto == 91)
			$nome_produto = "Career";
		elseif(self::$id_produto == 121)
			$nome_produto = "Sucesso em Lideranзa";
		elseif(self::$id_produto == 122)
			$nome_produto = "Sucesso em Vendas";
		elseif(self::$id_produto == 123)
			$nome_produto = "Sucesso em Pessoal";
		elseif(self::$id_produto == 124)
			$nome_produto = "Sucesso em Negуcios";
		elseif(self::$id_produto == 125)
			$nome_produto = "Sucesso em Administraзгo de Tempo";
		else
			$nome_produto = "";
	
		$sqlInserirAtividade = "
			INSERT INTO is_atividade (
				`id_tp_atividade`,
				`assunto`,
				`id_usuario_resp`,
				`id_situacao`,
				`wcp_grupo_trabalho`,
				`dt_inicio`,
				`hr_inicio`,
				`dt_prev_fim`,
				`hr_prev_fim`,
				`id_usuario_cad`,
				`dt_cadastro`,
				`hr_cadastro`,
				`id_atividade_pai`,
				`obs`,
				`id_produto`,
				`id_pedido`,
				`id_pessoa`
			) VALUES (
				'1024',
				'Enviar senha $nome_produto',
				'113',
				'1',
				'54',
				date_format(now(), '%Y-%m-%d'),
				date_format(now(), '%H:%i'),
				date_format(now(), '%Y-%m-%d'),
				date_format(now(), '%H:%i'),
				'1',
				date_format(now(), '%Y-%m-%d'),
				date_format(now(), '%H:%i'),
				'".(self::$id_pedido)."',
				'',
				'".(self::$id_produto)."',
				'".(self::$id_pedido)."',
				'".(self::$id_pessoa)."'
			);
		";

		return (mysql_query($sqlInserirAtividade)) ? TRUE : FALSE;
	}
	
	static function DeletaAtividade(){
        $sqlAtividade = "DELETE FROM is_atividade WHERE 
							id_atividade_pai = '".self::$id_pedido."' 
							AND id_tp_atividade = '1023' 
							AND id_usuario_resp = '113' 
							AND id_situacao = '1' 
							AND wcp_grupo_trabalho = '54' 
							AND id_pedido = '".self::$id_pedido."' 
							AND id_pessoa = '".self::$id_pessoa."' 
							AND id_produto = '".(self::$id_produto)."';";
		mysql_query($sqlAtividade);
        return (mysql_affected_rows() > 0) ? true : false;
    }
	
    static function AtualizaCursosOnline(){
        $sqlCursosOnline = "
            UPDATE c_coaching_cursos_online SET 
                sn_ead_completo_master=".self::$arrCamposCursosOnline['sn_ead_completo_master'].",
                sn_ead_completo_master_data=".self::$arrCamposCursosOnline['sn_ead_completo_master_data'].",
                sn_avaliacao_online_realizada_master=".self::$arrCamposCursosOnline['sn_avaliacao_online_realizada_master'].",
                sn_avaliacao_online_realizada_master_data=".self::$arrCamposCursosOnline['sn_avaliacao_online_realizada_master_data'].",
                sn_certificado_emitido_master=".self::$arrCamposCursosOnline['sn_certificado_emitido_master'].",
                sn_certificado_emitido_master_data=".self::$arrCamposCursosOnline['sn_certificado_emitido_master_data'].",
                sn_pagamento_quitado_master=".self::$arrCamposCursosOnline['sn_pagamento_quitado_master'].",
                sn_pagamento_quitado_master_data=".self::$arrCamposCursosOnline['sn_pagamento_quitado_master_data'].",
                sn_projeto_comprovacao_cientifica_ppc_master=".self::$arrCamposCursosOnline['sn_projeto_comprovacao_cientifica_ppc_master'].",
                sn_projeto_comprovacao_cientifica_ppc_master_data=".self::$arrCamposCursosOnline['sn_projeto_comprovacao_cientifica_ppc_master_data'].",
                sn_projeto_comprovacao_cientifica_executive_master=".self::$arrCamposCursosOnline['sn_projeto_comprovacao_cientifica_executive_master'].",
                sn_projeto_comprovacao_cientifica_executive_m_data=".self::$arrCamposCursosOnline['sn_projeto_comprovacao_cientifica_executive_m_data'].",
                sn_projeto_comprovacao_cientifica_xtreme_master=".self::$arrCamposCursosOnline['sn_projeto_comprovacao_cientifica_xtreme_master'].",
                sn_projeto_comprovacao_cientifica_xtreme_m_data=".self::$arrCamposCursosOnline['sn_projeto_comprovacao_cientifica_xtreme_m_data'].",
                sn_avaliacao_enviada_master=".self::$arrCamposCursosOnline['sn_avaliacao_enviada_master'].",
                sn_avaliacao_enviada_master_data=".self::$arrCamposCursosOnline['sn_avaliacao_enviada_master_data'].",
                sn_material_didatico_enviado_master=".self::$arrCamposCursosOnline['sn_material_didatico_enviado_master'].",
                sn_material_didatico_enviado_master_data=".self::$arrCamposCursosOnline['sn_material_didatico_enviado_master_data'].",
                sn_ead_completo_career=".self::$arrCamposCursosOnline['sn_ead_completo_career'].",
                sn_ead_completo_career_data=".self::$arrCamposCursosOnline['sn_ead_completo_career_data'].",
                sn_projeto_certificacao_entregue_career=".self::$arrCamposCursosOnline['sn_projeto_certificacao_entregue_career'].",
                sn_projeto_certificacao_entregue_career_data=".self::$arrCamposCursosOnline['sn_projeto_certificacao_entregue_career_data'].",
                sn_avaliacao_online_realizada_career=".self::$arrCamposCursosOnline['sn_avaliacao_online_realizada_career'].",
                sn_avaliacao_online_realizada_career_data=".self::$arrCamposCursosOnline['sn_avaliacao_online_realizada_career_data'].",
                sn_certificado_emitido_career=".self::$arrCamposCursosOnline['sn_certificado_emitido_career'].",
                sn_certificado_emitido_career_data=".self::$arrCamposCursosOnline['sn_certificado_emitido_career_data'].",
                sn_pagamento_quitado_career=".self::$arrCamposCursosOnline['sn_pagamento_quitado_career'].",
                sn_pagamento_quitado_career_data=".self::$arrCamposCursosOnline['sn_pagamento_quitado_career_data'].",
                sn_avaliacao_enviada_career=".self::$arrCamposCursosOnline['sn_avaliacao_enviada_career'].",
                sn_avaliacao_enviada_career_data=".self::$arrCamposCursosOnline['sn_avaliacao_enviada_career_data'].",
                sn_material_didatico_enviado_career=".self::$arrCamposCursosOnline['sn_material_didatico_enviado_career'].",
                sn_material_didatico_enviado_career_data=".self::$arrCamposCursosOnline['sn_material_didatico_enviado_career_data'].",
                sn_ppc_finalizado=".self::$arrCamposCursosOnline['sn_ppc_finalizado'].",
                sn_ead_enviado_career=".self::$arrCamposCursosOnline['sn_ead_enviado_career'].",
                ead_enviado_career_data=".self::$arrCamposCursosOnline['ead_enviado_career_data'].",
                sn_ead_enviado_master=".self::$arrCamposCursosOnline['sn_ead_enviado_master'].",
                ead_enviado_master_data=".self::$arrCamposCursosOnline['ead_enviado_master_data'].",
                observacao=".self::$arrCamposCursosOnline['observacao']."
            WHERE id_pedido = '".self::$id_pedido."' AND id_pessoa = '".self::$id_pessoa."' AND id_produto = '".(self::$id_produto)."';
        ";
        mysql_query($sqlCursosOnline);
        return (mysql_affected_rows() > 0) ? true : false;
    }
    
    static function DeletaCursosOnline(){
        $sqlCursosOnline = "DELETE FROM c_coaching_cursos_online WHERE id_pedido = '".self::$id_pedido."' AND id_pessoa = '".self::$id_pessoa."' AND id_produto = '".(self::$id_produto)."';";
        mysql_query($sqlCursosOnline);
        return (mysql_affected_rows() > 0) ? true : false;
    }
    
}
?>