<?php
@session_start();
require_once("../../conecta.php");
require_once("../../functions.php");

$a_tlmk = farray(query("SELECT * FROM is_atividade where numreg = '" . $_GET['pnumreg'] . "'"));
$a_script = farray(query("SELECT * FROM is_script where numreg = '" . $a_tlmk['id_script'] . "'"));
$a_pessoa = farray(query("SELECT * FROM is_pessoa where numreg = '" . $a_tlmk['id_pessoa'] . "'"));
$a_usuario = farray(query("SELECT * FROM is_usuario where numreg = '" . $a_tlmk['id_usuario_resp'] . "'"));

//data e usuario responsavel
$data    = $info_cad['data'];
$usuario = $a_usuario['nome_usuario'];

if(empty ($info_cad['data'])){
    $data = date('Y-m-d');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: Telemarketing - Scripts de Pesquisa</title>
        <link href="css_pesquisa/css_pesquisa.css" rel="stylesheet" type="text/css" />
        <link href="../../js/function.js" type="text/javascript" />
    </head>
    <body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
        <div id="wrap">
            
            <div id="conteudo_pesquisa">
                <form id="form1" name="form1" method="post" action="executar_pesquisa_tlmk_post.php">
                    <?php
                    if($a_pessoa['status'] == "2"){
                        echo "<input name=\"acao\" id=\"acao\" type=\"hidden\" value=\"u\" />";
                    }else{
                        echo "<input name=\"acao\" id=\"acao\" type=\"hidden\" value=\"s\" />";
                    }
                    ?>
                    <div class="info_cliente">
                        <fieldset class="info">
                            <legend><?php echo $a_script['nome_script']; ?></legend>
                            <!-- img src="img/topo.png" / -->
                            <ul style="list-style: none;">
                                <li class="li_id">Cliente:</li>
                                &nbsp;&nbsp;<li class="li_dados"><?php echo $a_pessoa['razao_social_nome']; ?>
                                    <input name="id_pessoa" type="hidden" value="<?php echo $a_tlmk['numreg']; ?>" />
                                    <input name="id_atividade" type="hidden" value="<?php echo $a_tlmk['numreg']; ?>" />
                                    <input name="id_script" type="hidden" value="<?php echo $a_tlmk['id_script']; ?>" />
                                    <input name="data_pesquisa" type="hidden" value="<?php echo empty($a_tlmk['dt_real_fim'])?$data:$a_tlmk['dt_real_fim']; ?>" />
                                    <input name="id_operador" type="hidden" value="<?php echo $a_tlmk['id_usuario_resp']; ?>" />
                                    <input name="nome_script" type="hidden" value="<?php echo $a_script['nome_script']; ?>" />
                                    <input name="pnumreg_sr" type="hidden" value="<?php echo $_GET['pnumreg']; ?>" />
                                </li><br />
                                <li class="li_id">Telefone:</li>
                                <li class="li_dados">
                                    <?php echo $a_pessoa['tel1']." - ".$a_pessoa['fax']; ?>
                                </li><br />
                                <li class="li_id">Data:</li>
                                &nbsp;&nbsp;&nbsp;&nbsp;<li class="li_dados">
                                    <?php echo dten2br($data); ?>
                                </li><br />
                                <!-- li class="li_id">Cod CLiente:</li>
                                <li class="li_dados">
                                    <?php #echo $a_pessoa['id_pessoa']; ?>
                                </li><br / -->
                                <li class="li_id">Operador</li>
                                &nbsp;<li class="li_dados">
                                    <?php #echo $a_usuario['nome_usuario']; ?>
                                    <?php echo $usuario; ?>
                                </li>
                            </ul>
                        </fieldset>
                    </div>
                    <div class="info_contudo">
                        <fieldset>
                            <legend>Perguntas</legend>
                            <?php
                            $i = 1;
                            $ini = 0;
                            $q_pergunta = query("SELECT * FROM is_script_pergunta  WHERE id_script = '".$a_tlmk['id_script']."' ORDER by ordem");

                            while($a_pergunta = farray($q_pergunta)){

                                $respostas = farray(query("SELECT * FROM is_script_realizado WHERE id_atividade = '".$_GET['pnumreg']."' and id_pergunta = '".$a_pergunta["numreg"]."'"));
                                echo "<ul>";
                                echo "<li class='li_pergunta'>".$a_pergunta['pergunta']."</li>";
                                echo "</ul>";
                                $q_resposta = query("SELECT * FROM is_script_resposta WHERE id_pergunta = '".$a_pergunta['numreg']."' ORDER by ordem");
                                $cont = 1;
                                while($a_resposta = farray($q_resposta)){
                                    echo "<ul class='ul_resp'>";
                                    if($a_pergunta['id_tipo'] == 2 || $a_pergunta['id_tipo'] == 3){
                                        $n_pos = strpos($respostas['id_resposta'].',',$a_resposta['numreg'].',');
                                        if($n_pos !== false){
                                            $checked = 'checked';
                                        }else{
                                            $checked = '';
                                        }
                                        echo '<li class="li_resp"><div align="left"><input type="checkbox" '.$checked.' name="chk['.$a_pergunta['numreg'].']['.$a_resposta['numreg'].']" id="chk['.$a_pergunta['numreg'].']['.$a_resposta['numreg'].']" value="'.$a_resposta['resposta'].'"/>';
                                        echo $a_resposta['resposta'].'</div>
                                                  </li>';
                                        $cont++;
                                    }else if($a_pergunta['id_tipo'] == 4 || $a_pergunta['id_tipo'] == 5){
                                        if($respostas['id_resposta'] == $a_resposta['numreg']){
                                            $checked = 'checked';
                                        }else{
                                            $checked = '';
                                        }
                                        echo '<li class="li_resp"><div align="left"><input '.$checked.' type="radio" '.$check.' name="rb['.$a_pergunta['numreg'].']" id="rb['.$a_pergunta['numreg'].']" value="'.$a_resposta['numreg']."--".$a_resposta['resposta'].'" />';
                                        echo $a_resposta['resposta'].'</div>
                                                  </li>';
                                    }else{
                                        echo '<li class="li_textarea"><div align="center">'.$a_resposta['resposta'];
                                        echo '<textarea cols="45" rows="5" name="memo['.$a_pergunta['numreg'].']" id="memo['.$a_pergunta['numreg'].']">'.$result['nome_resposta'].$respostas['nome_resposta'].'</textarea></div>
                                                  </li>';
                                    }
                                    echo "</ul>";
                                }
                                echo "<div class='linha'></div>";
                                $i++;
                                if(($i % 2) != 0){
                                    echo "</ul>";
                                }
                            }
                            ?>
                        </fieldset>
                    </div>
                    <div class="bt_salvar"><input type="submit" name="button" id="button" value="Salvar Pesquisa" /></div>
                </form>
            </div>
        </div>
    </body>
</html>