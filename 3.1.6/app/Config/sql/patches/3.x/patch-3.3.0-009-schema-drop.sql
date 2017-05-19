SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = notice;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
-- Patch SQl lié à la refonte du flux bénéficiaire de suppression de colonnes ou
-- de tables
-- *****************************************************************************
SELECT NOW();
BEGIN;
-- *****************************************************************************

-- *****************************************************************************
-- /IdentificationFlux
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/IdentificationRSA/Organisme
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/IdentificationRSA/Partenaire
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/IdentificationRSA/DemandeRSA
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- 4°bis) /InfosFoyerRSA/IdentificationRSA/DemandeRMI/DateDemandeRMI et
--        /InfosFoyerRSA/IdentificationRSA/DemandeRMI/NumeroInstructionRMI
-- JOBS, WEBRSA:
--  a°) ces données ne sont présentes que dans l'ancien flux bénéficiaire
--  b°) Les données suivantes sont supprimées
--  - DateDemandeRMI/DTDEMRMI
--  - NumeroInstructionRMI/TYPEINSRMI
--  - NumeroInstructionRMI/NUMCOMINSRMI
--  - NumeroInstructionRMI/NUMDOSINSRMI
--  - NumeroInstructionRMI/NUMCLI
--  - NumeroInstructionRMI/NUMAGRINSRMI
--  - NumeroInstructionRMI/NUMDEPINSRMI
--  c°) très peu utilisé dans l'application
-- *****************************************************************************

ALTER TABLE dossiers DROP COLUMN dtdemrmi;
ALTER TABLE dossiers DROP COLUMN typeinsrmi;
ALTER TABLE dossiers DROP COLUMN numcominsrmi;
ALTER TABLE dossiers DROP COLUMN numdosinsrmi;
ALTER TABLE dossiers DROP COLUMN numcli;
ALTER TABLE dossiers DROP COLUMN numagrinsrmi;
ALTER TABLE dossiers DROP COLUMN numdepinsrmi;

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
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/DossierCafMsa
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/FonctionPersonnePFA
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/FonctionPersonneRSA
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Rattachement
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- 11°bis) /InfosFoyerRSA/Personne/DossierPoleEmploi
-- JOBS, WEBRSA:
--  a°) cette donnée n'est présente que dans l'ancien flux bénéficiaire
--  b°) La donnée suivante est supprimée
--  - DossierPoleEmploi/IDASSEDIC
--  c°) un peu utilisée dans les moteurs de recherche et quelques formulaires
--  d°) Dans web-rsa, il faudra se baser sur les données venant de Pôle Emploi
-- *****************************************************************************

ALTER TABLE personnes DROP column idassedic;

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Ressources/GeneraliteRessourcesTrimestre
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Ressources/RessourcesMensuelles/GeneraliteRessourcesMensuelles
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Ressources/RessourcesMensuelles/DetailRessourcesMensuelles
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Ressources/CaracteristiquesEmployabilite
-- *****************************************************************************

DROP TABLE IF EXISTS activites;

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/Grossesse
-- *****************************************************************************

-- Cette information n'est plus envoyée par la CNAF
ALTER TABLE grossesses DROP COLUMN natfingro;

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/MesureProtection/IdentificationMesureProtection
-- *****************************************************************************

-- @see /InfosFoyerRSA/Personne/MesureProtection/MesureProtectionCommun

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/MesureProtection/MesureProtectionCommun
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisPCGPersonne/ConditionNonSalarie
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisPCGPersonne/ConditionExploitantAgricole
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisPCGPersonne/Derogation
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisPCGPersonne/ExclueRSA
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisPCGPersonne/Liberalite
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- 23°bis) /InfosFoyerRSA/Personne/PensionAlimentaire/Asf
-- JOBS, WEBRSA:
--  a°) ces données ne sont présentes que dans l'ancien flux bénéficiaire
--  b°) ATTENTION: une donnée est toujours envoyée dans le flux instruction
--  - Asf/SITASF
--  c°) Les données suivantes sont supprimées
--  - Asf/DDASF
--  - Asf/DFASF
--  - Asf/PARASSOASF
--  d°) très peu utilisé dans l'application
-- *****************************************************************************

