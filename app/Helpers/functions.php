<?php

if (!function_exists('red')) {
    /**
     * استخراج قيمة اللون الأحمر من قيمة لونية
     *
     * @param string|int $value قيمة اللون بصيغة RGB أو Hex
     * @return int قيمة اللون الأحمر (0-255)
     */
    function red($value)
    {
        return \App\Helpers\ColorHelper::red($value);
    }
}

if (!function_exists('green')) {
    /**
     * استخراج قيمة اللون الأخضر من قيمة لونية
     *
     * @param string|int $value قيمة اللون بصيغة RGB أو Hex
     * @return int قيمة اللون الأخضر (0-255)
     */
    function green($value)
    {
        return \App\Helpers\ColorHelper::green($value);
    }
}

if (!function_exists('blue')) {
    /**
     * استخراج قيمة اللون الأزرق من قيمة لونية
     *
     * @param string|int $value قيمة اللون بصيغة RGB أو Hex
     * @return int قيمة اللون الأزرق (0-255)
     */
    function blue($value)
    {
        return \App\Helpers\ColorHelper::blue($value);
    }
}
