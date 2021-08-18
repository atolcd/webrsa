SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- ---------- Dispositif Jeune ----------
-- Suppression des anciennes contraintes
ALTER TABLE public.orientsstructs DROP CONSTRAINT IF EXISTS orientsstructs_origine_check;
ALTER TABLE public.orientsstructs DROP CONSTRAINT IF EXISTS orientsstructs_origine_in_list_chk;
ALTER TABLE public.orientsstructs DROP CONSTRAINT IF EXISTS orientsstructs_statut_orient_in_list_chk;

-- Mise à jour des anciens codes vers les nouveaux
UPDATE orientsstructs SET origine = 'prestaorient' WHERE origine = 'prestadiagno';
UPDATE orientsstructs SET origine = 'entdiag' WHERE origine = 'prestadefaut';

-- Ajout des nouvelles contraintes à jour
ALTER TABLE public.orientsstructs ADD CONSTRAINT orientsstructs_origine_check CHECK (((origine IS NULL) AND (date_valid IS NULL)) OR ((origine IS NOT NULL) AND (date_valid IS NOT NULL) AND ((((rgorient >= 1) OR (rgorient IS NULL)) AND ((origine)::text = ANY (ARRAY[('manuelle'::character varying)::text, ('cohorte'::character varying)::text, ('prestaorient'::character varying)::text, ('entdiag'::character varying)::text, ('initinap'::character varying)::text]))) OR ((rgorient > 1) AND ((origine)::text = 'reorientation'::text)) OR ((rgorient > 1) AND ((origine)::text = 'demenagement'::text)))) OR ((origine IS NOT NULL) AND (date_valid IS NULL) AND (rgorient >= 1) AND ((origine)::text = ANY (ARRAY[('manuelle'::character varying)::text, ('cohorte'::character varying)::text, ('prestaorient'::character varying)::text, ('entdiag'::character varying)::text, ('initinap'::character varying)::text]))));
ALTER TABLE public.orientsstructs ADD CONSTRAINT orientsstructs_origine_in_list_chk CHECK (cakephp_validate_in_list((origine)::text, ARRAY['manuelle'::text, 'cohorte'::text, 'reorientation'::text, 'demenagement'::text, 'prestaorient'::text, 'entdiag'::text, 'initinap'::text]));
ALTER TABLE public.orientsstructs ADD CONSTRAINT orientsstructs_statut_orient_in_list_chk CHECK (cakephp_validate_in_list((statut_orient)::text, ARRAY['Orienté'::text, 'En attente'::text, 'Non orienté'::text, 'Refusé'::text]));

-- Ajout de la colonne de workflow de validation pour les structures référentes
ALTER TABLE public.structuresreferentes ADD COLUMN IF NOT EXISTS workflow_valid int2 NOT NULL DEFAULT 0;

-- Workflow de validation
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Orientation.validation.enabled', 'false', 'Active le workflow de validation des orientations',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Orientation.validation.enabled');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Orientation.validation.enabled';

-- Liste des origines
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Orientation.validation.listeorigine', '["prestaorient","entdiag"]', 'Liste des origines d''orientations qui doivent suivre le workflow de validation
Les origines possibles sont :
"manuelle" => Orientation manuelle
"prestaorient" => Orientation prestataire
"entdiag" => Entretien diagnostic
"inintinap" => Orientation initiale inappropriée',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Orientation.validation.listeorigine');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Orientation.validation.listeorigine';

