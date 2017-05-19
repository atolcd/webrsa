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

INSERT INTO regroupementseps ( id, name ) VALUES
	(1, 'CLI 1'),
	(2, 'CLI 2'),
	(5, 'CLI 3'),
	(6, 'CLI 4'),
	(7, 'CLI 5'),
	(8, 'CLI 6');

SELECT pg_catalog.setval('regroupementseps_id_seq', 8, true);

--

INSERT INTO eps (id, name, identifiant, regroupementep_id, defautinsertionep66, saisineepbilanparcours66, saisineepdpdo66, nonrespectsanctionep93, saisineepreorientsr93) VALUES
	(2, 'CLI 1 Equipe 1.2', 'EP2011020000000001', 1, 'nontraite', 'nontraite', 'nontraite', 'cg', 'cg'),
	(3, 'CLI 2 Equipe 2.1', 'EP2011020000000002', 2, 'nontraite', 'nontraite', 'nontraite', 'cg', 'cg'),
	(4, 'CLI 2 Equipe 2.2', 'EP2011020000000003', 2, 'nontraite', 'nontraite', 'nontraite', 'cg', 'cg'),
	(5, 'CLI 3 Equipe 3.1', 'EP2011020000000004', 5, 'nontraite', 'nontraite', 'nontraite', 'cg', 'cg'),
	(6, 'CLI 3 Equipe 3.2', 'EP2011020000000005', 5, 'nontraite', 'nontraite', 'nontraite', 'cg', 'cg'),
	(7, 'CLI 4 Equipe 4.1', 'EP2011020000000006', 6, 'nontraite', 'nontraite', 'nontraite', 'cg', 'cg'),
	(8, 'CLI 4 Equipe 4.2', 'EP2011020000000007', 6, 'nontraite', 'nontraite', 'nontraite', 'cg', 'cg'),
	(9, 'CLI 5 Equipe 5.1', 'EP2011020000000008', 7, 'nontraite', 'nontraite', 'nontraite', 'cg', 'cg'),
	(10, 'CLI 5 Equipe 5.2', 'EP2011020000000009', 7, 'nontraite', 'nontraite', 'nontraite', 'cg', 'cg'),
	(11, 'CLI 6 Equipe 6.1', 'EP2011020000000010', 8, 'nontraite', 'nontraite', 'nontraite', 'cg', 'cg'),
	(1, 'CLI 1, équipe 1.1', 'EP2011020000000011', 1, 'nontraite', 'nontraite', 'nontraite', 'cg', 'cg'),
	(12, 'CLI 6 Equipe 6.2', 'EP2011020000000012', 8, 'nontraite', 'nontraite', 'nontraite', 'cg', 'cg');


SELECT pg_catalog.setval('eps_id_seq', 12, true);

--

INSERT INTO fonctionsmembreseps (id, name) VALUES
	(1, 'Chef de projet de ville'),
	(2, 'Représentant de Pôle Emploi'),
	(4, 'Représentant du Département'),
	(5, 'Représentant du CCAS'),
	(6, 'Représentant du Service Social'),
	(7, 'Représentant du bénéficiaire du RSA');

SELECT pg_catalog.setval('fonctionsmembreseps_id_seq', 7, true);

--

INSERT INTO membreseps (id, fonctionmembreep_id, qual, nom, prenom, tel, mail, suppleant_id) VALUES
	(1, 1, 'Mlle.', 'Dupont', 'Anne', NULL, NULL, NULL),
	(2, 1, 'M.', 'Martin', 'Pierre', NULL, NULL, NULL),
	(3, 2, 'M.', 'Dubois', 'Alphonse', NULL, NULL, NULL),
	(4, 2, 'Mme.', 'Roland', 'Adeline', NULL, NULL, NULL),
	(6, 2, 'Mlle.', 'ZZZZZZZZZZZZZZZZZZZZZ', 'ZZZZZZZZZZZZZZZZZZZZ', '', '', NULL),
	(5, 6, 'M.', 'AAAAAAAAAAAAAAAAAAAA', 'AAAAAAAAAAAAAAAAAAAA', '0111111110', '', NULL);

SELECT pg_catalog.setval('membreseps_id_seq', 6, true);

--

INSERT INTO eps_membreseps (id, ep_id, membreep_id) VALUES
	(1, 1, 1),
	(2, 1, 2),
	(3, 7, 2),
	(4, 2, 1),
	(5, 12, 2),
	(6, 12, 6),
	(7, 2, 3);

SELECT pg_catalog.setval('eps_membreseps_id_seq', 7, true);

--

/*INSERT INTO eps_zonesgeographiques ( ep_id, zonegeographique_id ) VALUES
	( 1, 14 ), -- EPINAY-SUR-SEINE
	( 1, 31 ), -- PIERREFITTE-SUR-SEINE
	( 1, 36 ); -- SAINT-OUEN
-- 	SELECT 1 AS ep_id, id AS zonegeographique_id FROM zonesgeographiques;*/

