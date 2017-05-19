SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
-- JOB: indique que quelque chose doit être réalisé au niveau des jobs Talend
-- WEBRSA: indique que quelque chose doit être réalisé au niveau de l'application web-rsa
-- *****************************************************************************
SELECT NOW();
BEGIN;
-- *****************************************************************************

-- *****************************************************************************
-- /IdentificationFlux
-- Informations supplémentaires dans le nouveau flux bénéficiaire
-- RAS de plus
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'identificationsflux', 'versionflux', 'VARCHAR(8) DEFAULT NULL' );
COMMENT ON COLUMN identificationsflux.versionflux IS 'Version de l''échange';

SELECT add_missing_table_field ('public', 'identificationsflux', 'typeparte', 'VARCHAR(3) DEFAULT NULL' );
COMMENT ON COLUMN identificationsflux.typeparte IS 'Type de partenaire';

SELECT add_missing_table_field ('public', 'identificationsflux', 'ideparte', 'VARCHAR(3) DEFAULT NULL' );
COMMENT ON COLUMN identificationsflux.ideparte IS 'Code d''identification du partenaire';

-- *****************************************************************************
-- /InfosFoyerRSA/IdentificationRSA/Organisme
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/IdentificationRSA/Partenaire
-- JOB: Lors de l'intégration du nouveau bénéficiaire, transformer les valeurs
-- CGD et CTE (nv. bénéficiaire) en CG et CT (tous les autres flux)
-- *****************************************************************************

ALTER TABLE dossiers ALTER COLUMN typeparte TYPE VARCHAR(4);
UPDATE dossiers SET typeparte = TRIM( BOTH ' ' FROM typeparte );
ALTER TABLE dossiers ALTER COLUMN typeparte TYPE VARCHAR(3);

-- *****************************************************************************
-- /InfosFoyerRSA/IdentificationRSA/DemandeRSA
-- JOBS: des informations ont simplement changé de bloc entre l'ancien et le
-- nouveau bénéficiaire:
--	- TroncCommunDroitRSA/DTORIDEMRSA -> DemandeRSA/DTORIDEMRSA
--	- TroncCommunDroitRSA/ORIDEMRSA -> DemandeRSA/ORIDEMRSA
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/IdentificationRSA/OrganismeCedant
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/IdentificationRSA/OrganismePrenant
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Identification
-- JOBS:
--	- ajout de nouveaux champs propres au nouveau flux bénéficiaire
--	- dans le nouveau bénéficiaire, le NIR est sur 13 caractères (que ce soit dans le bloc Identification ou Rattachement), dans les autres flux il est sur 15
--	-> il faudra calculer la clé pour intégrer des NIR 15 avec le job nouveau bénéfiaire (à partir de NIR 13 ou 15: OK)
--		-> SELECT '2222222222222' || COALESCE( calcul_cle_nir('2222222222222'), '' );
--		-> SELECT '222222222222240' || COALESCE( calcul_cle_nir('222222222222240'), '' );
-- *****************************************************************************

ALTER TABLE personnes ALTER COLUMN nomnai TYPE VARCHAR(63);

SELECT add_missing_table_field ('public', 'personnes', 'lisprenoms', 'VARCHAR(50) DEFAULT NULL' );

SELECT add_missing_table_field ('public', 'personnes', 'lieunai', 'VARCHAR(3) DEFAULT NULL' );

SELECT add_missing_table_field ('public', 'personnes', 'locanai', 'VARCHAR(40) DEFAULT NULL' );

SELECT add_missing_table_field ('public', 'personnes', 'paysnai', 'VARCHAR(40) DEFAULT NULL' );

SELECT add_missing_table_field ('public', 'personnes', 'dtdc', 'DATE DEFAULT NULL' );

COMMENT ON COLUMN personnes.lisprenoms IS 'Liste des prénoms';
COMMENT ON COLUMN personnes.lieunai IS 'Lieu de naissance';
COMMENT ON COLUMN personnes.locanai IS 'Localité de naissance';
COMMENT ON COLUMN personnes.paysnai IS 'Pays de naissance';
COMMENT ON COLUMN personnes.dtdc IS 'Date de décès';

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/DossierCafMsa
-- JOBS: des informations ont simplement changé de bloc entre l'ancien (et d'autres
-- flux) et le nouveau bénéficiaire:
--	- DossierCAF/DFRATDOS -> DossierCafMsa/DFRATDOS
--	- DossierCAF/TOPRESPDOS -> DossierCafMsa/TOPRESPDOS
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/FonctionPersonnePFA
-- JOBS: des informations ont changé de bloc entre l'ancien (ainsi que les flux
-- instruction et DSP) et le nouveau bénéficiaire:
-- 1°) Le bloc Prestations a été scindé en deux blocs:
--	* FonctionPersonnePFA -> équivalent d'un bloc Prestations/NATPREST "PFA"
--	* FonctionPersonneRSA -> équivalent d'un bloc Prestations/NATPREST "RSA"
-- 2°) Les changement des blocs est le suivant:
--	- Prestations/NATPREST -> implicite par le nom du nouveau bloc
--	- Prestations/ROLEPERS -> FonctionPersonnePFA/ROLEPERS et FonctionPersonneRSA/ROLEPERS
--	- Prestations/TOPCHAPERS -> FonctionPersonneRSA/TOPCHAPERS (mettre à NULL pour le bloc FonctionPersonnePFA)
-- 3°) ATTENTION: pour ne pas perdre de données, il ne faudra pas supprimer les entrées de la table prestations, au moins pour le cas a°
--	a°) dans le nouveau bénéficiaire, on n'a pas la notion DEM ou CJT (on n'aura que BEN)
--	b°) dans le nouveau bénéficiaire, on garde les valeurs AUT et ENF
--	c°) dans le (nouveau) bénéficiaire, on aura toujours RDO
-- *****************************************************************************

-- RAS en-dehors des remarques du point 9

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/FonctionPersonneRSA
-- *****************************************************************************

-- RAS en-dehors des remarques du point 9

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Rattachement
-- JOBS:
--	a°) Le bloc Rattachement est toujours présent dans le flux instruction
--	b°) Balises de l'ancien flux bénéficiaire (et actuel flux instruction) et nouveau bénéficiaire
--	- Rattachement/DTNAI -> a disparu du nouveau flux instruction
--	- Rattachement/NIR -> il sera envoyé sur 13 caractères dans le nouveau instruction, voir point c°
--	- Rattachement/NOMNAI -> a disparu du nouveau flux instruction
--	- Rattachement/PRENOM -> a disparu du nouveau flux instruction
--	- Rattachement/TYPEPAR -> perte d'information dans le nouveau flux
--                         -> 3 catégories au lieu de 13 dans instruction
--                         -> il faudra simplifier lors de l'intégration du flux instruction (voir requêtes ci-dessous)
--	c°) Dans le nouveau bénéficiaire, le NIR est sur 13 caractères (que ce soit dans le bloc Identification ou Rattachement), dans les autres flux il est sur 15
--	--> il faudra calculer la clé pour intégrer des NIR 15 avec le job nouveau bénéfiaire (à partir de NIR 13 ou 15: OK)
--	    ex.: SELECT '2222222222222' || COALESCE( calcul_cle_nir('2222222222222'), '' );
--		ex.: SELECT '222222222222240' || COALESCE( calcul_cle_nir('222222222222240'), '' );
--	WEBRSA:
--	- vérifier les conditions du rattachement, car DTNAI sera partiellement vide à l'avenir
-- *****************************************************************************

