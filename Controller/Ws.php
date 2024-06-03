<?php

/**
 * @name $WsController
 */
namespace Ws\Controller;

use MVC\DataType\DTRequestCurrent;
use MVC\DataType\DTRoute;
use MVC\Request;
use Ws\DataType\DTWsPackage;

/**
 * @extends \Ws\Controller\_Master
 */
class Ws extends _Master
{
    /**
     * @param \MVC\DataType\DTRequestCurrent $oDTRequestCurrent
     * @param \MVC\DataType\DTRoute          $oDTRoute
     * @throws \ReflectionException
     */
    public function __construct(DTRequestCurrent $oDTRequestCurrent, DTRoute $oDTRoute)
    {
        // CLI only
        if (false === Request::isCli())
        {
            echo 'CLI only. Abort.';
            exit();
        }

        parent::__construct($oDTRequestCurrent, $oDTRoute);
    }

    /**
     * starts WebSocket Server
     * @param \MVC\DataType\DTRequestCurrent $oDTRequestCurrent
     * @param \MVC\DataType\DTRoute          $oDTRoute
     * @return void
     * @throws \ReflectionException
     */
	public function serve(DTRequestCurrent $oDTRequestCurrent, DTRoute $oDTRoute)
	{
        \Ws\Model\Ws::init()->serve();
	}

    /**
     * push Messages to Server
     * @param DTRequestCurrent $oDTRequestCurrent
     * @param DTRoute $oDTRoute
     * @return void
     * @throws \ReflectionException
     */
    public function pushtest(DTRequestCurrent $oDTRequestCurrent, DTRoute $oDTRoute)
    {
        while (true)
        {
            $aType = array('info', 'success', 'notice', 'error');
            $iRand = rand(0, 3);

            \Ws\Model\Ws::init()->push(
                DTWsPackage::create()
                    ->set_sType($aType[$iRand])
                    ->set_sMessage('**PUSH TEST** at ' . date('Y-m-d H:i:s'))
            );
            sleep(5);
        }
    }

    public function __destruct()
    {
        parent::__destruct();
        \Ws\Model\Ws::init()->freeService();
    }
}