<?php

namespace Jidaikobo\Kontiki\Frontend;

class Bootstrap
{
    public static function init(string $env = 'production')
    {
        // Load Functions
        if ($env === 'development') {
            require __DIR__ . '/../functions/dev/functions.php';
        }
        require __DIR__ . '/../functions/functions.php';
        require __DIR__ . '/../functions/frontend.php';
    }
}
