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

-- DROP DATABASE IF EXISTS cg58_20140306_v270;
-- CREATE DATABASE cg58_20140306_v270 TEMPLATE cg58_20140306_v2610 ENCODING 'UTF8' OWNER webrsa;
-- /usr/lib/postgresql/8.4/bin/psql -U webrsa -W -d cg58_20140306_v270
-- \i app/Config/sql/patches/2.x/patch-2.7.0.sql

-- DROP DATABASE IF EXISTS cg66_20140627_v270;
-- CREATE DATABASE cg66_20140627_v270 TEMPLATE cg66_20140627_v2610 ENCODING 'UTF8' OWNER webrsa;
-- /usr/lib/postgresql/8.4/bin/psql -U webrsa -W -d cg66_20140627_v270
-- \i app/Config/sql/patches/2.x/patch-2.7.0.sql

-- DROP DATABASE IF EXISTS cg93_20140514_v270;
-- CREATE DATABASE cg93_20140514_v270 TEMPLATE cg93_20140514_v2610 ENCODING 'UTF8' OWNER webrsa;
-- /usr/lib/postgresql/8.4/bin/psql -U webrsa -W -d cg93_20140514_v270
-- \i app/Config/sql/patches/2.x/patch-2.7.0.sql
-- \i app/Config/sql/patches/2.x/patch-2.7.0-datas-cg93.sql
-- \i app/Config/sql/patches/2.x/patch-2.7.0-transfert_data-cg93.sql

-- *****************************************************************************

-- INFO: numvoie est sensé passer de 6 à 5 caractères (norme AFNOR)... mais ce n'est pas reflété par les descriptions des flux
-- SELECT numvoie FROM adresses WHERE LENGTH(numvoie) > 5;

-- *****************************************************************************
-- Création de la table de correspondance des (libellés de) types de voies
-- *****************************************************************************

DROP TABLE IF EXISTS correspondancestypevoie;
CREATE TABLE correspondancestypevoie (
    id          SERIAL NOT NULL PRIMARY KEY,
    typevoie	VARCHAR(4) NOT NULL,
    libtypevoie	VARCHAR(30) NOT NULL
);

