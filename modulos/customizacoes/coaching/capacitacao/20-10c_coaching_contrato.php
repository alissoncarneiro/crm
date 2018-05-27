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
    font-family: "Tahoma"; 
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
	font-size:14px;
	font-weight:bold;
	text-decoration:underline;
	text-align:center;
	font-family:"Century Gothic";
	}
.logo{
	text-align:right;
	}
.clausuras{
	font-weight:bold;
	}
.paragrafo_clausuras{
	font-family:Tahoma;
	font-size:11px;
	margin-bottom:20px;
		}
.titulo_clausuras{
	text-align:center;
	font-weight:bold;
	font-family:Tahoma;
	font-size:12px;
	}
.quebra_pagina { 
	page-break-after: always; 
}
.tabela{
	padding:10px;
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
	$SqlInscricao = "SELECT * FROM c_coaching_inscricao WHERE numreg = '".$_GET['id_inscricao']."'";
	$QryInscricao = query($SqlInscricao);
	
}
else{
	$SqlInscricao="
		SELECT
			  InscricaoCurso.id_pessoa, InscricaoCurso.id_pessoa_licenciado,nome.razao_social_nome, InscricaoCurso.id_inscricao, InscricaoCurso.id_curso ,c.*
				FROM c_coaching_inscricao_curso_detalhe as InscricaoCurso
          INNER JOIN c_coaching_inscricao AS c
				  ON c.id_pessoa = InscricaoCurso.id_pessoa and  c.id_curso =InscricaoCurso.id_curso
          INNER JOIN  is_pessoa as nome
			    ON c.id_pessoa = nome.numreg
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
<p class="titulo">CONTRATO  PARTICULAR DE PRESTA&Ccedil;&Atilde;O DE SERVI&Ccedil;OS</p><br>

<div class="paragrafo_clausuras">
<span class="clausuras"> CONTRATANTE:</span> <?php echo $ArPessoa['razao_social_nome']; 
echo ($NomeNacionalidade == "")?"":", ".$NomeNacionalidade;
echo ($EstadoCivil == "")?"":", ".$EstadoCivil;
echo ($Cargo == "")?"":", ".$Cargo;
echo ", inscrito(a) no 
".$LabelCNPJCPF." ".$CNPJCPFCliente.", residente e domiciliado(a) na ".$ArPessoa['endereco'].", nr:".$ArPessoa['numero']." - ".$ArPessoa['complemento']."". ", ".$ArPessoa['bairro'].",".$ArPessoa['cidade']."/".$ArPessoa['uf'].", CEP ".$ArPessoa['cep'];?>.
<br>
<span class="clausuras">CONTRATADA:</span> <?php echo $Estabelecimento;?>, empresa inscrita no CNPJ sob o n&ordm; <?php echo $CnpjFormatado;?>, estabelecida na  Avenida Fagundes Filho n&ordm; 141, Vila Monte Alegre, S&atilde;o Paulo/SP, CEP 04304-010. <br />
Pelo presente instrumento, resolvem, m&uacute;tua e reciprocamente, celebrar  Contrato Particular de Presta&ccedil;&atilde;o de Servi&ccedil;os, nos seguintes termos:&nbsp;
</div>  
<br>

<div class="paragrafo_clausuras">
<center><span class="titulo_clausuras">DO OBJETO</span></center>
<span class="clausuras">CL&Aacute;USULA 1 -</span> Constitui  objeto do presente contrato a presta&ccedil;&atilde;o de servi&ccedil;os de ensino em treinamento  denominado <?php echo $NomeCurso;?>, cuja especifica&ccedil;&atilde;o detalhada encontra-se na  cl&aacute;usula 10, no quadro descritivo, ministrado por profissionais selecionados pela CONTRATADA e de total conhecimento da  CONTRATANTE.<br />
<span class="clausuras">PAR&Aacute;GRAFO &Uacute;NICO - </span> Est&atilde;o inclusos no valor pago pelo objeto contratual  material de apoio, certificado e coffee break.
</div>

<div class="paragrafo_clausuras">
<center><span class="titulo_clausuras">DAS OBRIGA&Ccedil;&Otilde;ES DA  CONTRATADA</span></center>
<span class="clausuras">CL&Aacute;USULA 2 -</span> A  CONTRATADA se compromete a realizar o <span class="StyleRed"><?php echo $NomeCurso;?></span> nas  datas estipuladas na cl&aacute;usula 10, no quadro descritivo. Caso haja a necessidade  de mudan&ccedil;as nas datas o CONTRATADO notificar&aacute; o CONTRATANTE com anteced&ecirc;ncia  m&iacute;nima de 72 horas.
<br />

<span class="clausuras">CL&Aacute;USULA 3 &ndash; </span> 
A CONTRATADA emitirá certificado de conclusão/participação no <?php echo $NomeCurso;?>, sendo este condicionado ao cumprimento integral da cláusula 5, parágrafo 1º, freqüência mínima de 75% das horas presenciais e 100% das horas de ensino a distância - EAD, entrega do projeto de certificação e conseqüente aprovação deste pela CONTRATADA, bem como aprovação em avaliação de conduta ética comportamental. O descumprimento de quaisquer dos requisitos desta cláusula desobriga a CONTRATADA da emissão do certificado citado.

<br />
<span class="clausuras">CLAUSULA 4 &ndash; </span>A CONTRATADA  proporcionar&aacute; gratuitamente ao CONTRATANTE a reposi&ccedil;&atilde;o de aula(s) que  porventura este n&atilde;o possa comparecer desde que o &uacute;ltimo avise expressamente  (por escrito) com anteced&ecirc;ncia m&iacute;nima de 72 horas. 
<br />
<span class="clausuras">PAR&Aacute;GRAFO &Uacute;NICO - </span> Caso o aviso n&atilde;o seja feito, ou mesmo procedido desrespeitando a anteced&ecirc;ncia  m&iacute;nima determinada no caput, a CONTRATADA cobrar&aacute; da CONTRATANTE para a  realiza&ccedil;&atilde;o da reposi&ccedil;&atilde;o a taxa de R$ 200,00 (duzentos reais) por dia de  reposi&ccedil;&atilde;o.
</div>

<div class="paragrafo_clausuras">
<center><span class="titulo_clausuras">DAS  OBRIGA&Ccedil;&Otilde;ES DO CONTRATANTE</span></center>
<span class="clausuras">CL&Aacute;USULA 5 &ndash; </span>O CONTRATANTE pagar&aacute; a CONTRATADA pela presta&ccedil;&atilde;o de servi&ccedil;os  os valores previstos na cl&aacute;usula 10, no quadro descritivo.
<br />
<span class="clausuras">PAR&Aacute;GRAFO 1&ordm; - </span>O CONTRATANTE  dever&aacute; respeitar a forma de pagamento convencionada e descrita na cl&aacute;usula 10,  no quadro descritivo, bem como efetuar os pagamentos das parcelas (caso sua  op&ccedil;&atilde;o de pagamento seja parcelada) pontualmente nas datas descriminadas, sob  pena de multa 2% ao m&ecirc;s, juros de 1% ao m&ecirc;s e corre&ccedil;&atilde;o monet&aacute;ria, honor&aacute;rios  advocat&iacute;cios e eventuais custas processuais.
<br />
<span class="clausuras">PAR&Aacute;GRAFO 2&ordm; - </span>O  CONTRATANTE somente poder&aacute; participar do treinamento se na data de realiza&ccedil;&atilde;o  deste estiver com os pagamentos em dia, nos moldes convencionados na cl&aacute;usula  9, quadro demonstrativo, reservando a CONTRATADA o direito de&nbsp; impedir seu ingresso no recinto onde este  ser&aacute; ministrado.
<br />
<span class="clausuras">PAR&Aacute;GRAFO 3&ordm; - </span>O CONTRATANTE declara estar ciente que ao assinar o presente instrumento, comprometendo-se a freqüentar o treinamento objeto deste, imporá a CONTRATADA despesas fixas (honorários dos palestrantes, estrutura administrativa, reserva de vaga, coffe break, material gráfico, acesso a plataforma EAD, etc) imediatamente após a contratação, motivo pelo qual será devido o valor firmado na ficha de inscrição e descrito na cláusula 10.
<br />
<span class="clausuras">PAR&Aacute;GRAFO 4&ordm; - </span>&Eacute; dever do  CONTRATANTE freq&uuml;entar o
<?php echo $NomeCurso;?>com pontualidade, assiduidade e  interesse.
</div>

<div class="quebra_pagina"></div> 

<div class="paragrafo_clausuras">
<center><span class="titulo_clausuras">ASPECTOS  GERAIS DA PRESTA&Ccedil;&Atilde;O DE SERVI&Ccedil;OS</span></center>
<span class="clausuras">CL&Aacute;USULA 6 - </span> O <?php echo $NomeCurso;?> n&atilde;o se  equivale &agrave;s atividades de aconselhamento, terapia, psicoterapia, psican&aacute;lise,  diagn&oacute;stico, tratamento de doen&ccedil;as f&iacute;sicas ou mentais, entre outras atividades  de natureza m&eacute;dica, jur&iacute;dica ou espiritual, n&atilde;o devendo o CONTRATANTE  utiliz&aacute;-la como substituta para tais.
<br />

<span class="clausuras">PARÁGRAFO 1º - </span> Caso o CONTRATANTE seja portador de alguma doença psíquica, mental, ou física, este deverá informar expressamente (por escrito) ao CONTRATADO com antecedência mínima de 10 dias do treinamento. 



<span class="clausuras">PARÁGRAFO 2º - </span> Com base nas informações fornecidas pelo CONTRATANTE, nos moldes descritos no parágrafo 1º, o CONTRATADO poderá impedir que o primeiro curse o treinamento, devendo o segundo devolver toda e qualquer quantia paga até então.

<span class="clausuras">CL&Aacute;USULA 7 - </span>Em decorr&ecirc;ncia de acontecimentos, oriundos de caso  fortuito ou for&ccedil;a maior, que venham a impossibilitar a realiza&ccedil;&atilde;o do <?php echo $NomeCurso;?> objeto deste, dever&aacute; a CONTRATADA remarcar  as datas de realiza&ccedil;&atilde;o do mesmo ou devolver os valores at&eacute; ent&atilde;o pagos pelo  ALUNO proporcionalmente.
<br>
<span class="clausuras">CL&Aacute;USULA 8 &ndash; </span> &Agrave; CONTRATADA reserva-se o direito de substituir o palestrante  que por ventura n&atilde;o possa comparecer por motivo justific&aacute;vel, por outro de  similar n&iacute;vel de especializa&ccedil;&atilde;o.
<br />
<span class="clausuras">CL&Aacute;USULA 9 &ndash; </span>&Eacute; estritamente  proibido o uso durante o treinamento de notebooks, netbooks, ou similares, bem  como gravadores de voz e imagem de qualquer natureza.
</div>
 

<div class="paragrafo_clausuras">
<center><span class="titulo_clausuras">DAS ESPECIFICIDADES  DO TREINAMENTO E FORMATA&Ccedil;&Atilde;O DE PAGAMENTO</span></center>
<span class="clausuras">CL&Aacute;USULA 10 &ndash; </span>A presta&ccedil;&atilde;o de  servi&ccedil;os bem como a formata&ccedil;&atilde;o do pagamento em virtude deste, dar-se-&aacute; nos  moldes estabelecidos no quadro descritivo infra:
</div>

    <?php
    $SqlAgendaCurso = "SELECT * FROM c_coaching_inscricao_curso WHERE id_inscricao = '".$ArInscricao['numreg']."' and id_situacao <> '3'";
    $QryAgendaCurso = query($SqlAgendaCurso);
    ?>
<table border="1" cellspacing="0" class="paragrafo_clausuras"  width="100%">
<tr><td class="tabela">
<center><span class="titulo_clausuras">QUADRO  DESCRITIVO </span></center>

<strong> a) DESCRI&Ccedil;&Atilde;O DO EVENTO</strong><br />
<?php echo $NomeCurso;?> <br />
Datas:<br>
<?php while($ArAgendaCurso = farray($QryAgendaCurso)){

$Modulo = deparaIdErpCrm($ArAgendaCurso['id_modulo'],"nome_modulo","numreg", "c_coaching_modulo");
$Local  = deparaIdErpCrm($ArAgendaCurso['id_local_curso'],"nome_local_curso","numreg", "c_coaching_local_curso");

$SqlDatas = "SELECT GROUP_CONCAT(DATE_FORMAT(dt_curso,'%d/%m/%Y') SEPARATOR ', ') AS datas FROM c_coaching_agenda_curso_detalhe WHERE id_agenda_curso = '".$ArAgendaCurso['id_agenda']."' ORDER BY dt_curso ASC";
$QryDatas = query($SqlDatas);
$ArDatas = farray($QryDatas);

$ArSqlHotel = farray(query("
						 select agendaDetalhe.id_hotel, hotel.nome_hotel, hotel.endereco, hotel.numero, hotel.bairro
							from c_coaching_inscricao_curso_detalhe as agendaDetalhe
							INNER JOIN c_coaching_hotel as hotel
							on agendaDetalhe.id_hotel = hotel.numreg
						where id_agenda = '".$ArAgendaCurso['id_agenda']."' and id_pessoa = '".$ArAgendaCurso['id_pessoa']."' group by id_agenda;"));

echo $Modulo." - ";
echo $ArDatas['datas'];
echo "<br>  -> <b>Hotel: </b>".$ArSqlHotel['nome_hotel']."  
	  <br>  -> <b>Endereço: </b>".$ArSqlHotel['endereco']." - ".$ArSqlHotel['numero']." - ".$ArSqlHotel['bairro']." - ".$Local."<br><br>";
}
?>

Carga Horária: 30 horas em EAD (ensino à distância) mais 60 horas de treinamento presencial, totalizando 90 horas.<br>
<strong> b) FORMATA&Ccedil;&Atilde;O DE PAGAMENTO</strong> <br />
Valor Total de
<?php
$SqlTotalPagamento = "SELECT SUM(vl_total_venda) as total FROM c_coaching_inscricao_venda WHERE id_inscricao = '".$ArInscricao['numreg']."'";
$QryTotalPagamento = query($SqlTotalPagamento);
$ArTotalPagamento  = farray($QryTotalPagamento);
echo "R$".number_format($ArTotalPagamento['total'],2,",",".")." (".valorPorExtenso($ArTotalPagamento['total']).")<br/>";
?>
<br>
Forma de Pagamento:<br />
<table border="1" class="paragrafo_clausuras"  cellspacing="0" >
<tr style="font-weight: bold;">

<td>Valor Total</td>
<td>Número de Parcela</td>
<td>Valor da Parcela</td>
<td>Forma Pagto.</td>
<td>1 &ordm; Vencimento</td>
<td>Tipo Pagto.</td>
<td width="20%">Obs</td>
</tr>
<?php
    $SqlGradePagto = "SELECT
                            t1.numreg,
                            t1.vl_pagto,
                            t1.obs,
                            t2.nome_forma_pagto,
                            t3.nome_cond_pagto,
                            t4.nome_tp_pagto,
                            t1.dt_primeiro_pagto,
                            t1.vl_parcela
                            FROM
                                c_coaching_inscricao_pagto t1
                            INNER JOIN
                                is_forma_pagto t2 ON t1.id_forma_pagto = t2.numreg
                            INNER JOIN
                                is_cond_pagto t3 ON t1.id_cond_pagto = t3.numreg
                            INNER JOIN c_coaching_tp_pagto t4 ON t1.id_tp_pagto = t4.numreg
                            WHERE
                                t1.id_inscricao = '".$ArInscricao['numreg']."'";

    $QryGradePagto = query($SqlGradePagto);
    $i = 0;
    while($ArGradePagto = farray($QryGradePagto)){
        $bgcolor = ($i % 2 == 0)?'#EAEAEA':'#FFFFFF';
        $i++;
        $VlTotalPagtos += $ArGradePagto['vl_pagto'];
        ?>
        <tr>
            
            <td><?php echo number_format($ArGradePagto['vl_pagto'],2,',','.');?>&nbsp;</td>
            <td><?php echo $ArGradePagto['nome_cond_pagto'];?>&nbsp;</td>
            <td><?php echo number_format($ArGradePagto['vl_parcela'],2,',','.');?>&nbsp;</td>
            <td><?php echo $ArGradePagto['nome_forma_pagto'];?>&nbsp;</td>                    
            <td><?php echo dten2br($ArGradePagto['dt_primeiro_pagto']);?>&nbsp;</td>
            <td><?php echo $ArGradePagto['nome_tp_pagto'];?>&nbsp;</td>
            <td><?php echo $ArGradePagto['obs'];?>&nbsp;</td>
        </tr>
<?php } ?>

</table>
<br>

&nbsp;Ciente  CONTRATANTE __________________________________ Local e data: S&atilde;o Paulo,<?php echo date('d/m/Y')?>. </div>

</tr></td></table>

<div class="paragrafo_clausuras">
<span class="clausuras">PARÁGRAFO 1º - </span>
O treinamento é ministrado sob o seguinte formato: 30 horas em EAD (ensino à distância) e 60 horas de treinamento presencial.
<br>
<span class="clausuras">PARÁGRAFO 2º - </span>
Antes do início do treinamento presencial o contratante receberá senha e instruções de acesso para uso da plataforma EAD. Feito o primeiro acesso o treinamento será considerado efetivamente iniciado vogando todas as condições especificadas neste instrumento.
<br>
<span class="clausuras">PARÁGRAFO 3º - </span>
O CONTRATANTE poderá acessar as primeiras 10 horas de treinamento EAD antes de iniciar o treinamento presencial, 10 durante e às 10 horas restantes após a conclusão deste.
<br>
<span class="clausuras">PARÁGRAFO 4º - </span>
- A interrupção do pagamento pela CONTRANTE incorrerá em interrupção do acesso ao conteúdo do EAD, sem prejuízo do disposto na cláusula 12.
</div>

<div class="quebra_pagina"></div> 

<div class="paragrafo_clausuras">
<center><span class="titulo_clausuras">DA POLÍTICA DE USO DA PLATAFORMA EAD</span></center>
<span class="clausuras">CLÁUSULA 11 – </span>Para acesso ao treinamento EAD o contratante utilizará plataforma de ensino fornecida pela CONTRATADA. O contratante deverá seguir a seguinte política de uso:
<br />
<span class="clausuras">PARÁGRAFO 1º – </span>O treinamento EAD deverá ser acessado exclusivamente pelo CONTRANTE, não devendo este disponibilizá-lo a terceiros. A senha de acesso recebida é individual e intransferível.
<br />
<span class="clausuras">PARÁGRAFO 2º – </span>Feito o primeiro acesso o treinamento será considerado efetivamente iniciado permitindo a cobrança total do treinamento, nos moldes acordados por meio da ficha de inscrição transmitida pelo CONTRATANTE à CONTRATADA e delineada no quadro descritivo constante na clausula 10. 
<br>
<span class="clausuras">PARÁGRAFO 3º – </span>O presente contrato será firmado inicialmente por meio de aceite eletrônico na própria plataforma EAD, e num segundo momento este deverá ser assinado de forma física no primeiro dia de treinamento presencial. 
</div>



<div class="paragrafo_clausuras">
<center><span class="titulo_clausuras">DO VENCIMENTO ANTECIPADO  DA D&Iacute;VIDA</span></center>
<span class="clausuras">CL&Aacute;USULA 12 &ndash; </span>A  CONTRATADA poder&aacute; considerar vencido antecipadamente o presente contrato se: a)  o CONTRATANTE  deixar de cumprir qualquer obriga&ccedil;&atilde;o contra&iacute;da neste Contrato; b) o CONTRATANTE deixar de efetuar o  pagamento dos valores explicitado na cl&aacute;usula 10, quadro demonstrativo, nas  datas aven&ccedil;adas.
<br />
<span class="clausuras">PAR&Aacute;GRAFO &Uacute;NICO &ndash; </span> O vencimento antecipado da d&iacute;vida possibilitar&aacute; a execu&ccedil;&atilde;o judicial imediata do  presente instrumento.
</div>

