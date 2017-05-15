#!/bin/bash

ME="$0"
APP="`dirname "$ME"`/.."
TMP="/tmp"

now=`date +"%Y%m%d-%H%M%S"`
ERROR_LOG="${TMP}/maj_odt_nouvelles_adresses_caf-error-${now}.log"
echo -n "" > "$ERROR_LOG"

# ==============================================================================

# Fonction permettant de remplacer le contenu d'un fichier .odt par des regexeps
# sed.
#
# @param string Le chemin vers le modèle .odt
# @params array Les expressions régulières permettant de remplacer avec sed
#
function sed_odt() {
	modeleodt="$1"
	shift
	sed_regexes="$*"

	tmp_dirname="`echo "$modeleodt" | sed "s/\//_/g"`"
	tmp_filename="$tmp_dirname.zip"
	tmp_dirname="$TMP/$tmp_dirname"

	mkdir -p "$tmp_dirname"
	unzip "$modeleodt" -d "$tmp_dirname" >> "/dev/null" 2>&1

	if [ -f "$tmp_dirname/content.xml" ] ; then
		(
			cd "$tmp_dirname"

			for sed_regex in $sed_regexes ; do
				sed -i "$sed_regex" content.xml
			done

			zip -o -r -m "../$tmp_filename" . >> "/dev/null" 2>&1
		)

		now=`date +"%Y%m%d-%H%M%S"`
		modeleodtbak="$modeleodt.bak.$now"
		mv "$modeleodt" "$modeleodtbak"

		mv "$TMP/$tmp_filename" "$modeleodt"
	else
		echo "	Erreur (${modeleodt})"
		echo "${modeleodt}" >> "$ERROR_LOG"
	fi

	rmdir "$tmp_dirname"
}

# ------------------------------------------------------------------------------

# Fonction permettant de remplacer les fichiers .odt se trouvant dans un répertoire
# ou ses sous-répertoires par des regexeps
# sed.
#
# @param string Le chemin vers le répertoire
# @params array Les expressions régulières permettant de remplacer avec sed
#
function sed_all_odt() {
	modeleodt_dir="$1"
	shift
	sed_regexes="$*"

	find "$modeleodt_dir" -type f -iname "*.odt" | while read -r ;
	do
		echo "Traitement du fichier $REPLY"
		sed_odt "$REPLY" "${sed_regexes[@]}"
	done
}

# ==============================================================================

sed_regexes=( \
	# Modèle Parcours
	"s/parcours_typevoie/parcours_libtypevoie/g" \
	"s/parcours_locaadr/parcours_nomcom/g" \
	# Modèle Orientation
	"s/orientation_typevoie/orientation_libtypevoie/g" \
	"s/orientation_locaadr/orientation_nomcom/g" \
	# Modèle Adresse
	"s/adresse_typevoie/adresse_libtypevoie/g" \
	"s/adresse_numcomrat/adresse_numcom/g" \
	"s/adresse_numcomptt/adresse_numcom/g" \
	"s/adresse_locaadr/adresse_nomcom/g" \
	# Modèle Canton
	"s/canton_typevoie/canton_libtypevoie/g" \
	"s/canton_numcomptt/canton_numcom/g" \
	"s/canton_locaadr/canton_nomcom/g" \
	# Modèle Situationallocataire
	"s/situationallocataire_typevoie/situationallocataire_libtypevoie/g" \
	"s/situationallocataire_numcomrat/situationallocataire_numcom/g" \
	"s/situationallocataire_numcomptt/situationallocataire_numcom/g" \
	"s/situationallocataire_locaadr/situationallocataire_nomcom/g" \
	# Modèle Cer93
	"s/cer93_locaadr/cer93_nomcom/g" \
	# Modèle Instantanedonneesfp93
	"s/instantanedonneesfp93_benef_typevoie/instantanedonneesfp93_benef_libtypevoie/g" \
	"s/instantanedonneesfp93_benef_numcomrat/instantanedonneesfp93_benef_numcom/g" \
	"s/instantanedonneesfp93_benef_numcomptt/instantanedonneesfp93_benef_numcom/g" \
	"s/instantanedonneesfp93_benef_locaadr/instantanedonneesfp93_benef_nomcom/g" \
)

# ==============================================================================

 sed_all_odt "$APP/Vendor/modelesodt" "${sed_regexes[@]}"

# ==============================================================================

echo "================================================================================"

NB_ERRORS=`wc -l "$ERROR_LOG" | sed "s/ .*$//g"`

if [ $NB_ERRORS -gt 0 ] ; then
	echo "$NB_ERRORS erreur(s) lors du traitement:"
	cat "$ERROR_LOG"
else
	echo "Traitement effectué sans erreur."
fi

rm  "$ERROR_LOG"