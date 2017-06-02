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

/*INSERT INTO regroupementseps ( name ) VALUES
	( 'Équipe pluridisciplinaire locale, commission Parcours' ),
	( 'Équipe pluridisciplinaire départementale' ),
	( 'Équipe pluridisciplinaire locale, commission Audition' );


INSERT INTO eps ( name, identifiant, regroupementep_id, saisineepbilanparcours66, saisineepdpdo66 ) VALUES
	( 'EP locale Parcours, Perpignan 1', 'EPL1', 1, 'cg', 'nontraite' ),
	( 'EP départementale', 'EPD', 2, 'nontraite', 'cg' );

INSERT INTO eps ( name, identifiant, regroupementep_id, defautinsertionep66 ) VALUES
	( 'EP locale Audition, Perpignan 1', 'EPA1', 3, 'cg' );

INSERT INTO fonctionsmembreseps ( name ) VALUES
-- 	( 'Chef de projet de ville' ),
	( 'Représentant de Pôle Emploi' ),
	( 'Chargé d''insertion' );

INSERT INTO membreseps ( fonctionmembreep_id, qual, nom, prenom ) VALUES
	( 1, 'Mlle.', 'Dupont', 'Anne' ),
	( 1, 'M.', 'Martin', 'Pierre' ),
	( 2, 'M.', 'Dubois', 'Alphonse' ),
	( 2, 'Mme.', 'Roland', 'Adeline' );

INSERT INTO eps_membreseps ( ep_id, membreep_id ) VALUES
	( 1, 1 ),
	( 2, 1 ),
	( 3, 1 );

INSERT INTO eps_zonesgeographiques ( ep_id, zonegeographique_id )
--	VALUES
--	( 1, ( SELECT id FROM zonesgeographiques WHERE libelle LIKE 'PERPIGNAN  1%' ) ), -- Perpignan 1
--	( 3, ( SELECT id FROM zonesgeographiques WHERE libelle LIKE 'PERPIGNAN  1%' ) ); -- Perpignan 1
	SELECT 1 AS ep_id, id AS zonegeographique_id FROM zonesgeographiques
	UNION
	SELECT 2 AS ep_id, id AS zonegeographique_id FROM zonesgeographiques
	UNION
	SELECT 3 AS ep_id, id AS zonegeographique_id FROM zonesgeographiques;

INSERT INTO motifsreorients ( name ) VALUES
	( 'Motif réorientation 1' ),
	( 'Motif réorientation 2' );

-- SELECT pg_catalog.setval('seanceseps_id_seq', 1, true);
INSERT INTO seanceseps ( identifiant, name, ep_id, structurereferente_id, dateseance ) VALUES
	( 'COM1', 'Commission 1', 1, 22, '2010-10-28 10:00:00' ),
	( 'COM2', 'Commission 2', 2, 22, '2010-10-29 10:00:00' ),
	( 'COM3', 'Commission 3', 3, 22, '2010-10-30 10:00:00' );*/

-- =============================================================================

INSERT INTO regroupementseps (id, name) VALUES
	(1, 'Équipe pluridisciplinaire locale, commission Parcours'),
	(2, 'Équipe pluridisciplinaire départementale'),
	(3, 'Équipe pluridisciplinaire locale, commission Audition'),
	(4, 'EP MSP COTE VERMEILLE');

SELECT pg_catalog.setval('regroupementseps_id_seq', 5, true);

-- -----------------------------------------------------------------------------

INSERT INTO eps (id, name, identifiant, regroupementep_id, defautinsertionep66, saisineepbilanparcours66, saisineepdpdo66, nonrespectsanctionep93, saisineepreorientsr93) VALUES
	(1, 'EP locale Parcours, Perpignan 1', 'EP2011020000000006', 1, 'cg', 'cg', 'cg', 'nontraite', 'nontraite'),
	(2, 'EP départementale', 'EP2011020000000001', 2, 'nontraite', 'nontraite', 'cg', 'nontraite', 'nontraite'),
	(3, 'EP locale Audition, Perpignan 1', 'EP2011020000000002', 3, 'cg', 'nontraite', 'nontraite', 'nontraite', 'nontraite'),
	(4, 'EPL Parcours SUD ', 'EP2011020000000003 ', 1, 'cg', 'cg', 'cg', 'nontraite', 'nontraite'),
	(5, 'EP Locale Commission Parcours', 'EP2011020000000004', 4, 'cg', 'cg', 'cg', 'ep', 'ep'),
	(6, 'EP Locale Commission Audition', 'EP2011020000000005', 4, 'cg', 'cg', 'cg', 'ep', 'ep');

