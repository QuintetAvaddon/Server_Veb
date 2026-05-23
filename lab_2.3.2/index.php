<?php
include 'trig.php';

$result = '';
$error = '';
$expression = '';

if (isset($_GET['expr']) && !empty($_GET['expr'])) {
    $expression = urldecode($_GET['expr']);
    

    $clean = preg_replace('/[^\-0-9+*.()^!\s\/]/', '', $expression);
    

    if (empty($clean)) {
        $error = 'Ошибка: недопустимые символы';
    } else {
        try {
            $result = calculateExpression($clean);
        } catch (Exception $e) {
            $error = 'Ошибка: ' . $e->getMessage();
        }
    }
}


$fileExpr = '';
if (file_exists('Task/expression.txt')) {
    $fileExpr = trim(file_get_contents('Task/expression.txt'));
}

function calculateExpression($expr) {
    $expr = trim($expr);
    

    $expr = str_replace('pi', M_PI, $expr);
    $expr = str_replace('e', M_E, $expr);
    $expr = processFactorial($expr);
    $expr = processTrig($expr);
    $expr = processMathFuncs($expr);

    while (($pos = findMatchingBracket($expr)) !== false) {
        $inner = substr($expr, 1, $pos - 1);
        $innerResult = calculateExpression($inner);
        $expr = $innerResult . substr($expr, $pos + 1);
    }
    
    $expr = processOperator($expr, '^');
    $expr = processOperator($expr, '*/');
    $expr = processOperator($expr, '+-');
    
    return floatval($expr);
}

function processFactorial($expr) {
    $pattern = '/(\d+)!/';
    while (preg_match($pattern, $expr, $matches)) {
        $n = intval($matches[1]);
        $res = 1;
        for ($i = 2; $i <= $n; $i++) {
            $res *= $i;
        }
        $expr = str_replace($matches[0], $res, $expr);
    }
    return $expr;
}

function processMathFuncs($expr) {
    // sqrt
    $pattern = '/sqrt\(([^()]+)\)/';
    while (preg_match($pattern, $expr, $matches)) {
        $val = calculateExpression($matches[1]);
        $res = sqrt($val);
        $expr = str_replace($matches[0], $res, $expr);
    }
    
    // ln
    $pattern = '/ln\(([^()]+)\)/';
    while (preg_match($pattern, $expr, $matches)) {
        $val = calculateExpression($matches[1]);
        $res = log($val);
        $expr = str_replace($matches[0], $res, $expr);
    }
    
    // log (десятичный)
    $pattern = '/log\(([^()]+)\)/';
    while (preg_match($pattern, $expr, $matches)) {
        $val = calculateExpression($matches[1]);
        $res = log10($val);
        $expr = str_replace($matches[0], $res, $expr);
    }
    
    return $expr;
}

function processTrig($expr) {
    $pattern = '/(sin|cos|tan|ctg|asin|acos|atan)\(([^()]+)\)/';
    while (preg_match($pattern, $expr, $matches)) {
        $func = $matches[1];
        $angle = calculateExpression($matches[2]);
        $res = trig($func, $angle);
        if ($res === null) throw new Exception("Неизвестная функция: $func");
        $expr = str_replace($matches[0], $res, $expr);
    }
    return $expr;
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

function processOperator($expr, $ops) {
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
                if ($b == 0) throw new Exception('Деление на ноль');
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
    <title>Калькулятор + Тригонометрия</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo"><img src="logo.png" alt="Логотип"></div>
        <div class="title">
            <h1>Калькулятор</h1>
            <p>С тригонометрическими функциями</p>
        </div>
    </header>

    <main>
        <div class="calc-container">
            <div class="file-expr">
                <?php if ($fileExpr): ?>
                    <p>Выражение из файла Task/expression.txt:</p>
                    <code><?php echo htmlspecialchars($fileExpr); ?></code>
                    <a href="?expr=<?php echo urlencode($fileExpr); ?>" class="btn-link">Вычислить из файла</a>
                <?php else: ?>
                    <p class="error">Файл Task/expression.txt не найден</p>
                <?php endif; ?>
            </div>

            <div class="display-wrapper">
                <input type="text" id="display" value="<?php echo $expression ? htmlspecialchars($expression) : ''; ?>" readonly placeholder="0">
                <?php if ($result !== '' || $error !== ''): ?>
                    <div class="result-line <?php echo $error ? 'error' : ''; ?>">
                        <?php echo $error ? $error : '= ' . round($result, 2); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="buttons-grid">
                <button class="btn-func" onclick="addFunc('sqrt')">√</button>
                <button class="btn-func" onclick="addFunc('ln')">ln</button>
                <button class="btn-func" onclick="addFunc('log')">log</button>
                <button class="btn-func" onclick="addOp('^')">x^y</button>
                <button class="btn-func" onclick="addOp('!')">n!</button>

                <button class="btn-func" onclick="addFunc('sin')">sin</button>
                <button class="btn-func" onclick="addFunc('cos')">cos</button>
                <button class="btn-func" onclick="addFunc('tan')">tan</button>
                <button class="btn-func" onclick="addFunc('ctg')">ctg</button>
                <button class="btn-clear" onclick="clearDisplay()">C</button>

                <button class="btn-func" onclick="addChar('(')">(</button>
                <button class="btn-func" onclick="addChar(')')">)</button>
                <button class="btn-func" onclick="addConst('pi')">π</button>
                <button class="btn-func" onclick="addConst('e')">e</button>
                <button class="btn-clear" onclick="clearDisplay()">AC</button>

                <button class="btn-num" onclick="addChar('7')">7</button>
                <button class="btn-num" onclick="addChar('8')">8</button>
                <button class="btn-num" onclick="addChar('9')">9</button>
                <button class="btn-op" onclick="addOp('/')">÷</button>
                <button class="btn-op" onclick="addOp('*')">×</button>

                <button class="btn-num" onclick="addChar('4')">4</button>
                <button class="btn-num" onclick="addChar('5')">5</button>
                <button class="btn-num" onclick="addChar('6')">6</button>
                <button class="btn-op" onclick="addOp('-')">−</button>
                <button class="btn-op" onclick="addOp('+')">+</button>

                <button class="btn-num" onclick="addChar('1')">1</button>
                <button class="btn-num" onclick="addChar('2')">2</button>
                <button class="btn-num" onclick="addChar('3')">3</button>
                <button class="btn-num" onclick="addChar('.')">.</button>
                <button class="btn-equals" onclick="calculate()">=</button>

                <button class="btn-num zero" onclick="addChar('0')">0</button>
                <button class="btn-num" onclick="addChar('00')">00</button>
                <button class="btn-op" onclick="backspace()">⌫</button>
                <button class="btn-equals" onclick="calculate()">=</button>
            </div>
        </div>
    </main>

    <footer><p>задание для самостоятельной работы</p></footer>

    <script>
        let display = document.getElementById('display');
        function addChar(c) { display.value += c; }
        function addOp(op) { display.value += ' ' + op + ' '; }
        function addFunc(func) { display.value += func + '('; }
        function addConst(c) { display.value += c; }
        function clearDisplay() { display.value = ''; window.location.href = window.location.pathname; }
        function backspace() { display.value = display.value.slice(0, -1); }
        function calculate() {
            let expr = display.value.trim();
            if (!expr) return;
            window.location.href = '?expr=' + encodeURIComponent(expr);
        }
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
    </script>
</body>
</html>