<?php
mb_substitute_character(0xFFFD);

$telebot = null;

function sendtelegram( $to, $mail, $tag = '' ){
	global $telebot;

	if( $telebot == null ){
		$telebot = new TelegramBot();
	}

	$cont = TelegramBot::parseHTMLToTelegram( $mail['simplecontent'] );
	$sub = TelegramBot::cleanHeaderField( $mail['subject'] );
	$fro = TelegramBot::cleanHeaderField( $mail['from'] );
	$r = $telebot->sendMessage(
		mb_convert_encoding(
				'*' . ( empty( $tag ) ? '' : $tag . ' ' ) . $sub . '* _' . $fro . '_' . "\n\n" . $cont,
				'UTF-8',
				'UTF-8'
			),
		$to
	);
	Logger::logTelegram( $r, $to, $sub, $fro, $cont, $tag, $telebot->getLastResponse() );
}

class TelegramBot{

	/**
	 * Decodes HEADER-Texte $f to UTF-8
	 */
	public static function cleanHeaderField( $f ){
		$dec = imap_mime_header_decode( $f );
		if( count($dec) > 0 ){
			$dec = $dec[0];
			$f = ( $dec->charset != 'default' ) ? mb_convert_encoding( $dec->text , 'utf-8', $dec->charset ) : $dec->text;
		}
		return str_replace( ['[','*','_','[',']','(',')','`',']'], '', $f );
	}

	/**
	 * Makes $cont (e.g html) to telegram sendable code
	 */
	public static function parseHTMLToTelegram($cont){
		$cont = str_replace( ['[','*','_','[',']','(',')','`',']'], ['\[','\*','\_','\[','\]','\(','\)','\`','\]'], $cont );
		$cont = preg_replace( //allowed html part to markdown
			array(
				'/<(?:i|(?:em))(?:[^>]*)>([^[^<\/]]*)<\/(?:i|(?:em))>/',
				'/<(?:b|(?:strong))(?:[^>]*)>([^[^<\/]]*)<\/(?:b|(?:strong))>/',
				'/<code(?:[^>]*)>([^<\/]*)<\/code>/',
				'/<pre(?:[^>]*)>([^<\/]*)<\/pre>/',
				'/<a(?:[^>]*)href="([^>]*)"(?:[^>]*)>([^<\/]*)<\/a>/'
			),
			array(
				'/_$1_/',
				'/*$1*/',
				'/`$1`/',
				'/```$1```/',
				'/[$2]($1)/'
			),
			$cont
		);
		return strip_tags( $cont, ''); // html cleanup
	}
	
	/**
	 * JSON Response saver
	 */
	private $json = null;
	
	/**
	 * Generates URL for Request, with given $method
	 */
	private function method_url( $method = 'getMe' ){
		return CONFIG::$TELEGRAM['API_URL'] . CONFIG::$TELEGRAM['API_TOKEN'] . '/' . $method;
	}

	/**
	 * Sends a html message $cont to a user $to (Chat ID)
	 */
	public function sendMessage( $cont, $to ){
		if( !empty( $to ) && !empty($cont)){
			return $this->send('sendMessage', array(
				'chat_id' => $to,
				'text' => $cont,
				'parse_mode' => 'Markdown',
				'disable_web_page_preview' => true
			));
		}
		else{
			return false;
		}
	}
	
	/**
     * Fetches the Bot updates
     */
	public function getUpdates(){
		return $this->send( 'getUpdates' );
	}
	
	/**
     * Gets the last Response data in a readable format
     */
    public function getLastResponse(){
     	 return ( $this->json !== null ? print_r( $this->json, true ) : print_r(['status' => 'no last query'], true));
    }
	
    /**
     * Sends the API Request
     */
	private function send( $method, $postarray = array() ){		
		//API Call
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $this->method_url($method));
		curl_setopt($c, CURLOPT_HEADER, false);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, $postarray );
		$response = curl_exec($c);
		$code = curl_getinfo($c, CURLINFO_HTTP_CODE);
		curl_close( $c );

		if( $response !== false ){
			$this->json = json_decode( $response, true );
			return $code == 200 ? true : false;
		}
		else{
			return false;
		}
	}
}
?>