<?php

/*

 * Tratamento para cadastro de prospect e clientes

 * Tratamento de duplicidade do campo de cnpj e Transformando prospect em cliente

 */


if(($id_funcao == 'pessoa') && $opc != 'excluir'){
    if($opc == 'incluir'){
        $_POST['edtdt_cadastro'] = date("d/m/Y");
        if($_POST['pgetcustom'] == 'suspect'){
            $_POST['edtsn_cliente'] = 0;
            $_POST['edtsn_prospect'] = 0;
            $_POST['edtsn_suspect'] = 1;
            $_POST['edtsn_consumidor_final'] = 0;
        }elseif($_POST['pgetcustom'] == 'prospect'){
           $_POST['edtsn_cliente'] = 0;
            $_POST['edtsn_prospect'] = 1;
            $_POST['edtsn_suspect'] = 0;
            $_POST['edtsn_consumidor_final'] = 0;
        }elseif($_POST['pgetcustom'] == 'cliente'){
            $_POST['edtsn_contato'] = 0;
            $_POST['edtsn_cliente'] = 1;
            $_POST['edtsn_prospect'] = 0;
            $_POST['edtsn_suspect'] = 0;
            $_POST['edtsn_consumidor_final'] = 0;
            $_POST['edtsn_importado_erp'] = 0;
            $_POST['edtsn_exportado_erp'] = 0;
        }elseif($_POST['pgetcustom'] == 'consumidor_final'){
          $_POST['edtsn_consumidor_final'] = 1;
            $_POST['edtsn_cliente'] = 0;
            $_POST['edtsn_prospect'] = 0;
            $_POST['edtsn_suspect'] = 0;
        }
    }

    /*

     * Se o campo empresa contato foi preenchido, fixo que o registro é um contato

     */

    if($_POST['edtid_empresa_contato'] != ''){

        $_POST['edtsn_contato'] = 1;

    }



    $Pessoa = new Pessoa((($opc == 'alterar') ? $pnumreg : false));



    foreach($_POST as $k => $v){

        if(substr($k,0,3) == 'edt'){

            $Pessoa->setArDados(substr($k,3,strlen($k) - 3),$v);

        }

    }

    /* Se não for um cliente estrangeiro valida CNPJ/CPF/IE */

    if($_POST['edtsn_estrangeiro'] != '1'){

        if(trim($_POST['edtcnpj_cpf']) != '' || $_POST['ptpec'] == 1){

            /*

             * Validando CNPJ/CPF

             */

            $Check1 = $Pessoa->CheckCNPJCPF();

            if($Check1[0] === true){

                /*

                 * Validando se está duplicado

                 */

                $Check2 = $Pessoa->CheckDuplicidadeCNPJCPF();

                if($Check2[0] === true){

                    $Url->AlteraParam('ppostback',$numreg_postback);

                    $url_retorno = $Url->getUrl();

                    echo "<script language=\"javascript\">alert('".$Check2[1]."'); window.location.href = '".$url_retorno."';</script>";

                    exit;

                }

            }else{

                $Url->AlteraParam('ppostback',$numreg_postback);

                $url_retorno = $Url->getUrl();

                echo "<script language=\"javascript\">alert('".$Check1[1]."'); window.location.href = '".$url_retorno."';</script>";

                exit;

            }

        }

		/* Validação de email desenvolvido por SBCoaching */

        if(trim($_POST['edtemail']) != '' || $_POST['ptpec'] == 1){

			$Check3 = $Pessoa->CheckDuplicidadeEmail();

			if($Check3[0] === true){	

                $Url->AlteraParam('ppostback',$numreg_postback);

                $url_retorno = $Url->getUrl();

                echo "<script language=\"javascript\">alert('".$Check3[1]."'); window.history.back(-1);</script>";

				exit;	

			}

		}

		/* Validação de telefone desenvolvido por SBCoaching */		

        if(trim($_POST['edttel1']) != '' || trim($_POST['edttel2']) != '' || trim($_POST['edtwcp_tel3']) != '' || trim($_POST['edtfax']) != '' || $_POST['ptpec'] == 1){

			$arTelefone = array($_POST['edttel1'],$_POST['edttel2'],$_POST['edtwcp_tel3'],$_POST['edtfax']);

			$Check4 = $Pessoa->CheckDuplicidadeTelefone($arTelefone);

			if($Check4[0] === true){	

                $Url->AlteraParam('ppostback',$numreg_postback);

                $url_retorno = $Url->getUrl();

                echo "<script language=\"javascript\">alert('".$Check4[1]."'); window.history.back(-1);</script>";

				exit;	

			}

		}

        if(trim($_POST['edtie_rg']) != '' || $_POST['ptpec'] == 1){

            /*

             * Validando Inscrição Estadual

             */

            $CheckIE = $Pessoa->CheckIE();

            if($CheckIE[0] === false){

                $Url->AlteraParam('ppostback',$numreg_postback);

                $url_retorno = $Url->getUrl();

                echo "<script language=\"javascript\">alert('".$CheckIE[1]."'); window.location.href = '".$url_retorno."';</script>";

                exit;

            }

        }

    }

    if(trim($_POST['edtfantasia_apelido']) != '' || $_POST['ptpec'] == 1){

        /*

        * Validando se está duplicado

        */

        $Check2 = $Pessoa->CheckDuplicidadeNomeFantasia();

        if($Check2[0] === true){

           $Url->AlteraParam('ppostback',$numreg_postback);

           $url_retorno = $Url->getUrl();

            echo "<script language=\"javascript\">alert('".$Check2[1]."'); window.location.href = '".$url_retorno."';</script>";

            exit;

        }

    }



    /*

     * Se for transformar prospect em cliente

     */

    if($_POST['ptpec'] == 1){

        /* Validando campos obrigatórios */

        $Check1 = $Pessoa->CheckDadosProspectParaCliente();

        if($Check1[0] === false){

            $Url->AlteraParam('ppostback',$numreg_postback);

            $url_retorno = $Url->getUrl();

            echo "<script language=\"javascript\">alert('".$Check1[1]."'); window.location.href = '".$url_retorno."';</script>";

            exit;

        }

    }

    if($_POST['ptsep'] == 1){

        /* Validando campos obrigatórios */

        $Check1 = $Pessoa->CheckDadosSuspectParaProspect();

        if($Check1[0] === false){

            $Url->AlteraParam('ppostback',$numreg_postback);

            $url_retorno = $Url->getUrl();

            echo "<script language=\"javascript\">alert('".$Check1[1]."'); window.location.href = '".$url_retorno."';</script>";

            exit;

        }

    }

}

?>