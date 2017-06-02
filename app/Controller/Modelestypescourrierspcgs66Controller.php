<?php
    /**
     * Code source de la classe Modelestypescourrierspcgs66Controller.
     *
     * PHP 5.3
     *
     * @package app.Controller
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

    /**
	 * La classe Modelestypescourrierspcgs66Controller s'occupe du paramétrage
	 * des modèles liés aux types de courriers PCG.
     *
     * @package app.Controller
     */
    class Modelestypescourrierspcgs66Controller extends AbstractWebrsaParametragesController
    {
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Modelestypescourrierspcgs66';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Modelestypescourrierspcgs66:edit'
		);

		/**
		 * Liste des types de modèles liés aux types de courriers PCG.
		 */
		public function index() {
			if( false === $this->Modeletypecourrierpcg66->Behaviors->attached( 'Occurences' ) ) {
				$this->Modeletypecourrierpcg66->Behaviors->attach( 'Occurences' );
			}

			$query = array(
				'fields' => array_merge(
					$this->Modeletypecourrierpcg66->fields(),
					array(
						$this->Modeletypecourrierpcg66->sqHasLinkedRecords( true ),
						'Typecourrierpcg66.name'
					)
				),
				'joins' => array(
					$this->Modeletypecourrierpcg66->join( 'Typecourrierpcg66', array( 'type' => 'INNER' ) )
				)
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'un modèle lié aux types de courriers PCG.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Modeletypecourrierpcg66']['typecourrierpcg66_id'] = $this->Modeletypecourrierpcg66->Typecourrierpcg66->find( 'list' );
			$this->set( compact( 'options' ) );
		}
    }
?>
