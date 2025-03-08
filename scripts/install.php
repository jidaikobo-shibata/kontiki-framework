<?php

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
$projectEnv = 'production';

// Prompt the user for input
do {
    $projectName = prompt("Project name", "My CMS");
    $projectLang = prompt("Project language", "en", ['en', 'ja']);
    $projectTimezone = prompt("Project timezone", date_default_timezone_get());
    // $projectEnv = prompt("Project environment", "production", ['staging', 'production']);
    $projectBaseurl = prompt("Base URL (ex: https://example.com)");

    echo "\nPlease check your input:\n";
    echo "----------------------------------\n";
    echo " Project Name     : $projectName\n";
    echo " Project Language : $projectLang\n";
    echo " Project Timezone : $projectTimezone\n";
    // echo " Project Environment : $projectEnv\n";
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
# Must be an absolute path starting with a slash
BASEPATH=/cms

# Database connection information
# DB_DATABASE: Relative path from the project root
DB_DATABASE=db/$projectEnv/database.sqlite3

# Upload directory
# Relative path from the project root
UPLOADDIR=/../uploads

# Allowed MIME types (in JSON format)
ALLOWED_MIME_TYPES=["image/jpeg","image/png","application/pdf"]

# Maximum file upload size (in bytes)
MAXSIZE=5000000

# admin favicon path

ADMIN_FAVICON_PATH=/kontikip/kontiki/admin/favicon.ico

# Post Default Settings

POST_HIDE_PARENT=true
POST_HIDE_AUTHOR=true
POST_HIDE_METADATA_EXCERPT=true
POST_HIDE_METADATA_EYECATCH=true
EOL;

// file_put_contents(__DIR__ . '/../.env', $envContent);

echo "\n `.env` file has been created!\n";

// Migration
echo "Running database migrations...\n";
// system("php artisan migrate --force");

echo "Installation complete!\n";
