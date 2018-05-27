<?php

echo "<table border=\"0\" width=\"100%\">\r\n\t<tr>\r\n\t\t<td bgcolor=\"#EEF2FB\" colspan=\"15\"><b>\r\n\t\t<font face=\"Verdana\" size=\"1\">Cadastro de Usuários</font></b></td>\r\n   \t</tr>\r\n\t<tr>\r\n\t\t<td width=\"50\"><font face=\"Verdana\" size=\"1\">Busca :\r\n\t\t</font></td>\r\n\t\t<td width=\"94\">";
echo "<s";
echo "elect size=\"1\" name=\"D1\">\r\n\t\t<option selected value=\"id_usuario\">Id.Usuário</option>\r\n\t\t<option value=\"nome_usuario\">Nome do Usuário</option>\r\n\t\t</select></td>\r\n\t\t<td width=\"159\"><input type=\"text\" name=\"edtbusca\" size=\"20\"></td>\r\n\t\t<td width=\"58\">\r\n\t\t<input type=\"button\" value=\"Filtrar\" name=\"btnfiltrar\"></td>\r\n\t\t<td width=\"395\">\r\n\t\t<input type=\"button\" value=\"+ Incluir\" name=\"btnincluir\"></td>\r\n\t\t<td width=\"30\"";
echo ">&nbsp;</td>\r\n\t</tr>\r\n\t<tr>\r\n\t\t<td colspan=\"6\" height=\"100%\">\r\n\t\t<table border=\"0\" width=\"100%\">\r\n\t\t\t<tr>\r\n\t\t\t\t<td bgcolor=\"#EEF2FB\"><b>\r\n\t\t\t\t<font face=\"Verdana\" size=\"1\">Id.Usuário</font></b></td>\r\n\t\t\t\t<td bgcolor=\"#EEF2FB\"><b>\r\n\t\t\t\t<font face=\"Verdana\" size=\"1\">Nome do Usuário</font></b></td>\r\n\t\t\t\t<td bgcolor=\"#EEF2FB\"><b>\r\n\t\t\t\t<font face=\"Verdana\" size=\"1\">e-mail</font></b></td>\r\n\t\t\t\t<td bgcolor=\"#EEF2FB\"><b>\r\n\t\t\t\t<font";
echo " face=\"Verdana\" size=\"1\">Perfil</font></b></td>\r\n\t\t\t\t<td bgcolor=\"#EEF2FB\"><b>\r\n\t\t\t\t<font face=\"Verdana\" size=\"1\">Dt.Cadastro</font></b></td>\r\n\t\t\t\t<td bgcolor=\"#EEF2FB\"><b>\r\n\t\t\t\t<font face=\"Verdana\" size=\"1\">Excluir</font></b></td>\r\n\t\t\t</tr>\r\n \t    ";
require_once( "../../conecta.php" );
$sql_cadastro = query( "select * from is_usuarios" );
while ( $qry_cadastro = farray( $sql_cadastro ) )
{
    echo "<tr>";
    echo "<td><font face=\"Verdana\" size=\"1\">".$qry_cadastro['id_usuario']."</font></td>";
    echo "<td><font face=\"Verdana\" size=\"1\">".$qry_cadastro['nome_usuario']."</font></td>";
    echo "<td><font face=\"Verdana\" size=\"1\">".$qry_cadastro['email']."</font></td>";
    echo "<td><font face=\"Verdana\" size=\"1\">".$qry_cadastro['id_perfil']."</font></td>";
    echo "<td><font face=\"Verdana\" size=\"1\">".$qry_cadastro['dt_cadastro']."</font></td>";
    echo "<td>";
    echo "<input type=\"button\" value=\"Excluir\" name=\"B5\"></td>";
    echo "</tr>";
}
echo "\r\n\t\t</table>\r\n\t</td>\r\n</tr>\r\n</table>\r\n\t\t\t\t\r\n";
?>
