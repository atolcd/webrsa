begin;
-- Type: type_accoemploi

DROP TYPE IF EXISTS type_accoemploi;

CREATE TYPE type_accoemploi AS ENUM
   ('1801',
    '1802',
    '1803');
ALTER TYPE type_accoemploi OWNER TO webrsa;
-- Type: type_action

DROP TYPE IF EXISTS type_action;

CREATE TYPE type_action AS ENUM
   ('heure',
    'poste');
ALTER TYPE type_action OWNER TO webrsa;
-- Type: type_activitebeneficiaire

DROP TYPE IF EXISTS type_activitebeneficiaire;

CREATE TYPE type_activitebeneficiaire AS ENUM
   ('E',
    'F',
    'C',
    'P');
ALTER TYPE type_activitebeneficiaire OWNER TO webrsa;
-- Type: type_aidesubvreint

DROP TYPE IF EXISTS type_aidesubvreint;

CREATE TYPE type_aidesubvreint AS ENUM
   ('aide1',
    'aide2',
    'subv1',
    'subv2');
ALTER TYPE type_aidesubvreint OWNER TO webrsa;
-- Type: type_autreavisradiation

DROP TYPE IF EXISTS type_autreavisradiation;

CREATE TYPE type_autreavisradiation AS ENUM
   ('END',
    'RDC',
    'MOA');
ALTER TYPE type_autreavisradiation OWNER TO webrsa;
-- Type: type_autreavissuspension

DROP TYPE IF EXISTS type_autreavissuspension;

CREATE TYPE type_autreavissuspension AS ENUM
   ('END',
    'RDC',
    'STE',
    'MOA');
ALTER TYPE type_autreavissuspension OWNER TO webrsa;
-- Type: type_aviscommission

DROP TYPE IF EXISTS type_aviscommission;

CREATE TYPE type_aviscommission AS ENUM
   ('SDC',
    'SNR',
    'MAL');
ALTER TYPE type_aviscommission OWNER TO webrsa;
-- Type: type_aviscoordonnateur

DROP TYPE IF EXISTS type_aviscoordonnateur;

CREATE TYPE type_aviscoordonnateur AS ENUM
   ('VAL',
    'DEM');
ALTER TYPE type_aviscoordonnateur OWNER TO webrsa;
-- Type: type_booleannumber

/*DROP TYPE IF EXISTS type_booleannumber;

CREATE TYPE type_booleannumber AS ENUM
   ('0',
    '1');
ALTER TYPE type_booleannumber OWNER TO webrsa;*/
-- Type: type_cessderact

DROP TYPE IF EXISTS type_cessderact;

CREATE TYPE type_cessderact AS ENUM
   ('2701',
    '2702');
ALTER TYPE type_cessderact OWNER TO webrsa;
-- Type: type_choixparcours

DROP TYPE IF EXISTS type_choixparcours;

CREATE TYPE type_choixparcours AS ENUM
   ('maintien',
    'reorientation');
ALTER TYPE type_choixparcours OWNER TO webrsa;
-- Type: type_choixpdo

DROP TYPE IF EXISTS type_choixpdo;

CREATE TYPE type_choixpdo AS ENUM
   ('PDO',
    'JUS');
ALTER TYPE type_choixpdo OWNER TO webrsa;
-- Type: type_confort

DROP TYPE IF EXISTS type_confort;

CREATE TYPE type_confort AS ENUM
   ('2401',
    '2402',
    '2403',
    '2404');
ALTER TYPE type_confort OWNER TO webrsa;
-- Type: type_contractualisation

DROP TYPE IF EXISTS type_contractualisation;

CREATE TYPE type_contractualisation AS ENUM
   ('marche',
    'subvention',
    'internecg');
ALTER TYPE type_contractualisation OWNER TO webrsa;
-- Type: type_convention

DROP TYPE IF EXISTS type_convention;

CREATE TYPE type_convention AS ENUM
   ('CES',
    'EES');
ALTER TYPE type_convention OWNER TO webrsa;
-- Type: type_dateactive

DROP TYPE IF EXISTS type_dateactive;

CREATE TYPE type_dateactive AS ENUM
   ('datedepart',
    'datereception');
