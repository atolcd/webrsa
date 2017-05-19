--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- Name: accoemplois_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('accoemplois_id_seq', 4, false);


--
-- Name: acos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('acos_id_seq', 296, true);


--
-- Name: actions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('actions_id_seq', 34, false);


--
-- Name: actionsinsertion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('actionsinsertion_id_seq', 1, false);


--
-- Name: activites_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('activites_id_seq', 1, false);


--
-- Name: adresses_foyers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('adresses_foyers_id_seq', 1, false);


--
-- Name: adresses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('adresses_id_seq', 1, false);


--
-- Name: aidesagricoles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('aidesagricoles_id_seq', 1, false);


--
-- Name: aidesdirectes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('aidesdirectes_id_seq', 1, false);


--
-- Name: allocationssoutienfamilial_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('allocationssoutienfamilial_id_seq', 1, false);


--
-- Name: aros_acos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('aros_acos_id_seq', 156, true);


--
-- Name: aros_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('aros_id_seq', 20, true);


--
-- Name: avispcgdroitrsa_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('avispcgdroitrsa_id_seq', 1, false);


--
-- Name: avispcgpersonnes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('avispcgpersonnes_id_seq', 1, false);


--
-- Name: condsadmins_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('condsadmins_id_seq', 1, false);


--
-- Name: connections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('connections_id_seq', 3, false);


--
-- Name: contratsinsertion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('contratsinsertion_id_seq', 1, false);


--
-- Name: creances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('creances_id_seq', 1, false);


--
-- Name: creancesalimentaires_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('creancesalimentaires_id_seq', 1, false);


--
-- Name: derogations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('derogations_id_seq', 1, false);


--
-- Name: detailscalculsdroitsrsa_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('detailscalculsdroitsrsa_id_seq', 1, false);


--
-- Name: detailsdroitsrsa_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('detailsdroitsrsa_id_seq', 1, false);


--
-- Name: detailsressourcesmensuelles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('detailsressourcesmensuelles_id_seq', 1, false);


--
-- Name: difdisps_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('difdisps_id_seq', 6, false);


--
-- Name: diflogs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('diflogs_id_seq', 10, false);


--
-- Name: difsocs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('difsocs_id_seq', 8, false);


--
-- Name: dossiers_rsa_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('dossiers_rsa_id_seq', 1, false);


--
-- Name: dossierscaf_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('dossierscaf_id_seq', 1, false);


--
-- Name: dspfs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('dspfs_id_seq', 1, false);


--
-- Name: dspps_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('dspps_id_seq', 1, false);


--
-- Name: evenements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('evenements_id_seq', 1, false);


--
-- Name: foyers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('foyers_id_seq', 1, false);


--
-- Name: grossesses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('grossesses_id_seq', 1, false);


--
-- Name: groups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('groups_id_seq', 4, false);


--
-- Name: identificationsflux_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('identificationsflux_id_seq', 1, false);


--
-- Name: informationseti_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('informationseti_id_seq', 1, false);


--
-- Name: infosagricoles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('infosagricoles_id_seq', 1, false);


--
-- Name: infosfinancieres_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('infosfinancieres_id_seq', 1, false);


--
-- Name: jetons_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('jetons_id_seq', 1, false);


--
-- Name: liberalites_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('liberalites_id_seq', 1, false);


--
-- Name: modescontact_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('modescontact_id_seq', 1, false);


--
-- Name: nataccosocfams_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('nataccosocfams_id_seq', 5, false);


--
-- Name: nataccosocindis_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('nataccosocindis_id_seq', 7, false);


--
-- Name: natmobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('natmobs_id_seq', 4, false);


--
-- Name: nivetus_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('nivetus_id_seq', 8, false);


--
-- Name: orientsstructs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('orientsstructs_id_seq', 1, false);


--
-- Name: paiementsfoyers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('paiementsfoyers_id_seq', 1, false);


--
-- Name: personnes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('personnes_id_seq', 1, false);


--
-- Name: prestations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('prestations_id_seq', 1, false);


--
-- Name: prestsform_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('prestsform_id_seq', 1, false);


--
-- Name: reducsrsa_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('reducsrsa_id_seq', 1, false);


--
-- Name: referents_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('referents_id_seq', 1, false);


--
-- Name: refsprestas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('refsprestas_id_seq', 1, false);


--
-- Name: regroupementszonesgeo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('regroupementszonesgeo_id_seq', 1, false);


--
-- Name: ressources_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('ressources_id_seq', 1, false);


--
-- Name: ressourcesmensuelles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('ressourcesmensuelles_id_seq', 1, false);


--
-- Name: servicesinstructeurs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('servicesinstructeurs_id_seq', 3, false);


--
-- Name: situationsdossiersrsa_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('situationsdossiersrsa_id_seq', 1, false);


--
-- Name: structuresreferentes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('structuresreferentes_id_seq', 6, false);


--
-- Name: suivisinstruction_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('suivisinstruction_id_seq', 1, false);


--
-- Name: suspensionsdroits_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('suspensionsdroits_id_seq', 1, false);


--
-- Name: suspensionsversements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('suspensionsversements_id_seq', 1, false);


--
-- Name: titres_sejour_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('titres_sejour_id_seq', 1, false);


--
-- Name: totalisationsacomptes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('totalisationsacomptes_id_seq', 1, false);


--
-- Name: typesactions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('typesactions_id_seq', 6, false);


