<?php
    /**
     * Code source de la classe Typesaidesapres66Controller.
     *
     * PHP 5.3
     *
     * @package app.Controller
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

    /**
     * La classe Typesaidesapres66Controller s'occupe du paramétrage des aides de
	 * l'APRE/ADRE.
     *
     * @package app.Controller
     */
    class Typesaidesapres66Controller extends AbstractWebrsaParametragesController
    {
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Typesaidesapres66';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Typeaideapre66' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Typesaidesapres66:edit'
		);

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array( 'piecesaides66_typesaidesapres66', 'piecescomptables66_typesaidesapres66' );

		/**
		 * Surcharge de liste des aides APRE/ADRE pour obtenir la thématique liée
		 * et ajouter des messages d'avertissement.
		 */
		public function index() {
			$messages = array();
			if( 0 === $this->Typeaideapre66->Pieceaide66->find( 'count' ) ) {
				$msg = 'Merci de renseigner au moins une pièce administrative avant de renseigner une aide de l\'APRE/ADRE.';
				$messages[$msg] = 'error';
			}
			if( 0 === $this->Typeaideapre66->Themeapre66->find( 'count' ) ) {
				$msg = 'Merci de renseigner au moins un thème avant de renseigner une aide de l\'APRE/ADRE.';
				$messages[$msg] = 'error';
			}
			$this->set( compact( 'messages' ) );

			$query = array(
				'fields' => array_merge(
					$this->Typeaideapre66->fields(),
					array(
						'Themeapre66.name',
						$this->Typeaideapre66->sqHasLinkedRecords( true, $this->blacklist )
					)
				),
				'joins' => array(
					$this->Typeaideapre66->join( 'Themeapre66', array( 'type' => 'INNER' ) )
				)
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Surcharge de la modification d'une aide APRE/ADRE pour obtenir en plus
		 * les valeurs des cases à cocher "Pièces administratives" et "Pièces
		 * comptables", ainsi que l'ajout d'options à envoyer au formulaire..
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			// Ajout des contain pour les cases à cocher
			$query = array(
				'contain' => array(
					'Pieceaide66',
					'Piececomptable66'
				)
			);
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit', 'query' => $query ) );

			// Pré-remplissage du formulaire à l'ajout
			if( 'add' === $this->action && empty( $this->request->data ) ) {
				$this->request->data['Typeaideapre66']['typeplafond'] = 'ADRE';
			}

			// Options
			$options = $this->viewVars['options'];
			$options['Typeaideapre66']['themeapre66_id'] = $this->Typeaideapre66->Themeapre66->find( 'list' );
			$options['Pieceaide66']['Pieceaide66'] = $this->Typeaideapre66->Pieceaide66->find( 'list' );
			$options['Piececomptable66']['Piececomptable66'] = $this->Typeaideapre66->Piececomptable66->find( 'list' );
			$this->set( compact( 'options' ) );
		}
    }
?>