<?php
$message = '';
$messageClass = '';
$editId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Получаем список для выбора
$listResult = mysqli_query($mysqli, "SELECT id, surname, name FROM contacts ORDER BY surname, name");

// Если выбрана запись - загружаем её
$row = [
    'surname' => '', 'name' => '', 'lastname' => '', 'gender' => '',
    'birth_date' => '', 'phone' => '', 'address' => '', 'email' => '', 'comment' => ''
];

if ($editId > 0) {
    $editResult = mysqli_query($mysqli, "SELECT * FROM contacts WHERE id = $editId");
    if ($editResult && mysqli_num_rows($editResult) > 0) {
        $row = mysqli_fetch_assoc($editResult);
    }
} else {
    // Берём первую запись по умолчанию
    $firstResult = mysqli_query($mysqli, "SELECT * FROM contacts ORDER BY surname, name LIMIT 1");
    if ($firstResult && mysqli_num_rows($firstResult) > 0) {
        $row = mysqli_fetch_assoc($firstResult);
        $editId = $row['id'];
    }
}

// Обработка сохранения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button'])) {
    $id = intval($_POST['id']);
    $surname = mysqli_real_escape_string($mysqli, $_POST['surname']);
    $name = mysqli_real_escape_string($mysqli, $_POST['name']);
    $lastname = mysqli_real_escape_string($mysqli, $_POST['lastname']);
    $gender = mysqli_real_escape_string($mysqli, $_POST['gender']);
    $birth_date = mysqli_real_escape_string($mysqli, $_POST['birth_date']);
    $phone = mysqli_real_escape_string($mysqli, $_POST['phone']);
    $address = mysqli_real_escape_string($mysqli, $_POST['address']);
    $email = mysqli_real_escape_string($mysqli, $_POST['email']);
    $comment = mysqli_real_escape_string($mysqli, $_POST['comment']);

    $query = "UPDATE contacts SET 
        surname='$surname', name='$name', lastname='$lastname', gender='$gender',
        birth_date='$birth_date', phone='$phone', address='$address', email='$email', comment='$comment'
        WHERE id=$id";

    if (mysqli_query($mysqli, $query)) {
        $message = 'Запись обновлена';
        $messageClass = 'success';
        // Перезагружаем данные
        $editResult = mysqli_query($mysqli, "SELECT * FROM contacts WHERE id = $id");
        $row = mysqli_fetch_assoc($editResult);
    } else {
        $message = 'Ошибка: запись не обновлена';
        $messageClass = 'error';
    }
}
?>

<?php if ($message): ?>
    <div class="<?php echo $messageClass; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<!-- Список записей -->
<div class="edit-list">
    <?php while ($listRow = mysqli_fetch_assoc($listResult)): ?>
        <?php $currentClass = ($listRow['id'] == $editId) ? 'currentRow' : ''; ?>
        <a href="?action=edit&id=<?php echo $listRow['id']; ?>" class="div-edit <?php echo $currentClass; ?>">
            <?php echo htmlspecialchars($listRow['surname'] . ' ' . $listRow['name']); ?>
        </a>
    <?php endwhile; ?>
</div>

<form method="POST" action="?action=edit&id=<?php echo $editId; ?>">
    <input type="hidden" name="id" value="<?php echo $editId; ?>">
    <div class="column">
        <div class="add">
            <label>Фамилия</label> <input type="text" name="surname" value="<?php echo htmlspecialchars($row['surname']); ?>" required>
        </div>
        <div class="add">
            <label>Имя</label> <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
        </div>
        <div class="add">
            <label>Отчество</label> <input type="text" name="lastname" value="<?php echo htmlspecialchars($row['lastname']); ?>">
        </div>
        <div class="add">
            <label>Пол</label> 
            <select name="gender">
                <option value="<?php echo $row['gender']; ?>"><?php echo $row['gender']; ?></option>
                <option value="мужской">мужской</option>
                <option value="женский">женский</option>
            </select>
        </div>
        <div class="add">
            <label>Дата рождения</label> <input type="date" name="birth_date" value="<?php echo $row['birth_date']; ?>">
        </div>
        <div class="add">
            <label>Телефон</label> <input type="text" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>">
        </div>
        <div class="add">
            <label>Адрес</label> <input type="text" name="address" value="<?php echo htmlspecialchars($row['address']); ?>">
        </div>
        <div class="add">
            <label>Email</label> <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
        </div>
        <div class="add">
            <label>Комментарий</label> <textarea name="comment"><?php echo htmlspecialchars($row['comment']); ?></textarea>
        </div>
        <button type="submit" name="button" value="Сохранить" class="form-btn">Сохранить</button>
    </div>
</form>