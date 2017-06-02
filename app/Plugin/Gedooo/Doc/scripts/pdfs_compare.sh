#!/bin/bash

# ------------------------------------------------------------------------------
# Fonctions utilitaires
# ------------------------------------------------------------------------------

verify_pdf_result="0"

function convert_pdf() {
	local src="${1}"
	local dir="${2}"
	local dst="`basename ${src}`"

	convert -density 150 "${src}" -quality 90 "${dir}/%05d.png"
	echo "${?}"
}

function compare_image() {
	local src="${1}"
	local dst="${2}"
	local diff="${3}"
	local diff_file="`basename ${src}`"
	local result=$(compare -metric PSNR -compose src "${src}" "${dst}" "${diff}/${diff_file}" 2>&1)

	if [ "${result}" = "inf" ] ; then
		echo "0"
	else
		echo "1"
	fi
}

function verify_pdf() {
	local srcdir="${1}"
	local dstdir="${2}"
	local diffdir="${3}"
	local num_page=0
	local tmp=""

	# Pages manquantes ou différentes ?
	for srcpage in ${srcdir}/*.png ; do
		page="`basename ${srcpage}`"
		dstpage="${dstdir}/${page}"
		num_page="`expr ${page%.*} + 1`"

		echo "Vérification de la page ${num_page} (${srcpage} et ${dstpage})"

		# Page différente ?
		if [ -e "${dstpage}" ] ; then
			tmp=`compare_image "${srcpage}" "${dstpage}" "${diffdir}"`
			if [ "${tmp}" != "0" ] ; then
				verify_pdf_result=`expr ${verify_pdf_result} + 1`
				echo "  Différences détectées dans les pages ${num_page}"
			else
				echo "  Pages ${num_page} identiques"
			fi
		# Page manquante ?
		else
			verify_pdf_result=`expr ${verify_pdf_result} + 1`
			echo "${dstpage} manquante"
		fi
	done

	# Pages en trop ?
	for dstpage in ${dstdir}/*.png ; do
		page="`basename ${dstpage}`"
		srcpage="${srcdir}/${page}"

		if [ ! -e "${srcpage}" ] ; then
			verify_pdf_result=`expr ${verify_pdf_result} + 1`
			echo "${dstpage} en trop"
		fi
	done
}

# ------------------------------------------------------------------------------
# Préparation: traitement des paramètres
# ------------------------------------------------------------------------------
dir="`basename ${0%.*}`"
dir="/tmp/${dir}"

# Vérification du nombre de paramètres envoyés
if [ $# -lt 2 ] || [ $# -gt 3 ] ; then
	echo "Ce script permet de comparer le rendu de deux fichiers PDF et accepte 2 ou 3 paramètres:"
	echo "  - le chemin du PDF \"canonique\""
	echo "  - le chemin du PDF à comparer"
	echo "  - le répertoire de travail (facultatif, par défaut ${dir})"
	exit 1
fi

file1="${1}"
file2="${2}"

if [ $# -eq 3 ] ; then
	dir="${3}"
fi

echo ""
echo "Comparaison des fichiers"
echo "  - ${file1}"
echo "  - ${file2}"
echo "  - répertoire de travail ${dir}"
echo ""

# Vérification de la présence des outils ImageMagick
which convert > /dev/null || ( echo "Le binaire convert du paquet imagemagick n'est pas disponible" && exit 3 )
which compare > /dev/null || ( echo "Le binaire compare du paquet imagemagick n'est pas disponible" && exit 3 )

# Vérification des paramètres envoyés
if [ ! -f "${file1}" ] || [ ! -r "${file1}" ] ; then
	echo "Le fichier \"${file1}\" n'existe pas ou n'est pas lisible"
	exit 2
fi

if [ ! -f "${file2}" ] || [ ! -r "${file2}" ] ; then
	echo "Le fichier \"${file2}\" n'existe pas ou n'est pas lisible"
	exit 2
fi

mkdir -p "${dir}"

if [ ! -d "${dir}" ] || [ ! -w "${dir}" ] ; then
	echo "Le dossier \"${dir}\" n'existe pas ou n'est pas inscriptible"
	exit 2
fi

# ------------------------------------------------------------------------------
# Préparation: nettoyage et création des réperoires
# ------------------------------------------------------------------------------
rm -r "${dir}/src" >> "/dev/null" 2>&1
rm -r "${dir}/dst" >> "/dev/null" 2>&1
rm -r "${dir}/diff" >> "/dev/null" 2>&1

mkdir -p "${dir}/src"
mkdir -p "${dir}/dst"
mkdir -p "${dir}/diff"

# ------------------------------------------------------------------------------
# Conversion des pages du PDF en images
# ------------------------------------------------------------------------------
# Fichier 1
tmp="`convert_pdf "${file1}" "${dir}/src"`"
if [ "${tmp}" != "0" ] ; then
	echo "Erreur lors de la transformation en image du fichier \"${file1}\""
	exit 4
fi

# Fichier 2
tmp="`convert_pdf "${file2}" "${dir}/dst"`"
if [ "${tmp}" != "0" ] ; then
	echo "Erreur lors de la transformation en image du fichier \"${file2}\""
	exit 4
fi

# ------------------------------------------------------------------------------
# Comparaison des images
# ------------------------------------------------------------------------------
verify_pdf "${dir}/src" "${dir}/dst" "${dir}/diff"

echo ""
if [ "${verify_pdf_result}" = "0" ] ; then
	echo "Succès: les fichiers sont identiques"
	exit 0
else
	echo "Erreurs: ${verify_pdf_result} pages présentent des différences"
	exit 5
fi