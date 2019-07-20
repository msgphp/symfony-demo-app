#!/usr/bin/env sh

force=0; [ "$1" = -f ] && shift && force=1
file=${1:?}
json=${2:?}

php="docker run --init -i --rm -v $(pwd):/app -w /app -u $(id -u):$(id -g) composer php"
source='{}'; [ -f "${file}" ] && source=$(cat "${file}")

left=$(printf '%s' "${source}" | ${php} -r "var_export(json_decode(trim(file_get_contents('php://stdin')), true, 512, JSON_THROW_ON_ERROR));")
right=$(printf '%s' "${json}" | ${php} -r "var_export(json_decode(trim(file_get_contents('php://stdin')), true, 512, JSON_THROW_ON_ERROR));")
args="${right}, ${left}"; [ ${force} -eq 1 ] && args="${left}, ${right}"
output=$(${php} -r "$(cat <<EOF
echo json_encode(array_replace_recursive(${args}), JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR);
EOF
)")
[ $? -ne 0 ] && exit 1

echo "${output}" > "${file}"