UPDATE rattachements SET typepar = 'ENF' WHERE typepar IN ('ADP', 'DES', 'LEG', 'LEA', 'NAT', 'REA', 'REC');
UPDATE rattachements SET typepar = 'PAR' WHERE typepar IN ('ASC', 'BFI', 'COL', 'FRE', 'NEV', 'ONC');

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Ressources/GeneraliteRessourcesTrimestre
-- JOBS: des informations ont simplement changé de bloc entre l'ancien (ainsi que
--	le flux instruction) et le nouveau bénéficiaire:
--	- GeneraliteRessourcesTrimestre/DDRESS -> TroncCommunDroitRSA/DDRESS
--	- GeneraliteRessourcesTrimestre/DFRESS -> TroncCommunDroitRSA/DFRESS
--	- GeneraliteRessourcesTrimestre/TOPRESSNUL reste au même endroit dans le nouveau flux
-- *****************************************************************************

-- RAS en-dehors des remarques du point 12

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Ressources/RessourcesMensuelles/GeneraliteRessourcesMensuelles
-- JOBS: une information n'est plus envoyée dans le nouveau bénéficiaire alors
-- qu'elle est toujours à priori présente dans le flux instruction
--	- GeneraliteRessourcesMensuelles/NBHEUMENTRA (à mettre à NULL lors de l'intégration)
-- L'autre balise reste à sa place:
--	- GeneraliteRessourcesMensuelles/MOISRESS
-- *****************************************************************************

-- RAS en-dehors des remarques du point 13

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Ressources/RessourcesMensuelles/DetailRessourcesMensuelles
-- JOBS:
--	a°) pas de changement de structure, on garde les informations
--	- DetailRessourcesMensuelles/ABANEU
--	- DetailRessourcesMensuelles/MTNATRESSMEN
--	- DetailRessourcesMensuelles/NATRESS
--	b°) Par contre, les valeurs de NATRESS ont changé dans le nouveau bénéficiaire
--	(par-rapport à l'ancien et au flux instruction) et des nouvelles valeurs sont
--	apparues dans le nouveau bénéficiaire
--	c°) Lors de l'intégration du flux instruction, intégrer les nouvelles valeurs
-- *****************************************************************************

