#!/usr/bin/env sh

file=${1:?}
json=${2:?}
php="docker run --init -it --rm -v $(pwd):/app -w /app -u $(id -u):$(id -g) composer php"
source='[]'; [ -f "${file}" ] && source="json_decode(trim('$(cat "${file}")'), true, 512, JSON_THROW_ON_ERROR)"
output=$(${php} -r "$(cat <<EOF
echo json_encode(array_replace_recursive(
    ${source},
    json_decode(trim('${json}'), true, 512, JSON_THROW_ON_ERROR)
), JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR);
EOF
)")
[ $? -ne 0 ] && exit 1

echo "${output}" > "${file}"
