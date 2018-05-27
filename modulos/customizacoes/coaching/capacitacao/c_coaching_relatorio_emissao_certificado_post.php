<?php
/*
 * c_coaching_reltorio_emissao_certificado_post.php
 * Autor: Alex
 * 18/08/2011 13:00:00
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

session_start();
if($_SESSION['id_usuario'] == ''){
    echo 'Usuário não logado.';
    exit;
}

require('../../../../conecta.php');
require('../../../../functions.php');
require('../../../../classes/class.uB.php');
require('c_coaching.class.Inscricao.php');

?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>SB Coaching - Relat&oacute;rio Emiss&atilde;o de Certificado</title>
        <link href="../../../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link href="../../../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />
        <link href="c_style.css" rel="stylesheet" type="text/css" />
        <link href="../../../venda/estilo_venda.css" rel="stylesheet" type="text/css" />
        <link href="../../../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../../js/jquery-ui-1.8.5.custom.min.js"></script>
    </head>
    <body style="margin: 20px;">
        <h3 style="font-size: 16px;">Relat&oacute;rio Emiss&atilde;o de Certificado - <?php echo date("d/m/Y H:i:s");?></h3>
        <?php
        if($_GET['id_curso'] != ''){
            $QryCurso = query("SELECT * FROM c_coaching_curso WHERE numreg = '".$_GET['id_curso']."'");
            $ArCurso = farray($QryCurso);
            $NomeCurso = $ArCurso['nome_curso'];
        }
        else{
            $NomeCurso = 'Todos';
        }
        if($_GET['id_situcao'] != ''){
            if($_GET['id_situcao'] == '1'){
                $NomeSituacao = 'Aptos';
            }
            elseif($_GET['id_situcao'] == '2'){
                $NomeSituacao = 'Inaptos';
            }
        }
        else{
            $NomeSituacao = 'Todos';
        }
		if($_GET['id_agenda'] != "") {
			$agenda = $_GET['id_agenda'];
		}
		else {
			$agenda = "Todas";
		}
		
        ?>
        <h3>Curso: <?php echo $NomeCurso;?></h3>
        <h3>Situa&ccedil;&atilde;o: <?php echo $NomeSituacao;?></h3>
        <h3>Agenda: <?php echo $agenda;?></h3>
        <span style="font-size:14px;">Legenda: </span>
        <span style="background-color:#DFF2BF; font-size: 14px;">Aptos</span>&nbsp;&nbsp;&nbsp;
        <span style="background-color:#FFBABA; font-size: 14px;">Inaptos</span>
        <hr/>
        <form action="c_coaching_confirma_emissao_certificado.php" id="form_fs" method="post">
            <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="venda_tabela_itens">
                <tr class="venda_titulo_tabela">
                    <td>C&oacute;d. Inscri&ccedil;&atilde;o</td>
                    <td>Agenda</td>
                    <td>Cliente</td>
                    <td>Instrutor</td>
                    <td>Curso</td>
                    <td>Hora Presencial</td>
                    <td>Dt. Conclusão</td>
                     <td align="center"><input type="checkbox" id="chk_marcar_todos" title="Marcar todos"/></td>
                </tr>
                <?php
                $SqlClientesAptosReceberCertificado = "SELECT
                                                          t1.*,
                                                          t2.razao_social_nome,
                                                          t3.nome_curso,
                                                          usuario.nome_usuario,
														  agenda_curso.numreg as agenda,
														  sum(parte.carga_horaria_etapa) as horas
                                                        FROM c_coaching_inscricao t1
                                                        
														INNER JOIN is_pessoa t2 ON t1.id_pessoa = t2.numreg
                                                        INNER JOIN c_coaching_curso t3 ON t1.id_curso = t3.numreg
                                                        
                                                        INNER JOIN c_coaching_inscricao_curso AS inscricao
                                                        ON inscricao.id_inscricao = t1.numreg and inscricao.id_situacao <> 3
                                                        
													    LEFT JOIN c_coaching_agenda_curso as agenda_curso
													      ON agenda_curso.numreg = (select max(id_agenda) from  c_coaching_inscricao_curso 
														  														where id_inscricao = t1.numreg and id_situacao <> 3) 
																												
														INNER JOIN c_coaching_parte as parte
                                                        on parte.numreg = agenda_curso.id_parte																												
													
                                                        LEFT JOIN is_usuario AS  usuario
                                                        ON  usuario.numreg = agenda_curso.id_instrutor
                                                            
                                                        WHERE
                                                            t1.id_situacao = 4
                                                        AND
                                                            t1.sn_certificado_emitido = 0";
                
                if($_GET['id_curso'] != ''){
                    $SqlClientesAptosReceberCertificado .= " AND t1.id_curso = '".$_GET['id_curso']."' ";
                }
                if($_GET['id_agenda'] != ''){
                    $SqlClientesAptosReceberCertificado .= " AND agenda_curso.numreg = '".$_GET['id_agenda']."' ";
                }			
                $SqlClientesAptosReceberCertificado .= " GROUP BY t1.numreg
                                                             ORDER BY 
                                                            t2.razao_social_nome";
																		
                                                            
                $QryClientesAptosReceberCertificado = query($SqlClientesAptosReceberCertificado);
                while($ArClientesAptosReceberCertificado = farray($QryClientesAptosReceberCertificado)){
                $Apto = true;
                if($ArClientesAptosReceberCertificado['id_curso'] == '1'){
                    if($ArClientesAptosReceberCertificado['c1_sn_projeto_completo'] == '10027' || $ArClientesAptosReceberCertificado['c1_sn_projeto_completo'] == ''  ){
                        $Apto = false;
                    }
                    elseif($ArClientesAptosReceberCertificado['c1_sn_pagto_quitado'] != '1'){
                        $Apto = false;
                    }
					elseif($ArClientesAptosReceberCertificado['ead_completo'] != '1'){
                        $Apto = false;
                    }
                    else{
                        $Inscricao = new Inscricao($ArClientesAptosReceberCertificado['numreg']);
                        $PctFrequencia = $Inscricao->getFrequenciaPresenca();
                        if($PctFrequencia < 75){
                            $Apto = false;
                        }
                    }
                }
                elseif($ArClientesAptosReceberCertificado['id_curso'] == '2'){
					
					if($ArClientesAptosReceberCertificado['c1_sn_projeto_completo'] == '10027' || $ArClientesAptosReceberCertificado['c1_sn_projeto_completo'] == ''  ){
						$Apto = false;
                    }
					elseif($ArClientesAptosReceberCertificado['edtc2_sn_form_certificao_alpha'] == '10027' || $ArClientesAptosReceberCertificado['edtc2_sn_form_certificao_alpha'] == ''){
                        $Apto = false;
                    }
					elseif($ArClientesAptosReceberCertificado['c2_sn_entrega_trab_grupo'] != '1'){
                        $Apto = false;
                    }
					elseif($ArClientesAptosReceberCertificado['c2_sn_pagto_quitado'] != '1'){
                        $Apto = false;
                    }
					elseif($ArClientesAptosReceberCertificado['ead_completo'] != '1'){
                        $Apto = false;
                    }
                    elseif($ArClientesAptosReceberCertificado['c2_sn_proposta_comercial'] != '1'){
                        $Apto = false;
                    }
                    else{
                        $Inscricao = new Inscricao($ArClientesAptosReceberCertificado['numreg']);
                        $PctFrequencia = $Inscricao->getFrequenciaPresenca();
                        if($PctFrequencia < 75){
                            $Apto = false;
                        }
                    }
                }
                 elseif($ArClientesAptosReceberCertificado['id_curso'] == '7'){
					
					if($ArClientesAptosReceberCertificado['c1_sn_projeto_completo'] == '10027' || $ArClientesAptosReceberCertificado['c1_sn_projeto_completo'] == ''  ){
						$Apto = false;
                    }
					elseif($ArClientesAptosReceberCertificado['c1_sn_pagto_quitado'] != '1'){
                        $Apto = false;
                    }
					elseif($ArClientesAptosReceberCertificado['ead_completo'] != '1'){
                        $Apto = false;
                    }
                    else{
                        $Inscricao = new Inscricao($ArClientesAptosReceberCertificado['numreg']);
                        $PctFrequencia = $Inscricao->getFrequenciaPresenca();
                        if($PctFrequencia < 75){
                            $Apto = false;
                        }
                    }
                }
				
                if($_GET['id_situcao'] == '1' && $Apto === false){
                    continue;
                }
                elseif($_GET['id_situcao'] == '2' && $Apto === true){
                    continue;
                }
                
                $bgcolor = ($Apto === true)?'#DFF2BF':'#FFBABA';
                ?>
                <tr bgcolor="<?php echo $bgcolor;?>">
                    <td><?php echo $ArClientesAptosReceberCertificado['numreg'];?></td>
                    <td><?php echo $ArClientesAptosReceberCertificado['agenda'];?></td>
                    <td><?php echo $ArClientesAptosReceberCertificado['razao_social_nome'];?></td>
                    <td><?php echo $ArClientesAptosReceberCertificado['nome_usuario'];?></td>
                    <td><?php echo $ArClientesAptosReceberCertificado['nome_curso'];?></td>
                    <td><?php echo $ArClientesAptosReceberCertificado['horas'];?></td>
                    <td><?php echo dten2br($ArClientesAptosReceberCertificado['dt_conclusao']);?></td>
                    <td align="center"><input type="checkbox" class="checkbox"  name="chk_fc-<?php echo $ArClientesAptosReceberCertificado['numreg'];?>" id="chk_fc-<?php echo $ArClientesAptosReceberCertificado['numreg'];?>" value="<?php echo $ArClientesAptosReceberCertificado['numreg'];?>"/></td>
                </tr>
                <?php } ?>
            </table>
            <hr/>
          	<table width="100%">
            	<tr>
                	<td colspan="6" align="right">
                        <input type="button" id="btn_confirmar_emissao_certificado" class="botao_jquery" value="Confirmar"/>
                        <input type="button" id="btn_fechar" class="botao_jquery" value="Fechar"/>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
<script type="text/javascript">
    $(document).ready(function(){
        $(".botao_jquery").button();
        $("#btn_confirmar_emissao_certificado").click(function(){
            if(confirm("Todos dos dados estão corretos ?")){
                $("#form_fs").submit();
            }
        });
        $("#chk_marcar_todos").click(function(){
            var Checked = $(this).attr("checked");
            $(".checkbox").each(function(){
                if(!$(this).is(':disabled')){
                    $(this).attr("checked",Checked);
                }
            });
        });
        $("#btn_fechar").click(function(){
            if(confirm('Deseja fechar ?')){
                window.close();
            }
        });
    });
</script>