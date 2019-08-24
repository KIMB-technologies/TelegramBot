<?php
//Setup
require_once( __DIR__ . '/config.php' );
require_once( __DIR__ . '/rules.php' );
require_once( __DIR__ . '/logger.php' );
require_once( __DIR__ . '/imap.php' );
//Sendprovider
require_once( __DIR__ . '/telegram.php' );
require_once( __DIR__ . '/overview.php' );

//get mails
$i = new IMAP( CONFIG::$MAIL );
$mess = $i->getNew();

//overview 
$overview = new Overview();

//check rules
foreach( $mess as $msg ){

	foreach( RULES::TELEGRAM as $rule ){
		if( count( array_intersect( $rule['mailto'], $msg['to'] )) > 0){
			foreach( $rule['telto'] as $to ){
				sendtelegram( $to, $msg, $rule['tag'] );
			}
		}
	}

	foreach( RULES::MAILOVERVIEW as $rule ){
		if( in_array( $rule['mailto'], $msg['to'] ) ){
			foreach( $rule['telto'] as $to ){
				$overview->addMail( $to, $msg['subject'], $msg['from'], $rule['tag']);
			}
		}
	}
}
$overview->send();
?>
