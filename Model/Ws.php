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
    protected $sPidFile;

    /**
     * @var string
     */
    protected $sLockFile;

    /**
     * @var string
     */
    protected $sSocketFile;

    /**
     * @var null
     */
    protected static $_oInstance = null;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->sSocketFile = Config::MODULE('Ws')['socketFile'];
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

        if (self::isRunning())
        {
            return false;
        }

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

    /**
     * @return string
     */
    public function getPidFileName()
    {
        return $this->sPidFile;
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function freeService()
    {
        if (false === empty($this->sPidFile) && true === file_exists($this->sPidFile))
        {
            unlink($this->sPidFile);
        }

        if (false === empty($this->sLockFile) && true === file_exists($this->sLockFile))
        {
            unlink($this->sLockFile);
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

        if (false === empty($iErrorCode))
        {
            Error::error($sErrorMessage, $iErrorCode);
        }

        return (true === empty($iErrorCode));
    }

    #-------------------------------------------------------------------------------------------------------------------
    # protected

    /**
     * @return bool success
     * @throws \ReflectionException
     */
    protected function createPidFile()
    {
        // create and save pidFileName
        $this->sPidFile = Config::get_MVC_BASE_PATH()
                          . '/'
                          . str_replace(
                '{pid}',
                getmypid(),
                Config::MODULE('Ws')['sPidFileName']
            );

        // create pidFile
        return touch($this->sPidFile);
    }

    /**
     * kills running process if pidfile is missing
     * @return void
     * @throws \ReflectionException
     */
    protected function killOnIsMissingPidFile()
    {
        if (false === file_exists($this->sPidFile))
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
