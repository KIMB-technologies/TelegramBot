<?php
// Fix file/dir owners on startup
exec("/bin/cchown");

// Start Server
$sock = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
if( socket_bind( $sock, '0.0.0.0', '80' ) ){
	if( socket_listen( $sock , 15 ) ){
		while( true ){
			// wait for connection and handle it
			handle_connection( socket_accept( $sock ) );
		}
	}
	else{
		die('Unable to listen to Socket.');
	}
}
else{
	die('Unable to bind to Socket.');
}

// handle connection
function handle_connection( $socket ){
	// read message
	$data = socket_read( $socket, 1024, PHP_BINARY_READ );

	//	echo $data;
	mail_telegram($data);

	// log
	$ipo = socket_getpeername( $socket, $ip );
	echo "Request from " . ( $ipo ? $ip : '??' ) . " at " . date('r') . PHP_EOL;

	// The answer
	$message = 'OK' . PHP_EOL;

	// Prepare Headers 
	$answer = 'HTTP/1.1 200 OK' . PHP_EOL .
		'Date: ' . date('r') . PHP_EOL .
		'Content-Type: text/plain;charset=utf-8' . PHP_EOL .
		'Content-Length: ' . strlen( $message ) . PHP_EOL .
		'Last-Modified: ' . date( 'r', filemtime( __DIR__ . '/server.php' ) ) . PHP_EOL .
		'Connection: close'. PHP_EOL .
		PHP_EOL .
		$message;
	// send answert
	socket_write( $socket, $answer );
	socket_close( $socket );
}

//do the mail => telegram
function mail_telegram($d){
	require( __DIR__ . '/cron.php' );
}
?>