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
-- Questionnaire B7 D2

ALTER TABLE sortiesaccompagnementsd2pdvs93 ADD COLUMN code character varying(50);
UPDATE sortiesaccompagnementsd2pdvs93 SET code = 'SORTIE_D2' WHERE name = 'Accès à un emploi CDI';
UPDATE sortiesaccompagnementsd2pdvs93 SET code = 'SORTIE_D2' WHERE name = 'Accès à un emploi CDD de + de 6 mois';
UPDATE sortiesaccompagnementsd2pdvs93 SET code = 'SORTIE_D2' WHERE name = 'Accès à un emploi temporaire (CDD de - de 6 mois, intérim)';
UPDATE sortiesaccompagnementsd2pdvs93 SET code = 'SORTIE_D2' WHERE name = 'Accès à une activité d''indépendant, création d''entreprise';
UPDATE sortiesaccompagnementsd2pdvs93 SET code = 'SORTIE_D2' WHERE name = 'Accès à un emploi aidé';
UPDATE sortiesaccompagnementsd2pdvs93 SET code = 'SORTIE_D2' WHERE name = 'Accès à un emploi salarié SIAE';

ALTER TABLE sortiesaccompagnementsd2pdvs93 ADD COLUMN codeTypeEmploi character varying(10);
UPDATE sortiesaccompagnementsd2pdvs93 SET codeTypeEmploi = 'EMP_CDI' WHERE name = 'Accès à un emploi CDI';
UPDATE sortiesaccompagnementsd2pdvs93 SET codeTypeEmploi = 'EMP_CDD' WHERE name = 'Accès à un emploi CDD de + de 6 mois';
UPDATE sortiesaccompagnementsd2pdvs93 SET codeTypeEmploi = 'INT_CDD' WHERE name = 'Accès à un emploi temporaire (CDD de - de 6 mois, intérim)';
UPDATE sortiesaccompagnementsd2pdvs93 SET codeTypeEmploi = 'ACT_IND' WHERE name = 'Accès à une activité d''indépendant, création d''entreprise';
UPDATE sortiesaccompagnementsd2pdvs93 SET codeTypeEmploi = 'EMP_AIDE' WHERE name = 'Accès à un emploi aidé';
UPDATE sortiesaccompagnementsd2pdvs93 SET codeTypeEmploi = 'EMP_SIAE' WHERE name = 'Accès à un emploi salarié SIAE';

ALTER TABLE typeemplois ADD COLUMN codeTypeEmploi character varying(10);
UPDATE typeemplois SET codeTypeEmploi = 'EMP_CDI' WHERE name = 'Accès à un emploi CDI';
UPDATE typeemplois SET codeTypeEmploi = 'EMP_CDD' WHERE name = 'Accès à un emploi CDD de plus de 6 mois';
UPDATE typeemplois SET codeTypeEmploi = 'INT_CDD' WHERE name = 'Accès à un emploi temporaire (CDD de moins de 6 mois, intérim)';
UPDATE typeemplois SET codeTypeEmploi = 'ACT_IND' WHERE name = 'Accès à une activité d''indépendant, création d''entreprise';
UPDATE typeemplois SET codeTypeEmploi = 'EMP_AIDE' WHERE name = 'Accès à un emploi aidé';
UPDATE typeemplois SET codeTypeEmploi = 'EMP_SIAE' WHERE name = 'Accès à un emploi salarié SIAE';


-- *****************************************************************************
COMMIT;
-- *****************************************************************************