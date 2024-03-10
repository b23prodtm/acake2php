# README #

Everything of an easy configuration resides in the CMS in e13/etc/ folder.
This project was made with the help of the software Netbeans IDE and the PHP 5 plugin code editor.
### Website demo
A preview of this CMS is run by the [www.b23prodtm.info](http://www.b23prodtm.info) website.

### CakePHP cartridge
As of Openshift 3 Starter edition, the new containers replace old cartridges that [boots images online](https://docs.openshift.com/online/getting_started/basic_walkthrough.html).
Project php-cms is undergoing a translation to the MVC pattern as designed by the CakePHP framework. Upcoming updates may be cloned from [Github's](https://github.com/b23prodtm/acake2php.git) by selecting the master branch.

### FAQ
1. The debug log says *Undefined key in content_lang ... for <some__property>*, what does it mean ?
Something must be wrong with the properties set in etc/menu.properties file. That key is not found in etc/locale/content_lang[en|fr|de].properties file. Please add the key=value some__property=a text in content_lang.properties at least to suppress the *[NOTICE]*.

### Souceforge.net Open Source Hub
This is a code repository for the Sourceforge.net's [pohse-php-cms project](https://sourceforge.net/projects/php-cms.pohse.p/)
[![Download PHP CMS](https://sourceforge.net/sflogo.php?type=10&group_id=163092)](https://sourceforge.net/p/pohse/php-cms)
### Cake PHP Framework
Original [cakephp-ex container image from Openshift](https://github.com/openshift/cakephp-ex).
[![CakePhp](https://cakephp.org/img/trademarks/logo-4.jpg)](http///www.cakephp.org)

# Author #
Tiana Rakoto.
