<?php
	/**
	 * Code source de la classe Titresuiviautreinfo.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'WebrsaAccessTitressuivisautresinfos', 'Utility' );

	/**
	 * La classe Titresuiviautreinfo ...
	 *
	 * @package app.Model
	 */
	class Titresuiviautreinfo extends AppModel
	{
		public $name = 'Titresuiviautreinfo';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'titressuivisautresinfos';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('WebrsaAccessTitressuivisautresinfos', 'WebrsaTitresuiviautreinfo');

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Belongs To".
		 * @var array
		 */
		public $belongsTo = array(
			'Typetitrecreancierautreinfo' => array(
				'className' => 'Typetitrecreancierautreinfo',
				'foreignKey' => 'typesautresinfos_id',
				'conditions' => array('Typetitrecreancierautreinfo.actif' => 1),
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
					'Fichiermodule.modele = \'Titresuiviautreinfo\'',
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
		 * Renvoi la requête permettant d'avoir la liste des autres infos
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
						'Titresuiviautreinfo.titrecreancier_id' => $titrecreancier_id
					),
					'contain' => false,
					'order' => array(
						'Titresuiviautreinfo.dtautreinfo DESC',
						'Titresuiviautreinfo.id DESC',
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
				'controller' => ClassRegistry::init('TitressuivisautresinfosController'),
				'webrsaModelName' => $this->WebrsaTitresuiviautreinfo,
				'webrsaAccessName' => 'WebrsaAccessTitressuivisautresinfos',
				'mainModelName' => $this
			);
		}

		/**
		 * Renvoi la liste des titres d'autres infos
		 * en ajoutant les droits à la suppression
		 *
		 * @param array titresAutresInfos
		 *
		 * @return array
		 */
		public function getList($titresAutreInfo){
			$reverseTitre = array_reverse($titresAutreInfo);

			foreach( $reverseTitre as $numTitre => $titre ) {
				$nomTypeAutreInfo = $this->Typetitrecreancierautreinfo->find('first', array(
					'fields' => 'Typetitrecreancierautreinfo.nom',
					'conditions' => array('Typetitrecreancierautreinfo.id' => $titre['Titresuiviautreinfo']['typesautresinfos_id'])
				));
				$titre['Typetitrecreancierautreinfo']['nom'] = $nomTypeAutreInfo['Typetitrecreancierautreinfo']['nom'];
				$reverseTitre[$numTitre] = $titre;
			}
			$titresAutreInfo = array_reverse($reverseTitre);
			$titresAutreInfo = $this->suppressionPossible( $titresAutreInfo );
			$titresAutreInfo = $this->annulationPossible( $titresAutreInfo );

			return $titresAutreInfo;
		}

		/**
		 * Vérifie si un ajout d'une autre info est possible
		 * @param int titrecreancier_id : ID du titre créancier
		 * @return boolean
		 */
		public function ajoutPossible($titrecreancier_id) {
			return true;
		}

		/**
		 * Vérifie quelle autre info est supprimable
		 *
		 * @param array : Tableau des titres d'autres infos
		 * @return array
		 */
		public function suppressionPossible($titres){
			foreach( $titres as $numTitre => $titre ){
				if( $numTitre == 0 || $titre['Titresuiviautreinfo']['etat'] === 'ANNULER' ) {
					$titre['/Titressuivisautresinfos/delete'] = true;
				}
				$titres[$numTitre] = $titre;
			}

			return $titres;
		}

		/**
		 * Vérifie quelle autre info est supprimable
		 *
		 * @param array : Tableau des titres d'autres infos
		 * @return array
		 */
		public function annulationPossible($titres){
			foreach( $titres as $numTitre => $titre ){
				if( $titre['Titresuiviautreinfo']['etat'] === 'ANNULER' ) {
					$titre['/Titressuivisautresinfos/cancel'] = false;
				}
				$titres[$numTitre] = $titre;
			}

			return $titres;
		}
	}