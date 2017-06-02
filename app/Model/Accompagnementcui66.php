<?php
	/**
	 * Fichier source de la classe Accompagnementcui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 App::uses( 'AbstractAppModelLieCui66', 'Model/Abstractclass' );

	/**
	 * La classe Accompagnementcui66 est la classe contenant les accompagnements du CUI pour le CG 66.
	 *
	 * @package app.Model
	 */
	class Accompagnementcui66 extends AbstractAppModelLieCui66
	{
		/**
		 * Alias de la table et du model
		 * @var string
		 */
		public $name = 'Accompagnementcui66';
		
		/**
		 * Possède des clefs étrangères vers d'autres models
		 * @var array
		 */
        public $belongsTo = array(
			'Cui66' => array(
				'className' => 'Cui66',
				'foreignKey' => 'cui66_id',
				'dependent' => true,
			),
			'Immersioncui66' => array(
				'className' => 'Immersioncui66',
				'foreignKey' => 'immersioncui66_id',
				'dependent' => true,
			),
        );
		
		/**
		 * Récupère les donnés par defaut dans le cas d'un ajout, ou récupère les données stocké en base dans le cas d'une modification
		 * 
		 * @param integer $cui66_id
		 * @param integer $id
		 * @return array
		 */
		public function prepareAddEditFormData( $cui66_id, $id = null ) {
			$result = parent::prepareAddEditFormData($cui66_id, $id);
			
			// Modification
			if( !empty( $id ) ) {
				$result = $this->Immersioncui66->Immersionromev3->prepareFormDataAddEdit( $result );
			}

			return $result;
		}

		/**
		 * Query utilisé pour la visualisation
		 * 
		 * @param integer $id
		 * @param boolean $joinEntreeromev3
		 * @return array
		 */
		public function queryView( $id, $joinEntreeromev3 = true ) {
			$query = array(
				'fields' => array_merge(
					$this->fields(),
					$this->Immersioncui66->fields(),
					$this->Immersioncui66->Immersionromev3->fields()
				),
				'conditions' => array(
					'Accompagnementcui66.id' => $id
				),
				'joins' => array(
					$this->join( 'Immersioncui66', array( 'type' => 'LEFT OUTER' ) ),
					$this->Immersioncui66->join( 'Immersionromev3', array( 'type' => 'LEFT OUTER' ) ),
				)
			);
			
			if( $joinEntreeromev3 ) {
				$aliases = array(
					'Familleromev3' => 'Familleimmersion',
					'Domaineromev3' => 'Domaineimmersion',
					'Metierromev3' => 'Metierimmersion',
					'Appellationromev3' => 'Appellationimmersion'
				);
				$query = $this->Immersioncui66->Immersionromev3->getCompletedRomev3Joins( $query, 'LEFT OUTER', $aliases );
			}

			return $query;
		}
		
		/**
		 * Sauvegarde du formulaire
		 * 
		 * @param array $data
		 * @return boolean
		 */
		public function saveAddEditFormData( array $data, $user_id = null ) {
			$data['Accompagnementcui66']['user_id'] = $user_id;
			$success = true;
			
			// Si le genre d'accompagnement est immersion
			if ( isset($data['Immersioncui66']) && $data['Accompagnementcui66']['genre'] === 'immersion' ){
				$data['Immersioncui66']['user_id'] = $user_id;//FIXME
				unset( $this->Immersioncui66->Immersionromev3->validate['familleromev3_id'][NOT_BLANK_RULE_NAME] );
				// Si un code famille (rome v3) est vide, on ne sauvegarde pas le code rome
				if ( !isset($data['Immersionromev3']['familleromev3_id']) || $data['Immersionromev3']['familleromev3_id'] === '' ){ 
					$data['Immersioncui66']['entreeromev3_id'] = null;

					// Si le code rome avait un id, on supprime l'entreeromev3 correspondant
					if ( isset($data['Immersionromev3']['id']) && $data['Immersionromev3']['id'] !== '' ){
						$this->Immersioncui66->Immersionromev3->id = $data['Immersionromev3']['id'];
						$success = $this->Immersioncui66->Immersionromev3->delete() && $success;
					}
				}
				// Dans le cas contraire, on enregistre le tout
				else{
					$this->Immersioncui66->Immersionromev3->create($data);
					$success = $this->Immersioncui66->Immersionromev3->save( null, array( 'atomic' => false ) ) && $success;
					$data['Immersioncui66']['entreeromev3_id'] = $this->Immersioncui66->Immersionromev3->id;
				}
				
				$this->Immersioncui66->create($data);
				$success = $this->Immersioncui66->save( null, array( 'atomic' => false ) ) && $success;
				$data['Accompagnementcui66']['immersioncui66_id'] = $this->Immersioncui66->id;
			}
			
			$this->create($data);
			$success = $this->save( null, array( 'atomic' => false ) ) && $success;
			
			return $success;
		}
		
		/**
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * @param array $params <=> array( 'allocataire' => true, 'find' => false, 'autre' => false, 'pdf' => false )
		 * @return array
		 */
		public function options( array $params = array() ) {
			$options = array();
			$params = $params + array( 'allocataire' => true, 'find' => false, 'autre' => false, 'pdf' => false );

			if( Hash::get( $params, 'allocataire' ) ) {
				$Allocataire = ClassRegistry::init( 'Allocataire' );

				$options = $Allocataire->options();
			}
			
			if( $params['find'] ) {
				$options = Hash::merge(
					$options,
					$this->Cui66->Cui->Entreeromev3->options()
				);
			}

			$options = Hash::merge(
				$options,
				$this->enums(),
				$this->Immersioncui66->enums(),
				$this->Immersioncui66->Immersionromev3->options()
			);

			return $options;
		}

		/**
		 * FIXME: doc
		 * 
		 * @param type $cui66_id
		 * @return string
		 */
		public function getCompleteDataImpressionQuery( $cui66_id ) {
			$query = parent::getCompleteDataImpressionQuery( $cui66_id );

			$query['fields'] = array_merge( $query['fields'], $this->Immersioncui66->fields() );
			$query['contain'] = array( 'Immersioncui66' );
					
			return $query;
		}
	}
?>