ALTER TYPE type_dateactive OWNER TO webrsa;
-- Type: type_decision

DROP TYPE IF EXISTS type_decision;

CREATE TYPE type_decision AS ENUM
   ('E',
    'V',
    'A',
    'R',
    'C');
ALTER TYPE type_decision OWNER TO webrsa;
-- Type: type_decisionapre

DROP TYPE IF EXISTS type_decisionapre;

CREATE TYPE type_decisionapre AS ENUM
   ('ACC',
    'REF');
ALTER TYPE type_decisionapre OWNER TO webrsa;
-- Type: type_decisioncomite

DROP TYPE IF EXISTS type_decisioncomite;

CREATE TYPE type_decisioncomite AS ENUM
   ('REF',
    'ACC',
    'AJ');
ALTER TYPE type_decisioncomite OWNER TO webrsa;
-- Type: type_decisioncontratcomplexeep93

DROP TYPE IF EXISTS type_decisioncontratcomplexeep93;

CREATE TYPE type_decisioncontratcomplexeep93 AS ENUM
   ('valide',
    'rejete',
    'annule',
    'reporte');
ALTER TYPE type_decisioncontratcomplexeep93 OWNER TO webrsa;
-- Type: type_decisioncontratcov

DROP TYPE IF EXISTS type_decisioncontratcov;

CREATE TYPE type_decisioncontratcov AS ENUM
   ('valide',
    'refuse',
    'annule',
    'reporte');
ALTER TYPE type_decisioncontratcov OWNER TO webrsa;
-- Type: type_decisionnonorientationproep93

DROP TYPE IF EXISTS type_decisionnonorientationproep93;

CREATE TYPE type_decisionnonorientationproep93 AS ENUM
   ('reorientation',
    'maintienref',
    'annule',
    'reporte');
ALTER TYPE type_decisionnonorientationproep93 OWNER TO webrsa;
-- Type: type_decisionnonrespectsanctionep93

DROP TYPE IF EXISTS type_decisionnonrespectsanctionep93;

CREATE TYPE type_decisionnonrespectsanctionep93 AS ENUM
   ('1reduction',
    '1maintien',
    '1pasavis',
    '1delai',
    '2suspensiontotale',
    '2suspensionpartielle',
    '2maintien',
    '2pasavis',
    'annule',
    'reporte');
ALTER TYPE type_decisionnonrespectsanctionep93 OWNER TO webrsa;
-- Type: type_decisionorientationcov

DROP TYPE IF EXISTS type_decisionorientationcov;

CREATE TYPE type_decisionorientationcov AS ENUM
   ('valide',
    'refuse',
    'annule',
    'reporte');
ALTER TYPE type_decisionorientationcov OWNER TO webrsa;
-- Type: type_decisionpcg

DROP TYPE IF EXISTS type_decisionpcg;

CREATE TYPE type_decisionpcg AS ENUM
   ('valide',
    'enattente');
ALTER TYPE type_decisionpcg OWNER TO webrsa;
-- Type: type_decisionreorientationep93

DROP TYPE IF EXISTS type_decisionreorientationep93;

CREATE TYPE type_decisionreorientationep93 AS ENUM
   ('accepte',
    'refuse',
    'annule',
    'reporte');
ALTER TYPE type_decisionreorientationep93 OWNER TO webrsa;
-- Type: type_decisionsignalementep93

DROP TYPE IF EXISTS type_decisionsignalementep93;

CREATE TYPE type_decisionsignalementep93 AS ENUM
   ('1reduction',
    '1maintien',
    '1pasavis',
    '1delai',
    '2suspensiontotale',
    '2suspensionpartielle',
    '2maintien',
    '2pasavis',
    'annule',
    'reporte');
ALTER TYPE type_decisionsignalementep93 OWNER TO webrsa;
-- Type: type_demarlog

DROP TYPE IF EXISTS type_demarlog;

CREATE TYPE type_demarlog AS ENUM
   ('1101',
    '1102',
    '1103');
ALTER TYPE type_demarlog OWNER TO webrsa;
-- Type: type_difdisp

DROP TYPE IF EXISTS type_difdisp;

