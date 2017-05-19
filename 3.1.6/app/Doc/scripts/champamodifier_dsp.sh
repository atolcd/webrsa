#!/bin/bash

# TODO: les modèles de documents ODT

#===============================================================================

function grepLines() {
    for champ in $*; do
        echo "---------------------------------------------------------------------"
        echo "$champ"
        echo "---------------------------------------------------------------------"
        grep -nRi --exclude="*\.svn*" --exclude="*\.sql"  "[^A-Z]$champ[^A-Z]" app/ \
			| grep -v "tests\/" \
			| grep -v "\/config\/sql\/" \
			| grep -v "champamodifier\.sh" \
			| grep -v "champsamodifier.*\.txt"
    done
}

#===============================================================================

# champsModifies=( \
#     # Champs Suiviinstruction
# #     'ETATIRSA' \
#     # Champs Dspp
#     'DRORSARMIANT' \
#     'COUVSOC' \
#     'LIBAUTRDIFSOC' \
#     'MOYLOCO' \
# )
#
# echo "==============================================================================="
# echo "Champs modifiés"
# echo "==============================================================================="
# grepLines "${champsModifies[@]}"

#-------------------------------------------------------------------------------


# champsSupprimes=( \
#     # Table + Champs Dspp
#     'Dspp' \
#     'dspps' \
#     'ELOPERSDIFDISP' \
#     'OBSTEMPLOIDIFDISP' \
#     'LIBAUTRACCOSOCINDI' \
#     'RAPPEMPLOIQUALI' \
#     'RAPPEMPLOIFORM' \
#     'PERMICONDUB' \
#     'LIBAUTRPERMICONDU' \
#     # Table Nivetu
#     'NIVETU' \
#     # Table Accoemploi
#     'ACCOEMPLOI' \
#     # Table + Champs Dspf
#     'Dspf' \
#     'dspfs' \
#     'MOTIDEMRSA' \
#     'ACCOSOCFAM' \
#     'LIBAUTRACCOSOCFAM' \
#     'LIBCOORACCOSOCFAM' \
#     'NATLOG' \
#     'LIBAUTRDIFLOG' \
#     'DEMARLOG' \
#     # Table Nataccosocfam
#     'NATACCOSOCFAM' \
#     # Table Diflog
#     'DIFLOG' \
# )
#
# echo "==============================================================================="
# echo "Champs supprimés"
# echo "==============================================================================="
# grepLines "${champsSupprimes[@]}"

# tablesSupprimees=( \
# 	'dspfs' \
# 	'Dspf' \
# 	'dspfs_diflogs' \
# 	'DspfDiflog' \
# 	'dspfs_nataccosocfams' \
# 	'DspfNataccosocfam' \
# 	'dspps_accoemplois' \
# 	'DsppAccoemploi' \
# 	'dspps_difdisps' \
# 	'DsppDifdisp' \
# 	'dspps' \
# 	'Dspp' \
# 	'dspps_difsocs' \
# 	'DsppDifsoc' \
# 	'dspps_nataccosocindis' \
# 	'DsppNataccosocindi' \
# 	'dspps_natmobs' \
# 	'DsppNatmob' \
# 	'dspps_nivetus' \
# 	'DsppNivetu' \
# )

# echo "==============================================================================="
# echo "Tables supprimées"
# echo "==============================================================================="
# grepLines "${tablesSupprimees[@]}"

# foyers.mtestrsa -> nouveau
# calculsdroitsrsa.toppersdrodevorsa -> bougé
# suivisinstruction.suiirsa (à la place de suivisinstruction.etatirsa)


tablesSupprimees=( \
# 	'Suiviinstruction' \
# 	'suivisinstruction' \
# 	'etatirsa' \
# 	'Prestation' \
# 	'prestations' \
	'toppersdrodevorsa' \
# 	'Ressource' \
# 	'ressources' \
	'mtpersressmenrsa' \
# 	'Ressourcesmensuelle' \
# 	'ressourcesmensuelles' \
	'mtabaneu' \
)

echo "==============================================================================="
echo "Tables supprimées"
echo "==============================================================================="
grepLines "${tablesSupprimees[@]}"

#===============================================================================

