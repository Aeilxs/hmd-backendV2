#!bin/bash

resetDB()
{
  symfony console doctrine:database:drop --force
  symfony console doctrine:database:create
  symfony console doctrine:schema:create
}

resetDB