<?php include __DIR__ . '/../header.php'; ?>

<h1><?= htmlspecialchars($article->getName()) ?></h1>
<p><?= nl2br(htmlspecialchars($article->getText())) ?></p>
<hr>
<p><strong>Author:</strong> <?= htmlspecialchars($article->getAuthor()->getNickname()) ?></p>
<p><small>Created: <?= htmlspecialchars($article->getCreatedAt()) ?></small></p>
<p><a href="/article/<?= $article->getId() ?>/edit">Edit article</a></p>

<?php include __DIR__ . '/../footer.php'; ?>
