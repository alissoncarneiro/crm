<?php

// Função que valida e-mail
function validaemail($email="") {
    if (eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $email)) {
        $valida = "1";
    } else {
        $valida = "0";
    }
    return $valida;
}

function soma_dias($numdias) {
    $dataret = mktime(0, 0, 0, date("m"), date("d") + $numdias, date("Y"));
    return gmdate("Y-m-d", $dataret);
}

function soma_dias_data($num_dias,$data) {
    $partes = explode("-", $data);
    $dataret = mktime(0, 0, 0, $partes[1], ($partes[2]*1) + ($numdias*1), $partes[0]);
    return gmdate("Y-m-d", $dataret);
}

function soma_meses_data($num_meses,$data) {
    $partes = explode("-", $data);
    $dataret = mktime(0, 0, 0, ($partes[1]*1) + ($num_meses*1), $partes[2], $partes[0]);
    return gmdate("Y-m-d", $dataret);
}

function soma_dias_ut($data1, $periodo, $formato_bd) {

    if ($data1) {
        $q_feriados = query("select * from is_feriado");
        $cFeriados = '';
        while ($a_feriados = farray($q_feriados)) {
            $cFeriados .= $a_feriados["dia_mes"] . ',';
        }

        if ($formato_bd == '1') {
            $partes = explode("-", $data1);
            $dt = mktime(0, 0, 0, $partes[1], $partes[2], $partes[0]);
        } else {
            $partes = explode("/", $data1);
            $dt = mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
        }

        while ($periodo > 0) {

            $dt = mktime(0, 0, 0, date("m", $dt), date("d", $dt) + 1, date("Y", $dt));

            $dw = date("N", $dt);
            $nFeriado = strpos($cFeriados, (gmdate("d/m", $dt)));
            if ($nFeriado === false) {
                $snFeriado = 'N';
            } else {
                $snFeriado = 'S';
            }
            if (($dw != 6) && ($dw != 7) && ($snFeriado == 'N')) {
                $periodo = $periodo - 1;
            }
        }
        return date("Y-m-d", $dt);
    }
}

