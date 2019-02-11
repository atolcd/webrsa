<?php
	/**
	 * Code source de la classe WebrsaAjaxInsertionsComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe WebrsaAjaxInsertionsComponent s'occupe de renvoyer les coordonnées
	 * d'une structure référente ou d'un référent via des appels Ajax depuis
	 * certains formulaires de l'application.
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaAjaxInsertionsComponent extends Component
	{
		/**
		 * Paramètres de ce component
		 *
		 * @var array
		 */
		public $settings = array();

		/**
		 * Paramètres par défaut de ce component
		 *
		 * @var array
		 */
		public $defaultSettings = array(
			'modelClass' => '%s',
			'aliasStructurereferente' => 'Structurereferente',
			'structurereferente_id' => '%s.structurereferente_id',
			'viewStructurereferente' => '/Elements/WebrsaAjaxInsertions/structurereferente',
			'aliasReferent' => 'Referent',
			'referent_id' => '%s.referent_id',
			'viewReferent' => '/Elements/WebrsaAjaxInsertions/referent',
			'viewPrescripteur' => '/Elements/WebrsaAjaxInsertions/prescripteur'
		);

		/**
		 * Called after the Controller::beforeFilter() and before the controller action.
		 *
		 * @todo: Ne pas appeler automatiquement
		 *
		 * @param Controller $controller Controller with components to startup
		 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::startup
		 */
		public function startup( Controller $controller ) {
			parent::startup( $controller );

			$Controller = $this->_Collection->getController();
			$this->settings += $this->defaultSettings;
			$this->settings = array_words_replace( $this->settings, array( '%s' => $Controller->modelClass ) );
		}

		/**
		 * Ajax permettant de renvoyer les coordonnées de la structure référente
		 * dont l'id est passé, soit en paramètre, soit dans request->data.
		 *
		 * Usage:
		 *	return $this->WebrsaAjaxInsertions->structurereferente( $structurereferente_id );
		 *
		 * @param integer $structurereferente_id
		 */
		public function structurereferente( $structurereferente_id = null ) {
			Configure::write( 'debug', 0 );
			$Controller = $this->_Collection->getController();
			$modelClass = $Controller->{$this->settings['modelClass']};

			$Structurereferente = ClassRegistry::init(
				array(
					'class' => 'Structurereferente',
					'alias' => $this->settings['aliasStructurereferente']
				)
			);

			$dataStructurereferente_id = Hash::get( $Controller->request->data, $this->settings['structurereferente_id'] );
			$structurereferente_id = ( empty($structurereferente_id) && !empty($dataStructurereferente_id) ? $dataStructurereferente_id : $structurereferente_id );

			if( false === empty( $structurereferente_id ) ) {
				$query = array(
					'fields' => array(
						'Typeorient.lib_type_orient',
						'Structurereferente.num_voie',
						'Structurereferente.type_voie',
						'Structurereferente.nom_voie',
						'Structurereferente.code_postal',
						'Structurereferente.ville'
					),
					'joins' => array(
						$Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						'Structurereferente.id' => $structurereferente_id
					),
					'order' => null,
					'recursive' => -1
				);

				$record = $Structurereferente->find('first', $query);
			}
			else {
				$record = array();
			}

			$Controller->set( compact( 'record' ) );
			$Controller->view = $this->settings['viewStructurereferente'];
			$Controller->layout = 'ajax';
		}

		/**
		 * Ajax permettant de renvoyer les coordonnées du référent dont l'id est
		 * passé, soit en paramètre, soit dans request->data.
		 *
		 * Usage:
		 *	return $this->WebrsaAjaxInsertions->referent( $referent_id );
		 *
		 * @param integer $referent_id
		 */
		public function referent( $referent_id = null ) {
			Configure::write( 'debug', 0 );
			$Controller = $this->_Collection->getController();
			$modelClass = $Controller->{$this->settings['modelClass']};

			if( false === empty( $referent_id ) ) {
				$referent_id = suffix( $referent_id );
			}
			else {
				$referent_id = suffix( Hash::get( $Controller->request->data, $this->settings['referent_id'] ) );
			}

			$record = array( );
			if( false === empty( $referent_id ) ) {
				$Referent = ClassRegistry::init(
					array(
						'class' => 'Referent',
						'alias' => $this->settings['aliasReferent']
					)
				);

				$query = array(
					'conditions' => array(
						'Referent.id' => $referent_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);

				$record = $Referent->find( 'first', $query );
			}
			else {
				$record = array();
			}

			$Controller->set( compact( 'record' ) );
			$Controller->view = $this->settings['viewReferent'];
			$Controller->layout = 'ajax';
		}

		/**
		 * Ajax permettant de renvoyer les coordonnées du prescripteur dont l'id est
		 * passé, soit en paramètre, soit dans request->data.
		 *
		 * Usage:
		 *	return $this->WebrsaAjaxInsertions->prescripteur( $referent_id );
		 *
		 * @param integer $referent_id
		 */
		public function prescripteur( $referent_id = null ) {
			Configure::write( 'debug', 0 );
			$Controller = $this->_Collection->getController();
			$modelClass = $Controller->{$this->settings['modelClass']};

			if( false === empty( $referent_id ) ) {
				$referent_id = suffix( $referent_id );
			}
			else {
				$referent_id = suffix( Hash::get( $Controller->request->data, $this->settings['referent_id'] ) );
			}

			$record = array( );
			if( false === empty( $referent_id ) ) {
				$Referent = ClassRegistry::init(
					array(
						'class' => 'Referent',
						'alias' => $this->settings['aliasReferent']
					)
				);

				$query = array(
					'fields' => array_merge(
						$Referent->fields(),
						$Referent->Structurereferente->fields()
					),
					'conditions' => array(
						'Referent.id' => $referent_id
					),
					'joins' => array(
						$Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) )
					),
					'order' => null,
					'recursive' => -1
				);

				$record = $Referent->find( 'first', $query );
			}
			else {
				$record = array();
			}

			$Controller->set( compact( 'record' ) );
			$Controller->view = $this->settings['viewPrescripteur'];
			$Controller->layout = 'ajax';
		}
	}
?>