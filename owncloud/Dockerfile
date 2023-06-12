FROM owncloud/server:10.12

RUN apt-get update &&\
    apt-get install -y \
    lsb-release \
    sudo \
    wget \
    curl \
    less \
    patch \
    git \
    mariadb-client \
    emacs

#RUN apt-get install -y \
#    php7.4-xdebug \
#    phpunit

#RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - && \
#    apt-get install -y \
#            nodejs \
#            make

ADD init_impersonate.sh /app/init_impersonate.sh
ADD init.sh /app/init.sh
ADD Helper.patch /app/Helper.patch

RUN cp /var/www/owncloud/apps/files_trashbin/lib/Helper.php /var/www/owncloud/apps/files_trashbin/lib/Helper.php.orig
RUN patch /var/www/owncloud/apps/files_trashbin/lib/Helper.php -i /app/Helper.patch

CMD ["owncloud", "server"]
