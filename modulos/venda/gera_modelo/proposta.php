<?php
session_start();
$PrefixoIncludes = '../';
require_once('../includes.php');
require_once '../../../phpword/PHPWord.php';

if(empty($_GET['pnumreg'])){
    exit(getError('0040010001',getParametrosGerais('RetornoErro')));
}else{
    if($_GET['ptp_venda'] == 1)
        $Venda = new Orcamento($_REQUEST['ptp_venda'],$_GET['pnumreg'],true,false);
    else
        $Venda = new Pedido($_REQUEST['ptp_venda'],$_GET['pnumreg'],true,false);
}

$SnImpresso = $Venda->getDadosVenda('sn_impresso');
$Venda->setDadoVenda('sn_impresso',1);

if($Venda->isOrcamento())
    $Venda->setDadoVenda('id_situacao_venda',2);
	
$Venda->AtualizaDadosVendaBD();

/* Se é um orçamento e o e-mail ainda nao foi enviado */
if($Venda->isOrcamento() && $SnImpresso != 1){
    $Venda->FinalizaAtividadeEnvioOrcamento();
    $Venda->CriaAtividadeFollowupOrcamento();
}
	$PHPWord = new PHPWord();
	
	$section = $PHPWord->createSection();
	$sectionStyle = $section->getSettings();
	$sectionStyle->setPortrait();
	$sectionStyle->setMarginLeft(900);
	$sectionStyle->setMarginRight(900);
	$sectionStyle->setMarginTop(3000);
	$sectionStyle->setMarginBottom(900);
	
	
	$sql = "SELECT 
				orcamento.*, usuario.*, pessoa.* 
			FROM is_orcamento AS orcamento
				INNER JOIN is_pessoa AS pessoa
					ON pessoa.numreg = orcamento.id_pessoa
				INNER JOIN is_usuario AS usuario
					ON usuario.numreg  = orcamento.id_usuario_cad
				LEFT JOIN is_contato AS contato
					ON contato.id_empresa = pessoa.numreg
			WHERE orcamento.numreg = ".$_GET['id_modelo'];
	$qry = query($sql);
	$arPessoaOrcamento = mysql_fetch_assoc($qry);
	
	
	$sqlProdutosLinha = "SELECT 
			comercial.nome_familia_comercial as nome_familia_comercial, 
			comercial.numreg as numreg_comercial, 
			produto. *, 
			preco. *, 
			linha.numreg as numreg_linha, 
			linha.descricao_linha as descricao_linha, 
			item.qtde, 
			item.vl_unitario_com_descontos, 
			item.vl_total_liquido 
				FROM is_produto AS produto INNER JOIN is_familia_comercial AS comercial ON comercial.numreg = produto.id_familia_comercial INNER JOIN is_produto_linha as linha ON linha.numreg = produto.id_linha INNER JOIN is_tab_preco_valor AS preco ON preco.id_produto = produto.numreg INNER JOIN is_orcamento_item AS item ON item.id_produto = produto.numreg INNER JOIN is_orcamento AS orcamento ON orcamento.numreg = item.id_orcamento WHERE orcamento.numreg  = ".$_GET['pnumreg']."  GROUP BY comercial.numreg ";

	
	$qryProdutosLinha = mysql_query($sqlProdutosLinha);
	while($ar = mysql_fetch_assoc($qryProdutosLinha)){
	    $arProduto[] = $ar;
		$idFamiliaComercial[] = $ar['numreg_comercial'];
		$idLinha [] = $ar['id_linha'];
		
	    if( ($ar['numreg_linha'] == 4) ||  ( $ar['numreg_linha'] == 1 || $ar['numreg_linha'] == 2 || $ar['numreg_linha'] == 3 || $ar['numreg_linha'] == 5 )){
	    	$servicos_processo = " processo de coaching e treinamentos corporativos.";
	    }else if( ($ar['numreg_linha'] != 4) ||  ( $ar['numreg_linha'] == 1 || $ar['numreg_linha'] == 2 || $ar['numreg_linha'] == 3 || $ar['numreg_linha'] == 5 )){
	    	$servicos_processo = " treinamentos corporativos.";
	    }else{
	    	$servicos_processo = " processo de coaching.";
	    }
	    $arDescricao_solucoes[] = array(
			'nome_produto'          => $ar['nome_produto'],
			'descricao_solucao'     => $ar['wcp_descricao_produto'],
			'descricao_protocolo'	=> $ar['descricao_linha'],
			'vl_unitario'           => $ar['vl_unitario_com_descontos'],
			'vl_total_liquido'      => $ar['vl_total_liquido'],
			'qtde'           		=> (int) $ar['qtde']
		);
	    $arProdutos[] = $ar['nome_produto'];

	}
	
	$idFamiliaComercial = implode(',' , $idFamiliaComercial);
	$idLinha = implode(',' , $idLinha);
	
	$sqlCompetenciasMacro = "SELECT competencias_macro_numreg, competencias_macro_nome FROM tb_competencias_macro where fk_competencias_familia_numreg IN($idFamiliaComercial)";
	$qryCompetenciasMacro = mysql_query($sqlCompetenciasMacro);
	while($arCompetenciasMacro = mysql_fetch_assoc($qryCompetenciasMacro )){
		$competenciaMacro[]= $arCompetenciasMacro['competencias_macro_nome'];
		$numregMacro[] = $arCompetenciasMacro['competencias_macro_numreg'];
	
	}

	$numregMacro = implode(", ", $numregMacro);
	$sqlMicroCompetencias = "select * from tb_competencias_micro where fk_competencias_macro_numreg in($numregMacro)";
	$qryMicroCompetencias = mysql_query($sqlMicroCompetencias);
		
	$nome_produtos = implode(', ', $arProdutos);
	
	// Create header
	$header = $section->createHeader();
	
	// Add a watermark to the header
	$header->addWatermark('papelTimbrado.jpg', array('align'=>left ,'marginTop'=>-60, 'marginLeft'=>-60));
	
	$pStyle = array('align'=>'left', 'spaceAfter'=>100);
	$PHPWord->addFontStyle('pStyle', $pStyle);
	
	$pStyleTitulo = array('align'=>'left', 'spaceAfter'=>300, 'bold'=>true );
	$PHPWord->addParagraphStyle('pStyleCenter', array('align'=>'center', 'spaceAfter'=>300));
	
	$section->addText(utf8_decode('{dia} de {mes} de {ano}')														,$pStyle );
	$section->addText(utf8_decode('{num}/{anoDoisDigitos}')															,$pStyle );
	$section->addText(utf8_decode('{nome_empresa}.')																,$pStyle );
	$section->addText('{cidade} - {estado}.'															,$pStyle );
	$section->addText('{nome_pessoa}.'																	,$pStyle );
	$section->addText(utf8_decode('Ref.: Proposta para prestação de serviços profissionais de {servicos_processo}')	,$pStyle );
	
	$section->addTextBreak(1);
	$textrun = $section->createTextRun();
	
	$section->addText(utf8_decode('Prezados Senhores,'),$pStyleTitulo);
	$section->addText(utf8_decode('Antecipadamente, expressamos nossa satisfação pela oportunidade desta proposta para prestação de serviços profissionais de {servicos_processo} V. Sas.') ,$pStyle );
	
	$objTextRun = $section->createTextRun();
	$objTextRun->addText(utf8_decode('Nossa proposta compreende a prestação de serviços de {servicos_processo} - ({nome_produtos}), cujo a finalidade é'), $pStyle );
	$objTextRun->addText(' (DESCREVER A FINALIDADE - ex: AUMENTAR VENDAS)', array('color' => "red"));
	$section->addText($objTextRun);
	
	$section->addText(utf8_decode('Neste ínterim, gostaríamos de salientar que o nosso trabalho não só se diferencia pela capacidade técnica, mas pela garantia dos serviços prestados, pela preocupação e foco na obtenção dos resultados almejado por vossa organização de modo a exceder suas expectativas.'),$pStyle );
	$section->addText(utf8_decode('Agradecemos e nos colocamos à disposição para qualquer complementação ou esclarecimento que se faça necessário.,'),$pStyle );
	
	
	$section->addTextBreak(2);
	$section->addText(utf8_decode('Atenciosamente,')							,null, 'pStyleCenter');
	$section->addText(utf8_decode('{nome_partner}')								,null, 'pStyleCenter');
	$section->addText(utf8_decode('Sócio Diretor')								,null, 'pStyleCenter');
	$section->addText(utf8_decode('Unidade Franquiada nº {unidade_franqueada}') ,null, 'pStyleCenter');
	
	
	$section->addPageBreak();
	
	$section->addText(utf8_decode('Índice')										,$pStyleTitulo);
	$section->addTextBreak(2);
	$section->addText(utf8_decode('1. Quem somos')								,$pStyle );
	$section->addText(utf8_decode('2. Escopo de trabalho')						,$pStyle );
	$section->addText(utf8_decode('3. Metodologia')								,$pStyle );
	$section->addText(utf8_decode('4. Organização do trabalho')					,$pStyle );
	$section->addText(utf8_decode('5. Proposta Comercial')						,$pStyle );
	$section->addText(utf8_decode('6. Início da Prestação de Serviços')			,$pStyle );
	$section->addText(utf8_decode('7. Condições Gerais')						,$pStyle );
	$section->addText(utf8_decode('8. Carta de Aceite')							,$pStyle );
	
	
	$section->addPageBreak();
	$section->addText(utf8_decode('1. Quem somos'),$pStyleTitulo);
	
	$section->addText(utf8_decode('A SBCoaching Empresas é uma instituição brasileira, fundada em 1999, que possui em sua essência o comprometimento com o aumento de performance de pessoas, times e empresas, por meio de soluções em coaching cientificamente validadas, que promovem resultados reais.') ,$pStyle );
	$section->addText(utf8_decode('Reconhecida por suas soluções corporativas de alto impacto, SBCoaching Empresas pertence ao conceituado grupo Sociedade Brasileira de Coaching - empresa que é referência no país pela qualidade de seus serviços, produtos e treinamentos no segmento de coaching.')	 ,$pStyle );
	$section->addText(utf8_decode('Frutos de uma parceria entre a Sociedade Brasileira de Coaching e de Brian Tracy, coach de renome internacional, que há mais de 30 anos dedica-se a aumentar os resultados de líderes e empresas, as soluções exclusivas da SBCoaching Empresas trazem ao Brasil o que há de mais moderno e eficaz em termos de produtos, serviços e treinamentos capazes de levar empresas a um novo patamar de resultados.'),$pStyle );
	$section->addText(utf8_decode('Pense nos principais desafios que uma empresa pode enfrentar: aumentar a produtividade de times, elevar o desempenho do empreendedor, ampliar os resultados do negócio, desenvolver novas lideranças ou maximizar a performance de líderes mais experientes. Para cada um desses desafios, a SBCoaching Empresas oferece soluções completas, utilizadas por mais de 1.000 organizações em todo o mundo.'),$pStyle );

	
	$section->addPageBreak();
	$section->addText(utf8_decode('2. Escopo de Trabalho'), $pStyleTitulo);
	
	$objTextRun = $section->createTextRun();
	$objTextRun->addText(utf8_decode('Com base no entendimento da situação, e nos assessments realizados identificamos os seguintes pontos a serem trabalhados e desenvolvidos e em '), $pStyle);
	$objTextRun->addText('(vossa equipe ou executivos ou V.sa.: - ELENCAR PUBLICO ALVO).', array('color' => "red"));
	$section->addText($objTextRun);
		
	$objTextRunMacroCompetencias = $section->createTextRun();
	$objTextRun->addText(utf8_decode('O escopo do nosso trabalho visa   '), $pStyle );
	foreach($competenciaMacro as $key){
		$objTextRun->addText(utf8_decode($key).", ", $pStyle);
	}
	$objTextRun->addText(' a fim de que alcancemos:',$pStyle );
	$section->addText($objTextRunMacroCompetencias);
	

	while($arMicroCompetencias = mysql_fetch_assoc($qryMicroCompetencias)){
		$section->addListItem($arMicroCompetencias['competencias_micro_nome'],0);
	}
	

	foreach($arProdutos as $prod){
 		if($prod == utf8_decode('Sessões Executive Coaching')){
			$section->addText(utf8_decode('EXECUTIVE COACHING'),$pStyleTitulo);
			
			$section->addText(utf8_decode('O programa Executive Coaching é uma solução customizada e poderosa, capaz de atender a necessidade do cliente de maneira contundente e eficaz. Ao trazer mais e melhores resultados para a empresa, entre eles o aumento da cooperação e do engajamento, a melhoria do trabalho em equipe, o estímulo às soluções criativas e a melhoria do clima organizacional, os executivos fortalecem seus pontos fortes e se tornam mais preparados e atingem os mais altos níveis de performance. '),$pStyle);
			$section->addText(utf8_decode('Embora o executive coaching foque na vida profissional do indivíduo, as sessões de coaching também geram desenvolvimento interpessoal, transformações e mudanças pessoais por meio do desenvolvimento de competências cruciais para o sucesso, por meio da parceria colaborativa e individualizada entre o líder/executivo e o coach. Entre os benefícios podemos citar: '),$pStyle);
			$section->addListItem(utf8_decode('Aumentar a eficácia do líder e seus recursos internos para superar desafios'), 0);
			$section->addListItem(utf8_decode('Elevar a capacidade do líder de influenciar e de liderar pelo exemplo'), 0);
			$section->addListItem(utf8_decode('Proporcionar a integração vida/trabalho e reduzir o stress de altos executivos'), 0);
			$section->addListItem(utf8_decode('Otimizar tomada de decisão, administração do tempo, delegação e outros processos fundamentais para o líder'), 0);
			$section->addListItem(utf8_decode('Elevar a performance e a produtividade do líder'), 0);
			$section->addListItem(utf8_decode('Mais satisfação e motivação'), 0);
			$section->addListItem(utf8_decode('Aumentar os resultados do líder'), 0);
			$section->addListItem(utf8_decode('Ganhar conhecimento e insight sobre si mesmo e a organização, gerando versatilidade e flexibilidade'), 0);
			$section->addListItem(utf8_decode('Superar bloqueios e resistência à mudança'), 0);
			$section->addListItem(utf8_decode('Reconhecer as forças existentes e identificar as melhores formas de utilizá-las no trabalho'), 0);
			$section->addListItem(utf8_decode('Administrar conflitos com eficácia'), 0);
		}if($prod == utf8_decode('Sessões Business Coaching')){

			$section->addText(utf8_decode('BUSINESS COACHING'),$pStyleTitulo);

			$section->addText(utf8_decode('As Soluções de Business Coaching foram especialmente desenvolvidas para atender às necessidades de pequenas e médias empresas e, também, do empreendedor à frente de seu negócio. Fruto de uma parceria entre a SBCoaching® - a maior empresa de coaching do Brasil - e de Brian Tracy, um dos mais renomados e bem-sucedidos coaches do mundo, este é o meio mais rápido e eficaz de promover as melhorias fundamentais para promover o crescimento das empresas, por meio do desenvolvimento de competências cruciais para o sucesso.'),$pStyle );
			$section->addText(utf8_decode('O objetivo do Programa Business Coaching é aumentar de modo sustentável a produtividade, a performance, os lucros e a longevidade dos negócios, atuando direta e poderosamente sobre os elementos cruciais que possibilitam o sucesso e o aprimoramento da gestão e do planejamento de vendas, tais como clareza, eficácia, desenvolvimento, performance e liderança.'),$pStyle );
			$section->addText(utf8_decode('Os módulos que compõem o Programa Business Coaching são:'),$pStyle );
			
			$section->addListItem(utf8_decode('Ganhe Poder por Meio da Clareza'), 0);
			$section->addListItem(utf8_decode('Aumente sua Eficácia'), 0);
			$section->addListItem(utf8_decode('Amplie seus Negócios'), 0);
			$section->addListItem(utf8_decode('Torne-se um Vendedor Superstar'), 0);
			$section->addListItem(utf8_decode('Torne-se um líder'), 0);
			
		}if($prod == utf8_decode('Sessões Positive Coaching')){
			$section->addText(utf8_decode('POSITIVE COACHING'),$pStyleTitulo);			
			
			$section->addText(utf8_decode('Baseado nas recentes descobertas da Psicologia Positiva e fundamentado nos benefícios da qualidade de vida, o Positive Coaching aborda questões precisas e fundamentais para a elevação da felicidade, satisfação e bem-estar, tanto para a vida pessoal como profissional das pessoas. 	'),$pStyle );
			$section->addText(utf8_decode('Dessa forma, o Positive Coaching aprofunda a importância e os bons proventos das emoções positivas, abrangendo o processo que, por meio de técnicas e ferramentas, habilita o coach a direcionar pessoas ao mundo de grandes satisfações e realizações, fortalecendo a autoestima, autoimagem e o entendimento pessoal. No campo profissional, há um crescimento da liderança positiva e centrada que emprega diferentes forças para aumentar a satisfação nas tarefas, carreira e vocação, além do desenvolvimento de competências cruciais para o sucesso pessoal e profissional. Além disso, na área pessoal, o processo serve para aumentar o nível de satisfação, bem-estar e felicidade ao focar no aumento de conquistas e realizações que ampliarão os resultados tanto para indivíduos como para times ou empresas.'),$pStyle );
			$section->addText(utf8_decode('Entre os benefícios, podemos citar:'),$pStyle );
			$section->addListItem(utf8_decode('Mais saúde física e mental.'), 0);
			$section->addListItem(utf8_decode('Mais criatividade.'), 0);
			$section->addListItem(utf8_decode('Mais realização profissional.'), 0);
			$section->addListItem(utf8_decode('Mais prosperidade e ganhos financeiros.'), 0);
			$section->addListItem(utf8_decode('Aumento do desempenho para obter picos de performance.'), 0);
			$section->addListItem(utf8_decode('Elevação da resiliência, da motivação e do prazer de viver e de trabalhar.'), 0);
			$section->addListItem(utf8_decode('Mais flexibilidade para lidar com desafios.'), 0);
			$section->addListItem(utf8_decode('Melhoria nos relacionamentos.'), 0);
			$section->addListItem(utf8_decode('Mais comprometimento e energia para atingir objetivos.'), 0);
			$section->addListItem(utf8_decode('Mais autoconfiança, autoestima e autoeficácia.'), 0);
			$section->addListItem(utf8_decode('Mais emoções positivas e bem-estar.'), 0);
			$section->addListItem(utf8_decode('E no ambiente profissional, elevação do engajamento, da motivação e da satisfação profissio¬nal, com significativas reduções no turnover e nos níveis de absenteísmo.'), 0);
		}if($prod == utf8_decode('Programa Líder Coach')){
			$section->addText(utf8_decode('COLEÇÃO LIDERANÇA - PROGRAMA LÍDER COACH'),$pStyleTitulo);
			
			$section->addText(utf8_decode('O Programa Líder Coach é indicado não só para quem deseja aprender a liderar ou aumentar os resultados como líder, mas também para quem busca êxito com clareza e direcionamento, por meio do desenvolvimento das competências cruciais para o sucesso. São 8 módulos que abordam as premissas essenciais para transformar indivíduos em líderes de sucesso que superam expectativas e atingem os melhores resultados.'),$pStyle );
			$section->addText(utf8_decode('O programa é composto pelos seguintes treinamentos:'),$pStyle );
			
			$section->addListItem(utf8_decode('Treinamento 1 '.htmlspecialchars('-').' Seja um Líder Coach'), 0);
			$section->addListItem(utf8_decode('Treinamento 2 '.htmlspecialchars('-').' Definindo Metas e Objetivos'), 0);
			$section->addListItem(utf8_decode('Treinamento 3 '.htmlspecialchars('-').' Desenvolvendo Novas Competências'), 0);
			$section->addListItem(utf8_decode('Treinamento 4 '.htmlspecialchars('-').' Promovendo Mudanças Comportamentais'), 0);
			$section->addListItem(utf8_decode('Treinamento 5 '.htmlspecialchars('-').' Feedback e Follow-up'), 0);
			$section->addListItem(utf8_decode('Treinamento 6 '.htmlspecialchars('-').' Foco em Soluções'), 0);
			$section->addListItem(utf8_decode('Treinamento 7 '.htmlspecialchars('-').' Faça Autocoaching'), 0);
			$section->addListItem(utf8_decode('Treinamento 8 '.htmlspecialchars('-').' O líder Coach como Modelo'), 0);
			
			$section->addText(utf8_decode('Entre os benefícios da Coleção Liderança por SBCoaching®, podeos citar: '),$pStyleTitulo);
			
			$section->addListItem(utf8_decode('Adquirir e dominar um poderoso conjunto de conceitos, técnicas e ferramentas que proporcionam muito mais foco, direcionamento, motivação e autoconfiança, ampliando opções e recursos internos para que as pessoas possam lidar satisfatoriamente com as mais variadas situações.'), 0);
			$section->addListItem(utf8_decode('Ampliar a inteligência emocional, isto é, a habilidade de entender e de lidar com as próprias emoções e com as emoções dos outros, tornando os indivíduos mais adaptáveis e mais capazes de responder com eficácia às demandas do ambiente que os cerca.'), 0);
			$section->addListItem(utf8_decode('Elevar a capacidade de promover mudanças e melhorias contínuas.'), 0);
			$section->addListItem(utf8_decode('Desenvolver e desbloquear o poder pessoal e assumir o controle da própria vida.'), 0);
			$section->addListItem(utf8_decode('Melhorar a habilidade de se relacionar e de interagir, de modo a obter mais apoio, participação e comprometimento, além de potencializar o desenvolvimento das equipes.'), 0);
			$section->addListItem(utf8_decode('Trazer à tona o que se tem de melhor '.htmlspecialchars('-').' e ajudar os outros a fazer o mesmo (sejam eles seus colegas, colaboradores, filhos, familiares...).'), 0);
			$section->addListItem(utf8_decode('Desenvolver ou aprimorar as habilidades de formar, motivar e liderar times de alta performance. '), 0);
				
		}if($prod == utf8_decode('Programa Estratégias de Liderança')){
			$section->addText(utf8_decode('COLEÇÃO LIDERANÇA - PROGRAMA ESTRATÉGIAS DE LIDERANÇA'),$pStyleTitulo);
			$section->addText(utf8_decode('odo líder precisa de estratégias eficientes para atingir resultados realmente impactantes. No Programa Estratégias de Liderança, o foco é o desenvolvimento de estratégias de liderança '.htmlspecialchars('-').' para líderes, líderes em potencial e, também, para todo o profissional que precisa comandar equipes. Este programa tem como objetivo promover o autodesenvolvimento das competências de liderança estratégica para o aumento de resultados no ambiente corporativo.'),$pStyle);
			$section->addText(utf8_decode('O programa é composto pelos seguintes treinamentos:'),$pStyle);
			
			$section->addListItem(utf8_decode('Treinamento 1 '.htmlspecialchars('-').' Desenvolva-se como Líder'), 0);
			$section->addListItem(utf8_decode('Treinamento 2 '.htmlspecialchars('-').' Desenvolva o Poder da Visão'), 0);
			$section->addListItem(utf8_decode('Treinamento 3 '.htmlspecialchars('-').' Comunique-se para Vencer'), 0);
			$section->addListItem(utf8_decode('Treinamento 4 '.htmlspecialchars('-').' Use as Forças Alpha'), 0);
			$section->addListItem(utf8_decode('Treinamento 5 '.htmlspecialchars('-').' Torne-se um Líder Extraordinário'), 0);
			$section->addListItem(utf8_decode('Treinamento 6 '.htmlspecialchars('-').' Desenvolva o Capital Humano'), 0);
			$section->addListItem(utf8_decode('Treinamento 7 '.htmlspecialchars('-').' Forme Novas Lideranças'), 0);
			$section->addListItem(utf8_decode('Treinamento 8 '.htmlspecialchars('-').' Gerencie Mudanças'), 0);
			
			$section->addText(utf8_decode('Entre os benefícios da Coleção Liderança por SBCoaching®, podemos citar: '),$pStyleTitulo);
			
			$section->addListItem(utf8_decode('Adquirir e dominar um poderoso conjunto de conceitos, técnicas e ferramentas que proporcionam muito mais foco, direcionamento, motivação e autoconfiança, ampliando opções e recursos internos para que as pessoas possam lidar satisfatoriamente com as mais variadas situações.'), 0);
			$section->addListItem(utf8_decode('Ampliar a inteligência emocional, isto é, a habilidade de entender e de lidar com as próprias emoções e com as emoções dos outros, tornando os indivíduos mais adaptáveis e mais capazes de responder com eficácia às demandas do ambiente que os cerca.'), 0);
			$section->addListItem(utf8_decode('Elevar a capacidade de promover mudanças e melhorias contínuas.'), 0);
			$section->addListItem(utf8_decode('Desenvolver e desbloquear o poder pessoal e assumir o controle da própria vida.'), 0);
			$section->addListItem(utf8_decode('Melhorar a habilidade de se relacionar e de interagir, de modo a obter mais apoio, participação e comprometimento, além de potencializar o desenvolvimento das equipes.'), 0);
			$section->addListItem(utf8_decode('Trazer à tona o que se tem de melhor '.htmlspecialchars('-').' e ajudar os outros a fazer o mesmo (sejam eles seus colegas, colaboradores, filhos, familiares...).'), 0);
			$section->addListItem(utf8_decode('Desenvolver ou aprimorar as habilidades de formar, motivar e liderar times de alta performance. '), 0);
			
				
		}if($prod == utf8_decode('Programa Competências de Liderança')){
			$section->addText(utf8_decode('COLEÇÃO LIDERANÇA - PROGRAMA COMPETÊNCIAS DE LIDERANÇA'),$pStyleTitulo);
			
			$section->addText(utf8_decode('Treinamento indicado para quem busca o aprimoramento máximo como líder. O programa é composto por 8 módulos que abordam técnicas e ações eficazes para potencializar as competências de liderança com excelência e promovendo maiores resultados.'),$pStyle);
			$section->addText(utf8_decode('O programa é composto pelos seguintes treinamentos:'),$pStyle);
			
			$section->addListItem(utf8_decode('Treinamento 1 '.htmlspecialchars('-').' Gestão do Tempo'), 0);
			$section->addListItem(utf8_decode('Treinamento 2 '.htmlspecialchars('-').' Gerenciamento do Stress'), 0);
			$section->addListItem(utf8_decode('Treinamento 3 '.htmlspecialchars('-').' Delegação de Tarefas'), 0);
			$section->addListItem(utf8_decode('Treinamento 4 '.htmlspecialchars('-').' Tomada de Decisão'), 0);
			$section->addListItem(utf8_decode('Treinamento 5 '.htmlspecialchars('-').' Resolução de Problemas'), 0);
			$section->addListItem(utf8_decode('Treinamento 6 '.htmlspecialchars('-').' Administração de Conflitos'), 0);
			$section->addListItem(utf8_decode('Treinamento 7 '.htmlspecialchars('-').' Apresentações em Público'), 0);
			$section->addListItem(utf8_decode('Treinamento 8 '.htmlspecialchars('-').' Reuniões Eficazes'), 0);
			
			
			$section->addText(utf8_decode('Entre os benefícios da Coleção Liderança por SBCoaching®, podemos citar:'),$pStyleTitulo);
			
			$section->addListItem(utf8_decode('Adquirir e dominar um poderoso conjunto de conceitos, técnicas e ferramentas que proporcionam muito mais foco, direcionamento, motivação e autoconfiança, ampliando opções e recursos internos para que as pessoas possam lidar satisfatoriamente com as mais variadas situações.'), 0);
			$section->addListItem(utf8_decode('Ampliar a inteligência emocional, isto é, a habilidade de entender e de lidar com as próprias emoções e com as emoções dos outros, tornando os indivíduos mais adaptáveis e mais capazes de responder com eficácia às demandas do ambiente que os cerca.'), 0);
			$section->addListItem(utf8_decode('Elevar a capacidade de promover mudanças e melhorias contínuas.'), 0);
			$section->addListItem(utf8_decode('Desenvolver e desbloquear o poder pessoal e assumir o controle da própria vida.'), 0);
			$section->addListItem(utf8_decode('Melhorar a habilidade de se relacionar e de interagir, de modo a obter mais apoio, participação e comprometimento, além de potencializar o desenvolvimento das equipes.'), 0);
			$section->addListItem(utf8_decode('Trazer à tona o que se tem de melhor '.htmlspecialchars('-').' e ajudar os outros a fazer o mesmo (sejam eles seus colegas, colaboradores, filhos, familiares...).'), 0);
			$section->addListItem(utf8_decode('Desenvolver ou aprimorar as habilidades de formar, motivar e liderar times de alta performance. '), 0);
		}if($prod == utf8_decode('Programa Times de Alta Performance')){
			
			$section->addText(utf8_decode('COLEÇÃO LIDERANÇA - PROGRAMA TIMES DE ALTA PERFORMANCE'),$pStyleTitulo);
			$section->addText(utf8_decode('O Programa Times de Alta Performance traz técnicas fundamentais para desenvolver equipes extraordinárias, com níveis elevados de desempenho, eficiência, comprometimento e que atinjam os mais altos resultados. Os times são o coração e o motor da empresa, pois são capazes de produzir resultados que os esforços individuais isolados não conseguem gerar. Este programa aborda a essência da formação, do desenvolvimento e da liderança de times de alta performance, por meio do desenvolvimento das competências cruciais para o sucesso. Composto por 8 etapas, o programa aborda conceitos para entender todas as etapas da formação dos “times dos sonhos”, como liderá-los com maestria e assertividade, compreendendo e superando todos os níveis de conflitos que bloqueiam a produtividade das equipes para, assim, atingir o sucesso dos negócios.'),$pStyle);
			$section->addText(utf8_decode('O programa é composto pelos seguintes treinamentos:'),$pStyle);
			
			$section->addListItem(utf8_decode('Treinamento 1 '.htmlspecialchars('-').' Formando seu Dream Team'), 0);
			$section->addListItem(utf8_decode('Treinamento 2 '.htmlspecialchars('-').' Estágios de Desenvolvimento dos Times'), 0);
			$section->addListItem(utf8_decode('Treinamento 3 '.htmlspecialchars('-').' Liderança para Times'), 0);
			$section->addListItem(utf8_decode('Treinamento 4 '.htmlspecialchars('-').' Resolução de Conflitos para Times'), 0);
			$section->addListItem(utf8_decode('Treinamento 5 '.htmlspecialchars('-').' Motivação para Times'), 0);
			$section->addListItem(utf8_decode('Treinamento 6 '.htmlspecialchars('-').' Elevando a Performance e a Produtividade dos Times'), 0);
			$section->addListItem(utf8_decode('Treinamento 7 '.htmlspecialchars('-').' Melhorando a Dinâmica dos Times'), 0);
			$section->addListItem(utf8_decode('Treinamento 8 '.htmlspecialchars('-').' Conduzindo Times à Excelência'), 0);
			
			$section->addText(utf8_decode('Entre os benefícios da Coleção Liderança por SBCoaching®, podemos citar:'),$pStyleTitulo);
			
			$section->addListItem(utf8_decode('Adquirir e dominar um poderoso conjunto de conceitos, técnicas e ferramentas que proporcionam muito mais foco, direcionamento, motivação e autoconfiança, ampliando opções e recursos internos para que as pessoas possam lidar satisfatoriamente com as mais variadas situações.'), 0);
			$section->addListItem(utf8_decode('Ampliar a inteligência emocional, isto é, a habilidade de entender e de lidar com as próprias emoções e com as emoções dos outros, tornando os indivíduos mais adaptáveis e mais capazes de responder com eficácia às demandas do ambiente que os cerca.'), 0);
			$section->addListItem(utf8_decode('Elevar a capacidade de promover mudanças e melhorias contínuas.'), 0);
			$section->addListItem(utf8_decode('Desenvolver e desbloquear o poder pessoal e assumir o controle da própria vida.'), 0);
			$section->addListItem(utf8_decode('Melhorar a habilidade de se relacionar e de interagir, de modo a obter mais apoio, participação e comprometimento, além de potencializar o desenvolvimento das equipes.'), 0);
			$section->addListItem(utf8_decode('Trazer à tona o que se tem de melhor '.htmlspecialchars('-').' e ajudar os outros a fazer o mesmo (sejam eles seus colegas, colaboradores, filhos, familiares...).'), 0);
			$section->addListItem(utf8_decode('Desenvolver ou aprimorar as habilidades de formar, motivar e liderar times de alta performance. '), 0);
				
		}if($prod == utf8_decode('Sessões Career Coaching')){
			$section->addText(utf8_decode('CAREER COACHING'),$pStyleTitulo);

			$section->addText(utf8_decode('Career coaching é um processo que prepara o indivíduo para abordar estrategicamente o planejamento e o desenvolvimento de sua carreira e transitar com sucesso por todas as etapas que compõem sua evolução profissional. '),$pStyle);
			$section->addText(utf8_decode('O foco deste programa é preparar o profissional para enfrentar os desafios em fases de transição profissional '.htmlspecialchars('-').' o que é crucial tanto para sua evolução profissional quanto para a empresa que nele investiu '.htmlspecialchars('-').' por meio do desenvolvimento de competências cruciais para o sucesso. A proposta, aqui, é implementar um programa de onboarding coaching especialmente desenhado para os primeiros 90 dias do executivo em sua nova função, visando garantir uma adaptação bem-sucedida e assegurar ao recém-promovido conquistas iniciais que contribuam para sua consolidação no novo cargo '.htmlspecialchars('-').' e, é claro, abrir caminho para futuras promoções. '),$pStyle);
			$section->addText(utf8_decode('Entre os benefícios podemos citar:'),$pStyle);
			$section->addListItem(utf8_decode('Fornecer ao executivo recém-promovido o apoio e os recursos internos necessários para consolidar-se em sua nova função'), 0);
			$section->addListItem(utf8_decode('Planejar sua carreira de modo mais focado e proativo'), 0);
			$section->addListItem(utf8_decode('Ter mais autoconhecimento, clareza e segurança ao fazer escolhas e tomar decisões cruciais relativas à sua vida profissional'), 0);
			$section->addListItem(utf8_decode('Desenvolver novas competências e promover mudanças comportamentais necessárias para alavancar sua ascensão profissional'), 0);
			$section->addListItem(utf8_decode('Planejar e atravessar com sucesso fases de transição '.htmlspecialchars('-').' inclusive promoções e novas responsabilidades'), 0);
			$section->addListItem(utf8_decode('Superar bloqueios e barreiras que impedem ou dificultam o crescimento profissional'), 0);
			$section->addListItem(utf8_decode('Estabelecer e atingir metas e objetivos relacionados à carreira'), 0);
			$section->addListItem(utf8_decode('Promover o alinhamento dos objetivos profissionais com valores, propósito e missão'), 0);
			$section->addListItem(utf8_decode('Promover equilíbrio/integração entre vida pessoal e trabalho'), 0);
			$section->addListItem(utf8_decode('Ter uma vida profissional mais plena, gratificante e bem-sucedida'), 0);
		}if($prod == utf8_decode('Sessões Alpha Coaching')){
			$section->addText(utf8_decode('ALPHA COACHING'),$pStyleTitulo);
			
			$section->addText(utf8_decode('Alavancar ou equilibrar forças e minimizar riscos é uma questão para o líder. Afinal, as mesmas forças que o levaram ao topo, quando em desequilíbrio, podem levá-lo ao fracasso '.htmlspecialchars('-').' bem como a empresa que ele comanda. Daí a importância do Alpha Coaching, ou o coaching para alfas, desenvolvido a partir do conceito criado por Kate Ludeman e Eddie Erlandson, da Worth Ethic Corporation. '),$pStyle);
			$section->addText(utf8_decode('O conceito tem suas origens na biologia. O termo macho alpha é usado para designar o líder, aquele que se destaca por sua força e habilidade e que, por isso mesmo, exerce um domínio natural sobre os demais '.htmlspecialchars('-').' daí o nome alpha, a primeira letra do alfabeto grego e que indica primazia. Entre os seres humanos, o alpha é definido como uma pessoa que tende a assumir um papel dominante em situações sociais ou profissionais. Com bases nesses princípios, Ludeman e Erlandson passaram a usar a palavra alpha em referência aos indivíduos cuja personalidade transmite poder e autoridade, e que possuem um conjunto específico de características.'),$pStyle);
			$section->addText(utf8_decode('Além de identificar e desenvolver as competências cruciais para o sucesso, conhecer, desenvolver e equilibrar as forças alpha proporciona aos líderes uma série de benefícios, entre os quais podemos citar:'),$pStyle);
			
			$section->addListItem(utf8_decode('Alavancar/equilibrar as forças e minimizar os riscos do líder alpha.'), 0);
			$section->addListItem(utf8_decode('Identificar seu tipo alpha e a prender a tirar o melhor proveito dele '.htmlspecialchars('-').' bem como desenvolver características importantes de outros tipos.'), 0);
			$section->addListItem(utf8_decode('Saber como se relacionar melhorar e obter cooperação de outros alphas.'), 0);
			$section->addListItem(utf8_decode('Reduzir a defensividade e abrir-se para o aprendizado.'), 0);
			$section->addListItem(utf8_decode('Melhorar todo o tipo de trabalho cooperativo.'), 0);
			$section->addListItem(utf8_decode('Entender e saber como aprimorar seu estilo de influência.'), 0);
			$section->addListItem(utf8_decode('Controlar o stress e a impulsividade.'), 0);
		}if($prod == utf8_decode('Sucesso em Administração do Tempo - Presencial')){
			
			$section->addText(utf8_decode('COLEÇÃO SUCESSO EM ADMINISTRAÇÃO DO TEMPO'),$pStyleTitulo);
			
			$section->addText(utf8_decode('Desenvolvido pelo maior especialista em coaching no mundo - Brian Tracy, o treinamento Sucesso em Administração do Tempo é indicado para quem busca técnicas eficazes para aumentar a produtividade em todas as áreas da vida por meio da otimização do tempo. O programa ensina técnicas de planejamento e controle das tarefas para a realização de tarefas com maestria, agilidade e sem stress.'),$pStyle);
			$section->addText(utf8_decode('Desenvolvido pela Brian Tracy International©, o treinamento que compõe a coleção Sucesso por Brian Tracy baseia-se no conceito do laser coaching, que visa não apenas à integração de novos conceitos e informações, mas também à sua aplicabilidade pós-treinamento. Compacto, intenso e diretamente focado em aspectos-chave para o desempenho, o lucro e o êxito da organização, ele oferece resultados rápidos e imediata transferência do aprendizado para o dia a dia do profissional e da organização. '),$pStyle);
			$section->addText(utf8_decode('O treinamento aborda os seguintes tópicos:'),$pStyle);
			$section->addListItem(utf8_decode('A psicologia da administração de tempo'), 0);
			$section->addListItem(utf8_decode('Estabelecendo objetivos estratégicos'), 0);
			$section->addListItem(utf8_decode('Como estabelecer prioridades'), 0);
			$section->addListItem(utf8_decode('Planejamento e organização'), 0);
			$section->addListItem(utf8_decode('Maximizando sua produtividade'), 0);
			$section->addListItem(utf8_decode('Superando a procrastinação'), 0);
			$section->addListItem(utf8_decode('Equilibrando trabalho e família'), 0);
		}if($prod == utf8_decode('Sucesso em Liderança - Presencial')){
			
			$section->addText(utf8_decode('COLEÇÃO SUCESSO EM LIDERANÇA'),$pStyleTitulo);
			
			$section->addText(utf8_decode('O treinamento Sucesso em Liderança, de Brian Tracy, o maior especialista em coaching do mundo, é voltado a quem deseja identificar limitações e as ações necessárias para liderar pessoas com êxito, por meio de técnicas e estratégias rápidas para desenvolver as competências dos líderes de sucesso. O programa indica os padrões comportamentais essenciais para aumentar a performance e, automaticamente, direcionar ao sucesso da liderança, conquistando resultados positivos e estimulando times e equipes.'),$pStyle);
			$section->addText(utf8_decode('Desenvolvido pela Brian Tracy International©, o treinamento que compõe a coleção Sucesso por Brian Tracy baseia-se no conceito do laser coaching, que visa não apenas à integração de novos conceitos e informações, mas também à sua aplicabilidade pós-treinamento. Compacto, intenso e diretamente focado em aspectos-chave para o desempenho, o lucro e o êxito da organização, ele oferece resultados rápidos e imediata transferência do aprendizado para o dia a dia do profissional e da organização. '),$pStyle);
			$section->addText(utf8_decode('O treinamento Sucesso em Liderança aborda os seguintes tópicos:'),$pStyle);
			$section->addListItem(utf8_decode('Tornando-se um líder'), 0);
			$section->addListItem(utf8_decode('Funções chave dos gerentes'), 0);
			$section->addListItem(utf8_decode('Como excelentes líderes comandam'), 0);
			$section->addListItem(utf8_decode('Motivando pessoas para resultados máximos'), 0);
			$section->addListItem(utf8_decode('Montando uma equipe vencedora'), 0);
			$section->addListItem(utf8_decode('Conseguindo o melhor das outras pessoas'), 0);
			$section->addListItem(utf8_decode('Equilibrando toda a sua vida'), 0);
		}if($prod == utf8_decode('Sucesso em Vendas - Presencial')){
			
			$section->addText(utf8_decode('COLEÇÃO SUCESSO EM VENDAS'),$pStyleTitulo);
			$section->addText(utf8_decode('O treinamento Sucesso em Vendas é um treinamento compacto e eficaz para quem deseja potencializar as etapas fundamentais de venda de qualquer produto ou serviço, por meio de técnicas precisas e eficazes que visam identificar as necessidades e elevar a satisfação e a retenção dos clientes e elevar os resultados das equipes de vendas. '),$pStyle);
			$section->addText(utf8_decode('Desenvolvido pela Brian Tracy International©, o treinamento que compõe a coleção Sucesso por Brian Tracy baseia-se no conceito do laser coaching, que visa não apenas à integração de novos conceitos e informações, mas também à sua aplicabilidade pós-treinamento. Compacto, intenso e diretamente focado em aspectos-chave para o desempenho, o lucro e o êxito da organização, ele oferece resultados rápidos e imediata transferência do aprendizado para o dia a dia do profissional e da organização. '),$pStyle);
			$section->addText(utf8_decode('O treinamento aborda os seguintes tópicos:'),$pStyle);
			$section->addListItem(utf8_decode('As novas realidades em vendas'), 0);
			$section->addListItem(utf8_decode('O poder da conquista dos clientes potenciais'), 0);
			$section->addListItem(utf8_decode('Vendas de relacionamento'), 0);
			$section->addListItem(utf8_decode('Identificando necessidades com precisão'), 0);
			$section->addListItem(utf8_decode('Fazendo apresentações convincentes'), 0);
			$section->addListItem(utf8_decode('Superando objeções'), 0);
			$section->addListItem(utf8_decode('Fechando a venda'), 0);
		}if($prod == utf8_decode('Sucesso nos Negócios - Presencial')){

			$section->addText(utf8_decode('COLEÇÃO SUCESSO NOS NEGÓCIOS'),$pStyleTitulo);
			
			$section->addText(utf8_decode('O treinamento Sucesso nos Negócios, desenvolvido por Brian Tracy, o coach mundialmente conhecido por suas técnicas e metodologias assertivas para atingir o sucesso em todos os âmbitos, aborda métodos fundamentais no que diz respeito à dinamização de vendas, captação de clientes, padronização de métodos organizacionais e análise profunda das entradas e saídas de recursos, com o objetivo de elevar os lucros dos negócios, por meio do desenvolvimento das competências essenciais para o sucesso.'),$pStyle);
			$section->addText(utf8_decode('Desenvolvido pela Brian Tracy International©, o treinamento que compõe a coleção Sucesso por Brian Tracy baseia-se no conceito do laser coaching, que visa não apenas à integração de novos conceitos e informações, mas também à sua aplicabilidade pós-treinamento. Compacto, intenso e diretamente focado em aspectos-chave para o desempenho, o lucro e o êxito da organização, ele oferece resultados rápidos e imediata transferência do aprendizado para o dia a dia do profissional e da organização. '),$pStyle);
			$section->addText(utf8_decode('O treinamento aborda os seguintes tópicos:'),$pStyle);
			$section->addListItem(utf8_decode('Comercialize e venda qualquer coisa'), 0);
			$section->addListItem(utf8_decode('Crie seu plano de vendas'), 0);
			$section->addListItem(utf8_decode('Conquistando clientes'), 0);
			$section->addListItem(utf8_decode('Obtendo o capital necessário'), 0);
			$section->addListItem(utf8_decode('Crie seus sistemas de negócios'), 0);
			$section->addListItem(utf8_decode('Atendendo seu cliente'), 0);
			$section->addListItem(utf8_decode('Aumente seus lucros!'), 0);
		}if($prod == utf8_decode('Sucesso Pessoal - Presencial')){

			$section->addText(utf8_decode('COLEÇÃO SUCESSO PESSOAL'),$pStyleTitulo);
			
			$section->addText(utf8_decode('O treinamento Sucesso Pessoal é indicado às pessoas que queiram assumir o controle de suas vidas e, consequentemente, desfrutar de bem-estar, qualidade de vida, plenitude e grandes realizações, por meio do desenvolvimento das competências cruciais para o sucesso em todas as esferas da vida. '),$pStyle);
			$section->addText(utf8_decode('Desenvolvido pela Brian Tracy International©, o treinamento que compõe a coleção Sucesso por Brian Tracy baseia-se no conceito do laser coaching, que visa não apenas à integração de novos conceitos e informações, mas também à sua aplicabilidade pós-treinamento. Compacto, intenso e diretamente focado em aspectos-chave para o desempenho, o lucro e o êxito da organização, ele oferece resultados rápidos e imediata transferência do aprendizado para o dia a dia do profissional e da organização. '),$pStyle);
			$section->addText(utf8_decode('O treinamento aborda os seguintes tópicos:'),$pStyle);
			$section->addListItem(utf8_decode('As chaves para a máxima performance'), 0);
			$section->addListItem(utf8_decode('Autoestima, a chave mestra da máxima performance'), 0);
			$section->addListItem(utf8_decode('Assumindo o controle de sua vida'), 0);
			$section->addListItem(utf8_decode('A habilidade principal para o sucesso'), 0);
			$section->addListItem(utf8_decode('Sete passos para realização de objetivos'), 0);
			$section->addListItem(utf8_decode('O princípio integrador'), 0);
			$section->addListItem(utf8_decode('Alcançando o equilíbrio na vida'), 0);
				
		}if($prod == utf8_decode('Certified Professional Coaching')){

			$section->addText(utf8_decode('CERTIFIED PROFESSIONAL COACHING©'),$pStyleTitulo);
			
			$section->addText(utf8_decode('O treinamento Certified Professional Coaching© destina-se à formação de coaches internos, de líderes coaches e de profissionais que irão desenvolver e utilizar as competências cruciais para promover uma drástica transfor¬mação '.htmlspecialchars('-').' tanto de seus resultados quanto dos resultados organizacionais. O treinamento se destina a empresas que querem desenvolver seus líderes, times, equipes de vendas, pessoal de RH e pro¬fissionais em geral a fim de elevar performance, resultados e lucratividade, por meio de módulos presenciais e em e-learning.'),$pStyle);
			$section->addText(utf8_decode('Entre as principais competências desenvolvidas com o Certified Professional Coaching© estão aquelas que são consideradas cruciais para o líder e para o profissional de sucesso, como:'),$pStyle);
			$section->addText(utf8_decode('Ter foco em resultados, proatividade e orientação para a ação'),$pStyle);
			$section->addText(utf8_decode('Promover o engajamento e elevar a motivação'),$pStyle);
			$section->addText(utf8_decode('Promover mudanças sustentáveis e desenvolver o capital humano'),$pStyle);
			$section->addText(utf8_decode('Definir metas e objetivos poderosos, bem como planos de ação altamente eficazes para que os objetivos sejam rapidamente atingidos'),$pStyle);
			$section->addText(utf8_decode('Gerar e manter relacionamentos positivos, parcerias e alianças'),$pStyle);
			$section->addText(utf8_decode('Possuir assertividade, capacidade de persuasão e habilidade de dar feedbacks capazes de estimular a compreensão e a cooperação'),$pStyle);
			$section->addText(utf8_decode('Dominar a administração de conflitos e resolução de problemas'),$pStyle);
			$section->addText(utf8_decode('Gerenciar o stress'),$pStyle);
			$section->addText(utf8_decode('Atuar ou liderar produtivamente todo o tipo de pessoas '.htmlspecialchars('-').' inclusive as con¬sideradas “difíceis”'),$pStyle);
			$section->addText(utf8_decode('Gerir o tempo de modo a aproveitá-lo ao máximo'),$pStyle);
			$section->addText(utf8_decode('Dominar a arte de delegar tarefas e duplicar sua produtividade '),$pStyle);
			$section->addText(utf8_decode('Realizar reuniões produtivas, focadas e extremante eficazes'),$pStyle);
			$section->addText(utf8_decode('Desenvolver o poder da visão, comunicar-se para vencer e tornar-se um líder extraordinário'),$pStyle);
		}
		
	}
	
	
	$section->addText(utf8_decode('Ressalvamos que os trabalhos praticados pela SBCoaching Empresas tem por objetivo o desenvolvimento, aprimoramento e aumento de performance do capital humano, e que os ações a serem realizadas demandam a interação e colaboração entre nossos coaches e o cliente.'),$pStyle);
	$section->addText(utf8_decode('METODOLOGIA'),$pStyleTitulo);
	
	$section->addText(utf8_decode('A Metodologia da SBCoaching é uma das melhores do mundo e a número 1 no Brasil e podemos ajuda a {nome_empresa} vencer seus desafios.'),$pStyle);
	$section->addListItem(utf8_decode('Nosso método é desenvolvido em 4 etapas, são elas:'),0);
		$section->addListItem(utf8_decode('Etapa 1: Avaliação e Planejamento'),1);
		$section->addListItem(utf8_decode('Etapa 2: Coaching e Mudança'),1);
		$section->addListItem(utf8_decode('Etapa 3: Avaliação e Resultado'),1);
		$section->addListItem(utf8_decode('Etapa 4: Melhoria e Manutenção'),1);
		
	$section->addListItem(utf8_decode('Neste contexto, trabalhamos as etapas descritas acima de três formas:'),0);
		$section->addListItem(utf8_decode('a. Treinamento'),1);
		$section->addListItem(utf8_decode('b. Tecnologia'),1);
		$section->addListItem(utf8_decode('c. Coaching'),1);
		
	
	$section->addText(utf8_decode('Possibilitamos ainda, que o cliente opte por uma ou mais formas de trabalhar as etapas de desenvolvimento do capital humano de acordo com suas necessidades.'),$pStyle);
	$section->addImage('frameworksbcoaching.jpg', array('width'=>300, 'height'=>300, 'align'=>'center'));
	
	
	$section->addText(utf8_decode('Organização do Trabalho'),$pStyleTitulo);
	
	$section->addText(utf8_decode('O trabalho delineado será desenvolvido da seguintes forma:'),$pStyleTitulo);
	
	$objTextRun = $section->createTextRun();
	$objTextRun->addText(utf8_decode('O Programa'), 'pStyle');
	$objTextRun->addText(utf8_decode(' será desenvolvido no contexto a seguir:'), 'pStyle');
	$section->addText($objTextRun);

	$sqlLinha = "select * from is_produto_linha where numreg IN($idLinha)";
