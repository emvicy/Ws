<?php

/**
 * @name $WsModel
 */
namespace Ws\Model;

use Bloatless\WebSocket\PushClient;
use Bloatless\WebSocket\Server;
use MVC\Application;
use MVC\Config;
use MVC\Error;
use MVC\Lock;
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
    protected $sPidFileName;

    /**
     * @var string
     */
    protected $sLockFile;

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
        $this->oServer->setLogger(new Log());

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
     * @return Server
     */
    public function getServer()
    {
        return $this->oServer;
    }

    /**
     * run WebSocket Server
     * @return false|void
     * @throws \ReflectionException
     */
    public function serve()
    {
        $this->sLockFile = Lock::create(bReturn: true);

        if (false === $this->createPidFile())
        {
            return false;
        }

        $this->oServer->registerApplication('Informer', Informer::getInstance());

        // Default Timer
        $this->oServer->addTimer(1000, function () {
            $this->killOnIsMissingPidFile();
            $this->killOnMaintenance();
        });

        $this->oServer->run();
    }

    /**
     * @return bool success
     * @throws \ReflectionException
     */
    protected function createPidFile()
    {
        // create and save pidFileName
        $this->sPidFileName = Config::get_MVC_BASE_PATH()
            . '/'
            . str_replace(
                '{pid}',
                getmypid(),
                Config::MODULE('Ws')['sPidFileName']
            );

        // create pidFile
        return touch($this->sPidFileName);
    }

    /**
     * @return string
     */
    public function getPidFileName()
    {
        return $this->sPidFileName;
    }

    /**
     * remove pidFile and lockFile
     * @return void
     */
    public function freeService()
    {
        unlink($this->sPidFileName);
        unlink($this->sLockFile);
    }

    /**
     * kills running process if pidfile is missing
     * @return void
     * @throws \ReflectionException
     */
    protected function killOnIsMissingPidFile()
    {
        if (false === file_exists($this->sPidFileName))
        {
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

            unlink($this->sLockFile);
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

            unlink($this->sLockFile);
        }
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
        $sMessage = \Parsedown::instance()->text($oDTWsPackage->get_sMessage());

        $oPushClient = new PushClient(Config::MODULE('Ws')['socketFile']);
        $oPushClient->sendToApplication(
            $oDTWsPackage->get_sApp(),
            array(
                'action' => $oDTWsPackage->get_sAction(),
                'data' => $oDTWsPackage->get_sType() . '||' . $sMessage,
            )
        );
    }
}
