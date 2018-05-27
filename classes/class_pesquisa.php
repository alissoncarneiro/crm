<?php
/*
 * c_coaching_envia_projeto_certificacao.php
 * Autor: Vitor
 * 09/10/2013 08:20:00
 */

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");
setlocale(LC_ALL, 'ptb', 'pt_BR', 'portuguese-brazil', 'bra', 'brazil', 'pt_BR.utf-8', 'pt_BR.iso-8859-1', 'br');

class AgendamentoPesquisa {
    
  
    // consulta ou gera senha
    public function geraSenha($idPessoa, $sn_contato){
        if($sn_contato == 1){
        	$tabela = 'is_contato';
        }else{
        	$tabela = 'is_pessoa';
        }
    	
        $sqlSenha = "select wcp_senha_padrao from $tabela where numreg = $idPessoa";
        $qrySenha = query($sqlSenha);
        $arSenha = farray($qrySenha);
        if($arSenha['wcp_senha_padrao'] != ''){
            return base64_decode($arSenha['wcp_senha_padrao']);
        }else{
            $senha = $this->criaSenha(6);
            $senha = base64_encode($senha);
            $sqlUpdate = "update $tabela set wcp_senha_padrao = '$senha' where numreg = $idPessoa";
            if($qrySenha = query($sqlUpdate)){
                return base64_decode($senha);
            }else{
                echo 'erro: 1 '.mysql_error();
            }
        }
    }
    
    // gera email
    public function geraEmail($id_modelo, $assunto, $emailPessoa, $id_script, $senhaProjeto, $nome_pessoa){
        $selectModeloEmail = "select textohtm_corpo from is_modelo_orcamento where numreg = $id_modelo";
        if($QrySelectModeloEmail = query($selectModeloEmail)){
            $arEmail = farray($QrySelectModeloEmail);
            $modelo_html = $arEmail['textohtm_corpo'];
            $modelo_html = str_replace("{VSPROJETO}",$assunto,$modelo_html);
            $modelo_html = str_replace("{VSLOGIN}","<a href='mailto:$emailPessoa' style='color:#000000'>".$emailPessoa."</a>",$modelo_html);
            $modelo_html = str_replace("VS_LOGIN","<a href='mailto:$emailPessoa' style='color:#000000'>".$emailPessoa."</a>",$modelo_html);
            $modelo_html = str_replace("VS_IDPESQUISA",$id_script,$modelo_html);
            $modelo_html = str_replace("{VSSENHA}",$senhaProjeto, $modelo_html);
            $modelo_html = str_replace("VS_SENHA",$senhaProjeto, $modelo_html);
            $modelo_html = str_replace("VS_NOME",$nome_pessoa, $modelo_html);
            $modelo_html = str_replace("VSNOME",$nome_pessoa, $modelo_html);
            return $modelo_html;
        }
    }
    
    // agenda email
    public function agendaEmail($id_pessoa,$dthr_email,$nomePessoa,$emailPessoa,$emailPessoal,$assunto,$modelo_html,$emailUsuario, $fk_coach){
        //$dthr_email = $dthr_email == '' ? date('Y-m-d H:i:s') : $dthr_email;
        $nomePessoa = addslashes($nomePessoa);
        $assunto = addslashes($assunto);
        $modelo_html = addslashes(utf8_encode($modelo_html));

        $insertEmail = "
                        INSERT INTO `is_email_pessoa` (
                            `id_pessoa`,
                            `dthr_email`,
                            `nome_contato`,
                            `email_contato`,
                            `email_cc`,
                            `email_assunto`,
                            `email_corpo`,
                            `email_remetente`,
                            `id_usuario_resp`,
                            `wcp_sn_envia`,
                            `wcp_num_tentativas`
                        )
                        VALUES (
                            '$id_pessoa',
                            '$dthr_email',
                            '$nomePessoa',
                            '$emailPessoa',
                            '$emailPessoal',
                            '$assunto',
                            '$modelo_html',
                            '$emailUsuario',
                            '$fk_coach',
                            '0',
                            '0')
        ";
        return mysql_query($insertEmail);
    }
    
    
    public function criaSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false){
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';

        $caracteres .= $lmin;
        if ($maiusculas) $caracteres .= $lmai;
        if ($numeros) $caracteres .= $num;
        if ($simbolos) $caracteres .= $simb;

        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) {
                $rand = mt_rand(1, $len);
                $retorno .= $caracteres[$rand-1];
        }
        return $retorno;
    }
        
}

?>
