<?php
require_once( __DIR__ . '/config.php' );
require_once( __DIR__ . '/telegram.php' );

$telebot = new TelegramBot();
$telebot->getUpdates();
echo $telebot->getLastResponse();
?>