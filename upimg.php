<?php
	foreach ($_FILES['imagem'] as $chave => $valor) {
		$img[$chave] = $valor;
	}


	if (( ( ( ( $img['type'] != 'image/gif' && $img['type'] != 'image/pjpeg' ) && $img['type'] != 'image/jpeg' ) && $img['type'] != 'image/jpg' ) && $img['type'] != 'application/x-shockwave-flash' )) {
		echo $img['type'];
		echo '<script> alert(\'ERRO: Favor enviar apenas imagens GIf, JPG ou arquivos flash SWF.\');</script>';
	}else {
		if (358400 < $img['size']) {
			echo $img['size'];
			echo '<script> alert(\'ERRO: A imagem não pode ter mais do que 350Kb\');</script>';
		}else {
			$root = str_replace( 'upimg.php', '', $_SERVER['SCRIPT_FILENAME'] );
			$imag = $img['name'];
			$i = 1;

			while (file_exists( '' . $root . '/../img/upload/' . $imag )) {
				$exten = substr( $img['name'], 0 - 4 );
				$imag = str_replace( $exten, '(' . $i . ')', $img['name'] );
				$imag = $imag . $exten;
				++$i;
			}

			copy( $img['tmp_name'], '' . $root . '/img/upload/' . $imag );
			$end = str_replace( 'upimg.php', 'img/upload/' . $imag, $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] );
			echo '<script>
   	   		 	top.document.getElementById(\'src\').value = \'http://' . $end . '\';
   	   		 	top.showPreviewImage(top.document.getElementById(\'src\').value);
   	   		 </script>';
		}
	}
?>
<script type="text/javascript">
   top.document.upimg.reset();
</script>
