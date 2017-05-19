<?php
	/**
	 * Code source de la classe Valsprogsfichescandidatures66Controller.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Valsprogsfichescandidatures66Controller s'occupe du paramétrage
	 * des valeurs des programmes région.
	 *
	 * @package app.Controller
	 */
	class Valsprogsfichescandidatures66Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Valsprogsfichescandidatures66';

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default',
			'Default2',
			'Theme',
			'Xform',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Valprogfichecandidature66' );

		/**
		 * Liste des valeurs des programmes région.
		 */
		public function index() {
			$messages = array();
			if( 0 === $this->Valprogfichecandidature66->Progfichecandidature66->find( 'count' ) ) {
				$msg = 'Merci de renseigner au moins un programme région avant de renseigner une une valeur de programme région.';
				$messages[$msg] = 'error';
			}
			$this->set( compact( 'messages' ) );

			$query = array(
				'contain' => array(
					'Progfichecandidature66.name',
					'Progfichecandidature66.isactif'
				)
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'une valeur des programme région.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			if( true === empty( $this->request->data ) ) {
				$this->request->data = array(
					'Valprogfichecandidature66' => array(
						'actif' => true
					)
				);
			}

			$options = $this->viewVars['options'];
			$options['Valprogfichecandidature66']['progfichecandidature66_id'] = $this->Valprogfichecandidature66->Progfichecandidature66->find('list');
			$this->set( compact( 'options' ) );
		}

	}
?>
