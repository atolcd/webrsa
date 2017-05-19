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

-- *****************************************************************************
-- @fixme re-création de la vue ... voir balises contenues dans flux refonte
-- bénéficiaire et instruction
-- *****************************************************************************

CREATE VIEW referentiel.fse_employabilites AS
	SELECT
		   toto.id,
		   toto.personne_id,
		   toto.reg,
		   toto.statuempl,
		   toto.paysact,
		   toto.ddstatuempl,
		   toto.dfstatuempl,
		   toto.natcontrtra,
		   toto.topcondadmnonsal,
		   toto.hauremusmic
		FROM (
			 SELECT
				 employabilites.id,
				 employabilites.personne_id,
				 employabilites.reg,
				 employabilites.statuempl,
				 employabilites.paysact,
				 employabilites.ddstatuempl,
				 employabilites.dfstatuempl,
				 employabilites.natcontrtra,
				 avispcgpersonnes.topcondadmnonsal,
				 employabilites.hauremusmic,
				 row_number() OVER (PARTITION BY employabilites.personne_id ORDER BY employabilites.ddstatuempl DESC) AS rn
			 FROM employabilites
				LEFT OUTER JOIN avispcgpersonnes ON ( avispcgpersonnes.personne_id = employabilites.personne_id )
		 ) toto
		 WHERE toto.rn = 1;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
