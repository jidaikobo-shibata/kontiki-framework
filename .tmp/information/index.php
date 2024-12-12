<?php
require_once (dirname(__DIR__) . '/kontiki3/functions/functions.php');
$url = 'https://kontiki3.jidaikobo.dev/kontiki3'.$_SERVER['REQUEST_URI'];

$response = kontiki3HttpRequest($url);

$pageMeta = [
    'title' => $response['body']['title'],
    'description' => 'This is the sample page.',
    'keywords' => 'sample, example'
];
require('../.inc_header.php');
?>

<?php
echo $response['body']['content'];
?>

<?php require('../.inc_footer.php'); ?>
