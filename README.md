# Docker Telegram Bot

```yaml

version: '2'

services:
  telebot:
    image: kimbtechnologies/telegrambot:latest
    container_name: telegram_mail
    volumes:
      - /var/docker-compose/telegram/rules.php:/home/php/telegram/rules.php:ro
      - /var/docker-compose/telegram/log/:/home/php/telegram/log
    restart: always
    environment:
      - TELEGRAM_API_TOKEN=tbf
      - MAIL_SERVER=tbf
      - MAIL_USER=tbf
      - MAIL_PW=tbf
      - SYSDOMAIN=tbf
      - DELETMAILS=tbf

```
