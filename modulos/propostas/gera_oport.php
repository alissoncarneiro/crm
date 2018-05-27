<?
  @header("Content-Type: text/html;  charset=ISO-8859-1",true);
  @session_start();

  require_once("../../conecta.php");
  require_once("../../funcoes.php");

  $pnumreg = $_GET["pnumreg"];
  $id_usuario = $_SESSION["id_usuario"];

  $qry_atend = farray(query("select * from is_atividades where numreg = '".$pnumreg."'"));

  $qry_opor = farray(query("select * from is_atividades where id_atividade_pai = '".$qry_atend["id_atividade"]."'"));

  if (empty($qry_opor["id_atividade"])) {
  	
	  $qry_max_opor = farray(query("select max(numreg) as ultimo from is_atividades"));
	  $sql = "INSERT INTO is_atividades ( id_atividade, id_pessoa , id_pessoa_contato, id_usuario_resp, id_atividade_pai, id_origem, id_tp_atividade, id_situacao, id_fase, id_tab_preco, assunto, dt_prev_fim, dt_inicio, dt_cadastro , hr_cadastro , id_usuario_cad , dt_alteracao , hr_alteracao , id_usuario_alt ) values ( '".
                                        (($qry_max_opor["ultimo"]*1)+1)."','".$qry_atend["id_pessoa"]."','".$qry_atend["id_pessoa_contato"]."','".$qry_atend["id_usuario_resp"]."','".$qry_atend["id_atividade"]."','".$qry_atend["id_origem"]."','OPOR','P','1','1','Oportunidade de Venda','".date("Y-m-d")."','".date("Y-m-d")."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."')";

	  query( $sql );

      $tot_opor = 0;
	  $sql_atend_iten = query("select * from is_ativ_prod where id_atividade = '".$qry_atend["id_atividade"]."'");
	  while ( $qry_atend_iten = farray($sql_atend_iten) ) {
          $preco = farray(query("select * from is_tab_preco_valor where id_lista_preco = '1' and id_produto = '".$qry_atend_iten["id_produto"]."'"));
          $vl_unit = $preco["valor_unitario"]*1;
          $vl_tot = $vl_unit*$qry_atend_iten["qtde"];
          $tot_opor += $vl_tot;
		  $sql_item = "INSERT INTO is_opor_itens  ( id_atividade , id_produto , id_modalidade, qtde , id_tab_preco, pct_desc , valor , valor_total , dt_cadastro , hr_cadastro , id_usuario_cad , dt_alteracao , hr_alteracao , id_usuario_alt ) values ( '".(($qry_max_opor["ultimo"]*1)+1)."','".$qry_atend_iten["id_produto"]."','".$qry_atend_iten["id_modalidade"]."','".$qry_atend_iten["qtde"]."','1','0','".$vl_unit."','".$vl_tot."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."','".date("Y-m-d")."','".date("H:i:s")."','".$id_usuario."')";
		  query( $sql_item );
	  }
	  
	  query("update is_atividades set receita_prev = '".$tot_opor."' where id_atividade = '".(($qry_max_opor["ultimo"]*1)+1)."'");

      echo '<script language="Javascript"> ';
      echo "window.alert('Oportunidade ".(($qry_max_opor["ultimo"]*1)+1)." gerada com sucesso !'); ";
      echo ' window.setTimeout( "'."window.close()".'", 100);';
      echo '</script>';



  } else {

      echo '<script language="Javascript"> ';
      echo "window.alert('Já existe oportunidade ".$qry_opor["id_proposta"]." gerada para este atendimento !'); ";
      echo ' window.setTimeout( "'."window.close()".'", 100);';
      echo '</script>';

  }



?>

