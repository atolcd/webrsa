<?php

/* SVN FILE: $Id$ */
/**
 * Custom Inflected Words.
 *
 * This file is used to hold words that are not matched in the normail Inflector::pluralize() and
 * Inflector::singularize()
 *
 * PHP versions 4 and %
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 1.0.0.2312
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * This is a key => value array of regex used to match words.
 * If key matches then the value is returned.
 *
 *  $pluralRules = array('/(s)tatus$/i' => '\1\2tatuses', '/^(ox)$/i' => '\1\2en', '/([m|l])ouse$/i' => '\1ice');
 */
$pluralRules = array();

/**
 * This is a key only array of plural words that should not be inflected.
 * Notice the last comma
 *
 * $uninflectedPlural = array('.*[nrlm]ese', '.*deer', '.*fish', '.*measles', '.*ois', '.*pox');
 */
$uninflectedPlural = array(
    'recours',
    'rendezvous',
    'parcours',
    'permisb',
    'avisref',
    //Harry
    'rejet_historique',
	//Fin harry
	'fluxcnaf',
);

/**
 * This is a key => value array of plural irregular words.
 * If key matches then the value is returned.
 *
 *  $irregularPlural = array('atlas' => 'atlases', 'beef' => 'beefs', 'brother' => 'brothers')
 */
