<?php

/*
 * class.ConsultaEstoque.php
 * Autor: Lucas
 * 24/11/2010 13:30:00
 *
 * Classe responsável por consultar o estoque
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

//include('../conecta.php');
//include('../functions.php');
//include('../funcoes.php');

class ConsultaEstoque{

    protected $TpConsulta;
    protected $cnx;
    protected $Item;
    protected $NumregItem;
    protected $VendaParametro;
    public $Estab;
    public $qtidade_disp = 0;

    public function __construct($VendaParametro){
        $this->VendaParametro = $VendaParametro;
        $this->TpConsulta = $this->VendaParametro->getModoConsultaEstoque();
        if($this->TpConsulta == '1'){
            $ArrayConf = parse_ini_file('../../conecta_odbc_erp_datasul.ini',true);
            try{
                $CnxODBC = ConectaODBCErpDatasul($ArrayConf,'saldo-estoq');
            }
            catch(Exception $e ){
                echo $e->getMessage();
                return false;
            }
            $this->cnx = $CnxODBC;
        }
    }

    public function setTpConsulta($TpConsultaCustom){
        if($TpConsultaCustom == '1'){
            $this->TpConsulta = $TpConsultaCustom;
        }else{
            $this->TpConsulta = 'local';
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
            $this->NumregItem = $Item;
        }else{
            $this->Item = $this->NumregItem = $Item;
        }
    }

    public function getSaldoEstoque(){
        $SqlDeposito = "SELECT id_deposito FROM is_estabel_x_depos";
        $SqlDeposito .= ($this->Estab != '')?" WHERE id_estabelecimento = '".$this->Estab."'":'';
        
        $qry_estabelecimento = query($SqlDeposito);
        $ar_deposito = array();
        while($ar_estabelecimento = farray($qry_estabelecimento)){
            $ar_deposito[] = $ar_estabelecimento['id_deposito'];
        }
        if($this->Estab != ''){
            $estab = farray(query('select * from is_estabelecimento where numreg =  \''.$this->Estab.'\''));
            $this->Estab = $estab['id_estabelecimento_erp'];
        }
        
        if($this->TpConsulta == '1'){
            if(!$this->cnx){return;}
            if(count($ar_deposito)>0){
                $in = '"cod-depos" IN (\''.implode("','",$ar_deposito).'\') AND';
            }
            if($this->Estab!=''){
                $stab = '"cod-estabel" = \''.$this->Estab.'\' AND ';
            }
            $sql_estoque_odbc = 'SELECT SUM("qtidade-atu") AS "saldo","cod-estabel","lote","dt-vali-lote","cod-refer"
                                FROM pub."saldo-estoq"
                                WHERE "it-codigo" = \''.$this->Item.'\' AND
                                    '.$in.$stab.'
                                    "qtidade-atu" > 0
                                GROUP BY "cod-estabel",
                                    "lote",
                                    "dt-vali-lote",
                                    "cod-refer"
                                ORDER BY "dt-vali-lote" ASC';
            $qry_estoque_odbc = odbc_exec($this->cnx,$sql_estoque_odbc);
            $new_array = array();
            while($ar_estoque_odbc = odbc_fetch_array($qry_estoque_odbc)){
                $new_array[] = array(
                                    $ar_estoque_odbc['cod-estabel'],
                                    $ar_estoque_odbc['saldo'],
                                    $ar_estoque_odbc['lote'],
                                    $ar_estoque_odbc['dt-vali-lote'],
                                    $ar_estoque_odbc['cod-refer']
                                );
            }
            return $new_array;
        }
        else{
            if(count($ar_deposito)>0){
                $in = 'cod_depos IN (\''.implode("','",$ar_deposito).'\') AND';
            }
            if($this->Estab!=''){
                    $stab = 'cod_estabel = \''.$this->Estab.'\' AND ';
            }
            $sql_estoque_odbc = 'SELECT SUM(qtidade_atu) AS saldo,cod_estabel,lote,dt_vali_lote,cod_refer
                                FROM is_saldo_estoq
                                WHERE it_codigo = \''.$this->Item.'\' AND
                                    '.$in.$stab.'
                                    qtidade_atu > 0
                                GROUP BY cod_estabel,
                                    lote,
                                    dt_vali_lote,
                                    cod_refer
                                ORDER BY dt_vali_lote ASC';
            $qry_estoque_odbc = query($sql_estoque_odbc);
            $new_array = array();
            while($ar_estoque_odbc = farray($qry_estoque_odbc)){
                $new_array[] = array(
                                    $ar_estoque_odbc['cod_estabel'],
                                    $ar_estoque_odbc['saldo'],
                                    $ar_estoque_odbc['lote'],
                                    $ar_estoque_odbc['dt_vali_lote'],
                                    $ar_estoque_odbc['cod_refer']
                                );
            }
            return $new_array;
        }
    }

    public function getSaldoEstoqueTotal(){
        if($this->TpConsulta == '1'){
            if(!$this->cnx){return;}
            if($this->Estab != ''){
                $stab = ' AND "cod-estabel" = \''.$this->Estab.'\'';
            }
            
            $SqlDeposito = "SELECT id_deposito FROM is_estabel_x_depos";
            $SqlDeposito .= ($this->Estab != '')?" WHERE id_estabelecimento = '".$this->Estab."'":'';
            
            $qry_estabelecimento = query($SqlDeposito);
            $ar_deposito = array();
            while($ar_estabelecimento = farray($qry_estabelecimento)){
                $ar_deposito[] = $ar_estabelecimento['id_deposito'];
            }
            if(count($ar_deposito)>0){
                $in = ' AND "cod-depos" IN (\''.implode("','",$ar_deposito).'\')';
            }
            
            $sql_estoque_odbc = 'SELECT SUM("qtidade-atu") AS "qtidade-atu", SUM("qtidade-atu" - ("qt-aloc-prod" + "qt-alocada" +  "qt-aloc-ped")) AS "qtidade_disp"
                                 FROM pub."saldo-estoq"
                                 WHERE "it-codigo" = \''.$this->Item.'\''.$in.$stab.' AND "qtidade-atu" > 0';
            $qry_estoque_odbc = odbc_exec($this->cnx,$sql_estoque_odbc);
            $ar_estoque_odbc = odbc_fetch_array($qry_estoque_odbc);
            /*
            $sql_qtde_aberto_odbc = 'SELECT SUM(t1."qt-pedida" - t1."qt-atendida") AS "qtidade-aberto"
                                     FROM pub."ped-item" t1
                                     INNER JOIN pub."ped-venda" t2 ON t1."nr-pedcli" = t2."nr-pedcli" AND t1."nome-abrev" = t2."nome-abrev"
                                     WHERE  t2."cod-estabel" = \''.$this->Estab.'\' AND
                                            (t2."cod-sit-ped" = \'1\' OR t2."cod-sit-ped" = \'2\') AND
                                            t1."it-codigo" = \''.$this->Item.'\'';
            $qry_qtde_aberto_odbc = odbc_exec($this->cnx,$sql_qtde_aberto_odbc);
            $ar_qtde_aberto_odbc = odbc_fetch_array($qry_qtde_aberto_odbc);

            $quantidade = $ar_estoque_odbc['qtidade-atu'] - $ar_qtde_aberto_odbc['qtidade-aberto'];
            */
            $this->qtidade_disp = $ar_estoque_odbc['qtidade_disp'];
            $quantidade = $ar_estoque_odbc['qtidade-atu'];
            return $quantidade;
        }else{
            if($this->Estab != ''){
                $stab = 'AND cod_estabel = \''.$this->Estab.'\'';
            }
            $sql_estoque = 'SELECT SUM(qtidade_atu) AS qtidade_atu
                             FROM is_saldo_estoq
                             WHERE it_codigo = \''.$this->Item.'\''.$stab;
            $qry_estoque = query($sql_estoque);
            $ar_estoque = farray($qry_estoque);

            $sql_qtde_aberto = 'SELECT SUM(qtde - qtde_faturada) AS qtidade_aberto
                                FROM is_pedido_item t1
                                    INNER JOIN is_pedido t2 ON t1.id_pedido = t2.numreg
                                WHERE  t2.id_estabelecimento = \''.$this->Estab.'\' AND
                                       (t2.id_situacao_pedido = \'1\' OR t2.id_situacao_pedido = \'2\') AND
                                       t1.id_produto = \''.$this->Item.'\'';
            $qry_qtde_aberto = query($sql_qtde_aberto);
            $ar_qtde_aberto = farray($qry_qtde_aberto);

            $quantidade = $ar_estoque['qtidade_atu'] - $ar_qtde_aberto['qtidade_aberto'];
            return $quantidade;
        }
    }

    public function getPedidosNaoFaturadosErp(){
        if(!$this->cnx){return;}
        if($this->TpConsulta == '2'){
            return array();
        }
        $sql_det_ped_ds = "SELECT  (t1.\"qt-pedida\" - t1.\"qt-alocada\") AS \"saldo\",
                                    t2.\"nome-abrev\",
                                    t2.\"dt-entrega\",
                                    t2.\"no-ab-reppri\"
                                FROM pub.\"ped-item\" t1 INNER JOIN pub.\"ped-venda\" t2 ON t1.\"nome-abrev\" = t2.\"nome-abrev\" AND t1.\"nr-pedcli\" = t2.\"nr-pedcli\"
                                WHERE t2.\"cod-sit-ped\" IN(1,2) AND t1.\"it-codigo\" = '".$this->Item."' AND (t1.\"qt-pedida\" - t1.\"qt-alocada\") > 0";
        $retorno = odbc_exec($this->cnx,$sql_det_ped_ds) or die(odbc_errormsg());
        $i = 0;
        while($rs_det_ped_ds = odbc_fetch_array($retorno)){
            $new_array[] = array(
                $rs_det_ped_ds['nome-abrev'],
                $rs_det_ped_ds['no-ab-reppri'],
                $rs_det_ped_ds['saldo'],
                $rs_det_ped_ds['dt-entrega']
            );
            $i++;
        }
        return $new_array;
    }

    public function getPedidosNaoFaturadosErpTotal(){
        if(!$this->cnx){return;}
        if($this->TpConsulta == '2'){
            return 0;
        }
        $sql_det_ped_ds = "SELECT
                                SUM(t1.\"qt-pedida\" - t1.\"qt-alocada\") AS \"saldo\"
                                FROM pub.\"ped-item\" t1 INNER JOIN pub.\"ped-venda\" t2 ON t1.\"nome-abrev\" = t2.\"nome-abrev\" AND t1.\"nr-pedcli\" = t2.\"nr-pedcli\"
                                WHERE t2.\"cod-sit-ped\" IN(1,2) AND t1.\"it-codigo\" = '".$this->Item."' AND (t1.\"qt-pedida\" - t1.\"qt-alocada\") > 0";
        $retorno = odbc_exec($this->cnx,$sql_det_ped_ds) or die(odbc_errormsg());

        $retorno = odbc_fetch_array($retorno);

        return $retorno['saldo'];
    }

    public function getPedidosNaoIntegrados(){
        $sql_det_ped_crm = "SELECT
                                    t1.dt_entrega,
                                    t1.id_pedido_cliente,
                                    t0.qtde,
                                    t3.fantasia_apelido,
                                    t4.nome_usuario
                            FROM
                                    is_pedido_item t0
                                    LEFT JOIN is_pedido t1 ON (t0.id_pedido = t1.numreg)
                                    LEFT JOIN is_pessoa t3 ON t3.numreg = t1.id_pessoa
                                    LEFT JOIN is_usuario t4 ON t4.numreg = t1.id_usuario_cad
                            WHERE
                                    t0.id_produto = '".$this->NumregItem."'
                                    AND t1.sn_exportado_erp = '0'
                                    AND t1.id_situacao_pedido IN (1)
                                    AND t1.dt_hr_exportado_erp IS NULL";

        $retorno = query($sql_det_ped_crm) or die(mysql_error());

        while($rs_det_ped_crm = farray($retorno)){
            $new_array[] = array(
                $rs_det_ped_crm['id_pedido_cliente'],
                $rs_det_ped_crm['fantasia_apelido'],
                $rs_det_ped_crm['qtde'],
                $rs_det_ped_crm['dt_entrega'],
                $rs_det_ped_crm['nome_usuario']
            );
        }
        return $new_array;
    }

    public function getPedidosNaoIntegradosTotal(){
        $sql_det_ped_crm = "SELECT
                                    sum(t0.qtde) as qtde
                            FROM
                                    is_pedido_item t0
                                    LEFT JOIN is_pedido t1 ON (t0.id_pedido = t1.numreg)
                                    LEFT JOIN is_pedido_representante t2 ON (t2.id_pedido = t1.numreg AND t2.sn_representante_principal = 1)
                                    LEFT JOIN is_pessoa t3 ON t3.numreg = t1.id_pessoa
                                    LEFT JOIN is_usuario t4 ON t4.numreg = t2.id_representante
                            WHERE
                                    t0.id_produto = '".$this->NumregItem."'
                                    AND t1.sn_exportado_erp = '0'
                                    AND t1.id_situacao_pedido IN (1)
                                    AND t1.dt_hr_exportado_erp IS NULL";
        $retorno = query($sql_det_ped_crm) or die(mysql_error());

        $rs_det_ped_crm = farray($retorno);
        return $rs_det_ped_crm['qtde'];
    }

    public function getArReferencia(){
        if($this->TpConsulta == '1'){
            if(!$this->cnx){return;}
            $SqlDeposito = "SELECT id_deposito FROM is_estabel_x_depos";
            $SqlDeposito .= ($this->Estab != '')?" WHERE id_estabelecimento = '".$this->Estab."'":'';
            $qry_estabelecimento = query($SqlDeposito);
            $ar_deposito = array();
            while($ar_estabelecimento = farray($qry_estabelecimento)){
                $ar_deposito[] = $ar_estabelecimento['id_deposito'];
            }

            $estab = farray(query('select * from is_estabelecimento where numreg =  \''.$this->Estab.'\''));

            $this->Estab = $estab['id_estabelecimento_erp'];

            if(count($ar_deposito) > 0){
                $in = '"cod-depos" IN (\''.implode("','",$ar_deposito).'\') AND';
            }
            if($this->Estab != ''){
                $stab = 'cod_estabel = \''.$this->Estab.'\' AND ';
            }
            $sql_estoque_odbc = 'SELECT "cod-refer"
			     FROM pub."saldo-estoq"
                             WHERE "it-codigo" = \''.$this->Item.'\' AND
                                   '.$in.$stab.'
                                   "qtidade-atu" > 0
                             GROUP BY "cod-estabel",
                                   "lote",
                                   "dt-vali-lote",
				   "cod-refer"
                             ORDER BY "dt-vali-lote" ASC';
            $qry_estoque_odbc = odbc_exec($this->cnx,$sql_estoque_odbc);
            $new_array = array();
            while($ar_estoque_odbc = odbc_fetch_array($qry_estoque_odbc)){
                if($ar_estoque_odbc['cod-refer'] != ''){
                    $new_array[$ar_estoque_odbc['cod-refer']] = $ar_estoque_odbc['cod-refer'];
                }
            }
            return $new_array;
        }
    }
}
?>