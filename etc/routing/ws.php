<?php

// start WebSocket Server
\MVC\Route::get(sPath: '/ws/serve/', sClassMethod: '\Ws\Controller\Ws::serve', sTag: 'WsServe');

// push Messages to WebSocket Server
\MVC\Route::get(sPath: '/ws/pushtest/', sClassMethod: '\Ws\Controller\Ws::pushtest', sTag: 'WsPushTest');

