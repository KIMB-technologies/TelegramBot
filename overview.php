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
			$count = 0;

			foreach( $msg as $v ){
				$n = '- ' . $v['tag'] . ' *' . $v['subject'] .'* _'. $v['from'] .'_ '. "\n";

				if(mb_strlen($cont . $n) > 4096){
					$r = $telebot->sendMessage( $cont, $to );
					Logger::logOverview( $r, $to, $cont, $telebot->getLastResponse() );

					$cont = '*Mailoverview*'."\n";
					$count = 0;
				}
				
				$cont .= $n; 
				$count++;				
			}
			if($count > 0){
				$r = $telebot->sendMessage($cont, $to );
				Logger::logOverview( $r, $to, $cont, $telebot->getLastResponse() );
			}
		}
	}
}

?>