function UTF8toiso8859_11($string) {

    if (!ereg("[\241-\377]", $string))
        return $string;

    $UTF8 = array(
        "\xe0\xb8\x81" => "\xa1",
        "\xe0\xb8\x82" => "\xa2",
        "\xe0\xb8\x83" => "\xa3",
        "\xe0\xb8\x84" => "\xa4",
        "\xe0\xb8\x85" => "\xa5",
        "\xe0\xb8\x86" => "\xa6",
        "\xe0\xb8\x87" => "\xa7",
        "\xe0\xb8\x88" => "\xa8",
        "\xe0\xb8\x89" => "\xa9",
        "\xe0\xb8\x8a" => "\xaa",
        "\xe0\xb8\x8b" => "\xab",
        "\xe0\xb8\x8c" => "\xac",
        "\xe0\xb8\x8d" => "\xad",
        "\xe0\xb8\x8e" => "\xae",
        "\xe0\xb8\x8f" => "\xaf",
        "\xe0\xb8\x90" => "\xb0",
        "\xe0\xb8\x91" => "\xb1",
        "\xe0\xb8\x92" => "\xb2",
        "\xe0\xb8\x93" => "\xb3",
        "\xe0\xb8\x94" => "\xb4",
        "\xe0\xb8\x95" => "\xb5",
        "\xe0\xb8\x96" => "\xb6",
        "\xe0\xb8\x97" => "\xb7",
        "\xe0\xb8\x98" => "\xb8",
        "\xe0\xb8\x99" => "\xb9",
        "\xe0\xb8\x9a" => "\xba",
        "\xe0\xb8\x9b" => "\xbb",
        "\xe0\xb8\x9c" => "\xbc",
        "\xe0\xb8\x9d" => "\xbd",
        "\xe0\xb8\x9e" => "\xbe",
        "\xe0\xb8\x9f" => "\xbf",
        "\xe0\xb8\xa0" => "\xc0",
        "\xe0\xb8\xa1" => "\xc1",
        "\xe0\xb8\xa2" => "\xc2",
        "\xe0\xb8\xa3" => "\xc3",
        "\xe0\xb8\xa4" => "\xc4",
        "\xe0\xb8\xa5" => "\xc5",
        "\xe0\xb8\xa6" => "\xc6",
        "\xe0\xb8\xa7" => "\xc7",
        "\xe0\xb8\xa8" => "\xc8",
        "\xe0\xb8\xa9" => "\xc9",
        "\xe0\xb8\xaa" => "\xca",
        "\xe0\xb8\xab" => "\xcb",
        "\xe0\xb8\xac" => "\xcc",
        "\xe0\xb8\xad" => "\xcd",
        "\xe0\xb8\xae" => "\xce",
        "\xe0\xb8\xaf" => "\xcf",
        "\xe0\xb8\xb0" => "\xd0",
        "\xe0\xb8\xb1" => "\xd1",
        "\xe0\xb8\xb2" => "\xd2",
        "\xe0\xb8\xb3" => "\xd3",
        "\xe0\xb8\xb4" => "\xd4",
        "\xe0\xb8\xb5" => "\xd5",
        "\xe0\xb8\xb6" => "\xd6",
        "\xe0\xb8\xb7" => "\xd7",
        "\xe0\xb8\xb8" => "\xd8",
        "\xe0\xb8\xb9" => "\xd9",
        "\xe0\xb8\xba" => "\xda",
        "\xe0\xb8\xbf" => "\xdf",
        "\xe0\xb9\x80" => "\xe0",
        "\xe0\xb9\x81" => "\xe1",
        "\xe0\xb9\x82" => "\xe2",
        "\xe0\xb9\x83" => "\xe3",
        "\xe0\xb9\x84" => "\xe4",
        "\xe0\xb9\x85" => "\xe5",
        "\xe0\xb9\x86" => "\xe6",
        "\xe0\xb9\x87" => "\xe7",
        "\xe0\xb9\x88" => "\xe8",
        "\xe0\xb9\x89" => "\xe9",
        "\xe0\xb9\x8a" => "\xea",
        "\xe0\xb9\x8b" => "\xeb",
        "\xe0\xb9\x8c" => "\xec",
        "\xe0\xb9\x8d" => "\xed",
        "\xe0\xb9\x8e" => "\xee",
        "\xe0\xb9\x8f" => "\xef",
        "\xe0\xb9\x90" => "\xf0",
        "\xe0\xb9\x91" => "\xf1",
        "\xe0\xb9\x92" => "\xf2",
        "\xe0\xb9\x93" => "\xf3",
        "\xe0\xb9\x94" => "\xf4",
        "\xe0\xb9\x95" => "\xf5",
        "\xe0\xb9\x96" => "\xf6",
        "\xe0\xb9\x97" => "\xf7",
        "\xe0\xb9\x98" => "\xf8",
        "\xe0\xb9\x99" => "\xf9",
        "\xe0\xb9\x9a" => "\xfa",
        "\xe0\xb9\x9b" => "\xfb",
    );

    $string = strtr($string, $UTF8);
    return $string;
}

// Função para adicionar acentos
function AdicionaAcentos($texto) {
    $a = array(
        'Ã¡', 'Ã ', 'Ã¤', 'Ã£',
        'Ãª', 'Ã¨', 'Ã©', 'Ã«',
        'Ã®', 'Ã¬', 'Ã­', 'Ã¯',
        'Ã´', 'Ãµ', 'Ã²', 'Ã³', 'Ã¶',
        'Ã»', 'Ãº', 'Ã¹', 'Ã¼',
        'Ã§'
    );
    $acentos_html = array(
        'á', 'à', 'ä', 'ã',
        'ê', 'è', 'é', 'ë',
        'î', 'ì', 'í', 'ï',
        'ô', 'õ', 'ò', 'ó', 'ö',
        'û', 'ú', 'ù', 'ü',
        'ç'
    );
    return str_replace($a, $acentos_html, $texto);
}

