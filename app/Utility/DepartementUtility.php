<?php
	/**
	 * Code source de la classe DepartementUtility.
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe DepartementUtility ...
	 *
	 * @package app.Utility
	 */
	abstract class DepartementUtility
	{
		/**
		 * Renvoi le texte prêt à afficher quand ont lui donne les informations
		 * de la page dans $data et qu'on précise la ligne en $key.
		 *
		 * @param array $data
		 * @param number $key
		 * @return string
		 */
		public static function getTypeorientName( $data, $key ){
			$cg = Configure::read( 'Cg.departement' );
			$domain = 'departement' . $cg;

			// Si il n'y a qu'une ligne ou que c'est la première, ce sera forcement une première orientation
			if ( count($data) === 1 || $key === count($data) -1 ){
				return __d( $domain, 'premorient' );
			}

			// Dans le cas contraire, seule l'entrée juste avant est intéressante
			else{
				return __d( $domain, self::_compareTypeorient( $data[$key], $data[$key+1] ) );
			}
		}

		/**
		 * Compare deux array pour savoir si il y a
		 * maintien/maintien_changementstruct/reorient.
		 *
		 * @param array $data1
		 * @param array $data2
		 * @return String
		 */
		protected static function _compareTypeorient( array $data1, array $data2 ) {
			$cg = Configure::read( 'Cg.departement' );
			$method = "_compareTypeorient{$cg}";
			return self::$method( $data1, $data2 );
		}

		/**
		 * Retourne le résultat de la comparaison de deux types d'orientations
		 * pour le CG 66.
		 *
		 * @param array $data1
		 * @param array $data2
		 * @return string
		 */
		protected static function _compareTypeorient66( array $data1, array $data2 ) {
			if( $data1['Typeorient']['id'] === $data2['Typeorient']['id'] ) {
				return 'maintien';
			}
			else {
				$typeorientPrincipale = Configure::read( 'Orientstruct.typeorientprincipale' );
				$parent1 = $data1['Typeorient']['parentid'];
				$parent2 = $data2['Typeorient']['parentid'];

				$grandSocial = (
					in_array( $parent1, (array)$typeorientPrincipale['SOCIAL'] )
					&& in_array( $parent2, (array)$typeorientPrincipale['SOCIAL'] )
				);

				$emploi = (
					in_array( $parent1, (array)$typeorientPrincipale['Emploi'] )
					&& in_array( $parent2, (array)$typeorientPrincipale['Emploi'] )
				);

				return ( $grandSocial || $emploi ) ? 'maintien_changementstruct' : 'reorient';
			}
		}
	}
?>