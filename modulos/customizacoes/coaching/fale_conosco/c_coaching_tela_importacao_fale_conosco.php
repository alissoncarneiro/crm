<?php
/*
* c_coaching_tela_importacao_fale_conosco.php
* Autor: Alex
* 24/08/2011 16:06:01
*/
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

if($_SESSION['id_usuario'] == ''){
    echo 'Sua sessão expirou, faça login novamente!';
    exit;
}

require('../../../../conecta.php');
require('../../../../functions.php');
require('../../../../classes/class.uB.php');

$SqlFaleConosco = "SELECT * FROM c_coaching_fale_conosco WHERE sn_importado = 0 group by contato_email ORDER BY contato_dt_inclusao asc, contato_nome asc,numreg ASC LIMIT 30";
$QryFaleConosco = query($SqlFaleConosco);
?>
<script type="text/javascript" src="../../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../../js/jquery-ui-1.8.5.custom.min.js"></script>
<link href="../../../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
<link href="../../../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
<link href="../../../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .fs_custom{
        margin: 15px;
    }
    .fs_custom legend{
        font-weight:bold;
        font-size:14px;
    }
    .fs_custom table{
        border: 1px solid #ACC6DB;
    }
    .fs_custom table th{
        font-weight: bold;
        color: #345c7d;
        text-align: left;
        padding-left: 5px;
        background-color: #DAE8F4;
    }
    .campo_data{
        width:65px;
        text-align: center;
    }