INSERT INTO correspondancestypevoie ( typevoie, libtypevoie ) VALUES
	( 'ABE', 'ABBAYE' ),
	( 'ACH', 'ANCIEN CHEMIN' ),
	( 'AGL', 'AGGLOMERATION' ),
	( 'AIRE', 'AIRE' ),
	( 'ALL', 'ALLEE' ),
	( 'ANSE', 'ANSE' ),
	( 'ARC', 'ARCADE' ),
	( 'ART', 'ANCIENNE ROUTE' ),
	( 'AUT', 'AUTOROUTE' ),
	( 'AV', 'AVENUE' ),
	( 'BAST', 'BASTION' ),
	( 'BCH', 'BAS CHEMIN' ),
	( 'BCLE', 'BOUCLE' ),
	( 'BD', 'BOULEVARD' ),
	( 'BEGI', 'BEGUINAGE' ),
	( 'BER', 'BERGE' ),
	( 'BOIS', 'BOIS' ),
	( 'BRE', 'BARRIERE' ),
	( 'BRG', 'BOURG' ),
	( 'BSTD', 'BASTIDE' ),
	( 'BUT', 'BUTTE' ),
	( 'CALE', 'CALE' ),
	( 'CAMP', 'CAMP' ),
	( 'CAR', 'CARREFOUR' ),
	( 'CARE', 'CARRIERE' ),
	( 'CARR', 'CARRE' ),
	( 'CAU', 'CARREAU' ),
	( 'CAV', 'CAVEE' ),
	( 'CGNE', 'CAMPAGNE' ),
	( 'CHE', 'CHEMIN' ),
	( 'CHEM', 'CHEMINEMENT' ),
	( 'CHEZ', 'CHEZ' ),
	( 'CHI', 'CHARMILLE' ),
	( 'CHL', 'CHALET' ),
	( 'CHP', 'CHAPELLE' ),
	( 'CHS', 'CHAUSSEE' ),
	( 'CHT', 'CHATEAU' ),
	( 'CHV', 'CHEMIN VICINAL' ),
	( 'CITE', 'CITE' ),
	( 'CLOI', 'CLOITRE' ),
	( 'CLOS', 'CLOS' ),
	( 'COL', 'COL' ),
	( 'COLI', 'COLLINE' ),
	( 'COR', 'CORNICHE' ),
	( 'COTE', 'COTE' ),
	( 'COTT', 'COTTAGE' ),
	( 'COUR', 'COUR' ),
	( 'CPG', 'CAMPING' ),
	( 'CRS', 'COURS' ),
	( 'CST', 'CASTEL' ),
	( 'CTR', 'CONTOUR' ),
	( 'CTRE', 'CENTRE' ),
	( 'DARS', 'DARSE' ),
	( 'DEG', 'DEGRE' ),
	( 'DIG', 'DIGUE' ),
	( 'DOM', 'DOMAINE' ),
	( 'DSC', 'DESCENTE' ),
	( 'ECL', 'ECLUSE' ),
	( 'EGL', 'EGLISE' ),
	( 'EN', 'ENCEINTE' ),
	( 'ENC', 'ENCLOS' ),
	( 'ENV', 'ENCLAVE' ),
	( 'ESC', 'ESCALIER' ),
	( 'ESP', 'ESPLANADE' ),
	( 'ESPA', 'ESPACE' ),
	( 'ETNG', 'ETANG' ),
	( 'FG', 'FAUBOURG' ),
	( 'FON', 'FONTAINE' ),
	( 'FORM', 'FORUM' ),
	( 'FORT', 'FORT' ),
	( 'FOS', 'FOSSE' ),
	( 'FOYR', 'FOYER' ),
	( 'FRM', 'FERME' ),
	( 'GAL', 'GALERIE' ),
	( 'GARE', 'GARE' ),
	( 'GARN', 'GARENNE' ),
	( 'GBD', 'GRAND BOULEVARD' ),
	( 'GDEN', 'GRAND ENSEMBLE' ),
	( 'GPE', 'GROUPE' ),
	( 'GPT', 'GROUPEMENT' ),
	( 'GR', 'GRAND RUE' ),
	( 'GRI', 'GRILLE' ),
	( 'GRIM', 'GRIMPETTE' ),
	( 'HAM', 'HAMEAU' ),
	( 'HCH', 'HAUT CHEMIN' ),
	( 'HIP', 'HIPPODROME' ),
	( 'HLE', 'HALLE' ),
	( 'HLM', 'HLM' ),
	( 'ILE', 'ILE' ),
	( 'IMM', 'IMMEUBLE' ),
	( 'IMP', 'IMPASSE' ),
	( 'JARD', 'JARDIN' ),
	( 'JTE', 'JETEE' ),
	( 'LD', 'LIEU DIT' ),
	( 'LEVE', 'LEVEE' ),
	( 'LOT', 'LOTISSEMENT' ),
	( 'MAIL', 'MAIL' ),
	( 'MAN', 'MANOIR' ),
	( 'MAR', 'MARCHE' ),
	( 'MAS', 'MAS' ),
	( 'MET', 'METRO' ),
	( 'MF', 'MAISON FORESTIERE' ),
	( 'MLN', 'MOULIN' ),
	( 'MTE', 'MONTEE' ),
	( 'MUS', 'MUSEE' ),
	( 'NTE', 'NOUVELLE ROUTE' ),
	( 'PAE', 'PETITE AVENUE' ),
	( 'PAL', 'PALAIS' ),
	( 'PARC', 'PARC' ),
	( 'PAS', 'PASSAGE' ),
	( 'PASS', 'PASSE' ),
	( 'PAT', 'PATIO' ),
	( 'PAV', 'PAVILLON' ),
	( 'PCH', 'PORCHE' ),
	( 'PERI', 'PERIPHERIQUE' ),
	( 'PIM', 'PETITE IMPASSE' ),
	( 'PKG', 'PARKING' ),
	( 'PL', 'PLACE' ),
	( 'PLAG', 'PLAGE' ),
	( 'PLAN', 'PLAN' ),
	( 'PLCI', 'PLACIS' ),
	( 'PLE', 'PASSERELLE' ),
	( 'PLN', 'PLAINE' ),
	( 'PLT', 'PLATEAU' ),
	( 'PN', 'PASSAGE A NIVEAU' ),
	( 'PNT', 'POINTE' ),
	( 'PONT', 'PONT' ),
	( 'PORQ', 'PORTIQUE' ),
	( 'PORT', 'PORT' ),
	( 'POT', 'POTERNE' ),
	( 'POUR', 'POURTOUR' ),
	( 'PRE', 'PRE' ),
	( 'PROM', 'PROMENADE' ),
	( 'PRQ', 'PRESQU''ILE' ),
	( 'PRT', 'PETITE ROUTE' ),
	( 'PRV', 'PARVIS' ),
	( 'PSTY', 'PERISTYLE' ),
	( 'PTA', 'PETITE ALLEE' ),
	( 'PTE', 'PORTE' ),
	( 'PTR', 'PETITE RUE' ),
	( 'QU', 'QUAI' ),
	( 'QUA', 'QUARTIER' ),
	( 'R', 'RUE' ),
	( 'RAC', 'RACCOURCI' ),
	( 'RAID', 'RAIDILLON' ),
	( 'REM', 'REMPART' ),
	( 'RES', 'RESIDENCE' ),
	( 'RLE', 'RUELLE' ),
	( 'ROC', 'ROC' ),
	( 'ROQT', 'ROQUET' ),
	( 'RPE', 'RAMPE' ),
	( 'RPT', 'ROND POINT' ),
	( 'RTD', 'ROTONDE' ),
	( 'RTE', 'ROUTE' ),
	( 'SEN', 'SENT' ),
	( 'SQ', 'SQUARE' ),
	( 'STA', 'STATION' ),
	( 'STDE', 'STADE' ),
	( 'TOUR', 'TOUR' ),
	( 'TPL', 'TERRE PLEIN' ),
	( 'TRA', 'TRAVERSE' ),
	( 'TRN', 'TERRAIN' ),
	( 'TRT', 'TERTRE' ),
	( 'TSSE', 'TERRASSE' ),
	( 'VAL', 'VALLEE' ),
	( 'VCHE', 'VIEUX CHEMIN' ),
	( 'VEN', 'VENELLE' ),
	( 'VGE', 'VILLAGE' ),
	( 'VIA', 'VIA' ),
	( 'VLA', 'VILLA' ),
	( 'VOI', 'VOIE' ),
	( 'VTE', 'VIEILLE ROUTE' ),
	( 'ZA', 'ZONE D''ACTIVITE' ),
	( 'ZAC', 'ZONE D''AMENAGEMENT CONCERTE' ),
	( 'ZAD', 'ZONE D''AMENAGEMENT DIFFERE' ),
	( 'ZI', 'ZONE INDUSTRIELLE' ),
	( 'ZONE', 'ZONE' ),
	( 'ZUP', 'ZONE A URBANISER EN PRIORITE' );

