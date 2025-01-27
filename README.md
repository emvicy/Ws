
# Ws

a WebSocket module for Emvicy2 (2.x) PHP Framework: https://github.com/emvicy/Emvicy/tree/2.x

---

## Installation

_run install bash script_    
~~~bash
cd /modules/Ws/; \
./_install.sh
~~~

_add styles and scripts to your (primary module) routes_  
~~~php
$oDTRoutingAdditional = DTRoutingAdditional::create()
    ...
    ->set_aStyle(array (
        ...    
        // WS_old
        '/Ws_old/assets/pnotify.min.css',
        '/Ws_old/assets/pnotify.brighttheme.min.css',
    ))
    ->set_aScript(array (
        ...
        // WS_old
        '/Ws_old/assets/pnotify.min.js',
        '/Ws_old/assets/pnotify.desktop.min.js',
        '/Ws_old/scripts/pnotify.min.js',
        '/Ws/scripts/ws.min.js',
    ));
~~~

**ddev** 

if you are using ddev:

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

**start**

there are several ways to start the websocket server.  

Once the server is running, you cannot start it again. If you try to, nothing happens, because the 
start routine always checks if the server already runs. And if it is already running, the code will exit.


_start via cronjob_    
~~~bash
# WebSocket Server; start in background
# change the cd Path to your Application
* * * * * cd /var/www/html/public; /usr/bin/php index.php '/ws/serve/' > /dev/null 2>/dev/null & echo $!
~~~

_start via Command in a Controller_    
~~~php
Process::callRouteAsync('/ws/serve/');
~~~
or
~~~php
Process::callRouteAsync(
    Route::getOnTag('WsServe')->get_path()
);
~~~

_start by hand on command line_  
~~~php
php index.php '/ws/serve/'
~~~

**stop**

remove the WebSocket `pid` from the pid folder; usually this is `application/pid/`

or

set Application to `maintainance` mode

or

just `kill` the process by hand

---

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

---

## apache2 vHost Config

_Requirements_
~~~bash
a2enmod proxy
a2enmod proxy_http
a2enmod proxy_wstunnel

systemctl restart apache2
~~~

In the application that is to access the WebSocket, a
ProxyPass to the local WebSocket must be declared.

_ProxyPass to WebSocket_
~~~bash
    ...
    # WebSocket
    RewriteEngine On
    # if WebSocket Request ...
    RewriteCond %{HTTP:Upgrade} =websocket [NC]
    # ...then pass through to local WebSocket server
    RewriteRule /(.*) ws://127.0.0.1:8000/$1 [P,L]
    ...
~~~

_complete real-life example_
~~~bash
<IfModule mod_ssl.c>
<VirtualHost example.com:443>
    ServerAdmin webmaster@mediafinanz.de
    DocumentRoot /var/www/example.com/public
    ServerName example.com

    <Directory /var/www/example.com/public>
        Options FollowSymlinks
        AllowOverride All
    </Directory>

    CustomLog       /var/log/apache2/example.com.log combined
    ErrorLog        /var/log/apache2/example.com.error.log

    SSLCertificateFile /etc/letsencrypt/live/example.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/example.com/privkey.pem
    Include /etc/letsencrypt/options-ssl-apache.conf

    RewriteEngine On
    RewriteCond %{HTTP:Upgrade} =websocket [NC]
    RewriteRule /(.*) ws://127.0.0.1:8000/$1 [P,L]

</VirtualHost>
</IfModule>
~~~
