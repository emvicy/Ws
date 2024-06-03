#!/bin/bash

/bin/grep -R -l "<?php" ./ | grep -v ".md" | grep -v ".example" | grep -v ".sh" | grep -v "./etc/config/Ws/vendor/" > phan.list
