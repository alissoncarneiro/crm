<?php
/*
 * class.SubstituicaoTributaria.php
 * Autor: Alex
 * 28/12/2010 10:37:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class SubstituicaoTributaria{
    private $IdPessoa;
    private $ObjPessoa;
    private $IdProduto;
    private $IdCFOP;
    private $UFOrigem;
    private $PaisOrigem;
    private $UFDestino;
    private $PaisDestino;
    private $VlMercadoria;
    private $VlFrete;
    private $VlExtra;

    private $AliquotaIPI;
    private $AliquotaIVA;
    private $AliquotaICMSST;
    private $AliquotaICMSEstadual;

    private $VlBase                 = 0;
    private $VlBaseComIPI           = 0;
    private $VlBaseComIPIeIVA       = 0;
    private $VlBaseICMSST           = 0;
    private $VlBaseICMSEstadual     = 0;

    private $Mensagem = array();
    private $MensagemLog = array();
    private $ObjCFOP;
    private $ObjProduto;
    private $ObjUFOrigem;
    private $ObjUFDestino;
    private $ObjVendaParametro;

    private $VlSubstituicaoTributaria;

    private $SnPossuiSubstituicaoTributaria = true;

    /**
     * Classe que efetua o cálculo do valor de substituição tributária
     * @param int $IdProduto Numreg do produto
     * @param int $IdCFOP Numreg do CFOP
     * @param string $UFOrigem Sigla do estado
     * @param string $PaisOrigem
     * @param string $UFDestino Sigla do estado
     * @param string $PaisDestino
     * @param decimal $VlMercadoria Valor da mercadoria
     * @param decimal $VlFrete Valor do Frete
     * @param decimal $VlExtra Valores adicionais para compor a base de cálculo
     * @return bool Retorna false caso algum parâmetro seja informado corretamente. Pode-se obter a mensagem de erro pelo método getMensagem()
     */
    public function  __construct($ObjVendaParametro,$IdPessoa,$IdProduto,$IdDestinoMercadoria,$IdCFOP,$UFOrigem,$PaisOrigem,$UFDestino,$PaisDestino,$AliquotaIPI,$VlMercadoria,$VlFrete,$VlExtra){
        $this->ObjVendaParametro    = $ObjVendaParametro;
        if(empty($IdProduto)){
            $this->setMensagem('Produto não informado.');
            return false;
        }
        elseif(empty($IdCFOP)){
            $this->setMensagem('CFOP não informado.');
            return false;
        }
        elseif($UFOrigem == '' || $PaisOrigem == '' || $UFDestino == '' || $PaisDestino == ''){
            $this->setMensagem('UF de Origem ou Pais de Origem ou UF Destino ou Pais de Destino não informado.');
            return false;
        }
        elseif($VlMercadoria == 0){
            return false;
        }
        $this->IdPessoa             = $IdPessoa;
        $this->ObjPessoa            = new Pessoa($this->IdPessoa);
        $this->IdProduto            = $IdProduto;
        $this->ObjProduto           = new Produto($this->IdProduto);
        $this->IdDestinoMercadoria  = $IdDestinoMercadoria;
        $this->IdCFOP               = $IdCFOP;
        $this->ObjCFOP              = new CFOP($this->IdCFOP);

        $this->UFOrigem             = $UFOrigem;
        $this->PaisOrigem           = $PaisOrigem;
        $this->ObjUFOrigem          = new UF($this->UFOrigem,$this->PaisOrigem);
        $this->UFDestino            = $UFDestino;
        $this->PaisDestino          = $PaisDestino;
        $this->ObjUFDestino         = new UF($this->UFDestino,$this->PaisDestino);

        $this->VlMercadoria         = $VlMercadoria;
        $this->VlFrete              = $VlFrete;
        $this->VlExtra              = $VlExtra;

        $this->AliquotaIPI          = $AliquotaIPI;
        $this->AliquotaReducaoIPI   = $this->ObjCFOP->getDadosCFOP('pct_reducao_ipi');

        $this->SnPossuiSuframa      = $this->ObjPessoa->getSnPossuiSuframa();
        $this->SnContribuinteICMS   = $this->ObjPessoa->getSnContribuinteICMS();

        $this->ObjICMS              = new ICMS($this->UFOrigem,$this->PaisOrigem,$this->UFDestino,$this->PaisDestino,$this->SnContribuinteICMS,$this->SnPossuiSuframa,$this->IdProduto,$this->ObjCFOP);

        $this->AliquotaICMSEstadual = $this->ObjICMS->CalculaAliquotaICMS();
        return true;
    }

    private function setMensagem($Mensagem){
        $this->Mensagem[] = $Mensagem;
    }

    private function setMensagemLog($MensagemLog){
        $this->MensagemLog[] = $MensagemLog;
    }

    /**
     * Se o parâmetro for true retorna uma array com os erro gerados, caso contrário retorna uma string com os erros gerados
     * @param bool $RetornaEmArray Define se será retornado em array ou em string
     * @param string $Separador String com o separador no caso de retorno em forma de string
     * @return string
     */
    public function getMensagem($RetornaEmArray=false,$Separador = ' | '){
        if($RetornaEmArray == true){
            return $this->Mensagem;
        }
        return implode($Separador,$this->Mensagem);
    }

    /**
     * Se o parâmetro for true retorna uma array com os erro gerados, caso contrário retorna uma string com os erros gerados
     * @param bool $RetornaEmArray Define se será retornado em array ou em string
     * @param string $Separador String com o separador no caso de retorno em forma de string
     * @return string
     */
    public function getMensagemLog($RetornaEmArray=false,$Separador = ' | '){
        if($RetornaEmArray == true){
            return $this->MensagemLog;
        }
        return implode($Separador,$this->MensagemLog);
    }

    /**
     * Calcula e Retorna o valor da substituição tributária
     * @return decimal
     */
    public function CalculaVlSubstituicaoTributaria(){

        /*
         * Fazendo as consistências para não calcular a ST
         */
        if(!$this->ObjVendaParametro->getSnCalculaST()){ /* Não calcula Substituição caso a flag do Oasis não esteja marcada com sim */
            $this->setMensagemLog('Parâmetro de venda para calular ST está como NÃO');

            $this->SnPossuiSubstituicaoTributaria = false;
            return 0;
        }
        if($this->IdDestinoMercadoria != '1'){ /* Se o destino da mercadoria for diferente 1 = Industrialização não possui ST */
            $this->setMensagemLog('Destino da mercadoria('.$this->IdDestinoMercadoria.') deve ser 1/Industrialização');

            $this->SnPossuiSubstituicaoTributaria = false;
            return 0;
        }
        if($this->ObjUFDestino->getDadosUF('sn_possui_st') != '1'){ /* Se o estado não possui ST */
            $this->setMensagemLog('O Estado de destino('.$this->ObjUFDestino->getDadosUF('uf').') não possui ST');

            $this->SnPossuiSubstituicaoTributaria = false;
            return 0;
        }
        if($this->ObjCFOP->getDadosCFOP('subs_trib') != '1'){ /* Quando na natureza de operação não possui a flag de ST retorn 0 */
            $this->setMensagemLog('A natureza de operação/CFOP('.$this->ObjCFOP->getDadosCFOP('id_cfop_erp').') não possui ST');

            $this->SnPossuiSubstituicaoTributaria = false;
            return 0;
        }

        $SqlItemUF = "SELECT pct_sub_tri,pct_icms_estadual FROM is_produto_uf WHERE id_produto = ".$this->IdProduto." AND uf_origem = '".$this->UFOrigem."' AND uf_destino = '".$this->UFDestino."'";
        $QryItemUF = query($SqlItemUF);
        if(numrows($QryItemUF) == 0){/* Caso não seja encontrado registro nesta tabela, indica que não possui substituição tributária */
            $this->setMensagemLog('Não encontrado nenhum registro para o produto '.$this->ObjProduto->getDadosProduto('id_produto_erp').' estado de origem '.$this->UFOrigem.' e estado de destino'.$this->UFDestino.' no cadastro de ProdutoxEstados');

            $this->SnPossuiSubstituicaoTributaria = false;
            return 0;
        }
        else{
            $ArItemUF = farray($QryItemUF);
            $this->AliquotaIVA = $ArItemUF['pct_sub_tri'];
            $this->AliquotaICMSST = $ArItemUF['pct_icms_estadual'];

            $this->setMensagemLog('Encontrado IVA de '.$this->AliquotaIVA.'% e aliquota de ICMS de '.$this->AliquotaICMSST.'%');
        }

        $SqlProdutoPessoa = "SELECT pct_sub_tri FROM is_produto_pessoa WHERE id_produto = ".$this->IdProduto." AND id_pessoa = ".$this->IdPessoa;
        $QryProdutoPessoa = query($SqlProdutoPessoa);
        if(numrows($QryProdutoPessoa) > 0){
            $ArProdutoPessoa = farray($QryProdutoPessoa);
            if($ArProdutoPessoa['pct_sub_tri'] > 0){
                $IvaAnterior = $this->AliquotaIVA;
                $this->AliquotaIVA = $ArProdutoPessoa['pct_sub_tri'];
                $this->setMensagemLog('Substituido o IVA de '.$IvaAnterior.'% para '.$this->AliquotaIVA.'% devido o cliente possuir um IVA especial');
            }
        }

        $this->VlBase               = $this->VlMercadoria;

        $IPI = new IPI();
        $IPI->setObjCFOP($this->ObjCFOP);
        $IPI->setPctAliquotaIPI($this->AliquotaIPI);

        $VlIPI = $IPI->CalculaVlIPI($this->VlBase);

        $this->VlBaseComIPI         = $this->VlBase + $VlIPI;
        $VlIVA                      = uM::uMath_pct_de_valor($this->AliquotaIVA, $this->VlBaseComIPI, 2, 2);
        $this->VlBaseComIPIeIVA     = $this->VlBaseComIPI + $VlIVA;

        if($this->ObjCFOP->getSnPossuiReducaoICMS()){
            $this->VlBaseComIPIeIVA = uM::uMath_vl_menos_pct($this->ObjCFOP->getDadosCFOP('pct_reducao_icms'), $this->VlBaseComIPIeIVA, 2, 2);
            $this->VlBase = uM::uMath_vl_menos_pct($this->ObjCFOP->getDadosCFOP('pct_reducao_icms'), $this->VlBase, 2, 2);
        }

        $this->VlBaseICMSST         = uM::uMath_pct_de_valor($this->AliquotaICMSST,$this->VlBaseComIPIeIVA, 2, 2);
        $this->VlBaseICMSEstadual   = uM::uMath_pct_de_valor($this->AliquotaICMSEstadual,$this->VlBase, 2, 2);

        $this->VlSubstituicaoTributaria = $this->VlBaseICMSST - $this->VlBaseICMSEstadual;

        $this->setMensagemLog('LOG ICMS<hr>'.$this->ObjICMS->getMensagemLog(false,'<br>').'<br>');
        $this->setMensagemLog('<br>Valor da Mercadoria = '.$this->VlBase);
        $this->setMensagemLog('<br>Valor Base de Calculo Com IPI = '.$this->VlBaseComIPI);
        $this->setMensagemLog('<br>Valor Base de Calculo Com IPI e IVA = '.$this->VlBaseComIPIeIVA);
        $this->setMensagemLog('<br>Valor Base de ICMS ST = '.$this->VlBaseICMSST);
        $this->setMensagemLog('<br>Valor Base de ICMS Estadual = '.$this->VlBaseICMSEstadual);
        $this->setMensagemLog('<br>Valor da ST = '.$this->VlSubstituicaoTributaria);

        return $this->VlSubstituicaoTributaria;
    }

    /**
     * Retorna o valor da substituição tributária após o cálculo
     * @return decimal
     */
    public function getVlSubstituicaoTributaria(){
        return $this->VlSubstituicaoTributaria;
    }

    public function getSnPossuiSubstituicaoTributaria(){
        return $this->SnPossuiSubstituicaoTributaria;
    }
}
?>