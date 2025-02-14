<?php
echo '<header><h1>' . $title . '</h1></header>';
echo '<main>' . Jidaikobo\MarkdownExtra::defaultTransform($content) . '</main>';
