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

	// get more info
	$ipo = socket_getpeername( $socket, $ip );
	// 	and the HTTP path
	if( $data === false || preg_match("/^(GET|HEAD) (\/[^ ]*) HTTP/", $data, $parts) !== 1){
		echo "Invalid request from " . ( $ipo ? $ip : '??' ) . " at " . date('r') . PHP_EOL;
	}
	else{
		// store HTTP infos
		$method = strtoupper($parts[1]);
		$path = $parts[2];

		echo "Request '$path' from " . ( $ipo ? $ip : '??' ) . " at: " . date('r') . PHP_EOL;

		// prepare response
		$code = "200 OK";
		if ($path == '/healthz'){
			$message = 'healthy' . PHP_EOL;
		}
		else if ($path == '/'){
			$message = 'Start: Mail overview' . PHP_EOL;
			mail_telegram();
			$message .= 'End: Mail overview' . PHP_EOL;
		}
		else{
			$code = "404 Not Found";
			$message = 'Unknown request!' . PHP_EOL;
		}
		
		// Prepare headers 
		//	no message with HEAD
		if($method == 'HEAD'){
			$message = '';
		}
		$response = 'HTTP/1.1 '. $code . PHP_EOL .
			'Date: ' . date('r') . PHP_EOL .
			'Content-Type: text/plain;charset=utf-8' . PHP_EOL .
			'Content-Length: ' . strlen( $message ) . PHP_EOL .
			'Last-Modified: ' . date( 'r', filemtime( __DIR__ . '/server.php' ) ) . PHP_EOL .
			'Connection: close'. PHP_EOL .
			PHP_EOL .
			$message;

		// send answer
		socket_write($socket, $response);
	}

	// and close
	socket_close($socket);
}

//do the mail => telegram
function mail_telegram(){
	require( __DIR__ . '/cron.php' );
}
?>