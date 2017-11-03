<?php

require_once $GLOBALS['include__php_info.class.inc'];
require_once $GLOBALS['include__php_SQL.class.inc'];
/**
 * Info helper
 *
 * @package       app.View.Helper
 */
class InfoHelper extends AppHelper {
	
	var $r;
	var $pageCount;
	public function __construct(View $view, $settings = array("countPerPage" => "5")) {
		parent::__construct($view, $settings);
		$this->r = new Index($view);
		if(array_key_exists("countPerPage", $settings)) {
				$this->pageCount = $settings["countPerPage"];
		}
	}
	
	
	/**
	 * @return toutes les dimensions dans l'ordre des indices ['w'=>[..],'h'=>[..]]
	 */
	function __getInfoImageSize(SQL &$sql, Info $ifo) {
					$taille = array("w" => array(), "h" => array());
					for ($i = 0; $i < count($ifo->images); $i++) {
									$img = $ifo->getImage($sql, $i);
									$taille["w"][] = $img->getWidth();
									$taille["h"][] = $img->getHeight();
					}
					return $taille;
	}

	/**
	 * @return string derniers posts publie en langue locale
	 *                  */
	private function __getInfo(SQL &$sql, $offset = 0, &$result = array()) {
			$lastinfo = new Info($sql, $null, $this->r->lang("titre_dsc", "infos"), "staff", $this->r->lang("contenu_dsc", "infos"));
			if ($sql->connect_succes()) {
					$dateSelect = " AND published <= CURDATE() ";
					$infos = $sql->query("SELECT * FROM info WHERE langue ='" . getPrimaryLanguage() . "'" . $dateSelect . " ORDER BY published DESC LIMIT (" . $offset . "," . $this->pageCount . ")");
					if ($sql->select_succes($infos)) {		
							do {
								$lastinfo = new Info($sql, $infos);
								if(!$lastinfo) {
									break;
								}
								$lastinfo->setContenu($this->r->view->Markdown->transform($lastinfo->getContenu()));
								$result[] = $lastinfo;
							} while(true);
							mysqli_free_result($infos);
					}
			}
			return $lastinfo;
	}
	
	/**
	 * @param bool $slider active un slider photos
	 * @return string balise de la premiere photo du dernier post  dernier post photo publie en langue locale
	 */
	function getInfoFlashPhoto($slider = false, $n = 1) {
			$sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);
			$lastinfo = $this->__getInfo($sql, $n);
			$html = "";
			$html = $lastinfo->tableauImages($sql, TBL_DIV);
			/* recuperer la taille de l'image la plus grande */
			$taille = $this->__getInfoImageSize($sql, $lastinfo);
			if (count($lastinfo->images) != 0) {
							if ($slider) {
									/*
										element cakephp View/Elements/E14/slideshow.jq.ctp
										dimensionnement en fonction de la plus grande image
									 */
									$slider = $this->r->view->element("E14/slideshow.jq", array("photos" => $html,
											"maxWidth" => max($taille["w"]),
											"maxHeight" => max($taille["h"])), array('cache' => array('config' => 'short', 'key' => 'unique value')));
									return $slider;
							} else {
									return $lastinfo->getImageAsTag($sql, 0, TBL_DIV);
							}
			} else {
							return $html;
			}
	}
	
	/**
	 * affiche le post le plus recent avec son contenu ronque a 80 caracteres
	 * @return string dernier post publie en langue locale sans photos
	 * NOTE : le rendu peut etre errone si le contenu revele des balise HTML incompletes apres tronquage
	 */
	function getInfoFlash() {
			$sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);
			return $this->getInfoFlashN($sql, 1);
	}
	
	/**
	* @param array $pages contiendra les offset de pages 
	* @return chaine HTML */
	function getInfoFlashN(SQL &$sql, $n = 1, &$pages = array()) {
			$html = "";
			/* 3 pages precedentes et une suivante */
			for($i = max(0, $n - 3); $i < $n + 1; $i++){				
					$infos = array();
					/* SQL offset d'une page */
					$pages[$i] = max(0, ($i - 1) * $this->pageCount);
					if($i == $n - 1) {	
							$infos = array();
							/* selection des lignes de donnees */
							$this->__getInfo($sql, $pages[$i], $infos);
							/* contenu de la page */
							foreach($infos as $lastinfo) {
								$c = $lastinfo->getContenu(null, true, true, 160);
								$html .= $this->r->view->HTML->para("info_flash", $lastinfo->getLien($lastinfo->getTitre()) . "<br>" . $this->r->view->Markdown->transform($c) . "...\n");
							}
					}
			}	
			return $html;
	}

}