<div class="paragrafo_clausuras">
<center><span class="titulo_clausuras">DA NATUREZA EXECUTIVA</span></center>
<span class="clausuras">CL&Aacute;USULA 13 &ndash; </span>O  presente contrato possui natureza e car&aacute;ter de t&iacute;tulo executivo extrajudicial  nos termos do art. 585, II, do CPC, podendo o mesmo ser executado em raz&atilde;o do  eventual inadimplemento, abdicando as partes de qualquer manifesta&ccedil;&atilde;o em  contr&aacute;rio quanto a sua natureza executiva.
</div>

  
<div class="paragrafo_clausuras">
<center><span class="titulo_clausuras">DA PROPRIEDADE  INTELECTUAL DO MATERIAL</span></center>
<span class="clausuras">CL&Aacute;USULA 14 &ndash; </span>O  CONTRATANTE declara ter plena ci&ecirc;ncia de que todo o material fornecido durante  o treinamento especialmente o impresso foi desenvolvido e/ou adaptado pela  CONTRATADA, constituindo propriedade intelectual, tendo este car&aacute;ter de obra  protegida nos termos da legisla&ccedil;&atilde;o vigente, n&atilde;o podendo, portanto,  reproduzi-lo, veicul&aacute;-lo, distribu&iacute;-lo, public&aacute;-lo, etc, no todo ou em parte  sem a expressa autoriza&ccedil;&atilde;o deste &uacute;ltimo, sob pena dos rigores da lei.
</div>