</style>
<form action="c_coaching_importacao_fale_conosco_post.php" id="form_fs" method="post">
<fieldset class="fs_custom" id="fs_fale_conosco">
    <legend>Importar Fale Conosco</legend>
    <table width="100%" border="0" cellspacing="2" cellpadding="0">
        <tr>
            <th align="center"><input type="checkbox" id="chk_marcar_todos" title="Marcar todos"/></th>
            <th>Data Inclus&atilde;o</th>
            <th>Nome</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>Mensagem</th>
            <th>Media</th>
            <th>Origem</th>
            <th>Campanha</th>
            <th>Anúncio</th>            
            <th>Usu&aacute;rio Resp.</th>
            <th>Conta</th>
        </tr>
        <?php
        $NumRows = numrows($QryFaleConosco);
                               
                               $SqlPermissaoUsuario = "SELECT numreg,wcp_altera_vendedor_importacao, id_perfil  FROM IS_USUARIO where id_perfil  in (4,1)";
                               $QrySqlPermissaoUsuario = mysql_query($SqlPermissaoUsuario);
                               while($ArQrySqlPermissaoUsuario = mysql_fetch_array($QrySqlPermissaoUsuario)){
                                               if($ArQrySqlPermissaoUsuario['wcp_altera_vendedor_importacao'] == 0){
                                                               $ArrayPermissao[] = $ArQrySqlPermissaoUsuario['numreg'];
                                               }
                               }
                               
        while($ArFaleConosco = farray($QryFaleConosco)){
            $bgcolor = ($i % 2 == 0)?'#EAEAEA':'#FFFFFF';
            $i++;
            
            $IdUsuarioResp = $_SESSION['id_usuario'];
			
			$telefone = "(".$ArFaleConosco['contato_ddd'].")".$ArFaleConosco['contato_tel'];
			$telefone = str_replace(" ", "", $telefone);
			$telefone = str_replace("-", "", $telefone);
			$telefone = str_replace("(", "", $telefone);
			$telefone = str_replace(")", "", $telefone);
			$telefone = trim($telefone);
			
			$email = str_replace(" ", "", $ArFaleConosco['contato_email']);
			$email = trim($email);
            
            $ArPessoas = array();
            $SqlPessoa = "SELECT 
								numreg,
								razao_social_nome,
								id_vendedor_padrao 
							FROM 
								is_pessoa 
							WHERE 
								TRIM(SUBSTRING(REPLACE(email, ' ', ''), 1, LOCATE(';',REPLACE(email, ' ', '')) -1)) = TRIM(REPLACE('".$email."',' ', '')) OR
								TRIM(SUBSTRING(REPLACE(email, ' ', ''), LOCATE(';',REPLACE(email, ' ', '')) +1  )) = TRIM(REPLACE('".$email."',' ', '')) OR
								trim(REPLACE(REPLACE(REPLACE(REPLACE(tel1,')',''),'(',''),'-',''),' ','')) =  '".$telefone."' OR
								trim(REPLACE(REPLACE(REPLACE(REPLACE(fax,')',''),'(',''),'-',''),' ','')) =  '".$telefone."' OR
								trim(REPLACE(REPLACE(REPLACE(REPLACE(wcp_tel3,')',''),'(',''),'-',''),' ','')) =  '".$telefone."'
						";
            $QryPessoa = query($SqlPessoa);
            while($ArPessoa = farray($QryPessoa)){
                $ArPessoas[] = array($ArPessoa['numreg'],$ArPessoa['razao_social_nome']);
                $IdUsuarioResp = $ArPessoa['id_vendedor_padrao'];
            }
            if(count($ArPessoas) == 0){
                $ArPessoas[] = array('','Incluir Prospect');
            }
            
            if($ArFaleConosco['id_usuario_resp'] != ''){
                $IdUsuarioResp = $ArFaleConosco['id_usuario_resp'];
            }
                                               
		   if(in_array($IdUsuarioResp,$ArrayPermissao)){
				$disabled = "disabled=\"disabled\"";
				$checked = "checked=\"checked\"";
		   }
		   else{
				$disabled = ""; 
				$checked = "";
		   }                                             
            
            $QryCampanha = query("SELECT nome_tp_campanha FROM is_tp_campanha WHERE numreg = '".$ArFaleConosco['contato_pagina']."'");
            $ArCampanha = farray($QryCampanha);
            $QryOrigem = query("SELECT nome_origem_conta FROM is_origem_conta WHERE numreg = '".$ArFaleConosco['contato_site']."' ");
            $ArOrigem = farray($QryOrigem);
			
			$QryCampanhaGoogle = query("select nome_auxiliar from is_auxiliar where grupo = 'wcp_campanha_google' and  numreg = '".$ArFaleConosco['wcp_campanha_google']."' ");
			$ArCampanhaGoogle = farray($QryCampanhaGoogle);
			
			$QryOrigemGoogle = query("select nome_auxiliar from is_auxiliar where grupo = 'wcp_grupo_google' and numreg  = '".$ArFaleConosco['wcp_grupo_google']."'");			
			$ArOrigemGoogle = farray($QryOrigemGoogle);
			
            ?>
            <tr bgcolor="<?php echo $bgcolor;?>">
                <td align="center"><input type="checkbox" class="checkbox" <?=$checked?> name="chk_fc_<?php echo $ArFaleConosco['numreg'];?>" id="chk_fc_<?php echo $ArFaleConosco['numreg'];?>" value="1"/></td>
                <td><?php echo uB::DataEn2Br($ArFaleConosco['contato_dt_inclusao']); ?></td>
                <td><?php echo $ArFaleConosco['contato_nome']; ?></td>
                <td><?php echo $ArFaleConosco['contato_ddd'].' '.$ArFaleConosco['contato_tel']; ?></td>
                <td><?php echo $ArFaleConosco['contato_email']; ?></td>
                <td><?php echo $ArFaleConosco['contato_obs']; ?></td>
                <td><?php echo $ArCampanha['nome_tp_campanha']; ?></td>
                <td><?php echo $ArOrigem['nome_origem_conta']; ?></td>

                <td><?php echo $ArCampanhaGoogle['nome_auxiliar']; ?></td>
                <td><?php echo $ArOrigemGoogle['nome_auxiliar']; ?></td>

                <td>
				<?php 
					echo str_replace("<select name","<select  $disabled name", TabelaParaCombobox('is_usuario', 'numreg', 'nome_usuario', 'edtid_usuario_resp_'.$ArFaleConosco['numreg'],$IdUsuarioResp, " WHERE id_perfil in (4,1) or numreg in('1', '138')", " ORDER BY nome_usuario ASC"));
	
   					if($disabled == "disabled=\"disabled\""){?>
 					   <input type="hidden" name="edtid_usuario_resp_hiden_<?=$ArFaleConosco['numreg']?>" value="<?=$IdUsuarioResp?>" />
              		<?php }	?>
                </td>
                <td><?php echo '<select name="edtid_pessoa_',$ArFaleConosco['numreg'],'" id="edtid_pessoa_',$ArFaleConosco['numreg'],'">',Array2Options($ArPessoas),'</select>';?></td>
            </tr>
        <?php } ?>
    </table>
    <?php if($NumRows >= 1){?>
    <hr/>
    <input type="button" id="btn_confirmar_fale_conosco" class="botao_jquery" value="Confirmar"/>
    <input type="button" id="btn_fechar" class="botao_jquery" value="Fechar"/>
    <?php } ?>
</fieldset>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        $(".botao_jquery").button();
        $("#btn_confirmar_fale_conosco").click(function(){
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