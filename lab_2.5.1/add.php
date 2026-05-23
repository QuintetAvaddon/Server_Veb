<?php
$message = '';
$messageClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button'])) {
    $surname = mysqli_real_escape_string($mysqli, $_POST['surname']);
    $name = mysqli_real_escape_string($mysqli, $_POST['name']);
    $lastname = mysqli_real_escape_string($mysqli, $_POST['lastname']);
    $gender = mysqli_real_escape_string($mysqli, $_POST['gender']);
    $birth_date = mysqli_real_escape_string($mysqli, $_POST['birth_date']);
    $phone = mysqli_real_escape_string($mysqli, $_POST['phone']);
    $address = mysqli_real_escape_string($mysqli, $_POST['address']);
    $email = mysqli_real_escape_string($mysqli, $_POST['email']);
    $comment = mysqli_real_escape_string($mysqli, $_POST['comment']);

    $query = "INSERT INTO contacts (surname, name, lastname, gender, birth_date, phone, address, email, comment) 
              VALUES ('$surname', '$name', '$lastname', '$gender', '$birth_date', '$phone', '$address', '$email', '$comment')";

    if (mysqli_query($mysqli, $query)) {
        $message = 'Запись добавлена';
        $messageClass = 'success';
    } else {
        $message = 'Ошибка: запись не добавлена';
        $messageClass = 'error';
    }
}
?>

<?php if ($message): ?>
    <div class="<?php echo $messageClass; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" action="?action=add">
    <div class="column">
        <div class="add">
            <label>Фамилия</label> <input type="text" name="surname" placeholder="Фамилия" required>
        </div>
        <div class="add">
            <label>Имя</label> <input type="text" name="name" placeholder="Имя" required>
        </div>
        <div class="add">
            <label>Отчество</label> <input type="text" name="lastname" placeholder="Отчество">
        </div>
        <div class="add">
            <label>Пол</label> 
            <select name="gender">
                <option value="">Выберите</option>
                <option value="мужской">мужской</option>
                <option value="женский">женский</option>
            </select>
        </div>
        <div class="add">
            <label>Дата рождения</label> <input type="date" name="birth_date">
        </div>
        <div class="add">
            <label>Телефон</label> <input type="text" name="phone" placeholder="Телефон">
        </div>
        <div class="add">
            <label>Адрес</label> <input type="text" name="address" placeholder="Адрес">
        </div>
        <div class="add">
            <label>Email</label> <input type="email" name="email" placeholder="Email">
        </div>
        <div class="add">
            <label>Комментарий</label> <textarea name="comment" placeholder="Краткий комментарий"></textarea>
        </div>
        <button type="submit" name="button" value="Добавить" class="form-btn">Добавить</button>
    </div>
</form>