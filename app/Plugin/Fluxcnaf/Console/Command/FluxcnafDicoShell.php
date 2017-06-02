<?php
	/**
	 * Code source de la classe FluxcnafDicoShell.
	 *
	 * PHP 5.3
	 *
	 * @package Fluxcnaf.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Xml', 'Utility' );
	App::uses( 'FluxcnafDico', 'Fluxcnaf.Utility' );

	/**
	 * La classe FluxcnafDicoShell ...
	 *
	 * sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafDico locale /tmp/vrsd0301DICO.xml
	 * sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafDico locale /tmp/vrsd0301DICO.xml,/tmp/virs0901DICO.xml,/tmp/vird0201DICO.xml,/tmp/vrsc0201DICO.xml,/tmp/vrsf0501DICO.xml
	 *
	 * sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafDico in_list /tmp/vrsd0301DICO.xml
	 * INFO: il en manque un (/tmp/vrsf0501DICO.xml), mais il semblerait que l'on ne l'intègre pas
	 * sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafDico in_list /tmp/vrsd0301DICO.xml,/tmp/virs0901DICO.xml,/tmp/vird0201DICO.xml,/tmp/vrsc0201DICO.xml,/tmp/vrsf0501DICO.xml
	 *
	 * sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafDico compare /tmp/vrsd0301DICO.xml,/tmp/vrsb0801DICO.xml
	 * sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafDico compare /tmp/vrsd0301DICO.xml,/tmp/virs0901DICO.xml,/tmp/vird0201DICO.xml,/tmp/vrsc0201DICO.xml,/tmp/vrsf0501DICO.xml
	 *
	 * @see http://xemelios.org/user-guide/documents/rsa.html
	 *
	 * @package Fluxcnaf.Console.Command
	 */
	class FluxcnafDicoShell extends AppShell
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
		public $commands = array(
			'locale' => array(
				'help' => "Génère un fichier de traductions à partir des enumérations de fichiers <schema>Dico.xml"
			),
			'compare' => array(
				'help' => "Génère un fichier HTML de comparaison des valeurs finies de balises (champs) pour un ensemble de fichiers <schema>Dico.xml"
			),
			'in_list' => array(
				'help' => "Génère un fichier PHP contenant les règles de validation inList à partir des enumérations de fichiers <schema>Dico.xml"
			)
		);

		/**
		 * Liste des arguments à passe au shell.
		 *
		 * @var array
		 */
		public $arguments = array(
			'xml' => array(
				'help' => 'Le chemin vers le(s) fichier(s) XML à traiter, séparés par une virgule, sans espace',
				'required' => true
			)
		);

		protected function _out( $default ) {
			return ( isset( $this->params['out'] ) && !empty( $this->params['out'] ) ) ? $this->params['out'] : $default;
		}

		public function comparison( array $names ) {
			$names = Hash::normalize( $names );
			foreach( array_keys( $names ) as $name ) {
				$names[$name] = sprintf( '/tmp/%sDICO.xml', $name );
			}

			$result = FluxcnafDico::comparison( $names );

			return $result;
		}

		protected function _compareToHtml( array $names, array $results ) {
			$thead = '';
			foreach( array_keys( $names ) as $name ) {
				$thead .= "<th>{$name}</th>";
			}
			$thead = "<tr><th>Balise</th>{$thead}<th>Valeurs communes</th></tr>";

			$tbody = '';
			foreach( $results as $field => $params ) {
				$line = "<th>{$field}</th>";

				foreach( $params as $column => $values ) {
					if( $values === false ) {
						$line .= "<td class=\"false\"></td>";
					}
					else if( empty( $values ) ) {
						$line .= "<td class=\"empty {$column}\"></td>";
					}
					else {
						$line .= "<td><ul><li>".implode( '</li><li>', $values )."</li></ul></td>";
					}
				}

				$tbody .= "<tr>{$line}</tr>";
			}

			$table = "<table><thead>{$thead}</thead><tbody>{$tbody}</tbody></table>";

			$title = sprintf( 'Énumérations de %s', implode( ', ', array_keys( $names ) ) );

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
			td.false, td.empty.common { background: silver; }
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


		protected function _files() {
			$result = array();
			$tokens = explode( ',', $this->args[0] );

			foreach( $tokens as $token ) {
				$name = preg_replace( '/Dico\.xml$/i', '', basename( $token ) );
				$result[$name] = $token;
			}

			return $result;
		}

		public function compare() {
			$files = $this->_files();
			$names = array_keys( $files );
			if( count($files) <= 1 ) {
				$msg = 'Cette commande n\'accepte qu\'un ensemble de fichiers séparés par une virgule';
				throw new ConsoleException( $msg );
			}

			$results = FluxcnafDico::comparison( $files );
			$content = $this->_compareToHtml( $files, $results );

			$out = $this->_out( LOGS.'compare_enums_'.implode( '_', $names ).'.html' );
			$success = $this->createFile( $out, $content );

			$this->_stop( $success ? self::SUCCESS : self::ERROR );
		}

		protected function _enumsToPo( array $enums ) {
			$content = array();
			$sep = str_repeat( '#', 80 );
			foreach( $enums as $field => $values ) {
				$block = "{$sep}\n# {$field}\n{$sep}\n\n";
				foreach( $values as $msgid => $msgstr ) {
					$msgid = str_replace( '"', '\\"', $msgid );
					$msgstr = preg_replace( '/\s+/', ' ', str_replace( '"', '\\"', $msgstr ) );

					$block .= "msgid \"ENUM::{$field}::{$msgid}\"\nmsgstr \"{$msgstr}\"\n\n";
				}
				$content[] = rtrim( $block )."\n";
			}
			$content = implode( "\n", $content );

			$po = <<<FOOBAR
msgid ""
msgstr ""
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"Plural-Forms: nplurals=2; plural=(n>1);\\n"
FOOBAR;

			return $po."\n\n".$content;
		}

		protected function _enumsToInList( array $enums ) {
			$content = array();

			foreach( $enums as $field => $values ) {
				$field = strtolower( $field );
				$values = "array( '".implode( "', '", array_keys( $values ) )."' )";
				$content[] = "\t'{$field}' => array(\n\t\t'inList' => array(\n\t\t\t'rule' => array( 'inList', {$values} ),\n\t\t\t'message' => null,\n\t\t\t'allowEmpty' => true\n\t\t),\n\t)";
			}

			$content = implode( ",\n", $content );

			return "<?php\n\$validate = array(\n".$content."\n);\n?>";
		}

		protected function _allEnums( array $files ) {
			$enums = array();

			foreach( $files as $file ) {
				$tmp = FluxcnafDico::enums( $file );
				foreach( $tmp as $field => $values ) {
					if( isset( $enums[$field] ) === false ) {
						$enums[$field] = $values;
					}
					else {
						$enums[$field] = $enums[$field] + $values;
					}
				}
			}

			return $enums;
		}

		/**
		 * Génère un fichier de traductions (.po) à partir des valeurs des
		 * énumérations d'un fichier <schema>Dico.xml.
		 *
		 * TODO: permettre de spécifier plusieurs fichiers, voir in_list -> attention à l'ordre pour les traductions multiples
		 */
		public function locale() {
			$files = $this->_files();

			$enums = $this->_allEnums( $files );
			$content = $this->_enumsToPo( $enums );

			$out = $this->_out( LOGS.'locale_'.implode( '_', array_keys( $files ) ).'.po' );
			$success = $this->createFile( $out, $content );

			$this->_stop( $success ? self::SUCCESS : self::ERROR );
		}

		/**
		 * Génère un fichier PHP contenant les règles de validation inList à
		 * partir des enumérations de fichiers <schema>Dico.xml
		 */
		public function in_list() {
			$files = $this->_files();

			$enums = $this->_allEnums( $files );

			$content = $this->_enumsToInList( $enums );

			$out = $this->_out( LOGS.'in_list_'.implode( '_', array_keys( $files ) ).'.php' );
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