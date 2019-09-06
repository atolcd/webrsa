SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

copy (
	select
		DISTINCT ON ("Orientstruct"."id") "Orientstruct"."id" as "Orientstruct__id",
		"Dossier"."matricule" as "MATRICULE",
		"Personne"."qual" AS "CIVILITE",
		"Personne"."nom" AS "NOM",
		"Personne"."nomnai" AS "NOMNAI",
		"Personne"."prenom" AS "PRENOM",
		"Personne"."dtnai" AS "DTNAI",
		"Structurereferente"."lib_struc" as "ORGANISME_ORIENTATION_REFERENT",
		'' as "ORGANISME_ORIENTATION_SIRET",
		"Typeorient"."lib_type_orient" as "NATURE_ORIENTATION",
		"Referent"."nom" as "NOM_REFERENT",
		"Referent"."prenom" as "PRENOM_REFERENT",
		"Orientstruct"."date_valid" as "DATE_DEBUT_ORIENTATION",
		'O' as "CIBLE",
		"Personne"."id" as "ID_CD"
	from "public"."orientsstructs" AS "Orientstruct"
		INNER JOIN "public"."personnes" AS "Personne" ON ("Orientstruct"."personne_id" = "Personne"."id")
		INNER JOIN "public"."foyers" AS "Foyer" ON ("Foyer"."id" = "Personne"."foyer_id")
		INNER JOIN "public"."dossiers" AS "Dossier" ON ("Foyer"."dossier_id" = "Dossier"."id")
		INNER JOIN "public"."situationsdossiersrsa" AS "Situationdossierrsa" ON ("Situationdossierrsa"."dossier_id" = "Dossier"."id")
		INNER JOIN "public"."typesorients" AS "Typeorient" ON ("Orientstruct"."typeorient_id" = "Typeorient"."id")
		INNER JOIN "public"."structuresreferentes" AS "Structurereferente" ON ("Structurereferente"."id" = "Orientstruct"."structurereferente_id")
		INNER JOIN "public"."referents" AS "Referent" ON ("Structurereferente"."id" = "Referent"."structurereferente_id")
		INNER JOIN "public"."prestations" AS "Prestation" ON ("Personne"."id" = "Prestation"."personne_id")
	where "Prestation"."rolepers" IN ('DEM', 'CJT')
	order by "Orientstruct"."id", "Personne"."id", "Orientstruct"."date_valid"
) to '/etl/rsa/out/Orientation.csv' WITH DELIMITER AS ';' CSV HEADER;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