INSERT INTO eps_zonesgeographiques (id, ep_id, zonegeographique_id) VALUES
	(47, 2, 17),
	(48, 2, 35),
	(49, 2, 43),
	(50, 3, 4),
	(51, 3, 18),
	(52, 4, 12),
	(53, 4, 13),
	(54, 4, 19),
	(55, 4, 38),
	(56, 5, 6),
	(57, 5, 28),
	(58, 5, 33),
	(59, 6, 32),
	(60, 6, 21),
	(61, 6, 24),
	(62, 7, 5),
	(63, 7, 7),
	(64, 8, 22),
	(65, 8, 37),
	(66, 8, 39),
	(67, 8, 40),
	(68, 8, 42),
	(69, 9, 11),
	(70, 9, 8),
	(71, 9, 15),
	(72, 9, 23),
	(73, 10, 16),
	(74, 10, 25),
	(75, 10, 26),
	(76, 10, 27),
	(77, 11, 9),
	(78, 11, 29),
	(84, 1, 14),
	(85, 1, 31),
	(86, 1, 36),
	(87, 12, 10),
	(88, 12, 20),
	(89, 12, 30),
	(90, 12, 34),
	(91, 12, 41);

SELECT pg_catalog.setval('eps_zonesgeographiques_id_seq', 91, true);

--


INSERT INTO motifsreorients ( name ) VALUES
	( 'Motif réorientation 1' ),
	( 'Motif réorientation 2' );

SELECT pg_catalog.setval('motifsreorients_id_seq', 2, true);

--

-- SELECT pg_catalog.setval('seanceseps_id_seq', 1, true);
-- INSERT INTO seanceseps VALUES ( 1, 'COM1', 'Commission 1', 1, 104, '2010-10-28 10:00:00', NULL );
/*INSERT INTO seanceseps ( identifiant, name, ep_id, structurereferente_id, dateseance ) VALUES
	( 'COM1', 'Commission 1', 1, 104, '2010-10-28 10:00:00' );*/

INSERT INTO seanceseps (id, identifiant, name, ep_id, structurereferente_id, dateseance, salle, observations, finalisee) VALUES
	(3, 'CO2011020000000003', 'tert', 2, 74, '2031-01-01 00:00:00', 'trert', NULL, NULL),
	(4, 'CO2011020000000004', 'p', 1, 74, '2024-01-01 00:00:00', 'trert', 'pppp', NULL),
	(5, 'CO2011020000000005', 'TEST DU 26 01', 2, 91, '2011-01-26 12:00:00', 'DFGDFGDFG', NULL, NULL),
	(7, 'CO2011020000000007', 'ERRRRRRRRRRRRRRRRRRRR', 8, 97, '2031-01-01 00:00:00', '111111111111111111', NULL, NULL),
	(1, 'CO2011020000000001', 'Commission 1', 1, 104, '2010-10-28 10:00:00', NULL, NULL, NULL),
	(6, 'CO2011020000000006', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 2, 67, '2017-01-01 00:00:00', NULL, NULL, NULL),
	(8, 'CO2011020000000008', 'TEST DU 27 01 2011', 12, 90, '2011-01-27 14:00:00', '111', NULL, NULL),
	(2, 'CO2011020000000002', 'Intitulé', 1, 74, '2010-01-01 03:15:00', NULL, NULL, NULL),
	(9, 'CO2011020000000009', 'df', 1, 86, '2011-01-31 00:00:00', NULL, NULL, NULL);

SELECT pg_catalog.setval('seanceseps_id_seq', 9, true);

--

INSERT INTO membreseps_seanceseps (id, seanceep_id, membreep_id, suppleant, suppleant_id, reponse, presence) VALUES
	(1, 2, 1, '0', NULL, 'confirme', NULL),
	(2, 2, 2, '0', NULL, 'decline', NULL),
	(3, 1, 1, '0', NULL, 'confirme', NULL),
	(4, 1, 2, '0', NULL, 'nonrenseigne', NULL),
	(5, 4, 1, '0', NULL, 'decline', NULL),
	(6, 4, 2, '0', NULL, 'confirme', NULL),
	(7, 5, 2, '0', NULL, 'confirme', NULL),
	(8, 6, 1, '0', NULL, 'confirme', NULL),
	(9, 8, 2, '0', NULL, 'nonrenseigne', NULL),
	(10, 8, 6, '0', NULL, 'nonrenseigne', NULL),
	(11, 9, 1, '0', NULL, 'decline', NULL),
	(12, 9, 2, '0', NULL, 'confirme', NULL);

SELECT pg_catalog.setval('membreseps_seanceseps_id_seq', 12, true);

--

TRUNCATE situationspdos CASCADE;
SELECT pg_catalog.setval('situationspdos_id_seq', ( SELECT COALESCE( max(situationspdos.id) + 1, 1 ) FROM situationspdos ), false);
INSERT INTO situationspdos (libelle) VALUES
	('Evaluation revenus non salariés')
;

TRUNCATE statutspdos CASCADE;
SELECT pg_catalog.setval('statutspdos_id_seq', ( SELECT COALESCE( max(statutspdos.id) + 1, 1 ) FROM statutspdos ), false);
INSERT INTO statutspdos (libelle) VALUES
	('TI')
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