--
-- Name: typesorients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('typesorients_id_seq', 4, false);


--
-- Name: typoscontrats_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('typoscontrats_id_seq', 4, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('users_id_seq', 8, true);


--
-- Name: users_zonesgeographiques_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('users_zonesgeographiques_id_seq', 21, true);


--
-- Name: zonesgeographiques_id_seq; Type: SEQUENCE SET; Schema: public; Owner: webrsa
--

SELECT pg_catalog.setval('zonesgeographiques_id_seq', 4, false);


--
-- Data for Name: accoemplois; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO accoemplois VALUES (1, '1801', 'Pas d''accompagnement');
INSERT INTO accoemplois VALUES (2, '1802', 'Pole emploi');
INSERT INTO accoemplois VALUES (3, '1803', 'Autres');


--
-- Data for Name: acos; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO acos VALUES (149, 0, '', 0, 'Dossiers:index', 1, 2);
INSERT INTO acos VALUES (189, 187, '', 0, 'Personnes:view', 80, 81);
INSERT INTO acos VALUES (151, 150, '', 0, 'Situationsdossiersrsa:index', 4, 5);
INSERT INTO acos VALUES (150, 0, '', 0, 'Situationsdossiersrsa', 3, 8);
INSERT INTO acos VALUES (152, 150, '', 0, 'Situationsdossiersrsa:view', 6, 7);
INSERT INTO acos VALUES (154, 153, '', 0, 'Referents:index', 10, 11);
INSERT INTO acos VALUES (155, 153, '', 0, 'Referents:add', 12, 13);
INSERT INTO acos VALUES (190, 187, '', 0, 'Personnes:add', 82, 83);
INSERT INTO acos VALUES (156, 153, '', 0, 'Referents:edit', 14, 15);
INSERT INTO acos VALUES (153, 0, '', 0, 'Referents', 9, 18);
INSERT INTO acos VALUES (157, 153, '', 0, 'Referents:delete', 16, 17);
INSERT INTO acos VALUES (159, 158, '', 0, 'Contratsinsertion:index', 20, 21);
INSERT INTO acos VALUES (187, 0, '', 0, 'Personnes', 77, 86);
INSERT INTO acos VALUES (160, 158, '', 0, 'Contratsinsertion:test2', 22, 23);
INSERT INTO acos VALUES (191, 187, '', 0, 'Personnes:edit', 84, 85);
INSERT INTO acos VALUES (161, 158, '', 0, 'Contratsinsertion:view', 24, 25);
INSERT INTO acos VALUES (162, 158, '', 0, 'Contratsinsertion:add', 26, 27);
INSERT INTO acos VALUES (163, 158, '', 0, 'Contratsinsertion:edit', 28, 29);
INSERT INTO acos VALUES (158, 0, '', 0, 'Contratsinsertion', 19, 32);
INSERT INTO acos VALUES (164, 158, '', 0, 'Contratsinsertion:valider', 30, 31);
INSERT INTO acos VALUES (210, 0, '', 0, 'Parametrages', 123, 130);
INSERT INTO acos VALUES (166, 165, '', 0, 'Orientsstructs:index', 34, 35);
INSERT INTO acos VALUES (193, 192, '', 0, 'Servicesinstructeurs:index', 88, 89);
INSERT INTO acos VALUES (167, 165, '', 0, 'Orientsstructs:add', 36, 37);
INSERT INTO acos VALUES (165, 0, '', 0, 'Orientsstructs', 33, 40);
INSERT INTO acos VALUES (168, 165, '', 0, 'Orientsstructs:edit', 38, 39);
INSERT INTO acos VALUES (170, 169, '', 0, 'Zonesgeographiques:index', 42, 43);
INSERT INTO acos VALUES (213, 210, '', 0, 'Parametrages:edit', 128, 129);
INSERT INTO acos VALUES (171, 169, '', 0, 'Zonesgeographiques:add', 44, 45);
INSERT INTO acos VALUES (194, 192, '', 0, 'Servicesinstructeurs:add', 90, 91);
INSERT INTO acos VALUES (172, 169, '', 0, 'Zonesgeographiques:edit', 46, 47);
INSERT INTO acos VALUES (169, 0, '', 0, 'Zonesgeographiques', 41, 50);
INSERT INTO acos VALUES (173, 169, '', 0, 'Zonesgeographiques:delete', 48, 49);
INSERT INTO acos VALUES (175, 174, '', 0, 'Ajoutdossiers:confirm', 52, 53);
INSERT INTO acos VALUES (174, 0, '', 0, 'Ajoutdossiers', 51, 56);
INSERT INTO acos VALUES (176, 174, '', 0, 'Ajoutdossiers:wizard', 54, 55);
INSERT INTO acos VALUES (178, 177, '', 0, 'Structuresreferentes:index', 58, 59);
INSERT INTO acos VALUES (195, 192, '', 0, 'Servicesinstructeurs:edit', 92, 93);
INSERT INTO acos VALUES (179, 177, '', 0, 'Structuresreferentes:add', 60, 61);
INSERT INTO acos VALUES (180, 177, '', 0, 'Structuresreferentes:edit', 62, 63);
INSERT INTO acos VALUES (177, 0, '', 0, 'Structuresreferentes', 57, 66);
INSERT INTO acos VALUES (181, 177, '', 0, 'Structuresreferentes:delete', 64, 65);
INSERT INTO acos VALUES (192, 0, '', 0, 'Servicesinstructeurs', 87, 96);
INSERT INTO acos VALUES (183, 182, '', 0, 'Groups:index', 68, 69);
INSERT INTO acos VALUES (196, 192, '', 0, 'Servicesinstructeurs:delete', 94, 95);
INSERT INTO acos VALUES (184, 182, '', 0, 'Groups:add', 70, 71);
INSERT INTO acos VALUES (185, 182, '', 0, 'Groups:edit', 72, 73);
INSERT INTO acos VALUES (182, 0, '', 0, 'Groups', 67, 76);
INSERT INTO acos VALUES (186, 182, '', 0, 'Groups:delete', 74, 75);
INSERT INTO acos VALUES (188, 187, '', 0, 'Personnes:index', 78, 79);
INSERT INTO acos VALUES (198, 197, '', 0, 'Aidesdirectes:add', 98, 99);
INSERT INTO acos VALUES (197, 0, '', 0, 'Aidesdirectes', 97, 102);
INSERT INTO acos VALUES (199, 197, '', 0, 'Aidesdirectes:edit', 100, 101);
INSERT INTO acos VALUES (215, 214, '', 0, 'Infosagricoles:index', 132, 133);
INSERT INTO acos VALUES (201, 200, '', 0, 'Detailsdroitsrsa:index', 104, 105);
INSERT INTO acos VALUES (200, 0, '', 0, 'Detailsdroitsrsa', 103, 108);
INSERT INTO acos VALUES (202, 200, '', 0, 'Detailsdroitsrsa:view', 106, 107);
INSERT INTO acos VALUES (204, 203, '', 0, 'Informationseti:index', 110, 111);
INSERT INTO acos VALUES (203, 0, '', 0, 'Informationseti', 109, 114);
INSERT INTO acos VALUES (205, 203, '', 0, 'Informationseti:view', 112, 113);
INSERT INTO acos VALUES (214, 0, '', 0, 'Infosagricoles', 131, 136);
INSERT INTO acos VALUES (207, 206, '', 0, 'Dossierssimplifies:view', 116, 117);
INSERT INTO acos VALUES (216, 214, '', 0, 'Infosagricoles:view', 134, 135);
INSERT INTO acos VALUES (208, 206, '', 0, 'Dossierssimplifies:add', 118, 119);
INSERT INTO acos VALUES (206, 0, '', 0, 'Dossierssimplifies', 115, 122);
INSERT INTO acos VALUES (209, 206, '', 0, 'Dossierssimplifies:edit', 120, 121);
INSERT INTO acos VALUES (211, 210, '', 0, 'Parametrages:index', 124, 125);
INSERT INTO acos VALUES (212, 210, '', 0, 'Parametrages:view', 126, 127);
INSERT INTO acos VALUES (217, 0, '', 0, 'Totalisationsacomptes', 137, 140);
INSERT INTO acos VALUES (218, 217, '', 0, 'Totalisationsacomptes:index', 138, 139);
INSERT INTO acos VALUES (227, 0, '', 0, 'Droits', 157, 160);
INSERT INTO acos VALUES (220, 219, '', 0, 'Modescontact:index', 142, 143);
INSERT INTO acos VALUES (228, 227, '', 0, 'Droits:edit', 158, 159);
INSERT INTO acos VALUES (221, 219, '', 0, 'Modescontact:add', 144, 145);
INSERT INTO acos VALUES (222, 219, '', 0, 'Modescontact:edit', 146, 147);
INSERT INTO acos VALUES (219, 0, '', 0, 'Modescontact', 141, 150);
INSERT INTO acos VALUES (223, 219, '', 0, 'Modescontact:view', 148, 149);
INSERT INTO acos VALUES (225, 224, '', 0, 'Actionsinsertion:index', 152, 153);
INSERT INTO acos VALUES (224, 0, '', 0, 'Actionsinsertion', 151, 156);
INSERT INTO acos VALUES (226, 224, '', 0, 'Actionsinsertion:edit', 154, 155);
INSERT INTO acos VALUES (229, 0, '', 0, 'Criteres', 161, 164);
INSERT INTO acos VALUES (230, 229, '', 0, 'Criteres:index', 162, 163);
INSERT INTO acos VALUES (235, 231, '', 0, 'Adressesfoyers:add', 172, 173);
INSERT INTO acos VALUES (232, 231, '', 0, 'Adressesfoyers:index', 166, 167);
INSERT INTO acos VALUES (233, 231, '', 0, 'Adressesfoyers:view', 168, 169);
INSERT INTO acos VALUES (234, 231, '', 0, 'Adressesfoyers:edit', 170, 171);
INSERT INTO acos VALUES (231, 0, '', 0, 'Adressesfoyers', 165, 174);
INSERT INTO acos VALUES (237, 236, '', 0, 'Dspps:view', 176, 177);
INSERT INTO acos VALUES (238, 236, '', 0, 'Dspps:add', 178, 179);
INSERT INTO acos VALUES (236, 0, '', 0, 'Dspps', 175, 182);
INSERT INTO acos VALUES (239, 236, '', 0, 'Dspps:edit', 180, 181);
INSERT INTO acos VALUES (242, 240, '', 0, 'Regroupementszonesgeo:add', 186, 187);
INSERT INTO acos VALUES (241, 240, '', 0, 'Regroupementszonesgeo:index', 184, 185);
INSERT INTO acos VALUES (243, 240, '', 0, 'Regroupementszonesgeo:edit', 188, 189);
INSERT INTO acos VALUES (244, 240, '', 0, 'Regroupementszonesgeo:delete', 190, 191);
INSERT INTO acos VALUES (240, 0, '', 0, 'Regroupementszonesgeo', 183, 192);
INSERT INTO acos VALUES (247, 245, '', 0, 'Infosfinancieres:view', 196, 197);
INSERT INTO acos VALUES (246, 245, '', 0, 'Infosfinancieres:index', 194, 195);
INSERT INTO acos VALUES (245, 0, '', 0, 'Infosfinancieres', 193, 198);
INSERT INTO acos VALUES (250, 248, '', 0, 'Typoscontrats:add', 202, 203);
INSERT INTO acos VALUES (249, 248, '', 0, 'Typoscontrats:index', 200, 201);
INSERT INTO acos VALUES (251, 248, '', 0, 'Typoscontrats:edit', 204, 205);
INSERT INTO acos VALUES (252, 248, '', 0, 'Typoscontrats:delete', 206, 207);
INSERT INTO acos VALUES (248, 0, '', 0, 'Typoscontrats', 199, 208);
INSERT INTO acos VALUES (254, 253, '', 0, 'Dspfs:view', 210, 211);
INSERT INTO acos VALUES (255, 253, '', 0, 'Dspfs:add', 212, 213);
INSERT INTO acos VALUES (253, 0, '', 0, 'Dspfs', 209, 216);
INSERT INTO acos VALUES (256, 253, '', 0, 'Dspfs:edit', 214, 215);
INSERT INTO acos VALUES (258, 257, '', 0, 'Prestsform:add', 218, 219);
INSERT INTO acos VALUES (257, 0, '', 0, 'Prestsform', 217, 222);
INSERT INTO acos VALUES (259, 257, '', 0, 'Prestsform:edit', 220, 221);
INSERT INTO acos VALUES (261, 260, '', 0, 'Ressources:index', 224, 225);
INSERT INTO acos VALUES (262, 260, '', 0, 'Ressources:view', 226, 227);
INSERT INTO acos VALUES (263, 260, '', 0, 'Ressources:add', 228, 229);
INSERT INTO acos VALUES (260, 0, '', 0, 'Ressources', 223, 232);
INSERT INTO acos VALUES (264, 260, '', 0, 'Ressources:edit', 230, 231);
INSERT INTO acos VALUES (265, 0, '', 0, 'Criteresci', 233, 236);
INSERT INTO acos VALUES (266, 265, '', 0, 'Criteresci:index', 234, 235);
INSERT INTO acos VALUES (268, 267, '', 0, 'Cohortes:nouvelles', 238, 239);
INSERT INTO acos VALUES (269, 267, '', 0, 'Cohortes:orientees', 240, 241);
INSERT INTO acos VALUES (267, 0, '', 0, 'Cohortes', 237, 244);
INSERT INTO acos VALUES (270, 267, '', 0, 'Cohortes:enattente', 242, 243);
INSERT INTO acos VALUES (272, 271, '', 0, 'Suivisinstruction:index', 246, 247);
INSERT INTO acos VALUES (271, 0, '', 0, 'Suivisinstruction', 245, 250);
INSERT INTO acos VALUES (273, 271, '', 0, 'Suivisinstruction:view', 248, 249);
INSERT INTO acos VALUES (275, 274, '', 0, 'Users:index', 252, 253);
INSERT INTO acos VALUES (276, 274, '', 0, 'Users:add', 254, 255);
INSERT INTO acos VALUES (277, 274, '', 0, 'Users:edit', 256, 257);
INSERT INTO acos VALUES (274, 0, '', 0, 'Users', 251, 260);
INSERT INTO acos VALUES (278, 274, '', 0, 'Users:delete', 258, 259);
INSERT INTO acos VALUES (280, 279, '', 0, 'Gedooos:notification_structure', 262, 263);
INSERT INTO acos VALUES (281, 279, '', 0, 'Gedooos:contratinsertion', 264, 265);
INSERT INTO acos VALUES (282, 279, '', 0, 'Gedooos:orientstruct', 266, 267);
INSERT INTO acos VALUES (279, 0, '', 0, 'Gedooos', 261, 270);
INSERT INTO acos VALUES (283, 279, '', 0, 'Gedooos:notifications_cohortes', 268, 269);
INSERT INTO acos VALUES (285, 284, '', 0, 'Avispcgdroitrsa:index', 272, 273);
INSERT INTO acos VALUES (284, 0, '', 0, 'Avispcgdroitrsa', 271, 276);
INSERT INTO acos VALUES (286, 284, '', 0, 'Avispcgdroitrsa:view', 274, 275);
INSERT INTO acos VALUES (288, 287, '', 0, 'Typesorients:index', 278, 279);
INSERT INTO acos VALUES (289, 287, '', 0, 'Typesorients:add', 280, 281);
INSERT INTO acos VALUES (290, 287, '', 0, 'Typesorients:edit', 282, 283);
INSERT INTO acos VALUES (287, 0, '', 0, 'Typesorients', 277, 286);
INSERT INTO acos VALUES (291, 287, '', 0, 'Typesorients:delete', 284, 285);
INSERT INTO acos VALUES (293, 292, '', 0, 'Grossesses:index', 288, 289);
INSERT INTO acos VALUES (292, 0, '', 0, 'Grossesses', 287, 292);
INSERT INTO acos VALUES (294, 292, '', 0, 'Grossesses:view', 290, 291);
INSERT INTO acos VALUES (295, 0, '', 0, 'Dossiers', 293, 296);
INSERT INTO acos VALUES (296, 295, '', 0, 'Dossiers:view', 294, 295);

--
-- Data for Name: actionsinsertion; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: activites; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: adresses; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: adresses_foyers; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: aidesagricoles; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: aidesdirectes; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: allocationssoutienfamilial; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: aros; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO aros VALUES (11, 10, '', 4, 'Utilisateur:cg66', 2, 3);
INSERT INTO aros VALUES (12, 10, '', 5, 'Utilisateur:cg93', 4, 5);
INSERT INTO aros VALUES (13, 10, '', 8, 'Utilisateur:jmille', 6, 7);
INSERT INTO aros VALUES (14, 10, '', 6, 'Utilisateur:webrsa', 8, 9);
INSERT INTO aros VALUES (16, 15, '', 7, 'Utilisateur:cg11', 11, 12);
INSERT INTO aros VALUES (10, NULL, '', 0, 'Group:Administrateurs', 1, 16);
INSERT INTO aros VALUES (15, 10, '', 0, 'Group:Sous_Administrateurs', 10, 15);
INSERT INTO aros VALUES (17, 15, '', 3, 'Utilisateur:cg58', 13, 14);
INSERT INTO aros VALUES (19, 18, '', 1, 'Utilisateur:cg23', 18, 19);
INSERT INTO aros VALUES (18, NULL, '', 0, 'Group:Utilisateurs', 17, 22);
INSERT INTO aros VALUES (20, 18, '', 2, 'Utilisateur:cg54', 20, 21);


--
-- Data for Name: aros_acos; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO aros_acos VALUES (79, 10, 149, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (80, 10, 150, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (81, 10, 153, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (82, 10, 158, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (83, 10, 165, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (84, 10, 169, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (85, 10, 174, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (86, 10, 177, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (87, 10, 182, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (88, 10, 187, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (89, 10, 192, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (90, 10, 197, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (91, 10, 200, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (92, 10, 203, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (93, 10, 206, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (94, 10, 210, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (95, 10, 214, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (96, 10, 217, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (97, 10, 219, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (98, 10, 224, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (99, 10, 227, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (100, 10, 229, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (101, 10, 231, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (102, 10, 236, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (103, 10, 240, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (104, 10, 245, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (105, 10, 248, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (106, 10, 253, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (107, 10, 257, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (108, 10, 260, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (109, 10, 265, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (110, 10, 267, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (111, 10, 271, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (112, 10, 274, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (113, 10, 279, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (114, 10, 284, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (115, 10, 287, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (116, 10, 292, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (117, 10, 295, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (118, 18, 149, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (119, 18, 150, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (120, 18, 153, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (121, 18, 158, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (122, 18, 165, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (123, 18, 169, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (124, 18, 174, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (125, 18, 177, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (126, 18, 182, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (127, 18, 187, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (128, 18, 192, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (129, 18, 197, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (130, 18, 200, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (131, 18, 203, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (132, 18, 206, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (133, 18, 210, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (134, 18, 214, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (135, 18, 217, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (136, 18, 219, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (137, 18, 224, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (138, 18, 227, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (139, 18, 229, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (140, 18, 231, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (141, 18, 236, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (142, 18, 240, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (143, 18, 245, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (144, 18, 248, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (145, 18, 253, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (146, 18, 257, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (147, 18, 260, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (148, 18, 265, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (149, 18, 267, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (150, 18, 271, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (151, 18, 274, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (152, 18, 279, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (153, 18, 284, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (154, 18, 287, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (155, 18, 292, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (156, 18, 295, '1 ', '1 ', '1 ', '1 ');


--
-- Data for Name: avispcgdroitrsa; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: avispcgpersonnes; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: condsadmins; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: connections; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: contratsinsertion; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: creances; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: creancesalimentaires; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: creancesalimentaires_personnes; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: derogations; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: detailscalculsdroitsrsa; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: detailsdroitsrsa; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: detailsressourcesmensuelles; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: difdisps; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO difdisps VALUES (1, '0501', 'Aucune difficulté');
INSERT INTO difdisps VALUES (2, '0502', 'La garde d''enfant de moins de 6 ans');
INSERT INTO difdisps VALUES (3, '0503', 'La garde d''enfant(s) de plus de 6 ans');
INSERT INTO difdisps VALUES (4, '0504', 'La garde d''enfant(s) ou de proche(s) invalide(s)');
INSERT INTO difdisps VALUES (5, '0505', 'La charge de proche(s) dépendant(s)');


--
-- Data for Name: diflogs; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO diflogs VALUES (1, '1001', 'Pas de difficultés');
INSERT INTO diflogs VALUES (2, '1002', 'Impayés de loyer ou de remboursement');
INSERT INTO diflogs VALUES (3, '1003', 'Problèmes financiers');
INSERT INTO diflogs VALUES (4, '1004', 'Qualité du logement (insalubrité, indécence)');
INSERT INTO diflogs VALUES (5, '1005', 'Qualité de l''environnement (isolement, absence de transport collectif)');
INSERT INTO diflogs VALUES (6, '1006', 'Fin de bail, expulsion');
INSERT INTO diflogs VALUES (7, '1007', 'Conditions de logement (surpeuplement)');
INSERT INTO diflogs VALUES (8, '1008', 'Eloignement entre le lieu de résidence et le lieu de travail');
INSERT INTO diflogs VALUES (9, '1009', 'Autres');


--
-- Data for Name: difsocs; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO difsocs VALUES (1, '0401', 'Aucune difficulté');
INSERT INTO difsocs VALUES (2, '0402', 'Santé');
INSERT INTO difsocs VALUES (3, '0403', 'Reconnaissance de la qualité du travailleur handicapé');
INSERT INTO difsocs VALUES (4, '0404', 'Lecture, écriture ou compréhension du fançais');
INSERT INTO difsocs VALUES (5, '0405', 'Démarches et formalités administratives');
INSERT INTO difsocs VALUES (6, '0406', 'Endettement');
INSERT INTO difsocs VALUES (7, '0407', 'Autres');


--
-- Data for Name: dossiers_rsa; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: dossierscaf; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: dspfs; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: dspfs_diflogs; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: dspfs_nataccosocfams; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: dspps; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: dspps_accoemplois; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: dspps_difdisps; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: dspps_difsocs; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: dspps_nataccosocindis; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: dspps_natmobs; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: dspps_nivetus; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: evenements; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: foyers; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: foyers_creances; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: foyers_evenements; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: grossesses; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: groups; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO groups VALUES (1, 'Administrateurs', 0);
INSERT INTO groups VALUES (2, 'Utilisateurs', 0);
INSERT INTO groups VALUES (3, 'Sous_Administrateurs', 1);


--
-- Data for Name: identificationsflux; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: informationseti; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: infosagricoles; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: infosfinancieres; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: jetons; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: liberalites; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: modescontact; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: nataccosocfams; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO nataccosocfams VALUES (1, '0410', 'Logement');
INSERT INTO nataccosocfams VALUES (2, '0411', 'Endettement');
INSERT INTO nataccosocfams VALUES (3, '0412', 'Familiale');
INSERT INTO nataccosocfams VALUES (4, '0413', 'Autres');


--
-- Data for Name: nataccosocindis; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO nataccosocindis VALUES (1, '0415', 'Pas d''accompagnement individuel');
INSERT INTO nataccosocindis VALUES (2, '0416', 'Santé');
INSERT INTO nataccosocindis VALUES (3, '0417', 'Emploi');
INSERT INTO nataccosocindis VALUES (4, '0418', 'Insertion professionnelle');
INSERT INTO nataccosocindis VALUES (5, '0419', 'Formation');
INSERT INTO nataccosocindis VALUES (6, '0420', 'Autres');


--
-- Data for Name: natmobs; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO natmobs VALUES (1, '2501', 'Sur la commune');
INSERT INTO natmobs VALUES (2, '2502', 'Sur le département');
INSERT INTO natmobs VALUES (3, '2503', 'Sur un autre département');


--
-- Data for Name: nivetus; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO nivetus VALUES (1, '1201', 'Niveau I/II: enseignement supérieur');
INSERT INTO nivetus VALUES (2, '1202', 'Niveau III: BAC + 2');
INSERT INTO nivetus VALUES (3, '1203', 'Niveau IV: BAC ou équivalent');
INSERT INTO nivetus VALUES (4, '1204', 'Niveau V: CAP/BEP');
INSERT INTO nivetus VALUES (5, '1205', 'Niveau Vbis: fin de scolarité obligatoire');
INSERT INTO nivetus VALUES (6, '1206', 'Niveau VI: pas de niveau');
INSERT INTO nivetus VALUES (7, '1207', 'Niveau VII: jamais scolarisé');


--
-- Data for Name: orientsstructs; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: orientsstructs_servicesinstructeurs; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: paiementsfoyers; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: personnes; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: prestations; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: prestsform; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: rattachements; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: reducsrsa; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: referents; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: refsprestas; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: regroupementszonesgeo; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: ressources; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: ressources_ressourcesmensuelles; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: ressourcesmensuelles; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: ressourcesmensuelles_detailsressourcesmensuelles; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: servicesinstructeurs; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO servicesinstructeurs VALUES (1, 'Service 1', '16', 'collines', '', '30900', '30000', 'Nimes', '001', 'A', '001', 1, 'R');
INSERT INTO servicesinstructeurs VALUES (2, 'Service 2', '775', 'moulin', '', '34080', '34000', 'Lattes', '002', 'G', '002', 2, 'R');


--
-- Data for Name: situationsdossiersrsa; Type: TABLE DATA; Schema: public; Owner: webrsa
--


--
-- Data for Name: suivisinstruction; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: suspensionsdroits; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: suspensionsversements; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: titres_sejour; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: totalisationsacomptes; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- Data for Name: typesactions; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO typesactions VALUES (1, 'Facilités offertes');
INSERT INTO typesactions VALUES (2, 'Autonomie sociale');
INSERT INTO typesactions VALUES (3, 'Logement');
INSERT INTO typesactions VALUES (4, 'Insertion professionnelle (stage, prestation, formation');
INSERT INTO typesactions VALUES (5, 'Emploi');


--
-- Data for Name: actions; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO actions VALUES (1, 1, '1P', 'Soutien, suivi social, accompagnement personnel');
INSERT INTO actions VALUES (2, 1, '1F', 'Soutien, suivi social, accompagnement familial');
INSERT INTO actions VALUES (3, 1, '02', 'Aide au retour d''enfants placés');
INSERT INTO actions VALUES (4, 1, '03', 'Soutien éducatif lié aux enfants');
INSERT INTO actions VALUES (5, 1, '04', 'Aide pour la garde des enfants');
INSERT INTO actions VALUES (6, 1, '05', 'Aide financière liée au logement');
INSERT INTO actions VALUES (7, 1, '06', 'Autre aide liée au logement');
INSERT INTO actions VALUES (8, 1, '07', 'Prise en charge financière des frais de formation (y compris stage de conduite automobile)');
INSERT INTO actions VALUES (9, 1, '10', 'Autre facilité offerte');
INSERT INTO actions VALUES (10, 2, '21', 'Démarche liée à la santé');
INSERT INTO actions VALUES (11, 2, '22', 'Alphabétisation, lutte contre l''illétrisme');
INSERT INTO actions VALUES (12, 2, '23', 'Organisation quotidienne');
INSERT INTO actions VALUES (13, 2, '24', 'Démarches administratives (COTOREP, demande d''AAH, de retraite, etc...)');
INSERT INTO actions VALUES (14, 2, '26', 'Bilan social');
INSERT INTO actions VALUES (15, 2, '29', 'Autre action visant à l''autonomie sociale');
INSERT INTO actions VALUES (16, 3, '31', 'Recherche d''un logement');
INSERT INTO actions VALUES (17, 3, '33', 'Demande d''intervention d''un organisme ou d''un fonds d''aide');
INSERT INTO actions VALUES (18, 4, '41', 'Aide ou suivi pour une recherche de stage ou de formation');
INSERT INTO actions VALUES (19, 4, '42', 'Activité en atelier de réinsertion (centre d''hébergement et de réadaptation sociale)');
INSERT INTO actions VALUES (20, 4, '43', 'Chantier école');
INSERT INTO actions VALUES (21, 4, '44', 'Stage de conduite automobile (véhicules légers)');
INSERT INTO actions VALUES (22, 4, '45', 'Stage de formation générale, préparation aux concours, poursuite d''études, etc...');
INSERT INTO actions VALUES (23, 4, '46', 'Stage de formation professionnelle (stage d''insertion et de formation à l''emploi, permis poids lourd, crédit-formation individuel, etc...)');
INSERT INTO actions VALUES (24, 4, '48', 'Bilan professionnel et orientation (évaluation du niveau de compétences professionnelles, module d''orientation approfondie, session d''oientation approfondie, évaluation en milieu de travail, VAE, etc...)');
INSERT INTO actions VALUES (25, 5, '51', 'Aide ou suivi pour une recherche d''emploi');
INSERT INTO actions VALUES (26, 5, '52', 'Contrat initiative emploi');
INSERT INTO actions VALUES (27, 5, '53', 'Contrat de qualification, contrat d''apprentissage');
INSERT INTO actions VALUES (28, 5, '54', 'Emploi dans une association intermédiaire ou une entreprise d''insertion');
INSERT INTO actions VALUES (29, 5, '55', 'Création d''entreprise');
INSERT INTO actions VALUES (30, 5, '56', 'Contrats aidés, Contrat d''Avenir, CIRMA');
INSERT INTO actions VALUES (31, 5, '57', 'Emploi consolidé: CDI');
INSERT INTO actions VALUES (32, 5, '58', 'Emploi familial, service de proximité');
INSERT INTO actions VALUES (33, 5, '59', 'Autre forme d''emploi: CDD, CNE');

--
-- Data for Name: typesorients; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO typesorients VALUES (1, NULL, 'Emploi', 'notif_orientation_cg66_mod3');
/*INSERT INTO typesorients VALUES (2, 1, 'Pôle emploi', 'notif_orientation_cg66_mod3');
INSERT INTO typesorients VALUES (3, 1, 'Exploitant agricole MSA', 'notif_orientation_cg66_mod3');*/
INSERT INTO typesorients VALUES (2, NULL, 'Socioprofessionnelle', 'notif_orientation_cg66_mod1');
-- INSERT INTO typesorients VALUES (5, 4, 'Conseil Général', 'notif_orientation_cg66_mod1');
INSERT INTO typesorients VALUES (3, NULL, 'Social', 'notif_orientation_cg66_mod2');
-- INSERT INTO typesorients VALUES (7, 6, 'Conseil Général', 'notif_orientation_cg66_mod2');
-- INSERT INTO typesorients VALUES (8, 6, 'MSA', 'notif_orientation_cg66_mod2');
-- INSERT INTO typesorients VALUES (9, 6, 'Organisme agréés ACAL', 'notif_orientation_cg66_mod2');
-- INSERT INTO typesorients VALUES (10, 6, 'ATR', 'notif_orientation_cg66_mod2');


--
-- Data for Name: structuresreferentes; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO structuresreferentes VALUES (1, 1, 'Pole emploi Mont Sud', '125', 'Avenue', 'Alco', '34090', 'Montpellier', '34095');
INSERT INTO structuresreferentes VALUES (2, 1, 'Assedic Nimes', '44', 'chemin', 'Parrot', '30000', 'Nimes', '30009');
INSERT INTO structuresreferentes VALUES (3, 3, 'MSA du Gard', '48', 'avenue', 'Paul Condorcet', '30900', 'Nimes', '30000');
INSERT INTO structuresreferentes VALUES (4, 2, 'Conseil Général de l''Hérault', '10', 'rue', 'Georges Freche', '34000', 'Montpellier', '34005');
INSERT INTO structuresreferentes VALUES (5, 3, 'Organisme ACAL Vauvert', '48', 'rue', 'Georges Freche', '30600', 'Vauvert', '30610');


--
-- Data for Name: structuresreferentes_zonesgeographiques; Type: TABLE DATA; Schema: public; Owner: webrsa
--


--
-- Data for Name: typoscontrats; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO typoscontrats VALUES (1, 'Premier contrat');
INSERT INTO typoscontrats VALUES (2, 'Renouvellement');
INSERT INTO typoscontrats VALUES (3, 'Redéfinition');


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO users VALUES (6, 1, 1, 'webrsa', '83a98ed2a57ad9734eb0a1694293d03c74ae8a57', 'auzolat', 'arnaud', NULL, '2009-01-01', '2010-01-01', '0606060606', false);
INSERT INTO users VALUES (5, 1, 1, 'cg93', 'ac860f0d3f51874b31260b406dc2dc549f4c6cde', 'Rasoa', 'James', NULL, '2009-01-01', '2010-01-01', '0143939777', true);
INSERT INTO users VALUES (2, 2, 1, 'cg54', '13bdf5c43c14722e3e2d62bfc0ff0102c9955cda', 'Dupont', 'Albert', NULL, '2009-01-01', '2010-01-01', '0101010101', true);
INSERT INTO users VALUES (1, 2, 2, 'cg23', 'e711d517faf274f83262f0cdd616651e7590927e', 'Cazier', 'Laurent', NULL, '2009-01-01', '2010-01-01', '050505050505', true);
INSERT INTO users VALUES (3, 3, 2, 'cg58', '5054b94efbf033a5fe624e0dfe14c8c0273fe320', 'Capelle', 'Philippe', NULL, '2009-01-01', '2010-01-01', '03.86.60.69.43', true);
INSERT INTO users VALUES (4, 1, 2, 'cg66', 'c41d80854d210d5f7512ab216b53b2f2b8e742dc', 'Dubois', 'Florent', NULL, '2009-01-01', '2010-01-01', '0468686868', true);
INSERT INTO users VALUES (7, 3, 1, 'cg11', '1f7643921e0f0a49e4c2d037e04c9be195eee415', 'Valles', 'Sylvie', NULL, '2009-01-01', '2010-01-01', '0404040404', true);
INSERT INTO users VALUES (8, 1, 1, 'jmille', 'fe34f1c4a8ba5943e0b21536d0fb6b2d28ec1820', 'Mille', 'Julien', NULL, '2008-01-01', '2010-01-01', '0143934192', true);


--
-- Data for Name: users_contratsinsertion; Type: TABLE DATA; Schema: public; Owner: webrsa
--


--
-- Data for Name: zonesgeographiques; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO zonesgeographiques VALUES (1, '34090', 'Pole Montpellier-Nord');
INSERT INTO zonesgeographiques VALUES (2, '34070', 'Pole Montpellier Sud-Est');
INSERT INTO zonesgeographiques VALUES (3, '34080', 'Pole Montpellier Ouest');


--
-- Data for Name: users_zonesgeographiques; Type: TABLE DATA; Schema: public; Owner: webrsa
--

INSERT INTO users_zonesgeographiques VALUES (5, 1, 1);
INSERT INTO users_zonesgeographiques VALUES (5, 2, 2);
INSERT INTO users_zonesgeographiques VALUES (5, 3, 3);
INSERT INTO users_zonesgeographiques VALUES (2, 1, 4);
INSERT INTO users_zonesgeographiques VALUES (2, 2, 5);
INSERT INTO users_zonesgeographiques VALUES (2, 3, 6);
INSERT INTO users_zonesgeographiques VALUES (1, 1, 7);
INSERT INTO users_zonesgeographiques VALUES (1, 2, 8);
INSERT INTO users_zonesgeographiques VALUES (1, 3, 9);
INSERT INTO users_zonesgeographiques VALUES (3, 1, 10);
INSERT INTO users_zonesgeographiques VALUES (3, 2, 11);
INSERT INTO users_zonesgeographiques VALUES (3, 3, 12);
INSERT INTO users_zonesgeographiques VALUES (4, 1, 13);
INSERT INTO users_zonesgeographiques VALUES (4, 2, 14);
INSERT INTO users_zonesgeographiques VALUES (4, 3, 15);
INSERT INTO users_zonesgeographiques VALUES (7, 1, 16);
INSERT INTO users_zonesgeographiques VALUES (7, 2, 17);
INSERT INTO users_zonesgeographiques VALUES (7, 3, 18);
INSERT INTO users_zonesgeographiques VALUES (8, 1, 19);
INSERT INTO users_zonesgeographiques VALUES (8, 2, 20);
INSERT INTO users_zonesgeographiques VALUES (8, 3, 21);

--
-- Data for Name: zonesgeographiques_regroupementszonesgeo; Type: TABLE DATA; Schema: public; Owner: webrsa
--



--
-- PostgreSQL database dump complete
--

