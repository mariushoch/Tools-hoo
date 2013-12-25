#!/bin/bash

if [ -r $(dirname $0)/phpunit.phar ]
then
	php $(dirname $0)/phpunit.phar --bootstrap=$(dirname $0)"/../../includes/Autoloader.php" "$@"
else
	phpunit --bootstrap=$(dirname $0)"/../../includes/Autoloader.php" "$@"
fi
