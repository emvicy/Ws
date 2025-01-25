<?php

/**
 * @name $WsController
 */
namespace Ws\Controller;

use MVC\DataType\DTRequestIn;
use MVC\DataType\DTRoute;
use MVC\Process;
use MVC\Request;
use Ws\DataType\DTWsPackage;

/**
 * @extends \Ws\Controller\_Master
 */
class Ws extends _Master
{
    /**
     * @param \MVC\DataType\DTRequestIn $oDTRequestIn
     * @param \MVC\DataType\DTRoute          $oDTRoute
     * @throws \ReflectionException
     */
    public function __construct(DTRequestIn $oDTRequestIn, DTRoute $oDTRoute)
    {
        // CLI only
        if (false === Request::in()->get_isCli())
        {
            echo 'CLI only. Abort.';
            exit();
        }

        parent::__construct($oDTRequestIn, $oDTRoute);
    }

    /**
     * starts WebSocket Server
     * @param \MVC\DataType\DTRequestIn $oDTRequestIn
     * @param \MVC\DataType\DTRoute          $oDTRoute
     * @return void
     * @throws \ReflectionException
     */
	public function serve(DTRequestIn $oDTRequestIn, DTRoute $oDTRoute)
	{
        \Ws\Model\Ws::init()->serve();
	}

    /**
     * push Messages to Server
     * @param DTRequestIn $oDTRequestIn
     * @param DTRoute $oDTRoute
     * @return void
     * @throws \ReflectionException
     */
    public function pushtest(DTRequestIn $oDTRequestIn, DTRoute $oDTRoute)
    {
        Process::savePid();

        while (true)
        {
            $aType = array('info', 'success', 'notice', 'error');
            $iRand = rand(0, 3);

            \Ws\Model\Ws::init()->push(
                DTWsPackage::create()
                    ->set_sType($aType[$iRand])
                    ->set_sMessage("<code>" . __METHOD__ . "</code>\n**PUSH TEST** at " . date('Y-m-d H:i:s') . "\n<code>pid: " . getmypid() . "</code>\n")
            );
            sleep(5);
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function __destruct()
    {
        parent::__destruct();
        \Ws\Model\Ws::init()->freeService();
    }
}