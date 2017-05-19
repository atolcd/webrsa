#!/bin/bash
# Flux:
#	* instruction: OK
#	* bénéficiaires: OK

path="/Racine/InfosFoyerRSA/IdentificationRSA/DemandeRSA/NUMDEMRSA"

function __rcv_export_dossier() {
	local dir="${1}"
	local path="${2}"
	local value="${3}"
	local tag=""
	local basename=""
	local encoding=""
	local racine=""
	local export=""

	for file in $( find "${dir}" -type f ); do
		echo "Analyse du fichier ${file}"
		tag="`basename ${path}`"
		grep -rF "<${tag}>${value}</${tag}>" "${file}" > /dev/null

		if [ $? -eq 0 ] ; then
			file_copy="${file}.tmp"
			basename="`basename "${file}"`"
			cp "${file}" "${file_copy}"
			export="${tag}_${value}_${basename}"
			encoding="`grep "<?xml " "${file_copy}"`"
			racine="`grep "<Racine" "${file_copy}"`"

			sed -i "s/^<Racine.*$/<Racine>/" "${file_copy}"
			# @todo: nombre d'occurences ?
			xmllint --xpath "${path}[text()='${value}']/ancestor::InfosFoyerRSA" "${file_copy}" > "${export}"
			ETATDOSRSA="`grep "ETATDOSRSA" "${export}" | sed "s/\s//"`"
			echo -e "\t${path}: ${value}\t${export}\t$ETATDOSRSA"

			sed -i "1s;^;${encoding}\n${racine}\n;" "${export}"
			echo "\n</Racine>" >> "${export}"
			rm "${file_copy}"
		fi
	done
}

# Vérification des paramètres
if [ $# -lt 2 ] || [ $# -gt 3 ] ; then
	echo "Script permettant d'extraire des fichiers XML contenant un dossier RSA particulier à partir d'un répertoire contenant des fichier XML de flux RSA."
	echo ""
	echo "Ce script prend 2 ou 3 paramètres:"
	echo "  - le premier est le répertoire contenant les flux XML dans lequel chercher"
	echo "  - le second est la valeur à rechercher (le numéro de dossier RSA à exporter, voir paramètre suivant)"
	echo "  - le troisième, facultatif, est la chemin xpath de la balise contenant la valeur à rechercher, par défaut \"/Racine/InfosFoyerRSA/IdentificationRSA/DemandeRSA/NUMDEMRSA\""
	echo ""
	echo "Exemples:"
	echo '  app/Doc/scripts/rcv_export_dossier.sh "/tmp/flux" "01234657893"'
	echo '  app/Doc/scripts/rcv_export_dossier.sh "/tmp/flux" "01234657893" "/Racine/InfosFoyerRSA/IdentificationRSA/DemandeRSA/NUMDEMRSA"'
	echo '  app/Doc/scripts/rcv_export_dossier.sh "/tmp/flux" "2017-03-20" "/Racine/InfosFoyerRSA/Personne/DossierCAF/DDRATDOS"'
	echo '  app/Doc/scripts/rcv_export_dossier.sh "/tmp/flux" "1790199131479" "/Racine/InfosFoyerRSA/Personne/Identification/NIR"'
	echo ""
	exit 1
fi

if [ $# -eq 3 ] ; then
	path="$3"
fi

XPATH_PRESENT="`which xmllint > /dev/null ; echo $?`"

if [ "$XPATH_PRESENT" != "0" ] ; then
	echo "Ce script nécessite le programme xmllint. Pour l'installer sur Ubuntu: sudo aptitude install libxml2-utils"
	exit 1
fi

if [ ! -d "${1}" ] ; then
	echo "Le répertoire ${1} n'existe pas."
	exit 1
fi

__rcv_export_dossier "${1}" "${path}" "${2}"
