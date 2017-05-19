<?php
	/**
	 * Fichier source de la classe AnomaliesbddShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::uses( 'View', 'View' );
	App::uses( 'HtmlHelper', 'View/Helper' );

	/**
	 * La classe AnomaliesbddShell ...
	 *
	 * INFO:
	 * 	- base de données d'environ 200 Mo, datée du 04/11/2009
	 * 		* 103664 dossiers
	 * 		* 282952 personnes
	 * 		=> script "full": environ 16 heures (57.430,52 secondes), fichier en sortie de 75,5 Mo
	 * 			+ Dossiers sans personne: 121 résultats
	 * 			+ Dossiers contenant des personnes mais sans demandeur RSA: 207 résultats
	 * 			+ Personnes avec NIR en doublon dans des foyers différents: 39986 personnes à traiter
	 * 			+ Personnes avec NIR en doublon dans le même foyer: 83157 personnes à traiter
	 * 			+ Personnes avec au moins un NIR manquant en doublon dans des foyers différents: 511 personnes à traiter
	 * 			+ Personnes avec au moins un NIR manquant en doublon dans le même foyer: 32 personnes à traiter
	 * 			+ Recherche des personnes demandeurs ou conjoints multiples au sein d'un foyer: 14444 personnes à traiter
	 *
	 * @package app.Console.Command
	 */
	class AnomaliesbddShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $uses = array( 'Personne' );

		/**
		 *
		 * @var type
		 */
		public $output = '';

		/**
		 *
		 * @var type
		 */
		public $outfile = null;

		/**
		 *
		 * @var type
		 */
		public $Html;

		/**
		 *
		 * @var type
		 */
		public $fields = array(
			'Foyer.Dossier.numdemrsa',
			'Personne.id',
			'Personne.nir',
			'Personne.qual',
			'Personne.nom',
			'Personne.prenom',
			'Personne.nomnai',
			'Personne.dtnai',
			'Prestation.rolepers',
		);

		/**
		 *
		 * @var type
		 */
		public $headers = array(
			'N° demande',
			'id',
			'NIR',
			'Qual',
			'Nom',
			'Prénom',
			'Nomnai',
			'Dtnai',
			'Rolepers',
			'# personnes',
			'# contrats',
			'# orientstructs',
			'# RDV',
			'# DSP',
			'Titre séjour',
			'# grossesses',
			'# activités',
			'# avis PCG personnes',
		);

		/**
		 *
		 * @var type
		 */
		public $limit = '';

		/**
		 *
		 * @var type
		 */
		public $pageTitle = 'Rapport sur les anomalies des données de la base de données du %s';

		/**
		 *
		 * @var type
		 */
		public $script = null;

		/**
		 *
		 * @var type
		 */
		private $_options = array(
			'type' => array(
				'short' => 't',
				'help' => "Type de rapport\n\tshort: permet de comptabiliser les différentes erreurs\n\tfull:  permet de lister toutes les entrées posant problème",
				'choices' => array( 'short', 'full' ),
				'default' => 'short'
			),
			'dossiersvides' => array(
				'short' => 'd',
				'help' => 'Dossiers ne contenant aucune personne liée',
				'boolean' => true,
				'default' => 'false'
			),
			'dossierssansdemandeur' => array(
				'short' => 'D',
				'help' => "Dossiers contenant des personnes, mais sans demandeur RSA",
				'boolean' => true,
				'default' => 'false'
			),
			'nirsdoublonsfoyersdiff' => array(
				'short' => 'n',
				'help' => "Personnes avec NIR en doublon dans des foyers différents",
				'boolean' => true,
				'default' => 'false'
			),
			'nirsdoublonsmemefoyer' => array(
				'short' => 'N',
				'help' => "Personnes avec NIR en doublon dans le même foyer",
				'boolean' => true,
				'default' => 'false'
			),
			'personnesdoublonsfoyersdiff' => array(
				'short' => 'p',
				'help' => "Personnes avec au moins un NIR manquant en doublon dans des foyers différents",
				'boolean' => true,
				'default' => 'false'
			),
			'personnesdoublonsmemefoyer' => array(
				'short' => 'P',
				'help' => "Personnes avec au moins un NIR manquant en doublon dans le même foyer",
				'boolean' => true,
				'default' => 'false'
			),
			'demcjtmultiples' => array(
				'short' => 'm',
				'help' => "Personnes demandeurs ou conjoints multiples au sein d\'un foyer",
				'boolean' => true,
				'default' => 'false'
			)
		);

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->addOptions( $this->_options );
			return $parser;
		}

		/**
		 *
		 */
		public function startup() {
			parent::startup();
			$this->hr();

			$this->script = strtolower( preg_replace( '/Shell$/', '', $this->name ) );

			$showHelp = true;
			foreach( $this->_options as $name => $option ) {
				if( $this->params[$name] === true ) {
					$showHelp = false;
				}
			}
			if( $showHelp ) {
				$this->out( $this->OptionParser->help() );
				$this->_stop();
			}

			/// Nom du fichier et titre de la page
			$this->outfile = sprintf( '%s-%s-%s.html', $this->script, date( 'Ymd-His' ), $this->params['type'] );
			$this->outfile = APP_DIR.'/tmp/logs/'.$this->outfile;
			$this->pageTitle = sprintf( $this->pageTitle, date( 'd-m-Y H:i:s' ) );
			App::uses( 'HtmlHelper', 'View/Helper' );
			$this->Html = new HtmlHelper( new View() );
		}

		protected function _showParams() {
			parent::_showParams();
			$this->out( '<info>Type de rapport : </info><important>'.$this->params['type'].'</important>' );
			$this->out( '<info>Dossiers ne contenant aucune personne liée : </info><important>'.($this->params['dossiersvides'] ? 'true' : 'false' ).'</important>' );
			$this->out( '<info>Dossiers contenant des personnes, mais sans demandeur RSA : </info><important>'.($this->params['dossierssansdemandeur'] ? 'true' : 'false' ).'</important>' );
			$this->out( '<info>Personnes avec NIR en doublons dans des foyers différents : </info><important>'.($this->params['nirsdoublonsfoyersdiff'] ? 'true' : 'false' ).'</important>' );
			$this->out( '<info>Personnes avec NIR en doublons dans le même foyer : </info><important>'.($this->params['nirsdoublonsmemefoyer'] ? 'true' : 'false' ).'</important>' );
			$this->out( '<info>Personnes avec au moins un NIR manquant en doublon dans des foyers différents : </info><important>'.($this->params['personnesdoublonsfoyersdiff'] ? 'true' : 'false' ).'</important>' );
			$this->out( '<info>Personnes avec au moins un NIR manquant en doublon dans le même foyer : </info><important>'.($this->params['personnesdoublonsmemefoyer'] ? 'true' : 'false' ).'</important>' );
			$this->out( '<info>Personnes demandeurs ou conjoints multiples au sein d\'un foyer : </info><important>'.($this->params['demcjtmultiples'] ? 'true' : 'false' ).'</important>' );
		}

		/**
		 *
		 * @param type $result
		 * @return type
		 */
		public function row( $result ) {
			$qd_p1 = array(
				'conditions' => array(
					'Personne.id' => Set::classicExtract( $result, 'p1.id' )
				),
				'fields' => null,
				'order' => null,
				'recursive' => 2
			);
			$p1 = $this->Personne->find( 'first', $qd_p1 );

			$qd_p2 = array(
				'conditions' => array(
					'Personne.id' => Set::classicExtract( $result, 'p2.id' )
				),
				'fields' => null,
				'order' => null,
				'recursive' => 2
			);
			$p2 = $this->Personne->find( 'first', $qd_p2 );

			$return = '';
			foreach( array( $p1, $p2 ) as $p ) {
				$row = array( );
				foreach( $this->fields as $field ) {
					$row[] = Set::classicExtract( $p, $field );
				}
				$row[] = count( Set::classicExtract( $p, 'Foyer.Personne' ) );
				$row[] = count( Set::classicExtract( $p, 'Contratinsertion' ) );
				$orientstructs = Set::classicExtract( $p, 'Orientstruct' );
				foreach( $orientstructs as $key => $orientstruct ) {
					if( Set::classicExtract( $orientstruct, 'statut_orient' ) == 'Non orienté' ) {
						unset( $orientstructs[$key] ); // FIXME -> OK ?
					}
				}
				$row[] = count( $orientstructs ); // FIXME: rempli ou pas ?
				$row[] = count( Set::classicExtract( $p, 'Rendezvous' ) );
				$dsp = Set::classicExtract( $p, 'Dsp' );
				foreach( array( 'id', 'personne_id' ) as $rField ) {
					unset( $dsp[$rField] );
				}
				$row[] = count( Hash::filter( (array)$dsp ) );

				$Titresejour = Hash::filter( (array)Set::classicExtract( $p, 'Titresejour' ) );
				$row[] = ( empty( $Titresejour ) ? 0 : 1 );

				$grossesses = $this->Personne->query( 'SELECT COUNT(*) AS count FROM grossesses WHERE personne_id = '.Set::classicExtract( $p, 'Personne.id' ) );
				$row[] = Set::classicExtract( $grossesses, '0.0.count' );

				$row[] = count( Set::classicExtract( $p, 'Activite' ) );

				$Avispcgpersonne = Hash::filter( (array)Set::classicExtract( $p, 'Avispcgpersonne' ) );
				$row[] = ( empty( $Avispcgpersonne ) ? 0 : 1 );

				$return .= $this->Html->tableCells( $row, array( 'class' => 'odd' ), array( 'class' => 'even' ) );
			}

			return $this->Html->tag( 'tbody', $return );
		}

		/**
		 *
		 * @param type $results
		 * @return string
		 */
		public function table( $results ) {
			$return = '';
			if( !empty( $results ) ) {
				$return .= '<table>';
				$return .= '<thead>'.$this->Html->tableHeaders( $this->headers ).'</thead>';
				foreach( $results as $result ) {
					$return .= $this->row( $result );
				}
				$return .= '</table>';
			}

			return $return;
		}

		/**
		 *
		 */
		public function main() {
			$queries = array(
				/// Recherche des personnes avec NIR en doublon dans des foyers différents
				'nirsdoublonsfoyersdiff' => array(
					'title' => 'Personnes avec NIR en doublon dans des foyers différents',
					'sql' => "SELECT p1.id AS p1__id, p2.id AS p2__id, p1.foyer_id AS p1__foyer_id, p2.foyer_id AS p2__foyer_id, p1.nom AS p1__nom, p1.prenom AS p1__prenom, p1.dtnai AS p1__dtnai, p1.nomnai AS p1__nomnai
								FROM personnes AS p1, personnes AS p2
								WHERE p1.nir = p2.nir
									AND p1.id < p2.id
									AND p1.foyer_id <> p2.foyer_id
									AND p1.nir <> '' AND p1.nir IS NOT NULL
								ORDER BY p1.nom ASC, p1.prenom ASC
								{$this->limit};"
				),
				/// Recherche des personnes avec NIR en doublon dans le même foyer
				'nirsdoublonsmemefoyer' => array(
					'title' => 'Personnes avec NIR en doublon dans le même foyer',
					'sql' => "SELECT
								p1.id AS p1__id,
								p1.foyer_id AS p1__foyer_id,
								p1.nom AS p1__nom,
								p1.prenom AS p1__prenom,
								p1.dtnai AS p1__dtnai,
								p1.nomnai AS p1__nomnai,
								p2.id AS p2__id,
								p2.foyer_id AS p2__foyer_id
							FROM personnes AS p1, personnes AS p2
							WHERE p1.nir = p2.nir
								AND p1.id < p2.id
								AND p1.foyer_id = p2.foyer_id
								AND p1.nir <> '' AND p1.nir IS NOT NULL
							ORDER BY p1.nom ASC, p1.prenom ASC
							{$this->limit};"
				),
				/// Recherche des personnes avec au moins un NIR manquant en doublon dans des foyers différents
				'personnesdoublonsfoyersdiff' => array(
					'title' => 'Personnes avec au moins un NIR manquant en doublon dans des foyers différents',
					'sql' => "SELECT
								p1.id AS p1__id,
								p1.foyer_id AS p1__foyer_id,
								p1.nom AS p1__nom,
								p1.prenom AS p1__prenom,
								p1.dtnai AS p1__dtnai,
								p1.nomnai AS p1__nomnai,
								p2.id AS p2__id,
								p2.foyer_id AS p2__foyer_id
							FROM personnes AS p1, personnes AS p2
							WHERE p1.nom ILIKE p2.nom
								AND p1.prenom ILIKE p2.prenom
								AND p1.dtnai = p2.dtnai
								AND p1.id < p2.id
								AND p1.foyer_id <> p2.foyer_id
								AND ( ( p1.nir = '' OR p1.nir IS NULL ) OR ( p2.nir = '' OR p2.nir IS NULL ) )
							ORDER BY p1.nom ASC, p1.prenom ASC
							{$this->limit};"
				),
				/// Recherche des personnes avec au moins un NIR manquant en doublon dans le même foyer
				'personnesdoublonsmemefoyer' => array(
					'title' => 'Personnes avec au moins un NIR manquant en doublon dans le même foyer',
					'sql' => "SELECT
								p1.id AS p1__id,
								p1.foyer_id AS p1__foyer_id,
								p1.nom AS p1__nom,
								p1.prenom AS p1__prenom,
								p1.dtnai AS p1__dtnai,
								p1.nomnai AS p1__nomnai,
								p2.id AS p2__id,
								p2.foyer_id AS p2__foyer_id
							FROM personnes AS p1, personnes AS p2
							WHERE p1.nom ILIKE p2.nom
								AND p1.prenom ILIKE p2.prenom
								AND p1.dtnai = p2.dtnai
								AND p1.id < p2.id
								AND p1.foyer_id = p2.foyer_id
								AND ( ( p1.nir = '' OR p1.nir IS NULL ) OR ( p2.nir = '' OR p2.nir IS NULL ) )
							ORDER BY p1.nom ASC, p1.prenom ASC
							{$this->limit};"
				),
				/// Recherche des personnes demandeurs ou conjoints multiples au sein d'un foyer
				'demcjtmultiples' => array(
					'title' => 'Personnes demandeurs ou conjoints multiples au sein d\'un foyer',
					'sql' => "SELECT
								p1.id AS p1__id,
								p1.foyer_id AS p1__foyer_id,
								p1.nom AS p1__nom,
								p1.prenom AS p1__prenom,
								p1.dtnai AS p1__dtnai,
								p1.nomnai AS p1__nomnai,
								p2.id AS p2__id,
								p2.foyer_id AS p2__foyer_id/*,
								pr2.rolepers AS p2__rolepers*/
							FROM personnes AS p1,
								personnes AS p2,
								prestations AS pr1,
								prestations AS pr2
							WHERE p1.foyer_id = p2.foyer_id
								AND p1.id < p2.id
								AND pr1.personne_id = p1.id
								AND pr2.personne_id = p2.id
								AND pr1.natprest = 'RSA'
								AND pr2.natprest = 'RSA'
								AND pr1.rolepers = pr2.rolepers
								AND ( pr1.rolepers = 'DEM' OR pr1.rolepers = 'CJT' )
							ORDER BY p1.foyer_id, p1.nom ASC, p1.prenom ASC
							{$this->limit};"
				),
			);

			$this->output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
								"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
								<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
									<head>
										<title>'.$this->pageTitle.'</title>
										<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
										<style type="text/css" media="all">
											body { font-size: 12px; }
											table { border-collapse: collapse; }
											thead, tbody { border: 3px solid black; }
											th, td { border: 1px solid black; padding: 0.125em 0.25em; }
											tr.odd { background: #eee; }
										</style>
									</head>';
			$this->output .= '<body><h1>'.$this->pageTitle.'</h1>';

			/// Foyers vides
			if( $this->params['dossiersvides'] ) {
				$sql = "SELECT dossiers.id, dossiers.numdemrsa, dossiers.dtdemrsa
							FROM dossiers
								INNER JOIN foyers ON ( dossiers.id = foyers.dossier_id )
							WHERE foyers.id NOT IN ( SELECT personnes.foyer_id FROM personnes GROUP BY personnes.foyer_id )
							{$this->limit};";
				$results = $this->Personne->query( $sql );
				echo sprintf( "Dossiers sans personne: %s résultats\n", count( $results ) );
				$this->output .= $this->Html->tag( 'h2', 'Dossiers sans personne' );
				$this->output .= $this->Html->tag( 'p', sprintf( "Dossiers sans personne: %s résultats\n", count( $results ) ) );
				if( ( $this->params['type'] == 'full' ) && !empty( $results ) ) {
					$this->output .= '<table>';
					$this->output .= '<thead>'.$this->Html->tableHeaders( array( 'Id', 'Numdemrsa', 'dtdemrsa' ) ).'</thead>';
					$this->output .= $this->Html->tableCells( Set::classicExtract( $results, '{n}.0' ), array( 'class' => 'odd' ), array( 'class' => 'even' ) );
					$this->output .= '</table>';
				}
				$this->hr();
			}

			/// Dossiers contenant des personnes mais sans demandeur RSA
			if( $this->params['dossierssansdemandeur'] ) {
				$sql = "SELECT dossiers.id, dossiers.numdemrsa, dossiers.dtdemrsa
							FROM dossiers
								INNER JOIN foyers ON dossiers.id = foyers.dossier_id
							WHERE foyers.id NOT IN ( SELECT personnes.foyer_id FROM personnes INNER JOIN prestations ON ( prestations.personne_id = personnes.id AND prestations.rolepers = 'DEM' AND prestations.natprest = 'RSA' ) )
								AND foyers.id IN ( SELECT personnes.foyer_id FROM personnes GROUP BY personnes.foyer_id )
							{$this->limit};";
				$results = $this->Personne->query( $sql );
				echo sprintf( "Dossiers contenant des personnes mais sans demandeur RSA: %s résultats\n", count( $results ) );
				$this->output .= $this->Html->tag( 'h2', 'Dossiers contenant des personnes mais sans demandeur RSA' );
				$this->output .= $this->Html->tag( 'p', sprintf( "Dossiers contenant des personnes mais sans demandeur RSA: %s résultats\n", count( $results ) ) );
				if( ( $this->params['type'] == 'full' ) && !empty( $results ) ) {
					$this->output .= '<table>';
					$this->output .= '<thead>'.$this->Html->tableHeaders( array( 'Id', 'Numdemrsa', 'dtdemrsa' ) ).'</thead>';
					$this->output .= $this->Html->tableCells( Set::classicExtract( $results, '{n}.0' ), array( 'class' => 'odd' ), array( 'class' => 'even' ) );
					$this->output .= '</table>';
				}
				$this->hr();
			}

			/// Doublons sur les personnes
			foreach( $queries as $name => $query ) {
				if( $this->params[$name] ) {
					$results = $this->Personne->query( $query['sql'] );
					echo sprintf( "%s: %s personnes à traiter\n", $query['title'], count( $results ) );
					$this->output .= $this->Html->tag( 'h2', $query['title'] );
					$this->output .= $this->Html->tag( 'p', sprintf( "%s personnes en doublon", count( $results ) ) );
					if( $this->params['type'] == 'full' ) {
						$this->output .= $this->table( $results );
					}
					$this->hr();
				}
			}

			$this->output .= '</body>';
			$this->output .= '</html>';

			file_put_contents( $this->outfile, $this->output );
			$this->out( 'fichier généré : '.$this->outfile );
		}

	}
?>