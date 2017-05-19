-- Après avoir repassé les patches eps-schema-current et eps-datas-current, pour "remettre à zéro";
BEGIN;
DELETE FROM orientsstructs CASCADE WHERE date_valid = now()::date;
DELETE FROM contratsinsertion CASCADE WHERE dd_ci = now()::date;
UPDATE contratsinsertion
	SET df_ci = df_ci + interval '1 mons'
	WHERE df_ci = now()::date;
COMMIT;

-- CG 66: personnes qui peuvent avoir une saisine bilan de parcours dans la zone Perpignan 1
-- TODO: dates

SELECT orientsstructs.personne_id
	FROM orientsstructs
		INNER JOIN prestations ON (
			orientsstructs.personne_id = prestations.personne_id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
		INNER JOIN contratsinsertion ON (
			contratsinsertion.personne_id = orientsstructs.personne_id
			AND contratsinsertion.structurereferente_id = orientsstructs.structurereferente_id
			AND contratsinsertion.df_ci >= now()
		)
		INNER JOIN personnes ON (
			contratsinsertion.personne_id = personnes.id
		)
		INNER JOIN foyers ON (
			personnes.foyer_id = foyers.id
		)
		INNER JOIN adressesfoyers ON (
			adressesfoyers.foyer_id = foyers.id
			AND adressesfoyers.rgadr = '01'
		)
		INNER JOIN adresses ON (
			adressesfoyers.adresse_id = adresses.id
		)
		INNER JOIN cantons ON (
			cantons.zonegeographique_id = '8'
			AND adresses.nomvoie ILIKE cantons.nomvoie
			AND adresses.locaadr ILIKE cantons.locaadr
		)
	WHERE orientsstructs.statut_orient = 'Orienté'
		AND orientsstructs.referent_id IS NOT NULL;

-- CG 93: personnes qui peuvent avoir une demande de réorientation transmise par
-- la structure référente pour les zones géographiques de EPINAY-SUR-SEINE,
-- PIERREFITTE-SUR-SEINE et SAINT-OUEN
-- TODO: dates

SELECT orientsstructs.personne_id
	FROM orientsstructs
		INNER JOIN prestations ON (
			orientsstructs.personne_id = prestations.personne_id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
		INNER JOIN contratsinsertion ON (
			contratsinsertion.personne_id = orientsstructs.personne_id
			AND contratsinsertion.structurereferente_id = orientsstructs.structurereferente_id
			AND contratsinsertion.df_ci >= now()
		)
		INNER JOIN personnes ON (
			contratsinsertion.personne_id = personnes.id
		)
		INNER JOIN foyers ON (
			personnes.foyer_id = foyers.id
		)
		INNER JOIN adressesfoyers ON (
			adressesfoyers.foyer_id = foyers.id
			AND adressesfoyers.rgadr = '01'
		)
		INNER JOIN adresses ON (
			adressesfoyers.adresse_id = adresses.id
			AND adresses.numcomptt IN ( '93031', '93059', '93070' )
		)
	WHERE orientsstructs.statut_orient = 'Orienté'
		AND orientsstructs.referent_id IS NOT NULL;
