<?php
    /**
     * Code source de la classe Piecesmodelestypescourrierspcgs66Controller.
     *
     * PHP 5.3
     *
     * @package app.Controller
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Piecesmodelestypescourrierspcgs66Controller s'occupe du paramétrage
	 * des pièces liées aux modèles de courriers PCG.
     *
     * @package app.Controller
     */
    class Piecesmodelestypescourrierspcgs66Controller extends AbstractWebrsaParametragesController
    {
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Piecesmodelestypescourrierspcgs66';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Piecesmodelestypescourrierspcgs66:edit'
		);

		/**
		 * Liste des pièces liées aux modèles de courriers PCG.
		 */
		public function index() {
			if( false === $this->Piecemodeletypecourrierpcg66->Behaviors->attached( 'Occurences' ) ) {
				$this->Piecemodeletypecourrierpcg66->Behaviors->attach( 'Occurences' );
			}

			$query = array(
				'fields' => array_merge(
					$this->Piecemodeletypecourrierpcg66->fields(),
					array(
						$this->Piecemodeletypecourrierpcg66->sqHasLinkedRecords( true ),
						'Typecourrierpcg66.name',
						'Modeletypecourrierpcg66.name'
					)
				),
				'joins' => array(
					$this->Piecemodeletypecourrierpcg66->join( 'Modeletypecourrierpcg66', array( 'type' => 'INNER' ) ),
					$this->Piecemodeletypecourrierpcg66->Modeletypecourrierpcg66->join( 'Typecourrierpcg66', array( 'type' => 'INNER' ) )
				),
				'order' => array(
					'Typecourrierpcg66.name ASC',
					'Modeletypecourrierpcg66.name ASC',
					'Piecemodeletypecourrierpcg66.name ASC'
				)
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'une pièce liée aux modèles de courriers PCG.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Piecemodeletypecourrierpcg66']['modeletypecourrierpcg66_id'] = $this->Piecemodeletypecourrierpcg66->Modeletypecourrierpcg66->find( 'list' );
			$this->set( compact( 'options' ) );
		}
    }
?>
