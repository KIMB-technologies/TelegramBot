FROM php:8-cli-alpine

# PHP dependencies, create users
RUN apk add --update --no-cache curl-dev libcap imap-dev openssl-dev \
    && docker-php-ext-install sockets \ 
    && docker-php-ext-install curl \
    && PHP_OPENSSL=yes docker-php-ext-configure imap --with-imap --with-imap-ssl \
    && docker-php-ext-install imap \
    && setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/php \
    && addgroup -S php && adduser -S php -G php \
    && mkdir -p /home/php/telegram/ \
    && mkdir /home/php/telegram/log

# Owner for bind-mounted directories
RUN apk add --no-cache --virtual .build-deps build-base \
	&& echo "#include <sys/types.h>" > /bin/cchown.c \
	&& echo "#include <unistd.h>" >> /bin/cchown.c \
	&& echo 'int main (void) { setuid(0); return execl("/bin/chown", "chown", "-R", "php:php", "/home/php/telegram/", NULL); }' >> /bin/cchown.c \
	&& gcc /bin/cchown.c -o /bin/cchown \
	&& chown root:root /bin/cchown \
	&& chmod ugo+x /bin/cchown \
	&& chmod u+s /bin/cchown \
	&& rm /bin/cchown.c \
	&& apk del .build-deps

# copy all files
WORKDIR /home/php/telegram/
COPY --chown=php:php . /home/php/telegram/

RUN rm -rf /home/php/telegram/Dockerfile /home/php/telegram/.travis.yml /home/php/telegram/dockerpublish.sh

# set server vars
ENV TELEGRAM_API_TOKEN=tbf \
	MAIL_SERVER=tbf \
	MAIL_USER=tbf \
	MAIL_PW=tbf \
	SYSDOMAIN=tbf \
	DELETMAILS=tbf

# open port
EXPOSE 80/tcp

# run
CMD ["php","/home/php/telegram/server.php"]
USER php
