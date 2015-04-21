#! /bin/bash
grep 'max_nesting_level' bin/behat || sed -i 's|<?php|<?php \n\nini_set("xdebug.max_nesting_level", 200);|' bin/behat
