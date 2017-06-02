#!/bin/bash

#-------------------------------------------------------------------------------

# sed -n '/<VirtualHost*/,/<\/VirtualHost>/p' /etc/httpd/conf/httpd.conf
#  sed -n '/<InfosFoyerRSA>/,/xxx/s/abc//g'
# sed -n '/<InfosFoyerRSA>/{N;00046238093!d};/<InfosFoyerRSA>/,/)\<\/InfosFoyerRSA>' NRSACGIM_RSABEM_20100101_A0407584.RCV
# sed -n '8,12p'

# FICHIER_BEM="NRSACGIM_RSABEM_20100101_A0407584.RCV"
# __exportDossierByNumdemrsa "$FICHIER_BEM" "00046238093"
# __exportDossierByNumdemrsa "$FICHIER_BEM" "00046770093"
# __exportDossierByNumdemrsa "$FICHIER_BEM" "00062767093"
# __exportDossierByNumdemrsa "$FICHIER_BEM" "00067764093"
# __exportDossierByNumdemrsa "$FICHIER_BEM" "00036517075"

#-------------------------------------------------------------------------------
# Exporte les un fichier contenant les informations entre les balises
# InfosFoyerRSA à partir du fichier passé en premier paramètre et où la la valeur
# se trouvant dans la balise NUMDEMRSA correspond au n° de dossier RSA passé en
# second paramètre
#-------------------------------------------------------------------------------

function __exportDossierByNumdemrsa() {
	FICHIER_BEM="$1"
	NUMDEMRSA="$2"

# 	lineNumdemrsa=`grep -n "<NUMDEMRSA>$NUMDEMRSA</NUMDEMRSA>" "$FICHIER_BEM" | cut -f1 -d:`
# 	echo $lineNumdemrsa
# 	lineInfosFoyerRSA=`head -$lineNumdemrsa "$FICHIER_BEM" | grep -n "<InfosFoyerRSA>" | cut -f1 -d:`
# 	echo $lineInfosFoyerRSA

	DossierRsa=`grep -B 2 -A 1000 "<NUMDEMRSA>$NUMDEMRSA</NUMDEMRSA>" "$FICHIER_BEM"`

	if [ $? -eq 0 ] ; then
		length=`echo "$DossierRsa" | grep -m 1 -n "</InfosFoyerRSA>" | cut -f1 -d:`
		DossierRsa=`echo "$DossierRsa" | head -$length`

		echo "<InfosFoyerRSA><IdentificationRSA>$DossierRsa" > "$NUMDEMRSA.xml"
		echo "Dossier RSA $NUMDEMRSA trouvé dans le fichier $FICHIER_BEM et exporté dans le fichier $NUMDEMRSA.xml."
		return 0
	else
		echo "Dossier RSA $NUMDEMRSA non trouvé dans le fichier $FICHIER_BEM."
		return 1
	fi
}

#-------------------------------------------------------------------------------
# Main
#-------------------------------------------------------------------------------

if [ $# -ne 2 ] ; then
	echo "Script permettant d'extraire un fichier XML contenant un dossier RSA particulier à partir d'un fichier XML de flux mensuel."
	echo "Ce script a besoin de 2 paramètres. Le premier est le fichier de flux XML dans lequel chercher, le second est le numéro de dossier RSA à exporter."
	exit 1
fi

if [ ! -e "$1" ] ; then
	echo "Le fichier $1 n'existe pas."
	exit 1
fi

__exportDossierByNumdemrsa "$1" "$2"
exit 0
