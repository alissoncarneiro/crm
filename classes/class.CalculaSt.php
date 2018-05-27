<?php
class CalculaSt{
    public $VlProduto;
    public $VlIPI;
    public $IdProduto;
    public $UFOrigem;
    public $PaisOrigem;
    public $UFDestino;
    public $PaisDestino;
    public $IdCFOP;
    public $IdClassificacaoFiscal;

    private $MensagemErro;
    private $PctIVA;
    private $PctICMSInterno;
    private $PctICMSExterno;
    private $VlICMSInterno;
    private $VlProdutoComIPI;
    private $VlICMSExterno;
    private $VlST;

    public function getMensagemErro(){
        return $this->MensagemErro;
    }

    public function getST(){
        //INICIANDO PROCESSO DE VALIDAÇÃO DE DADOS
        if(!is_numeric($this->VlProduto)){
            $this->MensagemErro = 'Valor produto não é um número';
            return false;
        } else if(empty($this->IdProduto)){
            $this->MensagemErro = 'Produto não foi informado';
            return false;
        } else if(empty($this->UFOrigem)){
            $this->MensagemErro = 'Estado de origem não informado';
            return false;
        } else if(empty($this->PaisOrigem)){
            $this->MensagemErro = 'País de origem não foi informado';
            return false;
        } else if(empty($this->UFDestino)){
            $this->MensagemErro = 'Estado de destino não informado';
            return false;
        } else if(empty($this->PaisDestino)){
            $this->MensagemErro = 'País de destino não foi informado';
            return false;
        } else if(empty($this->IdCFOP)){
            $this->MensagemErro = 'CFOP não foi informada';
            return false;
        }
        /*
        $SqlCFOP = "SELECT subs_trib, consum_final, icms_subs_trib, aliquota_icm FROM is_cfop WHERE numreg = '".$this->IdCFOP."'";
        $QryCFOP = query($SQLCFOP);
        $ArCFOP = farray($QryCFOP);

        //Sem ST
        if($ArCFOP['subs_trib'] != 1 || $ArCFOP['consum_final'] != 1){
            return 0;
        }
        */
        $this->VlProdutoComIPI = $this->VlProduto + $this->VlIPI;

        $QryICMS = query("SELECT pct_icms_interno, pct_icms_externo FROM is_aliquota_icms WHERE uf = '".$this->UFDestino."' AND pais = '".TrataApostrofoBD($this->PaisDestino)."'");
        $ArICMS = farray($QryICMS);

        $QryIVA = query("SELECT pct_iva FROM is_aliquota_iva WHERE id_classificacao_fiscal = '".$this->IdClassificacaoFiscal."' AND uf_destino = '".$this->UFDestino."'");
        $ArIVA = farray($QryIVA);

        $QryCFOP = query("SELECT * FROM is_cfop WHERE numreg = ".$this->IdCFOP);
        $ArCFOP = farray($QryCFOP);

        if(!empty($ArCFOP['pct_reducao_icms'])){
            $ArICMS['pct_icms_interno'] = uM::uMath_vl_menos_pct($ArCFOP['pct_reducao_icms'],$ArICMS['pct_icms_interno'],2);
        }

        /*
         * Iniciando os cálculos
         */
        $this->PctIVA = $ArIVA['pct_iva'];
        $this->PctICMSInterno = $ArICMS['pct_icms_interno'];
        $this->PctICMSExterno = $ArICMS['pct_icms_externo'];

        $this->VlUnitarioComIva = uM::uMath_vl_mais_pct($this->PctIVA,$this->VlProdutoComIPI);

        $this->VlICMSInterno = uM::uMath_pct_de_valor($this->PctICMSInterno,$this->VlUnitarioComIva);
        $this->VlICMSExterno = uM::uMath_pct_de_valor($this->PctICMSExterno,$this->VlProduto);

        $this->VlST = $this->VlICMSInterno - $this->VlICMSExterno;

        return $this->VlST;
    }
}



class CalculaStOld{

