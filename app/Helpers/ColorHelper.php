<?php

namespace App\Helpers;

/**
 * مساعد للتعامل مع الألوان
 */
class ColorHelper
{
    /**
     * استخراج قيمة اللون الأحمر من قيمة لونية
     *
     * @param string|int $value قيمة اللون بصيغة RGB أو Hex
     * @return int قيمة اللون الأحمر (0-255)
     */
    public static function red($value)
    {
        // إذا كانت القيمة رقمية (RGB)
        if (is_numeric($value)) {
            return ($value >> 16) & 0xFF;
        }
        
        // إذا كانت القيمة نصية (Hex)
        $value = ltrim($value, '#');
        if (strlen($value) == 3) {
            $r = substr($value, 0, 1);
            return hexdec($r . $r);
        } else {
            return hexdec(substr($value, 0, 2));
        }
    }

    /**
     * استخراج قيمة اللون الأخضر من قيمة لونية
     *
     * @param string|int $value قيمة اللون بصيغة RGB أو Hex
     * @return int قيمة اللون الأخضر (0-255)
     */
    public static function green($value)
    {
        // إذا كانت القيمة رقمية (RGB)
        if (is_numeric($value)) {
            return ($value >> 8) & 0xFF;
        }
        
        // إذا كانت القيمة نصية (Hex)
        $value = ltrim($value, '#');
        if (strlen($value) == 3) {
            $g = substr($value, 1, 1);
            return hexdec($g . $g);
        } else {
            return hexdec(substr($value, 2, 2));
        }
    }

    /**
     * استخراج قيمة اللون الأزرق من قيمة لونية
     *
     * @param string|int $value قيمة اللون بصيغة RGB أو Hex
     * @return int قيمة اللون الأزرق (0-255)
     */
    public static function blue($value)
    {
        // إذا كانت القيمة رقمية (RGB)
        if (is_numeric($value)) {
            return $value & 0xFF;
        }
        
        // إذا كانت القيمة نصية (Hex)
        $value = ltrim($value, '#');
        if (strlen($value) == 3) {
            $b = substr($value, 2, 1);
            return hexdec($b . $b);
        } else {
            return hexdec(substr($value, 4, 2));
        }
    }
}
