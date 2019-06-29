#!/usr/bin/env sh

ref=${1:-HEAD}
hash=$(git rev-parse --verify -q --short "${ref}")
[ $? -ne 0 ] && echo "Invalid GIT ref" >&2 && exit 1

dir=${2:-$(pwd)}
name=${3:-"app-${hash}"}

if [ "${ref}" = 'HEAD' ] || [ "${ref}" = "$(git rev-parse --abbrev-ref HEAD)" ]; then
    [ "$(git status --porcelain)" ] && echo "WARNING: local changes are EXCLUDED in archive!" >&2
fi
[ ! "$(git branch -r --contains "${hash}")" ] && echo "WARNING: un-pushed commits are INCLUDED in archive!" >&2

file="${dir}/${name}.tgz"
if [ ! -f "${file}" ]; then
    mkdir -p "${dir}" && git archive --output "${file}" --format tgz "${hash}"
    [ $? -ne 0 ] && exit 1
fi

echo "${file}"
