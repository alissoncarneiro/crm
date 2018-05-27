<?php
/*
 * class.Contato.php
 * Autor: Bruno C Fonseca
 * 22/12/2010 16:22:00
 * Classe responsável por tratar os contatos
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class Contato {
    private $IdContato;
    private $ArrayDadosContato = array();

    public function __construct($IdContato){
        if(empty($IdContato)){
            $this->IdContato = 0;
        } else {
            $this->IdContato = $IdContato;
        }
        $SqlContato = "SELECT * FROM is_contato WHERE numreg = ".$this->IdContato;
        $QryContato = query($SqlContato);
        if(numrows($QryContato) == 1){
            $ArUsuario = farray($QryContato);
            $this->ArrayDadosContato = $ArUsuario;
        }/* else { ERRO COMENTADO POIS NÃO SEI SE CONTATO É OBRIGATÓRIO NO ORÇAMENTO/PEDIDO
            echo 'Contato não encontrado!';
        }*/
    }

    public function getNome(){
        return $this->ArrayDadosContato['nome'];
    }

    public function getTel1(){
        return $this->ArrayDadosContato['tel1'];
    }

    public function getTel2(){
        return $this->ArrayDadosContato['tel2'];
    }

    public function getTel3(){
        return $this->ArrayDadosContato['tel3'];
    }

    public function getEmail(){
        return $this->ArrayDadosContato['email_profissional'];
    }

    public function getDadosContato($CampoTabelaContato){
        return $this->ArrayDadosContato[$CampoTabelaContato];
    }
}
?>