#!/bin/bash

ME="$0"
APP_DIR="`dirname "$ME"`"
WORK_DIR="$PWD"

# ==============================================================================

function rename_in_files() {
	for regex in $*; do
		find app -regex "^\(.*\.\(php\|ctp\|po\|inc\|inc\.default\)\|.*/eps-.*\.sql\)$" \
			| grep -v "\.svn" \
			| xargs ssed -i -R "$regex"
	done
}

function rename_files() {
	oldpart="$1"
	newpart="$2"

	# Move directories
	for path in `find app -regex ".*$oldpart.*" -type d | grep -v "\.svn" | grep -v "$newpart"`; do
		newpath="`echo "$path" | ssed -R "s/(?<!\w)$oldpart(?<!\W)/$newpart/g"`"
		svn mv "$path" "$newpath"
	done

	# Move files
	for path in `find app -regex ".*/\(controllers\|models\|views\|locale\|tests\|vendors/shells\)/.*$oldpart.*" -type f | grep -v "\.svn" | grep -v "$newpart"`; do
		newpath="`echo "$path" | ssed -R "s/(?<!\w)$oldpart(?<!\W)/$newpart/g"`"
		svn mv "$path" "$newpath"
	done
}

# ==============================================================================
# seanceseps, membreseps_seanceseps
# -> commissionseps, commissionseps_membreseps
#
# cake/console/cake rename_table membreseps_seanceseps membreep_seanceep commissionseps_membreseps commissionep_membreep
# cake/console/cake rename_table seanceseps seanceep commissionseps commissionep
# ==============================================================================

regexes=( \
	"s/(?<!\w)MembresepsSeanceseps(?<!W)/CommissionsepsMembreseps/g" \
	"s/(?<!\w)membreseps_seanceseps(?<!W)/commissionseps_membreseps/g" \
	"s/(?<!\w)MembreepSeanceep(?<!W)/CommissionepMembreep/g" \
	"s/(?<!\w)membreep_seanceep(?<!W)/commissionep_membreep/g" \
	"s/(?<!\w)MEMBREEP_SEANCEEP(?<!W)/COMMISSIONEP_MEMBREEP/g" \
	
	"s/(?<!\w)Seanceseps(?<!W)/Commissionseps/g" \
	"s/(?<!\w)seanceseps(?<!W)/commissionseps/g" \
	"s/(?<!\w)Seanceep(?<!W)/Commissionep/g" \
	"s/(?<!\w)seanceep(?<!W)/commissionep/g" \
	"s/(?<!\w)SEANCEEP(?<!W)/COMMISSIONEP/g" \
)
rename_in_files "${regexes[@]}"

rename_files "membreseps_seanceseps" "commissionseps_membreseps"
rename_files "membreep_seanceep" "commissionep_membreep"
rename_files "seanceseps" "commissionseps"
rename_files "seanceep" "commissionep"

# ==============================================================================
# motifsreorients
# -> motifsreorientseps93
#
# cake/console/cake rename_table motifsreorients motifreorient motifsreorientseps93 motifreorientep93
# ==============================================================================

regexes=( \
	"s/(?<!\w)Motifsreorients(?<!W)/Motifsreorientseps93/g" \
	"s/(?<!\w)motifsreorients(?<!W)/motifsreorientseps93/g" \
	"s/(?<!\w)Motifreorient(?<!W)/Motifreorientep93/g" \
	"s/(?<!\w)motifreorient(?<!W)/motifreorientep93/g" \
	"s/(?<!\w)MOTIFREORIENT(?<!W)/MOTIFREORIENTEP93/g" \
)
rename_in_files "${regexes[@]}"

rename_files "motifsreorients" "motifsreorientseps93"
rename_files "motifreorient" "motifreorientep93"

# ==============================================================================
# saisinesepsreorientsrs93, nvsrsepsreorientsrs93
# -> reorientationseps93, decisionsreorientationseps93
#
# cake/console/cake rename_table nvsrsepsreorientsrs93 nvsrepreorientsr93 decisionsreorientationseps93 decisionreorientationep93
# cake/console/cake rename_table saisinesepsreorientsrs93 saisineepreorientsr93 reorientationseps93 reorientationep93
# ==============================================================================

