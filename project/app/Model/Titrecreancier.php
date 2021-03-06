<?php
	/**
	 * Code source de la classe Titrecreancier.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'AppModel', 'Creance' );
	App::uses( 'AppModel', 'Paiementfoyer' );

	/**
	 * La classe TitreCreance ...
	 *
	 * @package app.Model
	 */
	class Titrecreancier extends AppModel
	{
		public $name = 'Titrecreancier';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/*
		 * List des valeurs pour les selections
		 * La valeur des etat decrit est dans le fichier titrecreancier.po
		 */
		public $fakeInLists = array(
			'haspiecejointe' => array('0', '1'),
			'etat' => array('CREE', 'INSTRUCTION', 'ATTAVIS', 'VALIDAVIS','NONVALID','ATTENVOICOMPTA', 'ATTRETOURCOMPTA', 'TITREEMIS', 'PAY','SUP', 'RED'),
			'typeadr' => array('D', 'P', 'R'),
			'etatadr' => array('CO', 'VO', 'VC', 'NC', 'AU'),
		);

		public $validate = array(
			'creances_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'qual' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'MR', 'MME'
						)
					)
				),
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'qualcjt' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'MR', 'MME'
						)
					)
				),
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			)
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Creance' => array(
				'className' => 'Creance',
				'foreignKey' => 'creance_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Motifemissiontitrecreancier' => array(
				'className' => 'Motifemissiontitrecreancier',
				'foreignKey' => 'motifemissiontitrecreancier_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Typetitrecreancier' => array(
				'className' => 'Typetitrecreancier',
				'foreignKey' => 'typetitrecreancier_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Titrecreancier\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Titresuiviannulationreduction' => array(
				'classname' => 'Titresuiviannulationreduction'
			),
			'Titresuiviautreinfo' => array(
				'classname' => 'Titresuiviautreinfo'
			),
			'Titresuiviinfopayeur' => array(
				'classname' => 'Titresuiviinfopayeur'
			),
			'Historiqueetat' => array(
				'classname' => 'Historiqueetat',
				'foreignKey' => 'modele_id',
			)
		);

		/**
		 * Liste des champs virtuels
		 */
		public $virtualFields = array(
			'commentaire_complet' => array(
				'type' => 'string',
				'postgres' => '( COALESCE( "%s"."mention", \'\' ) || \' \' || COALESCE( "%s"."commentairevalidateur", \'\' ) || \' \' )'
			),
		);

		/**
		 * Retourne l'id d'une creance à partir de l'id d'un TitreCreance.
		 *
		 * @param integer $titrecreancier_id
		 * @return integer
		 */
		public function creanceId( $titrecreancier_id ) {
			$qd_creance = array(
				'fields' => array( 'Titrecreancier.creance_id' ),
				'joins' => array(
					$this->join( 'Creance', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					'Titrecreancier.id' => $titrecreancier_id
				),
				'recursive' => -1
			);
			$creance = $this->find('first', $qd_creance);

			if( !empty( $creance ) ) {
				return $creance['Titrecreancier']['creance_id'];
			}
			else {
				return null;
			}
		}

		/**
		 * Retourne l'id d'un foyer à partir de l'id d'un TitreCreance.
		 *
		 * @param integer $titrecreancier_id
		 * @return integer
		 */
		public function foyerId( $creance_id ) {
			$qd_creance = "SELECT foyer_id FROM Creances WHERE id = ".$creance_id." LIMIT 1";
			$creance = $this->query($qd_creance);
			if( !empty( $creance ) ) {
				return $creance[0][0]['foyer_id'];
			}
			else {
				return null;
			}
		}

		/**
		 * Retourne l'id d'un dossier à partir de l'id d'un TitreCreance.
		 *
		 * @param integer $titrecreancier_id
		 * @return integer
		 */
		public function dossierId( $creance_id ) {
			$qd_creance = "SELECT Foyers.dossier_id FROM Creances INNER JOIN Foyers ON Foyers.id = Creances.foyer_id WHERE Creances.id = ".$creance_id." LIMIT 1";
			$creance = $this->query($qd_creance);
			if( !empty( $creance ) ) {
				return $creance[0][0]['dossier_id'];
			}
			else {
				return null;
			}

		}

		/**
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * @return array
		 */
		public function options() {
			$options = $this->enums();
			$options['Typetitrecreancier']['type'] = ClassRegistry::init( 'Typetitrecreancier' )->find( 'list' );
			$options['Typetitrecreancier']['type_actif'] = ClassRegistry::init( 'Typetitrecreancier' )->find( 'list', array( 'conditions' => array( 'actif' => true ) ) );
			return $options;
		}

		/**
		 * Met à jour l'état du titre en fonction de l'ID passé en paramètre
		 *
		 * @param int id
		 * @param string action
		 *
		 * @return boolean
		 */
		public function setEtat($id, $action) {
			$data = array();
			$data['id'] = $id;
			$dernierEtat = $this->Historiqueetat->find('all', array(
				'conditions' => array(
					'Historiqueetat.modele_id' => $id,
					'Historiqueetat.modele' => $this->name,
					'NOT' => array(
						'Historiqueetat.etat' => array('RED', 'SUP')
					)
				),
				'order' => array(
					'Historiqueetat.created' => 'DESC'
				)
			) );
			$data['etat'] = $dernierEtat[0]['Historiqueetat']['etat'];

			// Récupération des annulations / réductions liées à l'ID
			$titresAnnReduc = $this->Titresuiviannulationreduction->find('all',
				array(
					'conditions' => array( 'titrecreancier_id' => $id ),
					'order' => 'Titresuiviannulationreduction.modified DESC'
				)
			);

			if( isset($titresAnnReduc) && !empty($titresAnnReduc) ) {
				foreach($titresAnnReduc as $titre) {
					if($titre['Titresuiviannulationreduction']['etat'] !== 'ANNULER'){
						if($titre['Typetitrecreancierannulationreduction']['nom'] === 'réduction') {
							$data['etat'] = 'RED';
						} elseif ($titre['Typetitrecreancierannulationreduction']['nom'] === 'annulation') {
							$data['etat'] = 'SUP';
						}
						break;
					}
				}
			}

			// Mise à jour de l'état
			$this->begin();
			$success = $this->save($data);
			if(	$success &&
				$this->Historiqueetat->setHisto(
					$this->name,
					$id,
					$this->creanceId($id),
					$action,
					$data['etat'],
					$this->foyerId( $this->creanceId( $id ) ) )
			){
				$this->commit();
			} else {
				$this->rollback();
			}
			return $success;
		}

		/**
		 * Met à jour le montant du titre en fonction de l'ID passé en paramètre
		 *
		 * @param int id
		 *
		 * @return boolean
		 */
		 public function calculMontantTitre($id) {
			$data = array();
			$data['id'] = $id;

			$titreCreancier = $this->find('first',
			array(
				'conditions' => array(
					'Titrecreancier.id ' => $id
				),
				'contain' => false
			));

			// Récupération des annulations / réductions liées à l'ID
			$titresAnnReduc = $this->Titresuiviannulationreduction->find('all',
				array(
					'conditions' => array( 'Titresuiviannulationreduction.titrecreancier_id' => $id ),
					'order' => 'dtaction ASC'
				)
			);

			$data['mnttitr'] = $titreCreancier['Titrecreancier']['mntinit'];
			foreach($titresAnnReduc as $titre){
				if($titre['Titresuiviannulationreduction']['etat'] !== 'ANNULER') {
					$data['mnttitr'] =  $data['mnttitr'] - $titre['Titresuiviannulationreduction']['mtreduit'];
				}
			}

			// Mise à jour du montant du titre
			$this->begin();
			$success = $this->save($data);
			if($success){
				$this->commit();
			}else{
				$this->rollback();
			}
			return $success;
		 }

		/**
		 * Génération des informations récuperer pour la créaction d'un titre créancier.
		 *
		 * @param array $titrecreancier tableau d'informations de base
		 * @param integer $creance_id id technique de la créance dont récuperer les infos
		 * @param integer $foyer_id id technique du foyer dont récuperer les infos
		 *
		 * @return array $titrecreancier tableau d'informations remplis
		 *
		**/
		public function getInfoTitrecreancier($titrecreancier =array(), $creance_id = null, $foyer_id = null ){

			if (!is_null($creance_id)){
				/* get value from Créance */
				$creances = $this->Creance->find('first',
					array(
						'conditions' => array(
							'Creance.id ' => $creance_id
						),
						'contain' => false
					)
				);
				if ( !empty ($creances['Creance'] ) ) {
					$titrecreancier['Titrecreancier']['mnttitr'] = $creances['Creance']['mtsolreelcretrans'];
				}
			}

			if (!is_null($foyer_id)){
				/* get nom, prénom, nir du bénéficiaire */
				$personne = $this->Creance->Foyer->Personne->find('first',
					array(
						'conditions' => array(
							'Foyer.id ' => $foyer_id,
							'Prestation.rolepers' => 'DEM'
						),
						'contain' => array (
							'Foyer',
							'Prestation'
						)
					)
				);
				if ( !empty ($personne['Personne'] ) ) {
					$titrecreancier['Titrecreancier']['qual'] = $personne['Personne']['qual'] ;
					$titrecreancier['Titrecreancier']['nom'] = $personne['Personne']['nom']." ". $personne['Personne']['prenom']  ;
					$titrecreancier['Titrecreancier']['nir'] = $personne['Personne']['nir'] ;
					$titrecreancier['Titrecreancier']['numtel'] =( $personne['Personne']['numfixe'] == null ) ? $personne['Personne']['numport'] : $personne['Personne']['numfixe'] ;
				}

				/* get nom, prénom, nir du bénéficiaire */
				$personne = $this->Creance->Foyer->Personne->find('first',
					array(
						'conditions' => array(
							'Foyer.id ' => $foyer_id,
							'Prestation.rolepers' => 'CJT'
						),
						'contain' => array (
							'Foyer',
							'Prestation'
						)
					)
				);
				if ( !empty ($personne['Personne'] ) ) {
					$titrecreancier['Titrecreancier']['qualcjt'] = $personne['Personne']['qual'] ;
					$titrecreancier['Titrecreancier']['nomcjt'] = $personne['Personne']['nom']." ". $personne['Personne']['prenom']  ;
					$titrecreancier['Titrecreancier']['nircjt'] = $personne['Personne']['nir'] ;
				}

				/* get RIB from RIB foyer */
				$infoRib = $this->Creance->Foyer->find('first',
					array(
						'conditions' => array(
							'Foyer.id ' => $foyer_id
						),
						'contain' => array (
							'Paiementfoyer'
						)
					)
				);
				if ( !empty ($infoRib['Paiementfoyer'] ) ) {
					//Génération du Nom pour le RIB
					//Qualificatif
					if ( ! empty ($infoRib['Paiementfoyer'][0]['titurib']) ) {
						$titrecreancier['Titrecreancier']['titulairecompte'] =
						(__d('paiementfoyer', 'ENUM::TITURIB::'.$infoRib['Paiementfoyer'][0]['titurib'] ));
					} else {
						$titrecreancier['Titrecreancier']['titulairecompte'] = $titrecreancier['Titrecreancier']['qual'];
					}
					// Nom
					if (
						! empty($infoRib['Paiementfoyer'][0]['nomprenomtiturib'])
						&& !(
							$infoRib['Paiementfoyer'][0]['nomprenomtiturib'] != 'USAGER'
							|| $infoRib['Paiementfoyer'][0]['nomprenomtiturib'] != 'COMMUN'
						)
					){
						$titrecreancier['Titrecreancier']['titulairecompte'].= ' '.$infoRib['Paiementfoyer'][0]['nomprenomtiturib'];
					} else {
						$titrecreancier['Titrecreancier']['titulairecompte'].= ' '.$titrecreancier['Titrecreancier']['nom']  ;
					}

					$titrecreancier['Titrecreancier']['iban'] =
						$infoRib['Paiementfoyer'][0]['numdebiban']
						.$infoRib['Paiementfoyer'][0]['etaban']
						.$infoRib['Paiementfoyer'][0]['guiban']
						.$infoRib['Paiementfoyer'][0]['numcomptban']
						.$infoRib['Paiementfoyer'][0]['clerib']
						.$infoRib['Paiementfoyer'][0]['numfiniban'] ;
					$titrecreancier['Titrecreancier']['bic'] = $infoRib['Paiementfoyer'][0]['bic'];
					$titrecreancier['Titrecreancier']['comban'] = $infoRib['Paiementfoyer'][0]['comban'];
				}

				/* get Adresse from Adresse foyer */
				$infoAdress = $this->Creance->Foyer->Adressefoyer->find('all',
					array(
						'conditions' => array(
							'Foyer.id ' => $foyer_id,
							'Adressefoyer.rgadr' => '01'
						),
						'contain' => array (
							'Adresse',
							'Foyer'
						)
					)
				);
				$titrecreancier['Titrecreancier']['dtemm'] = $infoAdress[0]['Adressefoyer']['dtemm'];
				$titrecreancier['Titrecreancier']['typeadr'] = $infoAdress[0]['Adressefoyer']['typeadr'];
				$titrecreancier['Titrecreancier']['etatadr'] = $infoAdress[0]['Adressefoyer']['etatadr'];
				$titrecreancier['Titrecreancier']['complete'] = $infoAdress[0]['Adresse']['complete'];
				$titrecreancier['Titrecreancier']['localite'] = $infoAdress[0]['Adresse']['localite'];
			}

			return $titrecreancier;
		}

		/**
		 * Génération des informations récuperer pour le fichier FICA.
		 *
		 * @param array $titrecreanciers_ids tableau des id techniques des titrescreanciers dont récuperer les infos
		 *
		 * @return array $infosFICA tableau CSV remplis
		 *
		**/
		public function buildfica($titrecreanciers_ids = array() ){
			$infosFICA[] = Configure::read('Creances.FICA.Champs');

			foreach ( $titrecreanciers_ids as $key => $titrecreancier_id  ) {
				$infoFICA = array();
				$infosFICA[] = $this->getInfoFICA($infoFICA, $titrecreancier_id);
			}
			return $infosFICA ;
		}

		/**
		 * Liste des fihciers a récuperer pour le ZIP FICA.
		 *
		 * @param array $titrecreanciers_ids tableau des id techniques des titrescreanciers dont récuperer les infos
		 *
		 * @return array $infosFICA liste des file names et ID en table fichier module
		 *
		**/
		public function findfilesfica($titrecreanciers_ids = array() ){
			$infosFICA = array();

			foreach ( $titrecreanciers_ids as $key => $titrecreancier_id  ) {
				//Récupération des fichier lié au Titre créancier
				$files = $this->Fichiermodule->sqListeFichiersLies('Titrecreancier' ,$titrecreancier_id);
				$infosFICA =array_merge( $infosFICA, $files);

				//Récupétation des fichier lié aux créances
				$creances = $this->find('first',
					array (
						'fields' => array('creance_id'),
						'recursive' => -1,
						'conditions' => array('id' => $titrecreancier_id)
					)
				);
				$creance_id = $creances['Titrecreancier']['creance_id'];

				//Récupération des fichier lié à la Creances lié au Titre créancier
				$files = $this->Fichiermodule->sqListeFichiersLies('Creances' ,$creance_id);
				$infosFICA = array_merge( $infosFICA, $files);
			}

			return $infosFICA ;
		}

		/**
		 * Génération des informations récuperer pour le fichier FICA.
		 *
		 * @param array $titrecreancier tableau d'informations de base
		 * @param integer $titrecreancier_id id technique du titrecreancier dont récuperer les infos
		 *
		 * @return array $titrecreancier tableau d'informations remplis
		 *
		**/
		public function getInfoFICA($infoFICA = array(), $titrecreancier_id = null ){
			$creance_id = $this->creanceId( $titrecreancier_id );
			$foyer_id = $this->foyerId( $creance_id );

			if (!is_null($titrecreancier_id)){
				/* get value from Titrecreancier */
				$titrecreancier = $this->find('first',
					array(
						'conditions' => array(
							'Titrecreancier.id ' => $titrecreancier_id
						),
						'contain' => false
					)
				);
				//listTypes
				$listTypes = $this->Typetitrecreancier->find(
					'list',
					array(
						'fields' => array ('id', 'name')
					)
				);

				if ( !empty ($titrecreancier['Titrecreancier'] ) ) {
					$creancier = $this->Creance->find('first',
						array(
							'conditions' => array(
								'Creance.id ' =>$creance_id
							),
							'contain' => false,
						)
					);
					$personne = $this->Creance->Foyer->Personne->find('first',
						array(
							'conditions' => array(
								'Foyer.id ' => $foyer_id,
								'Prestation.rolepers' => 'DEM'
							),
							'contain' => array (
								'Foyer',
								'Prestation',
							)
						)
					);
					$dossier = $this->Creance->Foyer->Dossier->Detaildroitrsa->find('first',
						array(
							'conditions' => array(
								'Detaildroitrsa.dossier_id ' => $personne['Foyer']['dossier_id'],
							),
							'contains' => array (
								'Detailcalculdroitrsa'
							)
						)
					);
					$adresse = $this->Creance->Foyer->Adressefoyer->find('all',
					array(
						'conditions' => array(
							'foyer_id' =>$foyer_id
						)
						)
					);
					$adresseComplete = implode(' ', array(
						$adresse[0]['Adresse']['numvoie'],
						$adresse[0]['Adresse']['libtypevoie'],
						$adresse[0]['Adresse']['nomvoie'],
						$adresse[0]['Adresse']['codepos'],
						$adresse[0]['Adresse']['nomcom'])
					);

					$infoFICA['PAIEMENT'] = Configure::read('Creances.FICA.TypePaiement');
					$infoFICA['CODTIERS'] = Configure::read('Creances.FICA.CodeTiers');
					$infoFICA['REF'] = $titrecreancier_id;

					$arrayValsSCC = Configure::read('Creances.FICA.SCC');
					if ( !empty ($dossier['Detailcalculdroitrsa'][0]['natpf']) ) {
						$infoFICA['SCC'] = $arrayValsSCC[$dossier['Detailcalculdroitrsa'][0]['natpf']];
					} else {
						$infoFICA['SCC'] = null;
					}

					$infoFICA['MONTANT'] = $titrecreancier['Titrecreancier']['mnttitr'] ;

					$infoFICA['LIBVIR'] =
						 __m('Indu RSA from').
						date('dmY', strtotime( $creancier['Creance']['ddregucre'] ) ).
						__m('to').
						date('dmY', strtotime( $creancier['Creance']['dfregucre'] ) ) ;
					$infoFICA['OBJET'] = '';
					$infoFICA['OBS'] = '';
					$infoFICA['OBS2'] =
						$dossier['Dossier']['matricule'].' / '.
						$titrecreancier['Titrecreancier']['nom'].' / '.
						$listTypes[$titrecreancier['Titrecreancier']['typetitrecreancier_id']].' / '.
						date('d-m-Y', strtotime( $personne['Personne']['dtnai']) ).' à '. $personne['Personne']['nomcomnai'] . ' / '.
						$adresseComplete.' / '.
						$creancier['Creance']['natcre'].' / '.
						__d('creance', 'ENUM::MOTIINDU::'.$creancier['Creance']['motiindu']).' / '.
						date('d-m-Y', strtotime($creancier['Creance']['dtdercredcretrans']))
					;
					if ( !empty ($titrecreancier['Titrecreancier']['bic']) ) {
						$infoFICA['RIB'] = $titrecreancier['Titrecreancier']['iban'];
					}else{
						$infoFICA['RIB'] = $titrecreancier['Titrecreancier']['bic'].$titrecreancier['Titrecreancier']['iban'];
					}

					if ( $infoFICA['CODTIERS'] == 999999 ){
						$infoFICA['LIBRIB'] = $titrecreancier['Titrecreancier']['titulairecompte'];

						$infoFICA['DESTCIVILITE'] = $titrecreancier['Titrecreancier']['qual'] ;
						if (!is_null($foyer_id)){
							if ( !empty ($personne['Personne'] ) ) {
								$infoFICA['DESTNOM'] = $personne['Personne']['nom'];
								$infoFICA['DESTPRENOM'] = $personne['Personne']['prenom']  ;
							}
						}
						$infoFICA['DESTCODPOSTAL'] = substr($titrecreancier['Titrecreancier']['localite'], 0, 5);
						$infoFICA['DESTCOMMUNE'] = substr($titrecreancier['Titrecreancier']['localite'], 5);
						$infoFICA['DESTADRESSE'] = $titrecreancier['Titrecreancier']['complete'];
						$infoFICA['DESTADRESSE2'] = '';
					}
				}
			}
			return $infoFICA;
		}

		/**
		 * Fonction de comptage des suivis du titre
		 *
		 * @param $titrecreancier_id => identifiant du titre
		 *
		 * @return $count => nombre de suivits liée au titre
		 *
		**/
		public function getCount($titrecreancier_id){
			$count = null;

			$Titresuiviannulationreduction = $this->Titresuiviannulationreduction->find('all', array('conditions' => array('Titresuiviannulationreduction.titrecreancier_id' => $titrecreancier_id)) );
			$count = count($Titresuiviannulationreduction);

			$Titresuiviautreinfo = $this->Titresuiviautreinfo->find('all', array('conditions' => array('Titresuiviautreinfo.titrecreancier_id' => $titrecreancier_id)) );
			$count = $count + count($Titresuiviautreinfo);

			$Titresuiviinfopayeur = $this->Titresuiviinfopayeur->find('all', array('conditions' => array('Titresuiviinfopayeur.titrecreancier_id' => $titrecreancier_id)) );
			$count = $count + count($Titresuiviinfopayeur);

			return $count;
		}

	}