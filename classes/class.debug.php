<?php
/*
 * class.Vendas.php
 * Autor: Alex
 * 18/10/2010 15:02:00
 * 
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class PHPDebug{

    private $str_buffer;
    public $buffer = false;
    public $write = true;
    public $writeType = 'file';

    public function createFile($file_name){
        $this->file = $file_name;
        $ponteiro = fopen($file_name,"w");
        $this->fp = $ponteiro;
    }

    public function useExistentFile($file_name){
        if(file_exists($file_name)){
            $this->file = $file_name;
            $ponteiro = fopen($file_name,"a+");
            $this->fp = $ponteiro;
        } else{
            echo 'Arquivo <strong><em>'.$file_name.'</em></strong> não encontrado.';
        }
    }

    public function w($string,$breakLine=true){
        if($this->write == true){
            $break = ($breakLine == true)?"\r\n":'<br/>';
            if($this->buffer == false){
                if($this->writeType == 'file'){
                    fwrite($this->fp,$string.$break);
                } else{
                    echo $string.$break;
                }
            } else{
                $this->str_buffer .= $string.$break;
            }
        }
    }

    public function write(){
        if($this->write == true){
            if($this->writeType == 'file'){
                fwrite($this->fp,$this->str_buffer);
                $this->str_buffer = NULL;
            } else{
                echo $this->str_buffer;
                $this->str_buffer = NULL;
            }
        }
    }

    public function sep($qtde){
        $newString = '';
        for($i = 0; $i <= $qtde; $i++){
            $newString .= '=';
        }
        $this->w($newString);
    }

}

?>