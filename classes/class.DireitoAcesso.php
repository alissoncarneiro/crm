<?php

/*
 * class.DireitoAcesso.php
 * Autor: Alex
 * 23/09/2010 14:09:00
 * Classe responsável por tratar os pedidos e orçamentos
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 * 2011-01-11 <Eduardo> <tratamento da classe para acessar a tabela is_usuario e campo numreg>
 */

class DireitoAcesso{

    static function getAcessoDados($usuario='',$tipo=1,$retorno=1,$clausula='AND',$clausula_campo=''){
        if($usuario == 'admin'){
            return '';
        }
        $array_out = array();
        $ar_rep = array();
        $qry_usuario = query("SELECT numreg,id_representante FROM is_usuario WHERE numreg = '".$usuario."'");
        $ar_usuario = farray($qry_usuario);

        if($tipo == 1 && $ar_usuario['numreg'] != ''){
            $array_out[] = $ar_usuario['numreg'];
        } elseif($tipo == 2 && $ar_usuario['id_representante'] != ''){
            $array_out[] = $ar_usuario['id_representante'];
        } elseif($tipo == 3 && $ar_usuario['nome_abreviado'] != ''){
            $array_out[] = $ar_usuario['nome_abreviado'];
        }


        $qry = query("SELECT t2.numreg,t2.id_representante FROM is_usuarios_acesso_usuarios t1 INNER JOIN is_usuario t2 ON t1.id_usuario = t2.numreg WHERE t1.id_usuario_mestre = '".$usuario."'");
        $numrows = numrows($qry);
        while($ar = farray($qry)){
            if($ar['numreg'] == 'admin'){
                return '';
            } else{
                if($tipo == 1 && $ar['numreg'] != ''){
                    $array_out[] = $ar['numreg'];
                } elseif($tipo == 2 && $ar['id_representante'] != ''){
                    $array_out[] = $ar['id_representante'];
                } elseif($tipo == 3 && $ar['nome_abreviado'] != ''){
                    $array_out[] = $ar['nome_abreviado'];
                }
            }
        }
        if($retorno == 1){
            if($ar_usuario['numreg'] == ''){
                return $clausula." 1 = 2 ";
            }
            $str_out = '';
            if(count($array_out) >= 1){
                $str_out = $clausula.' '.$clausula_campo." IN('".implode("','",$array_out)."') ";
            } else{
                if($tipo == 1 && $ar_usuario['numreg'] != ''){
                    $str_out = $clausula.' '.$clausula_campo." = '".$ar_usuario['numreg']."' ";
                } elseif($tipo == 2 && $ar_usuario['id_representante'] != ''){
                    $str_out = $clausula.' '.$clausula_campo." = '".$ar_usuario['id_representante']."' ";
                } elseif($tipo == 3 && $ar_usuario['nome_abreviado'] != ''){
                    $str_out = $clausula.' '.$clausula_campo." = '".$ar_usuario['nome_abreviado']."' ";
                } else{
                    return $clausula." 1 = 2 ";
                }
            }
            return $str_out;
        } elseif($retorno == 2){
            return $array_out;
        }
    }

}

?>