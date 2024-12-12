<?php

namespace jidaikobo\kontiki\Utils;

use Dotenv\Dotenv;

class Env
{
    private static array $variables = [];
    private static string $path = __DIR__ . '/../../';

    /**
     * Load environment variables from the .env file.
     *
     * @param  string|null $path Path to the directory containing the .env file.
     *                           Defaults to `__DIR__ . '/../../'`.
     * @return void
     */
    public static function load(?string $path = null): void
    {
        self::$path = $path ?? self::$path;
        $dotenv = Dotenv::createImmutable(self::$path);
        self::$variables = $dotenv->load();
    }

    /**
     * Get an environment variable by key.
     *
     * @param  string $key     The name of the environment variable.
     * @param  mixed  $default Default value if the variable is not set.
     * @return mixed The value of the environment variable or the default value.
     */
    public static function get(string $key, $default = null)
    {
        return self::$variables[$key] ?? $default;
    }

    /**
     * Change the path for the .env file and reload the variables.
     *
     * @param  string $path The new path to the .env file.
     * @return void
     */
    public static function setPath(string $path): void
    {
        self::$path = $path;
        self::load();
    }
}
