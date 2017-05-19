<?php
	/**
	 * Code source de la classe ParticipantscomitesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ParticipantscomitesController s'occupe du paramétrage des
	 * participants aux comités d'examen APRE.
	 *
	 * @package app.Controller
	 */
	class ParticipantscomitesController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Participantscomites';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Participantcomite' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Participantscomites:edit'
		);

		/**
		 * Liste des participants aux comités d'examen APRE
		 */
		public function index() {
			$query = array(
				'fields' => array_merge(
					$this->Participantcomite->fields(),
					array(
						'Participantcomite.nomcomplet',
						$this->Participantcomite->sqHasLinkedRecords( true, $this->blacklist )
					)
				)
			);
			$this->WebrsaParametrages->index( $query );
		}
	}
?>