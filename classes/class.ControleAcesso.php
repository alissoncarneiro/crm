<?php

/*
 * class.ControleAcesso.php
 * Autor: Alex
 * 14/04/2011 11:30:00
 *
 * Log de Altera??es
 * yyyy-mm-dd <Pessoa respons?vel> <Descri??o das altera??es>
 */

class ControleAcesso{
    private $IdUsuario;
    private $IdCadastro;
    private $ArrayAcessoUsuarios;
    private $TipoBD;
    private $Sep = array();

    /**
     *
     * @param type $IdUsuario
     * @param type $IdCadastro 
     */
    public function  __construct($IdUsuario,$IdCadastro) {
        $this->IdUsuario    = $IdUsuario;
        $this->IdCadastro   = $IdCadastro;
        $this->TipoBD       = TipoBancoDados;
        $this->PreencheArrayAcessoUsuarios();
    }

    public function getIdCadastro(){
        return $this->IdCadastro;
    }

    public function getIdUsuario(){
        return $this->IdUsuario;
    }

    private function PreencheSep(){
        switch($this->TipoBD){
            case 'mysql' :
                $this->Sep[0] = '`';
                $this->Sep[1] = '`';
                $this->Sep[2] = "'";
                $this->Sep[3] = "'";
                break;
            case 'mssql' :
                $this->Sep[0] = '[';
                $this->Sep[1] = ']';
                $this->Sep[2] = "'";
                $this->Sep[3] = "'";
                break;
            case 'progress' :
                $this->Sep[0] = '"';
                $this->Sep[1] = '"';
                $this->Sep[2] = "'";
                $this->Sep[3] = "'";
                if($Schema == ''){$Schema = 'pub';}
                break;
            default :
                die;
        }
    }

    public function AplicaFiltroBloqueio(){

        if($this->IdUsuario == 1){
            return false;
        }
        return ($_SESSION['sn_bloquear_leitura'] == '1');
    }

    public function PreencheArrayAcessoUsuarios(){
        $this->ArrayAcessoUsuarios = array();
        $this->ArrayAcessoUsuarios[$this->IdUsuario] = $this->IdUsuario;
        $SqlAcessoUsuarios = "SELECT t2.numreg
                                        FROM is_usuarios_acesso_usuarios t1
                                        INNER JOIN is_usuario t2 ON t1.id_usuario = t2.numreg
                                        WHERE t1.id_usuario_mestre = '".$this->IdUsuario."'";
        $QryAcessoUsuarios = query($SqlAcessoUsuarios);
        while($ArAcessoUsuarios = farray($QryAcessoUsuarios)){
            $this->ArrayAcessoUsuarios[$ArAcessoUsuarios['numreg']] = $ArAcessoUsuarios['numreg'];
            $SqlAcessoUsuarios2Nivel = "SELECT t2.numreg
                                        FROM is_usuarios_acesso_usuarios t1
                                        INNER JOIN is_usuario t2 ON t1.id_usuario = t2.numreg
                                        WHERE t1.id_usuario_mestre = '".$ArAcessoUsuarios['numreg']."'";
            $QryAcessoUsuarios2Nivel = query($SqlAcessoUsuarios2Nivel);
            while($ArAcessoUsuarios2Nivel = farray($QryAcessoUsuarios2Nivel)){
                $this->ArrayAcessoUsuarios[$ArAcessoUsuarios2Nivel['numreg']] = $ArAcessoUsuarios2Nivel['numreg'];
            }
        }
    }

    public function GeraSqlBloqueio($Campo,$Clausula='AND'){

        if(count($this->ArrayAcessoUsuarios) == 0){
            $Sql = ' '.$Clausula.' 1=1 ';
            return $Sql;
        }
        switch($this->IdCadastro){
            case 'pedido':
                return $this->GeraSqlBloqueioPedido($Campo,$Clausula);
                break;
            case 'orcamento':
                return $this->GeraSqlBloqueioOrcamento($Campo,$Clausula);
                break;
            case 'atividades_cad_lista':
                return $this->GeraSqlBloqueioAtividade($Campo, $Clausula);
                break;
            case 'televendas_cad':
                return $this->GeraSqlBloqueioAtividade($Campo, $Clausula);
                break;
            case 'visitas_cad_lista':
                return $this->GeraSqlBloqueioAtividade($Campo, $Clausula);
                break;
            case 'assist_cad_chamados':
                return $this->GeraSqlBloqueioAtividade($Campo, $Clausula);
                break;

            case 'telemarketing_cad':
                return $this->GeraSqlBloqueioAtividade($Campo, $Clausula);
                break;
            case 'cobrancas_cad':
                return $this->GeraSqlBloqueioAtividade($Campo, $Clausula);
                break;
            case 'pessoa':
                return $this->GeraSqlBloqueioPessoa($Campo, $Clausula);
                break;
            case 'contato':
                return $this->GeraSqlBloqueioPadrao($Campo, $Clausula);
                break;
            case 'oportunidade':
                return $this->GeraSqlBloqueioPadrao($Campo, $Clausula);
                break;
            case 'arquivos_cad':
                return $this->GeraSqlBloqueioPadrao($Campo, $Clausula);
                break;
            default:
                return $this->GeraSqlBloqueioPadrao($Campo, $Clausula);
                break;
        }
    }
    
    private function GeraSqlBloqueioPadrao($Campo,$Clausula){
        $Sep = $this->Sep;
        
        $Sql = ' '.$Clausula.' '.$Sep[0].$Campo.$Sep[1].' IN('.implode(',',$this->ArrayAcessoUsuarios).')';
        return $Sql;
    }
    
    private function GeraSqlBloqueioPedido($Campo,$Clausula){
        $Sep = $this->Sep;
        $Sql = ' '.$Clausula.' '.$Sep[0].$Campo.$Sep[1].' IN('.implode(',',$this->ArrayAcessoUsuarios).')';
        return $Sql;
    }

    private function GeraSqlBloqueioOrcamento($Campo,$Clausula){
        $Sep = $this->Sep;
        $Sql = ' '.$Clausula.' '.$Sep[0].$Campo.$Sep[1].' IN('.implode(',',$this->ArrayAcessoUsuarios).')';
        return $Sql;
    }

    private function GeraSqlBloqueioAtividade($Campo,$Clausula){
        $Sep = $this->Sep;
        $Sql = ' '.$Clausula.' '.$Sep[0].$Campo.$Sep[1].' IN('.implode(',',$this->ArrayAcessoUsuarios).')';
        return $Sql;
    }
    
    private function GeraSqlBloqueioPessoa($Campo,$Clausula){
        $Sep = $this->Sep;
        $Sql = ' '.$Clausula.' '.$Sep[0].$Campo.$Sep[1].' IN('.implode(',',$this->ArrayAcessoUsuarios).')';


        return $Sql;
    }
}
?>