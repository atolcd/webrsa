SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.29.5', CURRENT_TIMESTAMP);

-- enregistrement de maintien et reorientation dans les decisions
alter table decisionsnonrespectssanctionseps93 alter column decision type VARCHAR(30);
ALTER TABLE decisionsnonrespectssanctionseps93 DROP CONSTRAINT IF EXISTS decisionsnonrespectssanctionseps93_decision_in_list_chk;
ALTER TABLE decisionsnonrespectssanctionseps93 ADD CONSTRAINT decisionsnonrespectssanctionseps93_decision_in_list_chk check (cakephp_validate_in_list((decision)::text, ARRAY['1reduction'::text, '1maintien'::text, '1pasavis'::text, '1delai'::text, '2suspensiontotale'::text, '2suspensionpartielle'::text, '2maintien'::text, '2pasavis'::text, 'annule'::text, 'reporte'::text, 'maintienreorientation'::text]));


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
