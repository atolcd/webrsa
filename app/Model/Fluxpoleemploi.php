<?php
	/**
	 * Code source de la classe Fluxpoleemploi.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Fluxpoleemploi ...
	 *
	 * @package app.Model
	 */
	class Fluxpoleemploi extends AppModel
	{
		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'informationspe';

		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Fluxpoleemploi';

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array();

		/**
		 *
		 * @param array $query Le querydata à compléter
		 * @param array $filters Les filtres venant du moteur de recherche.
		 * @return array
		 */
		public function completeQuery ( array $query, array $filters = array() ) {
			if (isset ($filters['Fluxpoleemploi'])) {
				$conditions = array ();
				$conditions[] = '"Fluxpoleemploi"."nir" = "Personne"."nir"';

				if (isset ($filters['Fluxpoleemploi']['suivi_structure_principale_bureau'])) {
					$conditions[] = 'UPPER(suivi_structure_principale_bureau) LIKE UPPER(\'%'.$filters['Fluxpoleemploi']['suivi_structure_principale_bureau'].'%\')';
				}
				if (isset ($filters['Fluxpoleemploi']['ppae_conseiller_pe'])) {
					$conditions[] = 'UPPER(ppae_conseiller_pe) LIKE UPPER(\'%'.$filters['Fluxpoleemploi']['ppae_conseiller_pe'].'%\')';
				}
				if (isset ($filters['Fluxpoleemploi']['romev3_lib_rome'])) {
					$conditions[] = 'UPPER(romev3_lib_rome) LIKE UPPER(\'%'.$filters['Fluxpoleemploi']['romev3_lib_rome'].'%\')';
				}

				$query['joins'][] = array (
					'table' => '"informationspe"',
					'alias' => 'Fluxpoleemploi',
					'type' => 'INNER',
					'conditions' => $conditions
				);
			}

			return $query;
		}
	}
?>