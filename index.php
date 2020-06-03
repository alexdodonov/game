<?php
namespace Game;

use Mezon\HtmlTemplate\HtmlTemplate;
require_once (__DIR__ . './conf/conf.php');

$app = new Kernel(new HtmlTemplate('./res/'));
$app->run();
