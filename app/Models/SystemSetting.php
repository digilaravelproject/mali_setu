<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key_name', 'value', 'description'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $value = static::query()->where('key_name', $key)->value('value');

        return $value === null ? $default : $value;
    }

    public static function setValue(string $key, mixed $value, ?string $description = null): self
    {
        return static::query()->updateOrCreate(
            ['key_name' => $key],
            ['value' => (string) $value, 'description' => $description]
        );
    }

    public static function bool(string $key, bool $default = false): bool
    {
        return filter_var(static::getValue($key, $default ? '1' : '0'), FILTER_VALIDATE_BOOLEAN);
    }
}
