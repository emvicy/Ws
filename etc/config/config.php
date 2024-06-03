<?php

//######################################################################################################################
// Module Ws

// add routing files dir
$aConfig['MVC_ROUTING_DIR'][] = realpath(__DIR__ . '/../') . '/routing';

// Linux: get SERVER_ADDR from hostname
//list($_SERVER['SERVER_ADDR']) = explode(' ', trim(shell_exec('hostname -I')));

$aConfig['MODULE']['Ws'] = array(

    'socketFile' => '/tmp/phpwss.sock',
    'sPidFileName' => '.mvc-websocket-run.{pid}',

    'sProtocol' => 'ws://',
    'sAddress' => '127.0.0.1',  # $_SERVER['SERVER_ADDR']
    'iPort' => 8000,
    'sPath' => '/Informer',

    // domain|host only (no protocol, no slashes)
    'sOrigin' => '127.0.0.1', # 'http://' . $_SERVER['SERVER_ADDR']
    'bCheckOrigin' => false,
    'bVerbose' => true,

    // Limits
    'iMaxClients' => 100,
    'iMaxConnectionsPerIp' => 20,
);


// Datatype
require realpath(__DIR__) . '/Ws/config/_datatype.php';
