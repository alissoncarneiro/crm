<style type="text/css">
*{
	outline-color:invert;
	outline-style:none;
	outline-width:medium;
	font-family: "Tahoma";
}
html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre,a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, dl, dt, dd, ol, ul, li,fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td {
    margin: 0;
	padding: 0;  
	outline: 0; 
	font-weight: normal; 
	font-style:inherit; 
	font-size: inherit;
	font-family:Arial, Helvetica, sans-serif;
	vertical-align: baseline;
}
body {
	line-height: 15px; 
	background: white; 
	font-size:100.01%;
	}
ol, ul { 
	list-style: none;
	}
caption, th, td {
	text-align: left; 
	font-weight: normal;
	}
strong{ 
	font-weight: bold; 
	}
.titulo{
	font-size:16px;
	text-align:center;
	margin-bottom:50px;	
	padding-top:20px;
	font-family:Arial, Helvetica, sans-serif;
	}
.logo{
	text-align:right;
	}
.termo{
	font-family:"Tahoma";
	font-size:12px;
	margin-bottom:30px;
	line-height:25px;
	margin-left:30px;
	margin-right:30px;
	text-align:justify;
		}
.termo p{
	padding-top:10px;}	
.dataAssina{
	font-family:"Tahoma";
	font-size:12px;
	margin-bottom:30px;
	padding-top:20px;
	line-height:25px;
	text-align:justify;
}
.dataAssinaTermo{
	font-family:"Tahoma";
	font-size:12px;
	line-height:16px;
	text-align:justify;
	padding-top:60px;
}
.quebra_pagina { 
	page-break-after: always; 
}

td{
	padding:3px;
	}
</style>
<?php

# contrato
# Expression package is undefined on line 3, column 5 in Templates/Scripting/EmptyPHP.php.
# Autor: Rodrigo Piva
# 09/09/2011
#
# Log de Alterações
# yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
#

require('../../../../conecta.php');
require('../../../../functions.php');
require('../../../../funcoes.php');

$desc_mes = array('01' => 'Janeiro',
	          '02' => 'Fevereiro',
                  '03' => 'Mar&ccedil;o',
                  '04' => 'Abril',
                  '05' => 'Maio',
                  '06' => 'Junho',
                  '07' => 'Julho',
                  '08' => 'Agosto',
                  '09' => 'Setembro',
                  '10' => 'Outubro',
                  '11' => 'Novembro',
                  '12' => 'Dezembro'
                  );


if($_GET['id_inscricao']){
	$CodInscricao = $_GET['id_inscricao'];
	$SqlInscricao = "SELECT * FROM c_coaching_inscricao WHERE numreg = '".$CodInscricao."'";
	$QryInscricao = query($SqlInscricao);
}
else{
	$SqlInscricao="SELECT
	InscricaoCurso.id_pessoa, InscricaoCurso.id_pessoa_licenciado,nome.razao_social_nome, InscricaoCurso.id_inscricao, c.*
		FROM c_coaching_inscricao_curso_detalhe as InscricaoCurso
		INNER JOIN c_coaching_inscricao AS c
		ON c.id_pessoa = InscricaoCurso.id_pessoa
		INNER JOIN  is_pessoa as nome
		on c.id_pessoa = nome.numreg
	  where InscricaoCurso.id_agenda ='".$_GET['id_agenda']."' AND c.id_situacao in(2,4)
   	group by InscricaoCurso.id_pessoa
	order by razao_social_nome asc";
	$QryInscricao = query($SqlInscricao);
}

