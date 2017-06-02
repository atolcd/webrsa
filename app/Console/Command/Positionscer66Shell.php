<?php
	/**
	 * Fichier source de la classe Positionscer66Shell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::uses( 'Contratinsertion', 'Model' );
	App::uses( 'File', 'Utility' );

	/**
	 * La classe Positionscer66Shell effectue la mise à jour de la position des CER qui en ont besoin :
	 * 	- les CER sont positionnés « En cours:Bilan à réaliser » lorsque la date de fin
	 * 	  du CER n'est pas encore dépassée, mais que celle-ci est plus petite que l'intervalle
	 * 	  spécifié par la configuration  Contratinsertion.Cg66.updateEncoursbilan.
	 * 	- les CER sont positionnés « Périmé » lorsque la date de fin du CER est dépassée, et
	 * 	  qu'il n'existe pas de bilan de parcours non annulé concernant ce CER.
	 * 	- les CER sont positionnés « XXXX » lorsque l'allocataire auxquel ils
	 * 	  se trouvent dans un dossier dont les droits sont clos et que la position
	 * 	  du CER n'est ni "Annulé", ni "Fin de contrat", ni "Périmé".
	 *
	 * Voir le document app/docs/Documentation administrateurs.odt, partie
	 * "Intervalles PostgreSQL"
	 *
	 * @package app.Console.Command
	 */
	class Positionscer66Shell extends XShell
	{

		/**
		 * Modèles utilisés par ce Shell
		 *
		 * @var array
		 */
		public $uses = array( 'Contratinsertion' );

		/**
		 * Le modèle du contrat d'insertion
		 *
		 * @var AppModel
		 */
		public $Contratinsertion;

		/**
		 * Permet de garder la trace du nombre de CER au total ainsi que dans
		 * chacune des positions.
		 *
		 * @var array
		 */
		public $counts = array( 'total' => null );

		/**
		 * Démarage du shell, vérification de l'environnement et des paramètres.
		 */
		public function startup() {
			parent::startup();
			$this->checkDepartement( 66 );

			$result = $this->Contratinsertion->WebrsaContratinsertion->checkConfigUpdateEncoursbilanCg66();
			if( $result !== true ) {
				$this->err( "Mauvaise configuration de Contratinsertion.Cg66.updateEncoursbilan dans le fichier webrsa.inc\n{$check}" );
				$this->_stop( self::ERROR );
			}

			// Vérification des arguments
			$valid = array_merge( array_keys( $this->Contratinsertion->enum( 'positioncer' ) ), array( 'all' ) );
			$diff = array_diff( $this->args, $valid );
			if( !empty( $diff ) ) {
				$msgstr = 'Argument(s) non valide(s): %s';
				$this->err( sprintf( $msgstr, implode( ', ', $diff ) ) );
				$this->_stop( self::ERROR );
			}
		}

		/**
		 * Mise à jour et comptage des CER pour une position donnée.
		 *
		 * @param string $positioncer
		 * @return boolean
		 */
		public function updatePositionsCersByPosition( $positioncer ) {
			$conditions = $this->Contratinsertion->WebrsaContratinsertion->getConditionsPositioncer( $positioncer );
			$this->counts[$positioncer] = $this->Contratinsertion->find( 'count', array( 'conditions' => $conditions, 'recursive' => -1 ) );

			$msgstr = 'Mise à jour de la position "%s" pour %d contrats d\'engagement réciproque ( %.2f %% ).';
			$this->out( sprintf( $msgstr, __d( 'contratinsertion', "ENUM::POSITIONCER::{$positioncer}" ), $this->counts[$positioncer], ( $this->counts[$positioncer] / max( $this->counts['total'], 1 ) ) * 100 ) );

			return $this->Contratinsertion->WebrsaContratinsertion->updatePositionsCersByPosition( $positioncer );
		}

		/**
		 * Retourne les conditions permettant de retrouver les CER dont la position
		 * ne peut pas être calculée.
		 *
		 * @return array
		 */
		public function getUndetectedRecords() {
			$query = array(
				'fields' => array( 'Contratinsertion.id' ),
				'conditions' => array(
					$this->Contratinsertion->WebrsaContratinsertion->getConditionNonCalculables()
				),
				'contain' => false
			);

			// Indication du nombre
			$count = $this->Contratinsertion->find( 'count', $query );
			$msgstr = '%d contrats d\'engagement réciproque dont la position ne peut pas être calculée ( %.2f %% )';
			$this->out( sprintf( $msgstr, $count, ( $count / $this->counts['total'] ) * 100 ) );

			// Génération de la requête SQL permettant de les trouver
			$sql = $this->Contratinsertion->sq( $query );
			$sql = str_replace( '"Contratinsertion"."id" AS "Contratinsertion__id"', '*', $sql );

			// Sauvegarde de la requête SQL dans le fichier
			$filename = LOGS.$this->name.'_non_mises_a_jour_'.date( 'Ymd-His' ).'.sql';
			$File = new File( $filename, true );
			$File->write( $sql );
			$File->close();

			$msgstr = "<info>La requête SQL des non calculables se trouve dans</info> %s";
			$this->out( sprintf( $msgstr, $this->shortPath( $File->pwd() ) ) );
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			// Début de la transaction
			$this->Contratinsertion->begin();
			$success = true;

			$this->counts['total'] = $this->Contratinsertion->find( 'count', array( 'contain' => false ) );
			$this->out();
			$this->out( sprintf( "%d contrats d'engagement réciproque au total", $this->counts['total'] ) );
			$this->out();
			$this->hr();
			$this->out();

			foreach( $this->args as $arg ) {
				if( $arg === 'all' ) {
					foreach( array_keys( $this->Contratinsertion->enum( 'positioncer' ) ) as $positioncer ) {
						$success = $success && $this->updatePositionsCersByPosition( $positioncer );
					}
				}
				else {
					$success = $success && $this->updatePositionsCersByPosition( $arg );
				}
			}

			// Fin de la transaction
			if( $success ) {
				$this->Contratinsertion->commit();
				$msg = "<success>La mise à jour des positions du CER a été effectuée avec succès.</success>";
			}
			else {
				$this->Contratinsertion->rollback();
				$msg = "<error>Erreur lors de la mise à jour des positions du CER.</error>";
			}

			$this->out();
			$this->hr();
			$this->out( $msg );

			// Résumé des opérations
			if( $success ) {
				$this->hr();
				$this->out();

				$counts = $this->counts;
				$total = max( $counts['total'], 1 );
				unset( $counts['total'] );
				$sum = array_sum( $counts );

				$msgstr = "%-60s\t%d\t%.2f %%";

				foreach( $this->counts as $positioncer => $number ) {
					if( $positioncer !== 'total' ) {
						$this->out( sprintf( $msgstr, __d( 'contratinsertion', "ENUM::POSITIONCER::{$positioncer}" ), $number, ( $number / $total ) * 100 ) );
					}
				}

				$this->out( sprintf( $msgstr, 'Total des CER dont la position a été mise à jour', $sum, ( $sum / $total ) * 100 ) );

				$this->out();
				$this->getUndetectedRecords();
			}

			$this->out();
			$this->_stop( $success ? self::SUCCESS : self::ERROR );
		}

		/**
		 * Paramétrages et aides du shell.
		 *
		 * @return ConsoleOptionParser
		 */
		public function getOptionParser() {
			$Parser = parent::getOptionParser();
			$Parser->description( 'Effectue la mise à jour de la position des CER.' );

			$arguments = array(
				'all' => array(
					'help' => 'Effectue la mise à jour vers toutes les positions'
				)
			);

			foreach( array_keys( $this->Contratinsertion->enum( 'positioncer' ) ) as $positioncer ) {
				$arguments[$positioncer] = array(
					'help' => sprintf( 'Met à jour les CER dont la position devrait être \'%s\'.', __d( 'contratinsertion', "ENUM::POSITIONCER::{$positioncer}" ) )
				);
			}

			$Parser->addSubcommands( $arguments );
			return $Parser;
		}

	}
?>