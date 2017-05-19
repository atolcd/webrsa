
INSERT INTO servicesinstructeurs VALUES ( 1, 'Service 1', '16', 'collines', null, '30900', '30000', 'Nimes');
INSERT INTO servicesinstructeurs VALUES ( 2, 'Service 2', '775', 'moulin',  null, '34080', '34000', 'Lattes' );

INSERT INTO groups VALUES (1, 'Administrateurs', 0);
INSERT INTO groups VALUES (2, 'Utilisateurs', 0);
INSERT INTO groups VALUES (3, 'Sous_Administrateurs', 1);


INSERT INTO users VALUES (5, '1', '1', 'cg93', 'ac860f0d3f51874b31260b406dc2dc549f4c6cde', NULL, NULL, NULL, NULL, NULL);
INSERT INTO users VALUES (4, '1', '1', 'cg66', 'c41d80854d210d5f7512ab216b53b2f2b8e742dc', NULL, NULL, NULL, NULL, NULL);
INSERT INTO users VALUES (3, '3', '1', 'cg58', '5054b94efbf033a5fe624e0dfe14c8c0273fe320', NULL, NULL, NULL, NULL, NULL);
INSERT INTO users VALUES (1, '2', '1', 'cg23', 'e711d517faf274f83262f0cdd616651e7590927e', NULL, NULL, NULL, NULL, NULL);
INSERT INTO users VALUES (2, '2', '1', 'cg54', '13bdf5c43c14722e3e2d62bfc0ff0102c9955cda', NULL, NULL, NULL, NULL, NULL);
INSERT INTO users VALUES (6, '1', '1', 'webrsa', '83a98ed2a57ad9734eb0a1694293d03c74ae8a57', 'auzolat', 'arnaud', NULL, NULL, NULL);