while ($ArInscricao  = farray($QryInscricao)){
$IdCliente = ($ArInscricao['id_pessoa_financeiro'] != '')?$ArInscricao['id_pessoa_financeiro']:$ArInscricao['id_pessoa'];

$SqlPessoa    = "SELECT * FROM is_pessoa WHERE numreg = '".$IdCliente."'";
$QryPessoa    = query($SqlPessoa);
$ArPessoa     = farray($QryPessoa);

$NomeCurso         = deparaIdErpCrm($ArInscricao['id_curso'],"nome_curso","numreg", "c_coaching_curso");
$NomeNacionalidade = deparaIdErpCrm($ArPessoa['id_nacionalidade'],"nome_nacionalidade","numreg", "is_nacionalidade");
$EstadoCivil       = deparaIdErpCrm($ArPessoa['id_estcivil'],"nome_estcivil","numreg", "is_estcivil");
$Cargo             = deparaIdErpCrm($ArPessoa['wcp_cargo'],"nome_cargo","numreg","is_cargo");
$Estabelecimento   = deparaIdErpCrm($ArInscricao['id_estabelecimento'],"nome_estabelecimento","numreg","is_estabelecimento");
$CnpjEstabel       = deparaIdErpCrm($ArInscricao['id_estabelecimento'],"cnpj_cpf","numreg","is_estabelecimento");
$Mascara           = '##.###.###/####-##';
$CnpjFormatado     = MascaraNumerica($Mascara, $CnpjEstabel);

$NomeCliente = $ArPessoa['razao_social_nome'];
$CNPJCPFCliente = $ArPessoa['cnpj_cpf'];
if($ArPessoa['id_tp_pessoa'] == '1' || $ArPessoa['id_tp_pessoa'] == '2'){
    $Mascara = ($ArPessoa['id_tp_pessoa'] == '1')?'##.###.###/####-##':'###.###.###-##';
    $LabelCNPJCPF = ($ArPessoa['id_tp_pessoa'] == '1')?'CNPJ':'CPF';
    $CNPJCPFCliente = MascaraNumerica($Mascara, $CNPJCPFCliente);
}
?>
<p class="logo"><img src="clip_image002_0001.gif" alt="" width="213" height="104" /></p>
<p class="titulo">TERMO DE RESPONSABILIDADE</p><br>

<div class="termo">
Eu, <?php echo $ArPessoa['razao_social_nome']; 
echo ($NomeNacionalidade == "")?"":", ".$NomeNacionalidade;
echo ($EstadoCivil == "")?"":", ".$EstadoCivil;
echo ", Maior de idade, capaz";
echo ($Cargo == "")?"":", ".$Cargo;
echo ", inscrito(a) no 
".$LabelCNPJCPF." ".$CNPJCPFCliente.", residente e domiciliado(a) na ".$ArPessoa['endereco'].", nr:".$ArPessoa['numero'].", ".$ArPessoa['bairro'].",
".$ArPessoa['cidade']."/".$ArPessoa['uf'].", CEP ".$ArPessoa['cep'];?>, por meio deste instrumento declaro, após receber instruções detalhadas do trainer que ministra o treinamento de Xtreme Life Coaching sobre a dinâmica de quebrar madeira e aclarar todas as minhas dúvidas, que:

<p>a) entendo os riscos implícitos neste tipo de prática, podendo ocorrer lesões de ordem física no transcorrer das atividades;</p>
<p>b) entendo, após orientação do trainer, que não sou obrigado a participar da mesma (dinâmica) e que isto nada me prejudicará na obtenção do certificado de conclusão;</p>
<p>c) responsabilizo-me por qualquer dano ou acidente que possa ocorrer comigo no desenvolver da atividade supracitada, isentando Sociedade Brasileira de Coaching, ou mesmo qualquer terceiro ligado a esta.</p>
<br />
</div>

<div class="dataAssina">
	<p align="center">S&atilde;o Paulo, _____ de __________ de  <?= date("Y")?>.</p>
</div>    

<div class="dataAssinaTermo">
      <p align="center">__________________________________________________</p>
      <p align="center"><span class="assinaturas"><?php echo $ArPessoa['razao_social_nome']; ?></span></p>
      <p align="center"><span class="assinaturas"><?php echo $LabelCNPJCPF.': '.$CNPJCPFCliente;?></span></p>

</div>

<p class="quebra_pagina"></p>
<?php } ?>  
