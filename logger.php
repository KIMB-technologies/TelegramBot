<?php


class Logger{

	public static function logTelegram( $r, $to, $sub, $fro, $cont, $tag, $teleres ){
		file_put_contents(
			__DIR__ . '/log/telegram.log',
			date('d.m.Y H:i:s') . ' ' . ($r ? 'true' : 'false') .' : <' . $to . '> ' . $sub . ' | ' . $fro . "\n",
			FILE_APPEND
		);

		if( !$r ){
			file_put_contents(
				__DIR__ . '/log/telegram_errors.log',
					date('d.m.Y H:i:s') . ' ' . ($r ? 'true' : 'false') .' : <' . $to . '>' ."\n\n" .
					'*' . ( empty( $tag ) ? '' : $tag . ' ' ) . $sub . '* _' . $fro . '_' . "\n\n" . $cont
					."\n\n" . $teleres . '=====================================================================' . "\n\n",
				FILE_APPEND
			);
		}
	}

	public static function logOverview( $r, $to, $cont, $teleres ){
		file_put_contents(
			__DIR__ . '/log/overview.log',
			date('d.m.Y H:i:s') . ' ' . ($r ? 'true' : 'false') .' : <' . $to . '>'."\n",
			FILE_APPEND
		);

		if( !$r ){
			file_put_contents(
				__DIR__ . '/log/overview_errors.log',
					date('d.m.Y H:i:s') . ' ' . ($r ? 'true' : 'false') .' : <' . $to . '>' ."\n\n" .
					$cont
					."\n\n" . $teleres . '=====================================================================' . "\n\n",
				FILE_APPEND
			);
		}
	}
}


?>