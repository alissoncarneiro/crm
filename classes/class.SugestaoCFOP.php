<?php
/*
 * class.SugestaoCFOP.php
 * Autor: Lucas
 * 23/11/2010 17:04:00
 * 
 * Classe responsável por sugerir a CFOP, podendo ser usada para o pedido todo e/ou item a item
 * A Classe deve receber um array de campos que será usado para montar o WHERE e o ORDER BY, sendo o último
 * podendo ser parametrizado a parte. O ORDER BY será montado com a ordem que estiver o array de campos.
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class SugestaoCFOP{
    private $Campos;
    private $CamposFixos;
    private $Ordem = NULL;
    private $Tabela;
    public $Retorno;
    public $Where;
    public $p1;
    public $p2;
    public $limit;
    public $top;
    
    public function  __construct(){
       if(TipoBancoDados =='mysql'){$this->limit = ' LIMIT 1 '; $this->p1='`';$this->p2='`';}
       if(TipoBancoDados =='mssql'){$this->top = ' TOP 1 '; $this->p1='[';$this->p2=']';}
       if(TipoBancoDados =='odbc'){$this->top = ''; $this->limit = ''; $this->p1='"';$this->p2='"';}
       $this->Tabela = 'is_param_cfop';
       //$this->Retorno = 'cfop_estadual';
       $this->Campos = array('uf'=>'SP','cidade'=>'São Bernardo do Campo');
       $this->CamposFixos = array('sn_ativo' => '=1','(\''.date('Y-m-d').'\'' =>' BETWEEN dthr_validade_ini AND dthr_validade_fim)');
       //$this->montaWhere();
       //$this->montaOrdem();
    }

    public function setRetornoCustom($RetornoCustom){
        if($RetornoCustom){
            $this->Retorno = $RetornoCustom;
        }
    }

    public function setTabelaCustom($TabelaCustom){
        $this->Tabela = $TabelaCustom;
    }

    public function setCamposCustom($CamposCustom){
        $this->Campos = $CamposCustom;
    }

    public function setOrdemCustom($OrdemCustom){
        $this->Ordem = $OrdemCustom;
    }

    public function montaWhere(){
        foreach($this->Campos as $k => $v){
            $where[] = '('.$this->p1.$k.$this->p2.' is NULL OR '.$this->p1.$k.$this->p2.' = \''.TrataApostrofoBD($v).'\')';
        }
        $this->Where = ' WHERE '.implode("\n".' AND ',$where);
    }

    public function montaSQL(){
        $sql = 'SELECT '.$this->top.' '.$p1.$this->Retorno.$p2.' FROM '.$this->p1.$this->Tabela.$this->p2.$this->Where.$this->Ordem.$this->limit;
        $ar_cfop = query($sql);
        $rs_cfop = farray($ar_cfop);
        
        if($_SESSION['debug'] === true){
            pre($sql);
            if($rs_cfop){
                echo 'Retornando CFOP '.$rs_cfop[$this->Retorno].'<br/>';
            }
        }
        
        return $this->Retorno = $rs_cfop[$this->Retorno];
    }

    public function montaOrdem(){
        if($this->Ordem==NULL || $this->Ordem==''){
            foreach($this->Campos as $k => $v){
                $ord[] = $this->p1.$k.$this->p2.' DESC';
            }
            $ord = implode(', ',$ord);
        } else {
            $ord = $this->Ordem;
        }
        $this->Ordem = ' ORDER BY '.$ord;
    }

    public function TrataCamposFixos(){
        foreach($this->CamposFixos as $k => $v){
            $we[] = $k.$v;
        }
        $where = implode(' AND ',$we);
        $this->Where .= ' AND '.$where;
    }

    public function getCFOP(){
        $this->montaWhere();
        $this->TrataCamposFixos();
        $this->montaOrdem();
        $this->montaSQL();

        return $this->Retorno;
    }

    public function setUFPais($PaisOrigem,$UFOrigem,$PaisDestino,$UFDestino){
        $PaisOrigem = strtoupper($PaisOrigem);
        $UFOrigem = strtoupper($UFOrigem);
        $PaisDestino = strtoupper($PaisDestino);
        $UFDestino = strtoupper($UFDestino);

        if($PaisOrigem != $PaisDestino){
            $ret = 'cfop_internacional';
        } else if($PaisOrigem != $PaisDestino && $UFOrigem != $UFDestino){
            $ret = 'cfop_internacional';
        } else if($PaisOrigem == $PaisDestino && $UFOrigem != $UFDestino){
            $ret = 'cfop_interestadual';
        } else if($PaisOrigem == $PaisDestino && $UFOrigem == $UFDestino){
            $ret = 'cfop_estadual';
        }

        if($this->Retorno != $ret && $this->Retorno != ''){
            return $this->Retorno;
        } else {
            return $this->Retorno = $ret;
        }

    }

}

//Exemplo de uso:
$ar_depara = array(
                //'sn_ativo'                    => '',
                //'dthr_validade_ini'           => '',
                //'dthr_validade_fim'           => '',
                'id_pessoa'                     => '',
                'id_pessoa_regiao'              => '',
                'pessoa_cidade'                 => '',
                'pessoa_uf'                     => '',
                'id_pessoa_canal_venda'         => '',
                'id_pessoa_grupo_cliente'       => '',
                'id_tp_pessoa'                  => '',
                'sn_contribuinte_icms'          => '',
                'id_produto'                    => '',
                'id_produto_familia'            => '',
                'id_produto_familia_comercial'  => '',
                'id_produto_grupo_estoque'      => '',
                'id_produto_linha'              => '',
                'id_pedido_estabelecimento'     => '',
                'id_pedido_repres_pri'          => '',
                'id_pedido_tab_preco'           => '',
                'id_pedido_tp_pedido'           => '',
                'id_pedido_tp_venda'            => '',
                'id_pedido_dest_merc'           => '',
                'id_pedido_moeda'               => '',
                //'cfop_estadual'                 => '',
                //'cfop_interestadual'            => '',
                //'cfop_internacional'            => '',
                //'pontos'                        => '',
                'id_tp_item'                    => '',
                'id_tp_cliente'                 => ''
    );
/*
include('../conecta.php');
include('../functions.php');
include('../funcoes.php');
$a = new SugestaoCFOP();
$a->setCamposCustom($ar_depara);
$a->setRetornoCustom('cfop_estadual');
//$a->setOrdemCustom('pontos DESC');
$a->setUFPais('55','SP','55','SP');
$a->getCFOP();
echo $a->Retorno;
*/
?>