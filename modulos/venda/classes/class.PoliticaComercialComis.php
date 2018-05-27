<?php
/*
 * class.PoliticaComercialComis.php
 * Autor: Alex
 * 13/12/2010 11:13
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class PoliticaComercialComis{
    
    private $VendaItem;

    private $ArDadosVenda;
    private $ArDadosPessoa;
    private $ArDadosItem;
    private $ArDadosProduto;
    private $IdTpParticipacao;
    private $Status = false;
    private $StringStatus;
    private $NumregRegraEncontrada;
    private $PctComissao;
    
    public function __construct(VendaItem $VendaItem){
        $this->VendaItem = $VendaItem;
    }

    public function setArDadosVenda($ArDadosVenda){
        $this->ArDadosVenda = $ArDadosVenda;
    }

    public function setArDadosPessoa($ArDadosPessoa){
        $this->ArDadosPessoa = $ArDadosPessoa;
    }

    public function setArDadosItem($ArDadosItem){
        $this->ArDadosItem = $ArDadosItem;
    }

    public function setArDadosProduto($ArDadosProduto){
        $this->ArDadosProduto = $ArDadosProduto;
    }

    public function setIdTpParticipacao($IdTpParticipacao){
        $this->IdTpParticipacao = $IdTpParticipacao;
    }

    public function setStatus($Status){
        $this->Status = $Status;
    }

    public function setArDadoItem($IdCampo,$Valor){
        $this->ArDadosItem[$IdCampo] = $Valor;
    }

    public function setArDadoProduto($IdCampo,$Valor){
        $this->ArDadosProduto[$IdCampo] = $Valor;
    }

    public function setArDadoVenda($IdCampo,$Valor){
        $this->ArDadosVenda[$IdCampo] = $Valor;
    }

    public function setStringStatus($String){
        $this->StringStatus = $String;
    }

    public function getStatus(){
        return $this->Status;
    }

    public function getStringStatus(){
        return $this->StringStatus;
    }

    public function getNumregRegraEncontrada(){
        return $this->NumregRegraEncontrada;
    }

    public function getPctComissao(){
        return $this->PctComissao;
    }


    public function CalculaComissao(){
        $ArrayCamposValores = array(
            'id_tp_participacao'            => $this->IdTpParticipacao,
            'id_pessoa'                     => $this->ArDadosVenda['id_pessoa'],
            'id_pessoa_regiao'              => $this->ArDadosPessoa['id_regiao'],
            'pessoa_cidade'                 => $this->ArDadosPessoa['cidade'],
            'pessoa_uf'                     => $this->ArDadosPessoa['uf'],
            'id_pessoa_canal_venda'         => $this->ArDadosPessoa['id_canal_venda'],
            'id_pessoa_grupo_cliente'       => $this->ArDadosPessoa['id_grupo_cliente'],
            'id_tp_pessoa'                  => $this->ArDadosPessoa['id_tp_pessoa'],
            'pessoa_sn_contribuinte_icms'   => $this->ArDadosPessoa['sn_contribuinte_icms'],
            'id_venda_estabelecimento'      => $this->ArDadosVenda['id_estabelecimento'],
            'id_venda_repres_pri'           => $this->ArDadosVenda['id_repres_pri'],
            'id_venda_participante'         => $this->ArDadosVenda['id_venda_participante'],
            'id_venda_tab_preco'            => $this->ArDadosVenda['id_tab_preco'],
            'id_venda_tp_venda'             => $this->ArDadosVenda['id_tp_venda'],
            'id_venda_dest_merc'            => $this->ArDadosVenda['id_dest_merc'],
            'id_venda_moeda'                => $this->ArDadosVenda['id_moeda'],
            'id_venda_grupo_tab_preco'      => $this->ArDadosVenda['id_grupo_tab_preco'],
            'id_venda_cond_pagto'           => $this->ArDadosVenda['id_cond_pagto'],
            'id_venda_tp_frete'             => $this->ArDadosVenda['id_tp_frete'],
            'id_venda_transportadora'       => $this->ArDadosVenda['id_transportadora'],
            'id_venda_cond_pagto'           => $this->ArDadosVenda['id_cond_pagto'],
            'venda_sn_faturamento_parcial'  => $this->ArDadosVenda['sn_faturamento_parcial'],
            'venda_sn_aprovacao_parcial'    => $this->ArDadosVenda['sn_aprovacao_parcial'],
            'venda_sn_antecipa_entrega'     => $this->ArDadosVenda['sn_antecipa_entrega'],
            'venda_vl_tot'                  => $this->ArDadosVenda['venda_vl_tot'],
            'venda_med_dias'                => $this->ArDadosVenda['med_dias'],
            'id_produto'                    => $this->ArDadosProduto['id_produto'],
            'id_produto_linha'              => $this->ArDadosProduto['id_linha'],
            'id_produto_familia_comercial'  => $this->ArDadosProduto['id_familia_comercial'],
            'id_produto_familia'            => $this->ArDadosProduto['id_familia'],
            'id_produto_unid_medida'        => $this->ArDadosProduto['id_unid_medida'],
            'id_produto_grupo_estoque'      => $this->ArDadosProduto['id_grupo_estoque'],
            'item_qtde'                     => $this->ArDadosItem['qtde'],
            'item_vl_tot'                   => $this->ArDadosItem['item_vl_tot'],
            'item_unid_medida'              => $this->ArDadosItem['id_unid_medida'],
            'item_pct_media_desc'           => $this->ArDadosItem['pct_media_desc']
        );
        $SqlComissao = "SELECT ".((TipoBancoDados == 'mssql')?'TOP(1)':'')." numreg,pct_comis FROM is_param_politica_comercial_comis WHERE sn_ativo = 1 AND ('".date("Y-m-d")."' BETWEEN dthr_validade_ini AND dthr_validade_fim) ";
        foreach($ArrayCamposValores as $Campo => $Valor){
            $Valor = trim($Valor);
            if($Campo == 'venda_vl_tot'){
                if($Valor != ''){
                    $SqlComissao .= " AND ((".uB::GeraStringSqlBetweenCampo('venda_vl_tot_ini','venda_vl_tot_fim',$Valor).") OR (venda_vl_tot_ini IS NULL AND venda_vl_tot_fim IS NULL))";
                }
                else{
                    $SqlComissao .= " AND (venda_vl_tot_ini IS NULL AND venda_vl_tot_fim IS NULL)";
                }
            }
            elseif($Campo == 'venda_med_dias'){
                if($Valor != ''){
                    $SqlComissao .= " AND ((".uB::GeraStringSqlBetweenCampo('venda_med_dias_ini','venda_med_dias_fim',$Valor).") OR (venda_med_dias_ini IS NULL AND venda_med_dias_fim IS NULL))";
                }
                else{
                    $SqlComissao .= " AND (venda_med_dias_ini IS NULL AND venda_med_dias_fim IS NULL)";
                }
            }
            elseif($Campo == 'item_qtde'){
                if($Valor != ''){
                    $SqlComissao .= " AND ((".uB::GeraStringSqlBetweenCampo('item_qtde_ini','item_qtde_fim',$Valor).") OR (item_qtde_ini IS NULL AND item_qtde_fim IS NULL))";
                }
                else{
                    $SqlComissao .= " AND (qtde_ini IS NULL AND qtde_fim IS NULL)";
                }
            }
            elseif($Campo == 'item_vl_tot'){
                if($Valor != ''){
                    $SqlComissao .= " AND ((".uB::GeraStringSqlBetweenCampo('item_vl_tot_ini','item_vl_tot_fim',$Valor).") OR (item_vl_tot_ini IS NULL AND item_vl_tot_fim IS NULL))";
                }
                else{
                    $SqlComissao .= " AND (item_vl_tot_ini IS NULL AND item_vl_tot_fim IS NULL)";
                }
            }
            elseif($Campo == 'item_pct_media_desc'){
                if($Valor != ''){
                    $SqlComissao .= " AND ((".uB::GeraStringSqlBetweenCampo('item_pct_media_desc_ini','item_pct_media_desc_fim',$Valor).") OR (item_pct_media_desc_ini IS NULL AND item_pct_media_desc_fim IS NULL))";
                }
                else{
                    $SqlComissao .= " AND (item_pct_media_desc_ini IS NULL AND item_pct_media_desc_fim IS NULL)";
                }
            }
            else{
                if($Valor != ''){
                    $SqlComissao .= " AND (".$Campo." = '".TrataApostrofoBD($Valor)."' OR ".$Campo." IS NULL)";
                }
                else{
                    $SqlComissao .= " AND ".$Campo." IS NULL";
                }
            }
        }
        $SqlComissao .= " ORDER BY nr_pontos DESC, pct_comis DESC ".((TipoBancoDados == 'mysql')?' LIMIT 1':'');
        $this->VendaItem->getObjVenda()->setMensagemDebug('SQL: '.$SqlComissao);
        $QryComissao = query($SqlComissao);
        $NumRowsComissao = numrows($QryComissao);
        
        $SqlComissaoAdicionalPorData = "SELECT pct_comis FROM is_param_comis_adicional_periodo_mes WHERE sn_ativo = 1 AND dia_inicio <= ".date("d")." AND dia_fim >= ".date("d");
        $QryComissaoAdicionalPorData = query($SqlComissaoAdicionalPorData);
        $ArComissaoAdicionalPorData = farray($QryComissaoAdicionalPorData);
        
        if($NumRowsComissao == 0 && $ArComissaoAdicionalPorData['pct_comis'] <= 0){
            $this->Status = false;
            $this->setStringStatus('Nenhuma regra de comissão econtrada');
            $this->VendaItem->getObjVenda()->setMensagemDebug('Nenhuma regra de comissão econtrada');
            return false;
        }
        else{
            $ComissaoExtra = ($ArComissaoAdicionalPorData['pct_comis'] != '')?$ArComissaoAdicionalPorData['pct_comis']:0;
            $ArComissao = farray($QryComissao);
            $this->NumregRegraEncontrada    = $ArComissao['numreg'];
            $this->PctComissao              = $ArComissao['pct_comis'] + $ComissaoExtra;

            $this->Status = true;
            $this->setStringStatus('Regra número '.$this->NumregRegraEncontrada.' encontrada');
            
            $this->VendaItem->getObjVenda()->setMensagemDebug('Regra número '.$this->NumregRegraEncontrada.' encontrada ('.$this->PctComissao.'%).');
            
            return true;
        }
    }
}
?>