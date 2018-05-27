<?php

/*
 * class.Url.php
 * Autor: Alex
 * 22/10/2010 09:15:00
 * Classe responsável por manipular URLs
 *
 * Exemplo de  Uso
 * $Url = new Url();
 * $Url->setUrl('http://192...../index.php?get1=2&get2=3&get3=4');
 * $Url->RemoveParam('get2');
 * $Url->AlteraParam('get3','teste');
 * echo $Url->getUrl();
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class Url{

    private $Url;
    private $Url_scheme;
    private $Url_host;
    private $Url_path;
    private $Url_query;

    private $QueryString;
    private $QueryStringParam;

    private $TipoUrl;
    private $TipoQueryString;

    /*
     * Metodos de encapsulamento
     */

    public function setUrl($Url){
        if($this->TipoUrl == true && $this->TipoQueryString == true){
            return false;
        }
        $ParseUrl = parse_url($Url);

        $this->Url = $Url;
        $this->Url_scheme = $ParseUrl['scheme'];
        $this->Url_host = $ParseUrl['host'];
        $this->Url_port = $ParseUrl['port'];
        $this->Url_path = $ParseUrl['path'];
        $this->Url_query = $ParseUrl['query'];
        
        $this->QueryStringParam = $this->getArrayVariaveisDeUrl($Url,true);

        $this->TipoUrl = true;
    }

    public function setQueryString($QueryString){
        if($this->TipoUrl == true && $this->TipoQueryString == true){
            return false;
        }
        $this->QueryString = $QueryString;
        $this->QueryStringParam = $this->getArrayVariaveisDeUrl($QueryString);

        $this->TipoQueryString = true;
    }

    public function getQueryStringParam(){
        return $this->QueryStringParam;
    }

    public function getParam($Param){
        return $this->QueryStringParam[$Param];
    }

    public function getUrl(){
        return $this->RemontaUrl();
    }
    /*
     * Metodos de funcionalidades
     */
    public function RemoveParam($Param){
        if(isset($this->QueryStringParam[$Param])){
            unset($this->QueryStringParam[$Param]);
        }
    }

    public function AlteraParam($Param, $Valor){
        $this->QueryStringParam[$Param] = $Valor;
    }

    public function getArrayVariaveisDeUrl($QueryString,$Url=false){

        $Retorno = array();

        if($Url == true){
            $InicioQueryString = strpos($QueryString,"?");
            if($InicioQueryString === false){
                return false;
            }
            $InicioQueryString += 1;
            $FimQueryString = strpos($QueryString,"#",$InicioQueryString);
            if($FimQueryString){
                $QueryString = substr($QueryString,$InicioQueryString,$FimQueryString - $InicioQueryString);
            }
            else{
                $QueryString = substr($QueryString,$InicioQueryString);
            }
        }

        $ArrayVariaveis = explode("&",$QueryString);
        foreach($ArrayVariaveis as $ArrayVariavel){
            $PrimeiroDivisor = strpos($ArrayVariavel,"=");
            $Retorno[substr($ArrayVariavel,0,$PrimeiroDivisor)] = substr($ArrayVariavel,$PrimeiroDivisor + 1,strlen($ArrayVariavel));
        }
        return($Retorno);
    }

    public function getStringQueryString(){
        $StringQueryString = '';
        $ArrayChavesValores = array();
        foreach($this->QueryStringParam as $k => $v){
            $ArrayChavesValores[] = $k.'='.$v;
        }
        $StringQueryString .= implode('&',$ArrayChavesValores);
        return $StringQueryString;
    }

    public function RemontaUrl(){
        if($this->TipoUrl != true){//Se o tipo não é URL
            return false;
        }
        $StringUrl = $this->Url_scheme;
        $StringUrl .= '://';
        $StringUrl .= $this->Url_host;
        $StringUrl .= ($this->Url_port != '')?':'.$this->Url_port:'';
        $StringUrl .= $this->Url_path;
        $StringUrl .= '?';
        $StringUrl .= $this->getStringQueryString();

        return $StringUrl;
    }
}
?>
