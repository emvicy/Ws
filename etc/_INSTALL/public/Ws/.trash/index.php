<?php

session_start();
require_once 'config.php';
$sHash = md5(uniqid() . microtime());

?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>WebSocket Demo</title>

		<link rel="stylesheet" type="text/css" href="/Emvicy/assets/bootstrap-5.3.2-dist/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/Emvicy/assets/fontawesome-free-6.4.2-web/css/all.min.css">
		<link rel="stylesheet" type="text/css" href="/Ws/assets/pnotify.min.css" />
		<link rel="stylesheet" type="text/css" href="/Ws/assets/pnotify.brighttheme.css" />
		
		<style>

			#terminal {
				overflow: auto;
				width: 100%;
				height: 200px;
			}
			.statusButton label {
				width: 150px;
				text-align: right;
			}
            #clients {
                border: 1px solid silver;
                height: 300px;
                width: 100%;
            }

		</style>
	</head>
	<body>
    <!--
    https://getbootstrap.com/docs/5.0/examples/cheatsheet/
    -->
		<a name="top"></a>
		<div class="container">
			<div class="header">
				<h3 class="text-muted">
					WebSocket Demo <small>[retrieve Data from a WebSocket Server Backend]</small>
				</h3>
	
				<div id="wsSocketStatus" class="statusButton">
					<label>WebSocket Server:</label>
					<span id="wsSocketStatusInfo">
                        <span class="badge rounded-pill bg-danger"><i class="fa fa-exclamation-triangle"></i> Down</span>
                    </span>
				</div>
			</div>
			<h2><?php 
			
			/**
			 * Baue ein Random Username
			 */
			$sUsername = "Browser-" . substr( md5(uniqid() . microtime()), 0, 5); # 'Guido Üffing';
			echo $sUsername;
			?></h2>
			<hr/>

			<div class="jumbotron">
				<ul id="terminal"></ul>		
			</div>

            <hr>
            Session: <code><?=session_id() ?></code>
            <hr>

