<?php
define( 'SERVER_ROOT', $_SERVER['DOCUMENT_ROOT'] );

$id_cat_eventos   = 3; // Noticias/Eventos id category.
$id_cat_novedades = 2; // Novedades id category.

$n_post_export    = 150; // Max. number of post to export.

$return           = "\r\n";

$array_photos     = array();

// Change for Mac! InDesign Tagged text https://help.adobe.com/en_US/indesign/cs/taggedtext/indesign_cs5_taggedtext.pdf !
$indesign_header  = '<ANSI-WIN>' . $return;
$indesign_header .= '<Version:13>';
$indesign_header .= <<<'EOD'
<FeatureSet:InDesign-Roman><ColorTable:=<Naranja DNG:COLOR:RGB:Process:0.8941176470588236,0.4235294117647059,0.0392156862745098><Black:COLOR:CMYK:Process:0,0,0,1>>
<DefineCharStyle:Hyperlink=<Nextstyle:Hyperlink><cColor:Naranja DNG><cTypeface:Italic><cUnderline:1><cUnderlineOffset:2.000000><cUnderlineType:JapaneseDots>>
<DefineParaStyle:Texto negro\:Texto negro indent=<BasedOn:Texto negro\:Texto negro base><Nextstyle:Texto negro\:Texto negro indent><pFirstLineIndent:14.173228>>
<DefineParaStyle:Texto negro\:Texto negro base=<Nextstyle:Texto negro\:Texto negro indent><cSize:14.000000><cLeading:20.000000><cFont:Myriad Pro><pTextAlignment:JustifyLeft><pGridAlign:BaseLine>>
<DefineParaStyle:Texto negro\:Titular=<BasedOn:Texto negro\:Texto negro base><Nextstyle:Texto negro\:Texto negro indent><cTypeface:Bold><cSize:20.000000><pSpaceBefore:8.503937>>
<DefineParaStyle:Texto negro\:Novedades Titulo=<BasedOn:Texto negro\:Titular><Nextstyle:Texto negro\:Novedades Titulo><cColor:Naranja DNG><pHyphenation:0><pTextAlignment:Left>>
<DefineParaStyle:Texto negro\:Novedades h1-6=<BasedOn:Texto negro\:Texto negro base><Nextstyle:Texto negro\:Novedades h1-6><cTypeface:Bold><cSize:18.000000><pSpaceBefore:2.834645>>
EOD;

$html_init = <<<'EOD'
<!DOCTYPE html><html><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>DNG Photo Magazine</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" media="screen" href="estilos.css">
</head><body>
<div class="header">
<h2 class="h2-cabecera">DNG Photo Magazine</h2>
<a title="Twitter DNG Photo Magazine" href="https://twitter.com/fotodng"><img class="icon-sm" src="./icons/d-twitter.png" alt="Twitter DNG Photo Magazine" width="30" height="30"></a>
<a title="FaceBook DNG Photo Magazine" href="https://www.facebook.com/revista.fotodng"><img class="icon-sm" src="./icons/d-facebook.png" alt="FaceBook DNG Photo Magazine" width="30" height="30"></a>
<a title="Flickr DNG Photo Magazine" href="https://www.flickr.com/groups/fotodng/"><img class="icon-sm" src="./icons/d-flickr.png" alt="Flickr DNG Photo Magazine" width="30" height="30"></a>
<a title="Google+ DNG Photo Magazine" href="https://www.google.com/+FotodngMag"><img class="icon-sm" src="./icons/d-gplus.png" alt="Google+ DNG Photo Magazine" width="30" height="30"></a>
<a title="Canal Youtube DNG Photo Magazine" href="https://www.youtube.com/user/fotodng"><img class="icon-sm" src="./icons/d-youtube.png" alt="Canal Youtube DNG Photo Magazine" width="30" height="30"></a>
<a title="Pinterest DNG Photo Magazine" href="https://pinterest.com/fotodng/foto-dng-magazine/"><img class="icon-sm" src="./icons/d-pinterest.png" alt="Pinterest DNG Photo Magazine" width="30" height="30"></a>
<a title="RSS DNG Photo Magazine" href="https://www.fotodng.com/feed/rss"><img class="icon-sm" src="./icons/d-rss.png" alt="RSS DNG Photo Magazine" width="30" height="30"></a>
</div>
<div class="content">
<div class="indicaciones">Despl&aacute;zate en vertical para leer los contenidos completos</div>
EOD;

$html_end  = <<<'EOD'
<div class="copy">Social Media Icons <a href="http://www.freepik.es/">Designed by Freepik</a></div>
</div>
</body></html>
EOD;

/**
 * File creation
 *
 * @param string $file    Name and path for file to create.
 * @param string $content Content for file.
 */
