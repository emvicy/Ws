<?php

/**
 * @name $WsModel
 */

namespace Ws\Model;

use Bloatless\WebSocket\Application\Application;
use Bloatless\WebSocket\Connection;
use MVC\Error;

class Informer extends Application
{
    /**
     * @var array $aClient
     */
    private array $aClient = [];

    /**
     * @var array $aNickname
     */
    private array $aNickname = [];

    /**
     * Handles new connections to the application.
     * @param Connection $oConnection
     * @return void
     */
    public function onConnect(Connection $oConnection): void
    {
        $sId = $oConnection->getClientId();
        $this->aClient[$sId] = $oConnection;
        $this->aNickname[$sId] = 'Client' . rand(10, 999);
    }

    /**
     * Handles client disconnects.
     * @param Connection $oConnection
     * @return void
     */
    public function onDisconnect(Connection $oConnection): void
    {
        $sId = $oConnection->getClientId();
        unset($this->aClient[$sId], $this->aNickname[$sId]);
    }

    /**
     * Handles incoming data/requests.
     * If valid action is given the according method will be called.
     * @param string $data
     * @param Connection $oConnection
     * @return void
     * @throws \ReflectionException
     */
    public function onData(string $data, Connection $oConnection): void
    {
        try {
            $aDecodedData = $this->decodeData($data);

            // check if action is valid
            if ($aDecodedData['action'] !== 'echo')
            {
                return;
            }

            $sMessage = get($aDecodedData['data'], '');

            if ($sMessage === '')
            {
                return;
            }

            $sClientId = $oConnection->getClientId();
            $sMessage = $this->aNickname[$sClientId] . ': ' . $sMessage;
            $this->actionEcho($sMessage);
        } catch (\RuntimeException $oRuntimeException) {
            Error::exception($oRuntimeException);
        }
    }

    /**
     * Handles data pushed into the websocket server using the push-client.
     * @param array $aData
     */
    public function onIPCData(array $aData): void
    {
        $sActionName = 'action' . ucfirst($aData['action']);
        $aMessage = get($aData['data'], '');

        if (method_exists($this, $sActionName))
        {
            call_user_func(
                [$this, $sActionName],
                $aMessage
            );
        }
    }

    /**
     * Echoes data back to client(s).
     * @param string $sType
     * @param string $sMessage
     * @return bool
     */
    private function actionEcho(string $sMessage = '')
    {
        if (true === empty($sMessage))
        {
            return false;
        }

        $sEncodedData = $this->encodeData('echo', $sMessage);

        /** @var Connection $oConnection */
        foreach ($this->aClient as $oConnection)
        {
            $oConnection->send($sEncodedData);
        }

        return true;
    }
}
