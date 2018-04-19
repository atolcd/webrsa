SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
SELECT NOW();
BEGIN;
-- *****************************************************************************

INSERT INTO thematiquesfps93 ( type, name, created, modified, yearthema)
VALUES
	('horspdi',  '01 POLE EMPLOI', NOW(), NOW(),'2018' ),
	('horspdi', '02 REGION / AFPA', NOW(), NOW(),'2018' ),
	('horspdi', '03 DEPARTEMENT', NOW(), NOW(),'2018' ),
	('horspdi',  '04 PLIE', NOW(), NOW(),'2018' ),
	('horspdi',  '05 MAISON EMPLOI, SERVICE EMPLOI ou EQUIVALENT', NOW(), NOW(),'2018' ),
	('horspdi',  '06 SIAE', NOW(), NOW(),'2018' ),
	('horspdi',  '07 ACTEURS DE L\'ENTREPRENEURIAT - ACCOMPAGNEMENT PORTEUR DE PROJET', NOW(), NOW(),'2018' ),
	('horspdi',  '08 AUTRES ACTEURS LOCAUX (hors PIE)', NOW(), NOW(),'2018' ),
	('horspdi',  '09 AUTRES', NOW(), NOW(),'2018' );
	
-- *****************************************************************************

INSERT INTO categoriesfps93 (thematiquefp93_id, name, created, modified) 
		VALUES 
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '01 POLE EMPLOI'), 'Accompagnement (droit commun renforcé...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '01 POLE EMPLOI'), 'Information / Sensibilisation (réunions, forum, ateliers...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '01 POLE EMPLOI'), 'Développement des compétences (formation, mise en situation pro...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '01 POLE EMPLOI'), 'Action de recrutement (forum, job dating...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '01 POLE EMPLOI'), 'Mobilisation d\'une aide financière' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '01 POLE EMPLOI'), 'Passerelles entreprises' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '01 POLE EMPLOI'), 'Autres' ,'NOW()','NOW()');
		
		
INSERT INTO categoriesfps93 (thematiquefp93_id, name, created, modified) 
		VALUES 
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '02 REGION / AFPA'), 'Accompagnement (droit commun renforcé...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '02 REGION / AFPA'), 'Information / Sensibilisation (réunions, forum, ateliers...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '02 REGION / AFPA'), 'Développement des compétences (formation, mise en situation pro...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '02 REGION / AFPA'), 'Action de recrutement (forum, job dating...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '02 REGION / AFPA'), 'Mobilisation d\'une aide financière' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '02 REGION / AFPA'), 'Passerelles entreprises' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '02 REGION / AFPA'), 'Autres' ,'NOW()','NOW()');
		
INSERT INTO categoriesfps93 (thematiquefp93_id, name, created, modified) 
		VALUES 
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '03 DEPARTEMENT'), 'Accompagnement (droit commun renforcé...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '03 DEPARTEMENT'), 'Information / Sensibilisation (réunions, forum, ateliers...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '03 DEPARTEMENT'), 'Développement des compétences (formation, mise en situation pro...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '03 DEPARTEMENT'), 'Action de recrutement (forum, job dating...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '03 DEPARTEMENT'), 'Mobilisation d\'une aide financière' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '03 DEPARTEMENT'), 'Passerelles entreprises' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '03 DEPARTEMENT'), 'Autres' ,'NOW()','NOW()');
		
INSERT INTO categoriesfps93 (thematiquefp93_id, name, created, modified) 
		VALUES 
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '04 PLIE'), 'Accompagnement (droit commun renforcé...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '04 PLIE'), 'Information / Sensibilisation (réunions, forum, ateliers...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '04 PLIE'), 'Développement des compétences (formation, mise en situation pro...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '04 PLIE'), 'Action de recrutement (forum, job dating...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '04 PLIE'), 'Mobilisation d\'une aide financière' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '04 PLIE'), 'Passerelles entreprises' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '04 PLIE'), 'Autres' ,'NOW()','NOW()');
		
