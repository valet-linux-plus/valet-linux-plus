<?php

namespace Valet;

class Interact
{
    /**
     * @param mixed $default
     * @return mixed
     */
    public function select(string $label, array $options, $default = null, int $scroll = 5)
    {
        if (testing() || !stream_isatty(STDIN)) {
            return $default;
        }

        return \Laravel\Prompts\select($label, $options, $default, $scroll);
    }

    /**
     * @return array
     */
    public function multiselect(string $label, array $options, array $default = [], int $scroll = 5)
    {
        if (testing() || !stream_isatty(STDIN)) {
            return $default;
        }

        return \Laravel\Prompts\multiselect($label, $options, $default, $scroll);
    }

    public function confirm(string $label, bool $default = true): bool
    {
        if (testing() || !stream_isatty(STDIN)) {
            return $default;
        }

        return \Laravel\Prompts\confirm($label, $default);
    }

    public function ask(string $label, string $default = '', string $placeholder = ''): string
    {
        if (testing() || !stream_isatty(STDIN)) {
            return $default;
        }

        return \Laravel\Prompts\text($label, $placeholder, $default);
    }

    public function password(string $label, string $placeholder = ''): string
    {
        if (testing() || !stream_isatty(STDIN)) {
            return '';
        }

        return \Laravel\Prompts\password($label, $placeholder);
    }
}