<div class="paragrafo_clausuras">
<center><span class="titulo_clausuras">DO DIREITO DE IMAGEM</span></center>
<span class="clausuras">CLÁUSULA 15 – </span> O CONTRATANTE cede, autoriza, permite a utilização de sua imagem pela CONTRATADA para fins de propaganda, marketing, divulgação, promoção de treinamentos, em quaisquer mídias e/ou veículos de comunicação.
</div>


<div class="paragrafo_clausuras">
<center><span class="titulo_clausuras">FORO</span></center>
<span class="clausuras">CL&Aacute;USULA 16 &ndash; </span> As partes elegem  o foro de S&atilde;o Paulo &ndash; SP, para dirimir quaisquer quest&otilde;es resultantes deste  contrato, seja qual for o domic&iacute;lio do CONTRATANTE.
<br />
Por estarem  justos e acertados, assinam o presente instrumento em duas vias de igual teor,  na presen&ccedil;a de duas testemunhas, para que o mesmo fa&ccedil;a surtir seus efeitos  legais a partir da presente data. 
</div>

<div class="paragrafo_clausuras">
  <p>S&atilde;o Paulo, <?php echo date('d')." de ".$desc_mes[date('m')]." de ".date("Y")?>.</p>
</div>  
  <br>
  <br>