UPDATE detailsressourcesmensuelles SET natress = (
	CASE
		WHEN natress = '001' THEN 'A01'
		WHEN natress = '006' THEN 'A02'
		WHEN natress = '007' THEN 'A03'
		WHEN natress = '008' THEN 'A04'
		WHEN natress = '020' THEN 'A10'
		WHEN natress = '030' THEN 'A20'
		WHEN natress = '100' THEN 'A30'
		WHEN natress = '201' THEN 'A40'
		WHEN natress = '205' THEN 'A41'
		WHEN natress = '009' THEN 'B01'
		WHEN natress = '010' THEN 'B10'
		WHEN natress = '013' THEN 'B11'
		WHEN natress = '014' THEN 'B12'
		WHEN natress = '021' THEN 'C01'
		WHEN natress = '022' THEN 'C02'
		WHEN natress = '024' THEN 'C03'
		WHEN natress = '025' THEN 'C04'
		WHEN natress = '026' THEN 'C05'
		WHEN natress = '027' THEN 'C06'
		WHEN natress = '070' THEN 'C10'
		WHEN natress = '071' THEN 'C11'
		WHEN natress = '072' THEN 'C12'
		WHEN natress = '023' THEN 'D01'
		WHEN natress = '040' THEN 'D10'
		WHEN natress = '041' THEN 'D11'
		WHEN natress = '044' THEN 'D12'
		WHEN natress = '203' THEN 'E01'
		WHEN natress = '206' THEN 'E02'
		WHEN natress = '217' THEN 'E03'
		WHEN natress = '500' THEN 'E10'
		WHEN natress = '000' THEN 'F01'
		ELSE natress
	END );

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Ressources/CaracteristiquesEmployabilite
-- INFOS:
--	a°) Simplification des valeurs par-rapport à l'ancien flux et le nouveau flux instruction (REG et ACT/STATUEMPL)
--	b°) En 0,n dans les flux bénéficiaires (ancien et nouveau, ainsi que dans la base)
--	c°) En 1,1 dans le flux instruction
--	d°) La signification est un poil différente:
--		- instruction: Décrit l'activité en cours de la personne traitée
--		- ancien bénéficiaire: Decrit les activités de la personne traitée
--		- nouveau bénéficiaire: Decrit les caratéristiques d'employabilité de la personne traitée
--	e°) Anciennes et nouvelles balises
--	* Dans le flux instruction et l'ancien bénéficiaire
--		- Activite/ACT -> CaracteristiquesEmployabilite/STATUEMPL
--		- Activite/DDACT -> CaracteristiquesEmployabilite/DDSTATUEMPL
--		- Activite/DFACT -> CaracteristiquesEmployabilite/DFSTATUEMPL
--		- Activite/HAUREMUSMIC (instruction uniquement); le champ devient hauremusmic (au lieu de hauremuscmic)
--		- Activite/NATCONTRTRA -> pas envoyée dans le nouveau bénéficiaire (pas utilisé dans web-rsa)
--		- Activite/PAYSACT (instruction uniquement, pas vraiement utilisé dans l'application)
--		- Activite/REG -> CaracteristiquesEmployabilite/REG
--		- Activite/TOPCONDADMETI (ancien bénéficiaire uniquement, pas utilisé dans web-rsa)
--	* Uniquement dans le nouveau flux bénéficiaire
--		- CaracteristiquesEmployabilite/TOPPERSMALHAN
--
-- JOBS:
--	- il faudra intégrer les données du bloc Activite du flux instruction dans la table employabilites
--	- lors de l'intégration, il faudra "traduire" les valeurs de REG et de ACT / STATUEMPL
--
-- WEBRSA: trouver les endroits où cette information est utilisée et utiliser les nouvelles valeurs
--
-- *****************************************************************************

DROP TABLE IF EXISTS employabilites CASCADE;
CREATE TABLE employabilites (
    id					SERIAL NOT NULL PRIMARY KEY,
	personne_id			INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	reg					VARCHAR(2) DEFAULT NULL,
	statuempl			VARCHAR(3) DEFAULT NULL, -- Pour pouvoir contenir les valeurs de Activite/ACT qui ne seront pas traduites
	toppersmalhan		CHAR(1) DEFAULT '0',
	ddstatuempl			DATE DEFAULT NULL,
	dfstatuempl			DATE DEFAULT NULL,
	natcontrtra			VARCHAR(3) DEFAULT NULL,
	hauremusmic			CHAR(1) DEFAULT NULL,
	paysact				VARCHAR(3) DEFAULT NULL
);

COMMENT ON TABLE employabilites IS 'Decrit les caratéristiques d''employabilité de la personne traitée';
COMMENT ON COLUMN employabilites.reg IS 'CD régime de protection sociale';
COMMENT ON COLUMN employabilites.statuempl IS 'Statut d''employabilité';
COMMENT ON COLUMN employabilites.toppersmalhan IS 'Top personne handicapée ou malade';
COMMENT ON COLUMN employabilites.ddstatuempl IS 'Date début du statut d''employabilité';
COMMENT ON COLUMN employabilites.dfstatuempl IS 'Date fin du statut d''employabilité';
COMMENT ON COLUMN employabilites.natcontrtra IS 'Code type de contrat de travail';
COMMENT ON COLUMN employabilites.hauremusmic IS 'Hauteur rémunération SMIC';
COMMENT ON COLUMN employabilites.paysact IS 'Pays d''activité';

-- On peut en avoir plusieurs dans le même flux, historisation possible
CREATE INDEX employabilites_personne_id_idx ON employabilites(personne_id);

--------------------------------------------------------------------------------
-- Remplissage
--------------------------------------------------------------------------------

INSERT INTO employabilites (personne_id, reg, statuempl, toppersmalhan, ddstatuempl, dfstatuempl, natcontrtra, hauremusmic, paysact)
	SELECT
			personne_id,
			( CASE WHEN reg IN ( 'AG', 'GE' ) THEN reg ELSE 'AU' END ) AS reg,
			(
				CASE
					WHEN act IN ( 'MNE' ) THEN '10'
					-- INFO: personne non en âge scolaire de 0 à 5 ans
					WHEN EXTRACT ( YEAR FROM AGE( personnes.dtnai ) ) <= 5 AND act IN ( 'NOB', 'INF' ) THEN '11'
					-- INFO: personne en âge scolaire de 5 à 16 ans
					WHEN EXTRACT ( YEAR FROM AGE( personnes.dtnai ) ) <= 16 AND act IN ( 'DEG', 'IAD', 'MMA', 'NAS', 'SCF', 'SCI', 'SCO', 'HAN', 'MAL', 'INF', 'INP' ) THEN '12'
					-- INFO: personne étudiante (plus de 16 ans)
					WHEN EXTRACT ( YEAR FROM AGE( personnes.dtnai ) ) > 16 AND act IN ( 'EBO', 'ETS', 'ETU', 'HAN', 'MAL', 'INF', 'INP' ) THEN '14'
					WHEN act IN ( 'APP' ) THEN '14'
					WHEN act IN ( 'AAP', 'AMA', 'AMT', 'ANI', 'CAP', 'CAT', 'CCV', 'CEA', 'CEN', 'CES', 'CGP', 'CHA', 'CIA', 'CIS', 'CSA', 'DNL', 'GSA', 'MAT', 'MOA', 'RAC', 'RMA', 'SAL', 'SNA' ) THEN '20'
					WHEN act IN ( 'ABA', 'ADA', 'ADN', 'AFC', 'AFD', 'AIN', 'ANP', 'ASP', 'ASS', 'CAR', 'CDA', 'CDN', 'CHO', 'CHR', 'CNI', 'CPL', 'FDA', 'FDN', 'MMC', 'MMI' ) THEN '21'
					WHEN act IN ( 'AFA', 'CJT', 'ETI', 'VRP' ) THEN '22'
					WHEN act IN ( 'EXP', 'EXS', 'MAR' ) THEN '23'
					WHEN act IN ( 'INT', 'TSA' ) THEN '24'
					WHEN act IN ( 'PIL', 'SFP', 'SNR' ) THEN '25'
					WHEN act IN ( 'CSS', 'SAB' ) THEN '26'
					WHEN act IN ( 'ABS', 'CAC', 'CBS', 'MOC', 'SAC', 'SSA', 'HAN', 'INP', 'SAV', 'CLD', 'MLD', 'MAL' ) THEN '30'
					WHEN act IN ( 'INV', 'PRE', 'RAT', 'RET', 'RSA', 'SUR' ) THEN '40'
					WHEN act IN ( 'DSF', 'JNF', 'SIN', 'NCH' ) THEN '90'
					ELSE act
				END
			) AS statuempl,
			-- TODO: à faire vérifier par quelqu'un du métier
			( CASE WHEN act IN ( 'AAP', 'CAT', 'CBS', 'CLD', 'HAN', 'INF', 'INP', 'INV', 'MAL', 'MLD', 'MMC', 'MMI', 'MNE', 'RAC', 'RAT', 'SAV', 'SCI', 'SUR' ) THEN '1' ELSE '0' END ) AS toppersmalhan,
			ddact AS ddstatuempl,
			dfact AS dfstatuempl,
			natcontrtra,
			hauremuscmic,
			paysact
		FROM activites
			INNER JOIN personnes ON ( activites.personne_id = personnes.id );

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Grossesse
-- JOBS: certaines informations ont été supprimées du nouveau flux bénéficiaire:
--	- Grossesse/DDGRO -> Grossesse/DDGRO
--	- Grossesse/DFGRO -> cette information ne sera plus envoyée!
--                    -> il faut que les jobs ajoutent la date du flux comme dfgro si ce bloc n'est plus présent et qu'il y avait un ddgro
--	- Grossesse/DTDECLGRO -> Grossesse/DTDECLGRO
--	- Grossesse/NATFINGRO -> cette information ne sera plus envoyée
--                        -> la colonne sera supprimée dnas le patch drop
--                        -> information pas réellement utilisée dans web-rsa
-- *****************************************************************************

-- RAS en-dehors des remarques ci-dessus

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/MesureProtection/IdentificationMesureProtection
-- JOBS: nouvelles données envoyées par la CNAF dans le nouveau flux bénéficiaire
-- *****************************************************************************

-- @see /InfosFoyerRSA/Personne/MesureProtection/MesureProtectionCommun

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/MesureProtection/MesureProtectionCommun
-- JOBS: nouvelles données envoyées par la CNAF dans le nouveau flux bénéficiaire
-- *****************************************************************************

DROP TABLE IF EXISTS mesuresprotections CASCADE;
CREATE TABLE mesuresprotections (
    id					SERIAL NOT NULL PRIMARY KEY,
	personne_id			INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	fonorg				VARCHAR(3) DEFAULT NULL,
	numorg				VARCHAR(3) DEFAULT NULL,
	numpersmesuprot		VARCHAR(11) DEFAULT NULL,
	typepersmesuprot	VARCHAR(2) DEFAULT NULL,
	typemesuprot		VARCHAR(3) DEFAULT NULL
);

COMMENT ON TABLE mesuresprotections IS 'Ce bloc délivre les informations sur la personne assurant une mesure de protection.';
COMMENT ON COLUMN mesuresprotections.fonorg IS 'Fonction de l''organisme gérant le dossier allocataire';
COMMENT ON COLUMN mesuresprotections.numorg IS 'Numéro de l''organisme gérant le dossier allocataire';
COMMENT ON COLUMN mesuresprotections.numpersmesuprot IS 'Numéro personne assurant mesure protection';
COMMENT ON COLUMN mesuresprotections.typepersmesuprot IS 'Type de personne assurant la mesure de protection';
COMMENT ON COLUMN mesuresprotections.typemesuprot IS 'Type de mesure de protection';

-- On peut en avoir plusieurs dans le même flux, pas d'historisation possible
CREATE INDEX mesuresprotections_personne_id_idx ON mesuresprotections(personne_id);

/*
FIXME: voir dans la base du 93 (976 OK) et des autres si on a bien une seule occurence,
pour tout regrouper sur la table avispcgpersonnes.
-> on en aura toujours un seul, pas beaucoup exploité dans l'application

AvisPCGPersonne (0,1 F) -> avispcgpersonnes
	ConditionNonSalarie (0,1 F) -> avispcgpersonnes
	ConditionExploitantAgricole (0,1 F) -> infosagricoles (liée à personne_id)
	Derogation (0,n F) -> derogations (avispcgpersonne_id)
	ExclueRSA (0,1 F) -> excluesrsa (FIXME personne_id)
	Liberalite (0,n F) -> liberalites (avispcgpersonne_id)
	CreanceAlimentaire (0,n F) -> creancesalimentaires (FIXME personne_id)
*/

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisPCGPersonne/ConditionNonSalarie (pas utliisé dans l'application)
-- JOBS: certaines de ces informations étaient dans l'ancien flux bénéficiaire, mais dans le bloc ConditionETI
--	- ConditionETI/AVISEVARESSNONSAL -> ConditionNonSalarie/AVISEVARESSNONSAL
--	- Nouvelle information: ConditionNonSalarie/COMMPROPRESSEVA
--	- Nouvelle information: ConditionNonSalarie/DDRESSNONSALEVA
--	- Nouvelle information: ConditionNonSalarie/DFRESSNONSALEVA
--	- ConditionETI/DTEVARESSNONSAL -> ConditionNonSalarie/DTEVARESSNONSAL
--	- ConditionETI/DTSOURESSNONSAL -> ConditionNonSalarie/DTSOURESSNONSAL
--	- ConditionETI/MTEVARESSNONSAL -> ConditionNonSalarie/MTEVARESSNONSAL (nommée mtevalressnonsal en base)
--	- Nouvelle information: ConditionNonSalarie/MTRESSNONSALEVA
--	- Nouvelle information: ConditionNonSalarie/RESSNONSALEVA
--	- Nouvelle information: ConditionNonSalarie/TOPCONDADMNONSAL
-- *****************************************************************************

CREATE UNIQUE INDEX avispcgpersonnes_personne_id_unique ON avispcgpersonnes (personne_id);

SELECT add_missing_table_field ('public', 'avispcgpersonnes', 'topcondadmnonsal', 'CHAR(1) DEFAULT NULL' );
SELECT add_missing_table_field ('public', 'avispcgpersonnes', 'commpropresseva', 'VARCHAR(60) DEFAULT NULL' );
SELECT add_missing_table_field ('public', 'avispcgpersonnes', 'ddressnonsaleva', 'DATE DEFAULT NULL' );
SELECT add_missing_table_field ('public', 'avispcgpersonnes', 'dfressnonsaleva', 'DATE DEFAULT NULL' );
SELECT add_missing_table_field ('public', 'avispcgpersonnes', 'mtressnonsaleva', 'NUMERIC(9,2) DEFAULT NULL' );
SELECT add_missing_table_field ('public', 'avispcgpersonnes', 'ressnonsaleva', 'CHAR(1) DEFAULT NULL' );

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisPCGPersonne/ConditionExploitantAgricole
-- JOBS / WEBRSA:
--	a°) certaines de ces informations étaient dans l'ancien flux bénéficiaire,
--	mais dans le bloc InformationsAgricoles et d'autres sont présentes dans le bloc
--	Benefices du flux instruction:
--	- Benefices/DTBENAGRI (InformationsAgricoles/DTBENAGRI dans ancien bénéficiaire) -> ConditionExploitantAgricole/DTBENAGRI
--	- Benefices/DTBENAGRIA-1 -> pas présent dans le flux bénéficiaire
--	- Benefices/DTCLOEXECOMPTA -> pas présent dans le flux bénéficiaire
--	- Benefices/MTBENAGRI (InformationsAgricoles/MTBENAGRI dans ancien bénéficiaire) -> ConditionExploitantAgricole/MTBENAGRI
--	- Benefices/MTBENAGRIA-1 -> pas présent dans le flux bénéficiaire
--	- Benefices/REGFISAGRI (InformationsAgricoles/REGFISAGRI dans ancien bénéficiaire) -> ConditionExploitantAgricole/REGFISAGRI
--	- Benefices/TOPRESSEVAAGRI -> pas présent dans le flux bénéficiaire
--	- Nouvelle information: ConditionExploitantAgricole/MTREVAGRI
--	- Nouvelle information: ConditionExploitantAgricole/TYPEXPAGRI
--	- Nouvelle information: ConditionExploitantAgricole/NATACTEXPAGRI
--	- Nouvelle information: ConditionExploitantAgricole/PARTEXPAGRI
--	b°) On n'a qu'une seule entrée par flux, historisation possible avec dtbenagri
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'infosagricoles', 'mtrevagri', 'NUMERIC(10,2) DEFAULT NULL' );
COMMENT ON COLUMN infosagricoles.mtrevagri IS 'Montant des revenus agricoles';