function create_file( $file, $content = 'Sin contenido' ) {
	if ( ! $handle = fopen( $file, 'wb' ) ) {
		echo '<h3>FALLO: No se puede abrir el archivo "' . $file . '"';
		exit;
	}

	if ( false === fwrite( $handle, $content ) ) {
		echo '<h3>FALLO: No se puede escribir el archivo "' . $file . '"';
		exit;
	}

	fclose( $handle );
}

/**
 * Convert from WordPress Charset to ANSI ISO-8859-1.
 *
 * @param string $text Text with string to convert.
 *
 * @return string      Text converted to Unicode ANSI ISO-8859-1.
 */
function convert_ansi( $text ) {
	setlocale( LC_CTYPE, 'es_ES' );

	$res = mb_convert_encoding( $text, 'UTF-8', get_bloginfo( 'charset' ) );
	$res = convert_html_entities_2_unicode( $res );

	return $res;
}

// TODO: Mirar las tablas de https://github.com/pathawks/Export-to-InDesign/blob/master/taggedtext.php !
/**
 * Convert from Unicode and HTML entities to Unidode HEX.
 *
 * @param string $text Text with string to convert.
 *
 * @return string      Text converted to Unicode Hex for InDesign.
 */
function convert_html_entities_2_unicode( $text ) {
	$unicode_array = array(
		'á'          => '<0x00E1>',
		'é'          => '<0x00E9>',
		'í'          => '<0x00ED>',
		'ó'          => '<0x00F3>',
		'ú'          => '<0x00FA>',
		'Á'          => '<0x00C1>',
		'É'          => '<0x00C9>',
		'Í'          => '<0x00CD>',
		'Ó'          => '<0x00D3>',
		'Ú'          => '<0x00DA>',
		'ñ'          => '<0x00F1>',
		'Ñ'          => '<0x00D1>',
		'à'          => '<0x00E0>',
		'è'          => '<0x00E8>',
		'ì'          => '<0x00EC>',
		'ò'          => '<0x00F2>',
		'ù'          => '<0x00F9>',
		'À'          => '<0x00C0>',
		'È'          => '<0x00C8>',
		'Ì'          => '<0x00CC>',
		'Ò'          => '<0x00D2>',
		'Ù'          => '<0x00D9>',
		'ä'          => '<0x00E4>',
		'Ä'          => '<0x00C4>',
		'ç'          => '<0x00E7>',
		'Ç'          => '<0x00C7>',
		'ü'          => '<0x00FC>',
		'Ü'          => '<0x00DC>',
		'¿'          => '<0x00BF>',
		'¡'          => '<0x00A1>',
		"'"          => '<0x2019>',
		'€'          => '<0x20AC>',
		'º'          => '<0x00BA>',
		'ª'          => '<0x00AA>',
		'«'          => '<0x00AB>',
		'»'          => '<0x00BB>',
		'&#8220;'    => '<0x201C>',
		'&#8221;'    => '<0x201D>',
		'&#8216;'    => '<0x201C>',
		'&#8217;'    => '<0x201D>',
		'“'          => '<0x201C>',
		'”'          => '<0x201D>',
		'&#8243'     => '<0x201D>',
		'...'        => '<0x2026>',
		'&#8230;'    => '<0x2026>',
		'‘'          => '<0x2018>',
		'’'          => '<0x2019>',
		'&#8211;'    => '-',
		'&#215;'     => 'x',
		'–'          => '-',
		'™'          => '<0x2122>',
		'®'          => '<0x00AE>',
		'α'          => '<0x03B1>',
		'&amp;'      => '&',
		'&sup1;'     => '<sup>1</sup>',
		'&sup2;'     => '<sup>2</sup>',
		'&sup3;'     => '<sup>3</sup>',
		'&oelig;'    => '<0x0097>',
		'&nbsp;'     => ' ',
		'&iexcl;'    => '<0x00A1>',
		'&cent;'     => '<0x00A2>',
		'&pound;'    => '<0x00A3>',
		'&curren;'   => '<0x00A4>',
		'&yen;'      => '<0x00A5>',
		'&brvbar;'   => '<0x00A6>',
		'&sect;'     => '<0x00A7>',
		'&uml;'      => '<0x00A8>',
		'&copy;'     => '<0x00A9>',
		'&ordf;'     => '<0x00AA>',
		'&laquo;'    => '<0x00AB>',
		'&not;'      => '<0x00AC>',
		'&shy;'      => '<0x00AD>',
		'&reg;'      => '<0x00AE>',
		'&macr;'     => '<0x00AF>',
		'&deg;'      => '<0x00B0>',
		'&plusmn;'   => '<0x00B1>',
		'&acute;'    => '<0x00B4>',
		'&micro;'    => '<0x00B5>',
		'&para;'     => '<0x00B6>',
		'&middot;'   => '<0x00B7>',
		'&cedil;'    => '<0x00B8>',
		'&ordm;'     => '<0x00BA>',
		'&raquo;'    => '<0x00BB>',
		'&frac14;'   => '<0x00BC>',
		'&frac12;'   => '<0x00BD>',
		'&frac34;'   => '<0x00BE>',
		'&iquest;'   => '<0x00BF>',
		'&Agrave;'   => '<0x00C0>',
		'&Aacute;'   => '<0x00C1>',
		'&Acirc;'    => '<0x00C2>',
		'&Atilde;'   => '<0x00C3>',
		'&Auml;'     => '<0x00C4>',
		'&Aring;'    => '<0x00C5>',
		'&AElig;'    => '<0x00C6>',
		'&Ccedil;'   => '<0x00C7>',
		'&Egrave;'   => '<0x00C8>',
		'&Eacute;'   => '<0x00C9>',
		'&Ecirc;'    => '<0x00CA>',
		'&Euml;'     => '<0x00CB>',
		'&Igrave;'   => '<0x00CC>',
		'&Iacute;'   => '<0x00CD>',
		'&Icirc;'    => '<0x00CE>',
		'&Iuml;'     => '<0x00CF>',
		'&ETH;'      => '<0x00D0>',
		'&Ntilde;'   => '<0x00D1>',
		'&Ograve;'   => '<0x00D2>',
		'&Oacute;'   => '<0x00D3>',
		'&Ocirc;'    => '<0x00D4>',
		'&Otilde;'   => '<0x00D5>',
		'&Ouml;'     => '<0x00D6>',
		'&times;'    => '<0x00D7>',
		'&Oslash;'   => '<0x00D8>',
		'&Ugrave;'   => '<0x00D9>',
		'&Yuml;'     => '<0x00D9>',
		'&Uacute;'   => '<0x00DA>',
		'&Ucirc;'    => '<0x00DB>',
		'&Uuml;'     => '<0x00DC>',
		'&Yacute;'   => '<0x00DD>',
		'&THORN;'    => '<0x00DE>',
		'&szlig;'    => '<0x00DF>',
		'&agrave;'   => '<0x00E0>',
		'&aacute;'   => '<0x00E1>',
		'&acirc;'    => '<0x00E2>',
		'&atilde;'   => '<0x00E3>',
		'&auml;'     => '<0x00E4>',
		'&aring;'    => '<0x00E5>',
		'&aelig;'    => '<0x00E6>',
		'&ccedil;'   => '<0x00E7>',
		'&egrave;'   => '<0x00E8>',
		'&eacute;'   => '<0x00E9>',
		'&ecirc;'    => '<0x00EA>',
		'&euml;'     => '<0x00EB>',
		'&igrave;'   => '<0x00EC>',
		'&iacute;'   => '<0x00ED>',
		'&icirc;'    => '<0x00EE>',
		'&OElig;'    => '<0x00EE>',
		'&iuml;'     => '<0x00EF>',
		'&eth;'      => '<0x00F0>',
		'&ntilde;'   => '<0x00F1>',
		'&ograve;'   => '<0x00F2>',
		'&oacute;'   => '<0x00F3>',
		'&ocirc;'    => '<0x00F4>',
		'&otilde;'   => '<0x00F5>',
		'&ouml;'     => '<0x00F6>',
		'&divide;'   => '<0x00F7>',
		'&oslash;'   => '<0x00F8>',
		'&ugrave;'   => '<0x00F9>',
		'&uacute;'   => '<0x00FA>',
		'&ucirc;'    => '<0x00FB>',
		'&uuml;'     => '<0x00FC>',
		'&yacute;'   => '<0x00FD>',
		'&thorn;'    => '<0x00FE>',
		'&yuml;'     => '<0x00FF>',
		'&Scaron;'   => '<0x0160>',
		'&scaron;'   => '<0x0161>',
		'&fnof;'     => '<0x0192>',
		'&circ;'     => '<0x02C6>',
		'&tilde;'    => '<0x02DC>',
		'&Alpha;'    => '<0x0391>',
		'&Beta;'     => '<0x0392>',
		'&Gamma;'    => '<0x0393>',
		'&Delta;'    => '<0x0394>',
		'&Epsilon;'  => '<0x0395>',
		'&Zeta;'     => '<0x0396>',
		'&Eta;'      => '<0x0397>',
		'&Theta;'    => '<0x0398>',
		'&Iota;'     => '<0x0399>',
		'&Kappa;'    => '<0x039A>',
		'&Lambda;'   => '<0x039B>',
		'&Mu;'       => '<0x039C>',
		'&Nu;'       => '<0x039D>',
		'&Xi;'       => '<0x039E>',
		'&Omicron;'  => '<0x039F>',
		'&Pi;'       => '<0x03A0>',
		'&Rho;'      => '<0x03A1>',
		'&Sigma;'    => '<0x03A3>',
		'&Tau;'      => '<0x03A4>',
		'&Upsilon;'  => '<0x03A5>',
		'&Phi;'      => '<0x03A6>',
		'&Chi;'      => '<0x03A7>',
		'&Psi;'      => '<0x03A8>',
		'&Omega;'    => '<0x03A9>',
		'&alpha;'    => '<0x03B1>',
		'&beta;'     => '<0x03B2>',
		'&gamma;'    => '<0x03B3>',
		'&delta;'    => '<0x03B4>',
		'&epsilon;'  => '<0x03B5>',
		'&zeta;'     => '<0x03B6>',
		'&eta;'      => '<0x03B7>',
		'&theta;'    => '<0x03B8>',
		'&iota;'     => '<0x03B9>',
		'&kappa;'    => '<0x03BA>',
		'&lambda;'   => '<0x03BB>',
		'&mu;'       => '<0x03BC>',
		'&nu;'       => '<0x03BD>',
		'&xi;'       => '<0x03BE>',
		'&omicron;'  => '<0x03BF>',
		'&pi;'       => '<0x03C0>',
		'&rho;'      => '<0x03C1>',
		'&sigmaf;'   => '<0x03C2>',
		'&sigma;'    => '<0x03C3>',
		'&tau;'      => '<0x03C4>',
		'&upsilon;'  => '<0x03C5>',
		'&phi;'      => '<0x03C6>',
		'&chi;'      => '<0x03C7>',
		'&psi;'      => '<0x03C8>',
		'&omega;'    => '<0x03C9>',
		'&thetasym;' => '<0x03D1>',
		'&upsih;'    => '<0x03D2>',
		'&piv;'      => '<0x03D6>',
		'&ensp;'     => '<0x2002>',
		'&emsp;'     => '<0x2003>',
		'&thinsp;'   => '<0x2009>',
		'&zwnj;'     => '<0x200C>',
		'&zwj;'      => '<0x200D>',
		'&lrm;'      => '<0x200E>',
		'&rlm;'      => '<0x200F>',
		'&ndash;'    => '<0x2013>',
		'&mdash;'    => '<0x2014>',
		'&lsquo;'    => '<0x2018>',
		'&rsquo;'    => '<0x2019>',
		'&sbquo;'    => '<0x201A>',
		'&ldquo;'    => '<0x201C>',
		'&rdquo;'    => '<0x201D>',
		'&bdquo;'    => '<0x201E>',
		'&dagger;'   => '<0x2020>',
		'&Dagger;'   => '<0x2021>',
		'&bull;'     => '<0x2022>',
		'&hellip;'   => '<0x2026>',
		'&permil;'   => '<0x2030>',
		'&prime;'    => '<0x2032>',
		'&Prime;'    => '<0x2033>',
		'&lsaquo;'   => '<0x2039>',
		'&rsaquo;'   => '<0x203A>',
		'&oline;'    => '<0x203E>',
		'&frasl;'    => '<0x2044>',
		'&euro;'     => '<0x20AC>',
		'&image;'    => '<0x2111>',
		'&weierp;'   => '<0x2118>',
		'&real;'     => '<0x211C>',
		'&trade;'    => '<0x2122>',
		'&alefsym;'  => '<0x2135>',
		'&larr;'     => '<0x2190>',
		'&uarr;'     => '<0x2191>',
		'&rarr;'     => '<0x2192>',
		'&darr;'     => '<0x2193>',
		'&harr;'     => '<0x2194>',
		'&crarr;'    => '<0x21B5>',
		'&lArr;'     => '<0x21D0>',
		'&uArr;'     => '<0x21D1>',
		'&rArr;'     => '<0x21D2>',
		'&dArr;'     => '<0x21D3>',
		'&hArr;'     => '<0x21D4>',
		'&forall;'   => '<0x2200>',
		'&part;'     => '<0x2202>',
		'&exist;'    => '<0x2203>',
		'&empty;'    => '<0x2205>',
		'&nabla;'    => '<0x2207>',
		'&isin;'     => '<0x2208>',
		'&notin;'    => '<0x2209>',
		'&ni;'       => '<0x220B>',
		'&prod;'     => '<0x220F>',
		'&sum;'      => '<0x2211>',
		'&minus;'    => '<0x2212>',
		'&lowast;'   => '<0x2217>',
		'&radic;'    => '<0x221A>',
		'&prop;'     => '<0x221D>',
		'&infin;'    => '<0x221E>',
		'&ang;'      => '<0x2220>',
		'&and;'      => '<0x2227>',
		'&or;'       => '<0x2228>',
		'&cap;'      => '<0x2229>',
		'&cup;'      => '<0x222A>',
		'&int;'      => '<0x222B>',
		'&there4;'   => '<0x2234>',
		'&sim;'      => '<0x223C>',
		'&cong;'     => '<0x2245>',
		'&asymp;'    => '<0x2248>',
		'&ne;'       => '<0x2260>',
		'&equiv;'    => '<0x2261>',
		'&le;'       => '<0x2264>',
		'&ge;'       => '<0x2265>',
		'&sub;'      => '<0x2282>',
		'&sup;'      => '<0x2283>',
		'&nsub;'     => '<0x2284>',
		'&sube;'     => '<0x2286>',
		'&supe;'     => '<0x2287>',
		'&oplus;'    => '<0x2295>',
		'&otimes;'   => '<0x2297>',
		'&perp;'     => '<0x22A5>',
		'&sdot;'     => '<0x22C5>',
		'&lceil;'    => '<0x2308>',
		'&rceil;'    => '<0x2309>',
		'&lfloor;'   => '<0x230A>',
		'&rfloor;'   => '<0x230B>',
		'&lang;'     => '<0x2329>',
		'&rang;'     => '<0x232A>',
		'&loz;'      => '<0x25CA>',
		'&spades;'   => '<0x2660>',
		'&clubs;'    => '<0x2663>',
		'&hearts;'   => '<0x2665>',
		'&diams;'    => '<0x2666>',
	);

	$res = strtr( $text, $unicode_array );

	return $res;
}