SELECT pg_catalog.setval('eps_id_seq', 6, true);

-- -----------------------------------------------------------------------------

INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (33, 2, 1);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (34, 2, 2);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (35, 2, 3);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (36, 2, 4);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (37, 2, 5);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (38, 2, 6);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (39, 2, 7);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (40, 2, 8);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (41, 2, 9);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (42, 2, 10);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (43, 2, 11);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (44, 2, 12);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (45, 2, 13);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (46, 2, 14);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (47, 2, 15);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (48, 2, 16);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (49, 2, 17);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (50, 2, 18);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (51, 2, 19);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (52, 2, 20);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (53, 2, 21);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (54, 2, 22);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (55, 2, 23);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (56, 2, 24);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (57, 2, 25);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (58, 2, 26);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (59, 2, 27);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (60, 2, 28);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (61, 2, 29);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (62, 2, 30);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (63, 2, 31);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (64, 2, 32);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (65, 3, 1);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (66, 3, 2);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (67, 3, 3);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (68, 3, 4);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (69, 3, 5);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (70, 3, 6);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (71, 3, 7);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (72, 3, 8);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (73, 3, 9);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (74, 3, 10);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (75, 3, 11);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (76, 3, 12);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (77, 3, 13);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (78, 3, 14);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (79, 3, 15);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (80, 3, 16);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (81, 3, 17);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (82, 3, 18);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (83, 3, 19);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (84, 3, 20);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (85, 3, 21);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (86, 3, 22);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (87, 3, 23);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (88, 3, 24);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (89, 3, 25);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (90, 3, 26);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (91, 3, 27);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (92, 3, 28);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (93, 3, 29);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (94, 3, 30);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (95, 3, 31);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (96, 3, 32);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (111, 4, 9);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (112, 4, 20);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (113, 4, 21);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (114, 4, 22);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (115, 4, 23);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (116, 4, 25);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (117, 4, 29);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (122, 5, 1);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (123, 5, 27);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (124, 5, 19);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (125, 5, 28);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (126, 6, 1);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (127, 6, 27);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (128, 6, 19);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (129, 1, 1);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (130, 1, 2);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (131, 1, 31);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (132, 1, 3);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (133, 1, 27);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (134, 1, 19);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (135, 1, 28);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (136, 1, 32);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (137, 1, 4);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (138, 1, 5);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (139, 1, 6);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (140, 1, 7);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (141, 1, 8);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (142, 1, 9);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (143, 1, 20);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (144, 1, 21);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (145, 1, 22);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (146, 1, 23);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (147, 1, 24);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (148, 1, 25);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (149, 1, 26);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (150, 1, 10);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (151, 1, 11);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (152, 1, 12);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (153, 1, 13);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (154, 1, 30);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (155, 1, 14);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (156, 1, 15);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (157, 1, 16);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (158, 1, 17);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (159, 1, 29);
INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES (160, 1, 18);

SELECT pg_catalog.setval('eps_zonesgeographiques_id_seq', 160, true);

-- -----------------------------------------------------------------------------

INSERT INTO fonctionsmembreseps (id, name) VALUES (1, 'Représentant de Pôle Emploi');
INSERT INTO fonctionsmembreseps (id, name) VALUES (2, 'Chargé d''insertion');
INSERT INTO fonctionsmembreseps (id, name) VALUES (3, 'Représentant des usagers');
INSERT INTO fonctionsmembreseps (id, name) VALUES (4, 'redacteur');
INSERT INTO fonctionsmembreseps (id, name) VALUES (5, 'Responsable MSP');

SELECT pg_catalog.setval('fonctionsmembreseps_id_seq', 5, true);

-- -----------------------------------------------------------------------------

INSERT INTO membreseps (id, fonctionmembreep_id, qual, nom, prenom, tel, mail, suppleant_id) VALUES (1, 1, 'Mlle.', 'Dupont', 'Anne', NULL, NULL, NULL);
INSERT INTO membreseps (id, fonctionmembreep_id, qual, nom, prenom, tel, mail, suppleant_id) VALUES (2, 1, 'M.', 'Martin', 'Pierre', NULL, NULL, NULL);
INSERT INTO membreseps (id, fonctionmembreep_id, qual, nom, prenom, tel, mail, suppleant_id) VALUES (3, 2, 'M.', 'Dubois', 'Alphonse', NULL, NULL, NULL);
INSERT INTO membreseps (id, fonctionmembreep_id, qual, nom, prenom, tel, mail, suppleant_id) VALUES (4, 2, 'Mme.', 'Roland', 'Adeline', NULL, NULL, NULL);
INSERT INTO membreseps (id, fonctionmembreep_id, qual, nom, prenom, tel, mail, suppleant_id) VALUES (5, 2, 'Mme.', 'Bouloc', 'Régine', '0466666666', 'rbouloc@test66.fr', NULL);