function tirarAcentos($string){
    return preg_replace(
            array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(Ç)/","/(ç)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A C c e E i I o O u U n N"),$string);
}
// Função para remover acentos
function RemoveAcentos($Msg) {
    $a = array(
        '/[ÂÀÁÄÃ]/' => 'A',
        '/[âãàáä]/' => 'a',
        '/[ÊÈÉË]/' => 'E',
        '/[êèéë]/' => 'e',
        '/[ÎÍÌÏ]/' => 'I',
        '/[îíìï]/' => 'i',
        '/[ÔÕÒÓÖ]/' => 'O',
        '/[ôõòóö]/' => 'o',
        '/[ÛÙÚÜ]/' => 'U',
        '/[ûúùü]/' => 'u',
        '/ç/' => 'c',
        '/Ç/' => 'C');

    // Tira o acento pela chave do array
    return preg_replace(array_keys($a), array_values($a), $Msg);
}

// dife_data("10-09-2003","10-11-2003");
function dife_data($data1, $data2) {
    //Listando dentro das variavies a data corrente.
    list($mes1, $dia1, $ano1) = explode("-", $data1);
    //Listando dentro das variavies a data corrente.
    list($mes2, $dia2, $ano2) = explode("-", $data2);

    //Jogando data em formato UNIX...
    $d_ini = mktime(0, 0, 0, $mes1, $dia1, $ano1);
    $d_fim = mktime(0, 0, 0, $mes2, $dia2, $ano2);

    //Subtrai data para ver a diferenda entre DIAS...
    $dias_dife = ($d_fim - $d_ini) / 86400;

    //Pegando numeros inteiros...
    $dias_dife = ceil($dias_dife);

    return $dias_dife;
}

function SomaMinutosUteis($cTime, $cData, $nMin) {

    if (empty($cData)) {
        return $cTime;
        exit;
    }

    $aMin = array();
    $aMin[1][1] = 0 * 60;
    $aMin[1][2] = 0 * 60;
    $aMin[1][3] = 0 * 60;
    $aMin[1][4] = 0 * 60;
    $aMin[2][1] = 8 * 60;
    $aMin[2][2] = 12 * 60;
    $aMin[2][3] = 13 * 60;
    $aMin[2][4] = 17 * 60;
    $aMin[3][1] = 8 * 60;
    $aMin[3][2] = 12 * 60;
    $aMin[3][3] = 13 * 60;
    $aMin[3][4] = 17 * 60;
    $aMin[4][1] = 8 * 60;
    $aMin[4][2] = 12 * 60;
    $aMin[4][3] = 13 * 60;
    $aMin[4][4] = 17 * 60;
    $aMin[5][1] = 8 * 60;
    $aMin[5][2] = 12 * 60;
    $aMin[5][3] = 13 * 60;
    $aMin[5][4] = 17 * 60;
    $aMin[6][1] = 8 * 60;
    $aMin[6][2] = 12 * 60;
    $aMin[6][3] = 13 * 60;
    $aMin[6][4] = 17 * 60;
    $aMin[7][1] = 0 * 60;
    $aMin[7][2] = 0 * 60;
    $aMin[7][3] = 0 * 60;
    $aMin[7][4] = 0 * 60;
    $aMin[8][1] = 0 * 60;
    $aMin[8][2] = 0 * 60;
    $aMin[8][3] = 0 * 60;
    $aMin[8][4] = 0 * 60;


    $dData = mktime(0, 0, 0, substr($cData, 5, 2), substr($cData, 8, 2), substr($cData, 0, 4));

    $nDia = gmdate("w", $dData) + 1;
    $nTime = (substr($cTime, 0, 2) * 60) + (substr($cTime, 3, 2));

    $lExit = 'N';

    $q_feriados = query("select * from is_feriado");
    $cFeriados = '';
    while ($a_feriados = farray($q_feriados)) {
        $cFeriados .= $a_feriados["dia_mes"] . ',';
    }

    while (($nMin > 0) && ($lExit == 'N')) {
        if ($nTime < $aMin[$nDia][1]) {
            $nTime = $aMin[$nDia][1];
        }
        if ($nTime > $aMin[$nDia][4]) {
            $nTime = $aMin[$nDia][4];
        }
        if (($nTime > $aMin[$nDia][2]) && ($nTime < $aMin[$nDia][3])) {
            $nTime = $aMin[$nDia][3];
        }

        $nFeriado = strpos($cFeriados, (gmdate("d/m", $dData)));
        if ($nFeriado === false) {
            $snFeriado = 'N';
        } else {
            $snFeriado = 'S';
        }

        if ($snFeriado == 'S') {
            if ($aMin[$nDia][1] == 0) {
                $dData = mktime(0, 0, 0, gmdate("m", $dData), gmdate("d", $dData) + 1, gmdate("Y", $dData));
                $nDia = gmdate("w", $dData) + 1;
            } else
                $nDia = 8;
        } else {
            if ($aMin[$nDia][1] == 0) {
                $dData = mktime(0, 0, 0, gmdate("m", $dData), gmdate("d", $dData) + 1, gmdate("Y", $dData));
                $nDia = gmdate("w", $dData) + 1;
            } else {
                if (($nTime >= $$aMin[$nDia][3]) && ($nTime <= $aMin[$nDia][4])) {
                    if (($nTime + $nMin) > $aMin[$nDia][4]) {
                        $dData = mktime(0, 0, 0, gmdate("m", $dData), gmdate("d", $dData) + 1, gmdate("Y", $dData));
                        $nMin = ($nTime + $nMin) - $aMin[$nDia][4];
                        $nTime = $aMin[$nDia][1];
                        $nDia = gmdate("w", $dData) + 1;
                    } else {
                        $nMin = $nMin + $nTime;
                        $lExit = 'S';
                    }
                } else {
                    if (($nTime >= $aMin[$nDia][1]) && ($nTime <= $aMin[$nDia][2])) {
                        if (($nTime + $nMin) > $aMin[$nDia][2]) {
                            $nMin = ($nTime + $nMin) - $aMin[$nDia][2];
                            $nTime = $aMin[$nDia][3];
                        } else {
                            $nMin = $nMin + $nTime;
                            $lExit = 'S';
                        }
                    }
                }
            }
        }
    }

    $cHoras = floor($nMin / 60);
    if ($cHoras < 10) {
        $cHoras = '0' . $cHoras;
    }
    $cMinutos = ($nMin % 60);
    if ($cMinutos < 10) {
        $cMinutos = '0' . $cMinutos;
    }
    $cTime = gmdate("Y-m-d", $dData) . '-' . $cHoras . ':' . $cMinutos;

    return $cTime;
}

function TextoBD($tpbd, $string) {
    $string = str_replace(chr(13), "", $string);
    $string = str_replace(chr(10), "", $string);

    if (($tpbd == "mssql") || ( $tpbd == "MSSQL")) {
        return str_replace("'", "''", stripslashes($string));
    }
    if (($tpbd == "mysql") || ( $tpbd == "MYSQL")) {
        if (get_magic_quotes_gpc() == 0) {
            $string = str_replace('"', '\"', str_replace("'", "\'", $string));
        }
        /*
         * Retirado para não ocorrer problemas com acentuação
         * $ret = UTF8toiso8859_11(utf8_encode($string));
         */
        $ret = $string;
        return $ret;
    }
}

function DataGetBD($vl_campo) {
    if (($vl_campo) == "17530101") {
        $vl_campo = "";
    }
    if ($vl_campo) {
        $vl_campo_trat = substr($vl_campo, 8, 2) . '/' . substr($vl_campo, 5, 2) . '/' . substr($vl_campo, 0, 4);
    } else {
        $vl_campo_trat = '';
    }
    return $vl_campo_trat;
}

function DataSetBD($vl_campo) {
    if ($vl_campo) {
        $vl_campo_trat = substr($vl_campo, 6, 4) . '-' . substr($vl_campo, 3, 2) . '-' . substr($vl_campo, 0, 2);
    } else {
        $vl_campo_trat = '';
    }
    return $vl_campo_trat;
}

function NumeroGetBD($vl_campo) {
    if ($vl_campo) {
        $vl_campo_trat = number_format(($vl_campo * 1), 2, ',', '.');
    } else {
        $vl_campo_trat = '0';
    }
    return $vl_campo_trat;
}

function NumeroSetBD($vl_campo) {
    if ($vl_campo) {
        $vl_campo_trat = str_replace(',', '.', str_replace('.', '', $vl_campo));
    } else {
        $vl_campo_trat = '0';
    }
    return $vl_campo_trat;
}

/*
  valorPorExtenso - ? :)
  Copyright (C) 2000 andre camargo

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

  Andr&eacute;) Ribeiro Camargo (acamargo@atlas.ucpel.tche.br)
  Rua Silveira Martins, 592/102
  Canguçu-RS-Brasil
  CEP 96.600-000
 */

// funcao............: valorPorExtenso
// ---------------------------------------------------------------------------
// desenvolvido por..: andré camargo
// versoes...........: 0.1 19:00 14/02/2000
//                     1.0 12:06 16/02/2000
// descricao.........: esta função recebe um valor numérico e retorna uma 
//                     string contendo o valor de entrada por extenso
// parametros entrada: $valor (formato que a função number_format entenda :)
// parametros saída..: string com $valor por extenso

function valorPorExtenso($valor=0) {
    $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
    $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões",
        "quatrilhões");

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
        "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
        "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
        "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
        "sete", "oito", "nove");

    $z = 0;

    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    for ($i = 0; $i < count($inteiro); $i++)
        for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
            $inteiro[$i] = "0" . $inteiro[$i];

    // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
    $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
    for ($i = 0; $i < count($inteiro); $i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
                $ru) ? " e " : "") . $ru;
        $t = count($inteiro) - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000"

            )$z++; elseif ($z > 0)
            $z--;
        if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
            $r .= ( ($z > 1) ? " de " : "") . $plural[$t];
        if ($r)
            $rt = $rt . ((($i > 0) && ($i <= $fim) &&
                    ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
    }

    return($rt ? $rt : "zero");
}