-- *****************************************************************************
-- A. Fichiers rSa_Echange_Instruction_v20140227.xls et rSa_Echange_Recueil DSP_v20140227.xls
-- *****************************************************************************
-- -----------------------------------------------------------------------------
-- A.1. Racine > InfoDemandeRSA > Personne > Parcours > OrganismeDecisionOrientation, lignes [248,258]
-- -----------------------------------------------------------------------------
-- La colonne compladr passe à 38 caractères
ALTER TABLE parcours ALTER COLUMN compladr TYPE VARCHAR(38);
UPDATE parcours SET compladr = NULL WHERE TRIM( BOTH ' ' FROM compladr ) = '';

-- La colonne typevoie est remplacée par libtypevoie
UPDATE parcours SET typevoie = NULL WHERE TRIM( BOTH ' ' FROM typevoie ) = '';
SELECT add_missing_table_field( 'public', 'parcours', 'libtypevoie', 'VARCHAR(30)' );
ALTER TABLE parcours ALTER COLUMN libtypevoie SET DEFAULT NULL;
UPDATE parcours SET libtypevoie = ( SELECT correspondancestypevoie.libtypevoie FROM correspondancestypevoie WHERE parcours.typevoie = correspondancestypevoie.typevoie );
ALTER TABLE parcours ALTER COLUMN typevoie SET DEFAULT NULL;

-- La colonne nomvoie passe à 32 caractères
ALTER TABLE parcours ALTER COLUMN nomvoie TYPE VARCHAR(32);

