<?php

namespace Jidaikobo\Kontiki\Frontend;

use Jidaikobo\Kontiki\Bootstrap as BaseBootstrap;

class Bootstrap
{
    public static function init(string $env = 'production')
    {
        // prepare timer, log and functions
        BaseBootstrap::init($env, true);

        // prepare frontend functions
        require __DIR__ . '/../functions/frontend.php';
    }
}
