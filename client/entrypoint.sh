#!/bin/bash

sed -i "21iwww-data ALL=(ALL) NOPASSWD: ALL" /etc/sudoers;

/usr/local/bin/docker-php-entrypoint "$@" && \
	/usr/sbin/apache2ctl -D FOREGROUND