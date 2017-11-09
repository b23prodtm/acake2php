<?php
$this->Flash->render();
$r = new Index($this);
require_once $GLOBALS['include__php_module_html.inc'];

/* ----- les différentes fonctionnalités ------ */
?><center><b><?php echo __d('admin', '"About us" zone'); ?></b></center>
<?php
/* ----- (1) ------- */
$liste = HTML_listeDebut();
/* ---- (1) --- */
$liste .= HTML_listeElement(HTML_lien($r->sitemap["activites__write"], __d('article', 'Add an article')));
/* ---- (2) --- */
$liste .= HTML_listeElement(HTML_lien($r->sitemap["activites__write"] . "?images", __d('article', 'Upload some images')));
/* ---- (3) --- */
$liste .= HTML_listeElement(HTML_lien($r->sitemap["activites__edit"], __d('article', 'Edit an article')));
/* ---- (4) --- */
$liste .= HTML_listeElement(HTML_lien($r->sitemap["activites__edit"] . "?images", __d('article', 'Edit an image')));
$liste .= HTML_listeFin();
echo $liste;
?>
<hr/>
<?php
require $GLOBALS['include__php_tbl.class.inc'];
$t = new Tableau(count($articles) + 1, 5, __d('article', 'db_articles'));
$t->setContenu_Ligne(0, array(__d('article', 'category'),
    __d('article', 'id'),
    __d('article', 'created'),
    __d('article', 'published'),
    __d('article', 'edition')));

/* On fait un tour des $articles array, et extractuib des donnees articles */

for ($i = 1; $i < count($articles) + 1; $i++) {
        $article = $articles[$i - 1];
        $t->setContenu_Cellule($i, 0, $article['Article']['fk_reference_categorie']);
        $t->setContenu_Cellule($i, 1, $this->Html->link($article['Article']['id'], array('controller' => 'article',
                    'action' => 'view',
                    array($article['Article']['id'])))
        );
        $t->setContenu_Cellule($i, 2, $article['Article']['date']);
        $t->setContenu_Cellule($i, 3, $article['Article']['published']);
        /*
         * Utiliser postLink() permet de créer un lien qui utilise du Javascript pour supprimer notre post en faisant une requête POST. Autoriser la suppression par une requête GET est dangereux à cause des robots d’indexation qui peuvent tous les supprimer.
         */
        $t->setContenu_Cellule($i, 4, $this->Form->postLink(
                        __d('article', 'Delete'), array('action' => 'delete',
                    $post['Article']['id']), array('confirm' => __d('article', 'Are you sure to delete article with id: %s?', h($article['Article']['id'])))) . $this->Html->link(
                        __d('article', 'Edit'), array('action' => 'edit',
                    $article['Article']['id'])
        ));
}
echo $t->fin();

/** TODO : gestion des images chargees en cache */
?>