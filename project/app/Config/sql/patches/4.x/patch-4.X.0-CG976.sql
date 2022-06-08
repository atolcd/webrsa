SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

--Insertion des conclusions
INSERT INTO conclusioncer(libelle)
SELECT 'Renouveler l''accompagnement'
WHERE NOT EXISTS (SELECT id FROM conclusioncer WHERE libelle LIKE 'Renouveler l''accompagnement');
INSERT INTO conclusioncer(libelle)
SELECT 'Réorientation vers l''équipe pluridisciplinaire'
WHERE NOT EXISTS (SELECT id FROM conclusioncer WHERE libelle LIKE 'Réorientation vers l''équipe pluridisciplinaire');
INSERT INTO conclusioncer(libelle)
SELECT 'Retour à l''emploi'
WHERE NOT EXISTS (SELECT id FROM conclusioncer WHERE libelle LIKE 'Retour à l''emploi');



--Insertion des types de contrats
INSERT INTO typescontrats(libelle)
SELECT 'CUI (CES, CEC, CDL) - Secteur non marchand'
WHERE NOT EXISTS (SELECT id FROM typescontrats WHERE libelle LIKE 'CUI (CES, CEC, CDL) - Secteur non marchand');
INSERT INTO typescontrats(libelle)
SELECT 'CUI (CES, CEC, CDL) - Secteur marchand'
WHERE NOT EXISTS (SELECT id FROM typescontrats WHERE libelle LIKE 'CUI (CES, CEC, CDL) - Secteur marchand');
INSERT INTO typescontrats(libelle)
SELECT 'Contrat d''apprentissage de professionnalisation'
WHERE NOT EXISTS (SELECT id FROM typescontrats WHERE libelle LIKE 'Contrat d''apprentissage de professionnalisation');
INSERT INTO typescontrats(libelle)
SELECT 'CDI'
WHERE NOT EXISTS (SELECT id FROM typescontrats WHERE libelle LIKE 'CDI');
INSERT INTO typescontrats(libelle)
SELECT 'CDD'
WHERE NOT EXISTS (SELECT id FROM typescontrats WHERE libelle LIKE 'CDD');
INSERT INTO typescontrats(libelle)
SELECT 'CDDI'
WHERE NOT EXISTS (SELECT id FROM typescontrats WHERE libelle LIKE 'CDDI');


--Insertion des temps de travail
INSERT INTO tempstravail(libelle)
SELECT 'Temps plein'
WHERE NOT EXISTS (SELECT id FROM tempstravail WHERE libelle LIKE 'Temps plein');
INSERT INTO tempstravail(libelle)
SELECT 'Temps partiel'
WHERE NOT EXISTS (SELECT id FROM tempstravail WHERE libelle LIKE 'Temps partiel');


--Insertion des sujet CER
INSERT INTO sujetscers(libelle)
SELECT 'L''emploi'
WHERE NOT EXISTS (SELECT id FROM sujetscers WHERE libelle LIKE 'L''emploi');
INSERT INTO sujetscers(libelle)
SELECT 'La formation'
WHERE NOT EXISTS (SELECT id FROM sujetscers WHERE libelle LIKE 'La formation');
INSERT INTO sujetscers(libelle)
SELECT 'L''autonomie sociale'
WHERE NOT EXISTS (SELECT id FROM sujetscers WHERE libelle LIKE 'L''autonomie sociale');
INSERT INTO sujetscers(libelle)
SELECT 'Le logement'
WHERE NOT EXISTS (SELECT id FROM sujetscers WHERE libelle LIKE 'Le logement');
INSERT INTO sujetscers(libelle)
SELECT 'La santé'
WHERE NOT EXISTS (SELECT id FROM sujetscers WHERE libelle LIKE 'La santé');
INSERT INTO sujetscers(libelle)
SELECT 'Autre'
WHERE NOT EXISTS (SELECT id FROM sujetscers WHERE libelle LIKE 'Autre');




