# Docker Telegram Bot

```yaml

version: '2'

services:
  telebot:
    image: kimbtechnologies/telegrambot:latest
    container_name: telegram_mail
    volumes:
      - /var/docker-compose/telegram/rules.php:/home/php/telegram/rules.php:ro
    restart: always
    environment:
      - TELEGRAM_API_TOKEN=tbf
	    - MAIL_SERVER=tbf
	    - MAIL_USER=tbf
	    - MAIL_PW=tbf
	    - SYSDOMAIN=tbf
	    - DELETMAILS=tbf
    networks:
      mailman_mailman:
        ipv4_address: 172.19.199.8

# curl 172.19.199.8 => bei neuen Mails aufrufen!

networks:
    mailman_mailman:
        external: true

```