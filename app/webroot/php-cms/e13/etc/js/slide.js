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


/** initialise un script js pour cacher l'element a gauche (c.f. la classe de style .slide) */
function hM(element, slide, hidden = 'hidden', position = 'absolute') {
    element.style.left = slide;
    element.style.position = position;
    element.style.visibility = hidden;
}
/** la fonction immediate modifie le statut de visibilite et la position de l'element */
function sM(element, timeout, setPosition = 'relative') {
    var hidden = element.style.visibility;
    element.style.visibility = 'visible';
    var position = element.style.position;
    element.style.position = setPosition;
    var slide = element.style.left;
    element.style.left = 0;
    setTimeout(function(){hM(element, slide, hidden, position);}, timeout);
}