-- La colonne lieudist passe à 38 caractères
ALTER TABLE parcours ALTER COLUMN lieudist TYPE VARCHAR(38);

-- La colonne locaadr est remplacée par nomcom
SELECT add_missing_table_field( 'public', 'parcours', 'nomcom', 'VARCHAR(32)' );
UPDATE parcours SET nomcom = locaadr;
ALTER TABLE parcours ALTER COLUMN locaadr SET DEFAULT NULL;

-- -----------------------------------------------------------------------------
-- A.2. Racine > InfoDemandeRSA > Personne > Orientation > OrganismeReferentOrientation, lignes [268,277]
-- -----------------------------------------------------------------------------
-- La colonne compladr passe à 38 caractères
ALTER TABLE orientations ALTER COLUMN compladr TYPE VARCHAR(38);
UPDATE orientations SET compladr = NULL WHERE TRIM( BOTH ' ' FROM compladr ) = '';

-- La colonne typevoie est remplacée par libtypevoie
UPDATE orientations SET typevoie = NULL WHERE TRIM( BOTH ' ' FROM typevoie ) = '';
SELECT add_missing_table_field( 'public', 'orientations', 'libtypevoie', 'VARCHAR(30)' );
ALTER TABLE orientations ALTER COLUMN libtypevoie SET DEFAULT NULL;
UPDATE orientations SET libtypevoie = ( SELECT correspondancestypevoie.libtypevoie FROM correspondancestypevoie WHERE orientations.typevoie = correspondancestypevoie.typevoie );
ALTER TABLE orientations ALTER COLUMN typevoie SET DEFAULT NULL;

-- La colonne nomvoie passe à 32 caractères
ALTER TABLE orientations ALTER COLUMN nomvoie TYPE VARCHAR(32);

-- La colonne lieudist passe à 38 caractères
ALTER TABLE orientations ALTER COLUMN lieudist TYPE VARCHAR(38);

-- La colonne locaadr est remplacée par nomcom
SELECT add_missing_table_field( 'public', 'orientations', 'nomcom', 'VARCHAR(32)' );
UPDATE orientations SET nomcom = locaadr;
ALTER TABLE orientations ALTER COLUMN locaadr SET DEFAULT NULL;

-- -----------------------------------------------------------------------------
-- A.3. Racine > InfoDemandeRSA > DonneesAdministratives > Adresse > TroncCommunAdresse, lignes [288,307]
-- -----------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'adressesfoyers', 'etatadr', 'VARCHAR(2)' );
ALTER TABLE adressesfoyers ALTER COLUMN etatadr SET DEFAULT 'NC';
UPDATE adressesfoyers SET etatadr = 'NC' WHERE etatadr IS NULL;
ALTER TABLE adressesfoyers ALTER COLUMN etatadr SET NOT NULL;

-- Racine > InfoDemandeRSA > DonneesAdministratives > Adresse > AdresseDetailleeFrance
ALTER TABLE adresses ALTER COLUMN compladr TYPE VARCHAR(38);
UPDATE adresses SET compladr = NULL WHERE TRIM( BOTH ' ' FROM compladr ) = '';

-- La colonne typevoie est remplacée par libtypevoie
UPDATE adresses SET typevoie = NULL WHERE TRIM( BOTH ' ' FROM typevoie ) = '';
SELECT add_missing_table_field( 'public', 'adresses', 'libtypevoie', 'VARCHAR(30)' );
ALTER TABLE adresses ALTER COLUMN libtypevoie SET DEFAULT NULL;
UPDATE adresses SET libtypevoie = ( SELECT correspondancestypevoie.libtypevoie FROM correspondancestypevoie WHERE adresses.typevoie = correspondancestypevoie.typevoie );
ALTER TABLE adresses ALTER COLUMN typevoie SET DEFAULT NULL;

-- La colonne nomvoie passe à 32 caractères
ALTER TABLE adresses ALTER COLUMN nomvoie TYPE VARCHAR(32);

-- La colonne lieudist passe à 38 caractères
ALTER TABLE adresses ALTER COLUMN lieudist TYPE VARCHAR(38);

-- numcomrat est remplacé par numcom (CHAR5)
SELECT add_missing_table_field( 'public', 'adresses', 'numcom', 'CHAR(5)' );
ALTER TABLE adresses ALTER COLUMN numcom SET DEFAULT NULL;
UPDATE adresses SET numcom = numcomrat;
ALTER TABLE adresses ALTER COLUMN numcomrat SET DEFAULT NULL;

