#!/bin/bash

ME="$0"
APP_DIR="`dirname "$ME"`"
WORK_DIR="$PWD"
RELEASES_DIR="$WORK_DIR/releases"
ChangeLog="ChangeLog.txt"
ASNV="svn://svn.adullact.net/svnroot/webrsa"
YUICOMPRESSOR="$HOME/bin/yuicompressor.jar"
echo $APP_DIR
# ------------------------------------------------------------------------------
# INFO: rgadr sur un char -> sed -i "s/<RGADR>\([1-3]\)<\/RGADR>/<RGADR>0\1<\/RGADR>/" XXX
# ------------------------------------------------------------------------------

function __svnDirExists() {
	svndir="$1"
	svn ls --verbose $svndir >> /dev/null 2>&1
	return=$?

	if [ $return -ne 0 ] ; then
		echo "Erreur: le répertoire $svndir n'existe pas"
	fi
	return $return
}

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

function __changelog() {
	version=${1}
	dir=${2}
	(
		cd $dir

		ChangeLogTmp="$ChangeLog.tmp"

		svn log $ASNV > $ChangeLogTmp

		startrev=`svn ls --verbose $ASNV/tags | grep " $version/" | sed -e 's/^ *//' | cut -d " " -f1`
		startline=`grep -n "^r$startrev" $ChangeLogTmp | cut -d ":" -f1`
		maxlines=`cat $ChangeLogTmp | wc -l`
		numlines=`expr $maxlines - $startline + 1`

		tail -n $numlines $ChangeLogTmp > $ChangeLog
		rm $ChangeLogTmp

		for tag in `svn ls --verbose $ASNV/tags | sed 's/^\W*\([0-9]\+\)\W\+.* \+\([^ ]\+\)\/$/\1 \2/g' | grep -v "^[0-9]\+ .$" | sort -n -r -k1 | sed 's/^\([^ ]\+\) \([^ ]\+\)$/\1\/\2/g'`; do
			rev=`echo "$tag" | cut -d "/" -f1`
			tag=`echo "$tag" | cut -d "/" -sf2`
			sed -i "s/^r$rev /\n************************************************************************\n Version $tag\n************************************************************************\n\nr$rev /" $ChangeLog
		done
	)
}


# ------------------------------------------------------------------------------

function __cleanFilesForRelease() {
	dir="$1"
	version="$2"

	cd "$dir"
	rm -f "app/Config/database.php.default" >> /dev/null 2>&1
	mv "app/Config/database.php" "app/Config/database.php.default" >> /dev/null 2>&1
	mv "app/Config/webrsa.inc" "app/Config/webrsa.inc.default" >> /dev/null 2>&1
	mv "app/Config/email.php" "app/Config/email.php.default" >> /dev/null 2>&1
	mv "app/Config/core.php" "app/Config/core.php.default" >> /dev/null 2>&1

	# Passage de tous les modèles .odt du répertoire app/Vendor/modelesodt en .odt7.default
	(
		if [ -e "app/Vendor/modelesodt" ] ; then
			cd "app/Vendor/modelesodt" && \
			find . -type f -iname "*.odt" | while read -r ; do mv "$REPLY" "$REPLY.default"; done
		fi
	)
	echo -n "$version" > "app/VERSION.txt"
	sed -i "s/Configure::write *( *'production' *, *[^)]\+ *) *;/Configure::write('production', true);/" "app/Config/core.php.default" >> /dev/null 2>&1
# 	sed -i "s/Configure::write *( *'debug' *, *[0-9] *) *;/Configure::write('debug', 0);/" "app/Config/core.php.default" >> /dev/null 2>&1
# 	sed -i "s/Configure::write *( *'Cache\.disable' *, *[^)]\+ *) *;/Configure::write('Cache.disable', false);/" "app/Config/core.php.default" >> /dev/null 2>&1
	sed -i "s/Configure::write *( *'CG\.cantons' *, *[^)]\+ *) *;/Configure::write('CG.cantons', false);/" "app/Config/webrsa.inc.default" >> /dev/null 2>&1
	sed -i "s/Configure::write *( *'Zonesegeographiques\.CodesInsee' *, *[^)]\+ *) *;/Configure::write('Zonesegeographiques.CodesInsee', true);/" "app/Config/webrsa.inc.default" >> /dev/null 2>&1

	# Suppression des fichiers "internes" au développement
	(
		cd "app" && \
		find . -type f -regex ".*\(TODO\|FIXME\).*" | while read -r ; do rm "$REPLY"; done
	)
}

# ------------------------------------------------------------------------------

# TODO: svn log app > log-20090716-10h52.txt
# http://svnbook.red-bean.com/en/1.5/svn.tour.history.html
# svn ls --verbose svn://svn.adullact.net/svnroot/webrsa/tags

function __package() {
	version=${1}
	mkdir -p "$RELEASES_DIR/webrsa-$version" >> "/dev/null" 2>&1 && \
	(
		cd "$RELEASES_DIR/webrsa-$version" >> "/dev/null" 2>&1 && \
		# TODO: RC pour trunk
		# svn export svn://svn.adullact.net/svnroot/webrsa/trunk >> "/dev/null" 2>&1 && \
#         svn export svn://svn.adullact.net/svnroot/webrsa/tags/$version/app >> "/dev/null" 2>&1 && \
		svn export $ASNV/tags/$version/app >> "/dev/null" 2>&1 && \

		__cleanFilesForRelease "$RELEASES_DIR/webrsa-$version" "$version"
	) && \
	(
		cd "$RELEASES_DIR" >> "/dev/null" 2>&1 && \
		__minify "$RELEASES_DIR/webrsa-$version/app" && \
		__changelog "$version" "$RELEASES_DIR/webrsa-$version/app" && \
		zip -o -r -m "$RELEASES_DIR/webrsa-$version.zip" "webrsa-$version" >> "/dev/null" 2>&1
	) && \
	echo $version
}

