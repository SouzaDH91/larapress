<?php

declare(strict_types=1);

namespace App\Services;

class HookManager
{
    protected array $actions = [];
    protected array $filters = [];
    protected array $shortcodes = [];

    /**
     * Register an action callback.
     */
    public function addAction(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        $this->actions[$tag][$priority][] = [
            'function' => $callback,
            'accepted_args' => $acceptedArgs,
        ];
    }

    /**
     * Execute actions hooked on a specific tag.
     */
    public function doAction(string $tag, ...$args): void
    {
        if (!isset($this->actions[$tag])) {
            return;
        }

        ksort($this->actions[$tag]);

        foreach ($this->actions[$tag] as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                $params = array_slice($args, 0, $callback['accepted_args']);
                call_user_func_array($callback['function'], $params);
            }
        }
    }

    /**
     * Register a filter callback.
     */
    public function addFilter(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        $this->filters[$tag][$priority][] = [
            'function' => $callback,
            'accepted_args' => $acceptedArgs,
        ];
    }

    /**
     * Apply all filters hooked to a tag to a given value.
     */
    public function applyFilters(string $tag, mixed $value, ...$args): mixed
    {
        if (!isset($this->filters[$tag])) {
            return $value;
        }

        ksort($this->filters[$tag]);

        foreach ($this->filters[$tag] as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                $params = array_merge([$value], array_slice($args, 0, $callback['accepted_args'] - 1));
                $value = call_user_func_array($callback['function'], $params);
            }
        }

        return $value;
    }

    /**
     * Register a shortcode tag and handler.
     */
    public function addShortcode(string $tag, callable $callback): void
    {
        $this->shortcodes[$tag] = $callback;
    }

    /**
     * Parse shortcodes in content.
     */
    public function parseShortcodes(string $content): string
    {
        if (empty($this->shortcodes) || !str_contains($content, '[')) {
            return $content;
        }

        $pattern = '/\[(\w+)([^\]]*)\](?:([^\[]+)\[\/\1\])?/s';

        return preg_replace_callback($pattern, function ($matches) {
            $tag = $matches[1];
            $attrsRaw = $matches[2] ?? '';
            $innerContent = $matches[3] ?? null;

            if (!isset($this->shortcodes[$tag])) {
                return $matches[0];
            }

            $attributes = [];
            if (preg_match_all('/(\w+)=["\']([^"\']*)["\']/', $attrsRaw, $attrMatches, PREG_SET_ORDER)) {
                foreach ($attrMatches as $m) {
                    $attributes[$m[1]] = $m[2];
                }
            }

            return (string) call_user_func($this->shortcodes[$tag], $attributes, $innerContent);
        }, $content);
    }
}
