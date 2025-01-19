#!/bin/bash

MODULENAME="$(basename "$(pwd)")";
sHere=`pwd`;
sAppRoot=`realpath "../../"`;
sModuleDir=`realpath "../../modules/"`;
xGit=`type -p git`;
xPhp=`type -p php`;

#------------------------------------------------------------
# read .env
. ../../.env;

#------------------------------------------------------------
# maintenance

echo -e "\ninstalling...";
cd "$sAppRoot";

# maintenance file
#/usr/bin/touch 'maintenance';

#------------------------------------------------------------
# public files
cd "$sHere";
. _publish.sh

#------------------------------------------------------------
# done

cd "$sAppRoot";

$xPhp emvicy up;

# clear cache
$xPhp emvicy cc;

# datatypes
$xPhp emvicy dt;

cd "$sHere";
echo -e "installing complete.\n\n";

