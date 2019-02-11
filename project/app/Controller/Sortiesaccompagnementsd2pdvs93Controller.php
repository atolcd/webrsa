<?php
	/**
	 * Code source de la classe Sortiesaccompagnementsd2pdvs93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Sortiesaccompagnementsd2pdvs93Controller s'occupe du paramétrage
	 * des sorties de l'accompagnement (CD 93).
	 *
	 * @package app.Controller
	 */
	class Sortiesaccompagnementsd2pdvs93Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Sortiesaccompagnementsd2pdvs93';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Sortieaccompagnementd2pdv93' );

		/**
		 * Pagination sur les motifs de sortie de l'accompagnement.
		 */
		public function index() {
			$query = array(
				'fields' => array(
					'Sortieaccompagnementd2pdv93.id',
					'Sortieaccompagnementd2pdv93.name',
					'Parent.name',
					'Sortieaccompagnementd2pdv93.actif',
					$this->Sortieaccompagnementd2pdv93->sqHasLinkedRecords( true, $this->blacklist )
				),
				'joins' => array(
					$this->Sortieaccompagnementd2pdv93->join( 'Parent' )
				),
				'order' => array(
					'( CASE WHEN Parent.name IS NULL THEN \'\' ELSE Parent.name END ) ASC',
					'Sortieaccompagnementd2pdv93.name ASC',
				),
				'limit' => 50
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'un motif de sortie de l'accompagnement.
		 *
		 * @throws NotFoundException
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			// Liste des parents, moins nous-même lorsque l'on fait une modification
			$query = array(
				'conditions' => array(
					'Sortieaccompagnementd2pdv93.parent_id IS NULL',
				),
				'order' => array(
					'Sortieaccompagnementd2pdv93.name ASC',
				),
			);

			if( $this->action == 'edit' ) {
				$query['conditions'][] = array(
					'NOT' => array( 'Sortieaccompagnementd2pdv93.id' => $id )
				);
			}

			$options = $this->viewVars['options'];
			$options['Sortieaccompagnementd2pdv93']['parent_id'] = $this->Sortieaccompagnementd2pdv93->find( 'list', $query );
			$this->set( compact( 'options' ) );
		}
	}
?>
