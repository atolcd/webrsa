<?php
	/**
	 * Code source de la classe AllocatairelieBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe AllocatairelieBehavior fournit les méthodes personneId et
	 * dossierId permettant respectivement d'obtenir la clé primaire d'un
	 * allocataire ou celle d'un dossier RSA à partir de la clé primaire d'un
	 * enregistrement lié indirectement à l'allocataire.
	 *
	 * @todo Appliquer aux modèles suivants, avec configuration, suite à l'ajout
	 * des jointures (et tester bien entendu):
	 *	- Accompagnementcui66: array( 'joins' => array( 'Cui' ) ) // @fixme Cui66
	 *	- Decisioncui66: array( 'joins' => array( 'Cui' ) )
	 *	- Decisionpersonnepcg66: array( 'joins' => array( 'Personnepcg66Situationpdo', 'Personnepcg66' ) )
	 *	- Manifestationbilanparcours66: array( 'joins' => array( 'Bilanparcours66' ) )
	 *	- Periodeimmersion: array( 'joins' => array( 'Cui' ) )
	 *	- Propodecisioncui66: array( 'joins' => array( 'Cui' ) )
	 *	- Suspensioncui66: array( 'joins' => array( 'Cui' ) )
	 *	- Traitementpcg66: array( 'joins' => array( 'Personnepcg66' ) )
	 *	- Traitementpdo: array( 'joins' => array( 'Propopdo' ) )
	 *
	 * @package app.Model.Behavior
	 */
	class AllocatairelieBehavior extends ModelBehavior
	{
		/**
		 *
		 * @var array
		 */
		public $config = array();

		/**
		 *
		 * @var array
		 */
		public $default = array();

		/**
		 *
		 * @param Model $model
		 * @param array $config
		 */
		public function setup( Model $model, $config = array() ) {
			parent::setup( $model, $config );

			$this->config[$model->alias] = Hash::merge( $this->default, $config );
		}

		/**
		 * Retourne la clé primaire du dossier RSA de l'allocataire auquel est
		 * lié un enregistrement.
		 *
		 * @param Model $Model Le modèle qui utilise ce behavior
		 * @param integer $id La clé primaire de l'enregistrement lié
		 * @return integer
		 */
		public function dossierId( Model $Model, $id ) {
			$joins = (array)Hash::get( $this->config, "{$Model->alias}.joins" );

			$querydata = array(
				'fields' => array( 'Foyer.dossier_id' ),
				'joins' => array(),
				'conditions' => array(
					"{$Model->alias}.id" => $id
				),
				'recursive' => -1
			);

			$LastModel = $Model;

			if( !empty( $joins ) ) {
				foreach( $joins as $joinModel ) {
					$querydata['joins'][] = $LastModel->join( $joinModel, array( 'type' => 'INNER' ) );
					$LastModel = $LastModel->{$joinModel};
				}
			}

			$querydata['joins'][] = $LastModel->join( 'Personne', array( 'type' => 'INNER' ) );
			$querydata['joins'][] = $LastModel->Personne->join( 'Foyer', array( 'type' => 'INNER' ) );

			$result = $Model->find( 'first', $querydata );

			return Hash::get( $result, 'Foyer.dossier_id' );
		}

		/**
		 * Retourne la clé primaire du foyer de l'allocataire auquel est
		 * lié un enregistrement.
		 *
		 * @param Model $Model Le modèle qui utilise ce behavior
		 * @param integer $id La clé primaire de l'enregistrement lié
		 * @return integer
		 */
		public function foyerId( Model $Model, $id ) {
			$joins = (array)Hash::get( $this->config, "{$Model->alias}.joins" );

			$querydata = array(
				'fields' => array( 'Foyer.id' ),
				'joins' => array(),
				'conditions' => array(
					"{$Model->alias}.id" => $id
				),
				'recursive' => -1
			);

			$LastModel = $Model;

			if( !empty( $joins ) ) {
				foreach( $joins as $joinModel ) {
					$querydata['joins'][] = $LastModel->join( $joinModel, array( 'type' => 'INNER' ) );
					$LastModel = $LastModel->{$joinModel};
				}
			}

			$querydata['joins'][] = $LastModel->join( 'Personne', array( 'type' => 'INNER' ) );
			$querydata['joins'][] = $LastModel->Personne->join( 'Foyer', array( 'type' => 'INNER' ) );

			$result = $Model->find( 'first', $querydata );

			return Hash::get( $result, 'Foyer.id' );
		}

		/**
		 * Retourne la clé primaire de l'allocataire auquel est lié un
		 * enregistrement.
		 *
		 * @param Model $Model Le modèle qui utilise ce behavior
		 * @param integer $id La clé primaire de l'enregistrement lié
		 * @return integer
		 */
		public function personneId( Model $Model, $id ) {
			$joins = (array)Hash::get( $this->config, "{$Model->alias}.joins" );
			if( !empty( $joins ) ) {
				$field = "{$joins[count($joins)-1]}.personne_id";
			}
			else {
				$field = "{$Model->alias}.personne_id";
			}

			$querydata = array(
				'fields' => array( $field ),
				'joins' => array(),
				'conditions' => array(
					"{$Model->alias}.id" => $id
				),
				'recursive' => -1
			);

			if( !empty( $joins ) ) {
				$LastModel = $Model;

				foreach( $joins as $joinModel ) {
					$querydata['joins'][] = $LastModel->join( $joinModel, array( 'type' => 'INNER' ) );
					$LastModel = $LastModel->{$joinModel};
				}
			}

			$result = $Model->find( 'first', $querydata );

			return Hash::get( $result, $field );
		}
	}
?>