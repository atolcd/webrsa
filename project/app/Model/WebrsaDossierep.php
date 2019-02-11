<?php
	/**
	 * Code source de la classe WebrsaDossierep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe WebrsaDossierep contient la logique métier concernant les
	 * dossiers d'EP.
	 *
	 * @package app.Model
	 */
	class WebrsaDossierep extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaDossierep';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $actsAs = array();

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Dossierep'
		);

		/**
		 * Retourne la liste des dossiers d'EP en cours ne débouchant pas sur une
		 * orientation pour un allocataire donné.
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function getNonReorientationsEnCours( $personne_id ) {
			// 1. Récupération des conditions concernant les dossiers d'EP ouverts pour le bénéficiaire
			$query = $this->Dossierep->qdDossiersepsOuverts( $personne_id );
			$conditions = $query['conditions'];

			// 2. Récupération du query permettant de récupérer les dossiers d'EP
			// liés à leur dernier passage en commission
			$query = $this->Dossierep->getDossiersQuery();
			$query['fields'] = array(
				'Personne.id',
				'Personne.qual',
				'Personne.nom',
				'Personne.prenom',
				'Dossierep.id',
				'Dossierep.created',
				'Dossierep.themeep',
				'Passagecommissionep.id',
				'Passagecommissionep.etatdossierep',
				'Commissionep.id',
				'Commissionep.dateseance',
				'Commissionep.etatcommissionep',
			);

			$query['conditions'][] = $conditions;

			// et qui ne conduisent pas à une réorientation (ils se trouvent déjà dans $reorientationseps)
			$query['conditions'][] = array(
				'NOT' => array(
					'Dossierep.themeep' => $this->Dossierep->getThematiquesReorientations()
				)
			);

			return $this->Dossierep->find( 'all', $query );
		}
	}
?>