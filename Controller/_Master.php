<?php

/**
 * @name $WsController
 */
namespace Ws\Controller;

use App\Controller;
use MVC\DataType\DTRequestIn;
use MVC\DataType\DTRoute;
use MVC\MVCTrait\TraitDataType;

/**
 * @extends Controller
 */
class _Master extends Controller
{
    use TraitDataType;

    /**
     * @return void
     * @throws \ReflectionException
     */
    public static function __preconstruct()
    {
        parent::__preconstruct();
    }

    /**
     * @param \MVC\DataType\DTRequestIn $oDTRequestIn
     * @param \MVC\DataType\DTRoute          $oDTRoute
     * @throws \ReflectionException
     */
    public function __construct(DTRequestIn $oDTRequestIn, DTRoute $oDTRoute)
	{
        parent::__construct($oDTRequestIn, $oDTRoute);
    }

    public function __destruct()
    {
        ;
    }
}
