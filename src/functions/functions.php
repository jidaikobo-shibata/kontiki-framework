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
        return \jidaikobo\kontiki\Utils\Lang::get($key, $default, $replace);
    }
}
