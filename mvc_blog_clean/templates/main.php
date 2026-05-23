<?php include __DIR__ . '/header.php'; ?>

<?php foreach ($articles as $article): ?>
    <h2><?= htmlspecialchars($article['title']) ?></h2>
    <p><?= htmlspecialchars($article['text']) ?></p>
    <hr>
<?php endforeach; ?>

<?php include __DIR__ . '/footer.php'; ?>