INSERT INTO acos VALUES (4790, 0, '', 0, 'Dossiers:index', 1, 2);
INSERT INTO acos VALUES (4792, 4791, '', 0, 'Gedooos:notification_structure', 4, 5);
INSERT INTO acos VALUES (4791, 0, '', 0, 'Gedooos', 3, 8);
INSERT INTO acos VALUES (4793, 4791, '', 0, 'Gedooos:contratinsertion', 6, 7);
INSERT INTO acos VALUES (4795, 4794, '', 0, 'Personnes:index', 10, 11);
INSERT INTO acos VALUES (4835, 4834, '', 0, 'Infosagricoles:index', 90, 91);
INSERT INTO acos VALUES (4796, 4794, '', 0, 'Personnes:view', 12, 13);
INSERT INTO acos VALUES (4797, 4794, '', 0, 'Personnes:add', 14, 15);
INSERT INTO acos VALUES (4794, 0, '', 0, 'Personnes', 9, 18);
INSERT INTO acos VALUES (4798, 4794, '', 0, 'Personnes:edit', 16, 17);
INSERT INTO acos VALUES (4834, 0, '', 0, 'Infosagricoles', 89, 94);
INSERT INTO acos VALUES (4800, 4799, '', 0, 'Prestsform:add', 20, 21);
INSERT INTO acos VALUES (4799, 0, '', 0, 'Prestsform', 19, 24);
INSERT INTO acos VALUES (4801, 4799, '', 0, 'Prestsform:edit', 22, 23);
INSERT INTO acos VALUES (4836, 4834, '', 0, 'Infosagricoles:view', 92, 93);
INSERT INTO acos VALUES (4803, 4802, '', 0, 'Tests:confirm', 26, 27);
INSERT INTO acos VALUES (4802, 0, '', 0, 'Tests', 25, 30);
INSERT INTO acos VALUES (4804, 4802, '', 0, 'Tests:wizard', 28, 29);
INSERT INTO acos VALUES (4805, 0, '', 0, 'Droits', 31, 34);
INSERT INTO acos VALUES (4806, 4805, '', 0, 'Droits:edit', 32, 33);
INSERT INTO acos VALUES (4808, 4807, '', 0, 'Informationseti:index', 36, 37);
INSERT INTO acos VALUES (4807, 0, '', 0, 'Informationseti', 35, 40);
INSERT INTO acos VALUES (4809, 4807, '', 0, 'Informationseti:view', 38, 39);
INSERT INTO acos VALUES (4810, 0, '', 0, 'Totalisationsacomptes', 41, 44);
INSERT INTO acos VALUES (4811, 4810, '', 0, 'Totalisationsacomptes:index', 42, 43);
INSERT INTO acos VALUES (4813, 4812, '', 0, 'Infosfinancieres:index', 46, 47);
INSERT INTO acos VALUES (4812, 0, '', 0, 'Infosfinancieres', 45, 50);
INSERT INTO acos VALUES (4814, 4812, '', 0, 'Infosfinancieres:view', 48, 49);
INSERT INTO acos VALUES (4816, 4815, '', 0, 'Situationsdossiersrsa:index', 52, 53);
INSERT INTO acos VALUES (4815, 0, '', 0, 'Situationsdossiersrsa', 51, 56);
INSERT INTO acos VALUES (4817, 4815, '', 0, 'Situationsdossiersrsa:view', 54, 55);
INSERT INTO acos VALUES (4838, 4837, '', 0, 'Ressources:index', 96, 97);
INSERT INTO acos VALUES (4819, 4818, '', 0, 'Actionsinsertion:index', 58, 59);
INSERT INTO acos VALUES (4818, 0, '', 0, 'Actionsinsertion', 57, 62);
INSERT INTO acos VALUES (4820, 4818, '', 0, 'Actionsinsertion:edit', 60, 61);
INSERT INTO acos VALUES (4822, 4821, '', 0, 'Dspps:view', 64, 65);
INSERT INTO acos VALUES (4859, 4857, '', 0, 'Adressesfoyers:view', 138, 139);
INSERT INTO acos VALUES (4823, 4821, '', 0, 'Dspps:add', 66, 67);
INSERT INTO acos VALUES (4821, 0, '', 0, 'Dspps', 63, 70);
INSERT INTO acos VALUES (4824, 4821, '', 0, 'Dspps:edit', 68, 69);
INSERT INTO acos VALUES (4839, 4837, '', 0, 'Ressources:view', 98, 99);
INSERT INTO acos VALUES (4826, 4825, '', 0, 'Grossesses:index', 72, 73);
INSERT INTO acos VALUES (4825, 0, '', 0, 'Grossesses', 71, 76);
INSERT INTO acos VALUES (4827, 4825, '', 0, 'Grossesses:view', 74, 75);
INSERT INTO acos VALUES (4829, 4828, '', 0, 'Contratsinsertion:index', 78, 79);
INSERT INTO acos VALUES (4830, 4828, '', 0, 'Contratsinsertion:view', 80, 81);
INSERT INTO acos VALUES (4840, 4837, '', 0, 'Ressources:add', 100, 101);
INSERT INTO acos VALUES (4831, 4828, '', 0, 'Contratsinsertion:add', 82, 83);
INSERT INTO acos VALUES (4832, 4828, '', 0, 'Contratsinsertion:edit', 84, 85);
INSERT INTO acos VALUES (4828, 0, '', 0, 'Contratsinsertion', 77, 88);
INSERT INTO acos VALUES (4833, 4828, '', 0, 'Contratsinsertion:delete', 86, 87);
INSERT INTO acos VALUES (4837, 0, '', 0, 'Ressources', 95, 104);
INSERT INTO acos VALUES (4841, 4837, '', 0, 'Ressources:edit', 102, 103);
INSERT INTO acos VALUES (4843, 4842, '', 0, 'Dspfs:view', 106, 107);
INSERT INTO acos VALUES (4860, 4857, '', 0, 'Adressesfoyers:edit', 140, 141);
INSERT INTO acos VALUES (4844, 4842, '', 0, 'Dspfs:add', 108, 109);
INSERT INTO acos VALUES (4842, 0, '', 0, 'Dspfs', 105, 112);
INSERT INTO acos VALUES (4845, 4842, '', 0, 'Dspfs:edit', 110, 111);
INSERT INTO acos VALUES (4847, 4846, '', 0, 'Suivisinstruction:index', 114, 115);
INSERT INTO acos VALUES (4846, 0, '', 0, 'Suivisinstruction', 113, 118);
INSERT INTO acos VALUES (4848, 4846, '', 0, 'Suivisinstruction:view', 116, 117);
INSERT INTO acos VALUES (4857, 0, '', 0, 'Adressesfoyers', 135, 144);
INSERT INTO acos VALUES (4850, 4849, '', 0, 'Detailsdroitsrsa:index', 120, 121);
INSERT INTO acos VALUES (4849, 0, '', 0, 'Detailsdroitsrsa', 119, 124);
INSERT INTO acos VALUES (4851, 4849, '', 0, 'Detailsdroitsrsa:view', 122, 123);
INSERT INTO acos VALUES (4861, 4857, '', 0, 'Adressesfoyers:add', 142, 143);
INSERT INTO acos VALUES (4853, 4852, '', 0, 'Dossierssimplifies:index', 126, 127);
INSERT INTO acos VALUES (4852, 0, '', 0, 'Dossierssimplifies', 125, 130);
INSERT INTO acos VALUES (4854, 4852, '', 0, 'Dossierssimplifies:add', 128, 129);
INSERT INTO acos VALUES (4855, 0, '', 0, 'Cohortes', 131, 134);
INSERT INTO acos VALUES (4856, 4855, '', 0, 'Cohortes:index', 132, 133);
INSERT INTO acos VALUES (4858, 4857, '', 0, 'Adressesfoyers:index', 136, 137);
INSERT INTO acos VALUES (4863, 4862, '', 0, 'Aidesdirectes:add', 146, 147);
INSERT INTO acos VALUES (4862, 0, '', 0, 'Aidesdirectes', 145, 150);
INSERT INTO acos VALUES (4864, 4862, '', 0, 'Aidesdirectes:edit', 148, 149);
INSERT INTO acos VALUES (4866, 4865, '', 0, 'Avispcgdroitrsa:index', 152, 153);
INSERT INTO acos VALUES (4865, 0, '', 0, 'Avispcgdroitrsa', 151, 156);
INSERT INTO acos VALUES (4867, 4865, '', 0, 'Avispcgdroitrsa:view', 154, 155);
INSERT INTO acos VALUES (4869, 4868, '', 0, 'Dossiers:menu', 158, 159);
INSERT INTO acos VALUES (4868, 0, '', 0, 'Dossiers', 157, 162);
INSERT INTO acos VALUES (4870, 4868, '', 0, 'Dossiers:view', 160, 161);



