<?php
/*
 * class.ICMS.php
 * Autor: Alex
 * 19/08/2011 14:00:00
 */
class ICMS{

    private $PctAliquotaICMS;

    private $UFOrigem;
    private $PaisOrigem;
    private $UFDestino;
    private $PaisDestino;
    private $SnContribuinteICMS;
    private $SnPossuiSuframa;
    private $IdProduto;
    private $ObjCFOP;

    public $PrecisaoCalculoIntermediario                = 2;
    public $TipoArredondamentoIntermediario             = 2;
    public $PrecisaoCalculoFinal                        = 2;
    public $TipoArredondamentoFinal                     = 2;

    /**
     * 1 - Unidade-Federaзгo / 2 - Natureza Operaзгo
     * @var int
     */
    private $ParametroAliquotaICMS;

    private $Mensagem = array();
    private $MensagemLog = array();

    /**
     * Classe para alнquota e cбlculos de ICMS
     * @param string $UFOrigem
     * @param string $PaisOrigem
     * @param string $UFDestino
     * @param string $PaisDestino
     * @param string $SnContribuinteICMS
     * @param boolean $SnPossuiSuframa
     * @param int $IdProduto
     * @param CFOP $ObjCFOP
     */
    public function __construct($UFOrigem,$PaisOrigem,$UFDestino,$PaisDestino,$SnContribuinteICMS,$SnPossuiSuframa,$IdProduto,CFOP $ObjCFOP){
        $this->UFOrigem = strtoupper($UFOrigem);
        $this->PaisOrigem = strtoupper($PaisOrigem);
        $this->UFDestino = strtoupper($UFDestino);
        $this->PaisDestino = strtoupper($PaisDestino);
        $this->SnContribuinteICMS = $SnContribuinteICMS;
        $this->SnPossuiSuframa = $SnPossuiSuframa;
        $this->IdProduto = $IdProduto;
        $this->ObjCFOP = $ObjCFOP;

        $this->setMensagemLog('Parametros de Entreda. Origem('.$this->UFOrigem.'-'.$this->PaisOrigem.') Destino('.$this->UFDestino.'-'.$this->PaisDestino.').');

        $SqlParamAliquotaICMS = "SELECT parametro FROM is_parametros_sistema WHERE id_parametro = 'aliq_icms_nao_contrib'";
        $QryParamAliquotaICMS = query($SqlParamAliquotaICMS);
        $ArParamAliquotaICMS = farray($QryParamAliquotaICMS);

        $this->ParametroAliquotaICMS = $ArParamAliquotaICMS['parametro'];

        $VendaParametro = new VendaParametro();
        $this->PrecisaoCalculoIntermediario               = $VendaParametro->getPrecisaoCalculoIntermediarioMoedaPadrao();
        $this->TipoArredondamentoIntermediario            = $VendaParametro->getTipoArredondamentoIntermediarioMoedaPadrao();
        $this->PrecisaoCalculoFinal                       = $VendaParametro->getPrecisaoCalculoFinalMoedaPadrao();
        $this->TipoArredondamentoFinal                    = $VendaParametro->getTipoArredondamentoFinalMoedaPadrao();
    }

    public function getAliquotaICMS(){
        return $this->PctAliquotaICMS;
    }

    private function setMensagem($Mensagem){
        $this->Mensagem[] = $Mensagem;
    }

    private function setMensagemLog($MensagemLog){
        $this->MensagemLog[] = $MensagemLog;
    }

    public function getMensagem($RetornaEmArray=false,$Separador = ' | '){
        if($RetornaEmArray == true){
            return $this->Mensagem;
        }
        return implode($Separador,$this->Mensagem);
    }

    public function getMensagemLog($RetornaEmArray=false,$Separador = ' | '){
        if($RetornaEmArray == true){
            return $this->MensagemLog;
        }
        return implode($Separador,$this->MensagemLog);
    }

    public function ValidaParametrosPreenchidos(){
        if($this->UFOrigem == ''){
            $this->setMensagemLog('UF origem deve ser preenchido.');
            return false;
        }
        if($this->PaisOrigem == ''){
            $this->setMensagemLog('Pais origem deve ser preenchido.');
            return false;
        }
        if($this->UFDestino == ''){
            $this->setMensagemLog('UF destino deve ser preenchido.');
            return false;
        }
        if($this->PaisDestino == ''){
            $this->setMensagemLog('Pais destino deve ser preenchido.');
            return false;
        }
        if($this->SnContribuinteICMS === NULL){
            $this->setMensagemLog('Contribuinte ICMS deve ser preenchido.');
            return false;
        }
        if($this->SnPossuiSuframa === NULL){
            $this->setMensagemLog('Cliente possui suframa deve ser preenchido.');
            return false;
        }
        if($this->IdProduto == ''){
            $this->setMensagemLog('Produto deve ser preenchido.');
            return false;
        }
        if(!is_object($this->ObjCFOP) || !$this->ObjCFOP instanceof CFOP){
            $this->setMensagemLog('Parвmetro CFOP deve ser um objeto do tipo CFOP.');
            return false;
        }
        return true;
    }