<!--            <button id="btnGetClients" class="btn btn-primary">getClients</button>-->
<!--            <div id="clients"></div>-->

            <h1>Server Status</h1>
            <span id="status" class="offline">disconnected</span>

            <div id="main">
                <div id="clientList">
                    <h2>Clients:</h2>
                    <select id="clientListSelect" class="form-control" multiple="multiple"></select>
                </div>

                <div id="serverInfo">
                    <h2>Server Info:</h2>
                    <p>Connected Clients: <span id="clientCount"></span></p>
                    <p>Limit Clients: <span id="maxClients"></span></p>
                    <p>Limit Connections/IP: <span id="maxConnections"></span></p>
                </div>

                <div class="clearer"></div>

                <div id="console">
                    <h2>Server Messages:</h2>
                    <div id="log"></div>
                </div>
            </div>

            <hr>


			<footer class="footer">
				<p><a href="https://blog.ueffing.net">blog.ueffing.net</a></p>
			</footer>
		</div>  

		<!--[if lt IE 9]><script src="./assets/html5.js"></script><![endif]-->
		<script src="/Emvicy/assets/jquery-3.7.1/jquery-3.7.1.min.js" type="text/javascript"></script>
		<script src="/Emvicy/assets/bootstrap-5.3.2-dist/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="/Ws/assets/pnotify.min.js"></script>

        <script src="scripts/status.js"></script>

		<script defer type="text/javascript">
            document.addEventListener("DOMContentLoaded", function(event) {

                console.log('%c WebSocket Demo ', 'background: #222; color: #bada55; font-size: x-large; font-family: monospace; border: 3px solid red;');
                console.log('%c [push Data from Backend to Frontend] ', 'background: #222; color: #bada55; font-size: medium; font-family: monospace; border: 3px solid red;');

                //--------------------------------------------------------------
                /**
                 * schaltet eventlistener auf onbeforeunload an/aus
                 *
                 * @param boolean state | default=true (aktiv)
                 * @returns boolean
                 */
                function confirmExit(state) {

                    if ('undefined' === typeof state) {state = true;}
                    if (true === state) {
                        window.onbeforeunload = function () {return '';}
                        return true;
                    }

                    window.onbeforeunload = null;
                    return false;
                }

                // confirmExit(true);

                /**
                 * @see http://stackoverflow.com/a/39914235/2487859
                 * @returns {Boolean}
                 */
                function sleep(ms) {
                    return new Promise(resolve => setTimeout(resolve, ms));
                }

                //-----------------------------------------------------------------------
                // websocket
                // baue URL mit anhängendem Hash
                // Fehlt der hash, wird die Verbindung vom WsServer aus gekappt
                var oWs, sUrl = '<?=$aConfig['ws']['sProtocol'] ?><?=$aConfig['ws']['sAddress'] ?>:<?=$aConfig['ws']['iPort'] ?><?=$aConfig['ws']['sPath'] ?>'; // '?<?=$sHash ?>';
                console.log('sUrl', sUrl);

                var oTerminal = $('#terminal');

                function websocket () {

                    try {

                        oWs = new WebSocket(sUrl);
    //					write('Connecting... (readyState ' + oWs.readyState + ')');

                        /**
                         * Übergeben von IP, Username
                         * via WebSocket
                         *
                         * @param {type} oMessage
                         * @returns {undefined}
                         */
                        oWs.onopen = function(oMessage) {

                            var sMsg = 'Connection successfully opened (readyState ' + this.readyState + ')';
                            console.info('%c' + sMsg, 'color: green; font-family: monospace;');
                            $('#wsSocketStatusInfo').html('<span class="badge rounded-pill bg-success"><i class="fa fa-check"></i> Up</span>');

                            var oClient = {};
                            oClient.sIp = "<?=$_SERVER['REMOTE_ADDR'] ?>";
                            oClient.sSessionId = "<?=session_id() ?>";
                            oClient.sName = "<?=$sUsername ?>";
                            oClient.aData = [{"id":"12345","hash":"<?=$sHash ?>"}];
                            // console.log('oClient', oClient);

                            // oWs.send('identify:' + JSON.stringify(oClient));
                            oWs.send('identify:{"sIp":"127.0.0.1","sName":"Browser-20067"}');//,"aData":[{"id":"12345","hash":"bcbc8091074c3d735716ca0bfb551a3a"}]}');
                            oWs.send('getClients');
                        };

                        oWs.onmessage = function(oMessage) {

                            // console.log('oMessage', oMessage);

                            var sText = oMessage.data;
                            sText = sText.trim();

                            if ('' !== sText) {
                                write(sText);
                            }
                        };

                        oWs.onclose = function(oMessage) {

                            if (this.readyState == 2) {

                                var sMsg = 'Closing... The connection is going throught the closing handshake (readyState ' + this.readyState + ')';
                                write('{"sMessage":"' + sMsg + '"}');
                            }
                            else if (this.readyState == 3) {

                                sMsg = 'Connection closed... The connection has been closed or could not be opened (readyState ' + this.readyState + ')';
    //							write('{"sMessage":"' + sMsg + '"}');
    //
                                /**
                                 * @see http://stackoverflow.com/a/951057/2487859
                                 */
                                sleep(5000).then(() => {
                                    console.log('try again...');
                                    websocket();
                                });
                            }
                            else {

                                write('Connection closed... (unhandled readyState ' + this.readyState + ')');

                                /**
                                 * @see http://stackoverflow.com/a/951057/2487859
                                 */
                                sleep(5000).then(() => {
                                    console.log('try again...');
                                    websocket();
                                });
                            }

                            $('#wsSocketStatusInfo').html('<span class="badge rounded-pill bg-danger"><i class="fa fa-exclamation-triangle"></i> Down</span>');
                        };

                        oWs.onerror = function(oEvent) {

                            // console.clear();
                            console.error('%c Connection to WebSocket failed. (' + oEvent.type + ', readyState: ' + oEvent.target.readyState + ')', 'color: red; font-family: monospace; font-weight: bold;');
    						oTerminal.html('<li style="color: red;">Error: Connection to WebSocket failed. (' + oEvent.type + ', readyState: ' + oEvent.target.readyState + ')</li>'); // + oTerminal.html());
                        };

                    }
                    catch (oException) {

                        console.error(oException);
                        return false;
                    }

                    return true;
                }

                window.onclose = function() {

                    console.log('window.onclose = function()');
                }

                window.onload = function() {

                    websocket();
                };

                var stack_topright = {"dir1": "down", "dir2": "left", "push": "top"};
                var stack_topleft = {"dir1": "down", "dir2": "right", "push": "top"};
                var stack_bottomright = {"dir1": "up", "dir2": "up", "push": "top"};
                var stack_bottomleft = {"dir1": "up", "dir2": "up", "push": "top"};
                var stack_modal = {"dir1": "down", "dir2": "right", "push": "top", "modal": true, "overlay_close": true};
                var stack_bar_top = {"dir1": "down", "dir2": "right", "push": "top", "spacing1": 0, "spacing2": 0};
                var stack_bar_bottom = {"dir1": "up", "dir2": "right", "spacing1": 0, "spacing2": 0};
                var stack_context = {"dir1": "down", "dir2": "left", "context": $("#stack-context")};

                // Ausgabe Responses
                function write(sText) {

                    // console.log('sText', sText);

                    // json parse
                    var oJson = JSON.parse(sText);
                    // console.log('oJson', oJson);

                    var oDate = new Date();
                    var sDateText = '[' + oDate.getFullYear() + '-' + (oDate.getMonth() + 1 > 9 ? oDate.getMonth() + 1 : '0' + oDate.getMonth() + 1) + '-' + (oDate.getDate() > 9 ? oDate.getDate() : '0' + oDate.getDate()) + ' ' + (oDate.getHours() > 9 ? oDate.getHours() : '0' + oDate.getHours()) + ':' + (oDate.getMinutes() > 9 ? oDate.getMinutes() : '0' + oDate.getMinutes()) + ':' + (oDate.getSeconds() > 9 ? oDate.getSeconds() : '0' + oDate.getSeconds()) + ']';

                    oTerminal.html('<li>' + sDateText + ' ' + oJson.data + '</li>'); // + oTerminal.html());

                    /**
                     * Bsp.:
                     * sDateText    [2024-041-30 15:21:23]
                     * sText        {"action":"echo","data":"System Message: Hello from PushClient at 2024-05-30 15:21:23"}
                     */
                    // console.log('sDateText / sText', sDateText + ' / ' + sText);

                    var sClass = "stack-topright";
                    var oStack = stack_topright;

                    if ('info' === oJson.sType) {
                        var sClass = "stack-topleft";
                        var oStack = stack_topleft;
                    }
                    if ('success' === oJson.sType) {
                        var sClass = "stack-topright";
                        var oStack = stack_topright;
                    }
                    if ('notice' === oJson.sType) {
                        var sClass = "stack-bottomright";
                        var oStack = stack_bottomright;
                    }
                    if ('error' === oJson.sType) {
                        var sClass = "stack-bottomleft";
                        var oStack = stack_bottomleft;
                    }
    //				console.log('Json.sType', oJson.sType);

                    new PNotify({
                        title: oJson.action,
                        text: oJson.data,
                        // type: oJson.sType,
                        addclass: sClass,
                        stack: oStack
    //					stack: stack_topleft
                    });
                }

    //			function quit()
    //			{
    //				oWs.send('quit');
    //			}

    //			function message(sText)
    //			{
    //				oWs.send('chat:' + sText);
    //			}


                $('#btnGetClients').on('click', function(){
                    console.log('this', this);
                    console.log('oWs', oWs);
                    // oWs.send('getClients:' + JSON.stringify(oClient));
                });
            });
		</script>			
				
	</body>
</html>