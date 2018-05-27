<?php
/*
 * Alisson Carneiro
 * 04/08/2011.
 */

class Transportadora {
    private $IdTransportadora;
    private $ArrayDadosTransportadora = array();

    public function __construct($IdTransportadora){
        $this->IdTransportadora = $IdTransportadora;
        $QryTransportadora = query("SELECT * FROM is_transportadora WHERE numreg = ".$this->IdTransportadora);
        if(numrows($QryTransportadora) == 1){
            $ArTransportadora = farray($QryTransportadora);
            $this->ArrayDadosTransportadora = $ArTransportadora;
        }
    }

    public function getDadosTransportadora($CampoTabelaTransportadora){
        return $this->ArrayDadosTransportadora[$CampoTabelaTransportadora];
    }

}
?>