$indesign_html_allowed_tags = array(
	'p'          => array(),
	'br'         => array(),
	'b'          => array(),
	'strong'     => array(),
	'i'          => array(),
	'em'         => array(),
	'u'          => array(),
	'ol'         => array(),
	'ul'         => array(),
	'li'         => array(),
	'sub'        => array(),
	'sup'        => array(),
	'h1'         => array(),
	'h2'         => array(),
	'h3'         => array(),
	'h4'         => array(),
	'blockquote' => array(),
);

$html_allowed_tags = array(
	'a' => array(
		'href'  => array(),
		'title' => array(),
	),
	'img' => array(
		'src'    => array(),
		'width'  => array(),
		'height' => array(),
		'alt'    => array(),
	),
	'p'          => array(),
	'br'         => array(),
	'b'          => array(),
	'strong'     => array(),
	'i'          => array(),
	'em'         => array(),
	'u'          => array(),
	'ol'         => array(),
	'ul'         => array(),
	'li'         => array(),
	'sub'        => array(),
	'sup'        => array(),
	'h1'         => array(),
	'h2'         => array(),
	'h3'         => array(),
	'h4'         => array(),
	'blockquote' => array(),
);

$conversion_table = array(
	"\r"             => '',
	"\n"             => '',
	"\r\n"           => '',
	"\f"             => '',
	'&nbsp;'         => ' ',
	'&amp;'          => '&',
	'<pre>'          => $return . '<ParaStyle:Texto negro\:Texto negro base><cFont:Andale Mono>',
	'</pre>'         => '<cFont:>' . $return,
	'<br />'         => $return . '<ParaStyle:Texto negro\:Texto negro base>',
	'<p>'            => $return . '<ParaStyle:Texto negro\:Texto negro base>',
	'</p>'           => '',
	'<p></p>'        => '',
	'<p> </p>'       => '',
	'<p>&nbsp;</p>'  => '',
	'<b><i>'         => '<cTypeface:Bold Italic>',
	'<i><b>'         => '<cTypeface:Bold Italic>',
	'<strong><i>'    => '<cTypeface:Bold Italic>',
	'<i><strong>'    => '<cTypeface:Bold Italic>',
	'<b><em>'        => '<cTypeface:Bold Italic>',
	'<em><b>'        => '<cTypeface:Bold Italic>',
	'<em><strong>'   => '<cTypeface:Bold Italic>',
	'<strong><em>'   => '<cTypeface:Bold Italic>',
	'<b>'            => '<cTypeface:Bold>',
	'<strong>'       => '<cTypeface:Bold>',
	'<i>'            => '<cTypeface:Italic>',
	'<em>'           => '<cTypeface:Italic>',
	'</strong></em>' => '<cTypeface:>',
	'</em></strong>' => '<cTypeface:>',
	'</b></i>'       => '<cTypeface:>',
	'</i></b>'       => '<cTypeface:>',
	'</strong></i>'  => '<cTypeface:>',
	'</i></strong>'  => '<cTypeface:>',
	'</em></b>'      => '<cTypeface:>',
	'</b></em>'      => '<cTypeface:>',
	'</b>'           => '<cTypeface:>',
	'</strong>'      => '<cTypeface:>',
	'</i>'           => '<cTypeface:>',
	'</em>'          => '<cTypeface:>',
	'<u>'            => '<cUnderline:1>',
	'</u>'           => '<cUnderline:>',
	'<del>'          => '<cStrikethru:1>',
	'</del>'         => '<cStrikethru:>',
	'<sub>'          => '<cPosition:Subscript>',
	'</sub>'         => '<cPosition:>',
	'<sup>'          => '<cPosition:Superscript>',
	'</sup>'         => '<cPosition:>',
	'<blockquote>'   => '<ParaStyle:Texto negro\:Texto negro base><pLeftIndent:12><pSpaceBefore:6><pSpaceAfter:6>',
	'</blockquote>'  => $return . '<pLeftIndent:><pSpaceBefore:><pSpaceAfter:>',
	'<ol>'           => $return . '<ParaStyle:Texto negro\:Texto negro base>',
	'</ol>'          => '',
	'<ul>'           => $return . '<ParaStyle:Texto negro\:Texto negro base>',
	'</ul>'          => '',
	'<li>'           => '<pLeftIndent:18.000000><pFirstLineIndent:-18.000000><bnListType:Bullet>',
	'</li>'          => $return . '<pLeftIndent:><pFirstLineIndent:><bnListType:>',
	'<h1>'           => $return . '<ParaStyle:Texto negro\:Novedades h1-6>',
	'</h1>'          => '',
	'<h2>'           => $return . '<ParaStyle:Texto negro\:Novedades h1-6>',
	'</h2>'          => '',
	'<h3>'           => $return . '<ParaStyle:Texto negro\:Novedades h1-6>',
	'</h3>'          => '',
	'<h4>'           => $return . '<ParaStyle:Texto negro\:Novedades h1-6>',
	'</h4>'          => '',
);

