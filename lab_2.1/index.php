<?php
// ===== Серверный динамический контент =====

// 1. Приветствие на разных языках (меняется при каждой загрузке страницы)
$greetings = [
    'ru' => 'Привет, Мир!',
    'en' => 'Hello, World!',
    'es' => '¡Hola, Mundo!',
    'fr' => 'Bonjour, le Monde!',
    'de' => 'Hallo, Welt!',
    'it' => 'Ciao, Mondo!',
    'zh' => '你好，世界！',
    'ja' => 'こんにちは、世界！'
];
$languages = array_keys($greetings);
$random_lang = $languages[array_rand($languages)];
$current_greeting = $greetings[$random_lang];

// 2. Серверное время (динамически генерируется на сервере)
$server_time = date('d.m.Y H:i:s');
$server_timezone = date_default_timezone_get();

// 3. Информация о сервере
$server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';
$server_software = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
$php_version = PHP_VERSION;

// 4. Счётчик посещений (сохраняется в файл — динамическое состояние)
$counter_file = 'visit_counter.txt';
if (file_exists($counter_file)) {
    $visits = (int)file_get_contents($counter_file);
} else {
    $visits = 0;
}
$visits++;
file_put_contents($counter_file, $visits);

// 5. Определение дня недели
$days = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];
$day_of_week = $days[date('w')];

// 6. Случайный цвет фона для контент-блока (генерируется на сервере)
$colors = ['#e8f6f3', '#fef9e7', '#ebf5fb', '#f5eef8', '#eafaf1', '#fdf2e9'];
$random_color = $colors[array_rand($colors)];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello, World!</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <!-- Замени "logo.png" на путь к своему логотипу МосПолитеха -->
            <img src="logo.png" alt="Логотип МосПолитеха">
        </div>
        <div class="title">
            <h1>Hello, World!</h1>
            <p>Лабораторная работа по веб-разработке</p>
        </div>
    </header>

    <main>
        <div class="content-box" style="background-color: <?php echo $random_color; ?>">
            <h2><?php echo $current_greeting; ?></h2>
            <p class="lang-tag">Язык: <?php echo strtoupper($random_lang); ?></p>

            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Серверное время:</span>
                    <span class="value"><?php echo $server_time; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Часовой пояс:</span>
                    <span class="value"><?php echo $server_timezone; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Сегодня:</span>
                    <span class="value"><?php echo $day_of_week; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Номер посещения:</span>
                    <span class="value highlight">#<?php echo $visits; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Сервер:</span>
                    <span class="value"><?php echo htmlspecialchars($server_software); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">PHP версия:</span>
                    <span class="value"><?php echo $php_version; ?></span>
                </div>
            </div>

            <p class="refresh-hint">Обновите страницу — контент изменится!</p>
        </div>
    </main>

    <footer>
        <p>задание для самостоятельной работы</p>
    </footer>
</body>
</html>