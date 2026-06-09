<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    protected $table    = 'system_config';
    protected $fillable = ['key', 'value', 'group'];

    // Get a single value by key
    public static function get(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    // Save / update a single key
    public static function set(string $key, $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key'   => $key],
            ['value' => $value, 'group' => $group]
        );
    }


    // Get all as flat key => value array
    public static function allAsArray(): array
    {
        return static::pluck('value', 'key')->toArray();
    }

}
