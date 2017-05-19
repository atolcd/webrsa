SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

BEGIN;

-- *****************************************************************************

-- Indexes pour accélérer les requêtes (surtout dans /repsddtefp/suivicontrole/ ; 294 s -> )
CREATE INDEX apres_statutapre_idx ON apres (statutapre);
CREATE INDEX apres_eligibiliteapre_idx ON apres (eligibiliteapre);
CREATE INDEX apres_etatdossierapre_idx ON apres (etatdossierapre);
CREATE INDEX apres_datedemandeapre_idx ON apres ( datedemandeapre );
CREATE INDEX apres_datedemandeapre_year_idx ON apres ( EXTRACT( YEAR FROM datedemandeapre ) );
CREATE INDEX apres_datedemandeapre_month_idx ON apres ( EXTRACT( MONTH FROM datedemandeapre ) );
CREATE INDEX apres_datedemandeapre_day_idx ON apres ( EXTRACT( DAY FROM datedemandeapre ) );
CREATE INDEX apres_datedemandeapre_quarter_idx ON apres ( EXTRACT( QUARTER FROM datedemandeapre ) );

CREATE INDEX etatsliquidatifs_typeapre_idx ON etatsliquidatifs (typeapre);
CREATE INDEX etatsliquidatifs_datecloture_idx ON etatsliquidatifs ( datecloture );
CREATE INDEX etatsliquidatifs_datecloture_day_idx ON etatsliquidatifs ( EXTRACT(DAY FROM datecloture ) );
CREATE INDEX etatsliquidatifs_datecloture_month_idx ON etatsliquidatifs ( EXTRACT(MONTH FROM datecloture ) );
CREATE INDEX etatsliquidatifs_datecloture_year_idx ON etatsliquidatifs ( EXTRACT(YEAR FROM datecloture ) );

--CREATE INDEX personnes_age_dtnai_idx ON personnes ( AGE( dtnai ) );
--CREATE INDEX personnes_age_dtnai_year_idx ON personnes ( EXTRACT ( YEAR FROM AGE( dtnai ) ) );
CREATE INDEX personnes_sexe_idx ON personnes ( sexe );
-- apres_etatsliquidatifs montantattribue

-- *****************************************************************************

UPDATE apres
    SET eligibiliteapre = 'O' WHERE apres.statutapre ='F';
UPDATE apres
    SET etatdossierapre = 'COM' WHERE apres.statutapre ='F';

-- *****************************************************************************

ALTER TABLE actionscandidats_personnes ADD COLUMN bilanrecu type_no DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ADD COLUMN daterecu DATE;
ALTER TABLE actionscandidats_personnes ADD COLUMN personnerecu VARCHAR( 50 );
-- *****************************************************************************
COMMIT;