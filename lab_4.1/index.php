<?php
// ===== Обработка POST-запроса =====
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $type = htmlspecialchars($_POST['type'] ?? '');
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));
    $response_types = isset($_POST['response_type']) ? $_POST['response_type'] : [];

    // Сохраняем в файл (серверная обработка)
    $data = "=== Новое обращение ===
";
    $data .= "Дата: " . date('d.m.Y H:i:s') . "
";
    $data .= "Имя: $name
";
    $data .= "Email: $email
";
    $data .= "Тип: $type
";
    $data .= "Сообщение: $message
";
    $data .= "Ответ: " . implode(', ', $response_types) . "
";
    $data .= "------------------------

";

    file_put_contents('feedback.txt', $data, FILE_APPEND);
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Логотип МосПолитеха">
        </div>
        <div class="title">
            <h1>Feedback form</h1>
            <p>Лабораторная работа по веб-разработке</p>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2>Форма обратной связи</h2>

            <?php if ($success): ?>
                <div class="success-message">
                    ✅ Обращение успешно отправлено и сохранено на сервере!
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Имя пользователя</label>
                    <input type="text" id="name" name="name" placeholder="Введите ваше имя" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail пользователя</label>
                    <input type="email" id="email" name="email" placeholder="example@mail.ru" required>
                </div>

                <div class="form-group">
                    <label for="type">Тип обращения</label>
                    <select id="type" name="type" required>
                        <option value="" disabled selected>Выберите тип обращения</option>
                        <option value="complaint">Жалоба</option>
                        <option value="suggestion">Предложение</option>
                        <option value="gratitude">Благодарность</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Текст обращения</label>
                    <textarea id="message" name="message" rows="5" placeholder="Опишите ваше обращение..." required></textarea>
                </div>

                <div class="form-group checkbox-group">
                    <p class="checkbox-label">Вариант ответа:</p>
                    <div class="checkbox-options">
                        <label class="checkbox-item">
                            <input type="checkbox" name="response_type[]" value="sms">
                            <span>СМС</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="response_type[]" value="email">
                            <span>E-mail</span>
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Отправить</button>
                </div>
            </form>

            <div class="page-link">
                <a href="page2.php" class="btn-link">Перейти на страницу 2 →</a>
            </div>
        </div>
    </main>

    <footer>
        <p>задание для самостоятельной работы</p>
    </footer>
</body>
</html>