#!/bin/bash

# GAT u.a.
. /home/admin1/_config/.env;

sRepository="emvicy/Ws";
sGitUser="gueff";
sBranch="main";

#--------------------

# update phanlist
. _phanlistcreate.sh

git remote set-url origin "https://$sGitUser:$sGAT@github.com/$sRepository"

# update
git pull;
