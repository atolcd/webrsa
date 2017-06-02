<?php
	/**
	 * Code source de la classe Polesdossierspcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Polesdossierspcgs66Controller s'occupe du paramétrage des pôles
	 * chargés des dossiers PCG.
	 *
	 * @package app.Controller
	 */
	class Polesdossierspcgs66Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Polesdossierspcgs66';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Poledossierpcg66' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Polesdossierspcgs66:edit'
		);

		/**
		 * Liste des pôles chargés des dossiers PCG
		 */
		public function index() {
			if( false === $this->Poledossierpcg66->Behaviors->attached( 'Occurences' ) ) {
				$this->Poledossierpcg66->Behaviors->attach( 'Occurences' );
			}

            $query = array(
				'fields' => array_merge(
					$this->Poledossierpcg66->fields(),
					array(
						$this->Poledossierpcg66->sqHasLinkedRecords( true ),
						'Originepdo.libelle',
						'Typepdo.libelle'
					)
				),
				'joins' => array(
					$this->Poledossierpcg66->join('Originepdo', array( 'type' => 'LEFT OUTER' ) ),
					$this->Poledossierpcg66->join('Typepdo', array( 'type' => 'LEFT OUTER' ) )
				),
				'order' => array( 'Poledossierpcg66.name ASC' )
            );
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'un pôle chargé des dossiers PCG.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
            $options['Poledossierpcg66']['originepdo_id'] = $this->Poledossierpcg66->Originepdo->find( 'list' );
            $options['Poledossierpcg66']['typepdo_id'] = $this->Poledossierpcg66->Typepdo->find( 'list' );
			$this->set( compact( 'options' ) );
		}
	}
?>