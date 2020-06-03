<?php
session_start();

require_once (__DIR__ . '/../vendor/autoload.php');

if ($_SERVER['HTTP_HOST'] == 'game.local') {
    \Mezon\Conf\Conf::addConnectionToConfig('default-db-connection', 'mysql:host=localhost;dbname=game', 'root', '');
} else {
    \Mezon\Conf\Conf::addConnectionToConfig('default-db-connection', 'mysql:host=localhost;dbname=game', 'root', '');
}
