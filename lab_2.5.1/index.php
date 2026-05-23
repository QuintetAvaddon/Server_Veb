<?php
// Подключение к БД
$mysqli = mysqli_connect('localhost', 'root', '', 'notebook');
if (!$mysqli) {
    die('Ошибка подключения: ' . mysqli_connect_error());
}

// Создание таблицы если не существует
$create_table = "CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    surname VARCHAR(100) NOT NULL,
    name VARCHAR(100) NOT NULL,
    lastname VARCHAR(100),
    gender VARCHAR(20),
    birth_date DATE,
    phone VARCHAR(20),
    address VARCHAR(255),
    email VARCHAR(100),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($mysqli, $create_table);

// Определяем активный пункт меню
$action = isset($_GET['action']) ? $_GET['action'] : 'view';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Записная книжка</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Логотип МосПолитеха">
        </div>
        <div class="title">
            <h1>Записная книжка</h1>
            <p>Лабораторная работа по PHP + MySQL</p>
        </div>
    </header>

    <?php include 'menu.php'; ?>

    <main>
        <?php
        switch ($action) {
            case 'view':
                include 'viewer.php';
                break;
            case 'add':
                include 'add.php';
                break;
            case 'edit':
                include 'edit.php';
                break;
            case 'delete':
                include 'delete.php';
                break;
            default:
                include 'viewer.php';
        }
        ?>
    </main>

    <footer>
        <p>задание для самостоятельной работы</p>
    </footer>
</body>
</html>
<?php mysqli_close($mysqli); ?>