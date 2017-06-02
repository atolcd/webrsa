#!/bin/bash

ME="$0"
APP_DIR="`dirname "$ME"`"
WORK_DIR="$PWD"

# ------------------------------------------------------------------------------

find "$APP_DIR/Vendor/modelesodt" -type f -iname "*.odt.default" | while read -r ;
do
	NORMAL=`echo "$REPLY" |sed 's/\.default$//g'`

	if [ -f "$NORMAL" ] ; then
		CREATEDNORMAL=`stat -c %Y "$NORMAL"`
		CREATEDREPLY=`stat -c %Y "$REPLY"`

		if [[ $CREATEDNORMAL -le $CREATEDREPLY ]] ; then
			echo "Remplacement du fichier $NORMAL par le fichier $REPLY qui est plus récent"
			sudo mv "$REPLY" "$NORMAL"
		else
			echo "Le fichier $NORMAL est plus récent que le fichier $REPLY"
		fi
	else
		echo "Fichier $NORMAL absent, fichier $REPLY renommé"
		sudo mv "$REPLY" "$NORMAL"
	fi
done