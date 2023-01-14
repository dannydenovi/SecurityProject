#!/bin/bash

sed -i "21iwww-data ALL=(ALL) NOPASSWD: ALL" /etc/sudoers;
import -u www-data -p security_project < /var/www/db/db.sql;

/usr/local/bin/docker-php-entrypoint "$@" && \
	/usr/sbin/apache2ctl -D FOREGROUND