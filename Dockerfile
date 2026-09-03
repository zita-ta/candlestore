FROM php:8.2-apache

ARG CACHEBUST=2

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite

# Fix "More than one MPM loaded" - hapus SEMUA modul mpm dulu, baru pasang prefork
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf \
    && apache2ctl -M 2>&1 | grep mpm || true

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