-- La colonne numcomptt est supprimée
ALTER TABLE adresses ALTER COLUMN numcomptt SET DEFAULT NULL;

-- La colonne locaadr est remplacée par nomcom (VARCHAR32)
SELECT add_missing_table_field( 'public', 'adresses', 'nomcom', 'VARCHAR(32)' );
UPDATE adresses SET nomcom = locaadr;
ALTER TABLE adresses ALTER COLUMN locaadr SET DEFAULT NULL;

-- *****************************************************************************
-- B. Fichiers vrsc0201.xsd et vrsf0501.xsd, ajout du bloc AdresseHorsFrance
-- *****************************************************************************

SELECT add_missing_table_field( 'public', 'adresses', 'liblig2adr', 'VARCHAR(38)' );
ALTER TABLE adresses ALTER COLUMN liblig2adr SET DEFAULT NULL;

SELECT add_missing_table_field( 'public', 'adresses', 'liblig3adr', 'VARCHAR(38)' );
ALTER TABLE adresses ALTER COLUMN liblig3adr SET DEFAULT NULL;

SELECT add_missing_table_field( 'public', 'adresses', 'liblig4adr', 'VARCHAR(38)' );
ALTER TABLE adresses ALTER COLUMN liblig4adr SET DEFAULT NULL;

SELECT add_missing_table_field( 'public', 'adresses', 'liblig5adr', 'VARCHAR(38)' );
ALTER TABLE adresses ALTER COLUMN liblig5adr SET DEFAULT NULL;

SELECT add_missing_table_field( 'public', 'adresses', 'liblig6adr', 'VARCHAR(38)' );
ALTER TABLE adresses ALTER COLUMN liblig6adr SET DEFAULT NULL;

SELECT add_missing_table_field( 'public', 'adresses', 'liblig7adr', 'VARCHAR(38)' );
ALTER TABLE adresses ALTER COLUMN liblig7adr SET DEFAULT NULL;

-- *****************************************************************************
-- Report de ces modifications dans la table cantons
-- *****************************************************************************
-- La colonne typevoie est remplacée par libtypevoie
UPDATE cantons SET typevoie = NULL WHERE TRIM( BOTH ' ' FROM typevoie ) = '';
SELECT add_missing_table_field( 'public', 'cantons', 'libtypevoie', 'VARCHAR(30)' );
ALTER TABLE cantons ALTER COLUMN libtypevoie SET DEFAULT NULL;
UPDATE cantons SET libtypevoie = ( SELECT correspondancestypevoie.libtypevoie FROM correspondancestypevoie WHERE cantons.typevoie = correspondancestypevoie.typevoie );
ALTER TABLE cantons ALTER COLUMN typevoie SET DEFAULT NULL;

-- La colonne nomvoie passe à 32 caractères
ALTER TABLE cantons ALTER COLUMN nomvoie TYPE VARCHAR(32);

-- La colonne locaadr est remplacée par nomcom
SELECT add_missing_table_field( 'public', 'cantons', 'nomcom', 'VARCHAR(32)' );
UPDATE cantons SET nomcom = locaadr;
ALTER TABLE cantons ALTER COLUMN locaadr SET DEFAULT NULL;

-- numcomptt est remplacé par numcom (CHAR5)
SELECT add_missing_table_field( 'public', 'cantons', 'numcom', 'CHAR(5)' );
ALTER TABLE cantons ALTER COLUMN numcom SET DEFAULT NULL;
UPDATE cantons SET numcom = numcomptt;
ALTER TABLE cantons ALTER COLUMN numcomptt SET DEFAULT NULL;

-- *****************************************************************************
-- Report de ces modifications dans la table situationsallocataires
-- *****************************************************************************

-- Suppression de la contrainte sur les valeurs de typevoie
ALTER TABLE situationsallocataires DROP CONSTRAINT situationsallocataires_typevoie_in_list_chk;

ALTER TABLE situationsallocataires ALTER COLUMN compladr TYPE VARCHAR(38);
UPDATE situationsallocataires SET compladr = NULL WHERE TRIM( BOTH ' ' FROM compladr ) = '';