CREATE TYPE type_difdisp AS ENUM
   ('0501',
    '0502',
    '0503',
    '0504',
    '0505',
    '0506',
    '0507',
    '0508',
    '0509',
    '0510',
    '0511',
    '0512',
    '0513',
    '0514');
ALTER TYPE type_difdisp OWNER TO webrsa;
-- Type: type_diflog

DROP TYPE IF EXISTS type_diflog;

CREATE TYPE type_diflog AS ENUM
   ('1001',
    '1002',
    '1003',
    '1004',
    '1005',
    '1006',
    '1007',
    '1008',
    '1009');
ALTER TYPE type_diflog OWNER TO webrsa;
-- Type: type_difsoc

DROP TYPE IF EXISTS type_difsoc;

CREATE TYPE type_difsoc AS ENUM
   ('0401',
    '0402',
    '0403',
    '0404',
    '0405',
    '0406',
    '0407');
ALTER TYPE type_difsoc OWNER TO webrsa;
-- Type: type_difsocpro

DROP TYPE IF EXISTS type_difsocpro;

CREATE TYPE type_difsocpro AS ENUM
   ('2101',
    '2102',
    '2103',
    '2104',
    '2105',
    '2106',
    '2107',
    '2108',
    '2109',
    '2110');
ALTER TYPE type_difsocpro OWNER TO webrsa;
-- Type: type_duractdomi

DROP TYPE IF EXISTS type_duractdomi;

CREATE TYPE type_duractdomi AS ENUM
   ('2104',
    '2105',
    '2106',
    '2107');
ALTER TYPE type_duractdomi OWNER TO webrsa;
-- Type: type_duree

DROP TYPE IF EXISTS type_duree;

CREATE TYPE type_duree AS ENUM
   ('0',
    '0.5',
    '1',
    '1.5',
    '2',
    '2.5',
    '3',
    '3.5',
    '4',
    '4.5',
    '5',
    '5.5',
    '6',
    '6.5',
    '7',
    '7.5',
    '8',
    '8.5',
    '9',
    '9.5',
    '10',
    '10.5',
    '11',
    '11.5',
    '12');
ALTER TYPE type_duree OWNER TO webrsa;
-- Type: type_etapecov

DROP TYPE IF EXISTS type_etapecov;

CREATE TYPE type_etapecov AS ENUM
   ('cree',
    'traitement',
    'ajourne',
    'finalise');
ALTER TYPE type_etapecov OWNER TO webrsa;
-- Type: type_etapedecisionep

DROP TYPE IF EXISTS type_etapedecisionep;

CREATE TYPE type_etapedecisionep AS ENUM
   ('ep',
    'cg');
ALTER TYPE type_etapedecisionep OWNER TO webrsa;
-- Type: type_etatcommissionep

DROP TYPE IF EXISTS type_etatcommissionep;

CREATE TYPE type_etatcommissionep AS ENUM
   ('cree',
    'quorum',
    'associe',
    'valide',
    'presence',
    'decisionep',
    'traiteep',
    'decisioncg',
    'traite',
    'annule',
    'reporte');
ALTER TYPE type_etatcommissionep OWNER TO webrsa;
-- Type: type_etatcov

DROP TYPE IF EXISTS type_etatcov;

CREATE TYPE type_etatcov AS ENUM
   ('cree',
    'associe',
    'valide',
    'decision',
    'traite',
    'finalise',
    'annule',
    'reporte');
ALTER TYPE type_etatcov OWNER TO webrsa;
-- Type: type_etatdossierapre

DROP TYPE IF EXISTS type_etatdossierapre;

CREATE TYPE type_etatdossierapre AS ENUM
   ('COM',
    'INC',
    'VAL',
    'TRA',
    'ANN');
ALTER TYPE type_etatdossierapre OWNER TO webrsa;
-- Type: type_etatdossiercov

DROP TYPE IF EXISTS type_etatdossiercov;

CREATE TYPE type_etatdossiercov AS ENUM
   ('cree',
    'associe',
    'traite',
    'annule',
    'reporte');
ALTER TYPE type_etatdossiercov OWNER TO webrsa;
-- Type: type_etatdossierep

DROP TYPE IF EXISTS type_etatdossierep;

CREATE TYPE type_etatdossierep AS ENUM
   ('associe',
    'decisionep',
    'decisioncg',
    'traite',
    'annule',
    'reporte');
