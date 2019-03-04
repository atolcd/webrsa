#!/bin/bash

ME="$0"
APP_DIR="`dirname "$ME"`"
MAIL="webrsa@atolcd.com"

echo ""
echo "     ----------------------------------------------------------------------"
echo "     $APP_DIR"
echo "     ----------------------------------------------------------------------"
echo ""


# ------------------------------------------------------------------------------
function __clearDir() {
	local dir="$1"

	if [ -d "$dir" ]
	then
		(
			cd "$dir"
			find . -type f -not -path '*/.svn/*' -not -name "empty" | while read -r ; do rm "$REPLY"; done
		)
	fi
}


# ------------------------------------------------------------------------------
function __clear() {
	local dir="$1"

	__clearDir "$dir/tmp/cache/"
	__clearDir "$dir/tmp/logs/"

	if [ -d "$dir/tmp/files/" ] ; then
		rm -R "$dir/tmp/files/"
	fi

	rm -f $dir/tmp/logs/*.log
	rm -f $dir/tmp/logs/*_odt
	rm -f $dir/tmp/logs/*.csv
}


# ------------------------------------------------------------------------------
function __deploy() {
	dir="$1"
	APP_LINK=$(readlink -f $(dirname $0))
	APP_DEPARTEMENT=$(readlink -f $(dirname $0)/../..)

	ln -s $APP_DEPARTEMENT/contrib/modelesodt $APP_LINK/Vendor/modelesodt

	echo "     Lien symbolique des modèles odt"
	echo "     $APP_DEPARTEMENT/contrib/modelesodt vers $APP_LINK/Vendor/modelesodt"
	echo "     Pour supprimer le lien symbolique : unlink $APP_LINK/Vendor/modelesodt"
	echo ""

	ln -s $APP_DEPARTEMENT/contrib/didacticiel/app/View/Pages $APP_LINK/View/Pages

	echo "     Lien symbolique du didacticiel"
	echo "     $APP_DEPARTEMENT/contrib/didacticiel/app/View/Pages vers $APP_LINK/View/Pages"
	echo "     Pour supprimer le lien symbolique : unlink $APP_LINK/View/Pages"
	echo ""

	ln -s $APP_DEPARTEMENT/contrib/didacticiel/app/webroot/didac $APP_LINK/webroot/didac

	echo "     $APP_DEPARTEMENT/contrib/didacticiel/app/webroot/didac vers $APP_LINK/webroot/didac"
	echo "     Pour supprimer le lien symbolique : unlink $APP_LINK/webroot/didac"
	echo ""

	echo "     Droits et permissions"
	echo ""

	sudo chown apache: -R .
	sudo chmod u+r,g+r,o+r -R app
	sudo chmod u+w,g+w -R app/tmp
	chmod +x vendor/cakephp/cakephp/lib/Cake/Console/cake

	chmod 755 -R app/
	chmod 755 -R vendor/
	chmod 777 -R app/tmp/

	echo "     Cache"
	echo ""

	bash $dir/webrsa.sh clearcache
}


# ------------------------------------------------------------------------------
function __error() {
	echo ""
	echo "     ----------------------------------------------------------------------"
	echo "     La fonctionnalité << $1 >> est inexistante ou obsolète."
	echo "     Vous pouvez envoyer votre demande à << $MAIL >> en expliquant votre besoin."
	echo "     ----------------------------------------------------------------------"
	echo ""
	echo ""
}


# ------------------------------------------------------------------------------
case $1 in
	clear)
		__clear "$APP_DIR"
		exit 0
	;;
	clearcache)
		__clearDir "$APP_DIR/tmp/cache/"
		exit 0
	;;
	clearlogs)
		__clearDir "$APP_DIR/tmp/logs/"
		exit 0
	;;
	deploy)
		__deploy "$APP_DIR" "$2"
		exit 0
	;;
	*)
		echo ""
		echo "     Usage: $ME {clearcache|deploy}"
		echo ""
		__error "$1"
		exit 1
	;;
esac
