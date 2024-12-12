<?php
require_once (__DIR__ . '/kontiki3/functions/functions.php');
?><!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= escHtml($pageMeta['title'] ?? 'Default Title') ?></title>

  <!--seo-->
  <meta name="keywords" content="<?= escHtml($pageMeta['keywords'] ?? '') ?>">
  <meta name="description" content="<?= escHtml($pageMeta['description'] ?? '') ?>">

	<!-- jQuery CDN -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>

  <!--script-->
  <script src="./js/jquery.inc.js"></script>

  <!--css-->
  <link rel="stylesheet" type="text/css" media="all" href="./css/base.css">
  <link rel="stylesheet" type="text/css" media="all" href="./css/layout.css">
</head>
<body>