ALTER TYPE type_etatdossierep OWNER TO webrsa;
-- Type: type_etatdossierpdo

DROP TYPE IF EXISTS type_etatdossierpdo;

CREATE TYPE type_etatdossierpdo AS ENUM
   ('attaffect',
    'attinstr',
    'instrencours',
    'attavistech',
    'attval',
    'dossiertraite',
    'attpj');
ALTER TYPE type_etatdossierpdo OWNER TO webrsa;
-- Type: type_etatop

DROP TYPE IF EXISTS type_etatop;

CREATE TYPE type_etatop AS ENUM
   ('atransmettre',
    'transmis');
ALTER TYPE type_etatop OWNER TO webrsa;
-- Type: type_etatpe

DROP TYPE IF EXISTS type_etatpe;

CREATE TYPE type_etatpe AS ENUM
   ('cessation',
    'inscription',
    'radiation');
ALTER TYPE type_etatpe OWNER TO webrsa;
-- Type: type_ficheliaisonnaturemobile

DROP TYPE IF EXISTS type_ficheliaisonnaturemobile;

CREATE TYPE type_ficheliaisonnaturemobile AS ENUM
   ('commune',
    'canton',
    'dept',
    'horsdept');
ALTER TYPE type_ficheliaisonnaturemobile OWNER TO webrsa;
-- Type: type_freinform

DROP TYPE IF EXISTS type_freinform;

CREATE TYPE type_freinform AS ENUM
   ('2301',
    '2302',
    '2303',
    '2304',
    '2305',
    '2306',
    '2307',
    '2308');
ALTER TYPE type_freinform OWNER TO webrsa;
-- Type: type_hispro

DROP TYPE IF EXISTS type_hispro;

CREATE TYPE type_hispro AS ENUM
   ('1901',
    '1902',
    '1903',
    '1904');
ALTER TYPE type_hispro OWNER TO webrsa;
-- Type: type_inscdememploi

DROP TYPE IF EXISTS type_inscdememploi;

CREATE TYPE type_inscdememploi AS ENUM
   ('4301',
    '4302',
    '4303',
    '4304');
ALTER TYPE type_inscdememploi OWNER TO webrsa;
-- Type: type_insertion

DROP TYPE IF EXISTS type_insertion;

CREATE TYPE type_insertion AS ENUM
   ('SOC',
    'EMP');
ALTER TYPE type_insertion OWNER TO webrsa;
-- Type: type_iscomplet

DROP TYPE IF EXISTS type_iscomplet;

CREATE TYPE type_iscomplet AS ENUM
   ('COM',
    'INC');
ALTER TYPE type_iscomplet OWNER TO webrsa;
-- Type: type_justificatif

DROP TYPE IF EXISTS type_justificatif;

CREATE TYPE type_justificatif AS ENUM
   ('CREA',
    'CDT',
    'CINS');
ALTER TYPE type_justificatif OWNER TO webrsa;
-- Type: type_motimodparco

DROP TYPE IF EXISTS type_motimodparco;

CREATE TYPE type_motimodparco AS ENUM
   ('CL',
    'EA');
ALTER TYPE type_motimodparco OWNER TO webrsa;
-- Type: type_moytrans

DROP TYPE IF EXISTS type_moytrans;

CREATE TYPE type_moytrans AS ENUM
   ('2001',
    '2002',
    '2003',
    '2004',
    '2005',
    '2006',
    '2007',
    '2008');
ALTER TYPE type_moytrans OWNER TO webrsa;
-- Type: type_munir

DROP TYPE IF EXISTS type_munir;

CREATE TYPE type_munir AS ENUM
   ('CER',
    'NCA',
    'CV',
    'AUT');
ALTER TYPE type_munir OWNER TO webrsa;
-- Type: type_nataccosocfam

DROP TYPE IF EXISTS type_nataccosocfam;

CREATE TYPE type_nataccosocfam AS ENUM
   ('0410',
    '0411',
    '0412',
    '0413');
ALTER TYPE type_nataccosocfam OWNER TO webrsa;
-- Type: type_nataccosocindi

DROP TYPE IF EXISTS type_nataccosocindi;