regexes=( \
	"s/(?<!\w)Nvsrsepsreorientsrs93(?<!W)/Decisionsreorientationseps93/g" \
	"s/(?<!\w)nvsrsepsreorientsrs93(?<!W)/decisionsreorientationseps93/g" \
	"s/(?<!\w)Nvsrepreorientsr93(?<!W)/Decisionreorientationep93/g" \
	"s/(?<!\w)nvsrepreorientsr93(?<!W)/decisionreorientationep93/g" \
	"s/(?<!\w)NVSREPREORIENTSR93(?<!W)/DECISIONREORIENTATIONEP93/g" \
	
	"s/(?<!\w)Saisinesepsreorientsrs93(?<!W)/Reorientationseps93/g" \
	"s/(?<!\w)saisinesepsreorientsrs93(?<!W)/reorientationseps93/g" \
	"s/(?<!\w)Saisineepreorientsr93(?<!W)/Reorientationep93/g" \
	"s/(?<!\w)saisineepreorientsr93(?<!W)/reorientationep93/g" \
	"s/(?<!\w)SAISINEEPREORIENTSR93(?<!W)/REORIENTATIONEP93/g" \
)
rename_in_files "${regexes[@]}"

rename_files "nvsrsepsreorientsrs93" "decisionsreorientationseps93"
rename_files "nvsrepreorientsr93" "decisionreorientationep93"
rename_files "saisinesepsreorientsrs93" "reorientationseps93"
rename_files "saisineepreorientsr93" "reorientationep93"

# ==============================================================================
# saisinesepdspdos66, nvsepdspdos66
# -> saisinespdoseps66, decisionssaisinespdoseps66
#
# cake/console/cake rename_table nvsepdspdos66 nvsepdpdo66 decisionssaisinespdoseps66 decisionsaisinepdoep66
# cake/console/cake rename_table saisinesepdspdos66 saisineepdpdo66 saisinespdoseps66 saisinepdoep66
# ==============================================================================

regexes=( \
	"s/(?<!\w)Nvsepdspdos66(?<!W)/Decisionssaisinespdoseps66/g" \
	"s/(?<!\w)nvsepdspdos66(?<!W)/decisionssaisinespdoseps66/g" \
	"s/(?<!\w)Nvsepdpdo66(?<!W)/Decisionsaisinepdoep66/g" \
	"s/(?<!\w)nvsepdpdo66(?<!W)/decisionsaisinepdoep66/g" \
	"s/(?<!\w)NVSEPDPDO66(?<!W)/DECISIONSAISINEPDOEP66/g" \
	
	"s/(?<!\w)Saisinesepdspdos66(?<!W)/Saisinespdoseps66/g" \
	"s/(?<!\w)saisinesepdspdos66(?<!W)/saisinespdoseps66/g" \
	"s/(?<!\w)Saisineepdpdo66(?<!W)/Saisinepdoep66/g" \
	"s/(?<!\w)saisineepdpdo66(?<!W)/saisinepdoep66/g" \
	"s/(?<!\w)SAISINEEPDPDO66(?<!W)/SAISINEPDOEP66/g" \
)
rename_in_files "${regexes[@]}"

rename_files "nvsepdspdos66" "decisionssaisinespdoseps66"
rename_files "nvsepdpdo66" "decisionsaisinepdoep66"
rename_files "saisinesepdspdos66" "saisinespdoseps66"
rename_files "saisineepdpdo66" "saisinepdoep66"

# ==============================================================================
# saisinesepsbilansparcours66, nvsrsepsreorient66
# -> saisinesbilansparcourseps66, decisionssaisinesbilansparcourseps66
#
# cake/console/cake rename_table nvsrsepsreorient66 nvsrepreorient66 decisionssaisinesbilansparcourseps66 decisionsaisinebilanparcoursep66
# cake/console/cake rename_table saisinesepsbilansparcours66 saisineepbilanparcours66 saisinesbilansparcourseps66 saisinebilanparcoursep66
# ==============================================================================