function textoDiadaSemana($dia) {
    $aDias = array("Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado");
    return $aDias[$dia];
}

function textoMes($mes) {
    $aMeses = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    return $aMeses[$mes - 1];
}

function trata_tags_sql($sql) {
    $sql = str_replace('@igual', '=', $sql);
    $sql = str_replace('@dif', '<>', $sql);
    $sql = str_replace('@in', ' in ', $sql);
    $sql = str_replace('@maior', '>', $sql);
    $sql = str_replace('@menor', '<', $sql);
    $sql = str_replace('@sf', "'", $sql);
    $sql = str_replace('@s', "'", $sql);
    $sql = str_replace('@sd@', '"', $sql);
    $sql = str_replace('@and', " and ", $sql);
    $sql = str_replace('@or', " or ", $sql);
    $sql = str_replace('@between', " between ", $sql);
    $sql = str_replace('@pctlike', "%", $sql);
    $sql = str_replace('@like', " like ", $sql);
    $sql = str_replace('@mais@', " + ", $sql);
    $sql = str_replace('@diahoje@', date("d"), $sql);
    $sql = str_replace('@meshoje@', date("m"), $sql);
    $sql = str_replace('@anohoje@', date("Y"), $sql);
    $sql = str_replace('@dthoje@', date("Y-m-d"), $sql);
    return $sql;
}
?>