SELECT add_missing_table_field ('public', 'infosagricoles', 'typexpagri', 'VARCHAR(4) DEFAULT NULL' );
COMMENT ON COLUMN infosagricoles.typexpagri IS 'Type d''exploitation agricole';

SELECT add_missing_table_field ('public', 'infosagricoles', 'natactexpagri', 'VARCHAR(3) DEFAULT NULL' );
COMMENT ON COLUMN infosagricoles.natactexpagri IS 'Code nature d''activité principale de l''exploitation agricole';

SELECT add_missing_table_field ('public', 'infosagricoles', 'partexpagri', 'NUMERIC(5,2) DEFAULT NULL' );
COMMENT ON COLUMN infosagricoles.partexpagri IS 'Pourcentage de part de l''individu dans l''exploitation agricole';

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisPCGPersonne/Derogation
-- INFO: ces données viennent uniquement de l'ancien flux bénéficiaire (vrsb0801)
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisPCGPersonne/ExclueRSA
-- INFO: RAS, on recrée les colonnes pour les placer en fin de table
-- *****************************************************************************

ALTER TABLE avispcgpersonnes ADD COLUMN nv_excl CHAR(1) DEFAULT NULL;
ALTER TABLE avispcgpersonnes ADD COLUMN nv_ddexcl DATE DEFAULT NULL;
ALTER TABLE avispcgpersonnes ADD COLUMN nv_dfexcl DATE DEFAULT NULL;

UPDATE avispcgpersonnes SET nv_excl = excl, nv_ddexcl = ddexcl, nv_dfexcl = dfexcl;

ALTER TABLE avispcgpersonnes DROP COLUMN excl;
ALTER TABLE avispcgpersonnes DROP COLUMN ddexcl;
ALTER TABLE avispcgpersonnes DROP COLUMN dfexcl;

ALTER TABLE avispcgpersonnes RENAME COLUMN nv_excl TO excl;
ALTER TABLE avispcgpersonnes RENAME COLUMN nv_ddexcl TO ddexcl;
ALTER TABLE avispcgpersonnes RENAME COLUMN nv_dfexcl TO dfexcl;

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisPCGPersonne/Liberalite
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisPCGPersonne/CreanceAlimentaire
-- JOBS: certaines informations ont été supprimées du nouveau flux bénéficiaire:
--	- CreanceAlimentaire/DFCREALIM (ancien bénéficiaire) -> pas présente dans le nouveau bénéficiaire
--      -> il faut que les jobs ajoutent la date du flux comme dfcrealim si ce bloc n'est plus présent et qu'il y avait un ddcrealim
--      -> FIXME: ou DROP de la colonne
--	- CreanceAlimentaire/ETATCREALIM (ancien bénéficiaire, instruction) -> CreanceAlimentaire/ETATCREALIM
--	- CreanceAlimentaire/MTSANCREALIM (ancien bénéficiaire, instruction) -> CreanceAlimentaire/MTSANCREALIM
--	- CreanceAlimentaire/DDCREALIM (ancien bénéficiaire, instruction) -> CreanceAlimentaire/DDCREALIM
--	- CreanceAlimentaire/ORIOBLALIM (ancien bénéficiaire, instruction) -> pas présente dans le nouveau bénéficiaire
--	- CreanceAlimentaire/MOTIDISCREALIM (ancien bénéficiaire, instruction) -> pas présente dans le nouveau bénéficiaire
--	- CreanceAlimentaire/COMMCREALIM (ancien bénéficiaire, instruction) -> pas présente dans le nouveau bénéficiaire
--	- CreanceAlimentaire/ENGPROCCREALIM (instruction)
--	- CreanceAlimentaire/TOPDEMDISPROCCREALIM (instruction)
--	- CreanceAlimentaire/VERSPA (instruction)
--	- CreanceAlimentaire/TOPJUGPA (instruction)
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisCGSSDOMPersonne
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/ContratPCGPersonneRSA
-- JOBS: nouvelles données du flux bénéficiaire
-- *****************************************************************************

DROP TABLE IF EXISTS contratspcgs CASCADE;
CREATE TABLE contratspcgs (
    id				SERIAL NOT NULL PRIMARY KEY,
	personne_id		INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	ddcontrrsa		DATE DEFAULT NULL,
	dfcontrrsa		DATE DEFAULT NULL,
	typecontrrsa	VARCHAR(4) DEFAULT NULL
);

COMMENT ON TABLE contratspcgs IS 'Restitution des informations portant sur le Projet Personnalisé d''Accès à l''Emploi (PPAE) ou le Contrat d''Engagement Réciproque (CER).';
COMMENT ON COLUMN contratspcgs.ddcontrrsa IS 'Date de début du PPAE ou du CER';
COMMENT ON COLUMN contratspcgs.dfcontrrsa IS 'Date de fin du PPAE ou du CER';
COMMENT ON COLUMN contratspcgs.typecontrrsa IS 'Type de contrat d''accompagnement liant le PCG et la personne';

