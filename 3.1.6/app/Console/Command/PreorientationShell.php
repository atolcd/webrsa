<?php
	/**
	 * Fichier source de la classe PreorientationShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe PreorientationShell ...
	 *
	 * @package app.Console.Command
	 */
	class PreorientationShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $uses = array( 'Orientstruct', 'Cohorte', 'Typeorient' );

		/**
		 *
		 */
		protected function _showParams() {
			parent::_showParams();
			$this->out( '<info>Nombre d\'enregistrements à traiter</info> : <important>'.$this->params['limit'].'</important>' );
			$this->out( '<info>Forcer le calcul de la préorientation</info> : <important>'.($this->params['force'] ? 'true' : 'false').'</important>' );
		}

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description('Ce script permet de calculer la préorientation des allocataires.');
			$options = array(
				'limit' => array(
					'short' => 'L',
					'default' => 0,
					'help' => 'Nombre d\'enregistrements à traiter. Utilisez 0 ou null pour ne pas avoir de limite et traiter tous les enregistrements.'
				),
				'force' => array(
					'short' => 'f',
					'default' => 'false',
					'boolean' => true,
					'help' => 'Doit-on forcer le calcul de la préorientation même si une préorientation a déjà été calculée ?'
				)
			);
			$parser->addOptions( $options );
			return $parser;
		}


		/**
		 * Ajout d'entrée dans la table orientsstructs pour les DEM ou CJT RSA n'en possédant pas.
		 *
		 * @return boolean
		 */
		public function fillAllocataire() {
			$sql = "INSERT INTO orientsstructs( personne_id, statut_orient )
				(
					SELECT
							DISTINCT personnes.id,
							'Non orienté' AS statut_orient
						FROM personnes
							INNER JOIN prestations ON (
								prestations.personne_id = personnes.id
								AND prestations.natprest = 'RSA'
								AND (
									prestations.rolepers = 'DEM'
									OR prestations.rolepers = 'CJT'
								)
							)
						WHERE personnes.id NOT IN (
							SELECT orientsstructs.personne_id
								FROM orientsstructs
						)
				);";
			return ( $this->Orientstruct->query( $sql ) !== false );
		}

		/**
		 *
		 */
		public function main() {
			$success = true;
			$compteur = 0;
			$nSuccess = 0;
			$nUndefined = 0;
			$nErrors = 0;


			$this->Orientstruct->begin();

			$typesOrient = $this->Typeorient->find(
					'list', array(
				'fields' => array(
					'Typeorient.id',
					'Typeorient.lib_type_orient'
				),
				'order' => 'Typeorient.lib_type_orient ASC'
					)
			);
			$typesOrient[null] = 'Non définissable';

			$countTypesOrient = array_combine( array_keys( $typesOrient ), array_pad( array( ), count( $typesOrient ), 0 ) );
			$typesOrient = array_flip( $typesOrient );

			//------------------------------------------------------------------

			if( $this->params['force'] ) {
				$this->_wait( "Remise à zéro des propositions d'orientation pour les personnes non orientées." );

				$sql = "UPDATE orientsstructs
							SET propo_algo = NULL,
								date_propo = NULL
							WHERE statut_orient <> 'Orienté'";
				$t = $this->Orientstruct->query( $sql );
			}

			//------------------------------------------------------------------

			$this->_wait( "Ajout d'entrée dans la table orientsstructs pour les DEM ou CJT RSA n'en possédant pas." );
			$t = $this->fillAllocataire();

			//------------------------------------------------------------------

			$sqlCommon = "FROM orientsstructs
						INNER JOIN prestations ON (
							orientsstructs.personne_id = prestations.personne_id
							AND prestations.natprest = 'RSA'
							AND prestations.rolepers IN ( 'DEM', 'CJT' )
						)
						INNER JOIN personnes ON ( orientsstructs.personne_id = personnes.id )
						INNER JOIN calculsdroitsrsa ON ( orientsstructs.personne_id = calculsdroitsrsa.personne_id )
					WHERE orientsstructs.propo_algo IS NULL
						AND orientsstructs.date_propo IS NULL
						AND calculsdroitsrsa.toppersdrodevorsa = '1'
						AND orientsstructs.statut_orient <> 'Orienté'
						".(!empty( $this->params['limit'] ) ? "LIMIT {$this->params['limit']}" : "" );

			//------------------------------------------------------------------

			$nPersonnes = $this->Orientstruct->query( "SELECT COUNT( personnes.id ) {$sqlCommon}" );
			$nPersonnes = $nPersonnes[0][0]['count'];
			if( !empty( $this->params['limit'] ) ) {
				$this->out( sprintf( "%s personnes à traiter (sur %s au total)", min( $nPersonnes, $this->params['limit'] ), $nPersonnes ) );
				$nPersonnes = min( $nPersonnes, $this->params['limit'] );
			}
			else {
				$this->out( "{$nPersonnes} personnes à traiter" );
			}
			$this->hr();


			$personnes = $this->Orientstruct->query(
					"SELECT
						personnes.id AS \"Personne__id\",
						personnes.nom AS \"Personne__nom\",
						personnes.prenom AS \"Personne__prenom\",
						personnes.nir AS \"Personne__nir\",
						personnes.dtnai AS \"Personne__dtnai\",
						orientsstructs.id AS \"Orientstruct__id\",
						orientsstructs.personne_id AS \"Orientstruct__personne_id\"
					{$sqlCommon}"
			);


			$this->XProgressBar->start( count( $personnes ) );
			foreach( $personnes as $personne ) {
				$this->XProgressBar->next();
				$preOrientationTexte = $this->Cohorte->preOrientation( $personne );
				$preOrientation = Set::enum( $preOrientationTexte, $typesOrient );
				$countTypesOrient[$preOrientation]++;

				if( empty( $preOrientation ) ) {
					$nUndefined++;
				}
				else {
					$nSuccess++;
				}

				$sql = "UPDATE orientsstructs
							SET
								date_propo = '".date( 'Y-m-d' )."',
								propo_algo = ".(!empty( $preOrientation ) ? $preOrientation : 'NULL' )."
							WHERE id = {$personne['Orientstruct']['id']}";

				$tmpSuccess = ( $this->Orientstruct->query( $sql ) !== false );

				if( !$tmpSuccess ) {
					$nErrors++;
				}
				$success = $tmpSuccess && $success;
				$compteur++;
			}


			/// Fin de la transaction
			$message = "%s ({$compteur} enregistrements traités; {$nSuccess} préorientations calculées, {$nUndefined} préorietations incalculables ({$nErrors} erreurs)";
			if( $success ) {
				$this->out( sprintf( $message, "Script terminé avec succès" ) );
				$this->Orientstruct->commit();
			}
			else {
				$this->out( sprintf( $message, "Script terminé avec erreurs" ) );
				$this->Orientstruct->rollback();
			}
		}

	}
?>