$enters_table = array(
	'<ParaStyle:Texto negro\:Texto negro base>' . "\r\n" => '',
	"\r\n\r\n"                                           => '',
);

$photos_search = '~(http.*\.)(jpe?g|png|[tg]iff?|svg)~i';

if ( empty( $_POST ) ) {
	$day_from   = '01';
	$month_from = date( 'm' );
	$year_from  = date( 'Y' );

	$leap_year  = date( 'L' );

	$day_to     = date( 't' );
	$month_to   = date( 'm' );
	$year_to    = date( 'Y' );
} else {
	$date_post_from = explode( '-', $_POST['date_from'] );
	$day_from       = $date_post_from[2];
	$month_from     = $date_post_from[1];
	$year_from      = $date_post_from[0];

	$date_post_to   = explode( '-', $_POST['date_to'] );
	$day_to         = $date_post_to[2];
	$month_to       = $date_post_to[1];
	$year_to        = $date_post_to[0];
}
$date_from = $year_from . '-' . $month_from . '-' . $day_from;
$date_to   = $year_to . '-' . $month_to . '-' . $day_to;

// For not script timeout.
set_time_limit( 0 );

// If we have POST data and nonce is ok export Begin, let's go!!!
if ( ! empty( $_POST ) && check_admin_referer( 'indesign_export', 'cl_wpnonce' ) ) {
	// Parameters for query our posts.
	$post_category_id = absint( $_POST['post_category_id'] );

	// https://wordpress.org/support/topic/orderby-custom-field-meta_key !
	$array_dates = array(
		array(
			'after' => array(
				'year'   => $year_from,
				'month'  => (int) $month_from,
				'day'    => (int) $day_from,
				'hour'   => 0,
				'minute' => 0,
				'second' => 0,
			),
			'before' => array(
				'year'   => $year_to,
				'month'  => (int) $month_to,
				'day'    => (int) $day_to,
				'hour'   => 23,
				'minute' => 59,
				'second' => 59,
			),
			'inclusive' => true,
		),
	);

	// Args for query post.
	$args = array(
		'date_query'     => $array_dates,
		'cat'            => $post_category_id,
		'post_status'    => array( 'publish', 'future' ),
		'posts_per_page' => $n_post_export,
	);

	// For Novedades category we order for meta_key nombre_marca !
	if ( 32 === (int) $post_category_id ) {
		$args['order']    = 'ASC';
		$args['orderby']  = 'meta_value';
		$args['meta_key'] = 'nombre_marca';
	}

	$query = new WP_Query( $args );

	$data_txt  = '';
	$data_html = '';

	$n_posts_processed = 0;

	if ( ! empty( $_GET['type'] ) && 'html' === $_GET['type'] ) { // For html file.
		while ( $query->have_posts() ) {
			$query->the_post();

			$title     = get_the_title();
			$post_link = get_permalink();
			$short_url = wp_get_shortlink();

			$content   = get_the_content();
			$content   = strip_shortcodes( $content ); // Delete all shortcodes.
			$content   = wpautop( $content ); // Changes double enter by <p></p>.
			$content   = wp_kses( $content, $html_allowed_tags ); // Delete HTML tags except allowed.
			$content   = wptexturize( $content ); // Change to Smart Quotes.
			$content   = trim( $content ); // Trim spaces.

			// Sarch all photos and add to array array_photos.
			preg_match_all( $photos_search, $content, $array_photos[ $n_posts_processed ] );

			$data_html .= '<article>'; // Start new article.
			$data_html .= '<h2 class="titulo-post">' . $title . '</h2>'; // Post title.
			$data_html .= $content; // Post content.
			$data_html .= '<div class="dng-url">Noticia en DNG Photo Magazine: <a class="dng-bitly" href="' . $short_url . '">' . $short_url . '</a></div>'; // Blog link and our text.
			$data_html .= '</article>'; // Close article.

			$n_posts_processed++; // Increase exported posts number.
		}
	} else { // For InDesign idml file.
		while ( $query->have_posts() ) {
			$query->the_post();

			$title = get_the_title();
			//$title = convert_unicode( $title ); // Covert title to UNICODE.
			$title = convert_ansi( $title ); // Covert title to UNICODE.
			$post_link = get_permalink();
			$short_url = wp_get_shortlink();

			if ( 32 === (int) $post_category_id ) { // For Novedades.
				$content = get_the_content();
				$content = strip_shortcodes( $content ); // Delete all shortcodes.
				$content = wpautop( $content ); // Changes double enter by <p></p>.
				$content = wp_kses( $content, $indesign_html_allowed_tags ); // // Delete HTML tags except allowed.
				$content = wptexturize( $content ); // Change to Smart Quotes.
				$content = trim( $content ); // Trim spaces.

				$content = strtr( $content, $conversion_table ); // Change HTML tags by InDesign tags.
				$content = strtr( $content, $enters_table ); // Delete empty paragraphs.

				$content = str_replace( "\xc2\xa0", ' ', $content ); // Replace Non Breaking Space by normal space.

				$content = convert_ansi( $content );
			} else { // For Noticias y Eventos.
				$content = null;
			}

			$data_txt .= '<ParaStyle:Texto negro\:Novedades Titulo>' . $title; // Add the title with InDesign Style.
			$data_txt .= $content; // Post content.
			$data_txt .= $return; // Add one Enter.
			$data_txt .= '<ParaStyle:Texto negro\:Texto negro base><cTypeface:Bold Italic>En DNG:<cTypeface:> ' . $short_url; // Blog link and our text.
			$data_txt .= $return; // Add one Enter.

			$n_posts_processed++; // Increase exported posts number.
		}
	}

	wp_reset_postdata(); // Reset query.

	if ( 23 === (int) $post_category_id ) { // For Noticias/Eventos category.
		$pre_name = 'eventos-';
	} elseif ( 32 === (int) $post_category_id ) { // For Novedades category.
		$pre_name = 'novedades-';
	}

	if ( ! empty( $_GET['type'] ) && 'html' === $_GET['type'] ) { // Fot html file.
		// Change full image path for  ./images/ .
		$html_content = preg_replace( '/https:\/\/www\.fotodng\.com\/wp-content\/uploads\/[0-9]{4}\/[0-9]{2}\//', './images/', $data_html );

		// Change Youtube video for image with link to full video.
		$html_content = preg_replace( '/https:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/', '<a href="https://www.youtube.com/embed/$1?autoplay=1&rel=0&modestbranding=1"><img src="./icons/youtube.jpg" alt="Youtube video" width="600" height="359"></a>', $html_content );

		// Change Vimeo video for image with link to full video.
		$html_content = preg_replace( '/https:\/\/vimeo\.com\/([0-9]*)/', '<a href="https://player.vimeo.com/video/$1?autoplay=1&badge=0&byline=0"><img src="./icons/youtube.jpg" alt="Vimeo video" width="600" height="359"></a>', $html_content );


		$data_content = $html_init . $html_content . $html_end;

		// This plugin don't check that path exists!
		$file_path = '/wp-content/indesign-exports/index-' . $pre_name . $date_from . '-to-' . $date_to . '.html';

		$zip_state = false;

		// Create zip file with images.
		// This plugin don't check that path exists!
		$zip_path = '/wp-content/indesign-exports/images-' . $pre_name . $date_from . '-to-' . $date_to . '.zip';

		$n_photos_post = 0;
		$zip = new ZipArchive();
		if ( true === $zip->open( SERVER_ROOT . $zip_path, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE ) ) {

			// Loop for each photo.
			for ( $i = 0; $i <= $n_posts_processed; $i++ ) {
				if ( ! empty( $array_photos[ $i ][0] ) ) {
					$n_photos_post = count( $array_photos[ $i ][0] ); // See post photos number for view if exist more than one.
				} else {
					$n_photos_post = 0;
				}

				for ( $j = 0; $j <= $n_photos_post; $j++ ) {
					if ( ! empty( $array_photos[ $i ][0][ $j ] ) ) {
						$url_photo_original = $array_photos[ $i ][0][ $j ];

						$photo_path = str_replace( 'https://www.fotodng.com/', SERVER_ROOT . '/', $url_photo_original );
						$photo_name = basename( $photo_path );

						if ( ! empty( $photo_path ) && file_exists( $photo_path ) ) { // if not empty photo and url exists.
							$zip->addFile( $photo_path, 'images/' . $photo_name ); // We add every photo to zip file.
						}
					}
				}
			}
			$zip->close();

			$zip_state = true;
		} else {
			echo '<h2>No se ha podido crear el archivo zip con las fotos</h2>';

			$zip_state = false;
		}
	} else { // For InDesign idml file.
		$data_content = $indesign_header . $data_txt;

		// This plugin don't check that path exists!
		$file_path = '/wp-content/indesign-exports/' . $pre_name . $date_from . '-to-' . $date_to . '.txt';
	}

	create_file( SERVER_ROOT . $file_path, $data_content );

}
?>

