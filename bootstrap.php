<?php

require __DIR__ . '/vendor/autoload.php';

// .envをロード（.devがあったら開発環境）
use jidaikobo\kontiki\Utils\Env;
$env = file_exists(__DIR__ . '/.dev') ? 'development' : 'production';
Env::setPath(__DIR__ . "/config/{$env}/");

if ($env === 'development') {
    require __DIR__ . '/dev/functions.php';
}

// PHP-DI コンテナを設定
use DI\Container;
use Slim\Factory\AppFactory;

$container = new Container();
AppFactory::setContainer($container);

// Load default language on class load
use jidaikobo\kontiki\Utils\Lang;
Lang::setLanguage(Env::get('LANG'));

// Slimアプリケーションの作成
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);
$app->setBasePath(Env::get('BASEPATH'));

// セキュリティ施策のheaderを追加
use jidaikobo\kontiki\Middleware\SecurityHeadersMiddleware;
$app->add(SecurityHeadersMiddleware::class);

// 依存関係の設定
(require __DIR__ . '/src/Dependencies.php')($app);

// エラーログのハンドラ設定
use jidaikobo\Log;
Log::getInstance()->registerHandlers();

return $app;
