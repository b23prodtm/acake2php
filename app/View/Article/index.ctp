<!--vue Article index.ctp-->
<?php
include_once $GLOBALS['include__php_tbl.class.inc'];
$t = new Tableau(count($articles) + 1, 3, __d('article', 'db_articles'));
$t->setContenu_Ligne(0, array(__d('article', "categorie"), __d('article', "article"), __d('article', "published")));

/* On fait un tour des $articles array, et sortie des infos articl */

for ($i = 1; $i < count($articles) + 1; $i++) {
        $article = $articles[$i - 1];
        $t->setContenu_Cellule($i, 0, $article['Article']['id__categorie']);
        $t->setContenu_Cellule($i, 1, $this->Html->link($article['Article']['id'], array('controller' => 'article',
                    'action' => 'view',
                    array($article['Article']['id'])))
        );
        $t->setContenu_Cellule($i, 2, $article['Article']['published']);
}
echo $t->fin();
?><hr/>
<?php
echo $this->Html->link(
        __d('article', 'Add an article'), array('controller' => 'article', 'action' => 'add')
);
?>
