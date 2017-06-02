SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
SELECT NOW();
BEGIN;
-- *****************************************************************************

TRUNCATE "accompagnementscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "accscreaentr" RESTART IDENTITY CASCADE;
TRUNCATE "accscreaentr_piecesaccscreaentr" RESTART IDENTITY CASCADE;
-- TRUNCATE "acos" RESTART IDENTITY CASCADE;
TRUNCATE "acqsmatsprofs" RESTART IDENTITY CASCADE;
TRUNCATE "acqsmatsprofs_piecesacqsmatsprofs" RESTART IDENTITY CASCADE;
TRUNCATE "actions" RESTART IDENTITY CASCADE;
TRUNCATE "actionscandidats" RESTART IDENTITY CASCADE;
TRUNCATE "actionscandidats_motifssortie" RESTART IDENTITY CASCADE;
TRUNCATE "actionscandidats_partenaires" RESTART IDENTITY CASCADE;
TRUNCATE "actionscandidats_personnes" RESTART IDENTITY CASCADE;
TRUNCATE "actionscandidats_zonesgeographiques" RESTART IDENTITY CASCADE;
TRUNCATE "actionsfps93" RESTART IDENTITY CASCADE;
TRUNCATE "actionsinsertion" RESTART IDENTITY CASCADE;
TRUNCATE "activites" RESTART IDENTITY CASCADE;
TRUNCATE "actsprofs" RESTART IDENTITY CASCADE;
TRUNCATE "actsprofs_piecesactsprofs" RESTART IDENTITY CASCADE;
TRUNCATE "adresses" RESTART IDENTITY CASCADE;
TRUNCATE "adressescuis" RESTART IDENTITY CASCADE;
TRUNCATE "adressesfoyers" RESTART IDENTITY CASCADE;
TRUNCATE "adressesprestatairesfps93" RESTART IDENTITY CASCADE;
TRUNCATE "aidesagricoles" RESTART IDENTITY CASCADE;
TRUNCATE "aidesapres66" RESTART IDENTITY CASCADE;
TRUNCATE "aidesapres66_piecesaides66" RESTART IDENTITY CASCADE;
TRUNCATE "aidesapres66_piecescomptables66" RESTART IDENTITY CASCADE;
TRUNCATE "aidesdirectes" RESTART IDENTITY CASCADE;
TRUNCATE "allocationssoutienfamilial" RESTART IDENTITY CASCADE;
TRUNCATE "amenagslogts" RESTART IDENTITY CASCADE;
TRUNCATE "amenagslogts_piecesamenagslogts" RESTART IDENTITY CASCADE;
TRUNCATE "anomalies" RESTART IDENTITY CASCADE;
TRUNCATE "appellationsromesv3" RESTART IDENTITY CASCADE;
TRUNCATE "apres" RESTART IDENTITY CASCADE;
TRUNCATE "apres_comitesapres" RESTART IDENTITY CASCADE;
TRUNCATE "apres_etatsliquidatifs" RESTART IDENTITY CASCADE;
TRUNCATE "apres_piecesapre" RESTART IDENTITY CASCADE;
-- TRUNCATE "aros" RESTART IDENTITY CASCADE;
-- TRUNCATE "aros_acos" RESTART IDENTITY CASCADE;
TRUNCATE "autresavisradiation" RESTART IDENTITY CASCADE;
TRUNCATE "autresavissuspension" RESTART IDENTITY CASCADE;
TRUNCATE "aviscgssdompersonnes" RESTART IDENTITY CASCADE;
TRUNCATE "avispcgdroitsrsa" RESTART IDENTITY CASCADE;
TRUNCATE "avispcgpersonnes" RESTART IDENTITY CASCADE;
TRUNCATE "bilanscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "bilansparcours66" RESTART IDENTITY CASCADE;
TRUNCATE "budgetsapres" RESTART IDENTITY CASCADE;
TRUNCATE "calculsdroitsrsa" RESTART IDENTITY CASCADE;
TRUNCATE "candidatures_progs66" RESTART IDENTITY CASCADE;
-- TRUNCATE "cantons" RESTART IDENTITY CASCADE;
TRUNCATE "categoriesfps93" RESTART IDENTITY CASCADE;
TRUNCATE "cers93" RESTART IDENTITY CASCADE;
TRUNCATE "cers93_sujetscers93" RESTART IDENTITY CASCADE;
TRUNCATE "codesromemetiersdsps66" RESTART IDENTITY CASCADE;
TRUNCATE "codesromesecteursdsps66" RESTART IDENTITY CASCADE;
TRUNCATE "comitesapres" RESTART IDENTITY CASCADE;
TRUNCATE "comitesapres_participantscomites" RESTART IDENTITY CASCADE;
TRUNCATE "commentairesnormescers93" RESTART IDENTITY CASCADE;
TRUNCATE "commentairesnormescers93_histoschoixcers93" RESTART IDENTITY CASCADE;
TRUNCATE "commissionseps" RESTART IDENTITY CASCADE;
TRUNCATE "commissionseps_membreseps" RESTART IDENTITY CASCADE;
TRUNCATE "composfoyerscers93" RESTART IDENTITY CASCADE;
TRUNCATE "composfoyerspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "compositionsregroupementseps" RESTART IDENTITY CASCADE;
TRUNCATE "conditionsactivitesprealables" RESTART IDENTITY CASCADE;
TRUNCATE "condsadmins" RESTART IDENTITY CASCADE;
-- TRUNCATE "connections" RESTART IDENTITY CASCADE;
TRUNCATE "contactspartenaires" RESTART IDENTITY CASCADE;
TRUNCATE "contenustextareascourrierspdos" RESTART IDENTITY CASCADE;
TRUNCATE "contratscomplexeseps93" RESTART IDENTITY CASCADE;
TRUNCATE "contratsinsertion" RESTART IDENTITY CASCADE;
TRUNCATE "contratsinsertion_users" RESTART IDENTITY CASCADE;
TRUNCATE "controlesadministratifs" RESTART IDENTITY CASCADE;
TRUNCATE "correspondancespersonnes" RESTART IDENTITY CASCADE;
TRUNCATE "correspondancesromesv2v3" RESTART IDENTITY CASCADE;
TRUNCATE "courrierspdos" RESTART IDENTITY CASCADE;
TRUNCATE "courrierspdos_traitementspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "courrierspdos_traitementspdos" RESTART IDENTITY CASCADE;
TRUNCATE "covs58" RESTART IDENTITY CASCADE;
TRUNCATE "creances" RESTART IDENTITY CASCADE;
TRUNCATE "creancesalimentaires" RESTART IDENTITY CASCADE;
TRUNCATE "cuis" RESTART IDENTITY CASCADE;
TRUNCATE "cuis66" RESTART IDENTITY CASCADE;
TRUNCATE "cuis_piecesmailscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "decisionscontratscomplexeseps93" RESTART IDENTITY CASCADE;
TRUNCATE "decisionscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsdefautsinsertionseps66" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsdossierspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsdossierspcgs66_decisionspersonnespcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsdossierspcgs66_decisionstraitementspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsdossierspcgs66_typesrsapcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsnonorientationsproscovs58" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsnonorientationsproseps58" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsnonorientationsproseps66" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsnonorientationsproseps93" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsnonrespectssanctionseps93" RESTART IDENTITY CASCADE;
TRUNCATE "decisionspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "decisionspdos" RESTART IDENTITY CASCADE;
TRUNCATE "decisionspersonnespcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsproposcontratsinsertioncovs58" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsproposnonorientationsproscovs58" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsproposorientationscovs58" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsproposorientssocialescovs58" RESTART IDENTITY CASCADE;
TRUNCATE "decisionspropospdos" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsregressionsorientationscovs58" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsregressionsorientationseps58" RESTART IDENTITY CASCADE;
TRUNCATE "decisionsreorientationseps93" RESTART IDENTITY CASCADE;
TRUNCATE "decisionssaisinesbilansparcourseps66" RESTART IDENTITY CASCADE;
TRUNCATE "decisionssaisinespdoseps66" RESTART IDENTITY CASCADE;
TRUNCATE "decisionssanctionseps58" RESTART IDENTITY CASCADE;
TRUNCATE "decisionssanctionsrendezvouseps58" RESTART IDENTITY CASCADE;
TRUNCATE "decisionssignalementseps93" RESTART IDENTITY CASCADE;
TRUNCATE "decisionstraitementspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "decsdospcgs66_orgsdospcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "defautsinsertionseps66" RESTART IDENTITY CASCADE;
TRUNCATE "departements" RESTART IDENTITY CASCADE;
TRUNCATE "derniersdossiersallocataires" RESTART IDENTITY CASCADE;
TRUNCATE "derogations" RESTART IDENTITY CASCADE;
TRUNCATE "descriptionspdos" RESTART IDENTITY CASCADE;
TRUNCATE "detailsaccosocfams" RESTART IDENTITY CASCADE;
TRUNCATE "detailsaccosocfams_revs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsaccosocindis" RESTART IDENTITY CASCADE;
TRUNCATE "detailsaccosocindis_revs" RESTART IDENTITY CASCADE;
TRUNCATE "detailscalculsdroitsrsa" RESTART IDENTITY CASCADE;
TRUNCATE "detailsconforts" RESTART IDENTITY CASCADE;
TRUNCATE "detailsconforts_revs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsdifdisps" RESTART IDENTITY CASCADE;
TRUNCATE "detailsdifdisps_revs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsdiflogs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsdiflogs_revs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsdifsocpros" RESTART IDENTITY CASCADE;
TRUNCATE "detailsdifsocpros_revs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsdifsocs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsdifsocs_revs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsdroitsrsa" RESTART IDENTITY CASCADE;
TRUNCATE "detailsfreinforms" RESTART IDENTITY CASCADE;
TRUNCATE "detailsfreinforms_revs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsmoytrans" RESTART IDENTITY CASCADE;
TRUNCATE "detailsmoytrans_revs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsnatmobs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsnatmobs_revs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsprojpros" RESTART IDENTITY CASCADE;
TRUNCATE "detailsprojpros_revs" RESTART IDENTITY CASCADE;
TRUNCATE "detailsressourcesmensuelles" RESTART IDENTITY CASCADE;
TRUNCATE "detailsressourcesmensuelles_ressourcesmensuelles" RESTART IDENTITY CASCADE;
TRUNCATE "diplomescers93" RESTART IDENTITY CASCADE;
TRUNCATE "documentsbenefsfps93" RESTART IDENTITY CASCADE;
TRUNCATE "documentsbenefsfps93_fichesprescriptions93" RESTART IDENTITY CASCADE;
TRUNCATE "domainesromesv3" RESTART IDENTITY CASCADE;
TRUNCATE "domiciliationsbancaires" RESTART IDENTITY CASCADE;
TRUNCATE "dossiers" RESTART IDENTITY CASCADE;
TRUNCATE "dossierscaf" RESTART IDENTITY CASCADE;
TRUNCATE "dossierscovs58" RESTART IDENTITY CASCADE;
TRUNCATE "dossierseps" RESTART IDENTITY CASCADE;
TRUNCATE "dossierspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "dsps" RESTART IDENTITY CASCADE;
TRUNCATE "dsps_revs" RESTART IDENTITY CASCADE;
TRUNCATE "emailscuis" RESTART IDENTITY CASCADE;
TRUNCATE "entreesromesv3" RESTART IDENTITY CASCADE;
TRUNCATE "entretiens" RESTART IDENTITY CASCADE;
TRUNCATE "eps" RESTART IDENTITY CASCADE;
TRUNCATE "eps_membreseps" RESTART IDENTITY CASCADE;
TRUNCATE "eps_zonesgeographiques" RESTART IDENTITY CASCADE;
TRUNCATE "etatsliquidatifs" RESTART IDENTITY CASCADE;
TRUNCATE "evenements" RESTART IDENTITY CASCADE;
TRUNCATE "expsproscers93" RESTART IDENTITY CASCADE;
TRUNCATE "famillesromesv3" RESTART IDENTITY CASCADE;
TRUNCATE "fichesprescriptions93" RESTART IDENTITY CASCADE;
TRUNCATE "fichesprescriptions93_modstransmsfps93" RESTART IDENTITY CASCADE;
TRUNCATE "fichiersmodules" RESTART IDENTITY CASCADE;
TRUNCATE "fichierstraitementspdos" RESTART IDENTITY CASCADE;
TRUNCATE "filieresfps93" RESTART IDENTITY CASCADE;
TRUNCATE "fonctionsmembreseps" RESTART IDENTITY CASCADE;
TRUNCATE "formationscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "formspermsfimo" RESTART IDENTITY CASCADE;
TRUNCATE "formspermsfimo_piecesformspermsfimo" RESTART IDENTITY CASCADE;
TRUNCATE "formsqualifs" RESTART IDENTITY CASCADE;
TRUNCATE "formsqualifs_piecesformsqualifs" RESTART IDENTITY CASCADE;
TRUNCATE "foyers" RESTART IDENTITY CASCADE;
TRUNCATE "fraisdeplacements66" RESTART IDENTITY CASCADE;
TRUNCATE "grossesses" RESTART IDENTITY CASCADE;
-- TRUNCATE "groups" RESTART IDENTITY CASCADE;
TRUNCATE "histoaprecomplementaires" RESTART IDENTITY CASCADE;
TRUNCATE "historiqueetatspe" RESTART IDENTITY CASCADE;
TRUNCATE "historiquepositionscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "historiquesdroits" RESTART IDENTITY CASCADE;
TRUNCATE "histoschoixcers93" RESTART IDENTITY CASCADE;
TRUNCATE "identificationsflux" RESTART IDENTITY CASCADE;
TRUNCATE "immersionscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "informationseti" RESTART IDENTITY CASCADE;
TRUNCATE "informationspe" RESTART IDENTITY CASCADE;
TRUNCATE "infosagricoles" RESTART IDENTITY CASCADE;
TRUNCATE "infosfinancieres" RESTART IDENTITY CASCADE;
TRUNCATE "instantanesdonneesfps93" RESTART IDENTITY CASCADE;
TRUNCATE "integrationfichiersapre" RESTART IDENTITY CASCADE;
TRUNCATE "jetons" RESTART IDENTITY CASCADE;
TRUNCATE "jetonsfonctions" RESTART IDENTITY CASCADE;
TRUNCATE "liberalites" RESTART IDENTITY CASCADE;
TRUNCATE "listesanctionseps58" RESTART IDENTITY CASCADE;
TRUNCATE "locsvehicinsert" RESTART IDENTITY CASCADE;
TRUNCATE "locsvehicinsert_pieceslocsvehicinsert" RESTART IDENTITY CASCADE;
TRUNCATE "manifestationsbilansparcours66" RESTART IDENTITY CASCADE;
TRUNCATE "membreseps" RESTART IDENTITY CASCADE;
TRUNCATE "memos" RESTART IDENTITY CASCADE;
TRUNCATE "metiersexerces" RESTART IDENTITY CASCADE;
TRUNCATE "metiersromesv3" RESTART IDENTITY CASCADE;
TRUNCATE "modelestraitementspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "modelestypescourrierspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "modelestypescourrierspcgs66_situationspdos" RESTART IDENTITY CASCADE;
TRUNCATE "modescontact" RESTART IDENTITY CASCADE;
TRUNCATE "modstransmsfps93" RESTART IDENTITY CASCADE;
TRUNCATE "montantsconsommes" RESTART IDENTITY CASCADE;
TRUNCATE "motifscersnonvalids66" RESTART IDENTITY CASCADE;
TRUNCATE "motifscersnonvalids66_proposdecisionscers66" RESTART IDENTITY CASCADE;
TRUNCATE "motifsnonintegrationsfps93" RESTART IDENTITY CASCADE;
TRUNCATE "motifsnonreceptionsfps93" RESTART IDENTITY CASCADE;
TRUNCATE "motifsnonretenuesfps93" RESTART IDENTITY CASCADE;
TRUNCATE "motifsnonsouhaitsfps93" RESTART IDENTITY CASCADE;
TRUNCATE "motifsrefuscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "motifsreorientseps93" RESTART IDENTITY CASCADE;
TRUNCATE "motifsrupturescuis66" RESTART IDENTITY CASCADE;
TRUNCATE "motifsrupturescuis66_rupturescuis66" RESTART IDENTITY CASCADE;
TRUNCATE "motifssortie" RESTART IDENTITY CASCADE;
TRUNCATE "motifssuspensioncuis66" RESTART IDENTITY CASCADE;
TRUNCATE "motifssuspensioncuis66_suspensionscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "mtpcgs66_pmtcpcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "naturescontrats" RESTART IDENTITY CASCADE;
TRUNCATE "nonorientationsproscovs58" RESTART IDENTITY CASCADE;
TRUNCATE "nonorientationsproseps58" RESTART IDENTITY CASCADE;
TRUNCATE "nonorientationsproseps66" RESTART IDENTITY CASCADE;
TRUNCATE "nonorientationsproseps93" RESTART IDENTITY CASCADE;
TRUNCATE "nonorientes66" RESTART IDENTITY CASCADE;
TRUNCATE "nonrespectssanctionseps93" RESTART IDENTITY CASCADE;
TRUNCATE "objetsentretien" RESTART IDENTITY CASCADE;
TRUNCATE "oldaccompagnementscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "orgstransmisdossierspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "orientations" RESTART IDENTITY CASCADE;
TRUNCATE "orientsstructs" RESTART IDENTITY CASCADE;
TRUNCATE "orientsstructs_servicesinstructeurs" RESTART IDENTITY CASCADE;
-- TRUNCATE "originespdos" RESTART IDENTITY CASCADE;
TRUNCATE "paiementsfoyers" RESTART IDENTITY CASCADE;
TRUNCATE "parametresfinanciers" RESTART IDENTITY CASCADE;
TRUNCATE "parcours" RESTART IDENTITY CASCADE;
TRUNCATE "partenaires" RESTART IDENTITY CASCADE;
TRUNCATE "partenairescuis" RESTART IDENTITY CASCADE;
TRUNCATE "partenairescuis66" RESTART IDENTITY CASCADE;
TRUNCATE "participantscomites" RESTART IDENTITY CASCADE;
TRUNCATE "passagescommissionseps" RESTART IDENTITY CASCADE;
TRUNCATE "passagescovs58" RESTART IDENTITY CASCADE;
TRUNCATE "pdfs" RESTART IDENTITY CASCADE;
TRUNCATE "periodesimmersion" RESTART IDENTITY CASCADE;
TRUNCATE "periodesimmersioncuis66" RESTART IDENTITY CASCADE;
TRUNCATE "permanences" RESTART IDENTITY CASCADE;
TRUNCATE "permisb" RESTART IDENTITY CASCADE;
TRUNCATE "permisb_piecespermisb" RESTART IDENTITY CASCADE;
TRUNCATE "personnes" RESTART IDENTITY CASCADE;
TRUNCATE "personnes_referents" RESTART IDENTITY CASCADE;
TRUNCATE "personnescuis" RESTART IDENTITY CASCADE;
TRUNCATE "personnescuis66" RESTART IDENTITY CASCADE;
TRUNCATE "personnespcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "personnespcgs66_situationspdos" RESTART IDENTITY CASCADE;
TRUNCATE "personnespcgs66_statutspdos" RESTART IDENTITY CASCADE;
TRUNCATE "piecesaccscreaentr" RESTART IDENTITY CASCADE;
TRUNCATE "piecesacqsmatsprofs" RESTART IDENTITY CASCADE;
TRUNCATE "piecesactsprofs" RESTART IDENTITY CASCADE;
TRUNCATE "piecesaides66" RESTART IDENTITY CASCADE;
TRUNCATE "piecesaides66_typesaidesapres66" RESTART IDENTITY CASCADE;
TRUNCATE "piecesamenagslogts" RESTART IDENTITY CASCADE;
TRUNCATE "piecesapre" RESTART IDENTITY CASCADE;
TRUNCATE "piecescomptables66" RESTART IDENTITY CASCADE;
TRUNCATE "piecescomptables66_typesaidesapres66" RESTART IDENTITY CASCADE;
TRUNCATE "piecesformspermsfimo" RESTART IDENTITY CASCADE;
TRUNCATE "piecesformsqualifs" RESTART IDENTITY CASCADE;
TRUNCATE "pieceslocsvehicinsert" RESTART IDENTITY CASCADE;
TRUNCATE "piecesmailscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "piecesmanquantescuis66" RESTART IDENTITY CASCADE;
TRUNCATE "piecesmodelestypescourrierspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "piecespdos" RESTART IDENTITY CASCADE;
TRUNCATE "piecespermisb" RESTART IDENTITY CASCADE;
-- TRUNCATE "polesdossierspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "populationsb3pdvs93" RESTART IDENTITY CASCADE;
TRUNCATE "populationsb4b5pdvs93" RESTART IDENTITY CASCADE;
TRUNCATE "populationsb6pdvs93" RESTART IDENTITY CASCADE;
TRUNCATE "populationsd1d2pdvs93" RESTART IDENTITY CASCADE;
TRUNCATE "prestatairesfps93" RESTART IDENTITY CASCADE;
TRUNCATE "prestataireshorspdifps93" RESTART IDENTITY CASCADE;
TRUNCATE "prestations" RESTART IDENTITY CASCADE;
TRUNCATE "prestsform" RESTART IDENTITY CASCADE;
TRUNCATE "progsfichescandidatures66" RESTART IDENTITY CASCADE;
TRUNCATE "proposcontratsinsertioncovs58" RESTART IDENTITY CASCADE;
TRUNCATE "proposdecisionscers66" RESTART IDENTITY CASCADE;
TRUNCATE "proposdecisionscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "propositionscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "proposnonorientationsproscovs58" RESTART IDENTITY CASCADE;
TRUNCATE "proposorientationscovs58" RESTART IDENTITY CASCADE;
TRUNCATE "proposorientssocialescovs58" RESTART IDENTITY CASCADE;
TRUNCATE "propospdos" RESTART IDENTITY CASCADE;
TRUNCATE "propospdos_situationspdos" RESTART IDENTITY CASCADE;
TRUNCATE "propospdos_statutsdecisionspdos" RESTART IDENTITY CASCADE;
TRUNCATE "propospdos_statutspdos" RESTART IDENTITY CASCADE;
TRUNCATE "questionnairesd1pdvs93" RESTART IDENTITY CASCADE;
TRUNCATE "questionnairesd2pdvs93" RESTART IDENTITY CASCADE;
TRUNCATE "questionspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "raisonssocialespartenairescuis66" RESTART IDENTITY CASCADE;
TRUNCATE "rattachements" RESTART IDENTITY CASCADE;
TRUNCATE "reducsrsa" RESTART IDENTITY CASCADE;
-- TRUNCATE "referents" RESTART IDENTITY CASCADE;
TRUNCATE "refsprestas" RESTART IDENTITY CASCADE;
TRUNCATE "regressionsorientationscovs58" RESTART IDENTITY CASCADE;
TRUNCATE "regressionsorientationseps58" RESTART IDENTITY CASCADE;
TRUNCATE "regroupementseps" RESTART IDENTITY CASCADE;
TRUNCATE "regroupementszonesgeo" RESTART IDENTITY CASCADE;
TRUNCATE "regroupementszonesgeo_zonesgeographiques" RESTART IDENTITY CASCADE;
TRUNCATE "relancesapres" RESTART IDENTITY CASCADE;
TRUNCATE "relancesnonrespectssanctionseps93" RESTART IDENTITY CASCADE;
TRUNCATE "rendezvous" RESTART IDENTITY CASCADE;
TRUNCATE "rendezvous_thematiquesrdvs" RESTART IDENTITY CASCADE;
TRUNCATE "reorientationseps93" RESTART IDENTITY CASCADE;
TRUNCATE "ressources" RESTART IDENTITY CASCADE;
TRUNCATE "ressources_ressourcesmensuelles" RESTART IDENTITY CASCADE;
TRUNCATE "ressourcesmensuelles" RESTART IDENTITY CASCADE;
TRUNCATE "rupturescuis66" RESTART IDENTITY CASCADE;
TRUNCATE "saisinesbilansparcourseps66" RESTART IDENTITY CASCADE;
TRUNCATE "saisinespdoseps66" RESTART IDENTITY CASCADE;
TRUNCATE "sanctionseps58" RESTART IDENTITY CASCADE;
TRUNCATE "sanctionsrendezvouseps58" RESTART IDENTITY CASCADE;
TRUNCATE "secteursactis" RESTART IDENTITY CASCADE;
TRUNCATE "secteurscuis" RESTART IDENTITY CASCADE;
-- TRUNCATE "servicesinstructeurs" RESTART IDENTITY CASCADE;
TRUNCATE "signalementseps93" RESTART IDENTITY CASCADE;
TRUNCATE "sitescovs58" RESTART IDENTITY CASCADE;
TRUNCATE "sitescovs58_zonesgeographiques" RESTART IDENTITY CASCADE;
TRUNCATE "situationsallocataires" RESTART IDENTITY CASCADE;
TRUNCATE "situationsdossiersrsa" RESTART IDENTITY CASCADE;
TRUNCATE "situationspdos" RESTART IDENTITY CASCADE;
TRUNCATE "sortiesaccompagnementsd2pdvs93" RESTART IDENTITY CASCADE;
TRUNCATE "soussujetscers93" RESTART IDENTITY CASCADE;
TRUNCATE "statintegrationbeneficiaire" RESTART IDENTITY CASCADE;
TRUNCATE "statintegrationfinancier" RESTART IDENTITY CASCADE;
TRUNCATE "statintegrationinstruction" RESTART IDENTITY CASCADE;
TRUNCATE "statutsdecisionspdos" RESTART IDENTITY CASCADE;
TRUNCATE "statutspdos" RESTART IDENTITY CASCADE;
TRUNCATE "statutsrdvs" RESTART IDENTITY CASCADE;
TRUNCATE "statutsrdvs_typesrdv" RESTART IDENTITY CASCADE;
-- TRUNCATE "structuresreferentes" RESTART IDENTITY CASCADE;
-- TRUNCATE "structuresreferentes_zonesgeographiques" RESTART IDENTITY CASCADE;
TRUNCATE "suivisaidesapres" RESTART IDENTITY CASCADE;
TRUNCATE "suivisaidesaprestypesaides" RESTART IDENTITY CASCADE;
TRUNCATE "suivisappuisorientation" RESTART IDENTITY CASCADE;
TRUNCATE "suivisinstruction" RESTART IDENTITY CASCADE;
TRUNCATE "sujetscers93" RESTART IDENTITY CASCADE;
TRUNCATE "suspensionscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "suspensionsdroits" RESTART IDENTITY CASCADE;
TRUNCATE "suspensionsversements" RESTART IDENTITY CASCADE;
TRUNCATE "tableauxsuivispdvs93" RESTART IDENTITY CASCADE;
TRUNCATE "tauxcgscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "textareascourrierspdos" RESTART IDENTITY CASCADE;
TRUNCATE "textsmailscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "thematiquesfps93" RESTART IDENTITY CASCADE;
TRUNCATE "thematiquesrdvs" RESTART IDENTITY CASCADE;
TRUNCATE "themesapres66" RESTART IDENTITY CASCADE;
TRUNCATE "themescovs58" RESTART IDENTITY CASCADE;
TRUNCATE "tiersprestatairesapres" RESTART IDENTITY CASCADE;
TRUNCATE "titressejour" RESTART IDENTITY CASCADE;
TRUNCATE "totalisationsacomptes" RESTART IDENTITY CASCADE;
TRUNCATE "traitementspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "traitementspdos" RESTART IDENTITY CASCADE;
TRUNCATE "traitementstypespdos" RESTART IDENTITY CASCADE;
TRUNCATE "transfertspdvs93" RESTART IDENTITY CASCADE;
TRUNCATE "transmissionsflux" RESTART IDENTITY CASCADE;
TRUNCATE "typesactions" RESTART IDENTITY CASCADE;
TRUNCATE "typesaidesapres66" RESTART IDENTITY CASCADE;
TRUNCATE "typescontratscuis66" RESTART IDENTITY CASCADE;
TRUNCATE "typescourrierspcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "typesnotifspdos" RESTART IDENTITY CASCADE;
-- TRUNCATE "typesorients" RESTART IDENTITY CASCADE;
-- TRUNCATE "typespdos" RESTART IDENTITY CASCADE;
TRUNCATE "typesrdv" RESTART IDENTITY CASCADE;
TRUNCATE "typesrsapcgs66" RESTART IDENTITY CASCADE;
TRUNCATE "typoscontrats" RESTART IDENTITY CASCADE;
-- TRUNCATE "users" RESTART IDENTITY CASCADE;
-- TRUNCATE "users_zonesgeographiques" RESTART IDENTITY CASCADE;
TRUNCATE "valeursparsoussujetscers93" RESTART IDENTITY CASCADE;
TRUNCATE "version" RESTART IDENTITY CASCADE;
-- TRUNCATE "zonesgeographiques" RESTART IDENTITY CASCADE;

-- *****************************************************************************
COMMIT;
SELECT NOW();
-- *****************************************************************************
