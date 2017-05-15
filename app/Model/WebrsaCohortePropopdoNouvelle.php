<?php
	/**
	 * Code source de la classe WebrsaCohortePropopdoNouvelle.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohortePropopdoNouvelle ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePropopdoNouvelle extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohortePropopdoNouvelle';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Allocataire', 'Personne', 'WebrsaCohortePropopdoValidee' );

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Dossier.id' => array( 'type' => 'hidden', 'hidden' => true ),
			'Propopdo.id' => array( 'type' => 'hidden', 'hidden' => true ),
			'Propopdo.personne_id' => array( 'type' => 'hidden', 'hidden' => true ),
			'Propopdo.user_id' => array( 'type' => 'select', 'empty' => true ),
			'Propopdo.commentairepdo' => array( 'type' => 'textarea' ), // INFO: ce champ n'existe plus
		);

		/**
		 * Valeurs par défaut pour le préremplissage des champs du formulaire de cohorte
		 * array(
		 *		'Mymodel' => array( 'Myfield' => 'MyValue' ) )
		 * )
		 *
		 * @var array
		 */
		public $defaultValues = array();

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Foyer' => 'INNER',
				'Propopdo' => 'INNER',
				'Prestation' => 'INNER',
				'Decisionpropopdo' => 'LEFT OUTER',
				'Traitementpdo' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'User' => 'LEFT OUTER'
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->WebrsaCohortePropopdoValidee->searchQuery( $types );
				$query['fields'][] = 'Dossier.id';
				$query['conditions'] = array(
					'Propopdo.user_id IS NULL'
				);

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			return $this->WebrsaCohortePropopdoValidee->searchConditions( $query, $search );
		}

		/**
		 * Tentative de sauvegarde à partir de la cohorte.
		 *
		 * @param array $data
		 * @param array $params
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$success = parent::saveCohorte($data, $params, $user_id);

			$propospdos = Hash::extract( $data, '{n}.Propopdo[user_id=/.+/]' );

			$success = $this->Personne->Propopdo->saveAll( $data, array( 'validate' => 'only', 'atomic' => false ) ) && $success;

			if( $success ) {
				$this->Personne->Propopdo->begin();
				$success = $this->Personne->Propopdo->saveAll( $propospdos, array( 'validate' => 'first', 'atomic' => false ) );

				if( $success ) {
					$this->Personne->Propopdo->commit();
				}
				else {
					$this->Personne->Propopdo->rollback();
				}
			}

			return $success;
		}
	}
?>