<?php
	/**
	 * Fichier source de la classe XlsHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe XlsHelper ...
	 *
	 * @package app.View.Helper
	 */
	class XlsHelper extends AppHelper
	{
		/**
		* Buffer. Chaque indice du tableau contient une ligne du fichier.
		*
		* @var array
		*/
		public $rows = array();

		/**
		* Ajoute un nombre
		*
		* @param int $row Abscisse
		* @param int $col Ordonnée
		* @param int $val Valeur numérique
		* @return string Valeur encodée
		*/
		public function addNumber($row, $col, $val) {
			$out  = pack("sssss", 0x203, 14, $row, $col, 0x0);
			$out .= pack("d", $val);

			return $out;
		}

		/**
		* Ajoute une chaine de caractères
		*
		* @param int $row Abscisse
		* @param int $col Ordonnée
		* @param int $val Chaine de caractères
		* @return string Valeur encodée
		*/
		public function addString($row, $col, $val) {
			$length = strlen($val);

			$out  = pack("ssssss", 0x204, 8 + $length, $row, $col, 0x0, $length);
			$out .= $val;

			return $out;
		}

		/**
		* Ajoute une ligne dans le buffer
		*
		* @param array $data Tableau de valeurs
		*/
		public function addRow($data = array()) {
			$out = '';

			$row = count($this->rows);

			foreach($data as $col => $val) {
				if(is_numeric($val)) {
					$out .= $this->addNumber($row, $col, $val);
				}
				else {
					$out .= $this->addString($row, $col, "{$val}");
				}
			}

			$this->rows[] = $out;
		}

		/**
		* Retourne buffer encodé, avec l'entête et la fin de fichier.
		*
		* @return string Contenu du fichier
		*/
		public function render() {
			$out  = pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
			$out .= join('', $this->rows);
			$out .= pack("ss", 0x0A, 0x00);

			return $out;
		}
	}
?>