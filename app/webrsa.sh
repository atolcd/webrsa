#!/bin/bash

ME="$0"
APP_DIR="`dirname "$ME"`"
WORK_DIR="$PWD"
RELEASES_DIR="$WORK_DIR/releases"
ChangeLog="ChangeLog.txt"
ASNV="ssh://gerrit:29418/cd93/webrsa"
YUICOMPRESSOR="$HOME/bin/yuicompressor.jar"
echo $APP_DIR

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

# 	rm "$dir/webroot/js/webrsa.js">> /dev/null 2>&1
# 	rm "$dir/webroot/css/webrsa.css" >> /dev/null 2>&1
}

# ------------------------------------------------------------------------------

function __minify() {
	if [ ! -e "$YUICOMPRESSOR" ];then
		echo "Le fichier $YUICOMPRESSOR n'existe pas (pensez à changer le chemin vers le yuicompressor dans ce script)."
		return 1
	fi

	JSDIR="$1/webroot/js"
	CSSDIR="$1/webroot/css"

	# CSS
	CSSFILE="$CSSDIR/webrsa.tmp.css"
	echo "@media all {" > "$CSSFILE"
	cat "$CSSDIR/all.reset.css" "$CSSDIR/all.base.css" "$CSSDIR/bootstrap.custom.css" "$CSSDIR/all.form.css" "$CSSDIR/fileuploader.css" "$CSSDIR/fileuploader.webrsa.css" "$CSSDIR/permissions.css" "$1/Plugin/Configuration/webroot/css/configuration_parser.css" >> "$CSSFILE"
	echo "}" >> "$CSSFILE"

	echo "@media screen,presentation {" >> "$CSSFILE"
	cat "$CSSDIR/menu.css" "$CSSDIR/popup.css" "$CSSDIR/screen.generic.css" "$CSSDIR/screen.search.css" >> "$CSSFILE"
	echo "}" >> "$CSSFILE"

	echo "@media print {" >> "$CSSFILE"
	cat "$CSSDIR/print.generic.css" >> "$CSSFILE"
	echo "}" >> "$CSSFILE"

	java -jar "$YUICOMPRESSOR" "$CSSFILE" -o "$CSSDIR/webrsa.css" --charset utf-8
 	rm "$CSSFILE"

	# Javascript (Prototype)
	JSFILE="$JSDIR/webrsa.tmp.js"

	cat "$JSDIR/prototype.js" \
	"$JSDIR/webrsa.extended.prototype.js" \
	"$JSDIR/prototype.livepipe.js" \
	"$JSDIR/prototype.tabs.js" \
	"$JSDIR/tooltip.prototype.js" \
	"$JSDIR/webrsa.custom.prototype.js" \
	"$JSDIR/webrsa.common.prototype.js" \
	"$JSDIR/prototype.event.simulate.js" \
	"$JSDIR/dependantselect.js" \
	"$JSDIR/prototype.maskedinput.js" \
	"$JSDIR/webrsa.validaterules.js" \
	"$JSDIR/webrsa.validateforms.js" \
	"$JSDIR/webrsa.additional.js" \
	"$JSDIR/fileuploader.js" \
	"$JSDIR/fileuploader.webrsa.js" \
	"$JSDIR/cake.prototype.js" \
	"$JSDIR/webrsa.cake.tabbed.paginator.js" \
	"$JSDIR/prototype.fabtabulous.js" \
	"$JSDIR/prototype.tablekit.js" \
	"$1/Plugin/Configuration/webroot/js/prototype.configuration-parser.js" \
	> "$JSFILE"

	java -jar "$YUICOMPRESSOR" "$JSFILE" -o "$JSDIR/webrsa.js" --charset utf-8 --preserve-semi
	rm "$JSFILE"
}

# ------------------------------------------------------------------------------

function __predeploy() {
	dir="$1"
	version="$2"
	
	echo "Minify"
	bash $dir/webrsa.sh minify
	
	echo "Test Codes"	
	chmod +x app/webrsa.sh app/Doc/scripts/find_non_utf8.sh 
	bash $dir//Doc/scripts/find_non_utf8.sh
	chmod +x app/Doc/scripts/find_whitespace.sh
	bash $dir/Doc/scripts/find_whitespace.sh
	
}

# ------------------------------------------------------------------------------

function __deploy() {
	dir="$1"
	version="$2"
	
	echo "minify"
	bash $dir/webrsa.sh minify
	
	echo "droits et permissions"
	sudo chown apache: -R .
	sudo chmod u+r,g+r,o+r -R app
	sudo chmod u+w,g+w -R app/tmp
	chmod +x lib/Cake/Console/cake
	
	echo "cache"
	bash $dir/webrsa.sh clearcache
	
	echo "test modelesodt"
	sudo -u apache lib/Cake/Console/cake Gedooo.test_modeles_odt "$dir/Vendor/modelesodt"
	
}
# ------------------------------------------------------------------------------

function __deployconfig() {
	dir="$1"
	version="$2"
	
	echo "Déplacement Config"
	
	cd  $dir/webrsa/app/Console/
	mkdir /etc/webrsa/
	rm -Rf /etc/webrsa/*
	mv $dir/Config/* /etc/webrsa/
	rm -Rf $dir/Config
	ln -nfs /etc/webrsa/ $dir/Config
	cd /var/tmp/
	cd $dir/
	
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
	minify)
		__minify "$APP_DIR"
		exit 0
	;;
	__predeploy)
		__deploy "$APP_DIR" "$2"
		exit 0
	;;
	deploy)
		__deploy "$APP_DIR" "$2"
		exit 0
	;;
	deployconfig)
		__deployconfig "$APP_DIR" "$2"
		exit 0
	;;
	*)
		echo "Usage: $ME {clearcache|clear|clearlogs|minify|deploy|deployconfig}"
		exit 1
	;;
esac

#  Afin d'enlever l'extension defualt des fichiers ODT sans avoir à le faire à la main
#         (
#             cd "app/Vendor/modelesodt" && \
#             find . -type f -iname "*.odt.default" | while read -r ; do mv "$REPLY" `echo "$REPLY" |sed 's/\.default$//g'` ; done
#         )