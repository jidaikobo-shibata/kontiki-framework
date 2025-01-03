<?php

if (!function_exists('__')) {
    /**
     * Translate the given text and replace placeholders.
     *
     * @param  string $key     The message key.
     * @param  string $default The default message if the key does not exist.
     * @param  array  $replace Variables to replace in the message.
     *
     * @return string
     */
    function __(string $key, string $default = '', array $replace = []): string
    {
        return \Jidaikobo\Kontiki\Utils\Lang::get($key, $default, $replace);
    }
}

if (!function_exists('e')) {
    /**
     * Escape the given string for safe HTML output.
     *
     * @param  string|null $value The string to escape. Null values are converted to an empty string.
     *
     * @return string
     */
    function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('env')) {
    /**
     * Get an environment variable from $_SERVER or $_ENV, or return a default value.
     *
     * @param string $key The environment variable key.
     * @param mixed $default The default value if the key does not exist.
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        // Check in $_SERVER
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        // Check in $_ENV
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        // Fallback to default value
        return $default;
    }
}
