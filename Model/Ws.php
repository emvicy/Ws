<?php

/**
 * @name $WsModel
 */
namespace Ws\Model;

use Bloatless\WebSocket\PushClient;
use Bloatless\WebSocket\Server;
use MVC\Application;
use MVC\Cache;
use MVC\Config;
use MVC\Error;
use MVC\Lock;
use MVC\Process;
use Ws\DataType\DTWsPackage;

/**
 * Index
 */
class Ws
{
    /**
     * @var Server
     */
    protected $oServer;

    /**
     * @var string
     */
    protected $iPid;

    /**
     * @var string
     */
    protected $sLockFile;

    /**
     * @var null
     */
    protected static $_oInstance = null;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->oServer = new Server(
            Config::MODULE('Ws')['sAddress'],
            Config::MODULE('Ws')['iPort'],
            Config::MODULE('Ws')['socketFile'],
        );

        // add a PSR-3 compatible logger (optional)
//        $this->oServer->setLogger(new Log());

        // server settings
        $this->oServer->setMaxClients(Config::MODULE('Ws')['iMaxClients']);
        $this->oServer->setCheckOrigin(Config::MODULE('Ws')['bCheckOrigin']);
        $this->oServer->setAllowedOrigin(Config::MODULE('Ws')['sOrigin']);
        $this->oServer->setMaxConnectionsPerIp(Config::MODULE('Ws')['iMaxConnectionsPerIp']);
    }

    /**
     * @return self|null
     */
    public static function init()
    {
        if (null === self::$_oInstance)
        {
            self::$_oInstance = new self();
        }

        return self::$_oInstance;
    }

    /**
     * run WebSocket Server
     * @return false|void
     * @throws \ReflectionException
     */
    public function serve()
    {
        if (true === Application::isMaintenance())
        {
            return false;
        }

        #---------------------------------------------------------------------------------------------------------------
        # Grace period

        $iMinimumWaitingSeconds = 10;
        (true === empty(Cache::getCache(__METHOD__))) ? Cache::saveCache(__METHOD__, time()) : false;
        $iTimePassed = (time() - (int) Cache::getCache(__METHOD__));
        $bPass = ($iTimePassed >= $iMinimumWaitingSeconds) ? true : false;
        (true === $bPass) ? Cache::saveCache(__METHOD__, time()) : false;

        if (false === $bPass)
        {
            return false;
        }

        #---------------------------------------------------------------------------------------------------------------

        $this->sLockFile = Lock::create(bReturn: true);

        if (false === Process::savePid())
        {
            return false;
        }

        if (true === self::isRunning())
        {
            return false;
        }

        #---------------------------------------------------------------------------------------------------------------
        # start server

        $this->oServer->registerApplication('Informer', Informer::getInstance());

        // Default Timer
        $this->oServer->addTimer(1000, function () {
            $this->killOnIsMissingPidFile();
            $this->killOnMissingSocketFile();
            $this->killOnMaintenance();
        });

        $this->oServer->run();
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->oServer;
    }

    /**
     * Important Hint: Push messages cannot be larger than 64kb!
     * This code shows how to push data into the running websocket server
     * In this case a system message is sent to the demo application registered as "chat"
     * @param DTWsPackage $oDTWsPackage
     * @return void
     * @throws \ReflectionException
     */
    public function push(DTWsPackage $oDTWsPackage)
    {
        if (false === self::isRunning())
        {
            return;
        }

        $sMessage = \Parsedown::instance()->text($oDTWsPackage->get_sMessage());

        try {
            $oPushClient = new PushClient(Config::MODULE('Ws')['socketFile']);
            $oPushClient->sendToApplication(
                $oDTWsPackage->get_sApp(),
                array(
                    'action' => $oDTWsPackage->get_sAction(),
                    'data' => $oDTWsPackage->get_sType() . '||' . $sMessage,
                )
            );
        } catch (\Exception $oException) {
            \MVC\Error::exception($oException);
        }
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function freeService()
    {
        Process::deletePidFile(getmypid());

        if (false === empty($this->sLockFile) && true === file_exists($this->sLockFile))
        {
            @unlink($this->sLockFile);
        }
    }

    /**
     * @return bool
     * @throws \ReflectionException
     */
    public static function isRunning()
    {
        @fsockopen(
            'tcp://' . Config::MODULE('Ws')['sAddress'], Config::MODULE('Ws')['iPort'],
            $iErrorCode,
            $sErrorMessage,
            2
        );

        return (true === empty($iErrorCode));
    }

    #-------------------------------------------------------------------------------------------------------------------
    # protected

    /**
     * kills running process if pidfile is missing
     * @return void
     * @throws \ReflectionException
     */
    protected function killOnIsMissingPidFile()
    {
        if (false === Process::hasPidFile(getmypid()))
        {
            $this->kill();
        }
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    protected function killOnMissingSocketFile()
    {
        if (false === file_exists(Config::MODULE('Ws')['socketFile']))
        {
            $this->kill();
        }
    }

    /**
     * kills running process if app is in maintenance mode
     * @return void
     * @throws \ReflectionException
     */
    protected function killOnMaintenance()
    {
        if (true === Application::isMaintenance())
        {
            $this->kill();
        }
    }

    #-------------------------------------------------------------------------------------------------------------------
    # private

    private function kill()
    {
        $this->freeService();
        Process::deletePidFile(getmypid());

        posix_kill(
            getmypid(),
            SIGKILL
        );

        if (posix_get_last_error() > 0)
        {
            Error::error(
                posix_strerror(posix_get_last_error())
            );
        }
    }
}
