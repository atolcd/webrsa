<?php
	/**
	 * Code source de la classe Ressource.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Ressource ...
	 *
	 * @package app.Model
	 */
	class Ressource extends AppModel
	{
		public $name = 'Ressource';

		protected $_modules = array( 'caf' );

		public $actsAs = array(
			'Allocatairelie',
		);

		public $validate = array(
			'ddress' => array(
				'rule' => 'date',
				'message' => 'Veuillez entrer une date valide'
			),
			'dfress' => array(
				'rule' => 'date',
				'message' => 'Veuillez entrer une date valide'
			)
		);

		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Ressourcemensuelle' => array(
				'className' => 'Ressourcemensuelle',
				'foreignKey' => 'ressource_id',
				'dependent' => true,
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

		public $hasAndBelongsToMany = array(
			'Ressourcemensuelle' => array(
				'className' => 'Ressourcemensuelle',
				'joinTable' => 'ressources_ressourcesmensuelles',
				'foreignKey' => 'ressource_id',
				'associationForeignKey' => 'ressourcemensuelle_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'RessourceRessourcemensuelle'
			)
		);

		/**
		 *
		 */
		public function afterFind( $results, $primary = false ) {
			$return = parent::afterFind( $results, $primary );

			if( !empty( $results ) ) {
				foreach( $results as $key => $result ) {
					if( isset( $result['Ressource'] ) ) {
						if( isset( $result['Ressource']['topressnul'] ) ) {
							$result['Ressource']['topressnotnul'] = !$result['Ressource']['topressnul'];
						}
					}
					$results[$key] = $result;
				}
			}
			return $results;
		}

		/**
		 *
		 */
		public function moyenne( $ressource ) {
			$somme = 0;
			$moyenne = 0;

			$montants = Set::extract( $ressource, '/Ressourcemensuelle/Detailressourcemensuelle/mtnatressmen' );
			if( empty( $montants ) ) {
				$montants = Set::extract( $ressource, '/Detailressourcemensuelle/mtnatressmen' );
			}

			if( count( $montants ) > 0 ) {
				foreach( $montants as $montant ) {
					$somme += $montant;
				}

				$moyenne = ( $somme / 3 );
			}
			return $moyenne;
		}

		/**
		 *
		 */
		public function refresh( $personne_id ) {
			$ressource = $this->find(
					'first', array(
				'conditions' => array(
					'Ressource.personne_id' => $personne_id
				),
				'order' => 'Ressource.dfress DESC',
				'contain' => array(
					'Ressourcemensuelle' => array(
						'Detailressourcemensuelle'
					)
				)
					)
			);

			if( !empty( $ressource ) ) {
				$moyenne = $this->moyenne( $ressource );
				$ressource['Ressource']['topressnotnul'] = ( $moyenne != 0 );
				$ressource['Ressource']['topressnul'] = !$ressource['Ressource']['topressnotnul'];
				$this->create( $ressource );
				$saved = $this->save();

				// INFO: en version2 c'est dans Calculdroitrsa
				$ModelCalculdroitrsa = ClassRegistry::init( 'Calculdroitrsa' );
				$qd_calculdroitrsa = array(
					'conditions' => array(
						'Calculdroitrsa.personne_id' => $personne_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$calculdroitrsa = $ModelCalculdroitrsa->find( 'first', $qd_calculdroitrsa );
				$calculdroitrsa['Calculdroitrsa']['personne_id'] = $personne_id;
				$calculdroitrsa['Calculdroitrsa']['mtpersressmenrsa'] = number_format( $moyenne, 2, '.', '' );
				$ModelCalculdroitrsa->create( $calculdroitrsa );
				$saved = $ModelCalculdroitrsa->save() && $saved;

				return $saved;
			}

			return true;
		}

		/**
		 *
		 */
		public function beforeSave( $options = array( ) ) {
			$return = parent::beforeSave( $options );

			$moyenne = $this->moyenne( $this->data );
			$this->data['Ressource']['topressnotnul'] = ( $moyenne != 0 );
			$this->data['Ressource']['topressnul'] = !$this->data['Ressource']['topressnotnul'];

			return $return;
		}

		/**
		 *
		 */
		public function afterSave( $created ) {
			$return = parent::afterSave( $created );

			$personne_id = Set::classicExtract( $this->data, 'Ressource.personne_id' );
			$modelCalculdroitrsa = ClassRegistry::init( 'Calculdroitrsa' );

			// Mise à jour de Calculdroitrsa
			$moyenne = $this->moyenne( $this->data );
			$qd_calculdroitrsa = array(
				'conditions' => array(
					'Calculdroitrsa.personne_id' => $personne_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$calculdroitrsa = $modelCalculdroitrsa->find( 'first', $qd_calculdroitrsa );


			// FIXME: si $calculdroitrsa est vide ? Ne doit pas arriver
			$calculdroitrsa[$modelCalculdroitrsa->alias]['personne_id'] = $personne_id;
			$calculdroitrsa[$modelCalculdroitrsa->alias]['mtpersressmenrsa'] = number_format( $moyenne, 2, '.', '' );
			$modelCalculdroitrsa->create( $calculdroitrsa );
			$modelCalculdroitrsa->save();

			$qd_thisPersonne = array(
				'conditions' => array(
					'Personne.id' => $personne_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$thisPersonne = $this->Personne->find( 'first', $qd_thisPersonne );


			$this->Personne->Foyer->refreshSoumisADroitsEtDevoirs( $thisPersonne['Personne']['foyer_id'] );

			return $return;
		}
	}
?>