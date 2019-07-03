#!/usr/bin/env sh

if [ -f mailhog/mhsendmail_linux_amd64 ]; then
    curl -sS -o mailhog/mhsendmail_linux_amd64 --fail -L https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64
    [ $? -ne 0 ] && exit 1
fi

exit 0
