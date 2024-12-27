<?php

namespace jidaikobo\kontiki\Utils;

class Lang
{
    private static string $language = 'en'; // Default language
    private static array $messages = [];
    private static string $langPath = __DIR__ . '/../../locale'; // Path to language files

    /**
     * Set the language to use.
     *
     * @param  string $language
     * @return void
     */
    public static function setLanguage(string $language): void
    {
        self::$language = $language;
        self::loadLanguage();
    }

    /**
     * Load the language file into memory.
     *
     * @return void
     */
    private static function loadLanguage(): void
    {
        $filePath = self::$langPath . '/' . self::$language . '/messages.php';

        if (!file_exists($filePath)) {
            throw new \Exception("Language file not found: $filePath");
        }

        self::$messages = include $filePath;
    }

    /**
     * Get a specific message by key.
     *
     * @param  string $key     The message key.
     * @param  string $default The default message if the key does not exist.
     * @param  array  $replace Variables to replace in the message.
     * @return string
     */
    public static function get(string $key, string $default = '', array $replace = []): string
    {
        if ($default === '') {
            $default = ucfirst(str_replace('_', ' ', $key));
        }

        $message = self::$messages[$key] ?? $default;

        foreach ($replace as $search => $value) {
            $message = str_replace(':' . $search, $value, $message);
        }

        return $message;
    }

    /**
     * Translate a message with placeholders.
     *
     * @param  string $key     The message key.
     * @param  array  $replace Variables to replace in the message.
     * @return string
     */
    public static function trans(string $key, array $replace = []): string
    {
        return self::get($key, $replace);
    }

    public static function mergeMessages(array $defaultMessages, array $additionalMessages): array
    {
        return array_merge($defaultMessages, $additionalMessages);
    }
}
