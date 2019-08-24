<?php

class RULES {

	//Telegram pushes, one array per rule
	const TELEGRAM = array(
		array(
			'mailto' => array( 'test'), // the addresses the mails goes to (only small letters, <part>@SYSDOMAIN)
			'tag' => '[TELEGRAM-TEST]', // TAG to be prepended to subject
			'telto' => array( '0000000' ) // the telegram chat, to send the mail to
		),
		// ....
	);

	//Mailoverview
	const MAILOVERVIEW = array(
		array(
			'mailto' => 'test', // the addresses the mails goes to (only small letters, <part>@SYSDOMAIN)
			'tag' => '[Overview-Test]', // TAG to be used as subject
			'telto' => array( '0000000' ) // the telegram chat, to send the mail to
		),
		// ...
	);
}
?>
