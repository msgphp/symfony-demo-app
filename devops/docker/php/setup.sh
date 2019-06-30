#!/usr/bin/env sh

version=${ICU:?missing ICU version}
file="icu/src-${version}.tgz"

if [ ! -f ${file} ]; then
    curl -sS -o "${file}" --fail -L "http://download.icu-project.org/files/icu4c/${version}/icu4c-$(echo ${version} | tr '.' '_')-src.tgz"
    [ $? -ne 0 ] && exit 1
fi
