FROM cytopia/php-fpm-7.2
MAINTAINER Andrew Nowak "andrewnowak@quickenloans.com"

################################################################################
# PHP XDEBUG for dev
################################################################################

RUN yum --assumeyes install \
    php72-php-pecl-event \
    && yum clean all

ADD php-fpm.conf /etc/php-fpm.conf

WORKDIR /root

EXPOSE 9000
CMD ["php-fpm", "--nodaemonize"]