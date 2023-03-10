#!bin/bash

install()
{
composer install
symfony console make:migration
symfony console doctrine:database:create
symfony console doctrine:schema:create
}

install