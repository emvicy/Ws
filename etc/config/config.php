<?php

//######################################################################################################################
// Module Ws

// add routing files dir
$aConfig['MVC_ROUTING_DIR'][] = realpath(__DIR__ . '/../') . '/routing';

$aConfig['MODULE']['Ws'] = array(

    'socketFile' => '/tmp/phpwss.sock',
    'sPidFileName' => '.mvc-websocket-run.{pid}',

    'sProtocol' => 'wss://',
    'sAddress' => '0.0.0.0',
    'iPort' => 8000,
    'sPath' => '/Informer',

    // domain|host only (no protocol, no slashes)
    'sOrigin' => '0.0.0.0',
    'bCheckOrigin' => false,
    'bVerbose' => true,

    // Limits
    'iMaxClients' => 100,
    'iMaxConnectionsPerIp' => 20,
);


// Datatype
require realpath(__DIR__) . '/Ws/config/_datatype.php';
