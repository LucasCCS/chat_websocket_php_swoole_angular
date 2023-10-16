FROM phpswoole/swoole:4.8-alpine

RUN curl -sS https://getcomposer.org/installer | php -- \
--install-dir=/usr/bin --filename=composer

WORKDIR "/src"
COPY . "/src"

RUN composer install --no-dev --optimize-autoloader --no-scripts
RUN composer dump-autoload

STOPSIGNAL SIGTERM
EXPOSE 9501

CMD ["/bin/sh", "-c", "php server.php"]
