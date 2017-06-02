#!/bin/bash

TMP="/tmp"

# ==============================================================================

function sedodt() {
	modeleodt="$1"
	shift
	sed_regexes="$*"

	tmp_dirname="`echo "$modeleodt" | sed "s/\//_/g"`"
	tmp_filename="$tmp_dirname.zip"
	tmp_dirname="$TMP/$tmp_dirname"

	mkdir -p "$tmp_dirname"
	unzip "$modeleodt" -d "$tmp_dirname" >> "/dev/null" 2>&1

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

	rmdir "$tmp_dirname"
}

# ==============================================================================

sed_regexes=( \
	"s/Structurereferente_ville/structurereferente_ville/g" \
	"s/Personne_/personne_/g" \
	"s/aidesapre66_piececomptable66/aideapre66_piecescomptables66/g" \
	"s/Aideapre66_Piececomptable66/aideapre66_piecescomptables66/g" \
	"s/aideapre66_pieceaide66/aideapre66_piecesaides66/g" \
	"s/Aideapre66_Pieceaide66/aideapre66_piecesaides66/g" \
	"s/Aideapre66_montantaccorde/aideapre66_montantaccorde/g" \
)

# sedodt "app/vendors/modelesodt/APRE/accordaide.odt" "${sed_regexes[@]}"

# ------------------------------------------------------------------------------

sed_regexes=( \
	"s/Structurereferente_ville/structurereferente_ville/g" \
	"s/Personne_/personne_/g" \
	"s/Aideapre66_motifrejetequipe/aideapre66_motifrejetequipe/g" \
)

# sedodt "app/vendors/modelesodt/APRE/refusaide.odt" "${sed_regexes[@]}"

# ------------------------------------------------------------------------------

sed_regexes=( \
	"s/aidesapre66_piececomptable66/aideapre66_piecescomptables66/g" \
	"s/Aideapre66_Piececomptable66/aideapre66_piecescomptables66/g" \
	"s/aideapre66_pieceaide66/aideapre66_piecesaides66/g" \
	"s/Aideapre66_Pieceaide66/aideapre66_piecesaides66/g" \
	"s/aideapre66_fraisdeplacement66_/fraisdeplacement66_/g" \
	"s/aideapre66_themeapre66_/themeapre66_/g" \
	"s/aideapre66_typeaideapre66_/typeaideapre66_/g" \
)

# sedodt "app/vendors/modelesodt/APRE/apre66.odt" "${sed_regexes[@]}"

# ------------------------------------------------------------------------------

sed_regexes=( \
	"s/structurereferente/structureactuelle/g" \
)

sedodt "app/Vendor/modelesodt/PDO/pdo_etudiant.odt" "${sed_regexes[@]}"
sedodt "app/Vendor/modelesodt/PDO/pdo_insertion.odt" "${sed_regexes[@]}"

# ------------------------------------------------------------------------------