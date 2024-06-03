<?php

// Linux: get SERVER_ADDR from hostname
list($_SERVER['SERVER_ADDR']) = explode(' ', trim(shell_exec('hostname -I')));

//echo $_SERVER['SERVER_ADDR'] . "\n";
//var_dump($_SERVER['SERVER_ADDR']);
//exit();

$aConfig = array(

    'phpExec' => PHP_BINDIR . '/php',

	'ws' => array(
			'sProtocol' => 'ws://'
		,	'sAddress' => '127.0.0.1' # $_SERVER['SERVER_ADDR']
		,	'iPort' => 8000
        ,   'sPath' => '/chat'

		,	'sOrigin' => 'http://127.0.0.1' # 'http://' . $_SERVER['SERVER_ADDR'] . '/'
		,	'bVerbose' => true
	)
);

//var_dump($aConfig);


# TEST Env
//// Linux: get SERVER_ADDR from hostname
//list($_SERVER['SERVER_ADDR']) = explode(' ', trim(shell_exec('hostname -I')));
//
////var_dump($_SERVER['SERVER_ADDR']);
////exit();
//
//$aConfig = array(
//
//    'phpExec' => PHP_BINDIR . '/php',
//
//        'ws' => array(
//                        'sProtocol' => 'ws://'
//                ,       'sAddress' => '127.0.0.1'
//                ,       'iPort' => 1969
//
//                #,       'sOrigin' => 'https://test.permulta.com/'
//                ,	'sOrigin' => 'http://' . $_SERVER['SERVER_ADDR'] . '/'
//                ,       'bVerbose' => true
//        )
//);