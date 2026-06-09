<?php

/**
 * Global helper functions for Zamar Valley
 * Handles number to words conversion without requiring the PHP intl extension.
 */

if (!function_exists('amountInWords')) {
    function amountInWords($number)
    {
        // 1. Clean the input: Remove commas, currency symbols, and spaces
        $number = str_replace([',', 'PKR', 'Rs', ' ', '/-'], '', $number);

        // 2. Configuration
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $dictionary  = [
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion'
        ];

        // 3. Validation
        if (!is_numeric($number)) {
            return "Zero";
        }

        $number = (float)$number;
        if ($number == 0) return 'Zero';
        if ($number < 0) return $negative . amountInWords(abs($number));

        $string = $fraction = null;

        // Handle decimals if they exist
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        // 4. Conversion Logic
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) $string .= $hyphen . $dictionary[$units];
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[(int) $hundreds] . ' ' . $dictionary[100];
                if ($remainder) $string .= $conjunction . amountInWords($remainder);
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = amountInWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= amountInWords($remainder);
                }
                break;
        }

        return ucwords($string);
    }
}
