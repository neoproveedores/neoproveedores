#!/usr/bin/env bash

npm install -g bower
bower install
npm install -g stylus
stylus web/styles
composer install
app/console neo:abilities:add
app/console neo:user:add
