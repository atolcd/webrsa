SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- *****************************************************************************
-- psql -U webrsa -d webrsa -h 127.0.0.1 -f /var/www/webrsa-web1/public_html/app/Config/sql/exports/fluxinstructionorientation/fluxinstructionorientation66.sql
-- *****************************************************************************

copy (
	select
		DISTINCT ON ("Personne"."id") "Personne"."id" as "ID_PERSONNE",
		"Orientstruct"."id" as "ID_ORIENTATION",
		-- Identification
		"Personne"."qual" AS "QUAL",
		"Personne"."nom" AS "NOM",
		"Personne"."nomnai" AS "NOMNAI",
		"Personne"."prenom" AS "PRENOM",
		"Personne"."nomcomnai" as "NOMCOMNAI",
		"Personne"."dtnai" AS "DTNAI",
		"Personne"."typedtnai" AS "TYPEDTNAI",
		"Personne"."nir" AS "NIR",
		"Personne"."sexe" AS "SEXE",
		--Nationalité
		"Personne"."nati" AS "NATI",
		"Personne"."dtnati" AS "DTNATI",
		-- Prestation
		"Prestation"."natprest" AS "NATPREST",
		"Prestation"."rolepers" AS "ROLEPERS",
		-- Activité
		"Activite"."act" AS "ACT",
		"Activite"."ddact" AS "DDACT",
		-- Orientation
		"Structurereferente"."lib_struc" as "ORGANISME_ORIENTATION_REFERENT",
		"Typeorient"."lib_type_orient" as "NATURE_ORIENTATION",
		"Orientstruct"."date_propo" as "DATE_DEMANDE_ORIENTATION",
		"Orientstruct"."date_valid" as "DATE_DEBUT_ORIENTATION"
	from "public"."orientsstructs" AS "Orientstruct"
		INNER JOIN "public"."personnes" AS "Personne" ON ("Orientstruct"."personne_id" = "Personne"."id")
		INNER JOIN "public"."foyers" AS "Foyer" ON ("Foyer"."id" = "Personne"."foyer_id")
		INNER JOIN "public"."dossiers" AS "Dossier" ON ("Foyer"."dossier_id" = "Dossier"."id")
		INNER JOIN "public"."situationsdossiersrsa" AS "Situationdossierrsa" ON ("Situationdossierrsa"."dossier_id" = "Dossier"."id")
		INNER JOIN "public"."typesorients" AS "Typeorient" ON ("Orientstruct"."typeorient_id" = "Typeorient"."id")
		INNER JOIN "public"."structuresreferentes" AS "Structurereferente" ON ("Structurereferente"."id" = "Orientstruct"."structurereferente_id")
		INNER JOIN "public"."referents" AS "Referent" ON ("Structurereferente"."id" = "Referent"."structurereferente_id")
		INNER JOIN "public"."prestations" AS "Prestation" ON ("Personne"."id" = "Prestation"."personne_id")
		INNER JOIN "public"."activites" AS "Activite" ON ("Personne"."id" = "Activite"."personne_id")
	where "Prestation"."rolepers" IN ('DEM', 'CJT')
		and "Orientstruct"."rgorient" = 1
		and "Orientstruct"."date_propo" >= '2018-01-01'
	order by "Personne"."id", "Orientstruct"."date_propo", "Orientstruct"."date_valid"
) to '/etl/rsa/out/InstructionOrientation.csv' WITH DELIMITER AS ';' CSV HEADER;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
