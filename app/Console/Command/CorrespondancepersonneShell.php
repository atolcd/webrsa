<?php
	/**
	 * Code source de la classe CorrespondancepersonneShell.
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 App::uses( 'XShell', 'Console/Command' );
	 App::uses( 'ConnectionManager', 'Model' );
	 
	/**
	 * La classe CorrespondancepersonneShell permet d'obtenir les correspondances entre les personne_id 
	 * de différents dossiers selon le nom/prenom/dtnai/nir
	 *
	 * @package app.Console.Command
	 */
	class CorrespondancepersonneShell extends XShell
	{
		/**
		 * La constante à utiliser dans la méthode _stop() en cas de succès.
		 */
		const SUCCESS = 0;

		/**
		 * La constante à utiliser dans la méthode _stop() en cas d'erreur.
		 */
		const ERROR = 1;

		/**
		 * Initialisation: lecture des paramètres, on s'assure d'avoir une connexion
		 * valide
		 */
		public function startup() {
			parent::startup();
			try {
				$this->connection = ConnectionManager::getDataSource( $this->params['connection'] );
			}
			catch( Exception $e ) {

			}
		}

		/**
		 * Lignes de bienvenue.
		 */
		protected function _welcome() {
			parent::_welcome();
		}
		
		/**
		 * Méthode principale. On appel updateCorrespondance() qui va supprimer les entrées de la table Correspondancepersonne
		 * pour les recalculer en fonction du nom/prenom/dtnai/nir
		 */
		public function main() {
			$this->out();

			$Correspondancepersonne = ClassRegistry::init( 'Correspondancepersonne' );
			$this->out( count($Correspondancepersonne->updateCorrespondance()) . ' correspondances ajoutés' );
			
			$this->out();
		}
		
		/**
		 * Trouve les correspondances selon un personne_id
		 * @arg[0] integer personne_id
		 */
		public function personne_id() {
			$this->out();
			$Correspondancepersonne = ClassRegistry::init( 'Correspondancepersonne' );
			if ( !isset($this->args[0] ) ){
				$this->args[0] = $this->in('Indiquez personne_id :');
			}
			
			$finded = $Correspondancepersonne->updateByPersonneId( $this->args[0], false );
			
			foreach( $finded as $value ) {
				$this->out( var_export($value) );
			}
			
			$this->out( sprintf( 'Soit %s enregistrements', count($finded) ) );
			
			$this->out();
		}
		
		public function find() {
			$this->out();
			$Correspondancepersonne = ClassRegistry::init( 'Correspondancepersonne' );
			if ( !isset($this->args[0]) ){
				$this->args[0] = $this->in('Filtrer selon anomalie ou pas d\'anomalie ?', array('o','n'), 'o');
			}
			if ( !isset($this->args[1]) ){
				$this->args[1] = $this->args[0] === 'o' ? $this->in('Afficher uniquement les anomalies (1) ou afficher uniquement les non anomalies (2) :', array(1,2), 2) : null;
			}
			if ( !isset($this->args[2]) ){
				$this->args[2] = $this->in('Indiquez la limit (format SQL) :');
			}
			
			$conditions = $this->args[1] === 1 ? array('anomalie' => true) : ($this->args[1] === 2 ? array('anomalie' => false) : array());
			$limit = $this->args[2];
			
			$finded = $Correspondancepersonne->find( 
				'all', 
				array( 
					'fields' => array(
						'personne1_id', 
						'personne2_id',
						'Foyer1.dossier_id',
						'Foyer2.dossier_id',
						'anomalie'
					),
					'joins' => array(
						array(
							'alias' => 'Personne1',
							'table' => 'personnes',
							'conditions' => 'Personne1.id = personne1_id',
							'type' => 'INNER'
						),
						array(
							'alias' => 'Personne2',
							'table' => 'personnes',
							'conditions' => 'Personne2.id = personne2_id',
							'type' => 'INNER'
						),
						array(
							'alias' => 'Foyer1',
							'table' => 'foyers',
							'conditions' => 'Foyer1.id = Personne1.foyer_id',
							'type' => 'INNER'
						),
						array(
							'alias' => 'Foyer2',
							'table' => 'foyers',
							'conditions' => 'Foyer2.id = Personne2.foyer_id',
							'type' => 'INNER'
						),
					),
					'conditions' => $conditions, 
					'limit' => $limit 
				) 
			);
			
			foreach( $finded as $value ) {
				$this->out( var_export(Hash::flatten($value, '.')) );
			}
			
			$this->out( sprintf( 'Soit %s enregistrements', count($finded) ) );
		}

		/**
		 * Paramétrages et aides du shell.
		 */
		public function getOptionParser() {
			$Parser = parent::getOptionParser();
			return $Parser;
		}
	}
?>