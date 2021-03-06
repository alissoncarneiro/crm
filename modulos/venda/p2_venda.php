<?php
/*
 * p2_venda.php
 * Autor: Alex
 * 28/10/2010 15:15
 * -
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */

if(!is_object($Venda)){
    $Venda = new Venda($TipoVenda,$NumregVenda);
    $Campos = new VendaCamposCustom($Venda->pfuncao,$Venda);
    $VendaParametro = new VendaParametro();
    exit;
}

/*
 * Carregando os itens da solicita��o comercial quando houver
 */
$Venda->CarregaSolicitacaoComercial();
$ItensSolicitacaoComercial = $Venda->getItensSolicitacaoComercial();
/*
 * Carregando os itens da oportunidade
 */
$Venda->CarregaOportunidadePai();
$ItensOportunidadePai = $Venda->getItensOportunidadePai();
/*
 * Carregando os itens pre
 */
$Venda->CarregaItenPre();
$ItensPre = $Venda->getItensPre();
?>
<input type="hidden" name="ptp_venda" id="ptp_venda" value="<?php echo $Venda->getTipoVenda();?>" />
<input type="hidden" name="pnumreg" id="pnumreg" value="<?php echo $Venda->getNumregVenda();?>" />
<input type="hidden" name="pvisualizar_revisao" id="pvisualizar_revisao" value="<?php echo $_GET['pvisualizar_revisao'];?>" />
<?php if(!$Venda->getDigitacaoCompleta()){ ?>
<script language="javascript" src="js/pesquisa_produto.js"></script>
<script language="javascript">
    $(document).ready(function(){
        $("#fs_selec_it_pesq legend").click(function(){
            $("#fs_selec_it_pesq div:first").slideToggle();
        }).css("cursor","pointer");

        $("#fs_selec_it_linha_familia legend").click(function(){
            $("#fs_selec_it_linha_familia div:first").slideToggle();
        }).css("cursor","pointer");

        $("#fs_prod_atendimento legend").click(function(){
            $("#fs_prod_atendimento div:first").slideToggle();
        }).css("cursor","pointer");

        $("#fs_prod_oportunidade_pai legend").click(function(){
            $("#fs_prod_oportunidade_pai div:first").slideToggle();
        }).css("cursor","pointer");
    });
</script>
<fieldset><legend>Adicionar Itens</legend>
    <?php if(!$Venda->getSnGeradoBonificacaoAuto()){ /* Se a venda n�o foi gerada automaticamente a partir de outro pedido */?>
        <?php if(getParametrosVenda('selec_it_pesq') == 1){ ?>
        <fieldset id="fs_selec_it_pesq">
            <legend title="Clique para expandir">Por Pesquisa</legend>
            <div>
                C&oacute;digo:
                <input type="text" name="edttexto_filtro_1" id="edttexto_filtro_1" style="width:100px;" />
                Descri&ccedil;&atilde;o:
                <input type="text" name="edttexto_filtro_2" id="edttexto_filtro_2" style="width:190px;" />
                <?php if(getParametrosVenda('sn_usa_tab_compl_prod')){?>
                <?php echo htmlentities($VendaParametro->getNomeCampoCodComplementarProduto());?>:
                <input name="edttexto_filtro_3" type="text" id="edttexto_filtro_3" />
                <?php } ?>
                <?php if($VendaParametro->getSnSnUsaCodProdutoCliente()){?>
                C&oacute;digo do Cliente:
                <input type="text" name="edttexto_filtro_4" id="edttexto_filtro_4" id_pessoa="<?php echo $Venda->getDadosVenda('id_pessoa');?>" style="width:100px;" />
                <?php } ?>
                <select name="edttp_filtro" id="edttp_filtro">
                    <option value="1" selected="selected">Iniciado com</option>
                    <option value="2">Contenha</option>
                    <option value="3">Igual</option>
                </select>
                <span id="span_selec_it_pesq" title="Pesquisar" style="cursor:pointer;"><img src="../../images/btn_busca.PNG" alt="Pesquisar" width="15" height="15" /><strong>Pesquisar</strong></span>
                <div id="div_resut_selec_it_pesq"></div>
            </div>
        </fieldset>
        <?php } ?>
        <?php if(getParametrosVenda('selec_it_linha_familia') == 1){ ?>
        <fieldset id="fs_selec_it_linha_familia">
            <legend title="Clique para expandir">Por Linha/Fam&iacute;lia</legend>
            <div>
                <?php if(getParametrosVenda('utiliza_linha_produto') == 1){ ?>
                Linha: <select id="edtselec_it_linha_familia_linha">
                            <option value="">--Selecione--</option>
                            <?php
                            $SqlLinha = "SELECT numreg,nome_produto_linha FROM is_produto_linha WHERE sn_ativo = 1 ";
                            if($VendaParametro->getSnUsaBloqueioRepxFam()){
                                $SqlLinha .= " AND numreg IN(SELECT tsq2.id_produto_linha FROM is_familia_comercial tsq2 WHERE tsq2.numreg IN(SELECT DISTINCT tsq1.id_familia_comercial FROM is_param_representantexfamilia_comercial tsq1 WHERE tsq1.id_representante = '".$_SESSION['id_usuario']."' AND tsq1.sn_ativo = 1 AND tsq1.dthr_validade_ini <= '".date("Y-m-d")."' AND tsq1.dthr_validade_fim >= '".date("Y-m-d")."'))";
                            }
                            $SqlLinha .= " ORDER BY nome_produto_linha ASC";
                            $QryLinha = query($SqlLinha);
                            while($ArLinha = farray($QryLinha)){
                                echo '<option value="'.$ArLinha['numreg'].'">'.$ArLinha['nome_produto_linha'].'</option>';
                            }
                            ?>
                        </select>
                Fam&iacute;lia: <select id="edtselec_it_linha_familia_familia"><option>--Selecione uma Linha--</option></select>
                <?php } ?>
                <?php if(getParametrosVenda('utiliza_linha_produto') == 0){ ?>
                    <select id="edtselec_it_linha_familia_familia">
                        <option value="">--Selecione--</option>
                        <?php
                        $SqlFamiliaComercial = "SELECT numreg,nome_familia_comercial FROM is_familia_comercial WHERE sn_ativo = 1 ";
                        if($VendaParametro->getSnUsaBloqueioRepxFam()){
                            $SqlFamiliaComercial .= " AND numreg IN(SELECT DISTINCT tsq1.id_familia_comercial FROM is_param_representantexfamilia_comercial tsq1 WHERE tsq1.id_representante = '".$_SESSION['id_usuario']."' AND tsq1.sn_ativo = 1 AND tsq1.dthr_validade_ini <= '".date("Y-m-d")."' AND tsq1.dthr_validade_fim >= '".date("Y-m-d")."')";
                        }
                        $BloqueioCustom = VendaCallBackCustom::ExecutaVenda($Venda, 'Passo_2Combobox_Familia', 'BloqueioCampo');
                        $SqlFamiliaComercial = ($BloqueioCustom != '1')?$SqlFamiliaComercial.$BloqueioCustom:$SqlFamiliaComercial;
                        $SqlFamiliaComercial .= " ORDER BY nome_familia_comercial ASC";
                        $QryFamiliaComercial = query($SqlFamiliaComercial);
                        while($ArFamiliaComercial = farray($QryFamiliaComercial)){
                            echo '<option value="'.$ArFamiliaComercial['numreg'].'">'.$ArFamiliaComercial['nome_familia_comercial'].'</option>';
                        }
                        ?>
                    </select>
                <?php } ?>
                Produto: <select id="edtselec_it_linha_familia_produto"><option>--Selecione uma Fam&iacute;lia--</option></select>
                <input type="button" id="edtit_linha_familia_produto_exibir_produto" class="botao_jquery" value="Exibir Detalhes" />

            </div>
        </fieldset>
        <script>
            $(document).ready(function(){
                $("#edtit_linha_familia_produto_exibir_produto").click(function(event){
                    var NumregProduto = $("#edtselec_it_linha_familia_produto").val();
                    if(NumregProduto == ''){
                        alert('Selecione um produto!');
                        event.preventDefault();
                    }
                    exibe_detalhe_produto(NumregProduto);
                });
            });
        </script>
        <?php } ?>
        <?php if(count($ItensSolicitacaoComercial) > 0){ ?>
        <fieldset id="fs_prod_atendimento">
            <legend title="Clique para expandir">Produtos do Atendimento</legend>
            <div>
                <table width="auto" border="0" align="left" cellpadding="2" cellspacing="2" class="bordatabela" id="tabela_itens">
                    <tr bgcolor="#DAE8F4" style="font-weight:bold;">
                        <td>C&oacute;d.</td>
                        <td>Descri&ccedil;&atilde;o Prod.</td>
                        <td>Qtde.</td>
                        <td>A&ccedil;&atilde;o</td>
                    </tr>
                    <?php
                    $i = 0;
                    foreach($ItensSolicitacaoComercial as $ItemSolicitacaoComercial){
                        if($ItemSolicitacaoComercial['id_produto'] != ''){
                            $ProdutoItemSolicitacao     = new Produto($ItemSolicitacaoComercial['id_produto']);
                            $IdProduto                  = $ProdutoItemSolicitacao->getDadosProduto('id_produto_erp');
                            $DescricaoProduto           = $ProdutoItemSolicitacao->getDadosProduto('nome_produto');
                            $Qtde                       = $ItemSolicitacaoComercial['qtde'];
                            $OnClick                    = ' onClick="javascript:exibe_detalhe_produto('.$ItemSolicitacaoComercial['id_produto'].',null,true,\''.$Venda->NFQ($Qtde).'\'); "';
                            $Classe                     = 'botao_jquery';
                            $Atributos                  = 'RecarregarPagina="true"';
                        }
                        else{
                            $IdProduto          = '';
                            $DescricaoProduto   = $ItemSolicitacaoComercial['inc_descricao'];
                            $Qtde               = $ItemSolicitacaoComercial['qtde'];
                            $OnClick            = '';
                            $Classe             = 'botao_jquery venda_btn_add_produto_nao_comercial';
                            $Atributos          = 'Descricao="'.htmlentities($DescricaoProduto).'" Qtde="'.$Venda->NFQ($Qtde).'" RecarregarPagina="true"';
                        }
                        $i++;
                        $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
                    ?>
                    <tr bgcolor="<?php echo $bgcolor;?>">
                        <td><?php echo $IdProduto;?></td>
                        <td><?php echo $DescricaoProduto;?></td>
                        <td align="right"><?php echo $Venda->NFQ($Qtde);?></td>
                        <td><input type="button" class="<?php echo $Classe;?>" <?php echo $Atributos;?> <?php echo $OnClick;?> value="Adicionar" /></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </fieldset>
        <?php } ?>
        <?php if(count($ItensOportunidadePai) > 0){ ?>
        <fieldset id="fs_prod_oportunidade_pai">
            <legend title="Clique para expandir">Produtos da Oportunidade</legend>
            <div>
                <table width="auto" border="0" align="left" cellpadding="2" cellspacing="2" class="bordatabela" id="tabela_itens_oportunidade_pai">
                    <tr bgcolor="#DAE8F4" style="font-weight:bold;">
                        <td>C&oacute;d.</td>
                        <td>Descri&ccedil;&atilde;o Prod.</td>
                        <td>Qtde.</td>
                        <td>A&ccedil;&atilde;o</td>
                    </tr>
                    <?php
                    $i = 0;
                    foreach($ItensOportunidadePai as $ItemOportunidadePai){
                        if($ItemOportunidadePai['id_produto'] != ''){
                            $ProdutoItemOportunidadePai = new Produto($ItemOportunidadePai['id_produto']);
                            $IdProduto                  = $ProdutoItemOportunidadePai->getDadosProduto('id_produto_erp');
                            $DescricaoProduto           = $ProdutoItemOportunidadePai->getDadosProduto('nome_produto');
                            $Qtde                       = $ItemOportunidadePai['qtde'];
                            $VlUnitario                 = $ItemOportunidadePai['valor'];
                            $OnClick                    = ' onClick="javascript:exibe_detalhe_produto('.$ItemOportunidadePai['id_produto'].',null,true,\''.$Venda->NFQ($Qtde).'\',\''.$Venda->NFQ($VlUnitario).'\'); "';
                            $Classe                     = 'botao_jquery';
                            $Atributos                  = 'RecarregarPagina="true"';
                        }
                        else{
                            $IdProduto          = '';
                            $DescricaoProduto   = $ItemOportunidadePai['inc_descricao'];
                            $Qtde               = $ItemOportunidadePai['qtde'];
                            $OnClick            = '';
                            $Classe             = 'botao_jquery venda_btn_add_produto_nao_comercial';
                            $Atributos          = 'Descricao="'.htmlentities($DescricaoProduto).'" Qtde="'.$Venda->NFQ($Qtde).'" RecarregarPagina="true"';
                        }
                        $i++;
                        $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
                    ?>
                    <tr bgcolor="<?php echo $bgcolor;?>">
                        <td><?php echo $IdProduto;?></td>
                        <td><?php echo $DescricaoProduto;?></td>
                        <td align="right"><?php echo $Venda->NFQ($Qtde);?></td>
                        <td><input type="button" class="<?php echo $Classe;?>" <?php echo $Atributos;?> <?php echo $OnClick;?> value="Adicionar" /></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </fieldset>
        <?php } ?>
        <?php if(count($ItensPre) > 0){ ?>
        <fieldset id="fs_prod_itens_pre">
            <legend title="Clique para expandir">Pr&eacute; Itens</legend>
            <div>
                <table width="auto" border="0" align="left" cellpadding="2" cellspacing="2" class="bordatabela" id="tabela_itens_pre">
                    <tr bgcolor="#DAE8F4" style="font-weight:bold;">
                        <td>C&oacute;d.</td>
                        <td>Descri&ccedil;&atilde;o Prod.</td>
                        <td>Qtde.</td>
                        <td>A&ccedil;&atilde;o</td>
                    </tr>
                    <?php
                    $i = 0;
                    foreach($ItensPre as $ItemPre){
                        if($ItemPre['id_produto'] != ''){
                            $ProdutoItemPre = new Produto($ItemPre['id_produto']);
                            $IdProduto                  = $ProdutoItemPre->getDadosProduto('id_produto_erp');
                            $DescricaoProduto           = $ProdutoItemPre->getDadosProduto('nome_produto');
                            $Qtde                       = $ItemPre['qtde'];
                            $VlUnitario                 = $ItemPre['vl_unitario'];
                            $OnClick                    = ' onClick="javascript:exibe_detalhe_produto('.$ItemPre['id_produto'].',null,true,\''.$Venda->NFQ($Qtde).'\',\''.$Venda->NFQ($VlUnitario).'\',\''.$ItemPre['numreg'].'\'); "';
                            $Classe                     = 'botao_jquery';
                            $Atributos                  = 'RecarregarPagina="true"';
                        }
                        else{
                            $IdProduto          = '';
                            $DescricaoProduto   = $ItemPre['inc_descricao'];
                            $Qtde               = $ItemPre['qtde'];
                            $OnClick            = '';
                            $Classe             = 'botao_jquery venda_btn_add_produto_nao_comercial';
                            $Atributos          = 'Descricao="'.htmlentities($DescricaoProduto).'" Qtde="'.$Venda->NFQ($Qtde).'" RecarregarPagina="true"';
                        }
                        $i++;
                        $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
                    ?>
                    <tr bgcolor="<?php echo $bgcolor;?>">
                        <td><?php echo $IdProduto;?></td>
                        <td><?php echo $DescricaoProduto;?></td>
                        <td align="right"><?php echo $Venda->NFQ($Qtde);?></td>
                        <td><input type="button" class="<?php echo $Classe;?>" <?php echo $Atributos;?> <?php echo $OnClick;?> value="Adicionar" /></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </fieldset>
        <?php } ?>
        <?php if($Venda->isOrcamento() && $VendaParametro->getSnUsaItemNaoComercial()){?>
        <input type="button" class="botao_jquery venda_btn_add_produto_nao_comercial" value="+ Incluir Produto N&atilde;o Comercial">
        <script>
            $(document).ready(function(){
                $(".venda_btn_add_produto_nao_comercial").click(function(){
                    var Descricao           = $(this).attr("Descricao");
                    var Qtde                = $(this).attr("Qtde");
                    var Valor               = $(this).attr("Valor");
                    var RecarregarPagina    = $(this).attr("RecarregarPagina");

                    Descricao       = (Descricao != undefined)?Descricao:'';
                    Qtde            = (Qtde != undefined)?Qtde:'';
                    Valor           = (Valor != undefined)?Valor:'';
                    RecarregarPagina= (RecarregarPagina == 'true')?RecarregarPagina:'false';
                    var HTMLDialog = '';

                    HTMLDialog += '<strong><?php echo $VendaParametro->getNomeCampoCodComplementarProduto();?>:</strong><br /><input type="text" id="venda_prod_nao_comercial_cod_compl"><br />';
                    HTMLDialog += '<strong>Descri&ccedil;&atilde;o:</strong><br /><input type="text" id="venda_prod_nao_comercial_descricao" value="' + Descricao + '"><br />';
                    HTMLDialog += '<strong>Qtde:</strong><br /><input type="text" class="venda_campo_qtde" id="venda_prod_nao_comercial_qtde" value="' + Qtde + '"><br />';
                    HTMLDialog += '<strong>Valor Unit&aacute;rio:</strong><br /><input type="text" class="venda_campo_vl_unitario" id="venda_prod_nao_comercial_vl_unitario" value="' + Valor + '"><br />';

                    $("#jquery-dialog").attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Adicionar Produto N&atilde;o Comercial');
                    $("#jquery-dialog").html(HTMLDialog);
                    $("#jquery-dialog").dialog({
                        buttons:{
                            "Adicionar": function(){
                                var cod_compl = $("#venda_prod_nao_comercial_cod_compl").val();
                                var descricao = $("#venda_prod_nao_comercial_descricao").val();
                                var qtde = $("#venda_prod_nao_comercial_qtde").val();
                                var vl_unitario = $("#venda_prod_nao_comercial_vl_unitario").val();
                                /* Consistencias de dados */
                                if(cod_compl == '' || descricao == '' || qtde == '' || vl_unitario == ''){
                                    alert('Todos os campos s�o obrigat�rio!');
                                    return false;
                                }
                                $(this).dialog("close");
                                $.ajax({
                                    url: "p2_adiciona_item.php",
                                    global: false,
                                    type: "POST",
                                    data: ({
                                        pnumreg: $("#pnumreg").val(),
                                        ptp_venda: $("#ptp_venda").val(),
                                        cod_compl: escape(cod_compl),
                                        descricao: escape(descricao),
                                        qtde: qtde,
                                        vl_unitario: vl_unitario,
                                        sn_produto_nao_comercial:1
                                    }),
                                    dataType: "xml",
                                    async: true,
                                    beforeSend: function(){

                                    },
                                    error: function(){
                                        alert('Erro com a requisi��o');
                                    },
                                    success: function(xml){
                                        $("#notify-container").notify("create",{
                                            title: 'Alerta',
                                            text: $(xml).find('mensagem').text()
                                        },{
                                            expires: 5000,
                                            speed: 500,
                                            sticky:true,
                                            stack: "above"
                                        });
                                        if(RecarregarPagina == 'true'){
                                            window.location.reload();
                                        }
                                        else{
                                            exibe_tabela_item();
                                        }
                                    }
                                });
                        },
                        Cancelar: function(){$(this).dialog("close");}},
                        open: function(){
                            $("#venda_prod_nao_comercial_qtde").keypress(function(event){
                                if(event.charCode && (event.charCode < 48 || event.charCode > 57)){
                                    event.preventDefault();
                                }
                            });
                        },
                        modal: true,
                        show: "fade",
                        hide: "fade"
                    });

                });
            });
        </script>
        <?php } ?>
        <?php if($VendaParametro->getPermiteAdicionarItemPorFamilia()){?>
        <input type="button" class="botao_jquery venda_btn_add_produto_familia" value="+ Incluir Produto Por Fam�lia">
        <script>
        $(document).ready(function(){
            $(".venda_btn_add_produto_familia").click(function(){
                var Dialog = $("#jquery-dialog");
                Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Adicionar Produto Por Fam&iacute;lia');
                Dialog.html(HTMLLoading);
                Dialog.dialog({
                    width: 800,
                    height: 600,
                    buttons:{"Fechar": function(){Dialog.dialog("close");}},
                    open: function(){
                        $.ajax({
                            url: "p2_adiciona_item_por_linha_familia.php",
                            global: false,
                            type: "POST",
                            data: ({
                                ptp_venda:$("#ptp_venda").val(),
                                pnumreg:'<?php echo $Venda->getNumregVenda();?>'
                            }),
                            dataType: "html",
                            async: true,
                            beforeSend: function(){

                            },
                            error: function(){
                                alert('Erro com a requisi��o');
                            },
                            success: function(responseText){
                                Dialog.html(responseText);
                            }
                        });
                    },
                    close: function(){$(this).dialog("destroy");},
                    modal: true,
                    show: "fade",
                    hide: "fade"
                });
            });
        });
        </script>
        <?php }?>
        <?php if($VendaParametro->getSnUsaKit()){?>
        <input type="button" class="botao_jquery venda_btn_kit_produto" value="+ Incluir Kit de Produtos">
        <script>
        $(document).ready(function(){
            $(".venda_btn_kit_produto").click(function(){
                var Dialog = $("#jquery-dialog");
                Dialog.attr("title",'<span style="float:left;" class="ui-icon ui-icon-alert"></span>&nbsp;Adicionar KIT');
                Dialog.html(HTMLLoading);
                Dialog.dialog({
                    width: 800,
                    height: 600,
                    buttons:{"Fechar": function(){Dialog.dialog("close");}},
                    open: function(){
                        $.ajax({
                            url: "p2_kit.php",
                            global: false,
                            type: "POST",
                            data: ({
                                ptp_venda:$("#ptp_venda").val(),
                                pnumreg:'<?php echo $Venda->getNumregVenda();?>'
                            }),
                            dataType: "html",
                            async: true,
                            beforeSend: function(){

                            },
                            error: function(){
                                alert('Erro com a requisi��o');
                            },
                            success: function(responseText){
                                Dialog.html(responseText);
                            }
                        });
                    },
                    close: function(){$(this).dialog("destroy");},
                    modal: true,
                    show: "fade",
                    hide: "fade"
                });
            });
        });
        </script>
        <?php }?>
        <?php if($VendaParametro->getSnUsaKit() && $Venda->getTipoVenda() == 1){?>
        <input type="button" class="botao_jquery venda_btn_multiplo_orc" value="Unificar Or�amentos">
        <script>
        $(document).ready(function(){
            $(".venda_btn_multiplo_orc").click(function(){
                window.open('multiplo_orcamento/lista_orcamento.php?pnumreg=<?php echo $Venda->getNumregVenda();?>&ptp_venda=<?php echo $Venda->getTipoVenda();?>&tab_preco=<?php echo $Venda->getDadosVenda('id_tab_preco');?>&pfuncao=<?php echo $Venda->getPFuncao();?>','kit_de_produto','width=1000,height=600, scrollbars=yes');
            });
        });
        </script>
        <?php } ?>
        <?php } else { /* FIM Se a venda n�o foi gerada automaticamente a partir de outro pedido */
        $IdCampanha = $Venda->getDadosVenda('id_campanha_bonificacao');
        if(!empty($IdCampanha)){
            $SqlProdutosPermitidos = "SELECT DISTINCT
                                                    t1.sn_obrigatorio,
                                                    t2.numreg,
                                                    t2.id_produto_erp,
                                                    t2.nome_produto
                                                FROM
                                                    is_campanha_bonificacao_produto_permitido t1
                                                INNER JOIN
                                                    is_produto t2 ON t1.id_produto = t2.numreg
                                                WHERE
                                                    t1.id_campanha = ".$IdCampanha."
                                                ORDER BY
                                                    t1.sn_obrigatorio DESC, t2.id_produto_erp ASC";
            $QryProdutosPermitidos = query($SqlProdutosPermitidos);
            ?>
            <select id="edtselect_bonificacao_produto">
            <?php
            while($ArProdutoPermitido = farray($QryProdutosPermitidos)){
                $Style = '';
                if($ArProdutoPermitido['sn_obrigatorio'] == '1'){
                    $Style = ' style="color:#FF0000;font-weight:bold;"';
                }
                echo '<option'.$Style.' value="'.$ArProdutoPermitido['numreg'].'">'.$ArProdutoPermitido['id_produto_erp'].' - '.$ArProdutoPermitido['nome_produto'].'</option>';
            }
            $SqlFamiliasPermitidas = "SELECT numreg,id_produto_erp,nome_produto FROM is_produto WHERE id_familia_comercial IN(SELECT id_familia_comercial FROM is_campanha_bonificacao_familia_comercial_permitida WHERE id_campanha = '".$IdCampanha."') ORDER BY id_produto_erp ASC";
            $QryFamiliasPermitidas = query($SqlFamiliasPermitidas);
            while($ArFamiliasPermitidas = farray($QryFamiliasPermitidas)){
                echo '<option value="'.$ArFamiliasPermitidas['numreg'].'">'.$ArFamiliasPermitidas['id_produto_erp'].' - '.$ArFamiliasPermitidas['nome_produto'].'</option>';
            }
            ?>
            </select> <input type="button" id="edtbtn_bonificacao_exibir_produto" class="botao_jquery" value="Exibir Detalhes" />
            Valor &agrave; bonificar: $<?php echo number_format_min($Venda->getVlBonificacaoComTolerancia(),2,',','.'); ?>
            <script>
            $(document).ready(function(){
                $("#edtbtn_bonificacao_exibir_produto").click(function(event){
                    var NumregProduto = $("#edtselect_bonificacao_produto").val();
                    if(NumregProduto == ''){
                        alert('Selecione um produto!');
                        event.preventDefault();
                    }
                    exibe_detalhe_produto(NumregProduto);
                });
            });
            </script>
        <?php
        }
    } ?>
    <div id="div_det_produto"></div>
    <?php /*
    <hr size="1" />
    <?php if(getParametrosVenda('exibe_cotacao_moeda_p2') == 1){ ?>
    <div style="height:auto;vertical-align: middle; padding-right: 10px; font-size: 14px; font-weight: bold;">
        <img src="img/icone_cotacao.png" style="float:left;" width="32" height="32" />
        1 US$ = R$1,897<br />
        1 &euro; = R$2,873
    </div>
    <?php } ?>
    <?php if(getParametrosVenda('exibe_produto_promocao_p2') == 1){ ?>
    Produtos em promo&ccedil;&atilde;o:
    <span style="display:inline;">
    <select name="select2">
      <option>Produto Promo&ccedil;&atilde;o 1</option>
      <option>Produto Promo&ccedil;&atilde;o 2</option>
      <option>Produto Promo&ccedil;&atilde;o 3</option>
    </select>
    </span>
    <?php } */?>
</fieldset>
<hr size="1" />
<?php } ?>
<fieldset><legend>Itens do <?php echo ucwords($Venda->TituloVenda);?></legend>
    <div id="div_tabela_item"></div>
</fieldset>
<?php
    $Url = new Url();
    $Url->setUrl(curPageURL());
?>
<div align="center" style="text-align: center;">

    <a href="<?php $Url->AlteraParam('ppagina','p1'); echo $Url->getUrl();?>" class="dicn_medium">
    <img src="img/voltar_pequeno.png" width="64" height="64" alt="&lt;&lt; Passo Anterior" title="&lt;&lt; Passo Anterior" />
    <p>&lt;&lt; Passo Anterior</p>
    </a>
    <?php if(!$Venda->getDigitacaoCompleta()){ ?>
    <a href="#" id="btn_atualiza_itens" class="dicn_medium">
    <img src="img/atualizar_pequeno.png" width="64" height="64" alt="Atualizar Itens" title="Atualizar Itens" />
    <p>Atualizar Itens</p>
    </a>
    <?php } ?>
    <?php if(!$Venda->getDigitacaoCompleta() || $Venda->getEmAprovacao()){ ?>
    <a href="<?php $Url->AlteraParam('ppagina','p3'); echo $Url->getUrl();?>" class="dicn_medium" id="btn_proximo_passo">
    <img src="img/avancar_pequeno.png" width="64" height="64" alt="&gt;&gt; Pr&oacute;ximo Passo" title="&gt;&gt; Pr&oacute;ximo Passo" />
    <p>&gt;&gt; Pr&oacute;ximo Passo</p>
    </a>
    <?php } else {?>
    <a href="<?php $Url->AlteraParam('ppagina','p4'); echo $Url->getUrl();?>" class="dicn_medium" id="btn_proximo_passo">
    <img src="img/avancar_pequeno.png" width="64" height="64" alt="&gt;&gt; Pr&oacute;ximo Passo" title="&gt;&gt; Pr&oacute;ximo Passo" />
    <p>&gt;&gt; Pr&oacute;ximo Passo</p>
    </a>
    <?php } ?>
</div>
<script>
    $(document).ready(function(){
        $("#btn_atualiza_itens").click(function(){
            $("#form_tabela_itens").submit();
        });
        $("#btn_proximo_passo").click(function(e){
            if($("#controle_item_alterado").val() == 1){
                e.preventDefault();
                $("#jquery-dialog").attr("title","Alerta");
                $("#jquery-dialog").html('H� itens pendentes de atualiza��o. Ao passar para o pr�ximo passo as altera��es n�o ser�o gravadas. Deseja continuar ?');
                $("#jquery-dialog").dialog({
                    dialogClass: 'jquery-dialog',
                    position: 'center',
                    resizable: false,
                    buttons:{
                        "Confirmar": function(){location.href = $("#btn_proximo_passo").attr("href");},
                        Cancelar: function(){$(this).dialog("close"); return false;}
                    },
                    close: function(){$(this).dialog("destroy");},
                    modal: true,
                    show: "fade",
                    hide: "fade"
                });
                return false;
            }
        });
        exibe_tabela_item();
    });
</script>
<?php
$CallBackCustom = VendaCallBackCustom::ExecutaVenda($Venda, 'Passo2', 'Final');
if($CallBackCustom !== true){
    echo $CallBackCustom;
}
?>