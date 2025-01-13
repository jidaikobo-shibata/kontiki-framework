<?php

namespace Jidaikobo\Kontiki\Frontend;

use Jidaikobo\Kontiki\Bootstrap as BaseBootstrap;

class Bootstrap
{
    public static function init(string $env = 'production')
    {
        BaseBootstrap::init($env, true);
        require __DIR__ . '/../functions/frontend.php';
    }
}
