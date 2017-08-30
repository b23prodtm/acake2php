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

/**
 * @link https://openclassrooms.com/courses/les-captchas-anti-bot création originale d'un captcha anti-bot pour php
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */
global $ClasseCapt;
if (!isset($ClasseCapt)) {
        $ClasseCapt = 1;
        require($GLOBALS['include__php_image.class.inc']);
        class Captcha {
                var $t;
                public function __construct($tailleMot) {
                        /* @var $tailleMot integer not greater than the dict-lang.txt longest word */
                        $this->t = $tailleMot;
                }
                /** générer une chaîne de caractères alphabétiques aléatoires. */
                function motHasard($n)
                {
                        $lettres = array_merge(range('a','z'),range('A','Z'),range('0','9'));
                        $nl = count($lettres)-1;
                        $mot = '';
                        for ($i = 0; $i < $n; ++$i) {
                                $mot .= $lettres[mt_rand(0, $nl)];
                        }
                        return $mot;
                }
                
                /** recherche aléatoire et recursive mot dans un dictionnaire de mots */
                function motListe($liste, $i)
                {
                        $c = count($liste);
                        $mot = trim($liste[array_rand($liste)]);
                        /** le mot n'est pas de la longueur desiree */
                        if(strlen($mot) != $this->t) {
                                if ($c > $i + 1) {
                                        $mot = $this->motListe($liste, $i + 1);
                                } else  {/** la liste a été parcourue '$c' fois, retourne un mot au hasard */
                                        $mot = $this->motHasard ($this->t);
                                }
                        }
                        return $mot;
                }

                public function captcha()
                {
                        $mot = $this->motListe(file($GLOBALS['locale__dict-lang.txt']),0);
                        $_SESSION['captcha'] = $mot;
                        return $mot;
                }
                
                /** verificatoin d'un captcha valide                
                 */
                public static function verification($page, &$retMsg = "")
                {
                        $captcha = filter_input(INPUT_POST,'captcha');
                        if ($captcha) {
                                if ($captcha == $_SESSION['captcha']) {
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