SELECT pg_catalog.setval('membreseps_id_seq', 5, true);

-- -----------------------------------------------------------------------------

INSERT INTO eps_membreseps (id, ep_id, membreep_id) VALUES (1, 1, 1);
INSERT INTO eps_membreseps (id, ep_id, membreep_id) VALUES (2, 2, 1);
INSERT INTO eps_membreseps (id, ep_id, membreep_id) VALUES (3, 3, 1);
INSERT INTO eps_membreseps (id, ep_id, membreep_id) VALUES (4, 4, 1);
INSERT INTO eps_membreseps (id, ep_id, membreep_id) VALUES (5, 4, 2);
INSERT INTO eps_membreseps (id, ep_id, membreep_id) VALUES (6, 4, 4);

SELECT pg_catalog.setval('eps_membreseps_id_seq', 6, true);

-- -----------------------------------------------------------------------------

INSERT INTO motifsreorients (id, name) VALUES (1, 'Motif réorientation 1');
INSERT INTO motifsreorients (id, name) VALUES (2, 'Motif réorientation 2');

SELECT pg_catalog.setval('motifsreorients_id_seq', 2, true);

-- -----------------------------------------------------------------------------

INSERT INTO seanceseps (id, identifiant, name, ep_id, lieuseance, adresseseance, codepostalseance, villeseance, dateseance, salle, observations, finalisee) VALUES
	(1, 'CO2011020000000001', 'Commission 1', 1, 'MSA','23 R FRANCOIS BROUSSAIS','66017','PERPIGNAN', '2010-10-28 10:00:00', NULL, NULL, NULL),
	(2, 'CO2011020000000002', 'Commission 2', 2, 'MSA','23 R FRANCOIS BROUSSAIS','66017','PERPIGNAN', '2010-10-29 10:00:00', NULL, NULL, NULL),
	(3, 'CO2011020000000003', 'Commission 3', 3, 'MSA','23 R FRANCOIS BROUSSAIS','66017','PERPIGNAN', '2010-10-30 10:00:00', NULL, NULL, NULL),
	(4, 'CO2011020000000004', 'Test Adullact EPL Audition', 3, 'MSA','23 R FRANCOIS BROUSSAIS','66017','PERPIGNAN', '2031-01-01 00:00:00', NULL, NULL, NULL),
	(5, 'CO2011020000000005', 'Pourquoi', 1, 'MSA','23 R FRANCOIS BROUSSAIS','66017','PERPIGNAN', '2011-01-20 14:00:00', 'sdfsfs', 'sdfsdf', NULL),
	(6, 'CO2011020000000006', 'dsfsdf', 4, 'MSP COTE VERMEILLE PréPro','2 BD EDOUARD HERRIOT','66700','ARGELES SUR MER', '2011-02-19 09:00:00', 'dfsdf', 'sdfsdf', NULL),
	(7, 'CO2011020000000007', 'sdfdsf', 4, 'MSP COTE VERMEILLE PréPro','2 BD EDOUARD HERRIOT','66700','ARGELES SUR MER', '2011-01-19 18:00:00', 'dsf', 'dsf', NULL),
	(8, 'CO2011020000000008', 'PERPIGNAN SUD', 3, 'MSP COTE VERMEILLE PréPro','2 BD EDOUARD HERRIOT','66700','ARGELES SUR MER', '2010-01-27 09:00:00', 'CANIGOU', 'PAZERUOPZEIUZOPERIAZOP', NULL),
	(9, 'CO2011020000000009', 'pozeriuazp', 3, 'MSP PERPIGNAN SUD PréPro','32 AV MARECHAL FOCH','66906','PERPIGNAN', '2011-01-27 10:00:00', 'canigou', NULL, NULL),
	(10, 'CO2011020000000010', 'dfg', 4, 'MSP COTE VERMEILLE PréPro','2 BD EDOUARD HERRIOT','66700','ARGELES SUR MER', '2031-02-01 02:00:00', 'sfvsdfsdf', 'sdfsdf', NULL),
	(11, 'CO2011020000000011', 'rbu', 1, 'MSP COTE VERMEILLE Social','2 BD EDOUARD HERRIOT','66700','ARGELES SUR MER', '2011-01-27 09:00:00', 'canigou', NULL, NULL),
	(12, 'CO2011020000000012', 'rbu', 1, 'MSP COTE VERMEILLE Social','2 BD EDOUARD HERRIOT','66700','ARGELES SUR MER', '2011-02-19 09:00:00', 'canigou', NULL, NULL),
	(13, 'CO2011020000000013', 'rbu', 3, 'MSP COTE VERMEILLE Social','2 BD EDOUARD HERRIOT','66700','ARGELES SUR MER', '2011-02-20 09:00:00', 'Canigou', 'aozeuaopruazop', NULL);

