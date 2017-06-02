----------- 13/01/2010 - 14h57 ----------------------
DROP TABLE creancesalimentaires_personnes;

----------- 14/01/2010 - 10h17 ----------------------
ALTER TABLE formsqualifs DROP COLUMN qualsuivi;
ALTER TABLE formsqualifs DROP COLUMN nomsuivi;
ALTER TABLE formsqualifs DROP COLUMN prenomsuivi;
ALTER TABLE formsqualifs DROP COLUMN numtelsuivi;

ALTER TABLE formspermsfimo DROP COLUMN qualsuivi;
ALTER TABLE formspermsfimo DROP COLUMN nomsuivi;
ALTER TABLE formspermsfimo DROP COLUMN prenomsuivi;
ALTER TABLE formspermsfimo DROP COLUMN numtelsuivi;

ALTER TABLE actsprofs DROP COLUMN qualsuivi;
ALTER TABLE actsprofs DROP COLUMN nomsuivi;
ALTER TABLE actsprofs DROP COLUMN prenomsuivi;
ALTER TABLE actsprofs DROP COLUMN numtelsuivi;

ALTER TABLE permisb DROP COLUMN qualsuivi;
ALTER TABLE permisb DROP COLUMN nomsuivi;
ALTER TABLE permisb DROP COLUMN prenomsuivi;
ALTER TABLE permisb DROP COLUMN numtelsuivi;

ALTER TABLE amenagslogts DROP COLUMN qualsuivi;
ALTER TABLE amenagslogts DROP COLUMN nomsuivi;
ALTER TABLE amenagslogts DROP COLUMN prenomsuivi;
ALTER TABLE amenagslogts DROP COLUMN numtelsuivi;

ALTER TABLE accscreaentr DROP COLUMN qualsuivi;
ALTER TABLE accscreaentr DROP COLUMN nomsuivi;
ALTER TABLE accscreaentr DROP COLUMN prenomsuivi;
ALTER TABLE accscreaentr DROP COLUMN numtelsuivi;

ALTER TABLE acqsmatsprofs DROP COLUMN qualsuivi;
ALTER TABLE acqsmatsprofs DROP COLUMN nomsuivi;
ALTER TABLE acqsmatsprofs DROP COLUMN prenomsuivi;
ALTER TABLE acqsmatsprofs DROP COLUMN numtelsuivi;

ALTER TABLE locsvehicinsert DROP COLUMN qualsuivi;
ALTER TABLE locsvehicinsert DROP COLUMN nomsuivi;
ALTER TABLE locsvehicinsert DROP COLUMN prenomsuivi;
ALTER TABLE locsvehicinsert DROP COLUMN numtelsuivi;

ALTER TABLE suivisaidesapres ADD COLUMN deleted CHAR(1) DEFAULT '0';
ALTER TABLE suivisaidesapres ADD COLUMN deleted_date DATE;


-------------------- 18/01/2010 - 09h39 ----------------------------
-- ALTER TABLE piecesapre ALTER COLUMN libelle DROP NOT NULL;
-- Permet de supprimer les données de la table pieces apre qui étaient en trop

UPDATE apres_piecesapre
    SET pieceapre_id = ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Curriculum%' ORDER BY id ASC LIMIT 1 )
    WHERE pieceapre_id IN ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Curriculum%' AND id NOT IN
        ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Curriculum%' ORDER BY id ASC LIMIT 1 ) );

DELETE FROM piecesapre
    WHERE libelle ILIKE 'Curriculum%'
        AND id NOT IN ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Curriculum%' ORDER BY id ASC LIMIT 1 );

--

UPDATE apres_piecesapre
    SET pieceapre_id = ( SELECT id FROM piecesapre WHERE libelle ILIKE 'RIB%' ORDER BY id ASC LIMIT 1 )
    WHERE pieceapre_id IN ( SELECT id FROM piecesapre WHERE libelle ILIKE 'RIB%' AND id NOT IN
        ( SELECT id FROM piecesapre WHERE libelle ILIKE 'RIB%' ORDER BY id ASC LIMIT 1 ) );

DELETE FROM piecesapre
    WHERE libelle ILIKE 'RIB%'
        AND id NOT IN ( SELECT id FROM piecesapre WHERE libelle ILIKE 'RIB%' ORDER BY id ASC LIMIT 1 );

--

UPDATE apres_piecesapre
    SET pieceapre_id = ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Lettre%' ORDER BY id ASC LIMIT 1 )
    WHERE pieceapre_id IN ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Lettre%' AND id NOT IN
        ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Lettre%' ORDER BY id ASC LIMIT 1 ) );

DELETE FROM piecesapre
    WHERE libelle ILIKE 'Lettre%'
        AND id NOT IN ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Lettre%' ORDER BY id ASC LIMIT 1 );

--

UPDATE apres_piecesapre
    SET pieceapre_id = ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Attestation%' ORDER BY id ASC LIMIT 1 )
    WHERE pieceapre_id IN ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Attestation%' AND id NOT IN
        ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Attestation%' ORDER BY id ASC LIMIT 1 ) );

DELETE FROM piecesapre
    WHERE libelle ILIKE 'Attestation%'
        AND id NOT IN ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Attestation%' ORDER BY id ASC LIMIT 1 );


DELETE FROM apres_piecesapre
    WHERE pieceapre_id IN ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Justificatif%' );

DELETE FROM apres_piecesapre
    WHERE pieceapre_id IN ( SELECT id FROM piecesapre WHERE libelle ILIKE 'Contrat%' );

DELETE FROM piecesapre
    WHERE libelle ILIKE 'Justificatif%';

DELETE FROM piecesapre
    WHERE libelle ILIKE 'Contrat%';