-- On a au plus une entrée par flux, historisation possible avec ddcontrrsa et dfcontrrsa
CREATE INDEX contratspcgs_personne_id_idx ON contratspcgs(personne_id);

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/CalculDroitRSAPersonne
-- JOBS:
--  a°) données présentes uniquement dans le flux bénéficiaire
--  b°) le bloc XML a été renommé de MontantCalculDroitRSAPersonne en CalculDroitRSAPersonne
--  c°) certaines informations ont été ajoutées:
--	- Nouvelle information: CalculDroitRSAPersonne/DDSOUDRODEV
--	- Nouvelle information: CalculDroitRSAPersonne/DFSOUDRODEV
--	- Nouvelle information: CalculDroitRSAPersonne/DTDERCALRSA
--	- MontantCalculDroitRSAPersonne/MTPERSABANEURSA -> CalculDroitRSAPersonne/MTPERSABANEURSA
--	- MontantCalculDroitRSAPersonne/MTPERSRESSMENRSA -> CalculDroitRSAPersonne/MTPERSRESSMENRSA
--	- MontantCalculDroitRSAPersonne/TOPPERSDRODEVORSA -> CalculDroitRSAPersonne/TOPPERSDRODEVORSA
--	- MontantCalculDroitRSAPersonne/TOPPERSENTDRODEVORSA -> CalculDroitRSAPersonne/TOPPERSENTDRODEVORSA
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'calculsdroitsrsa', 'ddsoudrodev', 'DATE DEFAULT NULL' );

SELECT add_missing_table_field ('public', 'calculsdroitsrsa', 'dfsoudrodev', 'DATE DEFAULT NULL' );

SELECT add_missing_table_field ('public', 'calculsdroitsrsa', 'dtdercalrsa', 'DATE DEFAULT NULL' );

COMMENT ON TABLE calculsdroitsrsa IS 'Eléments résultant du calcul du droit au RSA pour le mois de référence traité pour les personnes bénéficiaires du RSA';
COMMENT ON COLUMN calculsdroitsrsa.ddsoudrodev IS 'Date de début d''effet du top droit et devoir de la personne décrite.';
COMMENT ON COLUMN calculsdroitsrsa.dfsoudrodev IS 'Date de fin d''effet du top droit et devoir de la personne décrite.';
COMMENT ON COLUMN calculsdroitsrsa.dtdercalrsa IS 'Date dernier calcul';

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/SituationFamille
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/Adresse
-- *****************************************************************************

--------------------------------------------------------------------------------
-- 29°) a°) Table adressesfoyers
--------------------------------------------------------------------------------

-- RAS

--------------------------------------------------------------------------------
-- 29°) b°) Table adresses
--------------------------------------------------------------------------------

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SituationDossierRSA/EtatDossierRSA
-- JOBS:
--  a°) données présentes uniquement dans le flux bénéficiaire
--  b°) Une information a été ajoutée:
--	- EtatDossierRSA/ETATDOSRSA -> EtatDossierRSA/ETATDOSRSA
--	- Nouvelle donnée: EtatDossierRSA/TOPFORCDRO
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'situationsdossiersrsa', 'topforcdro', 'CHAR(1) DEFAULT ''0''' );
COMMENT ON COLUMN situationsdossiersrsa.topforcdro IS 'Top forcage de droit';

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SituationDossierRSA/DroitOuvert
-- JOBS, WEBRSA: nouveau bloc
--	- Nouvelle donnée: DroitOuvert/DTODRSA
--	- Nouvelle donnée: DroitOuvert/MOTIODRSA
--	- Nouvelle donnée: DroitOuvert/TOPMAINTDRORSA
--	- Nouvelle donnée: DroitOuvert/TOPMTINFSEUIRSA
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'situationsdossiersrsa', 'dtodrsa', 'DATE DEFAULT NULL' );
COMMENT ON COLUMN situationsdossiersrsa.dtodrsa IS 'Date ouverture du droit du RSA';

SELECT add_missing_table_field ('public', 'situationsdossiersrsa', 'motiodrsa', 'VARCHAR(3) DEFAULT NULL' );
COMMENT ON COLUMN situationsdossiersrsa.motiodrsa IS 'Motif actualisé de l''ouverture du RSA';

SELECT add_missing_table_field ('public', 'situationsdossiersrsa', 'topmaintdrorsa', 'CHAR(1) DEFAULT ''0''' );
COMMENT ON COLUMN situationsdossiersrsa.topmaintdrorsa IS 'Indicateur de maintien de droit';

SELECT add_missing_table_field ('public', 'situationsdossiersrsa', 'topmtinfseuirsa', 'CHAR(1) DEFAULT ''0''' );
COMMENT ON COLUMN situationsdossiersrsa.topmtinfseuirsa IS 'Indicateur précisant que le montant versable a été ramené à zéro car inférieur au seuil de versement.';

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SituationDossierRSA/SuspensionDroit
-- JOBS, WEBRSA
--  a°) ces données ne sont présentes que dans le flux bénéficiaire
--  b°) une donnée a été supprimée, des valeurs ont été simplifiées
--	- SuspensionDroit/NATGROUPFSUS -> donnée supprimée (pas utilisée dans l'application)
--	- SuspensionDroit/MOTISUSDRORSA -> SuspensionDroit/MOTISUSDRORSA
--	- SuspensionDroit/DDSUSDRORSA -> SuspensionDroit/DDSUSDRORSA (valeurs simplifiées, pratiquement pas utilisé dans l'application)
-- *****************************************************************************

ALTER TABLE suspensionsdroits ALTER COLUMN motisusdrorsa TYPE VARCHAR(3);

UPDATE suspensionsdroits SET motisusdrorsa = (
	CASE
		WHEN motisusdrorsa IN ('DA', 'DB', 'DG', 'DH', 'DI', 'DJ', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'GE', 'GF', 'GN', 'GP', 'GR', 'GW', 'GX', 'GY', '00', '36', '02', '03', '04', '09', '19', '31', '34', '35', '36', '70', '78', '84', '85', 'AB', 'CZ', 'DA', 'DB', 'DC') THEN 'SCA'
		WHEN motisusdrorsa IN ('DC', 'DD', 'DE', 'DK', 'DS', 'DT', 'GA', 'GS', 'GC', 'GB', 'GT', '63') THEN 'SCO'
		WHEN motisusdrorsa IN ('DF', 'GI') THEN 'SMU'
		WHEN motisusdrorsa IN ('GJ', 'GK', 'GL', '20', '27', '37', '44') THEN 'SCG'
		WHEN motisusdrorsa IN ('06') THEN 'SDT'
		ELSE motisusdrorsa
	END
);

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SituationDossierRSA/RessourcesTropElevees
-- JOBS, WEBRSA:
--  a°) ces données ne sont présentes que dans le flux bénéficiaire
--  b°) la cardinalité: passe de 0,n à 0,1 (de même qu'en base et dans l'application -> dans suspensionsversements)
--	-> un seul enregistrement par situationsdossiersrsa ? -> Oui
--	-> SELECT DISTINCT dossier_id FROM situationsdossiersrsa GROUP BY dossier_id HAVING COUNT(dossier_id) > 1;
--  -> OK@cg58_20150930, OK@cg66_20150318, OK@cg93_20151029, OK@cg976_20150520,
--  c°) des valeurs ont été simplifiées
--  - SuspensionVersement/DDSUSVERSRSA -> RessourcesTropElevees/DDSUSVERSRSA
--  - SuspensionVersement/MOTISUSVERSRSA -> RessourcesTropElevees/MOTISUSVERSRSA
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'situationsdossiersrsa', 'ddsusversrsa', 'DATE DEFAULT NULL' );
SELECT add_missing_table_field ('public', 'situationsdossiersrsa', 'motisusversrsa', 'VARCHAR(3) DEFAULT NULL' );

COMMENT ON COLUMN situationsdossiersrsa.motisusversrsa IS 'Motif de suspension versement RSA';
COMMENT ON COLUMN situationsdossiersrsa.ddsusversrsa IS 'Date début suspension versement RSA';

