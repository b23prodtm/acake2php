<?php

//        echo $this->Markdown->transform(file_get_contents($pUrl));
$r = new Index($this);
require $GLOBALS['include__php_tbl.class.inc'];
$t = new Tableau(count($articles) + 1, 3, $r->lang("page", "content"));
$t->setContenu_Ligne(0, $r->lang(array("categorie", "article", "published"), "content"));

/* On fait un tour des $articles array, et sortie des infos articl */

for ($i = 1; $i < count($articles) + 1; $i++) {
        $article = $articles[$i - 1];
        $t->setContenu_Cellule($i, 0, $article['Article']['fk_reference_categorie']);
        $t->setContenu_Cellule($i, 1, $this->Html->link($article['Article']['id'], array('controller' => 'article',
                    'action' => 'view',
                    array($article['Article']['id'])))
        );
        $t->setContenu_Cellule($i, 2, $article['Article']['published']);
}
$t->fin(1);
