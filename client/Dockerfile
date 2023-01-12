FROM php:8.2.1-apache 
RUN docker-php-ext-install mysqli
RUN apt update
RUN apt install -y python3 python3-pip sudo
RUN pip3 install RPi.GPIO PyMLX90614