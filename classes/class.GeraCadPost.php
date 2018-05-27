<?php
/*
 * class.geraCadPost.php
 * Autor: Alex
 * 24/09/2010 21:16:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class geraCadPost{
    private $NumregPostBack;

    public function getNumregPostBack(){
        return $this->NumregPostBack;
    }

    public function backupPost($POST,$IdPostBack=''){
        $ArrayPostBack = array();
        foreach($POST as $k => $v){
            if(substr($k,0,3) == 'edt'){
                $ArrayPostBack[$k] = stripslashes($v);
            }
        }
        $ArInsert['dthr'] = date("Y-m-d");
        $ArInsert['valido_ate'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." + 10 minutes"));
        $ArInsert['post'] = str_replace("'","''",serialize($ArrayPostBack));

        if($IdPostBack == ''){
            $SqlInsert = AutoExecuteSql(getParametrosGerais('TipoBancoDados'),'is_postback',$ArInsert,'INSERT');
            

            $QryPostback = iquery($SqlInsert);
            if(!$QryPostback){
                return false;
            }
            $this->NumregPostBack = $QryPostback;
            return $QryPostback;
        }
        else{
            $ArInsert['numreg'] = $IdPostBack;
            $SqlInsert = AutoExecuteSql(getParametrosGerais('TipoBancoDados'),'is_postback',$ArInsert,'UPDATE',array('numreg'));
            $QryPostback = query($SqlInsert);
            return $IdPostBack;
        }
    }

    public function DoJsPostBack($ObjUrl){
        $ObjUrl->AlteraParam('ppostback',$this->getNumregPostBack());
        $UrlRetorno = $ObjUrl->getUrl();
        echo windowlocationhref($UrlRetorno);
    }

}

?>