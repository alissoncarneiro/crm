<?php
    header("Content-Type: text/html; charset=ISO-8859-1");
    session_start();

    $PrefixoIncludes = '../';
    include('../includes.php');

    if(empty($_REQUEST['pnumreg'])){
        echo getError('0040010001',getParametrosGerais('RetornoErro'));
        exit;
    }else{
        if($_REQUEST['ptp_venda'] == 1){
            $Venda = new Orcamento($_REQUEST['ptp_venda'],$_REQUEST['pnumreg'],true,false);
        }
        else{
            $Venda = new Pedido($_REQUEST['ptp_venda'],$_REQUEST['pnumreg'],true,false);
        }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Impress&atilde;o</title>
        <link rel="stylesheet" type="text/css" href="<?=$url_path_pedido;?>/estilos_css/estilo.css" />
        <link rel="stylesheet" type="text/css" href="<?=$url_path_pedido;?>/estilos_css/cadastro.css">
    </head>
    <body>
        <table width="670" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <table width="100%" border="0" cellspacing="2" cellpadding="0">
                        <tr>
                            <td width="79%">
                                <?php
                                if(isset($_GET['envia_email']) && $_GET['envia_email'] == 1){
                                        echo '<img src="cid:logoimg" />';
                                } else {
                                    echo '<img src="../../../images/logo_login.png" alt="Alpha Print"/>';
                                }?>
                            </td>
                            </tr>
                                <tr>
                                <td align="center"><strong><?php echo ($Venda->getTipoVenda()==1)?'ORÇAMENTO':'PEDIDO';?></strong></td>
                            </tr>
                        </table>		
                    </td>
            </tr>
            <tr>
                <td align="center"></td>
            </tr>
            <tr>
                <td>
                    <table width="100%" border="0">
                        <tr>
                            <td width="70%"><strong>Cliente: </strong><?php echo $Venda->getPessoa()->getDadoPessoa('razao_social_nome');?></td>
                            <td width="30%"><strong>Nr. <?php echo ($Venda->getTipoVenda()==1)?'Orçamento':'Pedido';?>: </strong><?php echo $Venda->getNumregVenda();?></td>
                        </tr>
                        <tr>
                            <td><strong>CNPJ: </strong><?php echo $Venda->getPessoa()->getDadoPessoa('cnpj_cpf');?></td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php
                        /*$qry_suf = query("SELECT id_suframa FROM is_pessoas WHERE id_pessoa = '".$ar_cliente['id_pessoa']."'");
                        $ar_suf = farray($qry_suf);
                        if($ar_suf['id_suframa'] != ''){
                            echo '<tr><td colspan="2"></tr>
                                        <tr>
                            <td colspan="2" align="center"><strong>Desconto de 7% referente o SUFRAMA sobre o valor total do pedido.</strong></td>
                          </tr>';
                        }*/
                        ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td bgcolor="#DBE9F4" align="center"><strong>Itens</strong></td>    
            </tr>
            <tr>
                <td>
                    <table width="100%" border="0" cellpadding="2" cellspacing="2" class="bordatabela">
                        <tr>
                            <td bgcolor="#DBE9F4" class="tit_tabela">C&oacute;d.</td>
                            <td bgcolor="#DBE9F4" class="tit_tabela">Descri&ccedil;&atilde;o</td>
                            <td bgcolor="#DBE9F4" class="tit_tabela">Unid. <br />Med.</td>
                            <td bgcolor="#DBE9F4" class="tit_tabela">Qtde.</td>
                            <td bgcolor="#DBE9F4" class="tit_tabela">% IPI</td>
                            <td bgcolor="#DBE9F4" class="tit_tabela">Valor Unit.</td>
                            <td bgcolor="#DBE9F4" class="tit_tabela">Valor Bruto</td>
                            <td bgcolor="#DBE9F4" class="tit_tabela">Valor C. Desconto</td>
                        </tr>
                        <?php
                        foreach($Venda->getItens() as $IndiceItem => $Item){
                            if($Venda->getTipoVenda() == 1 && $Item->getDadosVendaItem('sn_item_perdido') == 1){
                                continue;
                            }
                        ?>
                        <tr>
                            <td bgcolor="<?=$bgcolor;?>"><?php echo $Item->getCodProdutoERP();?></td>
                            <td bgcolor="<?=$bgcolor;?>"><?php echo $Item->getNomeProduto();?></td>
                            <td bgcolor="<?=$bgcolor;?>">
                            <?php
                            if($Venda->getDigitacaoCompleta() || 1==1){ //Se a venda já estiver completa FIXO TEXTO
                                if($Item->getDadosVendaItem('id_unid_medida') != ''){
                                    if($Venda->isAtacado()){
                                        echo 'CX';
                                    }
                                    else{
                                        $QryUnidMedida = query("SELECT numreg, nome_unid_medida FROM is_unid_medida WHERE numreg = ".$Item->getDadosVendaItem('id_unid_medida'));
                                        $ArUnidMedida = farray($QryUnidMedida);
                                        echo $ArUnidMedida['nome_unid_medida'];
                                    }
                                }
                            }
                            ?>
                            </td>
                            <td bgcolor="<?=$bgcolor;?>"><div align="right"><?php echo $Venda->NFQ($Item->getDadosVendaItem('qtde'));?></div></td>
                            <td bgcolor="<?=$bgcolor;?>"><div align="right"><?php echo str_replace('.',',',$Item->getDadosVendaItem('pct_aliquota_ipi'));?></div></td>
                            <td bgcolor="<?=$bgcolor;?>"><div align="right"><?php echo $Venda->NFV($Item->getDadosVendaItem('vl_unitario_com_descontos'));?></div></td>
                            <td bgcolor="<?=$bgcolor;?>"><div align="right"><?php echo $Venda->NFV($Item->getDadosVendaItem('vl_total_bruto'));?></div></td>
                            <td bgcolor="<?=$bgcolor;?>"><div align="right"><?php echo $Venda->NFV($Item->getDadosVendaItem('vl_total_liquido'));?></div></td>
                        </tr>
                        <?php }  ?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="7"><strong>Total sem IPI</strong></td>
                            <td><div align="right"><?php echo $Venda->NFV($Venda->getVlTotalVendaLiquido());?></div></td>
                        </tr>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="7"><strong>Total com IPI</strong></td>
                            <td><div align="right"><?php echo $Venda->NFV($Venda->getVlTotalVendaLiquido()+$Venda->getVlTotalVendaIPI());?></div></td>
                        </tr>
                        
                        <?php if($ar_suf['id_suframa'] != ''){ ?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="7"><strong>Valor Desconto SUFRAMA</strong></td>
                            <td><div align="right"> - </div></td>
                        </tr>
                        <?php }?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="7"><strong>Total com IPI e ST</strong></td>
                            <td><div align="right"><?php echo $Venda->NFV($Venda->getVlTotalVendaLiquido()+$Venda->getVlTotalVendaIPI()+$Venda->getVlTotalVendaST());?></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <p>&nbsp;</p>
                    <table width="100%" border="0">
                        <tr>
                            <td><strong>Tab. Pre&ccedil;o: </strong><?php echo $Venda->getDadosTabPreco('nome_tab_preco');?></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td><strong>Dt. Pedido: </strong><?php echo dten2br($Venda->getDadosVenda('dt_venda'));?></td>
                            <td><strong>Cond. Pagto.: </strong><?php echo $Venda->getDadosCondPagto('nome_cond_pagto');?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Obs. Pedido: </strong><?php echo chrbr(strsadds($Venda->getDadosVenda('obs')));?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Obs. NF: </strong><?php echo chrbr(strsadds($Venda->getDadosVenda('obs_nf')));?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>End. Entrega: </strong><?php echo $Venda->getDadosEnderecoEntrega('endereco'), ' <br /> ', $Venda->getDadosEnderecoEntrega('bairro'), ' - ', $Venda->getDadosEnderecoEntrega('cep'), ' - ', $Venda->getDadosEnderecoEntrega('uf');?></td>
                        </tr>
                      </table>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>
    </body>
</html>