ALTER TABLE allocationssoutienfamilial DROP COLUMN ddasf;
ALTER TABLE allocationssoutienfamilial DROP COLUMN dfasf;
ALTER TABLE allocationssoutienfamilial DROP COLUMN parassoasf;

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisPCGPersonne/CreanceAlimentaire
-- *****************************************************************************

-- RAS
-- FIXME: DROP de la colonne dfcrealim ?

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/AvisCGSSDOMPersonne
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/ContratPCGPersonneRSA
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Personne/CalculDroitRSAPersonne
-- *****************************************************************************

-- RAS

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
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SituationDossierRSA/DroitOuvert
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SituationDossierRSA/SuspensionDroit
-- *****************************************************************************

ALTER TABLE suspensionsdroits DROP COLUMN natgroupfsus;

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SituationDossierRSA/RessourcesTropElevees
-- *****************************************************************************

DROP TABLE suspensionsversements;

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SituationDossierRSA/FinDroit
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SituationDossierRSA/FinDroitMoisAnterieur
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/SpecificiteDOM
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/DetailDroitRSA/TroncCommunDroitRSA
-- JOBS: Suppression de la balise TOPFOYDRODEVORSA (voir patch drop)
-- *****************************************************************************

ALTER TABLE detailsdroitsrsa DROP COLUMN topfoydrodevorsa;

ALTER TABLE detailscalculsdroitsrsa DROP COLUMN mtrsavers;

ALTER TABLE detailscalculsdroitsrsa DROP COLUMN dtderrsavers;

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/DetailDroitRSA/MontantsCalculDroitRSA
-- Suppression suite au "renommage" de colonnes
-- *****************************************************************************

ALTER TABLE detailsdroitsrsa DROP COLUMN mtsanoblalimrsa;

ALTER TABLE detailsdroitsrsa DROP COLUMN mtredhosrsa;

ALTER TABLE detailsdroitsrsa DROP COLUMN mtredcgrsa;

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/DetailDroitRSA/DetailCalculDroitRSA/RSASocle
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/DetailDroitRSA/DetailCalculDroitRSA/RSAActivite
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/DetailDroitRSA/DetailCalculDroitRSA/RSASocle-1
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/PrestationRSA/DetailDroitRSA/DetailCalculDroitRSA/RSAActivite-1
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/AvisPCGDroitRSA/ConditionAdministrative
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/PrestationRSA/DetailDroitRSA/AvisPCGDroitRSA/ReductionRSA
-- JOBS, WEBRSA:
--   a°) Données uniquement présentes dans l'ancien bénéficiaire
--   b°) Très peu utilisés dans l'application (une seule vue)
--   c°) Données plus envoyées, suppression de la table
--   - ReductionRSA/DDREDRSA
--   - ReductionRSA/MTREDRSA
--   - ReductionRSA/DFREDRSA
-- *****************************************************************************

DROP TABLE IF EXISTS reducsrsa;

-- *****************************************************************************
-- /InfosFoyerRSA/DonneesAdministratives/AvisPCGDroitRSA/PaiementTiers
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Evenement/SessionEvenement
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /InfosFoyerRSA/Evenement/ListeMotifsEvenement
-- *****************************************************************************

ALTER TABLE evenements DROP COLUMN fg;

-- *****************************************************************************
-- /InfosFoyerRSA/Anomalie
-- *****************************************************************************

-- RAS

-- *****************************************************************************
-- /TransmissionFlux
-- *****************************************************************************

-- RAS

-- *****************************************************************************
COMMIT;
SELECT NOW();
-- *****************************************************************************
