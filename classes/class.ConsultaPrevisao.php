<?php

/*
 * class.ConsultaPrevisao.php
 * Autor: Lucas
 * 24/11/2010 19:00:00
 *
 * Classe responsável por localizar as previsões de COMPRA
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class ConsultaPrevisao{

    protected $Item;
    protected $cnx;
    protected $cnxCliente;
    public $TpConsulta;

    public function __construct($VendaParametro){
        $this->VendaParametro = $VendaParametro;
        $this->TpConsulta = $this->VendaParametro->getModoConsultaEstoque();
        if($this->TpConsulta == '1'){
            $ArrayConf = parse_ini_file('../../conecta_odbc_erp_datasul.ini',true);
            $CnxODBC = ConectaODBCErpDatasul($ArrayConf,'ordem-compra');
            $this->cnx = $CnxODBC;
            $CnxODBC2 = ConectaODBCErpDatasul($ArrayConf,'emitente');
            $this->cnxCliente = $CnxODBC2;
        }

    }

    public function setIdEstabelecimento($estab){
        $this->Estab = $estab;
    }

    public function setIdProduto($Item){
        if($this->TpConsulta == '1'){
            $qry_produtos = query('SELECT id_produto_erp FROM is_produto WHERE numreg = \''.$Item.'\'');
            $produto = farray($qry_produtos);
            $this->Item = $produto['id_produto_erp'];
        }else{
            $this->Item = $Item;
        }
    }

    public function getConsultaPrevisao(){
        if($this->TpConsulta == '2'){
            return array();
        }
        $sql_previsao = 'SELECT t1."num-pedido", t1."data-emissao", t1."cod-emitente", t2."data-entrega", t2."quantidade"
                         FROM pub."ordem-compra" t1 JOIN pub."prazo-compra" t2 ON (t1."numero-ordem" = t2."numero-ordem")
			 WHERE t1."it-codigo" = \''.$this->Item.'\' AND
                               t1."situacao" != \'4\' AND
                               t2."qtd-sal-forn" != \'0.0000\'';
        $qry_previsao = odbc_exec($this->cnx,$sql_previsao) or die(odbc_errormsg().$sql_previsao);
        $new_array = array();
        while($ar_previsao = odbc_fetch_array($qry_previsao)){
            $sql_emitente = 'SELECT "nome-abrev" FROM pub.emitente WHERE "cod-emitente" = \''.$ar_previsao['cod-emitente'].'\'';
            $qry_emitente = odbc_exec($this->cnxCliente,$sql_emitente);
            $rs_emitente = odbc_fetch_array($qry_emitente);
            $nome_abrev = $rs_emitente['nome-abrev'];

            $new_array[] = array(
                $ar_previsao['num-pedido'],
                $ar_previsao['data-emissao'],
                $nome_abrev,
                $ar_previsao['data-entrega'],
                $ar_previsao['quantidade']
            );
        }
        return $new_array;
    }

}

//Exemplo de uso:
/*
  $a = new ConsultaPrevisao();
  $a->setCNX($cnx2);
  $a->setCNXCliente($cnx1);
  $a->setItem('01197');
 */
?>