    public function CalculaAliquotaICMS(){
        if(!$this->ValidaParametrosPreenchidos()){
            return false;
        }
        if($this->SnPossuiSuframa){
            $this->setMensagemLog('Cliente possui suframa, retornando alнquota de 0%');
            $this->PctAliquotaICMS = 0;
            return $this->PctAliquotaICMS;
        }
        $this->setMensagemLog('Cliente nгo й SUFRAMA');
        $this->setMensagemLog('Parвmetro Utilizado para obter a aliquota de ICMS '.$this->ParametroAliquotaICMS.' (1 = Unidade-Federaзгo / 2 = Natureza Operaзгo (ERP Datasul: FT0301))');


        if($this->SnContribuinteICMS){
            $this->setMensagemLog('Iniciando regra de clientes contribuintes de ICMS.');

            if($this->PaisOrigem == $this->PaisDestino && $this->UFOrigem == $this->UFDestino){
                $this->setMensagemLog('Estados e paнses de origem e destino iguais.');

                $SqlProdutoDiferenciado = "SELECT pct_icms FROM is_icms_produto_diferenciado WHERE uf = '".TrataApostrofoBD($this->UFDestino)."' AND pais = '".TrataApostrofoBD($this->PaisDestino)."' AND id_produto = '".TrataApostrofoBD($this->IdProduto)."'";
                $QryProdutoDiferenciado = query($SqlProdutoDiferenciado);
                $ArProdutoDiferenciado = farray($QryProdutoDiferenciado);
                if($ArProdutoDiferenciado){
                    $this->setMensagemLog('Encontrada alнquota de '.$ArProdutoDiferenciado['pct_icms'].'% no cadastro de icms produtos diferenciados.');
                    $this->PctAliquotaICMS = $ArProdutoDiferenciado['pct_icms'];
                    return $this->PctAliquotaICMS;
                }

                $SqlEstadosUF = "SELECT per_icms_int FROM is_estados_uf WHERE uf = '".TrataApostrofoBD($this->UFOrigem)."' AND pais = '".TrataApostrofoBD($this->PaisOrigem)."'";
                $QryEstadosUF = query($SqlEstadosUF);
                $ArEstadosUF = farray($QryEstadosUF);

                if($ArEstadosUF['per_icms_int'] > 0){
                    $this->setMensagemLog('Econtrada aliquota de '.$ArEstadosUF['per_icms_int'].'% no cadastro de Unidade de Federaзгo');
                    $this->PctAliquotaICMS = $ArEstadosUF['per_icms_int'];
                    return $this->PctAliquotaICMS;
                }
                else{
                    $this->setMensagemLog('Alнquota da Unidade de federaзгo 0, buscando a alнquota da CFOP');
                    $SqlCFOP = "SELECT aliquota_icm FROM is_cfop WHERE numreg = '".$this->ObjCFOP->getDadosCFOP('numreg')."'";
                    $QryCFOP = query($SqlCFOP);
                    $ArCFOP = farray($QryCFOP);
                    $this->PctAliquotaICMS = $ArCFOP['aliquota_icm'];
                    return $this->PctAliquotaICMS;
                }
            }
            else{ /* Se nгo й dentro do estado */
                $this->setMensagemLog('Estado de origem diferente do estado de destino.');
                $SqlICMSUFExcecoes = "SELECT pct_icms_interestadual FROM is_icms_uf_excecoes WHERE uf_origem = '".TrataApostrofoBD($this->UFOrigem)."' AND uf_destino = '".TrataApostrofoBD($this->UFDestino)."' AND pais_destino = '".TrataApostrofoBD($this->PaisDestino)."'";
                $QryICMSUFExcecoes = query($SqlICMSUFExcecoes);
                $ArICMSUFExcecoes = farray($QryICMSUFExcecoes);
                if($ArICMSUFExcecoes){
                    $this->setMensagemLog('Encontrada alнquota de ICMS de '.$ArICMSUFExcecoes['pct_icms_interestadual'].' % no cadastro de exceзхes para o estado de destino');
                    $this->PctAliquotaICMS = $ArICMSUFExcecoes['pct_icms_interestadual'];
                    return $this->PctAliquotaICMS;
                }

                $SqlEstadosUF = "SELECT per_icms_ext FROM is_estados_uf WHERE uf = '".TrataApostrofoBD($this->UFOrigem)."' AND pais = '".TrataApostrofoBD($this->PaisOrigem)."'";
                $QryEstadosUF = query($SqlEstadosUF);
                $ArEstadosUF = farray($QryEstadosUF);

                if($ArEstadosUF['per_icms_ext'] > 0 ){
                    $this->setMensagemLog('Encontrada aliquota de ICMS de '.$ArEstadosUF['per_icms_ext'].'% para estado de origem.');
                    $this->PctAliquotaICMS = $ArEstadosUF['per_icms_ext'];
                    return $this->PctAliquotaICMS;
                }
                else{
                    $this->setMensagemLog('Alнquota da Unidade de federaзгo 0, buscando a alнquota da CFOP');
                    $SqlCFOP = "SELECT aliquota_icm FROM is_cfop WHERE numreg = '".$this->ObjCFOP->getDadosCFOP('numreg')."'";
                    $QryCFOP = query($SqlCFOP);
                    $ArCFOP = farray($QryCFOP);
                    $this->setMensagemLog('Obtendo alнquota de '.$ArCFOP['aliquota_icm'].'% da CFOP.');
                    $this->PctAliquotaICMS = $ArCFOP['aliquota_icm'];
                    return $this->PctAliquotaICMS;
                }
            }
        }
        else{
            $this->setMensagemLog('Cliente nгo contribuinte de ICMS');

            $SqlProdutoDiferenciado = "SELECT pct_icms,sn_descons_nao_contrib FROM is_icms_produto_diferenciado WHERE uf = '".TrataApostrofoBD($this->UFDestino)."' AND pais = '".TrataApostrofoBD($this->PaisDestino)."' AND id_produto = '".TrataApostrofoBD($this->IdProduto)."'";
            $QryProdutoDiferenciado = query($SqlProdutoDiferenciado);
            $ArProdutoDiferenciado = farray($QryProdutoDiferenciado);
            if($ArProdutoDiferenciado){
                $this->setMensagemLog('Encontrada aliquota de produto diferenciado');
                if($ArProdutoDiferenciado['sn_descons_nao_contrib'] == '1'){
                    $this->setMensagemLog('A aliquota diferenciada desconsidera nгo contribuintes de ICMS, retornando 0%');
                    $this->PctAliquotaICMS = 0;
                    return $this->PctAliquotaICMS;
                }
                elseif($ArProdutoDiferenciado['sn_descons_nao_contrib'] == '0'){
                    $this->setMensagemLog('Encontrada alнquota de '.$ArProdutoDiferenciado['pct_icms'].'% no cadastro de icms produtos diferenciados.');
                    $this->PctAliquotaICMS = $ArProdutoDiferenciado['pct_icms'];
                    return $this->PctAliquotaICMS;
                }
                else{
                    return false;
                }
            }

            if($this->ParametroAliquotaICMS == '1'){
                $SqlEstadosUF = "SELECT per_icms_int FROM is_estados_uf WHERE uf = '".TrataApostrofoBD($this->UFOrigem)."' AND pais = '".TrataApostrofoBD($this->PaisOrigem)."'";
                $QryEstadosUF = query($SqlEstadosUF);
                $ArEstadosUF = farray($QryEstadosUF);

                $this->setMensagemLog('Obtendo a aliquota de '.$ArEstadosUF['per_icms_int'].'% do estado de origem');
                $this->PctAliquotaICMS = $ArEstadosUF['per_icms_int'];
                return $this->PctAliquotaICMS;
            }
            elseif($this->ParametroAliquotaICMS == '2'){
                $SqlCFOP = "SELECT aliquota_icm FROM is_cfop WHERE numreg = '".$this->ObjCFOP->getDadosCFOP('numreg')."'";
                $QryCFOP = query($SqlCFOP);
                $ArCFOP = farray($QryCFOP);
                $this->setMensagemLog('Obtendo alнquota de '.$ArCFOP['aliquota_icm'].'% da CFOP.');
                $this->PctAliquotaICMS = $ArCFOP['aliquota_icm'];
                return $this->PctAliquotaICMS;
            }
            else{
                return false;
            }
        }
    }

    public function CalculaValorICMS($VlBaseCalculo){
        $PctAliquotaICMS = ($PctAliquotaICMS === NULL)?$this->PctAliquotaICMS:$PctAliquotaICMS;
        $this->setMensagemLog('Calculando valor de ICMS sobre a base de '.$VlBaseCalculo);
        if($this->ObjCFOP->getSnPossuiReducaoICMS() > 0){
            $PctReducaoICMS = $this->ObjCFOP->getDadosCFOP('pct_reducao_icms');
            $VlBaseCalculo = uM::uMath_vl_menos_pct($PctReducaoICMS, $VlBaseCalculo, $this->PrecisaoCalculoFinal, $this->TipoArredondamentoFinal);
            $this->setMensagemLog('CFOP possui reduзгo de ICMS de '.$PctReducaoICMS.'%. Valor da base com reduзгo: '.$VlBaseCalculo);
        }
        $VlICMS = uM::uMath_pct_de_valor($PctAliquotaICMS, $VlBaseCalculo, $this->PrecisaoCalculoFinal, $this->TipoArredondamentoFinal);
        $this->setMensagemLog('Valor do ICMS '.$VlICMS.'.');
        return $VlICMS;
    }
}
?>