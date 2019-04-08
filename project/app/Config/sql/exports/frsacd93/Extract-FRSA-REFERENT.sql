SET client_encoding = 'UTF8';
-- *****************************************************************************
BEGIN;
-- *****************************************************************************

copy (
SELECT current_date AS "e_date_extraction",
id AS "ref_identifiant",
structurereferente_id AS "ref_structurereferente_id",
nom AS "ref_nom",
prenom AS "ref_prenom",
trim(regexp_replace(numero_poste,'[^(0-9)]','','g'))
	AS "ref_numposte",
email AS "ref_email",
CASE
	when qual='MR' then 'Homme'
	when qual='MME' then 'Femme'
	ELSE null
END AS "ref_qual",
fonction AS "ref_fonction",
case
	when actif='O' then 'true'
	when actif='N' then 'false'
	ELSE null
end AS "ref_actif",
datecloture AS "ref_datecloture"
FROM referents
) to '/etl/rsa/out/FRSA/referents/REFERENT_W_yyyy_MM_dd__hh_mm.csv' WITH DELIMITER AS ';' CSV HEADER;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