CREATE TYPE type_nataccosocindi AS ENUM
   ('0416',
    '0417',
    '0418',
    '0419',
    '0420');
ALTER TYPE type_nataccosocindi OWNER TO webrsa;
-- Type: type_natlog

DROP TYPE IF EXISTS type_natlog;

CREATE TYPE type_natlog AS ENUM
   ('0901',
    '0902',
    '0903',
    '0904',
    '0905',
    '0906',
    '0907',
    '0908',
    '0909',
    '0910',
    '0911',
    '0912',
    '0913');
ALTER TYPE type_natlog OWNER TO webrsa;
-- Type: type_natmob

DROP TYPE IF EXISTS type_natmob;

CREATE TYPE type_natmob AS ENUM
   ('2504',
    '2501',
    '2502',
    '2503');
ALTER TYPE type_natmob OWNER TO webrsa;
-- Type: type_natparco

DROP TYPE IF EXISTS type_natparco;

CREATE TYPE type_natparco AS ENUM
   ('AS',
    'PP',
    'PS');
ALTER TYPE type_natparco OWNER TO webrsa;
-- Type: type_naturelogement

DROP TYPE IF EXISTS type_naturelogement;

CREATE TYPE type_naturelogement AS ENUM
   ('P',
    'L',
    'H',
    'S',
    'A');
ALTER TYPE type_naturelogement OWNER TO webrsa;
-- Type: type_nivdipmaxobt

DROP TYPE IF EXISTS type_nivdipmaxobt;

CREATE TYPE type_nivdipmaxobt AS ENUM
   ('2601',
    '2602',
    '2603',
    '2604',
    '2605',
    '2606');
ALTER TYPE type_nivdipmaxobt OWNER TO webrsa;
-- Type: type_niveaudecisionep

DROP TYPE IF EXISTS type_niveaudecisionep;

CREATE TYPE type_niveaudecisionep AS ENUM
   ('nontraite',
    'decisionep',
    'decisioncg');
ALTER TYPE type_niveaudecisionep OWNER TO webrsa;
-- Type: type_nivetu

DROP TYPE IF EXISTS type_nivetu;

CREATE TYPE type_nivetu AS ENUM
   ('1201',
    '1202',
    '1203',
    '1204',
    '1205',
    '1206',
    '1207');
ALTER TYPE type_nivetu OWNER TO webrsa;
-- Type: type_no

DROP TYPE IF EXISTS type_no;

CREATE TYPE type_no AS ENUM
   ('N',
    'O');
ALTER TYPE type_no OWNER TO webrsa;
-- Type: type_nonadmis

DROP TYPE IF EXISTS type_nonadmis;

CREATE TYPE type_nonadmis AS ENUM
   ('CAN',
    'RSP');
ALTER TYPE type_nonadmis OWNER TO webrsa;
-- Type: type_nos

DROP TYPE IF EXISTS type_nos;

CREATE TYPE type_nos AS ENUM
   ('N',
    'O',
    'S');
ALTER TYPE type_nos OWNER TO webrsa;
-- Type: type_nov

DROP TYPE IF EXISTS type_nov;

CREATE TYPE type_nov AS ENUM
   ('N',
    'O',
    'V');
ALTER TYPE type_nov OWNER TO webrsa;
-- Type: type_num_contrat

DROP TYPE IF EXISTS type_num_contrat;

CREATE TYPE type_num_contrat AS ENUM
   ('PRE',
    'REN');
ALTER TYPE type_num_contrat OWNER TO webrsa;
-- Type: type_oamsp

DROP TYPE IF EXISTS type_oamsp;

CREATE TYPE type_oamsp AS ENUM
   ('oa',
    'msp');
ALTER TYPE type_oamsp OWNER TO webrsa;
-- Type: type_objectifimmersion

DROP TYPE IF EXISTS type_objectifimmersion;

CREATE TYPE type_objectifimmersion AS ENUM
   ('acquerir',
    'confirmer',
    'decouvrir',
    'initier');
ALTER TYPE type_objectifimmersion OWNER TO webrsa;
-- Type: type_orgpayeur

DROP TYPE IF EXISTS type_orgpayeur;

CREATE TYPE type_orgpayeur AS ENUM
   ('CAF',
    'MSA');
