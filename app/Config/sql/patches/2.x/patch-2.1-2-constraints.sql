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
-- Ajout des contraintes pour la table accscreaentr.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE accscreaentr DROP CONSTRAINT accscreaentr_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'accscreaentr', 'accscreaentr_apre_id_fkey', 'apres', 'apre_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table accscreaentr_piecesaccscreaentr.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE accscreaentr_piecesaccscreaentr DROP CONSTRAINT accscreaentr_piecesaccscreaentr_acccreaentr_id_fkey;
SELECT public.add_missing_constraint( 'public', 'accscreaentr_piecesaccscreaentr', 'accscreaentr_piecesaccscreaentr_acccreaentr_id_fkey', 'accscreaentr', 'acccreaentr_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE accscreaentr_piecesaccscreaentr DROP CONSTRAINT accscreaentr_piecesaccscreaentr_pieceacccreaentr_id_fkey;
SELECT public.add_missing_constraint( 'public', 'accscreaentr_piecesaccscreaentr', 'accscreaentr_piecesaccscreaentr_pieceacccreaentr_id_fkey', 'piecesaccscreaentr', 'pieceacccreaentr_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table acqsmatsprofs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE acqsmatsprofs DROP CONSTRAINT acqsmatsprofs_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'acqsmatsprofs', 'acqsmatsprofs_apre_id_fkey', 'apres', 'apre_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table acqsmatsprofs_piecesacqsmatsprofs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE acqsmatsprofs_piecesacqsmatsprofs DROP CONSTRAINT acqsmatsprofs_piecesacqsmatsprofs_acqmatprof_id_fkey;
SELECT public.add_missing_constraint( 'public', 'acqsmatsprofs_piecesacqsmatsprofs', 'acqsmatsprofs_piecesacqsmatsprofs_acqmatprof_id_fkey', 'acqsmatsprofs', 'acqmatprof_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE acqsmatsprofs_piecesacqsmatsprofs DROP CONSTRAINT acqsmatsprofs_piecesacqsmatsprofs_pieceacqmatprof_id_fkey;
SELECT public.add_missing_constraint( 'public', 'acqsmatsprofs_piecesacqsmatsprofs', 'acqsmatsprofs_piecesacqsmatsprofs_pieceacqmatprof_id_fkey', 'piecesacqsmatsprofs', 'pieceacqmatprof_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table actions.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actions DROP CONSTRAINT actions_typeaction_id_fkey;
SELECT public.add_missing_constraint( 'public', 'actions', 'actions_typeaction_id_fkey', 'typesactions', 'typeaction_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table actionscandidats.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actionscandidats DROP CONSTRAINT actionscandidats_referent_id_fkey;
SELECT public.add_missing_constraint( 'public', 'actionscandidats', 'actionscandidats_referent_id_fkey', 'referents', 'referent_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actionscandidats DROP CONSTRAINT actionscandidats_contactpartenaire_id_fk;
SELECT public.add_missing_constraint( 'public', 'actionscandidats', 'actionscandidats_contactpartenaire_id_fkey', 'contactspartenaires', 'contactpartenaire_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actionscandidats DROP CONSTRAINT actionscandidats_chargeinsertion_id_fk;
SELECT public.add_missing_constraint( 'public', 'actionscandidats', 'actionscandidats_chargeinsertion_id_fkey', 'users', 'chargeinsertion_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actionscandidats DROP CONSTRAINT actionscandidats_secretaire_id_fk;
SELECT public.add_missing_constraint( 'public', 'actionscandidats', 'actionscandidats_secretaire_id_fkey', 'users', 'secretaire_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table actionscandidats_personnes.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actionscandidats_personnes DROP CONSTRAINT actionscandidats_personnes_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'actionscandidats_personnes', 'actionscandidats_personnes_personne_id_fkey', 'personnes', 'personne_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actionscandidats_personnes DROP CONSTRAINT actionscandidats_personnes_actioncandidat_id_fkey;
SELECT public.add_missing_constraint( 'public', 'actionscandidats_personnes', 'actionscandidats_personnes_actioncandidat_id_fkey', 'actionscandidats', 'actioncandidat_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actionscandidats_personnes DROP CONSTRAINT actionscandidats_personnes_referent_id_fkey;
SELECT public.add_missing_constraint( 'public', 'actionscandidats_personnes', 'actionscandidats_personnes_referent_id_fkey', 'referents', 'referent_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actionscandidats_personnes DROP CONSTRAINT actionscandidats_personnes_motifsortie_id_fkey;
SELECT public.add_missing_constraint( 'public', 'actionscandidats_personnes', 'actionscandidats_personnes_motifsortie_id_fkey', 'motifssortie', 'motifsortie_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table actionsinsertion.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actionsinsertion DROP CONSTRAINT actionsinsertion_contratinsertion_id_fkey;
SELECT public.add_missing_constraint( 'public', 'actionsinsertion', 'actionsinsertion_contratinsertion_id_fkey', 'contratsinsertion', 'contratinsertion_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table activites.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE activites DROP CONSTRAINT activites_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'activites', 'activites_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table actsprofs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actsprofs DROP CONSTRAINT actsprofs_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'actsprofs', 'actsprofs_apre_id_fkey', 'apres', 'apre_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actsprofs DROP CONSTRAINT actsprofs_tiersprestataireapre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'actsprofs', 'actsprofs_tiersprestataireapre_id_fkey', 'tiersprestatairesapres', 'tiersprestataireapre_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table actsprofs_piecesactsprofs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actsprofs_piecesactsprofs DROP CONSTRAINT actsprofs_piecesactsprofs_actprof_id_fkey;
SELECT public.add_missing_constraint( 'public', 'actsprofs_piecesactsprofs', 'actsprofs_piecesactsprofs_actprof_id_fkey', 'actsprofs', 'actprof_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE actsprofs_piecesactsprofs DROP CONSTRAINT actsprofs_piecesactsprofs_pieceactprof_id_fkey;
SELECT public.add_missing_constraint( 'public', 'actsprofs_piecesactsprofs', 'actsprofs_piecesactsprofs_pieceactprof_id_fkey', 'piecesactsprofs', 'pieceactprof_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table adressesfoyers.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE adressesfoyers DROP CONSTRAINT adresses_foyers_adresse_id_fkey;
SELECT public.add_missing_constraint( 'public', 'adressesfoyers', 'adressesfoyers_adresse_id_fkey', 'adresses', 'adresse_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE adressesfoyers DROP CONSTRAINT adresses_foyers_foyer_id_fkey;
SELECT public.add_missing_constraint( 'public', 'adressesfoyers', 'adressesfoyers_foyer_id_fkey', 'foyers', 'foyer_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table aidesagricoles.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE aidesagricoles DROP CONSTRAINT aidesagricoles_infoagricole_id_fkey;
SELECT public.add_missing_constraint( 'public', 'aidesagricoles', 'aidesagricoles_infoagricole_id_fkey', 'infosagricoles', 'infoagricole_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table aidesapres66.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE aidesapres66 DROP CONSTRAINT aidesapres66_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'aidesapres66', 'aidesapres66_apre_id_fkey', 'apres', 'apre_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE aidesapres66 DROP CONSTRAINT aidesapres66_themeapre66_id_fkey;
SELECT public.add_missing_constraint( 'public', 'aidesapres66', 'aidesapres66_themeapre66_id_fkey', 'themesapres66', 'themeapre66_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE aidesapres66 DROP CONSTRAINT aidesapres66_typeaideapre66_id_fkey;
SELECT public.add_missing_constraint( 'public', 'aidesapres66', 'aidesapres66_typeaideapre66_id_fkey', 'typesaidesapres66', 'typeaideapre66_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table aidesapres66_piecesaides66.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE aidesapres66_piecesaides66 DROP CONSTRAINT aidesapres66_piecesaides66_aideapre66_id_fkey;
SELECT public.add_missing_constraint( 'public', 'aidesapres66_piecesaides66', 'aidesapres66_piecesaides66_aideapre66_id_fkey', 'aidesapres66', 'aideapre66_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE aidesapres66_piecesaides66 DROP CONSTRAINT aidesapres66_piecesaides66_pieceaide66_id_fkey;
SELECT public.add_missing_constraint( 'public', 'aidesapres66_piecesaides66', 'aidesapres66_piecesaides66_pieceaide66_id_fkey', 'piecesaides66', 'pieceaide66_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table aidesapres66_piecescomptables66.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE aidesapres66_piecescomptables66 DROP CONSTRAINT aidesapres66_piecescomptables66_aideapre66_id_fkey;
SELECT public.add_missing_constraint( 'public', 'aidesapres66_piecescomptables66', 'aidesapres66_piecescomptables66_aideapre66_id_fkey', 'aidesapres66', 'aideapre66_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE aidesapres66_piecescomptables66 DROP CONSTRAINT aidesapres66_piecescomptables66_piececomptable66_id_fkey;
SELECT public.add_missing_constraint( 'public', 'aidesapres66_piecescomptables66', 'aidesapres66_piecescomptables66_piececomptable66_id_fkey', 'piecescomptables66', 'piececomptable66_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table aidesdirectes.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE aidesdirectes DROP CONSTRAINT aidesdirectes_actioninsertion_id_fkey;
SELECT public.add_missing_constraint( 'public', 'aidesdirectes', 'aidesdirectes_actioninsertion_id_fkey', 'actionsinsertion', 'actioninsertion_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table allocationssoutienfamilial.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE allocationssoutienfamilial DROP CONSTRAINT allocationssoutienfamilial_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'allocationssoutienfamilial', 'allocationssoutienfamilial_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table amenagslogts.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE amenagslogts DROP CONSTRAINT amenagslogts_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'amenagslogts', 'amenagslogts_apre_id_fkey', 'apres', 'apre_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table amenagslogts_piecesamenagslogts.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE amenagslogts_piecesamenagslogts DROP CONSTRAINT amenagslogts_piecesamenagslogts_amenaglogt_id_fkey;
SELECT public.add_missing_constraint( 'public', 'amenagslogts_piecesamenagslogts', 'amenagslogts_piecesamenagslogts_amenaglogt_id_fkey', 'amenagslogts', 'amenaglogt_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE amenagslogts_piecesamenagslogts DROP CONSTRAINT amenagslogts_piecesamenagslogts_pieceamenaglogt_id_fkey;
SELECT public.add_missing_constraint( 'public', 'amenagslogts_piecesamenagslogts', 'amenagslogts_piecesamenagslogts_pieceamenaglogt_id_fkey', 'piecesamenagslogts', 'pieceamenaglogt_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table anomalies.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE anomalies DROP CONSTRAINT anomalies_foyer_id_fkey;
SELECT public.add_missing_constraint( 'public', 'anomalies', 'anomalies_foyer_id_fkey', 'foyers', 'foyer_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table apres.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE apres DROP CONSTRAINT apres_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'apres', 'apres_personne_id_fkey', 'personnes', 'personne_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE apres DROP CONSTRAINT apres_structurereferente_id_fkey;
SELECT public.add_missing_constraint( 'public', 'apres', 'apres_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE apres DROP CONSTRAINT apres_referent_id_fkey;
SELECT public.add_missing_constraint( 'public', 'apres', 'apres_referent_id_fkey', 'referents', 'referent_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table apres_comitesapres.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE apres_comitesapres DROP CONSTRAINT apres_comitesapres_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'apres_comitesapres', 'apres_comitesapres_apre_id_fkey', 'apres', 'apre_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE apres_comitesapres DROP CONSTRAINT apres_comitesapres_comiteapre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'apres_comitesapres', 'apres_comitesapres_comiteapre_id_fkey', 'comitesapres', 'comiteapre_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table apres_etatsliquidatifs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE apres_etatsliquidatifs DROP CONSTRAINT apres_etatsliquidatifs_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'apres_etatsliquidatifs', 'apres_etatsliquidatifs_apre_id_fkey', 'apres', 'apre_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE apres_etatsliquidatifs DROP CONSTRAINT apres_etatsliquidatifs_etatliquidatif_id_fkey;
SELECT public.add_missing_constraint( 'public', 'apres_etatsliquidatifs', 'apres_etatsliquidatifs_etatliquidatif_id_fkey', 'etatsliquidatifs', 'etatliquidatif_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table apres_piecesapre.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE apres_piecesapre DROP CONSTRAINT apres_piecesapre_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'apres_piecesapre', 'apres_piecesapre_apre_id_fkey', 'apres', 'apre_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE apres_piecesapre DROP CONSTRAINT apres_piecesapre_pieceapre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'apres_piecesapre', 'apres_piecesapre_pieceapre_id_fkey', 'piecesapre', 'pieceapre_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table autresavisradiation.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE autresavisradiation DROP CONSTRAINT autresavisradiation_contratinsertion_id_fkey;
SELECT public.add_missing_constraint( 'public', 'autresavisradiation', 'autresavisradiation_contratinsertion_id_fkey', 'contratsinsertion', 'contratinsertion_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table autresavissuspension.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE autresavissuspension DROP CONSTRAINT autresavissuspension_contratinsertion_id_fkey;
SELECT public.add_missing_constraint( 'public', 'autresavissuspension', 'autresavissuspension_contratinsertion_id_fkey', 'contratsinsertion', 'contratinsertion_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table avispcgdroitsrsa.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE avispcgdroitsrsa DROP CONSTRAINT avispcgdroitrsa_dossier_rsa_id_fkey;
SELECT public.add_missing_constraint( 'public', 'avispcgdroitsrsa', 'avispcgdroitsrsa_dossier_id_fkey', 'dossiers', 'dossier_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table avispcgpersonnes.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE avispcgpersonnes DROP CONSTRAINT avispcgpersonnes_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'avispcgpersonnes', 'avispcgpersonnes_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table bilansparcours66.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE bilansparcours66 DROP CONSTRAINT bilansparcours66_structurereferente_id_fk;
SELECT public.add_missing_constraint( 'public', 'bilansparcours66', 'bilansparcours66_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table calculsdroitsrsa.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE calculsdroitsrsa DROP CONSTRAINT calculsdroitsrsa_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'calculsdroitsrsa', 'calculsdroitsrsa_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table comitesapres_participantscomites.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE comitesapres_participantscomites DROP CONSTRAINT comitesapres_participantscomites_comiteapre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'comitesapres_participantscomites', 'comitesapres_participantscomites_comiteapre_id_fkey', 'comitesapres', 'comiteapre_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE comitesapres_participantscomites DROP CONSTRAINT comitesapres_participantscomites_participantcomite_id_fkey;
SELECT public.add_missing_constraint( 'public', 'comitesapres_participantscomites', 'comitesapres_participantscomites_participantcomite_id_fkey', 'participantscomites', 'participantcomite_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table commissionseps_membreseps.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE commissionseps_membreseps DROP CONSTRAINT membreseps_seanceseps_seanceep_id_fkey;
SELECT public.add_missing_constraint( 'public', 'commissionseps_membreseps', 'commissionseps_membreseps_commissionep_id_fkey', 'commissionseps', 'commissionep_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE commissionseps_membreseps DROP CONSTRAINT membreseps_seanceseps_membreep_id_fkey;
SELECT public.add_missing_constraint( 'public', 'commissionseps_membreseps', 'commissionseps_membreseps_membreep_id_fkey', 'membreseps', 'membreep_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table condsadmins.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE condsadmins DROP CONSTRAINT condsadmins_avispcgdroitrsa_id_fkey;
SELECT public.add_missing_constraint( 'public', 'condsadmins', 'condsadmins_avispcgdroitrsa_id_fkey', 'avispcgdroitsrsa', 'avispcgdroitrsa_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table connections.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE connections DROP CONSTRAINT connections_user_id_fkey;
SELECT public.add_missing_constraint( 'public', 'connections', 'connections_user_id_fkey', 'users', 'user_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table contactspartenaires.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE contactspartenaires DROP CONSTRAINT contactspartenaires_partenaire_id_fkey;
SELECT public.add_missing_constraint( 'public', 'contactspartenaires', 'contactspartenaires_partenaire_id_fkey', 'partenaires', 'partenaire_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table contratsinsertion.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE contratsinsertion DROP CONSTRAINT contratsinsertion_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'contratsinsertion', 'contratsinsertion_personne_id_fkey', 'personnes', 'personne_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE contratsinsertion DROP CONSTRAINT contratsinsertion_structurereferente_id_fkey;
SELECT public.add_missing_constraint( 'public', 'contratsinsertion', 'contratsinsertion_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE contratsinsertion DROP CONSTRAINT contratsinsertion_typocontrat_id_fkey;
SELECT public.add_missing_constraint( 'public', 'contratsinsertion', 'contratsinsertion_typocontrat_id_fkey', 'typoscontrats', 'typocontrat_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE contratsinsertion DROP CONSTRAINT contratsinsertion_referent_id_fkey;
SELECT public.add_missing_constraint( 'public', 'contratsinsertion', 'contratsinsertion_referent_id_fkey', 'referents', 'referent_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE contratsinsertion DROP CONSTRAINT contratsinsertion_zonegeographique_id_fkey;
SELECT public.add_missing_constraint( 'public', 'contratsinsertion', 'contratsinsertion_zonegeographique_id_fkey', 'zonesgeographiques', 'zonegeographique_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table contratsinsertion_users.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE contratsinsertion_users DROP CONSTRAINT users_contratsinsertion_user_id_fkey;
SELECT public.add_missing_constraint( 'public', 'contratsinsertion_users', 'contratsinsertion_users_user_id_fkey', 'users', 'user_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE contratsinsertion_users DROP CONSTRAINT users_contratsinsertion_contratinsertion_id_fkey;
SELECT public.add_missing_constraint( 'public', 'contratsinsertion_users', 'contratsinsertion_users_contratinsertion_id_fkey', 'contratsinsertion', 'contratinsertion_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table creances.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE creances DROP CONSTRAINT creances_foyer_id_fkey;
SELECT public.add_missing_constraint( 'public', 'creances', 'creances_foyer_id_fkey', 'foyers', 'foyer_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table creancesalimentaires.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE creancesalimentaires DROP CONSTRAINT distfk;
SELECT public.add_missing_constraint( 'public', 'creancesalimentaires', 'creancesalimentaires_personne_id_fkey', 'personnes', 'personne_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table cuis.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE cuis DROP CONSTRAINT cuis_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'cuis', 'cuis_personne_id_fkey', 'personnes', 'personne_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE cuis DROP CONSTRAINT cuis_referent_id_fkey;
SELECT public.add_missing_constraint( 'public', 'cuis', 'cuis_referent_id_fkey', 'referents', 'referent_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE cuis DROP CONSTRAINT cuis_structurereferente_id_fkey;
SELECT public.add_missing_constraint( 'public', 'cuis', 'cuis_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table decisionspropospdos.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE decisionspropospdos DROP CONSTRAINT decisionspropospdos_decisionpdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'decisionspropospdos', 'decisionspropospdos_decisionpdo_id_fkey', 'decisionspdos', 'decisionpdo_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE decisionspropospdos DROP CONSTRAINT decisionspropospdos_propopdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'decisionspropospdos', 'decisionspropospdos_propopdo_id_fkey', 'propospdos', 'propopdo_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table decisionssaisinespdoseps66.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE decisionssaisinespdoseps66 DROP CONSTRAINT nvsepdspdos66_decisionpdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'decisionssaisinespdoseps66', 'decisionssaisinespdoseps66_decisionpdo_id_fkey', 'decisionspdos', 'decisionpdo_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table decisionssanctionsrendezvouseps58.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE decisionssanctionsrendezvouseps58 DROP CONSTRAINT decisionssanctionsrendezvouseps58_listesanctionep58_id_fk;
SELECT public.add_missing_constraint( 'public', 'decisionssanctionsrendezvouseps58', 'decisionssanctionsrendezvouseps58_listesanctionep58_id_fkey', 'listesanctionseps58', 'listesanctionep58_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table derogations.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE derogations DROP CONSTRAINT derogations_avispcgpersonne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'derogations', 'derogations_avispcgpersonne_id_fkey', 'avispcgpersonnes', 'avispcgpersonne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsaccosocfams.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsaccosocfams DROP CONSTRAINT detailsaccosocfams_dsp_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsaccosocfams', 'detailsaccosocfams_dsp_id_fkey', 'dsps', 'dsp_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsaccosocfams_revs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsaccosocfams_revs DROP CONSTRAINT detailsaccosocfams_revs_dsp_rev_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsaccosocfams_revs', 'detailsaccosocfams_revs_dsp_rev_id_fkey', 'dsps_revs', 'dsp_rev_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsaccosocindis.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsaccosocindis DROP CONSTRAINT detailsaccosocindis_dsp_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsaccosocindis', 'detailsaccosocindis_dsp_id_fkey', 'dsps', 'dsp_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsaccosocindis_revs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsaccosocindis_revs DROP CONSTRAINT detailsaccosocindis_revs_dsp_rev_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsaccosocindis_revs', 'detailsaccosocindis_revs_dsp_rev_id_fkey', 'dsps_revs', 'dsp_rev_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailscalculsdroitsrsa.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailscalculsdroitsrsa DROP CONSTRAINT detailscalculsdroitsrsa_detaildroitrsa_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailscalculsdroitsrsa', 'detailscalculsdroitsrsa_detaildroitrsa_id_fkey', 'detailsdroitsrsa', 'detaildroitrsa_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsconforts.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsconforts DROP CONSTRAINT detailsconforts_dsp_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsconforts', 'detailsconforts_dsp_id_fkey', 'dsps', 'dsp_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsconforts_revs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsconforts_revs DROP CONSTRAINT detailsconforts_revs_dsp_rev_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsconforts_revs', 'detailsconforts_revs_dsp_rev_id_fkey', 'dsps_revs', 'dsp_rev_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsdifdisps.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsdifdisps DROP CONSTRAINT detailsdifdisps_dsp_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsdifdisps', 'detailsdifdisps_dsp_id_fkey', 'dsps', 'dsp_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsdifdisps_revs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsdifdisps_revs DROP CONSTRAINT detailsdifdisps_revs_dsp_rev_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsdifdisps_revs', 'detailsdifdisps_revs_dsp_rev_id_fkey', 'dsps_revs', 'dsp_rev_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsdiflogs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsdiflogs DROP CONSTRAINT detailsdiflogs_dsp_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsdiflogs', 'detailsdiflogs_dsp_id_fkey', 'dsps', 'dsp_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsdiflogs_revs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsdiflogs_revs DROP CONSTRAINT detailsdiflogs_revs_dsp_rev_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsdiflogs_revs', 'detailsdiflogs_revs_dsp_rev_id_fkey', 'dsps_revs', 'dsp_rev_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsdifsocpros.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsdifsocpros DROP CONSTRAINT detailsdifsocpros_dsp_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsdifsocpros', 'detailsdifsocpros_dsp_id_fkey', 'dsps', 'dsp_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsdifsocpros_revs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsdifsocpros_revs DROP CONSTRAINT detailsdifsocpros_revs_dsp_rev_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsdifsocpros_revs', 'detailsdifsocpros_revs_dsp_rev_id_fkey', 'dsps_revs', 'dsp_rev_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsdifsocs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsdifsocs DROP CONSTRAINT detailsdifsocs_dsp_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsdifsocs', 'detailsdifsocs_dsp_id_fkey', 'dsps', 'dsp_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsdifsocs_revs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsdifsocs_revs DROP CONSTRAINT detailsdifsocs_revs_dsp_rev_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsdifsocs_revs', 'detailsdifsocs_revs_dsp_rev_id_fkey', 'dsps_revs', 'dsp_rev_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsdroitsrsa.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsdroitsrsa DROP CONSTRAINT detailsdroitsrsa_dossier_rsa_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsdroitsrsa', 'detailsdroitsrsa_dossier_id_fkey', 'dossiers', 'dossier_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsfreinforms.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsfreinforms DROP CONSTRAINT detailsfreinforms_dsp_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsfreinforms', 'detailsfreinforms_dsp_id_fkey', 'dsps', 'dsp_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsfreinforms_revs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsfreinforms_revs DROP CONSTRAINT detailsfreinforms_revs_dsp_rev_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsfreinforms_revs', 'detailsfreinforms_revs_dsp_rev_id_fkey', 'dsps_revs', 'dsp_rev_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsmoytrans.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsmoytrans DROP CONSTRAINT detailsmoytrans_dsp_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsmoytrans', 'detailsmoytrans_dsp_id_fkey', 'dsps', 'dsp_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsmoytrans_revs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsmoytrans_revs DROP CONSTRAINT detailsmoytrans_revs_dsp_rev_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsmoytrans_revs', 'detailsmoytrans_revs_dsp_rev_id_fkey', 'dsps_revs', 'dsp_rev_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsnatmobs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsnatmobs DROP CONSTRAINT detailsnatmobs_dsp_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsnatmobs', 'detailsnatmobs_dsp_id_fkey', 'dsps', 'dsp_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsnatmobs DROP CONSTRAINT detailsnatmobs_dsp_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsnatmobs', 'detailsnatmobs_dsp_id_fkey', 'dsps', 'dsp_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsnatmobs_revs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsnatmobs_revs DROP CONSTRAINT detailsnatmobs_revs_dsp_rev_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsnatmobs_revs', 'detailsnatmobs_revs_dsp_rev_id_fkey', 'dsps_revs', 'dsp_rev_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsprojpros.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsprojpros DROP CONSTRAINT detailsprojpros_dsp_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsprojpros', 'detailsprojpros_dsp_id_fkey', 'dsps', 'dsp_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsprojpros_revs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsprojpros_revs DROP CONSTRAINT detailsprojpros_revs_dsp_rev_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsprojpros_revs', 'detailsprojpros_revs_dsp_rev_id_fkey', 'dsps_revs', 'dsp_rev_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsressourcesmensuelles.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsressourcesmensuelles DROP CONSTRAINT detailsressourcesmensuelles_ressourcemensuelle_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsressourcesmensuelles', 'detailsressourcesmensuelles_ressourcemensuelle_id_fkey', 'ressourcesmensuelles', 'ressourcemensuelle_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table detailsressourcesmensuelles_ressourcesmensuelles.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsressourcesmensuelles_ressourcesmensuelles DROP CONSTRAINT ressourcesmensuelles_detailsre_detailressourcemensuelle_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsressourcesmensuelles_ressourcesmensuelles', 'detailsressourcesmensuelles_ressourcesmensuelles_detailressourcemensuelle_id_fkey', 'detailsressourcesmensuelles', 'detailressourcemensuelle_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE detailsressourcesmensuelles_ressourcesmensuelles DROP CONSTRAINT ressourcesmensuelles_detailsressourc_ressourcemensuelle_id_fkey;
SELECT public.add_missing_constraint( 'public', 'detailsressourcesmensuelles_ressourcesmensuelles', 'detailsressourcesmensuelles_ressourcesmensuelles_ressourcemensuelle_id_fkey', 'ressourcesmensuelles', 'ressourcemensuelle_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table dossierscaf.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE dossierscaf DROP CONSTRAINT dossierscaf_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'dossierscaf', 'dossierscaf_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table dsps.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE dsps DROP CONSTRAINT dsps_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'dsps', 'dsps_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table dsps_revs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE dsps_revs DROP CONSTRAINT dsps_revs_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'dsps_revs', 'dsps_revs_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table entretiens.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE entretiens DROP CONSTRAINT entretiens_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'entretiens', 'entretiens_personne_id_fkey', 'personnes', 'personne_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE entretiens DROP CONSTRAINT entretiens_referent_id_fkey;
SELECT public.add_missing_constraint( 'public', 'entretiens', 'entretiens_referent_id_fkey', 'referents', 'referent_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE entretiens DROP CONSTRAINT entretiens_structurereferente_id_fkey;
SELECT public.add_missing_constraint( 'public', 'entretiens', 'entretiens_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE entretiens DROP CONSTRAINT entretiens_typerdv_id_fkey;
SELECT public.add_missing_constraint( 'public', 'entretiens', 'entretiens_typerdv_id_fkey', 'typesrdv', 'typerdv_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE entretiens DROP CONSTRAINT entretiens_rendezvous_id_fkey;
SELECT public.add_missing_constraint( 'public', 'entretiens', 'entretiens_rendezvous_id_fkey', 'rendezvous', 'rendezvous_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table eps_membreseps.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE eps_membreseps DROP CONSTRAINT eps_membreseps_ep_id_fkey;
SELECT public.add_missing_constraint( 'public', 'eps_membreseps', 'eps_membreseps_ep_id_fkey', 'eps', 'ep_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE eps_membreseps DROP CONSTRAINT eps_membreseps_membreep_id_fkey;
SELECT public.add_missing_constraint( 'public', 'eps_membreseps', 'eps_membreseps_membreep_id_fkey', 'membreseps', 'membreep_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table etatsliquidatifs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE etatsliquidatifs DROP CONSTRAINT etatsliquidatifs_budgetapre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'etatsliquidatifs', 'etatsliquidatifs_budgetapre_id_fkey', 'budgetsapres', 'budgetapre_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table evenements.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE evenements DROP CONSTRAINT evenements_foyer_id_fkey;
SELECT public.add_missing_constraint( 'public', 'evenements', 'evenements_foyer_id_fkey', 'foyers', 'foyer_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table formspermsfimo.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE formspermsfimo DROP CONSTRAINT formspermsfimo_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'formspermsfimo', 'formspermsfimo_apre_id_fkey', 'apres', 'apre_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE formspermsfimo DROP CONSTRAINT formspermsfimo_tiersprestataireapre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'formspermsfimo', 'formspermsfimo_tiersprestataireapre_id_fkey', 'tiersprestatairesapres', 'tiersprestataireapre_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table formspermsfimo_piecesformspermsfimo.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE formspermsfimo_piecesformspermsfimo DROP CONSTRAINT formspermsfimo_piecesformspermsfimo_formpermfimo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'formspermsfimo_piecesformspermsfimo', 'formspermsfimo_piecesformspermsfimo_formpermfimo_id_fkey', 'formspermsfimo', 'formpermfimo_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE formspermsfimo_piecesformspermsfimo DROP CONSTRAINT formspermsfimo_piecesformspermsfimo_pieceformpermfimo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'formspermsfimo_piecesformspermsfimo', 'formspermsfimo_piecesformspermsfimo_pieceformpermfimo_id_fkey', 'piecesformspermsfimo', 'pieceformpermfimo_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table formsqualifs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE formsqualifs DROP CONSTRAINT formsqualifs_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'formsqualifs', 'formsqualifs_apre_id_fkey', 'apres', 'apre_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE formsqualifs DROP CONSTRAINT formsqualifs_tiersprestataireapre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'formsqualifs', 'formsqualifs_tiersprestataireapre_id_fkey', 'tiersprestatairesapres', 'tiersprestataireapre_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table formsqualifs_piecesformsqualifs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE formsqualifs_piecesformsqualifs DROP CONSTRAINT formsqualifs_piecesformsqualifs_formqualif_id_fkey;
SELECT public.add_missing_constraint( 'public', 'formsqualifs_piecesformsqualifs', 'formsqualifs_piecesformsqualifs_formqualif_id_fkey', 'formsqualifs', 'formqualif_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE formsqualifs_piecesformsqualifs DROP CONSTRAINT formsqualifs_piecesformsqualifs_pieceformqualif_id_fkey;
SELECT public.add_missing_constraint( 'public', 'formsqualifs_piecesformsqualifs', 'formsqualifs_piecesformsqualifs_pieceformqualif_id_fkey', 'piecesformsqualifs', 'pieceformqualif_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table foyers.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE foyers DROP CONSTRAINT foyers_dossier_rsa_id_fkey;
SELECT public.add_missing_constraint( 'public', 'foyers', 'foyers_dossier_id_fkey', 'dossiers', 'dossier_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table fraisdeplacements66.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE fraisdeplacements66 DROP CONSTRAINT fraisdeplacements66_aideapre66_id_fkey;
SELECT public.add_missing_constraint( 'public', 'fraisdeplacements66', 'fraisdeplacements66_aideapre66_id_fkey', 'aidesapres66', 'aideapre66_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table grossesses.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE grossesses DROP CONSTRAINT grossesses_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'grossesses', 'grossesses_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table informationseti.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE informationseti DROP CONSTRAINT informationseti_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'informationseti', 'informationseti_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table infosagricoles.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE infosagricoles DROP CONSTRAINT infosagricoles_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'infosagricoles', 'infosagricoles_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table infosfinancieres.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE infosfinancieres DROP CONSTRAINT infosfinancieres_dossier_rsa_id_fkey;
SELECT public.add_missing_constraint( 'public', 'infosfinancieres', 'infosfinancieres_dossier_id_fkey', 'dossiers', 'dossier_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table jetons.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE jetons DROP CONSTRAINT jetons_dossier_id_fkey;
SELECT public.add_missing_constraint( 'public', 'jetons', 'jetons_dossier_id_fkey', 'dossiers', 'dossier_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE jetons DROP CONSTRAINT jetons_user_id_fkey;
SELECT public.add_missing_constraint( 'public', 'jetons', 'jetons_user_id_fkey', 'users', 'user_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table liberalites.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE liberalites DROP CONSTRAINT liberalites_avispcgpersonne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'liberalites', 'liberalites_avispcgpersonne_id_fkey', 'avispcgpersonnes', 'avispcgpersonne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table locsvehicinsert.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE locsvehicinsert DROP CONSTRAINT locsvehicinsert_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'locsvehicinsert', 'locsvehicinsert_apre_id_fkey', 'apres', 'apre_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table locsvehicinsert_pieceslocsvehicinsert.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE locsvehicinsert_pieceslocsvehicinsert DROP CONSTRAINT locsvehicinsert_pieceslocsvehicinsert_locvehicinsert_id_fkey;
SELECT public.add_missing_constraint( 'public', 'locsvehicinsert_pieceslocsvehicinsert', 'locsvehicinsert_pieceslocsvehicinsert_locvehicinsert_id_fkey', 'locsvehicinsert', 'locvehicinsert_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE locsvehicinsert_pieceslocsvehicinsert DROP CONSTRAINT locsvehicinsert_pieceslocsvehicinse_piecelocvehicinsert_id_fkey;
SELECT public.add_missing_constraint( 'public', 'locsvehicinsert_pieceslocsvehicinsert', 'locsvehicinsert_pieceslocsvehicinsert_piecelocvehicinsert_id_fkey', 'pieceslocsvehicinsert', 'piecelocvehicinsert_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table memos.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE memos DROP CONSTRAINT memos_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'memos', 'memos_personne_id_fkey', 'personnes', 'personne_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table modescontact.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE modescontact DROP CONSTRAINT modescontact_foyer_id_fkey;
SELECT public.add_missing_constraint( 'public', 'modescontact', 'modescontact_foyer_id_fkey', 'foyers', 'foyer_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table montantsconsommes.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE montantsconsommes DROP CONSTRAINT montantsconsommes_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'montantsconsommes', 'montantsconsommes_apre_id_fkey', 'apres', 'apre_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table orientations.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE orientations DROP CONSTRAINT orientations_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'orientations', 'orientations_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table orientsstructs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'orientsstructs', 'orientsstructs_personne_id_fkey', 'personnes', 'personne_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_typeorient_id_fkey;
SELECT public.add_missing_constraint( 'public', 'orientsstructs', 'orientsstructs_typeorient_id_fkey', 'typesorients', 'typeorient_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_structurereferente_id_fkey;
SELECT public.add_missing_constraint( 'public', 'orientsstructs', 'orientsstructs_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_referent_id_fkey;
SELECT public.add_missing_constraint( 'public', 'orientsstructs', 'orientsstructs_referent_id_fkey', 'referents', 'referent_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_structureorientante_id_fkey;
SELECT public.add_missing_constraint( 'public', 'orientsstructs', 'orientsstructs_structureorientante_id_fkey', 'structuresreferentes', 'structureorientante_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_referentorientant_id_fkey;
SELECT public.add_missing_constraint( 'public', 'orientsstructs', 'orientsstructs_referentorientant_id_fkey', 'referents', 'referentorientant_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table orientsstructs_servicesinstructeurs.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE orientsstructs_servicesinstructeurs DROP CONSTRAINT orientsstructs_servicesinstructeurs_orientstruct_id_fkey;
SELECT public.add_missing_constraint( 'public', 'orientsstructs_servicesinstructeurs', 'orientsstructs_servicesinstructeurs_orientstruct_id_fkey', 'orientsstructs', 'orientstruct_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE orientsstructs_servicesinstructeurs DROP CONSTRAINT orientsstructs_servicesinstructeurs_serviceinstructeur_id_fkey;
SELECT public.add_missing_constraint( 'public', 'orientsstructs_servicesinstructeurs', 'orientsstructs_servicesinstructeurs_serviceinstructeur_id_fkey', 'servicesinstructeurs', 'serviceinstructeur_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table paiementsfoyers.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE paiementsfoyers DROP CONSTRAINT paiementsfoyers_foyer_id_fkey;
SELECT public.add_missing_constraint( 'public', 'paiementsfoyers', 'paiementsfoyers_foyer_id_fkey', 'foyers', 'foyer_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table parcours.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE parcours DROP CONSTRAINT parcours_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'parcours', 'parcours_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table periodesimmersion.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE periodesimmersion DROP CONSTRAINT periodesimmersion_cui_id_fkey;
SELECT public.add_missing_constraint( 'public', 'periodesimmersion', 'periodesimmersion_cui_id_fkey', 'cuis', 'cui_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table permanences.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE permanences DROP CONSTRAINT permanences_structurereferente_id_fkey;
SELECT public.add_missing_constraint( 'public', 'permanences', 'permanences_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table permisb.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE permisb DROP CONSTRAINT permisb_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'permisb', 'permisb_apre_id_fkey', 'apres', 'apre_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE permisb DROP CONSTRAINT permisb_tiersprestataireapre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'permisb', 'permisb_tiersprestataireapre_id_fkey', 'tiersprestatairesapres', 'tiersprestataireapre_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table permisb_piecespermisb.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE permisb_piecespermisb DROP CONSTRAINT permisb_piecespermisb_permisb_id_fkey;
SELECT public.add_missing_constraint( 'public', 'permisb_piecespermisb', 'permisb_piecespermisb_permisb_id_fkey', 'permisb', 'permisb_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE permisb_piecespermisb DROP CONSTRAINT permisb_piecespermisb_piecepermisb_id_fkey;
SELECT public.add_missing_constraint( 'public', 'permisb_piecespermisb', 'permisb_piecespermisb_piecepermisb_id_fkey', 'piecespermisb', 'piecepermisb_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table personnes.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE personnes DROP CONSTRAINT personnes_foyer_id_fkey;
SELECT public.add_missing_constraint( 'public', 'personnes', 'personnes_foyer_id_fkey', 'foyers', 'foyer_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table personnes_referents.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE personnes_referents DROP CONSTRAINT personnes_referents_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'personnes_referents', 'personnes_referents_personne_id_fkey', 'personnes', 'personne_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE personnes_referents DROP CONSTRAINT personnes_referents_referent_id_fkey;
SELECT public.add_missing_constraint( 'public', 'personnes_referents', 'personnes_referents_referent_id_fkey', 'referents', 'referent_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE personnes_referents DROP CONSTRAINT personnes_referents_structurereferente_id_fkey;
SELECT public.add_missing_constraint( 'public', 'personnes_referents', 'personnes_referents_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table piecesaides66_typesaidesapres66.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE piecesaides66_typesaidesapres66 DROP CONSTRAINT typesaidesapres66_piecesaides66_typeaideapre66_id_fkey;
SELECT public.add_missing_constraint( 'public', 'piecesaides66_typesaidesapres66', 'piecesaides66_typesaidesapres66_typeaideapre66_id_fkey', 'typesaidesapres66', 'typeaideapre66_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE piecesaides66_typesaidesapres66 DROP CONSTRAINT typesaidesapres66_piecesaides66_pieceaide66_id_fkey;
SELECT public.add_missing_constraint( 'public', 'piecesaides66_typesaidesapres66', 'piecesaides66_typesaidesapres66_pieceaide66_id_fkey', 'piecesaides66', 'pieceaide66_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table piecescomptables66_typesaidesapres66.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE piecescomptables66_typesaidesapres66 DROP CONSTRAINT typesaidesapres66_piecescomptables66_typeaideapre66_id_fkey;
SELECT public.add_missing_constraint( 'public', 'piecescomptables66_typesaidesapres66', 'piecescomptables66_typesaidesapres66_typeaideapre66_id_fkey', 'typesaidesapres66', 'typeaideapre66_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE piecescomptables66_typesaidesapres66 DROP CONSTRAINT typesaidesapres66_piecescomptables66_piececomptable66_id_fkey;
SELECT public.add_missing_constraint( 'public', 'piecescomptables66_typesaidesapres66', 'piecescomptables66_typesaidesapres66_piececomptable66_id_fkey', 'piecescomptables66', 'piececomptable66_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table piecespdos.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE piecespdos DROP CONSTRAINT piecespdos_propopdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'piecespdos', 'piecespdos_propopdo_id_fkey', 'propospdos', 'propopdo_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table prestations.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE prestations DROP CONSTRAINT personneidfk;
SELECT public.add_missing_constraint( 'public', 'prestations', 'prestations_personne_id_fkey', 'personnes', 'personne_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table prestsform.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE prestsform DROP CONSTRAINT prestsform_actioninsertion_id_fkey;
SELECT public.add_missing_constraint( 'public', 'prestsform', 'prestsform_actioninsertion_id_fkey', 'actionsinsertion', 'actioninsertion_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE prestsform DROP CONSTRAINT prestsform_refpresta_id_fkey;
SELECT public.add_missing_constraint( 'public', 'prestsform', 'prestsform_refpresta_id_fkey', 'refsprestas', 'refpresta_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table propospdos.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos DROP CONSTRAINT propospdos_typepdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos', 'propospdos_typepdo_id_fkey', 'typespdos', 'typepdo_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos DROP CONSTRAINT propospdos_typenotifpdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos', 'propospdos_typenotifpdo_id_fkey', 'typesnotifspdos', 'typenotifpdo_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos DROP CONSTRAINT propospdos_originepdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos', 'propospdos_originepdo_id_fkey', 'originespdos', 'originepdo_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos DROP CONSTRAINT propospdos_referent_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos', 'propospdos_referent_id_fkey', 'referents', 'referent_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos DROP CONSTRAINT propospdos_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos', 'propospdos_personne_id_fkey', 'personnes', 'personne_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos DROP CONSTRAINT propospdos_user_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos', 'propospdos_user_id_fkey', 'users', 'user_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos DROP CONSTRAINT propospdos_structurereferente_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos', 'propospdos_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos DROP CONSTRAINT propospdos_serviceinstructeur_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos', 'propospdos_serviceinstructeur_id_fkey', 'servicesinstructeurs', 'serviceinstructeur_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table propospdos_situationspdos.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos_situationspdos DROP CONSTRAINT propospdos_situationspdos_propopdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos_situationspdos', 'propospdos_situationspdos_propopdo_id_fkey', 'propospdos', 'propopdo_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos_situationspdos DROP CONSTRAINT propospdos_situationspdos_situationpdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos_situationspdos', 'propospdos_situationspdos_situationpdo_id_fkey', 'situationspdos', 'situationpdo_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table propospdos_statutsdecisionspdos.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos_statutsdecisionspdos DROP CONSTRAINT propospdos_statutsdecisionspdos_propopdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos_statutsdecisionspdos', 'propospdos_statutsdecisionspdos_propopdo_id_fkey', 'propospdos', 'propopdo_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos_statutsdecisionspdos DROP CONSTRAINT propospdos_statutsdecisionspdos_statutdecisionpdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos_statutsdecisionspdos', 'propospdos_statutsdecisionspdos_statutdecisionpdo_id_fkey', 'statutsdecisionspdos', 'statutdecisionpdo_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table propospdos_statutspdos.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos_statutspdos DROP CONSTRAINT propospdos_statutspdos_propopdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos_statutspdos', 'propospdos_statutspdos_propopdo_id_fkey', 'propospdos', 'propopdo_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE propospdos_statutspdos DROP CONSTRAINT propospdos_statutspdos_statutpdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'propospdos_statutspdos', 'propospdos_statutspdos_statutpdo_id_fkey', 'statutspdos', 'statutpdo_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table rattachements.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE rattachements DROP CONSTRAINT rattachements_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'rattachements', 'rattachements_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table reducsrsa.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE reducsrsa DROP CONSTRAINT reducsrsa_avispcgdroitrsa_id_fkey;
SELECT public.add_missing_constraint( 'public', 'reducsrsa', 'reducsrsa_avispcgdroitrsa_id_fkey', 'avispcgdroitsrsa', 'avispcgdroitrsa_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table referents.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE referents DROP CONSTRAINT referents_structurereferente_id_fkey;
SELECT public.add_missing_constraint( 'public', 'referents', 'referents_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table regroupementszonesgeo_zonesgeographiques.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE regroupementszonesgeo_zonesgeographiques DROP CONSTRAINT zonesgeographiques_regroupementszonesg_zonegeographique_id_fkey;
SELECT public.add_missing_constraint( 'public', 'regroupementszonesgeo_zonesgeographiques', 'regroupementszonesgeo_zonesgeographiques_zonegeographique_id_fkey', 'zonesgeographiques', 'zonegeographique_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE regroupementszonesgeo_zonesgeographiques DROP CONSTRAINT zonesgeographiques_regroupementszon_regroupementzonegeo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'regroupementszonesgeo_zonesgeographiques', 'regroupementszonesgeo_zonesgeographiques_regroupementzonegeo_id_fkey', 'regroupementszonesgeo', 'regroupementzonegeo_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table relancesapres.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE relancesapres DROP CONSTRAINT relancesapres_apre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'relancesapres', 'relancesapres_apre_id_fkey', 'apres', 'apre_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table rendezvous.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE rendezvous DROP CONSTRAINT rendezvous_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'rendezvous', 'rendezvous_personne_id_fkey', 'personnes', 'personne_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE rendezvous DROP CONSTRAINT rendezvous_structurereferente_id_fkey;
SELECT public.add_missing_constraint( 'public', 'rendezvous', 'rendezvous_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE rendezvous DROP CONSTRAINT rendezvous_typerdv_id_fkey;
SELECT public.add_missing_constraint( 'public', 'rendezvous', 'rendezvous_typerdv_id_fkey', 'typesrdv', 'typerdv_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE rendezvous DROP CONSTRAINT rendezvous_referent_id_fkey;
SELECT public.add_missing_constraint( 'public', 'rendezvous', 'rendezvous_referent_id_fkey', 'referents', 'referent_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE rendezvous DROP CONSTRAINT rendezvous_permanence_id_fkey;
SELECT public.add_missing_constraint( 'public', 'rendezvous', 'rendezvous_permanence_id_fkey', 'permanences', 'permanence_id' , FALSE );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE rendezvous DROP CONSTRAINT rendezvous_statutrdv_id_fkey;
SELECT public.add_missing_constraint( 'public', 'rendezvous', 'rendezvous_statutrdv_id_fkey', 'statutsrdvs', 'statutrdv_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table ressources.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE ressources DROP CONSTRAINT ressources_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'ressources', 'ressources_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table ressources_ressourcesmensuelles.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE ressources_ressourcesmensuelles DROP CONSTRAINT ressources_ressourcesmensuelles_ressourcemensuelle_id_fkey;
SELECT public.add_missing_constraint( 'public', 'ressources_ressourcesmensuelles', 'ressources_ressourcesmensuelles_ressourcemensuelle_id_fkey', 'ressourcesmensuelles', 'ressourcemensuelle_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE ressources_ressourcesmensuelles DROP CONSTRAINT ressources_ressourcesmensuelles_ressource_id_fkey;
SELECT public.add_missing_constraint( 'public', 'ressources_ressourcesmensuelles', 'ressources_ressourcesmensuelles_ressource_id_fkey', 'ressources', 'ressource_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table ressourcesmensuelles.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE ressourcesmensuelles DROP CONSTRAINT ressourcesmensuelles_ressource_id_fkey;
SELECT public.add_missing_constraint( 'public', 'ressourcesmensuelles', 'ressourcesmensuelles_ressource_id_fkey', 'ressources', 'ressource_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table signalementseps93.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE signalementseps93 DROP CONSTRAINT signalementseps93_dossierep_id_fk;
SELECT public.add_missing_constraint( 'public', 'signalementseps93', 'signalementseps93_dossierep_id_fkey', 'dossierseps', 'dossierep_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table situationsdossiersrsa.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE situationsdossiersrsa DROP CONSTRAINT situationsdossiersrsa_dossier_rsa_id_fkey;
SELECT public.add_missing_constraint( 'public', 'situationsdossiersrsa', 'situationsdossiersrsa_dossier_id_fkey', 'dossiers', 'dossier_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table structuresreferentes.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE structuresreferentes DROP CONSTRAINT structuresreferentes_typeorient_id_fkey;
SELECT public.add_missing_constraint( 'public', 'structuresreferentes', 'structuresreferentes_typeorient_id_fkey', 'typesorients', 'typeorient_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table structuresreferentes_zonesgeographiques.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE structuresreferentes_zonesgeographiques DROP CONSTRAINT structuresreferentes_zonesgeographiq_structurereferente_id_fkey;
SELECT public.add_missing_constraint( 'public', 'structuresreferentes_zonesgeographiques', 'structuresreferentes_zonesgeographiques_structurereferente_id_fkey', 'structuresreferentes', 'structurereferente_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE structuresreferentes_zonesgeographiques DROP CONSTRAINT structuresreferentes_zonesgeographique_zonegeographique_id_fkey;
SELECT public.add_missing_constraint( 'public', 'structuresreferentes_zonesgeographiques', 'structuresreferentes_zonesgeographiques_zonegeographique_id_fkey', 'zonesgeographiques', 'zonegeographique_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table suivisaidesaprestypesaides.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE suivisaidesaprestypesaides DROP CONSTRAINT suivisaidesaprestypesaides_suiviaideapre_id_fkey;
SELECT public.add_missing_constraint( 'public', 'suivisaidesaprestypesaides', 'suivisaidesaprestypesaides_suiviaideapre_id_fkey', 'suivisaidesapres', 'suiviaideapre_id' , FALSE );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table suivisappuisorientation.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE suivisappuisorientation DROP CONSTRAINT suivisappuisorientation_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'suivisappuisorientation', 'suivisappuisorientation_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table suivisinstruction.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE suivisinstruction DROP CONSTRAINT suivisinstruction_dossier_rsa_id_fkey;
SELECT public.add_missing_constraint( 'public', 'suivisinstruction', 'suivisinstruction_dossier_id_fkey', 'dossiers', 'dossier_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table suspensionsdroits.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE suspensionsdroits DROP CONSTRAINT suspensionsdroits_situationdossierrsa_id_fkey;
SELECT public.add_missing_constraint( 'public', 'suspensionsdroits', 'suspensionsdroits_situationdossierrsa_id_fkey', 'situationsdossiersrsa', 'situationdossierrsa_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table suspensionsversements.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE suspensionsversements DROP CONSTRAINT suspensionsversements_situationdossierrsa_id_fkey;
SELECT public.add_missing_constraint( 'public', 'suspensionsversements', 'suspensionsversements_situationdossierrsa_id_fkey', 'situationsdossiersrsa', 'situationdossierrsa_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table titressejour.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE titressejour DROP CONSTRAINT titres_sejour_personne_id_fkey;
SELECT public.add_missing_constraint( 'public', 'titressejour', 'titressejour_personne_id_fkey', 'personnes', 'personne_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table totalisationsacomptes.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE totalisationsacomptes DROP CONSTRAINT totalisationsacomptes_identificationflux_id_fkey;
SELECT public.add_missing_constraint( 'public', 'totalisationsacomptes', 'totalisationsacomptes_identificationflux_id_fkey', 'identificationsflux', 'identificationflux_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table traitementspdos.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE traitementspdos DROP CONSTRAINT traitementspdos_propopdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'traitementspdos', 'traitementspdos_propopdo_id_fkey', 'propospdos', 'propopdo_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE traitementspdos DROP CONSTRAINT traitementspdos_descriptionpdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'traitementspdos', 'traitementspdos_descriptionpdo_id_fkey', 'descriptionspdos', 'descriptionpdo_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE traitementspdos DROP CONSTRAINT traitementspdos_traitementtypepdo_id_fkey;
SELECT public.add_missing_constraint( 'public', 'traitementspdos', 'traitementspdos_traitementtypepdo_id_fkey', 'traitementstypespdos', 'traitementtypepdo_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table transmissionsflux.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE transmissionsflux DROP CONSTRAINT transmissionsflux_identificationflux_id_fkey;
SELECT public.add_missing_constraint( 'public', 'transmissionsflux', 'transmissionsflux_identificationflux_id_fkey', 'identificationsflux', 'identificationflux_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table typesaidesapres66.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE typesaidesapres66 DROP CONSTRAINT typesaidesapres66_themeapre66_id_fkey;
SELECT public.add_missing_constraint( 'public', 'typesaidesapres66', 'typesaidesapres66_themeapre66_id_fkey', 'themesapres66', 'themeapre66_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table users.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE users DROP CONSTRAINT users_group_id_fkey;
SELECT public.add_missing_constraint( 'public', 'users', 'users_group_id_fkey', 'groups', 'group_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE users DROP CONSTRAINT users_serviceinstructeur_id_fkey;
SELECT public.add_missing_constraint( 'public', 'users', 'users_serviceinstructeur_id_fkey', 'servicesinstructeurs', 'serviceinstructeur_id'  );

-- -----------------------------------------------------------------------------
-- Ajout des contraintes pour la table users_zonesgeographiques.
-- -----------------------------------------------------------------------------
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE users_zonesgeographiques DROP CONSTRAINT users_zonesgeographiques_user_id_fkey;
SELECT public.add_missing_constraint( 'public', 'users_zonesgeographiques', 'users_zonesgeographiques_user_id_fkey', 'users', 'user_id'  );
-- Aucune action n'était définie pour la clé étrangère
ALTER TABLE users_zonesgeographiques DROP CONSTRAINT users_zonesgeographiques_zonegeographique_id_fkey;
SELECT public.add_missing_constraint( 'public', 'users_zonesgeographiques', 'users_zonesgeographiques_zonegeographique_id_fkey', 'zonesgeographiques', 'zonegeographique_id'  );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************