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

-- Suppression de certaines valeurs de la liste déroulante des modalitées de transmission
DELETE FROM modstransmsfps93 WHERE name IN ('par fax', 'courrier postal', 'par mail' );

--------------------------------------------------------------------------------

-- Ajout des valeurs  de la liste déroulante des motifs de contact
INSERT INTO motifscontactsfps93 ( name, created, modified )
VALUES
	( 'Information collective', NOW(), NOW() ),
	( 'Entretien', NOW(), NOW() ),
	( 'Tests d’accès', NOW(), NOW() ),
	( 'Sélection', NOW(), NOW() ),
	( 'Diagnostic', NOW(), NOW() );

--------------------------------------------------------------------------------

-- Mise a jour des valeurs de la liste déroulante de motifs de non retenue par la structure
UPDATE motifsnonretenuesfps93
	SET name = 'Sur liste d''attente'
	WHERE name LIKE 'En file dattente'

INSERT INTO motifsnonretenuesfps93
    (name, autre, created, modified)
    VALUES ( 'Refus de financement', 0, NOW(), NOW()
);


--------------------------------------------------------------------------------

-- Mise a jour des valeurs de la liste déroulante de motifs de non intégration a l'action
UPDATE motifsnonintegrationsfps93
	SET name = 'Changement de Projet Professionnel'
	WHERE name LIKE 'Abandon'

INSERT INTO motifsnonintegrationsfps93
	(name, autre, created, modified)
    VALUES ( 'Le bénéficiaire ne souhaite plus intégrer l''action', 0, NOW(), NOW() )
);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************