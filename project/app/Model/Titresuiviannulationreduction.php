<?php
	/**
	 * Code source de la classe Titresuiviannulationreduction.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'WebrsaAccessTitressuivisannulationsreductions', 'Utility' );

	/**
	 * La classe Titresuiviannulationreduction ...
	 *
	 * @package app.Model
	 */
	class Titresuiviannulationreduction extends AppModel
	{
		public $name = 'Titresuiviannulationreduction';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'titressuivisannulationreduction';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('WebrsaAccessTitressuivisannulationsreductions', 'WebrsaTitresuiviannulationreduction');

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Gedooo.Gedooo',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		public $fakeInLists = array(
			'haspiecejointe' => array('0', '1'),
		);

		public $validate = array(
			'etat' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'ENCOURS', 'ANNULER',
						)
					)
				)
			),
		);

		/**
		 * Associations "Belongs To".
		 * @var array
		 */
		public $belongsTo = array(
			'Typetitrecreancierannulationreduction' => array(
				'className' => 'Typetitrecreancierannulationreduction',
				'foreignKey' => 'typeannulationreduction_id',
				'conditions' => array('Typetitrecreancierannulationreduction.actif' => 1),
				'fields' => '',
				'order' => ''
			),
			'Titrecreancier' => array(
				'className' => 'Titrecreancier',
				'foreignKey' => 'titrecreancier_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
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
					'Fichiermodule.modele = \'Titresuiviannulationreduction\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		/**
		 * Renvoi la requête permettant d'avoir la liste des annulation / réduction
		 * d'un titre de recette selon son ID
		 *
		 * @param int titrecreancier_id
		 *
		 * @return array
		 */
		public function getQuery($titrecreancier_id) {
			return 	array(
					'fields' => array_merge(
						$this->fields()
						,array(
							$this->Fichiermodule->sqNbFichiersLies( $this, 'nb_fichiers_lies' )
						)
					),
					'conditions' => array(
						'Titresuiviannulationreduction.titrecreancier_id' => $titrecreancier_id
					),
					'contain' => false,
					'order' => array(
						'Titresuiviannulationreduction.dtaction DESC',
						'Titresuiviannulationreduction.id DESC',
					)
			);
		}

		/**
		 * Renvoi le context permettant l'appel à WebrsaAccess
		 *
		 * @return array
		 */
		public function getContext() {
			return array(
				'controller' => ClassRegistry::init('TitressuivisannulationsreductionsController'),
				'webrsaModelName' => $this->WebrsaTitresuiviannulationreduction,
				'webrsaAccessName' => 'WebrsaAccessTitressuivisannulationsreductions',
				'mainModelName' => $this
			);
		}

		/**
		 * Renvoi la liste des titres d'annulations / réductions
		 * en ajoutant le calcul des montants et des droits à la suppression
		 *
		 * @param array titresAnnReduc
		 * @param int montantInitial
		 *
		 * @return array
		 */
		public function getList($titresAnnReduc, $montantInitial){
			$reverseTitre = array_reverse($titresAnnReduc);

			foreach( $reverseTitre as $numTitre => $titre ) {
				$nomTypeAnnRed = $this->Typetitrecreancierannulationreduction->find('first', array(
					'fields' => 'Typetitrecreancierannulationreduction.nom',
					'conditions' => array('Typetitrecreancierannulationreduction.id' => $titre['Titresuiviannulationreduction']['typeannulationreduction_id'])
				));
				$titre['Typetitrecreancierannulationreduction']['nom'] = $nomTypeAnnRed['Typetitrecreancierannulationreduction']['nom'];

				if( $numTitre == 0 ) {
					$titre['Titresuiviannulationreduction']['mtavantacte'] = $montantInitial;
					$mtavant_tmp = $montantInitial;
				} else {
					$titre['Titresuiviannulationreduction']['mtavantacte'] = $mtavant_tmp;
				}

				if( $titre['Titresuiviannulationreduction']['etat'] !== 'ANNULER' ) {
					$titre['Titresuiviannulationreduction']['mtapresacte'] = $mtavant_tmp - $titre['Titresuiviannulationreduction']['mtreduit'];
					$mtavant_tmp = $titre['Titresuiviannulationreduction']['mtapresacte'];
				} else {
					$titre['Titresuiviannulationreduction']['mtapresacte'] = '';
					$mtavant_tmp = $titre['Titresuiviannulationreduction']['mtavantacte'];
				}
				$reverseTitre[$numTitre] = $titre;
			}
			$titresAnnReduc = array_reverse($reverseTitre);
			$titresAnnReduc = $this->suppressionPossible( $titresAnnReduc );
			$titresAnnReduc = $this->annulationPossible( $titresAnnReduc );

			return $titresAnnReduc;
		}

		/**
		 * Vérifie si un ajout d'une annulation / réduction est possible
		 * @param int titrecreancier_id : ID du titre créancier
		 * @return boolean
		 */
		public function ajoutPossible($titrecreancier_id) {
			$titresLies = $this->find('all', array(
				'conditions' => array('titrecreancier_id' => $titrecreancier_id),
				'order' => array('Titresuiviannulationreduction.dtaction ASC', 'Titresuiviannulationreduction.id ASC')
			));
			if( isset($titresLies) && !empty($titresLies) ) {
				foreach( $titresLies as $titre ) {
					if( $titre['Titresuiviannulationreduction']['etat'] !== 'ANNULER' ) {
						if( $titre['Typetitrecreancierannulationreduction']['nom'] !== 'annulation' ) {
							return true;
						} else {
							return false;
						}
					}
				}
			}

			return true;
		}

		/**
		 * Vérifie quelle annulation / réduction est supprimable
		 *
		 * @param array : Tableau des titres d'annulation / réduction
		 * @return array
		 */
		public function suppressionPossible($titres){
			foreach( $titres as $numTitre => $titre ){
				if( $numTitre == 0 || $titre['Titresuiviannulationreduction']['etat'] === 'ANNULER' ) {
					$titre['/Titressuivisannulationsreductions/delete'] = true;
				}
				$titres[$numTitre] = $titre;
			}

			return $titres;
		}

		/**
		 * Vérifie quelle annulation / réduction est supprimable
		 *
		 * @param array : Tableau des titres d'annulation / réduction
		 * @return array
		 */
		public function annulationPossible($titres){
			foreach( $titres as $numTitre => $titre ){
				if( $titre['Titresuiviannulationreduction']['etat'] === 'ANNULER' ) {
					$titre['/Titressuivisannulationsreductions/cancel'] = false;
				}
				$titres[$numTitre] = $titre;
			}

			return $titres;
		}
	}
