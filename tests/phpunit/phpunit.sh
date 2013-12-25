#!/bin/bash

if [[ $( type phpunit ) == '' ]]
then
	php $(dirname $0)/phpunit.phar --bootstrap=$(dirname $0)"/../../includes/Autoloader.php" "$@"
else
	phpunit --bootstrap=$(dirname $0)"/../../includes/Autoloader.php" "$@"
fi
