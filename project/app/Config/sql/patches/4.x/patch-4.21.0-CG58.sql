SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************


-- Update de la variable de configuration pour l'affichage des referents sectorisation
UPDATE public.configurations SET value_variable = 'true' WHERE lib_variable LIKE 'PlanPauvrete.Nouveauxentrants.PPAE';

-- Update des variables de configuration pour ne pas afficher le rdv 3 en 1 dans le menu du plan pauvreté
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_second_rdv_nouveaux';
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_imprime_second_rdv_nouveaux';
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_venu_nonvenu_second_rdv_nouveaux';
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_second_rdv_stock';
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_imprime_second_rdv_stock';
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_venu_nonvenu_second_rdv_stock';

-- Update de la variable de configuration permettant de n'activer la modification que pour la dernière version des dsp
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Dsp.modification.all.enabled';


-- Update de la variable de configuration pour l'export CSV de la recherche par dsp
UPDATE public.configurations SET value_variable = '{
	"filters": {
		"defaults": {
			"Dossier": {
				"dernier": 0,
				"dtdemrsa": 0,
				"dtdemrsa_from": "TAB::-1WEEK",
				"dtdemrsa_to": "TAB::NOW"
			}
		},
		"accepted": [],
		"skip": []
	},
	"query": {
		"restrict": [],
		"conditions": [],
		"order": []
	},
	"results": {
		"fields": [
			"Dossier.numdemrsa",
			"Dossier.matricule",
			"Situationdossierrsa.etatdosrsa",
			"Personne.qual",
			"Personne.nom",
			"Personne.prenom",
			"Personne.nir",
			"Personne.dtnai",
			"Personne.age",
			"Personne.sexe",
			"Foyer.sitfam",
			"Typeorient.lib_type_orient",
			"Adresse.numvoie",
			"Adresse.libtypevoie",
			"Adresse.nomvoie",
			"Adresse.complideadr",
			"Adresse.compladr",
			"Adresse.codepos",
			"Adresse.nomcom",
			"Donnees.libsecactderact",
			"Donnees.libderact",
			"Donnees.libsecactdomi",
			"Donnees.libactdomi",
			"Donnees.libsecactrech",
			"Donnees.libemploirech",
			"Structurereferenteparcours.lib_struc",
			"Referentparcours.nom_complet"
		]
	},
	"ini_set": []
}' WHERE lib_variable LIKE 'ConfigurableQuery.Dsps.exportcsv';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************