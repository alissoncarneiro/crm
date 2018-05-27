<?php
/*
 * class.Venda.Bonificacao.php
 * Autor: Alex
 * 13/12/2010 18:05
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class VendaBonificacao{
    private $ObjVenda;
    private $IdFamiliaComercial;
    private $IdProduto;
    private $IdRepresentantePrincipal;
    private $QtdeItens;
    private $VlTotVenda;
    private $NumregCampanhaBonificacao;
    private $SnUsoImediato;
    
    public function __construct(Venda $ObjVenda){
        $this->ObjVenda = $ObjVenda;        
    }

    public function setIdFamiliaComercial($IdFamiliaComercial){
        $this->IdFamiliaComercial = $IdFamiliaComercial;
    }

    public function setIdProduto($IdProduto){
        $this->IdProduto = $IdProduto;
    }

    public function setIdRepresentantePrincipal($IdRepresentantePrincipal){
        $this->IdRepresentantePrincipal = $IdRepresentantePrincipal;
    }

    public function setQtdeItens($QtdeItens){
        $this->QtdeItens = $QtdeItens;
    }

    public function setVlTotVenda($VlTotVenda){
        $this->VlTotVenda = $VlTotVenda;
    }

    public function setNumregCampanhaBonificacao($NumregCampanhaBonificacao){
        $this->NumregCampanhaBonificacao = $NumregCampanhaBonificacao;
    }

    public function setSnUsoImediato($SnUsoImediato){
        $this->SnUsoImediato = $SnUsoImediato;
    }

    public function getSnUsoImediato(){
        return $this->SnUsoImediato;
    }

    public function getNumregCampanhaBonificacao(){
        return $this->NumregCampanhaBonificacao;
    }

    public function VerificaCampanhaBonificacao(){
        $SqlCampanhaBonificacao = "SELECT numreg,sn_uso_imediato FROM is_campanha WHERE
                                                        sn_tipo_bonificacao = 1
                                                        AND sn_ativo = 1
                                                        AND ('".date("Y-m-d")."' BETWEEN dt_inicio AND dt_fim)
                                                        AND qtde_min_item <= ".$this->QtdeItens."
                                                        AND vl_min_tot_venda <= ".$this->VlTotVenda;

        $QryCampanhaBonificacao = query($SqlCampanhaBonificacao);
        $NumrowsCampanhaBonificacao = numrows($QryCampanhaBonificacao);
        if($NumrowsCampanhaBonificacao == 0){//Se não foi encontrada nenhuma campanha de bonificação
            return false;
        }
        $ArCampanhaBonificacao = farray($QryCampanhaBonificacao);
        $this->setNumregCampanhaBonificacao($ArCampanhaBonificacao['numreg']);
        $this->setSnUsoImediato($ArCampanhaBonificacao['sn_uso_imediato']);
        return $ArCampanhaBonificacao['numreg'];
    }

    public function VerificaCampanhaBonificacaoRepresentantePrincipal(){
        if(empty($this->NumregCampanhaBonificacao) || empty($this->IdRepresentantePrincipal)){//Se for vazio o numreg da campanha ou o numreg do representante
            return false;
        }
        $SqlCampanhaBonificacaoRepresentantePrincipal = "SELECT pct_bonificacao FROM is_campanha_bonificacao_representante_principal WHERE
                                                        id_campanha = ".$this->NumregCampanhaBonificacao."
                                                        AND id_representante = ".$this->IdRepresentantePrincipal;

        $QryCampanhaBonificacaoRepresentantePrincipal = query($SqlCampanhaBonificacaoRepresentantePrincipal);
        $NumrowsCampanhaBonificacaoRepresentantePrincipal = numrows($QryCampanhaBonificacaoRepresentantePrincipal);
        if($NumrowsCampanhaBonificacaoRepresentantePrincipal == 0){//Se não foi encontrada nenhuma campanha de bonificação
            return false;
        }
        $ArCampanhaBonificacaoRepresentantePrincipal = farray($QryCampanhaBonificacaoRepresentantePrincipal);
        return $ArCampanhaBonificacaoRepresentantePrincipal['pct_bonificacao'];
    }

    public function VerificaCampanhaBonificacaoFamiliaComercial(){
        if(empty($this->NumregCampanhaBonificacao) || empty($this->IdFamiliaComercial)){//Se for vazio o numreg da campanha ou o numreg da familia comercial
            return false;
        }
        $SqlCampanhaBonificacaoFamiliaComercial = "SELECT pct_bonificacao FROM is_campanha_bonificacao_familia_comercial WHERE
                                                        id_campanha = ".$this->NumregCampanhaBonificacao."
                                                        AND id_familia_comercial = ".$this->IdFamiliaComercial;
        $this->ObjVenda->setMensagemDebug($SqlCampanhaBonificacaoFamiliaComercial);

        $QryCampanhaBonificacaoFamiliaComercial = query($SqlCampanhaBonificacaoFamiliaComercial);
        $NumrowsCampanhaBonificacaoFamiliaComercial = numrows($QryCampanhaBonificacaoFamiliaComercial);
        if($NumrowsCampanhaBonificacaoFamiliaComercial == 0){//Se não foi encontrada nenhuma campanha de bonificação
            return false;
        }
        $ArCampanhaBonificacaoFamiliaComercial = farray($QryCampanhaBonificacaoFamiliaComercial);
        return $ArCampanhaBonificacaoFamiliaComercial['pct_bonificacao'];
    }

    public function VerificaCampanhaBonificacaoProduto(){
        if(empty($this->NumregCampanhaBonificacao) || empty($this->IdProduto)){//Se for vazio o numreg da campanha ou o numreg do produto
            return false;
        }
        $SqlCampanhaBonificacaoProduto = "SELECT pct_bonificacao FROM is_campanha_bonificacao_produto WHERE
                                                        id_campanha = ".$this->NumregCampanhaBonificacao."
                                                        AND id_produto = ".$this->IdProduto;

        $QryCampanhaBonificacaoProduto = query($SqlCampanhaBonificacaoProduto);
        $NumrowsCampanhaBonificacaoProduto = numrows($QryCampanhaBonificacaoProduto);
        if($NumrowsCampanhaBonificacaoProduto == 0){//Se não foi encontrada nenhuma campanha de bonificação
            return false;
        }
        $ArCampanhaBonificacaoProduto = farray($QryCampanhaBonificacaoProduto);
        return $ArCampanhaBonificacaoProduto['pct_bonificacao'];
    }

    public function CalculaPctBonificacao(){
        $this->ObjVenda->setMensagemDebug('Calculando bonificação<br/>');
        $VerificaCampanhaBonificacao = $this->VerificaCampanhaBonificacao();
        if($VerificaCampanhaBonificacao !== false){//Se foi encontrado um campanha
            $this->ObjVenda->setMensagemDebug('Campanha de bonificação encontrada<br/>');
            /*
             * Verificando se há alguma regra para o representante principal
             */
            $VerificaCampanhaBonificacaoRepresentantePrincipal = $this->VerificaCampanhaBonificacaoRepresentantePrincipal();
            if($VerificaCampanhaBonificacaoRepresentantePrincipal !== false){//Se foi encontrada alguma regra para o representante principal
                $this->ObjVenda->setMensagemDebug('Encontrada regra para representante principal<br/>');
                return $VerificaCampanhaBonificacaoRepresentantePrincipal;
            }
            else{
                /*
                 * Verificando se há alguma regra para a família comercial
                 */
                $VerificaCampanhaBonificacaoFamiliaComercial = $this->VerificaCampanhaBonificacaoFamiliaComercial();
                if($VerificaCampanhaBonificacaoFamiliaComercial !== false){
                    $this->ObjVenda->setMensagemDebug('Encontrada regra para familia comercial<br/>');
                    return $VerificaCampanhaBonificacaoFamiliaComercial;
                }
                else{
                    /*
                     * Verificando se há alguma regra para o produto
                     */
                    $VerificaCampanhaBonificacaoProduto = $this->VerificaCampanhaBonificacaoProduto();
                    if($VerificaCampanhaBonificacaoProduto !== false){
                        $this->ObjVenda->setMensagemDebug('Encontrada regra para produto<br/>');
                        return $VerificaCampanhaBonificacaoProduto;
                    }
                    else{
                        $this->ObjVenda->setMensagemDebug('Nenhuma regra encontrado<br/>');
                        return 0;
                    }
                }
            }
        }
        else{
            $this->ObjVenda->setMensagemDebug('Campanha de bonificação não encontrada');
            return 0;
        }
    }
}
?>