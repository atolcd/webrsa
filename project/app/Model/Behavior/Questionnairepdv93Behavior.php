<?php
	/**
	 * Code source de la classe Questionnairepdv93Behavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe Questionnairepdv93Behavior contient du code commun des classes
	 * de modèles Questionnaired1pdv93 et Questionnaired2pdv93.
	 *
	 * @package app.Model.Behavior
	 */
	class Questionnairepdv93Behavior extends ModelBehavior
	{

		/**
		 * Retourne la soumission à droits et devoirs d'un allocataire donné.
		 *
		 * @param Model $Model
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function toppersdrodevorsa( Model $Model, $personne_id ) {
			$querydata = array(
				'fields' => array( 'toppersdrodevorsa' ),
				'contain' => false,
				'conditions' => array( 'personne_id' => $personne_id )
			);
			$calculdroitrsa = $Model->Personne->Calculdroitrsa->find( 'first', $querydata );
			$toppersdrodevorsa = Hash::get( $calculdroitrsa, 'Calculdroitrsa.toppersdrodevorsa' );

			return $toppersdrodevorsa;

		}

		/**
		 * Retourne la soumission à droits et devoirs d'un allocataire donné.
		 *
		 * @param Model $Model
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function droitsouverts( Model $Model, $personne_id ) {
			$querydata = array(
				'fields' => array( 'Situationdossierrsa.etatdosrsa' ),
				'contain' => false,
				'conditions' => array( 'Personne.id' => $personne_id ),
				'joins' => array(
					$Model->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Model->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$Model->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
				)
			);

			$situationdossierrsa = $Model->Personne->find( 'first', $querydata );
			$situationdossierrsa = Hash::get( $situationdossierrsa, 'Situationdossierrsa.etatdosrsa' );
			$situationdossierrsa = in_array( $situationdossierrsa, (array)Configure::read( 'Situationdossierrsa.etatdosrsa.ouvert' ), true );

			return $situationdossierrsa;
		}
	}
?>