--------------------------------------------------------------------------------
-- Mise à jour
--------------------------------------------------------------------------------
-- FIXME: vérifier, les données doivent être erronées!
UPDATE situationsdossiersrsa
	SET motisusversrsa = ( CASE
			-- en absence de contrat d’accompagnement valide
			WHEN tmp.motisusversrsa IN ( '01', '97' ) AND NOT tmp.has_contrat THEN 'MRE'
			-- en présence d’un contrat d’accompagnement valide
			WHEN tmp.motisusversrsa IN ( '01', '97' ) AND tmp.has_contrat THEN 'MCO'
			ELSE 'MAU'
		END ),
		ddsusversrsa = tmp.ddsusversrsa
	FROM (
		SELECT
				dossiers.id AS dossier_id,
				situationdossierrsa_id,
				motisusversrsa,
				ddsusversrsa,
				EXISTS(
					SELECT *
						FROM contratsinsertion
							INNER JOIN personnes ON ( contratsinsertion.personne_id = personnes.id )
							INNER JOIN foyers ON ( personnes.foyer_id = foyers.id )
						WHERE
							foyers.dossier_id = dossiers.id
							AND contratsinsertion.decision_ci = 'V'
							AND ddsusversrsa BETWEEN contratsinsertion.dd_ci AND contratsinsertion.df_ci
				) AS has_contrat
			FROM suspensionsversements, dossiers
			ORDER BY ddsusversrsa DESC
-- 			LIMIT 1

	) AS tmp
	WHERE
		tmp.dossier_id = situationsdossiersrsa.dossier_id
		AND tmp.situationdossierrsa_id = situationsdossiersrsa.id;

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SituationDossierRSA/FinDroit
-- JOBS, WEBRSA:
--  a°) ces données ne sont présentes que dans le flux bénéficiaire
--  d°) une information a été ajoutée et des valeurs ont été simplifiées
--  - FinDroit/DTCLORSA -> FinDroit/DTCLORSA
--  - FinDroit/MOTICLORSA -> FinDroit/MOTICLORSA
--  - Nouvelle donnée: FinDroit/TOPANNUOD
-- *****************************************************************************

UPDATE situationsdossiersrsa SET moticlorsa = (
	CASE
		WHEN motirefursa IN ('PCG', 'FDI') THEN 'CCG'
		WHEN motirefursa IN ('ECH') THEN 'CMD'
		WHEN motirefursa IN ('MUT') THEN 'CMU'
		WHEN motirefursa IN ('EFF', 'RFD', 'RAU', 'RST', 'RSO') THEN 'CFD'
		ELSE 'CAU'
	END
);

SELECT add_missing_table_field ('public', 'situationsdossiersrsa', 'topannuod', 'CHAR(1) DEFAULT ''0''' );
COMMENT ON COLUMN situationsdossiersrsa.topannuod IS 'Annulation de l''ouverture du droit.';

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SituationDossierRSA/FinDroitMoisAnterieur
-- JOBS:
--  a°) ces données ne sont présentes que dans le flux bénéficiaire
--  d°) ce bloc a été supprimé car les FinDroit en etatdosrsa 6 sont les mêmes qu'en etatdosrsa 5
--  - FinDroitMoisAnterieur/DTCLORSA -> FinDroit/DTCLORSA
--  - FinDroitMoisAnterieur/MOTICLORSA -> FinDroit/MOTICLORSA
--  - Nouvelle donnée: FinDroit/TOPANNUOD
-- *****************************************************************************

-- RAS hormi ce qui figure ci-dessus

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SpecificiteDOM
-- JOBS:
--   a°) Une partie de ces données est dans le flux instruction mais dans un autre bloc:
--  - PrestationRSA/DDSURFAGRIDOM
--  - PrestationRSA/NBTOTAIDEFAMSURFDOM
--  - PrestationRSA/NBTOTPERSMAJOSURFDOM
--  - PrestationRSA/SURFAGRIDOM
--   b°) Ces données étaient déjà dans l'ancien flux bénéficiaire
--  - SpecificiteDOM/DDSURFAGRIDOM
--  - SpecificiteDOM/NBTOTAIDEFAMSURFDOM
--  - SpecificiteDOM/NBTOTPERSMAJOSURFDOM
--  - SpecificiteDOM/SURFAGRIDOM
--  - SpecificiteDOM/SURFAGRIDOMPLA
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/DetailDroitRSA/TroncCommunDroitRSA
-- JOBS/WEBRSA:
--   a°) Une partie de ces données est dans le flux instruction et l'ancien bénéficiaire mais dans un autre bloc:
--  - GeneraliteRessourcesTrimestre/DDRESS -> TroncCommunDroitRSA/DDRESS (FIXME: faire remonter ?)
--  - GeneraliteRessourcesTrimestre/DDRESS -> TroncCommunDroitRSA/DFRESS (FIXME: faire remonter ?)
--  - GeneraliteRessourcesTrimestre/TOPRESSNUL -> GeneraliteRessourcesTrimestre/TOPRESSNUL (voir plus haut)
--   b°) Suppression de la balise TOPFOYDRODEVORSA (voir patch drop)
--   c°) Une autre partie des balises changent entre l'ancien et le nouveau bénéficiaire:
--  - DetailCalculDroitRSA/DTDERRSAVERS -> TroncCommunDroitRSA/DTDERRSAVERS
--  - DetailCalculDroitRSA/MTRSAVERS -> TroncCommunDroitRSA/MTRSAVERS
--   d°) Une donnée reste au même endroit
--  - TroncCommunDroitRSA/TOPSANSDOMFIXE -> TroncCommunDroitRSA/TOPSANSDOMFIXE
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'detailsdroitsrsa', 'dtderrsavers', 'DATE DEFAULT NULL' );
COMMENT ON COLUMN detailsdroitsrsa.dtderrsavers IS 'Date dernier mois RSA versable';

SELECT add_missing_table_field ('public', 'detailsdroitsrsa', 'mtrsavers', 'NUMERIC(9,2) DEFAULT NULL' );
COMMENT ON COLUMN detailsdroitsrsa.mtrsavers IS 'mtrsavers';

--------------------------------------------------------------------------------
-- Mise à jour
--------------------------------------------------------------------------------

-- FIXME: vérifier, les données doivent être erronées!

UPDATE detailsdroitsrsa
	SET mtrsavers = tmp.mtrsavers,
		dtderrsavers = tmp.dtderrsavers
	FROM (
		SELECT
				detaildroitrsa_id,
				SUM(COALESCE(mtrsavers, 0)) AS mtrsavers,
				dtderrsavers
			FROM detailscalculsdroitsrsa
			GROUP BY detaildroitrsa_id, dtderrsavers
			ORDER BY dtderrsavers DESC
	) AS tmp
	WHERE tmp.detaildroitrsa_id = detailsdroitsrsa.id;

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/DetailDroitRSA/MontantsCalculDroitRSA
-- JOBS:
--   a°) Données déjà présentes, uniquement dans l'ancien bénéficiaire:
--   - MontantsCalculDroitRSA/DDELECAL -> MontantsCalculDroitRSA/DDELECAL
--   - MontantsCalculDroitRSA/DFELECAL -> MontantsCalculDroitRSA/DFELECAL
--   - MontantsCalculDroitRSA/MTABANEURSA -> MontantsCalculDroitRSA/MTABANEURSA
--   - MontantsCalculDroitRSA/MTALRSA -> MontantsCalculDroitRSA/MTALRSA
--   - MontantsCalculDroitRSA/MTCUMINTEGRSA -> MontantsCalculDroitRSA/MTCUMINTEGRSA
--   - MontantsCalculDroitRSA/MTLOCALRSA -> MontantsCalculDroitRSA/MTLOCALRSA
--   - MontantsCalculDroitRSA/MTPENTRSA -> MontantsCalculDroitRSA/MTPENTRSA
--   - MontantsCalculDroitRSA/MTPFRSA -> MontantsCalculDroitRSA/MTPFRSA
--   - MontantsCalculDroitRSA/MTRESSMENRSA -> MontantsCalculDroitRSA/MTRESSMENRSA
--   - MontantsCalculDroitRSA/MTREVGARARSA -> MontantsCalculDroitRSA/MTREVGARARSA
--   - MontantsCalculDroitRSA/MTREVMINIGARARSA -> MontantsCalculDroitRSA/MTREVMINIGARARSA
--   - MontantsCalculDroitRSA/MTTOTDRORSA -> MontantsCalculDroitRSA/MTTOTDRORSA
--	 b°) Certaines informations changent de nom:
--   - MontantsCalculDroitRSA/MTSANOBLALIMRSA -> MontantsCalculDroitRSA/MTSANADMRSA
--   - MontantsCalculDroitRSA/MTREDHOSRSA -> MontantsCalculDroitRSA/MTREDADMRSA
--   - MontantsCalculDroitRSA/MTREDCGRSA -> MontantsCalculDroitRSA/MTSANCGRSA
--	 c°) Certaines informations sont ajoutées:
--   - Nouvelle donnée: MontantsCalculDroitRSA/MTBRUTRSA
--   d°) Une balise bouge de bloc, voir plus haut
--   - TroncCommunDroitRSA/NBENFAUTCHA -> MontantsCalculDroitRSA/NBENFAUTCHA
-- *****************************************************************************

