<?php

/**
 * KONVERT v1.1
 * by kmS!
 *
 * HISTORY:
 * v1.1.1 - Ene 09 - haxor para "inverted exclamation" del tin
 * v1.1 - Ago 08 - haxor para & en url y letra A con tilde de word
 * v1.1 - Jun 08 - rc
 */


function get_html_translation_table_CP1252_extended() {
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
	$trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
	$trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
	$trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
	$trans[chr(134)] = '&dagger;';    // Dagger
	$trans[chr(135)] = '&Dagger;';    // Double Dagger
	$trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
	$trans[chr(137)] = '&permil;';    // Per Mille Sign
	$trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
	$trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
	$trans[chr(140)] = '&OElig;    ';    // Latin Capital Ligature OE
	$trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
	$trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
	$trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
	$trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
	$trans[chr(149)] = '&bull;';    // Bullet
	$trans[chr(150)] = '&ndash;';    // En Dash
	$trans[chr(151)] = '&mdash;';    // Em Dash
	$trans[chr(152)] = '&tilde;';    // Small Tilde
	$trans[chr(153)] = '&trade;';    // Trade Mark Sign
	$trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
	$trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
	$trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
	$trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
	$trans[chr(32)] = "&nbsp;";
	$trans[chr(241)] = "&ntilde;";
	$trans[chr(209)] = "&Ntilde;";
	$trans[chr(161)] = "&iexcl;";
//	$trans["&"] = "&amp;";     #ampersand
	$trans["á"] = "&aacute;";     #latin small letter a
	$trans["Â"] = "&Acirc;";     #latin capital letter A
	$trans["â"] = "&acirc;";     #latin small letter a
	$trans["Æ"] = "&AElig;";     #latin capital letter AE
	$trans["æ"] = "&aelig;";     #latin small letter ae
	$trans[chr(193)] = "&Aacute;";     #latin capital letter A
	$trans[chr(239)] = "&Aacute;";     #latin capital letter A
	$trans["À"] = "&Agrave;";     #latin capital letter A
	$trans["à"] = "&agrave;";     #latin small letter a
	$trans["Å"] = "&Aring;";     #latin capital letter A
	$trans["å"] = "&aring;";     #latin small letter a
	$trans["Ã"] = "&Atilde;";     #latin capital letter A
	$trans["ã"] = "&atilde;";     #latin small letter a
	$trans["Ä"] = "&Auml;";     #latin capital letter A
	$trans["ä"] = "&auml;";     #latin small letter a
	$trans["Ç"] = "&Ccedil;";     #latin capital letter C
	$trans["ç"] = "&ccedil;";     #latin small letter c
	$trans["É"] = "&Eacute;";     #latin capital letter E
	$trans["é"] = "&eacute;";     #latin small letter e
	$trans["Ê"] = "&Ecirc;";     #latin capital letter E
	$trans["ê"] = "&ecirc;";     #latin small letter e
	$trans["È"] = "&Egrave;";     #latin capital letter E
	$trans["û"] = "&ucirc;";     #latin small letter u
	$trans["Ù"] = "&Ugrave;";     #latin capital letter U
	$trans["ù"] = "&ugrave;";     #latin small letter u
	$trans["Ü"] = "&Uuml;";     #latin capital letter U
	$trans["ü"] = "&uuml;";     #latin small letter u
	$trans["Ý"] = "&Yacute;";     #latin capital letter Y
	$trans["ý"] = "&yacute;";     #latin small letter y
	$trans["ÿ"] = "&yuml;";     #latin small letter y
	$trans["Ÿ"] = "&Yuml;";     #latin capital letter Y
	ksort($trans);
	return $trans;
}

function xmlcharacters($string, $trans='') {
   $trans_tbl1 = get_html_translation_table_CP1252_extended(HTML_ENTITIES);
   foreach ( $trans_tbl1 as $ascii => $htmlentitie ) {
        $trans_tbl2[$ascii] = '&#'.ord($ascii).';';
   }
   $trans_tbl1 = array_flip ($trans_tbl1);
   $trans_tbl2 = array_flip ($trans_tbl2);
   return strtr (strtr ($string, $trans_tbl1), $trans_tbl2);
}
function konvert($text) {
	$prepatch = str_replace("¡", chr(161), $text); // inverted exclamation haxor :D
	$prepatch = preg_replace('/[^!-%\x27-;=?-~<>&\x09\x0a\x0d\x0B ]/e', '"&#".ord("$0").chr(59)', preg_replace('/[^\x00-\x7F]/e', '"&#".ord("$0").";"', xmlcharacters($prepatch)));
	$prepatch = preg_replace('/&#195;&#([0-9]+);/e', '"&#".((int) \\1 + 64).";"', $prepatch);

/*	$prepatch = str_replace("&#195;&#177;", "&#241;", preg_replace('/[^\x00-\x7F]/e', '"&#".ord("$0").";"', xmlcharacters($text)));
	$prepatch = str_replace("&#195;&#179;", "&#243;", $prepatch);
	$prepatch = str_replace("&#195;&#169;", "&#233;", $prepatch); // é
	$prepatch = str_replace("&#195;&#186;", "&#250;", $prepatch); // Ú
	$prepatch = str_replace("&#195;&#173;", "&#237;", $prepatch); // í
	$prepatch = str_replace("&#195;&#161;", "&#225;", $prepatch); // á
*/
	$prepatch = str_replace("&#", "^", $prepatch); // h4x0r para url's!
	$prepatch = str_replace("&", "&amp;", $prepatch); // h4x0r para url's!!
	$prepatch = str_replace("^", "&#", $prepatch); // h4x0r para url's!!!

	$prepatch = str_replace(chr(239), "&#193;", $prepatch); // Á <-- letra MUY ladilla (solo para Á escritas en word -.-)
	$prepatch = str_replace("&#195;?", "&#193;", $prepatch); // Á <-- si, es rompepija y pide estas 2 cosas -.-

	return $prepatch;
}
?>
