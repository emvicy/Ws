<?php

// start WebSocket Server
\MVC\Route::get(sPath: '/ws/serve/', sQuery: '\Ws\Controller\Ws::serve', sTag: 'WsServe');

// push Messages to WebSocket Server
\MVC\Route::get(sPath: '/ws/pushtest/', sQuery: '\Ws\Controller\Ws::pushtest', sTag: 'WsPushTest');

