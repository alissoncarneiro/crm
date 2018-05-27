<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');
$importa = new txtimport;

$importa->ProcuraArquivo('repres');
$importa->TabelaName('is_usuario');

$ar_chave = array('id_representante');

$importa->troca_valor_fixo = array('id_representante'=>'id_usuario');

$importa->SetArrayChaves($ar_chave);

$ar_campos = array(
                    'cod-rep' 	=> 'id_usuario',
                    'nome' 	=> 'nome_usuario',
                    'e-mail' 	=> 'email',
                    'nome-abrev' => 'nome_abreviado'
		);
$ar_default = array(
                    'hr_cadastro' 	=> date("H:i:s"),
                    'id_usuario_cad' 	=> 'IMPORT',
                    'dt_alteracao' 	=> date("Y-m-d"),
                    'hr_alteracao' 	=> date("H:i:s"),
                    'id_usuario_alt' 	=> 'IMPORT',
                    'id_perfil' 	=> '5',
                    'idioma' 		=> 'PT',
                    'senha' 		=> 'oasis'
		);
$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_pessoa');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>