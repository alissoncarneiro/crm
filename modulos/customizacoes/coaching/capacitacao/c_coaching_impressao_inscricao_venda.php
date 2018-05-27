<?php
/*
 * c_coaching_impressao_inscricao_venda.php
 * Autor: Alex
 * 23/08/2011 16:35:21
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

session_start();
if($_SESSION['id_usuario'] == ''){
    echo 'Usuário não logado.';
    exit;
}

require_once('../../../../conecta.php');
require_once('../../../../functions.php');
require_once('../../../../classes/class.DataHora.php');

$IdInscricao = $_GET['id_inscricao'];
$IdVenda = $_GET['id_venda'];

$SqlInscricao = "SELECT 
                    t1.dt_inscricao,
                    t1.id_pessoa,
					t1.id_pessoa_financeiro,
                    t2.nome_curso,
                    t3.nome_usuario
                FROM 
                    c_coaching_inscricao t1 
                INNER JOIN 
                    c_coaching_curso t2 ON t1.id_curso = t2.numreg 
                INNER JOIN 
                    is_usuario  t3 ON t1.id_vendedor = t3.numreg
                WHERE 
                    t1.numreg = '".$IdInscricao."'";
$QryInscricao = query($SqlInscricao);
$ArInscricao = farray($QryInscricao);

$SqlPessoa = "SELECT
                    numreg,
                    razao_social_nome,
                    cnpj_cpf,
                    ie_rg,
                    endereco,
					complemento,
                    numero,
                    bairro,
                    cidade,
                    uf,
                    cep,
                    tel1,
                    tel2,
					email,
					email_pessoal,
					id_estcivil,
					wcp_cargo,
					dianascto,
					mesnascto,
					anonascto
                FROM
                    is_pessoa
                WHERE
                    numreg = '".$ArInscricao['id_pessoa']."'";

$QryPessoa = query($SqlPessoa);
$ArPessoa = farray($QryPessoa);

$SqlPessoaFinanceiro = "SELECT
							numreg,
							razao_social_nome,
							email,
							cnpj_cpf
						FROM
							is_pessoa
						WHERE
							numreg = '".$ArInscricao['id_pessoa_financeiro']."'";

$QryPessoaFinanceiro = query($SqlPessoaFinanceiro);
$linhaPessoaFinanceiro = mysql_num_rows($QryPessoaFinanceiro);
$ArPessoaFinanceiro = farray($QryPessoaFinanceiro);

$SqlPessoaDetalhe ="SELECT
						nome_estcivil
					FROM
						is_estcivil
					WHERE
    	                numreg = '".$ArPessoa['id_estcivil']."'";
						
$QryPessoaDetalhe = query($SqlPessoaDetalhe);
$ArPessoaDetalhe = farray($QryPessoaDetalhe);	

$SqlPessoaDetalheProfissao ="SELECT
								nome_cargo
							FROM
								is_cargo
							WHERE
								numreg = '".$ArPessoa['wcp_cargo']."'";
						
$QryPessoaDetalheProfissao = query($SqlPessoaDetalheProfissao);
$ArPessoaDetalheProfissao = farray($QryPessoaDetalheProfissao);	


?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>SB Coaching - Relat&oacute;rio Inscri&ccedil;&atilde;o</title>
        <link href="../../../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link href="../../../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />
        <link href="c_style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <img src="../../../../images/logo_coaching_pequeno.png" alt="Sociedade Brasileira de Coaching"/>
        <h2 class="c_h2">Dados da Inscri&ccedil;&atilde;o N&ordm; <?php echo $IdInscricao; echo (($IdVenda != '')?'/'.$IdVenda:'');?></h2>
        <table width="700" border="0" cellpadding="2" cellspacing="2" class="c_tabela_itens">
            <tr>
                <td class="c_td_label" width="100">Treinamento:</td>
                <td><?php echo $ArInscricao['nome_curso'] ;?></td>
            </tr>
            <tr>
                <td class="c_td_label">Data Inscri&ccedil;&atilde;o:</td>
                <td><?php echo dten2br($ArInscricao['dt_inscricao']); ?></td>
            </tr>
            <tr>
                <td class="c_td_label">Vendedor:</td>
                <td><?php echo $ArInscricao['nome_usuario']; ?></td>
            </tr>
            <tr>
                <td class="c_td_label">Nome Completo:</td>
                <td><?php echo $ArPessoa['razao_social_nome']; ?></td>
            </tr>
            <tr>
                <td class="c_td_label">CPF:</td>
                <td><?php echo $ArPessoa['cnpj_cpf']; ?></td>
            </tr>            
            <tr>
                <td class="c_td_label">RG:</td>
                <td><?php echo $ArPessoa['ie_rg']; ?></td>
            </tr>
            <tr>
                <td class="c_td_label">Estado Civil:</td>
                <td><?php echo $ArPessoaDetalhe['nome_estcivil']; ?></td>
            </tr>  

            <tr>
                <td class="c_td_label">Profissão / Cargo:</td>
                <td><?php echo $ArPessoaDetalheProfissao['nome_cargo']; ?></td>
            </tr>  
            
            <tr>
                <td class="c_td_label">Data de Nascimento:</td>
                <td><?php echo $ArPessoa['dianascto'],'/', $ArPessoa['mesnascto'],'/', $ArPessoa['anonascto'] ?></td>
            </tr>  
                                              
            <tr>
                <td class="c_td_label">Endere&ccedil;o:</td>
                <td><?php 
					echo $ArPessoa['endereco']; 
					echo (($ArPessoa['numero'] != '')?', '.$ArPessoa['numero']:'');  
				    echo (($ArPessoa['complemento'] != '')?', '.$ArPessoa['complemento']:'');?>
                </td>
            </tr>
            <tr>
                <td class="c_td_label">Bairro:</td>
                <td><?php echo $ArPessoa['bairro']; ?></td>
            </tr>
            <tr>
                <td class="c_td_label">Cidade:</td>
                <td><?php echo $ArPessoa['cidade']; ?></td>
            </tr>
            <tr>
                <td class="c_td_label">UF:</td>
                <td><?php echo $ArPessoa['uf']; ?></td>
            </tr>
            <tr>
                <td class="c_td_label">CEP:</td>
                <td><?php echo $ArPessoa['cep']; ?></td>
            </tr>
            <tr>
                <td class="c_td_label">Fones:</td>
                <td><?php echo $ArPessoa['tel1'],' ',$ArPessoa['tel2'] ?></td>
            </tr>
            <tr>
                <td class="c_td_label">Email:</td>
                <td><?php echo $ArPessoa['email'] ,' / ',$ArPessoa['email_pessoal']  ?></td>
            </tr>
            
            <?php 
			if ($linhaPessoaFinanceiro != 0){
			echo"
			<tr>
                <td class=\"c_td_label\">Cliente Financeiro:</td>
                <td> $ArPessoaFinanceiro[razao_social_nome] </td>
            </tr>    
			<tr>
                <td class=\"c_td_label\">CNPJ / CPF:</td>
                <td> $ArPessoaFinanceiro[cnpj_cpf] </td>
            </tr>   
			<tr>
                <td class=\"c_td_label\">Email Cliente Financeiro:</td>
                <td> $ArPessoaFinanceiro[email] </td>
            </tr>    
			";
			}
			?>
        </table>
        <h2 class="c_h2">M&oacute;dulos adquiridos</h2>
        <table width="700" border="0" cellpadding="2" cellspacing="2" class="c_tabela_itens">
            <tr class="c_titulo_tabela">
                <td>M&oacute;dulo</td>
                <td>Local</td>
                <td>Datas</td>
            </tr>
            <?php
            $SqlAgendaCurso = "SELECT
                                    t1.id_agenda,t2.nome_modulo,t3.nome_local_curso
                                FROM
                                    c_coaching_inscricao_curso t1
                                INNER JOIN
                                    c_coaching_modulo t2 ON t1.id_modulo = t2.numreg
                                INNER JOIN
                                    c_coaching_local_curso t3 ON t1.id_local_curso = t3.numreg
                                WHERE
                                    t1.id_inscricao = '".$IdInscricao."' and   t1.id_situacao <> '3'";
            if($IdVenda != ''){
                $SqlAgendaCurso .= " AND t1.id_venda = '".$IdVenda."'";
            }
            $QryAgendaCurso = query($SqlAgendaCurso);
            $i = 0;
            while($ArAgendaCurso = farray($QryAgendaCurso)){
                $bgcolor = ($i % 2 == 0)?'#EAEAEA':'#FFFFFF';
                $i++;
                $SqlDatas = "SELECT GROUP_CONCAT(DATE_FORMAT(dt_curso,'%d/%m/%Y') SEPARATOR ', ') AS datas FROM c_coaching_agenda_curso_detalhe WHERE id_agenda_curso = '".$ArAgendaCurso['id_agenda']."' ORDER BY dt_curso ASC";
                $QryDatas = query($SqlDatas);
                $ArDatas = farray($QryDatas);
            ?>
            <tr bgcolor="<?php echo $bgcolor; ?>">
                <td><?php echo $ArAgendaCurso['nome_modulo'];?></td>
                <td><?php echo $ArAgendaCurso['nome_local_curso'];?></td>
                <td><?php echo $ArDatas['datas'];?></td>
            </tr>
            <?php } ?>
        </table>
        <h2 class="c_h2">Pagamentos</h2>
        <table width="700" border="0" cellpadding="2" cellspacing="2" class="c_tabela_itens">
            <tr class="c_titulo_tabela">
                <td width="15">#</td>
                <td>Valor Total</td>
                <td>Forma Pagto.</td>
                <td>Cond. Pagto.</td>
                <td>1 &ordm; Vencimento</td>
                <td>Tipo Pagto.</td>
                <!--<td>Estabelecimento</td>-->
                <td>Obs</td>
	       	</tr>
            <?php
            $VlTotalPagtos = 0;
            $SqlGradePagto = "SELECT
                                    t1.numreg,
                                    t1.vl_pagto,
                                    t1.obs,
                                    t2.nome_forma_pagto,
                                    t3.nome_cond_pagto,
                                    t4.nome_tp_pagto,
                                    t1.dt_primeiro_pagto
                                    FROM
                                        c_coaching_inscricao_pagto t1
                                    INNER JOIN
                                        is_forma_pagto t2 ON t1.id_forma_pagto = t2.numreg
                                    INNER JOIN
                                        is_cond_pagto t3 ON t1.id_cond_pagto = t3.numreg
                                    INNER JOIN 
										c_coaching_tp_pagto t4 ON t1.id_tp_pagto = t4.numreg																		
                                    WHERE
                                        t1.id_inscricao = '".$IdInscricao."'";
            if($IdVenda != ''){
                $SqlGradePagto .= " AND t1.id_venda = '".$IdVenda."'";
            }
            $QryGradePagto = query($SqlGradePagto);
            $i = 0;
            while($ArGradePagto = farray($QryGradePagto)){
                $bgcolor = ($i % 2 == 0)?'#EAEAEA':'#FFFFFF';
                $i++;
                $VlTotalPagtos += $ArGradePagto['vl_pagto'];
                ?>
                <tr bgcolor="<?php echo $bgcolor; ?>">
                    <td><?php echo $i; ?></td>
                    <td><?php echo number_format($ArGradePagto['vl_pagto'],2,',','.');?></td>
                    <td><?php echo $ArGradePagto['nome_forma_pagto'];?></td>
                    <td><?php echo $ArGradePagto['nome_cond_pagto'];?></td>
                    <td><?php echo dten2br($ArGradePagto['dt_primeiro_pagto']);?></td>
                    <td><?php echo $ArGradePagto['nome_tp_pagto'];?></td>
                    <!--<td><?php echo $ArGradePagto['nome_estabelecimento'];?></td>-->
					<td><?php echo $ArGradePagto['obs'];?></td>
                </tr>
        <?php } ?>
                <tr>
                    <td colspan="7"><strong>Total: </strong><?php echo number_format($VlTotalPagtos,2,',','.');?></td>
                </tr>
        </table>
        <script type="text/javascript">
           window.print();
        </script>
    </body>
</html>