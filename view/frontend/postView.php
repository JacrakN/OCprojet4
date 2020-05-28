<?php $title = htmlspecialchars($post['title']); ?>

<?php ob_start(); ?>
<h1>"Billet simple pour l'Alaska"</h1>
<p><a href="index.php">Retour à la liste des billets</a></p>

<div class="news">
    <h3>
        <?= htmlspecialchars($post['title']) ?>
        <em>le <?= $post['creation_date_fr'] ?></em>
    </h3>
    
    <p>
        <?= nl2br(htmlspecialchars($post['content'])) ?>
    </p>
</div>

<h2>Commentaires</h2>

<form action="index.php?action=addComment&amp;id=<?= $post['id'] ?>" method="post">
    <div>
        <label for="author">Auteur</label><br />
        <input type="text" id="author" name="author" />
    </div>
    <div>
        <label for="comment">Commentaire</label><br />
        <textarea id="comment" name="comment"></textarea>
    </div>
    <div>
        <input type="submit" />
    </div>
</form>

<?php
while ($comment = $comments->fetch())
{
?>
    <p><strong><?= htmlspecialchars($comment['author']) ?></strong> <span class="coms-date">le <?= $comment['comment_date_fr'] ?></span></p>

    <p><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
    <p>
        <a href="index.php?action=reportComment&amp;id=<?= $comment['id'] ?>&amp;postid=<?= $post['id'] ?>" class="coms-options">⚐ Signaler</a>
          <a href="index.php?action=printComment&amp;id=<?= $comment['id'] ?>" class="coms-options">✎ Modifier</a> 
          <a href="index.php?action=deleteComment&amp;id=<?= $comment['id'] ?>&amp;postid=<?= $post['id'] ?>" class="coms-options">(-) Supprimer</a>
    </p>
    <br>
<?php
}
?>
<?php $content = ob_get_clean(); ?>

<?php require('template.php'); ?>