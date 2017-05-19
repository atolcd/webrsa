#!/bin/bash

# Ce script bash permet de changer une chaîne de caractères par une autre
# (SRC_STRING sera remplacé par DST_STRING) au sein de documents .odt se trouvant
# dans le répertoire SRC_DIR ou un de ses sous-répertoires. Le répertoire TMP_DIR
# doit être accessible en écriture car c'est dans ce répertoire que seront
# décompressés les fichiers .odt.
# Seuls les fichiers contentant la chaîne de caractères à remplacer se retrouvent
# dans DST_DIR.

SRC_DIR="/home/cbuffin/www/webrsa/trunk/app/vendors/modelesodt"
DST_DIR="/home/cbuffin/tmp/sed_modelesodt"
TMP_DIR="/tmp"

SRC_STRING="Sylvie FABRE-ORTUNO"
DST_STRING="Arnaud AUZOLAT"

# ==============================================================================

find "$SRC_DIR" -type f -iname "*.odt" | while read -r ;
do
	echo "Traitement du fichier $REPLY"

	tmp_dirname="`echo "$REPLY" | sed "s/\//_/g"`"
	tmp_filename="$tmp_dirname.zip"
	tmp_dirname="$TMP_DIR/$tmp_dirname"

	mkdir -p "$tmp_dirname"
	unzip "$REPLY" -d "$tmp_dirname" >> "/dev/null" 2>&1

	(
		cd "$tmp_dirname"

		grep "$SRC_STRING" "$tmp_dirname/content.xml" >> "/dev/null" 2>&1
		if [ "$?" -eq "0" ] ; then
			sed -i "s/$SRC_STRING/$DST_STRING/g" "$tmp_dirname/content.xml"
			zip -o -r -m "../$tmp_filename" . >> "/dev/null" 2>&1

			NEW_DIR="`echo "${REPLY/$SRC_DIR/$DST_DIR}" | xargs dirname`"
			mkdir -p "$NEW_DIR"
			mv "../$tmp_filename" "${REPLY/$SRC_DIR/$DST_DIR}"
		fi
	)

	rm -R "$tmp_dirname"
done

# ==============================================================================