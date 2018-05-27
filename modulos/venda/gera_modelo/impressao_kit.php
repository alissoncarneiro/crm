<?php
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

  
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Impress&atilde;o</title>
    <link rel="stylesheet" type="text/css" href="<?=$url_path_pedido;?>/estilos_css/estilo.css" />
    <link rel="stylesheet" type="text/css" href="<?=$url_path_pedido;?>/estilos_css/cadastro.css">
    <style>
            .c_campo_data{
                    width:65px;
                    text-align: center;
            }
            .c_tabela_itens{
                    border: 1px solid #ACC6DB;
            }
            .c_titulo_tabela{
                    font-weight: bold;
                    color: #345C7D;
                    background-color: #DAE8F4;
                    text-align: left;
                    padding-left: 5px;
            }
            .c_campo_dt{
                    width: 60px;
                    text-align: center;
            }
            .c_campo_vl{
                    width: 50px;
                    text-align: right;
            }
            .c_title{
                    font-size: 10px;
                    font-weight: bold;
            }
            .c_tabela_lista_presenca{
                    border-collapse: collapse;
            }
            .c_tabela_lista_presenca tr td {
                    border:2px solid #000000;
            }
            .c_campo_obrigatorio{
                    font-weight: bold;
                    color: #00F;
            }
            .c_h2{
                    font-size: 14px;
                    font-weight: bold;
                    color: #345C7D;
            }
            .c_td_label{
                    text-align: left;
                    font-weight: bold;
            }
    </style>

    </head>
<body>
		<div align="center">
		<h2 class="c_h2">Reserva de <?php echo ($Venda->getTipoVenda()==1)?'Orçamento':'Pedido';?> N&ordm; <?php echo $Venda->getNumregVenda();?></h2>
        </div>
