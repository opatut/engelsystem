#!/bin/bash

set -e

wait-for-it mysql:3306
sleep 2 # give it time to set up the database
bin/migrate
apache2-foreground
