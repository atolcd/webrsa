SET client_encoding = 'UTF8';
-- *****************************************************************************
BEGIN;
-- *****************************************************************************

copy (
SELECT current_date AS "e_date_extraction",
id AS "structref_id",
lib_struc AS "structref_lib_struc",
num_voie AS "structref_num_voie",
type_voie AS "structref_type_voie",
nom_voie AS "structref_nom_voie",
code_postal AS "structref_code_postal",
ville AS "structref_ville",
code_insee AS "structref_code_insee",
trim(regexp_replace(numtel,'[^(0-9)]','','g'))
	AS "structref_tel",
typestructure AS "structref_typestructure",
case
	when actif='O' then 'true'
	when actif='N' then 'false'
	ELSE null
end AS "structref_actif"
  FROM structuresreferentes
) to '/etl/rsa/out/FRSA/referents/STRUCTREF_W_yyyy_MM_dd__hh_mm.csv' WITH DELIMITER AS ';' CSV HEADER;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************