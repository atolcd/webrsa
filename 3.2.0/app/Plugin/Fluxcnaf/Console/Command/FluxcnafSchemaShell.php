<?php
	/**
	 * Code source de la classe FluxcnafSchemaShell.
	 *
	 * PHP 5.3
	 *
	 * @package Fluxcnaf
	 * @subpackage Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppShell', 'Console/Command' );
	App::uses( 'Xml', 'Utility' );
	App::uses( 'FluxcnafSchema', 'Fluxcnaf.Utility' );

	/**
	 * La classe FluxcnafSchemaShell ...
	 *
	 * sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafSchema /tmp/vrsd0301.xsd,/tmp/vrsb0801.xsd
	 * FIXME: rien dans /tmp/vrsf0501.xsd ?
	 *        -> les autres ont xsd:element/xsd:complexType/xsd:sequence/xsd:element
	 *        -> vrsf0501 a xsd:complexType/xsd:sequence/xsd:element
	 * sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafSchema /tmp/vrsd0301.xsd,/tmp/virs0901.xsd,/tmp/vird0201.xsd,/tmp/vrsc0201.xsd
	 *
	 * @see http://xemelios.org/user-guide/documents/rsa.html
	 *
	 * @package Fluxcnaf
	 * @subpackage Console.Command
	 */
	class FluxcnafSchemaShell extends AppShell
	{
		/**
		 * La constante à utiliser dans la méthode _stop() en cas de succès.
		 */
		const SUCCESS = 0;

		/**
		 * La constante à utiliser dans la méthode _stop() en cas d'erreur.
		 */
		const ERROR = 1;

		public $uses = array( 'Fluxcnaf.Fluxcnaf' );

		/**
		 * Description du shell.
		 *
		 * @var string
		 */
		public $description = null;

		/**
		 * Les options à passer au shell.
		 *
		 * @var array
		 */
		public $options = array(
			'out' => array(
				'short' => 'o',
				'help' => 'Le fichier de sortie.',
				'default' => null
			)
		);

		/**
		 * Liste des sous-commandes et de leur description.
		 *
		 * @var array
		 */
		public $commands = array();

		/**
		 * Liste des arguments à passe au shell.
		 *
		 * @var array
		 */
		public $arguments = array(
			'xsd' => array(
				'help' => 'Le chemin vers le(s) fichier(s) XML à traiter, séparés par une virgule, sans espace',
				'required' => true
			)
		);

		protected function _out( $default ) {
			return ( isset( $this->params['out'] ) && !empty( $this->params['out'] ) ) ? $this->params['out'] : $default;
		}

		protected function _files() {
			$result = array();
			$tokens = explode( ',', $this->args[0] );

			foreach( $tokens as $token ) {
				$name = preg_replace( '/\.xsd/i', '', basename( $token ) );
				$result[$name] = $token;
			}

			return $result;
		}

		protected function _arrayToHtml( array $assoc, $keys = true ) {
			$result = '';

			if( !empty( $assoc ) ) {
				foreach( $assoc as $key => $value ) {
					if( $keys ) {
						$result .= "<li><strong>{$key}</strong>: $value</li>";
					}
					else {
						$result .= "<li><strong>{$value}</strong></li>";
					}
				}
				$result = "<ul>{$result}</ul>";
			}


			return $result;
		}

		protected function _parseComplexType( $xsd ) {
			$result = array();
			$parentName = (string)$xsd->attributes()->name;

			$elements = $xsd->xpath( 'xsd:complexType/xsd:sequence/xsd:element' );
			foreach( $elements as $element ) {
				$line = array( 'minOccurs' => 1, 'maxOccurs' => 1, 'type' => null, 'maxLength' => null, 'totalDigits' => null, 'fractionDigits' => null );
				$attributes = $element->attributes();
				$name = (string)$attributes->name;
//				debug($name);

				foreach( array_keys( $line ) as $key ) {
				if( isset( $attributes->{$key} ) ) {
						$line[$key] = (string)$attributes->{$key};
					}
				}

				$restrictions = $element->xpath( 'xsd:simpleType/xsd:restriction' );
				if( !empty( $restrictions ) ) {
					// Type de la donnée si on ne l'avait pas ci-dessus
					if( $line['type'] === null ) {
						$attributes = $restrictions[0]->attributes();
						$line['type'] = (string)$attributes->base;
					}

					foreach($restrictions as $restriction) {
						foreach( $restriction->xpath( '*' ) as $child ) {
							$line[$child->getName()] = (string)$child->attributes()->value;
						}
					}
				}

				foreach( array_keys( $line ) as $l ) {
					if( $line[$l] === null ) {
						unset( $line[$l] );
					}
				}
				ksort( $line );
				$result[$parentName . '/' . $name] = $line;
			}

			return $result;
		}

		protected function _diff( array $values ) {
			$result = array();

			foreach( $values as $current => $data ) {
				foreach( $values as $key => $params ) {
					if( $current > $key ) {
						$diff1 = array_diff_assoc( $data, $params );
						$diff2 = array_diff_assoc( $params, $data );
						$result = array_merge( $result, array_keys( $diff1 ), array_keys( $diff2 ) );
					}
				}
			}

			return array_unique( $result );
		}

		protected function _compareToHtml( array $names, array $results ) {
			$thead = '';
			foreach( array_keys( $names ) as $name ) {
				$label = Hash::get( $this->Fluxcnaf->names, $name );
				$thead .= "<th>{$label} ({$name})</th>";
			}
			$thead = "<tr><th>Balise</th>{$thead}<th>Différences</th></tr>";

			$tbody = '';
			foreach( $results as $field => $params ) {
				$values = array();

				$line = "<th>{$field}</th>";

				foreach( array_keys( $names ) as $name ) {
					if( $params[$name] === false ) {
						$line .= "<td class=\"false\"></td>";
					}
					else {
						$values[] = $params[$name];
						$line .= "<td>".$this->_arrayToHtml($params[$name])."</td>";
					}
				}

				// FIXME: dans la classe utilitaire
				$diff = $this->_diff( $values );

				if( count( $diff ) > 0 ) {
					$line .= "<td class=\"diff\">".$this->_arrayToHtml( $diff, false )."</td>";
				}
				else {
					$line .= "<td class=\"diff empty\"></td>";
				}

				$tbody .= "<tr>{$line}</tr>";
			}

			$table = "<table><thead>{$thead}</thead><tbody>{$tbody}</tbody></table>";

			$labels = array();
			foreach( array_keys( $names ) as $name ) {
				$labels[] = Hash::get( $this->Fluxcnaf->names, $name ) . " ({$name})";
			}

			$title = sprintf( 'Comparaison des balises de %s', implode( ', ', $labels ) );

			$encoding = strtolower( Configure::read( 'App.encoding' ) );

			$html = <<<FOOBAR
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$encoding}" />
		<title>{$title}</title>
		<style type="text/css">
			html { font-size: 12px; font-familty: sans-serif; }
			table { border-collapse: collapse; }
			th, td { vertical-align: top; border: 1px solid black; }
			td.false, td.empty.common, td.empty.diff { background: silver; }
			tbody th { text-align: left; }
			td ul { padding: 0; margin: 0; }
			td li { margin-left: 1em; }
		</style>
	</head>
	<body>
		<h1>{$title}</h1>
		{$table}
	</body>
</html>
FOOBAR;

			return $html;
		}

		public function main() {
			$files = $this->_files();
			$names = array_keys( $files );
			if( count($files) <= 1 ) {
				$msg = 'Cette commande n\'accepte qu\'un ensemble de fichiers séparés par une virgule';
				throw new ConsoleException( $msg );
			}

			// Extraction
			$results = array();
			foreach( $files as $flux => $file ) {
				$results[$flux] = array();
				$xsd = Xml::build( $file );

//				$donnees = $xsd->xpath( '/xsd:schema/xsd:element' );
				$donnees = $xsd->xpath( '/xsd:schema/xsd:element/xsd:complexType/xsd:sequence/xsd:element[@name]/../../..' );
				// TODO: scinder, puis xsd:complexType/xsd:sequence/xsd:element avec attribut name ou ref
				foreach( $donnees as $donnee ) {
					$results[$flux] = array_merge( $results[$flux], $this->_parseComplexType($donnee) );
				}

				ksort( $results[$flux] );
			}

			// Tableau de comparaison
			$paths = array();
			foreach( $results as $name => $infos ) {
				$paths = array_merge( $paths, array_keys( $infos ) );
			}
			sort($paths);
			$paths = Hash::normalize( array_unique($paths) );

			foreach( array_keys( $paths ) as $path ) {
				$paths[$path] = array();

				foreach( $results as $name => $infos ) {
					if( isset( $infos[$path] ) ) {
						$paths[$path][$name] = $infos[$path];
					}
					else {
						$paths[$path][$name] = false;
					}
				}
			}

			$content = $this->_compareToHtml( $files, $paths );

			$out = $this->_out( LOGS.'schema_compare_'.implode( '_', $names ).'.html' );
			$success = $this->createFile( $out, $content );

			$this->_stop( $success ? self::SUCCESS : self::ERROR );
		}

		/**
		 * Paramétrages et aides du shell.
		 */
		public function getOptionParser() {
			$Parser = parent::getOptionParser();

			if( $this->description !== null ) {
				$Parser->description( $this->description );
			}

			$Parser->addOptions( $this->options );

			$Parser->addSubcommands( $this->commands );

			$Parser->addArguments( $this->arguments );

			return $Parser;
		}
	}
?>