-- Cohorte de validation
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Orientsstructs.cohorte_validation', '{"filters":{"defaults":{"Dossier":{"dernier":1},"Detailcalculdroitrsa":{"natpf_choice":1,"natpf":["RSD","RSI"]},"Detaildroitrsa":{"oridemrsa_choice":1,"oridemrsa":["DEM"]},"Situationdossierrsa":{"etatdosrsa_choice":1,"etatdosrsa":[2,3,4]}},"accepted":{"Situationdossierrsa.etatdosrsa":[2,3,4],"Detailcalculdroitrsa.natpf":["RSD","RSI","RSU","RSJ"]},"skip":["Dossier.numdemrsa","Dossier.matricule","Dossier.anciennete_dispositif","Serviceinstructeur.id","Dossier.fonorg","Foyer.sitfam","Personne.dtnai","Personne.nomnai","Personne.nir","Personne.sexe","Personne.trancheage"]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":1,"Situationdossierrsa.etatdosrsa":[2,3,4],"Detailcalculdroitrsa.natpf_choice":1,"Detailcalculdroitrsa.natpf":["RSD","RSI","RSU","RSJ"]},"conditions":[],"order":["Dossier.dtdemrsa"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Structureorientante.lib_struc":{"sort":false},"Referentorientant.nom_complet":{"sort":false},"Orientstruct.origine":{"sort":false},"Typeorient.lib_type_orient":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Referent.nom_complet":{"sort":false},"Calculdroitrsa.toppersdrodevorsa":{"sort":false,"type":"boolean"},"Orientstruct.date_propo":{"sort":false},"/Dossiers/view/#Dossier.id#":{"class":"external"}},"innerTable":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Situationdossierrsa.dtclorsa","Situationdossierrsa.moticlorsa","Prestation.rolepers","Situationdossierrsa.etatdosrsa","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Accès à la cohortes de validation d''orientation :
Cohortes > Orientation > Structures référentes > Orientations en attente de validation',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Orientsstructs.cohorte_validation');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Orientsstructs' AND configurations.lib_variable LIKE 'ConfigurableQuery.Orientsstructs.cohorte_validation';

-- Cohorte d'impression des orientations validées
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Orientsstructs.cohorte_orientees_validees', '{"filters":{"defaults":{"Dossier":{"dernier":1},"Detailcalculdroitrsa":{"natpf_choice":1,"natpf":["RSD","RSI"]},"Detaildroitrsa":{"oridemrsa_choice":1,"oridemrsa":["DEM"]},"Situationdossierrsa":{"etatdosrsa_choice":1,"etatdosrsa":[2,3,4]}},"accepted":{"Situationdossierrsa.etatdosrsa":[2,3,4],"Detailcalculdroitrsa.natpf":["RSD","RSI","RSU","RSJ"]},"skip":["Dossier.numdemrsa","Dossier.matricule","Dossier.anciennete_dispositif","Serviceinstructeur.id","Dossier.fonorg","Foyer.sitfam","Personne.dtnai","Personne.nomnai","Personne.nir","Personne.sexe","Personne.trancheage"]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":1,"Situationdossierrsa.etatdosrsa":[2,3,4],"Detailcalculdroitrsa.natpf_choice":1,"Detailcalculdroitrsa.natpf":["RSD","RSI","RSU","RSJ"]},"conditions":[],"order":["Dossier.dtdemrsa"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Structureorientante.lib_struc":{"sort":false},"Referentorientant.nom_complet":{"sort":false},"Orientstruct.origine":{"sort":false},"Typeorient.lib_type_orient":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Referent.nom_complet":{"sort":false},"Calculdroitrsa.toppersdrodevorsa":{"sort":false,"type":"boolean"},"Orientstruct.date_propo":{"sort":false},"/Orientsstructs/impression/#Orientstruct.id#":{"class":"external"},"/Dossiers/view/#Dossier.id#":{"class":"external"}},"innerTable":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Situationdossierrsa.dtclorsa","Situationdossierrsa.moticlorsa","Prestation.rolepers","Situationdossierrsa.etatdosrsa","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}', 'Accès à la cohortes d''impression des orientations validées :
Cohortes > Orientation > Structures référentes > Impression des orientations validées',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Orientsstructs.cohorte_orientees_validees');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Orientsstructs' AND configurations.lib_variable LIKE 'ConfigurableQuery.Orientsstructs.cohorte_orientees_validees';

UPDATE public.configurations
SET value_variable = '{"Personne":{"trancheage":{"0_24":"- 25 ans","25_34":"25 - 34 ans","35_44":"35 - 44 ans","45_54":"45 - 54 ans","55_999":"+ 55 ans"},"trancheagesup":{"18":"> à 18 ans","25":"> à 25 ans","35":"> à 35 ans","45":"> à 45 ans","55":"> à 55 ans"},"trancheageprec":{"18":"< à 18 ans","25":"< à 25 ans","35":"< à 35 ans","45":"< à 45 ans","55":"< à 55 ans"}}}'
WHERE lib_variable = 'Search.Options.enums';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************