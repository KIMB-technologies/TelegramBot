<?php

class CONFIG {

	public static array $TELEGRAM;

	public static array $MAIL;

	public static string $SYS_DOMAIN;
	
	public static bool $DELETE_MAILS;

	public static function setup(){
		self::$TELEGRAM = array(
			'API_TOKEN' => isset( $_ENV['TELEGRAM_API_TOKEN'] ) ? $_ENV['TELEGRAM_API_TOKEN'] : '',
			'API_URL' => 'https://api.telegram.org/bot'
		);

		self::$MAIL = array(
			'server' => isset( $_ENV['MAIL_SERVER'] ) ? $_ENV['MAIL_SERVER'] : '{imap.example.com:993/imap/ssl}INBOX',
			'user' => isset( $_ENV['MAIL_USER'] ) ? $_ENV['MAIL_USER'] : 'mail@mail.example.com',
			'pw' => isset( $_ENV['MAIL_PW'] ) ? $_ENV['MAIL_PW'] : ''
		);

		self::$SYS_DOMAIN = isset( $_ENV['SYSDOMAIN'] ) ? $_ENV['SYSDOMAIN'] : 'mail.example.com';
	
		self::$DELETE_MAILS = (isset( $_ENV['DELETEMAILS'] ) && $_ENV['DELETEMAILS'] == 'true') ||
			(isset( $_ENV['DELETMAILS'] ) && $_ENV['DELETMAILS'] == 'true'); # fallback to old typo name
	}
}
CONFIG::setup();

?>