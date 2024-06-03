<?php

# documentation
#   https://mymvc.ueffing.net/3.4.x/generating-datatype-classes#array_config
# creation
#   php emvicy.php datatype

#---------------------------------------------------------------
#  Defining DataType Classes

$sThisModuleDir = realpath(__DIR__ . '/../../../../');
$sThisModuleName = basename($sThisModuleDir);
$sThisModuleDataTypeDir = $sThisModuleDir . '/DataType';
$sThisModuleNamespace = str_replace('/', '\\', substr($sThisModuleDataTypeDir, strlen($aConfig['MVC_MODULES_DIR'] . '/')));

// base setup
$aDataType = array(

    // directory
    'dir' => $sThisModuleDataTypeDir,

    // remove complete dir before new creation
    'unlinkDir' => false,

    // enable creation of events in datatype methods
    'createEvents' => true,
);

$aDataType['class']['DTWsPackage'] = array(
    'name' => 'DTWsPackage',
    'file' => 'DTWsPackage.php',
    'namespace' => $sThisModuleNamespace,
    'createHelperMethods' => true,
    'constant' => array(),
    'property' => array(
        array('key' => 'sApp', 'var' => 'string', 'value' => 'Informer', 'required' => true, 'forceCasting' => true,),
        array('key' => 'sAction', 'var' => 'string', 'value' => 'echo', 'required' => true, 'forceCasting' => true,),
        array('key' => 'sMessage', 'var' => 'string', 'value' => '', 'required' => true, 'forceCasting' => true,),
        array('key' => 'sType', 'var' => 'string', 'value' => 'info', 'required' => true, 'forceCasting' => true,),
    )
);

#---------------------------------------------------------------
# copy settings to module's config
# in your code you can access this datatype config by: \MVC\Config::MODULE()['DATATYPE'];

$aConfig['MODULE'][$sThisModuleName]['DATATYPE'] = $aDataType;
