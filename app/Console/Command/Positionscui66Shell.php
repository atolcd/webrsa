<?php
	/**
	 * Fichier source de la classe Positionscui66Shell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('XShell', 'Console/Command');

	/**
	 * La classe Positionscui66Shell
	 *
	 * @package app.Console.Command
	 */
	class Positionscui66Shell extends XShell
	{
		/**
		 * Modèles utilisés par ce shell
		 *
		 * @var array
		 */
		public $uses = array('Cui66');

		/**
		 * Démarage du shell, vérification de l'environnement et des paramètres.
		 */
		public function startup() {
			parent::startup();
			$this->checkDepartement( 66 );
		}

		/**
		 * Affiche le résumé du nombre de CUI pour achacune des positions.
		 */
		protected function _summary() {
			$this->out("<info>Nouvelle ventilation des positions du CUI</info>");
			$positions = $this->Cui66->enum( 'etatdossiercui66' );
			asort( $positions );

			$query = array(
				'fields' => array(
					'COUNT(*) AS "Cui66__count"',
					'"Cui66"."etatdossiercui66" AS "Cui66__etatdossiercui66"'
				),
				'contain' => false,
				'group' => array( 'Cui66.etatdossiercui66' )
			);
			$results = $this->Cui66->find( 'all', $query );
			$results = Hash::combine(
				$results,
				'{n}.Cui66.etatdossiercui66',
				'{n}.Cui66.count'
			);

			$format = "%-67s\t%7d";
			$total = 0;
			foreach($positions as $position => $label) {
				$count = (int)Hash::get($results, $position);
				$total += $count;
				$this->out(sprintf($format, trim($label), $count));
			}
			$this->hr();
			$this->out(sprintf($format, 'Total', $total));
		}

		/**
		 * Mise à jour de la position des CUIs
		 */
		public function main() {
			$this->out("<info>Mise à jour des positions du CUI...</info>");
			$success = $this->Cui66->WebrsaCui66->updatePositionsCuisByConditions(array());
			if(true === $success) {
				$this->out("<success>Mise à jour terminée avec succès.</success>");
			}
			else {
				$this->out("<error>Erreur(s) lors de la mise à jour.</error>");
			}

			$this->hr();
			$this->_summary();

			$this->_stop( $success ? self::SUCCESS : self::ERROR );
		}
	}