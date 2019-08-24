<?php

class Overview{

	private $a;

	public function __construct(){
		$this->a = array();
	}

	public function addMail( $to, $subject, $from, $tag ){
		if( !isset( $this->a[$to] ) ){
			$this->a[$to] = array();
		}
		$this->a[$to][] = array(
			'subject' => TelegramBot::cleanHeaderField( $subject ),
			'from' => TelegramBot::cleanHeaderField( $from ),
			'tag' => $tag
		);
	}

	public function send(){
		$telebot = new TelegramBot();
		foreach( $this->a as $to => $msg ){
			$cont = '*Mailoverview*'."\n";
			foreach( $msg as $v ){
				$cont .= '- ' . $v['tag'] . ' *' . $v['subject'] .'* _'. $v['from'] .'_ '. "\n";
			}
			$r = $telebot->sendMessage( mb_substr( $cont, 0, 4096), $to );
			Logger::logOverview( $r, $to, mb_substr( $cont, 0, 4096), $telebot->getLastResponse() );
		}
	}
}

?>