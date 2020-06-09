<?php
session_start();

require_once (__DIR__ . '/../vendor/autoload.php');

if ($_SERVER['HTTP_HOST'] == 'game.local') {
    \Mezon\Conf\Conf::addConnectionToConfig('default-db-connection', 'mysql:host=localhost;dbname=game', 'root', '');
    define('TIMEZONE_SHIFT', 1 * 3600);
} else {
    \Mezon\Conf\Conf::addConnectionToConfig(
        'default-db-connection',
        'mysql:host=localhost;dbname=gdzoner5_game',
        'gdzoner5_game',
        'E=_q6j28&,[h');
    define('TIMEZONE_SHIFT', 3 * 3600);
}
