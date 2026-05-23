<?php
function trig($func, $angle) {
    $rad = deg2rad($angle);
    $funcs = [
        'sin' => sin($rad),
        'cos' => cos($rad),
        'tan' => tan($rad),
        'ctg' => 1 / tan($rad),
        'asin' => rad2deg(asin($angle)),
        'acos' => rad2deg(acos($angle)),
        'atan' => rad2deg(atan($angle))
    ];
    return $funcs[$func] ?? null;
}
?>