<?php
	/**
	 * Fichier source de la classe ApreversementHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ApreversementHelper ...
	 *
	 * @package app.View.Helper
	 */
	class ApreversementHelper extends AppHelper
	{
		public $helpers = array( 'Xform', 'Xhtml', 'Html', 'Locale' );

		public $validate = array(
			'montantattribue' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
				array(
					'rule' => 'numeric',
					'message' => 'Valeur numérique seulement'
				),
			)
		);

		/**
		*
		*/

		public function cells( $i, $apre, $nbpaiementsouhait ) {
			$apre_id = Set::classicExtract( $apre, 'Apre.id' );
			$personne_id = Set::classicExtract( $apre, 'Apre.personne_id' );
			$apreetatliquidatif_id = Set::classicExtract( $apre, 'ApreEtatliquidatif.id' );
			$etatliquidatif_id = $this->request->params['pass'][0];



			$montanttotal = Set::classicExtract( $apre, 'Apre.montantaverser' );

			$montantattribue = Set::classicExtract( $apre, 'ApreEtatliquidatif.montantattribue' );
			$montantdejaverse = Set::classicExtract( $apre, 'Apre.montantdejaverse' );
			$nbpaiementeff = Set::classicExtract( $apre, 'Apre.nbpaiementeff' );
			$nbcourantpaiement = Set::classicExtract( $apre, 'Apre.nbpaiementsouhait' );


			if( $nbpaiementeff > 0 ) {
				$montanttotal =  $montanttotal - $montantattribue;
				$nbpaiementsouhait = array( $nbcourantpaiement - $nbpaiementeff );
				$montantattribue = $montanttotal - $montantdejaverse;
				$montanttotal = Set::classicExtract( $apre, 'Apre.montantaverser' );
			}

			if( isset( $this->request->data['ApreEtatliquidatif'][$i]['montantattribue'] ) ) {
				$montantattribue = $this->request->data['ApreEtatliquidatif'][$i]['montantattribue'];
			}

			$cells = array(
				$this->Xhtml->tag( 'td', Set::classicExtract( $apre, 'Dossier.numdemrsa' ) ),
				$this->Xhtml->tag( 'td', Set::classicExtract( $apre, 'Apre.numeroapre' ) ),
				$this->Xhtml->tag( 'td', $this->Locale->date( 'Date::short', Set::classicExtract( $apre, 'Apre.datedemandeapre' ) ) ),
				$this->Xhtml->tag( 'td', Set::classicExtract( $apre, 'Personne.nom' ) ),
				$this->Xhtml->tag( 'td', Set::classicExtract( $apre, 'Personne.prenom' ) ),
				$this->Xhtml->tag( 'td', Set::classicExtract( $apre, 'Adresse.nomcom' ) ),
				$this->Xhtml->tag( 'td', $this->Locale->money( $montanttotal ), array( 'class' => 'number' ) ),
			$this->Xhtml->tag( 'td', $this->Xform->input( "Apre.{$i}.id", array( 'type' => 'hidden', 'value' => $apre_id ) ).$this->Xform->input( "Apre.{$i}.personne_id", array( 'type' => 'hidden', 'value' => $personne_id ) ).$this->Xform->input( "Apre.{$i}.nbpaiementsouhait", array( 'label' => false, 'type' => 'select', 'options' => $nbpaiementsouhait, 'empty' => true, 'disabled' => ( $nbpaiementeff > 0 ) ) ) ),

				$this->Xhtml->tag( 'td', $this->Locale->number( ( !is_null( $nbpaiementeff ) ? $nbpaiementeff : 0 ) ), array( 'class' => 'number' ) ),

				$this->Xhtml->tag( 'td', $this->Xform->input( "ApreEtatliquidatif.{$i}.id", array( 'type' => 'hidden', 'value' => $apreetatliquidatif_id ) ).
				$this->Xform->input( "ApreEtatliquidatif.{$i}.etatliquidatif_id", array( 'type' => 'hidden', 'value' => $etatliquidatif_id ) ).
				$this->Xform->input( "ApreEtatliquidatif.{$i}.apre_id", array( 'type' => 'hidden', 'value' => $apre_id ) ).
				$this->Xform->input( "ApreEtatliquidatif.{$i}.montantaverser", array( 'type' => 'hidden', 'value' => $montanttotal ) ).
				$this->Xform->input( "ApreEtatliquidatif.{$i}.montantattribue", array( 'type' => 'text', 'label' => false, 'value' => str_replace( '.', ',', $montantattribue ) ) ) ),

				$this->Xhtml->tag( 'td', $this->Locale->money( $montantdejaverse ), array( 'class' => 'number' ) )
			);

			return implode( '', $cells );
		}
	}
?>