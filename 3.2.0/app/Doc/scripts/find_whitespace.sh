#!/bin/bash

# Petit script bash permettant de détecter les fichiers .php, .inc et .ctp
# contenant uniquement des espaces blancs après la balise fermante php (?>) ou
# contenant des espaces blancs en début de fichier
# Nécessite le programme pcregrep
# @link http://www.johnnyslink.com/technology/2012/06/15/use-grep-find-php-files-white-space

PCREGREP_PRESENT="`which pcregrep > /dev/null ; echo $?`"

if [ "$PCREGREP_PRESENT" != "0" ] ; then
	echo "Ce script nécessite le programme pcregrep."
	echo "Pour l'installer sur Ubuntu: sudo aptitude install pcregrep"
	exit 1
fi

ME="$0"
HERE="`dirname "${ME}"`"
APP_DIR="`readlink -f "${HERE}/../.."`"

(
	cd "$APP_DIR"

	find . -type f -iregex '.*\.\(php\|inc\|ctp\)' \! -path \*/\.svn/\* | while read -r ;
	do
		# Espace(s) en début de fichier
		head -n1 "$REPLY" | pcregrep -M '\A[\s\n]+' >> /dev/null
		if [ "$?" -eq "0" ] ; then
			echo "$REPLY" | sed "s/^\./Caractère(s) blanc(s) au début de: app/g"
		fi

		# Espace(s) en fin de fichier
		pcregrep -Ml '\?>\s*[ \t]+\z' "$REPLY" | sed "s/^\./Caractère(s) blanc(s) à la fin de: app/g"
	done
)