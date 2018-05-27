<?php
/*
 * class.IPI.php
 * Autor: Alex
 * 19/08/2011 13:25:00
 */
class IPI{
    private $ObjCFOP;
    private $PctAliquotaIPI;

    private $Mensagem = array();
    private $MensagemLog = array();

    public $PrecisaoCalculoIntermediario                = 2;
    public $TipoArredondamentoIntermediario             = 2;
    public $PrecisaoCalculoFinal                        = 2;
    public $TipoArredondamentoFinal                     = 2;

    public $PrecisaoCalculoUnitarioConversao            = 2;
    public $TipoArredondamentoCalculoUnitarioConversao  = 2;
    public $PrecisaoCalculoTotalConversao               = 2;
    public $TipoArredondamentoCalculoTotalConversao     = 2;

    public function __construct(){
        $VendaParametro = new VendaParametro();
        $this->PrecisaoCalculoIntermediario               = $VendaParametro->getPrecisaoCalculoIntermediarioMoedaPadrao();
        $this->TipoArredondamentoIntermediario            = $VendaParametro->getTipoArredondamentoIntermediarioMoedaPadrao();
        $this->PrecisaoCalculoFinal                       = $VendaParametro->getPrecisaoCalculoFinalMoedaPadrao();
        $this->TipoArredondamentoFinal                    = $VendaParametro->getTipoArredondamentoFinalMoedaPadrao();
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

    public function setObjCFOP(CFOP $ObjCFOP){
        $this->ObjCFOP = $ObjCFOP;
    }

    public function setPctAliquotaIPI($PctAliquotaIPI){
        $this->PctAliquotaIPI = $PctAliquotaIPI;
    }

    public function CalculaVlIPI($VlBaseCalculo){
        $VlTotalIPI = 0;
        $PctAliquotaReducaoIPI = $this->ObjCFOP->getDadosCFOP('pct_reducao_ipi');

        if($this->ObjCFOP->getDadosCFOP('cd_trib_ipi') == '1'){/* Tributado */
            $this->setMensagemLog('CFOP tributa IPI<br/>');
            $this->setMensagemLog('Cálculo de IPI('.$this->PctAliquotaIPI.'%) sobre base de ('.$VlBaseCalculo.')<br/>');
            $VlTotalIPI = uM::uMath_pct_de_valor($this->PctAliquotaIPI, $VlBaseCalculo, $this->PrecisaoCalculoFinal, $this->TipoArredondamentoFinal);
            $this->setMensagemLog('Valor total IPI '.number_format_min($VlTotalIPI,2,',','.').'<br/>');
        }
        elseif($this->ObjCFOP->getDadosCFOP('cd_trib_ipi') == '2'){/* Isento */
            $this->setMensagemLog('CFOP não tributa IPI<br/>');
            $VlTotalIPI = 0;
        }
        elseif($this->ObjCFOP->getDadosCFOP('cd_trib_ipi') == '4'){/* Reduzido */
            $this->setMensagemLog('CFOP tributa IPI com redução<br/>');
            $this->setMensagemLog('Cálculo de IPI('.$this->PctAliquotaIPI.'%) sobre base de ('.$VlBaseCalculo.') com redução de ('.$PctAliquotaReducaoIPI.'%)<br/>');

            $VlBaseCalculo = uM::uMath_vl_menos_pct($PctAliquotaReducaoIPI,$VlBaseCalculo,$this->PrecisaoCalculoFinal,$this->TipoArredondamentoFinal);
            $this->setMensagemLog('Valor da base reduzida ('.$VlBaseCalculo.')');

            $VlTotalIPI = uM::uMath_pct_de_valor($this->PctAliquotaIPI, $VlBaseCalculo, $this->PrecisaoCalculoFinal, $this->TipoArredondamentoFinal);
            $this->setMensagemLog('Valor do IPI com redução ('.$VlTotalIPI.')<br/>');
        }
        $this->setMensagemLog('Fim do cálculo de IPI');
        return $VlTotalIPI;
    }
}
?>