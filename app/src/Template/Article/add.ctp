<!-- Fichier : /app/View/Article/add.ctp -->
<?php
$this->Flash->render();
function getTypes() {
        return $mtypes = array("jpeg", "png", 'gif');
}

if (filter_input(INPUT_GET, 'images')) {
        echo $this->HTML->tag("h1", __d('article', 'Upload some images'), array());
        if (!filter_input(INPUT_POST, 'n_img')) {
                $n = 5;
        } else {
                $n = filter_input(INPUT_POST, 'n_img');
        }
        $f = new Formulaire(__d('article', 'Upload'). ' '.__dn('article', '%s image', 'images', $i + 1), $url . "/?images=publie");
        for ($i = 0; $i < $n; $i++) {
                $champ[] = new ChampFile("image_$i", __d('article', 'file format image/%s', getTypes()[1]));
                $f->ajouterChamp($champ[$i]);
        }
        $valider = new ChampValider(__d("article", 'Upload'));
        $f->ajouterChamp($valider);
        echo $f->fin();
} else {
        echo $this->HTML->tag("h1", __d('article', 'Add an article'), array());
        echo $this->Form->create('Article', array('type' => 'file'));
        echo $this->Form->input('entete', array("label" => __d('article', 'Header')));
        echo $this->Form->input('published', array("label" => __d('article', 'Published')));
        echo $this->Form->input('corps', array('rows' => '10', "label" => __d('article', 'Body')));
        echo $this->Form->input('date', array('type' => 'hidden'));
        echo $this->Form->end(__d('article', 'Save and continue with images'));
}
?>