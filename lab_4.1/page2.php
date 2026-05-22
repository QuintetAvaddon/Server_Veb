<?php
// ===== Функция get_headers() на PHP =====
$url = 'https://httpbin.org/get';
$headers = get_headers($url, 1);

// Форматируем для красивого вывода
$formatted_headers = print_r($headers, true);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Headers Result</title>
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
            <h2>Результат работы функции get_headers()</h2>

            <div class="form-group">
                <label>URL:</label>
                <p class="url-display"><?php echo htmlspecialchars($url); ?></p>
            </div>

            <div class="form-group">
                <label for="headers-result">HTTP-заголовки ответа:</label>
                <textarea id="headers-result" rows="20" readonly><?php echo htmlspecialchars($formatted_headers); ?></textarea>
            </div>

            <div class="page-link">
                <a href="index.php" class="btn-link">← Вернуться на страницу 1</a>
            </div>
        </div>
    </main>

    <footer>
        <p>задание для самостоятельной работы</p>
    </footer>
</body>
</html>