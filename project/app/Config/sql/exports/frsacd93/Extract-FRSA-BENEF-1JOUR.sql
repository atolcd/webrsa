SET client_encoding = 'UTF8';
-- *****************************************************************************
BEGIN;
-- *****************************************************************************

copy (
SELECT current_date AS "e_date_extraction",
personnes.id AS "i_identifiant",
personnes.nom AS "i_nom_usage",
personnes.nomnai AS "i_nom_naissance",
personnes.prenom AS "i_prenoms",
personnes.dtnai AS "i_date_nai",
dossiers.matricule AS "i_numcaf",
CASE
	when sexe='1' then 'Homme'
	when sexe='2' then 'Femme'
	when sexe NOT IN ('1', '2') AND personnes.qual='MR'  then 'Homme'
	when sexe NOT IN ('1', '2') AND personnes.qual='MME'  then 'Femme'
	ELSE null
END AS "i_sexe",
CASE
	when prestations.rolepers='DEM' then 'Demandeur'
	when prestations.rolepers='CJT' then 'Conjoint'
	when prestations.rolepers='ENF' then 'Enfant'
	when prestations.rolepers='AUT' then 'Autre'
	ELSE null
END AS "i_role",
adressesfoyers.dtemm AS "adr_dtemm",
adresses.numvoie AS "adr_numvoie",
adresses.nomvoie AS "adr_nomvoie",
adresses.libtypevoie AS "adr_libtypevoie",
adresses.nomcom AS "adr_nomcom",
adresses.complideadr AS "adr_complideadr",
adresses.compladr AS "adr_compladr",
adresses.lieudist AS "adr_lieudist",
adresses.codepos AS "adr_codepos",
adresses.pays AS "adr_pays",
adresses.canton AS "adr_canton",
trim(regexp_replace(personnes.numfixe,'[^(0-9)]','','g'))
	AS "c_fixe",
trim(regexp_replace(personnes.numport,'[^(0-9)]','','g'))
	AS "c_mobile",
personnes.email AS "c_email",
dossiers.dtdemrsa AS "date_demandersa",
CASE
	WHEN calculsdroitsrsa.toppersdrodevorsa='1' THEN 'true'
	WHEN calculsdroitsrsa.toppersdrodevorsa='0' THEN 'false'
	ELSE null
END AS "droit_devoir",
situationsdossiersrsa.etatdosrsa AS "etat_dossier",
referents.id AS "ref_identifiant",
referents.structurereferente_id AS "ref_structurereferente_id",
cers93.nivetu AS "cer_etudes_niveau",
(SELECT jsonb_agg(jsonb_build_object('diplome', diplomescers93.name, 'annee', diplomescers93.annee, 'etranger' , CAST (diplomescers93.isetranger AS boolean)))
	FROM diplomescers93 WHERE diplomescers93.cer93_id=cers93.id GROUP BY cer93_id)
 AS "cer_diplomes_ou_certifications",
(SELECT jsonb_agg(jsonb_build_object('nom',metiersexerces.name,'annee',expsproscers93.anneedeb,'duree_valeur',expsproscers93.nbduree,'duree_unite',expsproscers93.typeduree))
	FROM expsproscers93
	INNER JOIN  metiersexerces ON metiersexerces.id = expsproscers93.metierexerce_id
	WHERE expsproscers93.cer93_id=cers93.id GROUP BY cer93_id)
AS "cer_experiences_professionnelles",
cers93.autresexps AS "cer_autres_experiences_et_savoirs",
contratsinsertion.dd_ci AS "cer_datedeb",
contratsinsertion.df_ci AS "cer_datefin",
to_char( cers93.modified, 'YYYY-MM-DD') AS "cer_modified",
CASE
	WHEN dsps.toppermicondub='1' THEN 'true'
	WHEN dsps.toppermicondub='0' THEN 'false'
	ELSE null
END AS "profil_permis_b"
-- Tables
FROM dossiers
INNER JOIN foyers ON (foyers.dossier_id = dossiers.id)
INNER JOIN personnes ON (personnes.foyer_id = foyers.id)
INNER JOIN derniersdossiersallocataires ON
	(derniersdossiersallocataires.dossier_id = dossiers.id
	AND derniersdossiersallocataires.personne_id = personnes.id)
INNER JOIN prestations ON (prestations.personne_id = personnes.id AND prestations.natprest = 'RSA')
INNER JOIN situationsdossiersrsa ON (situationsdossiersrsa.dossier_id = dossiers.id)
INNER JOIN adressesfoyers ON ( adressesfoyers.foyer_id = foyers.id AND rgadr = '01' )
INNER JOIN adresses ON ( adressesfoyers.adresse_id = adresses.id )
LEFT OUTER JOIN calculsdroitsrsa ON (calculsdroitsrsa.personne_id = personnes.id)
LEFT OUTER JOIN personnes_referents ON (
	personnes_referents.personne_id = personnes.id
	AND ((personnes_referents.id IS NULL) OR (personnes_referents.id IN
	( SELECT personnes_referents.id FROM personnes_referents
		WHERE personnes_referents.personne_id = personnes.id
		AND personnes_referents.dfdesignation IS NULL
		ORDER BY personnes_referents.dddesignation DESC LIMIT 1
	)))
)
LEFT OUTER JOIN referents ON (personnes_referents.referent_id = referents.id)
LEFT OUTER JOIN contratsinsertion ON (
	personnes.id = contratsinsertion.personne_id
	AND contratsinsertion.id IN(
		SELECT  max(sub_contratsinsertion.id) AS id
		FROM contratsinsertion AS sub_contratsinsertion
		WHERE sub_contratsinsertion.personne_id = contratsinsertion.personne_id
	)
)
LEFT OUTER JOIN cers93 ON ( contratsinsertion.id = cers93.contratinsertion_id )
LEFT OUTER JOIN dsps ON ( personnes.id = dsps.personne_id )
-- Conditions
WHERE
prestations.rolepers IN ('DEM','CJT')
AND (
	dossiers.id IN (
		SELECT dossiersmodifies.dossier_id FROM dossiersmodifies
		WHERE dossiersmodifies.modified >= (now() - interval '7' DAY)
	)
	OR  foyers.id IN (
		SELECT evenements.foyer_id FROM evenements
		WHERE evenements.dtliq >= (now() - interval '7' DAY)
	)
)
) to '/etl/rsa/out/FRSA/beneficiaire/BENEF_W_yyyy_MM_dd__hh_mm.csv' WITH DELIMITER AS ';' CSV HEADER;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