-- mtsanoblalimrsa devient mtsanadmrsa
SELECT add_missing_table_field ('public', 'detailsdroitsrsa', 'mtsanadmrsa', 'numeric(9,2) DEFAULT NULL' );
UPDATE detailsdroitsrsa SET mtsanadmrsa = mtsanoblalimrsa;
COMMENT ON COLUMN detailsdroitsrsa.mtsanadmrsa IS 'Montant des sanctions administratives';

-- mtredhosrsa devient mtredadmrsa
SELECT add_missing_table_field ('public', 'detailsdroitsrsa', 'mtredadmrsa', 'numeric(9,2) DEFAULT NULL' );
UPDATE detailsdroitsrsa SET mtredadmrsa = mtredhosrsa;
COMMENT ON COLUMN detailsdroitsrsa.mtredadmrsa IS 'Montant des réductions pour mesures administratives';

-- mtredcgrsa devient mtsancgrsa
SELECT add_missing_table_field ('public', 'detailsdroitsrsa', 'mtsancgrsa', 'numeric(9,2) DEFAULT NULL' );
UPDATE detailsdroitsrsa SET mtsancgrsa = mtredcgrsa;
COMMENT ON COLUMN detailsdroitsrsa.mtsancgrsa IS 'Montant des sanctions Conseil général RSA';

-- ajout de mtbrutrsa
SELECT add_missing_table_field ('public', 'detailsdroitsrsa', 'mtbrutrsa', 'numeric(9,2) DEFAULT NULL' );
COMMENT ON COLUMN detailsdroitsrsa.mtbrutrsa IS 'Montant total brut de RSA';

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/DetailDroitRSA/DetailCalculDroitRSA/RSASocle
-- INFO:
--   a°) Données déjà présentes, uniquement dans l'ancien bénéficiaire
--   b°) Le fait de scinder en 2 blocs dans le nouveau flux ne change pratiquement
--   rien à ce bloc (sauf la valeur ABS, voir ci-dessous)
--   - DetailCalculDroitRSA/DDNATDRO -> RSASocle/DDNATDRO
--   - DetailCalculDroitRSA/DFNATDRO -> RSASocle/DFNATDRO
--   - DetailCalculDroitRSA/NATPF -> RSASocle/NATPF
--   - DetailCalculDroitRSA/SOUSNATPF -> RSASocle/SOUSNATPF
--   b°) Ajout de nouvelles données
--   - Nouvelles donnée: RSASocle/DTINIRSASOCL
--   - Nouvelles donnée: RSASocle/MTBRUTRSASOCL
--   c°) Données ayant bougé de bloc
--   - DetailCalculDroitRSA/DTDERRSAVERS -> TroncCommunDroitRSA/DTDERRSAVERS
--   - DetailCalculDroitRSA/MTRSAVERS -> TroncCommunDroitRSA/MTRSAVERS
--   d°) Anciennes valeurs possibles (RSA Socle): RSB, RSD, RSI, RSJ, RSU
--   e°) Nouvelles valeurs possibles (RSA Socle): ABS, RSB, RSD, RSI, RSJ, RSU
-- JOBS, ATTENTION: les nouvelles valeur de natpf "ABS" et de SOUSNATPF "ABSEN"
-- ne doivent pas générer d'entrée en base
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'detailsdroitsrsa', 'dtinirsasocl', 'DATE DEFAULT NULL' );
COMMENT ON COLUMN detailsdroitsrsa.dtinirsasocl IS 'Date initiale de présence d''un RSA Socle positif pour la demande en cours.';

SELECT add_missing_table_field ('public', 'detailsdroitsrsa', 'mtbrutrsasocl', 'numeric(9,2) DEFAULT NULL' );
COMMENT ON COLUMN detailsdroitsrsa.mtbrutrsasocl IS 'Montant RSA socle  pour la sous nature traitée, avant application du seuil de versement, sanction PCG et CRDS';

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/DetailDroitRSA/DetailCalculDroitRSA/RSAActivite
-- INFO:
--   a°) Données déjà présentes, uniquement dans l'ancien bénéficiaire
--   b°) Le fait de scinder en 2 blocs dans le nouveau flux ne change pratiquement
--   rien à ce bloc (sauf la valeur ABS, voir ci-dessous)
--   - DetailCalculDroitRSA/DDNATDRO -> RSAActivite/DDNATDRO
--   - DetailCalculDroitRSA/DFNATDRO -> RSAActivite/DFNATDRO
--   - DetailCalculDroitRSA/NATPF -> RSAActivite/NATPF
--   - DetailCalculDroitRSA/SOUSNATPF -> RSAActivite/SOUSNATPF
--   b°) Ajout de nouvelles données
--   - Nouvelles donnée: RSAActivite/DTINIRSAACT
--   - Nouvelles donnée: RSAActivite/MTBRUTRSAACT
--   c°) Données ayant bougé de bloc
--   - DetailCalculDroitRSA/DTDERRSAVERS -> TroncCommunDroitRSA/DTDERRSAVERS
--   - DetailCalculDroitRSA/MTRSAVERS -> TroncCommunDroitRSA/MTRSAVERS
--   d°) Anciennes valeurs possibles (RSA Activité): RCB, RCD, RCI, RCJ, RCU
--   e°) Nouvelles valeurs possibles (RSA Activité): ABS, RCB, RCD, RCI, RCJ, RCU
-- JOBS, ATTENTION: les nouvelles valeur de natpf "ABS" et de SOUSNATPF "ABSEN"
-- ne doivent pas générer d'entrée en base
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'detailsdroitsrsa', 'dtinirsaact', 'DATE DEFAULT NULL' );
COMMENT ON COLUMN detailsdroitsrsa.dtinirsaact IS 'Date initiale de présence d''un RSA Activité positif pour la demande en cours.';

SELECT add_missing_table_field ('public', 'detailsdroitsrsa', 'mtbrutrsaact', 'numeric(9,2) DEFAULT NULL' );
COMMENT ON COLUMN detailsdroitsrsa.mtbrutrsaact IS 'Montant RSA Activité  pour la sous nature traitée, avant application du seuil de versement, sanction PCG et CRDS';

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/DetailDroitRSA/DetailCalculDroitRSA/RSASocle-1
-- *****************************************************************************

-- TODO: on pourra historiser ces données
-- RSASocle-1 -> Présent si  pour une période précédente, la sous nature du  RSA Socle était différente.
-- Concerne les champs natpf, sousnatpf, ddnatdro, dfnatdro et mtbrutrsasocl

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/DetailDroitRSA/DetailCalculDroitRSA/RSAActivite-1
-- *****************************************************************************

