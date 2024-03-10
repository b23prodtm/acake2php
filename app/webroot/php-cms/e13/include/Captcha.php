<?php

/*
 * Copyright 2017 wwwb23prodtminfo <b23prodtm at sourceforge.net>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!isset($_ENV['ClasseCapt'])) {
        $_ENV['ClasseCapt'] = 1;
        ${__FILE__} = new Index();
        include basename(${__FILE__}->r['include__php_image.class.inc']);

        /** @var NOMBRE pour captcha()*/
        define("NOMBRE", 1);
        /** @var TEXTE pour captcha()*/
        define("TEXTE", 2);
        /** @var MOT pour captcha()*/
        define("MOT", 4);

        /**
        * @link https://openclassrooms.com/courses/les-captchas-anti-bot cr�ation originale d'un captcha anti-bot pour php
        * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
        */
        class Captcha {

                var $t;

                protected static $_r;
                var $r;
                /* @var $tailleMot entier <= nombre de caractere du plus long mot dict-lang.txt */
                public function __construct($tailleMot) {
                        if (self::$_r === null) {
                            self::$_r = new Index();
                        }
                        $this->r = self::$_r;
                        $this->t = $tailleMot;
                }

                /** e�nerer une chaine de caracteres alphabetiques aleatoires. */
                function motHasard($n) {
                        $lettres = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
                        $nl = count($lettres) - 1;
                        $mot = '';
                        for ($i = 0; $i < $n; ++$i) {
                                $mot .= $lettres[mt_rand(0, $nl)];
                        }
                        return $mot;
                }

                /** recherche aleatoire et recursive pour trouver un mot dans un dictionnaire de mots de longueur $this->t caracteres */
                function motListe($liste, $i = 0) {
                        $c = count($liste);
                        $mot = trim($liste[array_rand($liste)]);
                        /** le mot n'est pas de la longueur desiree */
                        if (strlen($mot) != $this->t) {
                                if ($c > $i + 1) {
                                        $mot = $this->motListe($liste, $i + 1);
                                } else {/** la liste a �t� parcourue '$c' fois, retourne un mot au hasard */
                                        $mot = $this->motHasard($this->t);
                                }
                        }
                        return $mot;
                }

                function nombre($n) {

                        return str_pad(mt_rand(0, pow(10, $n) - 1), $n, '0', STR_PAD_LEFT);
                }

                /** @return une instance Image pour affichage  */
                public function image($mot) {
                        $largeur = strlen($mot) * 10;
                        $hauteur = 20;
                        $img = imagecreate($largeur, $hauteur);
                        $blanc = imagecolorallocate($img, 255, 255, 255);
                        $noir = imagecolorallocate($img, 0, 0, 0);
                        $milieuHauteur = ($hauteur / 2) - 8;
                        imagestring($img, 6, strlen($mot) / 2, $milieuHauteur, $mot, $noir);
                        imagerectangle($img, 1, 1, $largeur - 1, $hauteur - 1, $noir); // La bordure

                        $captcha = new Image($mot);
                        $captcha->setImageGD($img, "image/png");
                        return $captcha;
                }

                /** @return un captcha tag en HTML (<img>)
                  *@param string $mode NOMBRE par default, ou MOT pour image avec un mot du dictionnaire etc/locale/dict-lang.txt, ou TEXTE
                  */
                public function captcha($mode = NOMBRE) {
                        $captcha = "";
                        $mot = "";
                        switch ($mode) {
                                case NOMBRE:
                                        $mot = $this->nombre($this->t);
                                        $captcha = HTML_image($this->r->sitemap["e13___image"] . "?captcha=" . strlen($mot));
                                        break;
                                case MOT:
                                        $mot = $this->motListe(file((defined('APP') ? APP : "") . $this->r->r['locale__dict-lang']));
                                        $captcha = HTML_image($this->r->sitemap["e13___image"] . "?captcha=" . strlen($mot));
                                        break;
                                case TEXTE: default:
                                        $captcha = $mot = $this->motListe(file($this->r->r['locale__dict-lang']));
                                        break;
                        }
                        /* le mot est enregistre en session pour l'envoyer a la fonction _image !!  */
                        $_SESSION['captcha'] = $mot;
                        return $captcha;
                }

                /** verification d'un captcha valide (controle insensible a la casse))
                 * @param $retMsg message localise
                 * @return true ou false avec un message
                 */
                public static function verification($page, &$retMsg = "") {
                        $captcha = filter_input(INPUT_POST, 'captcha');
                        if ($captcha) {
                                /* */
                                if (strtolower($captcha) === strtolower($_SESSION['captcha'])) {
                                        $retMsg = $page->r->lang("captchagood", "form");
                                        return TRUE;
                                } else {
                                        $retMsg = $captcha ? $page->r->lang("captchafail", "form") : "";
                                        return FALSE;
                                }
                        } else {
                                return FALSE;
                        }
                }

        }

}
?>
