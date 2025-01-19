
# Ws

a WebSocket module for Emvicy2 (2.x) PHP Framework: https://github.com/emvicy/Emvicy/tree/2.x

---

## Installation

_publish public Files_    
~~~bash
cd /modules/Ws/; \
./_install.sh
~~~

_add styles and script to your (primary module) routes_  
~~~php
$oDTRoutingAdditional = DTRoutingAdditional::create()
    ...
    ->set_aStyle(array (
        ...    
        // WS
        '/Ws/assets/pnotify.min.css',         
        '/Ws/assets/pnotify.brighttheme.min.css',     
    ))
    ->set_aScript(array (
        ...
        // WS
        '/Ws/assets/pnotify.min.js',        
        '/Ws/scripts/pnotify.min.js',
        '/Ws/scripts/ws.min.js',
    ));
~~~

**ddev** 

_add to your `.ddev/config.yaml`_  
~~~bash
# WebSocket
web_extra_exposed_ports:
    - name: websocket
      container_port: 8000
      http_port: 7999
      https_port: 8000
~~~

---

## Templating

_add WebSocket Server Status somewhere (maybe `<footer>`) to your HTML_  
~~~html
<!--WS-->
<div class="float-end" style="position: fixed; bottom: 20px; right: 10px; margin: 0 10px !important;">
    <small><kbd>WebSocket</kbd></small>
    <span id="wsSocketStatusInfo">
		<a class="badge text-danger" title="WebSocket Server"><i class="fa fa-exclamation-triangle"></i></a>
	</span>
</div>
~~~

## WebSocket

### Server

_start via cronjob: WebSocket Server_  
~~~bash
# WebSocket Server; start in background
# change the cd Path to your Application
* * * * * cd /var/www/Emvicy/public; /usr/bin/php index.php '/ws/serve/' > /dev/null 2>/dev/null & echo $!
~~~

_start by hand: WebSocket Server_  
~~~php
php index.php '/ws/serve/'
~~~

### Message

_push Message to WebSocket Server_
~~~php
\Ws\Model\Ws::init()->push(
    DTWsPackage::create()
        ->set_sType('notice')
        ->set_sMessage('This is a **PUSH** Message at ' . date('Y-m-d H:i:s'))
);
~~~

- possible types: `info`, `success`, `notice`, `error`
- you can write HTML as well as Markdown syntax as message text


## Customizing

_WS config file_  
~~~
modules/Ws/etc/config/config.php
~~~

overwrite those config settings in your primary module.