SELECT pg_catalog.setval('seanceseps_id_seq', 13, true);

-- -----------------------------------------------------------------------------

INSERT INTO membreseps_seanceseps (id, seanceep_id, membreep_id, suppleant, suppleant_id, reponse, presence) VALUES (1, 4, 1, '0', NULL, 'confirme', NULL);
INSERT INTO membreseps_seanceseps (id, seanceep_id, membreep_id, suppleant, suppleant_id, reponse, presence) VALUES (2, 8, 1, '0', NULL, 'nonrenseigne', NULL);
INSERT INTO membreseps_seanceseps (id, seanceep_id, membreep_id, suppleant, suppleant_id, reponse, presence) VALUES (3, 10, 1, '0', NULL, 'confirme', NULL);
INSERT INTO membreseps_seanceseps (id, seanceep_id, membreep_id, suppleant, suppleant_id, reponse, presence) VALUES (4, 10, 2, '0', NULL, 'confirme', NULL);
INSERT INTO membreseps_seanceseps (id, seanceep_id, membreep_id, suppleant, suppleant_id, reponse, presence) VALUES (5, 10, 4, '0', NULL, 'confirme', NULL);
INSERT INTO membreseps_seanceseps (id, seanceep_id, membreep_id, suppleant, suppleant_id, reponse, presence) VALUES (6, 11, 1, '0', NULL, 'confirme', NULL);
INSERT INTO membreseps_seanceseps (id, seanceep_id, membreep_id, suppleant, suppleant_id, reponse, presence) VALUES (7, 13, 1, '0', NULL, 'confirme', NULL);

SELECT pg_catalog.setval('membreseps_seanceseps_id_seq', 7, true);

-- =============================================================================

TRUNCATE situationspdos CASCADE;
SELECT pg_catalog.setval('situationspdos_id_seq', ( SELECT COALESCE( max(situationspdos.id) + 1, 1 ) FROM situationspdos ), false);
INSERT INTO situationspdos (libelle) VALUES
	('Evaluation revenus non salariés'),
	('Evaluation revenus de capitaux placés ou non'),
	('Evaluation de revenus de capitaux mobiliers'),
	('Démission, disponibilité, congé sans solde'),
	('Eléments non déclarés'),
	('Refus de ctrl'),
	('Dispense pension alimentaire'),
	('Parcours scolaire'),
	('Parcours de stage'),
	('Neutralisation'),
	('Droit au déjour EEE'),
	('Droit au déjour Hors EEE'),
	('Révision du droit'),
	('Défaut de conclusion contrat'),
	('Non respect du contrat'),
	('Radiation list Pôle Emploi'),
	('Dérogation'),
	('Subsidiarité')
;

TRUNCATE statutspdos CASCADE;
SELECT pg_catalog.setval('statutspdos_id_seq', ( SELECT COALESCE( max(statutspdos.id) + 1, 1 ) FROM statutspdos ), false);
INSERT INTO statutspdos (libelle) VALUES
	('TI'),
	('Ex TI'),
	('Exploitant agricole'),
	('Ex exploitant agricole'),
	('Auto-entrepreneur'),
	('Ex auto-entrepreneur'),
	('Gérant de Sté'),
	('Ex gérant de Sté'),
	('EEE'),
	('Hors EEE'),
	('Salarié'),
	('Ex salarié'),
	('Chômeur indemnisé'),
	('Chômeur non indemnisé'),
	('Etudiant'),
	('Stagiaire non rémunéré'),
	('Stagiaire rémunéré')
;

TRUNCATE descriptionspdos CASCADE;
SELECT pg_catalog.setval('descriptionspdos_id_seq', ( SELECT COALESCE( max(descriptionspdos.id) + 1, 1 ) FROM descriptionspdos ), false);
INSERT INTO descriptionspdos (name, dateactive, declencheep) VALUES
	('Courrier à l''allocataire', 'datedepart', '0'),
	('Pièces arrivées', 'datereception', '0'),
	('Courrier Révision de ressources', 'datedepart', '0'),
	('Enquête administrative demandée', 'datedepart', '0'),
	('Enquête administrative reçue', 'datereception', '0'),
	('Saisine EP Dépt', 'datedepart', '1')
;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************