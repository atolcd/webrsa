#!/bin/bash

ME="$0"
HERE="`dirname "${ME}"`"
APP_DIR="`readlink -f "${HERE}/../.."`"

find "${APP_DIR}" -type f -regextype posix-extended -regex ".*(\.inc(.*)|.*\.php|.*\.ctp)" \! -regex ".*/(\.svn|\.git|Doc|Vendor)/.*" | while read -r ;
do
	file --mime "${REPLY}" | grep "charset=\(utf-8\|us-ascii\|binary\)" > /dev/null 2>&1 && \
		iconv -t UTF-8 < "${REPLY}" > /dev/null 2>&1

	if [ $? != 0 ] ; then
		echo "${REPLY}"
	fi
done