$irregularPlural = array(
    // Tables
    'acccreaentr' => 'accscreaentr',
    'acccreaentr_pieceacccreaentr' => 'accscreaentr_piecesaccscreaentr',
    'acqmatprof' => 'acqsmatsprofs',
    'acqmatprof_pieceacqmatprof' => 'acqsmatsprofs_piecesacqsmatsprofs',
    'actioncandidat' => 'actionscandidats',
    'actioncandidat_motifsortie' => 'actionscandidats_motifssortie',
    'actioncandidat_partenaire' => 'actionscandidats_partenaires',
    'actioncandidat_personne' => 'actionscandidats_personnes',
    'actioncandidat_zonegeographique' => 'actionscandidats_zonesgeographiques',
    'actioninsertion' => 'actionsinsertion',
    'actprof' => 'actsprofs',
    'actprof_pieceactprof' => 'actsprofs_piecesactsprofs',
    'adresse' => 'adresses',
    'adressefoyer' => 'adressesfoyers',
    'aideagricole' => 'aidesagricoles',
    'aideapre66' => 'aidesapres66',
    'aideapre66_pieceaide66' => 'aidesapres66_piecesaides66',
    'aideapre66_piececomptable66' => 'aidesapres66_piecescomptables66',
    'aidedirecte' => 'aidesdirectes',
    'allocationsoutienfamilial' => 'allocationssoutienfamilial',
    'amenaglogt' => 'amenagslogts',
    'amenaglogt_pieceamenaglogt' => 'amenagslogts_piecesamenagslogts',
    'anomalie' => 'anomalies',
    'apre66' => 'apres66',
    'apre_comiteapre' => 'apres_comitesapres',
    'apre_etatliquidatif' => 'apres_etatsliquidatifs',
    'apre_pieceapre' => 'apres_piecesapre',
    'aro_aco' => 'aros_acos',
    'avispcgdroitrsa' => 'avispcgdroitsrsa',
    'avispcgpersonne' => 'avispcgpersonnes',
    'budgetapre' => 'budgetsapres',
    'calculdroitrsa' => 'calculsdroitsrsa',
    'candidature_prog66' => 'candidatures_progs66', // table de laision entre actionscandidats_personnes et progsfichescandidatures66
	// Début codes ROME V3, nouvelle version
	'familleromev3' => 'famillesromesv3',
	'domaineromev3' => 'domainesromesv3',
	'metierromev3' => 'metiersromesv3',
	'appellationromev3' => 'appellationsromesv3',
	'correspondanceromev2v3' => 'correspondancesromesv2v3',
	'catalogueromev3' => 'cataloguesromesv3',
	'entreeromev3' => 'entreesromesv3',
	// Fin codes ROME V3, nouvelle version
    'cohortecomiteapre' => 'cohortescomitesapres',
    'comiteapre' => 'comitesapres',
    'comiteapre_participantcomite' => 'comitesapres_participantscomites',
    'condadmin' => 'condsadmins',
    'contactpartenaire' => 'contactspartenaires',
    //CER
    'contratinsertion' => 'contratsinsertion',
    'contratinsertion_user' => 'contratsinsertion_users',
    'motifcernonvalid66' => 'motifscersnonvalids66',
    'propodecisioncer66' => 'proposdecisionscers66',
    'motifcernonvalid66_propodecisioncer66' => 'motifscersnonvalids66_proposdecisionscers66',
    //
    'controleadministratif' => 'controlesadministratifs',
    'creancealimentaire' => 'creancesalimentaires',
    'decisionpdo' => 'decisionspdos',
    'descriptionpdo' => 'descriptionspdos',
    'detailaccosocfam' => 'detailsaccosocfams',
    'detailaccosocindi' => 'detailsaccosocindis',
    'detailcalculdroitrsa' => 'detailscalculsdroitsrsa',
    'detaildifdisp' => 'detailsdifdisps',
    'detaildiflog' => 'detailsdiflogs',
    'detaildifsoc' => 'detailsdifsocs',
    'detaildroitrsa' => 'detailsdroitsrsa',
    'detailnatmob' => 'detailsnatmobs',
    /// Dsp CG
    'detailmoytrans' => 'detailsmoytrans',
    'detaildifsocpro' => 'detailsdifsocpros',
    'detailprojpro' => 'detailsprojpros',
    'detailfreinform' => 'detailsfreinforms',
    'detailconfort' => 'detailsconforts',
    'dsp_rev' => 'dsps_revs',
    'detaildifsoc_rev' => 'detailsdifsocs_revs',
    'detailaccosocfam_rev' => 'detailsaccosocfams_revs',
    'detailaccosocindi_rev' => 'detailsaccosocindis_revs',
    'detaildifdisp_rev' => 'detailsdifdisps_revs',
    'detailnatmob_rev' => 'detailsnatmobs_revs',
    'detaildiflog_rev' => 'detailsdiflogs_revs',
    'detailmoytrans_rev' => 'detailsmoytrans_revs',
    'detaildifsocpro_rev' => 'detailsdifsocpros_revs',
    'detailprojpro_rev' => 'detailsprojpros_revs',
    'detailfreinform_rev' => 'detailsfreinforms_revs',
    'detailconfort_rev' => 'detailsconforts_revs',
    /// Fin DSP CG
    'detailressourcemensuelle' => 'detailsressourcesmensuelles',
    'detailressourcemensuelle_ressourcemensuelle' => 'detailsressourcesmensuelles_ressourcesmensuelles',
    'domiciliationbancaire' => 'domiciliationsbancaires',
    'dossiercaf' => 'dossierscaf',
    'dossiersimplifie' => 'dossierssimplifies',
    'etatliquidatif' => 'etatsliquidatifs',
    'formpermfimo' => 'formspermsfimo',
    'formpermfimo_pieceformpermfimo' => 'formspermsfimo_piecesformspermsfimo',
    'formqualif' => 'formsqualifs',
    'formqualif_pieceformqualif' => 'formsqualifs_piecesformsqualifs',
    'fraisdeplacement66' => 'fraisdeplacements66',
    'grossesse' => 'grossesses',
    'identificationflux' => 'identificationsflux',
    'indicateurmensuel' => 'indicateursmensuels',
    'indicateursuivi' => 'indicateurssuivis',
    'informationeti' => 'informationseti',
    'infoagricole' => 'infosagricoles',
    'infofinanciere' => 'infosfinancieres',
    'integrationfichierapre' => 'integrationfichiersapre',
    'jetonfonction' => 'jetonsfonctions',
    'locvehicinsert_piecelocvehicinsert' => 'locsvehicinsert_pieceslocsvehicinsert',
    'locvehicinsert' => 'locsvehicinsert',
    'modecontact' => 'modescontact',
    'montantconsomme' => 'montantsconsommes',
    'motifsortie' => 'motifssortie',
    'orientstruct' => 'orientsstructs',
    'orientstruct_serviceinstructeur' => 'orientsstructs_servicesinstructeurs',
    'originepdo' => 'originespdos',
    'paiementfoyer' => 'paiementsfoyers',
    'parametrefinancier' => 'parametresfinanciers',
    'participantcomite' => 'participantscomites',
    'periodeimmersion' => 'periodesimmersion',
    'permisb_piecepermisb' => 'permisb_piecespermisb',
    'personne_referent' => 'personnes_referents',
    'pieceacccreaentr' => 'piecesaccscreaentr',
    'pieceacqmatprof' => 'piecesacqsmatsprofs',
    'pieceactprof' => 'piecesactsprofs',
    'pieceaide66' => 'piecesaides66',
    'pieceamenaglogt' => 'piecesamenagslogts',
    'pieceapre' => 'piecesapre',
    'piececomptable66' => 'piecescomptables66',
    'pieceformpermfimo' => 'piecesformspermsfimo',
    'pieceformqualif' => 'piecesformsqualifs',
    'piecelocvehicinsert' => 'pieceslocsvehicinsert',
    'piecepdo' => 'piecespdos',
    'piecepermisb' => 'piecespermisb',
    'prestform' => 'prestsform',
    'progfichecandidature66' => 'progsfichescandidatures66',
    'propopdo' => 'propospdos',
    'propopdo_situationpdo' => 'propospdos_situationspdos',
    'propopdo_statutdecisionpdo' => 'propospdos_statutsdecisionspdos',
    'propopdo_statutpdo' => 'propospdos_statutspdos',
    'recoursapre' => 'recoursapres',
    'reducrsa' => 'reducsrsa',
    'refpresta' => 'refsprestas',
    'regroupementzonegeo' => 'regroupementszonesgeo',
    'regroupementzonegeo_zonegeographique' => 'regroupementszonesgeo_zonesgeographiques',
    'relanceapre' => 'relancesapres',
    'repddtefp' => 'repsddtefp',
    'ressource_ressourcemensuelle' => 'ressources_ressourcesmensuelles',
    'ressourcemensuelle' => 'ressourcesmensuelles',
    'serviceinstructeur' => 'servicesinstructeurs',
    'situationdossierrsa' => 'situationsdossiersrsa',
    'situationpdo' => 'situationspdos',
    'statutdecisionpdo' => 'statutsdecisionspdos',
    'statutpdo' => 'statutspdos',
    'statutrdv' => 'statutsrdvs',
    'statutrdv_typerdv' => 'statutsrdvs_typesrdv',
    'structurereferente_zonegeographique' => 'structuresreferentes_zonesgeographiques',
    'structurereferente' => 'structuresreferentes',
    'suiviaideapre' => 'suivisaidesapres',
    'suiviaideapretypeaide' => 'suivisaidesaprestypesaides',
    'suiviappuiorientation' => 'suivisappuisorientation',
    'suiviinsertion' => 'suivisinsertion',
    'suiviinstruction' => 'suivisinstruction',
    'suspensiondroit' => 'suspensionsdroits',
    'suspensionversement' => 'suspensionsversements',
    'themeapre66' => 'themesapres66',
    'tiersprestataireapre' => 'tiersprestatairesapres',
    'titresejour' => 'titressejour',
    'totalisationacompte' => 'totalisationsacomptes',
    'traitementpdo' => 'traitementspdos',
    'traitementtypepdo' => 'traitementstypespdos',
    'transmissionflux' => 'transmissionsflux',
    'typeaction' => 'typesactions',
    'typeaideapre66' => 'typesaidesapres66',
    'pieceaide66_typeaideapre66' => 'piecesaides66_typesaidesapres66',
    'piececomptable66_typeaideapre66' => 'piecescomptables66_typesaidesapres66',
    'typenotifpdo' => 'typesnotifspdos',
    'typeorient' => 'typesorients',
    'typepdo' => 'typespdos',
    'typerdv' => 'typesrdv',
    'typocontrat' => 'typoscontrats',
    'user_zonegeographique' => 'users_zonesgeographiques',
    'zonegeographique' => 'zonesgeographiques',
    // Béta EPs
    'avissrmrep93' => 'avissrmreps93',
    'saisineep66' => 'saisineseps66',
    'bilanparcours66' => 'bilansparcours66',
    'manifestationbilanparcours66' => 'manifestationsbilansparcours66',
    'decisionreorientationep93' => 'decisionsreorientationseps93',
    'reorientationep93' => 'reorientationseps93',
    'saisinebilanparcoursep66' => 'saisinesbilansparcourseps66',
    'saisinepdoep66' => 'saisinespdoseps66',
    'dossierep' => 'dossierseps',
    'ep_zonegeographique' => 'eps_zonesgeographiques',
    'membreep' => 'membreseps',
    'presencemembreep' => 'presencesmembreseps',
    'fonctionmembreep' => 'fonctionsmembreseps',
    'commissionep' => 'commissionseps',
    'ep' => 'eps',
    'regroupementep' => 'regroupementseps',
    'gestionep' => 'gestionseps',
    'motifreorientep93' => 'motifsreorientseps93',
    'decisionsaisinebilanparcoursep66' => 'decisionssaisinesbilansparcourseps66',
    'decisionsaisinepdoep66' => 'decisionssaisinespdoseps66',
    'commissionep_membreep' => 'commissionseps_membreseps',
    'nonrespectsanctionep93' => 'nonrespectssanctionseps93',
    'relancenonrespectsanctionep93' => 'relancesnonrespectssanctionseps93',
    'decisionnonrespectsanctionep93' => 'decisionsnonrespectssanctionseps93',
    'ep_membreep' => 'eps_membreseps',
    'decisionpropopdo' => 'decisionspropospdos',
    'defautinsertionep66' => 'defautsinsertionseps66',
    'decisiondefautinsertionep66' => 'decisionsdefautsinsertionseps66',
    'nonorientationproep58' => 'nonorientationsproseps58',
    'decisionnonorientationproep58' => 'decisionsnonorientationsproseps58',
    'nonorientationproep93' => 'nonorientationsproseps93',
    'decisionnonorientationproep93' => 'decisionsnonorientationsproseps93',
    'nonorientationproep66' => 'nonorientationsproseps66',
    'regressionorientationep58' => 'regressionsorientationseps58',
    'decisionregressionorientationep58' => 'decisionsregressionsorientationseps58',
    'sanctionep58' => 'sanctionseps58',
    'decisionsanctionep58' => 'decisionssanctionseps58',
    'listesanctionep58' => 'listesanctionseps58',
    'sanctionep93' => 'sanctionseps93',
    'decisionsanctionep93' => 'decisionssanctionseps93',
    'sanctionrendezvousep58' => 'sanctionsrendezvouseps58',
    'decisionsanctionrendezvousep58' => 'decisionssanctionsrendezvouseps58',
    'objetcontratinsertion' => 'objetscontratsinsertion',
    // Données flux Pôle Emploi
    'informationpe' => 'informationspe',
    'historiqueetatpe' => 'historiqueetatspe',
    // Tables pour gérer la COV
    'themecov58' => 'themescovs58',
    'dossiercov58' => 'dossierscovs58',
    'propoorientationcov58' => 'proposorientationscovs58',
    'decisionpropoorientationcov58' => 'decisionsproposorientationscovs58',
    'propocontratinsertioncov58' => 'proposcontratsinsertioncovs58',
    'decisionpropocontratinsertioncov58' => 'decisionsproposcontratsinsertioncovs58',
    'propononorientationprocov58' => 'proposnonorientationsproscovs58',
    'decisionpropononorientationprocov58' => 'decisionsproposnonorientationsproscovs58',
	'nonorientationprocov58' => 'nonorientationsproscovs58',
	'regressionorientationcov58' => 'regressionsorientationscovs58',
	'decisionnonorientationprocov58' => 'decisionsnonorientationsproscovs58',
	'decisionregressionorientationcov58' => 'decisionsregressionsorientationscovs58',
    'passagecov58' => 'passagescovs58',
    'cov58' => 'covs58',
    'sitecov58' => 'sitescovs58',
    'sitecov58_zonegeographique' => 'sitescovs58_zonesgeographiques',
    'autreavissuspension' => 'autresavissuspension',
    'autreavisradiation' => 'autresavisradiation',
    'objetentretien' => 'objetsentretien',
    'fichiertraitementpdo' => 'fichierstraitementspdos',
    'criteredossiercov58' => 'criteresdossierscovs58',
    'courrierpdo' => 'courrierspdos',
    'courrierpdo_traitementpdo' => 'courrierspdos_traitementspdos',
    'textareacourrierpdo' => 'textareascourrierspdos',
    'contenutextareacourrierpdo' => 'contenustextareascourrierspdos',
    'fichiermodule' => 'fichiersmodules',
    // EPs restructuration
    'passagecommissionep' => 'passagescommissionseps',
    'compositionregroupementep' => 'compositionsregroupementseps',
    'signalementep93' => 'signalementseps93',
    'decisionsignalementep93' => 'decisionssignalementseps93',
    'contratcomplexeep93' => 'contratscomplexeseps93',
    'decisioncontratcomplexeep93' => 'decisionscontratscomplexeseps93',
    'motifsortie' => 'motifssortie',
    'criterefichecandidature' => 'criteresfichescandidature',
    'historiqueep' => 'historiqueseps',
    // Dossiers PCGs
    'dossierpcg66' => 'dossierspcgs66',
    'decisiondossierpcg66' => 'decisionsdossierspcgs66',
    'personnepcg66' => 'personnespcgs66',
    'decisiontraitementpcg66' => 'decisionstraitementspcgs66',
    'traitementpcg66' => 'traitementspcgs66',
    'decisionpersonnepcg66' => 'decisionspersonnespcgs66',
    'personnepcg66_situationpdo' => 'personnespcgs66_situationspdos',
    'personnepcg66_statutpdo' => 'personnespcgs66_statutspdos',
    'courrierpdo_traitementpcg66' => 'courrierspdos_traitementspcgs66',
    'compofoyerpcg66' => 'composfoyerspcgs66',
    'decisionpcg66' => 'decisionspcgs66',
    'questionpcg66' => 'questionspcgs66',
    'decisiondossierpcg66_decisionpersonnepcg66' => 'decisionsdossierspcgs66_decisionspersonnespcgs66',
    'decisiondossierpcg66_decisiontraitementpcg66' => 'decisionsdossierspcgs66_decisionstraitementspcgs66',
    'typersapcg66' => 'typesrsapcgs66',
    'decisiondossierpcg66_typersapcg66' => 'decisionsdossierspcgs66_typesrsapcgs66',
    'piecemodeletypecourrierpcg66' => 'piecesmodelestypescourrierspcgs66',
    'modeletypecourrierpcg66' => 'modelestypescourrierspcgs66',
    'modeletypecourrierpcg66_situationpdo' => 'modelestypescourrierspcgs66_situationspdos',
    'modeletraitementpcg66' => 'modelestraitementspcgs66',
    'mtpcg66_pmtcpcg66' => 'mtpcgs66_pmtcpcgs66',
    'typecourrierpcg66' => 'typescourrierspcgs66',
    'courrierpcg66' => 'courrierspcgs66',
    'orgtransmisdossierpcg66' => 'orgstransmisdossierspcgs66',
    'decdospcg66_orgdospcg66' => 'decsdospcgs66_orgsdospcgs66',
    'poledossierpcg66' => 'polesdossierspcgs66',
    // DSPS cg66
    'coderomemetierdsp66' => 'codesromemetiersdsps66',
    'coderomesecteurdsp66' => 'codesromesecteursdsps66',
    'historiqueemploi' => 'historiqueemplois',
    // Gestion des anomalies
    'gestionano' => 'gestionsanos',
    'conditionactiviteprealable' => 'conditionsactivitesprealables',
    'decisionnonorientationproep66' => 'decisionsnonorientationsproseps66',
    'nonoriente66' => 'nonorientes66',
    'gestionsanctionep58' => 'gestionssanctionseps58',
    // CUI CG66
	'adressecui' => 'adressescuis',
	'partenairecui' => 'partenairescuis',
	'personnecui' => 'personnescuis',
	'cui66' => 'cuis66',
	'adressecui66' => 'adressescuis66',
	'partenairecui66' => 'partenairescuis66',
	'personnecui66' => 'personnescuis66',
	'suspensioncui66' => 'suspensionscuis66',
	'propositioncui66' => 'propositionscuis66',
	'accompagnementcui66' => 'accompagnementscuis66',
	'immersioncui66' => 'immersionscuis66',
	'decisioncui66' => 'decisionscuis66',
	'rupturecui66' => 'rupturescuis66',
	'bilancui66' => 'bilanscuis66',
	'formationcui66' => 'formationscuis66',
	'periodeimmersioncui66' => 'periodesimmersioncuis66',
	'periodeimmersioncui66' => 'periodesimmersioncuis66',
	'propodecisioncui66' => 'proposdecisionscuis66',
	'emailcui' => 'emailscuis',
	'piecemailcui66' => 'piecesmailscuis66',
	'typecontratcui66' => 'typescontratscuis66',
	'textmailcui66' => 'textsmailscuis66',
	'motifrupturecui66' => 'motifsrupturescuis66',
	'motifrupturecui66_rupturecui66' => 'motifsrupturescuis66_rupturescuis66',
	'motifsuspensioncui66' => 'motifssuspensioncuis66',
	'motifsuspensioncui66_suspensioncui66' => 'motifssuspensioncuis66_suspensionscuis66',
	'historiquepositioncui66' => 'historiquepositionscuis66',
	'piecemanquantecui66' => 'piecesmanquantescuis66',
	'motifrefuscui66' => 'motifsrefuscuis66',

	'offreinsertion' => 'offresinsertion',
    'propoorientsocialecov58' => 'proposorientssocialescovs58',
    'decisionpropoorientsocialecov58' => 'decisionsproposorientssocialescovs58',
    // Workflow CER
    'cer93' => 'cers93',
    'compofoyercer93' => 'composfoyerscers93',
    'diplomecer93' => 'diplomescers93',
    'metierexerce' => 'metiersexerces',
    'secteuracti' => 'secteursactis',
    'expprocer93' => 'expsproscers93',
    'cohortecer93' => 'cohortescers93',
    'histochoixcer93' => 'histoschoixcers93',
    'naturecontrat' => 'naturescontrats',
    'sujetcer93' => 'sujetscers93',
    'cer93_sujetcer93' => 'cers93_sujetscers93',
    'soussujetcer93' => 'soussujetscers93',
    'dernierdossierallocataire' => 'derniersdossiersallocataires',
    'valeurparsoussujetcer93' => 'valeursparsoussujetscers93',
    'transfertpdv93' => 'transfertspdvs93',
    'cohortetransfertpdv93' => 'cohortestransfertspdvs93',
    'commentairenormecer93' => 'commentairesnormescers93',
    'commentairenormecer93_histochoixcer93' => 'commentairesnormescers93_histoschoixcers93',
    'secteurcui' => 'secteurscuis',
    'statistiqueministerielle' => 'statistiquesministerielles',
    'raisonsocialepartenairecui66' => 'raisonssocialespartenairescuis66',
    'tauxcgcui66' => 'tauxcgscuis66',
    'situationallocataire' => 'situationsallocataires',
    'questionnaired1pdv93' => 'questionnairesd1pdvs93',
    'tableausuivipdv93' => 'tableauxsuivispdvs93',
    'thematiquerdv' => 'thematiquesrdvs',
    'rendezvous_thematiquerdv' => 'rendezvous_thematiquesrdvs',
    'historiquedroit' => 'historiquesdroits',
    // Module FSE, CG 93
    'sortieaccompagnementd2pdv93' => 'sortiesaccompagnementsd2pdvs93',
    'questionnaired2pdv93' => 'questionnairesd2pdvs93',
    'populationd1d2pdv93' => 'populationsd1d2pdvs93',
    'cohorted2pdv93' => 'cohortesd2pdvs93',
    'gestiondoublon' => 'gestionsdoublons',
	// Module Fiche de prescription - CG93
	'thematiquefp93' => 'thematiquesfps93',
	'categoriefp93' => 'categoriesfps93',
	'filierefp93' => 'filieresfps93',
	'prestatairefp93' => 'prestatairesfps93',
	'actionfp93' => 'actionsfps93',
	'ficheprescription93' => 'fichesprescriptions93',
	'instantanedonneesfp93' => 'instantanesdonneesfps93',
	'cataloguepdifp93' => 'cataloguespdisfps93',
	'motifcontactfp93' => 'motifscontactsfps93',
	'ficheprescription93_motifcontactfp93' => 'fichesprescriptions93_motifscontactsfps93',
	'modtransmfp93' => 'modstransmsfps93',
	'ficheprescription93_modtransmfp93' => 'fichesprescriptions93_modstransmsfps93',
	'adresseprestatairefp93' => 'adressesprestatairesfps93',
	'motifnonretenuefp93' => 'motifsnonretenuesfps93',
	'motifnonintegrationfp93' => 'motifsnonintegrationsfps93',
	'motifactionachevefp93' => 'motifsactionachevesfps93',
	'ficheprescription93_motifactionachevefp93' => 'fichesprescriptions93_motifsactionachevesfps93',
	'motifnonactionachevefp93' => 'motifsnonactionachevesfps93',
	'ficheprescription93_motifnonactionachevefp93' => 'fichesprescriptions93_motifsnonactionachevesfps93',
	'documentbeneffp93' => 'documentsbenefsfps93',
	'documentbeneffp93_ficheprescription93' => 'documentsbenefsfps93_fichesprescriptions93',
	'prestatairehorspdifp93' => 'prestataireshorspdifps93',
	'cohorterendezvous' => 'cohortesrendezvous',
	'populationb3pdv93' => 'populationsb3pdvs93',
	'populationb4b5pdv93' => 'populationsb4b5pdvs93',
	'populationb6pdv93' => 'populationsb6pdvs93',
	'corpuspdv93' => 'corpuspdvs93',
	// Module de sauvegarde de recherche (en construction)
	'savesearch' => 'savesearchs',
	'group_savesearch' => 'groups_savesearchs',
	// Correspondance entre les personne_id
	'correspondancepersonne' => 'correspondancespersonnes', // Correspondance entre les personne_id
	'requestmanager' => 'requestsmanager',
	'requestgroup' => 'requestgroups',
	'adresse_canton' => 'adresses_cantons',
	// Tags
	'categorietag' => 'categorietags',
	'valeurtag' => 'valeurstags',
	'tag' => 'tags',
	'entite_tag' => 'entites_tags',
	// Conservation des données d'impression
	'dataimpression' => 'dataimpressions',
	// Paramêtrage des valeurs pour les programmes région
	'valprogfichecandidature66' => 'valsprogsfichescandidatures66',
	// Fiche de liaison
	'fichedeliaison_personne' => 'fichedeliaisons_personnes',
	'logicielprimo_primoanalyse' => 'logicielprimos_primoanalyses',
	'communautesr' => 'communautessrs',
	'communautesr_structurereferente' => 'communautessrs_structuresreferentes',
	'structurereferente_tableausuivipdv93' => 'structuresreferentes_tableauxsuivispdvs93',
	'service66' => 'services66',
	'destinataireemail' => 'destinatairesemails',
	'destinataireemail_fichedeliaison' => 'destinatairesemails_fichedeliaisons',
	// Tableau de bord
	'role_user' => 'roles_users',
	'categorieactionrole' => 'categoriesactionroles',
	// Module accompagnement 93
	'accompagnementbeneficiaire' => 'accompagnementsbeneficiaires',
	// Liens entre referents
	'dernierreferent' => 'derniersreferents',
	'poledossierpcg66_user' => 'polesdossierspcgs66_users',
	'actionroleresultuser' => 'actionrolesresultsusers'
);

/**
 * This is a key => value array of regex used to match words.
 * If key matches then the value is returned.
 *
 *  $singularRules = array('/(s)tatuses$/i' => '\1\2tatus', '/(matr)ices$/i' =>'\1ix','/(vert|ind)ices$/i')
 */
$singularRules = array();

/**
 * This is a key only array of singular words that should not be inflected.
 * You should not have to change this value below if you do change it use same format
 * as the $uninflectedPlural above.
 */
$uninflectedSingular = $uninflectedPlural;

/**
 * This is a key => value array of singular irregular words.
 * Most of the time this will be a reverse of the above $irregularPlural array
 * You should not have to change this value below if you do change it use same format
 *
 * $irregularSingular = array('atlases' => 'atlas', 'beefs' => 'beef', 'brothers' => 'brother')
 */
$irregularSingular = array_flip($irregularPlural);
?>