// 	echo $sqlLinha ;
// 	exit;
	$qryLinha = mysql_query($sqlLinha);
	while($arLInha = mysql_fetch_assoc($qryLinha)){
		if($arLInha['nome_produto_linha'] == 'CPC' ){
			
			$section->addText(utf8_decode('Conteúdo Programatico Certified Professional Coaching'),$pStyleTitulo);
			
			$section->addListItem(utf8_decode('Carga horária total '.htmlspecialchars('-').' 100 horas, distribuídas da seguinte forma:'), 0);
			$section->addListItem(utf8_decode('Presencial: 40 horas-aula, distribuídas em 4 dias de treinamento intenso e transformacional. '), 0);
			$section->addListItem(utf8_decode('E-learnings: 40 horas, distribuídas em 4 módulos, com 8 e-learnings cada (total = 32 e-learnings). '), 0);
			$section->addListItem(utf8_decode('Videotraining: 20 horas dos treinamentos compactos em vídeo de Brian Tracy (Sucesso em Liderança e Sucesso em Administração do Tempo). '), 0);
			$section->addListItem(utf8_decode('Projeto de certificação: Sessões conduzidas durante o treinamento.'), 0);
			$section->addText(utf8_decode('Qual é a programação do treinamento?'),$pStyle );
			
			$section->addText(utf8_decode('Treinamento presencial'),$pStyleTitulo );
			$section->addText(utf8_decode('Núcleo do programa de formação de coaching, este treinamento presencial intenso e dinâmico aborda os principais conceitos e fundamentos do coaching, assim como as técnicas e ferramentas, e promove a prática e o desenvolvimento de competências por meio de metodologia única e exclusiva, com comprovação de resultados e apoiada em processos de aprendizagem acelerada.'),$pStyle );
			$section->addText(utf8_decode('Composição:'),$pStyle );
			$section->addListItem(utf8_decode('Conceitos, fundamentos básicos e avançados de coaching.'), 0);
			$section->addListItem(utf8_decode('Técnicas e ferramentas com comprovação científica.'), 0);
			$section->addListItem(utf8_decode('Modelos e frameworks de coaching.'), 0);
			$section->addListItem(utf8_decode('Metodologia exclusiva de coaching e ensino da SBC®. '), 0);
			$section->addListItem(utf8_decode('Desenvolvimento de competências do coach.'), 0);
			$section->addListItem(utf8_decode('Desenvolvimento de liderança. '), 0);
			$section->addListItem(utf8_decode('Temas inspiracionais e atuais sobre o coaching, suas aplicações e benefícios.'), 0);
			$section->addText(utf8_decode('Metodologia, técnicas e ferramentas:'),$pStyle );
			$section->addText(utf8_decode('Nossa metodologia exclusiva é apresentada no treinamento por meio de roteiros, técnicas, ferramentas e práticas sobre temas como: aumento de foco, definição de metas e objetivos; planejamento e administração do tempo; estímulo à ação, transformação e aumento de performance; desenvolvimento de novos comportamentos e competências;  estímulo à ação eficaz do líder; estímulo à ação eficaz do profissional de RH e de profissionais em geral; estimulo à atuação do coach interno; melhoria contínua visando a evolução progressiva.'),$pStyle );
			$section->addText(utf8_decode('Tudo isso acompanhado de roteiros para intervenções avançadas de coaching, desenvolvidos com exclusividade pela SBCoaching®, para atuar com: análise de problemas e tomada de decisões; controle de estados emocionais; identificação e mudança de crenças limitantes, ideias fixas ou estratégias de pensamento limitadoras;  e desenvolvimento de novos comportamentos, competências ou estratégias de pensamento, sentimento e ação.'),$pStyle );
			$section->addText(utf8_decode('O módulo presencial do treinamento também inclui técnicas de coaching para aumento de comprometimento; de expectativa e de motivação;  desenvolvimento de estratégias mentais; e criatividade para construção de cenários '.htmlspecialchars('-').' entre outras.'),$pStyle );
			
			$section->addText(utf8_decode('Módulos em e-learning'),$pStyleTitulo );
			$section->addText(utf8_decode('Para facilitar seu aprendizado, e também para permitir que você otimize seu tempo, nossos e-learnings foram desenvolvidos de acordo com o sistema self-paced. Isto significa que você mesmo define seu ritmo. Você pode fazer cada um deles em até uma hora, ou levar mais ou menos tempo '.htmlspecialchars('-').' você define a velocidade que mais se adapta ao seu estilo de aprendizado.'),$pStyle );
			
			$section->addText(utf8_decode('Módulo I: Teoria, Conceitos e Processos do Coaching'),$pStyleTitulo );
			$section->addText(utf8_decode('Este módulo oferece ampla, porém focada introdução aos fundamentos e processos do coaching '.htmlspecialchars('-').' definições, estilos, fundamentação e modelos teóricos, vantagens e benefícios, coaching e liderança e muito mais.'),$pStyle );
			$section->addListItem(utf8_decode('1. Benefícios e vantagens do coaching '), 0);
			$section->addListItem(utf8_decode('2. O que é e o que não é coaching'), 0);
			$section->addListItem(utf8_decode('3. Fundamentação teórica e comprovação científica do coaching'), 0);
			$section->addListItem(utf8_decode('4. O poder das perguntas no processo de coaching'), 0);
			$section->addListItem(utf8_decode('5. Passos para o sucesso do coach '), 0);
			$section->addListItem(utf8_decode('6. Coaching e liderança '), 0);
			$section->addListItem(utf8_decode('7. Desenvolva-se como líder '), 0);
			$section->addListItem(utf8_decode('8. Torne-se um líder extraordinário'), 0);
			
			$section->addText(utf8_decode('Módulo II: Competências do Coach'),$pStyleTitulo );
			$section->addText(utf8_decode('Módulo focado na performance do coach, especialmente desenhado para que você possa conhecer e começar a desenvolver as competências necessárias para ser um excelente coach.'),$pStyle );
			$section->addListItem(utf8_decode('1. Competências básicas do coach'), 0);
			$section->addListItem(utf8_decode('2. Competências de comunicação'), 0);
			$section->addListItem(utf8_decode('3. Competências emocionais'), 0);
			$section->addListItem(utf8_decode('4. Qualidades de um excelente coach'), 0);
			$section->addListItem(utf8_decode('5. Autocoaching para desenvolvimento de competências'), 0);
			$section->addListItem(utf8_decode('6. Peer coaching para desenvolvimento de competências'), 0);
			$section->addListItem(utf8_decode('7. Administração de conflitos '), 0);
			$section->addListItem(utf8_decode('8. Gerenciamento do stress'), 0);
			
			$section->addText(utf8_decode('Módulo III: Líder Coach'),$pStyleTitulo );
			$section->addText(utf8_decode('O ponto forte do líder coach é desenvolver-se continuamente e trazer à tona o que as pessoas têm de melhor, e esse é um dos resultados que você obterá ao utilizar os conceitos, as técnicas e as ferramentas deste módulo.'),$pStyle );
			$section->addListItem(utf8_decode('1. Seja um líder coach '), 0);
			$section->addListItem(utf8_decode('2. Definindo metas e objetivos '), 0);
			$section->addListItem(utf8_decode('3. Desenvolvendo novas competências '), 0);
			$section->addListItem(utf8_decode('4. Promovendo mudanças comportamentais'), 0);
			$section->addListItem(utf8_decode('5. Feedback e follow-up '), 0);
			$section->addListItem(utf8_decode('6. Foco em soluções '), 0);
			$section->addListItem(utf8_decode('7. Faça autocoaching'), 0);
			$section->addListItem(utf8_decode('8. O líder como modelo'), 0);
			
			$section->addText(utf8_decode('Módulo IV '.htmlspecialchars('-').' Estratégias e Habilidades '),$pStyleTitulo );
			$section->addText(utf8_decode('Este módulo apresenta conceitos e técnicas fundamentais para que você possa utilizar o coaching com a máxima eficácia, seja para atuar como coach interno, seja para de- senvolver-se como líder ou como profissional.'),$pStyle );
			$section->addListItem(utf8_decode('1. Avaliações e perfis'), 0);
			$section->addListItem(utf8_decode('2. Trabalhando com pessoas difíceis'), 0);
			$section->addListItem(utf8_decode('3. Gestão do tempo '), 0);
			$section->addListItem(utf8_decode('4. Resolução de problemas '), 0);
			$section->addListItem(utf8_decode('5. Delegação de tarefas '), 0);
			$section->addListItem(utf8_decode('6. Reuniões eficazes '), 0);
			$section->addListItem(utf8_decode('7. Desenvolva o poder da visão '), 0);
			$section->addListItem(utf8_decode('8. Comunique-se para vencer'), 0);
		}if($arLInha['nome_produto_linha'] == utf8_decode('Coleção Liderança por SBCoaching') ){
			
			$section->addText(utf8_decode('Coleção Liderança por SBCoaching'),$pStyleTitulo );
			
			$section->addText(utf8_decode('A Coleção Liderança é composta por 4 programas, cada qual contendo 8 treinamentos em e-learning e 4 livros de apoio com as transcrições dos e-learnings. A coleção aborda os seguintes temas:'),$pStyle );
			
			$section->addText(utf8_decode('Conteúdo Programático Programa Competências de Liderança '),$pStyleTitulo );
				$section->addText(utf8_decode('Treinamento 1 '.htmlspecialchars('-').' Gestão do Tempo'),$pStyle );
				$section->addText(utf8_decode('Este treinamento foi especialmente elaborado para que você domine as técnicas e ferramentas necessárias para gerir seu tempo com a máxima eficácia. Você aprenderá a evitar as armadilhas do workaholismo e a organizar suas atividades de modo mais saudável e produtivo. Além disso, será capaz de priorizar e alcançar seus objetivos, fechar os drenos que sugam seu tempo e aumentar sua produtividade.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 2 '.htmlspecialchars('-').' Gerenciamento do Stress'),$pStyle );
				$section->addText(utf8_decode('As técnicas ensinadas em gerenciamento do stress permitem que você produza mais e viva melhor. Além disso, quando o líder aprende a controlar seu stress, afeta positivamente todos ao redor. Para gerenciar o stress, você receberá inúmeras informações sobre o que é e quais são as causas desse mal. Desse modo, ficará mais atento para identificar os sinais de alerta que indicam o seu nível de stress e as estratégias de recomposição para recuperar sua energia e elevar o seu bem-estar.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 3 '.htmlspecialchars('-').' Delegação de Tarefas'),$pStyle );
				$section->addText(utf8_decode('Nesta etapa, são fornecidas instruções fundamentais para promover com eficácia a delegação de tarefas, uma ferramenta essencial para evitar que o líder fique sobrecarregado, garantir a fluidez e a agilidade dos processos e também para estimular o aprendizado e a performance da equipe. Neste treinamento, você vai aprender a superar os obstáculos que dificultam a delegação, os 7 passos da delegação eficaz e a relação entre delegar tarefas e delegar autoridade.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 4 '.htmlspecialchars('-').' Tomada de Decisão'),$pStyle );
				$section->addText(utf8_decode('Neste treinamento são abordados os conceitos-chave para você desenvolver e aprimorar a habilidade de tomar decisões para atingir os melhores resultados em sua vida e em seus negócios. Nos tópicos, levantam-se aspectos cruciais para a tomada de decisão eficiente, os quais incluem: como a razão e a emoção influenciam suas decisões, técnica analítica de tomada de decisão, e técnica para decidir sob pressão.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 5 '.htmlspecialchars('-').' Resolução de Problemas'),$pStyle );
				$section->addText(utf8_decode('Neste treinamento, você aprenderá técnicas para estabelecer um ritmo altamente eficaz na condução e resolução de seus problemas, permitindo, assim,  que você atue com mais propriedade e precisão em face de todas as suas atribuições. A fim de que você desenvolva estratégias infalíveis para solucionar seus problemas, são ensinadas técnicas como: Lei de Pareto na resolução de problemas; técnicas multimodais e estilos de resolução.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 6 '.htmlspecialchars('-').' Administração de Conflitos'),$pStyle );
				$section->addText(utf8_decode('Por meio deste treinamento, você vai desenvolver e aperfeiçoar as competências necessárias para prevenir e administrar conflitos de maneira mais precisa e eficaz e aprender a converter a energia gerada em situações adversas em uma força criativa a serviço do crescimento, da inovação e dos resultados organizacionais; além de descobrir o que deflagra as divergências e como agir em cada situação.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 7 '.htmlspecialchars('-').' Apresentações em Público'),$pStyle );
				$section->addText(utf8_decode('Este treinamento o ajudará a desenvolver um atributo fundamental para o líder de sucesso: a competência de fazer apresentações impactantes e de alto nível. Você aprenderá a planejar, estruturar, preparar e realizar sua apresentação, organizar o conteúdo e desenvolver a apresentação certa para cada finalidade.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 8 '.htmlspecialchars('-').' Reuniões Eficazes'),$pStyle );
				$section->addText(utf8_decode('Você gasta mais tempo do que deveria com reuniões de trabalho? Acredita que elas são desgastantes e geram poucos resultados? Por meio deste treinamento, você se tornará significativamente mais apto para realizar reuniões mais produtivas e realmente voltadas aos objetivos. Você aprenderá a preparar, conduzir e finalizar reuniões, lidar com pessoas difíceis, que podem emperrar o fluxo das reuniões, e os segredos de um bom condutor de reuniões.'),$pStyle );
				$section->addText(utf8_decode('Programa Estratégias de Liderança'),$pStyle );
				$section->addText(utf8_decode('Treinamento 1 '.htmlspecialchars('-').' Desenvolva-se como Líder'),$pStyle );
				$section->addText(utf8_decode('Você vai aprender técnicas de autodesenvolvimento para aprimorar suas habilidades de lide- rança e elevar sua performance e seus resultados. Os temas abordados incluem o autodesen- volvimento como estratégia pessoal e organizacional, obstáculos ao autodesenvolvimento '.htmlspecialchars('-').' e como superá-los –, técnica para desenvolver um plano de autodesenvolvimento.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 2 '.htmlspecialchars('-').' Desenvolva o Poder da Visão'),$pStyle );
				$section->addText(utf8_decode('Você vai aprender a criar e a compartilhar uma visão realmente poderosa, capaz de ajudá-lo a construir o futuro que você deseja '.htmlspecialchars('-').' para si mesmo e para o seu negócio. O conteúdo inclui diferença entre missão e visão, visão e liderança e traz orientações de como se tornar um líder visionário '.htmlspecialchars('-').' entre outros temas.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 3 '.htmlspecialchars('-').' Comunique-se para Vencer'),$pStyle );
				$section->addText(utf8_decode('A liderança se realiza em sua plenitude por meio da comunicação. Um líder está sempre se co- municando com o público interno da empresa, para garantir que as as tarefas sejam executadas, e com o público externo, para garantir um bom relacionamento com clientes, fornecedores e a sociedade em geral. Neste treinamento, você aprenderá a utilizar a comunicação como estratégia para tornar-se um líder vencedor, de uma empresa vencedora.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 4 '.htmlspecialchars('-').' Use as Forças Alpha'),$pStyle );
				$section->addText(utf8_decode('Você é um líder arrojado, dinâmico e voltado para resultados? Você se destaca por sua capacidade de  liderança e foco no objetivo? Pessoas com essas características fazem parte de um grupo muito especial: o dos líderes alpha. Neste treinamento, você aprenderá a trabalhar as características positivas de seu perfil alpha e minimizar os riscos que esse estilo pode trazer.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 5 '.htmlspecialchars('-').' Torne-se um Líder Extraordinário'),$pStyle );
				$section->addText(utf8_decode('Neste treinamento você conhecerá os segredos e as ferramentas fundamentais para se tornar um líder extraordinário, o que inclui, entre outros temas, o que é liderança centrada, as ferramentas de um líder excepcional e as técnicas para desenvolver uma abordagem positiva.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 6 '.htmlspecialchars('-').' Desenvolva o Capital Humano'),$pStyle );
				$section->addText(utf8_decode('Este treinamento fornece instruções essenciais e esclarecedoras a respeito do desenvolvimento do capital humano, garantia do sucesso e da durabilidade de uma organização. Aqui, você aprenderá a desenvolver o capital humano para assegurar o aumento da competitividade, da performance e dos resultados de sua empresa.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 7 '.htmlspecialchars('-').' Forme Novas Lideranças'),$pStyle );
				$section->addText(utf8_decode('Este treinamento o ajudará a dominar um processo fundamental para o presente e para o futuro de sua empresa: identificar e desenvolver novas lideranças. Você aprenderá a identificar líderes em potencial e a utilizar as estratégias certas para conduzi-los ao próximo nível.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 8 '.htmlspecialchars('-').' Gerencie Mudanças'),$pStyle );
				$section->addText(utf8_decode('Você vai aprender a desenvolver uma estratégia vencedora para implementar com sucesso as mudanças de que sua empresa tanto precisa. O conteúdo inclui o que é gestão de mudança, quando e por que mudar, os 8 passos de uma mudança eficaz e como superar resistências.'),$pStyle );
			
			$section->addText(utf8_decode('Conteúdo Programático Programa Líder Coach'),$pStyleTitulo );
				$section->addText(utf8_decode('Treinamento 1 '.htmlspecialchars('-').' Seja um Líder Coach'),$pStyle );
				$section->addText(utf8_decode('Para atingir e consolidar o sucesso num mundo dinâmico e em constante transformação, o coaching é, para o líder, uma ferramenta fundamental. Ao tornar-se um líder coach, você estará habilitado a aplicar competências de coaching para: prover mais clareza, contexto e direcionamento '.htmlspecialchars('-').' para si mesmo e para os outros '.htmlspecialchars('-').' motivar e trazer à tona o que as pessoas têm de melhor, tornar-se uma po- derosa fonte de influência positiva e transformadora '.htmlspecialchars('-').' e muito mais.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 2 '.htmlspecialchars('-').' Definindo Metas e Objetivos'),$pStyle );
				$section->addText(utf8_decode('Tudo o que você quer ser, ter, fazer e realizar em sua vida, em sua carreira e em seus negócios de- pende da boa formulação de metas e objetivos. Por meio deste treinamento, você se tornará capaz de formular corretamente objetivos e metas de modo a potencializar suas chances de atingi-los.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 3 '.htmlspecialchars('-').' Desenvolvendo Novas Competências'),$pStyle );
				$section->addText(utf8_decode('Pense em tudo o que você quer obter em sua vida e em seus negócios. O caminho para o sucesso passa pela aquisição de competências estratégicas para atingir seus objetivos. Neste treinamento,você vai aprender a identificar, desenvolver e colocar em prática as competências necessárias para conquistar tudo aquilo com que você sempre sonhou.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 4 '.htmlspecialchars('-').' Promovendo Mudanças Comportamentais'),$pStyle );
				$section->addText(utf8_decode('Muitos dos resultados insatisfatórios que você está obtendo podem estar sendo gerados por comportamentos improdutivos. Suas ações exercem um impacto direto nos seus resultados. E a eficácia dessas ações é, em grande parte, determinada por seu comportamento. O conteúdo deste treinamento traça as diretrizes essenciais para você aprender a promover mudanças comportamentais duradouras e sustentáveis, capazes de aumentar drasticamente os resultados que você busca alcançar.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 5 '.htmlspecialchars('-').' Feedback e Follow-up'),$pStyle );
				$section->addText(utf8_decode('Este treinamento amplia sua habilidade para dar feedback, fazer follow-up e utilizar com a máxima eficácia essas poderosas ferramentas para otimizar o aprendizado e aumentar a performance de sua equipe. Além disso, você se tornará apto a proporcionar as respostas mais eficazes para acelerar o desenvolvimento de seus colaboradores; corrigir o que não funciona e estimular comportamentos mais proativos e eficazes.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 6 '.htmlspecialchars('-').' Foco em Soluções'),$pStyle );
				$section->addText(utf8_decode('O foco em soluções compreende um conjunto de técnicas e de estratégias que promovem uma mudança significativa no modo como problemas e desafios são percebidos e abordados. Essa mudança propicia uma postura muito mais proativa e voltada para resultados, o que se traduz em ganhos consideráveis em termos de produtividade, performance, tempo e outros recursos.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 7 '.htmlspecialchars('-').' Faça Autocoaching'),$pStyle );
				$section->addText(utf8_decode('Este treinamento fornece as ferramentas necessárias para promover o autocoaching e utilizar as poderosas técnicas do coaching em sua vida, em sua carreira e em seu negócio. E com isso superar bloqueios, atingir objetivos, resolver problemas, promover mudanças e obter muito mais resultados. Tudo isso é possível porque as técnicas do autocoaching trabalham três aspectos fundamentais para atingir o equilíbrio, a felicidade e o sucesso. Esses aspectos consistem em: formular e estruturar pensamentos e raciocínios positivos, lidar de modo mais produtivo com suas emoções e descobrir, desenvolver e utilizar todo o seu potencial.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 8 '.htmlspecialchars('-').' O líder Coach como Modelo'),$pStyle );
				$section->addText(utf8_decode('Um dos papéis mais importantes do líder é ser um modelo de atitudes e comportamentos que ele deseja despertar em outras pessoas. Para ser um modelo, o líder deve dar o exemplo. Ao dar o exemplo, o líder torna-se uma referência e inspira as pessoas a buscar a excelência e a dar o melhor de si. Com este treinamento você aprenderá como aumentar a influência e o impacto que você exerce sobre seus funcionários e colaboradores, elevando sua capacidade de potencializar, desenvolver, energizar e inspirar sua equipe.'),$pStyle );
				
			$section->addText(utf8_decode('Conteúdo Programático Programa Times de Alta Performance'),$pStyleTitulo );
				$section->addText(utf8_decode('Treinamento 1 '.htmlspecialchars('-').' Formando seu Dream Team'),$pStyle );
				$section->addText(utf8_decode('No mundo corporativo, dream team passou a designar um tipo muito especial de equipe: o time de alta performance. Isto é, um time que obtém resultados extraordinários. O time de alta performance atua num nível muito mais elevado de desempenho, comprometimento e resultados. Neste treinamento, você aprenderá a formar um time capaz de elevar ainda mais os lucros e os resultados de sua organização.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 2 '.htmlspecialchars('-').' Estágios de Desenvolvimento dos Times'),$pStyle );
				$section->addText(utf8_decode('Ao aplicar os conceitos deste treinamento, você vai aprender a lidar com uma ferramenta fundamental para desenvolver times coesos, eficazes e voltados para resultados. Você conhecerá os Estágios de Desenvolvimento dos Times e descobrir como atuar em cada um deles para conduzir sua equipe a picos de performance cada vez mais elevados.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 3 '.htmlspecialchars('-').' Liderança para Times'),$pStyle );
				$section->addText(utf8_decode('O sucesso dos times está diretamente relacionado com a liderança. Um excelente líder cresce com o time e faz seu time crescer. Neste treinamento, você aprenderá conceitos e técnicas de liderança para times de alta performance, os quais foram extraídos de estudos realizados com mais de 2 mil equipes em 40 países.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 4 '.htmlspecialchars('-').' Resolução de Conflitos para Times'),$pStyle );
				$section->addText(utf8_decode('Mais de 65% dos problemas de performance estão ligados aos conflitos, não às habilidades dos funcionários. Logo, menos conflito corresponde a mais performance. Neste treinamento,você aprenderá a identificar diferentes tipos de conflitos, suas causas e também a aplicar as técnicas mais eficazes para solucioná-los e aumentar o engajamento e a cooperação entre os membros de seu time.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 5 '.htmlspecialchars('-').' Motivação para Times'),$pStyle );
				$section->addText(utf8_decode('Motivação é um processo interno que energiza, direciona e sustenta o comportamento de cada indivíduo. Um funcionário engajado, que supera expectativas, é uma pessoa altamente motivada. A partir de estudos realizados com mais de 4 milhões de funcionários em todo o mundo, foram levantados os princípios para que você se torne um expert em um dos atributos mais importantes do líder: a habilidade de motivar seu time para mantê-lo coeso, engajado e ainda mais eficaz.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 6 '.htmlspecialchars('-').' Elevando a Performance e a Produtividade dos Times'),$pStyle );
				$section->addText(utf8_decode('O resultado que um time apresenta é a soma de sua produtividade e de sua performance. E a liderança desempenha um papel fundamental para que essa soma seja capaz de atingir '.htmlspecialchars('-').' ou mesmo superar '.htmlspecialchars('-').' expectativas. Este treinamento foi especialmente elaborado para que você domine as técnicas necessárias para identificar e solucionar problemas que estão comprometendo a performance de sua equipe e conduzi-la a dar um grande salto em termos de qualidade e produtividade.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 7 '.htmlspecialchars('-').' Melhorando a Dinâmica dos Times'),$pStyle );
				$section->addText(utf8_decode('A produtividade e a eficiência de grupos e equipes estão relacionadas não apenas à competência de seus membros, mas, principalmente, à qualidade de suas relações interpessoais. Quanto melhor for a dinâmica do time, melhor será a relação entre seus integrantes e, consequentemente, melhores serão os resultados que o time obtém. Neste treinamento, você entenderá o que é a dinâmica e como aperfeicoá-la.'),$pStyle );
				$section->addText(utf8_decode('Treinamento 8 '.htmlspecialchars('-').' Conduzindo Times à Excelência'),$pStyle );
				$section->addText(utf8_decode('A excelência é atingida por times extraordinários, ou seja, times de alta performance capazes de produzir uma poderosa energia positiva e de superar continuamente as expectativas. Um time extraordinário vai além do modo tradicional de exercer suas funções e cria um nível inusitadamente elevado de resultados positivos. Neste treinamento, você saberá como o líder pode impulsionar o time a obter resultados consistentes de maneira contínua.'),$pStyle );
		}if($arLInha['nome_produto_linha'] == utf8_decode('Serviços de Coaching') ){
			
			$section->addText(utf8_decode('Conteúdo Programático Programa Serviços de Coaching'),$pStyleTitulo );
				$section->addText(utf8_decode('Fase I: Avaliação & Planejamento'),$pStyle );
				$section->addText(utf8_decode('Nesta fase são realizadas as avaliações iniciais e o levantamento de dados sobre o cliente. O objetivo dessas sessões é proporcionar ao coach a prospecção das informações essenciais para desenvolver uma proposta de serviços capaz de agregar valor e de ir plenamente ao encontro das necessidades e expectativas do contratante e do coachee (ou coachees).'),$pStyle );
				$section->addText(utf8_decode('Fase II: Coaching & Mudança'),$pStyle );
				$section->addText(utf8_decode('A fase de Coaching & Mudança é o núcleo do programa Executive Coaching. O objetivo principal é promover aumento de resultados e desempenho do executivo e a consequente melhoria nos resultados do negócio. Os benefícios são diversos, e incluem o aumento do autoconhecimento; uso de forças e talentos; aumento de foco; planejamento e clareza de objetivos e propósito; melhoria de desempenho nas competências de liderança, capacidades organizacionais e habilidades na gestão do negócio; melhoria no processo de tomada de decisão; boa forma cognitiva; desenvolvimento e potencialização do estilo de liderança e inﬂuência; melhoria na comunicação; trabalho em equipe e com os pares; resolução de problemas, mediação, negociação; entre outros.'),$pStyle );
				$section->addText(utf8_decode('Fase III: Avaliação & Resultados'),$pStyle );
				$section->addText(utf8_decode('Nesta fase chegou a hora de medir, avaliar e mostrar o que foi feito e os resultados alcançados. O objetivo é identificar e apresentar as mudanças, melhorias e impacto gerados pelo processo de coaching, além de detectar o que ainda pode ser melhorado e deﬁnir os próximos passos. Aqui, mediremos o quanto as expectativas e objetivos do contratante e do(s) coachee(s) foram atingidos.'),$pStyle );
				$section->addText(utf8_decode('Fase IV: Melhoria & Manutenção'),$pStyle );
				$section->addText(utf8_decode('O processo é concluído com sessões de reforço e manutenção visando a melhoria contínua. Os resultados são avaliados e apresentados e o coach faz suas recomendações quanto a outros programas ou treinamentos que possam suprir necessidades especíﬁcas detectadas durante o coaching ou para aumentar ainda mais os resultados do contratante/coachee.'),$pStyle );
		}if($arLInha['nome_produto_linha'] == 'Business' ){
			$section->addText(utf8_decode('Conteúdo Programático Business'),$pStyleTitulo );
				$section->addText(utf8_decode('O programa é formado por 5 módulos que totalizam 72 sessões de business coaching. A realização de todas as sessões pode levar, em média, em torno de 18 meses. Conheça agora os temas abordados em cada módulo e as sessões que os compõem.'),$pStyle );
				$section->addText(utf8_decode('Módulo 1: Ganhe Poder por meio da Clareza'),$pStyle );
				$section->addText(utf8_decode('Neste módulo, você vai aprender a desenvolver absoluta clareza no que se refere a si mesmo, seus objetivos pessoais e profissionais, suas áreas de excelência e possíveis bloqueios, e também em relação ao seu negócio: onde você está, quem são seus concorrentes, quais são seus desafios, para onde você vai e como chegar lá. Por meio de sessões de coaching com foco no aumento do autoconhecimento, você irá valer-se do poder da clareza para ganhar tempo e otimizar recursos; utilizar suas forças com mais foco e eficácia; entender melhor a si mesmo, melhorar seus relacionamentos e reduzir seu stress; focar seu tempo e energia no que lhe trará mais resultados; alcançar mais objetivos pessoais, profissionais e do negócio em menos tempo.'),$pStyle );
				$section->addText(utf8_decode('Módulo 2: Aumente sua eficácia'),$pStyle );
				$section->addText(utf8_decode('Neste módulo, você aplicará técnicas mensuráveis e imprescindíveis de avaliação e alavancagem da eficácia em todas as esferas do seu negócio. Visando aperfeiçoar procedimentos e resultados obtidos por meio da administração eficaz de tempo, atenção e energia, além de princípios orientadores voltados a estimular atitudes mais assertivas, você e sua equipe atingirão altos níveis de produtividade tirando o máximo proveito do tempo, apurando o enfoque dos processos e desenvolvendo a habilidade de simplificar procedimentos para obter melhores dividendos.'),$pStyle );
				$section->addText(utf8_decode('Módulo 3: Amplie seus negócios'),$pStyle );
				$section->addText(utf8_decode('Neste módulo, o foco das sessões será o estímulo à ampliação dos seus negócios por meio de estratégias valiosas que resultarão em aumento de rentabilidade e redimensionamento de sua base de clientes potenciais. A visão estratégica envolvida neste processo de ampliação de negócios envolve o estímulo à eficiência de vendas, à fidelização de seus clientes, ao boca a boca das indicações positivas, ao fortalecimento de suas marcas corporativa e pessoal, à estratégia de marketing e ao próprio aprimoramento da abordagem de vendas'),$pStyle );
				$section->addText(utf8_decode('Módulo 4: Torne-se um vendedor superstar'),$pStyle );
				$section->addText(utf8_decode('Neste módulo, você desenvolverá de maneira extraordinária suas habilidades como vendedor por meio de técnicas e exercícios cuidadosamente elaborados para destacar suas melhores qua- lidades e colocá-las em evidência da maneira mais eficaz possível, a partir das táticas dos ven- dedores mais bem-sucedidos do mundo. Aqui, serão abordadas estratégias para definir quem é seu cliente potencial ideal e os benefícios de construir uma imagem positiva e confiável para, assim, gerar cada vez mais contatos. Além disso, em busca de desempenho e resultados verda- deiramente impressionantes, seu discurso e sua abordagem de vendas serão aprimorados visando evitar e superar as eventuais objeções dos seus clientes potenciais, conhecendo o passo a passo de uma venda de sucesso '.htmlspecialchars('-').' da prospecção de clientes ao fechamento.'),$pStyle );
				$section->addText(utf8_decode('Módulo 5: Torne-se um líder'),$pStyle );
				$section->addText(utf8_decode('Neste módulo, você será estimulado a exercer suas habilidades de liderança e a superar obstáculos por meio de ferramentas atuais e eficazes de desenvolvimento pessoal e gerenciamento. Para atingir seus objetivos, as sessões de coaching abordarão, de forma clara e objetiva, etapas cruciais para o sucesso do seu empreendimento, entre as quais a definição dos quatro pilares fundamentais da liderança eficaz '.htmlspecialchars('-').' o Propósito, os Valores, a Missão e a Visão do seu negócio. Você desenvolverá uma estratégia vencedora por meio da análise detalhada dos seus concorrentes, o domínio das emoções negativas e a própria execução estratégica de projetos para atuar como um líder engajado com o sucesso, diminuindo o stress e ganhando qualidade de vida.'),$pStyle );
		}if($arLInha['nome_produto_linha'] == utf8_decode('Coleção Sucesso por Brian Tracy') ){
			$section->addText(utf8_decode('Conteúdo Programático Coleção Sucesso por Brian Tracy'),$pStyleTitulo );
			
			$section->addText(utf8_decode('Sucesso em Liderança'),$pStyle );
			$section->addText(utf8_decode('Neste treinamento compacto, composto por vídeo e livro de apoio, Brian Tracy, de maneira esclarecedora, indica os padrões comportamentais indispensáveis capazes de aumentar a performance daqueles que realmente desejam  ser líderes de sucesso.'),$pStyle );
			$section->addText(utf8_decode('Neste treinamento, valendo-se da experiência acumulada ao longo de sua ampla vivência no setor corporativo, Brian Tracy ensina estratégias vencedoras para lidar com situações recorrentes que envolvem os profissionais no ambiente organizacional.'),$pStyle );
			
			$section->addText(utf8_decode('Sucesso em Vendas'),$pStyle );
			$section->addText(utf8_decode('Neste treinamento compacto, composto por vídeo e livro de apoio, Brian Tracy expõe estratégias cruciais para a elaboração de seu planejamento de vendas, seguindo etapas precisas e eficazes, sempre pautadas na credibilidade atrelada ao seu produto ou serviço. Ao ter acesso às informações dispostas neste treinamento, torna-se possível reestruturar seus conceitos em relação às tendências de mercado para definir com mais clareza seu campo de atuação, selecionar a clientela mais adequada ao seu produto ou serviço, estabelecer vínculos mais sólidos com seus clientes para identificar suas necessidades mais facilmente, tornar a apresentação de seu produto mais atraente e criativa, argumentar de maneira mais persuasiva contra as objeções dos clientes e dispor dos recursos mais eficazes para fechar a venda com objetividade.'),$pStyle );
			$section->addText(utf8_decode('Sucesso nos Negócios'),$pStyle );
			$section->addText(utf8_decode('Neste treinamento compacto, composto por vídeo e livro de apoio, Brian Tracy aborda as técnicas essenciais e indispensáveis para organizar os processos de sua empresa no que diz respeito à dinamização das vendas, captação de clientes, padronização dos métodos de abordagem, gestão eficiente de recursos financeiros, sistematização dos procedimentos organizacionais e análise profunda das entradas e saídas de recursos, com o objetivo de reestruturar a administração do capital para elevar os lucros.'),$pStyle );
			
			$section->addText(utf8_decode('Sucesso Pessoal'),$pStyle );
			$section->addText(utf8_decode('Neste treinamento compacto, composto por vídeo e livro de apoio, Brian Tracy aborda as estratégias mais precisas e eficientes para promover equilíbrio emocional, melhorar drasticamente seu modo de lidar com suas diversas atribuições e conduzi-lo ao sucesso em sua vida pessoal.'),$pStyle );
			$section->addText(utf8_decode('Brian Tracy aponta os aspectos essenciais para fortalecer suas habilidades pessoais e demonstra técnicas poderosas, capazes de prepará-lo para assumir o controle de sua vida e, consequentemente, estabelecer relacionamentos mais felizes e desfrutar de mais bem-estar e qualidade de vida'),$pStyle );
			$section->addText(utf8_decode('Sucesso em Administração do Tempo'),$pStyle );
			$section->addText(utf8_decode('Neste treinamento compacto, composto por vídeo e livro de apoio, Brian Tracy aponta os caminhos mais eficazes para administrar seu tempo e torná-lo um importante aliado em termos de aumento de produtividade em todas as áreas de sua vida. Para tornar os objetivos mais claros e fáceis de serem atingidos, Brian Tracy ensina técnicas de planejamento e controle das tarefas, a fim de que você consiga executar todas elas com agilidade e sem stress.'),$pStyle );
		}
		
		
		
	}
	
	$section->addText(utf8_decode('Proposta Comercial'),$pStyleTitulo);
	$section->addText(utf8_decode('Responsabilidades'),$pStyle);
	
	$section->addText(utf8_decode('As principais responsabilidades que a EMPRESA CONTRATANTE e a {unidade_franqueada} - Unidade Franqueada da SBCOACHING CORPORATE CONSULTORIA EM PERFORMANCE deverão assumir durante a prestação dos serviços, estão contidas na minuta do Contrato de Prestação de Serviços.'),$pStyle);
	$section->addText(utf8_decode('Investimento'),$pStyle);
	$section->addText(utf8_decode('Pelos serviços prestados, a EMPRESA CONTRATANTE pagará à {unidade_franqueada} - Unidade Franqueada da SBCOACHING CORPORATE CONSULTORIA EM PERFORMANCE os valores abaixo descritos:'),$pStyle);
	
	
	// Add table style
	
	$styleTable = array('borderSize'=>6, 'borderColor'=>'006699');
	$styleFirstRow  = array('borderBottomSize'=>18, 'borderBottomColor'=>'0000FF', 'bgColor'=>'66BBFF','align'=>'center', 'bold'=>true);
	$styleCell= array('height'=>20);
	$PHPWord->addTableStyle('myOwnTableStyle', $styleTable, $styleFirstRow);
	$table = $section->addTable('myOwnTableStyle');
	$table->addRow(4);

	// Add cells
	$table->addCell(10000, $styleCell)->addText('Nome'	 					   ,$styleTable);
	$table->addCell(500, $styleCell)->addText('Quantidade' 					   ,$styleTable);
	$table->addCell(500, $styleCell)->addText(utf8_decode('Valor unitário')	   ,$styleTable);
	$table->addCell(500, $styleCell)->addText('Total' 						   ,$styleTable);
	
	// Add more rows / cells
	foreach($arDescricao_solucoes as $arDescricao){
		$table->addRow();
		$table->addCell(2000)->addText("".$arDescricao['nome_produto']."");
		$table->addCell(2000)->addText("{$arDescricao['qtde']}");
		$table->addCell(2000)->addText("R$ ".number_format($arDescricao['vl_unitario'], 2, ',', '.')."");
		$table->addCell(2000)->addText("R$ ".number_format(($arDescricao['vl_total_liquido']), 2, ',', '.')."");
	}
	
	
	
	$section->addText(utf8_decode('Despesas',$pStyleTitulo));
	$section->addText(utf8_decode('Nos valores apresentados nesta Proposta não estão inclusos os custos relativos às despesas de viagens, bem como o deslocamento para atividades externas, estadia e alimentação dos COACHES da {unidade_franqueada} unidade Franqueada da SBCOACHING CORPORATE CONSULTORIA EM PERFORMANCE, sendo de responsabilidade da contratante esses custos.'),$pStyle);
	$section->addText(utf8_decode('Validade da proposta'),$pStyleTitulo);
	$section->addText(utf8_decode('A proposta terá validade até {data_br}.'),$pStyle);
	
	
	$section->addText(utf8_decode('Início da Prestação dos Serviços'),$pStyleTitulo);
	$section->addText(utf8_decode('Para início da prestação dos serviços precisamos desta proposta e contrato de prestação de serviços assinados, com 30 dias de antecedência.'),$pStyle);
	
	$section->addText(utf8_decode('Condições Gerais'),$pStyleTitulo);
	$section->addText(utf8_decode('Havendo divergência entre as condições gerais estipuladas nesta Proposta, em contrato firmado, ou quaisquer documentos trocados, entre a {nome_empresa} e a {unidade_franqueada} - Unidade Franqueada da SBCOACHING CORPORATE CONSULTORIA EM PERFORMANCE, prevalecerá à seguinte ordem: (a) Contrato de Prestação de Serviços (se existente); (b) Proposta Comercial; os documentos mais recentes sobre os mais antigos.'),$pStyle);
	$section->addText(utf8_decode('Eventuais alterações aos termos desta Proposta somente serão aceitas se efetuadas por escrito e assinadas por representantes legais das Partes.'),$pStyle);
	
	
	$section->addText(utf8_decode('Carta de Aceite'),$pStyleTitulo);
	$section->addText(utf8_decode('Para que os serviços aqui especificados possam ser iniciados, solicitamos o de acordo de V.Sas. e o retorno deste documento à {unidade_franqueada} - Unidade Franqueada da SBCOACHING CORPORATE CONSULTORIA EM PERFORMANCE, juntamente com coacute;pia da minuta do Contrato de Prestação de Serviços e Instrumento de Procuração (caso necessário), constando o nome dos representantes legais que assinaram esta Proposta.'),$pStyle);
	$section->addText(utf8_decode('Os abaixo-assinados declaram que todos os itens constantes nesta Proposta foram perfeitamente compreendidos, estando de acordo com as premissas e condições estabelecidas para a prestação dos serviços nela descritos.'),$pStyle);
	$section->addText(utf8_decode('{cidade}, {dia} de {mes} de 2014.'),$pStyle);
	
	
	
	$tableStyle = array('borderSize'=>6, 'borderColor'=>'006699', 'cellMargin'=>0);
	$PHPWord->addTableStyle('myOwnTableStyleAssinatura',$tableStyle,null);
	$table    = $section->addTable('myOwnTableStyleAssinatura');
	

	$section->addTextBreak(2);
	
	$table = $section->addTable();
	$table->addRow(0);
	$table->addCell(10000, 	$styleCell)->addText('--------------------------------------------------------',null,$noSpace);
	$table->addCell(10000, 	$styleCell)->addText('--------------------------------------------------------',null,$noSpace);
	
	
	$table->addRow(10);
	$table->addCell(10000, 	$styleCell)->addText('Nome:{nome_pessoa}',null,$noSpace);
	$table->addCell(10000, 	$styleCell)->addText('{nome_partner}', null,$noSpace);
	
	
	$table->addRow(10);
	$table->addCell(10000, 	$styleCell)->addText('Cargo:{nome_partner}',null,noSpace);
	$table->addCell(10000,	$styleCell)->addText('Unidade Franqueada da SBCOACHING CORPORATE CONSULTORIA EM PERFORMANCE',null,$noSpace);
	
	$table->addRow(10);
	$table->addCell(10000, $styleCell)->addText('',null,$noSpace);
	$table->addCell(10000, $styleCell)->addText('Nome:',null,$noSpace);
	
	$table->addRow(10);
	$table->addCell(10000, $styleCell)->addText(' ',null,$noSpace);
	$table->addCell(10000, $styleCell)->addText('CNPJ:',null,$noSpace);
	
	
	
	
	
	
	
	// Add footer
	$footer = $section->createFooter();
	$footer->addPreserveText('Page {PAGE} of {NUMPAGES}.', array('align'=>'center'));

	$arMes = array('01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Marco', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Novembro', '10' => 'Setembro', '11' => 'Outubro', '12' => 'Dezembro'	);
	// variaveis para o replace
	$dia 						= date('d');
	$mes 						= strtolower($arMes[date('m')]);
	$ano 						= date('Y');
	$anoDoisDigitos				= date('y');
	$data_br 					= date('d/m/Y');
	$nome_empresa 				= $arPessoaOrcamento['razao_social_nome'];
	$cidade 					= $arPessoaOrcamento['cidade'];
	$estado 					= $arPessoaOrcamento['uf'];
	$nome_pessoa 				= $arPessoaOrcamento['nome'] != "" ? "At: ".$arPessoaOrcamento['nome'] : "";
	$nome_produtos 				= substr_replace($nome_produtos, ' e', strripos($nome_produtos,','), 1);
	$nome_partner 				= $arPessoaOrcamento['nome_usuario'];
	$unidade_franqueada 		= $arPessoaOrcamento['unidade_franqueada'];
	$num_unidade_franqueada 	= $arPessoaOrcamento['n_contrato'];
	$descricao_solucoes 		= $descricaoSolucoes;
	$protocolo_fases 			= $protocolo;
	$descricao_valores 			= $produtoPreco;
	

if(!empty($ar_modelo['textohtm_corpo'])){
    // Save File
	$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
	$objWriter->save('proposta'.$_GET['pnumreg'].'.docx');
    $template = $PHPWord->loadTemplate('proposta'.$_GET['pnumreg'].'.docx');
	// replace
	$template->setValue('{num}', 				$_GET['pnumreg']);
	$template->setValue('{dia}', 				$dia);
	$template->setValue('{competencias_macro}',	$competenciaMacro);
	$template->setValue('{competencias_micro}',	$microCompetencias);
	$template->setValue('{unidade_franqueada}',	$num_unidade_franqueada);
	$template->setValue('{mes}', 				$mes);
	$template->setValue('{ano}', 				$ano);
	$template->setValue('{anoDoisDigitos}',		$anoDoisDigitos);
	$template->setValue('{nome_empresa}', 		utf8_encode($nome_empresa));
	$template->setValue('{cidade}', 			ucfirst($cidade));
	$template->setValue('{estado}', 			$estado);
	$template->setValue('{servicos_processo}', 	$servicos_processo);
	$template->setValue('{nome_pessoa}', 		$nome_pessoa);
	$template->setValue('{nome_produtos}', 		$nome_produtos);
	$template->setValue('{nome_partner}', 		$nome_partner);
	$template->setValue('{unidade_franqueada}', $unidade_franqueada);
	$template->setValue('{descricao_solucoes}', $descricao_solucoes);
	$template->setValue('{protocolo_fases}', 	$protocolo_fases);
	$template->setValue('{data_br}', $data_br);
	$template->save('proposta'.$_GET['pnumreg'].'.docx');
    $nomeProposta = 'proposta'.$_GET['pnumreg'].'.docx';
    $caminho ="/modulos/venda/gera_modelo/";
    
	}else{
	    echo "Nenhum modelo encontrado.";
	}
	header("Location: http://".$_SERVER['SERVER_NAME'].$caminho.$nomeProposta);
	
// Imprimir
//if($ar_modelo['tp_arquivo'] == 'html'){
    //echo '<script language="JavaScript"> window.print(); </script>';
//}

?>	