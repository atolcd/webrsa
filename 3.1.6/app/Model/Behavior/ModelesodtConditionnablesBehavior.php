<?php
	/**
	 * Code source de la classe ModelesodtConditionnables.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Permet de compléter l'attribut modelesOdt du modèle, suivant la valeur de
	 * configuration de 'Cg.departement'.
	 *
	 * Exemple:
	 * <pre>
	 * 	class Foo extends AppModel
	 * 	{
	 * 		public $modelesOdt = array(
	 * 			'%s/modele_commun.odt'
	 * 		);
	 *
	 * 		public $actsAs = array(
	 * 			'ModelesodtConditionnables' => array(
	 * 				66 => '%s/modele_66.odt',
	 * 				93 => array(
	 * 					'%s/modele_93.odt',
	 * 					'%s/modele_93_autre.odt',
	 * 				)
	 * 			)
	 * 		);
	 * 	}
	 * </pre>
	 *
	 * Une fois la classe instanciée, contenu de l'attribut modelesOdt:
	 * 	- CG 58: array( 'Foo/modele_commun.odt' )
	 * 	- CG 66: array( 'Foo/modele_commun.odt', 'Foo/modele_66.odt' )
	 * 	- CG 93: array( 'Foo/modele_commun.odt', 'Foo/modele_93.odt', 'Foo/modele_93_autre.odt' )
	 *
	 * @package app.Model.Behavior
	 */
	class ModelesodtConditionnablesBehavior extends ModelBehavior
	{
		/**
		*
		*/
		public function setup( Model $model, $settings = array() ) {
			if( !empty( $settings ) ) {
				$cg = Configure::read( 'Cg.departement' );
				$model->modelesOdt = (array)$model->modelesOdt;
				foreach( $settings as $cgKey => $modelesodt ) {
					if( $cg == $cgKey ) {
						$modelesodt = (array)$modelesodt;
						$model->modelesOdt = array_merge( $model->modelesOdt, $modelesodt );
					}
				}
				$model->modelesOdt = array_unique( $model->modelesOdt );
			}
		}
	}
?>