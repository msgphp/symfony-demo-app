#!/usr/bin/env sh

file=${1:?}
json=${2:?}
php="docker run --init -it --rm -v $(pwd):/app -w /app -u $(id -u):$(id -g) composer php"

if [ -f "${file}" ]; then
    source=$(cat "${file}")
    contents=$(${php} -r "$(cat <<EOF
echo json_encode(
    array_replace_recursive(
        json_decode(trim('${source}'), true, 512, JSON_THROW_ON_ERROR),
        json_decode(trim('${json}'), true, 512, JSON_THROW_ON_ERROR)
    ),
    JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR
);
EOF
    )")
else
    contents=$(${php} -r "$(cat <<EOF
echo json_encode(
    json_decode(trim('${json}'), true, 512, JSON_THROW_ON_ERROR),
    JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR
);
EOF
    )")
fi

[ $? -ne 0 ] && exit 1

echo "${contents}" > "${file}"
