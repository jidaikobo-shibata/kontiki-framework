<?php
/**
  * @var string $pageTitle
  * @var string $content
  */

echo '<header><h1>' . $pageTitle . '</h1></header>';
echo '<main>' . Jidaikobo\MarkdownExtra::defaultTransform($content) . '</main>';
