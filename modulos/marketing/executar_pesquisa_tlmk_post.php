<?php

@session_start();
require_once("../../conecta.php");
require_once("../../functions.php");
#echo "<pre>"; print_r($_POST);exit;
foreach ($_POST as $k3 => $v3) {
    if ($k3 == "chk" || $k3 == "memo" || $k3 == "rb") {
        foreach ($v3 as $k => $v) {
            $opcoes = $valores = "";
            if ($k3 == "chk") {
                foreach ($v as $k2 => $v2) {
                    $opcoes = $opcoes . "," . $k2;
                    $valores = $valores . "," . $v2;
                }
            }
            $val = substr($valores, 1);
            if (substr($valores, 1) == "") {
                $val = $v;
            }
            //echo "Tipo: " . $k3 . " | Pergunta: " . $k . " | Respostas: " . substr($opcoes, 1) . " -> " . $val . "<br>";
            $values = explode("--", $val);
            $id_resp = $values[0];
            $n_pontos = 0;
            if ($k3 == "chk") {
                $nome_resp = $val;
                $opcoes = substr($opcoes, 1, strlen($opcoes) - 1);
                $ids_pontos = str_replace(',', "','", $opcoes);
                $a_pontos = farray(query("SELECT sum(pontos) as tot from is_script_resposta WHERE numreg in ('" . $ids_pontos . "')"));
                $n_pontos = $a_pontos["tot"] * 1;
            } else {
                $nome_resp = $values[1];
                if ($k3 == "rb") {
                    $a_pontos = farray(query("SELECT pontos from is_script_resposta WHERE numreg = '" . $id_resp . "'"));
                    $n_pontos = $a_pontos["pontos"] * 1;
                }
            }
            if ($id_resp != "") {
                $val = $nome_resp;
            }
            if (substr($opcoes, 1) == "") {
                $opcoes = substr($opcoes, 1);
                $opcoes = $id_resp;
            }
            if ($k3 == "memo") {
                $val = $v;
                $opcoes = "";
            }
            $perguntas = farray(query("SELECT * from is_script_pergunta WHERE numreg = '" . $k . "'"));

            $a_existe = farray(query("SELECT numreg from is_script_realizado WHERE id_atividade = '" . $_POST['id_atividade'] . "' and id_script = '" . $_POST['id_script'] . "' and id_pergunta = '" . $k . "'"));
            if ($a_existe["numreg"] * 1 > 0) {
                $sql = "UPDATE is_script_realizado SET
			id_resposta = '" . $opcoes . "',
			nome_resposta = '" . $val . "',
                        pontos = '" . $n_pontos . "' WHERE id_pergunta = '" . $k . "' and id_script = '" . $_POST['id_script'] . "' AND id_atividade = '" . $_POST['id_atividade'] . "'";
            } else {
                $sql = "INSERT INTO is_script_realizado 
                    (id_atividade,id_pessoa,data,id_script,nome_pesquisa,id_pergunta,nome_pergunta,id_resposta,nome_resposta,id_usuario_resp,pontos)
                    VALUES 
                    ('" . $_POST['id_atividade'] . "','" . $_POST['id_pessoa'] . "','" . $_POST['data_pesquisa'] . "','" . $_POST['id_script'] . "','" . $_POST['nome_script'] . "','" . $k . "','" . $perguntas['pergunta'] . "','" . $opcoes . "','" . $val . "','" . $_SESSION['id_usuario'] . "','" . $n_pontos . "')";
            }
            //echo $sql;
            if ($_POST['acao'] == 's') {
                query($sql);
            } else {
                echo mysql_error($sql);
            }
        }
    }
}
$a_tot_pontos = farray(query("select sum(pontos) as tot from is_script_realizado where id_atividade = '".$_POST['id_atividade']."'"));
query("UPDATE is_atividade SET script_pontos = ".($a_tot_pontos["tot"]*1)." WHERE numreg='" . $_POST['id_atividade'] . "'");

echo "<script>window.opener.document.getElementById('edtscript_pontos').value='".str_replace(".",",",$a_tot_pontos["tot"]*1)."';</script>";
echo "<script>alert('Pesquisa realizada com sucesso!');</script>";
echo "<script>window.close();</script>";
?>