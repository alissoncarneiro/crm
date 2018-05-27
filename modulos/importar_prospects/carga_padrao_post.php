<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: </title>
        <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../../estilos_css/cadastro.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="../../estilos_css/calendar-blue.css" title="win2k-cold-1" />
        <style type="text/css">
            <!--
            body {
                margin-left: 0px;
                margin-top: 0px;
                margin-right: 0px;
                margin-bottom: 0px;
            }
            -->
        </style>
    </head>
    <body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
        <center>
            <div id="principal_detalhes">
                <div id="topo_detalhes">
                    <div id="logo_empresa"></div>
                    <!--logo -->
                </div><!--topo -->
                <div id="conteudo_detalhes">
                    <span class="tit_detalhes">Carga de Dados</span><br/><br/>
                    <?
                    @session_start();
                    @header("Content-Type: text/html;  charset=ISO-8859-1",true);
                    @header ("Pragma: no-cache");
                    @header("Cache-Control: no-store, no-cache, must-revalidate");
                    @header("Cache-Control: post-check=0, pre-check=0", false);

                    include "../../conecta.php";
                    include "../../funcoes.php";

                    /* ----------------------------------------------------------------------------
                    RECEBE E COPIA O ARQUIVO
                    -----------------------------------------------------------------------------*/

                    $temp = $_FILES['edtarq']["tmp_name"];
                    $nome_arquivo = $_FILES['edtarq']["name"];
                    $size = $_FILES['edtarq']["size"];
                    $type = $_FILES['edtarq']["type"];

                    if ($nome_arquivo) {
                        copy($temp,$caminho_arquivos."carga.csv");
                    }

                    $conteiner = file($caminho_arquivos.'carga.csv');
                    $file = count($conteiner);

                    $outros = trim($_POST["edtoutros"]);

                    if ($outros) {
                        $nome_tabela = $outros;
                    } else {
                        $nome_tabela = $_POST["edttabela"];
                    }

                    $id_usuario = $_SESSION["id_usuario"];

                    $registro = '';
                    $f = -1;
                    $contador = 0;
                    //mysql_query("TRUNCATE TABLE ".$nome_tabela."");
                    foreach($conteiner as $k => $v) {
                        $conteudos = array();
                        $colunas = explode(";",$v);
                        if ($k==0) {
                            $campos = "";
                            foreach($colunas as $k1 => $v1) {
                                $campos[$k1] = trim($v1);
                            }
                        } else {
                            // senao deve montar os conteúdos
                            $conteudos = array();
                            foreach($colunas as $k1 => $v1) {
                                if (trim($v1)=="") {
                                    $conteudos[$campos[$k1]] = "NULL";
                                } else {
                                    $conteudos[$campos[$k1]] = "'".RemoveAcentos(str_replace("'","´",trim($v1)))."'";
                                }
                            }

                            $conteudos['cnpj_cpf'] = str_replace("-","",$conteudos['cnpj_cpf']);
                            $conteudos['cnpj_cpf'] = str_replace("/","",$conteudos['cnpj_cpf']);
                            $conteudos['cnpj_cpf'] = str_replace(".","",$conteudos['cnpj_cpf']);
                            if(mysql_num_rows(mysql_query("SELECT cnpj_cpf FROM is_pessoas WHERE cnpj_cpf = ".$conteudos['cnpj_cpf'].""))>0) {
                                $id_pessoa = mysql_fetch_array(mysql_query("SELECT id_pessoa FROM is_pessoas WHERE cnpj_cpf = ".$conteudos['cnpj_cpf'].""));
                                $id_colaborador = mysql_fetch_array(mysql_query("SELECT * FROM is_usuarios WHERE nome_usuario LIKE '%".str_replace("'","",$conteudos['atendente'])."%'"));

                                $aniver1 = explode("/",$conteudos['datan_1']);
                                $aniver2 = explode("/",$conteudos['datan_2']);
                                $relac = 9;

                                $new_id = mysql_fetch_array(mysql_query("SELECT id_pessoa FROM is_pessoas ORDER BY id_pessoa DESC"));

                                $new_id['id_pessoa'] = $new_id['id_pessoa']+1;

                                mysql_query("INSERT INTO is_pessoas (id_pessoa,id_relac,razao_social_nome,id_cargo,telcom,email_prof,telres,fax,dianascto,mesnascto,anonascto,`dt_cadastro`,`hr_cadastro`,`id_usuario_cad`,`dt_alteracao`,`hr_alteracao`,`id_usuario_alt`)
                VALUES('".$new_id['id_pessoa']."','".$relac."','".$conteudos['contato_1']."','".$conteudos['cargo_1']."','".$conteudos['tel_1']."',
                  '".$conteudos['e_mail1']."','".$conteudos['tel_1_2']."','".$conteudos['fax_1']."','".$aniver1[0]."','".$aniver1[1]."','".$aniver1[2]."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario.")'");

                                mysql_query("INSERT INTO is_pessoas (id_pessoa,id_relac,razao_social_nome,id_cargo,telcom,email_prof,telres,fax,dianascto,mesnascto,anonascto,`dt_cadastro`,`hr_cadastro`,`id_usuario_cad`,`dt_alteracao`,`hr_alteracao`,`id_usuario_alt`)
                VALUES('".($new_id['id_pessoa']+1)."','".$relac."','".$conteudos['contato_2']."','".$conteudos['cargo_2']."','".$conteudos['tel_2']."',
                  '".$conteudos['e_mail2']."','".$conteudos['tel_2_2']."','".$conteudos['fax_2']."','".$aniver2[0]."','".$aniver2[1]."','".$aniver2[2]."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario.")'");

                                mysql_query("INSERT INTO is_relac_colaborador (id_pessoa,id_colaborador,`dt_cadastro`,`hr_cadastro`,`id_usuario_cad`,`dt_alteracao`,`hr_alteracao`,`id_usuario_alt`) VALUES ('".$new_id['id_pessoa']."','".$id_colaborador['id_usuario']."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario.")'");

                                mysql_query("INSERT INTO is_relac_colaborador (id_pessoa,id_colaborador,`dt_cadastro`,`hr_cadastro`,`id_usuario_cad`,`dt_alteracao`,`hr_alteracao`,`id_usuario_alt`) VALUES ('".($new_id['id_pessoa']+1)."','".$id_colaborador['id_usuario']."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario.")'");

                            } else {
                                $id_colaborador = mysql_fetch_array(mysql_query("SELECT * FROM is_usuarios WHERE nome_usuario LIKE '%".str_replace("'","",$conteudos['atendente'])."%'"));

                                $aniver1 = explode("/",$conteudos['datan_1']);
                                $aniver2 = explode("/",$conteudos['datan_2']);
                                $relac = 4;

                                $new_id = mysql_fetch_array(mysql_query("SELECT id_pessoa FROM is_pessoas ORDER BY id_pessoa DESC"));
                                mysql_query("INSERT INTO is_pessoas
                               (id_pessoa,id_relac,razao_social_nome,nome_abreviado,cnpj_cpf,ie_rg,endereco, numero, complemento, bairro, cidade, uf, cep, sicode,`dt_cadastro`,`hr_cadastro`,`id_usuario_cad`,`dt_alteracao`,`hr_alteracao`,`id_usuario_alt`))
              VALUES('".($new_id['id_pessoa']+1)."','".$relac."','".$conteudos['razao_social_nome']."','".$conteudos['nome_abreviado']."','".$conteudos['cnpj_cpf']."',
                  '".$conteudos['ie_rg']."','".$conteudos['endereco']."','".$conteudos['numero']."','".$conteudos['complemento']."','".$conteudos['bairro']."','".$conteudos['cidade']."','".$conteudos['uf']."','".$conteudos['cep']."','".$conteudos['sicode']."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario.")'");

                                $new_id['id_pessoa'] = $new_id['id_pessoa']+1;

                                mysql_query("INSERT INTO is_pessoas (id_pessoa,id_relac,razao_social_nome,id_cargo,telcom,email_prof,telres,fax,dianascto,mesnascto,anonascto,`dt_cadastro`,`hr_cadastro`,`id_usuario_cad`,`dt_alteracao`,`hr_alteracao`,`id_usuario_alt`))
                VALUES('".($new_id['id_pessoa']+1)."','".$relac."','".$conteudos['contato_1']."','".$conteudos['cargo_1']."','".$conteudos['tel_1']."',
                  '".$conteudos['e_mail1']."','".$conteudos['tel_1_2']."','".$conteudos['fax_1']."','".$aniver1[0]."','".$aniver1[1]."','".$aniver1[2]."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario.")'");

                                mysql_query("INSERT INTO is_pessoas (id_pessoa,id_relac,razao_social_nome,id_cargo,telcom,email_prof,telres,fax,dianascto,mesnascto,anonascto,`dt_cadastro`,`hr_cadastro`,`id_usuario_cad`,`dt_alteracao`,`hr_alteracao`,`id_usuario_alt`))
                VALUES('".($new_id['id_pessoa']+2)."','".$relac."','".$conteudos['contato_2']."','".$conteudos['cargo_2']."','".$conteudos['tel_2']."',
                  '".$conteudos['e_mail2']."','".$conteudos['tel_2_2']."','".$conteudos['fax_2']."','".$aniver2[0]."','".$aniver2[1]."','".$aniver2[2]."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario.")'");

                                mysql_query("INSERT INTO is_relac_colaborador (id_pessoa,id_colaborador,`dt_cadastro`,`hr_cadastro`,`id_usuario_cad`,`dt_alteracao`,`hr_alteracao`,`id_usuario_alt`) VALUES ('".($new_id['id_pessoa']+1)."','".$id_colaborador['id_usuario']."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario.")'");

                                mysql_query("INSERT INTO is_relac_colaborador (id_pessoa,id_colaborador,`dt_cadastro`,`hr_cadastro`,`id_usuario_cad`,`dt_alteracao`,`hr_alteracao`,`id_usuario_alt`) VALUES ('".($new_id['id_pessoa']+2)."','".$id_colaborador['id_usuario']."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario.")'");

                            }
                        }
                    }

                    /* ----------------------------------------------------------------------------
                    ************************         FIM              *****************************
                    -----------------------------------------------------------------------------*/
                    echo "<br>Total de Registros Importados : ".$contador."<br>";
                    ?>
                    <br/>
                    <input type="button" value="Fechar"  class="botao_form"  onclick="javascript:window.close();"/>
                    </body>
                    </html>