<table border="0" cellspacing="0" cellpadding="0" width="100%"  class="paragrafo_clausuras" align="left">
  <tr>
    <td width="27%" valign="bottom">
      <p align="left">__________________________________________________</p></td>
    <td width="28%" valign="bottom"></td>
    <td width="45%" valign="bottom">
      <p align="left">__________________________________________________</p></td>
  </tr>
  
  <tr>
    <td width="27%" valign="bottom">
      <p align="left"><span class="assinaturas"><?php echo $NomeCliente;?></span></p></td>
    <td width="28%" valign="bottom"></td>
    <td width="45%" valign="bottom">
      <p align="left"><span class="assinaturas"><?php echo $Estabelecimento;?></span></p></td>
  </tr>
  
   <tr>
    <td width="27%" valign="bottom"> 
      <p align="left"><span class="assinaturas"><?php echo $LabelCNPJCPF.': '.$CNPJCPFCliente;?></span></p></td>
      <td width="28%" valign="bottom"> </td>
    <td width="45%" valign="bottom"> 
      <p align="left"><span class="assinaturas">CNPJ: <?php echo $CnpjFormatado;?></span></p></td>
  </tr>
</table>

<p><br>
</p>
<p>&nbsp;</p>
<p>Testemunhas: </p>
<p><br>
</p>
<table border="0" cellspacing="0" cellpadding="0" width="100%"  class="paragrafo_clausuras" align="left">
  <tr>
    <td width="27%" valign="bottom">
      <p align="left">__________________________________________________</p></td>
    <td width="28%" valign="bottom"></td>
    <td width="45%" valign="bottom">
      <p align="left">__________________________________________________</p></td>
  </tr>
  
  <tr>
    <td width="27%" valign="bottom">
      <p align="left"><span class="assinaturas">Rafaela Pardo de Moraes</span></p></td>
    <td width="28%" valign="bottom"></td>
    <td width="45%" valign="bottom">
      <p align="left"><span class="assinaturas">Samyra de Souza Cruz</span></p></td>
  </tr>
  
   <tr>
    <td width="27%" valign="bottom"> 
      <p align="left"><span class="assinaturas">RG: 43195577-3</span></p></td>
      <td width="28%" valign="bottom"> </td>
    <td width="45%" valign="bottom"> 
      <p align="left"><span class="assinaturas">RG: 1857562</span></p></td>
  </tr>
</table>
<div class="quebra_pagina"></div> 
<p class="quebra_pagina">
.
</p>

<?php } ?>