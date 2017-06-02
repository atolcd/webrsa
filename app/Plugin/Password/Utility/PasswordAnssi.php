<?php
	/**
	 * Code source de la classe PasswordAnssi.
	 *
	 * PHP 5.3
	 *
	 * @package Password.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'PasswordPassword', 'Password.Utility' );

	/**
	 * La classe PasswordAnssi permet de calculer la force d'un mot de passe
	 * généré aléatoirement en fonction de la plage de symboles utilisée.
	 *
	 * @url https://www.ssi.gouv.fr/administration/precautions-elementaires/calculer-la-force-dun-mot-de-passe/
	 * @see https://en.wikipedia.org/wiki/Password_strength#Entropy_as_a_measure_of_password_strength
	 *
	 * @package Password.Utility
	 */
	class PasswordAnssi extends PasswordPassword
	{
		/**
		 * La constante utilisée pour une force de mot de passe "très forte".
		 */
		const STRENGTH_VERY_STRONG = 5;

		/**
		 * La constante utilisée pour une force de mot de passe "forte".
		 */
		const STRENGTH_STRONG = 4;

		/**
		 * La constante utilisée pour une force de mot de passe "moyenne".
		 */
		const STRENGTH_MEDIUM = 3;

		/**
		 * La constante utilisée pour une force de mot de passe "faible".
		 */
		const STRENGTH_WEAK = 2;

		/**
		 * La constante utilisée pour une force de mot de passe "très faible".
		 */
		const STRENGTH_REALLY_WEAK = 1;

		/**
		 * Options par défaut.
		 *
		 * @var array
		 */
		protected static $_defaults = array(
			'length' => 20,
			'typesafe' => true,
			'class_extra2' => true,
			'class_extra1' => true,
			'class_alphabetical_lower' => true,
			'class_hexadeciaml_lower' => true,
			'class_alphabetical_upper' => true,
			'class_hexadecimal_upper' => true,
			'class_numerical' => true,
			'class_binary' => true
		);

		/**
		 * Les caractères possibles, par classe.
		 *
		 * @var array
		 */
		protected static $_possibles = array(
			'class_extra2' => '&[|]@^µ§:/,.,<>°²³\'"',
			'class_extra1' => '€!#$*%? ',
			'class_alphabetical_lower' => 'ghijklmnopqrstuvwxyz',
			'class_hexadeciaml_lower' => 'abcdef',
			'class_alphabetical_upper' => 'GHIJKLMNOPQRSTUVWXYZ',
			'class_hexadecimal_upper' => 'ABCDEF',
			'class_numerical' => '23456789',
			'class_binary' => '01'
		);

		/**
		 * Les caractères qui ne peuvent pas être employés avec l'option typesafe
		 * à true, par classe.
		 *
		 * @var array
		 */
		protected static $_unsafe = array(
			'class_binary' => array( '0', '1' ),
			'class_alphabetical_lower' => array( 'l', 'i', 'o' ),
			'class_alphabetical_upper' => array( 'I', 'O' ),
			'class_extra1' => array( '€', ' ' ),
			'class_extra2' => array( '^', ',', '.', '°', '²', '³' )
		);

		/**
		 * Retourne le nombre estimé de symboles à tester pour la chaîne de
		 * caractères.
		 *
		 * @param string $string La chaîne de caractères
		 * @return int
		 */
		public static function symbols($string) {
			$symbols = 0;
			$alphabetic = false !== mb_eregi('[g-z]', $string);
			$hexadecimal = false !== mb_eregi('[a-f]', $string);
			$result = array(
				// 20
				'extra2' => false !== mb_ereg('[&\[\|\]@\^µ§:/;\.,<>°²³\'"]', $string),
				// 8
				'extra1' => false !== mb_ereg('[€!#$*%\? ]', $string),
				// 40 (52 - 12)
				'alphabetic' => $alphabetic,
				// 12 ((16 - 8 - 2) + 6)
				'hexadecimal' => $hexadecimal,
				// 8 (10 - 2)
				'numeric' => false !== mb_ereg('[2-9]', $string),
				// 2
				'binary' => false !== mb_ereg('[0-1]', $string),
				'lower' => $alphabetic || $hexadecimal ? mb_ereg('[a-z]', $string) : null,
				'upper' => $alphabetic || $hexadecimal ? mb_ereg('[A-Z]', $string) : null
			);

			if(0 < $result['extra2']) {
				$symbols += 20;
			}
			if(0 < $result['extra1']) {
				$symbols += 8;
			}
			if(0 < $result['alphabetic']) {
				$symbols += 26;
				if(0 < $result['lower'] && 0 < $result['upper']) {
					$symbols += 26;
				}
				if(0 < $result['numeric'] || 0 < $result['binary']) {
					$symbols += 10;
				}
			} elseif(0 < $result['hexadecimal']) {
				$symbols += 16;
				if(0 < $result['lower'] && 0 < $result['upper']) {
					$symbols += 6;
				}
			} elseif(0 < $result['numeric']) {
				$symbols += 10;
			} elseif(0 < $result['binary']) {
				$symbols += 2;
			}

			return $symbols;
		}

		/**
		 * Retourne le nombre de bits d'entropie pour une chaîne de caractères
		 * choisis aléatoirement.
		 *
		 * @param string $string La chaîne de caractères
		 * @return integer
		 */
		public static function entropyBits($string) {
			$length = mb_strlen($string);
			$symbols = static::symbols($string);
			return (int)round($length*log($symbols,2));
		}

		/**
		 * Retourne la "force" d'un mot de passe.
		 *
		 * Il s'agit d'un chiffre entre 1 (très faible) et 5 (très fort) qui
		 * correspond au constantes de classe STRENGTH_REALLY_WEAK (1), STRENGTH_WEAK (2),
		 * STRENGTH_MEDIUM (3), STRENGTH_STRONG (4) et STRENGTH_VERY_STRONG (5)
		 * à partir de l'entropie calculée par la méthode entropyBits().
		 *
		 * @see PasswordAnssi::entropyBits()
		 *
		 * @param string $string Le mot de passe
		 * @return integer
		 */
		public static function strength($string) {
			$entropyBits = static::entropyBits($string);

			if(128 <= $entropyBits) {
				return static::STRENGTH_VERY_STRONG;
			} elseif(100 <= $entropyBits) {
				return static::STRENGTH_STRONG;
			} elseif(80 <= $entropyBits) {
				return static::STRENGTH_MEDIUM;
			} elseif(64 <= $entropyBits) {
				return static::STRENGTH_WEAK;
			}

			return static::STRENGTH_REALLY_WEAK;
		}
	}
?>