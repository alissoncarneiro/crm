<?php
/*
 * gera_end_contato.php
 * Autor: Alex
 * 05/11/2010 09:31
 * - Arquivo respons�vel para tratar o cadastro de contatos
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
if($id_funcao == 'contato'){?>
    <script language="javascript" src="js/jquery.meio.mask.min.js"></script>
    <script language="javascript">
    $(document).ready(function(){
        $('#edttel1').setMask({mask:'(99)9999-9999',defaultValue:'',
            onInvalid:function(c,nKey){
                alert('Permitido apenas números!');
                return false;
            }
        });
        $('#edttel2').setMask({mask:'(99)9999-9999',defaultValue:'',
            onInvalid:function(c,nKey){
                alert('Permitido apenas números!');
                return false;
            }
        });
        $('#edttel3').setMask({mask:'(99)9999-9999',defaultValue:'',
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