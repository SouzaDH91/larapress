<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Option;
use Illuminate\Support\Facades\Cache;

class OptionService
{
    protected const CACHE_KEY = 'larapress_autoload_options';

    /**
     * Get an option by name.
     */
    public function get(string $name, mixed $default = null): mixed
    {
        try {
            $autoloadOptions = Cache::rememberForever(self::CACHE_KEY, function () {
                if (!\Illuminate\Support\Facades\Schema::hasTable('options')) {
                    return [];
                }
                return Option::where('autoload', true)
                    ->pluck('option_value', 'option_name')
                    ->toArray();
            });

            if (array_key_exists($name, $autoloadOptions)) {
                $value = $autoloadOptions[$name];
                return $this->decodeValue($value);
            }

            if (!\Illuminate\Support\Facades\Schema::hasTable('options')) {
                return $default;
            }

            $option = Option::where('option_name', $name)->first();
            if ($option) {
                return $this->decodeValue($option->option_value);
            }
        } catch (\Throwable $e) {
            return $default;
        }

        return $default;
    }

    /**
     * Set or update an option.
     */
    public function set(string $name, mixed $value, bool $autoload = true): void
    {
        $encoded = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;

        Option::updateOrCreate(
            ['option_name' => $name],
            ['option_value' => $encoded, 'autoload' => $autoload]
        );

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Delete an option.
     */
    public function delete(string $name): bool
    {
        $deleted = Option::where('option_name', $name)->delete() > 0;
        Cache::forget(self::CACHE_KEY);
        return $deleted;
    }

    protected function decodeValue(?string $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
            return $decoded;
        }

        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }

        if ($value === 'true' || $value === 'false') {
            return $value === 'true';
        }

        return $value;
    }
}
