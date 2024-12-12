<?php
$pageMeta = [
    'title' => 'Sample Page',
    'description' => 'This is the sample page.',
    'keywords' => 'sample, example'
];
require('.inc_header.php');
?>

<h1>サンプルページ</h1>
<p>これはサンプルページです。</p>

<h2>お知らせの一覧</h2>

<?php
$url = 'https://kontiki3.jidaikobo.dev/kontiki3/information/';
$response = kontiki3HttpRequest($url);

if (isset($response['body'])) :
    $html = '';
    $html.= '<ul>';
    foreach ($response['body'] as $item):
      $html.= '<li>';
      $html.= '<a href="/information/'.escHtml($item['slug']).'">';
      $html.= escHtml($item['title']);
      $html.= '</a>';
      $html.= '</li>';
    endforeach;
    $html.= '</ul>';
    echo $html;
endif;
?>

<?php require('.inc_footer.php'); ?>
