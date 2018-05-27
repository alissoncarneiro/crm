<?php
/*
 * class.Contato.php
 * Autor: Bruno C Fonseca
 * 22/12/2010 16:22:00
 * Classe respons�vel por tratar os contatos
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
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
        }/* else { ERRO COMENTADO POIS N�O SEI SE CONTATO � OBRIGAT�RIO NO OR�AMENTO/PEDIDO
            echo 'Contato n�o encontrado!';
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