#!/bin/bash
# Flux:
#	* instruction: OK
#	* bénéficiaires: OK

function __rcv_export_dossier() {
	RCV_DIR="$1"
	NUMDEMRSA="$2"

	for file in $( ls "$RCV_DIR" ); do
		grep "<NUMDEMRSA>$NUMDEMRSA</NUMDEMRSA>" "$RCV_DIR/$file" > /dev/null
		if [ $? -eq 0 ] ; then
			xpath -q -e "//DemandeRSA[NUMDEMRSA=$NUMDEMRSA]/ancestor::InfosFoyerRSA" "$RCV_DIR/$file" > "${NUMDEMRSA}_$file"
			ETATDOSRSA="`grep "ETATDOSRSA" "${NUMDEMRSA}_$file" | sed "s/\s//"`"
			echo -e "$NUMDEMRSA\t$file\t$ETATDOSRSA"
		fi
	done
}

if [ $# -ne 2 ] ; then
	echo "Script permettant d'extraire des fichiers XML contenant un dossier RSA particulier à partir d'un répertoire contenant des fichier XML de flux RSA."
	echo "Ce script a besoin de 2 paramètres. Le premier est le répertoire contenant les flux XML dans lequel chercher, le second est le numéro de dossier RSA à exporter."
	exit 1
fi

XPATH_PRESENT="`which xpath > /dev/null ; echo $?`"

if [ "$XPATH_PRESENT" != "0" ] ; then
	echo "Ce script nécessite le programme xpath. Pour l'installer sur Ubuntu: sudo aptitude install libxml-xpath-perl"
	exit 1
fi

if [ ! -d "$1" ] ; then
	echo "Le répertoire $1 n'existe pas."
	exit 1
fi

__rcv_export_dossier "$1" "$2"
