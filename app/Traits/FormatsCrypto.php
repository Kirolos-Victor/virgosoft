<?php

namespace App\Traits;

trait FormatsCrypto
{
    protected function formatPrice(string|float|null $value): string
    {
        if ($value === null) {
            return '0';
        }

        $num = (float) $value;

        if ($num >= 1000) {
            return number_format($num, 2, '.', ',');
        }

        if ($num >= 1) {
            return rtrim(rtrim(number_format($num, 4, '.', ','), '0'), '.');
        }

        return rtrim(rtrim(number_format($num, 8, '.', ''), '0'), '.');
    }

    protected function formatAmount(string|float|null $value): string
    {
        if ($value === null) {
            return '0';
        }

        $num = (float) $value;

        if ($num === 0.0) {
            return '0';
        }

        if ($num >= 1) {
            return rtrim(rtrim(number_format($num, 4, '.', ','), '0'), '.');
        }

        return rtrim(rtrim(number_format($num, 8, '.', ''), '0'), '.');
    }
}
