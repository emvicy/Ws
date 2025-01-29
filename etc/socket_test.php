<?php

$sDataToSend = '{"type":2,"action":"Informer","data":{"action":"echo","data":"info||<p>TEST\n<strong>' . basename(__FILE__) . '<\/strong><\/p>"}}';
$iDataLength = strlen($sDataToSend);

socket_sendto(
    socket_create(AF_UNIX, SOCK_DGRAM, 0),
    $sDataToSend,
    $iDataLength,
    MSG_EOF,
    '/tmp/phpwss.sock',
    0
);

//var_dump(socket_last_error());
