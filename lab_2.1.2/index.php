<?php
// ===== Решатель уравнений вида a * X = b =====

$result = null;
$error = '';
$steps = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $a = isset($_POST['a']) ? (float)$_POST['a'] : 0;
    $b = isset($_POST['b']) ? (float)$_POST['b'] : 0;
    $operator = $_POST['operator'] ?? '*';

    // Валидация
    if ($a == 0) {
        $error = 'Ошибка: коэффициент a не может быть равен 0 (деление на ноль)!';
    } else {
        // Определяем оператор и решаем
        switch ($operator) {
            case '*':
                $result = $b / $a;
                $steps = [
                    "Уравнение: {$a} * X = {$b}",
                    "Оператор: умножение (*)",
                    "Неизвестная X находится справа от оператора",
                    "Для решения делим обе части на {$a}",
                    "X = {$b} / {$a}",
                    "X = {$result}"
                ];
                break;
            case '+':
                $result = $b - $a;
                $steps = [
                    "Уравнение: {$a} + X = {$b}",
                    "Оператор: сложение (+)",
                    "X = {$b} - {$a}",
                    "X = {$result}"
                ];
                break;
            case '-':
                $result = $a - $b;
                $steps = [
                    "Уравнение: {$a} - X = {$b}",
                    "Оператор: вычитание (-)",
                    "X = {$a} - {$b}",
                    "X = {$result}"
                ];
                break;
            case '/':
                $result = $a * $b;
                $steps = [
                    "Уравнение: {$a} / X = {$b}",
                    "Оператор: деление (/)",
                    "X = {$a} / {$b}",
                    "X = {$result}"
                ];
                break;
        }
    }
}

// Значения по умолчанию для твоего варианта (4 * X = 36)
$default_a = isset($_POST['a']) ? $_POST['a'] : 4;
$default_b = isset($_POST['b']) ? $_POST['b'] : 36;
$default_op = isset($_POST['operator']) ? $_POST['operator'] : '*';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Решатель уравнений</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Логотип МосПолитеха">
        </div>
        <div class="title">
            <h1>Решатель уравнений</h1>
            <p>Лабораторная работа 2.1.2</p>
        </div>
    </header>

    <main>
        <div class="content-box">
            <h2>Введите уравнение</h2>

            <form method="POST" action="" class="equation-form">
                <div class="equation-row">
                    <input type="number" name="a" value="<?php echo $default_a; ?>" step="any" required>
                    <select name="operator" class="operator-select">
                        <option value="*" <?php echo $default_op == '*' ? 'selected' : ''; ?>>*</option>
                        <option value="+" <?php echo $default_op == '+' ? 'selected' : ''; ?>>+</option>
                        <option value="-" <?php echo $default_op == '-' ? 'selected' : ''; ?>>-</option>
                        <option value="/" <?php echo $default_op == '/' ? 'selected' : ''; ?>>/</option>
                    </select>
                    <span class="x-label">X</span>
                    <span class="equals">=</span>
                    <input type="number" name="b" value="<?php echo $default_b; ?>" step="any" required>
                </div>

                <button type="submit" class="btn-submit">Решить уравнение</button>
            </form>

            <?php if ($error): ?>
                <div class="error-box">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($result !== null && !$error): ?>
                <div class="result-box">
                    <h3>🔍 Анализ уравнения</h3>
                    <div class="steps">
                        <?php foreach ($steps as $step): ?>
                            <div class="step"><?php echo $step; ?></div>
                        <?php endforeach; ?>
                    </div>

                    <div class="final-result">
                        <span class="result-label">Ответ:</span>
                        <span class="result-value">X = <?php echo $result; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="block-schema">
                <h3>📊 Блок-схема алгоритма</h3>
                <img src="block_schema.png" alt="Блок-схема алгоритма решения уравнения">
            </div>
        </div>
    </main>

    <footer>
        <p>задание для самостоятельной работы</p>
    </footer>
</body>
</html>