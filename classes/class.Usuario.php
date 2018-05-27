<?php
/*
 * class.Usuario.php
 * Autor: Alex
 * 18/10/2010 15:02:00
 * Classe responsável por tratar os usuários
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class Usuario {
    private $IdUsuario;
    private $ArrayDadosUsuario = array();
    
    public function __construct($IdUsuario){
        $this->IdUsuario = $IdUsuario;
        $QryUsuario = query("SELECT * FROM is_usuario WHERE numreg = ".$this->IdUsuario);
        if(numrows($QryUsuario) == 1){
            $ArUsuario = farray($QryUsuario);
            foreach($ArUsuario as $k => $v){
                if(!is_numeric($k)){
                    if($k == 'senha'){
                        continue;
                    }
                    $this->ArrayDadosUsuario[$k] = $v;
                }
            }
        }
        else{
            echo getError('0030010001',getParametrosGerais('RetornoErro'));
        }
    }

    public function getPermissao($IdPermissao){
        if($this->ArrayDadosUsuario[$IdPermissao] == 1){
            return true;
        }
        else{
            return false;
        }
    }

    public function getNome(){
        return $this->ArrayDadosUsuario['nome_usuario'];
    }

    public function getNomeAbreviado(){
        return $this->ArrayDadosUsuario['nome_abreviado'];
    }

    public function getIdUsuarioGestor(){
        return $this->ArrayDadosUsuario['id_usuario_gestor'];
    }

    public function getIdRepresentante(){
        return $this->ArrayDadosUsuario['id_representante'];
    }
    
    public function getEmail(){
        return $this->ArrayDadosUsuario['email'];
    }

    public function getDadosUsuario($IdCampo = NULL){
        if($IdCampo === NULL){
            return $this->ArrayDadosUsuario;
        }
        if($IdCampo == 'senha'){
            return '*****';
        }
        return $this->ArrayDadosUsuario[$IdCampo];
    }
}
?>