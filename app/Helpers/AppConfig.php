<?php

namespace App\Helpers;

use App\Models\SystemConfig;

class AppConfig
{
    public static function get(string $key, $default = null)
    {
        return SystemConfig::get($key, $default);
    }

    public static function qrEnabled(): bool
    {
        return static::get('qr_on_documents', '1') === '1';
    }

    public static function logoEnabled(): bool
    {
        return static::get('show_logo_on_receipt', '1') === '1';
    }

    public static function societyName(): string
    {
        return static::get('society_name', 'Zamar Valley');
    }

    public static function currency(): string
    {
        return static::get('currency_symbol', 'PKR');
    }

    public static function contactPhone(): string
    {
        return static::get('society_phone', '');
    }

    public static function contactPhone2(): string
    {
        return static::get('society_phone2', '');
    }

    public static function contactPhone3(): string
    {
        return static::get('society_phone3', '');
    }

    /** Returns all non-empty contact numbers as an array. */
    public static function contactNumbers(): array
    {
        return array_filter([
            static::get('society_phone',  ''),
            static::get('society_phone2', ''),
            static::get('society_phone3', ''),
        ]);
    }
}