INSERT INTO categoriesfps93 (thematiquefp93_id, name, created, modified) 
		VALUES 
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '05 MAISON EMPLOI, SERVICE EMPLOI ou EQUIVALENT'), 'Accompagnement (droit commun renforcé...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '05 MAISON EMPLOI, SERVICE EMPLOI ou EQUIVALENT'), 'Information / Sensibilisation (réunions, forum, ateliers...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '05 MAISON EMPLOI, SERVICE EMPLOI ou EQUIVALENT'), 'Développement des compétences (formation, mise en situation pro...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '05 MAISON EMPLOI, SERVICE EMPLOI ou EQUIVALENT'), 'Action de recrutement (forum, job dating...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '05 MAISON EMPLOI, SERVICE EMPLOI ou EQUIVALENT'), 'Mobilisation d\'une aide financière' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '05 MAISON EMPLOI, SERVICE EMPLOI ou EQUIVALENT'), 'Passerelles entreprises' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '05 MAISON EMPLOI, SERVICE EMPLOI ou EQUIVALENT'), 'Autres' ,'NOW()','NOW()');
		
INSERT INTO categoriesfps93 (thematiquefp93_id, name, created, modified) 
		VALUES 
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '06 SIAE'), 'Accompagnement (droit commun renforcé...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '06 SIAE'), 'Information / Sensibilisation (réunions, forum, ateliers...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '06 SIAE'), 'Développement des compétences (formation, mise en situation pro...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '06 SIAE'), 'Action de recrutement (forum, job dating...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '06 SIAE'), 'Mobilisation d\'une aide financière' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '06 SIAE'), 'Passerelles entreprises' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '06 SIAE'), 'Autres' ,'NOW()','NOW()');
				
INSERT INTO categoriesfps93 (thematiquefp93_id, name, created, modified) 
		VALUES 
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '07 ACTEURS DE L\'ENTREPRENEURIAT - ACCOMPAGNEMENT PORTEUR DE PROJET'), 'Accompagnement (droit commun renforcé...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '07 ACTEURS DE L\'ENTREPRENEURIAT - ACCOMPAGNEMENT PORTEUR DE PROJET'), 'Information / Sensibilisation (réunions, forum, ateliers...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '07 ACTEURS DE L\'ENTREPRENEURIAT - ACCOMPAGNEMENT PORTEUR DE PROJET'), 'Développement des compétences (formation, mise en situation pro...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '07 ACTEURS DE L\'ENTREPRENEURIAT - ACCOMPAGNEMENT PORTEUR DE PROJET'), 'Action de recrutement (forum, job dating...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '07 ACTEURS DE L\'ENTREPRENEURIAT - ACCOMPAGNEMENT PORTEUR DE PROJET'), 'Mobilisation d\'une aide financière' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '07 ACTEURS DE L\'ENTREPRENEURIAT - ACCOMPAGNEMENT PORTEUR DE PROJET'), 'Passerelles entreprises' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '07 ACTEURS DE L\'ENTREPRENEURIAT - ACCOMPAGNEMENT PORTEUR DE PROJET'), 'Autres' ,'NOW()','NOW()');
				
INSERT INTO categoriesfps93 (thematiquefp93_id, name, created, modified) 
		VALUES 
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '08 AUTRES ACTEURS LOCAUX (hors PIE)'), 'Accompagnement (droit commun renforcé...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '08 AUTRES ACTEURS LOCAUX (hors PIE)'), 'Information / Sensibilisation (réunions, forum, ateliers...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '08 AUTRES ACTEURS LOCAUX (hors PIE)'), 'Développement des compétences (formation, mise en situation pro...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '08 AUTRES ACTEURS LOCAUX (hors PIE)'), 'Action de recrutement (forum, job dating...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '08 AUTRES ACTEURS LOCAUX (hors PIE)'), 'Mobilisation d\'une aide financière' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '08 AUTRES ACTEURS LOCAUX (hors PIE)'), 'Passerelles entreprises' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '08 AUTRES ACTEURS LOCAUX (hors PIE)'), 'Autres' ,'NOW()','NOW()');
				
INSERT INTO categoriesfps93 (thematiquefp93_id, name, created, modified) 
		VALUES 
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '09 AUTRES'), 'Accompagnement (droit commun renforcé...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '09 AUTRES'), 'Information / Sensibilisation (réunions, forum, ateliers...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '09 AUTRES'), 'Développement des compétences (formation, mise en situation pro...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '09 AUTRES'), 'Action de recrutement (forum, job dating...)' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '09 AUTRES'), 'Mobilisation d\'une aide financière' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '09 AUTRES'), 'Passerelles entreprises' ,'NOW()','NOW()'),
		( (SELECT id FROM thematiquesfps93 WHERE type='horspdi' AND name LIKE '09 AUTRES'), 'Autres' ,'NOW()','NOW()');
		
	

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
		
		