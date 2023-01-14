FROM php:8.2.1-apache

COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
COPY ./dump/db.sql /var/www/db/db.sql 
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN docker-php-ext-install mysqli
RUN apt update
RUN apt install -y python3 python3-pip sudo
RUN pip3 install RPi.GPIO PyMLX90614

ENTRYPOINT [ "bash", "-c", "/usr/local/bin/entrypoint.sh" ]
CMD [ "apache2-foreground" ]