regexes=( \
	"s/(?<!\w)Nvsrsepsreorient66(?<!W)/Decisionssaisinesbilansparcourseps66/g" \
	"s/(?<!\w)nvsrsepsreorient66(?<!W)/decisionssaisinesbilansparcourseps66/g" \
	"s/(?<!\w)Nvsrepreorient66(?<!W)/Decisionsaisinebilanparcoursep66/g" \
	"s/(?<!\w)nvsrepreorient66(?<!W)/decisionsaisinebilanparcoursep66/g" \
	"s/(?<!\w)NVSREPREORIENT66(?<!W)/DECISIONSAISINEBILANPARCOURSEP66/g" \
	
	"s/(?<!\w)Saisinesepsbilansparcours66(?<!W)/Saisinesbilansparcourseps66/g" \
	"s/(?<!\w)saisinesepsbilansparcours66(?<!W)/saisinesbilansparcourseps66/g" \
	"s/(?<!\w)Saisineepbilanparcours66(?<!W)/Saisinebilanparcoursep66/g" \
	"s/(?<!\w)saisineepbilanparcours66(?<!W)/saisinebilanparcoursep66/g" \
	"s/(?<!\w)SAISINEEPBILANPARCOURS66(?<!W)/SAISINEBILANPARCOURSEP66/g" \
)
rename_in_files "${regexes[@]}"

rename_files "nvsrsepsreorient66" "decisionssaisinesbilansparcourseps66"
rename_files "nvsrepreorient66" "decisionsaisinebilanparcoursep66"
rename_files "saisinesepsbilansparcours66" "saisinesbilansparcourseps66"
rename_files "saisineepbilanparcours66" "saisinebilanparcoursep66"

# ==============================================================================
# nonorientationspros
# -> nonorientationsproseps
#
# cake/console/cake rename_table decisionsnonorientationspros decisionnonorientationpro decisionsnonorientationsproseps decisionnonorientationproep
# cake/console/cake rename_table nonorientationspros nonorientationpro nonorientationsproseps nonorientationproep
# ==============================================================================

regexes=( \
	"s/(?<!\w)Decisionsnonorientationspros(?<!W)/Decisionsnonorientationsproseps/g" \
	"s/(?<!\w)decisionsnonorientationspros(?<!W)/decisionsnonorientationsproseps/g" \
	"s/(?<!\w)Decisionnonorientationpro(?<!W)/Decisionnonorientationproep/g" \
	"s/(?<!\w)decisionnonorientationpro(?<!W)/decisionnonorientationproep/g" \
	"s/(?<!\w)DECISIONNONORIENTATIONPRO(?<!W)/DECISIONNONORIENTATIONPROEP/g" \
	
	"s/(?<!\w)Nonorientationspros(?<!W)/Nonorientationsproseps/g" \
	"s/(?<!\w)nonorientationspros(?<!W)/nonorientationsproseps/g" \
	"s/(?<!\w)Nonorientationpro(?<!W)/Nonorientationproep/g" \
	"s/(?<!\w)nonorientationpro(?<!W)/nonorientationproep/g" \
	"s/(?<!\w)NONORIENTATIONPRO(?<!W)/NONORIENTATIONPROEP/g" \
)
rename_in_files "${regexes[@]}"

rename_files "decisionsnonorientationspros" "decisionsnonorientationsproseps"
rename_files "decisionnonorientationpro" "decisionnonorientationproep"
rename_files "nonorientationpro" "nonorientationproep"
rename_files "nonorientationspros" "nonorientationsproseps"

# ==============================================================================
# nonorientationspros
# -> nonorientationsproseps
#
# cake/console/cake rename_table commissionseps_dossierseps commissionep_dossierep passagescommissionseps passagecommissionep
# ==============================================================================

regexes=( \
	"s/(?<!\w)CommissionsepsDossierseps(?<!W)/Passagescommissionseps/g" \
	"s/(?<!\w)commissionseps_dossierseps(?<!W)/passagescommissionseps/g" \
	"s/(?<!\w)CommissionepDossierep(?<!W)/Passagecommissionep/g" \
	"s/(?<!\w)commissionep_dossierep(?<!W)/passagecommissionep/g" \
	"s/(?<!\w)COMMISSIONEP_DOSSIEREP(?<!W)/PASSAGECOMMISSIONEP/g" \
)
rename_in_files "${regexes[@]}"

rename_files "commissionseps_dossierseps" "passagescommissionseps"
rename_files "commissionep_dossierep" "passagecommissionep"