    public $VlProduto;
    public $IdProduto;
    public $UFOrigem;
    public $PaisOrigem;
    public $UFDestino;
    public $PaisDestino;
    public $NatOperacao;
    private $MensagemErro;
    private $PctIva;
    private $PctIcmsInterno;
    private $PctIcmsExterno;
    private $VlIcmsInterno;
    private $VlUnitarioComIva;
    private $VlIcmsExterno;
    private $VlSt;

    public function getMensagemErro(){
        return $this->MensagemErro;
    }

    public function getST(){
        //INICIANDO PROCESSO DE VALIDAÇÃO DE DADOS
        if(!is_numeric($this->VlProduto)){
            $this->MensagemErro = 'Valor produto não é um número';
            return false;
        } else if(empty($this->IdProduto)){
            $this->MensagemErro = 'Produto não foi informado';
            return false;
        } else if(empty($this->UFOrigem)){
            $this->MensagemErro = 'Estado de origem não informado';
            return false;
        } else if(empty($this->PaisOrigem)){
            $this->MensagemErro = 'País de origem não foi informado';
            return false;
        } else if(empty($this->UFDestino)){
            $this->MensagemErro = 'Estado de destino não informado';
            return false;
        } else if(empty($this->PaisDestino)){
            $this->MensagemErro = 'País de destino não foi informado';
            return false;
        } else if(empty($this->NatOperacao)){
            $this->MensagemErro = 'Natureza de operação não foi informada';
            return false;
        }

        //INICIANDO O PROCESSO DE CÁLCULO DE SUBSTITUIÇÃO TRIBUTÁRIA
        $SQLNatOperaca = "SELECT subs_trib, consum_final, icms_subs_trib, aliquota_icm FROM is_cfop WHERE numreg = '".$this->NatOperacao."'";
        $QryNatOperacao = query($SQLNatOperaca);
        $ArNatOperacao = farray($QryNatOperacao);

        $SqlEstadoOrigem = "SELECT per_icms_int,per_icms_ext FROM is_estados_uf WHERE uf = '".$this->UFOrigem."' AND pais = '".$this->PaisOrigem."'";
        $QryEstadoOrigem = query($SqlEstadoOrigem);
        $ArEstadoOrigem = farray($QryEstadoOrigem);

        $SqlEstadoDestino = "SELECT per_icms_int,per_icms_ext FROM is_estados_uf WHERE uf = '".$this->UFDestino."' AND pais = '".$this->PaisDestino."'";
        $QryEstadoDestino = query($SqlEstadoDestino);
        $ArEstadoDestino = farray($QryEstadoDestino);


        //Sem ST
        if($ArNatOperacao['subs_trib'] != 1){
            return 0;
        }
        //Regra CD0606
        elseif($ArNatOperacao['subs_trib'] == 1 && $ArNatOperacao['consum_final'] == 1){
            /*
             * Iniciando os cálculos
             */
            $this->PctIva = $ArNatOperacao['icms_subs_trib'];
            $this->PctIcmsInterno = $ArEstadoDestino['per_icms_int'];
            $this->PctIcmsExterno = $ArEstadoDestino['per_icms_ext'];

            #1º  ACHAR O ICMS PROPRIO
            $this->VlIcmsInterno = uM::uMath_pct_de_valor($this->PctIcmsInterno,$this->VlProduto);
            #2º ACHAR O VALOR DA BASE DE CALCULO DA ST
            #3º SOMAR O VALOR DO PRODUTO + O VALOR DA BASE DE ST
            $this->VlUnitarioComIva = uM::uMath_vl_mais_pct($this->PctIva,$this->VlProduto);
            #4º DIVIDIR O VALOR DA B.C ST PELA ALIQUOTA INTERESTADUAL
            $this->VlIcmsExterno = uM::uMath_pct_de_valor($this->PctIcmsExterno,$this->VlUnitarioComIva);
            #5º SUBTRAIR O VALOR DO ICMS PROPRIO COM O VALOR DA ALIQUOTA INTERESTADUAL
            $this->VlSt = $this->VlIcmsInterno - $this->VlIcmsExterno;

            return $this->VlSt;
        }
    }

}
?>