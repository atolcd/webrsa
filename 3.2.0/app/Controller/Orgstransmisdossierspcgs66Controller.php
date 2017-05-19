<?php
	/**
	 * Code source de la classe Orgstransmisdossierspcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe  ...
	 * La classe Orgstransmisdossierspcgs66Controller s'occupe du paramétrage des
	 * organismes auxquels seront transmis les dossiers PCG traités.
	 *
	 * @package app.Controller
	 */
	class Orgstransmisdossierspcgs66Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Orgstransmisdossierspcgs66';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Orgstransmisdossierspcgs66:edit'
		);

		/**
		 * Liste des organismes auxquels seront transmis les dossiers PCG traités.
		 */
		public function index() {
			if( false === $this->Orgtransmisdossierpcg66->Behaviors->attached( 'Occurences' ) ) {
				$this->Orgtransmisdossierpcg66->Behaviors->attach( 'Occurences' );
			}

            $query = array(
				'fields' => array_merge(
					$this->Orgtransmisdossierpcg66->fields(),
					array(
						$this->Orgtransmisdossierpcg66->sqHasLinkedRecords( true ),
						'Poledossierpcg66.name'
					)
				),
				'joins' => array(
					$this->Orgtransmisdossierpcg66->join( 'Poledossierpcg66', array( 'type' => 'LEFT OUTER') )
				),
				'order' => array( 'Poledossierpcg66.name ASC' )
            );
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'un organisme auquel sera transmis les dossiers PCG traités.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
            $options['Orgtransmisdossierpcg66']['poledossierpcg66_id'] = $this->Orgtransmisdossierpcg66->Poledossierpcg66->find( 'list' );
			$this->set( compact( 'options' ) );
		}
	}
?>
