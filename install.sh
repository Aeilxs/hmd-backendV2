#!bin/bash

install()
{
composer install
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
symfony console lexik:jwt:generate-keypair
}

install