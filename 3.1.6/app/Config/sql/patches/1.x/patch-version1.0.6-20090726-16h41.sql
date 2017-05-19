--------------- Ajout du 29/07/2009 à 16h41 ------------------
ALTER TABLE structuresreferentes_zonesgeographiques DROP CONSTRAINT structuresreferentes_zonesgeographiques_pkey;
ALTER TABLE structuresreferentes_zonesgeographiques ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;
ALTER TABLE structuresreferentes ADD COLUMN filtre_zone_geo BOOLEAN DEFAULT true;

--------------- Ajout du 29/07/2009 à 16h41 ------------------
ALTER TABLE contratsinsertion ADD COLUMN forme_ci CHAR(1);

--------------- Ajout du 11/08/2009 à 16h03 ------------------
ALTER TABLE dspps_nivetus ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;

--------------- Ajout des id sur les tables liées : 14/08/2009 à 14h33 ------------------
ALTER TABLE ressources_ressourcesmensuelles ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;
ALTER TABLE ressourcesmensuelles_detailsressourcesmensuelles ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;

ALTER TABLE orientsstructs_servicesinstructeurs DROP CONSTRAINT orientsstructs_servicesinstructeurs_pkey;
ALTER TABLE orientsstructs_servicesinstructeurs ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;

ALTER TABLE zonesgeographiques_regroupementszonesgeo DROP CONSTRAINT zonesgeographiques_regroupementszonesgeo_pkey;
ALTER TABLE zonesgeographiques_regroupementszonesgeo ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;

ALTER TABLE users_contratsinsertion DROP CONSTRAINT users_contratsinsertion_pkey;
ALTER TABLE users_contratsinsertion ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;

ALTER TABLE foyers_evenements ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;
ALTER TABLE foyers_creances ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;

ALTER TABLE dspps_natmobs ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;
ALTER TABLE dspps_nataccosocindis ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;
ALTER TABLE dspps_difsocs ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;
ALTER TABLE dspps_difdisps ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;
ALTER TABLE dspps_accoemplois ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;

ALTER TABLE dspfs_nataccosocfams ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;
ALTER TABLE dspfs_diflogs ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;

ALTER TABLE creancesalimentaires_personnes ADD COLUMN id SERIAL NOT NULL PRIMARY KEY;

--------------- Ajout du 14/08/2009 à 15h30 ------------------
ALTER TABLE ressourcesmensuelles DROP CONSTRAINT ressourcesmensuelles_ressource_id_fkey;
ALTER TABLE ressourcesmensuelles ADD CONSTRAINT ressourcesmensuelles_ressource_id_fkey FOREIGN KEY (ressource_id) REFERENCES ressources (id) ON DELETE CASCADE;

ALTER TABLE detailsressourcesmensuelles DROP CONSTRAINT detailsressourcesmensuelles_ressourcemensuelle_id_fkey;
ALTER TABLE detailsressourcesmensuelles ADD CONSTRAINT detailsressourcesmensuelles_ressourcemensuelle_id_fkey FOREIGN KEY (ressourcemensuelle_id) REFERENCES ressourcesmensuelles (id) ON DELETE CASCADE;

--------------- Ajout du 17/08/2009 à 14h37------------------
-- les fichiers insérés à la main doivent aussi avoir leur situation quand ils étaient insérés avant
INSERT INTO situationsdossiersrsa (dossier_rsa_id, etatdosrsa, dtrefursa, moticlorsa, dtclorsa)
SELECT dossiers_rsa.id, 'Z', NULL, NULL, NULL
    FROM dossiers_rsa
    WHERE dossiers_rsa.id NOT IN (SELECT dossier_rsa_id FROM situationsdossiersrsa);

/**
-- Dossiers n'aparaissant pas dans les jeux de résultat
-- explication: il n'y a pas de demandeur dans ces dossiers
SELECT *
    FROM personnes
        INNER JOIN prestations ON ( personnes.id = prestations.personne_id )
    WHERE prestations.natprest = 'RSA' AND prestations.rolepers IN ( 'DEM', 'CJT' ) AND personnes.foyer_id IN
(
    SELECT foyers.id FROM foyers WHERE foyers.dossier_rsa_id IN
    (
        SELECT "d2".id FROM "dossiers_rsa" AS "d2" WHERE "d2"."id" NOT IN
            ( SELECT "Dossier"."id"
                FROM "dossiers_rsa" AS "Dossier"
                INNER JOIN foyers AS "Foyer" ON ("Dossier"."id" = "Foyer"."dossier_rsa_id")
                INNER JOIN personnes AS "Personne" ON ("Personne"."foyer_id" = "Foyer"."id")
                INNER JOIN prestations AS "Prestation" ON ("Personne"."id" = "Prestation"."personne_id" AND "Prestation"."rolepers" = 'DEM' AND "Prestation"."natprest" = 'RSA')
                LEFT OUTER JOIN situationsdossiersrsa AS "Situationdossierrsa" ON ("Situationdossierrsa"."dossier_rsa_id" = "Dossier"."id")
                LEFT OUTER JOIN adresses_foyers AS "Adressefoyer" ON ("Foyer"."id" = "Adressefoyer"."foyer_id" AND "Adressefoyer"."rgadr" = '01')
                LEFT OUTER JOIN adresses AS "Adresse" ON ("Adresse"."id" = "Adressefoyer"."adresse_id")
            )
    )
)
*/

ALTER TABLE identificationsflux ALTER COLUMN heucreaflux TYPE timestamp with time zone;
ALTER TABLE identificationsflux ALTER COLUMN heucreaflux TYPE time;
