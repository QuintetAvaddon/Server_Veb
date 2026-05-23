<?php
$message = '';
$messageClass = '';

// Удаление
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $getResult = mysqli_query($mysqli, "SELECT surname FROM contacts WHERE id = $id");
    $surname = '';
    if ($getResult && mysqli_num_rows($getResult) > 0) {
        $surname = mysqli_fetch_assoc($getResult)['surname'];
    }

    if (mysqli_query($mysqli, "DELETE FROM contacts WHERE id = $id")) {
        $message = "Запись с фамилией $surname удалена";
        $messageClass = 'success';
    } else {
        $message = 'Ошибка при удалении';
        $messageClass = 'error';
    }
}

// Список для удаления
$result = mysqli_query($mysqli, "SELECT id, surname, name, lastname FROM contacts ORDER BY surname, name");
?>

<?php if ($message): ?>
    <div class="<?php echo $messageClass; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="delete-list">
    <h3>Выберите запись для удаления:</h3>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <?php $initials = mb_substr($row['name'], 0, 1) . '.' . mb_substr($row['lastname'], 0, 1) . '.'; ?>
        <a href="?action=delete&id=<?php echo $row['id']; ?>" class="div-edit" onclick="return confirm('Удалить запись <?php echo htmlspecialchars($row['surname']); ?>?')">
            <?php echo htmlspecialchars($row['surname'] . ' ' . $initials); ?>
        </a>
    <?php endwhile; ?>
</div>