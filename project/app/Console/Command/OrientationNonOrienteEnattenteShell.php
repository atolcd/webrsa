<?php
/**
	 * Code source de la classe OrientationNonOrienteEnattenteShell.
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 App::uses( 'XShell', 'Console/Command' );
	 App::uses( 'ConnectionManager', 'Model' );
	 App::uses( 'View', 'View' );

	/**
	 * La classe OrientationNonOrienteEnattenteShell permet de transformer les statut des personnes
	 * ayant une orientation avec le statut Non Orientés en statut En attente
	 *
	 * sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake OrientationNonOrienteEnattente -app app
	 *
	 * @package app.Console.Command
	 */
	class OrientationNonOrienteEnattenteShell extends XShell
	{

		public $uses = array(
			'Orientstruct',
		);

		/**
		 * Récupère les orientations ayant un statut d'orientation "Non orienté" avec un rendez-vous prévu après aujourd'hui
		 * @return array
		 */
		private function _getOrientations() {
			$now = new DateTime();
			$query = array(
				'fields' => array(
					'Orientstruct.*',
					'Rendezvous.daterdv',
				),
				'recursive' => -1,
				'joins' => array(
					$this->Orientstruct->join("Personne"),
					$this->Orientstruct->Personne->join("Rendezvous"),
					$this->Orientstruct->Personne->Rendezvous->join("Statutrdv")
				),
				'conditions' => array(
					"Orientstruct.statut_orient" => "Non orienté",
					"Statutrdv.code_statut" => "PREVU",
					"Rendezvous.daterdv >" => $now->format("Y-m-d")
				)
			);
			$datas = $this->Orientstruct->find('all', $query);
			return $datas;
		}

		function main() {
			$this->out( __d('shells', "Shells:OrientationNonOrienteEnattente:intro") );
			$orientations = $this->_getOrientations();
			$nbOrientation = count($orientations);
			if($nbOrientation > 0) {
				$this->out(sprintf(__d('shells', "Shells:OrientationNonOrienteEnattente:nbOrientOK"), $nbOrientation ) );
			} else {
				$this->out( __d('shells', "Shells:OrientationNonOrienteEnattente:nbOrientNOK" ) );
				return;
			}

			// Prépare le tableau pour modification du statut d'orientation
			$datas = array();
			$key = 0;
			foreach($orientations as $orient) {
				$datas[$key] = $orient['Orientstruct'];
				$datas[$key]['statut_orient'] = "En attente";
				$key++;
			}

			// Sauvegarde des données
			if($this->Orientstruct->saveAll($datas)) {
				$this->out( __d('shells', "Shells:OrientationNonOrienteEnattente:modifOK" ) );
			} else {
				$this->out( __d('shells', "Shells:OrientationNonOrienteEnattente:modifNOK" ) );
			}
		}
	}