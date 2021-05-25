SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Configuration de la recherche par données Pôle Emploi
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
select 'Module.RecherchePoleEmploi.enabled', 'true', 'Accès à la recherche par données Pôle Emploi', current_timestamp, current_timestamp
where not exists (select id from configurations where lib_variable LIKE 'Module.RecherchePoleEmploi.enabled');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.RecherchePoleEmploi.enabled';

-- Création d'une fonction permettant de mettre toutes les personnes en radié PE
CREATE OR REPLACE FUNCTION radie_pe(motif_radiation TEXT, lib_radiation TEXT) RETURNS boolean
LANGUAGE plpgsql
AS $function$
DECLARE
	infope RECORD;
BEGIN
FOR infope IN
	SELECT
		DISTINCT ON (i.id) i.id AS infope__id,
		i.nir AS infope__nir,
		i.nom AS infope__nom,
		i.prenom AS infope__prenom,
		i.dtnai AS infope__dtnai,
		i.individu_nom_marital AS infope__individu_nom_marital,
		i.individu_certification_identite AS infope__individu_certification_identite,
		i.individu_commune_residence AS infope__individu_commune_residence,
		i.allocataire_identifiant_caf AS infope__allocataire_identifiant_caf,
		i.allocataire_identifiant_msa AS infope__allocataire_identifiant_msa,
		i.allocataire_code_pe AS infope__allocataire_code_pe,
		i.allocataire_identifiant_pe AS infope__allocataire_identifiant_pe,
		i.inscription_date_debut_ide AS infope__inscription_date_debut_ide,
		i.inscription_code_categorie AS infope__inscription_code_categorie,
		i.inscription_lib_categorie AS infope__inscription_lib_categorie,
		i.inscription_code_situation AS infope__inscription_code_situation,
		i.inscription_lib_situation AS infope__inscription_lib_situation,
		i.inscription_date_cessation_ide AS infope__inscription_date_cessation_ide,
		i.inscription_motif_cessation_ide AS infope__inscription_motif_cessation_ide,
		i.inscription_lib_cessation_ide AS infope__inscription_lib_cessation_ide,
		i.inscription_date_radiation_ide AS infope__inscription_date_radiation_ide,
		i.inscription_motif_radiation_ide AS infope__inscription_motif_radiation_ide,
		i.inscription_lib_radiation_ide AS infope__inscription_lib_radiation_ide,
		i.suivi_structure_principale_nom AS infope__suivi_structure_principale_nom,
		i.suivi_structure_principale_voie AS infope__suivi_structure_principale_voie,
		i.suivi_structure_principale_complement AS infope__suivi_structure_principale_complement,
		i.suivi_structure_principale_code_postal AS infope__suivi_structure_principale_code_postal,
		i.suivi_structure_principale_cedex AS infope__suivi_structure_principale_cedex,
		i.suivi_structure_principale_bureau AS infope__suivi_structure_principale_bureau,
		i.suivi_structure_deleguee_nom AS infope__suivi_structure_deleguee_nom,
		i.suivi_structure_deleguee_voie AS infope__suivi_structure_deleguee_voie,
		i.suivi_structure_deleguee_complement AS infope__suivi_structure_deleguee_complement,
		i.suivi_structure_deleguee_code_postal AS infope__suivi_structure_deleguee_code_postal,
		i.suivi_structure_deleguee_cedex AS infope__suivi_structure_deleguee_cedex,
		i.suivi_structure_deleguee_bureau AS infope__suivi_structure_deleguee_bureau,
		i.formation_code_niveau AS infope__formation_code_niveau,
		i.formation_lib_niveau AS infope__formation_lib_niveau,
		i.formation_code_secteur AS infope__formation_code_secteur,
		i.formation_lib_secteur AS infope__formation_lib_secteur,
		i.romev3_code_rome AS infope__romev3_code_rome,
		i.romev3_lib_rome AS infope__romev3_lib_rome,
		i.ppae_conseiller_pe AS infope__ppae_conseiller_pe,
		i.ppae_date_signature AS infope__ppae_date_signature,
		i.ppae_date_notification AS infope__ppae_date_notification,
		i.ppae_axe_code AS infope__ppae_axe_code,
		i.ppae_axe_libelle AS infope__ppae_axe_libelle,
		i.ppae_modalite_code AS infope__ppae_modalite_code,
		i.ppae_modalite_libelle AS infope__ppae_modalite_libelle,
		i.ppae_date_dernier_ent AS infope__ppae_date_dernier_ent,
		i.date_creation AS infope__date_creation,
		i.date_modification AS infope__date_modification,
		h.informationpe_id AS histo__informationpe_id,
		h.identifiantpe AS histo__identifiantpe,
		h."date" AS histo__date,
		h.etat AS histo__etat,
		h.code AS histo__code,
		h.motif AS histo__motif,
		h.codeinsee AS histo__codeinsee,
		h.localite AS histo__localite,
		h.adresse AS histo__adresse,
		h.ale AS histo__ale
	FROM
		informationspe i
	LEFT JOIN historiqueetatspe h ON h.informationpe_id = i.id
	ORDER BY i.id, h.id DESC
	LOOP
		IF infope.histo__etat = 'inscription' OR
		(
			infope.infope__inscription_date_debut_ide IS NOT NULL
			AND infope.infope__inscription_date_radiation_ide IS NULL
			AND infope.infope__inscription_date_cessation_ide IS NULL
		) THEN
			INSERT INTO historiqueetatspe (
				informationpe_id,
				identifiantpe,
				"date",
				etat,
				code,
				motif,
				codeinsee,
				localite,
				adresse,
				ale,
				inscription_date_debut_ide,
				inscription_code_categorie,
				inscription_lib_categorie,
				inscription_code_situation,
				inscription_lib_situation,
				inscription_date_cessation_ide,
				inscription_motif_cessation_ide,
				inscription_lib_cessation_ide,
				inscription_date_radiation_ide,
				inscription_motif_radiation_ide,
				inscription_lib_radiation_ide,
				suivi_structure_principale_nom,
				suivi_structure_principale_voie,
				suivi_structure_principale_complement,
				suivi_structure_principale_code_postal,
				suivi_structure_principale_cedex,
				suivi_structure_principale_bureau,
				suivi_structure_deleguee_nom,
				suivi_structure_deleguee_voie,
				suivi_structure_deleguee_complement,
				suivi_structure_deleguee_code_postal,
				suivi_structure_deleguee_cedex,
				suivi_structure_deleguee_bureau,
				formation_code_niveau,
				formation_lib_niveau,
				formation_code_secteur,
				formation_lib_secteur,
				romev3_code_rome,
				romev3_lib_rome,
				ppae_conseiller_pe,
				ppae_date_signature,
				ppae_date_notification,
				ppae_axe_code,
				ppae_axe_libelle,
				ppae_modalite_code,
				ppae_modalite_libelle,
				ppae_date_dernier_ent,
				date_creation,
				date_modification
			)
			VALUES (
				infope.infope__id,
				CONCAT(infope.infope__allocataire_identifiant_pe, infope.infope__allocataire_code_pe),
				DATE( NOW() ),
				'radiation',
				infope.infope__inscription_code_categorie,
				infope.infope__inscription_lib_categorie,
				infope.histo__codeinsee,
				infope.histo__localite,
				infope.histo__adresse,
				infope.histo__ale,
				infope.infope__inscription_date_debut_ide,
				infope.infope__inscription_code_categorie,
				infope.infope__inscription_lib_categorie,
				infope.infope__inscription_code_situation,
				infope.infope__inscription_lib_situation,
				infope.infope__inscription_date_cessation_ide,
				infope.infope__inscription_motif_cessation_ide,
				infope.infope__inscription_lib_cessation_ide,
				DATE( NOW() ),
				motif_radiation,
				lib_radiation,
				infope.infope__suivi_structure_principale_nom,
				infope.infope__suivi_structure_principale_voie,
				infope.infope__suivi_structure_principale_complement,
				infope.infope__suivi_structure_principale_code_postal,
				infope.infope__suivi_structure_principale_cedex,
				infope.infope__suivi_structure_principale_bureau,
				infope.infope__suivi_structure_deleguee_nom,
				infope.infope__suivi_structure_deleguee_voie,
				infope.infope__suivi_structure_deleguee_complement,
				infope.infope__suivi_structure_deleguee_code_postal,
				infope.infope__suivi_structure_deleguee_cedex,
				infope.infope__suivi_structure_deleguee_bureau,
				infope.infope__formation_code_niveau,
				infope.infope__formation_lib_niveau,
				infope.infope__formation_code_secteur,
				infope.infope__formation_lib_secteur,
				infope.infope__romev3_code_rome,
				infope.infope__romev3_lib_rome,
				infope.infope__ppae_conseiller_pe,
				infope.infope__ppae_date_signature,
				infope.infope__ppae_date_notification,
				infope.infope__ppae_axe_code,
				infope.infope__ppae_axe_libelle,
				infope.infope__ppae_modalite_code,
				infope.infope__ppae_modalite_libelle,
				infope.infope__ppae_date_dernier_ent,
				NOW(),
				NOW()
			);
			UPDATE informationspe
			SET inscription_date_radiation_ide = DATE( NOW() ),
				inscription_motif_radiation_ide = motif_radiation,
				inscription_lib_radiation_ide = lib_radiation,
				date_modification = DATE( NOW() )
			WHERE id = infope.infope__id;
		END IF;
	END LOOP;
	RETURN 't';
END;
$function$;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************