-- TODO: on pourra historiser ces données
-- RSAActivite-1 -> Présent si  pour une période précédente, la sous nature du  RSA Activité était différente.
-- Concerne les champs natpf, sousnatpf, ddnatdro, dfnatdro et mtbrutrsaact

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/AvisPCGDroitRSA/ConditionAdministrative
-- JOBS:
--   a°) Données uniquement présentes dans l'ancien bénéficiaire
--   - ConditionAdministrative/AVISCONDADMRSA -> ConditionAdministrative/AVISCONDADMRSA
--   - ConditionAdministrative/COMM1CONDADMRSA -> ConditionAdministrative/COMM1CONDADMRSA
--   - ConditionAdministrative/COMM2CONDADMRSA -> ConditionAdministrative/COMM2CONDADMRSA
--   - ConditionAdministrative/DTEFFAVISCONDADMRSA -> ConditionAdministrative/DTEFFAVISCONDADMRSA
--   - ConditionAdministrative/MOTICONDADMRSA -> ConditionAdministrative/MOTICONDADMRSA
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/PrestationRSA/DetailDroitRSA/AvisPCGDroitRSA/ReductionRSA
-- JOBS:
--   a°) Données uniquement présentes dans l'ancien bénéficiaire
--   b°) Données plus envoyées, suppression de la table (voir patch drop)
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/AvisPCGDroitRSA/PaiementTiers
-- JOBS:
--   a°) Données uniquement présentes dans l'ancien bénéficiaire
--   - PaiementTiers/AVISDESTPAIRSA -> PaiementTiers/AVISDESTPAIRSA
--   - PaiementTiers/DTAVISDESTPAIRSA -> PaiementTiers/DTAVISDESTPAIRSA
--   - PaiementTiers/NOMTIE -> PaiementTiers/NOMTIE
--   - PaiementTiers/TYPEPERSTIE -> PaiementTiers/TYPEPERSTIE
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Evenement/SessionEvenement
--   a°) Données uniquement présentes dans l'ancien bénéficiaire
--   b°) Des données changent de (nom de ) bloc
--   - Evenement/DTLIQ -> SessionEvenement/DTLIQ
--   - Evenement/FG -> change de bloc, voir point 46
--   - Evenement/HEULIQ -> SessionEvenement/HEULIQ
--   c°) Des données sont ajoutées
--   - Nouvelle donnée: SessionEvenement/DDEFFLIQ
--   - Nouvelle donnée: SessionEvenement/DFEFFLIQ
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'evenements', 'ddeffliq', 'DATE DEFAULT NULL' );
COMMENT ON COLUMN evenements.ddeffliq IS 'Date de début d''effet de la prise en compte des nouvelles informations';

SELECT add_missing_table_field ('public', 'evenements', 'dfeffliq', 'DATE DEFAULT NULL' );
COMMENT ON COLUMN evenements.dfeffliq IS 'Date de fin d''effet de la prise en compte des nouvelles informations';

-- *****************************************************************************
-- /InfosFoyerRSA/Evenement/ListeMotifsEvenement
--   a°) Données uniquement présentes dans l'ancien bénéficiaire
--   b°) Des données changent de (nom de) bloc et de valeurs
--   - Evenement/FG -> ListeMotifsEvenement/MOTITRANSFLUX
-- *****************************************************************************

DROP TABLE IF EXISTS motifsevenements CASCADE;
CREATE TABLE motifsevenements (
    id				SERIAL NOT NULL PRIMARY KEY,
	evenement_id	INTEGER NOT NULL REFERENCES evenements(id) ON DELETE CASCADE ON UPDATE CASCADE,
	motitransflux	VARCHAR(6) DEFAULT NULL
);

COMMENT ON TABLE motifsevenements IS 'Liste des motifs de transmission du dossier dans le flux quotidien.';
COMMENT ON COLUMN motifsevenements.motitransflux IS 'Motif de transmission du flux quotidien';

CREATE INDEX motifsevenements_evenement_id_idx ON motifsevenements(evenement_id);

--------------------------------------------------------------------------------
-- Remplissage
--------------------------------------------------------------------------------

INSERT INTO motifsevenements (evenement_id, motitransflux)
	SELECT
			id AS evenement_id,
			(
				CASE
					WHEN fg = 'ADR' THEN 'CHADRE'
					WHEN fg = 'CARRSA' THEN 'CHCARE'
					WHEN fg = 'HOSPLA' THEN 'CHCARE'
					WHEN fg = 'RECPEN' THEN 'CHCARE'
					WHEN fg = 'TITPEN' THEN 'CHCARE'
					WHEN fg = 'CREALI' THEN 'CHCARE'
					WHEN fg IN ('ASF', 'DEMASF') THEN 'CHCARE'
					WHEN fg = 'JUSRSAJEU' THEN 'CHCARE'
					WHEN fg = 'SURPONEXP' THEN 'CHCARE'
					WHEN fg = 'AIDFAM' THEN 'CHCARE'
					WHEN fg = 'REDDRO' THEN 'CHCARE'
					WHEN fg = 'DESALL' THEN 'CHRDOS'
					WHEN fg = 'RESDOS' THEN 'CHRDOS'
					WHEN fg IN ('ETACIV', 'NATTITSEJ') THEN 'CHFAMI'
					WHEN fg IN ('SITFAM', 'CHASITFAM') THEN 'CHFAMI'
					WHEN fg = 'INTGRO' THEN 'CHFAMI'
					WHEN fg = 'IDEPER' THEN 'CHFAMI'
					WHEN fg = 'MODPER' THEN 'CHFAMI'
					WHEN fg = 'EXAPRE' THEN 'CHFAMI'
					WHEN fg = 'LIEPAR' THEN 'CHFAMI'
					WHEN fg = 'SITPRO' THEN 'CHSTPE'
					WHEN fg IN ('SITENF', 'SITENFAUT') THEN 'CHSTPE'
					WHEN fg = 'PROACC' THEN 'CONACC'
					WHEN fg = 'RAD' THEN 'CLOTUR'
					WHEN fg = 'DEMRSA' THEN 'CREDEM'
					WHEN fg = 'PROPCG' THEN 'DDEPCG'
					WHEN fg = 'DECDEMPCG' THEN 'DECPCG'
					WHEN fg = 'EXCPRE' THEN 'DECPCG'
					WHEN fg = 'DERPRE' THEN 'DECPCG'
					WHEN fg = 'RESTRIRSA' THEN 'DECRES'
					WHEN fg = 'RESEVAETI' THEN 'DECRES'
					WHEN fg = 'ABANEURES' THEN 'DECRES'
					WHEN fg = 'MUT' THEN 'MUTATI'
					WHEN fg = 'SANRSA' THEN 'SANPCG'
					WHEN fg = 'SUS' THEN 'SUSADM'
					WHEN fg IN ('ENTDED', 'CIRMA', 'SUIRMA', 'JUSACT', 'REA') THEN fg
					ELSE TRIM( BOTH ' ' FROM SUBSTRING( fg FROM 1 FOR 6 ) )
				END
			) AS motitransflux
		FROM evenements;

-- *****************************************************************************
-- /InfosFoyerRSA/Anomalie
-- JOBS:
--   a°) Données présentes dans l'ancien bénéficiaire, les flux créance et financier
--   - Anomalie/LIBANO -> Anomalie/LIBANO
--   b°) libano devrait être VARCHAR(200), mais par sécurité il passe en TEXT
-- *****************************************************************************

ALTER TABLE anomalies ALTER COLUMN libano TYPE TEXT;

-- *****************************************************************************
-- /TransmissionFlux
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'transmissionsflux', 'nbcaratransm', 'BIGINT DEFAULT NULL' );
COMMENT ON COLUMN transmissionsflux.nbcaratransm IS 'Nombre de caractères transmis';

-- *****************************************************************************
COMMIT;
SELECT NOW();
-- *****************************************************************************