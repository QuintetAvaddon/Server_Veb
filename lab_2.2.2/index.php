<?php
// ===== BACKEND: Обработка выражения =====

$result = '';
$error = '';
$expression = '';

if (isset($_GET['expr']) && !empty($_GET['expr'])) {
    $expression = urldecode($_GET['expr']);

    // Проверка на безопасность — убираем всё кроме разрешённых символов
    $allowed = '0123456789+-*/().^! piePIElnlogsqrt,';
    $clean = '';
    for ($i = 0; $i < strlen($expression); $i++) {
        if (strpos($allowed, $expression[$i]) !== false) {
            $clean .= $expression[$i];
        }
    }

    if (empty($clean)) {
        $error = 'Ошибка: недопустимые символы в выражении!';
    } else {
        try {
            $result = calculateExpression($clean);
        } catch (Exception $e) {
            $error = 'Ошибка вычисления: ' . $e->getMessage();
        }
    }
}

// ===== Рекурсивные функции для операций =====

function calculateExpression($expr) {
    $expr = trim($expr);

    // Замена констант
    $expr = str_replace(['pi', 'PI', 'Pi'], pi(), $expr);
    $expr = str_replace(['e', 'E'], exp(1), $expr);

    // Обработка функций (sqrt, ln, log, factorial)
    $expr = processFunctions($expr);

    // Обработка скобок рекурсивно
    while (($pos = findMatchingBracket($expr)) !== false) {
        $inner = substr($expr, 1, $pos - 1);
        $innerResult = calculateExpression($inner);
        $expr = $innerResult . substr($expr, $pos + 1);
    }

    // Степень
    $expr = processOperator($expr, '^', 'power');
    // Умножение и деление
    $expr = processOperator($expr, '*/', 'muldiv');
    // Сложение и вычитание
    $expr = processOperator($expr, '+-', 'addsub');

    return floatval($expr);
}

function processFunctions($expr) {
    // sqrt(x)
    while (preg_match('/sqrt\(([^()]+)\)/', $expr, $matches)) {
        $val = calculateExpression($matches[1]);
        $expr = str_replace($matches[0], sqrt($val), $expr);
    }
    // ln(x)
    while (preg_match('/ln\(([^()]+)\)/', $expr, $matches)) {
        $val = calculateExpression($matches[1]);
        $expr = str_replace($matches[0], log($val), $expr);
    }
    // log(x) - log10
    while (preg_match('/log\(([^()]+)\)/', $expr, $matches)) {
        $val = calculateExpression($matches[1]);
        $expr = str_replace($matches[0], log10($val), $expr);
    }
    // factorial x!
    while (preg_match('/(\d+)!/', $expr, $matches)) {
        $expr = str_replace($matches[0], factorial(intval($matches[1])), $expr);
    }
    return $expr;
}

function factorial($n) {
    if ($n < 0) return 0;
    if ($n <= 1) return 1;
    return $n * factorial($n - 1); // рекурсия!
}

function findMatchingBracket($expr) {
    if (empty($expr) || $expr[0] != '(') return false;
    $depth = 1;
    for ($i = 1; $i < strlen($expr); $i++) {
        if ($expr[$i] == '(') $depth++;
        if ($expr[$i] == ')') $depth--;
        if ($depth == 0) return $i;
    }
    return false;
}

