<?php
/*
 * class.Arquivo.php
 * Autor: Alex
 * 08/12/2011 15:39:37
 */
class Arquivo extends RegistroOasis{
   
    public function __construct($Numreg = NULL){
        $this->NomeTabela = 'is_arquivo';
        parent::__construct($Numreg);
    }
    
    public function MoveCaminhoDefinitivo($ArquivoTemporario,$NomeArquivo){
        $CaminhoArquivos = CaminhoArquivosUpload;
        return move_uploaded_file($ArquivoTemporario, $CaminhoArquivos.$NomeArquivo);
    }
}
?>