ALTER TYPE type_orgpayeur OWNER TO webrsa;
-- Type: type_orient

DROP TYPE IF EXISTS type_orient;

CREATE TYPE type_orient AS ENUM
   ('social',
    'prepro',
    'pro');
ALTER TYPE type_orient OWNER TO webrsa;
-- Type: type_originesanction

DROP TYPE IF EXISTS type_originesanction;

CREATE TYPE type_originesanction AS ENUM
   ('radiepe',
    'noninscritpe',
    'nonrespectcer');
ALTER TYPE type_originesanction OWNER TO webrsa;
-- Type: type_originesanctionep93

DROP TYPE IF EXISTS type_originesanctionep93;

CREATE TYPE type_originesanctionep93 AS ENUM
   ('orientstruct',
    'contratinsertion',
    'pdo',
    'radiepe');
ALTER TYPE type_originesanctionep93 OWNER TO webrsa;
-- Type: type_positionbilan

DROP TYPE IF EXISTS type_positionbilan;

CREATE TYPE type_positionbilan AS ENUM
   ('eplaudit',
    'eplparc',
    'attcga',
    'attct',
    'ajourne',
    'annule',
    'traite');
ALTER TYPE type_positionbilan OWNER TO webrsa;
-- Type: type_positioncer

DROP TYPE IF EXISTS type_positioncer;

CREATE TYPE type_positioncer AS ENUM
   ('encours',
    'attvalid',
    'annule',
    'fincontrat',
    'encoursbilan',
    'attrenouv',
    'perime',
    'nonvalid',
    'perimebilanarealiser',
    'bilanrealiseattenteeplparcours');
ALTER TYPE type_positioncer OWNER TO webrsa;
-- Type: type_positionfiche

DROP TYPE IF EXISTS type_positionfiche;

CREATE TYPE type_positionfiche AS ENUM
   ('enattente',
    'encours',
    'nonretenue',
    'sortie',
    'annule');
ALTER TYPE type_positionfiche OWNER TO webrsa;
-- Type: type_presence

DROP TYPE IF EXISTS type_presence;

CREATE TYPE type_presence AS ENUM
   ('PRE',
    'ABS',
    'EXC');
ALTER TYPE type_presence OWNER TO webrsa;
-- Type: type_presenceseanceep

DROP TYPE IF EXISTS type_presenceseanceep;

CREATE TYPE type_presenceseanceep AS ENUM
   ('present',
    'excuse',
    'absent',
    'remplacepar');
ALTER TYPE type_presenceseanceep OWNER TO webrsa;
-- Type: type_projpro

DROP TYPE IF EXISTS type_projpro;

CREATE TYPE type_projpro AS ENUM
   ('2201',
    '2202',
    '2203',
    '2204',
    '2205',
    '2206',
    '2207',
    '2208',
    '2209',
    '2210',
    '2211',
    '2212',
    '2213');
ALTER TYPE type_projpro OWNER TO webrsa;
-- Type: type_proposition

DROP TYPE IF EXISTS type_proposition;

CREATE TYPE type_proposition AS ENUM
   ('traitement',
    'parcours',
    'audition');
ALTER TYPE type_proposition OWNER TO webrsa;
-- Type: type_propositionbilanparcours

DROP TYPE IF EXISTS type_propositionbilanparcours;

CREATE TYPE type_propositionbilanparcours AS ENUM
   ('audition',
    'parcours',
    'traitement',
    'auditionpe',
    'parcourspe',
    'aucun');
ALTER TYPE type_propositionbilanparcours OWNER TO webrsa;
-- Type: type_qual

DROP TYPE IF EXISTS type_qual;

CREATE TYPE type_qual AS ENUM
   ('M.',
    'Mme.');
ALTER TYPE type_qual OWNER TO webrsa;
-- Type: type_regimefichecalcul

DROP TYPE IF EXISTS type_regimefichecalcul;

CREATE TYPE type_regimefichecalcul AS ENUM
   ('fagri',
    'ragri',
    'reel',
    'microbic',
    'microbicauto',
    'microbnc');
ALTER TYPE type_regimefichecalcul OWNER TO webrsa;
-- Type: type_reorientation

DROP TYPE IF EXISTS type_reorientation;