<table width="700" border="0" cellpadding="2" cellspacing="2" class="c_tabela_itens">
            <tr>
                <td width="161" class="c_td_label">Data Venda:</td>
                <td width="523"><?php echo dten2br($Venda->getDadosVenda('dt_venda'));?></td>
            </tr>
            <tr>
                <td class="c_td_label">Vendedor:</td>
                
                <?php
				$SqlUsuario= mysql_query("select * from is_usuario where numreg=".$Venda->getDadosVenda('id_representante_principal'));
				$ArSqlUsuario= mysql_fetch_array($SqlUsuario);
				?>
                <td><?php echo $ArSqlUsuario['nome_usuario']; ?></td>
            </tr>

            <tr>
                <td class="c_td_label">Nome Completo:</td>
                <td><?php echo $Venda->getPessoa()->getDadoPessoa('razao_social_nome');?></td>
            </tr>
			<tr>
				<td>
			<?php 
			
			//print_r($Venda->getContato());

			if($Venda->getDadosVenda('id_contato') != '') {
			?>
            <tr>
				<td class="c_td_label">Nome Contato:</td>
                <td> <?php echo $Venda->getContato($Venda->getDadosVenda('id_contato'))->getNome()?>&nbsp;</td>
            </tr>
            <?php }	?>
            

            
            
            
            
            
            <tr>
                <td class="c_td_label">CPF:</td>
                <td><?php echo $Venda->getPessoa()->getDadoPessoa('cnpj_cpf');?></td>
            </tr>            
            <tr>
                <td class="c_td_label">RG:</td>
                <td><?php echo $Venda->getPessoa()->getDadoPessoa('ie_rg');?></td>
            </tr>
            <tr>
                <td class="c_td_label">Estado Civil:</td>
                <?php
				$SqlEstCivil=mysql_query("select * from is_estcivil where numreg=".$Venda->getPessoa()->getDadoPessoa('id_estcivil'));
				$ArSqlEstCivil= mysql_fetch_array($SqlEstCivil);
				?>
                <td><?php echo $ArSqlEstCivil['nome_estcivil'];?></td>
            </tr>  

            <tr>
                <td class="c_td_label">Profissão / Cargo:</td>
                <?php
				$SqlProfissao=mysql_query("SELECT * FROM is_cargo where numreg=".$Venda->getPessoa()->getDadoPessoa('wcp_cargo'));
				$ArSqlProfissao= mysql_fetch_array($SqlProfissao);
				?>
                
                <td><?php echo $ArSqlProfissao['nome_cargo'];?></td>
            </tr>  
            
            <tr>
                <td class="c_td_label">Data de Nascimento:</td>
                <td><?php echo $Venda->getPessoa()->getDadoPessoa('dianascto') ,'/',$Venda->getPessoa()->getDadoPessoa('mesnascto')  ,'/', $Venda->getPessoa()->getDadoPessoa('anonascto') ?></td>
            </tr>  
                                              
            <tr>
                <td class="c_td_label">Endere&ccedil;o:</td>
                
                <td><?php 
				$sqlEnderecoEntrega = "select * from is_pessoa_endereco 
							where id_pessoa= '".$Venda->getDadosVenda('id_pessoa')."' and id_tp_endereco = '1'";
				$aryEnderecoEntrega = farray(query($sqlEnderecoEntrega));
				echo $aryEnderecoEntrega['endereco']." - "; 
				echo $aryEnderecoEntrega['numero']." - "; 
				echo $aryEnderecoEntrega['complemento']; ?></td>
			</tr>
            <tr>
                <td class="c_td_label">Bairro:</td>
                <td><?php echo $aryEnderecoEntrega['bairro']; ?></td>
            </tr>
            <tr>
                <td class="c_td_label">Cidade:</td>
                <td><?php echo $aryEnderecoEntrega['cidade']; ?></td>
            </tr>
            <tr>
                <td class="c_td_label">UF:</td>
                <td><?php echo $aryEnderecoEntrega['uf']; ?></td>
            </tr>
            <tr>
                <td class="c_td_label">CEP:</td>
                <td><?php echo $aryEnderecoEntrega['cep']; ?></td>
            </tr>
            <tr>
                <td class="c_td_label">Fones:</td>
                <td><?php echo $Venda->getPessoa()->getDadoPessoa('tel1'),' ',$Venda->getPessoa()->getDadoPessoa('tel2')?></td>
            </tr>
            <tr>
                <td class="c_td_label">Email:</td>
                <td><?php echo $Venda->getPessoa()->getDadoPessoa('email'),' / ',$Venda->getPessoa()->getDadoPessoa('email_pessoal') ?></td>
            </tr>
            <?php 
			if ($Venda->getDadosVenda('id_pessoa_triangular') != 0){
				$SqlClienteFinanceiro=mysql_query("SELECT razao_social_nome,cnpj_cpf FROM is_pessoa where numreg=".$Venda->getDadosVenda('id_pessoa_triangular'));
				$ArSqlClienteFinanceiro= mysql_fetch_array($SqlClienteFinanceiro);				
				
			echo"
			<tr>
                <td class=\"c_td_label\">Cliente Financeiro:</td>
                <td> $ArSqlClienteFinanceiro[razao_social_nome] </td>
            </tr>    
			<tr>
                <td class=\"c_td_label\">CNPJ / CPF:</td>
                <td> $ArSqlClienteFinanceiro[cnpj_cpf] </td>
            </tr>    
			";
			}
			?>
            
        </table>
        
        <h2 class="c_h2">Produtos Adquiridos</h2>
<table width="700" border="0" cellpadding="2" cellspacing="2" class="c_tabela_itens">
            <tr class="c_titulo_tabela">
                <td width="225">Nome</td>
                <td width="118">Qtde.</td>
                <td width="168">Valor Unit.</td>
                <td width="161">Valor</td>
            </tr>
            <tr>    
                <?php
				foreach($Venda->getItens() as $IndiceItem => $Item){
                            if($Venda->getTipoVenda() == 1 && $Item->getDadosVendaItem('sn_item_perdido') == 1){
                                continue;
                            } 
				?>	
                <td width="225"><?php echo $Item->getNomeProduto();?></td>
                <td width="118"><?php echo $Venda->NFQ($Item->getDadosVendaItem('qtde'));?></td>
                <td width="168"><?php echo $Venda->NFV($Item->getDadosVendaItem('vl_unitario_com_descontos'));?></td>
                <td width="161"><?php echo $Venda->NFV($Item->getDadosVendaItem('vl_total_bruto'));?></td>		
            </tr>
			<?php } ?>