--Insertion des sous-sujet CER
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Projet professionnel', (SELECT id FROM sujetscers WHERE libelle LIKE 'L''emploi')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Projet professionnel');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Validation diplôme étranger', (SELECT id FROM sujetscers WHERE libelle LIKE 'La formation')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Validation diplôme étranger');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Demande d''aide financière', (SELECT id FROM sujetscers WHERE libelle LIKE 'La formation')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Demande d''aide financière');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Aide ou suivi pour la recherche de stage ou formation', (SELECT id FROM sujetscers WHERE libelle LIKE 'La formation')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Aide ou suivi pour la recherche de stage ou formation');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Accès aux droits', (SELECT id FROM sujetscers WHERE libelle LIKE 'L''autonomie sociale')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Accès aux droits');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Démarche pour percevoir l''Allocation Adulte Handicapé', (SELECT id FROM sujetscers WHERE libelle LIKE 'La santé')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Démarche pour percevoir l''Allocation Adulte Handicapé');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Reconnaissance qualité de travailleur handicapé', (SELECT id FROM sujetscers WHERE libelle LIKE 'La santé')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Reconnaissance qualité de travailleur handicapé');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Actions liées à la résolution de difficultés en lien avec la parentalité', (SELECT id FROM sujetscers WHERE libelle LIKE 'L''autonomie sociale')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Actions liées à la résolution de difficultés en lien avec la parentalité');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Actions liées à la résolution de difficultés financières', (SELECT id FROM sujetscers WHERE libelle LIKE 'L''autonomie sociale')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Actions liées à la résolution de difficultés financières');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Recherche d''un logement', (SELECT id FROM sujetscers WHERE libelle LIKE 'Le logement')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Recherche d''un logement');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Se maintenir dans le logement suite à des impayés de loyers', (SELECT id FROM sujetscers WHERE libelle LIKE 'Le logement')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Se maintenir dans le logement suite à des impayés de loyers');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Amélioration de l''habitat pour faire face à une difficulté liée au handicap', (SELECT id FROM sujetscers WHERE libelle LIKE 'Le logement')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Amélioration de l''habitat pour faire face à une difficulté liée au handicap');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Reconversion professionnelle', (SELECT id FROM sujetscers WHERE libelle LIKE 'L''emploi')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Reconversion professionnelle');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Aide ou suivi pour la recherche d''emploi', (SELECT id FROM sujetscers WHERE libelle LIKE 'L''emploi')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Aide ou suivi pour la recherche d''emploi');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Bilan professionnel et orientation', (SELECT id FROM sujetscers WHERE libelle LIKE 'L''emploi')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Bilan professionnel et orientation');
INSERT INTO soussujetscers(libelle, sujetcer_id)
SELECT 'Création d''entreprise', (SELECT id FROM sujetscers WHERE libelle LIKE 'L''emploi')
WHERE NOT EXISTS (SELECT id FROM soussujetscers WHERE libelle LIKE 'Création d''entreprise');

--Insertion des valeurs par sous sujet CER
INSERT INTO valeursparsoussujetscers(libelle, soussujetcer_id)
SELECT 'Aide à la constitution du dossier de surendettement', (SELECT id FROM soussujetscers WHERE libelle LIKE 'Actions liées à la résolution de difficultés financières')
WHERE NOT EXISTS (SELECT id FROM valeursparsoussujetscers WHERE libelle LIKE 'Aide à la constitution du dossier de surendettement');
INSERT INTO valeursparsoussujetscers(libelle, soussujetcer_id)
SELECT 'Aide et accompagnement à la résolution des dettes diverses', (SELECT id FROM soussujetscers WHERE libelle LIKE 'Actions liées à la résolution de difficultés financières')
WHERE NOT EXISTS (SELECT id FROM valeursparsoussujetscers WHERE libelle LIKE 'Aide et accompagnement à la résolution des dettes diverses');
INSERT INTO valeursparsoussujetscers(libelle, soussujetcer_id)
SELECT 'Garde d''enfant', (SELECT id FROM soussujetscers WHERE libelle LIKE 'Actions liées à la résolution de difficultés en lien avec la parentalité')
WHERE NOT EXISTS (SELECT id FROM valeursparsoussujetscers WHERE libelle LIKE 'Garde d''enfant');
INSERT INTO valeursparsoussujetscers(libelle, soussujetcer_id)
SELECT 'Démarches retraites', (SELECT id FROM soussujetscers WHERE libelle LIKE 'Accès aux droits')
WHERE NOT EXISTS (SELECT id FROM valeursparsoussujetscers WHERE libelle LIKE 'Démarches retraites');
INSERT INTO valeursparsoussujetscers(libelle, soussujetcer_id)
SELECT 'Accés à la mobilité', (SELECT id FROM soussujetscers WHERE libelle LIKE 'Accès aux droits')
WHERE NOT EXISTS (SELECT id FROM valeursparsoussujetscers WHERE libelle LIKE 'Accés à la mobilité');
INSERT INTO valeursparsoussujetscers(libelle, soussujetcer_id)
SELECT 'Linguistique : Alphabétisation', (SELECT id FROM soussujetscers WHERE libelle LIKE 'Aide ou suivi pour la recherche de stage ou formation')
WHERE NOT EXISTS (SELECT id FROM valeursparsoussujetscers WHERE libelle LIKE 'Linguistique : Alphabétisation');


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