CREATE TYPE type_reorientation AS ENUM
   ('SP',
    'PS',
    'PP');
ALTER TYPE type_reorientation OWNER TO webrsa;
-- Type: type_reponseseanceep

DROP TYPE IF EXISTS type_reponseseanceep;

CREATE TYPE type_reponseseanceep AS ENUM
   ('confirme',
    'decline',
    'nonrenseigne',
    'remplacepar');
ALTER TYPE type_reponseseanceep OWNER TO webrsa;
-- Type: type_retenu

DROP TYPE IF EXISTS type_retenu;

CREATE TYPE type_retenu AS ENUM
   ('RET',
    'NRE');
ALTER TYPE type_retenu OWNER TO webrsa;
-- Type: type_roleparcours

DROP TYPE IF EXISTS type_roleparcours;

CREATE TYPE type_roleparcours AS ENUM
   ('equipe',
    'conseil');
ALTER TYPE type_roleparcours OWNER TO webrsa;
-- Type: type_rolereorient

DROP TYPE IF EXISTS type_rolereorient;

CREATE TYPE type_rolereorient AS ENUM
   ('referent',
    'equipe',
    'conseil');
ALTER TYPE type_rolereorient OWNER TO webrsa;
-- Type: type_sitfambilanparcours

DROP TYPE IF EXISTS type_sitfambilanparcours;

CREATE TYPE type_sitfambilanparcours AS ENUM
   ('couple',
    'coupleenfant',
    'isole',
    'isoleenfant');
ALTER TYPE type_sitfambilanparcours OWNER TO webrsa;
-- Type: type_sitpersdemrsa

DROP TYPE IF EXISTS type_sitpersdemrsa;

CREATE TYPE type_sitpersdemrsa AS ENUM
   ('0101',
    '0102',
    '0103',
    '0104',
    '0105',
    '0106',
    '0107',
    '0108',
    '0109');
ALTER TYPE type_sitpersdemrsa OWNER TO webrsa;
-- Type: type_sitperssocpro

DROP TYPE IF EXISTS type_sitperssocpro;

CREATE TYPE type_sitperssocpro AS ENUM
   ('AF',
    'EF',
    'RE');
ALTER TYPE type_sitperssocpro OWNER TO webrsa;
-- Type: type_sortieprocedurenrs93

DROP TYPE IF EXISTS type_sortieprocedurenrs93;

CREATE TYPE type_sortieprocedurenrs93 AS ENUM
   ('nvcontrat',
    'inscriptionpe');
ALTER TYPE type_sortieprocedurenrs93 OWNER TO webrsa;
-- Type: type_statutapre

DROP TYPE IF EXISTS type_statutapre;

CREATE TYPE type_statutapre AS ENUM
   ('C',
    'F');
ALTER TYPE type_statutapre OWNER TO webrsa;
-- Type: type_statutdecision

DROP TYPE IF EXISTS type_statutdecision;

CREATE TYPE type_statutdecision AS ENUM
   ('DEF',
    'UND');
ALTER TYPE type_statutdecision OWNER TO webrsa;
-- Type: type_statutoccupation

DROP TYPE IF EXISTS type_statutoccupation;

CREATE TYPE type_statutoccupation AS ENUM
   ('proprietaire',
    'locataire');
ALTER TYPE type_statutoccupation OWNER TO webrsa;
-- Type: type_themeep

DROP TYPE IF EXISTS type_themeep;

CREATE TYPE type_themeep AS ENUM
   ('reorientationseps93',
    'saisinesbilansparcourseps66',
    'saisinespdoseps66',
    'nonrespectssanctionseps93',
    'defautsinsertionseps66',
    'nonorientationsproseps58',
    'nonorientationsproseps93',
    'regressionsorientationseps58',
    'sanctionseps58',
    'signalementseps93',
    'sanctionsrendezvouseps58',
    'contratscomplexeseps93',
    'nonorientationsproseps66');
ALTER TYPE type_themeep OWNER TO webrsa;
-- Type: type_type_demande

DROP TYPE IF EXISTS type_type_demande;

CREATE TYPE type_type_demande AS ENUM
   ('DOD',
    'DRD');
