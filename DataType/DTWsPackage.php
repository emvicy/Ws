<?php

/**
 * @name $WsDataType
 */
namespace Ws\DataType;

use MVC\DataType\DTValue;
use MVC\MVCTrait\TraitDataType;

class DTWsPackage
{
	use TraitDataType;

	public const DTHASH = 'fd955f01507ae8482d7239982826f37e';

	/**
	 * @required true
	 * @var string
	 */
	protected $sApp;

	/**
	 * @required true
	 * @var string
	 */
	protected $sAction;

	/**
	 * @required true
	 * @var string
	 */
	protected $sMessage;

	/**
	 * @required true
	 * @var string
	 */
	protected $sType;

	/**
	 * DTWsPackage constructor.
	 * @param DTValue $oDTValue
	 * @throws \ReflectionException 
	 */
	protected function __construct(DTValue $oDTValue)
	{
		\MVC\Event::run('DTWsPackage.__construct.before', $oDTValue);
		$aData = $oDTValue->get_mValue();
		$this->sApp = "Informer";
		$this->sAction = "echo";
		$this->sMessage = '';
		$this->sType = "info";
		$this->setProperties($oDTValue);

		$oDTValue = DTValue::create()->set_mValue($aData); 
		\MVC\Event::run('DTWsPackage.__construct.after', $oDTValue);
	}

    /**
     * @param array|null $aData
     * @return DTWsPackage
     * @throws \ReflectionException
     */
    public static function create(?array $aData = array())
    {            
        (null === $aData) ? $aData = array() : false;
        $oDTValue = DTValue::create()->set_mValue($aData);
		\MVC\Event::run('DTWsPackage.create.before', $oDTValue);
		$oObject = new self($oDTValue);
        $oDTValue = DTValue::create()->set_mValue($oObject); \MVC\Event::run('DTWsPackage.create.after', $oDTValue);

        return $oDTValue->get_mValue();
    }

	/**
	 * @param string $mValue 
	 * @return $this
	 * @throws \ReflectionException
	 */
	public function set_sApp(string $mValue)
	{
		$oDTValue = DTValue::create()->set_mValue($mValue); 
		\MVC\Event::run('DTWsPackage.set_sApp.before', $oDTValue);
		$this->sApp = (string) $oDTValue->get_mValue();

		return $this;
	}

	/**
	 * @param string $mValue 
	 * @return $this
	 * @throws \ReflectionException
	 */
	public function set_sAction(string $mValue)
	{
		$oDTValue = DTValue::create()->set_mValue($mValue); 
		\MVC\Event::run('DTWsPackage.set_sAction.before', $oDTValue);
		$this->sAction = (string) $oDTValue->get_mValue();

		return $this;
	}

	/**
	 * @param string $mValue 
	 * @return $this
	 * @throws \ReflectionException
	 */
	public function set_sMessage(string $mValue)
	{
		$oDTValue = DTValue::create()->set_mValue($mValue); 
		\MVC\Event::run('DTWsPackage.set_sMessage.before', $oDTValue);
		$this->sMessage = (string) $oDTValue->get_mValue();

		return $this;
	}

	/**
	 * @param string $mValue 
	 * @return $this
	 * @throws \ReflectionException
	 */
	public function set_sType(string $mValue)
	{
		$oDTValue = DTValue::create()->set_mValue($mValue); 
		\MVC\Event::run('DTWsPackage.set_sType.before', $oDTValue);
		$this->sType = (string) $oDTValue->get_mValue();

		return $this;
	}

	/**
	 * @return string
	 * @throws \ReflectionException
	 */
	public function get_sApp() : string
	{
		$oDTValue = DTValue::create()->set_mValue($this->sApp); 
		\MVC\Event::run('DTWsPackage.get_sApp.before', $oDTValue);

		return $oDTValue->get_mValue();
	}

	/**
	 * @return string
	 * @throws \ReflectionException
	 */
	public function get_sAction() : string
	{
		$oDTValue = DTValue::create()->set_mValue($this->sAction); 
		\MVC\Event::run('DTWsPackage.get_sAction.before', $oDTValue);

		return $oDTValue->get_mValue();
	}

	/**
	 * @return string
	 * @throws \ReflectionException
	 */
	public function get_sMessage() : string
	{
		$oDTValue = DTValue::create()->set_mValue($this->sMessage); 
		\MVC\Event::run('DTWsPackage.get_sMessage.before', $oDTValue);

		return $oDTValue->get_mValue();
	}

	/**
	 * @return string
	 * @throws \ReflectionException
	 */
	public function get_sType() : string
	{
		$oDTValue = DTValue::create()->set_mValue($this->sType); 
		\MVC\Event::run('DTWsPackage.get_sType.before', $oDTValue);

		return $oDTValue->get_mValue();
	}

	/**
	 * @return string
	 */
	public static function getPropertyName_sApp()
	{
        return 'sApp';
	}

	/**
	 * @return string
	 */
	public static function getPropertyName_sAction()
	{
        return 'sAction';
	}

	/**
	 * @return string
	 */
	public static function getPropertyName_sMessage()
	{
        return 'sMessage';
	}

	/**
	 * @return string
	 */
	public static function getPropertyName_sType()
	{
        return 'sType';
	}

	/**
	 * @return false|string JSON
	 */
	public function __toString()
	{
        return $this->getPropertyJson();
	}

	/**
	 * @return false|string
	 */
	public function getPropertyJson()
	{
        return json_encode(\MVC\Convert::objectToArray($this));
	}

	/**
	 * @return array
	 */
	public function getPropertyArray()
	{
        return get_object_vars($this);
	}

	/**
	 * @return array
	 * @throws \ReflectionException
	 */
	public function getConstantArray()
	{
		$oReflectionClass = new \ReflectionClass($this);
		$aConstant = $oReflectionClass->getConstants();

		return $aConstant;
	}

	/**
	 * @return $this
	 */
	public function flushProperties()
	{
		foreach ($this->getPropertyArray() as $sKey => $mValue)
		{
			$sMethod = 'set_' . $sKey;

			if (method_exists($this, $sMethod)) 
			{
				$this->$sMethod('');
			}
		}

		return $this;
	}

}
