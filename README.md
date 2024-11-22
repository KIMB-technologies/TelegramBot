# Docker TelegramBot

This Docker Image provides a TelegramBot which can fetch emails via IMAP and forward them or only their subjects to Telegram Chats.

## Configuration

You will need an IMAP Mailserver (account), a Docker host and a TelegramBot token. Create the last using the [BotFather](https://core.telegram.org/bots). 

The IMAP account should have multiple email addresses, so the TelegramBot can handle multiple Telegram Chats as destination for the mails.

1. Create the IMAP account.
2. Prepare the Docker container.
    1. Copy the `docker-compose.yml` and fill in the details.
    2. Copy the `rules.php` and fill with rules.
    3. Add the `log` folder, if you want to.
        - set the rights using `docker exec -it --user root telegram_mail chown -R php:php /home/php/telegram/log`
3. Start the docker container, e.g. `docker-compose up -d`.
4. Test the setup, send a Mail to the IMAP account covered by a rule. 
    1. Call `curl 172.19.198.8` from the host, the container should answer `OK`.
    2. The Telegram message should arrive.
5. Make sure to notify the container about new emails, e.g.:
    1. Use a cronjob and call every `x` minutes.
    2. Use `incron` and monitor the IMAP (Dovecot) folder for the user -- use `IN_MOVED_TO`.
    3. Use a Postfix Filter to call the script.
    4. ...

### TelegramBot Rules

The `rules.php` which should be mapped into the container at `/home/php/telegram/rules.php`. You may use the `:ro` 
flag.

 - `RULES::TELEGRAM` is an array and defines the email address which receives the emails and the telegram chat,
    where the mails are send to.
    - `mailto` the user-part of the email (`@SYSDOMAIN` will be appended).
    - `tag` a tag to name the mails in the telegram chats
    -  `telto` an array of telegram users (first send a message to the bot and the see the id at `docker exec -it telegram_mail php ./telegram_info.php`)
- `RULES::MAILOVERVIEW` is an array and defines the email address which receives the emails and the telegram chat,
    where reports about the subject of the mails are send to.
    - see at `RULES::TELEGRAM`

```php

<?php
class RULES {
	//Telegram pushes, one array per rule
	const TELEGRAM = array(
		array(
			'mailto' => array( 'test'), // the addresses the mails goes to (only small letters, <part>@SYSDOMAIN)
			'tag' => '[TELEGRAM-TEST]', // TAG to be prepended to subject
			'telto' => array( '0000000' ) // the telegram chat, to send the mail to
		),
		// ....
	);
	//Mailoverview
	const MAILOVERVIEW = array(
		array(
			'mailto' => 'test', // the addresses the mails goes to (only small letters, <part>@SYSDOMAIN)
			'tag' => '[Overview-Test]', // TAG to be used as subject
			'telto' => array( '0000000' ) // the telegram chat, to send the mail to
		),
		// ...
	);
}
?>


```

### Docker Compose

The `docker-compose.yml` to setup the Docker Image.

- `TELEGRAM_API_TOKEN` The TelegramBot token created by the BotFather.
- `MAIL_SERVER` The IMAP server in [PHP notation](https://www.php.net/manual/de/function.imap-open.php), e.g. `{localhost:993/imap/ssl}INBOX`
- `MAIL_USER` The username for the IMAP account.
- `MAIL_PW` The password for the IMAP account.
- `SYSDOMAIN` The domain wich will be appended to the `mailto` parts in the rules.
- `DELETMAILS` Should the mails be deleted? `true, false` (If using `false` mails will be forwarded forever.)

```yaml

services:
  telebot:
    image: kimbtechnologies/telegrambot:latest
    container_name: telegram_mail
    volumes:
      - ./rules.php:/home/php/telegram/rules.php:ro # load the rules from outside, rules.php has to be placed on the host manually.
      # - ./log/:/home/php/telegram/log # place the logs outside of the container, make sure php has the right to write the files
    restart: always
    environment:
      - TELEGRAM_API_TOKEN=tbf
      - MAIL_SERVER=tbf
      - MAIL_USER=tbf
      - MAIL_PW=tbf
      - SYSDOMAIN=tbf
      - DELETMAILS=tbf
    networks:
      telegram:
       ipv4_address: 172.19.198.8
    restart: always

networks:
  telegram:
    driver: bridge
    ipam:
      driver: default
      config:
        -
          subnet: 172.19.198.0/24

```

### New Mail Script

The script which runs the bot and should be called on new mails.
(It just does a HTTP request to the container, the container will do the rest.)

```bash

#!/bin/sh
curl -s 172.19.198.8 > /dev/null

```

