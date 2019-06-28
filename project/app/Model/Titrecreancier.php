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
			'etat' => array('CREE', 'INSTRUCTION', 'ATTAVIS', 'VALIDAVIS','NONVALID','ATTENVOICOMPTA', 'ATTRETOURCOMPTA', 'TITREEMIS', 'PAY','SUP', 'RED', 'REMB'),
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
			)
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
		 *
		 * @return boolean
		 */
		public function setEtat($id) {
			$data = array();
			$data['id'] = $id;
			$data['etat'] = 'CREE';

			// Récupération des annulations / réductions liées à l'ID
			$titresAnnReduc = $this->Titresuiviannulationreduction->find('all',
				array(
					'conditions' => array( 'titrecreancier_id' => $id ),
					'order' => 'dtaction ASC'
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

			// Récupération des autres infos liées à l'ID
			$titresAutresInfos = $this->Titresuiviautreinfo->find('all',
				array(
					'conditions' => array( 'titrecreancier_id' => $id ),
					'order' => 'dtautreinfo ASC'
				)
			);

			if( isset($titresAutresInfos) && !empty($titresAutresInfos) ) {
				foreach($titresAutresInfos as $titre) {
					if($titre['Titresuiviautreinfo']['etat'] !== 'ANNULER'){
						if(strpos($titre['Typetitrecreancierautreinfo']['nom'], 'emboursement') !== false ) {
							$data['etat'] = 'REMB';
							break;
						}
					}
				}
			}


			// Mise à jour de l'état
			$this->begin();
			$success = $this->save($data);
			if($success){
				$this->commit();
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

			// Récupération des autres infos liées à l'ID
			$titresAutresInfos = $this->Titresuiviautreinfo->find('all',
				array(
					'conditions' => array( 'Titresuiviautreinfo.titrecreancier_id' => $id ),
					'order' => 'dtautreinfo ASC'
				)
			);

			if( isset($titresAutresInfos) && !empty($titresAutresInfos) ) {
				foreach($titresAutresInfos as $titre) {
					if($titre['Titresuiviautreinfo']['etat'] !== 'ANNULER'){
						if(strpos($titre['Typetitrecreancierautreinfo']['nom'], 'emboursement') !== false ) {
							$data['mnttitr'] = 0;
							break;
						}
					}
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

	}