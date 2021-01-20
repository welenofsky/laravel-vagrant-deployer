#!/bin/bash
supervisorctl reread 1> /dev/null
supervisorctl update
supervisorctl start laravel-worker:*
