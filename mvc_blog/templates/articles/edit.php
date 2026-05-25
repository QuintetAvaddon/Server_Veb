<?php include __DIR__ . '/../header.php'; ?>

<h1>Edit Article</h1>

<form action="/article/<?= $article->getId() ?>/edit" method="post">
    <div>
        <label for="name">Title:</label><br>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($article->getName()) ?>" style="width: 100%; padding: 8px;">
    </div>
    <br>
    <div>
        <label for="text">Text:</label><br>
        <textarea id="text" name="text" rows="10" style="width: 100%; padding: 8px;"><?= htmlspecialchars($article->getText()) ?></textarea>
    </div>
    <br>
    <div>
        <button type="submit" style="padding: 10px 20px; background: darkgreen; color: white; border: none; cursor: pointer;">Save</button>
        <a href="/articles/<?= $article->getId() ?>" style="margin-left: 10px;">Cancel</a>
    </div>
</form>

<?php include __DIR__ . '/../footer.php'; ?>