-- La colonne typevoie est remplacée par libtypevoie
UPDATE situationsallocataires SET typevoie = NULL WHERE TRIM( BOTH ' ' FROM typevoie ) = '';
SELECT add_missing_table_field( 'public', 'situationsallocataires', 'libtypevoie', 'VARCHAR(30)' );
ALTER TABLE situationsallocataires ALTER COLUMN libtypevoie SET DEFAULT NULL;
UPDATE situationsallocataires SET libtypevoie = ( SELECT correspondancestypevoie.libtypevoie FROM correspondancestypevoie WHERE situationsallocataires.typevoie = correspondancestypevoie.typevoie );
ALTER TABLE situationsallocataires ALTER COLUMN typevoie SET DEFAULT NULL;

-- La colonne nomvoie passe à 32 caractères
ALTER TABLE situationsallocataires ALTER COLUMN nomvoie TYPE VARCHAR(32);

-- numcomrat est remplacé par numcom (CHAR5)
SELECT add_missing_table_field( 'public', 'situationsallocataires', 'numcom', 'CHAR(5)' );
ALTER TABLE situationsallocataires ALTER COLUMN numcom SET DEFAULT NULL;
UPDATE situationsallocataires SET numcom = numcomrat;
ALTER TABLE situationsallocataires ALTER COLUMN numcomrat SET DEFAULT NULL;

-- La colonne numcomptt est supprimée
ALTER TABLE situationsallocataires ALTER COLUMN numcomptt SET DEFAULT NULL;

-- La colonne locaadr est remplacée par nomcom (VARCHAR32)
SELECT add_missing_table_field( 'public', 'situationsallocataires', 'nomcom', 'VARCHAR(32)' );
UPDATE situationsallocataires SET nomcom = locaadr;
ALTER TABLE situationsallocataires ALTER COLUMN locaadr SET DEFAULT NULL;

-- *****************************************************************************
-- Report de ces modifications dans la table cers93
-- *****************************************************************************

-- La colonne locaadr est remplacée par nomcom (VARCHAR32)
SELECT add_missing_table_field( 'public', 'cers93', 'nomcom', 'VARCHAR(32)' );
UPDATE cers93 SET nomcom = locaadr;
ALTER TABLE cers93 ALTER COLUMN locaadr SET DEFAULT NULL;

-- *****************************************************************************
-- Nettoyage de la table de correspondance des (libellés de) types de voies
-- *****************************************************************************

DROP TABLE IF EXISTS correspondancestypevoie;

-- *****************************************************************************
-- Suppression des colonnes des anciens flux
-- *****************************************************************************

/*SELECT alter_table_drop_column_if_exists( 'public', 'parcours', 'typevoie' );
SELECT alter_table_drop_column_if_exists( 'public', 'parcours', 'locaadr' );

SELECT alter_table_drop_column_if_exists( 'public', 'orientations', 'typevoie' );
SELECT alter_table_drop_column_if_exists( 'public', 'orientations', 'locaadr' );

SELECT alter_table_drop_column_if_exists( 'public', 'adresses', 'typevoie' );
SELECT alter_table_drop_column_if_exists( 'public', 'adresses', 'numcomrat' );
SELECT alter_table_drop_column_if_exists( 'public', 'adresses', 'numcomptt' );
SELECT alter_table_drop_column_if_exists( 'public', 'adresses', 'locaadr' );

SELECT alter_table_drop_column_if_exists( 'public', 'cantons', 'typevoie' );
SELECT alter_table_drop_column_if_exists( 'public', 'cantons', 'locaadr' );
SELECT alter_table_drop_column_if_exists( 'public', 'cantons', 'numcomptt' );

SELECT alter_table_drop_column_if_exists( 'public', 'situationsallocataires', 'typevoie' );
SELECT alter_table_drop_column_if_exists( 'public', 'situationsallocataires', 'numcomrat' );
SELECT alter_table_drop_column_if_exists( 'public', 'situationsallocataires', 'numcomptt' );
SELECT alter_table_drop_column_if_exists( 'public', 'situationsallocataires', 'locaadr' );

SELECT alter_table_drop_column_if_exists( 'public', 'cers93', 'locaadr' );*/

-- *****************************************************************************
COMMIT;
-- *****************************************************************************