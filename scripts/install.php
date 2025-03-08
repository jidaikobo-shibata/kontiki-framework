<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Phinx\Console\PhinxApplication;

function prompt($message, $default = null, $allowedValues = null) {
    while (true) {
        echo $message;
        if ($default !== null) {
            echo " [$default]";
        }
        if (is_array($allowedValues)) {
            echo " (" . implode(" / ", $allowedValues) . ")";
        }
        echo ": ";

        $input = trim(fgets(STDIN));

        // use default
        if ($input === "" && $default !== null) {
            $input = $default;
        }

        // required
        if ($input === "") {
            echo "Input is required, please try again.\n";
            continue;
        }

        // check value
        if ($allowedValues !== null && !in_array($input, $allowedValues, true)) {
            echo "Invalid choice. Please enter one of: " . implode(", ", $allowedValues) . "\n";
            continue;
        }

        return $input;
    }
}

echo "Welcome to Kontiki CMS Setup.\n\n";

// Prompt the user for input
do {
    $projectName = prompt("Project name", "My CMS");
    $projectLang = prompt("Project language", "en", ['en', 'ja']);
    $projectTimezone = prompt("Project timezone", date_default_timezone_get());
    $projectEnv = prompt("Project environment", "production", ['staging', 'production']);
    $projectBaseurl = prompt("Base URL (ex: https://example.com)");

    echo "\nPlease check your input:\n";
    echo "----------------------------------\n";
    echo " Project Name     : $projectName\n";
    echo " Project Language : $projectLang\n";
    echo " Project Timezone : $projectTimezone\n";
    echo " Project Environment : $projectEnv\n";
    echo " Project URL : $projectBaseurl\n";
    echo "----------------------------------\n";

    $confirm = prompt("Are these okay?", "yes", ['yes', 'no']);
} while ($confirm !== "yes");

// Create `.env`
$envContent = <<<EOL
# Application language setting
LANG="$projectLang"

# Timezone
TIMEZONE="$projectTimezone"

# Copyright text used in the application
COPYRIGHT="$projectName"

# Base URL
BASEURL="$projectBaseurl"

# Upload Directory
BASEURL_UPLOAD_DIR=/uploads

# Base path for Slim's setBasePath
BASEPATH=/cms

# Database connection information
DB_DATABASE=db/$projectEnv/database.sqlite3

# Upload directory
# Relative path from the project root
UPLOADDIR=/../cms/uploads

# Allowed MIME types (in JSON format)
ALLOWED_MIME_TYPES=["image/jpeg","image/png","application/pdf"]

# Maximum file upload size (in bytes)
MAXSIZE=5000000

# admin favicon path
ADMIN_FAVICON_PATH=favicon.ico

# Post Default Settings
POST_HIDE_PARENT=true
POST_HIDE_AUTHOR=true
POST_HIDE_METADATA_EXCERPT=true
POST_HIDE_METADATA_EYECATCH=true
EOL;

$envFilePath = __DIR__ . "/../config/$projectEnv/.env";
if (file_put_contents($envFilePath, $envContent) === false) {
    echo "\n Error: Failed to create `.env` file at $envFilePath!\n";
    exit(1);
}
echo "\n `.env` file has been created at $envFilePath!\n";

// Ensure the database directory exists
$dbDir = __DIR__ . "/../db/$projectEnv";
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
    echo "\n Created database directory at $dbDir\n";
}

// Ensure SQLite database file exists
$dbFile = "$dbDir/database.sqlite3";
if (!file_exists($dbFile)) {
    touch($dbFile);
    echo "\n Created SQLite database file at $dbFile\n";
}

// Run Phinx migrations without `system()`
try {
    echo "\nRunning database migrations for `$projectEnv` environment...\n";

    $phinxApp = new PhinxApplication();
    $phinxApp->setAutoExit(false);

    $input = new ArrayInput([
        'command' => 'migrate',
        '-e' => $projectEnv,
    ]);
    $output = new ConsoleOutput();

    $exitCode = $phinxApp->run($input, $output);

    if ($exitCode !== 0) {
        throw new RuntimeException("Phinx migration failed with exit code: $exitCode");
    }

    echo "\n Database migrations completed successfully!\n";
} catch (Exception $e) {
    echo "\n Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n Installation complete!\n";
