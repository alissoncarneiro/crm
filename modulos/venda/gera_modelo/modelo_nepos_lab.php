<?php
# modelo_nepos_lab
# Expression package is undefined on line 3, column 5 in Templates/Scripting/EmptyPHP.php.
# Autor: Rodrigo Piva
# 04/10/2011
#
# Log de Alterações
# yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
#

header("Content-Type: text/html; charset=ISO-8859-1");
session_start();

$PrefixoIncludes = '../';
include('../includes.php');

if(empty($_REQUEST['pnumreg'])){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
else{
    if($_REQUEST['ptp_venda'] == 1){
        $Venda = new Orcamento($_REQUEST['ptp_venda'],$_REQUEST['pnumreg'],true,false);
    }
    else{
        $Venda = new Pedido($_REQUEST['ptp_venda'],$_REQUEST['pnumreg'],true,false);
    }
}

?>

<head>
<title>Untitled Document</title>
</head>
<body>
<table cellspacing="0" cellpadding="0">
  <col width="21" span="2" />
  <col width="106" />
  <col width="47" />
  <col width="134" />
  <col width="37" />
  <col width="46" />
  <col width="255" />
  <col width="83" />
  <col width="90" />
  <col width="19" />
  <tr>
    <td width="21">&nbsp;</td>
    <td width="21">&nbsp;</td>
    <td colspan="5" rowspan="2" align="center">DEMONSTRATIVO</td>
    <td width="241" align="left" valign="top"><img width="167" height="73" src="logo_nepos.png" alt="LOGO NEPOS" />
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td width="255">&nbsp;</td>
        </tr>
      </table></td>
    <td width="90">&nbsp;</td>
    <td width="90">&nbsp;</td>
    <td width="19">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Or&ccedil;amento n&ordm;</td>
    <td><?php echo $Venda->getNumregVenda();?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="5" align="center">de servi&ccedil;o realizado    no Centro de Reparos</td>
    <td>&nbsp;</td>
    <td>Data</td>
    <td><?php echo dten2br($Venda->getDadosVenda('dt_venda'));?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td width="118">&nbsp;</td>
    <td width="150">&nbsp;</td>
    <td width="230">&nbsp;</td>
    <td width="67">&nbsp;</td>
    <td width="102">&nbsp;</td>
    <td>&nbsp;</td>
    <td>Pag n&ordm;</td>
    <td>1</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>N&ordm; do Cliente:</td>
    <td colspan="2">1104213</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Contrato:</td>
    <td>N&Atilde;O</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Cliente:</td>
    <td colspan="3"><?php echo $Venda->getPessoa()->getDadoPessoa('fantasia_apelido');?>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>Sistema:</td>
    <td>SPACE</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Raz&atilde;o social:</td>
    <td colspan="2"><?php echo $Venda->getPessoa()->getDadoPessoa('razao_social_nome');?>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Cidade:</td>
    <td colspan="2"><?php echo $Venda->getPessoa()->getDadoPessoa('cidade');?>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td colspan="2"><?php echo $Venda->getPessoa()->getDadoPessoa('uf');?>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Nome contato:</td>
    <td colspan="3">
    <?php echo $Venda->getContato() != ''?$Venda->getContato()->getNome():'';?>&nbsp;</td>
    <td>Telefone:</td>
    <td colspan="2"><?php echo $Venda->getContato() != ''?$Venda->getContato()->getTel1():'';?>&nbsp;
                    <?php echo $Venda->getContato() != ''?$Venda->getContato()->getTel2():'';?>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>E-mail:</td>
    <td colspan="2"><?php echo $Venda->getContato() != ''?$Venda->getContato()->getEmail():'';?>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>CNPJ:</td>
    <td colspan="2"><?php echo $Venda->getPessoa()->getDadoPessoa('cnpj_cpf');?>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Insc. Est.</td>
    <td colspan="2"><?php echo $Venda->getPessoa()->getDadoPessoa('ie_rg');?>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center" bgcolor="#D6D6D6">Atendimento</td>
    <td align="center" bgcolor="#D6D6D6">NF</td>
    <td width="230" align="center" bgcolor="#D6D6D6">Data de recebimento</td>
    <td align="center" bgcolor="#D6D6D6">Garantia</td>
    <td align="center" bgcolor="#D6D6D6">C&oacute;digo</td>
    <td align="center" bgcolor="#D6D6D6">Descri&ccedil;&atilde;o</td>
    <td align="center" bgcolor="#D6D6D6">Qtde</td>
    <td align="center" bgcolor="#D6D6D6">Peças</td>
    <td align="center" bgcolor="#D6D6D6">Horas</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <?php
    //Cria array com todos os itens do pedido
    foreach($Venda->getItens() as $IndiceItem => $Item){
        if($Venda->getTipoVenda() == 1 && $Item->getDadosVendaItem('sn_item_perdido') == 1){
            continue;
        }
        $ArItems[$Item->getDadosVendaItem('id_produto')] = $Item;
    }


    $SqlAtividade = "SELECT * FROM is_atividade WHERE id_orcamento = '".$Venda->getNumregVenda()."'";
    $QryAtividade = query($SqlAtividade);
    $DescricaoAtividade = '';
    $ValorHoras = 0;
    $ValorPecas = 0;
    $i = 0;

    while($ArAtividade = farray($QryAtividade)){
        $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
        $i++;

        $DescricaoAtividade.=  '<tr>
                                <td>&nbsp;</td>
                                <td align="center">&nbsp;</td>
                                <td align="center" bgcolor="'.$bgcolor.'">'.$ArAtividade['id_atividade'].'</td>
                                <td align="center" bgcolor="'.$bgcolor.'">'.$ArAtividade['tempo_real'].'</td>
                                <td align="center" bgcolor="'.$bgcolor.'">&nbsp;</td>
                                <td align="center" bgcolor="'.$bgcolor.'"></td>
                                <td colspan="2" align="center" bgcolor="'.$bgcolor.'">&nbsp;</td>
                                <td align="center" bgcolor="'.$bgcolor.'">&nbsp;</td>
                                <td align="center" bgcolor="'.$bgcolor.'">&nbsp;</td>
                                <td align="center" bgcolor="'.$bgcolor.'">&nbsp;</td>
                              </tr>';



        $SqlProdutoOrcamento = "SELECT * FROM is_produto_orcamento_lab WHERE id_atividade = '".$ArAtividade['numreg']."'
                                                                       AND   sn_orcamento = '1'";
        $QryProdutoOrcamento = query($SqlProdutoOrcamento);
        while($ArProdutoOrcamento = farray($QryProdutoOrcamento)){

             $DescricaoAtividade.= '<tr>
                                    <td>&nbsp;</td>
                                    <td align="center">&nbsp;</td>
                                    <td align="center" bgcolor="'.$bgcolor.'">&nbsp;</td>
                                    <td align="center" bgcolor="'.$bgcolor.'">&nbsp;</td>
                                    <td align="center" bgcolor="'.$bgcolor.'">'.deparaIdErpCrm($ArProdutoOrcamento['id_produto'], 'id_produto_erp', 'numreg', 'is_produto').'</td>
                                    <td align="center" bgcolor="'.$bgcolor.'">'.$ArProdutoOrcamento['qtde'].'</td>
                                    <td colspan="2" align="center" bgcolor="'.$bgcolor.'">'.deparaIdErpCrm($ArProdutoOrcamento['id_produto'], 'nome_produto', 'numreg', 'is_produto').'</td>
                                    <td align="center" bgcolor="'.$bgcolor.'">&nbsp;</td>
                                    <td align="center" bgcolor="'.$bgcolor.'">&nbsp;</td>
                                    <td align="center" bgcolor="'.$bgcolor.'">&nbsp;</td>
                                  </tr>';
             if($ArItems[$ArProdutoOrcamento['id_produto']] != ''){
                 if($ArProdutoOrcamento['id_unid_medida'] == "32"){
                    $ValorHoras += $ArItems[$ArProdutoOrcamento['id_produto']]->getDadosVendaItem('vl_unitario_com_descontos') * $ArProdutoOrcamento['qtde'];
                    //echo $ValorHoras." <-horas ".deparaIdErpCrm($ArProdutoOrcamento['id_produto'], 'id_produto_erp', 'numreg', 'is_produto') ."<br>";
                 }else{
                    $ValorPecas += $ArItems[$ArProdutoOrcamento['id_produto']]->getDadosVendaItem('vl_unitario_com_descontos') * $ArProdutoOrcamento['qtde'];
                    //echo $ValorPecas." <-pecas ".deparaIdErpCrm($ArProdutoOrcamento['id_produto'], 'id_produto_erp', 'numreg', 'is_produto') ."<br>";
                 }
             }
        }
    ?>
      <tr>
        <td>&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center" bgcolor="<?php echo $bgcolor ?>"><?php echo $ArAtividade['id_atividade'] ?></td>
        <td align="center" bgcolor="<?php echo $bgcolor ?>"><?php echo $ArAtividade['nr_nota'] ?></td>
        <td align="center" bgcolor="<?php echo $bgcolor ?>"><?php echo dten2br($ArAtividade['dt_nota']) ?></td>
        <td align="center" bgcolor="<?php echo $bgcolor ?>">N&Atilde;O</td>
        <td align="center" bgcolor="<?php echo $bgcolor ?>"><?php echo deparaIdErpCrm($ArAtividade['id_produto'], 'id_produto_erp', 'numreg', 'is_produto')?></td>
        <td align="center" bgcolor="<?php echo $bgcolor ?>"><?php echo deparaIdErpCrm($ArAtividade['id_produto'], 'nome_produto', 'numreg', 'is_produto')?></td>
        <td align="center" bgcolor="<?php echo $bgcolor ?>"><?php echo $ArAtividade['qtde']?></td>
        <td align="center" bgcolor="<?php echo $bgcolor ?>"><?php echo $ValorPecas ?>&nbsp;</td>
        <td align="center" bgcolor="<?php echo $bgcolor ?>"><?php echo $ValorHoras ?></td>
        <td align="center">&nbsp;</td>
      </tr>
    <?php
    }
    ?>
   <tr>
    <td>&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>

  <tr>
    <td>&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">Total</td>
    <td align="center">Geral</td>
    <td align="center"><?php echo $Venda->NFV($Venda->getVlTotalVendaLiquido());?>&nbsp;</td>
  </tr>

   <tr>
    <td>&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="9" align="center">Demonstrativo    das Pe&ccedil;as Utilizadas no Reparo dos Equipamentos acima</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center" bgcolor="#D6D6D6">Atendimento</td>
    <td align="center" bgcolor="#D6D6D6">Tempo reparo</td>
    <td align="center" bgcolor="#D6D6D6">C&oacute;digo</td>
    <td align="center" bgcolor="#D6D6D6">Quant</td>
    <td colspan="2" align="center" bgcolor="#D6D6D6">Descri&ccedil;&atilde;o</td>
    <td align="center" bgcolor="#D6D6D6">&nbsp;</td>
    <td align="center" bgcolor="#D6D6D6">&nbsp;</td>
    <td align="center" bgcolor="#D6D6D6">&nbsp;</td>
  </tr>
  <?php echo $DescricaoAtividade;?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="6">1. Condi&ccedil;&otilde;es para    Fornecimento dos Servi&ccedil;os de Manuten&ccedil;&atilde;o em Laboratorio</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="3">1.1 - Faturamento m&iacute;nimo    de 01 hora.</td>
    <td>&nbsp;</td>
    <td colspan="2">1.4 - Condi&ccedil;&atilde;o de    pagamento</td>
    <td><?php echo $Venda->getDadosCondPagto('nome_cond_pagto')?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="4">1.2 - Ap&oacute;s a primeira    hora, o faturamento ser&aacute; a cada 1/2 hs.</td>
    <td colspan="2">1.5 - Prazo de validade    do Or&ccedil;amento&nbsp; = 20 dias</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="3">1.3 - Garantia do    Servi&ccedil;o = 90 dias.</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="6">2. Condi&ccedil;&otilde;es para    Fornecimento dos Servi&ccedil;os de Manuten&ccedil;&atilde;o em Campo e Visita Tecnica</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="3">2.1 - Faturamento m&iacute;nimo    de 01 hora.</td>
    <td>&nbsp;</td>
    <td colspan="2">2.4 - Condi&ccedil;&atilde;o de    pagamento</td>
    <td><?php echo $Venda->getDadosCondPagto('nome_cond_pagto')?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="4">2.2 - Ap&oacute;s a primeira    hora, o faturamento ser&aacute; a cada 1/2 hs.</td>
    <td colspan="2">2.5 - Valor do    quil&ocirc;metro rodado = R$ 1,00.</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="4">2.3 - Despesas do    T&eacute;cnico, como, passagem, hospedagem</td>
    <td colspan="4">2.6 - Servi&ccedil;os    executados fora do hor&aacute;rio de atendimento (segunda a sexta-feira</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ped&aacute;gio e alimenta&ccedil;&atilde;o, s&atilde;o de    responsabilidade do Cliente.</td>
    <td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; das 08:00 as 18:00 hs), acrescidos    em 100% no valor do servi&ccedil;o.</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="3">3. Condi&ccedil;&otilde;es para    Fornecimento de Pe&ccedil;as</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="4">3.1 - Prazo de Entrega =    Ap&oacute;s aprova&ccedil;&atilde;o deste e disponibilidade em estoque.</td>
    <td colspan="2">3.4 - Prazo de validade    do Or&ccedil;amento = 20 dias.</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">3.2 - Garantia = 90    dias.</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">3.5 - Despesas com Frete    = Cliente (FOB).</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">3.3 - Condi&ccedil;&atilde;o de    Pagamento</td>
    <td><?php echo $Venda->getDadosCondPagto('nome_cond_pagto')?></td>
    <td>&nbsp;</td>
    <td colspan="2">3.5.1 Transportadora    utilizada</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</body>