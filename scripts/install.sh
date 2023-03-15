#!bin/bash

install()
{
  composer install
  symfony console doctrine:database:create
  symfony console doctrine:schema:create
  symfony console lexik:jwt:generate-keypair
  symfony console do:fi:lo
}

install