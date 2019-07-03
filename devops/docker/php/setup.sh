#!/usr/bin/env sh

version=${ICU:?}

if [ ! -f "icu/src-${version}.tgz" ]; then
    curl -sS -o "icu/src-${version}.tgz" --fail -L "http://download.icu-project.org/files/icu4c/${version}/icu4c-$(echo ${version} | tr '.' '_')-src.tgz"
    [ $? -ne 0 ] && exit 1
fi

exit 0