# dspfs.motidemrsa  -> dsps.sitpersdemrsa,
# dspps.drorsarmiant  -> dsps.topdrorsarmiant
# dspps.drorsarmianta2  -> dsps.drorsarmianta2
# dspps.couvsoc  -> dsps.topcouvsoc
# dspps.accosocfam  -> dsps.accosocfam
# dspfs.libcooraccosocfam  -> dsps.libcooraccosocfam,
# COUNT(dspps_nataccosocindis.*)  -> dsps.accosocindi
# dspps.libcooraccosocindi  -> dsps.libcooraccosocindi,
# dspps.soutdemarsoc  -> dsps.soutdemarsoc
# dspps.libautrqualipro  -> dsps.libautrqualipro,
# dspps.libcompeextrapro  -> dsps.libcompeextrapro,
# MIN(nivetus.code)  -> dsps.nivetu,
# EXTRACT(YEAR FROM dspps.annderdipobt)  -> dsps.annobtnivdipmax,
# dspps.persisogrorechemploi  -> dsps.topisogrorechemploi
# MIN(accoemplois.code)  -> dsps.accoemploi,
# dspps.libcooraccoemploi  -> dsps.libcooraccoemploi,
# dspps.hispro  -> dsps.hispro,
# dspps.libderact  -> dsps.libderact
# dspps.libsecactderact -> dsps.libsecactderact,
# AGE( dspps.dfderact ) -> dsps.cessderact,
# dspps.domideract -> dsps.topdomideract,
# dspps.libactdomi -> dsps.libactdomi,
# dspps.libsecactdomi -> dsps.libsecactdomi,
# dspps.duractdomi -> dsps.duractdomi,
# dspps.libemploirech -> dsps.libemploirech,
# dspps.libsecactrech -> dsps.libsecactrech,
# dspps.creareprisentrrech -> dsps.topcreareprientre,
# dspfs.natlog -> dsps.natlog,
# dspfs.demarlog -> dsps.demarlog
# dspps.moyloco -> dsps.topmoyloco
# dspps.permicondub -> dsps.toppermicondub,
# dspps.libautrpermicondu -> dsps.libautrpermicondu

# difsocs.code, dspps.libautrdifsoc, dspps_difsocs -> detailsdifsocs.difsoc, detailsdifsocs.libautrdifsoc

# nataccosocfams.code, dspfs.libautraccosocfam, dspfs_nataccosocfams -> detailsaccosocfams.nataccosocfam, detailsaccosocfams.libautraccosocfam

# nataccosocindis.code, dspps.libautraccosocindi, dspps_nataccosocindis -> detailsaccosocindis.nataccosocindi, detailsaccosocindis.libautraccosocindi

# difdisps.code, dspps_difdisps -> detailsdifdisps.difdisp

# natmobs.code, dspps_natmobs -> detailsnatmobs.natmob

# diflogs.code, dspfs.libautrdiflog, dspfs_diflogs -> detailsdiflogs.diflog, detailsdiflogs.libautrdiflog

#===============================================================================

# SELECT table_name as name FROM INFORMATION_SCHEMA.tables WHERE table_schema = 'public' AND ( table_name LIKE 'dspps%' OR table_name LIKE 'dspfs%' );

# dspfs
# dspfs_diflogs
# dspfs_nataccosocfams
# dspps_accoemplois
# dspps_difdisps
# dspps
# dspps_difsocs
# dspps_nataccosocindis
# dspps_natmobs
# dspps_nivetus

# dspfs.motidemrsa				-> dsps.sitpersdemrsa
# dspfs.accosocfam				-> -
# dspfs.libautraccosocfam		-> detailsaccosocfams.libautraccosocfam
# dspfs.libcooraccosocfam		-> dsps.libcooraccosocfam
# dspfs.natlog					-> dsps.natlog
# dspfs.libautrdiflog			-> detailsdiflogs.libautrdiflog
# dspfs.demarlog				-> dsps.demarlog

# dspps.drorsarmiant			-> dsps.topdrorsarmiant
# dspps.drorsarmianta2			-> dsps.drorsarmianta2
# dspps.couvsoc					-> dsps.topcouvsoc
# dspps.libautrdifsoc			-> detailsdifsocs.libautrdifsoc
# dspps.elopersdifdisp			-> -
# dspps.obstemploidifdisp		-> -
# dspps.soutdemarsoc			-> dsps.soutdemarsoc
# dspps.libautraccosocindi		-> detailsaccosocindis.libautraccosocindi
# dspps.libcooraccosocindi		-> dsps.libcooraccosocindi
# dspps.annderdipobt			-> dsps.annobtnivdipmax (EXTRACT YEAR)
# dspps.rappemploiquali			-> -
# dspps.rappemploiform			-> -
# dspps.libautrqualipro			-> dsps.libautrqualipro
# dspps.permicondub				-> dsps.toppermicondub
# dspps.libautrpermicondu		-> dsps.libautrpermicondu
# dspps.libcompeextrapro		-> dsps.libcompeextrapro
# dspps.persisogrorechemploi	-> dsps.topisogrorechemploi
# dspps.libcooraccoemploi		-> dsps.libcooraccoemploi
# dspps.hispro					-> dsps.hispro
# dspps.libderact				-> dsps.libderact
# dspps.libsecactderact			-> dsps.libsecactderact
# dspps.dfderact				-> dsps.cessderact (AGE)
# dspps.domideract				-> dsps.topdomideract
# dspps.libactdomi				-> dsps.libactdomi
# dspps.libsecactdomi			-> dsps.libsecactdomi
# dspps.duractdomi				-> dsps.duractdomi
# dspps.libemploirech			-> dsps.libemploirech
# dspps.libsecactrech			-> dsps.libsecactrech
# dspps.creareprisentrrech		-> dsps.topcreareprientre
# dspps.moyloco					-> dsps.topmoyloco
# dspps.diplomes				-> -
# dspps.dipfra					-> -