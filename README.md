
## add Route

**Emvicy >= 1.12:**

nothing to do. All routes are added.

**Emvicy < 1.12:**

_copy or symlink the WS routing file `ws.php` to your primary module_  
~~~bash
/modules/Ws/etc/routing/ws.php 
~~~

## public files

_publish public Files_    
~~~bash
cd /modules/Ws/; \
./_publish.sh;
~~~

_add styles and script to your (primary module) routes_  
~~~php
$oDTRoutingAdditional = DTRoutingAdditional::create()
    ...
    ->set_aStyle(array (
        ...    
        // WS
        '/Ws/assets/pnotify.min.css',         
        '/Ws/assets/pnotify.brighttheme.css',     
    ))
    ->set_aScript(array (
        ...
        // WS
        '/Ws/assets/pnotify.min.js',        
        '/Ws/scripts/pnotify.min.js',
        '/Ws/scripts/ws.min.js',
    ));
~~~

## Templating

_add WebSocket Server Status somewhere (maybe `<footer>`) to your HTML_  
~~~html
<!--WS-->
<div class="float-end" style="position: fixed; bottom: 20px; right: 10px; margin: 0 10px !important;">
	<span id="wsSocketStatusInfo">
		<a class="badge text-success" title="WebSocket Server"><i class="fa fa-check"></i></a>
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