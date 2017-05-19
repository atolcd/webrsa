<?php
	/**
	 * Code source de la classe Codesromemetiersdsps66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Codesromemetiersdsps66Controller s'occupe du paramétrage des
	 * codes ROME pour les métiers.
	 *
	 * @package app.Controller
	 */
	class Codesromemetiersdsps66Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Codesromemetiersdsps66';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Codesromemetiersdsps66:edit'
		);

		/**
		 * Liste des codes ROME pour les métiers.
		 */
		public function index() {
			if( false === $this->Coderomemetierdsp66->Behaviors->attached( 'Occurences' ) ) {
				$this->Coderomemetierdsp66->Behaviors->attach( 'Occurences' );
			}

			$query = array(
				'fields' => array_merge(
					$this->Coderomemetierdsp66->fields(),
					array(
						$this->Coderomemetierdsp66->sqHasLinkedRecords( true ),
						$this->Coderomemetierdsp66->Coderomesecteurdsp66->sqVirtualField( 'intitule' )
					)
				),
				'joins' => array(
					$this->Coderomemetierdsp66->join( 'Coderomesecteurdsp66', array( 'type' => 'INNER' ) )
				)
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'un code ROME pour les métiers.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Coderomemetierdsp66']['coderomesecteurdsp66_id'] = $this->Coderomemetierdsp66->Coderomesecteurdsp66->find( 'list' );
			$this->set( compact( 'options' ) );
		}
	}
?>