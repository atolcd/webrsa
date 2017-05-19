#!/bin/bash
# Calcule le md5sum des fichiers .od* d'un répertoire et de ses sous-répertoires

if [ ! -d "$1" ] ; then
	echo "Le répertoire $1 n'existe pas."
	exit 1
fi

BASE_DIR="$1"

OLDIFS=$IFS
IFS=$'\n'
files=`find \
		"$BASE_DIR" \
		-iname "*.od*" \
		-exec echo {} \; \
		| grep -v "\.svn"`

for file in ${files[@]} ; do :
	md5sum "$file"
done
IFS=$OLDIFS