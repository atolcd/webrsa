<?php
	/**
	 * Fichier source de la classe HistoriquesdroitsShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe HistoriquesdroitsShell ...
	 *
	 * @package app.Console.Command
	 */
	class HistoriquesdroitsShell extends XShell
	{
		/**
		 * Modèles utilisés par ce shell
		 *
		 * @var array
		 */
		public $uses = array( 'Historiquedroit' );

		/**
		 * Paramètres par défaut pour ce shell
		 *
		 * @var array
		 */
		public $defaultParams = array(
			'log' => false,
			'logpath' => LOGS,
			'verbose' => false,
		);

		/**
		 * Affiche l'en-tête du shell
		 */
		public function _welcome() {
			$this->out();
			$this->out( 'Shell d\'historisation des états droits et devoirs et de l\'état du dossier des allocataires' );
			$this->out();
			$this->hr();
			$this->out();
		}

		/**
		 *
		 * @param array $querydata
		 * @param boolean $cloturePcd
		 * @return boolean
		 */
		protected function _ajoutHistoriques( array $querydata, $cloturePcd = false ) {
			$success = true;

			$count = $this->Historiquedroit->Personne->find( 'count', $querydata );
			$this->out( "\t{$count} enregistrement(s) à traiter" );

			if( $count > 0 ) {
				if( $cloturePcd ) {
					$queryPcd = $querydata;
					$queryPcd['fields'] = array( 'Historiquedroit.id' );
					$sql = $this->Historiquedroit->Personne->sq( $queryPcd );
					$sql = "UPDATE historiquesdroits SET modified = NOW() - INTERVAL '1 day' WHERE id IN ( {$sql} );";
					$success = $success && ( $this->Historiquedroit->query( $sql ) !== false );
				}

				$sql = $this->Historiquedroit->Personne->sq( $querydata );
				$sql = "INSERT INTO historiquesdroits ( personne_id, toppersdrodevorsa, etatdosrsa, created, modified ) {$sql}";
				$success = ( $this->Historiquedroit->query( $sql ) !== false );
			}

			return $success;
		}

		/**
		 *
		 * @param array $querydata
		 * @param boolean $update
		 * @return array
		 */
		protected function _qdHistoriquesExistants( array $querydata, $update ) {
			$querydata['joins'][] = $this->Historiquedroit->Personne->join( 'Historiquedroit', array( 'type' => 'INNER' ) );

			$querydataSq = array(
				'alias' => 'historiquesdroits',
				'fields' => array( 'historiquesdroits.id' ),
				'contain' => false,
				'conditions' => array(
					'historiquesdroits.personne_id = Personne.id',
				),
				'order' => array( 'historiquesdroits.modified DESC' ),
				'limit' => 1
			);
			$sq = $this->Historiquedroit->sq( $querydataSq );
			$querydata['conditions'][] = "Historiquedroit.id IN ( {$sq} )";

			// On doit se méfier des valeurs NULL pour les comparaisons
			$conditionToppersdrodevorsaEquals = array(
				'OR' => array(
					array(
						'"Historiquedroit"."toppersdrodevorsa" IS NOT NULL',
						'"Calculdroitrsa"."toppersdrodevorsa" IS NOT NULL',
						'"Historiquedroit"."toppersdrodevorsa" = "Calculdroitrsa"."toppersdrodevorsa"::VARCHAR(1)',
					),
					array(
						'"Historiquedroit"."toppersdrodevorsa" IS NULL',
						'"Calculdroitrsa"."toppersdrodevorsa" IS NULL',
					),
				)
			);

			// On doit se méfier des valeurs NULL pour les comparaisons
			$conditionEtatdosrsaEquals = array(
				'OR' => array(
					array(
						'Historiquedroit.etatdosrsa IS NOT NULL',
						'Situationdossierrsa.etatdosrsa IS NOT NULL',
						'Historiquedroit.etatdosrsa = Situationdossierrsa.etatdosrsa'
					),
					array(
						'Historiquedroit.etatdosrsa IS NULL',
						'Situationdossierrsa.etatdosrsa IS NULL',
					),
				)
			);

			if( $update ) {
				$querydata['conditions'][] = array(
					$conditionToppersdrodevorsaEquals,
					$conditionEtatdosrsaEquals,
				);
			}
			else {
				$querydata['conditions'][] = array(
					'OR' => array(
						array( 'NOT' => $conditionToppersdrodevorsaEquals ),
						array( 'NOT' => $conditionEtatdosrsaEquals ),
					)
				);
			}

			return $querydata;
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			$success = true;
			$this->Historiquedroit->begin();

			$querydataBase = array(
				'fields' => array(
					'"Personne"."id" AS "personne_id"',
					'"Calculdroitrsa"."toppersdrodevorsa" AS "toppersdrodevorsa"',
					'"Situationdossierrsa"."etatdosrsa" AS "etatdosrsa"',
					'NOW() AS "created"',
					'NOW() AS "modified"'
				),
				'contain' => false,
				'joins' => array(
					$this->Historiquedroit->Personne->join( 'Calculdroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$this->Historiquedroit->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Historiquedroit->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$this->Historiquedroit->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->Historiquedroit->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'LEFT OUTER' ) ),
				),
				'conditions' => array(
					'Prestation.rolepers' => array( 'DEM', 'CJT' )
				)
			);

			// -----------------------------------------------------------------
			// Mise à jour des enregistrements pour les allocataires en possédant déjà et dont l'état n'a pas évolué
			// -----------------------------------------------------------------
			$this->out( 'Mise à jour des enregistrements pour les allocataires en possédant déjà et dont l\'état n\'a pas évolué' );
			$querydata = $this->_qdHistoriquesExistants( $querydataBase, true );
			$querydata['fields'] = array( '"Historiquedroit"."id" AS "id"' );

			$count = $this->Historiquedroit->Personne->find( 'count', $querydata );
			$this->out( "\t{$count} enregistrement(s) à traiter" );

			$sql = $this->Historiquedroit->Personne->sq( $querydata );
			$sql = "UPDATE historiquesdroits SET modified = NOW() WHERE id IN ( {$sql} );";
			$success = $success && ( $this->Historiquedroit->query( $sql ) !== false );

			// -----------------------------------------------------------------
			// Mise à jour et ajout d'enregistrements pour les allocataires en
			// possédant déjà mais dont un état a évolué
			// -----------------------------------------------------------------
			$this->out( 'Ajout d\'enregistrements pour les allocataires en possédant déjà mais dont un état a évolué' );
			$querydata = $this->_qdHistoriquesExistants( $querydataBase, false );
			$success = $success && $this->_ajoutHistoriques( $querydata, true );

			// -----------------------------------------------------------------
			// Ajout d'enregistrements pour les allocataires n'en possédant pas encore
			// -----------------------------------------------------------------
			$this->out( 'Ajout d\'enregistrements pour les allocataires n\'en possédant pas encore' );
			$querydata = $querydataBase;
			$querydataSq = array(
				'alias' => 'historiquesdroits',
				'fields' => array( 'historiquesdroits.personne_id' ),
				'contain' => false,
				'conditions' => array(
					'historiquesdroits.personne_id = Personne.id'
				)
			);
			$sq = $this->Historiquedroit->sq( $querydataSq );
			$querydata['conditions'][] = array( "Personne.id NOT IN ( {$sq} )" );

			$success = $success && $this->_ajoutHistoriques( $querydata );

			if( $success ) {
				$this->Historiquedroit->commit();
				$this->out( 'Succès' );
			}
			else {
				$this->Historiquedroit->rollback();
				$this->err( 'Erreur' );
			}
		}
	}
?>