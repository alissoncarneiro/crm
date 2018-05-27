<?php
/*
 * gera_end_is_contato_hist_prof.php
 * Autor: Alex
 * 05/11/2010 09:34
 * - Arquivo respons�vel para tratar o cadastro de hist�rico profissional no cadastro de contatos
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
if($id_funcao == 'is_contato_hist_prof'){?>
    <script language="javascript" src="js/jquery.meio.mask.min.js"></script>
    <script language="javascript">
    $(document).ready(function(){
        $('#edttel1').setMask({mask:'(99)9999-9999',defaultValue:'',
            onInvalid:function(c,nKey){
                alert('Permitido apenas números!');
                return false;
            }
        });
    });
</script>
<?php
}
?>