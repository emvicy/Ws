document.addEventListener("DOMContentLoaded", function (event) {

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    //-----------------------------------------------------------------------
    // websocket
    var oWs, sUrl = 'wss://' + location.hostname + '/Informer';

    function websocket() {

        try {

            oWs = new WebSocket(sUrl);
            // WsProcess('Connecting... (readyState ' + oWs.readyState + ')');

            oWs.onopen = function (oMessage) {
                var sMsg = 'Connection successfully opened (readyState ' + this.readyState + ')';
                console.info('%c' + sMsg, 'color: green; font-family: monospace;');
                $('#wsSocketStatusInfo').html('<a class="badge text-success" title="WebSocket Server"><i class="fa fa-check"></i></a>');
            };

            oWs.onmessage = function (oMessage) {
                var sData = oMessage.data;
                sData = sData.trim();

                if ('' !== sData) {
                    WsProcess(sData);
                }
            };

            oWs.onclose = function (oMessage) {
                if (this.readyState == 2) {
                    var sMsg = 'Closing... The connection is going throught the closing handshake (readyState ' + this.readyState + ')';
                    WsProcess('{"sMessage":"' + sMsg + '"}');
                } else if (this.readyState == 3) {
                    sMsg = 'Connection closed... The connection has been closed or could not be opened (readyState ' + this.readyState + ')';
                    // @see http://stackoverflow.com/a/951057/2487859
                    sleep(5000).then(() => {
                        console.log('try again...');
                        websocket();
                    });
                } else {
                    WsProcess('Connection closed... (unhandled readyState ' + this.readyState + ')');
                    // @see http://stackoverflow.com/a/951057/2487859
                    sleep(5000).then(() => {
                        console.log('try again...');
                        websocket();
                    });
                }

                $('#wsSocketStatusInfo').html('<a class="badge text-danger" title="WebSocket Server"><i class="fa fa-exclamation-triangle"></i></a>');
            };

            oWs.onerror = function (oEvent) {
                console.error('%c Connection to WebSocket failed. (' + oEvent.type + ', readyState: ' + oEvent.target.readyState + ')', 'color: red; font-family: monospace; font-weight: bold;');
            };
        } catch (oException) {
            console.error(oException);
            return false;
        }

        return true;
    }

    window.onclose = function () {
        console.log('window.onclose = function()');
    }

    window.onload = function () {
        websocket();
    };
});