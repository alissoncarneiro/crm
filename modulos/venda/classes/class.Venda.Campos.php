<?php
/*
 * class.Venda.Campos.php
 * Autor: Alex
 * 26/10/2010 10:45
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class VendaCampos extends GeraCadCampos{
    /**
     * @var Venda
     */
    protected $ObjVenda;

    public function  __construct($IdCadastro, Venda $ObjVenda, $ArGET){
        $this->ObjVenda = $ObjVenda;
        $this->setDadosRegistro($ObjVenda->DadosVenda);
        parent::__construct($IdCadastro, $ArGET);
    }

    public function getObjVenda(){
        return $this->ObjVenda;
    }

    public function CampoCustom($IdCampo,$IdCadastro){
        /* Travando o calendário da data da venda, para não permitir data maior que hoje */
        if($IdCampo == 'dt_orcamento' || $IdCampo == 'dt_pedido'){
            $this->setPropriedadeCampo($IdCampo,'maxdate','0');
        }
        
        /* Tratamento para o tipo de preço */
        if($IdCampo == 'id_tp_preco'){
            if($this->ObjVenda->getUsuario()->getPermissao('sn_permite_alterar_preco_venda')){
                $this->setPropriedadeCampo($IdCampo,'editavel',1);
            }
            else{
                $this->setPropriedadeCampo($IdCampo,'editavel',0);
            }
        }
        
        /* Tratamento para o campo grupo tabela de preço */
        if($IdCampo == 'id_grupo_tab_preco'){
            if($this->getObjVenda()->getVendaParametro()->getModoUnidMedida() != 3){
                $this->RemoveCampo($IdCampo);
                $this->setPropriedadeCampo('id_tp_preco','quebra_linha',1);
            }
        }
        
        /* Removendo o campo de id_cfop da classe caso o usuário não possa permissão para alterar a CFOP */
        if($IdCampo == 'id_cfop'){
            if(!$this->getObjVenda()->getVendaParametro()->getPermiteAlterarCFOPItem()){// Se o parametro de venda não permite alteracao de CFOP
                $this->RemoveCampo('id_cfop');
            }
            elseif($this->getObjVenda()->getUsuario()->getPermissao('sn_permite_alterar_cfop_item') != 1){ // Se o usuário não permite alteracao de CFOP
                $this->RemoveCampo('id_cfop');
            }
        }
        /* Removendo o campo de tabela de preço caso a moeda seja selecionada por item */
        if($IdCampo == 'id_tab_preco'){
            if($this->getObjVenda()->getVendaParametro()->getSnUsaTabPrecoPorItem()){// Se usa a tabela de preço por item
                $this->RemoveCampo('id_tab_preco');
            }
        }

        /* Tratamentos com a data de entrega e desejada */
        if($IdCampo == 'dt_entrega_desejada'){ /* Travando o calendário da data desejada de entrega, para não permitir data menor que hoje */
            $this->setPropriedadeCampo($IdCampo,'mindate','0');
        }

        /* Se o parametro de utiliza campo data de entrega desejada não estiver como 1 */
        if($IdCampo == 'dt_entrega_desejada' && !$this->getObjVenda()->getVendaParametro()->getSnUsaDataDesejadaEntrega()){ /* Desativando o campo caso não seja utilizado */
            $this->RemoveCampo('dt_entrega_desejada');
        }

        /* Se não permite edição da data de entrega trava o campo */
        if($IdCampo == 'dt_entrega'){
            if(!$this->getObjVenda()->getVendaParametro()->getSnPermiteAlterarDtEntrega()){
                $this->setPropriedadeCampo($IdCampo,'editavel',0);
            }
            else{
                $this->setPropriedadeCampo($IdCampo,'sn_obrigatorio',1);
            }
        }
        
        /* Travando o campo de situação e moeda */
        if($IdCampo == 'id_situacao_pedido' || $IdCampo == 'id_situacao_orcamento' || $IdCampo == 'id_moeda'){
            $this->setPropriedadeCampo($IdCampo,'editavel',0);
        }

        /* Travando o campo Forma de Pagto */
        if($this->getObjVenda()->getVendaParametro()->getSnUsaRestrEstFCondPagto()){
            if($IdCampo == 'id_forma_pagto'){
                $this->setPropriedadeCampo($IdCampo,'sn_obrigatorio',1);
            }
        }
        
        /* Travando os campos que não podem ser editados caso haja algum item na venda */
        if($this->getObjVenda()->getQtdeItens() > 0){
            $ArCamposNaoEditaveis = array('id_estabelecimento','id_tp_pedido','tp_pedido','tp_orcamento','id_tp_orcamento','id_destino_mercadoria','id_pessoa','id_tp_preco','id_grupo_tab_preco','id_tab_preco');
            if(is_int(array_search($IdCampo,$ArCamposNaoEditaveis))){
                $this->setPropriedadeCampo($IdCampo,'sn_obrigatorio',0);
                $this->setPropriedadeCampo($IdCampo,'editavel',0);
            }
        }

        /* Tratamento para os campos de desconto da capa */
        if($IdCampo == 'pct_desconto_tab_preco'){
            if($this->getObjVenda()->getParamCampoDescontoVendaFixo('1','sn_ativo') != '1'){
                $this->RemoveCampo($IdCampo);
            }
            else{
                $this->setPropriedadeCampo($IdCampo,'editavel',$this->getObjVenda()->getParamCampoDescontoVendaFixo('1','sn_editavel'));
                $this->setPropriedadeCampo($IdCampo,'quebra_linha',1);
            }
        }
        if($IdCampo == 'pct_desconto_pessoa'){
            if($this->getObjVenda()->getParamCampoDescontoVendaFixo('2','sn_ativo') != '1'){
                $this->RemoveCampo($IdCampo);
            }
            else{
                $this->setPropriedadeCampo($IdCampo,'editavel',$this->getObjVenda()->getParamCampoDescontoVendaFixo('2','sn_editavel'));
                $this->setPropriedadeCampo($IdCampo,'quebra_linha',1);
                $this->setPropriedadeCampo('pct_desconto_tab_preco','quebra_linha',0);
            }
        }
        if($IdCampo == 'pct_desconto_informado'){
            if($this->getObjVenda()->getParamCampoDescontoVendaFixo('3','sn_ativo') != '1'){
                $this->RemoveCampo($IdCampo);
            }
            else{
                $this->setPropriedadeCampo($IdCampo,'editavel',$this->getObjVenda()->getParamCampoDescontoVendaFixo('3','sn_editavel'));
                $this->setPropriedadeCampo($IdCampo,'quebra_linha',1);
                $this->setPropriedadeCampo('pct_desconto_tab_preco','quebra_linha',0);
                $this->setPropriedadeCampo('pct_desconto_pessoa','quebra_linha',0);
            }
        }
        /* Tratamento para o parâmetro de pedido em moeda única */
        if($IdCampo == 'id_moeda'){
            if($this->ObjVenda->getVendaParametro()->getSnVendaMoedaUnica()){
                $this->setPropriedadeCampo($IdCampo,'editavel','1');
                $this->setPropriedadeCampo($IdCampo,'quebra_linha',1);
                $this->setPropriedadeCampo($IdCampo,'exibe_formulario',1);
            }
        }
        
        /* Tratamento para não tornar a condição de pagamento obrigatória, quando informada condição especial */
        if($IdCadastro == 'pedido'){
            if($IdCampo == 'id_cond_pagto' && trim($this->POST[$this->PrefixoCampo.'cond_pagto_especial']) != ''){
                $this->setPropriedadeCampo($IdCampo,'sn_obrigatorio',0);
            }
        }
        
        /* Tratamento para os campo fase (oportunidade) */
        if($IdCampo == 'id_fase'){
            if($this->getDadosRegistro('id_ciclo') != ''){
                $this->setPropriedadeCampo($IdCampo, 'sql_lupa', 'select numreg,nome_opor_fase from is_opor_fase where numreg in(select id_opor_fase from is_opor_ciclo_fase where id_opor_ciclo = '.$this->getDadosRegistro('id_ciclo').')');
            }            
        }

        /* Se o orçamento ou pedido já estiverem completo, trava todos os campos */
        if($this->getDadosRegistro('sn_digitacao_completa') == 1){
            $this->setPropriedadeCampo($IdCampo,'sn_obrigatorio',0);
            $this->setPropriedadeCampo($IdCampo,'editavel',0);
        }
        
        /* Se for um pedido de bonificação gerado a partir de outro pedido, trava todos campos campos do passo 1 */
        if($this->getDadosRegistro('sn_gerado_bonificacao_auto') == 1){
            $this->setPropriedadeCampo($IdCampo,'sn_obrigatorio',0);
            $this->setPropriedadeCampo($IdCampo,'editavel',0);
        }
        
        if($IdCampo == 'id_cond_pagto' && $this->ObjVenda->getDadosVenda('id_tp_venda') == '2'){
            $this->setPropriedadeCampo($IdCampo,'sn_obrigatorio',0);
            $this->setPropriedadeCampo($IdCampo,'editavel',0);
        }

        if($IdCampo == 'id_cond_pagto' && $this->getPropriedadeCampo($IdCampo, 'editavel') == '0'){
            $this->setPropriedadeCampo($IdCampo, 'sql_lupa', 'select numreg,nome_cond_pagto from is_cond_pagto');
        }
        
        if($IdCampo == 'id_tab_preco' && $this->getPropriedadeCampo($IdCampo, 'editavel') == '0'){
            $this->setPropriedadeCampo($IdCampo, 'sql_lupa', 'select numreg,nome_tab_preco from is_tab_preco');
        }
    }

    public function ValorCustom($IdCadastro,$IdCampo,$Valor){
        if($IdCadastro == 'pedido' || $IdCadastro == 'orcamento'){
            if($IdCampo == 'id_situacao_pedido' || $IdCampo == 'id_situacao_orcamento'){ //Situação em Aberto
                if(!$this->ObjVenda->getDigitacaoCompleta()){
                    $Valor = 1;
                }
            }

            if($IdCampo == 'id_moeda'){ //Moeda em Real R$
                $Valor = 1;
            }

            if(($IdCampo == 'vl_total_bruto' || $IdCampo == 'vl_total_liquido') && empty($Valor)){
                $Valor = 0;
            }

        }
        return $Valor;
    }

    public function HTMLPersonalizado($IdCampo,$IdCadastro){
        /*
         * DESATIVADO TEMPORARIAMENTE
         */
        /*
        if($IdCadastro == 'pedido' || $IdCadastro == 'orcamento'){
            if($IdCampo == 'id_pessoa'){
                return '<img alt="Clique para ver mais detalhes" title="Clique para ver mais detalhes" style="cursor:pointer"  id="btn_det_pessoa" src="../../images/b_small_help.gif" border="0" height="15" width="15">';
            }
        }
        */
        if($IdCadastro == 'pedido' || $IdCadastro == 'orcamento'){
            if(!$this->ObjVenda->getDigitacaoCompleta()){
                if($IdCampo == 'id_cfop'){
                    $StringRetorno = '';
                    if($this->ObjVenda->getSnPermiteAlterarCFOP()){
                        $StringRetorno = '<img alt="Sugerir CFOP do cliente" style="cursor:pointer"  id="btn_sugerir_cfop_cliente" src="img/sugerir_cfop_cliente_pequeno.png" border="0" height="15" width="15">';
                    }
                    return $StringRetorno;
                }
            }
        }
        if($IdCadastro == 'pedido' || $IdCadastro == 'orcamento'){
            if($IdCampo == 'pct_desconto_tab_preco' && !$this->ObjVenda->getDigitacaoCompleta() && $this->getObjVenda()->getParamCampoDescontoVendaFixo(1, 'sn_autopreenchido_com_desc_max') == '1'){
                $StringRetorno = '<img alt="Sugerir Desconto" style="cursor:pointer" class="btn_sugerir_desconto_fixo" id_campo_desconto="1" src="img/sugerir_cfop_cliente_pequeno.png" border="0" height="15" width="15">';
                return $StringRetorno;
            }
            if($IdCampo == 'pct_desconto_pessoa' && !$this->ObjVenda->getDigitacaoCompleta() && $this->getObjVenda()->getParamCampoDescontoVendaFixo(2, 'sn_autopreenchido_com_desc_max') == '1'){
                $StringRetorno = '<img alt="Sugerir Desconto" style="cursor:pointer" class="btn_sugerir_desconto_fixo" id_campo_desconto="2" src="img/sugerir_cfop_cliente_pequeno.png" border="0" height="15" width="15">';
                return $StringRetorno;
            }
            if($IdCampo == 'pct_desconto_informado' && !$this->ObjVenda->getDigitacaoCompleta() && $this->getObjVenda()->getParamCampoDescontoVendaFixo(3, 'sn_autopreenchido_com_desc_max') == '1'){
                $StringRetorno = '<img alt="Sugerir Desconto" style="cursor:pointer" class="btn_sugerir_desconto_fixo" id_campo_desconto="3" src="img/sugerir_cfop_cliente_pequeno.png" border="0" height="15" width="15">';
                return $StringRetorno;
            }
        }
    }

    public function ValidaCamposObrigatorios(){
        $StringRetorno = '';
        foreach($this->ArrayCampos as $k => $v){
            /* Tratamento para campo de tabela de preço */
            if($k == 'id_tab_preco'){
                if($this->decodeValor($k,$this->POST[$this->PrefixoCampo.'id_tp_preco']) == '1' && $this->decodeValor($k,$this->POST[$this->PrefixoCampo.$k]) == ''){ /* Se o tipo de reço for informado e o campo tabela de preço for vazio */
                    continue;
                }
                elseif($this->decodeValor($k,$this->POST[$this->PrefixoCampo.'id_tp_preco']) == '1' && $this->decodeValor($k,$this->POST[$this->PrefixoCampo.$k]) != ''){ /* Se o tipo de reço for informado e o campo tabela de preço não for vazio */
                    $StringRetorno .= 'Para o tipo de preço Informado, o campo Tab. Preço deve ser vazio !\\n';
                }
            }
            if($k == 'id_cond_pagto'){
                if($this->decodeValor($k,$this->POST[$this->PrefixoCampo.'id_tp_pedido']) == '2' || $this->decodeValor($k,$this->POST[$this->PrefixoCampo.'id_tp_orcamento']) == '2'){
                    continue;
                }
            }
            if($this->ArrayCampos[$k]['sn_obrigatorio'] == 1 && $this->decodeValor($k,$this->POST[$this->PrefixoCampo.$k]) == ''){
                $StringRetorno .= $this->ArrayCampos[$k]['nome_campo'].' não pode ser vazio !\\n';
            }
        }
        if($StringRetorno != ''){
            $geraCadPost = new geraCadPost();
            $IdPostback = $geraCadPost->backupPost($_POST);
            $Url = new Url();
            $Url->setUrl($this->UrlRetorno);
            $Url->AlteraParam('ppostback',$IdPostback);
            echo alert($StringRetorno);
            echo windowlocationhref($Url->getUrl());
            exit;
        }
    }

    public function ValidaCamposDescontoFixo(){
        $ArrayCampos = array('1' => 'pct_desconto_tab_preco', '2' => 'pct_desconto_pessoa', '3' => 'pct_desconto_informado');
        for($i=1;$i<=3;$i++){
            $SnAtivo = $this->getObjVenda()->getParamCampoDescontoVendaFixo($i, 'sn_ativo');
            $SnEditavel = $this->getObjVenda()->getParamCampoDescontoVendaFixo($i, 'sn_editavel');
            $PctMaxDescIni = $this->getObjVenda()->getParamCampoDescontoVendaFixo($i, 'pct_max_desc_ini');
            $PctMaxDescFim = $this->getObjVenda()->getParamCampoDescontoVendaFixo($i, 'pct_max_desc_fim');
            $Nomecampo = $this->getObjVenda()->getParamCampoDescontoVendaFixo($i, 'nome_campo');
            if($SnAtivo == 0 || $SnEditavel == 0){
                continue;
            }
            $PctDesconto = TrataFloatPost($this->POST[$this->PrefixoCampo.$ArrayCampos[$i]]);
            if($PctDesconto < $PctMaxDescIni){
                $StringRetorno .= 'Campo '.$Nomecampo.' não pode ser inferior à '.number_format_min($PctMaxDescIni,2,',','.').'\n';
            }
            elseif($PctDesconto > $PctMaxDescFim){
                $StringRetorno .= 'Campo '.$Nomecampo.' não pode ser superior à '.number_format_min($PctMaxDescFim,2,',','.').'\n';
            }
        }
        if($StringRetorno != ''){
            $geraCadPost = new geraCadPost();
            $IdPostback = $geraCadPost->backupPost($_POST);
            $Url = new Url();
            $Url->setUrl($this->UrlRetorno);
            $Url->AlteraParam('ppostback',$IdPostback);
            echo alert($StringRetorno);
            echo windowlocationhref($Url->getUrl());
            exit;
        }
    }

    public function ValidaTrataPOSTCustom($IdCampo){
        $Erro = false;
        $ArrayMsgErro = array();
        if($this->ObjVenda->getDadosVenda('sn_passou_pelo_passo1') == 0){
            if(($IdCampo == 'dt_pedido' || $IdCampo == 'dt_orcamento') && $this->POST[$this->PrefixoCampo.$IdCampo] != ''){
                $DiferencaDias = DiferencaEntreDatas(date("Y-m-d"),DataHoraDecode($this->POST[$this->PrefixoCampo.$IdCampo]));
                $Paramqtde_max_dias_dt_retro = getParametrosVenda('qtde_max_dias_dt_retro');
                if($DiferencaDias < 0 && abs($DiferencaDias) > $Paramqtde_max_dias_dt_retro){
                    $Erro = true;
                    $ArrayMsgErro[] = getError('0040010003',getParametrosGerais('RetornoErro'),array($this->ObjVenda->TituloVenda,$Paramqtde_max_dias_dt_retro));
                }
            }
            if($IdCampo == 'dt_entrega_desejada' && $this->POST[$this->PrefixoCampo.$IdCampo] != ''){
                $DiferencaDias = DiferencaEntreDatas(date("Y-m-d"),DataHoraDecode($this->POST[$this->PrefixoCampo.$IdCampo]));
                $Paramqtde_max_dias_dt_retro = getParametrosVenda('qtde_max_dias_dt_des_ent_retro');
                if($DiferencaDias < 0 && abs($DiferencaDias) > $Paramqtde_max_dias_dt_retro){
                    $Erro = true;
                    $ArrayMsgErro[] = getError('0040010004',getParametrosGerais('RetornoErro'),array($this->ObjVenda->TituloVenda,$Paramqtde_max_dias_dt_retro));
                }
            }
        }
        /* Tratamento da cond. pagto para pedidos de bonificação */
        if($IdCampo == 'id_cond_pagto' && $this->POST[$this->PrefixoCampo.'id_tp_pedido'] == '2' && $this->ObjVenda->getVendaParametro()->getIdCondPagtoBonificPadrao() != ''){
            $this->POST[$this->PrefixoCampo.'id_cond_pagto'] = $this->ObjVenda->getVendaParametro()->getIdCondPagtoBonificPadrao();
        }

        if($Erro == true){
            $geraCadPost = new geraCadPost();
            $IdPostback = $geraCadPost->backupPost($_POST,$this->IdPostBack);
            $Url = new Url();
            $Url->setUrl($this->UrlRetorno);
            $Url->AlteraParam('ppostback',$IdPostback);
            echo alert(implode('\n',$ArrayMsgErro));
            echo windowlocationhref($Url->getUrl());
            exit;
        }
    }
}
?>