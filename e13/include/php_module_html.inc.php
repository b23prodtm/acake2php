<?php

/*! 
@copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
@author	www.b23prodtm.infoNA
@date	Sat Sep 18 15:42:56 CEST 2004 @613 /Internet Time/
@filename	php_module_html.inc
*/

global $ModuleHTML;
if (!isset($ModuleHTML)) {
	
	$ModuleHTML = 1;
	
	function indexedArray_toString($options,$separateur1 = " ", $separateur2 = "=", $strings = TRUE) {
		if($strings) $s = "\"";
		else $s = "";
		$o = "";
		while (list ($key, $value) = each($options)) {
			if($value != -1)
				$o .= $key.$separateur2.$s.$value.$s.$separateur1;
		}
		return $o;
	}
	
	//TRADUCTION des options de style d'un Ã©lÃ©ment HTML. placer le tableau d'options dans $options avec un index explicite (p.ex. HTML, css, javascript)
	function optionsArrayToHTML($options=array("HTML" => array (),
											   "css" => array(),
											   "javascript" => array())) {
		$html = $css = $js = $class = "";
		if(is_array($options)) {
			while (list ($key, $value) = each($options)) {
				switch ($key) {
					case "HTML":
						$html = indexedArray_toString($value," ", "=");
						break;
					case "css":
						$css = " style = \"".indexedArray_toString($value, ";", ":", FALSE)."\"";
						break;
					case "javascript":
						$js = indexedArray_toString($value, " ", "=");
						break;
					case "class":
						$class = " class = \"".$value."\"";
					default:
						break;
				}
			}
		}
		
		return array("HTML" => $html, "css" => $css, "javascript" => $js, "class" => $class);
	}
	
	
	/* module de production de balises HTML */
	
	function HTML_lien($url,$libelle,$options = array(),$cible="_self")
	{
		$o = optionsArrayToHTML($options);
		return "<A HREF=\"$url\" TARGET='$cible' ".$o["class"].$o["HTML"].$o["css"].$o["javascript"].">$libelle</A>\n";
	}
	
	function HTML_image($origine,$options = array ())
	{
		
		$o = optionsArrayToHTML($options);
                $chaine="";
		if($o["class"] != "") $chaine = "<DIV ".$o["class"].">";
		$chaine .="<IMG SRC=\"$origine\" ".$o["HTML"].$o["css"].$o["javascript"].">\n";
		if($o["class"] != "") $chaine .= "</DIV>\n";
		return $chaine;
	}
	
	// bouton ouvrant une page
	
	function HTML_boutonLoad($page,$label)
	{
		$chaine_bouton="<input type=\"button\" name=\"Bouton\" value=\"$label\" onClick=\"location.href='$page'\">";
		return $chaine_bouton;
	}
	
	// LISTES
	function HTML_listeDebut($align="LEFT",$modenum=FALSE)
	{
		return HTML_listeDebut_div($modenum, array("HTML" => array("align" => $align)));
	}

	function HTML_listeDebut_div($modenum=FALSE,$options = array())
	{
		$o = optionsArrayToHTML($options);
		$listedebut = "<div ".$o["HTML"].$o["class"].">";
		if ($modenum) $listedebut .= "<ol>\n";
		else $listedebut .= "<ul>\n";
		return $listedebut;
	}
	
	function HTML_listeDebut_num($align="LEFT")
	{
		return HTML_listeDebut($align,TRUE);
	}
	
	function HTML_listeFin($modenum=FALSE)
	{
		$listefin="</div></ul>\n";
		if ($modenum) $listefin="</div></ol>\n";
		return $listefin;
	}
	
	function HTML_listeFin_num()
	{
		return HTML_listeFin(TRUE);
	}
	
	function HTML_listeElement($contenu)
	{
		$html = "";
		if (is_array($contenu))
		{
			$nbElements = count($contenu);
			for ($i=0; $i < $nbElements; $i++)
			{
				$html .= "<li>";
				$html .= $contenu[$i];
				$html .= "</li>\n";
			}
		}
		else
		{
			$html .= "<li>";
			$html .= $contenu;
			$html .= "</li>\n";
		}
		return $html;
	}
}
?>