INSERT INTO aros VALUES (447, 446, '', 1, 'Utilisateur:cg23', 2, 3);
INSERT INTO aros VALUES (448, 446, '', 2, 'Utilisateur:cg54', 4, 5);
INSERT INTO aros VALUES (446, NULL, '', 0, 'Group:Administrateurs', 1, 10);
INSERT INTO aros VALUES (449, 446, '', 0, 'Group:Sous_Administrateurs', 6, 9);
INSERT INTO aros VALUES (450, 449, '', 6, 'Utilisateur:webrsa', 7, 8);
INSERT INTO aros VALUES (452, 451, '', 3, 'Utilisateur:cg58', 12, 13);
INSERT INTO aros VALUES (453, 451, '', 4, 'Utilisateur:cg66', 14, 15);
INSERT INTO aros VALUES (451, NULL, '', 0, 'Group:Utilisateurs', 11, 18);
INSERT INTO aros VALUES (454, 451, '', 5, 'Utilisateur:cg93', 16, 17);



INSERT INTO aros_acos VALUES (2894, 446, 4790, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2895, 446, 4791, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2896, 446, 4794, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2897, 446, 4799, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2898, 446, 4802, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2899, 446, 4805, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2900, 446, 4807, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2901, 446, 4810, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2902, 446, 4812, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2903, 446, 4815, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2904, 446, 4818, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2905, 446, 4821, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2906, 446, 4825, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2907, 446, 4828, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2908, 446, 4834, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2909, 446, 4837, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2910, 446, 4842, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2911, 446, 4846, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2912, 446, 4849, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2913, 446, 4852, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2914, 446, 4855, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2915, 446, 4857, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2916, 446, 4862, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2917, 446, 4865, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2918, 446, 4868, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2919, 450, 4790, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2920, 450, 4794, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2921, 450, 4799, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2922, 450, 4802, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2923, 450, 4805, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2924, 450, 4807, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2925, 450, 4810, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2926, 450, 4812, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2927, 450, 4815, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2928, 450, 4818, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2929, 450, 4821, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2930, 450, 4825, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2931, 450, 4828, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2932, 450, 4834, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2933, 450, 4837, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2934, 450, 4842, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2935, 450, 4846, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2936, 450, 4849, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2937, 450, 4852, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2938, 450, 4855, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2939, 450, 4857, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2940, 450, 4862, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2941, 450, 4865, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2942, 450, 4868, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos VALUES (2943, 451, 4790, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2944, 451, 4791, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2945, 451, 4794, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2946, 451, 4799, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2947, 451, 4802, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2948, 451, 4805, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2949, 451, 4807, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2950, 451, 4810, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2951, 451, 4812, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2952, 451, 4815, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2953, 451, 4818, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2954, 451, 4821, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2955, 451, 4825, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2956, 451, 4828, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2957, 451, 4834, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2958, 451, 4837, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2959, 451, 4842, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2960, 451, 4846, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2961, 451, 4849, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2962, 451, 4852, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2963, 451, 4855, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2964, 451, 4857, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2965, 451, 4862, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2966, 451, 4865, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2967, 451, 4868, '-1', '-1', '-1', '-1');
INSERT INTO aros_acos VALUES (2968, 453, 4790, '1 ', '1 ', '1 ', '1 ');
--------------------------
-- ------ Zones geograhiques -----------
-- -------------------------------------
INSERT INTO zonesgeographiques VALUES
(
     1,   --     id                  SERIAL NOT NULL PRIMARY KEY,
     '34090',   --     codeinsee           CHAR(5) NOT NULL,
     'Pole Montpellier-Nord'   --     libelle             VARCHAR(50) NOT NULL
);

INSERT INTO zonesgeographiques VALUES
(
     2,   --     id                  SERIAL NOT NULL PRIMARY KEY,
     '34070',   --     codeinsee           CHAR(5) NOT NULL,
     'Pole Montpellier Sud-Est'   --     libelle             VARCHAR(50) NOT NULL
);

INSERT INTO zonesgeographiques VALUES
(
     3,   --     id                  SERIAL NOT NULL PRIMARY KEY,
     '34080',   --     codeinsee           CHAR(5) NOT NULL,
     'Pole Montpellier Ouest'   --     libelle             VARCHAR(50) NOT NULL
);

-- -----------------------------------------------------------------------------
--       table Action: pour les prestations et aides
-- -----------------------------------------------------------------------------
INSERT INTO typesactions VALUES
   (
      1,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      'Facilités offertes'                  --         libelle             VARCHAR(250)
   );

INSERT INTO typesactions VALUES
   (
      2,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      'Autonomie sociale'                  --         libelle             VARCHAR(250)
   );

INSERT INTO typesactions VALUES
   (
      3,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      'Logement'                  --         libelle             VARCHAR(250)
   );

INSERT INTO typesactions VALUES
   (
      4,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      'Insertion professionnelle (stage, prestation, formation'                  --         libelle             VARCHAR(250)
   );

INSERT INTO typesactions VALUES
   (
      5,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      'Emploi'                  --         libelle             VARCHAR(250)
   );

-----------------------------------------
---  actions avec Type Facilités offertes (typeaction_id=1)
-----------------------------------------
INSERT INTO actions VALUES
   (
      1,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      1,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '1P',                  --         code                CHAR(2),
      'Soutien, suivi social, accompagnement personnel'                  --         libelle             VARCHAR(250)
   );


INSERT INTO actions VALUES
   (
      2,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      1,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '1F',                  --         code                CHAR(2),
      'Soutien, suivi social, accompagnement familial'                  --         libelle             VARCHAR(250)
   );


INSERT INTO actions VALUES
   (
      3,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      1,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '02',                  --         code                CHAR(2),
      '02 - Aide au retour d''enfants placés'                  --         libelle             VARCHAR(250)
   );


INSERT INTO actions VALUES
   (
      4,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      1,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '03',                  --         code                CHAR(2),
      'Soutien éducatif lié aux enfants'                  --         libelle             VARCHAR(250)
   );


INSERT INTO actions VALUES
   (
      5,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      1,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '04',                  --         code                CHAR(2),
      'Aide pour la garde des enfants'                  --         libelle             VARCHAR(250)
   );


INSERT INTO actions VALUES
   (
      6,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      1,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '05',                  --         code                CHAR(2),
      'Aide financière liée au logement'                  --         libelle             VARCHAR(250)
   );


INSERT INTO actions VALUES
   (
      7,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      1,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '06',                  --         code                CHAR(2),
      'Autre aide liée au logement'                  --         libelle             VARCHAR(250)
   );


INSERT INTO actions VALUES
   (
      8,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      1,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '07',                  --         code                CHAR(2),
      'Prise en charge financière des frais de formation (y compris stage de conduite automobile)'                  --         libelle             VARCHAR(250)
   );


INSERT INTO actions VALUES
   (
      9,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      1,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '10',                  --         code                CHAR(2),
      'Autre facilité offerte'                  --         libelle             VARCHAR(250)
   );

----------------------------------------------------------
---  actions avec Type Autonomie sociale (typeaction_id=2)
----------------------------------------------------------
INSERT INTO actions VALUES
   (
      10,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      2,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '21',                  --         code                CHAR(2),
      'Démarche liée à la santé'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      11,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      2,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '22',                  --         code                CHAR(2),
      'Alphabétisation, lutte contre l''illétrisme'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      12,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      2,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '23',                  --         code                CHAR(2),
      'Organisation quotidienne'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      13,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      2,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '24',                  --         code                CHAR(2),
      'Démarches administratives (COTOREP, demande d''AAH, de retraite, etc...)'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      14,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      2,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '26',                  --         code                CHAR(2),
      'Bilan social'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      15,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      2,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '29',                  --         code                CHAR(2),
      'Autre action visant à l''autonomie sociale'                  --         libelle             VARCHAR(250)
   );

-------------------------------------------------
---  actions avec Type Logement (typeaction_id=3)
-------------------------------------------------

INSERT INTO actions VALUES
   (
      16,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      3,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '31',                  --         code                CHAR(2),
      'Recherche d''un logement'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      17,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      3,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '33',                  --         code                CHAR(2),
      'Demande d''intervention d''un organisme ou d''un fonds d''aide'                  --         libelle             VARCHAR(250)
   );

-------------------------------------------------
---  actions avec Type Insertion pro (typeaction_id=4)
-------------------------------------------------

INSERT INTO actions VALUES
   (
      18,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      4,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '41',                  --         code                CHAR(2),
      'Aide ou suivi pour une recherche de stage ou de formation'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      19,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      4,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '42',                  --         code                CHAR(2),
      'Activité en atelier de réinsertion (centre d''hébergement et de réadaptation sociale)'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      20,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      4,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '43',                  --         code                CHAR(2),
      'Chantier école'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      21,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      4,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '44',                  --         code                CHAR(2),
      'Stage de conduite automobile (véhicules légers)'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      22,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      4,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '45',                  --         code                CHAR(2),
      'Stage de formation générale, préparation aux concours, poursuite d''études, etc...'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      23,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      4,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '46',                  --         code                CHAR(2),
      'Stage de formation professionnelle (stage d''insertion et de formation à l''emploi, permis poids lourd, crédit-formation individuel, etc...)'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      24,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      4,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '48',                  --         code                CHAR(2),
      'Bilan professionnel et orientation (évaluation du niveau de compétences professionnelles, module d''orientation approfondie, session d''oientation approfondie, évaluation en milieu de travail, VAE, etc...)'                  --         libelle             VARCHAR(250)
   );

-------------------------------------------------
---  actions avec Type Insertion pro (typeaction_id=5)
-------------------------------------------------

INSERT INTO actions VALUES
   (
      25,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      5,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '51',                  --         code                CHAR(2),
      'Aide ou suivi pour une recherche d''emploi'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      26,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      5,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '52',                  --         code                CHAR(2),
      'Contrat initiative emploi'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      27,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      5,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '53',                  --         code                CHAR(2),
      'Contrat de qualification, contrat d''apprentissage'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      28,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      5,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '54',                  --         code                CHAR(2),
      'Emploi dans une association intermédiaire ou une entreprise d''insertion'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      29,                  --         id                  SERIAL NOT NULL PRIMARY KEY
      5,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '55',                  --         code                CHAR(2),
      'Création d''entreprise'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      30,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      5,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '56',                  --         code                CHAR(2),
      'Contrats aidés, Contrat d''Avenir, CIRMA'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      31,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      5,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '57',                  --         code                CHAR(2),
      'Emploi consolidé: CDI'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      32,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      5,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '58',                  --         code                CHAR(2),
      'Emploi familial, service de proximité'                  --         libelle             VARCHAR(250)
   );

INSERT INTO actions VALUES
   (
      33,                  --         id                  SERIAL NOT NULL PRIMARY KEY,
      5,                    --       typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
      '59',                  --         code                CHAR(2),
      'Autre forme d''emploi: CDD, CNE'                  --         libelle             VARCHAR(250)
   );

--------------------------------------
INSERT INTO nataccosocfams VALUES(
    1,
    '0410',
    'Logement'
);

INSERT INTO nataccosocfams VALUES(
    2,
    '0411',
    'Endettement'
);

INSERT INTO nataccosocfams VALUES(
    3,
    '0412',
    'Familiale'
);

INSERT INTO nataccosocfams VALUES(
    4,
    '0413',
    'Autres'
);
--------------------------------------
--------------------------------------
INSERT INTO diflogs VALUES(
    1,
    '1001',
    'Pas de difficultés'
);

INSERT INTO diflogs VALUES(
    2,
    '1002',
    'Impayés de loyer ou de remboursement'
);

INSERT INTO diflogs VALUES(
    3,
    '1003',
    'Problèmes financiers'
);

INSERT INTO diflogs VALUES(
    4,
    '1004',
    'Qualité du logement (insalubrité, indécence)'
);

INSERT INTO diflogs VALUES(
    5,
    '1005',
    'Qualité de l''environnement (isolement, absence de transport collectif)'
);

INSERT INTO diflogs VALUES(
    6,
    '1006',
    'Fin de bail, expulsion'
);

INSERT INTO diflogs VALUES(
    7,
    '1007',
    'Conditions de logement (surpeuplement)'
);

INSERT INTO diflogs VALUES(
    8,
    '1008',
    'Eloignement entre le lieu de résidence et le lieu de travail'
);

INSERT INTO diflogs VALUES(
    9,
    '1009',
    'Autres'
);

--------------------------------------
INSERT INTO difsocs VALUES(
    1,
    '0401',
    'Aucune difficulté'
);
INSERT INTO difsocs VALUES(
    2,
    '0402',
    'Santé'
);
INSERT INTO difsocs VALUES(
    3,
    '0403',
    'Reconnaissance de la qualité du travailleur handicapé'
);
INSERT INTO difsocs VALUES(
    4,
    '0404',
    'Lecture, écriture ou compréhension du fançais'
);
INSERT INTO difsocs VALUES(
    5,
    '0405',
    'Démarches et formalités administratives'
);
INSERT INTO difsocs VALUES(
    6,
    '0406',
    'Endettement'
);
INSERT INTO difsocs VALUES(
    7,
    '0407',
    'Autres'
);
--------------------------------------
--------------------------------------
INSERT INTO nataccosocindis VALUES(
    1,
    '0415',
    'Pas d''accompagnement individuel'
);
INSERT INTO nataccosocindis VALUES(
    2,
    '0416',
    'Santé'
);
INSERT INTO nataccosocindis VALUES(
    3,
    '0417',
    'Emploi'
);
INSERT INTO nataccosocindis VALUES(
    4,
    '0418',
    'Insertion professionnelle'
);
INSERT INTO nataccosocindis VALUES(
    5,
    '0419',
    'Formation'
);
INSERT INTO nataccosocindis VALUES(
    6,
    '0420',
    'Autres'
);

--------------------------------------
--------------------------------------

INSERT INTO difdisps VALUES(
    1,
    '0501',
    'Aucune difficulté'
);

INSERT INTO difdisps VALUES(
    2,
    '0502',
    'La garde d''enfant de moins de 6 ans'
);

INSERT INTO difdisps VALUES(
    3,
    '0503',
    'La garde d''enfant(s) de plus de 6 ans'
);

INSERT INTO difdisps VALUES(
    4,
    '0504',
    'La garde d''enfant(s) ou de proche(s) invalide(s)'
);

INSERT INTO difdisps VALUES(
    5,
    '0505',
    'La charge de proche(s) dépendant(s)'
);
--------------------------------------
--------------------------------------
INSERT INTO nivetus VALUES(
    1,
    '1201',
    'Niveau I/II: enseignement supérieur'
);
INSERT INTO nivetus VALUES(
    2,
    '1202',
    'Niveau III: BAC + 2'
);
INSERT INTO nivetus VALUES(
    3,
    '1203',
    'Niveau IV: BAC ou équivalent'
);
INSERT INTO nivetus VALUES(
    4,
    '1204',
    'Niveau V: CAP/BEP'
);
INSERT INTO nivetus VALUES(
    5,
    '1205',
    'Niveau Vbis: fin de scolarité obligatoire'
);
INSERT INTO nivetus VALUES(
    6,
    '1206',
    'Niveau VI: pas de niveau'
);
INSERT INTO nivetus VALUES(
    7,
    '1207',
    'Niveau VII: jamais scolarisé'
);
--------------------------------------
--------------------------------------
INSERT INTO natmobs VALUES(
    1,
    '2501',
    'Sur la commune'
);
INSERT INTO natmobs VALUES(
    2,
    '2502',
    'Sur le département'
);
INSERT INTO natmobs VALUES(
    3,
    '2503',
    'Sur un autre département'
);

------------------------------------
------------------------------------
INSERT INTO accoemplois VALUES(
    1,
    '1801',
    'Pas d''accompagnement'
);
INSERT INTO accoemplois VALUES(
    2,
    '1802',
    'Pole emploi'
);
INSERT INTO accoemplois VALUES(
    3,
    '1803',
    'Autres'
);
-- ----------------------------------------
-- -- ------ Types orientations -----------
-- -- -------------------------------------
INSERT INTO typesorients VALUES
   (
     1,   --     id                  SERIAL NOT NULL PRIMARY KEY,
     null,   --            INTEGER,
     'Emploi',
     'notif_orientation_cg66_mod3'
   );

-- INSERT INTO typesorients VALUES
--    (
--      2,   --     id                  SERIAL NOT NULL PRIMARY KEY,
--      1,   --                INTEGER,
--      'Pôle emploi',
--      'notif_orientation_cg66_mod3'
--    );

-- INSERT INTO typesorients VALUES
--    (
--      3,   --     id                  SERIAL NOT NULL PRIMARY KEY,
--      1,   --                INTEGER,
--     'Exploitant agricole MSA',
--      'notif_orientation_cg66_mod3'
--    );

INSERT INTO typesorients VALUES
   (
     2,   --     id                  SERIAL NOT NULL PRIMARY KEY,
     null,   --                INTEGER,
     'Socioprofessionnelle',
     'notif_orientation_cg66_mod1'
   );

-- INSERT INTO typesorients VALUES
--    (
--      5,   --     id                  SERIAL NOT NULL PRIMARY KEY,
--      4,     --                INTEGER,
--     'Conseil Général',   --     name varchar(30) null
--      'notif_orientation_cg66_mod1'
--    );

INSERT INTO typesorients VALUES
   (
     3,   --     id                  SERIAL NOT NULL PRIMARY KEY,
     null,  --                INTEGER,
    'Social',
     'notif_orientation_cg66_mod2'
   );

-- INSERT INTO typesorients VALUES
--    (
--      7,   --     id                  SERIAL NOT NULL PRIMARY KEY,
--      6,  --                INTEGER,
--     'Conseil Général',
--      'notif_orientation_cg66_mod2'
--    );

-- INSERT INTO typesorients VALUES
--    (
--      8,   --     id                  SERIAL NOT NULL PRIMARY KEY,
--      6,  --                INTEGER,
--     'MSA',
--      'notif_orientation_cg66_mod2'
--    );

-- INSERT INTO typesorients VALUES
--    (
--      9,   --     id                  SERIAL NOT NULL PRIMARY KEY,
--      6,  --                INTEGER,
--     'Organisme agréés ACAL',
--      'notif_orientation_cg66_mod2'
--    );

-- INSERT INTO typesorients VALUES
--    (
--      10,   --     id                  SERIAL NOT NULL PRIMARY KEY,
--      6,  --                INTEGER,
--     'ATR',
--      'notif_orientation_cg66_mod2'
--    );

----------------------------
----------------------------
INSERT INTO typoscontrats VALUES
   (
     1,   --     id                  SERIAL NOT NULL PRIMARY KEY,
    'Premier contrat'
   );

INSERT INTO typoscontrats VALUES
   (
     2,   --     id                  SERIAL NOT NULL PRIMARY KEY,
    'Renouvellement'
   );

INSERT INTO typoscontrats VALUES
(
    3,   --     id                  SERIAL NOT NULL PRIMARY KEY,
    'Redéfinition'
);
--------------------------------------
-- ------ Structures référentes -----------
-- -------------------------------------
INSERT INTO structuresreferentes VALUES
   (
      1,  --     id                  SERIAL NOT NULL PRIMARY KEY,
      1,  --     typeorient_id           INTEGER NOT NULL REFERENCES typesorients(id),
      'Pole emploi Mont Sud',  --     lib_struc           VARCHAR(32) NOT NULL,
      '125',  --     num_voie            VARCHAR(6) NOT NULL,
      'Avenue',  --     type_voie           VARCHAR(6) NOT NULL,
      'Alco', --     nom_voie            VARCHAR(30) NOT NULL,
      '34090',  --     code_postal         CHAR(5) NOT NULL,
      'Montpellier',  --     ville               VARCHAR(45) NOT NULL,
      '34095'  --     code_insee          CHAR(5) NOT NULL
   );

INSERT INTO structuresreferentes VALUES
   (
      2,  --     id                  SERIAL NOT NULL PRIMARY KEY,
      1,  --     typeorient_id           INTEGER NOT NULL REFERENCES typesorients(id),
      'Assedic Nimes',  --     lib_struc           VARCHAR(32) NOT NULL,
      '44',  --     num_voie            VARCHAR(6) NOT NULL,
      'chemin',  --     type_voie           VARCHAR(6) NOT NULL,
      'Parrot', --     nom_voie            VARCHAR(30) NOT NULL,
      '30000',  --     code_postal         CHAR(5) NOT NULL,
      'Nimes',  --     ville               VARCHAR(45) NOT NULL,
      '30009'  --     code_insee          CHAR(5) NOT NULL
   );

INSERT INTO structuresreferentes VALUES
   (
      3,  --     id                  SERIAL NOT NULL PRIMARY KEY,
      2,  --     typeorient_id           INTEGER NOT NULL REFERENCES typesorients(id),
      'MSA du Gard',  --     lib_struc           VARCHAR(32) NOT NULL,
      '48',  --     num_voie            VARCHAR(6) NOT NULL,
      'avenue',  --     type_voie           VARCHAR(6) NOT NULL,
      'Paul Condorcet', --     nom_voie            VARCHAR(30) NOT NULL,
      '30900',  --     code_postal         CHAR(5) NOT NULL,
      'Nimes',  --     ville               VARCHAR(45) NOT NULL,
      '30000'  --     code_insee          CHAR(5) NOT NULL
   );


INSERT INTO structuresreferentes VALUES
   (
      4,  --     id                  SERIAL NOT NULL PRIMARY KEY,
      3,  --     typeorient_id           INTEGER NOT NULL REFERENCES typesorients(id),
      'Conseil Général de l''Hérault',  --     lib_struc           VARCHAR(32) NOT NULL,
      '10',  --     num_voie            VARCHAR(6) NOT NULL,
      'rue',  --     type_voie           VARCHAR(6) NOT NULL,
      'Georges Freche', --     nom_voie            VARCHAR(30) NOT NULL,
      '34000',  --     code_postal         CHAR(5) NOT NULL,
      'Montpellier',  --     ville               VARCHAR(45) NOT NULL,
      '34005'  --     code_insee          CHAR(5) NOT NULL
   );


INSERT INTO structuresreferentes VALUES
   (
      5,  --     id                  SERIAL NOT NULL PRIMARY KEY,
      3,  --     typeorient_id           INTEGER NOT NULL REFERENCES typesorients(id),
      'Organisme ACAL Vauvert',  --     lib_struc           VARCHAR(32) NOT NULL,
      '48',  --     num_voie            VARCHAR(6) NOT NULL,
      'rue',  --     type_voie           VARCHAR(6) NOT NULL,
      'Georges Freche', --     nom_voie            VARCHAR(30) NOT NULL,
      '30600',  --     code_postal         CHAR(5) NOT NULL,
      'Vauvert',  --     ville               VARCHAR(45) NOT NULL,
      '30610'  --     code_insee          CHAR(5) NOT NULL
   );
