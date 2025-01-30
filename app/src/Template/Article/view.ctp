<?php $r = new Index($this); ?>
<!-- Fichier : /app/View/Articles/view.ctp -->

<h1><?php echo h($article['Article']['entete']); ?></h1>

<p><small><?php echo $article['Article']['date']; ?></small></p>

<p><?php echo h($article['Article']['corps']); ?></p>