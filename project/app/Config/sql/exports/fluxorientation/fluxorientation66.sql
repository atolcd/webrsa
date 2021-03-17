SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

copy (
	with "selections" as (
		select
			DISTINCT ON ("Dossier"."matricule") "Dossier"."matricule" as "Dossier__matricule",
			"Orientstruct"."id" as "Orientstruct__id",
			"Dossier"."matricule" as "MATRICULE",
			"Personne"."qual" AS "CIVILITE",
			"Personne"."nom" AS "NOM",
			"Personne"."email" AS "EMAIL",
			"Personne"."numfixe" AS "TELEPHONE_FIXE",
			"Personne"."numport" AS "TELEPHONE_PORTABLE",
			"Personne"."nomnai" AS "NOMNAI",
			"Personne"."prenom" AS "PRENOM",
			"Personne"."dtnai" AS "DTNAI",
			"Structurereferente"."lib_struc" as "ORGANISME_ORIENTATION_REFERENT",
			'' as "ORGANISME_ORIENTATION_SIRET",
			"Typeorient"."lib_type_orient" as "NATURE_ORIENTATION",
			"Referent"."nom" as "NOM_REFERENT",
			"Referent"."prenom" as "PRENOM_REFERENT",
			"Orientstruct"."date_valid" as "DATE_DEBUT_ORIENTATION",
--			case	when "Situationdossierrsa"."etatdosrsa" in ('2', '3', '4') then 'O'::text
--					else 'N'::text
--			end as "CIBLE",
			'O' as "CIBLE",
			"Personne"."id" as "ID_CD"
		from "public"."orientsstructs" AS "Orientstruct"
			INNER JOIN "public"."personnes" AS "Personne" ON ("Orientstruct"."personne_id" = "Personne"."id")
			INNER JOIN "public"."foyers" AS "Foyer" ON ("Foyer"."id" = "Personne"."foyer_id")
			INNER JOIN "public"."dossiers" AS "Dossier" ON ("Foyer"."dossier_id" = "Dossier"."id")
			INNER JOIN "public"."situationsdossiersrsa" AS "Situationdossierrsa" ON ("Situationdossierrsa"."dossier_id" = "Dossier"."id")
			INNER JOIN "public"."typesorients" AS "Typeorient" ON ("Orientstruct"."typeorient_id" = "Typeorient"."id")
			INNER JOIN "public"."structuresreferentes" AS "Structurereferente" ON ("Structurereferente"."id" = "Orientstruct"."structurereferente_id")
			INNER JOIN "public"."personnes_referents" AS "PersonneReferent" ON ("PersonneReferent"."personne_id" = "Personne"."id" AND "PersonneReferent"."dfdesignation" IS NULL)
			INNER JOIN "public"."referents" AS "Referent" ON ("PersonneReferent"."referent_id" = "Referent"."id")
			INNER JOIN "public"."prestations" AS "Prestation" ON ("Personne"."id" = "Prestation"."personne_id")
		where "Prestation"."rolepers" IN ('DEM', 'CJT')
			and "Dossier"."matricule" not like ''
		order by "Dossier"."matricule", "Personne"."id" desc, "Orientstruct"."date_valid" desc
	)
	select
		"Orientstruct__id",
		ltrim("MATRICULE", '0') as "MATRICULE",
--		right("MATRICULE", 7) as "MATRICULE",
		"CIVILITE",
		"NOM",
		"NOMNAI",
		"PRENOM",
		"DTNAI",
		"ORGANISME_ORIENTATION_REFERENT",
		"ORGANISME_ORIENTATION_SIRET",
		"NATURE_ORIENTATION",
		"NOM_REFERENT",
		"PRENOM_REFERENT",
		"DATE_DEBUT_ORIENTATION",
		"CIBLE",
		"EMAIL",
		"TELEPHONE_FIXE",
		"TELEPHONE_PORTABLE",
		"ID_CD"
	from "selections"
) to '/etl/rsa/out/Orientation.csv' WITH DELIMITER AS ';' CSV HEADER;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