function processOperator($expr, $ops, $type) {
    $pattern = '/(-?\d+\.?\d*)\s*([' . preg_quote($ops, '/') . '])\s*(-?\d+\.?\d*)/';

    while (preg_match($pattern, $expr, $matches)) {
        $a = floatval($matches[1]);
        $op = $matches[2];
        $b = floatval($matches[3]);

        switch ($op) {
            case '+': $res = $a + $b; break;
            case '-': $res = $a - $b; break;
            case '*': $res = $a * $b; break;
            case '/': 
                if ($b == 0) throw new Exception('Деление на ноль!');
                $res = $a / $b; 
                break;
            case '^': $res = pow($a, $b); break;
        }

        $expr = str_replace($matches[0], $res, $expr);
    }
    return $expr;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Калькулятор</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Логотип МосПолитеха">
        </div>
        <div class="title">
            <h1>Калькулятор</h1>
            <p>Лабораторная работа по PHP</p>
        </div>
    </header>

    <main>
        <div class="calc-container">
            <div class="display-wrapper">
                <input type="text" id="display" value="<?php echo $expression ? htmlspecialchars($expression) : ''; ?>" readonly placeholder="0">
                <?php if ($result !== '' || $error !== ''): ?>
                    <div class="result-line <?php echo $error ? 'error' : ''; ?>">
                        <?php echo $error ? $error : '= ' . $result; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="buttons-grid">
                <!-- Ряд 1: функции -->
                <button class="btn-func" onclick="addFunc('sqrt')">√</button>
                <button class="btn-func" onclick="addFunc('ln')">ln</button>
                <button class="btn-func" onclick="addFunc('log')">log</button>
                <button class="btn-func" onclick="addOp('^')">x^y</button>
                <button class="btn-func" onclick="addOp('!')">n!</button>

                <!-- Ряд 2: скобки и константы -->
                <button class="btn-func" onclick="addChar('(')">(</button>
                <button class="btn-func" onclick="addChar(')')">)</button>
                <button class="btn-func" onclick="addConst('pi')">π</button>
                <button class="btn-func" onclick="addConst('e')">e</button>
                <button class="btn-clear" onclick="clearDisplay()">C</button>

                <!-- Ряд 3: цифры -->
                <button class="btn-num" onclick="addChar('7')">7</button>
                <button class="btn-num" onclick="addChar('8')">8</button>
                <button class="btn-num" onclick="addChar('9')">9</button>
                <button class="btn-op" onclick="addOp('/')">÷</button>
                <button class="btn-op" onclick="addOp('*')">×</button>

                <!-- Ряд 4 -->
                <button class="btn-num" onclick="addChar('4')">4</button>
                <button class="btn-num" onclick="addChar('5')">5</button>
                <button class="btn-num" onclick="addChar('6')">6</button>
                <button class="btn-op" onclick="addOp('-')">−</button>
                <button class="btn-op" onclick="addOp('+')">+</button>

                <!-- Ряд 5 -->
                <button class="btn-num" onclick="addChar('1')">1</button>
                <button class="btn-num" onclick="addChar('2')">2</button>
                <button class="btn-num" onclick="addChar('3')">3</button>
                <button class="btn-num" onclick="addChar('.')">.</button>
                <button class="btn-equals" onclick="calculate()">=</button>

                <!-- Ряд 6 -->
                <button class="btn-num zero" onclick="addChar('0')">0</button>
                <button class="btn-num" onclick="addChar('00')">00</button>
                <button class="btn-op" onclick="backspace()">⌫</button>
                <button class="btn-equals" onclick="calculate()">=</button>
            </div>

            <div class="info">
                <p>Ввод: <span id="currentExpr"></span></p>
                <p>JS → POST → PHP (рекурсия) → GET → результат</p>
            </div>
        </div>
    </main>

    <footer>
        <p>задание для самостоятельной работы</p>
    </footer>

    <script>
        let display = document.getElementById('display');
        let currentExpr = document.getElementById('currentExpr');

        function addChar(c) {
            display.value += c;
            updateInfo();
        }

        function addOp(op) {
            display.value += ' ' + op + ' ';
            updateInfo();
        }

        function addFunc(func) {
            display.value += func + '(';
            updateInfo();
        }

        function addConst(c) {
            display.value += c;
            updateInfo();
        }

        function clearDisplay() {
            display.value = '';
            updateInfo();
            window.history.replaceState({}, document.title, window.location.pathname);
            location.reload();
        }

        function backspace() {
            display.value = display.value.slice(0, -1);
            updateInfo();
        }

        function updateInfo() {
            currentExpr.textContent = display.value || 'пусто';
        }

        function calculate() {
            let expr = display.value.trim();
            if (!expr) return;

            window.location.href = '?expr=' + encodeURIComponent(expr);
        }

        // Бонус: ввод с клавиатуры
        document.addEventListener('keydown', function(e) {
            if (e.key >= '0' && e.key <= '9') addChar(e.key);
            else if (e.key === '.') addChar('.');
            else if (e.key === '+') addOp('+');
            else if (e.key === '-') addOp('-');
            else if (e.key === '*') addOp('*');
            else if (e.key === '/') addOp('/');
            else if (e.key === '^') addOp('^');
            else if (e.key === '(') addChar('(');
            else if (e.key === ')') addChar(')');
            else if (e.key === 'Enter') calculate();
            else if (e.key === 'Backspace') backspace();
            else if (e.key === 'Escape') clearDisplay();
        });

        updateInfo();
    </script>
</body>
</html>