# ------------------------------------------------------------------------------

function __patch() {
	REFERENCE="$1"
	TRUNK="$2"

	REFERENCE=`echo $REFERENCE | sed 's/\/$//'`
	TRUNK=`echo $TRUNK | sed 's/\/$//'`

	versionReference=`basename $REFERENCE`
	versionTrunk=`basename $TRUNK`

	NAME="patch-webrsa-$versionReference-$versionTrunk"
	DESTINATION="$RELEASES_DIR/$NAME"

	mkdir -p "$DESTINATION" >> /dev/null


	REFERENCE_ESCAPED=`echo $REFERENCE | sed 's/\//\\\\\//g'`

	(
		IFS=$'\n'
		for serverfile in `svn diff "$REFERENCE" "$TRUNK" --summarize | grep -e '^M ' -e '^A ' -e '^AM ' | sed 's/^\(A\|M\|AM\) \+//'`; do
			file="`echo $serverfile | sed "s/"$REFERENCE_ESCAPED"\///"`"
			dir="`dirname $file`"
			mkdir -p "$DESTINATION/$dir" >> "/dev/null" 2>&1
			svn export "$TRUNK/$file" "$DESTINATION/$file" >> "/dev/null" 2>&1
		done
	)

	__cleanFilesForRelease "$DESTINATION" "$versionTrunk"
	__changelog "$versionTrunk" "$DESTINATION/app"
	cd "$DESTINATION/.."
	zip -o -r -m "$DESTINATION.zip" "$NAME" >> "/dev/null" 2>&1
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

function __svnbackup() {
	APP_DIR="`readlink -f "$1"`"

	xml="`svn info --xml app | sed ':a;N;$!ba;s/\n/ /g'`"
	revision="`echo $xml| sed 's/^.*<entry[^>]* revision=\"\([0-9]\+\)\".*$/\1/g'`"
	project="`echo $xml| sed 's/^.*<root>.*\/\([^\/]\+\)<\/root>.*$/\1/g'`"
	subfolder="`echo $xml| sed 's/^.*<url>.*\/\([^\/]\+\)\/app<\/url>.*$/\1/g'`"

	NOW=`date +"%Y%m%d-%H%M%S"` # FIXME: M sur 2 chars
	PATCH_DIR="$APP_DIR/../svnbackup-${project}_${subfolder}-r${revision}-${NOW}"
	PATCH_DIR="`readlink -f "$PATCH_DIR"`"

	mkdir -p "$PATCH_DIR"
	if [[ $? -ne 0 ]] ; then
		echo "Impossible de créer le répertoire ${PATCH_DIR}"
		exit 1
	fi

	(
		cd "$APP_DIR"
		local SAVEIFS=$IFS
		IFS=$(echo -en "\n\b")

		status="`svn status . | grep -v "\(^\(\!\|D\)\|>\)" | sed 's/^\(.\{8\}\)\(.*\)$/\2/'`";
		for file in `echo "$status"`; do
			dir="`dirname "$file" | sed "s@^\./@$PWD@"`"
			if [ "$dir" != '.' ] ; then
				mkdir -p "$PATCH_DIR/app/$dir"
			fi
			cp -R "$file" "$PATCH_DIR/app/$dir"
		done
		IFS=$SAVEIFS
	)

	(
		cd "$PATCH_DIR"
		SVNBACKUP_SUBDIR="`basename "$PATCH_DIR"`"

		zip -o -r -m "../$SVNBACKUP_SUBDIR.zip" app >> "/dev/null" 2>&1
		if [[ $? -ne 0 ]] ; then
			echo "Impossible de créer le fichier $SVNBACKUP_SUBDIR.zip"
		else
			echo "Fichier $SVNBACKUP_SUBDIR.zip créé"
			cd ..
			rmdir "$PATCH_DIR"
		fi
	)
}

# ------------------------------------------------------------------------------

case $1 in
	changelog)
		__svnDirExists "$ASNV/tags/$2"
		existsDir=$?
		if [[ $existsDir -ne 0 ]] ; then
			exit 1
		fi
		__changelog "$2" .
		exit 0
	;;
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
	package)
		# Vérification de l'argument
		__svnDirExists "$ASNV/tags/$2"
		if [[ $? -ne 0 ]] ; then
			exit 1
		fi

		__package $2
		exit 0
	;;
	patch)
		# Vérification des arguments
		__svnDirExists "$ASNV/$2"
		existsDir1=$?
		__svnDirExists "$ASNV/$3"
		existsDir2=$?
		if [[ $existsDir1 -ne 0 || $existsDir2 -ne 0 ]] ; then
			exit 1
		fi
		# ex: app/webrsa.sh patch tags/1.0.9 branches/1.0.8
		__patch "$ASNV/$2" "$ASNV/$3"
		exit 0
	;;
	svnbackup)
		__clear "$APP_DIR"
		__svnbackup "$APP_DIR"
		exit 0
	;;
	*)
		echo "Usage: $ME {changelog|clearcache|clear|clearlogs|minify|package|patch|svnbackup}"
		exit 1
	;;
esac

#  Afin d'enlever l'extension defualt des fichiers ODT sans avoir à le faire à la main
#         (
#             cd "app/Vendor/modelesodt" && \
#             find . -type f -iname "*.odt.default" | while read -r ; do mv "$REPLY" `echo "$REPLY" |sed 's/\.default$//g'` ; done
#         )