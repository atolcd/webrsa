#!/bin/bash

APP="/home/cbuffin/www/webrsa/WebRSA-trunk/app"
TMP="/tmp"

IFS=$(echo -en "\n\b")

# ------------------------------------------------------------------------------

function odt_lint() {
	modeleodt="$1"

	tmp_dirname="`echo "$modeleodt" | sed "s/\//_/g"`"
	tmp_dirname="$TMP/$tmp_dirname"

	mkdir -p "$tmp_dirname"
	unzip "$modeleodt" -d "$tmp_dirname" >> "/dev/null" 2>&1

	(
		success='1'

		cd "$tmp_dirname"
		for xml_file in `find . -name '*.xml'` ; do
			if [ -s "$xml_file" ] ; then
				xmllint --noout "$xml_file"

				if [ $? -ne 0 ] ; then
					success='0'
				fi
			fi
		done

		if [ $success -ne 0 ] ; then
			echo "[apply] $odf"
		else
			echo "[error] $odf"
		fi
	)

	rm -r "$tmp_dirname"
}

# ------------------------------------------------------------------------------

for odf in `find $APP/Vendor/modelesodt -name '*.od*' | sort` ; do
	odt_lint "$odf"
done