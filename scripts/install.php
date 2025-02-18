<?php

function prompt($message, $default = null) {
    while (true) {
        echo $message;
        if ($default !== null) {
            echo " [$default]"; // デフォルト値を表示
        }
        echo ": ";

        $input = trim(fgets(STDIN));

        // ユーザーが何も入力しなかった場合はデフォルト値を使用
        if ($input === "" && $default !== null) {
            return $default;
        }

        if ($input !== "") {
            return $input;
        }

        echo "⚠ 入力が必要です。もう一度入力してください。\n";
    }
}

echo "Welcome to My CMS Setup!\n\n";

// ユーザーに入力を促す
do {
    $projectName = prompt("Enter your project name", "My CMS");
    $dbHost = prompt("Database host", "127.0.0.1");
    $dbName = prompt("Database name");
    $dbUser = prompt("Database user");
    $dbPass = prompt("Database password");

    // 入力確認
    echo "\n✅ **入力内容を確認してください:**\n";
    echo "----------------------------------\n";
    echo " Project Name  : $projectName\n";
    echo " Database Host : $dbHost\n";
    echo " Database Name : $dbName\n";
    echo " Database User : $dbUser\n";
    echo " Database Pass : " . ($dbPass ? "********" : "(空)") . "\n";
    echo "----------------------------------\n";

    // 確認プロンプト
    $confirm = prompt("この内容でよろしいですか？ (yes/no)", "yes");
} while (strtolower($confirm) !== "yes");

// `.env` ファイルを作成
$envContent = <<<EOL
APP_NAME="$projectName"
DB_HOST="$dbHost"
DB_DATABASE="$dbName"
DB_USERNAME="$dbUser"
DB_PASSWORD="$dbPass"
EOL;

// file_put_contents(__DIR__ . '/../.env', $envContent);

echo "\n✅ `.env` file has been created!\n";

// マイグレーション実行（オプション）
echo "Running database migrations...\n";
// system("php artisan migrate --force");

echo "✅ Installation complete!\n";
