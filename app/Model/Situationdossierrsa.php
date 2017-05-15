<?php
	/**
	 * Code source de la classe Situationdossierrsa.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Situationdossierrsa ...
	 *
	 * @package app.Model
	 */
	class Situationdossierrsa extends AppModel
	{

		public $name = 'Situationdossierrsa';
		public $useTable = 'situationsdossiersrsa';
		public $validate = array(
			'etatdosrsa' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'dtrefursa' => array(
				array(
					'rule' => 'date',
					'message' => 'Veuillez vérifier le format de la date.'
				),
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'moticlorsa' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			)
		);
		
		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 * 
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'etatdosrsa' => array('Z', '0', '1', '2', '3', '4', '5', '6'),
			'motirefursa' => array('F02', 'F04', 'F09', 'F85', 'F97', 'FDD', 'DSD', 'FDB', 'PCG'),
			'moticlorsa' => array('PCG', 'ECH', 'EFF', 'MUT', 'RGD', 'RFD', 'RAU', 'RST', 'RSO'),
		);
		
		public $belongsTo = array(
			'Dossier' => array(
				'className' => 'Dossier',
				'foreignKey' => 'dossier_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
		public $hasMany = array(
			'Suspensiondroit' => array(
				'className' => 'Suspensiondroit',
				'foreignKey' => 'situationdossierrsa_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Suspensionversement' => array(
				'className' => 'Suspensionversement',
				'foreignKey' => 'situationdossierrsa_id',
				'dependent' => false,
				'conditions' => '',
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
		 *
		 */
		public function etatOuvert() {
			return array( 'Z', '2', '3', '4' ); // Z => dossier ajouté avec le formulaire "Préconisation ..."
		}

		/**
		 *
		 */
		public function etatAttente() {
			return array( '0', 'Z' );
		}

		/**
		 * Import de Option::etatdosrsa
		 * 
		 * Enums pour les champs
		 *	- historiquesdroits.etatdosrsa
		 *	- situationsallocataires.etatdosrsa
		 *	- situationsdossiersrsa.etatdosrsa
		 *
		 * Retourne la liste des états de dossier.
		 * Peut-être filtré par une liste de clés d'états de dossier.
		 *
		 * @param array $etatsDemandes
		 * @return array liste des états à afficher.
		 * @example ClassRegistry::init('Dossier')->enum('etatdosrsa', array('filter' => $this->Situationdossierrsa->etatAttente()))
		 */
		public function etatdosrsa($etatsDemandes=array()) {

			$etats = array(
				'Z' => 'Non défini',
				'0'  => 'Nouvelle demande en attente de décision CG pour ouverture du droit',
				'1'  => 'Droit refusé',
				'2'  => 'Droit ouvert et versable',
				'3'  => 'Droit ouvert et suspendu (le montant du droit est calculable, mais l\'existence du droit est remis en cause)',
				'4'  => 'Droit ouvert mais versement suspendu (le montant du droit n\'est pas calculable)',
				'5'  => 'Droit clos',
				'6'  => 'Droit clos sur mois antérieur ayant eu des créances transferées ou une régularisation dans le mois de référence pour une période antérieure.'
			);

			if( empty($etatsDemandes) ) {
				return $etats;
			}
			else {
				$return = array();
				foreach( $etatsDemandes as $etatDemande ) {
					if( isset( $etats[$etatDemande] ) ) {
						$return[$etatDemande] = $etats[$etatDemande];
					}
				}
				return $return;
			}
		}

		/**
		 *
		 */
		public function droitsOuverts( $dossier_id ) {
			if( valid_int( $dossier_id ) ) {

				$qd_situation = array(
					'conditions' => array(
						'Situationdossierrsa.dossier_id' => $dossier_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$situation = $this->find( 'first', $qd_situation );

				return in_array( Set::extract( $situation, 'Situationdossierrsa.etatdosrsa' ), $this->etatOuvert() );
			}
			else {
				return false;
			}
		}

		/**
		 *
		 */
		public function droitsEnAttente( $dossier_id ) {
			if( valid_int( $dossier_id ) ) {

				$qd_situation = array(
					'conditions' => array(
						'Situationdossierrsa.dossier_id' => $dossier_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$situation = $this->find( 'first', $qd_situation );

				return in_array( Set::extract( $situation, 'Situationdossierrsa.etatdosrsa' ), $this->etatAttente() );
			}
			else {
				return false;
			}
		}

		/**
		 * Retourne l'id du dossier auquel est lié un enregistrement.
		 *
		 * @param integer $id L'id de l'enregistrement
		 * @return integer
		 */
		public function dossierId( $id ) {
			$querydata = array(
				'fields' => array( "{$this->alias}.dossier_id" ),
				'conditions' => array(
					"{$this->alias}.id" => $id
				),
				'recursive' => -1
			);

			$result = $this->find( 'first', $querydata );

			if( !empty( $result ) ) {
				return $result[$this->alias]['dossier_id'];
			}
			else {
				return null;
			}
		}
	}
?>
