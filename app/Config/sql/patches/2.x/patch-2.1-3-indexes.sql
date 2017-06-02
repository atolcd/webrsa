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

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table actionscandidats.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS actionscandidats_contactpartenaire_id_idx;
CREATE INDEX actionscandidats_contactpartenaire_id_idx ON actionscandidats( contactpartenaire_id );
DROP INDEX IF EXISTS actionscandidats_chargeinsertion_id_idx;
CREATE INDEX actionscandidats_chargeinsertion_id_idx ON actionscandidats( chargeinsertion_id );
DROP INDEX IF EXISTS actionscandidats_secretaire_id_idx;
CREATE INDEX actionscandidats_secretaire_id_idx ON actionscandidats( secretaire_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table bilansparcours66.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS bilansparcours66_typeorientprincipale_id_idx;
CREATE INDEX bilansparcours66_typeorientprincipale_id_idx ON bilansparcours66( typeorientprincipale_id );
DROP INDEX IF EXISTS bilansparcours66_nvtypeorient_id_idx;
CREATE INDEX bilansparcours66_nvtypeorient_id_idx ON bilansparcours66( nvtypeorient_id );
DROP INDEX IF EXISTS bilansparcours66_nvstructurereferente_id_idx;
CREATE INDEX bilansparcours66_nvstructurereferente_id_idx ON bilansparcours66( nvstructurereferente_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table codesromemetiersdsps66.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS codesromemetiersdsps66_coderomesecteurdsp66_id_idx;
CREATE INDEX codesromemetiersdsps66_coderomesecteurdsp66_id_idx ON codesromemetiersdsps66( coderomesecteurdsp66_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table commissionseps_membreseps.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS commissionseps_membreseps_reponsesuppleant_id_idx;
CREATE INDEX commissionseps_membreseps_reponsesuppleant_id_idx ON commissionseps_membreseps( reponsesuppleant_id );
DROP INDEX IF EXISTS commissionseps_membreseps_presencesuppleant_id_idx;
CREATE INDEX commissionseps_membreseps_presencesuppleant_id_idx ON commissionseps_membreseps( presencesuppleant_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table contratsinsertion.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS contratsinsertion_avenant_id_idx;
CREATE INDEX contratsinsertion_avenant_id_idx ON contratsinsertion( avenant_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionsdefautsinsertionseps66.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionsdefautsinsertionseps66_referent_id_idx;
CREATE INDEX decisionsdefautsinsertionseps66_referent_id_idx ON decisionsdefautsinsertionseps66( referent_id );
DROP INDEX IF EXISTS decisionsdefautsinsertionseps66_passagecommissionep_id_idx;
CREATE INDEX decisionsdefautsinsertionseps66_passagecommissionep_id_idx ON decisionsdefautsinsertionseps66( passagecommissionep_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionsdossierspcgs66.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionsdossierspcgs66_decisionpdo_id_idx;
CREATE INDEX decisionsdossierspcgs66_decisionpdo_id_idx ON decisionsdossierspcgs66( decisionpdo_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionsnonorientationsproseps58.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionsnonorientationsproseps58_passagecommissionep_id_idx;
CREATE INDEX decisionsnonorientationsproseps58_passagecommissionep_id_idx ON decisionsnonorientationsproseps58( passagecommissionep_id );
DROP INDEX IF EXISTS decisionsnonorientationsproseps58_referent_id_idx;
CREATE INDEX decisionsnonorientationsproseps58_referent_id_idx ON decisionsnonorientationsproseps58( referent_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionsnonorientationsproseps93.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionsnonorientationsproseps93_passagecommissionep_id_idx;
CREATE INDEX decisionsnonorientationsproseps93_passagecommissionep_id_idx ON decisionsnonorientationsproseps93( passagecommissionep_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionsproposnonorientationsproscovs58.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionsproposnonorientationsproscovs58_typeorient_id_idx;
CREATE INDEX decisionsproposnonorientationsproscovs58_typeorient_id_idx ON decisionsproposnonorientationsproscovs58( typeorient_id );
DROP INDEX IF EXISTS decisionsproposnonorientationsproscovs58_structurereferente_id_idx;
CREATE INDEX decisionsproposnonorientationsproscovs58_structurereferente_id_idx ON decisionsproposnonorientationsproscovs58( structurereferente_id );
DROP INDEX IF EXISTS decisionsproposnonorientationsproscovs58_referent_id_idx;
CREATE INDEX decisionsproposnonorientationsproscovs58_referent_id_idx ON decisionsproposnonorientationsproscovs58( referent_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionsproposorientationscovs58.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionsproposorientationscovs58_typeorient_id_idx;
CREATE INDEX decisionsproposorientationscovs58_typeorient_id_idx ON decisionsproposorientationscovs58( typeorient_id );
DROP INDEX IF EXISTS decisionsproposorientationscovs58_structurereferente_id_idx;
CREATE INDEX decisionsproposorientationscovs58_structurereferente_id_idx ON decisionsproposorientationscovs58( structurereferente_id );
DROP INDEX IF EXISTS decisionsproposorientationscovs58_referent_id_idx;
CREATE INDEX decisionsproposorientationscovs58_referent_id_idx ON decisionsproposorientationscovs58( referent_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionsregressionsorientationseps58.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionsregressionsorientationseps58_passagecommissionep_id_idx;
CREATE INDEX decisionsregressionsorientationseps58_passagecommissionep_id_idx ON decisionsregressionsorientationseps58( passagecommissionep_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionsreorientationseps93.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionsreorientationseps93_referent_id_idx;
CREATE INDEX decisionsreorientationseps93_referent_id_idx ON decisionsreorientationseps93( referent_id );
DROP INDEX IF EXISTS decisionsreorientationseps93_passagecommissionep_id_idx;
CREATE INDEX decisionsreorientationseps93_passagecommissionep_id_idx ON decisionsreorientationseps93( passagecommissionep_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionssaisinesbilansparcourseps66.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionssaisinesbilansparcourseps66_referent_id_idx;
CREATE INDEX decisionssaisinesbilansparcourseps66_referent_id_idx ON decisionssaisinesbilansparcourseps66( referent_id );
DROP INDEX IF EXISTS decisionssaisinesbilansparcourseps66_passagecommissionep_id_idx;
CREATE INDEX decisionssaisinesbilansparcourseps66_passagecommissionep_id_idx ON decisionssaisinesbilansparcourseps66( passagecommissionep_id );
DROP INDEX IF EXISTS decisionssaisinesbilansparcourseps66_typeorientprincipale_id_idx;
CREATE INDEX decisionssaisinesbilansparcourseps66_typeorientprincipale_id_idx ON decisionssaisinesbilansparcourseps66( typeorientprincipale_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionssaisinespdoseps66.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionssaisinespdoseps66_passagecommissionep_id_idx;
CREATE INDEX decisionssaisinespdoseps66_passagecommissionep_id_idx ON decisionssaisinespdoseps66( passagecommissionep_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionssanctionseps58.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionssanctionseps58_passagecommissionep_id_idx;
CREATE INDEX decisionssanctionseps58_passagecommissionep_id_idx ON decisionssanctionseps58( passagecommissionep_id );
DROP INDEX IF EXISTS decisionssanctionseps58_autrelistesanctionep58_id_idx;
CREATE INDEX decisionssanctionseps58_autrelistesanctionep58_id_idx ON decisionssanctionseps58( autrelistesanctionep58_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionssanctionsrendezvouseps58.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionssanctionsrendezvouseps58_listesanctionep58_id_idx;
CREATE INDEX decisionssanctionsrendezvouseps58_listesanctionep58_id_idx ON decisionssanctionsrendezvouseps58( listesanctionep58_id );
DROP INDEX IF EXISTS decisionssanctionsrendezvouseps58_autrelistesanctionep58_id_idx;
CREATE INDEX decisionssanctionsrendezvouseps58_autrelistesanctionep58_id_idx ON decisionssanctionsrendezvouseps58( autrelistesanctionep58_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table decisionstraitementspcgs66.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS decisionstraitementspcgs66_traitementpcg66_id_idx;
CREATE INDEX decisionstraitementspcgs66_traitementpcg66_id_idx ON decisionstraitementspcgs66( traitementpcg66_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table dossierspcgs66.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS dossierspcgs66_bilanparcours66_id_idx;
CREATE INDEX dossierspcgs66_bilanparcours66_id_idx ON dossierspcgs66( bilanparcours66_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table dsps.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS dsps_libderact66_metier_id_idx;
CREATE INDEX dsps_libderact66_metier_id_idx ON dsps( libderact66_metier_id );
DROP INDEX IF EXISTS dsps_libsecactderact66_secteur_id_idx;
CREATE INDEX dsps_libsecactderact66_secteur_id_idx ON dsps( libsecactderact66_secteur_id );
DROP INDEX IF EXISTS dsps_libactdomi66_metier_id_idx;
CREATE INDEX dsps_libactdomi66_metier_id_idx ON dsps( libactdomi66_metier_id );
DROP INDEX IF EXISTS dsps_libsecactdomi66_secteur_id_idx;
CREATE INDEX dsps_libsecactdomi66_secteur_id_idx ON dsps( libsecactdomi66_secteur_id );
DROP INDEX IF EXISTS dsps_libemploirech66_metier_id_idx;
CREATE INDEX dsps_libemploirech66_metier_id_idx ON dsps( libemploirech66_metier_id );
DROP INDEX IF EXISTS dsps_libsecactrech66_secteur_id_idx;
CREATE INDEX dsps_libsecactrech66_secteur_id_idx ON dsps( libsecactrech66_secteur_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table dsps_revs.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS dsps_revs_libderact66_metier_id_idx;
CREATE INDEX dsps_revs_libderact66_metier_id_idx ON dsps_revs( libderact66_metier_id );
DROP INDEX IF EXISTS dsps_revs_libsecactderact66_secteur_id_idx;
CREATE INDEX dsps_revs_libsecactderact66_secteur_id_idx ON dsps_revs( libsecactderact66_secteur_id );
DROP INDEX IF EXISTS dsps_revs_libactdomi66_metier_id_idx;
CREATE INDEX dsps_revs_libactdomi66_metier_id_idx ON dsps_revs( libactdomi66_metier_id );
DROP INDEX IF EXISTS dsps_revs_libsecactdomi66_secteur_id_idx;
CREATE INDEX dsps_revs_libsecactdomi66_secteur_id_idx ON dsps_revs( libsecactdomi66_secteur_id );
DROP INDEX IF EXISTS dsps_revs_libemploirech66_metier_id_idx;
CREATE INDEX dsps_revs_libemploirech66_metier_id_idx ON dsps_revs( libemploirech66_metier_id );
DROP INDEX IF EXISTS dsps_revs_libsecactrech66_secteur_id_idx;
CREATE INDEX dsps_revs_libsecactrech66_secteur_id_idx ON dsps_revs( libsecactrech66_secteur_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table entretiens.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS entretiens_objetentretien_id_idx;
CREATE INDEX entretiens_objetentretien_id_idx ON entretiens( objetentretien_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table nonorientationsproseps58.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS nonorientationsproseps58_user_id_idx;
CREATE INDEX nonorientationsproseps58_user_id_idx ON nonorientationsproseps58( user_id );
DROP INDEX IF EXISTS nonorientationsproseps58_decisionpropononorientationprocov58_id_idx;
CREATE INDEX nonorientationsproseps58_decisionpropononorientationprocov58_id_idx ON nonorientationsproseps58( decisionpropononorientationprocov58_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table nonorientationsproseps93.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS nonorientationsproseps93_user_id_idx;
CREATE INDEX nonorientationsproseps93_user_id_idx ON nonorientationsproseps93( user_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table nonrespectssanctionseps93.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS nonrespectssanctionseps93_historiqueetatpe_id_idx;
CREATE INDEX nonrespectssanctionseps93_historiqueetatpe_id_idx ON nonrespectssanctionseps93( historiqueetatpe_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table orientsstructs.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS orientsstructs_structureorientante_id_idx;
CREATE INDEX orientsstructs_structureorientante_id_idx ON orientsstructs( structureorientante_id );
DROP INDEX IF EXISTS orientsstructs_referentorientant_id_idx;
CREATE INDEX orientsstructs_referentorientant_id_idx ON orientsstructs( referentorientant_id );
DROP INDEX IF EXISTS orientsstructs_user_id_idx;
CREATE INDEX orientsstructs_user_id_idx ON orientsstructs( user_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table passagescommissionseps.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS passagescommissionseps_commissionep_id_idx;
CREATE INDEX passagescommissionseps_commissionep_id_idx ON passagescommissionseps( commissionep_id );
DROP INDEX IF EXISTS passagescommissionseps_dossierep_id_idx;
CREATE INDEX passagescommissionseps_dossierep_id_idx ON passagescommissionseps( dossierep_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table proposcontratsinsertioncovs58.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS proposcontratsinsertioncovs58_avenant_id_idx;
CREATE INDEX proposcontratsinsertioncovs58_avenant_id_idx ON proposcontratsinsertioncovs58( avenant_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table proposorientationscovs58.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS proposorientationscovs58_user_id_idx;
CREATE INDEX proposorientationscovs58_user_id_idx ON proposorientationscovs58( user_id );
DROP INDEX IF EXISTS proposorientationscovs58_covreferent_id_idx;
CREATE INDEX proposorientationscovs58_covreferent_id_idx ON proposorientationscovs58( covreferent_id );
DROP INDEX IF EXISTS proposorientationscovs58_structureorientante_id_idx;
CREATE INDEX proposorientationscovs58_structureorientante_id_idx ON proposorientationscovs58( structureorientante_id );
DROP INDEX IF EXISTS proposorientationscovs58_referentorientant_id_idx;
CREATE INDEX proposorientationscovs58_referentorientant_id_idx ON proposorientationscovs58( referentorientant_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table reorientationseps93.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS reorientationseps93_referent_id_idx;
CREATE INDEX reorientationseps93_referent_id_idx ON reorientationseps93( referent_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table saisinesbilansparcourseps66.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS saisinesbilansparcourseps66_typeorientprincipale_id_idx;
CREATE INDEX saisinesbilansparcourseps66_typeorientprincipale_id_idx ON saisinesbilansparcourseps66( typeorientprincipale_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table saisinespdoseps66.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS saisinespdoseps66_traitementpcg66_id_idx;
CREATE INDEX saisinespdoseps66_traitementpcg66_id_idx ON saisinespdoseps66( traitementpcg66_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table sanctionseps58.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS sanctionseps58_dossierep_id_idx;
CREATE INDEX sanctionseps58_dossierep_id_idx ON sanctionseps58( dossierep_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table signalementseps93.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS signalementseps93_dossierep_id_idx;
CREATE INDEX signalementseps93_dossierep_id_idx ON signalementseps93( dossierep_id );

-- -----------------------------------------------------------------------------
-- Ajout des indexes pour la table traitementspcgs66.
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS traitementspcgs66_compofoyerpcg66_id_idx;
CREATE INDEX traitementspcgs66_compofoyerpcg66_id_idx ON traitementspcgs66( compofoyerpcg66_id );
DROP INDEX IF EXISTS traitementspcgs66_personnepcg66_situationpdo_id_idx;
CREATE INDEX traitementspcgs66_personnepcg66_situationpdo_id_idx ON traitementspcgs66( personnepcg66_situationpdo_id );

-- -----------------------------------------------------------------------------
-- Ajout et modification d'indexes pour les performances
-- -----------------------------------------------------------------------------
DROP INDEX IF EXISTS jetons_dossier_id_idx;
CREATE UNIQUE INDEX jetons_dossier_id_idx ON jetons (dossier_id);

DROP INDEX IF EXISTS foyers_dossier_rsa_id_idx;

DROP INDEX IF EXISTS informationspe_join_personnes_nir_dtnai_idx;
CREATE UNIQUE INDEX informationspe_join_personnes_nir_dtnai_idx ON informationspe ( SUBSTRING( nir FROM 1 FOR 13 ), dtnai ) WHERE nir IS NOT NULL;

DROP INDEX IF EXISTS informationspe_join_personnes_nom_prenom_dtnai_idx;
CREATE UNIQUE INDEX informationspe_join_personnes_nom_prenom_dtnai_idx ON informationspe ( TRIM( BOTH ' ' FROM nom ), TRIM( BOTH ' ' FROM prenom ), dtnai );

DROP INDEX IF EXISTS personnes_join_informationspe_nir_dtnai_idx;
CREATE INDEX personnes_join_informationspe_nir_dtnai_idx ON personnes ( SUBSTRING( nir FROM 1 FOR 13 ), dtnai ) WHERE nir IS NOT NULL;

DROP INDEX IF EXISTS personnes_join_informationspe_nom_prenom_dtnai_idx;
CREATE INDEX personnes_join_informationspe_nom_prenom_dtnai_idx ON personnes ( TRIM( BOTH ' ' FROM nom ), TRIM( BOTH ' ' FROM prenom ), dtnai ) WHERE nom IS NOT NULL AND prenom IS NOT NULL AND dtnai IS NOT NULL AND TRIM( BOTH ' ' FROM nom ) <> '' AND TRIM( BOTH ' ' FROM prenom ) <> '';

DROP INDEX IF EXISTS personnes_trim_nom_idx;
CREATE INDEX personnes_trim_nom_idx ON personnes ( TRIM( BOTH ' ' FROM nom ) );

DROP INDEX IF EXISTS personnes_trim_prenom_idx;
CREATE INDEX personnes_trim_prenom_idx ON personnes ( TRIM( BOTH ' ' FROM prenom ) );

DROP INDEX IF EXISTS personnes_nir13_idx;
CREATE INDEX personnes_nir13_idx ON personnes ( SUBSTRING( nir FROM 1 FOR 13 ) );

DROP INDEX IF EXISTS detailsdroitsrsa_oridemrsa_idx;
CREATE INDEX detailsdroitsrsa_oridemrsa_idx ON detailsdroitsrsa ( oridemrsa );

DROP INDEX IF EXISTS situationsdossiersrsa_etatdosrsa_ouvert_indefini_idx;
CREATE INDEX situationsdossiersrsa_etatdosrsa_ouvert_indefini_idx ON situationsdossiersrsa (dossier_id, etatdosrsa) WHERE etatdosrsa IN ( 'Z', '2', '3', '4' );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************