ALTER TYPE type_type_demande OWNER TO webrsa;
-- Type: type_typeaidelogement

DROP TYPE IF EXISTS type_typeaidelogement;

CREATE TYPE type_typeaidelogement AS ENUM
   ('AEL',
    'AML');
ALTER TYPE type_typeaidelogement OWNER TO webrsa;
-- Type: type_typeapre

DROP TYPE IF EXISTS type_typeapre;

CREATE TYPE type_typeapre AS ENUM
   ('forfaitaire',
    'complementaire');
ALTER TYPE type_typeapre OWNER TO webrsa;
-- Type: type_typeauditionpe

DROP TYPE IF EXISTS type_typeauditionpe;

CREATE TYPE type_typeauditionpe AS ENUM
   ('noninscriptionpe',
    'radiationpe');
ALTER TYPE type_typeauditionpe OWNER TO webrsa;
-- Type: type_typecommission

DROP TYPE IF EXISTS type_typecommission;

CREATE TYPE type_typecommission AS ENUM
   ('cov',
    'ep');
ALTER TYPE type_typecommission OWNER TO webrsa;
-- Type: type_typecontrat

DROP TYPE IF EXISTS type_typecontrat;

CREATE TYPE type_typecontrat AS ENUM
   ('CDI',
    'CDD',
    'CON',
    'AUT');
ALTER TYPE type_typecontrat OWNER TO webrsa;
-- Type: type_typecontratact

DROP TYPE IF EXISTS type_typecontratact;

CREATE TYPE type_typecontratact AS ENUM
   ('CI',
    'CA',
    'SA');
ALTER TYPE type_typecontratact OWNER TO webrsa;
-- Type: type_typedemandeapre

DROP TYPE IF EXISTS type_typedemandeapre;

CREATE TYPE type_typedemandeapre AS ENUM
   ('FO',
    'AU');
ALTER TYPE type_typedemandeapre OWNER TO webrsa;
-- Type: type_typeentretien

DROP TYPE IF EXISTS type_typeentretien;

CREATE TYPE type_typeentretien AS ENUM
   ('PHY',
    'TEL',
    'COU',
    'MAI');
ALTER TYPE type_typeentretien OWNER TO webrsa;
-- Type: type_typeeplocale

DROP TYPE IF EXISTS type_typeeplocale;

CREATE TYPE type_typeeplocale AS ENUM
   ('audition',
    'parcours');
ALTER TYPE type_typeeplocale OWNER TO webrsa;
-- Type: type_typefichiertraitementpdo

DROP TYPE IF EXISTS type_typefichiertraitementpdo;

CREATE TYPE type_typefichiertraitementpdo AS ENUM
   ('courrier',
    'piecejointe');
ALTER TYPE type_typefichiertraitementpdo OWNER TO webrsa;
-- Type: type_typeformulaire

DROP TYPE IF EXISTS type_typeformulaire;

CREATE TYPE type_typeformulaire AS ENUM
   ('cg',
    'pe');
ALTER TYPE type_typeformulaire OWNER TO webrsa;
-- Type: type_typenotification

DROP TYPE IF EXISTS type_typenotification;

CREATE TYPE type_typenotification AS ENUM
   ('normale',
    'systematique');
ALTER TYPE type_typenotification OWNER TO webrsa;
-- Type: type_typetraitement

DROP TYPE IF EXISTS type_typetraitement;

CREATE TYPE type_typetraitement AS ENUM
   ('courrier',
    'revenu',
    'analyse',
    'aucun');
ALTER TYPE type_typetraitement OWNER TO webrsa;
-- Type: type_venu

DROP TYPE IF EXISTS type_venu;

CREATE TYPE type_venu AS ENUM
   ('VEN',
    'NVE');
ALTER TYPE type_venu OWNER TO webrsa;
-- Type: type_versement

DROP TYPE IF EXISTS type_versement;

CREATE TYPE type_versement AS ENUM
   ('DEM',
    'TIE');
ALTER TYPE type_versement OWNER TO webrsa;
-- Type: type_virement

DROP TYPE IF EXISTS type_virement;

CREATE TYPE type_virement AS ENUM
   ('RIB',
    'CHE');
ALTER TYPE type_virement OWNER TO webrsa;
commit;