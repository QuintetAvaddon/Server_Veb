<?php include __DIR__ . '/../header.php'; ?>

<h1>Articles</h1>

<?php foreach ($articles as $article): ?>
    <h2><a href="/articles/<?= $article->getId() ?>"><?= htmlspecialchars($article->getName()) ?></a></h2>
    <p><?= htmlspecialchars(mb_substr($article->getText(), 0, 100)) ?>...</p>
    <hr>
<?php endforeach; ?>

<?php include __DIR__ . '/../footer.php'; ?>