</table>
        
        <h2 class="c_h2">Pagamentos</h2>
        <table width="700" border="0" cellpadding="2" cellspacing="2" class="c_tabela_itens">
            <tr class="c_titulo_tabela">
                <td width="15">#</td>
                <td width="113">Valor Total</td>
                <td width="253">Forma Pagto.</td>
                <td width="116">Cond. Pagto.</td>

                <td width="169">Obs</td>
            </tr>
                <tr>
	                <td width="15">#</td>
                    <td width="113"><?php echo $Venda->NFV($Venda->getVlTotalVendaLiquido());?></td>
<?php
$SqlFormaPagamento = mysql_query("select numreg, nome_forma_pagto from is_forma_pagto where numreg=".$Venda->getDadosVenda('id_forma_pagto'));
$ArSqlFormaPagamento=mysql_fetch_array($SqlFormaPagamento);
?>

                    <td width="253"><?php echo $ArSqlFormaPagamento['nome_forma_pagto'] ?></td>
                    <td width="116"><?php echo $Venda->getDadosCondPagto('nome_cond_pagto');?></td>
                    <td width="169"><?php echo chrbr(strsadds($Venda->getDadosVenda('obs')));?></td>
                </tr>
        </table>

        <h2 class="c_h2">Observação</h2>
<table width="700" border="0" cellpadding="2" cellspacing="2" class="c_tabela_itens">
           
            <tr>
                <td height="50"><?php echo chrbr(strsadds($Venda->getDadosVenda('obs_nf')));?></td>
  </tr>

          </table>  
          
<h2 class="c_h2">Situação Pagamento</h2>
<table width="697" border="0">
  <tr>
    <td width="52" height="49" valign="bottom">Pago:</td>
    <td width="48" align="center" valign="bottom"><table width="34" border="1">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</td>
    <td width="8" valign="bottom">&nbsp;</td>
    <td width="69" valign="bottom">Dt. Pagto:</td>
    <td colspan="2" align="center" valign="bottom">_________</td>
    <td width="66" valign="bottom">Dt. Envio:</td>
    <td colspan="2" align="center" valign="bottom">________</td>
    <td width="242" align="center" valign="bottom">__________________________</td>
  </tr>
  <tr>
    <td height="42" valign="bottom">Aberto:</td>
    <td align="center" valign="bottom"><table width="35" border="1">
      <tr>
        <td width="18">&nbsp;</td>
      </tr>
    </table></td>
    <td valign="bottom">&nbsp;</td>
    <td valign="bottom">Sit. Envio:</td>
    <td width="52" align="center" valign="bottom" style="font-size:8px;"><table width="35" border="1">
      <tr>
        <td width="18">&nbsp;</td>
      </tr>
    </table>
    Enviado</td>
    <td width="46" align="center" valign="bottom" style="font-size:8px;"><table width="35" border="1">
      <tr>
        <td width="18">&nbsp;</td>
      </tr>
    </table> 
      Pendente
</td>
    <td align="center" valign="bottom"  style="font-size:8px;">
        <table width="35" border="1">
          <tr>
            <td width="18">&nbsp;</td>
          </tr>
        </table>
        Em Processo
    </td>
    <td width="54" align="center" valign="bottom" style="font-size:8px;">
    	<table width="35" border="1">
	      <tr>
        	<td width="18">&nbsp;</td>
    	  </tr>
    	</table>
    	Não Autorizado
    </td>
    <td width="18" valign="bottom"></td>
    <td align="center" valign="bottom">Autorizado por</td>
  </tr>
</table>
  <script>
       window.print();
    </script>