<?php

declare(strict_types=1);

namespace Bloatless\WebSocket\Application;

use Bloatless\WebSocket\Connection;

interface ApplicationInterface
{
    /**
     * This method is tirggered when a new client connects to server/application.
     *
     * @param Connection $oConnection
     */
    public function onConnect(Connection $oConnection): void;

    /**
     * This methods is triggered when a client disconnects from server/application.
     *
     * @param Connection $oConnection
     */
    public function onDisconnect(Connection $oConnection): void;

    /**
     * This method is triggered when the server recieves new data from a client.
     *
     * @param string $data
     * @param Connection $oConnection
     */
    public function onData(string $data, Connection $oConnection): void;

    /**
     * This method is called when server recieves to for an application on the IPC socket.
     *
     * @param array $aData
     */
    public function onIPCData(array $aData): void;

    /**
     * Creates and returns a new instance of the application.
     *
     * @return ApplicationInterface
     */
    public static function getInstance(): ApplicationInterface;
}
