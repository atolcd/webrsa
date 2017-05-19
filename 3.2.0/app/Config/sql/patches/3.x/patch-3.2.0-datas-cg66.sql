SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- 20170421: suite à la modification du paramétrage des poles PCG, il faut lier le bon type de dossier PCG
UPDATE polesdossierspcgs66
	SET typepdo_id = ( SELECT typespdos.id FROM typespdos WHERE typespdos.libelle = 'Position Mission PDA-MGA (Auto)' LIMIT 1 )
	WHERE name = 'PDA';

UPDATE polesdossierspcgs66
	SET typepdo_id = ( SELECT typespdos.id FROM typespdos WHERE typespdos.libelle = 'Position Mission PDU-MMR (Auto)' LIMIT 1 )
	WHERE name = 'PDU';

-- 20170424: déplacement de l'ancienne configuration de "Generationdossierpcg.
-- Orgtransmisdossierpcg66.id" dans les données de la table orgstransmisdossierspcgs66
UPDATE orgstransmisdossierspcgs66
	SET generation_auto = '1'
	WHERE id IN ( 4, 6 );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************