<div class="wrap">
	<form method="post" accept-charset="UTF-8" action="<?php echo $_SERVER['REQUEST_URI'];?>" id="posts_2id">
		<h1>Generar Tagged Text:</h1>

		<label for="post_category_id">Categor&iacute;a:</label>
		<select name="post_category_id">
			<option value="<?php echo absint( $id_cat_eventos ); ?>">Noticias/Eventos</option>
			<option value="<?php echo absint( $id_cat_novedades ); ?>" selected>Novedades</option>
		</select><br/>

		<label for="date_from">Desde: </label><input name="date_from" type="date" require="required" value="<?php echo $date_from;?>" /><br />
		<label for="date_to">Hasta: </label><input name="date_to" type="date" require="required" value="<?php echo $date_to;?>" /><br />

		<input type="hidden" name="archivo_descarga" value="descarga_posts" />

		<?php wp_nonce_field( 'indesign_export', 'cl_wpnonce' ); ?>

		<input type="reset" value="Restablecer Valores" />
		<input type="submit" value="Generar archivo Tagged Text" />
	</form>
</div>

<div class="wrap">
	<form method="post" accept-charset="UTF-8" action="<?php echo $_SERVER['REQUEST_URI']; ?>&type=html" id="posts_2html">
		<h1>Generar HTML:</h1>

		<label for="post_category_id">Categoría:</label>
		<select name="post_category_id">
			<option value="<?php echo absint( $id_cat_eventos ); ?>" selected>Noticias/Eventos</option>
			<option value="<?php echo absint( $id_cat_novedades ); ?>">Novedades</option>
		</select><br/>

		<label for="date_from">Desde: </label><input name="date_from" type="date" require="required" value="<?php echo $date_from;?>" /><br />
		<label for="date_to">Hasta: </label><input name="date_to" type="date" require="required" value="<?php echo $date_to;?>" /><br />

		<input type="hidden" name="archivo_descarga" value="descarga_posts" />

		<?php wp_nonce_field( 'indesign_export', 'cl_wpnonce' ); ?>

		<input type="reset" value="Restablecer Valores" />
		<input type="submit" value="Generar archivo HTML" />
	</form>
</div>

<?php
if ( ! empty( $_POST ) ) {
	echo '<p>Archivo <strong>' . $file_path . '</strong> creado correctamente (<strong>' . absint( $n_posts_processed ) . '</strong> posts exportados)</p>';
	echo '<p>Descargar <a href="' . $file_path . '" download target="_blank""> ' . $file_path . '</a> <em>(' . date( 'd' ) . '/' . date( 'm' ) . '/' . date( 'Y' ) . ' ' . date( 'H' ) . ':' . date( 'i' ) . ')</em></p>';

	if ( ! empty( $_GET['type'] ) && 'html' === $_GET['type'] ) { // For html file.
		if ( $zip_state ){ // Si zip creation is ok.
			echo '<p>Descargar <a href="' . $zip_path . '" download target="_blank""> ' . $zip_path . '</a> <em>(' . date( 'd' ) . '/' . date( 'm' ) . '/' . date( 'Y' ) . ' ' . date( 'H' ) . ':' . date( 'i' ) . ')</em></p>';
		} else {
			echo '<h2>Ha ocurrido un fallo al crear el archivo zip con la fotos</p>';
		}
	}
}
