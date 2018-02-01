<?php
	/**
	 * Code source de la classe WebrsaCohorteDossierpcg66Enattenteaffectation.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteDossierpcg66', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteDossierpcg66Enattenteaffectation ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteDossierpcg66Enattenteaffectation extends AbstractWebrsaCohorteDossierpcg66
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteDossierpcg66Enattenteaffectation';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryDossierspcgs66.cohorte_enattenteaffectation.fields',
			'ConfigurableQueryDossierspcgs66.cohorte_enattenteaffectation.innerTable',
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'WebrsaRechercheDossierpcg66',
			'Dossierpcg66'
		);

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Dossierpcg66.atraiter' => array( 'type' => 'checkbox' ),
			'Dossierpcg66.poledossierpcg66_id' => array( 'empty' => true ),
			'Dossierpcg66.user_id' => array( 'empty' => true ),
			'Dossierpcg66.dateaffectation' => array( 'type' => 'date' ),
			'Dossierpcg66.id' => array( 'type' => 'hidden' ),
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
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			$query = parent::searchConditions( $query, $search );

			$query['conditions'][] = array(
				'Dossierpcg66.etatdossierpcg' => 'attaffect',
			);

			// Possède un pôle chargé du dossier ?
			$has_poledossierpcg66_id = (string)Hash::get($search, 'Dossierpcg66.has_poledossierpcg66_id');
			if('' !== $has_poledossierpcg66_id) {
				if('0' === $has_poledossierpcg66_id) {
					$query['conditions'][] = 'Dossierpcg66.poledossierpcg66_id IS NULL';
				} else {
					$query['conditions'][] = 'Dossierpcg66.poledossierpcg66_id IS NOT NULL';
				}
			}

			return $query;
		}

		/**
		 * Surcharge du searchQuery pour obtenir le champ permettant de pré-remplir
		 * le "Pôle chargé du dossier" le cas échéant.
		 *
		 * @param array $types
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$query = parent::searchQuery( $types );
			$query['fields'][] = 'Dossierpcg66.poledossierpcg66_id';
			return $query;
		}

		/**
		 * Logique de sauvegarde de la cohorte
		 *
		 * @param type $data
		 * @param type $params
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			foreach ( $data as $key => $value ) {
				if ( $value['Dossierpcg66']['atraiter'] === '0' ) {
					unset($data[$key]);
				}
				else {
					unset($data[$key]['Dossierpcg66']['atraiter']);
					$data[$key]['Dossierpcg66']['user_id'] = suffix(Hash::get($value, 'Dossierpcg66.user_id'));
				}
			}

			$success = !empty($data) && $this->Dossierpcg66->saveAll( $data )
				&& $this->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsByConditions(
					array(
						'Dossierpcg66.id' => Hash::extract($data, '{n}.Dossierpcg66.id')
					)
				)
			;

			return $success;
		}
	}
?>