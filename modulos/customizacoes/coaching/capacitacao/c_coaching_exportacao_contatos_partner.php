<?

/*\
|*| 	Arquivo: c_coaching_exportacao_contatos_partner.php
|*| 	Autor: Alisson
|*| 	Início: 19/07/2013 15:17 
|*| 	Fim: xx/xx/xx 00:00
|*| 	Programa de Exportação de contatos recebidos para Partner
|*| 	Log de Alterações
|*| 	yyyy-mm-dd hh:mm <Pessoa responsável> <Descrição das alterações>
\*/

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('../../../../conecta.php');
require('../../../../functions.php');

$sqlContatos[0] = "
					select
					  pessoa.razao_social_nome,
					  pessoa.tel1,
					  usuario.nome_usuario,
					  SUBSTRING(replace(atividade.obs, 'Telefone: (', '' ) FROM 23 ) as Mensagem,
					  DATE_FORMAT(atividade.dt_inicio, '%d-%m-%Y')   as dt_inicio,
					  DATE_FORMAT(atividade.dt_prev_fim, '%d-%m-%Y')   as dt_prev_fim,
					  DATE_FORMAT(atividade.dt_real_fim, '%d-%m-%Y')   as dt_real_fim,
					  situacao.nome_situacao as situcao
						from is_atividade as atividade
						  inner join is_pessoa as pessoa
						  on pessoa.numreg = atividade.id_pessoa
						  inner join is_usuario as usuario
						  on usuario.numreg = atividade.id_usuario_resp
						  inner join is_situacao as situacao
						  on situacao.numreg  = atividade.id_situacao
					  where atividade.id_tp_atividade = 1";

for($a = 0; $a <= count($sqlContatos)-1; $a++){
	$qryContatos = mysql_query($sqlContatos[$a]);
	$contResultsContatos[$a] = mysql_num_rows($qryContatos);
}

?>

<style type="text/css">
    #relatorio_custom{ margin:10px; }
    #relatorio_custom .line_form { padding:10px; }	
    #relatorio_custom legend{ font-size:16px; font-size:18px; font-weight:bold; padding:0 5px; }
	#form_prospect{ display:none; }
	#btn_exportar_contatos_career{ margin-top:10px; }
</style>

<div id="relatorio_custom">

    <form id="form_exportacao_contatos_partner" name="form_exportacao_contatos_partner" method="post">
        <fieldset>
            <legend class="legend_form">Contatos x Atividades</legend>
            <div class="line_form">
                <label>Base:
                    <select name="base" id="base">
                        <option value="1" selected="selected"> (<?=$contResultsContatos[0]?>)</option>
                    </select>
                </label>
            </div>
            <div id="form_prospect">
                <div class="line_form">
                    <label>
                        <input type="radio" name="date" id="date" class="date" value="1" checked="checked" /> Todos registros
                    </label>
                </div>
                <div class="line_form">
                    <label>
                        <input type="radio" name="date" id="date" class="date" value="2" />
                        <input type="text" size="12" name="date_ini" id="date_ini" value="" placeholder="dd/mm/aaaa" />
                        até
                        <input type="text" size="12" name="date_end" id="date_end" value="<?=date("d/m/Y")?>" />
                    </label>
                </div>
            </div>
        </fieldset>
        <input type="button" id="btn_exportar_contatos_partner" class="botao_jquery" value="Exportar Contatos (Excel)" />
    </form>

</div>
<script language="JavaScript">
    $(document).ready(function(){
        $(".botao_jquery").button();
        $("#btn_exportar_contatos_partner").click(function(){
			var data = $("#form_exportacao_contatos_partner").serialize();
			location.href="modulos/customizacoes/coaching/capacitacao/c_coaching_exportacao_contatos_partner_post.php?"+data;
        });
				
		$("#base").live('change', function(){
			$("#base option:selected").each(function (){	
				if($(this).val() == 4){
					$("#form_prospect").show();
				}else{
					$(".date:eq(0)").attr("checked", "checked");
					$(".date:eq(1)").removeAttr("checked");
					$("#form_prospect").hide();
				}
			});
		}).trigger('change');
    });
</script>