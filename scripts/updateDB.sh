#!bin/bash

updateDB()
{
  symfony console make:migration
  symfony console doctrine:migration:migrate
}

updateDB