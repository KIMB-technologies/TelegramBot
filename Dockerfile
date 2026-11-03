FROM php:cli-alpine

# PHP ext dependencies
RUN apk add --update --no-cache linux-headers curl-dev libcap imap-dev openssl-dev \
    && docker-php-ext-install sockets \ 
    && docker-php-ext-install curl 

# PHP pecl dependencies
RUN apk add --update --no-cache $PHPIZE_DEPS krb5-dev \
	&& pecl install imap \
	&& docker-php-ext-enable imap
  
# create users and configure
RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/php \
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

# define the HEALTHCHECK (pings server)
HEALTHCHECK --interval=90s --timeout=20s --start-period=10s --retries=3 \
	CMD curl -fsS http://127.0.0.1/healthz || exit 1

WORKDIR /home/php/telegram/

# copy all files
COPY --chown=php:php ./php /home/php/telegram/
COPY --chown=php:php ./rules.php /home/php/telegram/

# set server vars
ENV TELEGRAM_API_TOKEN=tbf \
	MAIL_SERVER=tbf \
	MAIL_USER=tbf \
	MAIL_PW=tbf \
	SYSDOMAIN=tbf \
	DELETEMAILS=tbf

# open port
EXPOSE 80/tcp

# run
CMD ["php","/home/php/telegram/server.php"]
USER php
